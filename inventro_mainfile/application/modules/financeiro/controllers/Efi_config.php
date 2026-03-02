<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Configuracoes Efi Pay (PIX + Cartao)
 * Segue o padrao de delivery/controllers/Config.php
 */
class Efi_config extends MX_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->permission->module('financeiro')->redirect();
        $this->load->model('delivery/Delivery_model');
    }

    /**
     * Pagina de configuracao Efi Pay
     */
    public function index()
    {
        $this->permission->method('financeiro', 'update')->redirect();

        $data['title'] = makeString(['efi_pay_config']);
        $data['get_appsetting'] = $this->db->get('setting')->row();
        $data['configs'] = $this->Delivery_model->get_all_configs();

        // Valores padrao
        $defaults = array(
            'efipay_ativo'            => '0',
            'efipay_sandbox'          => '1',
            'efipay_client_id'        => '',
            'efipay_client_secret'    => '',
            'efipay_certificate_path' => '',
            'efipay_pix_chave'        => '',
            'efipay_pix_chave_tipo'   => '',
            'efipay_webhook_url'      => '',
            'efipay_skip_mtls'        => '1',
            'efipay_expiracao_padrao' => '3600',
            'efipay_status'           => 'nao_configurado',
            'efipay_ultimo_teste'     => '',
            'efipay_cartao_ativo'     => '0',
            'efipay_account_id'       => '',
        );

        foreach ($defaults as $key => $val) {
            if (!isset($data['configs'][$key])) {
                $data['configs'][$key] = $val;
            }
        }

        // Sugestao de URL do webhook
        $data['webhook_url_sugestao'] = base_url('efi_webhook');

        $data['module'] = 'financeiro';
        $data['page'] = 'efi_config';
        echo Modules::run('template/layout', $data);
    }

    /**
     * Salvar configuracoes (POST)
     */
    public function save()
    {
        $this->permission->method('financeiro', 'update')->redirect();

        // Toggles
        $configs = array();
        $toggles = array('efipay_ativo', 'efipay_sandbox', 'efipay_skip_mtls', 'efipay_cartao_ativo');
        foreach ($toggles as $key) {
            $configs[$key] = $this->input->post($key) ? '1' : '0';
        }

        // Campos texto
        $text_fields = array(
            'efipay_client_id', 'efipay_pix_chave', 'efipay_pix_chave_tipo',
            'efipay_webhook_url', 'efipay_expiracao_padrao', 'efipay_account_id',
        );
        foreach ($text_fields as $key) {
            $val = $this->input->post($key, true);
            if ($val !== null) {
                $configs[$key] = $val;
            }
        }

        // Client Secret — criptografar se fornecido
        $secret = $this->input->post('efipay_client_secret', true);
        if (!empty($secret)) {
            $this->load->library('encryption');
            $configs['efipay_client_secret'] = $this->encryption->encrypt($secret);
        }

        $this->Delivery_model->save_configs($configs);

        $this->session->set_flashdata('message', makeString(['save_successfully']));
        redirect('financeiro/efi_config');
    }

    /**
     * Upload de certificado .pem/.p12 (AJAX)
     */
    public function upload_certificate()
    {
        header('Content-Type: application/json');
        $this->permission->method('financeiro', 'update')->redirect();

        $upload_path = APPPATH . 'config/certs/efi_pay/';

        if (!is_dir($upload_path)) {
            mkdir($upload_path, 0700, true);
        }

        $config = array(
            'upload_path'   => $upload_path,
            'allowed_types' => 'pem|p12',
            'max_size'      => 1024, // 1MB
            'encrypt_name'  => true,
        );

        $this->load->library('upload', $config);

        if (!$this->upload->do_upload('certificate')) {
            echo json_encode(array(
                'success'    => false,
                'message'    => $this->upload->display_errors('', ''),
                'csrf_token' => $this->security->get_csrf_hash(),
            ));
            return;
        }

        $upload_data = $this->upload->data();
        $cert_path = $upload_data['full_path'];

        // Salvar caminho no config
        $this->Delivery_model->save_config('efipay_certificate_path', $cert_path);

        // Proteger arquivo
        @chmod($cert_path, 0600);

        echo json_encode(array(
            'success'    => true,
            'message'    => 'Certificado enviado: ' . $upload_data['file_name'],
            'file_name'  => $upload_data['file_name'],
            'csrf_token' => $this->security->get_csrf_hash(),
        ));
    }

    /**
     * Testar conexao com a API Efi (AJAX)
     */
    public function test_connection()
    {
        header('Content-Type: application/json');
        $this->permission->method('financeiro', 'update')->redirect();

        $this->load->library('Efi_pay');

        if (!$this->efi_pay->is_active()) {
            echo json_encode(array(
                'success'    => false,
                'message'    => 'Configure Client ID, Client Secret e Certificado primeiro.',
                'csrf_token' => $this->security->get_csrf_hash(),
            ));
            return;
        }

        $result = $this->efi_pay->test_connection();

        // Atualizar status
        $status = $result['success'] ? 'conectado' : 'erro';
        $this->Delivery_model->save_config('efipay_status', $status);
        if ($result['success']) {
            $this->Delivery_model->save_config('efipay_ultimo_teste', date('Y-m-d H:i:s'));
        }

        echo json_encode(array(
            'success'    => $result['success'],
            'message'    => $result['message'],
            'csrf_token' => $this->security->get_csrf_hash(),
        ));
    }

    /**
     * Registrar webhook na Efi Pay (AJAX)
     */
    public function register_webhook()
    {
        header('Content-Type: application/json');
        $this->permission->method('financeiro', 'update')->redirect();

        $this->load->library('Efi_pay');

        $webhook_url = $this->efi_pay->get_config('efipay_webhook_url');
        if (empty($webhook_url)) {
            echo json_encode(array(
                'success'    => false,
                'message'    => 'Configure a URL do webhook primeiro.',
                'csrf_token' => $this->security->get_csrf_hash(),
            ));
            return;
        }

        $result = $this->efi_pay->register_webhook($webhook_url);

        echo json_encode(array(
            'success'    => $result['success'],
            'message'    => $result['success'] ? 'Webhook registrado com sucesso!' : 'Erro: ' . $result['error'],
            'csrf_token' => $this->security->get_csrf_hash(),
        ));
    }
}
