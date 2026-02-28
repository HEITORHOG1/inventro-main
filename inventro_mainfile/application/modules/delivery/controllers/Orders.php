<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Controller para Pedidos Online
 * Gerencia pedidos recebidos pelo cardápio digital
 */
class Orders extends MX_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('delivery/delivery_model');
        $this->load->library(['session', 'form_validation']);
        $this->load->helper(['url', 'form']);
        
        // Verificar login
        if (!$this->session->userdata('isLogIn')) {
            redirect('login');
        }
    }

    /**
     * Lista de pedidos
     */
    public function index() {
        $data['title'] = 'Pedidos Online';
        
        // Filtros
        $filters = [
            'status' => $this->input->get('status'),
            'data_inicio' => $this->input->get('data_inicio'),
            'data_fim' => $this->input->get('data_fim')
        ];
        
        $data['orders'] = $this->delivery_model->get_orders($filters);
        $data['status_counts'] = $this->delivery_model->count_orders_by_status();
        $data['current_filters'] = $filters;
        
        $this->load->view('templates/header', $data);
        $this->load->view('delivery/orders_list', $data);
        $this->load->view('templates/footer');
    }

    /**
     * Detalhes do pedido
     */
    public function view($id) {
        $data['title'] = 'Detalhes do Pedido';
        $data['order'] = $this->delivery_model->get_order($id);
        
        if (!$data['order']) {
            $this->session->set_flashdata('error', 'Pedido não encontrado.');
            redirect('delivery/orders');
            return;
        }
        
        $this->load->view('templates/header', $data);
        $this->load->view('delivery/order_view', $data);
        $this->load->view('templates/footer');
    }

    /**
     * Atualizar status do pedido
     */
    public function update_status($id) {
        $status = $this->input->post('status');
        
        $valid_statuses = ['pendente', 'confirmado', 'preparando', 'saiu_entrega', 'entregue', 'cancelado'];
        
        if (!in_array($status, $valid_statuses)) {
            $this->session->set_flashdata('error', 'Status inválido.');
            redirect('delivery/orders/view/' . $id);
            return;
        }

        if ($this->delivery_model->update_order_status($id, $status)) {
            $this->session->set_flashdata('success', 'Status atualizado para: ' . ucfirst(str_replace('_', ' ', $status)));
        } else {
            $this->session->set_flashdata('error', 'Erro ao atualizar status.');
        }

        redirect('delivery/orders/view/' . $id);
    }

    /**
     * Atualizar status via AJAX
     */
    public function ajax_update_status() {
        header('Content-Type: application/json');
        
        $id = $this->input->post('order_id');
        $status = $this->input->post('status');
        
        $result = $this->delivery_model->update_order_status($id, $status);
        
        echo json_encode([
            'success' => $result,
            'message' => $result ? 'Status atualizado!' : 'Erro ao atualizar'
        ]);
    }

    /**
     * Imprimir pedido
     */
    public function print_order($id) {
        $data['order'] = $this->delivery_model->get_order($id);
        
        if (!$data['order']) {
            echo 'Pedido não encontrado.';
            return;
        }
        
        $this->load->view('delivery/order_print', $data);
    }

    /**
     * API para buscar pedidos (AJAX)
     */
    public function api_list() {
        header('Content-Type: application/json');
        
        $filters = [
            'status' => $this->input->get('status'),
            'limit' => $this->input->get('limit') ?: 50
        ];
        
        $orders = $this->delivery_model->get_orders($filters);
        $counts = $this->delivery_model->count_orders_by_status();
        
        echo json_encode([
            'success' => true,
            'orders' => $orders,
            'counts' => $counts
        ]);
    }

    /**
     * Estatísticas Dashboard
     */
    public function stats() {
        header('Content-Type: application/json');
        
        $period = $this->input->get('period') ?: 'today';
        $stats = $this->delivery_model->get_stats($period);
        
        echo json_encode(['success' => true, 'stats' => $stats]);
    }
}
