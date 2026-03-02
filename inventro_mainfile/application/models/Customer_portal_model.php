<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Model para o Portal do Cliente
 *
 * Gerencia autenticacao, perfil, pedidos e pagamentos do cliente.
 * Segue o padrao do Motoboy_model — model de top-level (nao HMVC).
 */
class Customer_portal_model extends CI_Model {

    // =========================================
    // Busca de clientes
    // =========================================

    /**
     * Buscar cliente por email (case-insensitive)
     */
    public function find_by_email($email)
    {
        return $this->db->where('LOWER(email)', strtolower(trim($email)))
            ->get('customer_tbl')
            ->row();
    }

    /**
     * Buscar cliente por ID
     */
    public function find_by_id($id)
    {
        return $this->db->where('id', (int) $id)
            ->get('customer_tbl')
            ->row();
    }

    /**
     * Buscar cliente por telefone (digitos limpos)
     */
    public function find_by_phone($phone)
    {
        $digits = preg_replace('/\D/', '', $phone);
        if (strlen($digits) < 8) {
            return null;
        }

        // Busca usando REPLACE para remover formatacao antes de comparar
        $search = substr($digits, -8);

        $results = $this->db->select('*')
            ->from('customer_tbl')
            ->where(
                "REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(mobile, '(', ''), ')', ''), ' ', ''), '-', ''), '+', '') LIKE "
                . $this->db->escape('%' . $search . '%')
            )
            ->get()
            ->result();

        // Match exato nos digitos limpos
        foreach ($results as $row) {
            $row_digits = preg_replace('/\D/', '', $row->mobile);
            if ($row_digits === $digits || substr($row_digits, -strlen($digits)) === $digits || substr($digits, -strlen($row_digits)) === $row_digits) {
                return $row;
            }
        }
        return null;
    }

    // =========================================
    // Registro e perfil
    // =========================================

    /**
     * Criar nova conta de cliente
     */
    public function create_account($data)
    {
        $this->db->insert('customer_tbl', $data);
        return $this->db->insert_id();
    }

    /**
     * Atualizar perfil (adicionar password_hash a registro existente, ou editar dados)
     */
    public function update_profile($id, $data)
    {
        return $this->db->where('id', (int) $id)
            ->update('customer_tbl', $data);
    }

    /**
     * Atualizar senha
     */
    public function update_password($id, $hash)
    {
        return $this->db->where('id', (int) $id)
            ->update('customer_tbl', ['password_hash' => $hash]);
    }

    /**
     * Atualizar timestamp de ultimo login
     */
    public function update_last_login($id)
    {
        return $this->db->where('id', (int) $id)
            ->update('customer_tbl', ['last_login' => date('Y-m-d H:i:s')]);
    }

    // =========================================
    // Pedidos
    // =========================================

    /**
     * Listar pedidos do cliente com paginacao
     */
    public function get_orders($customer_id, $limit = 20, $offset = 0, $status_filter = null)
    {
        $this->db->where('customer_id', (int) $customer_id);
        if ($status_filter) {
            $this->db->where('status', $status_filter);
        }
        $this->db->order_by('created_at', 'DESC');
        $this->db->limit((int) $limit, (int) $offset);
        return $this->db->get('orders')->result();
    }

    /**
     * Contar pedidos do cliente (para paginacao)
     */
    public function get_order_count($customer_id, $status_filter = null)
    {
        $this->db->where('customer_id', (int) $customer_id);
        if ($status_filter) {
            $this->db->where('status', $status_filter);
        }
        return $this->db->count_all_results('orders');
    }

    /**
     * Buscar pedido com itens (com verificacao de ownership)
     */
    public function get_order_with_items($order_number, $customer_id)
    {
        $order = $this->db->where('order_number', $order_number)
            ->where('customer_id', (int) $customer_id)
            ->get('orders')
            ->row();

        if (!$order) {
            return null;
        }

        $order->items = $this->db->where('order_id', $order->id)
            ->get('order_items')
            ->result();

        return $order;
    }

    /**
     * Estatisticas do cliente
     */
    public function get_stats($customer_id)
    {
        $result = $this->db->select('COUNT(*) as total_pedidos, COALESCE(SUM(total), 0) as total_gasto')
            ->where('customer_id', (int) $customer_id)
            ->where('status !=', 'cancelado')
            ->get('orders')
            ->row();

        return $result ?: (object) ['total_pedidos' => 0, 'total_gasto' => 0];
    }

    // =========================================
    // Pagamentos (PIX + Cartao)
    // =========================================

    /**
     * Listar cobrancas PIX do cliente (via orders JOIN)
     */
    public function get_pix_charges($customer_id, $limit = 20)
    {
        return $this->db->select('p.*, o.order_number')
            ->from('efi_pix_charges p')
            ->join('orders o', 'o.id = p.order_id', 'inner')
            ->where('o.customer_id', (int) $customer_id)
            ->order_by('p.created_at', 'DESC')
            ->limit((int) $limit)
            ->get()
            ->result();
    }

    /**
     * Listar cobrancas Cartao do cliente (via orders JOIN)
     */
    public function get_card_charges($customer_id, $limit = 20)
    {
        return $this->db->select('c.*, o.order_number')
            ->from('efi_card_charges c')
            ->join('orders o', 'o.id = c.order_id', 'inner')
            ->where('o.customer_id', (int) $customer_id)
            ->order_by('c.created_at', 'DESC')
            ->limit((int) $limit)
            ->get()
            ->result();
    }

    // =========================================
    // Vincular pedidos ao cliente
    // =========================================

    /**
     * Backfill: vincular pedidos existentes pelo telefone
     */
    public function link_orders_by_phone($customer_id, $phone)
    {
        $digits = preg_replace('/\D/', '', $phone);
        if (strlen($digits) < 8) {
            return 0;
        }

        // Buscar pedidos sem customer_id que batem com o telefone
        $search = substr($digits, -8);
        $orders = $this->db->select('id, cliente_telefone')
            ->where('customer_id IS NULL')
            ->where(
                "REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(cliente_telefone, '(', ''), ')', ''), ' ', ''), '-', ''), '+', '') LIKE "
                . $this->db->escape('%' . $search . '%')
            )
            ->get('orders')
            ->result();

        $linked = 0;
        foreach ($orders as $order) {
            $order_digits = preg_replace('/\D/', '', $order->cliente_telefone);
            if ($order_digits === $digits || substr($order_digits, -strlen($digits)) === $digits || substr($digits, -strlen($order_digits)) === $order_digits) {
                $this->db->where('id', $order->id)
                    ->update('orders', ['customer_id' => (int) $customer_id]);
                $linked++;
            }
        }
        return $linked;
    }

    // =========================================
    // Password Reset
    // =========================================

    /**
     * Criar token de reset de senha
     */
    public function create_reset_token($customer_id, $token_hash, $expires_at)
    {
        // Invalidar tokens anteriores nao usados
        $this->db->where('customer_id', (int) $customer_id)
            ->where('used_at IS NULL')
            ->update('customer_password_resets', ['used_at' => date('Y-m-d H:i:s')]);

        $this->db->insert('customer_password_resets', [
            'customer_id' => (int) $customer_id,
            'token'       => $token_hash,
            'expires_at'  => $expires_at,
        ]);
        return $this->db->insert_id();
    }

    /**
     * Buscar token de reset valido (nao expirado, nao usado)
     */
    public function find_valid_reset_token($token_hash)
    {
        return $this->db->where('token', $token_hash)
            ->where('used_at IS NULL')
            ->where('expires_at >', date('Y-m-d H:i:s'))
            ->get('customer_password_resets')
            ->row();
    }

    /**
     * Marcar token como usado
     */
    public function mark_token_used($token_id)
    {
        return $this->db->where('id', (int) $token_id)
            ->update('customer_password_resets', ['used_at' => date('Y-m-d H:i:s')]);
    }

    // =========================================
    // Rate Limiting
    // =========================================

    /**
     * Contar tentativas recentes de login
     */
    public function count_recent_attempts($email, $ip, $minutes = 15)
    {
        $since = date('Y-m-d H:i:s', strtotime("-{$minutes} minutes"));
        return $this->db->where('email', strtolower(trim($email)))
            ->where('ip_address', $ip)
            ->where('success', 0)
            ->where('attempted_at >', $since)
            ->count_all_results('customer_login_attempts');
    }

    /**
     * Registrar tentativa de login
     */
    public function record_attempt($email, $ip, $success = false)
    {
        $this->db->insert('customer_login_attempts', [
            'email'      => strtolower(trim($email)),
            'ip_address' => $ip,
            'success'    => $success ? 1 : 0,
        ]);
    }

    /**
     * Limpar tentativas antigas (> 24h)
     */
    public function cleanup_old_attempts()
    {
        $cutoff = date('Y-m-d H:i:s', strtotime('-24 hours'));
        $this->db->where('attempted_at <', $cutoff)
            ->delete('customer_login_attempts');
    }
}
