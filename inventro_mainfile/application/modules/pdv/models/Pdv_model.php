<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Pdv_model — Operações de banco para o módulo PDV
 *
 * Tabelas: pdv_terminal, pdv_caixa, pdv_caixa_mov, pdv_audit_log, user, login_attempts
 */
class Pdv_model extends CI_Model {

    // =========================================================================
    // TERMINAL
    // =========================================================================

    /**
     * Busca terminal pelo número (ex: '001')
     */
    public function get_terminal_by_numero($numero)
    {
        return $this->db
            ->where('numero', $numero)
            ->where('ativo', 1)
            ->get('pdv_terminal')
            ->row();
    }

    /**
     * Busca terminal pelo ID
     */
    public function get_terminal($id)
    {
        return $this->db
            ->where('id', (int) $id)
            ->where('ativo', 1)
            ->get('pdv_terminal')
            ->row();
    }

    // =========================================================================
    // AUTENTICAÇÃO DO OPERADOR
    // =========================================================================

    /**
     * Busca usuário pela matrícula (login do PDV)
     * Retorna user com role_name para verificar permissão
     */
    public function get_user_by_matricula($matricula)
    {
        return $this->db
            ->select("u.id, CONCAT(u.firstname, ' ', COALESCE(u.lastname, '')) AS fullname, u.email, u.matricula, u.password_bcrypt AS password, u.image, u.is_admin AS user_type, u.status", false)
            ->from('user u')
            ->where('u.matricula', $matricula)
            ->where('u.status', 1)
            ->get()
            ->row();
    }

    /**
     * Busca usuário pelo email (fallback se matrícula não encontrada)
     */
    public function get_user_by_email($email)
    {
        return $this->db
            ->select("u.id, CONCAT(u.firstname, ' ', COALESCE(u.lastname, '')) AS fullname, u.email, u.matricula, u.password_bcrypt AS password, u.image, u.is_admin AS user_type, u.status", false)
            ->from('user u')
            ->where('u.email', $email)
            ->where('u.status', 1)
            ->get()
            ->row();
    }

    /**
     * Verifica se o usuário tem permissão para acessar o PDV
     * Checa se a role do usuário tem permissão no módulo 'pdv'
     */
    public function user_has_pdv_permission($user_id)
    {
        // Admin tem acesso a tudo
        $user = $this->db->where('id', (int) $user_id)->get('user')->row();
        if ($user && $user->is_admin == 1) {
            return true;
        }

        // Qualquer usuário ativo com role atribuída pode usar o PDV
        // (o controle fino é via matrícula + senha no login PDV)
        $result = $this->db
            ->select('role_acc_id')
            ->from('sec_user_access_tbl')
            ->where('fk_user_id', (int) $user_id)
            ->get()
            ->row();

        return !empty($result);
    }

    // =========================================================================
    // RATE LIMITING (reutiliza login_attempts)
    // =========================================================================

    /**
     * Conta tentativas de login falhadas nos últimos N minutos
     */
    public function count_login_attempts($identifier, $ip, $minutes = 15)
    {
        // Verifica se tabela existe (graceful fallback)
        if (!$this->db->table_exists('login_attempts')) {
            return 0;
        }

        $cutoff = date('Y-m-d H:i:s', strtotime("-{$minutes} minutes"));

        return $this->db
            ->where('email', $identifier)
            ->where('ip_address', $ip)
            ->where('attempted_at >=', $cutoff)
            ->count_all_results('login_attempts');
    }

    /**
     * Registra tentativa de login falhada
     */
    public function record_login_attempt($identifier, $ip)
    {
        if (!$this->db->table_exists('login_attempts')) {
            return;
        }

        $this->db->insert('login_attempts', [
            'email'        => $identifier,
            'ip_address'   => $ip,
            'attempted_at' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Limpa tentativas após login bem-sucedido
     */
    public function clear_login_attempts($identifier, $ip)
    {
        if (!$this->db->table_exists('login_attempts')) {
            return;
        }

        $this->db
            ->where('email', $identifier)
            ->where('ip_address', $ip)
            ->delete('login_attempts');
    }

    // =========================================================================
    // CAIXA (abertura/fechamento)
    // =========================================================================

    /**
     * Retorna o caixa aberto de um terminal (ou null se fechado)
     */
    public function get_caixa_aberto($terminal_id)
    {
        return $this->db
            ->select("c.*, CONCAT(u.firstname, ' ', COALESCE(u.lastname, '')) as operador_nome", false)
            ->from('pdv_caixa c')
            ->join('user u', 'u.id = c.operador_id')
            ->where('c.terminal_id', (int) $terminal_id)
            ->where('c.status', 'aberto')
            ->get()
            ->row();
    }

    /**
     * Retorna caixa por ID
     */
    public function get_caixa($id)
    {
        return $this->db
            ->select("c.*, CONCAT(u.firstname, ' ', COALESCE(u.lastname, '')) as operador_nome, t.numero as terminal_numero, t.nome as terminal_nome", false)
            ->from('pdv_caixa c')
            ->join('user u', 'u.id = c.operador_id')
            ->join('pdv_terminal t', 't.id = c.terminal_id')
            ->where('c.id', (int) $id)
            ->get()
            ->row();
    }

    /**
     * Abre um novo caixa
     *
     * @param array $data [terminal_id, operador_id, valor_abertura, observacao]
     * @return int ID do caixa criado
     */
    public function abrir_caixa($data)
    {
        $insert = [
            'terminal_id'    => (int) $data['terminal_id'],
            'operador_id'    => (int) $data['operador_id'],
            'valor_abertura' => (float) $data['valor_abertura'],
            'aberto_em'      => date('Y-m-d H:i:s'),
            'status'         => 'aberto',
            'observacao'     => isset($data['observacao']) ? $data['observacao'] : null,
        ];

        $this->db->insert('pdv_caixa', $insert);
        return $this->db->insert_id();
    }

    // =========================================================================
    // MOVIMENTAÇÕES DO CAIXA
    // =========================================================================

    /**
     * Registra uma movimentação no caixa
     *
     * @param array $data [caixa_id, tipo, valor, forma_pagamento, invoice_id, descricao, operador_id]
     */
    public function registrar_movimento($data)
    {
        $insert = [
            'caixa_id'        => (int) $data['caixa_id'],
            'tipo'            => $data['tipo'],
            'valor'           => (float) $data['valor'],
            'forma_pagamento' => isset($data['forma_pagamento']) ? $data['forma_pagamento'] : null,
            'invoice_id'      => isset($data['invoice_id']) ? (int) $data['invoice_id'] : null,
            'descricao'       => isset($data['descricao']) ? $data['descricao'] : null,
            'operador_id'     => (int) $data['operador_id'],
        ];

        // Supervisor ID (para sangria/suprimento)
        if (isset($data['supervisor_id']) && $data['supervisor_id']) {
            $insert['supervisor_id'] = (int) $data['supervisor_id'];
        }

        $this->db->insert('pdv_caixa_mov', $insert);
        return $this->db->insert_id();
    }

    // =========================================================================
    // ESTOQUE
    // =========================================================================

    /**
     * Calcula estoque disponível de um produto
     *
     * O inv_stock.case_qty já consolida o saldo real:
     * compras + devoluções_cliente - vendas - devoluções_fornecedor
     *
     * @param int $product_id
     * @return float
     */
    public function calcularEstoqueDisponivel($product_id)
    {
        $result = $this->db
            ->select('COALESCE(SUM(case_qty), 0) AS estoque', false)
            ->from('inv_stock')
            ->where('product_id', (int) $product_id)
            ->get()
            ->row();

        return $result ? (float) $result->estoque : 0.0;
    }

    /**
     * Recalcula estoque de múltiplos produtos de uma vez
     *
     * @param array $product_ids  Array de IDs
     * @return array  [product_id => estoque_disponivel]
     */
    public function calcularEstoqueLote($product_ids)
    {
        $product_ids = array_filter(array_map('intval', $product_ids));
        if (empty($product_ids)) {
            return [];
        }

        $result = $this->db
            ->select('product_id, COALESCE(SUM(case_qty), 0) AS estoque', false)
            ->from('inv_stock')
            ->where_in('product_id', $product_ids)
            ->group_by('product_id')
            ->get()
            ->result();

        $map = [];
        // Inicializar todos com 0
        foreach ($product_ids as $pid) {
            $map[$pid] = 0.0;
        }
        // Preencher com valores reais
        foreach ($result as $row) {
            $map[(int) $row->product_id] = (float) $row->estoque;
        }

        return $map;
    }

    // =========================================================================
    // BUSCA DE PRODUTO (PDV)
    // =========================================================================

    /**
     * Busca produto por código (cascata: ean_gtin → product_code → id)
     *
     * @param string $codigo Código lido no leitor de barras
     * @return object|null
     */
    public function buscar_produto_por_codigo($codigo)
    {
        $codigo = trim($codigo);
        if (empty($codigo)) {
            return null;
        }

        // 1) Busca por ean_gtin (código de barras)
        $produto = $this->_buscar_produto_query()
            ->where('p.ean_gtin', $codigo)
            ->get()
            ->row();

        if ($produto) return $produto;

        // 2) Busca por product_code
        $produto = $this->_buscar_produto_query()
            ->where('p.product_code', $codigo)
            ->get()
            ->row();

        if ($produto) return $produto;

        // 3) Busca por ID numérico
        if (is_numeric($codigo)) {
            $produto = $this->_buscar_produto_query()
                ->where('p.id', (int) $codigo)
                ->get()
                ->row();

            if ($produto) return $produto;
        }

        return null;
    }

    /**
     * Busca produto por nome (LIKE) para modal F5
     *
     * @param string $termo Termo de busca
     * @param int    $limit Máximo de resultados
     * @return array
     */
    public function buscar_produto_por_nome($termo, $limit = 20)
    {
        $termo = trim($termo);
        if (empty($termo)) {
            return [];
        }

        return $this->_buscar_produto_query()
            ->group_start()
                ->like('p.name', $termo)
                ->or_like('p.product_code', $termo)
                ->or_like('p.ean_gtin', $termo)
            ->group_end()
            ->limit((int) $limit)
            ->get()
            ->result();
    }

    /**
     * Busca produto por codigo_balanca (5 dígitos, para produtos pesáveis)
     *
     * @param string $codigo_balanca
     * @return object|null
     */
    public function buscar_produto_por_balanca($codigo_balanca)
    {
        return $this->_buscar_produto_query()
            ->where('p.codigo_balanca', $codigo_balanca)
            ->where('p.pesavel', 1)
            ->get()
            ->row();
    }

    /**
     * Query base para busca de produto (reusável)
     * Retorna: id, nome, preço, código, EAN, unidade, estoque, pesável, etc.
     */
    private function _buscar_produto_query()
    {
        return $this->db
            ->select('
                p.id,
                p.name AS nome,
                p.price AS preco,
                p.product_code,
                p.ean_gtin,
                p.pesavel,
                p.codigo_balanca,
                p.tipo_barcode_balanca,
                p.estoque_minimo,
                pu.unit_name AS unidade,
                COALESCE(SUM(inv.case_qty), 0) AS estoque_disponivel
            ')
            ->from('product_tbl p')
            ->join('product_unit pu', 'pu.id = p.unit', 'left')
            ->join('inv_stock inv', 'inv.product_id = p.id', 'left')
            ->where('p.status', 1)
            ->group_by('p.id');
    }

    /**
     * Lista categorias ativas (para modal produto genérico)
     */
    public function get_categorias()
    {
        return $this->db
            ->select('id, name')
            ->from('category_tbl')
            ->order_by('name', 'ASC')
            ->get()
            ->result();
    }

    // =========================================================================
    // VENDA SUSPENSA
    // =========================================================================

    /**
     * Salva venda suspensa
     *
     * @param array $data
     * @return int ID da venda suspensa
     */
    public function suspender_venda($data)
    {
        $expira_minutos = (int) (getenv('PDV_SUSPENSA_EXPIRA_MINUTOS') ?: 120);

        $insert = [
            'terminal_id'  => (int) $data['terminal_id'],
            'caixa_id'     => (int) $data['caixa_id'],
            'operador_id'  => (int) $data['operador_id'],
            'itens'        => json_encode($data['itens']),
            'cpf_cliente'  => isset($data['cpf_cliente']) ? $data['cpf_cliente'] : null,
            'customer_id'  => isset($data['customer_id']) ? (int) $data['customer_id'] : null,
            'total'        => (float) $data['total'],
            'motivo'       => isset($data['motivo']) ? $data['motivo'] : null,
            'status'       => 'suspensa',
            'suspensa_em'  => date('Y-m-d H:i:s'),
            'expires_at'   => date('Y-m-d H:i:s', strtotime("+{$expira_minutos} minutes")),
        ];

        $this->db->insert('pdv_venda_suspensa', $insert);
        return $this->db->insert_id();
    }

    /**
     * Lista vendas suspensas de um terminal (não expiradas)
     */
    public function listar_vendas_suspensas($terminal_id)
    {
        return $this->db
            ->select("vs.*, CONCAT(u.firstname, ' ', COALESCE(u.lastname, '')) AS operador_nome", false)
            ->from('pdv_venda_suspensa vs')
            ->join('user u', 'u.id = vs.operador_id')
            ->where('vs.terminal_id', (int) $terminal_id)
            ->where('vs.status', 'suspensa')
            ->where('vs.expires_at >=', date('Y-m-d H:i:s'))
            ->order_by('vs.suspensa_em', 'DESC')
            ->get()
            ->result();
    }

    /**
     * Conta vendas suspensas ativas de um terminal
     */
    public function contar_vendas_suspensas($terminal_id)
    {
        return $this->db
            ->where('terminal_id', (int) $terminal_id)
            ->where('status', 'suspensa')
            ->where('expires_at >=', date('Y-m-d H:i:s'))
            ->count_all_results('pdv_venda_suspensa');
    }

    /**
     * Recupera venda suspensa por ID
     */
    public function get_venda_suspensa($id)
    {
        return $this->db
            ->where('id', (int) $id)
            ->where('status', 'suspensa')
            ->where('expires_at >=', date('Y-m-d H:i:s'))
            ->get('pdv_venda_suspensa')
            ->row();
    }

    /**
     * Marca venda suspensa como recuperada
     */
    public function recuperar_venda_suspensa($id)
    {
        return $this->db
            ->where('id', (int) $id)
            ->update('pdv_venda_suspensa', [
                'status'        => 'recuperada',
                'recuperada_em' => date('Y-m-d H:i:s'),
            ]);
    }

    // =========================================================================
    // FINALIZAR VENDA (Fase 5)
    // =========================================================================

    /**
     * Grava venda completa no banco (atômico)
     *
     * @param array $dados [
     *   'transaction_id', 'customer_id', 'terminal_id', 'caixa_id',
     *   'operador_id', 'subtotal', 'desconto_total', 'total',
     *   'cpf_cliente', 'itens' => [...], 'pagamentos' => [...]
     * ]
     * @return array ['success' => bool, 'invoice_id' => int, 'message' => string]
     */
    public function gravar_venda($dados)
    {
        $this->db->trans_start();

        // 1. Inserir invoice_tbl
        $invoice_data = [
            'invoice_id'              => $dados['transaction_id'],
            'invoice'                 => $dados['transaction_id'],
            'customer_id'             => !empty($dados['customer_id']) ? (string) $dados['customer_id'] : '0',
            'date'                    => date('Y-m-d'),
            'total_amount'            => (float) $dados['subtotal'],
            'invoice_discount'        => (float) $dados['desconto_total'],
            'total_discount'          => (float) $dados['desconto_total'],
            'paid_amount'             => (float) $dados['total'],
            'due_amount'              => 0,
            'total_tax'               => 0,
            'status'                  => 2,
            'is_inhouse'              => 0,
            'shipping_method'         => '',
            'description'             => !empty($dados['cpf_cliente']) ? 'CPF: ' . $dados['cpf_cliente'] : 'Venda PDV',
            'payment_method'          => isset($dados['pagamentos'][0]['forma']) ? $dados['pagamentos'][0]['forma'] : 'dinheiro',
            'terminal_id'             => (int) $dados['terminal_id'],
            'caixa_id'                => (int) $dados['caixa_id'],
            'desconto_total'          => (float) $dados['desconto_total'],
            'desconto_autorizado_por' => isset($dados['desconto_autorizado_por']) ? (int) $dados['desconto_autorizado_por'] : null,
            'created_by'              => (string) $dados['operador_id'],
            'created_at'              => date('Y-m-d H:i:s'),
            'updated_by'              => (string) $dados['operador_id'],
        ];
        $this->db->insert('invoice_tbl', $invoice_data);
        $invoice_pk = $this->db->insert_id();

        if (!$invoice_pk) {
            $this->db->trans_rollback();
            return ['success' => false, 'message' => 'Erro ao inserir venda.'];
        }

        // 2. Inserir invoice_details para cada item
        foreach ($dados['itens'] as $item) {
            $detail = [
                'invoice_id'         => $dados['transaction_id'],
                'invoice_details_id' => $dados['transaction_id'] . '-' . (isset($item['product_id']) ? $item['product_id'] : uniqid()),
                'product_id'         => !empty($item['product_id']) ? (string) $item['product_id'] : '0',
                'quantity'           => (float) $item['quantidade'],
                'price'              => (float) $item['preco'],
                'discount'           => isset($item['desconto_calculado']) ? (float) $item['desconto_calculado'] : 0,
                'discount_amount'    => isset($item['desconto_calculado']) ? (float) $item['desconto_calculado'] : 0,
                'total_price'        => (float) $item['subtotal'],
                'tax'                => 0,
                'desconto_pct'       => isset($item['desconto_tipo']) && $item['desconto_tipo'] === 'percentual' ? (float) $item['desconto_valor'] : 0,
                'desconto_valor'     => isset($item['desconto_calculado']) ? (float) $item['desconto_calculado'] : 0,
                'descricao_manual'   => isset($item['descricao_manual']) ? $item['descricao_manual'] : null,
                'created_date'       => date('Y-m-d H:i:s'),
            ];
            $this->db->insert('invoice_details', $detail);

            // 3. Decrementar estoque (se tem product_id)
            if (!empty($item['product_id'])) {
                $this->db->set('case_qty', 'case_qty - ' . (float) $item['quantidade'], false)
                    ->where('product_id', (int) $item['product_id'])
                    ->update('inv_stock');
            }
        }

        // 4. Inserir invoice_payment para cada forma de pagamento
        foreach ($dados['pagamentos'] as $pgto) {
            $payment = [
                'invoice_id'  => $invoice_pk,
                'forma'       => $pgto['forma'],
                'valor'       => (float) $pgto['valor'],
                'troco'       => isset($pgto['troco']) ? (float) $pgto['troco'] : 0,
                'created_at'  => date('Y-m-d H:i:s'),
            ];
            $this->db->insert('invoice_payment', $payment);
        }

        // 5. Inserir ledger_tbl (escrituração contábil)
        $this->db->insert('ledger_tbl', [
            'transaction_id'   => $dados['transaction_id'],
            'transaction_type' => 1,
            'ledger_id'        => 'PDV-' . $dados['transaction_id'],
            'd_c'              => 'd', // Débito no caixa (entrada)
            'amount'           => (float) $dados['total'],
            'invoice_no'       => $dados['transaction_id'],
            'description'      => 'Venda PDV #' . $dados['transaction_id'],
            'payment_type'     => isset($dados['pagamentos'][0]['forma']) ? $dados['pagamentos'][0]['forma'] : 'dinheiro',
            'date'             => date('Y-m-d'),
            'created_by'       => (int) $dados['operador_id'],
            'created_at'       => date('Y-m-d H:i:s'),
        ]);

        // 6. Inserir pdv_caixa_mov tipo 'venda'
        $this->registrar_movimento([
            'caixa_id'        => (int) $dados['caixa_id'],
            'tipo'            => 'venda',
            'valor'           => (float) $dados['total'],
            'forma_pagamento' => $dados['pagamentos'][0]['forma'], // principal
            'invoice_id'      => $invoice_pk,
            'descricao'       => 'Venda #' . $dados['transaction_id'],
            'operador_id'     => (int) $dados['operador_id'],
        ]);

        // 7. Se tem fiado, registrar em pdv_fiado e atualizar customer_id
        $customer_id = isset($dados['customer_id']) ? (int) $dados['customer_id'] : 0;
        foreach ($dados['pagamentos'] as $pgto) {
            if ($pgto['forma'] === 'fiado' && $customer_id > 0) {
                $this->registrarFiado([
                    'customer_id' => $customer_id,
                    'invoice_id'  => $invoice_pk,
                    'valor'       => (float) $pgto['valor'],
                    'operador_id' => (int) $dados['operador_id'],
                ]);

                // Atualizar customer_id na invoice
                $this->db->where('id', $invoice_pk)->update('invoice_tbl', [
                    'customer_id' => $customer_id,
                ]);
            }
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === false) {
            return ['success' => false, 'message' => 'Erro na transação. Venda não gravada.'];
        }

        return [
            'success'        => true,
            'invoice_id'     => $invoice_pk,
            'transaction_id' => $dados['transaction_id'],
            'message'        => 'Venda finalizada com sucesso!',
        ];
    }

    /**
     * Busca dados de uma venda pelo ID (para cupom/reimpressão)
     */
    public function get_venda_completa($invoice_pk)
    {
        $venda = $this->db
            ->select("i.*, CONCAT(u.firstname, ' ', COALESCE(u.lastname, '')) as operador_nome, t.numero as terminal_numero, t.nome as terminal_nome", false)
            ->from('invoice_tbl i')
            ->join('user u', 'u.id = i.created_by', 'left')
            ->join('pdv_terminal t', 't.id = i.terminal_id', 'left')
            ->where('i.id', (int) $invoice_pk)
            ->get()
            ->row();

        if (!$venda) return null;

        // invoice_details.invoice_id is VARCHAR (business ID like "T260309ABC"),
        // not INT FK — match by the business invoice_id
        $venda->itens = $this->db
            ->select('d.*, p.name AS product_name, p.ean_gtin, p.product_code')
            ->from('invoice_details d')
            ->join('product_tbl p', 'p.id = d.product_id', 'left')
            ->where('d.invoice_id', $venda->invoice_id)
            ->get()
            ->result();

        $venda->pagamentos = $this->db
            ->where('invoice_id', (int) $invoice_pk)
            ->get('invoice_payment')
            ->result();

        return $venda;
    }

    // =========================================================================
    // CONTROLE DE CAIXA — Fase 6
    // =========================================================================

    /**
     * Busca usuários com role supervisor ou admin (user_type=1)
     *
     * @return array  Lista de objetos user (id, fullname, password)
     */
    public function get_supervisores()
    {
        // Admins (user_type = 1)
        $admins = $this->db
            ->select("u.id, CONCAT(u.firstname, ' ', COALESCE(u.lastname, '')) AS fullname, u.password_bcrypt AS password", false)
            ->from('user u')
            ->where('u.user_type', 1)
            ->where('u.status', 1)
            ->get()
            ->result();

        // Usuários com role contendo 'supervisor'
        $supervisores = $this->db
            ->select("u.id, CONCAT(u.firstname, ' ', COALESCE(u.lastname, '')) AS fullname, u.password_bcrypt AS password", false)
            ->from('user u')
            ->join('sec_user_role ur', 'ur.user_id = u.id')
            ->join('sec_role r', 'r.id = ur.role_id')
            ->like('r.role_name', 'supervisor', 'both')
            ->where('u.status', 1)
            ->where('u.user_type !=', 1) // evitar duplicata com admins
            ->get()
            ->result();

        return array_merge($admins, $supervisores);
    }

    /**
     * Leitura X — relatório completo do caixa aberto
     *
     * @param int $caixa_id
     * @return array  Dados consolidados do caixa
     */
    public function leituraX($caixa_id)
    {
        $caixa_id = (int) $caixa_id;

        // Dados do caixa
        $caixa = $this->get_caixa($caixa_id);
        $fundo_troco = $caixa ? (float) $caixa->valor_abertura : 0;

        // Total de vendas e quantidade
        $vendas = $this->db
            ->select('COUNT(*) as qtd, COALESCE(SUM(valor), 0) as total', false)
            ->from('pdv_caixa_mov')
            ->where('caixa_id', $caixa_id)
            ->where('tipo', 'venda')
            ->get()
            ->row();

        $total_vendas = $vendas ? (float) $vendas->total : 0;
        $qtd_vendas   = $vendas ? (int) $vendas->qtd : 0;

        // Total por forma de pagamento (das vendas)
        $formas_result = $this->db
            ->select('forma_pagamento, COALESCE(SUM(valor), 0) as total', false)
            ->from('pdv_caixa_mov')
            ->where('caixa_id', $caixa_id)
            ->where('tipo', 'venda')
            ->group_by('forma_pagamento')
            ->get()
            ->result();

        $total_por_forma = [
            'dinheiro' => 0,
            'debito'   => 0,
            'credito'  => 0,
            'pix'      => 0,
            'fiado'    => 0,
        ];
        foreach ($formas_result as $f) {
            $key = $f->forma_pagamento;
            if (array_key_exists($key, $total_por_forma)) {
                $total_por_forma[$key] = (float) $f->total;
            }
        }

        // Sangrias (valores negativos)
        $sangrias_result = $this->db
            ->select('COALESCE(SUM(ABS(valor)), 0) as total, COUNT(*) as qtd', false)
            ->from('pdv_caixa_mov')
            ->where('caixa_id', $caixa_id)
            ->where('tipo', 'sangria')
            ->get()
            ->row();

        $sangrias = $sangrias_result ? (float) $sangrias_result->total : 0;

        // Suprimentos
        $suprimentos_result = $this->db
            ->select('COALESCE(SUM(valor), 0) as total, COUNT(*) as qtd', false)
            ->from('pdv_caixa_mov')
            ->where('caixa_id', $caixa_id)
            ->where('tipo', 'suprimento')
            ->get()
            ->row();

        $suprimentos = $suprimentos_result ? (float) $suprimentos_result->total : 0;

        // Cancelamentos (valores negativos)
        $cancelamentos_result = $this->db
            ->select('COALESCE(SUM(ABS(valor)), 0) as total, COUNT(*) as qtd', false)
            ->from('pdv_caixa_mov')
            ->where('caixa_id', $caixa_id)
            ->where('tipo', 'cancelamento')
            ->get()
            ->row();

        $cancelamentos = $cancelamentos_result ? (float) $cancelamentos_result->total : 0;

        // Saldo estimado em dinheiro = fundo_troco + vendas_dinheiro + suprimentos - sangrias - cancelamentos_dinheiro
        // Para simplificar: saldo = soma de todas as movimentações + fundo de troco
        $saldo_estimado = $this->getSaldoEstimado($caixa_id);

        // Lista de movimentações
        $movimentacoes = $this->db
            ->select("m.*, CONCAT(u.firstname, ' ', COALESCE(u.lastname, '')) as operador_nome", false)
            ->from('pdv_caixa_mov m')
            ->join('user u', 'u.id = m.operador_id', 'left')
            ->where('m.caixa_id', $caixa_id)
            ->order_by('m.created_at', 'ASC')
            ->get()
            ->result();

        $movs_list = [];
        foreach ($movimentacoes as $m) {
            $movs_list[] = [
                'id'              => (int) $m->id,
                'tipo'            => $m->tipo,
                'valor'           => (float) $m->valor,
                'forma_pagamento' => $m->forma_pagamento,
                'descricao'       => $m->descricao,
                'operador_nome'   => $m->operador_nome,
                'created_at'      => $m->created_at,
            ];
        }

        // Dinheiro esperado em caixa = fundo + vendas_dinheiro + suprimentos - sangrias
        $dinheiro_esperado = $fundo_troco + $total_por_forma['dinheiro'] + $suprimentos - $sangrias;

        // Atualizar total_por_forma['dinheiro'] para refletir o dinheiro esperado em caixa
        $total_por_forma['dinheiro'] = round($dinheiro_esperado, 2);

        return [
            'fundo_troco'     => $fundo_troco,
            'total_vendas'    => round($total_vendas, 2),
            'qtd_vendas'      => $qtd_vendas,
            'total_por_forma' => $total_por_forma,
            'sangrias'        => round($sangrias, 2),
            'suprimentos'     => round($suprimentos, 2),
            'cancelamentos'   => round($cancelamentos, 2),
            'saldo_estimado'  => round($saldo_estimado, 2),
            'movimentacoes'   => $movs_list,
            'aberto_em'       => $caixa ? $caixa->aberto_em : null,
            'operador_nome'   => $caixa ? $caixa->operador_nome : null,
        ];
    }

    /**
     * Calcula saldo estimado do caixa (soma de todas as movimentações + fundo de troco)
     *
     * @param int $caixa_id
     * @return float
     */
    public function getSaldoEstimado($caixa_id)
    {
        $caixa_id = (int) $caixa_id;

        // Fundo de troco
        $caixa = $this->db
            ->select('valor_abertura')
            ->from('pdv_caixa')
            ->where('id', $caixa_id)
            ->get()
            ->row();

        $fundo = $caixa ? (float) $caixa->valor_abertura : 0;

        // Soma de todas as movimentações (vendas positivas, sangrias negativas, suprimentos positivos, cancelamentos negativos)
        $movs = $this->db
            ->select('COALESCE(SUM(valor), 0) as total', false)
            ->from('pdv_caixa_mov')
            ->where('caixa_id', $caixa_id)
            ->get()
            ->row();

        $total_movs = $movs ? (float) $movs->total : 0;

        return round($fundo + $total_movs, 2);
    }

    /**
     * Fecha o caixa — atualiza pdv_caixa + insere movimentação final
     *
     * @param array $data [caixa_id, operador_id, valor_contado, diferenca, total_vendas, qtd_vendas, observacao, saldo_estimado]
     * @return bool
     */
    public function fecharCaixa($data)
    {
        $caixa_id = (int) $data['caixa_id'];

        $this->db->trans_start();

        // Atualizar pdv_caixa
        $this->db->where('id', $caixa_id)->update('pdv_caixa', [
            'valor_fechamento' => (float) $data['saldo_estimado'],
            'valor_contado'    => (float) $data['valor_contado'],
            'diferenca'        => (float) $data['diferenca'],
            'fechado_em'       => date('Y-m-d H:i:s'),
            'status'           => 'fechado',
            'total_vendas'     => (float) $data['total_vendas'],
            'qtd_vendas'       => (int) $data['qtd_vendas'],
            'observacao'       => isset($data['observacao']) ? $data['observacao'] : null,
        ]);

        // Inserir movimentação de fechamento
        $this->registrar_movimento([
            'caixa_id'        => $caixa_id,
            'tipo'            => 'fechamento',
            'valor'           => 0,
            'forma_pagamento' => null,
            'invoice_id'      => null,
            'descricao'       => 'Fechamento de caixa. Contado: R$ '
                . number_format((float) $data['valor_contado'], 2, ',', '.')
                . ' | Diferença: R$ '
                . number_format((float) $data['diferenca'], 2, ',', '.'),
            'operador_id'     => (int) $data['operador_id'],
        ]);

        $this->db->trans_complete();

        return $this->db->trans_status();
    }

    /**
     * Cancela uma venda — estorna estoque, atualiza status, insere mov
     * Operação atômica (transação).
     *
     * @param int    $invoice_id     PK da invoice_tbl
     * @param string $motivo         Motivo do cancelamento
     * @param int    $supervisor_id  ID do supervisor que autorizou
     * @param int    $operador_id    ID do operador que executou
     * @return array ['success' => bool, 'message' => string]
     */
    public function cancelarVenda($invoice_id, $motivo, $supervisor_id, $operador_id)
    {
        $invoice_id    = (int) $invoice_id;
        $supervisor_id = (int) $supervisor_id;
        $operador_id   = (int) $operador_id;

        $this->db->trans_start();

        // 1. Buscar invoice e itens
        $invoice = $this->db->where('id', $invoice_id)->get('invoice_tbl')->row();
        if (!$invoice) {
            $this->db->trans_rollback();
            return ['success' => false, 'message' => 'Venda não encontrada.'];
        }

        // invoice_details.invoice_id is VARCHAR business ID
        $itens = $this->db->where('invoice_id', $invoice->invoice_id)->get('invoice_details')->result();

        // 2. Estornar estoque — incrementar de volta
        foreach ($itens as $item) {
            if (!empty($item->product_id)) {
                $this->db->set('case_qty', 'case_qty + ' . (float) $item->quantity, false)
                    ->where('product_id', (int) $item->product_id)
                    ->update('inv_stock');
            }
        }

        // 3. Atualizar invoice_tbl
        $this->db->where('id', $invoice_id)->update('invoice_tbl', [
            'status'      => 0, // 0 = cancelado
            'description' => ($invoice->description ? $invoice->description . ' | ' : '')
                . 'CANCELADO: ' . $motivo
                . ' (Supervisor: ' . $supervisor_id . ')'
                . ' em ' . date('d/m/Y H:i:s'),
        ]);

        // 4. Inserir movimentação de cancelamento (valor negativo)
        if (!empty($invoice->caixa_id)) {
            $this->registrar_movimento([
                'caixa_id'        => (int) $invoice->caixa_id,
                'tipo'            => 'cancelamento',
                'valor'           => -(float) $invoice->total_amount,
                'forma_pagamento' => null,
                'invoice_id'      => $invoice_id,
                'descricao'       => 'Cancelamento venda #' . $invoice->invoice_id . ': ' . $motivo,
                'operador_id'     => $operador_id,
            ]);
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === false) {
            return ['success' => false, 'message' => 'Erro na transação de cancelamento.'];
        }

        return ['success' => true, 'message' => 'Venda cancelada com sucesso.'];
    }

    /**
     * Busca a última venda finalizada de um terminal
     *
     * @param int $terminal_id
     * @return object|null
     */
    public function getUltimaVenda($terminal_id)
    {
        return $this->db
            ->where('terminal_id', (int) $terminal_id)
            ->where('status', 2) // 2 = finalizada
            ->order_by('id', 'DESC')
            ->limit(1)
            ->get('invoice_tbl')
            ->row();
    }

    /**
     * Troca operador do caixa — atualiza pdv_caixa + insere mov
     *
     * @param int $caixa_id
     * @param int $novo_operador_id
     * @param int $operador_anterior_id
     */
    public function trocarOperador($caixa_id, $novo_operador_id, $operador_anterior_id)
    {
        $caixa_id              = (int) $caixa_id;
        $novo_operador_id      = (int) $novo_operador_id;
        $operador_anterior_id  = (int) $operador_anterior_id;

        $this->db->trans_start();

        // Buscar nomes para a descrição
        $anterior = $this->db->select("CONCAT(firstname, ' ', COALESCE(lastname, '')) AS fullname", false)->where('id', $operador_anterior_id)->get('user')->row();
        $novo     = $this->db->select("CONCAT(firstname, ' ', COALESCE(lastname, '')) AS fullname", false)->where('id', $novo_operador_id)->get('user')->row();

        $nome_anterior = $anterior ? $anterior->fullname : 'ID ' . $operador_anterior_id;
        $nome_novo     = $novo ? $novo->fullname : 'ID ' . $novo_operador_id;

        // Atualizar operador do caixa
        $this->db->where('id', $caixa_id)->update('pdv_caixa', [
            'operador_id' => $novo_operador_id,
        ]);

        // Inserir movimentação de troca
        $this->registrar_movimento([
            'caixa_id'        => $caixa_id,
            'tipo'            => 'troca_operador',
            'valor'           => 0,
            'forma_pagamento' => null,
            'invoice_id'      => null,
            'descricao'       => $nome_anterior . ' → ' . $nome_novo,
            'operador_id'     => $novo_operador_id,
        ]);

        $this->db->trans_complete();
    }

    // =========================================================================
    // FIADO / CREDIÁRIO — Fase 8
    // =========================================================================

    /**
     * Busca clientes por CPF, telefone ou nome (para fiado)
     *
     * @param string $termo  Termo de busca
     * @param int    $limit  Máximo de resultados
     * @return array
     */
    public function buscarClienteFiado($termo, $limit = 10)
    {
        $termo = trim($termo);
        if (empty($termo)) {
            return [];
        }

        $clientes = $this->db
            ->select('c.id, c.customerid, c.name, c.mobile, c.cpf, c.limite_credito, c.fiado_bloqueado')
            ->from('customer_tbl c')
            ->group_start()
                ->like('c.name', $termo)
                ->or_like('c.mobile', $termo)
                ->or_like('c.cpf', $termo)
                ->or_like('c.customerid', $termo)
            ->group_end()
            ->where('c.status', 1)
            ->limit((int) $limit)
            ->get()
            ->result();

        // Enriquecer com débito atual
        foreach ($clientes as &$c) {
            $resumo = $this->getResumoCredito((int) $c->id);
            $c->debito_atual = $resumo['debito_atual'];
            $c->limite       = $resumo['limite'];
            $c->disponivel   = $resumo['disponivel'];
        }
        unset($c);

        return $clientes;
    }

    /**
     * Retorna resumo de crédito de um cliente
     *
     * @param int $customer_id  PK do customer_tbl
     * @return array [debito_atual, limite, disponivel]
     */
    public function getResumoCredito($customer_id)
    {
        $customer_id = (int) $customer_id;

        // Limite do cliente (0 = usa padrão do sistema)
        $cliente = $this->db
            ->select('limite_credito, fiado_bloqueado')
            ->where('id', $customer_id)
            ->get('customer_tbl')
            ->row();

        $limite_padrao = (float) (getenv('PDV_FIADO_LIMITE_PADRAO') ?: 500);
        $limite = ($cliente && (float) $cliente->limite_credito > 0)
            ? (float) $cliente->limite_credito
            : $limite_padrao;

        $bloqueado = ($cliente && (int) $cliente->fiado_bloqueado === 1);

        // Débito atual (soma de saldos pendentes + parciais)
        $debito = $this->db
            ->select('COALESCE(SUM(valor - valor_pago), 0) AS total', false)
            ->from('pdv_fiado')
            ->where('customer_id', $customer_id)
            ->where_in('status', ['pendente', 'parcial'])
            ->get()
            ->row();

        $debito_atual = $debito ? (float) $debito->total : 0;
        $disponivel = round($limite - $debito_atual, 2);

        return [
            'debito_atual' => round($debito_atual, 2),
            'limite'       => round($limite, 2),
            'disponivel'   => $disponivel,
            'bloqueado'    => $bloqueado,
        ];
    }

    /**
     * Lista débitos pendentes de um cliente (FIFO — mais antigo primeiro)
     *
     * @param int $customer_id
     * @return array
     */
    public function getDebitosCliente($customer_id)
    {
        return $this->db
            ->select('f.id, f.invoice_id, f.valor, f.valor_pago, (f.valor - f.valor_pago) AS saldo, f.status, f.created_at, i.invoice_id AS numero_cupom')
            ->from('pdv_fiado f')
            ->join('invoice_tbl i', 'i.id = f.invoice_id', 'left')
            ->where('f.customer_id', (int) $customer_id)
            ->where_in('f.status', ['pendente', 'parcial'])
            ->order_by('f.created_at', 'ASC')
            ->get()
            ->result();
    }

    /**
     * Cadastro rápido de cliente (PDV)
     *
     * @param array $dados [nome, telefone, cpf]
     * @return object  Cliente inserido
     */
    public function cadastrarClienteRapido($dados)
    {
        // Gerar customerid
        $this->load->library('generators');
        $customerid = 'C' . date('ymd') . strtoupper($this->generators->generator(4));

        $insert = [
            'customerid'   => $customerid,
            'name'         => $dados['nome'],
            'mobile'       => isset($dados['telefone']) ? $dados['telefone'] : '',
            'cpf'          => isset($dados['cpf']) ? $dados['cpf'] : '',
            'email'        => '',
            'address'      => '',
            'status'       => 1,
            'created_by'   => isset($dados['operador_id']) ? (int) $dados['operador_id'] : null,
            'created_date' => date('Y-m-d H:i:s'),
        ];

        $this->db->insert('customer_tbl', $insert);
        $id = $this->db->insert_id();

        return $this->db->where('id', $id)->get('customer_tbl')->row();
    }

    /**
     * Registra fiado para uma venda
     *
     * @param array $dados [customer_id, invoice_id, valor, operador_id]
     * @return int  ID do pdv_fiado
     */
    public function registrarFiado($dados)
    {
        $insert = [
            'customer_id' => (int) $dados['customer_id'],
            'invoice_id'  => (int) $dados['invoice_id'],
            'valor'       => (float) $dados['valor'],
            'valor_pago'  => 0,
            'status'      => 'pendente',
            'operador_id' => (int) $dados['operador_id'],
        ];

        $this->db->insert('pdv_fiado', $insert);
        return $this->db->insert_id();
    }

    /**
     * Recebe pagamento de fiado (FIFO)
     *
     * Abate o valor nos débitos mais antigos primeiro.
     * Operação atômica (transação).
     *
     * @param int    $customer_id
     * @param float  $valor
     * @param string $forma_pagamento
     * @param int    $caixa_id
     * @param int    $operador_id
     * @return array ['success', 'abatimentos' => [...], 'message']
     */
    public function receberFiado($customer_id, $valor, $forma_pagamento, $caixa_id, $operador_id)
    {
        $customer_id = (int) $customer_id;
        $valor       = round((float) $valor, 2);
        $caixa_id    = (int) $caixa_id;
        $operador_id = (int) $operador_id;

        if ($valor <= 0) {
            return ['success' => false, 'message' => 'Valor deve ser maior que zero.'];
        }

        $this->db->trans_start();

        // Buscar débitos pendentes (FIFO)
        $debitos = $this->getDebitosCliente($customer_id);

        if (empty($debitos)) {
            $this->db->trans_rollback();
            return ['success' => false, 'message' => 'Cliente não possui débitos pendentes.'];
        }

        $restante    = $valor;
        $abatimentos = [];

        foreach ($debitos as $debito) {
            if ($restante <= 0) break;

            $saldo_debito = round((float) $debito->saldo, 2);
            $abater       = min($restante, $saldo_debito);

            // Atualizar pdv_fiado
            $novo_valor_pago = round((float) $debito->valor_pago + $abater, 2);
            $novo_saldo      = round((float) $debito->valor - $novo_valor_pago, 2);
            $novo_status     = ($novo_saldo <= 0.01) ? 'quitado' : 'parcial';

            $update = [
                'valor_pago' => $novo_valor_pago,
                'status'     => $novo_status,
            ];
            if ($novo_status === 'quitado') {
                $update['quitado_em'] = date('Y-m-d H:i:s');
            }

            $this->db->where('id', (int) $debito->id)->update('pdv_fiado', $update);

            // Inserir pdv_fiado_pagamento
            $this->db->insert('pdv_fiado_pagamento', [
                'fiado_id'        => (int) $debito->id,
                'valor'           => $abater,
                'forma_pagamento' => $forma_pagamento,
                'operador_id'     => $operador_id,
                'caixa_id'        => $caixa_id,
                'observacao'      => 'Recebimento via PDV',
            ]);

            $abatimentos[] = [
                'fiado_id'     => (int) $debito->id,
                'numero_cupom' => $debito->numero_cupom,
                'valor_abatido'=> $abater,
                'novo_status'  => $novo_status,
                'saldo_restante' => max(0, $novo_saldo),
            ];

            $restante = round($restante - $abater, 2);
        }

        // Inserir movimentação no caixa
        $this->registrar_movimento([
            'caixa_id'        => $caixa_id,
            'tipo'            => 'recebimento_fiado',
            'valor'           => $valor,
            'forma_pagamento' => $forma_pagamento,
            'invoice_id'      => null,
            'descricao'       => 'Recebimento fiado — Cliente #' . $customer_id,
            'operador_id'     => $operador_id,
        ]);

        $this->db->trans_complete();

        if ($this->db->trans_status() === false) {
            return ['success' => false, 'message' => 'Erro na transação de recebimento.'];
        }

        // Novo resumo
        $resumo = $this->getResumoCredito($customer_id);

        return [
            'success'      => true,
            'abatimentos'  => $abatimentos,
            'valor_recebido' => $valor,
            'debito_restante' => $resumo['debito_atual'],
            'message'      => 'Recebimento de R$ ' . number_format($valor, 2, ',', '.') . ' registrado com sucesso.',
        ];
    }

    /**
     * Busca cliente por ID
     *
     * @param int $id
     * @return object|null
     */
    public function getCliente($id)
    {
        return $this->db
            ->where('id', (int) $id)
            ->get('customer_tbl')
            ->row();
    }

    /**
     * Busca registro de fiado pela invoice (venda) ID
     */
    public function getFiadoByInvoice($invoice_id)
    {
        return $this->db
            ->where('invoice_id', (int) $invoice_id)
            ->order_by('id', 'DESC')
            ->get('pdv_fiado')
            ->row();
    }

    // =========================================================================
    // DEVOLUÇÃO / TROCA (Fase 9)
    // =========================================================================

    /**
     * Busca venda para devolução pelo invoice_id (código textual)
     *
     * @param string $invoice_id  Código da venda (campo invoice_id da invoice_tbl)
     * @return array|null  ['venda' => object, 'itens' => array] ou null
     */
    public function buscarVendaParaDevolucao($invoice_id)
    {
        $venda = $this->db
            ->select("i.id, i.invoice_id, i.total_amount, i.date, i.created_at, i.status,
                      i.created_by, i.terminal_id, i.caixa_id, i.customer_id,
                      CONCAT(u.firstname, ' ', COALESCE(u.lastname, '')) AS operador_nome", false)
            ->from('invoice_tbl i')
            ->join('user u', 'u.id = i.created_by', 'left')
            ->where('i.invoice_id', $invoice_id)
            ->get()
            ->row();

        if (!$venda) {
            return null;
        }

        // invoice_details.invoice_id is VARCHAR business ID
        $itens = $this->db
            ->select('d.id AS detail_id, d.product_id, d.quantity, d.price, d.total_price,
                      p.name AS nome_produto, p.ean_gtin, p.product_code')
            ->from('invoice_details d')
            ->join('product_tbl p', 'p.id = d.product_id', 'left')
            ->where('d.invoice_id', $venda->invoice_id)
            ->get()
            ->result();

        $devolvidos = $this->getItensJaDevolvidos((int) $venda->id);

        foreach ($itens as &$item) {
            $key = (int) $item->product_id . '_' . (int) $item->detail_id;
            $item->ja_devolvido = isset($devolvidos[$key]) ? (float) $devolvidos[$key] : 0;
            $item->max_devolver = max(0, (float) $item->quantity - $item->ja_devolvido);
        }

        return [
            'venda' => $venda,
            'itens' => $itens,
        ];
    }

    /**
     * Retorna quantidades já devolvidas por item de uma venda
     *
     * @param int $invoice_pk  PK da invoice_tbl
     * @return array  ['product_id_detail_id' => qty_devolvida]
     */
    public function getItensJaDevolvidos($invoice_pk)
    {
        $invoice_pk = (int) $invoice_pk;

        $results = $this->db
            ->select('rd.product_id, rd.invoice_detail_id, SUM(rd.return_qty) AS total_devolvido', false)
            ->from('return_details rd')
            ->join('product_return pr', 'pr.return_id = rd.return_id')
            ->where('pr.invoice_id', $invoice_pk)
            ->group_by(['rd.product_id', 'rd.invoice_detail_id'])
            ->get()
            ->result();

        $map = [];
        foreach ($results as $row) {
            $key = (int) $row->product_id . '_' . (int) $row->invoice_detail_id;
            $map[$key] = (float) $row->total_devolvido;
        }

        return $map;
    }

    /**
     * Processa devolução de itens — operação atômica
     *
     * @param array $dados  Dados da devolução
     * @return array ['success' => bool, 'return_pk' => int, 'message' => string]
     */
    public function processarDevolucao($dados)
    {
        $this->db->trans_start();

        $invoice_pk = (int) $dados['invoice_pk'];

        // 1. Inserir product_return (cabeçalho)
        $this->db->insert('product_return', [
            'return_id'        => $dados['return_id'],
            'invoice_id'       => $invoice_pk,
            'customer_id'      => isset($dados['customer_id']) ? $dados['customer_id'] : null,
            'return_date'      => date('Y-m-d'),
            'deduction'        => 0,
            'invoice_discount' => 0,
            'total_amount'     => (float) $dados['total'],
            'reason'           => $dados['motivo_geral'],
            'paymet_type'      => 0,
            'status'           => 1,
            'created_by'       => (int) $dados['operador_id'],
            'created_at'       => date('Y-m-d H:i:s'),
        ]);
        $return_pk = $this->db->insert_id();

        if (!$return_pk) {
            $this->db->trans_rollback();
            return ['success' => false, 'message' => 'Erro ao inserir registro de devolução.'];
        }

        // 2. Inserir return_details e reverter estoque
        foreach ($dados['itens'] as $item) {
            $product_id = (int) $item['product_id'];
            $qty        = (float) $item['qty'];
            $preco      = (float) $item['preco'];
            $detail_id  = (int) $item['detail_id'];

            // Validar qty no servidor
            $devolvidos = $this->getItensJaDevolvidos($invoice_pk);
            $key = $product_id . '_' . $detail_id;
            $ja_devolvido = isset($devolvidos[$key]) ? (float) $devolvidos[$key] : 0;

            $detail_row = $this->db->where('id', $detail_id)->get('invoice_details')->row();
            if (!$detail_row) {
                $this->db->trans_rollback();
                return ['success' => false, 'message' => 'Item não encontrado na venda original.'];
            }

            $max_devolver = (float) $detail_row->quantity - $ja_devolvido;
            if ($qty > $max_devolver + 0.001) {
                $this->db->trans_rollback();
                return [
                    'success' => false,
                    'message' => 'Quantidade excede o permitido para "'
                        . ($item['product_name'] ?? 'item')
                        . '" (máx: ' . $max_devolver . ').',
                ];
            }

            $this->db->insert('return_details', [
                'return_id'         => $dados['return_id'],
                'product_id'        => $product_id,
                'invoice_detail_id' => $detail_id,
                'return_qty'        => $qty,
                'sold_pur_qty'      => (float) $detail_row->quantity,
                'price'             => $preco,
                'amount'            => round($qty * $preco, 2),
                'status'            => 1,
                'motivo'            => isset($item['motivo']) ? $item['motivo'] : null,
            ]);

            // Reverter estoque
            if ($product_id > 0) {
                $this->db->set('case_qty', 'case_qty + ' . $qty, false)
                    ->where('product_id', $product_id)
                    ->update('inv_stock');
            }
        }

        // 3. Inserir pdv_caixa_mov tipo 'devolucao' (valor negativo)
        $this->registrar_movimento([
            'caixa_id'        => (int) $dados['caixa_id'],
            'tipo'            => 'devolucao',
            'valor'           => -(float) $dados['total'],
            'forma_pagamento' => null,
            'invoice_id'      => $invoice_pk,
            'descricao'       => 'Devolução ref. venda #' . $dados['invoice_id']
                . ' — ' . $dados['motivo_geral'],
            'operador_id'     => (int) $dados['operador_id'],
            'supervisor_id'   => isset($dados['supervisor_id']) ? (int) $dados['supervisor_id'] : null,
        ]);

        $this->db->trans_complete();

        if ($this->db->trans_status() === false) {
            return ['success' => false, 'message' => 'Erro na transação de devolução.'];
        }

        return [
            'success'   => true,
            'return_pk' => $return_pk,
            'return_id' => $dados['return_id'],
            'message'   => 'Devolução processada com sucesso!',
        ];
    }

    /**
     * Busca dados de uma devolução pelo PK (para comprovante)
     *
     * @param int $return_pk
     * @return object|null
     */
    public function getDevolucao($return_pk)
    {
        $devolucao = $this->db
            ->select("pr.*, CONCAT(u.firstname, ' ', COALESCE(u.lastname, '')) AS operador_nome, i.invoice_id AS venda_codigo", false)
            ->from('product_return pr')
            ->join('user u', 'u.id = pr.created_by', 'left')
            ->join('invoice_tbl i', 'i.id = pr.invoice_id', 'left')
            ->where('pr.id', (int) $return_pk)
            ->get()
            ->row();

        if (!$devolucao) return null;

        $devolucao->itens = $this->db
            ->select('rd.*, p.name AS nome_produto, p.ean_gtin, p.product_code')
            ->from('return_details rd')
            ->join('product_tbl p', 'p.id = rd.product_id', 'left')
            ->where('rd.return_id', $devolucao->return_id)
            ->get()
            ->result();

        return $devolucao;
    }

    // =========================================================================
    // AUDIT LOG
    // =========================================================================

    /**
     * Registra ação no log de auditoria
     *
     * @param array $data [terminal_id, caixa_id, operador_id, acao, entidade, entidade_id, detalhes, ip]
     */
    public function registrar_audit($data)
    {
        $insert = [
            'terminal_id' => (int) $data['terminal_id'],
            'caixa_id'    => isset($data['caixa_id']) ? (int) $data['caixa_id'] : null,
            'operador_id' => (int) $data['operador_id'],
            'acao'        => $data['acao'],
            'entidade'    => isset($data['entidade']) ? $data['entidade'] : null,
            'entidade_id' => isset($data['entidade_id']) ? (int) $data['entidade_id'] : null,
            'detalhes'    => isset($data['detalhes']) ? json_encode($data['detalhes']) : null,
            'ip'          => $data['ip'],
        ];

        $this->db->insert('pdv_audit_log', $insert);
        return $this->db->insert_id();
    }

    // =========================================================================
    // AUDIT LOG — CONSULTA (Fase 10)
    // =========================================================================

    /**
     * Lista audit log com server-side DataTables
     *
     * @param array $params  DataTable params + filters
     * @return array  [draw, iTotalRecords, iTotalDisplayRecords, aaData]
     */
    public function listarAuditLog($params)
    {
        $draw   = isset($params['draw']) ? (int) $params['draw'] : 1;
        $start  = isset($params['start']) ? (int) $params['start'] : 0;
        $length = isset($params['length']) ? (int) $params['length'] : 25;
        $search = isset($params['search']) ? trim($params['search']) : '';

        // Ordenação
        $order_col_map = [
            0 => 'a.created_at',
            1 => 't.numero',
            2 => 'u.firstname',
            3 => 'a.acao',
            4 => 'a.entidade',
            5 => 'a.detalhes',
            6 => 'a.ip',
        ];
        $order_col = isset($params['order_col']) && isset($order_col_map[$params['order_col']])
            ? $order_col_map[$params['order_col']]
            : 'a.created_at';
        $order_dir = (isset($params['order_dir']) && strtolower($params['order_dir']) === 'asc') ? 'ASC' : 'DESC';

        // Filtros parsed
        $filtros = [
            'terminal_id' => isset($params['terminal_id']) ? (int) $params['terminal_id'] : 0,
            'operador_id' => isset($params['operador_id']) ? (int) $params['operador_id'] : 0,
            'acao'         => isset($params['acao']) ? trim($params['acao']) : '',
            'data_inicio'  => isset($params['data_inicio']) ? trim($params['data_inicio']) : '',
            'data_fim'     => isset($params['data_fim']) ? trim($params['data_fim']) : '',
            'search'       => $search,
        ];

        // Total de registros (sem filtro)
        $totalRecords = $this->db->count_all('pdv_audit_log');

        // Total filtrado
        $this->_auditLogBaseQuery($filtros);
        $totalFiltered = $this->db->count_all_results();

        // Dados paginados
        $this->_auditLogBaseQuery($filtros, true);
        $this->db->order_by($order_col, $order_dir);
        $this->db->limit($length, $start);
        $results = $this->db->get()->result();

        return [
            'draw'                 => $draw,
            'iTotalRecords'        => $totalRecords,
            'iTotalDisplayRecords' => $totalFiltered,
            'aaData'               => $results,
        ];
    }

    /**
     * Monta query base do audit log com filtros (helper reutilizável)
     *
     * @param array $filtros  Filtros a aplicar
     * @param bool  $select   Se true, inclui SELECT columns; se false, apenas FROM + WHERE (para count)
     */
    private function _auditLogBaseQuery($filtros, $select = false)
    {
        if ($select) {
            $this->db->select("a.id, a.created_at, a.acao, a.entidade, a.entidade_id, a.detalhes, a.ip,
                      t.numero AS terminal_numero, t.nome AS terminal_nome,
                      CONCAT(u.firstname, ' ', COALESCE(u.lastname, '')) AS operador_nome", false);
        }

        $this->db
            ->from('pdv_audit_log a')
            ->join('pdv_terminal t', 't.id = a.terminal_id', 'left')
            ->join('user u', 'u.id = a.operador_id', 'left');

        if ($filtros['terminal_id'] > 0) {
            $this->db->where('a.terminal_id', $filtros['terminal_id']);
        }
        if ($filtros['operador_id'] > 0) {
            $this->db->where('a.operador_id', $filtros['operador_id']);
        }
        if (!empty($filtros['acao'])) {
            $this->db->where('a.acao', $filtros['acao']);
        }
        if (!empty($filtros['data_inicio'])) {
            $this->db->where('a.created_at >=', $filtros['data_inicio'] . ' 00:00:00');
        }
        if (!empty($filtros['data_fim'])) {
            $this->db->where('a.created_at <=', $filtros['data_fim'] . ' 23:59:59');
        }
        if (!empty($filtros['search'])) {
            $this->db->group_start()
                ->like('a.acao', $filtros['search'])
                ->or_like('u.firstname', $filtros['search'])
                ->or_like('a.entidade', $filtros['search'])
                ->or_like('a.detalhes', $filtros['search'])
                ->or_like('a.ip', $filtros['search'])
                ->or_like('t.numero', $filtros['search'])
            ->group_end();
        }
    }

    /**
     * Lista terminais ativos (para dropdown de filtro)
     *
     * @return array
     */
    public function listarTerminais()
    {
        return $this->db
            ->select('id, numero, nome')
            ->from('pdv_terminal')
            ->where('ativo', 1)
            ->order_by('numero', 'ASC')
            ->get()
            ->result();
    }

    /**
     * Lista operadores que já aparecem no audit log (para dropdown de filtro)
     *
     * @return array
     */
    public function listarOperadoresAudit()
    {
        return $this->db
            ->select("DISTINCT(a.operador_id) AS id, CONCAT(u.firstname, ' ', COALESCE(u.lastname, '')) AS nome", false)
            ->from('pdv_audit_log a')
            ->join('user u', 'u.id = a.operador_id')
            ->order_by('u.firstname', 'ASC')
            ->get()
            ->result();
    }

    /**
     * Lista ações distintas no audit log (para dropdown de filtro)
     *
     * @return array
     */
    public function listarAcoesAudit()
    {
        return $this->db
            ->select('DISTINCT(acao) AS acao', false)
            ->from('pdv_audit_log')
            ->order_by('acao', 'ASC')
            ->get()
            ->result();
    }

    // =========================================================================
    // AUTO-FECHAMENTO (Cron)
    // =========================================================================

    /**
     * Retorna caixas abertos há mais de N horas
     *
     * @param int $hours  Número de horas
     * @return array
     */
    public function getCaixasAbertosExpirados($hours = 24)
    {
        $hours  = (int) $hours;
        $cutoff = date('Y-m-d H:i:s', strtotime("-{$hours} hours"));

        return $this->db
            ->select('c.*, t.numero as terminal_numero')
            ->from('pdv_caixa c')
            ->join('pdv_terminal t', 't.id = c.terminal_id')
            ->where('c.status', 'aberto')
            ->where('c.aberto_em <', $cutoff)
            ->get()
            ->result();
    }
}
