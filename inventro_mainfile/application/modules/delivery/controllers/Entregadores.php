<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Controller para Entregadores (Delivery Drivers)
 * Gerencia cadastro, status e disponibilidade dos entregadores
 */
class Entregadores extends MX_Controller {

    public function __construct() {
        parent::__construct();
        $this->permission->module('delivery')->redirect();
        $this->load->model(array('Entregadores_model'));
    }

    /**
     * Lista de entregadores
     */
    public function index() {
        $this->permission->method('delivery', 'read')->redirect();

        $data['title']         = makeString(['entregadores']);
        $data['entregadores']  = $this->Entregadores_model->get_all();
        $data['module']        = 'delivery';
        $data['page']          = 'entregadores_list';
        echo Modules::run('template/layout', $data);
    }

    /**
     * Formulario de criar/editar entregador
     */
    public function form($id = null) {
        $this->permission->method('delivery', 'create')->redirect();

        $data['title']       = makeString(['novo_entregador']);
        $data['entregador']  = null;

        if (!empty($id)) {
            $this->permission->method('delivery', 'update')->redirect();
            $data['title']      = makeString(['edit']) . ' - ' . makeString(['entregador']);
            $data['entregador'] = $this->Entregadores_model->get_by_id((int) $id);

            if (!$data['entregador']) {
                $this->session->set_flashdata('exception', makeString(['please_try_again']));
                redirect('delivery/entregadores/index');
                return;
            }
        }

        $data['module'] = 'delivery';
        $data['page']   = 'entregadores_form';
        echo Modules::run('template/layout', $data);
    }

    /**
     * Salvar entregador (criar ou atualizar)
     */
    public function save() {
        $this->form_validation->set_rules('nome', makeString(['nome']), 'required|max_length[255]');
        $this->form_validation->set_rules('telefone', makeString(['telefone']), 'required|max_length[20]');
        $this->form_validation->set_rules('veiculo', makeString(['veiculo']), 'required|in_list[moto,bicicleta,carro,a_pe]');
        $this->form_validation->set_rules('taxa_entrega_fixa', 'Taxa por Entrega', 'required|numeric|greater_than_equal_to[0]');

        $id = $this->input->post('id', TRUE);

        // Senha obrigatória para novos entregadores
        if (empty($id)) {
            $this->form_validation->set_rules('senha', 'Senha do Portal', 'required|min_length[4]');
        }

        if ($this->form_validation->run()) {

            $postData = array(
                'nome'              => $this->input->post('nome', TRUE),
                'telefone'          => $this->input->post('telefone', TRUE),
                'veiculo'           => $this->input->post('veiculo', TRUE),
                'taxa_entrega_fixa' => (float) $this->input->post('taxa_entrega_fixa', TRUE),
            );

            // Hash da senha com bcrypt (apenas se preenchida)
            $senha = $this->input->post('senha', TRUE);
            if (!empty($senha)) {
                $postData['senha'] = password_hash($senha, PASSWORD_BCRYPT);
            }

            if (empty($id)) {
                // Criar novo entregador
                $postData['status']     = 'disponivel';
                $postData['ativo']      = 1;
                $postData['created_at'] = date('Y-m-d H:i:s');
                $postData['updated_at'] = date('Y-m-d H:i:s');

                $this->Entregadores_model->create($postData);
                $this->session->set_flashdata('message', makeString(['save_successfully']));
            } else {
                // Atualizar entregador existente
                $postData['updated_at'] = date('Y-m-d H:i:s');

                $this->Entregadores_model->update((int) $id, $postData);
                $this->session->set_flashdata('message', makeString(['update_successfully']));
            }

            redirect('delivery/entregadores/index');
        } else {
            $this->session->set_flashdata('exception', validation_errors());
            $id = $this->input->post('id', TRUE);
            if (!empty($id)) {
                redirect('delivery/entregadores/form/' . (int) $id);
            } else {
                redirect('delivery/entregadores/form');
            }
        }
    }

    /**
     * Soft delete (desativar entregador)
     */
    public function delete($id) {
        $this->permission->method('delivery', 'delete')->redirect();

        $entregador = $this->Entregadores_model->get_by_id((int) $id);
        if (!$entregador) {
            $this->session->set_flashdata('exception', makeString(['please_try_again']));
            redirect('delivery/entregadores/index');
            return;
        }

        if ($this->Entregadores_model->delete((int) $id)) {
            $this->session->set_flashdata('message', makeString(['delete_successfully']));
        } else {
            $this->session->set_flashdata('exception', makeString(['please_try_again']));
        }

        redirect('delivery/entregadores/index');
    }

    /**
     * Toggle status disponivel/indisponivel via AJAX
     */
    public function toggle_status($id) {
        $this->permission->method('delivery', 'update')->redirect();

        header('Content-Type: application/json');

        $entregador = $this->Entregadores_model->get_by_id((int) $id);
        if (!$entregador) {
            echo json_encode(array(
                'success' => false,
                'message' => makeString(['please_try_again'])
            ));
            return;
        }

        // Only toggle between disponivel and indisponivel (not em_entrega)
        if ($entregador->status === 'em_entrega') {
            echo json_encode(array(
                'success'    => false,
                'message'    => makeString(['entregador_em_entrega']),
                'new_status' => $entregador->status
            ));
            return;
        }

        $result = $this->Entregadores_model->toggle_status((int) $id);

        if ($result) {
            $updated = $this->Entregadores_model->get_by_id((int) $id);
            echo json_encode(array(
                'success'    => true,
                'new_status' => $updated->status,
                'message'    => makeString(['update_successfully']),
                'csrf_token' => $this->security->get_csrf_hash()
            ));
        } else {
            echo json_encode(array(
                'success' => false,
                'message' => makeString(['please_try_again'])
            ));
        }
    }
}
