<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Invoice extends MX_Controller {

    public $data = [];
    public $user_id = '';

    public function __construct() {
        parent::__construct();
        $this->permission->module()->redirect();
        $this->load->model(array(
            'Invoice_model', 'accounts/Account_model'
        ));
        $this->load->library('Generators');
        $this->user_id = $this->session->userdata('id');
    }

    public function index() {
        $this->permission->method('invoice', 'read')->redirect();
        $data['get_customer'] = $this->Invoice_model->get_customer();
        $data['get_products'] = $this->Invoice_model->get_products();
        $data['bank_list'] = $this->Account_model->get_banks();


        $data['title'] = makeString(['add_invoice']);
        $data['module'] = "invoice";
        $data['page'] = "add_invoice";
        echo Modules::run('template/layout', $data);
    }

          // Pos sale from
        public function add_pos() {
        $this->permission->method('invoice', 'create')->redirect();
        $data['get_customer'] = $this->Invoice_model->get_customer();
        $data['get_products'] = $this->Invoice_model->products_list();
        $data['bank_list'] = $this->Account_model->get_banks();
        $data['category_list'] = $this->Invoice_model->get_categories();
        $data['get_appsetting'] = $this->Invoice_model->get_appsetting();
        $data['title'] = makeString(['add_invoice']);
        $data['module'] = "invoice";
        $data['page'] = "pos_invoice";
        echo Modules::run('template/layout', $data);
    }


// ============= Item search Result =====================
    public function getsearchitem(){
      
                 $catid=$this->input->post('category_id',TRUE);
                 $getproduct = $this->Invoice_model->searchprod($catid);
                 $data['get_appsetting'] = $this->Invoice_model->get_appsetting();
                 $data['get_products']=$getproduct;
                  if(!empty($getproduct)){
                 $this->load->view('invoice/getproductlist', $data);
                 }else{
                    $this->load->view('invoice/404', $data);
                 }   
               
        }

 // ===========  Item Search By Name Model code =============
        public function searchitem_byname(){
                 $item=$this->input->post('item',TRUE);
                 $data['get_appsetting'] = $this->Invoice_model->get_appsetting();
                 $getproduct = $this->Invoice_model->searchproductbyname($item);
                 $data['get_products']=$getproduct;
                 if(!empty($getproduct)){
                 $this->load->view('invoice/getproductlist', $data);
                 }else{
                    $this->load->view('invoice/404', $data);
                 }  
        }

        // ================  pos invoice row data =========
         public function pos_product_data() {
        $product_id = $this->input->post('product_id',TRUE);
        $product_details = $this->Invoice_model->pos_invoice_setup($product_id);

        $tr = " ";
        if (!empty($product_details)) {

        
            $tr .= "<tr>
                     <td> <input type=\"text\" style=\"width:150px\" name=\"\" class=\"form-control\" value=\"" . htmlspecialchars($product_details->product_name, ENT_QUOTES, 'UTF-8') . "- (" . htmlspecialchars($product_details->product_model, ENT_QUOTES, 'UTF-8') . ")" ."\" id=\"item_name_\" readonly/>
                     <input type=\"hidden\" name=\"product_id[]\" class=\"\" value=\"".(int)$product_details->product_id."\" id=\"product_id_".(int)$product_details->product_id."\"/>
                     <input type=\"hidden\" name=\"available_qnt[]\" class=\"form-control\" id=\"available_qnt_".(int)$product_details->product_id."\" value=\"".(int)$product_details->total_product."\" readonly>
                                        </td>
                                        
                                        <td>
                                            <input type=\"number\" name=\"product_quantity[]\" id=\"quantity_".$product_details->product_id."\" class=\"form-control\" onkeyup=\"QtyCal('" . $product_details->product_id . "')\" onchange=\"QtyCal('" . $product_details->product_id . "')\" placeholder=\"0.00\" value=\"1\" min=\"0\" />

                                        </td>
                                     
                                        <td>
                                            <input type=\"text\" name=\"product_rate[]\" onkeyup=\"QtyCal('" . $product_details->product_id . "')\" onchange=\"QtyCal('" . $product_details->product_id . "'),,TotalCalculation()\" id=\"product_rate_".$product_details->product_id."\" class=\"form-control\" placeholder=\"0.00\"  value=\"".$product_details->price."\" min=\"0\" required=\"\"/>
                                        </td>
                                        <td>
                                            <input type=\"text\" name=\"product_discount[]\" onkeyup=\"QtyCal('" . $product_details->product_id . "'),TotalCalculation()\" onchange=\"QtyCal('" . $product_details->product_id . "'),TotalCalculation()\" id=\"product_discount_".$product_details->product_id."\" class=\"form-control\" placeholder=\"0.00\" value=\"\" min=\"0\"/>
                                        </td>
                                        <td>
                                            <input class=\"form-control totalprice\" type=\"text\" name=\"total_price[]\" id=\"total_price_".$product_details->product_id."\" value=\"".$product_details->price."\" readonly=\"readonly\" />
                                        </td>
                                        <td>
                                            <input type=\"hidden\" id=\"all_discount_".$product_details->product_id."\" class=\"all_discount\" name=\"discount_amount[]\" />
                                            <button class=\"btn btn-danger btn-xs\" type=\"button\"  onclick=\"deleteRow(this),TotalCalculation()\"><i class=\"fa fa-trash\"> </i></button>
                                        </td>
                                    </tr>";
            echo $tr;
        } else {
            return false;
        }
    }

//    ========== its for product information ===========
    public function get_only_service_info() {
        $service_id = $this->input->post('product_id',TRUE);
        $get_service_info = $this->Invoice_model->get_only_service_info($service_id);

        echo json_encode($get_service_info);
    }

//    =========== its for get_products ============
    public function get_products() {
        $get_products = $this->Invoice_model->get_products();
        header('Content-Type: application/json');
        echo json_encode($get_products);
    }

//    =========== its for invoice save =========== 
    public function invoice_save() {
        $transaction_id = "T" . date('d') . $this->generators->generator(8);
        $invoice_id = "INV" . date('d') . $this->generators->generator(9);
        $invoice_id = strtoupper($invoice_id);
        $receipt_no = "R" . date('d') . $this->generators->generator(10);

        $customer_id = $this->input->post('customer_id',TRUE);
        $date = $this->input->post('date',TRUE);
        $details = $this->input->post('details',TRUE);
        $product_id = $this->input->post('product_id',TRUE);
 
        $product_quantity = $this->input->post('product_quantity',TRUE);
        $product_rate = $this->input->post('product_rate',TRUE);
        $product_discount = $this->input->post('product_discount',TRUE);
        $discount_amount = $this->input->post('discount_amount',TRUE);
        $total_price = $this->input->post('total_price',TRUE);
        $invoice_discount = $this->input->post('invoice_discount',TRUE);
        $total_discount = $this->input->post('total_discount',TRUE);
        $grand_total_price = $this->input->post('grand_total_price',TRUE);
        $paid_amount = $this->input->post('paid_amount',TRUE);
        $due_amount = $this->input->post('due_amount',TRUE);
        $paytype = $this->input->post('paytype',TRUE);
        $bank_id = $this->input->post('bank_id',TRUE);
        if ($details) {
            $details = $details;
        } else {
            $details = 'Invoice Generated By Customer!';
        }
        //Insert to customer_ledger Table 
        $customer_ledger_debit = array(
            'transaction_id' => $transaction_id,
            'invoice_no' => $invoice_id,
            'ledger_id' => $customer_id,
            'receipt_no' => NULL,
            'date' => $date,
            'amount' => $grand_total_price,
            'payment_type' => $paytype,
            'description' => 'Compra pelo Cliente',
            'd_c' => 'd',
            'created_by' => $this->user_id,
        );
  
        $this->db->insert('ledger_tbl', $customer_ledger_debit);

//        ============ its for customer ledger when credit ============
        $customerer_ledger_credit = array(
            'transaction_id' => $transaction_id,
            'invoice_no' => null,
            'ledger_id' => $customer_id,
            'receipt_no' => $receipt_no,
            'date' => $date,
            'amount' => $paid_amount,
            'payment_type' => $paytype,
            'description' => $details,
            'd_c' => 'c',
            'created_by' => $this->user_id,
        );
        $this->db->insert('ledger_tbl', $customerer_ledger_credit);

        if ($paytype == 2) {
            $bank_ledger_credit = array(
                'transaction_id' => $transaction_id,
                'invoice_no' => null,
                'ledger_id' => $bank_id,
                'receipt_no' => $receipt_no,
                'date' => $date,
                'amount' => $paid_amount,
                'payment_type' => $paytype,
                'description' => $details,
                'd_c' => 'c',
                'created_by' => $this->user_id,
            );
            $this->db->insert('ledger_tbl', $bank_ledger_credit);
        }
        if ($paytype == 1) {
            $cash_ledger = array(
                'transaction_id' => $transaction_id,
                'invoice_no' => null,
                'ledger_id' => 1,
                'receipt_no' => $receipt_no,
                'date' => $date,
                'amount' => $paid_amount,
                'payment_type' => $paytype,
                'description' => $details,
                'd_c' => 'c',
                'created_by' => $this->user_id,
            );
            $this->db->insert('ledger_tbl', $cash_ledger);
        }
        //Data inserting into invoice table
        $invoice_data = array(
            'invoice_id' => $invoice_id,
            'customer_id' => $customer_id,
            'date' => $date,
            'invoice' => $this->generators->number_generator(),
            'invoice_discount' => $invoice_discount,
            'total_discount' => $total_discount,
            'total_amount' => $grand_total_price,
            'paid_amount' => $paid_amount,
            'due_amount' => $due_amount,
            'status' => 1,
            'is_inhouse' => 1,
            'shipping_method' => 0,
            'payment_method' => $paytype,
            'bank_id' => $bank_id,
            'description' => $details,
            'created_by' => $this->user_id,
            'created_at' => date('Y-m-d H:i:s'),
        );
        $this->db->insert('invoice_tbl', $invoice_data);

        //============= its for invoice details entry ============
        for ($i = 0; $i < count($product_id); $i++) {
            $invoice_details_data = array(
                'invoice_details_id' => $this->generators->generator(15),
                'invoice_id' => $invoice_id,
                'product_id' => $product_id[$i],
 
                'quantity' => $product_quantity[$i],
                'price' => $product_rate[$i],
                'discount' => $product_discount[$i],
                'total_price' => $total_price[$i],
                'discount_amount' => $discount_amount[$i],
 
            );
            if (!empty($product_quantity)) {
                $this->db->insert('invoice_details', $invoice_details_data);
            }
        }

        $this->session->set_flashdata('success', "<div class='alert alert-success'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button>Fatura salva com sucesso!</div>");
        redirect('invoice/Invoice/index');
    }

//    ============= its for invoice_list ===========
    public function invoice_list() {
        $this->permission->method('invoice', 'read')->redirect();
        $data['title'] = makeString(['invoice_list']);
        $data['get_appsetting'] = $this->Invoice_model->get_appsetting();
        $config["base_url"] = base_url('sales/CSales/sales_list');
        $config["total_rows"] = $this->db->count_all('invoice_tbl');
 
        $config["per_page"] = 10;
        $config["uri_segment"] = 4;
        $config["last_link"] = "Último";
        $config["first_link"] = "Primeiro";
        $config['next_link'] = 'Próximo';
        $config['prev_link'] = 'Anterior';
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
        $data["sales_list"] = $this->Invoice_model->sales_list($config["per_page"], $page);
        $data["links"] = $this->pagination->create_links();
        $data['pagenum'] = $page;

        $data['module'] = "invoice";
        $data['page'] = "invoice_list";
        echo Modules::run('template/layout', $data);
    }

//    =========== its for invoice edit ========
    public function invoice_edit($invoice_id) {        
        $this->permission->method('invoice', 'edit')->redirect();
        $data['get_customer'] = $this->Invoice_model->get_customer();
        $data['bank_list'] = $this->Account_model->get_banks();
        $data['get_products'] = $this->Invoice_model->get_products();
        $data['edit_invoice'] = $this->Invoice_model->edit_invoice($invoice_id);
        $data['edit_invoicedetails'] = $this->Invoice_model->edit_invoicedetails($invoice_id);
        $data['title'] = makeString(['add_invoice']);
        $data['module'] = "invoice";
        $data['page'] = "invoice_edit";
        echo Modules::run('template/layout', $data);
    }

//    ============== its for invoice_update ========
    public function invoice_update() {
        $invoice_id = $this->input->post('invoice_id',TRUE);

        $customer_id = $this->input->post('customer_id',TRUE);
        $date = $this->input->post('date',TRUE);
        $details = $this->input->post('details',TRUE);
        $product_id = $this->input->post('product_id',TRUE);
 
        $product_quantity = $this->input->post('product_quantity',TRUE);
        $product_rate = $this->input->post('product_rate',TRUE);
        $product_discount = $this->input->post('product_discount',TRUE);
        $discount_amount = $this->input->post('discount_amount',TRUE);
        $total_price = $this->input->post('total_price',TRUE);
        $invoice_discount = $this->input->post('invoice_discount',TRUE);
        $total_discount = $this->input->post('total_discount',TRUE);
        $grand_total_price = $this->input->post('grand_total_price',TRUE);
        $paid_amount = $this->input->post('paid_amount',TRUE);
        $due_amount = $this->input->post('due_amount',TRUE);
        $paytype = $this->input->post('paytype',TRUE);
        $bank_id = $this->input->post('bank_id',TRUE);
        if ($details) {
            $details = $details;
        } else {
            $details = 'Fatura Gerada pelo Cliente!';
        }
        $customer_ledger = $this->db->select('*')->from('ledger_tbl')->where('invoice_no', $invoice_id)->get()->result();

        $transaction_id = "T" . date('d') . $this->generators->generator(15);
        $receipt_no = strtoupper("R" . date('d') . $this->generators->generator(11));
        foreach ($customer_ledger as $value) {
            $transaction_id = $value->transaction_id;
            $this->db->where('transaction_id', $transaction_id)->delete('transaction');
            $this->db->where('transaction_id', $transaction_id)->delete('ledger_tbl');
//            ======== its for invoice record delete ===========
            $this->db->where('invoice_id', $invoice_id)->delete('invoice_details');
        }
        //Insert to customer_ledger Table 
        $customer_ledger_debit = array(
            'transaction_id' => $transaction_id,
            'invoice_no' => $invoice_id,
            'ledger_id' => $customer_id,
            'receipt_no' => NULL,
            'date' => $date,
            'amount' => $grand_total_price,
            'payment_type' => $paytype,
            'description' => 'Purchase By Customer',
            'd_c' => 'd',
            'created_by' => $this->user_id,
        );

        $this->db->insert('ledger_tbl', $customer_ledger_debit);

//        ============ its for customer ledger when credit ============
        $customerer_ledger_credit = array(
            'transaction_id' => $transaction_id,
            'invoice_no' => null,
            'ledger_id' => $customer_id,
            'receipt_no' => $receipt_no,
            'date' => $date,
            'amount' => $paid_amount,
            'payment_type' => $paytype,
            'description' => $details,
            'd_c' => 'c',
            'created_by' => $this->user_id,
        );
        $this->db->insert('ledger_tbl', $customerer_ledger_credit);


        if ($paytype == 2) {
            $bank_ledger_credit = array(
                'transaction_id' => $transaction_id,
                'invoice_no' => null,
                'ledger_id' => $bank_id,
                'receipt_no' => $receipt_no,
                'date' => $date,
                'amount' => $paid_amount,
                'payment_type' => $paytype,
                'description' => $details,
                'd_c' => 'c',
                'created_by' => $this->user_id,
            );
            $this->db->insert('ledger_tbl', $bank_ledger_credit);
        }
        if ($paytype == 1) {
            $cash_ledger = array(
                'transaction_id' => $transaction_id,
                'invoice_no' => null,
                'ledger_id' => 1,
                'receipt_no' => $receipt_no,
                'date' => $date,
                'amount' => $paid_amount,
                'payment_type' => $paytype,
                'description' => $details,
                'd_c' => 'c',
                'created_by' => $this->user_id,
            );
            $this->db->insert('ledger_tbl', $cash_ledger);
        }
        //Data inserting into invoice table
        $invoice_data = array(
            'invoice_id' => $invoice_id,
            'customer_id' => $customer_id,
            'date' => $date,
            'invoice' => $this->generators->number_generator(),
            'invoice_discount' => $invoice_discount,
            'total_discount' => $total_discount,
            'total_amount' => $grand_total_price,
            'paid_amount' => $paid_amount,
            'due_amount' => $due_amount,
            'status' => 1,
            'is_inhouse' => 1,
            'shipping_method' => 0,
            'payment_method' => $paytype,
            'bank_id' => $bank_id,
            'description' => $details,
            'created_by' => $this->user_id,
            'created_at' => date('Y-m-d H:i:s'),
        );
        $this->db->where('invoice_id', $invoice_id)->update('invoice_tbl', $invoice_data);

        //============= its for invoice details entry ============
        for ($i = 0; $i < count($product_id); $i++) {
            $invoice_details_data = array(
                'invoice_details_id' => $this->generators->generator(15),
                'invoice_id' => $invoice_id,
                'product_id' => $product_id[$i],
                'quantity' => $product_quantity[$i],
                'price' => $product_rate[$i],
                'discount' => $product_discount[$i],
                'total_price' => $total_price[$i],
                'discount_amount' => $discount_amount[$i],

            );
            if (!empty($product_quantity)) {
                $this->db->insert('invoice_details', $invoice_details_data);
            }
        }

        $this->session->set_flashdata('success', "<div class='alert alert-success'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button>Fatura salva com sucesso!</div>");
        redirect('invoice/Invoice/invoice_list');
    }

//    ======== its for order status change ============
    public function order_status_change() {
        $invoice_id = $this->input->post('invoice_id',TRUE);
        $status = $this->input->post('status',TRUE);
        $invoice_info = $this->Invoice_model->get_invoice_info($invoice_id);
        $is_inhouse = $invoice_info->is_inhouse;
        if ($is_inhouse == 2 && $status == 2) {
            $status_data = array(
                'status' => $status,
                'paid_amount' => $invoice_info->total_amount,
            );
            $this->db->where('invoice_id', $invoice_id)->update('invoice_tbl', $status_data);
            $this->orderstatus_payment($invoice_info);
        } else {
            $status_data = array(
                'status' => $status,
            );
            $this->db->where('invoice_id', $invoice_id)->update('invoice_tbl', $status_data);
        }
        $this->session->set_flashdata('success', "<div class='alert alert-success'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button>Status do pedido atualizado com sucesso!</div>");
        redirect('invoice/Invoice/invoice_list');
    }

//    ======== single order pdf generate =========
    public function single_order_pdf($order_id) {
        $data['title'] = '';
        $data['get_appsetting'] = $this->Invoice_model->get_appsetting();

        $this->load->library('pdfgenerator');
        $this->load->helper('download');
        $content = $this->load->view('invoice/demo_invoice_pdf', $data, true);

        $dompdf = new DOMPDF();
        $dompdf->load_html($content);
        $dompdf->render();
        $output = $dompdf->output();
        file_put_contents('admin_assets/pdf/invoice/' . 'customer-' . $order_id . '.pdf', $output);
        $file_path = 'admin_assets/pdf/invoice/' . 'customer-' . $order_id . '.pdf';
        $file_name = 'customer-' . $order_id . '.pdf';
        force_download(FCPATH . 'admin_assets/pdf/invoice/' . $file_name, null);
    }

// ================ 
    public function get_banks() {
        $bank_list = $this->Account_model->get_banks();
        header('Content-Type: application/json');
        echo json_encode($bank_list);
    }

//    ============= its for invoice_delete ===========
    public function invoice_delete($invoice_id) {
        $this->permission->method('invoice', 'delete')->redirect();
        $customer_ledger = $this->db->select('*')->from('ledger_tbl')->where('invoice_no', $invoice_id)->get()->result();

        $this->db->where('invoice_id', $invoice_id)->delete('invoice_tbl');
        $this->db->where('invoice_id', $invoice_id)->delete('invoice_details');
        foreach ($customer_ledger as $value) {
            $transaction_id = $value->transaction_id;
            $this->db->where('transaction_id', $transaction_id)->delete('ledger_tbl');
        }
        $this->session->set_flashdata('success', "<div class='alert alert-success'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button>Fatura excluída com sucesso!</div>");
        redirect('invoice/Invoice/invoice_list');
    }

//    ============ its for single invoice ==============
    public function single_invoice($invoice_id) {
        $data['title'] = makeString(['add_invoice']);        
        $data['get_appsetting'] = $this->Invoice_model->get_appsetting();
        $data['get_invoice_info'] = $this->Invoice_model->get_invoice_info($invoice_id);
        $data['get_invoice_details'] = $this->Invoice_model->get_invoice_details($invoice_id);

        $data['module'] = "invoice";
        $data['page'] = "single_invoice";
        echo Modules::run('template/layout', $data);
    }

    /// Save Pos Invoice 
     public function insert_pos_sale() {
        $transaction_id = "T" . date('d') . $this->generators->generator(8);
        $invoice_id = "INV" . date('d') . $this->generators->generator(9);
        $invoice_id = strtoupper($invoice_id);
        $receipt_no = "R" . date('d') . $this->generators->generator(10);

        $customer_id = $this->input->post('customer_id',TRUE);
        if(empty($customer_id)){
            $this->session->set_flashdata('exception',  'Por favor, selecione um cliente');
            redirect('invoice/invoice/add_pos'); 
        }
        
        $date = date('Y-m-d');
        $details = 'Da Venda PDV';
        $product_id = $this->input->post('product_id',TRUE);
        $product_quantity = $this->input->post('product_quantity',TRUE);
        
        // Validar se há produtos no carrinho
        if (empty($product_id) || !is_array($product_id) || count($product_id) == 0) {
            $this->session->set_flashdata('error', "<div class='alert alert-danger'>Por favor, adicione produtos ao carrinho antes de finalizar.</div>");
            redirect('invoice/invoice/add_pos');
        }
        $product_rate = $this->input->post('product_rate',TRUE);
        $product_discount = $this->input->post('product_discount',TRUE);
        $discount_amount = $this->input->post('discount_amount',TRUE);
        $total_price = $this->input->post('total_price',TRUE);
        $invoice_discount = $this->input->post('invoice_discount',TRUE);
        $total_discount = $this->input->post('total_discount',TRUE);
        $grand_total_price = $this->input->post('grand_total_price',TRUE);
        $paid_amount = $this->input->post('paid_amount',TRUE);
        $due_amount = $this->input->post('due_amount',TRUE);
        $paytype = (int)$this->input->post('paytype', TRUE) ?: 1;
        $bank_id = $this->input->post('bank_id',TRUE);
        if ($details) {
            $details = $details;
        } else {
            $details = 'Invoice Generated By Customer!';
        }
        //Insert to customer_ledger Table 
        $customer_ledger_debit = array(
            'transaction_id' => $transaction_id,
            'invoice_no' => $invoice_id,
            'ledger_id' => $customer_id,
            'receipt_no' => NULL,
            'date' => $date,
            'amount' => $grand_total_price,
            'payment_type' => $paytype,
            'description' => 'Purchase By Customer',
            'd_c' => 'd',
            'created_by' => $this->user_id,
        );

        $this->db->insert('ledger_tbl', $customer_ledger_debit);

//        ============ its for customer ledger when credit ============
        $customerer_ledger_credit = array(
            'transaction_id' => $transaction_id,
            'invoice_no' => null,
            'ledger_id' => $customer_id,
            'receipt_no' => $receipt_no,
            'date' => $date,
            'amount' => $paid_amount,
            'payment_type' => $paytype,
            'description' => $details,
            'd_c' => 'c',
            'created_by' => $this->user_id,
        );
        $this->db->insert('ledger_tbl', $customerer_ledger_credit);

        if ($paytype == 2) {
            $bank_ledger_credit = array(
                'transaction_id' => $transaction_id,
                'invoice_no' => null,
                'ledger_id' => $bank_id,
                'receipt_no' => $receipt_no,
                'date' => $date,
                'amount' => $paid_amount,
                'payment_type' => $paytype,
                'description' => $details,
                'd_c' => 'c',
                'created_by' => $this->user_id,
            );
            $this->db->insert('ledger_tbl', $bank_ledger_credit);
        }
        if ($paytype == 1) {
            $cash_ledger = array(
                'transaction_id' => $transaction_id,
                'invoice_no' => null,
                'ledger_id' => 1,
                'receipt_no' => $receipt_no,
                'date' => $date,
                'amount' => $paid_amount,
                'payment_type' => $paytype,
                'description' => $details,
                'd_c' => 'c',
                'created_by' => $this->user_id,
            );
            $this->db->insert('ledger_tbl', $cash_ledger);
        }
        //Data inserting into invoice table
        $invoice_data = array(
            'invoice_id' => $invoice_id,
            'customer_id' => $customer_id,
            'date' => $date,
            'invoice' => $this->generators->number_generator(),
            'invoice_discount' => $invoice_discount,
            'total_discount' => $total_discount,
            'total_amount' => $grand_total_price,
            'paid_amount' => $paid_amount,
            'due_amount' => $due_amount,
            'status' => 1,
            'is_inhouse' => 1,
            'shipping_method' => 0,
            'payment_method' => $paytype,
            'bank_id' => $bank_id,
            'description' => $details,
            'created_by' => $this->user_id,
            'created_at' => date('Y-m-d H:i:s'),
        );
        $this->db->insert('invoice_tbl', $invoice_data);

        //============= its for invoice details entry ============
        for ($i = 0; $i < count($product_id); $i++) {
            $invoice_details_data = array(
                'invoice_details_id' => $this->generators->generator(15),
                'invoice_id' => $invoice_id,
                'product_id' => $product_id[$i],
                'quantity' => $product_quantity[$i],
                'price' => $product_rate[$i],
                'discount' => $product_discount[$i],
                'total_price' => $total_price[$i],
                'discount_amount' => $discount_amount[$i],
            );
            if (!empty($product_quantity)) {
                $this->db->insert('invoice_details', $invoice_details_data);
            }
        }

        $this->session->set_flashdata('success', "<div class='alert alert-success'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button>Invoice save successfully!</div>");
        redirect('invoice/Invoice/single_invoice/'.$invoice_id);
    }


public function add_payment(){
    $payment_type = $this->input->post('paytype',TRUE);
    $bank_id      = $this->input->post('bank',TRUE);
    $data['bank']        = $bank_id;
    $data['paymenttype'] = $payment_type;
    echo json_encode($data);

}
}
