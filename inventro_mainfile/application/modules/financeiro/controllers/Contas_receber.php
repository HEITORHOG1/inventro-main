<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Controller para Contas a Receber
 * Gerencia vendas a prazo, fiados e recebimentos
 */
class Contas_receber extends MX_Controller {

    public $data = [];

    public function __construct() {
        parent::__construct();
        $this->permission->module()->redirect();
        $this->load->model(array(
            'Contas_receber_model',
            'invoice/Invoice_model'
        ));
        $this->load->library('Generators');
    }

    /**
     * Lista de contas a receber
     */
    public function lista() {
        $this->permission->method('financeiro', 'read')->redirect();
        
        $data['title'] = makeString(['contas_a_receber']);
        $data['total_contas'] = $this->Contas_receber_model->count_all();
        $data['clientes'] = $this->Contas_receber_model->get_clientes();
        $data['categorias'] = $this->Contas_receber_model->get_categorias('receita');
        $data['get_appsetting'] = $this->Invoice_model->get_appsetting();
        
        $data['module'] = "financeiro";
        $data['page'] = "receber/lista";
        echo Modules::run('template/layout', $data);
    }

    /**
     * Busca dados para DataTable via AJAX
     */
    public function get_lista() {
        $postData = $this->input->post();
        $data = $this->Contas_receber_model->get_lista_datatable($postData);
        echo json_encode($data);
    }

    /**
     * Formulário de nova conta a receber
     */
    public function form($id = null) {
        $this->permission->method('financeiro', 'create')->redirect();
        
        $data['title'] = makeString(['nova_conta_receber']);
        $data['clientes'] = $this->Contas_receber_model->get_clientes();
        $data['categorias'] = $this->Contas_receber_model->get_categorias('receita');
        $data['bancos'] = $this->Contas_receber_model->get_bancos();
        $data['conta'] = null;
        
        if (!empty($id)) {
            $this->permission->method('financeiro', 'update')->redirect();
            $data['title'] = makeString(['edit']) . ' - ' . makeString(['contas_a_receber']);
            $data['conta'] = $this->Contas_receber_model->find_by_id($id);
        }
        
        $data['module'] = "financeiro";
        $data['page'] = "receber/form";
        echo Modules::run('template/layout', $data);
    }

    /**
     * Salvar conta a receber
     */
    public function salvar() {
        $this->form_validation->set_rules('descricao', makeString(['description']), 'required|max_length[255]');
        $this->form_validation->set_rules('valor_original', makeString(['valor_original']), 'required|numeric');
        $this->form_validation->set_rules('data_vencimento', makeString(['data_vencimento']), 'required');
        
        if ($this->form_validation->run()) {
            $id = $this->input->post('id', TRUE);
            $total_parcelas = (int) $this->input->post('total_parcelas', TRUE);
            $total_parcelas = $total_parcelas > 0 ? $total_parcelas : 1;
            
            $postData = array(
                'descricao' => $this->input->post('descricao', TRUE),
                'tipo' => $this->input->post('tipo', TRUE) ?: 'venda',
                'categoria_id' => $this->input->post('categoria_id', TRUE) ?: NULL,
                'cliente_id' => $this->input->post('cliente_id', TRUE) ?: NULL,
                'valor_original' => $this->input->post('valor_original', TRUE),
                'data_emissao' => $this->input->post('data_emissao', TRUE) ?: date('Y-m-d'),
                'data_vencimento' => $this->input->post('data_vencimento', TRUE),
                'total_parcelas' => $total_parcelas,
                'observacao' => $this->input->post('observacao', TRUE),
                'created_by' => $this->session->userdata('id')
            );
            
            if (empty($id)) {
                // Criar novas contas (pode ser parcelado)
                if ($total_parcelas > 1) {
                    $valor_parcela = round($postData['valor_original'] / $total_parcelas, 2);
                    $data_venc = new DateTime($postData['data_vencimento']);
                    
                    for ($i = 1; $i <= $total_parcelas; $i++) {
                        $parcela_data = $postData;
                        $parcela_data['codigo'] = $this->gerar_codigo();
                        $parcela_data['valor_original'] = $valor_parcela;
                        $parcela_data['parcela_atual'] = $i;
                        $parcela_data['data_vencimento'] = $data_venc->format('Y-m-d');
                        $parcela_data['descricao'] = $postData['descricao'] . " ({$i}/{$total_parcelas})";
                        
                        $this->Contas_receber_model->create($parcela_data);
                        $data_venc->modify('+1 month');
                    }
                } else {
                    $postData['codigo'] = $this->gerar_codigo();
                    $postData['parcela_atual'] = 1;
                    $this->Contas_receber_model->create($postData);
                }
                
                $this->session->set_flashdata('message', makeString(['conta_receber_salva']));
            } else {
                // Atualizar conta existente
                $postData['id'] = $id;
                $this->Contas_receber_model->update($postData);
                $this->session->set_flashdata('message', makeString(['update_successfully']));
            }
            
            redirect('financeiro/contas_receber/lista');
        } else {
            $this->session->set_flashdata('exception', validation_errors());
            redirect('financeiro/contas_receber/form');
        }
    }

    /**
     * Tela de baixa (recebimento)
     */
    public function baixa($id) {
        $this->permission->method('financeiro', 'update')->redirect();
        
        $conta = $this->Contas_receber_model->find_by_id($id);
        if (!$conta) {
            $this->session->set_flashdata('exception', makeString(['please_try_again']));
            redirect('financeiro/contas_receber/lista');
        }
        
        $data['title'] = makeString(['baixa_recebimento']);
        $data['conta'] = $conta;
        $data['valor_pendente'] = $conta->valor_original - $conta->valor_recebido;
        $data['bancos'] = $this->Contas_receber_model->get_bancos();
        $data['historico'] = $this->Contas_receber_model->get_historico_baixas($id, 'receber');
        $data['get_appsetting'] = $this->Invoice_model->get_appsetting();
        
        $data['module'] = "financeiro";
        $data['page'] = "receber/baixa";
        echo Modules::run('template/layout', $data);
    }

    /**
     * Registrar baixa de recebimento
     */
    public function registrar_baixa() {
        $this->form_validation->set_rules('conta_id', 'ID', 'required|integer');
        $this->form_validation->set_rules('valor', makeString(['amount']), 'required|numeric');
        $this->form_validation->set_rules('forma_pagamento', makeString(['forma_pagamento']), 'required');
        
        if ($this->form_validation->run()) {
            $conta_id = $this->input->post('conta_id', TRUE);
            $valor = floatval($this->input->post('valor', TRUE));
            
            $conta = $this->Contas_receber_model->find_by_id($conta_id);
            $valor_pendente = $conta->valor_original - $conta->valor_recebido;
            
            if ($valor > $valor_pendente) {
                $this->session->set_flashdata('exception', makeString(['valor_maior_pendente']));
                redirect('financeiro/contas_receber/baixa/' . $conta_id);
                return;
            }
            
            $transaction_id = "CR" . date('ymd') . $this->generators->generator(8);
            
            // Registrar baixa
            $baixa_data = array(
                'tipo' => 'receber',
                'conta_id' => $conta_id,
                'valor' => $valor,
                'data_baixa' => $this->input->post('data_baixa', TRUE) ?: date('Y-m-d'),
                'forma_pagamento' => $this->input->post('forma_pagamento', TRUE),
                'banco_id' => $this->input->post('banco_id', TRUE) ?: NULL,
                'observacao' => $this->input->post('observacao', TRUE),
                'transaction_id' => $transaction_id,
                'created_by' => $this->session->userdata('id')
            );
            
            $this->Contas_receber_model->registrar_baixa($baixa_data);
            
            // Atualizar conta
            $novo_valor_recebido = $conta->valor_recebido + $valor;
            $novo_status = ($novo_valor_recebido >= $conta->valor_original) ? 'recebido' : 'parcial';
            
            $this->Contas_receber_model->update(array(
                'id' => $conta_id,
                'valor_recebido' => $novo_valor_recebido,
                'status' => $novo_status,
                'data_recebimento' => ($novo_status == 'recebido') ? date('Y-m-d') : NULL,
                'forma_pagamento' => $this->input->post('forma_pagamento', TRUE),
                'banco_id' => $this->input->post('banco_id', TRUE)
            ));
            
            // Registrar na ledger_tbl para manter compatibilidade
            $ledger_data = array(
                'transaction_id' => $transaction_id,
                'transaction_category' => 'Conta a Receber',
                'ledger_id' => $conta->cliente_id ?: 'receita',
                'invoice_no' => $conta->codigo,
                'amount' => $valor,
                'description' => $conta->descricao,
                'payment_type' => $this->input->post('forma_pagamento', TRUE),
                'date' => $this->input->post('data_baixa', TRUE) ?: date('Y-m-d'),
                'd_c' => 'd', // Débito (entrada de dinheiro)
                'source_bank' => $this->input->post('banco_id', TRUE),
                'created_by' => $this->session->userdata('id'),
                'status' => 1
            );
            $this->db->insert('ledger_tbl', $ledger_data);
            
            $this->session->set_flashdata('message', makeString(['baixa_registrada']));
            redirect('financeiro/contas_receber/lista');
        } else {
            $conta_id = $this->input->post('conta_id', TRUE);
            $this->session->set_flashdata('exception', validation_errors());
            redirect('financeiro/contas_receber/baixa/' . $conta_id);
        }
    }

    /**
     * Excluir conta a receber
     */
    public function delete($id) {
        $this->permission->method('financeiro', 'delete')->redirect();
        
        if ($this->Contas_receber_model->delete($id)) {
            $this->session->set_flashdata('message', makeString(['delete_successfully']));
        } else {
            $this->session->set_flashdata('exception', makeString(['please_try_again']));
        }
        
        redirect('financeiro/contas_receber/lista');
    }

    /**
     * Cancelar conta
     */
    public function cancelar($id) {
        $this->permission->method('financeiro', 'update')->redirect();
        
        $this->Contas_receber_model->update(array(
            'id' => $id,
            'status' => 'cancelado'
        ));
        
        $this->session->set_flashdata('message', makeString(['conta_cancelada']));
        redirect('financeiro/contas_receber/lista');
    }

    /**
     * Gerar código único
     */
    private function gerar_codigo() {
        return 'CR' . date('ymd') . strtoupper($this->generators->generator(5));
    }
}
