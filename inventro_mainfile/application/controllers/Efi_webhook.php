<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Efi_webhook Controller (Publico)
 *
 * Recebe notificacoes de pagamento PIX da Efi Pay.
 * Estende CI_Controller (sem auth) — mesmo padrao de Cardapio.php.
 *
 * A Efi Pay envia POST para /efi_webhook/pix quando um PIX e pago.
 */
class Efi_webhook extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        // Publico — sem sessao, sem permission check
        $this->load->library(array('Efi_pay'));
        $this->load->model('financeiro/Efi_pix_model');
        $this->load->model('financeiro/Contas_receber_model');
        $this->load->library('Generators');
    }

    /**
     * Endpoint para receber notificacoes PIX
     * URL: /efi_webhook/pix (Efi appends /pix to registered URL)
     */
    public function pix()
    {
        // Rate limiting basico por IP
        $remote_ip = $this->input->ip_address();

        // Ler body cru
        $raw_body = file_get_contents('php://input');

        if (empty($raw_body)) {
            log_message('info', 'Efi_webhook::pix - Empty body from IP: ' . $remote_ip);
            http_response_code(200);
            echo json_encode(array('status' => 'ok'));
            return;
        }

        // Validar payload
        $validation = $this->efi_pay->validate_webhook($raw_body);
        if (!$validation['valid']) {
            log_message('error', 'Efi_webhook::pix - Invalid payload from IP: ' . $remote_ip . ' | Error: ' . $validation['error']);
            http_response_code(400);
            echo json_encode(array('error' => 'Invalid payload'));
            return;
        }

        // Processar cada PIX no array
        $pix_array = $validation['pix_data'];
        foreach ($pix_array as $pix) {
            $this->_process_payment($pix, $raw_body);
        }

        http_response_code(200);
        echo json_encode(array('status' => 'ok'));
    }

    /**
     * Processa um pagamento PIX individual
     */
    private function _process_payment($pix, $raw_body)
    {
        $txid = isset($pix['txid']) ? $pix['txid'] : null;
        $e2e_id = isset($pix['endToEndId']) ? $pix['endToEndId'] : null;
        $valor = floatval(isset($pix['valor']) ? $pix['valor'] : 0);

        if (empty($txid)) {
            log_message('error', 'Efi_webhook: PIX sem txid');
            return;
        }

        // Buscar charge
        $charge = $this->Efi_pix_model->find_by_txid($txid);
        if (!$charge) {
            log_message('info', 'Efi_webhook: txid nao encontrado: ' . $txid);
            return;
        }

        // Idempotencia — ja confirmada
        if ($charge->status !== 'pending') {
            log_message('info', 'Efi_webhook: txid ja processado (' . $charge->status . '): ' . $txid);
            return;
        }

        // Validar valor (tolerancia de R$0.02)
        $expected = floatval($charge->valor);
        if (abs($expected - $valor) > 0.02) {
            log_message('error', 'Efi_webhook: valor divergente para txid=' . $txid . ' | esperado=' . $expected . ' | recebido=' . $valor);
            // Processa mesmo assim, mas loga o alerta
        }

        // Marcar charge como confirmada
        $this->Efi_pix_model->mark_as_confirmed($txid, $e2e_id, $raw_body);

        // Verificar se e pedido delivery (order_id) ou conta a receber
        if (!empty($charge->order_id)) {
            // Delivery order — atualizar status do pedido para 'pendente'
            $this->load->model('delivery/Delivery_model');
            $this->Delivery_model->update_order_status(
                (int)$charge->order_id,
                'pendente',
                ['pagamento_confirmado' => 1]
            );
            log_message('info', 'Efi_webhook: Pedido delivery #' . $charge->order_id . ' pago via PIX (txid=' . $txid . ')');
            return;
        }

        // Contas a receber — fluxo financeiro (auto-baixa + ledger)
        $conta = $this->Contas_receber_model->find_by_id($charge->conta_receber_id);
        if (!$conta) {
            log_message('error', 'Efi_webhook: conta_receber nao encontrada: ' . $charge->conta_receber_id);
            return;
        }

        // Auto-baixa
        $transaction_id = "CR" . date('ymd') . $this->generators->generator(8);

        // 1. Registrar baixa
        $this->Contas_receber_model->registrar_baixa(array(
            'tipo'             => 'receber',
            'conta_id'         => $conta->id,
            'valor'            => $valor,
            'data_baixa'       => date('Y-m-d'),
            'forma_pagamento'  => 'pix',
            'observacao'       => 'Pagamento automatico via PIX Efi Pay (e2e: ' . $e2e_id . ')',
            'transaction_id'   => $transaction_id,
            'created_by'       => $conta->created_by,
        ));

        // 2. Atualizar conta
        $novo_valor_recebido = $conta->valor_recebido + $valor;
        $novo_status = ($novo_valor_recebido >= $conta->valor_original) ? 'recebido' : 'parcial';

        $this->Contas_receber_model->update(array(
            'id'               => $conta->id,
            'valor_recebido'   => $novo_valor_recebido,
            'status'           => $novo_status,
            'data_recebimento' => ($novo_status == 'recebido') ? date('Y-m-d') : NULL,
            'forma_pagamento'  => 'pix',
        ));

        // 3. Registrar no ledger
        $this->db->insert('ledger_tbl', array(
            'transaction_id'       => $transaction_id,
            'transaction_category' => 'Conta a Receber',
            'ledger_id'            => $conta->cliente_id ?: 'receita',
            'invoice_no'           => $conta->codigo,
            'amount'               => $valor,
            'description'          => $conta->descricao . ' (PIX Efi Pay)',
            'payment_type'         => 'pix',
            'date'                 => date('Y-m-d'),
            'd_c'                  => 'd',
            'created_by'           => $conta->created_by,
            'status'               => 1,
        ));

        log_message('info', 'Efi_webhook: Pagamento confirmado txid=' . $txid . ' | e2e=' . $e2e_id . ' | valor=' . $valor . ' | conta=' . $conta->codigo);
    }
}
