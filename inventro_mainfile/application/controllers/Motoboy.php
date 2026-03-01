<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Portal do Motoboy (Entregador)
 *
 * Controller público (extends CI_Controller, sem RBAC).
 * Autenticação própria via sessão: motoboy_logado, motoboy_id, motoboy_nome.
 *
 * Funcionalidades:
 * - Login/logout por telefone + senha (bcrypt)
 * - Pool de entregas disponíveis (pronto_coleta sem entregador)
 * - Aceitar entrega (race-condition safe, 1 por vez)
 * - Coletar / Entregar (atualiza status em tempo real)
 * - Histórico de entregas + ganhos
 */
class Motoboy extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Motoboy_model');
    }

    // =========================================
    // Autenticação
    // =========================================

    /**
     * Página inicial — redireciona para login ou dashboard
     */
    public function index() {
        if ($this->session->userdata('motoboy_logado')) {
            redirect('motoboy/dashboard');
        } else {
            redirect('motoboy/login');
        }
    }

    /**
     * Login do motoboy
     * GET: mostra tela de login
     * POST: autentica telefone + senha
     */
    public function login() {
        if ($this->session->userdata('motoboy_logado')) {
            redirect('motoboy/dashboard');
            return;
        }

        if ($this->input->method() === 'post') {
            $telefone = $this->input->post('telefone', TRUE);
            $senha    = $this->input->post('senha', TRUE);

            $entregador = $this->Motoboy_model->autenticar($telefone, $senha);

            if ($entregador) {
                $this->session->set_userdata([
                    'motoboy_logado'   => true,
                    'motoboy_id'       => (int) $entregador->id,
                    'motoboy_nome'     => $entregador->nome,
                    'motoboy_telefone' => $entregador->telefone
                ]);
                redirect('motoboy/dashboard');
                return;
            } else {
                $data['erro'] = 'Telefone ou senha incorretos.';
            }
        }

        // Buscar dados da loja para exibir logo/nome
        $data['loja'] = $this->db->get('setting')->row();
        $this->load->view('motoboy/login', $data);
    }

    /**
     * Logout
     */
    public function logout() {
        $this->session->unset_userdata(['motoboy_logado', 'motoboy_id', 'motoboy_nome', 'motoboy_telefone']);
        redirect('motoboy/login');
    }

    // =========================================
    // Dashboard e Pool
    // =========================================

    /**
     * Dashboard principal do motoboy
     */
    public function dashboard() {
        $this->_check_auth();

        $motoboy_id = (int) $this->session->userdata('motoboy_id');

        $data['motoboy']        = $this->db->where('id', $motoboy_id)->get('entregadores')->row();
        $data['entrega_ativa']  = $this->Motoboy_model->get_entrega_ativa($motoboy_id);
        $data['pool']           = $this->Motoboy_model->get_pool();
        $data['resumo']         = $this->Motoboy_model->get_resumo_ganhos($motoboy_id);
        $data['loja']           = $this->db->get('setting')->row();

        $this->load->view('motoboy/dashboard', $data);
    }

    /**
     * Página de histórico
     */
    public function historico() {
        $this->_check_auth();

        $motoboy_id = (int) $this->session->userdata('motoboy_id');

        $data['motoboy']   = $this->db->where('id', $motoboy_id)->get('entregadores')->row();
        $data['resumo']    = $this->Motoboy_model->get_resumo_ganhos($motoboy_id);
        $data['entregas']  = $this->Motoboy_model->get_historico($motoboy_id, 'todos', 100);
        $data['loja']      = $this->db->get('setting')->row();

        $this->load->view('motoboy/historico', $data);
    }

    // =========================================
    // Ações do Motoboy
    // =========================================

    /**
     * Aceitar entrega do pool
     */
    public function aceitar($order_id) {
        $this->_check_auth();

        $motoboy_id   = (int) $this->session->userdata('motoboy_id');
        $motoboy_nome = $this->session->userdata('motoboy_nome');

        // Buscar taxa fixa do motoboy
        $motoboy = $this->db->where('id', $motoboy_id)->get('entregadores')->row();
        $taxa = $motoboy ? (float) $motoboy->taxa_entrega_fixa : 5.00;

        $result = $this->Motoboy_model->aceitar_entrega((int) $order_id, $motoboy_id, $motoboy_nome, $taxa);

        if ($this->input->is_ajax_request()) {
            header('Content-Type: application/json');
            echo json_encode($result);
            return;
        }

        if ($result['success']) {
            $this->session->set_flashdata('message', $result['message']);
        } else {
            $this->session->set_flashdata('exception', $result['message']);
        }

        redirect('motoboy/dashboard');
    }

    /**
     * Registrar coleta (pegou a mercadoria)
     */
    public function coletar($order_id) {
        $this->_check_auth();

        $motoboy_id = (int) $this->session->userdata('motoboy_id');
        $result = $this->Motoboy_model->registrar_coleta((int) $order_id, $motoboy_id);

        if ($this->input->is_ajax_request()) {
            header('Content-Type: application/json');
            echo json_encode($result);
            return;
        }

        if ($result['success']) {
            $this->session->set_flashdata('message', $result['message']);
        } else {
            $this->session->set_flashdata('exception', $result['message']);
        }

        redirect('motoboy/dashboard');
    }

    /**
     * Registrar entrega (entregou ao cliente)
     */
    public function entregar($order_id) {
        $this->_check_auth();

        $motoboy_id = (int) $this->session->userdata('motoboy_id');
        $result = $this->Motoboy_model->registrar_entrega((int) $order_id, $motoboy_id);

        if ($this->input->is_ajax_request()) {
            header('Content-Type: application/json');
            echo json_encode($result);
            return;
        }

        if ($result['success']) {
            $this->session->set_flashdata('message', $result['message']);
        } else {
            $this->session->set_flashdata('exception', $result['message']);
        }

        redirect('motoboy/dashboard');
    }

    // =========================================
    // APIs JSON (polling)
    // =========================================

    /**
     * API: entregas disponíveis no pool (polling 15s)
     */
    public function api_pool() {
        $this->_check_auth_ajax();

        $pool = $this->Motoboy_model->get_pool();

        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'pool'    => $pool,
            'count'   => count($pool)
        ]);
    }

    /**
     * API: entrega ativa do motoboy logado
     */
    public function api_minha_entrega() {
        $this->_check_auth_ajax();

        $motoboy_id    = (int) $this->session->userdata('motoboy_id');
        $entrega_ativa = $this->Motoboy_model->get_entrega_ativa($motoboy_id);

        header('Content-Type: application/json');
        echo json_encode([
            'success'       => true,
            'entrega_ativa' => $entrega_ativa,
            'tem_ativa'     => !empty($entrega_ativa)
        ]);
    }

    /**
     * API: histórico de entregas
     */
    public function api_historico() {
        $this->_check_auth_ajax();

        $motoboy_id = (int) $this->session->userdata('motoboy_id');
        $periodo    = $this->input->get('periodo') ?: 'todos';

        $entregas = $this->Motoboy_model->get_historico($motoboy_id, $periodo, 50);
        $resumo   = $this->Motoboy_model->get_resumo_ganhos($motoboy_id);

        header('Content-Type: application/json');
        echo json_encode([
            'success'  => true,
            'entregas' => $entregas,
            'resumo'   => $resumo
        ]);
    }

    // =========================================
    // Métodos privados
    // =========================================

    /**
     * Verificar autenticação do motoboy (para páginas)
     */
    private function _check_auth() {
        if (!$this->session->userdata('motoboy_logado')) {
            redirect('motoboy/login');
            exit;
        }
    }

    /**
     * Verificar autenticação para AJAX (retorna 401 JSON)
     */
    private function _check_auth_ajax() {
        if (!$this->session->userdata('motoboy_logado')) {
            http_response_code(401);
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Não autorizado']);
            exit;
        }
    }
}
