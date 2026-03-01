<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . 'controllers/api/Api_controller.php';

/**
 * API v1 — App do Cardápio Digital
 *
 * Todos endpoints são públicos (sem JWT) pois o cardápio é aberto.
 * Rate limiting aplicado por IP.
 *
 * Reutiliza os mesmos Models da versão web:
 * - delivery/delivery_model (zonas, config, pedidos)
 * - delivery/cupons_model (cupons de desconto)
 */
class V1_cardapio extends Api_controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('delivery/delivery_model');
        $this->load->model('delivery/cupons_model');
        $this->load->helper(['url', 'html']);
    }

    // =========================================
    // Produtos e Categorias
    // =========================================

    /**
     * GET /api/v1/cardapio/produtos
     *
     * Lista todos os produtos disponíveis no cardápio, agrupados por categoria.
     */
    public function produtos() {
        $this->_require_method('GET');
        $this->_check_rate_limit('public');

        $this->db->select('p.id, p.product_id, p.name, p.price, p.description,
                           c.name as category_name, c.id as category_id,
                           pu.unit_name, pic.picture');
        $this->db->from('product_tbl p');
        $this->db->join('category_tbl c', 'p.category_id = c.id', 'left');
        $this->db->join('product_unit pu', 'p.unit = pu.id', 'left');
        $this->db->join('picture_tbl pic', "pic.from_id = p.product_id AND pic.picture_type = 'product'", 'left');
        $this->db->where('p.status', 1);
        $this->db->where('p.disponivel_cardapio', 1);
        $this->db->order_by('p.ordem_exibicao', 'ASC');
        $this->db->order_by('c.name', 'ASC');
        $this->db->order_by('p.name', 'ASC');
        $produtos_raw = $this->db->get()->result();

        $produtos = [];
        foreach ($produtos_raw as $p) {
            $produtos[] = [
                'id'            => (int) $p->id,
                'product_id'    => $p->product_id,
                'name'          => $p->name,
                'price'         => (float) $p->price,
                'description'   => $p->description,
                'category_id'   => (int) $p->category_id,
                'category_name' => $p->category_name ?? 'Outros',
                'unit_name'     => $p->unit_name,
                'picture'       => $p->picture ? base_url('admin_assets/img/product/' . $p->picture) : null
            ];
        }

        $this->_success(['produtos' => $produtos, 'count' => count($produtos)]);
    }

    /**
     * GET /api/v1/cardapio/categorias
     *
     * Lista categorias de produtos.
     */
    public function categorias() {
        $this->_require_method('GET');
        $this->_check_rate_limit('public');

        $categorias_raw = $this->db->get('category_tbl')->result();

        $categorias = [];
        foreach ($categorias_raw as $c) {
            $categorias[] = [
                'id'   => (int) $c->id,
                'name' => $c->name
            ];
        }

        $this->_success(['categorias' => $categorias]);
    }

    // =========================================
    // Zonas de Entrega
    // =========================================

    /**
     * GET /api/v1/cardapio/zonas
     *
     * Lista zonas de entrega ativas com taxas e tempos estimados.
     */
    public function zonas() {
        $this->_require_method('GET');
        $this->_check_rate_limit('public');

        $zonas_raw = $this->delivery_model->get_zones(true);

        $zonas = [];
        foreach ($zonas_raw as $z) {
            $zonas[] = [
                'id'        => (int) $z->id,
                'nome'      => $z->nome,
                'taxa'      => (float) $z->taxa,
                'tempo_min' => (int) ($z->tempo_min ?? 20),
                'tempo_max' => (int) ($z->tempo_max ?? 40)
            ];
        }

        $this->_success(['zonas' => $zonas]);
    }

    /**
     * GET /api/v1/cardapio/zona/detectar?bairro=Centro
     *
     * Detecta zona de entrega pelo nome do bairro.
     */
    public function detectar_zona() {
        $this->_require_method('GET');
        $this->_check_rate_limit('public');

        $bairro = $this->input->get('bairro', true);
        if (empty($bairro)) {
            $this->_error('Parâmetro "bairro" é obrigatório', 400);
        }

        $bairro_limpo = mb_strtolower(trim($bairro), 'UTF-8');
        $zonas = $this->db->where('ativo', 1)->get('delivery_zones')->result();

        $zona_encontrada = null;

        // Busca exata (case-insensitive)
        foreach ($zonas as $z) {
            if (mb_strtolower(trim($z->nome), 'UTF-8') === $bairro_limpo) {
                $zona_encontrada = $z;
                break;
            }
        }

        // Busca parcial
        if (!$zona_encontrada) {
            foreach ($zonas as $z) {
                $zona_limpa = mb_strtolower(trim($z->nome), 'UTF-8');
                if (mb_strpos($bairro_limpo, $zona_limpa) !== false || mb_strpos($zona_limpa, $bairro_limpo) !== false) {
                    $zona_encontrada = $z;
                    break;
                }
            }
        }

        if ($zona_encontrada) {
            $this->_success([
                'found' => true,
                'zona' => [
                    'id'        => (int) $zona_encontrada->id,
                    'nome'      => $zona_encontrada->nome,
                    'taxa'      => (float) $zona_encontrada->taxa,
                    'tempo_min' => (int) ($zona_encontrada->tempo_min ?? 20),
                    'tempo_max' => (int) ($zona_encontrada->tempo_max ?? 40)
                ]
            ]);
        } else {
            $this->_success(['found' => false, 'zona' => null]);
        }
    }

    // =========================================
    // Configuração da Loja
    // =========================================

    /**
     * GET /api/v1/cardapio/config
     *
     * Retorna configurações públicas da loja (horários, pedido mínimo, etc).
     */
    public function config() {
        $this->_require_method('GET');
        $this->_check_rate_limit('public');

        $config = $this->delivery_model->get_config();
        $loja   = $this->db->get('setting')->row();

        $this->_success([
            'config' => [
                'loja_nome'           => $loja->title ?? '',
                'loja_endereco'       => $loja->address ?? '',
                'loja_telefone'       => $loja->phone ?? '',
                'taxa_entrega'        => (float) ($config['taxa_entrega'] ?? 0),
                'pedido_minimo'       => (float) ($config['pedido_minimo'] ?? 0),
                'horario_abertura'    => $config['horario_abertura'] ?? '08:00',
                'horario_fechamento'  => $config['horario_fechamento'] ?? '22:00',
                'dias_funcionamento'  => $config['dias_funcionamento'] ?? '1,2,3,4,5,6',
                'aceita_cartao'       => ($config['aceita_cartao'] ?? '1') === '1',
                'aceita_dinheiro'     => ($config['aceita_dinheiro'] ?? '1') === '1',
                'aceita_pix'          => ($config['aceita_pix'] ?? '1') === '1',
                'loja_pausada'        => ($config['loja_pausada'] ?? '0') === '1',
                'loja_aberta'         => $this->_verificar_loja_aberta($config)
            ]
        ]);
    }

    // =========================================
    // Pedidos
    // =========================================

    /**
     * POST /api/v1/cardapio/pedido
     *
     * Criar novo pedido. Mesma lógica do processar_pedido web.
     *
     * Body: {
     *   "cliente_nome": "string",
     *   "cliente_telefone": "string",
     *   "cliente_endereco": "string",
     *   "cliente_complemento": "string (optional)",
     *   "cliente_cep": "string (optional)",
     *   "cliente_cidade": "string (optional)",
     *   "cliente_estado": "string (optional)",
     *   "cpf_nota": "string (optional)",
     *   "zona_id": "int (optional)",
     *   "forma_pagamento": "dinheiro|cartao|pix",
     *   "tipo_entrega": "entrega|retirada",
     *   "troco_para": "float (optional)",
     *   "observacao": "string (optional)",
     *   "cupom_codigo": "string (optional)",
     *   "items": [{"id": int, "name": "string", "price": float, "qty": int}]
     * }
     */
    public function criar_pedido() {
        $this->_require_method('POST');
        $this->_check_rate_limit('public');

        $input = $this->_get_json_body();

        // Sanitizar
        $input['cliente_nome']         = strip_tags(trim($input['cliente_nome'] ?? ''));
        $input['cliente_telefone']     = preg_replace('/[^0-9()\-\s+]/', '', $input['cliente_telefone'] ?? '');
        $input['cliente_endereco']     = strip_tags(trim($input['cliente_endereco'] ?? ''));
        $input['cliente_complemento']  = strip_tags(trim($input['cliente_complemento'] ?? ''));
        $input['cliente_cep']          = preg_replace('/[^0-9\-]/', '', $input['cliente_cep'] ?? '');
        $input['observacao']           = strip_tags(trim($input['observacao'] ?? ''));

        // Validar campos obrigatórios
        $tipo_entrega = ($input['tipo_entrega'] ?? 'entrega') === 'retirada' ? 'retirada' : 'entrega';

        $required = ['cliente_nome', 'cliente_telefone', 'forma_pagamento', 'items'];
        if ($tipo_entrega === 'entrega') {
            $required[] = 'cliente_endereco';
        }

        foreach ($required as $field) {
            if (empty($input[$field])) {
                $this->_error("Campo obrigatório: {$field}", 400);
            }
        }

        if (!is_array($input['items']) || count($input['items']) === 0) {
            $this->_error('Carrinho vazio', 400);
        }

        // Validar forma de pagamento
        $formas_validas = ['dinheiro', 'cartao', 'pix'];
        if (!in_array($input['forma_pagamento'], $formas_validas)) {
            $this->_error('Forma de pagamento inválida', 400);
        }

        // Config da loja
        $config = $this->delivery_model->get_config();

        if (!$this->_verificar_loja_aberta($config)) {
            $this->_error('A loja está fechada no momento', 400);
        }

        // Taxa de entrega
        $zona_id = isset($input['zona_id']) ? (int) $input['zona_id'] : null;
        $zona = null;
        $taxa_entrega = 0;

        if ($tipo_entrega === 'entrega') {
            if ($zona_id) {
                $zona = $this->delivery_model->get_zone($zona_id);
                if ($zona && $zona->ativo) {
                    $taxa_entrega = (float) $zona->taxa;
                } else {
                    $this->_error('Zona de entrega inválida', 400);
                }
            } else {
                $taxa_entrega = (float) ($config['taxa_entrega'] ?? 0);
            }
        }

        // Calcular subtotal
        $subtotal = 0;
        $items_to_save = [];
        foreach ($input['items'] as $item) {
            $qty   = max(1, (int) $item['qty']);
            $price = (float) $item['price'];
            $subtotal += $price * $qty;

            $items_to_save[] = [
                'product_id'   => (int) $item['id'],
                'product_name' => strip_tags($item['name'] ?? ''),
                'quantity'     => $qty,
                'unit_price'   => $price,
                'total_price'  => $price * $qty
            ];
        }

        // Pedido mínimo
        $pedido_minimo = (float) ($config['pedido_minimo'] ?? 0);
        if ($pedido_minimo > 0 && $subtotal < $pedido_minimo) {
            $this->_error('Pedido mínimo R$ ' . number_format($pedido_minimo, 2, ',', '.'), 400);
        }

        // Cupom de desconto
        $desconto = 0;
        $cupom_codigo = strtoupper(trim($input['cupom_codigo'] ?? ''));
        if (!empty($cupom_codigo)) {
            $cupom = $this->cupons_model->get_by_codigo($cupom_codigo);
            if ($cupom && $cupom->ativo) {
                switch ($cupom->tipo) {
                    case 'percentual':
                        $desconto = $subtotal * ((float) $cupom->valor / 100);
                        break;
                    case 'valor_fixo':
                        $desconto = (float) $cupom->valor;
                        break;
                    case 'frete_gratis':
                        $taxa_entrega = 0;
                        break;
                }
                $this->cupons_model->incrementar_uso($cupom_codigo);
            }
        }

        $total = $subtotal + $taxa_entrega - $desconto;
        if ($total < 0) $total = 0;

        // CPF na nota
        $cpf_nota = null;
        if (!empty($input['cpf_nota'])) {
            $cpf_nota = preg_replace('/[^0-9]/', '', $input['cpf_nota']);
            if (strlen($cpf_nota) == 11) {
                $cpf_nota = substr($cpf_nota, 0, 3) . '.' .
                            substr($cpf_nota, 3, 3) . '.' .
                            substr($cpf_nota, 6, 3) . '-' .
                            substr($cpf_nota, 9, 2);
            }
        }

        $order_data = [
            'cliente_nome'         => $input['cliente_nome'],
            'cliente_telefone'     => $input['cliente_telefone'],
            'cliente_endereco'     => $input['cliente_endereco'],
            'cliente_complemento'  => $input['cliente_complemento'] ?: null,
            'zona_id'              => $zona ? (int) $zona->id : null,
            'zona_nome'            => $zona ? $zona->nome : null,
            'subtotal'             => $subtotal,
            'taxa_entrega'         => $taxa_entrega,
            'desconto'             => $desconto,
            'total'                => $total,
            'forma_pagamento'      => $input['forma_pagamento'],
            'troco_para'           => ($input['forma_pagamento'] == 'dinheiro' && !empty($input['troco_para']))
                                      ? (float) str_replace([',', 'R$', ' '], ['.', '', ''], $input['troco_para'])
                                      : null,
            'tipo_checkout'        => 'app',
            'tipo_entrega'         => $tipo_entrega,
            'status'               => 'pendente',
            'observacao'           => $input['observacao'] ?: null,
            'cpf_nota'             => $cpf_nota
        ];

        $result = $this->delivery_model->create_order($order_data, $items_to_save);

        if ($result) {
            $this->_success([
                'order_id'     => $result['order_id'],
                'order_number' => $result['order_number'],
                'subtotal'     => $subtotal,
                'taxa_entrega' => $taxa_entrega,
                'desconto'     => $desconto,
                'total'        => $total,
                'message'      => 'Pedido realizado com sucesso!'
            ], 201);
        } else {
            $this->_error('Erro ao processar pedido', 500);
        }
    }

    /**
     * GET /api/v1/cardapio/pedido/{order_number}/status
     *
     * Status do pedido em tempo real.
     */
    public function status_pedido($order_number) {
        $this->_require_method('GET');
        $this->_check_rate_limit('public');

        if (empty($order_number)) {
            $this->_error('Número do pedido obrigatório', 400);
        }

        $order = $this->delivery_model->get_order_by_number($order_number);
        if (!$order) {
            $this->_error('Pedido não encontrado', 404);
        }

        $this->_success([
            'order_number'      => $order->order_number,
            'status'            => $order->status,
            'tipo_entrega'      => $order->tipo_entrega ?? 'entrega',
            'zona_nome'         => $order->zona_nome,
            'entregador_nome'   => $order->entregador_nome ?? null,
            'hora_confirmado'   => $order->hora_confirmado ?? null,
            'hora_preparando'   => $order->hora_preparando ?? null,
            'hora_pronto_coleta' => $order->hora_pronto_coleta ?? null,
            'hora_saiu_entrega' => $order->hora_saiu_entrega ?? null,
            'hora_entregue'     => $order->hora_entregue ?? null,
            'updated_at'        => $order->updated_at
        ]);
    }

    /**
     * POST /api/v1/cardapio/pedido/{order_number}/avaliar
     *
     * Body: {"nota": 1-5, "comentario": "string (optional)"}
     */
    public function avaliar_pedido($order_number) {
        $this->_require_method('POST');
        $this->_check_rate_limit('public');

        $order = $this->delivery_model->get_order_by_number($order_number);
        if (!$order) {
            $this->_error('Pedido não encontrado', 404);
        }

        if (!empty($order->avaliacao_nota)) {
            $this->_error('Este pedido já foi avaliado', 400);
        }

        $input = $this->_get_json_body();
        $nota      = isset($input['nota']) ? (int) $input['nota'] : 0;
        $comentario = strip_tags(trim($input['comentario'] ?? ''));

        if ($nota < 1 || $nota > 5) {
            $this->_error('Nota deve ser entre 1 e 5', 400);
        }

        $this->db->where('id', $order->id)->update('orders', [
            'avaliacao_nota'       => $nota,
            'avaliacao_comentario' => $comentario
        ]);

        $this->_success(['message' => 'Avaliação registrada! Obrigado.']);
    }

    // =========================================
    // Cupons
    // =========================================

    /**
     * GET /api/v1/cardapio/cupom/validar?codigo=ABC&subtotal=50.00
     */
    public function validar_cupom() {
        $this->_require_method('GET');
        $this->_check_rate_limit('public');

        $codigo   = strtoupper(trim($this->input->get('codigo', TRUE)));
        $subtotal = (float) $this->input->get('subtotal', TRUE);

        if (empty($codigo)) {
            $this->_error('Informe o código do cupom', 400);
        }

        $cupom = $this->db->where('codigo', $codigo)
            ->where('ativo', 1)
            ->get('cupons_desconto')->row();

        if (!$cupom) {
            $this->_success(['valido' => false, 'message' => 'Cupom não encontrado']);
            return;
        }

        $hoje = date('Y-m-d');
        if ($cupom->validade_inicio && $hoje < $cupom->validade_inicio) {
            $this->_success(['valido' => false, 'message' => 'Cupom ainda não está ativo']);
            return;
        }
        if ($cupom->validade_fim && $hoje > $cupom->validade_fim) {
            $this->_success(['valido' => false, 'message' => 'Cupom expirado']);
            return;
        }
        if ($cupom->uso_maximo !== null && $cupom->uso_atual >= $cupom->uso_maximo) {
            $this->_success(['valido' => false, 'message' => 'Cupom esgotado']);
            return;
        }
        if ($subtotal < (float) $cupom->valor_minimo_pedido) {
            $minimo = number_format($cupom->valor_minimo_pedido, 2, ',', '.');
            $this->_success(['valido' => false, 'message' => "Pedido mínimo R$ {$minimo} para este cupom"]);
            return;
        }

        $desconto = 0;
        switch ($cupom->tipo) {
            case 'percentual':
                $desconto = $subtotal * ((float) $cupom->valor / 100);
                break;
            case 'valor_fixo':
                $desconto = (float) $cupom->valor;
                break;
            case 'frete_gratis':
                $desconto = 0;
                break;
        }

        $this->_success([
            'valido'            => true,
            'tipo'              => $cupom->tipo,
            'valor'             => (float) $cupom->valor,
            'desconto_calculado' => round($desconto, 2),
            'message'           => 'Cupom aplicado!'
        ]);
    }

    // =========================================
    // Clientes
    // =========================================

    /**
     * GET /api/v1/cardapio/cliente/{telefone}
     *
     * Busca cliente por telefone (limpo, sem formatação).
     * SQL Injection CORRIGIDO — usa Query Builder seguro.
     */
    public function buscar_cliente($telefone) {
        $this->_require_method('GET');
        $this->_check_rate_limit('public');

        $telefone_limpo = preg_replace('/[^0-9]/', '', $telefone);
        if (strlen($telefone_limpo) < 8) {
            $this->_success(['found' => false, 'cliente' => null]);
            return;
        }

        // Query segura usando LIKE via Query Builder (SEM where(..., NULL, FALSE))
        $this->db->select('id, name, mobile, address, cpf, cep, cidade, estado');
        $this->db->from('customer_tbl');
        $this->db->where('status', 1);
        $this->db->like('mobile', $telefone_limpo);
        $cliente = $this->db->get()->row();

        if ($cliente) {
            $this->_success([
                'found'   => true,
                'cliente' => [
                    'id'       => (int) $cliente->id,
                    'nome'     => $cliente->name,
                    'telefone' => $cliente->mobile,
                    'endereco' => $cliente->address,
                    'cpf'      => $cliente->cpf ?? '',
                    'cep'      => $cliente->cep ?? '',
                    'cidade'   => $cliente->cidade ?? '',
                    'estado'   => $cliente->estado ?? ''
                ]
            ]);
        } else {
            $this->_success(['found' => false, 'cliente' => null]);
        }
    }

    /**
     * GET /api/v1/cardapio/cliente/{telefone}/pedidos
     *
     * Lista pedidos pendentes de um cliente (por telefone).
     * SQL Injection CORRIGIDO — usa Query Builder seguro.
     */
    public function pedidos_cliente($telefone) {
        $this->_require_method('GET');
        $this->_check_rate_limit('public');

        $telefone_limpo = preg_replace('/[^0-9]/', '', $telefone);
        if (strlen($telefone_limpo) < 8) {
            $this->_success(['pedidos' => []]);
            return;
        }

        // Query segura usando LIKE via Query Builder
        $this->db->select('id, order_number, status, total, forma_pagamento, tipo_entrega, created_at');
        $this->db->from('orders');
        $this->db->like('cliente_telefone', $telefone_limpo);
        $this->db->where_not_in('status', ['entregue', 'cancelado']);
        $this->db->order_by('created_at', 'DESC');
        $this->db->limit(10);
        $pedidos_raw = $this->db->get()->result();

        $status_labels = [
            'pendente'       => 'Aguardando confirmação',
            'confirmado'     => 'Confirmado',
            'preparando'     => 'Em preparo',
            'pronto_coleta'  => 'Pronto para coleta',
            'saiu_entrega'   => 'Saiu para entrega'
        ];

        $pedidos = [];
        foreach ($pedidos_raw as $p) {
            $pedidos[] = [
                'order_number'    => $p->order_number,
                'status'          => $p->status,
                'status_label'    => $status_labels[$p->status] ?? $p->status,
                'total'           => (float) $p->total,
                'forma_pagamento' => $p->forma_pagamento,
                'tipo_entrega'    => $p->tipo_entrega ?? 'entrega',
                'created_at'      => $p->created_at
            ];
        }

        $this->_success(['pedidos' => $pedidos, 'count' => count($pedidos)]);
    }

    // =========================================
    // Método privado
    // =========================================

    /**
     * Verifica se a loja está aberta (horário + dia + pausa)
     */
    private function _verificar_loja_aberta($config) {
        if (($config['loja_pausada'] ?? '0') === '1') {
            return false;
        }

        $dia_atual = (int) date('w');
        $dias_funcionamento = $config['dias_funcionamento'] ?? '1,2,3,4,5,6';
        $dias_array = array_map('intval', explode(',', $dias_funcionamento));

        if (!in_array($dia_atual, $dias_array)) {
            return false;
        }

        $hora_atual = date('H:i');
        $abertura   = $config['horario_abertura'] ?? '08:00';
        $fechamento = $config['horario_fechamento'] ?? '22:00';

        if ($hora_atual < $abertura || $hora_atual >= $fechamento) {
            return false;
        }

        return true;
    }
}
