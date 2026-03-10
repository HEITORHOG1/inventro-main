<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Estoque Library — Controle de estoque no PDV
 *
 * Responsabilidades:
 * - Calcular estoque disponível
 * - Validar estoque de múltiplos itens
 * - Lock Redis por produto (previne oversell multi-terminal)
 * - Decrementar estoque atomicamente
 */
class Estoque {

    protected $CI;
    protected $redis = null;
    protected $lock_ttl;
    protected $permitir_sem_estoque;

    public function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->load->model('pdv/Pdv_model', 'pdv_model');

        $this->lock_ttl = (int) (getenv('PDV_ESTOQUE_LOCK_TTL') ?: 30);
        $this->permitir_sem_estoque = (getenv('PDV_PERMITIR_VENDA_SEM_ESTOQUE') ?: 'false') === 'true';
    }

    // =========================================================================
    // CÁLCULO DE ESTOQUE
    // =========================================================================

    /**
     * Calcula estoque disponível de um produto
     *
     * Fórmula: SUM(inv_stock.case_qty) para o product_id
     * O inv_stock já consolida: compras + devol_cliente - vendas - devol_fornecedor
     *
     * @param int $product_id
     * @return float Estoque disponível
     */
    public function calcular_disponivel($product_id)
    {
        return $this->CI->pdv_model->calcularEstoqueDisponivel((int) $product_id);
    }

    /**
     * Verifica se a venda sem estoque está permitida
     *
     * @return bool
     */
    public function permitir_sem_estoque()
    {
        return $this->permitir_sem_estoque;
    }

    // =========================================================================
    // VALIDAÇÃO EM LOTE (pré-finalização)
    // =========================================================================

    /**
     * Valida estoque de todos os itens de uma venda
     *
     * @param array $itens  Array de ['product_id' => int, 'quantidade' => float, 'nome' => string]
     * @return array ['valido' => bool, 'erros' => [...]]
     */
    public function validar_itens($itens)
    {
        $erros = [];

        // Agregar quantidades por product_id (mesmo produto pode aparecer múltiplas vezes)
        $agrupado = [];
        foreach ($itens as $item) {
            $pid = isset($item['product_id']) ? (int) $item['product_id'] : 0;
            if ($pid === 0) continue; // Genérico — sem controle de estoque

            if (!isset($agrupado[$pid])) {
                $agrupado[$pid] = [
                    'product_id' => $pid,
                    'quantidade' => 0,
                    'nome'       => isset($item['nome']) ? $item['nome'] : 'Produto #' . $pid,
                ];
            }
            $agrupado[$pid]['quantidade'] += (float) $item['quantidade'];
        }

        // Validar cada produto
        foreach ($agrupado as $pid => $dados) {
            $estoque = $this->calcular_disponivel($pid);

            if ($estoque < $dados['quantidade']) {
                if (!$this->permitir_sem_estoque) {
                    $erros[] = [
                        'product_id'         => $pid,
                        'nome'               => $dados['nome'],
                        'quantidade_pedida'  => $dados['quantidade'],
                        'estoque_disponivel' => $estoque,
                        'message'            => 'Estoque insuficiente: "' . $dados['nome']
                            . '" — pedido: ' . $dados['quantidade']
                            . ', disponível: ' . $estoque,
                    ];
                }
            }
        }

        return [
            'valido' => empty($erros),
            'erros'  => $erros,
        ];
    }

    // =========================================================================
    // REDIS STOCK LOCK
    // =========================================================================

    /**
     * Inicializa conexão Redis
     *
     * @return Redis|null
     */
    private function _get_redis()
    {
        if ($this->redis !== null) {
            return $this->redis;
        }

        if (!class_exists('Redis')) {
            log_message('debug', 'Estoque: extensão Redis não disponível, locks desabilitados');
            return null;
        }

        try {
            $this->redis = new Redis();
            $host = getenv('REDIS_HOST') ?: '127.0.0.1';
            $port = (int) (getenv('REDIS_PORT') ?: 6379);

            if (!$this->redis->connect($host, $port, 2)) {
                log_message('error', 'Estoque: falha ao conectar Redis ' . $host . ':' . $port);
                $this->redis = null;
                return null;
            }

            return $this->redis;
        } catch (Exception $e) {
            log_message('error', 'Estoque: exceção Redis — ' . $e->getMessage());
            $this->redis = null;
            return null;
        }
    }

    /**
     * Adquire lock de estoque para um produto (Redis SET NX EX)
     *
     * @param int    $product_id
     * @param string $lock_owner  Identificador único (ex: terminal_id + timestamp)
     * @param int    $ttl         Segundos de TTL (padrão: PDV_ESTOQUE_LOCK_TTL)
     * @return bool  true se adquiriu o lock
     */
    public function adquirir_lock($product_id, $lock_owner, $ttl = null)
    {
        $redis = $this->_get_redis();
        if (!$redis) {
            // Sem Redis — prossegue sem lock (fallback para ambientes sem Redis)
            log_message('debug', 'Estoque: sem Redis, lock ignorado para product_id=' . $product_id);
            return true;
        }

        $ttl = $ttl ?: $this->lock_ttl;
        $key = 'pdv:stock_lock:' . (int) $product_id;

        // SET key value NX EX ttl — atômico
        $result = $redis->set($key, $lock_owner, ['NX', 'EX' => $ttl]);

        return $result === true;
    }

    /**
     * Libera lock de estoque de um produto
     *
     * @param int    $product_id
     * @param string $lock_owner  Mesmo identificador usado na aquisição
     * @return bool
     */
    public function liberar_lock($product_id, $lock_owner)
    {
        $redis = $this->_get_redis();
        if (!$redis) {
            return true;
        }

        $key = 'pdv:stock_lock:' . (int) $product_id;

        // Lua script para liberar apenas se o owner for o mesmo (atomicidade)
        $lua = <<<'LUA'
if redis.call("GET", KEYS[1]) == ARGV[1] then
    return redis.call("DEL", KEYS[1])
else
    return 0
end
LUA;

        try {
            $result = $redis->eval($lua, [$key, $lock_owner], 1);
            return $result == 1;
        } catch (Exception $e) {
            log_message('error', 'Estoque: falha ao liberar lock — ' . $e->getMessage());
            // Fallback: tenta DEL direto
            $redis->del($key);
            return true;
        }
    }

    /**
     * Adquire locks de estoque para múltiplos produtos com retry
     *
     * @param array  $product_ids  Array de product_id
     * @param string $lock_owner   Identificador único
     * @param int    $max_retries  Máximo de tentativas (padrão: 3)
     * @param int    $retry_ms     Delay entre tentativas em ms (padrão: 500)
     * @return array ['success' => bool, 'locked' => [product_ids], 'failed' => [product_ids]]
     */
    public function adquirir_locks($product_ids, $lock_owner, $max_retries = 3, $retry_ms = 500)
    {
        // Remover duplicatas e zeros
        $product_ids = array_unique(array_filter(array_map('intval', $product_ids)));

        if (empty($product_ids)) {
            return ['success' => true, 'locked' => [], 'failed' => []];
        }

        $locked = [];
        $failed = [];

        for ($attempt = 1; $attempt <= $max_retries; $attempt++) {
            $failed = [];

            foreach ($product_ids as $pid) {
                if (in_array($pid, $locked)) {
                    continue; // Já lockado
                }

                if ($this->adquirir_lock($pid, $lock_owner)) {
                    $locked[] = $pid;
                } else {
                    $failed[] = $pid;
                }
            }

            if (empty($failed)) {
                return ['success' => true, 'locked' => $locked, 'failed' => []];
            }

            // Se não é a última tentativa, aguardar
            if ($attempt < $max_retries) {
                usleep($retry_ms * 1000);
            }
        }

        // Falhou após todas tentativas — liberar locks já adquiridos
        foreach ($locked as $pid) {
            $this->liberar_lock($pid, $lock_owner);
        }

        return ['success' => false, 'locked' => [], 'failed' => $failed];
    }

    /**
     * Libera locks de múltiplos produtos
     *
     * @param array  $product_ids
     * @param string $lock_owner
     */
    public function liberar_locks($product_ids, $lock_owner)
    {
        foreach ($product_ids as $pid) {
            $this->liberar_lock((int) $pid, $lock_owner);
        }
    }
}
