<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Controller para Cupons de Desconto
 * Gerencia cupons de desconto para pedidos do delivery
 */
class Cupons extends MX_Controller {

    public function __construct() {
        parent::__construct();
        $this->permission->module('delivery')->redirect();
        $this->load->model('delivery/Cupons_model');
    }

    /**
     * Lista de cupons de desconto
     */
    public function index() {
        $this->permission->method('delivery', 'read')->redirect();

        $data['title']  = makeString(['cupons_desconto']);
        $data['cupons'] = $this->Cupons_model->get_all();

        $data['module'] = 'delivery';
        $data['page']   = 'cupons_list';
        echo Modules::run('template/layout', $data);
    }

    /**
     * Formulario de criar/editar cupom
     */
    public function form($id = null) {
        $this->permission->method('delivery', 'create')->redirect();

        $data['title'] = makeString(['novo_cupom']);
        $data['cupom'] = null;

        if (!empty($id)) {
            $this->permission->method('delivery', 'update')->redirect();
            $data['title'] = makeString(['edit']) . ' - ' . makeString(['cupom']);
            $data['cupom'] = $this->Cupons_model->get_by_id((int) $id);

            if (!$data['cupom']) {
                $this->session->set_flashdata('exception', makeString(['please_try_again']));
                redirect('delivery/cupons/index');
                return;
            }
        }

        $data['module'] = 'delivery';
        $data['page']   = 'cupons_form';
        echo Modules::run('template/layout', $data);
    }

    /**
     * Salvar cupom (criar ou atualizar)
     */
    public function save() {
        $this->form_validation->set_rules('codigo', makeString(['codigo']), 'required|max_length[20]|alpha_numeric');
        $this->form_validation->set_rules('tipo', makeString(['type']), 'required|in_list[percentual,valor_fixo,frete_gratis]');
        $this->form_validation->set_rules('valor', makeString(['amount']), 'required|numeric|greater_than_equal_to[0]');

        if ($this->form_validation->run()) {
            $id = $this->input->post('id', TRUE);
            $codigo = strtoupper(trim($this->input->post('codigo', TRUE)));

            // Verificar duplicidade de codigo
            $existente = $this->Cupons_model->get_by_codigo($codigo);
            if ($existente && (!$id || $existente->id != $id)) {
                $this->session->set_flashdata('exception', makeString(['codigo_cupom_duplicado']));
                if ($id) {
                    redirect('delivery/cupons/form/' . $id);
                } else {
                    redirect('delivery/cupons/form');
                }
                return;
            }

            $postData = array(
                'codigo'              => $codigo,
                'tipo'                => $this->input->post('tipo', TRUE),
                'valor'               => (float) $this->input->post('valor', TRUE),
                'valor_minimo_pedido' => $this->input->post('valor_minimo_pedido', TRUE) !== '' ? (float) $this->input->post('valor_minimo_pedido', TRUE) : 0,
                'uso_maximo'          => $this->input->post('uso_maximo', TRUE) !== '' ? (int) $this->input->post('uso_maximo', TRUE) : NULL,
                'validade_inicio'     => $this->input->post('validade_inicio', TRUE) ?: NULL,
                'validade_fim'        => $this->input->post('validade_fim', TRUE) ?: NULL,
                'ativo'               => $this->input->post('ativo', TRUE) ? 1 : 0,
            );

            if (empty($id)) {
                $postData['created_at'] = date('Y-m-d H:i:s');
                $this->Cupons_model->create($postData);
                $this->session->set_flashdata('message', makeString(['save_successfully']));
            } else {
                $this->Cupons_model->update((int) $id, $postData);
                $this->session->set_flashdata('message', makeString(['update_successfully']));
            }

            redirect('delivery/cupons/index');
        } else {
            $this->session->set_flashdata('exception', validation_errors());
            $id = $this->input->post('id', TRUE);
            if ($id) {
                redirect('delivery/cupons/form/' . $id);
            } else {
                redirect('delivery/cupons/form');
            }
        }
    }

    /**
     * Excluir cupom
     */
    public function delete($id) {
        $this->permission->method('delivery', 'delete')->redirect();

        if ($this->Cupons_model->delete((int) $id)) {
            $this->session->set_flashdata('message', makeString(['delete_successfully']));
        } else {
            $this->session->set_flashdata('exception', makeString(['please_try_again']));
        }

        redirect('delivery/cupons/index');
    }

    /**
     * Toggle ativo/inativo via AJAX
     */
    public function toggle($id) {
        $this->permission->method('delivery', 'update')->redirect();

        header('Content-Type: application/json');

        $cupom = $this->Cupons_model->get_by_id((int) $id);
        if (!$cupom) {
            echo json_encode(array(
                'success' => false,
                'message' => makeString(['please_try_again'])
            ));
            return;
        }

        $result = $this->Cupons_model->toggle_ativo((int) $id);
        $new_status = $cupom->ativo ? 0 : 1;

        echo json_encode(array(
            'success'    => $result,
            'new_status' => $new_status,
            'csrf_token' => $this->security->get_csrf_hash(),
            'message'    => $result ? makeString(['update_successfully']) : makeString(['please_try_again'])
        ));
    }
}
