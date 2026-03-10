<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Pdv Controller — Frente de Caixa (Terminal PDV)
 *
 * Fluxo do operador:
 *   /pdv/terminal/001 → login (se não autenticado) → abertura de caixa → frente de caixa
 *
 * NÃO usa template/layout do admin — telas fullscreen independentes.
 * Autenticação própria via matrícula + senha (não admin session).
 */
class Pdv extends MX_Controller {

    /** Métodos que não exigem sessão PDV */
    private $public_methods = ['login', 'autenticar', 'display', 'auto_fechamento'];

    /** Máximo de tentativas de login antes de bloquear */
    const MAX_LOGIN_ATTEMPTS = 5;

    /** Minutos de bloqueio após atingir limite */
    const LOGIN_BLOCK_MINUTES = 15;

    public function __construct()
    {
        parent::__construct();
        $this->load->model(['Pdv_model']);
        $this->load->library('form_validation');

        // Métodos públicos (login, autenticar) não exigem sessão PDV
        $method = $this->router->fetch_method();
        if (!in_array($method, $this->public_methods)) {
            $this->_require_pdv_session();
        }
    }

    // =========================================================================
    // ENTRY POINT: /pdv/terminal/{numero}
    // =========================================================================

    /**
     * Ponto de entrada do terminal PDV
     *
     * @param string $terminal_numero  Número do terminal (ex: '001')
     */
    public function index($terminal_numero = null)
    {
        if (empty($terminal_numero)) {
            show_404();
            return;
        }

        // Valida terminal
        $terminal = $this->Pdv_model->get_terminal_by_numero($terminal_numero);
        if (!$terminal) {
            show_404();
            return;
        }

        // Atualiza terminal na sessão (caso operador navegue entre terminais)
        $this->session->set_userdata([
            'pdv_terminal_id'     => $terminal->id,
            'pdv_terminal_numero' => $terminal->numero,
            'pdv_terminal_nome'   => $terminal->nome,
        ]);

        // Verifica se tem caixa aberto neste terminal
        $this->load->library('Caixa');
        $caixa_aberto = $this->caixa->get_caixa_aberto($terminal->id);

        if (!$caixa_aberto) {
            // Sem caixa aberto → tela de abertura
            redirect('pdv/abrir_caixa');
            return;
        }

        // Verifica se o operador logado é o dono do caixa aberto
        $operador_id = $this->session->userdata('pdv_operador_id');
        if ((int) $caixa_aberto->operador_id !== (int) $operador_id) {
            $this->session->set_flashdata('exception',
                'Este caixa está aberto por ' . html_escape($caixa_aberto->operador_nome)
                . '. Solicite o fechamento ou troca de operador.'
            );
            redirect('pdv/abrir_caixa');
            return;
        }

        // Atualiza caixa_id na sessão
        $this->session->set_userdata('pdv_caixa_id', $caixa_aberto->id);

        // Caixa aberto e operador correto → Frente de caixa (placeholder Fase 3)
        $data = [
            'terminal'  => $terminal,
            'caixa'     => $caixa_aberto,
            'operador'  => (object) [
                'id'        => $this->session->userdata('pdv_operador_id'),
                'nome'      => $this->session->userdata('pdv_operador_nome'),
                'matricula' => $this->session->userdata('pdv_operador_matricula'),
            ],
            'setting'   => $this->_get_setting(),
        ];

        $this->load->view('frente_caixa', $data);
    }

    // =========================================================================
    // LOGIN DO OPERADOR
    // =========================================================================

    /**
     * Tela de login do operador PDV
     *
     * @param string $terminal_numero  Número do terminal (via URL)
     */
    public function login($terminal_numero = null)
    {
        // Se já logado no PDV, redireciona para o terminal
        if ($this->session->userdata('pdv_logado') === true) {
            $num = $this->session->userdata('pdv_terminal_numero');
            if ($num) {
                redirect('pdv/terminal/' . $num);
                return;
            }
        }

        // Valida terminal
        $terminal = null;
        if (!empty($terminal_numero)) {
            $terminal = $this->Pdv_model->get_terminal_by_numero($terminal_numero);
        }

        if (!$terminal) {
            show_404();
            return;
        }

        $data = [
            'terminal' => $terminal,
            'setting'  => $this->_get_setting(),
        ];

        $this->load->view('login', $data);
    }

    /**
     * Processa autenticação do operador (POST)
     *
     * Valida matrícula + senha, verifica permissão PDV, cria sessão.
     * Rate limiting: 5 tentativas / 15 minutos.
     */
    public function autenticar()
    {
        // Apenas POST
        if ($this->input->method() !== 'post') {
            show_404();
            return;
        }

        $terminal_numero = $this->input->post('terminal_numero', true);
        $matricula       = trim($this->input->post('matricula', true));
        $senha           = $this->input->post('senha');

        // Valida terminal
        $terminal = null;
        if (!empty($terminal_numero)) {
            $terminal = $this->Pdv_model->get_terminal_by_numero($terminal_numero);
        }

        if (!$terminal) {
            $this->session->set_flashdata('exception', 'Terminal inválido.');
            redirect('pdv/login/' . html_escape($terminal_numero));
            return;
        }

        $redirect_login = 'pdv/login/' . $terminal->numero;
        $ip = $this->input->ip_address();

        // Rate limiting
        $attempts = $this->Pdv_model->count_login_attempts($matricula, $ip, self::LOGIN_BLOCK_MINUTES);
        if ($attempts >= self::MAX_LOGIN_ATTEMPTS) {
            $this->session->set_flashdata('exception',
                'Muitas tentativas. Aguarde ' . self::LOGIN_BLOCK_MINUTES . ' minutos.'
            );
            redirect($redirect_login);
            return;
        }

        // Validação básica
        if (empty($matricula) || empty($senha)) {
            $this->session->set_flashdata('exception', 'Informe matrícula e senha.');
            redirect($redirect_login);
            return;
        }

        // Busca usuário por matrícula (ou email como fallback)
        $user = $this->Pdv_model->get_user_by_matricula($matricula);
        if (!$user) {
            $user = $this->Pdv_model->get_user_by_email($matricula);
        }

        if (!$user) {
            $this->Pdv_model->record_login_attempt($matricula, $ip);
            $this->session->set_flashdata('exception', 'Matrícula ou senha inválida.');
            redirect($redirect_login);
            return;
        }

        // Verifica senha (bcrypt)
        if (!password_verify($senha, $user->password)) {
            $this->Pdv_model->record_login_attempt($matricula, $ip);

            // Audit: tentativa falhada
            $this->Pdv_model->registrar_audit([
                'terminal_id' => $terminal->id,
                'caixa_id'    => null,
                'operador_id' => $user->id,
                'acao'        => 'login_falha',
                'entidade'    => 'user',
                'entidade_id' => $user->id,
                'detalhes'    => ['matricula' => $matricula, 'motivo' => 'senha_invalida'],
                'ip'          => $ip,
            ]);

            $this->session->set_flashdata('exception', 'Matrícula ou senha inválida.');
            redirect($redirect_login);
            return;
        }

        // Verifica permissão PDV
        if (!$this->Pdv_model->user_has_pdv_permission($user->id)) {
            $this->Pdv_model->record_login_attempt($matricula, $ip);

            $this->Pdv_model->registrar_audit([
                'terminal_id' => $terminal->id,
                'caixa_id'    => null,
                'operador_id' => $user->id,
                'acao'        => 'login_falha',
                'entidade'    => 'user',
                'entidade_id' => $user->id,
                'detalhes'    => ['matricula' => $matricula, 'motivo' => 'sem_permissao_pdv'],
                'ip'          => $ip,
            ]);

            $this->session->set_flashdata('exception', 'Usuário sem permissão para operar o PDV.');
            redirect($redirect_login);
            return;
        }

        // Login bem-sucedido — criar sessão PDV
        $this->Pdv_model->clear_login_attempts($matricula, $ip);

        $this->session->set_userdata([
            'pdv_logado'             => true,
            'pdv_operador_id'        => $user->id,
            'pdv_operador_nome'      => $user->fullname,
            'pdv_operador_matricula' => $user->matricula ?: $user->email,
            'pdv_operador_image'     => $user->image,
            'pdv_operador_is_admin'  => ($user->user_type == 1),
            'pdv_terminal_id'        => $terminal->id,
            'pdv_terminal_numero'    => $terminal->numero,
            'pdv_terminal_nome'      => $terminal->nome,
            'pdv_login_at'           => date('Y-m-d H:i:s'),
        ]);

        // Regenera session ID para prevenir session fixation
        $this->session->sess_regenerate(true);

        // Audit: login bem-sucedido
        $this->Pdv_model->registrar_audit([
            'terminal_id' => $terminal->id,
            'caixa_id'    => null,
            'operador_id' => $user->id,
            'acao'        => 'login',
            'entidade'    => 'user',
            'entidade_id' => $user->id,
            'detalhes'    => ['matricula' => $matricula, 'terminal' => $terminal->numero],
            'ip'          => $ip,
        ]);

        redirect('pdv/terminal/' . $terminal->numero);
    }

    /**
     * Logout do operador PDV
     */
    public function logout()
    {
        $terminal_numero = $this->session->userdata('pdv_terminal_numero');
        $operador_id     = $this->session->userdata('pdv_operador_id');
        $terminal_id     = $this->session->userdata('pdv_terminal_id');

        // Audit: logout
        if ($operador_id && $terminal_id) {
            $this->Pdv_model->registrar_audit([
                'terminal_id' => $terminal_id,
                'caixa_id'    => $this->session->userdata('pdv_caixa_id'),
                'operador_id' => $operador_id,
                'acao'        => 'logout',
                'entidade'    => 'user',
                'entidade_id' => $operador_id,
                'ip'          => $this->input->ip_address(),
            ]);
        }

        // Limpa apenas dados PDV da sessão (preserva admin session se existir)
        $pdv_keys = [
            'pdv_logado', 'pdv_operador_id', 'pdv_operador_nome',
            'pdv_operador_matricula', 'pdv_operador_image', 'pdv_operador_is_admin',
            'pdv_terminal_id', 'pdv_terminal_numero', 'pdv_terminal_nome',
            'pdv_caixa_id', 'pdv_login_at',
        ];
        $this->session->unset_userdata($pdv_keys);

        if ($terminal_numero) {
            redirect('pdv/login/' . $terminal_numero);
        } else {
            redirect('pdv/login/001');
        }
    }

    // =========================================================================
    // ABERTURA DE CAIXA
    // =========================================================================

    /**
     * Tela/processamento de abertura de caixa
     *
     * GET  → mostra formulário
     * POST → processa abertura
     */
    public function abrir_caixa()
    {
        $terminal_id = $this->session->userdata('pdv_terminal_id');
        $terminal    = $this->Pdv_model->get_terminal((int) $terminal_id);

        if (!$terminal) {
            redirect('pdv/login/001');
            return;
        }

        $this->load->library('Caixa');

        // Verifica se já existe caixa aberto
        $caixa_aberto = $this->caixa->get_caixa_aberto($terminal->id);
        if ($caixa_aberto) {
            $operador_id = $this->session->userdata('pdv_operador_id');
            if ((int) $caixa_aberto->operador_id === (int) $operador_id) {
                // Caixa aberto pelo próprio operador → vai direto para frente
                $this->session->set_userdata('pdv_caixa_id', $caixa_aberto->id);
                redirect('pdv/terminal/' . $terminal->numero);
                return;
            }
            // Caixa aberto por outro operador
            $this->session->set_flashdata('exception',
                'Caixa já aberto por ' . html_escape($caixa_aberto->operador_nome)
                . ' desde ' . date('H:i', strtotime($caixa_aberto->aberto_em)) . '.'
            );
        }

        // POST — processar abertura
        if ($this->input->method() === 'post') {
            return $this->_processar_abertura($terminal);
        }

        // GET — mostrar formulário
        $data = [
            'terminal' => $terminal,
            'operador' => (object) [
                'id'        => $this->session->userdata('pdv_operador_id'),
                'nome'      => $this->session->userdata('pdv_operador_nome'),
                'matricula' => $this->session->userdata('pdv_operador_matricula'),
            ],
            'setting'  => $this->_get_setting(),
        ];

        $this->load->view('abertura_caixa', $data);
    }

    /**
     * Processa POST de abertura de caixa
     */
    private function _processar_abertura($terminal)
    {
        $this->form_validation->set_rules('valor_abertura', 'Fundo de Troco', 'required|numeric|greater_than_equal_to[0]');

        if (!$this->form_validation->run()) {
            $this->session->set_flashdata('exception', validation_errors());
            redirect('pdv/abrir_caixa');
            return;
        }

        // Converte valor BR (200,00) para float (200.00)
        $valor_raw = $this->input->post('valor_abertura', true);
        $valor_abertura = (float) str_replace(['.', ','], ['', '.'], $valor_raw);
        $observacao = $this->input->post('observacao', true);

        $operador_id = $this->session->userdata('pdv_operador_id');

        $this->load->library('Caixa');
        $result = $this->caixa->abrir($terminal->id, $operador_id, $valor_abertura, $observacao);

        if (!$result['success']) {
            $this->session->set_flashdata('exception', $result['message']);
            redirect('pdv/abrir_caixa');
            return;
        }

        // Atualiza sessão com caixa_id
        $this->session->set_userdata('pdv_caixa_id', $result['caixa_id']);

        $this->session->set_flashdata('message', $result['message']);
        redirect('pdv/terminal/' . $terminal->numero);
    }

    // =========================================================================
    // BUSCA DE PRODUTO (AJAX)
    // =========================================================================

    /**
     * Busca produto por código de barras / EAN / product_code
     * AJAX GET: /pdv/buscar_produto?codigo=7891234567890
     */
    public function buscar_produto()
    {
        $codigo = trim($this->input->get('codigo', true));

        if (empty($codigo) || strlen($codigo) < 3) {
            return $this->_json(['found' => false, 'message' => 'Código deve ter no mínimo 3 caracteres']);
        }

        // Detectar barcode de balança (prefixo 2, 13 dígitos)
        if (strlen($codigo) === 13 && $codigo[0] === '2') {
            return $this->_buscar_produto_balanca($codigo);
        }

        $produto = $this->Pdv_model->buscar_produto_por_codigo($codigo);

        if (!$produto) {
            return $this->_json(['found' => false, 'message' => 'Produto não encontrado']);
        }

        $permitir_sem_estoque = (getenv('PDV_PERMITIR_VENDA_SEM_ESTOQUE') ?: 'false') === 'true';
        $estoque  = (float) $produto->estoque_disponivel;
        $minimo   = (int) $produto->estoque_minimo;

        return $this->_json([
            'found'              => true,
            'id'                 => (int) $produto->id,
            'nome'               => $produto->nome,
            'preco'              => (float) $produto->preco,
            'product_code'       => $produto->product_code,
            'ean'                => $produto->ean_gtin,
            'unidade'            => $produto->unidade ?: 'UN',
            'estoque_disponivel' => $estoque,
            'estoque_minimo'     => $minimo,
            'estoque_baixo'      => ($minimo > 0 && $estoque > 0 && $estoque <= $minimo),
            'sem_estoque'        => ($estoque <= 0),
            'pesavel'            => (int) $produto->pesavel,
        ]);
    }

    /**
     * Processa barcode de balança (prefixo 2, 13 dígitos)
     * Formato: 2CCCCCPPPPPD onde C=código produto, P=peso ou preço, D=dígito verificador
     */
    private function _buscar_produto_balanca($barcode)
    {
        $codigo_balanca = substr($barcode, 1, 5);
        $valor_raw      = substr($barcode, 6, 5);
        $valor_numerico  = (int) $valor_raw;

        $produto = $this->Pdv_model->buscar_produto_por_balanca($codigo_balanca);

        if (!$produto) {
            return $this->_json(['found' => false, 'message' => 'Produto de balança não encontrado']);
        }

        $quantidade = 1;
        $preco = (float) $produto->preco;

        if ($produto->tipo_barcode_balanca === 'peso') {
            // Peso em gramas (ex: 01250 = 1,250 KG)
            $quantidade = $valor_numerico / 1000;
        } else {
            // Preço em centavos (ex: 01299 = R$ 12,99)
            $preco = $valor_numerico / 100;
        }

        $estoque = (float) $produto->estoque_disponivel;
        $minimo  = (int) $produto->estoque_minimo;

        return $this->_json([
            'found'              => true,
            'id'                 => (int) $produto->id,
            'nome'               => $produto->nome,
            'preco'              => $preco,
            'product_code'       => $produto->product_code,
            'ean'                => $produto->ean_gtin,
            'unidade'            => 'KG',
            'estoque_disponivel' => $estoque,
            'estoque_minimo'     => $minimo,
            'estoque_baixo'      => ($minimo > 0 && $estoque > 0 && $estoque <= $minimo),
            'sem_estoque'        => ($estoque <= 0),
            'pesavel'            => 1,
            'quantidade_balanca' => round($quantidade, 3),
            'barcode_balanca'    => true,
        ]);
    }

    /**
     * Busca produto por nome (modal F5)
     * AJAX GET: /pdv/buscar_produto_nome?termo=coca
     */
    public function buscar_produto_nome()
    {
        $termo = trim($this->input->get('termo', true));

        if (empty($termo) || strlen($termo) < 2) {
            return $this->_json(['produtos' => []]);
        }

        $produtos = $this->Pdv_model->buscar_produto_por_nome($termo);

        $resultado = [];
        foreach ($produtos as $p) {
            $estoque = (float) $p->estoque_disponivel;
            $minimo  = (int) $p->estoque_minimo;
            $resultado[] = [
                'id'                 => (int) $p->id,
                'nome'               => $p->nome,
                'preco'              => (float) $p->preco,
                'product_code'       => $p->product_code,
                'ean'                => $p->ean_gtin,
                'unidade'            => $p->unidade ?: 'UN',
                'estoque_disponivel' => $estoque,
                'estoque_minimo'     => $minimo,
                'estoque_baixo'      => ($minimo > 0 && $estoque > 0 && $estoque <= $minimo),
                'sem_estoque'        => ($estoque <= 0),
                'pesavel'            => (int) $p->pesavel,
            ];
        }

        return $this->_json(['produtos' => $resultado]);
    }

    /**
     * Consulta de preço (tecla C) — mesmo que buscar_produto mas registra audit
     * AJAX GET: /pdv/consultar_preco?codigo=xxx
     */
    public function consultar_preco()
    {
        $codigo = trim($this->input->get('codigo', true));

        if (empty($codigo) || strlen($codigo) < 3) {
            return $this->_json(['found' => false, 'message' => 'Código deve ter no mínimo 3 caracteres']);
        }

        $produto = $this->Pdv_model->buscar_produto_por_codigo($codigo);

        if (!$produto) {
            return $this->_json(['found' => false, 'message' => 'Produto não encontrado']);
        }

        // Audit log
        $this->Pdv_model->registrar_audit([
            'terminal_id' => $this->session->userdata('pdv_terminal_id'),
            'caixa_id'    => $this->session->userdata('pdv_caixa_id'),
            'operador_id' => $this->session->userdata('pdv_operador_id'),
            'acao'        => 'consulta_preco',
            'entidade'    => 'product_tbl',
            'entidade_id' => $produto->id,
            'detalhes'    => ['codigo' => $codigo, 'nome' => $produto->nome, 'preco' => $produto->preco],
            'ip'          => $this->input->ip_address(),
        ]);

        return $this->_json([
            'found'              => true,
            'id'                 => (int) $produto->id,
            'nome'               => $produto->nome,
            'preco'              => (float) $produto->preco,
            'product_code'       => $produto->product_code,
            'ean'                => $produto->ean_gtin,
            'unidade'            => $produto->unidade ?: 'UN',
            'estoque_disponivel' => (float) $produto->estoque_disponivel,
        ]);
    }

    /**
     * Lista categorias para modal produto genérico
     * AJAX GET: /pdv/get_categorias
     */
    public function get_categorias()
    {
        $categorias = $this->Pdv_model->get_categorias();
        return $this->_json(['categorias' => $categorias]);
    }

    // =========================================================================
    // VENDA SUSPENSA (F12)
    // =========================================================================

    /**
     * Suspender venda atual
     * AJAX POST: /pdv/suspender_venda
     */
    public function suspender_venda()
    {
        if ($this->input->method() !== 'post') {
            return $this->_json(['success' => false, 'message' => 'Método inválido'], 405);
        }

        $terminal_id = $this->session->userdata('pdv_terminal_id');
        $caixa_id    = $this->session->userdata('pdv_caixa_id');
        $operador_id = $this->session->userdata('pdv_operador_id');

        // Verificar limite
        $max = (int) (getenv('PDV_SUSPENSA_MAX_POR_TERMINAL') ?: 3);
        $count = $this->Pdv_model->contar_vendas_suspensas($terminal_id);
        if ($count >= $max) {
            return $this->_json([
                'success' => false,
                'message' => "Limite de {$max} vendas suspensas atingido neste terminal.",
            ]);
        }

        $itens_json = $this->input->post('itens');
        $itens = json_decode($itens_json, true);
        if (empty($itens)) {
            return $this->_json(['success' => false, 'message' => 'Nenhum item para suspender.']);
        }

        $total       = (float) $this->input->post('total');
        $motivo      = $this->input->post('motivo', true);
        $cpf_cliente = $this->input->post('cpf_cliente', true);

        $id = $this->Pdv_model->suspender_venda([
            'terminal_id'  => $terminal_id,
            'caixa_id'     => $caixa_id,
            'operador_id'  => $operador_id,
            'itens'        => $itens,
            'total'        => $total,
            'motivo'       => $motivo,
            'cpf_cliente'  => $cpf_cliente,
        ]);

        // Audit
        $this->Pdv_model->registrar_audit([
            'terminal_id' => $terminal_id,
            'caixa_id'    => $caixa_id,
            'operador_id' => $operador_id,
            'acao'        => 'venda_suspensa',
            'entidade'    => 'pdv_venda_suspensa',
            'entidade_id' => $id,
            'detalhes'    => ['total' => $total, 'qtd_itens' => count($itens), 'motivo' => $motivo],
            'ip'          => $this->input->ip_address(),
        ]);

        return $this->_json(['success' => true, 'id' => $id, 'message' => 'Venda suspensa com sucesso.']);
    }

    /**
     * Listar vendas suspensas do terminal
     * AJAX GET: /pdv/listar_suspensas
     */
    public function listar_suspensas()
    {
        $terminal_id = $this->session->userdata('pdv_terminal_id');
        $suspensas = $this->Pdv_model->listar_vendas_suspensas($terminal_id);

        $resultado = [];
        foreach ($suspensas as $s) {
            $resultado[] = [
                'id'             => (int) $s->id,
                'operador_nome'  => $s->operador_nome,
                'total'          => (float) $s->total,
                'itens'          => json_decode($s->itens, true),
                'motivo'         => $s->motivo,
                'suspensa_em'    => $s->suspensa_em,
                'expires_at'     => $s->expires_at,
            ];
        }

        return $this->_json(['suspensas' => $resultado]);
    }

    /**
     * Recuperar venda suspensa
     * AJAX POST: /pdv/recuperar_venda
     */
    public function recuperar_venda()
    {
        if ($this->input->method() !== 'post') {
            return $this->_json(['success' => false, 'message' => 'Método inválido'], 405);
        }

        $id = (int) $this->input->post('id');
        $terminal_id = $this->session->userdata('pdv_terminal_id');

        $venda = $this->Pdv_model->get_venda_suspensa($id);

        if (!$venda) {
            return $this->_json(['success' => false, 'message' => 'Venda suspensa não encontrada ou expirada.']);
        }

        if ((int) $venda->terminal_id !== (int) $terminal_id) {
            return $this->_json(['success' => false, 'message' => 'Venda pertence a outro terminal.']);
        }

        $this->Pdv_model->recuperar_venda_suspensa($id);

        // Audit
        $this->Pdv_model->registrar_audit([
            'terminal_id' => $terminal_id,
            'caixa_id'    => $this->session->userdata('pdv_caixa_id'),
            'operador_id' => $this->session->userdata('pdv_operador_id'),
            'acao'        => 'venda_recuperada',
            'entidade'    => 'pdv_venda_suspensa',
            'entidade_id' => $id,
            'detalhes'    => ['total' => $venda->total],
            'ip'          => $this->input->ip_address(),
        ]);

        $itens = json_decode($venda->itens, true);

        return $this->_json([
            'success' => true,
            'itens'   => $itens,
            'total'   => (float) $venda->total,
            'message' => 'Venda recuperada com sucesso.',
        ]);
    }

    // =========================================================================
    // FINALIZAR VENDA (Fase 5)
    // =========================================================================

    /**
     * Finaliza a venda — grava no banco e retorna cupom
     * AJAX POST: /pdv/finalizar_venda
     *
     * Recebe: itens (JSON), pagamentos (JSON), cpf_cliente, lock_owner
     * Retorna: {success, venda_id, transaction_id, cupom_url}
     */
    public function finalizar_venda()
    {
        if ($this->input->method() !== 'post') {
            return $this->_json(['success' => false, 'message' => 'Método inválido'], 405);
        }

        $terminal_id = $this->session->userdata('pdv_terminal_id');
        $caixa_id    = $this->session->userdata('pdv_caixa_id');
        $operador_id = $this->session->userdata('pdv_operador_id');

        $itens_json      = $this->input->post('itens');
        $pagamentos_json = $this->input->post('pagamentos');
        $cpf_cliente     = $this->input->post('cpf_cliente', true);
        $lock_owner      = $this->input->post('lock_owner', true);

        $itens      = json_decode($itens_json, true);
        $pagamentos = json_decode($pagamentos_json, true);

        if (empty($itens)) {
            return $this->_json(['success' => false, 'message' => 'Nenhum item na venda.']);
        }

        if (empty($pagamentos)) {
            return $this->_json(['success' => false, 'message' => 'Nenhuma forma de pagamento informada.']);
        }

        // Recalcular totais no servidor (nunca confiar no JS)
        // Security: re-fetch product prices from DB to prevent price manipulation
        $product_ids = [];
        foreach ($itens as $it) {
            $pid = isset($it['product_id']) ? (int) $it['product_id'] : 0;
            if ($pid > 0) $product_ids[] = $pid;
        }
        $product_ids = array_unique($product_ids);
        $db_prices = [];
        if (!empty($product_ids)) {
            $products = $this->db->select('id, price, name')
                ->where_in('id', $product_ids)
                ->get('product_tbl')
                ->result();
            foreach ($products as $p) {
                $db_prices[(int) $p->id] = (float) $p->price;
            }
        }

        $subtotal = 0;
        foreach ($itens as &$item) {
            $pid = isset($item['product_id']) ? (int) $item['product_id'] : 0;
            // Use server-side price for known products; allow client price only for generics
            if ($pid > 0 && isset($db_prices[$pid])) {
                $item['preco'] = $db_prices[$pid];
            } else {
                $item['preco'] = (float) $item['preco'];
            }
            $item['quantidade'] = max(0, (float) $item['quantidade']);
            $item['subtotal']   = round($item['preco'] * $item['quantidade'], 2);

            // Sanitize generic description
            if (!empty($item['generico']) && !empty($item['descricao_manual'])) {
                $item['descricao_manual'] = mb_substr(strip_tags($item['descricao_manual']), 0, 255);
            }
            // Validate generic price > 0
            if (!empty($item['generico']) && $item['preco'] <= 0) {
                return $this->_json(['success' => false, 'message' => 'Produto genérico deve ter preço maior que zero.']);
            }

            $subtotal += $item['subtotal'];
        }
        unset($item);
        $subtotal = round($subtotal, 2);

        // Fase 7: Calcular descontos
        $desconto_total = 0;

        // 7.1 — Descontos por item (já aplicados no JS, recalcular no servidor)
        foreach ($itens as &$item_desc) {
            $desc_tipo  = isset($item_desc['desconto_tipo']) ? $item_desc['desconto_tipo'] : null;
            $desc_valor = isset($item_desc['desconto_valor']) ? (float) $item_desc['desconto_valor'] : 0;
            $desc_calc  = 0;

            if ($desc_tipo && $desc_valor > 0) {
                $item_bruto = round($item_desc['preco'] * $item_desc['quantidade'], 2);
                if ($desc_tipo === 'percentual') {
                    $desc_calc = round($item_bruto * ($desc_valor / 100), 2);
                } else {
                    // tipo 'valor' — desconto em reais
                    $desc_calc = round(min($desc_valor, $item_bruto), 2);
                }
                $item_desc['desconto_calculado'] = $desc_calc;
                $item_desc['subtotal'] = round($item_bruto - $desc_calc, 2);
            }
            $desconto_total += $desc_calc;
        }
        unset($item_desc);

        // 7.2 — Desconto na venda (rateio proporcional)
        $desconto_venda_tipo  = $this->input->post('desconto_venda_tipo', true);
        $desconto_venda_valor = (float) $this->input->post('desconto_venda_valor', true);
        $desconto_autorizado_por = (int) $this->input->post('desconto_autorizado_por', true);

        if ($desconto_venda_tipo && $desconto_venda_valor > 0) {
            // Recalcular subtotal (já com descontos por item)
            $subtotal_pos_item = 0;
            foreach ($itens as $it) {
                $subtotal_pos_item += $it['subtotal'];
            }
            $subtotal_pos_item = round($subtotal_pos_item, 2);

            $desconto_venda_calc = 0;
            if ($desconto_venda_tipo === 'percentual') {
                $desconto_venda_calc = round($subtotal_pos_item * ($desconto_venda_valor / 100), 2);
            } else {
                $desconto_venda_calc = round(min($desconto_venda_valor, $subtotal_pos_item), 2);
            }

            // Validar limite do operador no servidor
            $limite_pct = (float) (getenv('PDV_DESCONTO_OPERADOR') ?: 5);
            $desconto_pct_real = ($subtotal > 0) ? ($desconto_venda_calc / $subtotal) * 100 : 0;

            if ($desconto_pct_real > $limite_pct && $desconto_autorizado_por <= 0) {
                return $this->_json([
                    'success' => false,
                    'message' => 'Desconto acima do limite requer autorização de supervisor.',
                ]);
            }

            // Rateio proporcional nos itens
            $rateio_acumulado = 0;
            $ultimo_idx = count($itens) - 1;
            foreach ($itens as $idx => &$item_rateio) {
                if ($idx === $ultimo_idx) {
                    // Último item recebe o restante (evitar centavos perdidos)
                    $item_rateio['desconto_rateio'] = round($desconto_venda_calc - $rateio_acumulado, 2);
                } else {
                    $proporcao = ($subtotal_pos_item > 0) ? $item_rateio['subtotal'] / $subtotal_pos_item : 0;
                    $item_rateio['desconto_rateio'] = round($desconto_venda_calc * $proporcao, 2);
                    $rateio_acumulado += $item_rateio['desconto_rateio'];
                }
                $item_rateio['subtotal'] = round($item_rateio['subtotal'] - $item_rateio['desconto_rateio'], 2);
            }
            unset($item_rateio);

            $desconto_total += $desconto_venda_calc;
        }

        // Recalcular subtotal final dos itens
        $subtotal = 0;
        foreach ($itens as $it_final) {
            $subtotal += $it_final['subtotal'] + (isset($it_final['desconto_calculado']) ? $it_final['desconto_calculado'] : 0) + (isset($it_final['desconto_rateio']) ? $it_final['desconto_rateio'] : 0);
        }
        $subtotal = round($subtotal, 2);

        $total = round($subtotal - $desconto_total, 2);

        // Validar que pagamentos cobrem o total
        $total_pago = 0;
        foreach ($pagamentos as $pgto) {
            $total_pago += (float) $pgto['valor'];
        }

        if (round($total_pago, 2) < $total) {
            return $this->_json([
                'success' => false,
                'message' => 'Valor pago (R$ ' . number_format($total_pago, 2, ',', '.') . ') é menor que o total (R$ ' . number_format($total, 2, ',', '.') . ').',
            ]);
        }

        // Gerar transaction_id
        $this->load->library('generators');
        $transaction_id = 'T' . date('ymd') . strtoupper($this->generators->generator(5));

        // Gravar venda
        $resultado = $this->Pdv_model->gravar_venda([
            'transaction_id' => $transaction_id,
            'customer_id'    => ((int) $this->input->post('customer_id')) > 0 ? (int) $this->input->post('customer_id') : null,
            'terminal_id'    => $terminal_id,
            'caixa_id'       => $caixa_id,
            'operador_id'    => $operador_id,
            'subtotal'       => $subtotal,
            'desconto_total'        => $desconto_total,
            'total'                 => $total,
            'cpf_cliente'           => $cpf_cliente,
            'itens'                 => $itens,
            'pagamentos'            => $pagamentos,
            'desconto_autorizado_por' => $desconto_autorizado_por > 0 ? $desconto_autorizado_por : null,
        ]);

        // Liberar locks Redis
        if (!empty($lock_owner)) {
            $this->load->library('Estoque');
            $product_ids = [];
            foreach ($itens as $item) {
                $pid = isset($item['product_id']) ? (int) $item['product_id'] : 0;
                if ($pid > 0) $product_ids[] = $pid;
            }
            $this->estoque->liberar_locks(array_unique($product_ids), $lock_owner);
        }

        if (!$resultado['success']) {
            return $this->_json(['success' => false, 'message' => $resultado['message']]);
        }

        // Audit log
        $this->Pdv_model->registrar_audit([
            'terminal_id' => $terminal_id,
            'caixa_id'    => $caixa_id,
            'operador_id' => $operador_id,
            'acao'        => 'venda_finalizada',
            'entidade'    => 'invoice_tbl',
            'entidade_id' => $resultado['invoice_id'],
            'detalhes'    => [
                'transaction_id'        => $transaction_id,
                'total'                 => $total,
                'desconto_total'        => $desconto_total,
                'qtd_itens'             => count($itens),
                'formas'                => array_column($pagamentos, 'forma'),
                'cpf'                   => $cpf_cliente,
                'desconto_autorizado_por' => $desconto_autorizado_por > 0 ? $desconto_autorizado_por : null,
            ],
            'ip' => $this->input->ip_address(),
        ]);

        // Audit: fiado_venda (se tem pagamento fiado)
        foreach ($pagamentos as $pgto_audit) {
            if (isset($pgto_audit['forma']) && $pgto_audit['forma'] === 'fiado') {
                $customer_id_fiado = (int) $this->input->post('customer_id');
                $this->Pdv_model->registrar_audit([
                    'terminal_id' => $terminal_id,
                    'caixa_id'    => $caixa_id,
                    'operador_id' => $operador_id,
                    'acao'        => 'fiado_venda',
                    'entidade'    => 'invoice_tbl',
                    'entidade_id' => $resultado['invoice_id'],
                    'detalhes'    => [
                        'transaction_id' => $transaction_id,
                        'valor_fiado'    => (float) $pgto_audit['valor'],
                        'customer_id'    => $customer_id_fiado,
                    ],
                    'ip' => $this->input->ip_address(),
                ]);
                break;
            }
        }

        // Calcular troco (se dinheiro)
        $troco = 0;
        foreach ($pagamentos as $pgto) {
            if (isset($pgto['troco']) && (float) $pgto['troco'] > 0) {
                $troco += (float) $pgto['troco'];
            }
        }

        return $this->_json([
            'success'        => true,
            'venda_id'       => $resultado['invoice_id'],
            'transaction_id' => $transaction_id,
            'total'          => $total,
            'troco'          => $troco,
            'message'        => 'Venda finalizada com sucesso!',
        ]);
    }

    /**
     * Retorna dados de uma venda para cupom (impressão)
     * AJAX GET: /pdv/get_cupom?venda_id=123
     */
    public function get_cupom()
    {
        $venda_id = (int) $this->input->get('venda_id');

        if ($venda_id <= 0) {
            return $this->_json(['success' => false, 'message' => 'ID da venda inválido.']);
        }

        $venda = $this->Pdv_model->get_venda_completa($venda_id);

        if (!$venda) {
            return $this->_json(['success' => false, 'message' => 'Venda não encontrada.']);
        }

        // Verificar que pertence ao terminal atual
        $terminal_id = $this->session->userdata('pdv_terminal_id');
        if ((int) $venda->terminal_id !== (int) $terminal_id) {
            // Allow if operator is admin
            if (!$this->session->userdata('pdv_operador_is_admin')) {
                return $this->_json(['success' => false, 'message' => 'Venda pertence a outro terminal.']);
            }
        }

        return $this->_json([
            'success' => true,
            'venda'   => [
                'id'             => (int) $venda->id,
                'invoice_id'     => $venda->invoice_id,
                'date'           => $venda->date,
                'created_at'     => isset($venda->created_at) ? $venda->created_at : null,
                'grand_total'    => (float) (isset($venda->total_amount) ? $venda->total_amount : 0),
                'discount'       => (float) (isset($venda->invoice_discount) ? $venda->invoice_discount : 0),
                'sub_total'      => (float) (isset($venda->total_amount) ? $venda->total_amount : 0),
                'description'    => isset($venda->description) ? $venda->description : '',
                'operador_nome'  => $venda->operador_nome,
                'terminal_numero'=> $venda->terminal_numero,
                'terminal_nome'  => $venda->terminal_nome,
            ],
            'itens'      => array_map(function($i) {
                return [
                    'product_name'     => isset($i->product_name) ? $i->product_name : '',
                    'qty'              => (float) (isset($i->quantity) ? $i->quantity : 0),
                    'rate'             => (float) (isset($i->price) ? $i->price : 0),
                    'total'            => (float) (isset($i->total_price) ? $i->total_price : 0),
                    'descricao_manual' => $i->descricao_manual,
                ];
            }, $venda->itens),
            'pagamentos' => array_map(function($p) {
                return [
                    'forma_pagamento' => isset($p->forma) ? $p->forma : (isset($p->forma_pagamento) ? $p->forma_pagamento : ''),
                    'valor'           => (float) $p->valor,
                    'troco'           => (float) $p->troco,
                ];
            }, $venda->pagamentos),
        ]);
    }

    /**
     * Renderiza view do cupom não-fiscal para impressão
     * GET: /pdv/cupom_impressao/{venda_id}
     */
    public function cupom_impressao($venda_id = 0, $segunda_via = 0)
    {
        // Support both URL segment and query string
        $venda_id = (int) $venda_id;
        if ($venda_id <= 0) {
            $venda_id = (int) $this->input->get('venda_id');
        }
        if ((int) $segunda_via <= 0) {
            $segunda_via = (int) $this->input->get('segunda_via');
        }
        if ($venda_id <= 0) {
            show_error('ID da venda inválido.', 400);
            return;
        }

        // Verificar sessão PDV ativa
        if (!$this->session->userdata('pdv_operador_id')) {
            show_error('Sessão PDV expirada.', 403);
            return;
        }

        $venda = $this->Pdv_model->get_venda_completa($venda_id);

        if (!$venda) {
            show_error('Venda não encontrada.', 404);
            return;
        }

        // Verificar pertence ao terminal (exceto admin)
        $terminal_id = $this->session->userdata('pdv_terminal_id');
        if ((int) $venda->terminal_id !== (int) $terminal_id) {
            if (!$this->session->userdata('pdv_operador_is_admin')) {
                show_error('Venda pertence a outro terminal.', 403);
                return;
            }
        }

        $setting = $this->db->get('setting')->row();

        // Fase 8: buscar dados de fiado se a venda tem pagamento fiado
        $fiado = null;
        foreach ($venda->pagamentos as $pgto) {
            if ((isset($pgto->forma) ? $pgto->forma : (isset($pgto->forma_pagamento) ? $pgto->forma_pagamento : '')) === 'fiado') {
                $fiado_reg = $this->Pdv_model->getFiadoByInvoice($venda->id);
                if ($fiado_reg) {
                    $cliente = $this->Pdv_model->getCliente($fiado_reg->customer_id);
                    $resumo  = $this->Pdv_model->getResumoCredito($fiado_reg->customer_id);
                    $fiado = (object) [
                        'cliente_nome'    => $cliente ? $cliente->name : '-',
                        'valor_fiado'     => $fiado_reg->valor,
                        'debito_anterior' => round(($resumo['debito_atual'] ?? 0) - (float) $fiado_reg->valor + (float) $fiado_reg->valor_pago, 2),
                        'debito_total'    => $resumo['debito_atual'] ?? 0,
                    ];
                }
                break;
            }
        }

        $data = [
            'venda'       => $venda,
            'itens'       => $venda->itens,
            'pagamentos'  => $venda->pagamentos,
            'setting'     => $setting,
            'segunda_via' => (bool) $segunda_via,
            'fiado'       => $fiado,
        ];

        $this->load->view('cupom', $data);
    }

    // =========================================================================
    // CONTROLE DE CAIXA — Fase 6 (F7 Menu)
    // =========================================================================

    /**
     * Menu do caixa (F7) — retorna opções disponíveis
     * AJAX GET: /pdv/menu_caixa
     */
    public function menu_caixa()
    {
        $opcoes = [
            ['tecla' => 1, 'label' => 'Sangria',               'acao' => 'sangria'],
            ['tecla' => 2, 'label' => 'Suprimento',            'acao' => 'suprimento'],
            ['tecla' => 3, 'label' => 'Leitura X',             'acao' => 'leitura_x'],
            ['tecla' => 4, 'label' => 'Fechamento',            'acao' => 'fechamento_caixa'],
            ['tecla' => 5, 'label' => 'Trocar Operador',       'acao' => 'trocar_operador'],
            ['tecla' => 6, 'label' => 'Devolução',             'acao' => 'devolucao'],
            ['tecla' => 7, 'label' => 'Cancelar Último Cupom', 'acao' => 'cancelar_ultimo_cupom'],
            ['tecla' => 8, 'label' => 'Cancelar por Número',   'acao' => 'cancelar_por_numero'],
        ];

        return $this->_json(['success' => true, 'opcoes' => $opcoes]);
    }

    /**
     * Sangria — retirada de valores do caixa
     * AJAX POST: /pdv/sangria
     *
     * Requer: valor (float > 0), motivo (string), senha_supervisor (string)
     */
    public function sangria()
    {
        if ($this->input->method() !== 'post') {
            return $this->_json(['success' => false, 'message' => 'Método inválido'], 405);
        }

        $valor_raw        = $this->input->post('valor', true);
        $motivo           = $this->input->post('motivo', true);
        $senha_supervisor = $this->input->post('senha_supervisor');

        // Validações
        if (empty($valor_raw) || empty($motivo) || empty($senha_supervisor)) {
            return $this->_json(['success' => false, 'message' => 'Valor, motivo e senha do supervisor são obrigatórios.']);
        }

        $valor = (float) str_replace(['.', ','], ['', '.'], $valor_raw);
        if ($valor <= 0) {
            return $this->_json(['success' => false, 'message' => 'Valor deve ser maior que zero.']);
        }

        // Validar supervisor
        $supervisor = $this->_validar_supervisor($senha_supervisor);
        if (!$supervisor) {
            return $this->_json(['success' => false, 'message' => 'Senha de supervisor inválida.']);
        }

        $caixa_id    = (int) $this->session->userdata('pdv_caixa_id');
        $operador_id = (int) $this->session->userdata('pdv_operador_id');
        $terminal_id = (int) $this->session->userdata('pdv_terminal_id');

        // Validar saldo estimado
        $saldo = $this->Pdv_model->getSaldoEstimado($caixa_id);
        if ($valor > $saldo) {
            return $this->_json([
                'success' => false,
                'message' => 'Valor da sangria (R$ ' . number_format($valor, 2, ',', '.')
                    . ') excede o saldo estimado (R$ ' . number_format($saldo, 2, ',', '.') . ').',
            ]);
        }

        // Registrar movimentação (valor negativo)
        $mov_id = $this->Pdv_model->registrar_movimento([
            'caixa_id'        => $caixa_id,
            'tipo'            => 'sangria',
            'valor'           => -$valor,
            'forma_pagamento' => null,
            'invoice_id'      => null,
            'descricao'       => $motivo,
            'operador_id'     => $operador_id,
            'supervisor_id'   => (int) $supervisor->id,
        ]);

        // Audit log
        $this->Pdv_model->registrar_audit([
            'terminal_id' => $terminal_id,
            'caixa_id'    => $caixa_id,
            'operador_id' => $operador_id,
            'acao'        => 'sangria',
            'entidade'    => 'pdv_caixa_mov',
            'entidade_id' => $mov_id,
            'detalhes'    => [
                'valor'         => $valor,
                'motivo'        => $motivo,
                'supervisor_id' => (int) $supervisor->id,
                'supervisor'    => $supervisor->fullname,
            ],
            'ip' => $this->input->ip_address(),
        ]);

        return $this->_json([
            'success' => true,
            'message' => 'Sangria registrada com sucesso.',
            'dados'   => [
                'tipo'       => 'SANGRIA',
                'valor'      => $valor,
                'motivo'     => $motivo,
                'operador'   => $this->session->userdata('pdv_operador_nome'),
                'supervisor' => $supervisor->fullname,
                'data_hora'  => date('d/m/Y H:i:s'),
                'terminal'   => $this->session->userdata('pdv_terminal_numero'),
            ],
        ]);
    }

    /**
     * Suprimento — entrada de valores no caixa
     * AJAX POST: /pdv/suprimento
     *
     * Requer: valor (float > 0), motivo (string), senha_supervisor (string)
     */
    public function suprimento()
    {
        if ($this->input->method() !== 'post') {
            return $this->_json(['success' => false, 'message' => 'Método inválido'], 405);
        }

        $valor_raw        = $this->input->post('valor', true);
        $motivo           = $this->input->post('motivo', true);
        $senha_supervisor = $this->input->post('senha_supervisor');

        // Validações
        if (empty($valor_raw) || empty($motivo) || empty($senha_supervisor)) {
            return $this->_json(['success' => false, 'message' => 'Valor, motivo e senha do supervisor são obrigatórios.']);
        }

        $valor = (float) str_replace(['.', ','], ['', '.'], $valor_raw);
        if ($valor <= 0) {
            return $this->_json(['success' => false, 'message' => 'Valor deve ser maior que zero.']);
        }

        // Validar supervisor
        $supervisor = $this->_validar_supervisor($senha_supervisor);
        if (!$supervisor) {
            return $this->_json(['success' => false, 'message' => 'Senha de supervisor inválida.']);
        }

        $caixa_id    = (int) $this->session->userdata('pdv_caixa_id');
        $operador_id = (int) $this->session->userdata('pdv_operador_id');
        $terminal_id = (int) $this->session->userdata('pdv_terminal_id');

        // Registrar movimentação (valor positivo)
        $mov_id = $this->Pdv_model->registrar_movimento([
            'caixa_id'        => $caixa_id,
            'tipo'            => 'suprimento',
            'valor'           => $valor,
            'forma_pagamento' => null,
            'invoice_id'      => null,
            'descricao'       => $motivo,
            'operador_id'     => $operador_id,
            'supervisor_id'   => (int) $supervisor->id,
        ]);

        // Audit log
        $this->Pdv_model->registrar_audit([
            'terminal_id' => $terminal_id,
            'caixa_id'    => $caixa_id,
            'operador_id' => $operador_id,
            'acao'        => 'suprimento',
            'entidade'    => 'pdv_caixa_mov',
            'entidade_id' => $mov_id,
            'detalhes'    => [
                'valor'         => $valor,
                'motivo'        => $motivo,
                'supervisor_id' => (int) $supervisor->id,
                'supervisor'    => $supervisor->fullname,
            ],
            'ip' => $this->input->ip_address(),
        ]);

        return $this->_json([
            'success' => true,
            'message' => 'Suprimento registrado com sucesso.',
            'dados'   => [
                'tipo'       => 'SUPRIMENTO',
                'valor'      => $valor,
                'motivo'     => $motivo,
                'operador'   => $this->session->userdata('pdv_operador_nome'),
                'supervisor' => $supervisor->fullname,
                'data_hora'  => date('d/m/Y H:i:s'),
                'terminal'   => $this->session->userdata('pdv_terminal_numero'),
            ],
        ]);
    }

    /**
     * Leitura X — relatório parcial do caixa (sem fechar)
     * AJAX GET: /pdv/leitura_x
     */
    public function leitura_x()
    {
        $caixa_id    = (int) $this->session->userdata('pdv_caixa_id');
        $terminal_id = (int) $this->session->userdata('pdv_terminal_id');
        $operador_id = (int) $this->session->userdata('pdv_operador_id');

        if ($caixa_id <= 0) {
            return $this->_json(['success' => false, 'message' => 'Nenhum caixa aberto.']);
        }

        $leitura = $this->Pdv_model->leituraX($caixa_id);

        // Audit log
        $this->Pdv_model->registrar_audit([
            'terminal_id' => $terminal_id,
            'caixa_id'    => $caixa_id,
            'operador_id' => $operador_id,
            'acao'        => 'leitura_x',
            'entidade'    => 'pdv_caixa',
            'entidade_id' => $caixa_id,
            'detalhes'    => [
                'saldo_estimado' => $leitura['saldo_estimado'],
                'total_vendas'   => $leitura['total_vendas'],
                'qtd_vendas'     => $leitura['qtd_vendas'],
            ],
            'ip' => $this->input->ip_address(),
        ]);

        return $this->_json([
            'success' => true,
            'dados'   => $leitura,
        ]);
    }

    /**
     * Fechamento de caixa — encerra o turno
     * AJAX POST: /pdv/fechamento_caixa
     *
     * Requer: valor_contado (float), observacao (obrigatória se diferença != 0)
     */
    public function fechamento_caixa()
    {
        if ($this->input->method() !== 'post') {
            return $this->_json(['success' => false, 'message' => 'Método inválido'], 405);
        }

        $caixa_id    = (int) $this->session->userdata('pdv_caixa_id');
        $terminal_id = (int) $this->session->userdata('pdv_terminal_id');
        $operador_id = (int) $this->session->userdata('pdv_operador_id');

        if ($caixa_id <= 0) {
            return $this->_json(['success' => false, 'message' => 'Nenhum caixa aberto.']);
        }

        $valor_contado_raw = $this->input->post('valor_contado', true);
        $observacao        = $this->input->post('observacao', true);

        if ($valor_contado_raw === null || $valor_contado_raw === '') {
            return $this->_json(['success' => false, 'message' => 'Valor contado é obrigatório.']);
        }

        $valor_contado = (float) str_replace(['.', ','], ['', '.'], $valor_contado_raw);

        // Obter leitura X para valores esperados
        $leitura = $this->Pdv_model->leituraX($caixa_id);

        // Dinheiro esperado = fundo_troco + vendas_dinheiro + suprimentos - sangrias - troco_dado
        $dinheiro_esperado = (float) $leitura['total_por_forma']['dinheiro'];
        $diferenca = round($valor_contado - $dinheiro_esperado, 2);

        // Se há diferença, observação é obrigatória
        if (abs($diferenca) > 0.01 && empty($observacao)) {
            return $this->_json([
                'success' => false,
                'message' => 'Há diferença de R$ ' . number_format($diferenca, 2, ',', '.')
                    . '. Informe uma observação.',
            ]);
        }

        // Fechar caixa
        $resultado = $this->Pdv_model->fecharCaixa([
            'caixa_id'         => $caixa_id,
            'operador_id'      => $operador_id,
            'valor_contado'    => $valor_contado,
            'diferenca'        => $diferenca,
            'total_vendas'     => (float) $leitura['total_vendas'],
            'qtd_vendas'       => (int) $leitura['qtd_vendas'],
            'observacao'       => $observacao,
            'saldo_estimado'   => (float) $leitura['saldo_estimado'],
        ]);

        if (!$resultado) {
            return $this->_json(['success' => false, 'message' => 'Erro ao fechar caixa.']);
        }

        // Audit log
        $this->Pdv_model->registrar_audit([
            'terminal_id' => $terminal_id,
            'caixa_id'    => $caixa_id,
            'operador_id' => $operador_id,
            'acao'        => 'fechamento_caixa',
            'entidade'    => 'pdv_caixa',
            'entidade_id' => $caixa_id,
            'detalhes'    => [
                'valor_contado'    => $valor_contado,
                'dinheiro_esperado'=> $dinheiro_esperado,
                'diferenca'        => $diferenca,
                'total_vendas'     => $leitura['total_vendas'],
                'qtd_vendas'       => $leitura['qtd_vendas'],
                'observacao'       => $observacao,
            ],
            'ip' => $this->input->ip_address(),
        ]);

        // Dados do resumo para impressão
        $resumo = [
            'terminal'          => $this->session->userdata('pdv_terminal_numero'),
            'terminal_nome'     => $this->session->userdata('pdv_terminal_nome'),
            'operador'          => $this->session->userdata('pdv_operador_nome'),
            'data_hora'         => date('d/m/Y H:i:s'),
            'fundo_troco'       => $leitura['fundo_troco'],
            'total_vendas'      => $leitura['total_vendas'],
            'qtd_vendas'        => $leitura['qtd_vendas'],
            'total_por_forma'   => $leitura['total_por_forma'],
            'sangrias'          => $leitura['sangrias'],
            'suprimentos'       => $leitura['suprimentos'],
            'saldo_estimado'    => $leitura['saldo_estimado'],
            'valor_contado'     => $valor_contado,
            'diferenca'         => $diferenca,
        ];

        // Limpar sessão PDV
        $terminal_numero = $this->session->userdata('pdv_terminal_numero');
        $pdv_keys = [
            'pdv_logado', 'pdv_operador_id', 'pdv_operador_nome',
            'pdv_operador_matricula', 'pdv_operador_image', 'pdv_operador_is_admin',
            'pdv_terminal_id', 'pdv_terminal_numero', 'pdv_terminal_nome',
            'pdv_caixa_id', 'pdv_login_at',
        ];
        $this->session->unset_userdata($pdv_keys);

        return $this->_json([
            'success'      => true,
            'message'      => 'Caixa fechado com sucesso.',
            'resumo'       => $resumo,
            'redirect_url' => site_url('pdv/login/' . $terminal_numero),
        ]);
    }

    /**
     * Trocar operador — troca de turno sem fechar o caixa
     * AJAX POST: /pdv/trocar_operador
     *
     * Requer: matricula (string), senha (string) do novo operador
     */
    public function trocar_operador()
    {
        if ($this->input->method() !== 'post') {
            return $this->_json(['success' => false, 'message' => 'Método inválido'], 405);
        }

        $matricula = trim($this->input->post('matricula', true));
        $senha     = $this->input->post('senha');

        if (empty($matricula) || empty($senha)) {
            return $this->_json(['success' => false, 'message' => 'Matrícula e senha são obrigatórios.']);
        }

        $caixa_id    = (int) $this->session->userdata('pdv_caixa_id');
        $terminal_id = (int) $this->session->userdata('pdv_terminal_id');
        $operador_anterior_id = (int) $this->session->userdata('pdv_operador_id');
        $operador_anterior_nome = $this->session->userdata('pdv_operador_nome');

        // Buscar novo operador
        $novo_operador = $this->Pdv_model->get_user_by_matricula($matricula);
        if (!$novo_operador) {
            $novo_operador = $this->Pdv_model->get_user_by_email($matricula);
        }

        if (!$novo_operador) {
            return $this->_json(['success' => false, 'message' => 'Matrícula ou senha inválida.']);
        }

        // Verificar senha (bcrypt)
        if (!password_verify($senha, $novo_operador->password)) {
            return $this->_json(['success' => false, 'message' => 'Matrícula ou senha inválida.']);
        }

        // Verificar permissão PDV
        if (!$this->Pdv_model->user_has_pdv_permission($novo_operador->id)) {
            return $this->_json(['success' => false, 'message' => 'Novo operador sem permissão para operar o PDV.']);
        }

        // Efetuar troca
        $this->Pdv_model->trocarOperador($caixa_id, (int) $novo_operador->id, $operador_anterior_id);

        // Atualizar sessão
        $this->session->set_userdata([
            'pdv_operador_id'        => $novo_operador->id,
            'pdv_operador_nome'      => $novo_operador->fullname,
            'pdv_operador_matricula' => $novo_operador->matricula ?: $novo_operador->email,
            'pdv_operador_image'     => $novo_operador->image,
            'pdv_operador_is_admin'  => ($novo_operador->user_type == 1),
            'pdv_login_at'           => date('Y-m-d H:i:s'),
        ]);

        // Regenerar session
        $this->session->sess_regenerate(true);

        // Audit log
        $this->Pdv_model->registrar_audit([
            'terminal_id' => $terminal_id,
            'caixa_id'    => $caixa_id,
            'operador_id' => (int) $novo_operador->id,
            'acao'        => 'troca_operador',
            'entidade'    => 'pdv_caixa',
            'entidade_id' => $caixa_id,
            'detalhes'    => [
                'operador_anterior_id'   => $operador_anterior_id,
                'operador_anterior_nome' => $operador_anterior_nome,
                'novo_operador_id'       => (int) $novo_operador->id,
                'novo_operador_nome'     => $novo_operador->fullname,
            ],
            'ip' => $this->input->ip_address(),
        ]);

        return $this->_json([
            'success' => true,
            'message' => 'Operador trocado com sucesso.',
            'operador' => [
                'id'        => (int) $novo_operador->id,
                'nome'      => $novo_operador->fullname,
                'matricula' => $novo_operador->matricula ?: $novo_operador->email,
            ],
        ]);
    }

    /**
     * Cancelar cupom — cancela uma venda e estorna estoque
     * AJAX POST: /pdv/cancelar_cupom
     *
     * Requer: venda_id OU ultimo=true, motivo (string), senha_supervisor (string)
     */
    public function cancelar_cupom()
    {
        if ($this->input->method() !== 'post') {
            return $this->_json(['success' => false, 'message' => 'Método inválido'], 405);
        }

        $venda_id         = (int) $this->input->post('venda_id');
        $ultimo           = $this->input->post('ultimo', true);
        $motivo           = $this->input->post('motivo', true);
        $senha_supervisor = $this->input->post('senha_supervisor');

        if (empty($motivo) || empty($senha_supervisor)) {
            return $this->_json(['success' => false, 'message' => 'Motivo e senha do supervisor são obrigatórios.']);
        }

        // Validar supervisor
        $supervisor = $this->_validar_supervisor($senha_supervisor);
        if (!$supervisor) {
            return $this->_json(['success' => false, 'message' => 'Senha de supervisor inválida.']);
        }

        $terminal_id = (int) $this->session->userdata('pdv_terminal_id');
        $caixa_id    = (int) $this->session->userdata('pdv_caixa_id');
        $operador_id = (int) $this->session->userdata('pdv_operador_id');

        // Se 'ultimo', buscar última venda do terminal
        if ($ultimo === 'true' || $ultimo === '1') {
            $ultima_venda = $this->Pdv_model->getUltimaVenda($terminal_id);
            if (!$ultima_venda) {
                return $this->_json(['success' => false, 'message' => 'Nenhuma venda encontrada neste terminal.']);
            }
            $venda_id = (int) $ultima_venda->id;
        }

        if ($venda_id <= 0) {
            return $this->_json(['success' => false, 'message' => 'ID da venda inválido.']);
        }

        // Buscar venda
        $venda = $this->Pdv_model->get_venda_completa($venda_id);
        if (!$venda) {
            return $this->_json(['success' => false, 'message' => 'Venda não encontrada.']);
        }

        // Validar que pertence ao terminal
        if ((int) $venda->terminal_id !== $terminal_id) {
            return $this->_json(['success' => false, 'message' => 'Venda pertence a outro terminal.']);
        }

        // Validar que não está cancelada
        if (isset($venda->status) && (int) $venda->status === 0) {
            return $this->_json(['success' => false, 'message' => 'Esta venda já foi cancelada.']);
        }

        // Validar tempo máximo para cancelamento
        $max_minutos = (int) (getenv('PDV_CANCELAMENTO_MINUTOS') ?: 30);
        $criada_em = strtotime(isset($venda->created_at) ? $venda->created_at : $venda->date);
        $agora = time();
        $diff_minutos = ($agora - $criada_em) / 60;

        if ($diff_minutos > $max_minutos) {
            return $this->_json([
                'success' => false,
                'message' => 'Prazo para cancelamento expirado (' . $max_minutos . ' minutos).',
            ]);
        }

        // Executar cancelamento (atômico)
        $resultado = $this->Pdv_model->cancelarVenda(
            $venda_id,
            $motivo,
            (int) $supervisor->id,
            $operador_id
        );

        if (!$resultado['success']) {
            return $this->_json(['success' => false, 'message' => $resultado['message']]);
        }

        // Audit log
        $this->Pdv_model->registrar_audit([
            'terminal_id' => $terminal_id,
            'caixa_id'    => $caixa_id,
            'operador_id' => $operador_id,
            'acao'        => 'cupom_cancelado',
            'entidade'    => 'invoice_tbl',
            'entidade_id' => $venda_id,
            'detalhes'    => [
                'invoice_id'    => $venda->invoice_id,
                'total'         => (float) (isset($venda->total_amount) ? $venda->total_amount : 0),
                'motivo'        => $motivo,
                'supervisor_id' => (int) $supervisor->id,
                'supervisor'    => $supervisor->fullname,
                'qtd_itens'     => count($venda->itens),
            ],
            'ip' => $this->input->ip_address(),
        ]);

        return $this->_json([
            'success' => true,
            'message' => 'Cupom cancelado com sucesso.',
            'dados'   => [
                'venda_id'    => $venda_id,
                'invoice_id'  => $venda->invoice_id,
                'total'       => (float) (isset($venda->total_amount) ? $venda->total_amount : 0),
                'motivo'      => $motivo,
                'supervisor'  => $supervisor->fullname,
                'data_hora'   => date('d/m/Y H:i:s'),
            ],
        ]);
    }

    /**
     * Reimprimir cupom — reimprime uma venda finalizada
     * GET: /pdv/reimprimir_cupom?venda_id=123 ou /pdv/reimprimir_cupom?ultimo=true
     */
    public function reimprimir_cupom()
    {
        $venda_id = (int) $this->input->get('venda_id');
        $ultimo   = $this->input->get('ultimo', true);

        $terminal_id = (int) $this->session->userdata('pdv_terminal_id');
        $operador_id = (int) $this->session->userdata('pdv_operador_id');

        // Se 'ultimo', buscar última venda do terminal
        if ($ultimo === 'true' || $ultimo === '1') {
            $ultima_venda = $this->Pdv_model->getUltimaVenda($terminal_id);
            if (!$ultima_venda) {
                return $this->_json(['success' => false, 'message' => 'Nenhuma venda encontrada neste terminal.']);
            }
            $venda_id = (int) $ultima_venda->id;
        }

        if ($venda_id <= 0) {
            return $this->_json(['success' => false, 'message' => 'ID da venda inválido.']);
        }

        // Verificar que venda pertence ao terminal (ou operador é supervisor/admin)
        $venda = $this->Pdv_model->get_venda_completa($venda_id);
        if (!$venda) {
            return $this->_json(['success' => false, 'message' => 'Venda não encontrada.']);
        }

        if ((int) $venda->terminal_id !== $terminal_id) {
            if (!$this->session->userdata('pdv_operador_is_admin')) {
                return $this->_json(['success' => false, 'message' => 'Venda pertence a outro terminal.']);
            }
        }

        // Audit log
        $this->Pdv_model->registrar_audit([
            'terminal_id' => $terminal_id,
            'caixa_id'    => (int) $this->session->userdata('pdv_caixa_id'),
            'operador_id' => $operador_id,
            'acao'        => 'reimpressao',
            'entidade'    => 'invoice_tbl',
            'entidade_id' => $venda_id,
            'detalhes'    => [
                'invoice_id' => $venda->invoice_id,
                'total'      => (float) (isset($venda->total_amount) ? $venda->total_amount : 0),
            ],
            'ip' => $this->input->ip_address(),
        ]);

        redirect('pdv/cupom_impressao/' . $venda_id . '/1');
    }

    // =========================================================================
    // FIADO / CREDIÁRIO — Fase 8
    // =========================================================================

    /**
     * Busca clientes para fiado
     * AJAX GET: /pdv/buscar_cliente_fiado?termo=...
     */
    public function buscar_cliente_fiado()
    {
        $termo = trim($this->input->get('termo', true));

        if (empty($termo) || strlen($termo) < 2) {
            return $this->_json(['clientes' => []]);
        }

        $clientes = $this->Pdv_model->buscarClienteFiado($termo);

        $resultado = [];
        foreach ($clientes as $c) {
            $resultado[] = [
                'id'              => (int) $c->id,
                'customerid'      => $c->customerid,
                'nome'            => $c->name,
                'telefone'        => $c->mobile,
                'cpf'             => isset($c->cpf) ? $c->cpf : '',
                'debito_atual'    => (float) $c->debito_atual,
                'limite'          => (float) $c->limite,
                'disponivel'      => (float) $c->disponivel,
                'fiado_bloqueado' => isset($c->fiado_bloqueado) ? (int) $c->fiado_bloqueado : 0,
            ];
        }

        return $this->_json(['clientes' => $resultado]);
    }

    /**
     * Cadastro rápido de cliente (PDV)
     * AJAX POST: /pdv/cadastrar_cliente_rapido
     */
    public function cadastrar_cliente_rapido()
    {
        if ($this->input->method() !== 'post') {
            return $this->_json(['success' => false, 'message' => 'Método inválido'], 405);
        }

        $nome     = trim($this->input->post('nome', true));
        $telefone = trim($this->input->post('telefone', true));
        $cpf      = trim($this->input->post('cpf', true));

        if (empty($nome)) {
            return $this->_json(['success' => false, 'message' => 'Nome é obrigatório.']);
        }
        if (empty($telefone)) {
            return $this->_json(['success' => false, 'message' => 'Telefone é obrigatório.']);
        }

        $operador_id = (int) $this->session->userdata('pdv_operador_id');

        $cliente = $this->Pdv_model->cadastrarClienteRapido([
            'nome'        => $nome,
            'telefone'    => $telefone,
            'cpf'         => $cpf,
            'operador_id' => $operador_id,
        ]);

        if (!$cliente) {
            return $this->_json(['success' => false, 'message' => 'Erro ao cadastrar cliente.']);
        }

        // Retornar com resumo de crédito
        $resumo = $this->Pdv_model->getResumoCredito((int) $cliente->id);

        // Audit
        $this->Pdv_model->registrar_audit([
            'terminal_id' => (int) $this->session->userdata('pdv_terminal_id'),
            'caixa_id'    => (int) $this->session->userdata('pdv_caixa_id'),
            'operador_id' => $operador_id,
            'acao'        => 'cadastro_cliente_rapido',
            'entidade'    => 'customer_tbl',
            'entidade_id' => (int) $cliente->id,
            'detalhes'    => ['nome' => $nome, 'telefone' => $telefone],
            'ip'          => $this->input->ip_address(),
        ]);

        return $this->_json([
            'success' => true,
            'cliente' => [
                'id'              => (int) $cliente->id,
                'customerid'      => $cliente->customerid,
                'nome'            => $cliente->name,
                'telefone'        => $cliente->mobile,
                'cpf'             => isset($cliente->cpf) ? $cliente->cpf : '',
                'debito_atual'    => $resumo['debito_atual'],
                'limite'          => $resumo['limite'],
                'disponivel'      => $resumo['disponivel'],
                'fiado_bloqueado' => 0,
            ],
            'message' => 'Cliente cadastrado com sucesso.',
        ]);
    }

    /**
     * Lista débitos pendentes de um cliente
     * AJAX GET: /pdv/get_debitos_cliente?customer_id=123
     */
    public function get_debitos_cliente()
    {
        $customer_id = (int) $this->input->get('customer_id');

        if ($customer_id <= 0) {
            return $this->_json(['success' => false, 'message' => 'Cliente inválido.']);
        }

        $debitos = $this->Pdv_model->getDebitosCliente($customer_id);
        $resumo  = $this->Pdv_model->getResumoCredito($customer_id);
        $cliente = $this->Pdv_model->getCliente($customer_id);

        $resultado = [];
        foreach ($debitos as $d) {
            $resultado[] = [
                'id'           => (int) $d->id,
                'numero_cupom' => $d->numero_cupom,
                'valor'        => (float) $d->valor,
                'valor_pago'   => (float) $d->valor_pago,
                'saldo'        => (float) $d->saldo,
                'status'       => $d->status,
                'data'         => date('d/m/Y', strtotime($d->created_at)),
            ];
        }

        return $this->_json([
            'success'  => true,
            'cliente'  => [
                'id'   => $customer_id,
                'nome' => $cliente ? $cliente->name : 'Desconhecido',
            ],
            'debitos'  => $resultado,
            'resumo'   => $resumo,
        ]);
    }

    /**
     * Receber pagamento de fiado
     * AJAX POST: /pdv/receber_fiado
     */
    public function receber_fiado()
    {
        if ($this->input->method() !== 'post') {
            return $this->_json(['success' => false, 'message' => 'Método inválido'], 405);
        }

        $customer_id     = (int) $this->input->post('customer_id');
        $valor_raw       = $this->input->post('valor', true);
        $forma_pagamento = $this->input->post('forma_pagamento', true);

        if ($customer_id <= 0) {
            return $this->_json(['success' => false, 'message' => 'Cliente inválido.']);
        }
        if (empty($valor_raw)) {
            return $this->_json(['success' => false, 'message' => 'Valor é obrigatório.']);
        }

        $valor = (float) str_replace(['.', ','], ['', '.'], $valor_raw);
        if ($valor <= 0) {
            return $this->_json(['success' => false, 'message' => 'Valor deve ser maior que zero.']);
        }

        $formas_validas = ['dinheiro', 'debito', 'credito', 'pix'];
        if (!in_array($forma_pagamento, $formas_validas)) {
            return $this->_json(['success' => false, 'message' => 'Forma de pagamento inválida.']);
        }

        $caixa_id    = (int) $this->session->userdata('pdv_caixa_id');
        $operador_id = (int) $this->session->userdata('pdv_operador_id');
        $terminal_id = (int) $this->session->userdata('pdv_terminal_id');

        $resultado = $this->Pdv_model->receberFiado(
            $customer_id,
            $valor,
            $forma_pagamento,
            $caixa_id,
            $operador_id
        );

        if (!$resultado['success']) {
            return $this->_json(['success' => false, 'message' => $resultado['message']]);
        }

        // Audit
        $this->Pdv_model->registrar_audit([
            'terminal_id' => $terminal_id,
            'caixa_id'    => $caixa_id,
            'operador_id' => $operador_id,
            'acao'        => 'recebimento_fiado',
            'entidade'    => 'customer_tbl',
            'entidade_id' => $customer_id,
            'detalhes'    => [
                'valor'            => $valor,
                'forma_pagamento'  => $forma_pagamento,
                'abatimentos'      => $resultado['abatimentos'],
                'debito_restante'  => $resultado['debito_restante'],
            ],
            'ip' => $this->input->ip_address(),
        ]);

        // Dados do cliente para comprovante
        $cliente = $this->Pdv_model->getCliente($customer_id);

        return $this->_json([
            'success'     => true,
            'message'     => $resultado['message'],
            'abatimentos' => $resultado['abatimentos'],
            'recibo'      => [
                'cliente_nome'    => $cliente ? $cliente->name : '',
                'valor_recebido'  => $valor,
                'forma_pagamento' => $forma_pagamento,
                'debito_restante' => $resultado['debito_restante'],
                'data_hora'       => date('d/m/Y H:i:s'),
                'operador'        => $this->session->userdata('pdv_operador_nome'),
                'terminal'        => $this->session->userdata('pdv_terminal_numero'),
            ],
        ]);
    }

    /**
     * Validar fiado com supervisor (quando excede limite)
     * AJAX POST: /pdv/validar_fiado_supervisor
     */
    public function validar_fiado_supervisor()
    {
        if ($this->input->method() !== 'post') {
            return $this->_json(['success' => false, 'message' => 'Método inválido'], 405);
        }

        $senha_supervisor = $this->input->post('senha_supervisor');

        if (empty($senha_supervisor)) {
            return $this->_json(['success' => false, 'message' => 'Senha do supervisor é obrigatória.']);
        }

        $supervisor = $this->_validar_supervisor($senha_supervisor);
        if (!$supervisor) {
            return $this->_json(['success' => false, 'message' => 'Senha de supervisor inválida.']);
        }

        // Audit
        $this->Pdv_model->registrar_audit([
            'terminal_id' => (int) $this->session->userdata('pdv_terminal_id'),
            'caixa_id'    => (int) $this->session->userdata('pdv_caixa_id'),
            'operador_id' => (int) $this->session->userdata('pdv_operador_id'),
            'acao'        => 'fiado_supervisor_autorizado',
            'entidade'    => 'user',
            'entidade_id' => (int) $supervisor->id,
            'detalhes'    => ['supervisor' => $supervisor->fullname],
            'ip'          => $this->input->ip_address(),
        ]);

        return $this->_json([
            'success'      => true,
            'supervisor_id' => (int) $supervisor->id,
            'message'      => 'Autorizado por ' . $supervisor->fullname,
        ]);
    }

    // =========================================================================
    // VALIDAÇÃO DE ESTOQUE (Fase 4)
    // =========================================================================

    /**
     * Valida estoque de todos os itens antes de finalizar venda
     * AJAX POST: /pdv/validar_estoque
     *
     * Recebe JSON de itens, re-verifica estoque no servidor,
     * adquire locks Redis por produto (30s TTL).
     *
     * Retorna: {valido: bool, erros: [...], lock_owner: string}
     */
    public function validar_estoque()
    {
        if ($this->input->method() !== 'post') {
            return $this->_json(['success' => false, 'message' => 'Método inválido'], 405);
        }

        $itens_json = $this->input->post('itens');
        $itens = json_decode($itens_json, true);

        if (empty($itens)) {
            return $this->_json(['success' => false, 'message' => 'Nenhum item informado.']);
        }

        $this->load->library('Estoque');

        // 1. Validar estoque de todos os itens
        $resultado = $this->estoque->validar_itens($itens);

        if (!$resultado['valido']) {
            return $this->_json([
                'success' => false,
                'valido'  => false,
                'erros'   => $resultado['erros'],
                'message' => $resultado['erros'][0]['message'],
            ]);
        }

        // 2. Adquirir locks Redis para todos os produtos (exceto genéricos)
        $product_ids = [];
        foreach ($itens as $item) {
            $pid = isset($item['product_id']) ? (int) $item['product_id'] : 0;
            if ($pid > 0) {
                $product_ids[] = $pid;
            }
        }
        $product_ids = array_unique($product_ids);

        $terminal_id = $this->session->userdata('pdv_terminal_id');
        $lock_owner  = 'pdv:' . $terminal_id . ':' . time() . ':' . mt_rand(1000, 9999);

        $lock_result = $this->estoque->adquirir_locks($product_ids, $lock_owner, 3, 500);

        if (!$lock_result['success']) {
            // Identificar quais produtos falharam
            $nomes_falha = [];
            foreach ($itens as $item) {
                $pid = isset($item['product_id']) ? (int) $item['product_id'] : 0;
                if (in_array($pid, $lock_result['failed'])) {
                    $nomes_falha[] = isset($item['nome']) ? $item['nome'] : 'Produto #' . $pid;
                }
            }

            return $this->_json([
                'success' => false,
                'valido'  => false,
                'message' => 'Produtos em uso por outro caixa: ' . implode(', ', array_unique($nomes_falha))
                    . '. Tente novamente em alguns segundos.',
            ]);
        }

        // 3. Re-validar estoque após adquirir locks (double-check)
        $resultado2 = $this->estoque->validar_itens($itens);

        if (!$resultado2['valido']) {
            // Liberar locks
            $this->estoque->liberar_locks($product_ids, $lock_owner);

            return $this->_json([
                'success' => false,
                'valido'  => false,
                'erros'   => $resultado2['erros'],
                'message' => $resultado2['erros'][0]['message'],
            ]);
        }

        // Tudo ok — locks adquiridos, estoque validado
        // O lock_owner será usado pelo finalizar_venda (Fase 5) para liberar
        return $this->_json([
            'success'    => true,
            'valido'     => true,
            'lock_owner' => $lock_owner,
            'message'    => 'Estoque validado.',
        ]);
    }

    /**
     * Libera locks de estoque (caso o operador cancele o pagamento)
     * AJAX POST: /pdv/liberar_locks_estoque
     */
    public function liberar_locks_estoque()
    {
        if ($this->input->method() !== 'post') {
            return $this->_json(['success' => false, 'message' => 'Método inválido'], 405);
        }

        $lock_owner = $this->input->post('lock_owner', true);
        $itens_json = $this->input->post('itens');
        $itens = json_decode($itens_json, true);

        if (empty($lock_owner) || empty($itens)) {
            return $this->_json(['success' => true]); // Nada a fazer
        }

        $this->load->library('Estoque');

        $product_ids = [];
        foreach ($itens as $item) {
            $pid = isset($item['product_id']) ? (int) $item['product_id'] : 0;
            if ($pid > 0) {
                $product_ids[] = $pid;
            }
        }

        $this->estoque->liberar_locks(array_unique($product_ids), $lock_owner);

        return $this->_json(['success' => true, 'message' => 'Locks liberados.']);
    }

    // =========================================================================
    // FASE 7: DESCONTOS
    // =========================================================================

    /**
     * Aplica desconto em um item individual (F9)
     * POST /pdv/aplicar_desconto_item
     *
     * Recebe: item_index, tipo_desconto ('percentual'|'valor'), valor_desconto, senha_supervisor (se necessário)
     * Retorna: desconto calculado, novo subtotal
     */
    public function aplicar_desconto_item()
    {
        if ($this->input->method() !== 'post') {
            return $this->_json(['success' => false, 'message' => 'Método inválido'], 405);
        }

        $item_index       = (int) $this->input->post('item_index', true);
        $tipo_desconto    = $this->input->post('tipo_desconto', true);
        $valor_desconto   = (float) $this->input->post('valor_desconto', true);
        $preco_unitario   = (float) $this->input->post('preco_unitario', true);
        $quantidade       = (float) $this->input->post('quantidade', true);
        $senha_supervisor = $this->input->post('senha_supervisor');

        // Validações básicas
        if (!in_array($tipo_desconto, ['percentual', 'valor'])) {
            return $this->_json(['success' => false, 'message' => 'Tipo de desconto inválido.']);
        }

        if ($valor_desconto <= 0) {
            return $this->_json(['success' => false, 'message' => 'Valor do desconto deve ser maior que zero.']);
        }

        if ($preco_unitario <= 0 || $quantidade <= 0) {
            return $this->_json(['success' => false, 'message' => 'Item inválido.']);
        }

        // Calcular desconto
        $subtotal_bruto = round($preco_unitario * $quantidade, 2);
        $desconto_calculado = 0;

        if ($tipo_desconto === 'percentual') {
            if ($valor_desconto > 100) {
                return $this->_json(['success' => false, 'message' => 'Percentual não pode ser maior que 100%.']);
            }
            $desconto_calculado = round($subtotal_bruto * ($valor_desconto / 100), 2);
        } else {
            if ($valor_desconto > $subtotal_bruto) {
                return $this->_json(['success' => false, 'message' => 'Desconto não pode ser maior que o subtotal do item.']);
            }
            $desconto_calculado = round($valor_desconto, 2);
        }

        // Calcular percentual efetivo para verificar limite
        $pct_efetivo = ($subtotal_bruto > 0) ? ($desconto_calculado / $subtotal_bruto) * 100 : 0;

        // Verificar limite do operador
        $limite_pct = (float) (getenv('PDV_DESCONTO_OPERADOR') ?: 5);
        $supervisor = null;
        $precisa_supervisor = ($pct_efetivo > $limite_pct);

        if ($precisa_supervisor) {
            if (empty($senha_supervisor)) {
                return $this->_json([
                    'success'            => false,
                    'precisa_supervisor' => true,
                    'message'            => 'Desconto de ' . number_format($pct_efetivo, 1) . '% excede o limite do operador (' . number_format($limite_pct, 0) . '%). Informe a senha do supervisor.',
                ]);
            }

            $supervisor = $this->_validar_supervisor($senha_supervisor);
            if (!$supervisor) {
                return $this->_json(['success' => false, 'message' => 'Senha de supervisor inválida.']);
            }
        }

        $novo_subtotal = round($subtotal_bruto - $desconto_calculado, 2);

        // Audit log
        $this->Pdv_model->registrar_audit([
            'terminal_id' => (int) $this->session->userdata('pdv_terminal_id'),
            'caixa_id'    => (int) $this->session->userdata('pdv_caixa_id'),
            'operador_id' => (int) $this->session->userdata('pdv_operador_id'),
            'acao'        => 'desconto_item',
            'entidade'    => 'item_carrinho',
            'entidade_id' => $item_index,
            'detalhes'    => [
                'tipo'               => $tipo_desconto,
                'valor_desconto'     => $valor_desconto,
                'desconto_calculado' => $desconto_calculado,
                'pct_efetivo'        => round($pct_efetivo, 2),
                'subtotal_bruto'     => $subtotal_bruto,
                'novo_subtotal'      => $novo_subtotal,
                'supervisor_id'      => $supervisor ? (int) $supervisor->id : null,
            ],
            'ip' => $this->input->ip_address(),
        ]);

        return $this->_json([
            'success'            => true,
            'desconto_tipo'      => $tipo_desconto,
            'desconto_valor'     => $valor_desconto,
            'desconto_calculado' => $desconto_calculado,
            'pct_efetivo'        => round($pct_efetivo, 2),
            'novo_subtotal'      => $novo_subtotal,
            'autorizado_por'     => $supervisor ? (int) $supervisor->id : null,
            'autorizado_nome'    => $supervisor ? $supervisor->fullname : null,
            'message'            => 'Desconto aplicado com sucesso.',
        ]);
    }

    /**
     * Aplica desconto na venda inteira (F10)
     * POST /pdv/aplicar_desconto_venda
     *
     * Recebe: tipo_desconto, valor_desconto, itens (JSON), senha_supervisor (se necessário)
     * Retorna: desconto total, rateio por item
     */
    public function aplicar_desconto_venda()
    {
        if ($this->input->method() !== 'post') {
            return $this->_json(['success' => false, 'message' => 'Método inválido'], 405);
        }

        $tipo_desconto    = $this->input->post('tipo_desconto', true);
        $valor_desconto   = (float) $this->input->post('valor_desconto', true);
        $itens_json       = $this->input->post('itens');
        $senha_supervisor = $this->input->post('senha_supervisor');

        $itens = json_decode($itens_json, true);

        // Validações
        if (!in_array($tipo_desconto, ['percentual', 'valor'])) {
            return $this->_json(['success' => false, 'message' => 'Tipo de desconto inválido.']);
        }

        if ($valor_desconto <= 0) {
            return $this->_json(['success' => false, 'message' => 'Valor do desconto deve ser maior que zero.']);
        }

        if (empty($itens)) {
            return $this->_json(['success' => false, 'message' => 'Nenhum item na venda.']);
        }

        // Calcular subtotal da venda (considerando descontos por item já aplicados)
        $subtotal_venda = 0;
        foreach ($itens as $item) {
            $item_bruto = round((float) $item['preco'] * (float) $item['quantidade'], 2);
            $desc_item  = isset($item['desconto_calculado']) ? (float) $item['desconto_calculado'] : 0;
            $subtotal_venda += round($item_bruto - $desc_item, 2);
        }
        $subtotal_venda = round($subtotal_venda, 2);

        if ($subtotal_venda <= 0) {
            return $this->_json(['success' => false, 'message' => 'Subtotal da venda é zero.']);
        }

        // Calcular desconto da venda
        $desconto_venda_calc = 0;
        if ($tipo_desconto === 'percentual') {
            if ($valor_desconto > 100) {
                return $this->_json(['success' => false, 'message' => 'Percentual não pode ser maior que 100%.']);
            }
            $desconto_venda_calc = round($subtotal_venda * ($valor_desconto / 100), 2);
        } else {
            if ($valor_desconto > $subtotal_venda) {
                return $this->_json(['success' => false, 'message' => 'Desconto não pode ser maior que o total da venda.']);
            }
            $desconto_venda_calc = round($valor_desconto, 2);
        }

        // Calcular percentual efetivo
        $pct_efetivo = ($subtotal_venda > 0) ? ($desconto_venda_calc / $subtotal_venda) * 100 : 0;

        // Verificar limite do operador
        $limite_pct = (float) (getenv('PDV_DESCONTO_OPERADOR') ?: 5);
        $supervisor = null;
        $precisa_supervisor = ($pct_efetivo > $limite_pct);

        if ($precisa_supervisor) {
            if (empty($senha_supervisor)) {
                return $this->_json([
                    'success'            => false,
                    'precisa_supervisor' => true,
                    'message'            => 'Desconto de ' . number_format($pct_efetivo, 1) . '% excede o limite do operador (' . number_format($limite_pct, 0) . '%). Informe a senha do supervisor.',
                ]);
            }

            $supervisor = $this->_validar_supervisor($senha_supervisor);
            if (!$supervisor) {
                return $this->_json(['success' => false, 'message' => 'Senha de supervisor inválida.']);
            }
        }

        // Rateio proporcional
        $rateio = [];
        $rateio_acumulado = 0;
        $ultimo_idx = count($itens) - 1;

        foreach ($itens as $idx => $item) {
            $item_bruto = round((float) $item['preco'] * (float) $item['quantidade'], 2);
            $desc_item  = isset($item['desconto_calculado']) ? (float) $item['desconto_calculado'] : 0;
            $item_liq   = round($item_bruto - $desc_item, 2);

            if ($idx === $ultimo_idx) {
                $rateio_valor = round($desconto_venda_calc - $rateio_acumulado, 2);
            } else {
                $proporcao = ($subtotal_venda > 0) ? $item_liq / $subtotal_venda : 0;
                $rateio_valor = round($desconto_venda_calc * $proporcao, 2);
                $rateio_acumulado += $rateio_valor;
            }

            $rateio[] = [
                'index'           => $idx,
                'desconto_rateio' => $rateio_valor,
                'novo_subtotal'   => round($item_liq - $rateio_valor, 2),
            ];
        }

        // Audit log
        $this->Pdv_model->registrar_audit([
            'terminal_id' => (int) $this->session->userdata('pdv_terminal_id'),
            'caixa_id'    => (int) $this->session->userdata('pdv_caixa_id'),
            'operador_id' => (int) $this->session->userdata('pdv_operador_id'),
            'acao'        => 'desconto_venda',
            'entidade'    => 'venda_carrinho',
            'entidade_id' => 0,
            'detalhes'    => [
                'tipo'               => $tipo_desconto,
                'valor_desconto'     => $valor_desconto,
                'desconto_calculado' => $desconto_venda_calc,
                'pct_efetivo'        => round($pct_efetivo, 2),
                'subtotal_venda'     => $subtotal_venda,
                'supervisor_id'      => $supervisor ? (int) $supervisor->id : null,
            ],
            'ip' => $this->input->ip_address(),
        ]);

        return $this->_json([
            'success'            => true,
            'desconto_tipo'      => $tipo_desconto,
            'desconto_valor'     => $valor_desconto,
            'desconto_calculado' => $desconto_venda_calc,
            'pct_efetivo'        => round($pct_efetivo, 2),
            'rateio'             => $rateio,
            'novo_total'         => round($subtotal_venda - $desconto_venda_calc, 2),
            'autorizado_por'     => $supervisor ? (int) $supervisor->id : null,
            'autorizado_nome'    => $supervisor ? $supervisor->fullname : null,
            'message'            => 'Desconto aplicado com sucesso.',
        ]);
    }

    // =========================================================================
    // FASE 9: DEVOLUÇÃO / TROCA
    // =========================================================================

    /**
     * Busca venda para devolução
     * GET: /pdv/buscar_venda_devolucao?invoice_id=T250309ABCDE
     */
    public function buscar_venda_devolucao()
    {
        $invoice_id = trim($this->input->get('invoice_id', true));

        if (empty($invoice_id)) {
            return $this->_json(['success' => false, 'message' => 'Informe o número do cupom.']);
        }

        $resultado = $this->Pdv_model->buscarVendaParaDevolucao($invoice_id);

        if (!$resultado) {
            return $this->_json(['success' => false, 'message' => 'Venda não encontrada.']);
        }

        $venda = $resultado['venda'];

        // Validar: venda não cancelada
        if ((int) $venda->status === 0) {
            return $this->_json(['success' => false, 'message' => 'Esta venda já foi cancelada.']);
        }

        // Verificar se todos os itens já foram totalmente devolvidos
        $algum_disponivel = false;
        $itens_response = [];
        foreach ($resultado['itens'] as $item) {
            if ($item->max_devolver > 0) {
                $algum_disponivel = true;
            }
            $itens_response[] = [
                'detail_id'     => (int) $item->detail_id,
                'product_id'    => (int) $item->product_id,
                'product_name'  => isset($item->nome_produto) ? $item->nome_produto : '',
                'product_code'  => isset($item->product_code) ? $item->product_code : '',
                'ean_gtin'      => isset($item->ean_gtin) ? $item->ean_gtin : '',
                'qty_original'  => (float) (isset($item->quantity) ? $item->quantity : 0),
                'preco'         => (float) (isset($item->price) ? $item->price : 0),
                'total'         => (float) (isset($item->total_price) ? $item->total_price : 0),
                'ja_devolvido'  => (float) $item->ja_devolvido,
                'max_devolver'  => (float) $item->max_devolver,
            ];
        }

        if (!$algum_disponivel) {
            return $this->_json(['success' => false, 'message' => 'Todos os itens desta venda já foram devolvidos.']);
        }

        // Calcular dias desde a venda
        $data_venda = strtotime($venda->created_at ?: $venda->date);
        $dias_desde_venda = (int) floor((time() - $data_venda) / 86400);

        // Limites do env
        $limite_operador = (float) (getenv('PDV_DEVOLUCAO_LIMITE_OPERADOR') ?: 50.00);
        $dias_limite     = (int) (getenv('PDV_DEVOLUCAO_DIAS_LIMITE') ?: 7);

        return $this->_json([
            'success' => true,
            'venda'   => [
                'id'              => (int) $venda->id,
                'invoice_id'      => $venda->invoice_id,
                'grand_total'     => (float) (isset($venda->total_amount) ? $venda->total_amount : 0),
                'date'            => $venda->date,
                'created_date'    => $venda->created_at,
                'operador_nome'   => $venda->operador_nome,
                'dias_desde_venda' => $dias_desde_venda,
            ],
            'itens'            => $itens_response,
            'limite_operador'  => $limite_operador,
            'dias_limite'      => $dias_limite,
        ]);
    }

    /**
     * Processa devolução de itens
     * POST: /pdv/processar_devolucao
     */
    public function processar_devolucao()
    {
        if ($this->input->method() !== 'post') {
            return $this->_json(['success' => false, 'message' => 'Método inválido'], 405);
        }

        $invoice_id       = trim($this->input->post('invoice_id', true));
        $itens_json       = $this->input->post('itens', true);
        $senha_supervisor = $this->input->post('senha_supervisor');

        if (empty($invoice_id) || empty($itens_json)) {
            return $this->_json(['success' => false, 'message' => 'Dados incompletos.']);
        }

        $itens = json_decode($itens_json, true);
        if (!is_array($itens) || count($itens) === 0) {
            return $this->_json(['success' => false, 'message' => 'Nenhum item selecionado para devolução.']);
        }

        // Buscar venda original
        $resultado = $this->Pdv_model->buscarVendaParaDevolucao($invoice_id);
        if (!$resultado) {
            return $this->_json(['success' => false, 'message' => 'Venda não encontrada.']);
        }

        $venda = $resultado['venda'];

        if ((int) $venda->status === 0) {
            return $this->_json(['success' => false, 'message' => 'Esta venda já foi cancelada.']);
        }

        // Calcular total da devolução e validar itens
        $total_devolucao = 0;
        $itens_validos = [];
        $itens_map = [];
        foreach ($resultado['itens'] as $item) {
            $itens_map[(int) $item->detail_id] = $item;
        }

        $motivos_validos = ['defeito', 'arrependimento', 'troca', 'erro_caixa'];

        foreach ($itens as $it) {
            $detail_id = (int) ($it['detail_id'] ?? 0);
            $qty       = (float) ($it['qty'] ?? 0);
            $motivo    = trim($it['motivo'] ?? '');

            if ($detail_id <= 0 || $qty <= 0) continue;

            if (!in_array($motivo, $motivos_validos)) {
                return $this->_json(['success' => false, 'message' => 'Motivo inválido: ' . $motivo]);
            }

            if (!isset($itens_map[$detail_id])) {
                return $this->_json(['success' => false, 'message' => 'Item não pertence a esta venda.']);
            }

            $original = $itens_map[$detail_id];
            if ($qty > $original->max_devolver + 0.001) {
                return $this->_json([
                    'success' => false,
                    'message' => 'Quantidade excede o permitido para "'
                        . ($original->nome_produto ?: $original->product_name)
                        . '" (máx: ' . $original->max_devolver . ').',
                ]);
            }

            $preco = (float) (isset($original->price) ? $original->price : 0);
            $total_devolucao += round($qty * $preco, 2);

            $itens_validos[] = [
                'product_id'   => (int) $original->product_id,
                'detail_id'    => $detail_id,
                'qty'          => $qty,
                'preco'        => $preco,
                'motivo'       => $motivo,
                'product_name' => isset($original->nome_produto) ? $original->nome_produto : '',
            ];
        }

        if (count($itens_validos) === 0) {
            return $this->_json(['success' => false, 'message' => 'Nenhum item válido para devolução.']);
        }

        $total_devolucao = round($total_devolucao, 2);

        // Verificar necessidade de autorização do supervisor
        $limite_operador = (float) (getenv('PDV_DEVOLUCAO_LIMITE_OPERADOR') ?: 50.00);
        $dias_limite     = (int) (getenv('PDV_DEVOLUCAO_DIAS_LIMITE') ?: 7);
        $data_venda      = strtotime($venda->created_at ?: $venda->date);
        $dias_desde_venda = (int) floor((time() - $data_venda) / 86400);

        $requer_supervisor = ($total_devolucao > $limite_operador) || ($dias_desde_venda > $dias_limite);

        $supervisor = null;
        if ($requer_supervisor) {
            if (empty($senha_supervisor)) {
                return $this->_json([
                    'success' => false,
                    'message' => 'Devolução requer autorização do supervisor.',
                    'requer_supervisor' => true,
                ]);
            }
            $supervisor = $this->_validar_supervisor($senha_supervisor);
            if (!$supervisor) {
                return $this->_json(['success' => false, 'message' => 'Senha do supervisor incorreta.']);
            }
        }

        // Gerar return_id
        $this->load->library('generators');
        $return_id = 'DEV' . date('ymd') . strtoupper($this->generators->generator(5));

        // Montar motivo geral (combinar motivos dos itens)
        $motivos = array_unique(array_column($itens_validos, 'motivo'));
        $motivo_geral = implode(', ', $motivos);

        $operador_id  = (int) $this->session->userdata('pdv_operador_id');
        $caixa_id     = (int) $this->session->userdata('pdv_caixa_id');
        $terminal_id  = (int) $this->session->userdata('pdv_terminal_id');

        // Processar devolução
        $result = $this->Pdv_model->processarDevolucao([
            'invoice_pk'    => (int) $venda->id,
            'invoice_id'    => $venda->invoice_id,
            'return_id'     => $return_id,
            'itens'         => $itens_validos,
            'total'         => $total_devolucao,
            'motivo_geral'  => $motivo_geral,
            'operador_id'   => $operador_id,
            'supervisor_id' => $supervisor ? (int) $supervisor->id : null,
            'caixa_id'      => $caixa_id,
            'terminal_id'   => $terminal_id,
            'customer_id'   => $venda->customer_id,
        ]);

        if (!$result['success']) {
            return $this->_json($result);
        }

        // Audit log
        $this->Pdv_model->registrar_audit([
            'terminal_id' => $terminal_id,
            'caixa_id'    => $caixa_id,
            'operador_id' => $operador_id,
            'acao'        => 'devolucao',
            'entidade'    => 'product_return',
            'entidade_id' => $result['return_pk'],
            'detalhes'    => [
                'return_id'     => $return_id,
                'invoice_id'    => $venda->invoice_id,
                'total'         => $total_devolucao,
                'itens_count'   => count($itens_validos),
                'motivo'        => $motivo_geral,
                'supervisor_id' => $supervisor ? (int) $supervisor->id : null,
            ],
            'ip' => $this->input->ip_address(),
        ]);

        return $this->_json([
            'success'     => true,
            'return_pk'   => $result['return_pk'],
            'return_id'   => $return_id,
            'total'       => $total_devolucao,
            'itens_count' => count($itens_validos),
            'message'     => 'Devolução processada com sucesso!',
        ]);
    }

    /**
     * Renderiza comprovante de devolução (80mm térmico)
     * GET: /pdv/comprovante_devolucao/{return_pk}
     */
    public function comprovante_devolucao($return_pk = 0)
    {
        $return_pk = (int) $return_pk;
        if ($return_pk <= 0) {
            $return_pk = (int) $this->input->get('return_id');
        }

        if ($return_pk <= 0) {
            show_404();
            return;
        }

        $devolucao = $this->Pdv_model->getDevolucao($return_pk);
        if (!$devolucao) {
            show_404();
            return;
        }

        $setting = $this->_get_setting();

        $data = [
            'devolucao' => $devolucao,
            'setting'   => $setting,
        ];

        $this->load->view('comprovante_devolucao', $data);
    }

    // =========================================================================
    // FASE 10: AUDIT LOG — REGISTRO DE EVENTOS DO FRONTEND
    // =========================================================================

    /**
     * Registra evento do frontend no audit log
     * AJAX POST: /pdv/registrar_evento
     *
     * Aceita apenas ações de uma whitelist conhecida.
     * Rate-limited: máximo 100 eventos por minuto por sessão.
     *
     * Recebe: acao (string), detalhes (JSON string)
     */
    public function registrar_evento()
    {
        if ($this->input->method() !== 'post') {
            return $this->_json(['success' => false, 'message' => 'Método inválido'], 405);
        }

        $acao      = trim($this->input->post('acao', true));
        $detalhes  = $this->input->post('detalhes', true);

        // Whitelist de ações aceitas via frontend
        $acoes_permitidas = [
            'venda_inicio',
            'venda_item',
            'venda_item_cancelado',
            'venda_cancelada',
            'venda_item_generico',
            'gaveta_aberta',
            'fiado_venda',
            'consulta_preco',
        ];

        if (empty($acao) || !in_array($acao, $acoes_permitidas)) {
            return $this->_json(['success' => false, 'message' => 'Ação não permitida.']);
        }

        // Rate limiting: max 100 eventos por minuto por sessão
        $rate_key = 'pdv_audit_rate_' . session_id();
        $rate_count = (int) $this->session->userdata($rate_key);
        $rate_window = (int) $this->session->userdata($rate_key . '_ts');
        $now = time();

        if ($rate_window && ($now - $rate_window) < 60) {
            if ($rate_count >= 100) {
                return $this->_json(['success' => false, 'message' => 'Rate limit excedido. Aguarde.']);
            }
            $this->session->set_userdata($rate_key, $rate_count + 1);
        } else {
            // Resetar janela
            $this->session->set_userdata($rate_key, 1);
            $this->session->set_userdata($rate_key . '_ts', $now);
        }

        // Parse detalhes
        $detalhes_arr = [];
        if (!empty($detalhes)) {
            $decoded = json_decode($detalhes, true);
            if (is_array($decoded)) {
                $detalhes_arr = $decoded;
            }
        }

        $terminal_id = (int) $this->session->userdata('pdv_terminal_id');
        $caixa_id    = (int) $this->session->userdata('pdv_caixa_id');
        $operador_id = (int) $this->session->userdata('pdv_operador_id');

        // Extrair entidade e entidade_id dos detalhes se fornecidos
        $entidade    = isset($detalhes_arr['entidade']) ? $detalhes_arr['entidade'] : null;
        $entidade_id = isset($detalhes_arr['entidade_id']) ? (int) $detalhes_arr['entidade_id'] : null;

        $this->Pdv_model->registrar_audit([
            'terminal_id' => $terminal_id,
            'caixa_id'    => $caixa_id,
            'operador_id' => $operador_id,
            'acao'        => $acao,
            'entidade'    => $entidade,
            'entidade_id' => $entidade_id,
            'detalhes'    => $detalhes_arr,
            'ip'          => $this->input->ip_address(),
        ]);

        return $this->_json(['success' => true]);
    }

    // =========================================================================
    // DISPLAY CLIENTE (público — sem auth)
    // =========================================================================

    /**
     * Display de cliente — tela voltada para o consumidor
     * Rota pública: /pdv/display/{terminal_numero}
     *
     * @param string $terminal_numero  Número do terminal (ex: '001')
     */
    public function display($terminal_numero = null)
    {
        if (empty($terminal_numero)) {
            show_404();
            return;
        }

        // Validar terminal existe e está ativo
        $terminal = $this->Pdv_model->get_terminal_by_numero($terminal_numero);
        if (!$terminal) {
            show_404();
            return;
        }

        // Cabeçalhos de segurança — display não deve ser indexado
        $this->output->set_header('X-Robots-Tag: noindex, nofollow');
        $this->output->set_header('X-Frame-Options: SAMEORIGIN');

        $setting = $this->_get_setting();

        $data = [
            'terminal' => $terminal,
            'setting'  => $setting,
        ];

        $this->load->view('display', $data);
    }

    // =========================================================================
    // AUTO-FECHAMENTO DE CAIXAS (cron / CLI)
    // =========================================================================

    /**
     * Fecha automaticamente caixas abertos há mais de 24 horas.
     *
     * Pode ser chamado por:
     *   - CLI: php index.php pdv auto_fechamento
     *   - HTTP com token: GET /pdv/auto_fechamento?token=<PDV_CRON_TOKEN>
     *
     * Protegido: apenas CLI ou token válido. Não requer sessão PDV.
     */
    public function auto_fechamento()
    {
        // Segurança: apenas CLI ou token de cron
        $is_cli = $this->input->is_cli_request();
        $token  = $this->input->get('token', true);
        $expected_token = getenv('PDV_CRON_TOKEN') ?: '';

        if (!$is_cli && (empty($expected_token) || $token !== $expected_token)) {
            show_404();
            return;
        }

        // Buscar caixas abertos há mais de 24 horas
        $caixas = $this->Pdv_model->getCaixasAbertosExpirados(24);

        $fechados = 0;
        foreach ($caixas as $caixa) {
            // Obter leitura X para valores
            $leitura = $this->Pdv_model->leituraX((int) $caixa->id);

            $dinheiro_esperado = isset($leitura['total_por_forma']['dinheiro'])
                ? (float) $leitura['total_por_forma']['dinheiro']
                : 0;

            $this->Pdv_model->fecharCaixa([
                'caixa_id'       => (int) $caixa->id,
                'operador_id'    => (int) $caixa->operador_id,
                'valor_contado'  => $dinheiro_esperado,
                'diferenca'      => 0,
                'total_vendas'   => (float) ($leitura['total_vendas'] ?? 0),
                'qtd_vendas'     => (int) ($leitura['qtd_vendas'] ?? 0),
                'observacao'     => 'Fechamento automático por inatividade (caixa aberto > 24h)',
                'saldo_estimado' => (float) ($leitura['saldo_estimado'] ?? 0),
            ]);

            // Audit log
            $this->Pdv_model->registrar_audit([
                'terminal_id' => (int) $caixa->terminal_id,
                'caixa_id'    => (int) $caixa->id,
                'operador_id' => (int) $caixa->operador_id,
                'acao'        => 'auto_fechamento',
                'entidade'    => 'pdv_caixa',
                'entidade_id' => (int) $caixa->id,
                'detalhes'    => [
                    'motivo'       => 'inatividade_24h',
                    'aberto_em'    => $caixa->aberto_em,
                    'total_vendas' => $leitura['total_vendas'] ?? 0,
                ],
                'ip' => $is_cli ? '127.0.0.1' : $this->input->ip_address(),
            ]);

            $fechados++;
        }

        if ($is_cli) {
            echo "Auto-fechamento concluído: {$fechados} caixa(s) fechado(s).\n";
        } else {
            return $this->_json([
                'success'  => true,
                'message'  => "Auto-fechamento: {$fechados} caixa(s) fechado(s).",
                'fechados' => $fechados,
            ]);
        }
    }

    // =========================================================================
    // HELPERS PRIVADOS
    // =========================================================================

    /**
     * Valida senha de supervisor
     *
     * Busca usuários com role 'supervisor' ou admin (user_type=1),
     * tenta password_verify contra cada um.
     *
     * @param string $senha  Senha informada
     * @return object|false  Objeto do supervisor ou false
     */
    private function _validar_supervisor($senha)
    {
        if (empty($senha)) {
            return false;
        }

        $supervisores = $this->Pdv_model->get_supervisores();

        foreach ($supervisores as $sup) {
            if (password_verify($senha, $sup->password)) {
                return $sup;
            }
        }

        return false;
    }

    /**
     * Retorna JSON e encerra a resposta
     */
    private function _json($data, $status = 200)
    {
        // Always include fresh CSRF token for JS to update
        if ($this->config->item('csrf_protection')) {
            $data['csrf_token'] = $this->security->get_csrf_hash();
            $data['csrf_name']  = $this->security->get_csrf_token_name();
        }
        $this->output
            ->set_status_header($status)
            ->set_content_type('application/json')
            ->set_output(json_encode($data));
    }

    /**
     * Verifica se o operador está logado no PDV
     * Redireciona para login se não estiver
     */
    private function _require_pdv_session()
    {
        if ($this->session->userdata('pdv_logado') !== true) {
            $terminal_numero = $this->session->userdata('pdv_terminal_numero') ?: '001';
            redirect('pdv/login/' . $terminal_numero);
        }
    }

    /**
     * Busca configurações do sistema (para status bar)
     */
    private function _get_setting()
    {
        $this->load->model('template/Template_model', 'template_model');
        return $this->template_model->setting();
    }
}
