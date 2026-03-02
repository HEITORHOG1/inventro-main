<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Model para cobrancas de Cartao de Credito via Efi Pay
 */
class Efi_card_model extends CI_Model {

    private $table = 'efi_card_charges';

    /**
     * Criar novo registro
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
     * Buscar por charge_id da Efi
     */
    public function find_by_charge_id($charge_id)
    {
        return $this->db->where('charge_id', (int) $charge_id)
            ->get($this->table)
            ->row();
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
