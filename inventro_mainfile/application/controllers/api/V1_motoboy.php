<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . 'controllers/api/Api_controller.php';

/**
 * API v1 — App do Entregador (Motoboy)
 *
 * Todos endpoints retornam JSON puro.
 * Autenticação via JWT (Authorization: Bearer {token}).
 *
 * Reutiliza os mesmos Models da versão web:
 * - Motoboy_model (auth, pool, aceitar, coletar, entregar, histórico)
 * - Entregadores table (perfil, ganhos)
 */
class V1_motoboy extends Api_controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Motoboy_model');
    }

    // =========================================
    // Autenticação
    // =========================================

    /**
     * POST /api/v1/motoboy/auth/login
     *
     * Body: {"telefone": "11999999999", "senha": "abc123"}
     * Response: {success, access_token, refresh_token, expires_in, motoboy: {...}}
     */
    public function login() {
        $this->_require_method('POST');
        $this->_check_rate_limit('login');

        $input = $this->_get_json_body();

        $telefone = isset($input['telefone']) ? trim($input['telefone']) : '';
        $senha    = isset($input['senha'])    ? $input['senha']          : '';

        if (empty($telefone) || empty($senha)) {
            $this->_error('Telefone e senha são obrigatórios', 400);
        }

        $entregador = $this->Motoboy_model->autenticar($telefone, $senha);

        if (!$entregador) {
            $this->_error('Telefone ou senha incorretos', 401);
        }

        // Gerar tokens JWT
        $tokens = $this->jwt_auth->generate_tokens(
            $entregador->id,
            'motoboy',
            ['nome' => $entregador->nome]
        );

        $this->_success([
            'access_token'  => $tokens['access_token'],
            'refresh_token' => $tokens['refresh_token'],
            'expires_in'    => $tokens['expires_in'],
            'token_type'    => $tokens['token_type'],
            'motoboy' => [
                'id'       => (int) $entregador->id,
                'nome'     => $entregador->nome,
                'telefone' => $entregador->telefone,
                'status'   => $entregador->status
            ]
        ]);
    }

    /**
     * POST /api/v1/motoboy/auth/refresh
     *
     * Body: {"refresh_token": "..."}
     * Response: {success, access_token, refresh_token, expires_in}
     */
    public function refresh() {
        $this->_require_method('POST');

        $input = $this->_get_json_body();
        $refresh_token = isset($input['refresh_token']) ? $input['refresh_token'] : '';

        if (empty($refresh_token)) {
            $this->_error('Refresh token é obrigatório', 400);
        }

        $tokens = $this->jwt_auth->refresh($refresh_token);

        if (!$tokens) {
            $this->_error('Refresh token inválido ou expirado. Faça login novamente.', 401);
        }

        $this->_success([
            'access_token'  => $tokens['access_token'],
            'refresh_token' => $tokens['refresh_token'],
            'expires_in'    => $tokens['expires_in'],
            'token_type'    => $tokens['token_type']
        ]);
    }

    /**
     * POST /api/v1/motoboy/auth/logout
     *
     * Revoga todos os tokens do motoboy.
     */
    public function logout() {
        $this->_require_method('POST');
        $auth = $this->_require_auth('motoboy');

        $this->jwt_auth->revoke($auth->sub, 'motoboy');

        $this->_success(['message' => 'Logout realizado com sucesso']);
    }

    // =========================================
    // Perfil
    // =========================================

    /**
     * GET /api/v1/motoboy/perfil
     *
     * Retorna dados do entregador logado.
     */
    public function perfil() {
        $this->_require_method('GET');
        $auth = $this->_require_auth('motoboy');
        $this->_check_rate_limit('auth', 'user_' . $auth->sub);

        $motoboy = $this->db
            ->select('id, nome, telefone, status, taxa_entrega_fixa, total_entregas, total_ganhos, ultimo_login')
            ->where('id', (int) $auth->sub)
            ->where('ativo', 1)
            ->get('entregadores')
            ->row();

        if (!$motoboy) {
            $this->_error('Entregador não encontrado ou desativado', 404);
        }

        $this->_success([
            'motoboy' => [
                'id'               => (int) $motoboy->id,
                'nome'             => $motoboy->nome,
                'telefone'         => $motoboy->telefone,
                'status'           => $motoboy->status,
                'taxa_por_entrega' => (float) $motoboy->taxa_entrega_fixa,
                'total_entregas'   => (int) $motoboy->total_entregas,
                'total_ganhos'     => (float) $motoboy->total_ganhos,
                'ultimo_login'     => $motoboy->ultimo_login
            ]
        ]);
    }

    // =========================================
    // Pool de Entregas
    // =========================================

    /**
     * GET /api/v1/motoboy/pool
     *
     * Lista pedidos disponíveis para aceitar (status=pronto_coleta, sem entregador).
     */
    public function pool() {
        $this->_require_method('GET');
        $auth = $this->_require_auth('motoboy');
        $this->_check_rate_limit('auth', 'user_' . $auth->sub);

        $pool = $this->Motoboy_model->get_pool();

        $result = [];
        foreach ($pool as $order) {
            $result[] = [
                'id'               => (int) $order->id,
                'order_number'     => $order->order_number,
                'cliente_nome'     => $order->cliente_nome,
                'cliente_endereco' => $order->cliente_endereco,
                'zona_nome'        => $order->zona_nome,
                'total'            => (float) $order->total,
                'taxa_entrega'     => (float) $order->taxa_entrega,
                'forma_pagamento'  => $order->forma_pagamento,
                'troco_para'       => $order->troco_para ? (float) $order->troco_para : null,
                'tipo_entrega'     => $order->tipo_entrega,
                'hora_pronto'      => $order->hora_pronto_coleta,
                'created_at'       => $order->created_at
            ];
        }

        $this->_success([
            'pool'  => $result,
            'count' => count($result)
        ]);
    }

    // =========================================
    // Entrega Ativa
    // =========================================

    /**
     * GET /api/v1/motoboy/entrega/ativa
     *
     * Retorna a entrega em andamento do motoboy (se houver).
     */
    public function entrega_ativa() {
        $this->_require_method('GET');
        $auth = $this->_require_auth('motoboy');
        $this->_check_rate_limit('auth', 'user_' . $auth->sub);

        $entrega = $this->Motoboy_model->get_entrega_ativa((int) $auth->sub);

        if (!$entrega) {
            $this->_success([
                'tem_ativa' => false,
                'entrega'   => null
            ]);
            return;
        }

        $items = [];
        if (!empty($entrega->items)) {
            foreach ($entrega->items as $item) {
                $items[] = [
                    'product_name' => $item->product_name,
                    'quantity'     => (int) $item->quantity,
                    'unit_price'   => (float) $item->unit_price,
                    'total_price'  => (float) $item->total_price
                ];
            }
        }

        $this->_success([
            'tem_ativa' => true,
            'entrega'   => [
                'id'                   => (int) $entrega->id,
                'order_number'         => $entrega->order_number,
                'cliente_nome'         => $entrega->cliente_nome,
                'cliente_telefone'     => $entrega->cliente_telefone,
                'cliente_endereco'     => $entrega->cliente_endereco,
                'cliente_complemento'  => $entrega->cliente_complemento ?? '',
                'zona_nome'            => $entrega->zona_nome,
                'total'                => (float) $entrega->total,
                'taxa_entrega'         => (float) $entrega->taxa_entrega,
                'forma_pagamento'      => $entrega->forma_pagamento,
                'troco_para'           => $entrega->troco_para ? (float) $entrega->troco_para : null,
                'observacao'           => $entrega->observacao ?? '',
                'status'               => $entrega->status,
                'items'                => $items,
                'valor_ganho'          => $entrega->entrega_info ? (float) $entrega->entrega_info->valor_ganho : 0,
                'aceito_em'            => $entrega->entrega_info ? $entrega->entrega_info->aceito_em : null,
                'coleta_status'        => $entrega->entrega_info ? $entrega->entrega_info->status : null
            ]
        ]);
    }

    // =========================================
    // Ações de Entrega
    // =========================================

    /**
     * POST /api/v1/motoboy/entrega/{id}/aceitar
     *
     * Aceitar entrega do pool (race-condition safe).
     */
    public function aceitar($order_id) {
        $this->_require_method('POST');
        $auth = $this->_require_auth('motoboy');
        $this->_check_rate_limit('auth', 'user_' . $auth->sub);

        $motoboy_id = (int) $auth->sub;

        // Buscar taxa fixa do motoboy
        $motoboy = $this->db->where('id', $motoboy_id)->get('entregadores')->row();
        if (!$motoboy || !$motoboy->ativo) {
            $this->_error('Entregador não encontrado ou desativado', 404);
        }

        $taxa = (float) $motoboy->taxa_entrega_fixa;
        $result = $this->Motoboy_model->aceitar_entrega(
            (int) $order_id,
            $motoboy_id,
            $motoboy->nome,
            $taxa
        );

        if ($result['success']) {
            $this->_success(['message' => $result['message']]);
        } else {
            $this->_error($result['message'], 409);
        }
    }

    /**
     * POST /api/v1/motoboy/entrega/{id}/coletar
     *
     * Registrar que pegou a mercadoria.
     */
    public function coletar($order_id) {
        $this->_require_method('POST');
        $auth = $this->_require_auth('motoboy');
        $this->_check_rate_limit('auth', 'user_' . $auth->sub);

        $result = $this->Motoboy_model->registrar_coleta(
            (int) $order_id,
            (int) $auth->sub
        );

        if ($result['success']) {
            $this->_success(['message' => $result['message']]);
        } else {
            $this->_error($result['message'], 400);
        }
    }

    /**
     * POST /api/v1/motoboy/entrega/{id}/entregar
     *
     * Registrar que entregou ao cliente.
     */
    public function entregar($order_id) {
        $this->_require_method('POST');
        $auth = $this->_require_auth('motoboy');
        $this->_check_rate_limit('auth', 'user_' . $auth->sub);

        $result = $this->Motoboy_model->registrar_entrega(
            (int) $order_id,
            (int) $auth->sub
        );

        if ($result['success']) {
            $this->_success(['message' => $result['message']]);
        } else {
            $this->_error($result['message'], 400);
        }
    }

    // =========================================
    // Histórico e Ganhos
    // =========================================

    /**
     * GET /api/v1/motoboy/historico
     *
     * Query params: ?periodo=hoje|semana|mes|todos&limit=50
     */
    public function historico() {
        $this->_require_method('GET');
        $auth = $this->_require_auth('motoboy');
        $this->_check_rate_limit('auth', 'user_' . $auth->sub);

        $periodo = $this->input->get('periodo') ?: 'todos';
        $limit   = min(100, max(1, (int) ($this->input->get('limit') ?: 50)));

        $valid_periodos = ['hoje', 'semana', 'mes', 'todos'];
        if (!in_array($periodo, $valid_periodos)) {
            $periodo = 'todos';
        }

        $entregas = $this->Motoboy_model->get_historico(
            (int) $auth->sub,
            $periodo,
            $limit
        );

        $result = [];
        foreach ($entregas as $e) {
            $result[] = [
                'order_number'  => $e->order_number,
                'cliente_nome'  => $e->cliente_nome,
                'zona_nome'     => $e->zona_nome,
                'order_total'   => (float) $e->order_total,
                'valor_ganho'   => (float) $e->valor_ganho,
                'aceito_em'     => $e->aceito_em,
                'coletado_em'   => $e->coletado_em,
                'entregue_em'   => $e->entregue_em
            ];
        }

        $this->_success([
            'periodo'  => $periodo,
            'entregas' => $result,
            'count'    => count($result)
        ]);
    }

    /**
     * GET /api/v1/motoboy/ganhos
     *
     * Resumo de ganhos: hoje, semana, mês.
     */
    public function ganhos() {
        $this->_require_method('GET');
        $auth = $this->_require_auth('motoboy');
        $this->_check_rate_limit('auth', 'user_' . $auth->sub);

        $resumo = $this->Motoboy_model->get_resumo_ganhos((int) $auth->sub);

        $this->_success([
            'ganhos' => [
                'hoje' => [
                    'valor' => $resumo->hoje_valor,
                    'qtd'   => $resumo->hoje_qtd
                ],
                'semana' => [
                    'valor' => $resumo->semana_valor,
                    'qtd'   => $resumo->semana_qtd
                ],
                'mes' => [
                    'valor' => $resumo->mes_valor,
                    'qtd'   => $resumo->mes_qtd
                ]
            ]
        ]);
    }

    // =========================================
    // Device Token (Push Notifications)
    // =========================================

    /**
     * POST /api/v1/motoboy/device
     *
     * Registrar FCM token para push notifications.
     * Body: {"fcm_token": "...", "platform": "android|ios"}
     */
    public function registrar_device() {
        $this->_require_method('POST');
        $auth = $this->_require_auth('motoboy');

        $input = $this->_get_json_body();
        $fcm_token = isset($input['fcm_token']) ? trim($input['fcm_token']) : '';
        $platform  = isset($input['platform'])  ? $input['platform']        : 'android';

        if (empty($fcm_token)) {
            $this->_error('FCM token é obrigatório', 400);
        }

        $valid_platforms = ['android', 'ios', 'web'];
        if (!in_array($platform, $valid_platforms)) {
            $platform = 'android';
        }

        // Upsert: se token já existe, atualiza o user associado
        $existing = $this->db->where('fcm_token', $fcm_token)->get('device_tokens')->row();

        if ($existing) {
            $this->db->where('id', $existing->id)->update('device_tokens', [
                'user_id'   => (int) $auth->sub,
                'user_type' => 'motoboy',
                'platform'  => $platform
            ]);
        } else {
            $this->db->insert('device_tokens', [
                'user_id'   => (int) $auth->sub,
                'user_type' => 'motoboy',
                'fcm_token' => $fcm_token,
                'platform'  => $platform
            ]);
        }

        $this->_success(['message' => 'Device registrado com sucesso']);
    }
}
