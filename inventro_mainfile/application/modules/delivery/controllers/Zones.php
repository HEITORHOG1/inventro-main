<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Controller para Zonas de Entrega
 * Gerencia bairros/regiões e suas taxas de entrega
 */
class Zones extends MX_Controller {

    public function __construct() {
        parent::__construct();
        $this->permission->module('delivery')->redirect();
        $this->load->model('delivery/delivery_model');
    }

    /**
     * Lista de zonas de entrega
     */
    public function index() {
        $this->permission->method('delivery', 'read')->redirect();

        $data['title']  = makeString(['zonas_entrega']);
        $data['zones']  = $this->delivery_model->get_zones();
        $data['module'] = 'delivery';
        $data['page']   = 'zones_list';
        echo Modules::run('template/layout', $data);
    }

    /**
     * Formulário de criar/editar zona
     */
    public function form($id = null) {
        $this->permission->method('delivery', 'create')->redirect();

        $data['title'] = makeString(['zonas_entrega']) . ' - ' . ($id ? makeString(['edit']) : makeString(['add_new']));
        $data['zone']  = $id ? $this->delivery_model->get_zone((int)$id) : null;

        if ($id && !$data['zone']) {
            $this->session->set_flashdata('exception', makeString(['please_try_again']));
            redirect('delivery/zones/index');
            return;
        }

        $data['module'] = 'delivery';
        $data['page']   = 'zones_form';
        echo Modules::run('template/layout', $data);
    }

    /**
     * Salvar zona
     */
    public function save() {
        $this->form_validation->set_rules('nome', makeString(['nome']), 'required|trim|max_length[100]');
        $this->form_validation->set_rules('taxa', makeString(['delivery_fee']), 'required|numeric');

        if ($this->form_validation->run() === FALSE) {
            $this->session->set_flashdata('exception', validation_errors());
            $id = $this->input->post('id', TRUE);
            redirect('delivery/zones/form/' . (int)$id);
            return;
        }

        $postData = [
            'nome'      => $this->input->post('nome', TRUE),
            'taxa'      => (float)$this->input->post('taxa', TRUE),
            'tempo_min' => (int)($this->input->post('tempo_min', TRUE) ?: 20),
            'tempo_max' => (int)($this->input->post('tempo_max', TRUE) ?: 40),
            'ativo'     => $this->input->post('ativo') ? 1 : 0
        ];

        $id = $this->input->post('id', TRUE);

        if ($this->delivery_model->save_zone($postData, $id ?: null)) {
            $this->session->set_flashdata('message', makeString(['save_successfully']));
        } else {
            $this->session->set_flashdata('exception', makeString(['please_try_again']));
        }

        redirect('delivery/zones');
    }

    /**
     * Excluir zona
     */
    public function delete($id) {
        $this->permission->method('delivery', 'delete')->redirect();

        if ($this->delivery_model->delete_zone((int)$id)) {
            $this->session->set_flashdata('message', makeString(['delete_successfully']));
        } else {
            $this->session->set_flashdata('exception', makeString(['please_try_again']));
        }
        redirect('delivery/zones');
    }

    /**
     * Toggle ativo/inativo via AJAX
     */
    public function toggle_status($id) {
        $this->permission->method('delivery', 'update')->redirect();

        header('Content-Type: application/json');

        $zone = $this->delivery_model->get_zone((int)$id);
        if (!$zone) {
            echo json_encode(['success' => false, 'message' => makeString(['please_try_again'])]);
            return;
        }

        $new_status = $zone->ativo ? 0 : 1;
        $result = $this->delivery_model->save_zone(['ativo' => $new_status], (int)$id);

        echo json_encode([
            'success'    => (bool)$result,
            'new_status' => $new_status,
            'message'    => $result ? makeString(['update_successfully']) : makeString(['please_try_again']),
            'csrf_token' => $this->security->get_csrf_hash()
        ]);
    }
}
