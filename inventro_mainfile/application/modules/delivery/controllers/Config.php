<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Configurações do Cardápio Digital
 */
class Config extends MX_Controller {

    public function __construct() {
        parent::__construct();
        $this->permission->module('delivery')->redirect();
        $this->load->model('delivery/delivery_model');
        $this->load->library(['session', 'form_validation']);
        $this->load->helper(['url', 'form']);
    }

    /**
     * Página de configurações
     */
    public function index() {
        $data['title'] = 'Configurações do Cardápio';
        $data['get_appsetting'] = $this->db->get('setting')->row();
        
        // Buscar todas as configurações
        $data['configs'] = $this->delivery_model->get_all_configs();
        
        // Valores padrão
        $defaults = [
            'taxa_entrega' => '0',
            'pedido_minimo' => '0',
            'horario_abertura' => '08:00',
            'horario_fechamento' => '22:00',
            'aceita_cartao' => '1',
            'aceita_dinheiro' => '1',
            'aceita_pix' => '1',
            'pix_chave' => '',
            'mensagem_confirmacao' => 'Obrigado pelo seu pedido! Em breve entraremos em contato.',
            'tempo_medio_entrega' => '45'
        ];
        
        foreach ($defaults as $key => $val) {
            if (!isset($data['configs'][$key])) {
                $data['configs'][$key] = $val;
            }
        }
        
        $data['module'] = "delivery";
        $data['page'] = "config_view";
        echo Modules::run('template/layout', $data);
    }

    /**
     * Toggle pausar/retomar loja (AJAX)
     */
    public function toggle_pause() {
        header('Content-Type: application/json');

        $current = $this->delivery_model->get_config('loja_pausada', '0');
        $new_value = ($current === '1') ? '0' : '1';
        $this->delivery_model->save_config('loja_pausada', $new_value);

        echo json_encode([
            'success' => true,
            'loja_pausada' => $new_value === '1',
            'message' => $new_value === '1' ? 'Loja pausada' : 'Loja retomada',
            'csrf_token' => $this->security->get_csrf_hash()
        ]);
    }

    /**
     * Salvar configurações
     */
    public function save() {
        $configs = [
            'taxa_entrega' => $this->input->post('taxa_entrega', true),
            'pedido_minimo' => $this->input->post('pedido_minimo', true),
            'horario_abertura' => $this->input->post('horario_abertura', true),
            'horario_fechamento' => $this->input->post('horario_fechamento', true),
            'aceita_cartao' => $this->input->post('aceita_cartao') ? '1' : '0',
            'aceita_dinheiro' => $this->input->post('aceita_dinheiro') ? '1' : '0',
            'aceita_pix' => $this->input->post('aceita_pix') ? '1' : '0',
            'pix_chave' => $this->input->post('pix_chave', true),
            'mensagem_confirmacao' => $this->input->post('mensagem_confirmacao', true),
            'tempo_medio_entrega' => $this->input->post('tempo_medio_entrega', true)
        ];
        
        $result = $this->delivery_model->save_configs($configs);
        
        if ($result) {
            $this->session->set_flashdata('success', "<div class='alert alert-success'>Configurações salvas com sucesso!</div>");
        } else {
            $this->session->set_flashdata('error', "<div class='alert alert-danger'>Erro ao salvar configurações.</div>");
        }
        
        redirect('delivery/config');
    }
}
