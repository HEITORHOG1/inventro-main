<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Controller para Zonas de Entrega
 * Gerencia bairros/regiões e suas taxas de entrega
 */
class Zones extends MX_Controller {

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
     * Lista de zonas de entrega
     */
    public function index() {
        $data['title'] = 'Zonas de Entrega';
        $data['zones'] = $this->delivery_model->get_zones();
        
        $this->load->view('templates/header', $data);
        $this->load->view('delivery/zones_list', $data);
        $this->load->view('templates/footer');
    }

    /**
     * Formulário de criar/editar zona
     */
    public function form($id = null) {
        $data['title'] = $id ? 'Editar Zona' : 'Nova Zona';
        $data['zone'] = $id ? $this->delivery_model->get_zone($id) : null;
        
        $this->load->view('templates/header', $data);
        $this->load->view('delivery/zones_form', $data);
        $this->load->view('templates/footer');
    }

    /**
     * Salvar zona
     */
    public function save() {
        $this->form_validation->set_rules('nome', 'Nome', 'required|trim');
        $this->form_validation->set_rules('taxa', 'Taxa', 'required|numeric');
        
        if ($this->form_validation->run() === FALSE) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('delivery/zones/form/' . $this->input->post('id'));
            return;
        }

        $data = [
            'nome' => $this->input->post('nome'),
            'taxa' => $this->input->post('taxa'),
            'tempo_min' => $this->input->post('tempo_min') ?: 20,
            'tempo_max' => $this->input->post('tempo_max') ?: 40,
            'ativo' => $this->input->post('ativo') ? 1 : 0
        ];

        $id = $this->input->post('id');
        
        if ($this->delivery_model->save_zone($data, $id)) {
            $this->session->set_flashdata('success', 'Zona salva com sucesso!');
        } else {
            $this->session->set_flashdata('error', 'Erro ao salvar zona.');
        }

        redirect('delivery/zones');
    }

    /**
     * Excluir zona
     */
    public function delete($id) {
        if ($this->delivery_model->delete_zone($id)) {
            $this->session->set_flashdata('success', 'Zona excluída com sucesso!');
        } else {
            $this->session->set_flashdata('error', 'Erro ao excluir zona.');
        }
        redirect('delivery/zones');
    }

    /**
     * Toggle ativo/inativo via AJAX
     */
    public function toggle_status($id) {
        header('Content-Type: application/json');
        
        $zone = $this->delivery_model->get_zone($id);
        if (!$zone) {
            echo json_encode(['success' => false, 'message' => 'Zona não encontrada']);
            return;
        }

        $new_status = $zone->ativo ? 0 : 1;
        $result = $this->delivery_model->save_zone(['ativo' => $new_status], $id);
        
        echo json_encode([
            'success' => $result,
            'new_status' => $new_status,
            'message' => $result ? 'Status atualizado!' : 'Erro ao atualizar'
        ]);
    }

    /**
     * API para buscar zonas (usado no cardápio)
     */
    public function api_list() {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        
        $zones = $this->delivery_model->get_zones(true);
        echo json_encode(['success' => true, 'zones' => $zones]);
    }
}
