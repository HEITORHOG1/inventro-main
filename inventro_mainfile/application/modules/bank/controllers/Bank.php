<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Bank extends MX_Controller {

    public $data = [];
    
    public function __construct(){

        parent::__construct();
        $this->permission->module()->redirect();
        $this->load->model(array(
            'bank_model',
        ));

    }



  
public function bank_form(){ 
  $data['title'] = makeString(['add_bank']);
  
  $this->form_validation->set_rules('bank_name', makeString(['bank_name'])  ,'required|max_length[150]');
  $this->form_validation->set_rules('account_no', makeString(['account_no'])  ,'required|max_length[30]');
  $this->form_validation->set_rules('branch_name', makeString(['branch_name'])  ,'max_length[250]');
   $lastid=$this->db->select("*")->from('bank_tbl')->order_by('id','desc')->get()->row();
  $bid = (!empty($lastid->id)?$lastid->id:0)+1;
  $bbid = 'bnk_'.$bid;
  $id =  $this->input->post('id');
  if(empty($this->input->post('bank_id',TRUE))){
    $bank_id = $bbid;
  }else{
    $bank_id = $this->input->post('bank_id',TRUE);
  }
  
   $data['banks']   = (Object) $postData = [
   'id'             => $this->input->post('id',TRUE),
   'bank_id'        => $bank_id,  
   'bank_name'      => $this->input->post('bank_name',TRUE),
   'account_no'     => $this->input->post('account_no',TRUE),
   'branch_name'    => $this->input->post('branch_name',TRUE),
   'created_by'     => $this->session->userdata('id'),
   'status'         => 1,

  ];


  if ($this->form_validation->run()) { 

   if (empty($postData['id'])) {
    if ($this->bank_model->create($postData)) { 

     $rdata['status'] = true;
     $rdata['message'] = makeString(['save_successfully']);

    } else {

   
     $rdata['status'] = false;
     $rdata['message'] = ('please_try_again');
    }
   echo json_encode($rdata);

   } else {
    if ($this->bank_model->update($postData)) { 
     
     $rdata['status'] = true;
     $rdata['message'] = makeString(['update_successfully']);
    } else {

     $rdata['status'] = false;
     $rdata['message'] = makeString(['please_try_again']);
    }
       echo json_encode($rdata); 
   }

  } else { 
   $rdata['status'] = false;
  $rdata['message'] = 'Please Check Required Field';
  echo json_encode($rdata); 
   }  
}


public function bank_list(){
   $data['title']    = makeString(['bank_list']);
   $data['banklist'] = $this->bank_model->bank_list();
   $data['banks'] = $this->bank_model->bank_list();
   $data['module'] = "bank";
   $data['page']   = "bank_list"; 
   echo Modules::run('template/layout', $data); 
}



    public function delete_bank(){
        $id = $this->input->post('id',TRUE);
  
        $this->db->where('id',$id)->delete('bank_tbl');
        echo json_encode(array('id'=>1));
    }
   

   public function editfrm($id){
    $this->permission->method('bank','update')->redirect();
    $data['title'] = makeString(['bank_edit']);
    $data['banks'] = $this->bank_model->findById($id);
    $data['module'] = "bank";  
    $data['page']   = "bank_editform";
    echo Modules::run('template/layout', $data);   
     }

      public function bank_ledger(){   
        $data['title']    = makeString(['bank']).' '.makeString(['ledger']);  ;
        $data['module']   = "bank";
        $data['bank_list'] = $this->bank_model->all_bank();
        $data['page']     = "bank_ledger";   
        echo Modules::run('template/layout', $data); 
    } 

    public function search_bankledger(){
        $postData = $this->input->post();
        $data = $this->bank_model->getbankledger($postData);
        echo json_encode($data);
    }

    public function Ledger($id){
        $data['title']    = makeString(['bank']).' '.makeString(['ledger']);  ;
        $data['module']   = "bank";
        $data['bankinfo'] = $this->bank_model->bankdetails($id);
        $data['ledgers'] = $this->bank_model->individualledger($id);
        $data['page']     = "ledger";   
        echo Modules::run('template/layout', $data); 

    }
    
       public function bank_adjustment(){   
        $data['title']    = makeString(['bank_adjustment']) ;
        $data['module']   = "bank";
        $data['bank_list'] = $this->bank_model->all_bank();
        $data['page']     = "bank_adjustment";   
        echo Modules::run('template/layout', $data); 
    } 
    
    public function add_adjustment(){ 
  $data['title'] = makeString(['bank_adjustment']);
  
  $this->form_validation->set_rules('bank_id', makeString(['bank_id'])  ,'required|max_length[15]');
  $this->form_validation->set_rules('payment_date', makeString(['payment_date'])  ,'required|max_length[30]');
  $this->form_validation->set_rules('payment_type', makeString(['payment_type'])  ,'max_length[2]');
  $this->form_validation->set_rules('amount', makeString(['amount'])  ,'max_length[15]');
  
   $data['banks']   = (Object) $postData = [
   'transaction_id'      => date('Ymdhis'),
   'transaction_category'=>'Adjustment',
   'ledger_id'           => $this->input->post('bank_id',TRUE),  
   'description'         => $this->input->post('details',TRUE),
   'amount'              => $this->input->post('amount',TRUE),
   'date'                => $this->input->post('payment_date',TRUE),
   'd_c'                 => $this->input->post('payment_type',TRUE),

  ];


  if ($this->form_validation->run()) { 

    if ($this->bank_model->create_adjustment($postData)) { 
    
     $this->session->set_flashdata('message', makeString(['save_successfully']));

    redirect('bank/bank/bank_adjustment');

    } else {

     $this->session->set_flashdata('exception',  makeString(['please_try_again']));
   redirect('bank/bank/bank_adjustment');
    }
  } else { 
  $this->session->set_flashdata('exception',  makeString(['please_try_again']));
   redirect('bank/bank/bank_adjustment');
   }  
}
}