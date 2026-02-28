<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Model para Cupons de Desconto
 */
class Cupons_model extends CI_Model {

    private $table = 'cupons_desconto';

    /**
     * Buscar todos os cupons
     */
    public function get_all() {
        $this->db->order_by('created_at', 'DESC');
        return $this->db->get($this->table)->result();
    }

    /**
     * Buscar cupom por ID
     */
    public function get_by_id($id) {
        return $this->db->where('id', (int) $id)->get($this->table)->row();
    }

    /**
     * Buscar cupom por codigo
     */
    public function get_by_codigo($codigo) {
        return $this->db->where('codigo', $codigo)->get($this->table)->row();
    }

    /**
     * Criar novo cupom
     */
    public function create($data) {
        return $this->db->insert($this->table, $data);
    }

    /**
     * Atualizar cupom
     */
    public function update($id, $data) {
        return $this->db->where('id', (int) $id)->update($this->table, $data);
    }

    /**
     * Excluir cupom
     */
    public function delete($id) {
        $this->db->where('id', (int) $id)->delete($this->table);
        return $this->db->affected_rows() > 0;
    }

    /**
     * Toggle ativo/inativo
     */
    public function toggle_ativo($id) {
        $cupom = $this->get_by_id($id);
        if (!$cupom) {
            return false;
        }

        $new_status = $cupom->ativo ? 0 : 1;
        return $this->db->where('id', (int) $id)->update($this->table, array('ativo' => $new_status));
    }

    /**
     * Incrementar contador de uso do cupom
     */
    public function incrementar_uso($codigo) {
        return $this->db->where('codigo', $codigo)
                        ->set('uso_atual', 'uso_atual + 1', FALSE)
                        ->update($this->table);
    }
}
