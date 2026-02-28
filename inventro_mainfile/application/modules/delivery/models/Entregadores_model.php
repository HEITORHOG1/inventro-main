<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Model para Entregadores (Delivery Drivers)
 * Tabela: entregadores
 */
class Entregadores_model extends CI_Model {

    private $table = 'entregadores';

    /**
     * Buscar todos os entregadores
     *
     * @param bool $only_active Se true, retorna apenas entregadores ativos
     * @return array
     */
    public function get_all($only_active = false) {
        if ($only_active) {
            $this->db->where('ativo', 1);
        }
        $this->db->order_by('nome', 'ASC');
        return $this->db->get($this->table)->result();
    }

    /**
     * Buscar entregador por ID
     *
     * @param int $id
     * @return object|null
     */
    public function get_by_id($id) {
        return $this->db->where('id', (int) $id)->get($this->table)->row();
    }

    /**
     * Buscar entregadores disponiveis (ativos e com status disponivel)
     *
     * @return array
     */
    public function get_disponiveis() {
        return $this->db
            ->where('ativo', 1)
            ->where('status', 'disponivel')
            ->order_by('nome', 'ASC')
            ->get($this->table)
            ->result();
    }

    /**
     * Criar novo entregador
     *
     * @param array $data
     * @return bool
     */
    public function create($data) {
        return $this->db->insert($this->table, $data);
    }

    /**
     * Atualizar entregador
     *
     * @param int   $id
     * @param array $data
     * @return bool
     */
    public function update($id, $data) {
        return $this->db->where('id', (int) $id)->update($this->table, $data);
    }

    /**
     * Soft delete: definir ativo = 0
     *
     * @param int $id
     * @return bool
     */
    public function delete($id) {
        $data = array(
            'ativo'      => 0,
            'status'     => 'indisponivel',
            'updated_at' => date('Y-m-d H:i:s')
        );
        $this->db->where('id', (int) $id)->update($this->table, $data);
        return $this->db->affected_rows() > 0;
    }

    /**
     * Toggle status entre disponivel e indisponivel
     * Nao altera se o status atual for em_entrega
     *
     * @param int $id
     * @return bool
     */
    public function toggle_status($id) {
        $entregador = $this->get_by_id((int) $id);
        if (!$entregador || $entregador->status === 'em_entrega') {
            return false;
        }

        $new_status = ($entregador->status === 'disponivel') ? 'indisponivel' : 'disponivel';

        $data = array(
            'status'     => $new_status,
            'updated_at' => date('Y-m-d H:i:s')
        );

        $this->db->where('id', (int) $id)->update($this->table, $data);
        return $this->db->affected_rows() > 0;
    }
}
