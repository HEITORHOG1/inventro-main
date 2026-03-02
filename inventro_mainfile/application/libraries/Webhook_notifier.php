<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Webhook_notifier Library
 *
 * Envia eventos para o n8n via webhook.
 * Se n8n_ativo == '0' (default), nenhum envio é feito e o sistema
 * continua usando o fluxo manual (wa.me/).
 *
 * Uso:
 *   $this->load->library('Webhook_notifier');
 *   $enviado = $this->webhook_notifier->send('pedido.criado', $dados);
 *   if (!$enviado) { // fallback manual }
 */
class Webhook_notifier {

    private $CI;
    private $configs = array();
    private $loaded = false;

    /** Mapeamento evento -> chave notif_* no cardapio_config */
    private $notification_map = array(
        'pedido.criado'                 => 'notif_pedido_criado_cliente',
        'pedido.criado.motoboy'         => 'notif_pedido_criado_motoboy',
        'pedido.status.confirmado'      => 'notif_status_confirmado',
        'pedido.status.preparando'      => 'notif_status_preparando',
        'pedido.status.pronto'          => 'notif_status_pronto',
        'pedido.status.saiu_entrega'    => 'notif_status_saiu_entrega',
        'pedido.status.entregue'        => 'notif_status_entregue',
        'pedido.status.cancelado'       => 'notif_status_cancelado',
        'pedido.motoboy_atribuido'      => 'notif_motoboy_atribuido',
        'pedido.cupom_fiscal'           => 'notif_cupom_fiscal',
        'resumo.diario'                 => 'notif_resumo_diario',
    );

    /** Mapeamento evento -> chave wpp_template_* no cardapio_config */
    private $template_map = array(
        'pedido.criado'                 => 'wpp_template_pedido_criado',
        'pedido.criado.motoboy'         => 'wpp_template_motoboy_novo',
        'pedido.status.confirmado'      => 'wpp_template_pedido_confirmado',
        'pedido.status.preparando'      => 'wpp_template_pedido_preparando',
        'pedido.status.pronto'          => 'wpp_template_pedido_pronto',
        'pedido.status.saiu_entrega'    => 'wpp_template_pedido_saiu',
        'pedido.status.entregue'        => 'wpp_template_pedido_entregue',
        'pedido.status.cancelado'       => 'wpp_template_pedido_cancelado',
        'pedido.motoboy_atribuido'      => 'wpp_template_motoboy_atribuido',
        'pedido.cupom_fiscal'           => 'wpp_template_cupom_fiscal',
    );

    /** Mapeamento evento -> sufixo do webhook path (cada workflow tem path unico no n8n) */
    private $webhook_path_map = array(
        'pedido.criado'                 => '/status-cliente',
        'pedido.criado.motoboy'         => '/pedido-motoboys',
        'pedido.status.confirmado'      => '/status-cliente',
        'pedido.status.preparando'      => '/status-cliente',
        'pedido.status.pronto'          => '/status-cliente',
        'pedido.status.saiu_entrega'    => '/status-cliente',
        'pedido.status.entregue'        => '/status-cliente',
        'pedido.status.cancelado'       => '/status-cliente',
        'pedido.motoboy_atribuido'      => '/motoboy-atribuido',
        'pedido.cupom_fiscal'           => '/cupom-fiscal',
        'teste.ping'                    => '/teste',
        'teste.whatsapp'                => '/teste',
    );

    public function __construct()
    {
        $this->CI =& get_instance();
    }

    /**
     * Carrega configs do cardapio_config (lazy load)
     */
    private function load_configs()
    {
        if ($this->loaded) {
            return;
        }

        $this->CI->load->model('delivery/Delivery_model');
        $all = $this->CI->Delivery_model->get_all_configs();

        if (is_array($all)) {
            $this->configs = $all;
        }

        $this->loaded = true;
    }

    /**
     * Retorna valor de uma config, com fallback
     */
    private function get_config($key, $default = '')
    {
        $this->load_configs();
        return isset($this->configs[$key]) ? $this->configs[$key] : $default;
    }

    /**
     * Verifica se a integração n8n está ativa e configurada
     */
    public function is_active()
    {
        return $this->get_config('n8n_ativo', '0') === '1'
            && !empty($this->get_config('n8n_webhook_url'));
    }

    /**
     * Verifica se a notificação de um evento está habilitada
     */
    public function is_notification_enabled($event)
    {
        if (!isset($this->notification_map[$event])) {
            return false;
        }
        $key = $this->notification_map[$event];
        return $this->get_config($key, '0') === '1';
    }

    /**
     * Retorna o nome do template WhatsApp para um evento
     */
    public function get_template_name($event)
    {
        if (!isset($this->template_map[$event])) {
            return '';
        }
        $key = $this->template_map[$event];
        return $this->get_config($key, '');
    }

    /**
     * Normaliza telefone brasileiro para E.164
     * Ex: (11) 99999-1234 -> 5511999991234
     */
    public function normalize_phone($phone)
    {
        // Remove tudo que não é dígito
        $digits = preg_replace('/\D/', '', $phone);

        if (empty($digits)) {
            return '';
        }

        // Se já começa com 55 e tem 12-13 dígitos, está ok
        if (strlen($digits) >= 12 && substr($digits, 0, 2) === '55') {
            return $digits;
        }

        // Se tem 10-11 dígitos (DDD + número), adiciona 55
        if (strlen($digits) >= 10 && strlen($digits) <= 11) {
            return '55' . $digits;
        }

        // Retorna como está se não se encaixa
        return $digits;
    }

    /**
     * Envia evento para o webhook n8n.
     *
     * @param string $event  Nome do evento (ex: 'pedido.criado')
     * @param array  $data   Dados do evento (pedido, cliente, etc)
     * @return bool  true se enviado com sucesso, false caso contrário (fallback)
     */
    public function send($event, $data = array())
    {
        // Guard 1: n8n desativado
        if (!$this->is_active()) {
            return false;
        }

        // Guard 2: notificação deste evento está desabilitada
        if (!$this->is_notification_enabled($event)) {
            return false;
        }

        // Injeta nome do template WhatsApp no payload
        $template_name = $this->get_template_name($event);

        // Normaliza telefones no payload
        if (isset($data['cliente_telefone'])) {
            $data['cliente_telefone_e164'] = $this->normalize_phone($data['cliente_telefone']);
        }
        if (isset($data['motoboy_telefone'])) {
            $data['motoboy_telefone_e164'] = $this->normalize_phone($data['motoboy_telefone']);
        }
        if (isset($data['admin_telefone'])) {
            $data['admin_telefone_e164'] = $this->normalize_phone($data['admin_telefone']);
        }

        // Monta payload
        $payload = array(
            'event'                   => $event,
            'whatsapp_template_name'  => $template_name,
            'timestamp'               => date('c'),
            'data'                    => $data,
        );

        // Determina URL do webhook (base + sufixo do evento)
        $webhook_base = rtrim($this->get_config('n8n_webhook_url'), '/');
        $webhook_secret = $this->get_config('n8n_webhook_secret', '');

        $path_suffix = isset($this->webhook_path_map[$event])
            ? $this->webhook_path_map[$event]
            : '';
        $webhook_url = $webhook_base . $path_suffix;

        return $this->_do_post($webhook_url, $payload, $webhook_secret);
    }

    /**
     * Envia POST cURL com timeout curto (fire-and-forget estilo)
     *
     * @param string $url
     * @param array  $payload
     * @param string $secret
     * @return bool
     */
    private function _do_post($url, $payload, $secret = '')
    {
        $json = json_encode($payload);

        $headers = array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($json),
        );

        if (!empty($secret)) {
            $signature = hash_hmac('sha256', $json, $secret);
            $headers[] = 'X-Webhook-Signature: ' . $signature;
        }

        $ch = curl_init($url);
        curl_setopt_array($ch, array(
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $json,
            CURLOPT_HTTPHEADER     => $headers,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 5,
            CURLOPT_CONNECTTIMEOUT => 2,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_SSL_VERIFYPEER => false,
        ));

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error || $http_code < 200 || $http_code >= 300) {
            log_message('error', "Webhook_notifier: Falha ao enviar evento '{$payload['event']}' para {$url}. HTTP {$http_code}. Erro: {$error}");
            return false;
        }

        log_message('info', "Webhook_notifier: Evento '{$payload['event']}' enviado com sucesso para {$url}. HTTP {$http_code}");
        return true;
    }
}
