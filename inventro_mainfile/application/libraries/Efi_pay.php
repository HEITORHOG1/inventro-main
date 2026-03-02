<?php defined('BASEPATH') OR exit('No direct script access allowed');

use Efi\Exception\EfiException;
use Efi\EfiPay;

/**
 * Efi_pay Library
 *
 * Wrapper para o SDK Efi Pay (PIX + Cartao de Credito).
 * Lazy-load de configs via cardapio_config (mesmo padrao de Webhook_notifier).
 *
 * Uso:
 *   $this->load->library('Efi_pay');
 *   if ($this->efi_pay->is_active()) {
 *       $result = $this->efi_pay->create_pix_charge(100.00, 'Venda #123');
 *   }
 */
class Efi_pay {

    private $CI;
    private $configs = array();
    private $loaded = false;

    /** IPs conhecidos da Efi Pay para webhook */
    private $efi_ips = array('34.193.116.226');

    public function __construct()
    {
        $this->CI =& get_instance();
    }

    // =========================================================
    // Config Management (lazy load from cardapio_config)
    // =========================================================

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
    public function get_config($key, $default = '')
    {
        $this->load_configs();
        return isset($this->configs[$key]) ? $this->configs[$key] : $default;
    }

    /**
     * Verifica se a integracao PIX esta ativa e configurada
     */
    public function is_active()
    {
        return $this->get_config('efipay_ativo', '0') === '1'
            && !empty($this->get_config('efipay_client_id'))
            && !empty($this->get_config('efipay_client_secret'))
            && !empty($this->get_config('efipay_certificate_path'));
    }

    /**
     * Verifica se cartao esta habilitado
     */
    public function is_card_active()
    {
        return $this->is_active()
            && $this->get_config('efipay_cartao_ativo', '0') === '1';
    }

    /**
     * Retorna instancia do SDK EfiPay configurada
     */
    private function get_api()
    {
        $sandbox = $this->get_config('efipay_sandbox', '1') === '1';

        // Descriptografar client_secret
        $client_secret = $this->get_config('efipay_client_secret', '');
        if (!empty($client_secret)) {
            $this->CI->load->library('encryption');
            $decrypted = $this->CI->encryption->decrypt($client_secret);
            if ($decrypted !== false) {
                $client_secret = $decrypted;
            }
        }

        $options = array(
            'clientId'       => $this->get_config('efipay_client_id'),
            'clientSecret'   => $client_secret,
            'certificate'    => $this->get_config('efipay_certificate_path'),
            'sandbox'        => $sandbox,
            'debug'          => false,
            'timeout'        => 30,
        );

        return new EfiPay($options);
    }

    // =========================================================
    // PIX - Cobranca Imediata
    // =========================================================

    /**
     * Cria cobranca PIX imediata
     *
     * @param float  $valor
     * @param string $descricao
     * @param string $devedor_nome
     * @param string $devedor_cpf
     * @param int    $expiracao  Segundos (default config)
     * @return array ['success' => bool, 'data' => [...], 'error' => string]
     */
    public function create_pix_charge($valor, $descricao, $devedor_nome = null, $devedor_cpf = null, $expiracao = null)
    {
        try {
            $api = $this->get_api();

            if (empty($expiracao)) {
                $expiracao = (int) $this->get_config('efipay_expiracao_padrao', '3600');
            }

            $body = array(
                'calendario' => array('expiracao' => $expiracao),
                'valor'      => array('original' => number_format($valor, 2, '.', '')),
                'chave'      => $this->get_config('efipay_pix_chave'),
                'solicitacaoPagador' => mb_substr($descricao, 0, 140),
            );

            // Devedor (opcional mas recomendado)
            if (!empty($devedor_cpf) && !empty($devedor_nome)) {
                $cpf_limpo = preg_replace('/\D/', '', $devedor_cpf);
                if (strlen($cpf_limpo) === 11) {
                    $body['devedor'] = array(
                        'cpf'  => $cpf_limpo,
                        'nome' => mb_substr($devedor_nome, 0, 200),
                    );
                } elseif (strlen($cpf_limpo) === 14) {
                    $body['devedor'] = array(
                        'cnpj' => $cpf_limpo,
                        'nome' => mb_substr($devedor_nome, 0, 200),
                    );
                }
            }

            $response = $api->pixCreateImmediateCharge(array(), $body);

            // Extrair location ID
            $location_id = null;
            $location_url = null;
            if (isset($response['loc'])) {
                $location_id = $response['loc']['id'];
                $location_url = isset($response['loc']['location']) ? $response['loc']['location'] : null;
            }

            // Gerar QR Code
            $qrcode_data = null;
            $pix_copia_cola = null;
            if ($location_id) {
                $qr_result = $this->get_qrcode($location_id);
                if ($qr_result['success']) {
                    $qrcode_data = $qr_result['data']['imagemQrcode'];
                    $pix_copia_cola = $qr_result['data']['qrcode'];
                }
            }

            return array(
                'success'        => true,
                'data'           => array(
                    'txid'           => $response['txid'],
                    'location_id'    => $location_id,
                    'location_url'   => $location_url,
                    'qrcode_base64'  => $qrcode_data,
                    'pix_copia_cola' => $pix_copia_cola,
                    'valor'          => $valor,
                    'expiracao'      => $expiracao,
                    'raw_response'   => $response,
                ),
                'error' => null,
            );

        } catch (EfiException $e) {
            log_message('error', 'Efi_pay::create_pix_charge - EfiException: ' . $e->error . ' | ' . $e->errorDescription);
            return array(
                'success' => false,
                'data'    => null,
                'error'   => $e->error . ': ' . $e->errorDescription,
            );
        } catch (\Exception $e) {
            log_message('error', 'Efi_pay::create_pix_charge - Exception: ' . $e->getMessage());
            return array(
                'success' => false,
                'data'    => null,
                'error'   => $e->getMessage(),
            );
        }
    }

    /**
     * Gera QR Code para uma location
     *
     * @param int $location_id
     * @return array
     */
    public function get_qrcode($location_id)
    {
        try {
            $api = $this->get_api();
            $response = $api->pixGenerateQRCode(array('id' => $location_id));

            return array(
                'success' => true,
                'data'    => $response,
                'error'   => null,
            );
        } catch (EfiException $e) {
            log_message('error', 'Efi_pay::get_qrcode - EfiException: ' . $e->error);
            return array(
                'success' => false,
                'data'    => null,
                'error'   => $e->error . ': ' . $e->errorDescription,
            );
        } catch (\Exception $e) {
            log_message('error', 'Efi_pay::get_qrcode - Exception: ' . $e->getMessage());
            return array(
                'success' => false,
                'data'    => null,
                'error'   => $e->getMessage(),
            );
        }
    }

    /**
     * Consulta status de uma cobranca PIX
     *
     * @param string $txid
     * @return array
     */
    public function get_pix_status($txid)
    {
        try {
            $api = $this->get_api();
            $response = $api->pixDetailCharge(array('txid' => $txid));

            return array(
                'success' => true,
                'data'    => $response,
                'error'   => null,
            );
        } catch (EfiException $e) {
            log_message('error', 'Efi_pay::get_pix_status - EfiException: ' . $e->error);
            return array(
                'success' => false,
                'data'    => null,
                'error'   => $e->error . ': ' . $e->errorDescription,
            );
        } catch (\Exception $e) {
            return array(
                'success' => false,
                'data'    => null,
                'error'   => $e->getMessage(),
            );
        }
    }

    // =========================================================
    // PIX - Webhook
    // =========================================================

    /**
     * Registra webhook na Efi Pay
     *
     * @param string $webhook_url URL publica HTTPS
     * @return array
     */
    public function register_webhook($webhook_url)
    {
        try {
            $api = $this->get_api();
            $chave = $this->get_config('efipay_pix_chave');

            $params = array('chave' => $chave);
            $body = array('webhookUrl' => $webhook_url);

            // Skip mTLS se configurado
            $skip_mtls = $this->get_config('efipay_skip_mtls', '1') === '1';
            if ($skip_mtls) {
                $params['x-skip-mtls-checking'] = 'true';
            }

            $response = $api->pixConfigWebhook($params, $body);

            return array(
                'success' => true,
                'data'    => $response,
                'error'   => null,
            );
        } catch (EfiException $e) {
            log_message('error', 'Efi_pay::register_webhook - EfiException: ' . $e->error . ' | ' . $e->errorDescription);
            return array(
                'success' => false,
                'data'    => null,
                'error'   => $e->error . ': ' . $e->errorDescription,
            );
        } catch (\Exception $e) {
            log_message('error', 'Efi_pay::register_webhook - Exception: ' . $e->getMessage());
            return array(
                'success' => false,
                'data'    => null,
                'error'   => $e->getMessage(),
            );
        }
    }

    /**
     * Valida payload de webhook da Efi
     *
     * @param string $raw_body Body cru da requisicao
     * @return array ['valid' => bool, 'pix_data' => array, 'error' => string]
     */
    public function validate_webhook($raw_body)
    {
        // Validar IP de origem
        $remote_ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
        $is_sandbox = $this->get_config('efipay_sandbox', '1') === '1';

        if (!$is_sandbox && !in_array($remote_ip, $this->efi_ips)) {
            log_message('error', 'Efi_pay::validate_webhook - IP nao autorizado: ' . $remote_ip);
            return array('valid' => false, 'pix_data' => null, 'error' => 'IP nao autorizado');
        }

        // Parse JSON
        $payload = json_decode($raw_body, true);
        if (json_last_error() !== JSON_ERROR_NONE || !isset($payload['pix'])) {
            log_message('error', 'Efi_pay::validate_webhook - JSON invalido ou sem campo pix');
            return array('valid' => false, 'pix_data' => null, 'error' => 'Payload invalido');
        }

        return array(
            'valid'    => true,
            'pix_data' => $payload['pix'],
            'error'    => null,
        );
    }

    // =========================================================
    // PIX - Devolucao
    // =========================================================

    /**
     * Devolucao PIX
     *
     * @param string $e2e_id  endToEndId da transacao
     * @param float  $valor
     * @return array
     */
    public function refund_pix($e2e_id, $valor)
    {
        try {
            $api = $this->get_api();
            $devolution_id = bin2hex(random_bytes(10));

            $params = array(
                'e2eId' => $e2e_id,
                'id'    => $devolution_id,
            );
            $body = array(
                'valor' => number_format($valor, 2, '.', ''),
            );

            $response = $api->pixDevolution($params, $body);

            return array(
                'success' => true,
                'data'    => $response,
                'error'   => null,
            );
        } catch (EfiException $e) {
            log_message('error', 'Efi_pay::refund_pix - EfiException: ' . $e->error);
            return array(
                'success' => false,
                'data'    => null,
                'error'   => $e->error . ': ' . $e->errorDescription,
            );
        } catch (\Exception $e) {
            return array(
                'success' => false,
                'data'    => null,
                'error'   => $e->getMessage(),
            );
        }
    }

    // =========================================================
    // Cartao de Credito
    // =========================================================

    /**
     * Cria cobranca de cartao one-step
     *
     * @param array  $items          [['name' => '...', 'value' => cents, 'amount' => 1]]
     * @param array  $customer       ['name', 'cpf', 'email', 'phone_number']
     * @param string $payment_token  Token gerado no frontend
     * @param int    $parcelas       Numero de parcelas
     * @param array  $billing_address ['street', 'number', 'neighborhood', 'zipcode', 'city', 'state']
     * @return array
     */
    public function create_card_charge_onestep($items, $customer, $payment_token, $parcelas = 1, $billing_address = null)
    {
        try {
            $api = $this->get_api();

            $body = array(
                'items'   => $items,
                'payment' => array(
                    'credit_card' => array(
                        'installments'  => (int) $parcelas,
                        'payment_token' => $payment_token,
                        'customer'      => $customer,
                    ),
                ),
            );

            if (!empty($billing_address)) {
                $body['payment']['credit_card']['billing_address'] = $billing_address;
            }

            $response = $api->createOneStepCharge(array(), $body);

            return array(
                'success' => true,
                'data'    => $response,
                'error'   => null,
            );
        } catch (EfiException $e) {
            log_message('error', 'Efi_pay::create_card_charge_onestep - EfiException: ' . $e->error . ' | ' . $e->errorDescription);
            return array(
                'success' => false,
                'data'    => null,
                'error'   => $e->error . ': ' . $e->errorDescription,
            );
        } catch (\Exception $e) {
            log_message('error', 'Efi_pay::create_card_charge_onestep - Exception: ' . $e->getMessage());
            return array(
                'success' => false,
                'data'    => null,
                'error'   => $e->getMessage(),
            );
        }
    }

    /**
     * Busca opcoes de parcelas para uma bandeira
     *
     * @param string $brand  visa, mastercard, amex, elo
     * @param int    $total  Valor total em centavos
     * @return array
     */
    public function get_installments($brand, $total)
    {
        try {
            $api = $this->get_api();
            $params = array(
                'brand' => $brand,
                'total' => $total,
            );

            $response = $api->getInstallments($params);

            return array(
                'success' => true,
                'data'    => $response,
                'error'   => null,
            );
        } catch (EfiException $e) {
            return array(
                'success' => false,
                'data'    => null,
                'error'   => $e->error . ': ' . $e->errorDescription,
            );
        } catch (\Exception $e) {
            return array(
                'success' => false,
                'data'    => null,
                'error'   => $e->getMessage(),
            );
        }
    }

    /**
     * Consulta cobranca de cartao
     *
     * @param int $charge_id
     * @return array
     */
    public function get_card_charge($charge_id)
    {
        try {
            $api = $this->get_api();
            $response = $api->detailCharge(array('id' => $charge_id));

            return array(
                'success' => true,
                'data'    => $response,
                'error'   => null,
            );
        } catch (EfiException $e) {
            return array(
                'success' => false,
                'data'    => null,
                'error'   => $e->error . ': ' . $e->errorDescription,
            );
        } catch (\Exception $e) {
            return array(
                'success' => false,
                'data'    => null,
                'error'   => $e->getMessage(),
            );
        }
    }

    // =========================================================
    // Teste de Conexao
    // =========================================================

    /**
     * Testa se as credenciais estao corretas
     *
     * @return array ['success' => bool, 'message' => string]
     */
    public function test_connection()
    {
        try {
            $api = $this->get_api();

            // Tenta listar webhooks como teste (endpoint leve)
            $params = array(
                'inicio' => date('Y-m-d\TH:i:s\Z', strtotime('-1 day')),
                'fim'    => date('Y-m-d\TH:i:s\Z'),
            );
            $api->pixListWebhook($params);

            return array(
                'success' => true,
                'message' => 'Conexao OK! Credenciais validas.',
            );
        } catch (EfiException $e) {
            // Erro 403 pode significar que nao tem permissao de webhook
            // mas as credenciais estao OK
            if (strpos($e->error, '403') !== false || strpos($e->errorDescription, 'permiss') !== false) {
                return array(
                    'success' => true,
                    'message' => 'Credenciais validas (verificar escopos de webhook).',
                );
            }
            return array(
                'success' => false,
                'message' => 'Erro: ' . $e->error . ' - ' . $e->errorDescription,
            );
        } catch (\Exception $e) {
            return array(
                'success' => false,
                'message' => 'Erro: ' . $e->getMessage(),
            );
        }
    }
}
