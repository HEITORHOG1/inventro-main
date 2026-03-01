<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Returns extends MX_Controller {

public function __construct()
	{
		parent::__construct();
		$this->permission->module('return')->redirect();
		$this->load->model(array(
			'Return_model',
		));
     $this->load->library('Generators');
	}

public function customer_return(){
        $this->permission->method('return', 'read')->redirect();
        $data['title']    = makeString(['customer_return']); 
        $invoice_id = $this->input->post('invoiceid');
        $data['invoiceid']= $invoice_id;
        $data['get_customer'] = $this->Return_model->get_customer();
        $data['bank_list'] = $this->Return_model->get_banks();
        $data['get_products'] = $this->Return_model->get_products();
        $data['edit_invoice'] = $this->Return_model->edit_invoice($invoice_id);
        $data['edit_invoicedetails'] = $this->Return_model->edit_invoicedetails($invoice_id);
		$data['module']   = "return";
		$data['page']     = "customer_return";   
		echo Modules::run('template/layout', $data); 
	} 

  public function supplier_return(){
        $this->permission->method('return', 'read')->redirect();
         $purchase_id = $this->input->post('purchase_id',TRUE);
        //  dd( $purchase_id);
         $data['title']         = makeString(['purchase_edit']);
         if($purchase_id){
         $purchase_detail = $this->Return_model->retrieve_purchase_editdata($purchase_id);
         $bank_list = $this->Return_model->bank_list();
        if($purchase_detail){
        $supplier_list = $this->Return_model->supplier_list();
        $data = array(
            'title'         => makeString(['purchase_edit']),
            'purchase_id'   => $purchase_id,
            'chalan_no'     => $purchase_detail[0]['chalan_no'],
            'supplier_name' => $purchase_detail[0]['supplier_name'],
            'supplier_id'   => $purchase_detail[0]['supplier_id'],
            'grand_total'   => $purchase_detail[0]['grand_total_amount'],
            'purchase_details' => $purchase_detail[0]['purchase_details'],
            'purchase_date' => $purchase_detail[0]['purchase_date'],
            'discount'      => $purchase_detail[0]['discount'],
            'bank_id'       =>  $purchase_detail[0]['bank_id'],
            'purchase_info' => $purchase_detail,
            'supplier_list' => $supplier_list,
            'bank_list'     => $bank_list,
            'paytype'       => $purchase_detail[0]['payment_type'],
        );
        }
     }
   $data['module'] = "return";
   $data['page']   = "supplier_return"; 
   echo Modules::run('template/layout', $data);
  } 

 //customer return list
 public function customer_return_list(){
   $this->permission->method('return', 'read')->redirect();
   $data['title'] = makeString(['customer_return_list']);
        $data['get_appsetting'] = $this->Return_model->get_appsetting();
        $config["base_url"] = base_url('sales/returns/customer_return_list');
        $config["total_rows"] = $this->Return_model->sales_return_count();
        $config["per_page"] = 10;
        $config["uri_segment"] = 4;
        $config["last_link"] = "Last";
        $config["first_link"] = "First";
        $config['next_link'] = 'Next';
        $config['prev_link'] = 'Prev';
        $config['full_tag_open'] = '<div class="pagging text-center"><nav><ul class="pagination">';
        $config['full_tag_close'] = '</ul></nav></div>';
        $config['num_tag_open'] = '<li class="page-item"><span class="page-link">';
        $config['num_tag_close'] = '</span></li>';
        $config['cur_tag_open'] = '<li class="page-item active"><span class="page-link">';
        $config['cur_tag_close'] = '<span class="sr-only">(current)</span></span></li>';
        $config['next_tag_open'] = '<li class="page-item"><span class="page-link">';
        $config['next_tagl_close'] = '<span aria-hidden="true">&raquo;</span></span></li>';
        $config['prev_tag_open'] = '<li class="page-item"><span class="page-link">';
        $config['prev_tagl_close'] = '</span></li>';
        $config['first_tag_open'] = '<li class="page-item"><span class="page-link">';
        $config['first_tagl_close'] = '</span></li>';
        $config['last_tag_open'] = '<li class="page-item"><span class="page-link">';
        $config['last_tagl_close'] = '</span></li>';
        /* ends of bootstrap */
        $this->pagination->initialize($config);
        $page = ($this->uri->segment(4)) ? $this->uri->segment(4) : 0;
        $data["return_list"] = $this->Return_model->sales_return_list($config["per_page"], $page);
        $data["links"] = $this->pagination->create_links();
        $data['pagenum'] = $page;
        $data['module'] = "return";
        $data['page'] = "customer_return_list";
        echo Modules::run('template/layout', $data);
 } 

 //supplier return
public function supplier_return_list(){
   $this->permission->method('return', 'read')->redirect();
   $data['title'] = makeString(['supplier_return_list']);
        $data['get_appsetting'] = $this->Return_model->get_appsetting();
        $config["base_url"] = base_url('sales/returns/supplier_return_list');
        $config["total_rows"] = $this->Return_model->supplier_return_count();
        $config["per_page"] = 10;
        $config["uri_segment"] = 4;
        $config["last_link"] = "Last";
        $config["first_link"] = "First";
        $config['next_link'] = 'Next';
        $config['prev_link'] = 'Prev';
        $config['full_tag_open'] = '<div class="pagging text-center"><nav><ul class="pagination">';
        $config['full_tag_close'] = '</ul></nav></div>';
        $config['num_tag_open'] = '<li class="page-item"><span class="page-link">';
        $config['num_tag_close'] = '</span></li>';
        $config['cur_tag_open'] = '<li class="page-item active"><span class="page-link">';
        $config['cur_tag_close'] = '<span class="sr-only">(current)</span></span></li>';
        $config['next_tag_open'] = '<li class="page-item"><span class="page-link">';
        $config['next_tagl_close'] = '<span aria-hidden="true">&raquo;</span></span></li>';
        $config['prev_tag_open'] = '<li class="page-item"><span class="page-link">';
        $config['prev_tagl_close'] = '</span></li>';
        $config['first_tag_open'] = '<li class="page-item"><span class="page-link">';
        $config['first_tagl_close'] = '</span></li>';
        $config['last_tag_open'] = '<li class="page-item"><span class="page-link">';
        $config['last_tagl_close'] = '</span></li>';
        /* ends of bootstrap */
        $this->pagination->initialize($config);
        $page = ($this->uri->segment(4)) ? $this->uri->segment(4) : 0;
        $data["return_list"] = $this->Return_model->supplier_return_list($config["per_page"], $page);
        $data["links"] = $this->pagination->create_links();
        $data['pagenum'] = $page;
        $data['module'] = "return";
        $data['page'] = "supplier_return_list";
        echo Modules::run('template/layout', $data);
 } 

//Save customer return
      public function save_customer_return() {
        $this->permission->method('return', 'create')->redirect();
        $transaction_id = "RT" . date('d') . $this->generators->generator(8);
        $invoice_id = $this->input->post('invoice_id',TRUE);
        $receipt_no = "R" . date('d') . $this->generators->generator(10);
        $customer_id = $this->input->post('customer_id',TRUE);
        $date = $this->input->post('date',TRUE);
        $details = $this->input->post('details',TRUE);
        $product_id = $this->input->post('product_id',TRUE);
        $product_quantity = $this->input->post('product_quantity',TRUE);
        $sold_qty  = $this->input->post('soldqty',TRUE);
        $product_rate = $this->input->post('product_rate',TRUE);
        $product_discount = $this->input->post('product_discount',TRUE);
        $discount_amount = $this->input->post('discount_amount',TRUE);
        $total_price = $this->input->post('total_price',TRUE);
        $deduction = $this->input->post('invoice_discount',TRUE);
        $invoice_discount = $this->input->post('inv_discount',TRUE);
        $grand_total_price = $this->input->post('grand_total_price',TRUE);
        $paytype = $this->input->post('paytype',TRUE);
        $bank_id = $this->input->post('bank_id',TRUE);
        if ($details) {
            $details = $details;
        } else {
            $details = 'Customer Returns';
        }
        //Insert to customer_ledger Table 
        $customer_ledger_debit = array(
            'transaction_id' => $transaction_id,
            'invoice_no'     => $transaction_id,
            'ledger_id'      => $customer_id,
            'receipt_no'     => $receipt_no,
            'date'           => $date,
            'amount'         => $grand_total_price,
            'payment_type'   => $paytype,
            'description'    => $details,
            'd_c'            => 'd',
            'created_by'     => $this->session->userdata('id'),
        );
        $this->db->insert('ledger_tbl', $customer_ledger_debit);


        if ($paytype == 2) {
            $bank_ledger_credit = array(
                'transaction_id' => $transaction_id,
                'invoice_no'     => null,
                'ledger_id'      => $bank_id,
                'receipt_no'     => $receipt_no,
                'date'           => $date,
                'amount'         => $grand_total_price,
                'payment_type'   => $paytype,
                'description'    => $details,
                'd_c'            => 'c',
                'created_by'     => $this->session->userdata('id'),
            );
            $this->db->insert('ledger_tbl', $bank_ledger_credit);
        }
        if ($paytype == 1) {
            $cash_ledger = array(
                'transaction_id' => $transaction_id,
                'invoice_no'     => null,
                'ledger_id'      => 1,
                'receipt_no'     => $receipt_no,
                'date'           => $date,
                'amount'         => $grand_total_price,
                'payment_type'   => $paytype,
                'description'    => $details,
                'd_c'            => 'c',
                'created_by'     => $this->session->userdata('id'),
            );
            $this->db->insert('ledger_tbl', $cash_ledger);
        }
        //Data inserting into invoice table
        $return_data = array(
            'return_id'    => $transaction_id,
            'invoice_id'   => $invoice_id,
            'customer_id'  => $customer_id,
            'return_date'  => $date,
            'deduction'    => $deduction,
            'invoice_discount' => $invoice_discount,
            'total_amount' => $grand_total_price,
            'status'       => 1,
            'paymet_type'  => $paytype,
            'bank_id'      => $bank_id,
            'reason'       => $details,
            'created_by'   => $this->session->userdata('id'),
            'created_at'   => date('Y-m-d H:i:s'),
        );
        $this->db->insert('product_return', $return_data);

        //============= its for return details entry ============
        for ($i = 0; $i < count($product_id); $i++) {

         
            $return_details_data = array(
                'return_id'   => $transaction_id,
                'product_id'  => $product_id[$i],
                'sold_pur_qty'=> $sold_qty[$i],
                'return_qty' => $product_quantity[$i],
                'price'      => $product_rate[$i],
                'amount'     => $total_price[$i],
                'status'     => 1
            );
            if (!empty($product_quantity[$i])) {
                $this->db->insert('return_details', $return_details_data);
            }
        }

       $this->session->set_flashdata('message', makeString(['save_successfully']));
     redirect('return/returns/customer_return_list');
    }


//Save supplier return
      public function save_supplier_return() {
        $this->permission->method('return', 'create')->redirect();
        $transaction_id = "RT" . date('d') . $this->generators->generator(8);
        $purchase_id = $this->input->post('purchase_id',TRUE);
        $receipt_no = "R" . date('d') . $this->generators->generator(10);
        $supplier_id = $this->input->post('supplier_id',TRUE);
        $date = $this->input->post('date',TRUE);
        $details = $this->input->post('reason',TRUE);
        $product_id = $this->input->post('product_id',TRUE);
        $product_quantity = $this->input->post('product_quantity',TRUE);
        $purchase_qty  = $this->input->post('purchase_qty',TRUE);
        $product_rate = $this->input->post('product_rate',TRUE);
        $product_discount = $this->input->post('product_discount',TRUE);
        $purchase_dicount = $this->input->post('purchase_discount',TRUE);
        $total_price = $this->input->post('total_price',TRUE);
        $deduction = $this->input->post('deduction',TRUE);
        $grand_total_price = $this->input->post('grand_total_price',TRUE);
        $paytype = $this->input->post('paytype',TRUE);
        $bank_id = $this->input->post('bank_id',TRUE);
        if ($details) {
            $details = $details;
        } else {
            $details = 'Supplier Returns';
        }
        //Insert to customer_ledger Table 
        $supplier_ledger_debit = array(
            'transaction_id' => $transaction_id,
            'invoice_no'     => $transaction_id,
            'ledger_id'      => $supplier_id,
            'receipt_no'     => $receipt_no,
            'date'           => $date,
            'amount'         => $grand_total_price,
            'payment_type'   => $paytype,
            'description'    => $details,
            'd_c'            => 'c',
            'created_by'     => $this->session->userdata('id'),
        );
        $this->db->insert('ledger_tbl', $supplier_ledger_debit);


        if ($paytype == 2) {
            $bank_ledger_credit = array(
                'transaction_id' => $transaction_id,
                'invoice_no'     => null,
                'ledger_id'      => $bank_id,
                'receipt_no'     => $receipt_no,
                'date'           => $date,
                'amount'         => $grand_total_price,
                'payment_type'   => $paytype,
                'description'    => $details,
                'd_c'            => 'd',
                'created_by'     => $this->session->userdata('id'),
            );
            $this->db->insert('ledger_tbl', $bank_ledger_credit);
        }
        if ($paytype == 1) {
            $cash_ledger = array(
                'transaction_id' => $transaction_id,
                'invoice_no'     => null,
                'ledger_id'      => 1,
                'receipt_no'     => $receipt_no,
                'date'           => $date,
                'amount'         => $grand_total_price,
                'payment_type'   => $paytype,
                'description'    => $details,
                'd_c'            => 'd',
                'created_by'     => $this->session->userdata('id'),
            );
            $this->db->insert('ledger_tbl', $cash_ledger);
        }
        //Data inserting into invoice table
        $return_data = array(
            'return_id'    => $transaction_id,
            'invoice_id'   => null,
            'purchase_id'  => $purchase_id,
            'supplier_id'  => $supplier_id,
            'return_date'  => $date,
            'deduction'    => $deduction,
            'invoice_discount' => $purchase_dicount,
            'total_amount' => $grand_total_price,
            'status'       => 2,
            'paymet_type'  => $paytype,
            'bank_id'      => $bank_id,
            'reason'       => $details,
            'created_by'   => $this->session->userdata('id'),
            'created_at'   => date('Y-m-d H:i:s'),
        );
        $this->db->insert('product_return', $return_data);

        //============= its for return details entry ============
        for ($i = 0; $i < count($product_id); $i++) {

          
            $return_details_data = array(
                'return_id'   => $transaction_id,
                'product_id'  => $product_id[$i],
                'sold_pur_qty'=> $purchase_qty[$i],
                'return_qty' => $product_quantity[$i],
                'price'      => $product_rate[$i],
                'amount'     => $total_price[$i],
                'status'     => 2
            );
            if (!empty($product_quantity[$i])) {
                $this->db->insert('return_details', $return_details_data);
            }
        }

       $this->session->set_flashdata('message', makeString(['save_successfully']));
     redirect('return/returns/supplier_return_list');
    }


 public function delete($id = null){
    $this->permission->method('return', 'delete')->redirect();
    if ($this->Return_model->delete($id)) {
      #set success message
      $this->session->set_flashdata('message',makeString(['delete_successfully']));
    } else {
      #set exception message
      $this->session->set_flashdata('exception',makeString(['please_try_again']));
    }
    redirect("return/returns/customer_return_list");
  }

// supplier return details
 public function supplier_return_details($return_id) {
        $this->permission->method('return', 'read')->redirect();
        $data['title'] = makeString(['supplier_return_details']);        
        $data['get_appsetting'] = $this->Return_model->get_appsetting();
        $data['return_info'] = $this->Return_model->get_supplier_return_info($return_id);
        $data['return_details'] = $this->Return_model->get_return_details($return_id);
         $data['module'] = "return";
         $data['page']   = "supplier_return_details"; 
         echo Modules::run('template/layout', $data); 
    }
//customer return details
    public function customer_return_details($return_id) {
        $this->permission->method('return', 'read')->redirect();
        $data['title'] = makeString(['customer_return_details']);        
        $data['get_appsetting'] = $this->Return_model->get_appsetting();
        $data['return_info'] = $this->Return_model->get_customer_return_info($return_id);
        $data['return_details'] = $this->Return_model->get_return_details($return_id);
        $data['module'] = "return";
        $data['page'] = "customer_return_details";
        echo Modules::run('template/layout', $data);
    }
}
