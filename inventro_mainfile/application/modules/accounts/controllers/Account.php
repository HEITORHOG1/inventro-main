<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Account extends MX_Controller {

    public $data = [];
    public $user_id = '';

    public function __construct() {
        parent::__construct();
        $this->permission->module()->redirect();
        $this->load->model(array(
            'Account_model','invoice/Invoice_model'
        ));
        $this->load->library('Generators');
        $this->baseurl = $this->config->item('base_url');
        $this->user_id = $this->session->userdata('id');
    }

    public function payment_receive_form() {
        $this->permission->method('accounts', 'create')->redirect();
        $data['title'] = makeString(['payment_or_receive']);

        $data['get_banks'] = $this->Account_model->get_banks();

        $data['module'] = "accounts";
        $data['page'] = "payment_receive_form";
        echo Modules::run('template/layout', $data);
    }

    //    =========== its for get_customers ============
    public function get_customers() {
        $get_customers = $this->Account_model->get_customers();
        echo "<option value=''>-- select one --</option>";
        foreach ($get_customers as $value) {
            echo "<option value='$value->customerid'>$value->name</option>";
        }
    }

    //    =========== its for get_allsuppliers ============
    public function get_allsuppliers() {
        $get_allsuppliers = $this->Account_model->get_suppliers();
        echo "<option value=''>-- select one --</option>";
        foreach ($get_allsuppliers as $value) {
            echo "<option value='$value->supplier_id'>$value->name</option>";
        }
    }

//    =========== its for transaction save ============
    public function transaction_save() {
        $data['baseurl'] = $this->baseurl;
        $transaction_id = "T" . date('d') . $this->generators->generator(15);
        $invoice_no = "INV" . date('d') . $this->generators->generator(10);
        $receipt_no = "R" . date('d') . $this->generators->generator(11);
        $deposit_no = "D" . date('d') . $this->generators->generator(12);
        $capitalbalance = 'capitalbalance';
        $transection_type = $this->input->post('transection_type',TRUE);
        $transaction_category = $this->input->post('transactioncategory',TRUE);
        $date = $this->input->post('date',TRUE);
        $description = $this->input->post('description',TRUE);

        $relation_id = $this->input->post('relation_id',TRUE);


        if ($relation_id == 'capitalbalance') {
            $is_capital = 1;
        } else {
            $is_capital = NULL;
        }

        $payment_type = $this->input->post('payment_type',TRUE);
        $amount = $this->input->post('amount');
        $cheque_bank_name = $this->input->post('cheque_bank_name',TRUE);

//        =========== its for when transaction type payment ===============
        if ($transection_type == 1) {
            $ledger_data_debit = array(
                'transaction_id' => $transaction_id,
                'transaction_type ' => $transection_type,
                'transaction_category' => $transaction_category,
                'ledger_id' => $relation_id,
                'source_bank' => $cheque_bank_name,
                'invoice_no' => Null,
                'receipt_no' => $receipt_no,
                'amount' => $amount,
                'description' => $description,
                'payment_type' => $payment_type,
                'date' => $date,
                'is_capital' => $is_capital,
                'is_transaction' => 1,
                'd_c' => 'd',
                'created_by' => $this->user_id,
            );
            $this->db->insert('ledger_tbl', $ledger_data_debit);

            if ($payment_type == 1) {
                $bank_ledger_credit = array(
                    'transaction_id' => $transaction_id,
                    'transaction_type ' => $transection_type,
                    'transaction_category' => $transaction_category,
                    'ledger_id' => 1,
                    'source_bank' => $relation_id,
                    'cheque_bank_name' => $cheque_bank_name,
                    'invoice_no' => Null,
                    'receipt_no' => $receipt_no,
                    'amount' => $amount,
                    'description' => $description,
                    'payment_type' => $payment_type,
                    'date' => $date,
                    'is_capital' => $is_capital,
                    'd_c' => 'c',
                    'created_by' => $this->user_id,
                );

                $this->db->insert('ledger_tbl', $bank_ledger_credit);
            }
            if ($payment_type == 2) {
                $bank_ledger_credit = array(
                    'transaction_id' => $transaction_id,
                    'transaction_type ' => $transection_type,
                    'transaction_category' => $transaction_category,
                    'ledger_id' => $cheque_bank_name,
                    'source_bank' => $relation_id,
                    'cheque_bank_name' => $cheque_bank_name,
                    'invoice_no' => Null,
                    'receipt_no' => $receipt_no,
                    'amount' => $amount,
                    'description' => $description,
                    'payment_type' => $payment_type,
                    'date' => $date,
                    'is_capital' => $is_capital,
                    'd_c' => 'c',
                    'created_by' => $this->user_id,
                );

                $this->db->insert('ledger_tbl', $bank_ledger_credit);
            }
        }
//        =========== its for when transaction type receipt ===============
        if ($transection_type == 2) {
            $ledger_data_credit = array(
                'transaction_id' => $transaction_id,
                'transaction_type ' => $transection_type,
                'transaction_category' => $transaction_category,
                'ledger_id' => $relation_id,
                'cheque_bank_name' => $cheque_bank_name,
                'invoice_no' => Null,
                'receipt_no' => $receipt_no,
                'amount' => $amount,
                'description' => $description,
                'payment_type' => $payment_type,
                'date' => $date,
                'is_capital' => $is_capital,
                'is_transaction' => 1,
                'd_c' => 'c',
                'created_by' => $this->user_id,
            );

            $this->db->insert('ledger_tbl', $ledger_data_credit);

            if ($payment_type == 1) {
                $bank_ledger_debit = array(
                    'transaction_id' => $transaction_id,
                    'transaction_type ' => $transection_type,
                    'transaction_category' => $transaction_category,
                    'ledger_id' => 1,
                    'source_bank' => $relation_id,
                    'invoice_no' => Null,
                    'receipt_no' => $receipt_no,
                    'amount' => $amount,
                    'description' => $description,
                    'payment_type' => $payment_type,
                    'date' => $date,
                    'is_capital' => $is_capital,
                    'd_c' => 'd',
                    'created_by' => $this->user_id,
                );

                $this->db->insert('ledger_tbl', $bank_ledger_debit);
            }
            if ($payment_type == 2) {
                $bank_ledger_debit = array(
                    'transaction_id' => $transaction_id,
                    'transaction_type ' => $transection_type,
                    'transaction_category' => $transaction_category,
                    'ledger_id' => $cheque_bank_name,
                    'cheque_bank_name' => $cheque_bank_name,
                    'source_bank' => $relation_id,
                    'invoice_no' => Null,
                    'receipt_no' => $receipt_no,
                    'amount' => $amount,
                    'description' => $description,
                    'payment_type' => $payment_type,
                    'date' => $date,
                    'is_capital' => $is_capital,
                    'd_c' => 'd',
                    'created_by' => $this->user_id,
                );

                $this->db->insert('ledger_tbl', $bank_ledger_debit);
            }
        }

        $this->session->set_flashdata('success', "<div class='alert alert-success'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button>Transaction save successfully!</div>");
        redirect($_SERVER['HTTP_REFERER']);
    }

    //    =================== its for manage transaction ===============
    public function manage_transaction() {
        $this->permission->method('accounts', 'read')->redirect();
        $data['baseurl'] = $this->baseurl;
        $data['title'] = makeString(['manage_transaction']);
        $data['get_appsetting'] = $this->Invoice_model->get_appsetting();
        $data['get_transaction_info'] = $this->Account_model->get_transaction_info();

        $data['module'] = "accounts";
        $data['page'] = "manage_transaction";
        echo Modules::run("template/layout", $data);
    }

//    =================== its for transaction_edit ===============
    public function transaction_edit($transaction_id) {
        $this->permission->method('accounts', 'edit')->redirect();
        $data['title'] = makeString(['transaction_edit']);
        $data['baseurl'] = $this->baseurl;

        $data['transaction_edit'] = $this->Account_model->transaction_edit($transaction_id);
        $data['get_customer'] = $this->Account_model->get_customers();
        $data['get_supplier'] = $this->Account_model->get_suppliers();
        $data['get_bank'] = $this->Account_model->get_banks();

        $data['module'] = "accounts";
        $data['page'] = "transaction_edit";
        echo Modules::run("template/layout", $data);
    }

//    ============its for transaction update ===========
    public function transaction_update() {
        $this->permission->method('accounts', 'edit')->redirect();
        $data['baseurl'] = $this->baseurl;
        $trans_id = $this->input->post('transaction_id',TRUE);

        $transaction_id = "T" . date('d') . $this->generators->generator(15);
        $invoice_no = "INV" . date('d') . $this->generators->generator(10);
        $receipt_no = "R" . date('d') . $this->generators->generator(11);
        $deposit_no = "D" . date('d') . $this->generators->generator(12);

        $transection_type = $this->input->post('transection_type',TRUE);
        $transaction_category = $this->input->post('transactioncategory',TRUE);

        $date = $this->input->post('date',TRUE);
        $description = $this->input->post('description',TRUE);
        $relation_id = $this->input->post('relation_id',TRUE);
        $payment_type = $this->input->post('payment_type',TRUE);
        $amount = $this->input->post('amount',TRUE);

        $cheque_bank_name = $this->input->post('cheque_bank_name',TRUE);

        $this->db->where('transaction_id', $trans_id)->delete('ledger_tbl');


//        =========== its for when transaction type payment ===============
        if ($transection_type == 1) {
            $ledger_data_debit = array(
                'transaction_id' => $transaction_id,
                'transaction_type ' => $transection_type,
                'transaction_category' => $transaction_category,
                'ledger_id' => $relation_id,
                'source_bank' => $cheque_bank_name,
                'invoice_no' => Null,
                'receipt_no' => $receipt_no,
                'amount' => $amount,
                'description' => $description,
                'payment_type' => $payment_type,
                'date' => $date,
                'is_capital' => $is_capital,
                'is_transaction' => 1,
                'd_c' => 'd',
                'created_by' => $this->user_id,
            );

            $this->db->insert('ledger_tbl', $ledger_data_debit);

            if ($payment_type == 1) {
                $bank_ledger_credit = array(
                    'transaction_id' => $transaction_id,
                    'transaction_type ' => $transection_type,
                    'transaction_category' => $transaction_category,
                    'ledger_id' => 1,
                    'source_bank' => $relation_id,
                    'cheque_bank_name' => $cheque_bank_name,
                    'invoice_no' => Null,
                    'receipt_no' => $receipt_no,
                    'amount' => $amount,
                    'description' => $description,
                    'payment_type' => $payment_type,
                    'date' => $date,
                    'is_capital' => $is_capital,
                    'd_c' => 'c',
                    'created_by' => $this->user_id,
                );

                $this->db->insert('ledger_tbl', $bank_ledger_credit);
            }
            if ($payment_type == 2) {
                $bank_ledger_credit = array(
                    'transaction_id' => $transaction_id,
                    'transaction_type ' => $transection_type,
                    'transaction_category' => $transaction_category,
                    'ledger_id' => $cheque_bank_name,
                    'source_bank' => $relation_id,
                    'cheque_bank_name' => $cheque_bank_name,
                    'invoice_no' => Null,
                    'receipt_no' => $receipt_no,
                    'amount' => $amount,
                    'description' => $description,
                    'payment_type' => $payment_type,
                    'date' => $date,
                    'is_capital' => $is_capital,
                    'd_c' => 'c',
                    'created_by' => $this->user_id,
                );

                $this->db->insert('ledger_tbl', $bank_ledger_credit);
            }
        }
//        =========== its for when transaction type receipt ===============
        if ($transection_type == 2) {
            $ledger_data_credit = array(
                'transaction_id' => $transaction_id,
                'transaction_type ' => $transection_type,
                'transaction_category' => $transaction_category,
                'ledger_id' => $relation_id,
                'cheque_bank_name' => $cheque_bank_name,
                'invoice_no' => Null,
                'receipt_no' => $receipt_no,
                'amount' => $amount,
                'description' => $description,
                'payment_type' => $payment_type,
                'date' => $date,
                'is_capital' => $is_capital,
                'is_transaction' => 1,
                'd_c' => 'c',
                'created_by' => $this->user_id,
            );

            $this->db->insert('ledger_tbl', $ledger_data_credit);

            if ($payment_type == 1) {
                $bank_ledger_debit = array(
                    'transaction_id' => $transaction_id,
                    'transaction_type ' => $transection_type,
                    'transaction_category' => $transaction_category,
                    'ledger_id' => 1,
                    'source_bank' => $relation_id,
                    'invoice_no' => Null,
                    'receipt_no' => $receipt_no,
                    'amount' => $amount,
                    'description' => $description,
                    'payment_type' => $payment_type,
                    'date' => $date,
                    'is_capital' => $is_capital,
                    'd_c' => 'd',
                    'created_by' => $this->user_id,
                );

                $this->db->insert('ledger_tbl', $bank_ledger_debit);
            }
            if ($payment_type == 2) {
                $bank_ledger_debit = array(
                    'transaction_id' => $transaction_id,
                    'transaction_type ' => $transection_type,
                    'transaction_category' => $transaction_category,
                    'ledger_id' => $cheque_bank_name,
                    'cheque_bank_name' => $cheque_bank_name,
                    'source_bank' => $relation_id,
                    'invoice_no' => Null,
                    'receipt_no' => $receipt_no,
                    'amount' => $amount,
                    'description' => $description,
                    'payment_type' => $payment_type,
                    'date' => $date,
                    'is_capital' => $is_capital,
                    'd_c' => 'd',
                    'created_by' => $this->user_id,
                );

                $this->db->insert('ledger_tbl', $bank_ledger_debit);
            }
        }

        $this->session->set_flashdata('success', "<div class='alert alert-success'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button>Transaction updated successfully!</div>");
        redirect('accounts/Account/manage_transaction');
    }

//=========== its for transaction_delete ============
    public function transaction_delete($transaction_id) {
        $this->permission->method('accounts', 'delete')->redirect();
        $this->db->where('transaction_id', $transaction_id)->delete('ledger_tbl');
        $this->session->set_flashdata('success', "<div class='alert alert-success'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button>Transaction deleted successfully!</div>");
        redirect('accounts/Account/manage_transaction');
    }

//    ============ its for account adjustment ===========
    public function account_adjustment() {
        $this->permission->method('accounts', 'create')->redirect();
        $data['title'] = makeString(['account_adjustment']);

        $data['module'] = "accounts";
        $data['page'] = "account_adjustment";
        echo Modules::run('template/layout', $data);
    }

//    ============ its for account_adjustment_save ===========
    public function account_adjustment_save() {
        $transaction_id = "T" . date('d') . $this->generators->generator(13);
        $payment_date = $this->input->post('payment_date');
        $payment_type = $this->input->post('payment_type');
        $amount = $this->input->post('amount');
        $details = $this->input->post('details');

        $account_adjustment = array(
            'transaction_id' => $transaction_id,
            'transaction_category' => '', // transaction_category,
            'ledger_id' => 1,  // relation_id,
            'source_bank' => '', // cheque_bank_name,
            'invoice_no' => Null,
            'receipt_no' => '', // receipt_no,
            'amount' => $amount,
            'description' => $details, // description,
            'payment_type' => '', // payment_type,
            'date' => $payment_date,
            'd_c' => $payment_type,
            'created_by' => $this->user_id,
        );

        $this->db->insert('ledger_tbl', $account_adjustment);

        $this->session->set_flashdata('success', "<div class='alert alert-success'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button>Adjustment successfully done!</div>");
        redirect('accounts/Account/account_adjustment');
    }

//    ============ its for receiptttt =============
    public function receiptttt() {
        $data['title'] = makeString(['account_adjustment']);

        $data['module'] = "accounts";
        $data['page'] = "test";
        echo Modules::run('template/layout', $data);
    }


     // Closing form
       public function closing_form() {
        $this->permission->method('accounts', 'create')->redirect();
        $data['title']     = makeString(['cash_closing']);
        $data['cash_data'] = $this->Account_model->cash_info();
        $data['module']    = "accounts";
        $data['page']      = "closing_form";
        echo Modules::run("template/layout", $data);
    }

    public function save_closing(){
        $date = date('Y-m-d');
        $checkdate = $this->Account_model->check_closing_date($date);
        if($checkdate > 0){
            $this->session->set_flashdata('exception', makeString(['cash_already_closed_for_this_day']));
      redirect('accounts/Account/closing_form');
 }

     $data = array(
        'last_day_closing' => $this->input->post('last_closing_balance',TRUE),
        'cash_in'          => $this->input->post('receipt',TRUE), 
        'cash_out'         => $this->input->post('payment',TRUE),
        'date'             => $date,
        'amount'           => $this->input->post('balance',TRUE),
        'adjustment'       => $this->input->post('adjustment',TRUE),
        'status'           => 1

        );
     $this->db->insert('daily_closing',$data);

     $this->session->set_flashdata('message', makeString(['save_successfully']));
      redirect('accounts/Account/closing_form');
     
    }

       public function closing_list() {
        $this->permission->method('accounts', 'read')->redirect();
        $data['title']     = makeString(['closing_list']);
        $data['get_appsetting'] = $this->Invoice_model->get_appsetting();
        $data['cash_data'] = $this->Account_model->closing_list();
        $data['module']    = "accounts";
        $data['page']      = "closing_list";
        echo Modules::run("template/layout", $data);
    }

    public function cash_closing_delete($id){
      if ($this->Account_model->delete($id)) {
      #set success message
      $this->session->set_flashdata('message',makeString(['delete_successfully']));
    } else {
      #set exception message
      $this->session->set_flashdata('exception',makeString(['please_try_again']));
    }
   redirect('accounts/Account/closing_list');
    }
}
