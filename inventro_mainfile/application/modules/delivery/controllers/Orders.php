<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Controller para Pedidos Online
 * Gerencia pedidos recebidos pelo cardápio digital
 */
class Orders extends MX_Controller {

    public function __construct() {
        parent::__construct();
        $this->permission->module('delivery')->redirect();
        $this->load->model('delivery/delivery_model');
        $this->load->library(['session', 'form_validation']);
        $this->load->helper(['url', 'form']);
    }

    /**
     * Lista de pedidos
     */
    public function index() {
        $this->permission->method('delivery', 'read')->redirect();

        $data['title'] = makeString(['pedidos_online']);
        $filters = [
            'status' => $this->input->get('status'),
            'data_inicio' => $this->input->get('data_inicio'),
            'data_fim' => $this->input->get('data_fim')
        ];

        $data['orders'] = $this->delivery_model->get_orders($filters);
        $data['status_counts'] = $this->delivery_model->count_orders_by_status();
        $data['current_filters'] = $filters;

        $data['module'] = 'delivery';
        $data['page'] = 'orders_list';
        echo Modules::run('template/layout', $data);
    }

    /**
     * Painel Kanban em tempo real
     */
    public function kanban() {
        $this->permission->method('delivery', 'read')->redirect();

        $data['title'] = makeString(['kanban']);
        $data['orders_by_status'] = $this->delivery_model->get_orders_grouped_by_status();
        $data['loja_pausada'] = $this->delivery_model->get_config('loja_pausada', '0') === '1';

        $data['module'] = 'delivery';
        $data['page'] = 'orders_kanban';
        echo Modules::run('template/layout', $data);
    }

    /**
     * API para polling de pedidos novos (Kanban auto-refresh)
     */
    public function api_novos() {
        header('Content-Type: application/json');

        $desde = $this->input->get('desde');
        $counts = $this->delivery_model->count_orders_by_status();

        // Pedidos atualizados desde o último check
        $orders = [];
        if ($desde) {
            $this->db->where('updated_at >=', $desde);
            $this->db->where('DATE(created_at)', date('Y-m-d'));
            $this->db->order_by('updated_at', 'DESC');
            $orders = $this->db->get('orders')->result();
        }

        echo json_encode([
            'success' => true,
            'counts' => $counts,
            'orders' => $orders,
            'timestamp' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Detalhes do pedido
     */
    public function view($id) {
        $this->permission->method('delivery', 'read')->redirect();

        $data['title'] = makeString(['detalhes_pedido']);
        $data['order'] = $this->delivery_model->get_order($id);

        if (!$data['order']) {
            $this->session->set_flashdata('exception', 'Pedido não encontrado.');
            redirect('delivery/orders');
            return;
        }

        // Entregadores disponíveis para dropdown
        $this->db->where('ativo', 1);
        $data['entregadores'] = $this->db->get('entregadores')->result();

        $data['module'] = 'delivery';
        $data['page'] = 'order_view';
        echo Modules::run('template/layout', $data);
    }

    /**
     * Atualizar status do pedido (com timestamps e WhatsApp)
     */
    public function update_status($id) {
        $this->permission->method('delivery', 'update')->redirect();

        $status = $this->input->post('status', TRUE);
        $valid_statuses = ['pendente', 'confirmado', 'preparando', 'saiu_entrega', 'entregue', 'cancelado'];

        if (!in_array($status, $valid_statuses)) {
            $this->session->set_flashdata('exception', 'Status inválido.');
            redirect('delivery/orders/view/' . (int)$id);
            return;
        }

        // Preparar dados extras (timestamp do status)
        $extra_data = $this->_get_status_timestamp_data($status);

        if ($this->delivery_model->update_order_status($id, $status, $extra_data)) {
            $this->session->set_flashdata('message', 'Status atualizado para: ' . ucfirst(str_replace('_', ' ', $status)));

            // Gerar link WhatsApp para notificar cliente
            $order = $this->delivery_model->get_order($id);
            if ($order && !empty($order->cliente_telefone)) {
                $whatsapp_link = $this->_gerar_whatsapp_status($order, $status);
                if ($whatsapp_link) {
                    $this->session->set_flashdata('whatsapp_link', $whatsapp_link);
                }
            }
        } else {
            $this->session->set_flashdata('exception', 'Erro ao atualizar status.');
        }

        redirect('delivery/orders/view/' . (int)$id);
    }

    /**
     * Atualizar status via AJAX (Kanban)
     */
    public function ajax_update_status() {
        header('Content-Type: application/json');

        $id = (int)$this->input->post('order_id', TRUE);
        $status = $this->input->post('status', TRUE);

        $valid_statuses = ['pendente', 'confirmado', 'preparando', 'saiu_entrega', 'entregue', 'cancelado'];
        if (!in_array($status, $valid_statuses)) {
            echo json_encode(['success' => false, 'message' => 'Status inválido']);
            return;
        }

        $extra_data = $this->_get_status_timestamp_data($status);
        $result = $this->delivery_model->update_order_status($id, $status, $extra_data);

        $whatsapp_link = null;
        if ($result) {
            $order = $this->delivery_model->get_order($id);
            if ($order && !empty($order->cliente_telefone)) {
                $whatsapp_link = $this->_gerar_whatsapp_status($order, $status);
            }
        }

        echo json_encode([
            'success' => (bool)$result,
            'message' => $result ? 'Status atualizado!' : 'Erro ao atualizar',
            'whatsapp_link' => $whatsapp_link
        ]);
    }

    /**
     * Atribuir entregador a pedido
     */
    public function atribuir_entregador() {
        header('Content-Type: application/json');

        $order_id = (int)$this->input->post('order_id', TRUE);
        $entregador_id = (int)$this->input->post('entregador_id', TRUE);

        $entregador = $this->db->where('id', $entregador_id)->get('entregadores')->row();
        if (!$entregador) {
            echo json_encode(['success' => false, 'message' => 'Entregador não encontrado']);
            return;
        }

        $this->db->where('id', $order_id)->update('orders', [
            'entregador_id' => $entregador_id,
            'entregador_nome' => $entregador->nome
        ]);

        // Atualizar status do entregador
        $this->db->where('id', $entregador_id)->update('entregadores', ['status' => 'em_entrega']);

        // Gerar link WhatsApp para o entregador
        $order = $this->delivery_model->get_order($order_id);
        $whatsapp_link = null;
        if ($order) {
            $msg = "🚀 *Novo Pedido #{$order->order_number}*\n\n";
            $msg .= "📍 *Endereço:* {$order->cliente_endereco}";
            if (!empty($order->cliente_complemento)) {
                $msg .= " - {$order->cliente_complemento}";
            }
            $msg .= "\n👤 *Cliente:* {$order->cliente_nome}\n";
            $msg .= "📱 *Tel:* {$order->cliente_telefone}\n\n";
            $msg .= "*Itens:*\n";
            if (isset($order->items)) {
                foreach ($order->items as $item) {
                    $msg .= "• {$item->quantity}x {$item->product_name}\n";
                }
            }
            $msg .= "\n💰 *Total:* R$ " . number_format($order->total, 2, ',', '.');
            $msg .= "\n💳 *Pagamento:* " . ucfirst($order->forma_pagamento);
            if ($order->forma_pagamento === 'dinheiro' && $order->troco_para > 0) {
                $msg .= " (troco p/ R$ " . number_format($order->troco_para, 2, ',', '.') . ")";
            }

            $whatsapp_link = $this->_gerar_link_whatsapp($entregador->telefone, $msg);
        }

        echo json_encode([
            'success' => true,
            'message' => "Entregador {$entregador->nome} atribuído!",
            'whatsapp_link' => $whatsapp_link
        ]);
    }

    /**
     * Confirmar pagamento manualmente
     */
    public function confirmar_pagamento() {
        header('Content-Type: application/json');

        $order_id = (int)$this->input->post('order_id', TRUE);

        $this->db->where('id', $order_id)->update('orders', ['pagamento_confirmado' => 1]);

        echo json_encode(['success' => true, 'message' => 'Pagamento confirmado!']);
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

        $data['loja'] = $this->db->get('setting')->row();
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

    // =========================================
    // Métodos privados
    // =========================================

    /**
     * Retorna array com coluna de timestamp conforme o novo status
     */
    private function _get_status_timestamp_data($status) {
        $map = [
            'confirmado'    => 'hora_confirmado',
            'preparando'    => 'hora_preparando',
            'saiu_entrega'  => 'hora_saiu_entrega',
            'entregue'      => 'hora_entregue'
        ];

        $data = [];
        if (isset($map[$status])) {
            $data[$map[$status]] = date('Y-m-d H:i:s');
        }
        return $data;
    }

    /**
     * Gera link wa.me com mensagem de status para o cliente
     */
    private function _gerar_whatsapp_status($order, $status) {
        $num = $order->order_number;
        $base_url = base_url("cardapio/acompanhar/{$num}");

        $mensagens = [
            'confirmado'   => "✅ Seu pedido *#{$num}* foi confirmado! Tempo estimado: " .
                              ($order->zona_nome ? "conforme zona {$order->zona_nome}" : "em breve") .
                              ".\n\n📍 Acompanhe: {$base_url}",
            'preparando'   => "👨‍🍳 Seu pedido *#{$num}* está sendo preparado!\n\n📍 Acompanhe: {$base_url}",
            'saiu_entrega' => "🛵 Seu pedido *#{$num}* saiu para entrega!\n\n📍 Acompanhe: {$base_url}",
            'entregue'     => "✅ Pedido *#{$num}* entregue! Obrigado pela preferência! 😊\n\n" .
                              "⭐ Avalie seu pedido: " . base_url("cardapio/avaliar/{$num}"),
            'cancelado'    => "❌ Seu pedido *#{$num}* foi cancelado. Entre em contato para mais informações."
        ];

        if (!isset($mensagens[$status])) {
            return null;
        }

        return $this->_gerar_link_whatsapp($order->cliente_telefone, $mensagens[$status]);
    }

    /**
     * Gera link wa.me formatado
     */
    private function _gerar_link_whatsapp($telefone, $mensagem) {
        $telefone = preg_replace('/\D/', '', $telefone);
        // Adicionar código do Brasil se necessário
        if (strlen($telefone) === 11 || strlen($telefone) === 10) {
            $telefone = '55' . $telefone;
        }
        return 'https://wa.me/' . $telefone . '?text=' . rawurlencode($mensagem);
    }
}
