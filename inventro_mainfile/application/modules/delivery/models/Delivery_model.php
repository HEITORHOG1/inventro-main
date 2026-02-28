<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Delivery Model
 * Gerencia zonas de entrega, pedidos e configurações
 */
class Delivery_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    // ========================================
    // ZONAS DE ENTREGA
    // ========================================

    /**
     * Buscar todas as zonas de entrega
     */
    public function get_zones($only_active = false) {
        if ($only_active) {
            $this->db->where('ativo', 1);
        }
        $this->db->order_by('nome', 'ASC');
        return $this->db->get('delivery_zones')->result();
    }

    /**
     * Buscar zona por ID
     */
    public function get_zone($id) {
        return $this->db->get_where('delivery_zones', ['id' => $id])->row();
    }

    /**
     * Salvar zona (criar ou atualizar)
     */
    public function save_zone($data, $id = null) {
        if ($id) {
            $this->db->where('id', $id);
            return $this->db->update('delivery_zones', $data);
        } else {
            return $this->db->insert('delivery_zones', $data);
        }
    }

    /**
     * Excluir zona
     */
    public function delete_zone($id) {
        return $this->db->delete('delivery_zones', ['id' => $id]);
    }

    // ========================================
    // PEDIDOS
    // ========================================

    /**
     * Gerar próximo número de pedido
     */
    public function generate_order_number() {
        $this->db->select_max('id');
        $result = $this->db->get('orders')->row();
        $next_id = ($result->id ?? 0) + 1;
        return str_pad($next_id, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Criar pedido
     */
    public function create_order($order_data, $items) {
        $this->db->trans_start();

        // Gerar número do pedido
        $order_data['order_number'] = $this->generate_order_number();
        
        // Inserir pedido
        $this->db->insert('orders', $order_data);
        $order_id = $this->db->insert_id();

        // Inserir itens
        foreach ($items as $item) {
            $item['order_id'] = $order_id;
            $this->db->insert('order_items', $item);
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            return false;
        }

        return [
            'order_id' => $order_id,
            'order_number' => $order_data['order_number']
        ];
    }

    /**
     * Buscar pedidos com filtros
     */
    public function get_orders($filters = []) {
        $this->db->select('orders.*, delivery_zones.nome as zona_nome_atual');
        $this->db->from('orders');
        $this->db->join('delivery_zones', 'orders.zona_id = delivery_zones.id', 'left');

        if (!empty($filters['status'])) {
            $this->db->where('orders.status', $filters['status']);
        }

        if (!empty($filters['data_inicio'])) {
            $this->db->where('DATE(orders.created_at) >=', $filters['data_inicio']);
        }

        if (!empty($filters['data_fim'])) {
            $this->db->where('DATE(orders.created_at) <=', $filters['data_fim']);
        }

        $this->db->order_by('orders.created_at', 'DESC');
        
        if (!empty($filters['limit'])) {
            $this->db->limit($filters['limit']);
        }

        return $this->db->get()->result();
    }

    /**
     * Buscar pedido por ID
     */
    public function get_order($id) {
        $order = $this->db->get_where('orders', ['id' => $id])->row();
        
        if ($order) {
            $order->items = $this->db->get_where('order_items', ['order_id' => $id])->result();
        }

        return $order;
    }

    /**
     * Buscar pedido por número
     */
    public function get_order_by_number($order_number) {
        $order = $this->db->get_where('orders', ['order_number' => $order_number])->row();
        
        if ($order) {
            $order->items = $this->db->get_where('order_items', ['order_id' => $order->id])->result();
        }

        return $order;
    }

    /**
     * Atualizar status do pedido
     */
    public function update_order_status($id, $status) {
        return $this->db->update('orders', ['status' => $status], ['id' => $id]);
    }

    /**
     * Contar pedidos por status
     */
    public function count_orders_by_status() {
        $this->db->select('status, COUNT(*) as total');
        $this->db->group_by('status');
        $result = $this->db->get('orders')->result();
        
        $counts = [
            'pendente' => 0,
            'confirmado' => 0,
            'preparando' => 0,
            'saiu_entrega' => 0,
            'entregue' => 0,
            'cancelado' => 0
        ];

        foreach ($result as $row) {
            $counts[$row->status] = (int)$row->total;
        }

        return $counts;
    }

    // ========================================
    // CONFIGURAÇÕES
    // ========================================

    /**
     * Buscar configuração
     */
    public function get_config($chave = null, $default = null) {
        if ($chave === null) {
            return $this->get_all_configs();
        }
        $row = $this->db->get_where('cardapio_config', ['chave' => $chave])->row();
        return $row ? $row->valor : $default;
    }

    /**
     * Buscar todas as configurações
     */
    public function get_all_configs() {
        $result = $this->db->get('cardapio_config')->result();
        $configs = [];
        foreach ($result as $row) {
            $configs[$row->chave] = $row->valor;
        }
        return $configs;
    }

    /**
     * Salvar configuração
     */
    public function save_config($chave, $valor) {
        $exists = $this->db->get_where('cardapio_config', ['chave' => $chave])->num_rows();
        
        if ($exists) {
            return $this->db->update('cardapio_config', ['valor' => $valor], ['chave' => $chave]);
        } else {
            return $this->db->insert('cardapio_config', ['chave' => $chave, 'valor' => $valor]);
        }
    }

    /**
     * Salvar múltiplas configurações
     */
    public function save_configs($configs) {
        foreach ($configs as $chave => $valor) {
            $this->save_config($chave, $valor);
        }
        return true;
    }

    // ========================================
    // ESTATÍSTICAS
    // ========================================

    /**
     * Estatísticas do dashboard
     */
    public function get_stats($period = 'today') {
        switch ($period) {
            case 'today':
                $where = "DATE(created_at) = CURDATE()";
                break;
            case 'week':
                $where = "YEARWEEK(created_at) = YEARWEEK(NOW())";
                break;
            case 'month':
                $where = "MONTH(created_at) = MONTH(NOW()) AND YEAR(created_at) = YEAR(NOW())";
                break;
            default:
                $where = "1=1";
        }

        // Total de pedidos
        $this->db->where($where);
        $total_pedidos = $this->db->count_all_results('orders');

        // Valor total
        $this->db->select_sum('total');
        $this->db->where($where);
        $this->db->where('status !=', 'cancelado');
        $valor_result = $this->db->get('orders')->row();
        $valor_total = $valor_result->total ?? 0;

        // Ticket médio
        $ticket_medio = $total_pedidos > 0 ? $valor_total / $total_pedidos : 0;

        return [
            'total_pedidos' => $total_pedidos,
            'valor_total' => $valor_total,
            'ticket_medio' => $ticket_medio
        ];
    }
}
