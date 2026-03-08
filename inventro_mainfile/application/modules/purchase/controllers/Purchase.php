<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Purchase extends MX_Controller {

public function __construct()
	{
		parent::__construct();
		
		$this->load->model(array(
			'Purchase_model'
		));		 
	}

public function purchase_list(){   
        $data['title']    = makeString(['purchase_list']);  ;
		$data['module']   = "purchase";
		$data['totalpurchase'] = $this->db->count_all('product_purchase');
		$data['page']     = "purchase_list";   
		echo Modules::run('template/layout', $data); 
	} 

    public function CheckPurchaseList(){
        // GET data
        $postData = $this->input->post();
        $data = $this->Purchase_model->getPurchaseList($postData);
        $data['csrf_token'] = $this->security->get_csrf_hash();
        echo json_encode($data);
    }

public function create_purchase($id = null){ 
  $data['title'] = makeString(['add_purchase']);
 
  $this->form_validation->set_rules('supplier_id', makeString(['supplier'])  ,'required|max_length[20]');
  $this->form_validation->set_rules('purchase_date', makeString(['date'])  ,'required|max_length[10]');
  $this->form_validation->set_rules('chalan_no', makeString(['chalan_no'])  ,'max_length[20]');
  $this->form_validation->set_rules('paytype', makeString(['paytype'])  ,'required|max_length[20]');
  $paytype = $this->input->post('paytype');
 
  $purchase_id = $this->generator(15);
   $data = array(
            'purchase_id'        => $purchase_id,
            'chalan_no'          => $this->input->post('chalan_no',TRUE),
            'supplier_id'        => $this->input->post('supplier_id',TRUE),
            'grand_total_amount' => $this->input->post('grand_total_price',TRUE),
            'purchase_date'      => $this->input->post('purchase_date',TRUE),
            'purchase_details'   => $this->input->post('purchase_details',TRUE),
            'discount'           => $this->input->post('discount',TRUE),
            'status'             => 1,
            'bank_id'            =>  $this->input->post('bank_id',TRUE),
            'payment_type'       =>  $this->input->post('paytype',TRUE),
        );

   $supplier_ledger = array(
            'transaction_id'      => $purchase_id,
            'transaction_category'=> 'Purchase',
            'ledger_id'           => $this->input->post('supplier_id',TRUE),
            'receipt_no'          => $this->input->post('chalan_no',TRUE),
            'amount'              => $this->input->post('grand_total_price',TRUE),
            'date'                => $this->input->post('purchase_date',TRUE),
            'description'         => $this->input->post('purchase_details',TRUE),
            'status'              => 1,
            'created_by'          =>  $this->session->userdata('id'),
            'd_c'                 =>  'c',
        );
   $supplier_debit = array(
            'transaction_id'      => $purchase_id,
            'transaction_category'=> 'Purchase',
            'ledger_id'           => $this->input->post('supplier_id',TRUE),
            'receipt_no'          => $this->input->post('chalan_no',TRUE),
            'amount'              => $this->input->post('grand_total_price',TRUE),
            'date'                => $this->input->post('purchase_date',TRUE),
            'description'         => $this->input->post('purchase_details',TRUE),
            'status'              => 1,
            'created_by'          =>  $this->session->userdata('id'),
            'd_c'                 =>  'd',
        );


   $cashinhand = array(
            'transaction_id'      => $purchase_id,
            'transaction_category'=> 'Purchase',
            'ledger_id'           => 1,
            'receipt_no'          => $this->input->post('chalan_no',TRUE),
            'amount'              => $this->input->post('grand_total_price',TRUE),
            'date'                => $this->input->post('purchase_date',TRUE),
            'description'         => $this->input->post('purchase_details',TRUE),
            'status'              => 1,
            'created_by'          =>  $this->session->userdata('id'),
            'd_c'                 =>  'c',
        );

    $bank_ledger = array(
            'transaction_id'      => $purchase_id,
            'transaction_category'=> 'Purchase',
            'ledger_id'           => $this->input->post('bank_id',TRUE),
            'receipt_no'          => $this->input->post('chalan_no',TRUE),
            'amount'              => $this->input->post('grand_total_price',TRUE),
            'date'                => $this->input->post('purchase_date',TRUE),
            'description'         => $this->input->post('purchase_details',TRUE),
            'status'              => 1,
            'created_by'          =>  $this->session->userdata('id'),
            'd_c'                 =>  'c',
        );


  if ($this->form_validation->run()) { 

    if ($this->Purchase_model->create_purchase($data)) { 
  if($paytype == 1){
  $this->db->insert('ledger_tbl',$supplier_debit);
  $this->db->insert('ledger_tbl',$cashinhand);
  }
  if($paytype == 2){
    $this->db->insert('ledger_tbl',$supplier_debit);
    $this->db->insert('ledger_tbl',$bank_ledger);

  }

      $this->db->insert('ledger_tbl',$supplier_ledger);
        $p_id     = $this->input->post('product_id',TRUE);
        $rate     = $this->input->post('product_rate',TRUE);
        $quantity = $this->input->post('product_quantity',TRUE);
        $t_price  = $this->input->post('total_price',TRUE);
        
        for ($i = 0, $n = count($p_id); $i < $n; $i++) {
            $product_quantity = $quantity[$i];
            $product_rate     = $rate[$i];
            $product_id       = $p_id[$i];
            $total_price      = $t_price[$i];

            $details = array(
                'purchase_detail_id' => $this->generator(15),
                'purchase_id'        => $purchase_id,
                'product_id'         => $product_id,
                'quantity'           => $product_quantity,
                'rate'               => $product_rate,
                'total_amount'       => $total_price,
                'status'             => 1
            );

            if (!empty($quantity)) {
                $this->db->insert('product_purchase_details', $details);
            }
        }

	$this->session->set_flashdata('message', makeString(['save_successfully']));
     redirect('purchase/purchase/purchase_list');
    } else {
     $this->session->set_flashdata('exception',  makeString(['please_try_again']));
    }
    redirect("purchase/purchase/create_purchase"); 

  } else { 
   $data['supplier_list'] = $this->Purchase_model->supplier_list();
   $data['title'] = makeString(['add_purchase']);
   $data['module'] = "purchase";
   $data['bank_list'] = $this->Purchase_model->bank_list();
   $data['page']   = "purchase_form"; 
   echo Modules::run('template/layout', $data); 
   }  
}

//purchase edit form
  public function purchase_edit_data($purchase_id) {
        
        $purchase_detail = $this->Purchase_model->retrieve_purchase_editdata($purchase_id);
         $bank_list = $this->Purchase_model->bank_list();
         $supplier_id = $purchase_detail[0]['supplier_id'];

       $supplier_list = $this->Purchase_model->supplier_list();
   
       
        $data = array(
            'title'         => makeString(['purchase_edit']),
            'purchase_id'   => $purchase_detail[0]['purchase_id'],
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

   $data['module'] = "purchase";
   $data['page']   = "purchase_edit_form"; 
   echo Modules::run('template/layout', $data); 
    }


    // purchase update
    public function update_purchase(){ 
  $data['title'] = makeString(['edit_purchase']);
 
  $this->form_validation->set_rules('purchase_id', makeString(['purchase_id'])  ,'required|max_length[20]');
  $this->form_validation->set_rules('supplier_id', makeString(['supplier'])  ,'required|max_length[20]');
  $this->form_validation->set_rules('purchase_date', makeString(['date'])  ,'required|max_length[10]');
  $this->form_validation->set_rules('chalan_no', makeString(['chalan_no'])  ,'required|max_length[20]');
  $this->form_validation->set_rules('paytype', makeString(['paytype'])  ,'required|max_length[20]');
 
  $purchase_id = $this->input->post('purchase_id');
  $paytype = $this->input->post('paytype');
   $data = array(
            'purchase_id'        => $purchase_id,
            'chalan_no'          => $this->input->post('chalan_no',TRUE),
            'supplier_id'        => $this->input->post('supplier_id',TRUE),
            'grand_total_amount' => $this->input->post('grand_total_price',TRUE),
            'purchase_date'      => $this->input->post('purchase_date',TRUE),
            'purchase_details'   => $this->input->post('purchase_details',TRUE),
            'discount'           => $this->input->post('discount',TRUE),
            'status'             => 1,
            'bank_id'            =>  $this->input->post('bank_id',TRUE),
            'payment_type'       =>  $this->input->post('paytype',TRUE),
        );

   $supplier_ledger = array(
            'transaction_id'      => $purchase_id,
            'transaction_category'=> 'Purchase',
            'ledger_id'           => $this->input->post('supplier_id',TRUE),
            'receipt_no'          => $this->input->post('chalan_no',TRUE),
            'amount'              => $this->input->post('grand_total_price',TRUE),
            'date'                => $this->input->post('purchase_date',TRUE),
            'description'         => $this->input->post('purchase_details',TRUE),
            'status'              => 1,
            'created_by'          =>  $this->session->userdata('id'),
            'd_c'                 =>  'c',
        );
   $supplier_debit = array(
            'transaction_id'      => $purchase_id,
            'transaction_category'=> 'Purchase',
            'ledger_id'           => $this->input->post('supplier_id',TRUE),
            'receipt_no'          => $this->input->post('chalan_no',TRUE),
            'amount'              => $this->input->post('grand_total_price',TRUE),
            'date'                => $this->input->post('purchase_date',TRUE),
            'description'         => $this->input->post('purchase_details',TRUE),
            'status'              => 1,
            'created_by'          =>  $this->session->userdata('id'),
            'd_c'                 =>  'd',
        );


   $cashinhand = array(
            'transaction_id'      => $purchase_id,
            'transaction_category'=> 'Purchase',
            'ledger_id'           => 1,
            'receipt_no'          => $this->input->post('chalan_no',TRUE),
            'amount'              => $this->input->post('grand_total_price',TRUE),
            'date'                => $this->input->post('purchase_date',TRUE),
            'description'         => $this->input->post('purchase_details',TRUE),
            'status'              => 1,
            'created_by'          =>  $this->session->userdata('id'),
            'd_c'                 =>  'c',
        );

    $bank_ledger = array(
            'transaction_id'      => $purchase_id,
            'transaction_category'=> 'Purchase',
            'ledger_id'           => $this->input->post('bank_id',TRUE),
            'receipt_no'          => $this->input->post('chalan_no',TRUE),
            'amount'              => $this->input->post('grand_total_price',TRUE),
            'date'                => $this->input->post('purchase_date',TRUE),
            'description'         => $this->input->post('purchase_details',TRUE),
            'status'              => 1,
            'created_by'          =>  $this->session->userdata('id'),
            'd_c'                 =>  'c',
        );


  if ($this->form_validation->run()) { 

    if ($this->Purchase_model->update($data)) { 


      if($paytype == 1){
  $this->db->insert('ledger_tbl',$supplier_debit);
  $this->db->insert('ledger_tbl',$cashinhand);
  }
  if($paytype == 2){
    $this->db->insert('ledger_tbl',$supplier_debit);
    $this->db->insert('ledger_tbl',$bank_ledger);

  }
      
      $this->db->insert('ledger_tbl',$supplier_ledger);
        $p_id     = $this->input->post('product_id',TRUE);
        $rate     = $this->input->post('product_rate',TRUE);
        $quantity = $this->input->post('product_quantity',TRUE);
        $t_price  = $this->input->post('total_price',TRUE);
        
        for ($i = 0, $n = count($p_id); $i < $n; $i++) {
            $product_quantity = $quantity[$i];
            $product_rate     = $rate[$i];
            $product_id       = $p_id[$i];
            $total_price      = $t_price[$i];

            $details = array(
                'purchase_detail_id' => $this->generator(15),
                'purchase_id'        => $purchase_id,
                'product_id'         => $product_id,
                'quantity'           => $product_quantity,
                'rate'               => $product_rate,
                'total_amount'       => $total_price,
                'status'             => 1
            );

            if (!empty($product_quantity)) {
                $this->db->insert('product_purchase_details', $details);
            }
        }

  $this->session->set_flashdata('message', makeString(['save_successfully']));
     redirect('purchase/purchase/purchase_list');
    } else {
     $this->session->set_flashdata('exception',  makeString(['please_try_again']));
    }
    redirect('purchase/purchase/purchase_list'); 

  } else { 
    $this->session->set_flashdata('exception',  makeString(['please_try_again']));
    redirect('purchase/purchase/purchase_list');
   }  
}

  public function delete($id = null){ 
		if ($this->Purchase_model->delete($id)) {
			#set success message
			$this->session->set_flashdata('message',makeString(['delete_successfully']));
		} else {
			#set exception message
			$this->session->set_flashdata('exception',makeString(['please_try_again']));
		}
		redirect("purchase/purchase/purchase_list");
	}


  // purchase details print page
    public function purchase_details($purchase_id) {
        $purchase_detail = $this->Purchase_model->purchase_details_data($purchase_id);
         $data = array(
            'title'            => makeString(['purchase_ledger']),
            'purchase_id'      => $purchase_detail[0]['purchase_id'],
            'purchase_details' => $purchase_detail[0]['purchase_details'],
            'supplier_name'    => $purchase_detail[0]['supplier_name'],
            'final_date'       => $purchase_detail[0]['purchase_date'],
            'sub_total_amount' => number_format($purchase_detail[0]['grand_total_amount'], 2, '.', ','),
            'discount' => number_format($purchase_detail[0]['discount'], 2, '.', ','),
            'chalan_no'        => $purchase_detail[0]['chalan_no'],
            'purchase_all_data'=> $purchase_detail
        );
         $data['module'] = "purchase";
         $data['page']   = "purchase_detail"; 
         echo Modules::run('template/layout', $data); 
    }


///Item Search By supplier 
	 public function product_search_by_supplier() {

        $supplier_id = $this->input->post('supplier_id',TRUE);
        $product_name = $this->input->post('product_name',TRUE);
        $product_info = $this->Purchase_model->product_search_item($supplier_id, $product_name);
        if(!empty($product_info)){
        $list[''] = '';
        foreach ($product_info as $value) {
            $json_product[] = array('label'=>$value['name'].'('.$value['model'].')','value'=>$value['product_id']);
        } 
    }else{
        $json_product[] = 'No Product Found';
        }
        echo json_encode($json_product);
    }
// Product data for autocomplete field
public function retrieve_product_data() {
        $product_id = $this->input->post('product_id',TRUE);
        $supplier_id = $this->input->post('supplier_id',TRUE);

        $product_info = $this->Purchase_model->get_total_product($product_id, $supplier_id);

        echo json_encode($product_info);
    }


    public function generator($lenth) {
        $number = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "N", "M", "O", "P", "Q", "R", "S", "U", "V", "T", "W", "X", "Y", "Z", "a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z", "1", "2", "3", "4", "5", "6", "7", "8", "9", "0");

        for ($i = 0; $i < $lenth; $i++) {
            $rand_value = rand(0, 61);
            $rand_number = $number["$rand_value"];

            if (empty($con)) {
                $con = $rand_number;
            } else {
                $con = "$con" . "$rand_number";
            }
        }
        return $con;
    }

}
