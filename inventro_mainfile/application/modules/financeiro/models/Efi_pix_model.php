<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Model para cobrancas PIX via Efi Pay
 */
class Efi_pix_model extends CI_Model {

    private $table = 'efi_pix_charges';

    /**
     * Criar novo registro de cobranca PIX
     */
    public function create($data)
    {
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    /**
     * Atualizar registro
     */
    public function update($data)
    {
        $id = $data['id'];
        unset($data['id']);
        return $this->db->where('id', $id)->update($this->table, $data);
    }

    /**
     * Buscar por ID
     */
    public function find_by_id($id)
    {
        return $this->db->where('id', (int) $id)
            ->get($this->table)
            ->row();
    }

    /**
     * Buscar por txid
     */
    public function find_by_txid($txid)
    {
        return $this->db->where('txid', $txid)
            ->get($this->table)
            ->row();
    }

    /**
     * Buscar cobranca ativa (pending e nao expirada) para uma conta a receber
     */
    public function get_active_charge($conta_receber_id)
    {
        $row = $this->db->where('conta_receber_id', (int) $conta_receber_id)
            ->where('status', 'pending')
            ->order_by('created_at', 'desc')
            ->get($this->table)
            ->row();

        if (!$row) {
            return null;
        }

        // Verificar se expirou
        $created_ts = strtotime($row->created_at);
        $expires_at = $created_ts + (int) $row->expiracao;
        if (time() > $expires_at) {
            $this->db->where('id', $row->id)->update($this->table, array('status' => 'expired'));
            return null;
        }

        return $row;
    }

    /**
     * Marcar cobranca como confirmada (paga)
     */
    public function mark_as_confirmed($txid, $e2e_id, $webhook_payload)
    {
        return $this->db->where('txid', $txid)
            ->where('status', 'pending')
            ->update($this->table, array(
                'status'          => 'confirmed',
                'e2e_id'          => $e2e_id,
                'webhook_payload' => $webhook_payload,
                'paid_at'         => date('Y-m-d H:i:s'),
            ));
    }

    /**
     * Marcar como expirada
     */
    public function mark_as_expired($id)
    {
        return $this->db->where('id', (int) $id)
            ->where('status', 'pending')
            ->update($this->table, array('status' => 'expired'));
    }

    /**
     * Buscar cobranca ativa (pending e nao expirada) para um pedido delivery
     */
    public function get_active_charge_by_order($order_id)
    {
        $row = $this->db->where('order_id', (int) $order_id)
            ->where('status', 'pending')
            ->order_by('created_at', 'desc')
            ->get($this->table)
            ->row();

        if (!$row) {
            return null;
        }

        // Verificar se expirou
        $created_ts = strtotime($row->created_at);
        $expires_at = $created_ts + (int) $row->expiracao;
        if (time() > $expires_at) {
            $this->db->where('id', $row->id)->update($this->table, array('status' => 'expired'));
            return null;
        }

        return $row;
    }

    /**
     * Listar cobrancas de uma conta a receber
     */
    public function get_charges_by_conta($conta_receber_id)
    {
        return $this->db->where('conta_receber_id', (int) $conta_receber_id)
            ->order_by('created_at', 'desc')
            ->get($this->table)
            ->result();
    }
}
