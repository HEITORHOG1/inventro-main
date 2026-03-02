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

    // =============================================
    // n8n + WhatsApp Business API
    // =============================================

    /**
     * Pagina de configuracao n8n + WhatsApp
     */
    public function n8n() {
        $this->permission->method('delivery', 'update')->redirect();

        $data['title'] = 'WhatsApp & Automacao';
        $data['get_appsetting'] = $this->db->get('setting')->row();
        $data['configs'] = $this->delivery_model->get_all_configs();

        // Defaults para chaves n8n/whatsapp
        $defaults = array(
            'n8n_ativo' => '0',
            'n8n_webhook_url' => 'http://n8n:5678/webhook/inventro',
            'n8n_webhook_secret' => '',
            'n8n_status' => 'nao_configurado',
            'whatsapp_api_ativa' => '0',
            'whatsapp_api_phone_id' => '',
            'whatsapp_api_token' => '',
            'whatsapp_api_business_id' => '',
            'whatsapp_api_status' => 'nao_configurado',
            'whatsapp_api_ultimo_teste' => '',
            'setup_whatsapp_passo' => '0',
            'notif_pedido_criado_cliente' => '1',
            'notif_pedido_criado_motoboy' => '1',
            'notif_status_confirmado' => '1',
            'notif_status_preparando' => '1',
            'notif_status_pronto' => '1',
            'notif_status_saiu_entrega' => '1',
            'notif_status_entregue' => '1',
            'notif_status_cancelado' => '1',
            'notif_cupom_fiscal' => '1',
            'notif_motoboy_atribuido' => '1',
            'notif_resumo_diario' => '0',
            'notif_resumo_horario' => '23:00',
            'notif_admin_telefone' => '',
            'wpp_template_pedido_criado' => 'pedido_confirmado',
            'wpp_template_pedido_confirmado' => 'pedido_confirmado',
            'wpp_template_pedido_preparando' => 'pedido_preparando',
            'wpp_template_pedido_pronto' => 'pedido_pronto',
            'wpp_template_pedido_saiu' => 'pedido_saiu_entrega',
            'wpp_template_pedido_entregue' => 'pedido_entregue',
            'wpp_template_pedido_cancelado' => 'pedido_cancelado',
            'wpp_template_motoboy_novo' => 'motoboy_novo_pedido',
            'wpp_template_motoboy_atribuido' => 'motoboy_entrega_atribuida',
            'wpp_template_cupom_fiscal' => 'cupom_fiscal',
        );

        foreach ($defaults as $key => $val) {
            if (!isset($data['configs'][$key])) {
                $data['configs'][$key] = $val;
            }
        }

        $data['module'] = 'delivery';
        $data['page'] = 'n8n_config_view';
        echo Modules::run('template/layout', $data);
    }

    /**
     * Salvar configuracoes n8n + WhatsApp (POST)
     */
    public function save_n8n() {
        $this->permission->method('delivery', 'update')->redirect();

        // Toggles (checkbox: presente = 1, ausente = 0)
        $toggles = array(
            'n8n_ativo', 'whatsapp_api_ativa',
            'notif_pedido_criado_cliente', 'notif_pedido_criado_motoboy',
            'notif_status_confirmado', 'notif_status_preparando',
            'notif_status_pronto', 'notif_status_saiu_entrega',
            'notif_status_entregue', 'notif_status_cancelado',
            'notif_cupom_fiscal', 'notif_motoboy_atribuido',
            'notif_resumo_diario',
        );

        $configs = array();
        foreach ($toggles as $key) {
            $configs[$key] = $this->input->post($key) ? '1' : '0';
        }

        // Campos texto
        $text_fields = array(
            'n8n_webhook_url', 'n8n_webhook_secret',
            'whatsapp_api_phone_id', 'whatsapp_api_token', 'whatsapp_api_business_id',
            'notif_resumo_horario', 'notif_admin_telefone',
            'wpp_template_pedido_criado', 'wpp_template_pedido_confirmado',
            'wpp_template_pedido_preparando', 'wpp_template_pedido_pronto',
            'wpp_template_pedido_saiu', 'wpp_template_pedido_entregue',
            'wpp_template_pedido_cancelado', 'wpp_template_motoboy_novo',
            'wpp_template_motoboy_atribuido', 'wpp_template_cupom_fiscal',
        );

        foreach ($text_fields as $key) {
            $val = $this->input->post($key, true);
            if ($val !== null) {
                $configs[$key] = $val;
            }
        }

        // Calcula passo do wizard
        $passo = 0;
        if ($configs['n8n_ativo'] === '1') $passo = 1;
        if ($passo >= 1 && $this->delivery_model->get_config('n8n_status') === 'conectado') $passo = 2;
        if ($passo >= 2 && $configs['whatsapp_api_ativa'] === '1' && !empty($configs['whatsapp_api_token'])) $passo = 3;
        if ($passo >= 3 && !empty($configs['wpp_template_pedido_criado'])) $passo = 4;
        if ($passo >= 4 && $this->delivery_model->get_config('whatsapp_api_status') === 'conectado') $passo = 5;
        $configs['setup_whatsapp_passo'] = (string)$passo;

        $this->delivery_model->save_configs($configs);

        $this->session->set_flashdata('msg', 'Configuracoes WhatsApp & Automacao salvas com sucesso!');
        $this->session->set_flashdata('msg_type', 'success');
        redirect('delivery/config/n8n');
    }

    /**
     * Testar conexao n8n (AJAX)
     */
    public function test_n8n() {
        header('Content-Type: application/json');

        $webhook_url = $this->delivery_model->get_config('n8n_webhook_url', '');
        $webhook_secret = $this->delivery_model->get_config('n8n_webhook_secret', '');

        if (empty($webhook_url)) {
            echo json_encode(array(
                'success' => false,
                'message' => 'Webhook URL nao configurada',
                'csrf_token' => $this->security->get_csrf_hash()
            ));
            return;
        }

        // Envia evento de teste (path /teste no n8n)
        $test_url = rtrim($webhook_url, '/') . '/teste';

        $payload = json_encode(array(
            'event' => 'teste.conexao',
            'timestamp' => date('c'),
            'data' => array('source' => 'inventro_admin_test')
        ));

        $headers = array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($payload),
        );

        if (!empty($webhook_secret)) {
            $signature = hash_hmac('sha256', $payload, $webhook_secret);
            $headers[] = 'X-Webhook-Signature: ' . $signature;
        }

        $ch = curl_init($test_url);
        curl_setopt_array($ch, array(
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $payload,
            CURLOPT_HTTPHEADER     => $headers,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 5,
            CURLOPT_CONNECTTIMEOUT => 3,
            CURLOPT_SSL_VERIFYPEER => false,
        ));

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error || $http_code < 200 || $http_code >= 300) {
            $this->delivery_model->save_config('n8n_status', 'erro');
            echo json_encode(array(
                'success' => false,
                'message' => 'Falha: HTTP ' . $http_code . ($error ? ' - ' . $error : ''),
                'csrf_token' => $this->security->get_csrf_hash()
            ));
            return;
        }

        $this->delivery_model->save_config('n8n_status', 'conectado');
        echo json_encode(array(
            'success' => true,
            'message' => 'Conexao OK! HTTP ' . $http_code,
            'csrf_token' => $this->security->get_csrf_hash()
        ));
    }

    /**
     * Testar conexao WhatsApp Business API (AJAX)
     * Envia evento de teste via webhook n8n
     */
    public function test_whatsapp() {
        header('Content-Type: application/json');

        $this->load->library('Webhook_notifier');

        $webhook_url = $this->delivery_model->get_config('n8n_webhook_url', '');
        $webhook_secret = $this->delivery_model->get_config('n8n_webhook_secret', '');
        $admin_tel = $this->delivery_model->get_config('notif_admin_telefone', '');

        if (empty($webhook_url)) {
            echo json_encode(array(
                'success' => false,
                'message' => 'Configure e teste o n8n primeiro',
                'csrf_token' => $this->security->get_csrf_hash()
            ));
            return;
        }

        // Envia evento de teste WhatsApp via webhook (path /teste no n8n)
        $test_url = rtrim($webhook_url, '/') . '/teste';

        $payload = json_encode(array(
            'event' => 'teste.whatsapp',
            'timestamp' => date('c'),
            'data' => array(
                'admin_telefone' => $admin_tel,
                'admin_telefone_e164' => $this->webhook_notifier->normalize_phone($admin_tel),
                'source' => 'inventro_admin_test'
            )
        ));

        $headers = array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($payload),
        );

        if (!empty($webhook_secret)) {
            $signature = hash_hmac('sha256', $payload, $webhook_secret);
            $headers[] = 'X-Webhook-Signature: ' . $signature;
        }

        $ch = curl_init($test_url);
        curl_setopt_array($ch, array(
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $payload,
            CURLOPT_HTTPHEADER     => $headers,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 10,
            CURLOPT_CONNECTTIMEOUT => 3,
            CURLOPT_SSL_VERIFYPEER => false,
        ));

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error || $http_code < 200 || $http_code >= 300) {
            $this->delivery_model->save_config('whatsapp_api_status', 'erro');
            echo json_encode(array(
                'success' => false,
                'message' => 'Falha: HTTP ' . $http_code . ($error ? ' - ' . $error : ''),
                'csrf_token' => $this->security->get_csrf_hash()
            ));
            return;
        }

        $this->delivery_model->save_config('whatsapp_api_status', 'conectado');
        $this->delivery_model->save_config('whatsapp_api_ultimo_teste', date('Y-m-d H:i:s'));
        echo json_encode(array(
            'success' => true,
            'message' => 'Teste enviado com sucesso! Verifique o WhatsApp do admin.',
            'csrf_token' => $this->security->get_csrf_hash()
        ));
    }

    /**
     * Verificar status dos templates na Meta API (AJAX)
     */
    public function check_templates() {
        header('Content-Type: application/json');

        $waba_id = $this->delivery_model->get_config('whatsapp_api_business_id', '');
        $token = $this->delivery_model->get_config('whatsapp_api_token', '');

        if (empty($waba_id) || empty($token)) {
            echo json_encode(array(
                'success' => false,
                'message' => 'WABA ID e Token sao necessarios para verificar templates',
                'csrf_token' => $this->security->get_csrf_hash()
            ));
            return;
        }

        // Busca templates na API Meta
        $url = 'https://graph.facebook.com/v18.0/' . urlencode($waba_id) . '/message_templates';

        $ch = curl_init($url);
        curl_setopt_array($ch, array(
            CURLOPT_HTTPHEADER     => array('Authorization: Bearer ' . $token),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 10,
            CURLOPT_CONNECTTIMEOUT => 5,
        ));

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error || $http_code !== 200) {
            echo json_encode(array(
                'success' => false,
                'message' => 'Erro ao consultar Meta API: HTTP ' . $http_code,
                'csrf_token' => $this->security->get_csrf_hash()
            ));
            return;
        }

        $body = json_decode($response, true);
        if (!isset($body['data'])) {
            echo json_encode(array(
                'success' => false,
                'message' => 'Resposta inesperada da Meta API',
                'csrf_token' => $this->security->get_csrf_hash()
            ));
            return;
        }

        // Indexa templates por nome
        $meta_templates = array();
        foreach ($body['data'] as $tpl) {
            $meta_templates[$tpl['name']] = $tpl['status'];
        }

        // Mapeia configs locais para status na Meta
        $configs = $this->delivery_model->get_all_configs();
        $template_keys = array(
            'wpp_template_pedido_criado', 'wpp_template_pedido_confirmado',
            'wpp_template_pedido_preparando', 'wpp_template_pedido_pronto',
            'wpp_template_pedido_saiu', 'wpp_template_pedido_entregue',
            'wpp_template_pedido_cancelado', 'wpp_template_motoboy_novo',
            'wpp_template_motoboy_atribuido', 'wpp_template_cupom_fiscal',
        );

        $result = array();
        foreach ($template_keys as $key) {
            $name = isset($configs[$key]) ? $configs[$key] : '';
            if (empty($name)) {
                $result[$key] = 'NAO_CONFIGURADO';
            } elseif (isset($meta_templates[$name])) {
                $result[$key] = $meta_templates[$name];
            } else {
                $result[$key] = 'NAO_ENCONTRADO';
            }
        }

        echo json_encode(array(
            'success' => true,
            'templates' => $result,
            'csrf_token' => $this->security->get_csrf_hash()
        ));
    }
}
