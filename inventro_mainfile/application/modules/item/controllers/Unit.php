<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Unit extends MX_Controller {

    public $data = [];
    
    public function __construct(){

        parent::__construct();
        $this->permission->module()->redirect();
        $this->load->model(array(
            'unit_model',
        ));
    }
  
public function unit_form($id = null){ 
  $data['title'] = makeString(['add_unit']);
  $this->form_validation->set_rules('unitname', makeString(['unit_name'])  ,'required|max_length[250]');

   $data['units']   = (Object) $postData = [
   'id'             => $this->input->post('id'), 
   'unit_name'      => $this->input->post('unitname',TRUE),
  ];


  if ($this->form_validation->run()) { 

   if (empty($postData['id'])) {
    if ($this->unit_model->create($postData)) { 
    
    $this->session->set_flashdata('message', makeString(['save_successfully']));

     redirect('item/unit/unit_form');
    } else {
     $this->session->set_flashdata('exception',  makeString(['please_try_again']));
    }
    redirect("item/unit/unit_form"); 

   } else {
    if ($this->unit_model->update($postData)) { 
     $this->session->set_flashdata('message', makeString(['update_successfully']));
    } else {
     $this->session->set_flashdata('exception',  makeString(['please_try_again']));
    }
    redirect("item/unit/unit_form");  
   }

  } else { 
   if(!empty($id)) {
    $data['title']    = makeString(['update_unit']);
    $data['units'] = $this->unit_model->findById($id);
   }
   $data['unitlist'] = $this->unit_model->unit_list();
   $data['module'] = "item";
   $data['page']   = "unit_form"; 
   echo Modules::run('template/layout', $data); 
   }  
}

public function delete_unit($id = null) 
  { 

    if ($this->unit_model->delete($id)) {
      #set success message
      $this->session->set_flashdata('message',makeString(['delete_successfully']));
    } else {
      #set exception message
      $this->session->set_flashdata('exception',makeString(['please_try_again']));
    }
    redirect("item/unit/unit_form");
  }
   
   public function editfrm($id){
    $this->permission->method('unit','update')->redirect();
    $data['title'] = makeString(['unit_edit']);
    $data['units'] = $this->unit_model->findById($id);
    $data['module'] = "item";  
    $data['page']   = "unit_editform";
    $this->load->view('item/unit_editform', $data);   
     }
}