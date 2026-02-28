<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Designation extends MX_Controller {

    
    public function __construct(){

        parent::__construct();
        $this->permission->module()->redirect();

        $this->load->model(array(
            'designation_model'
        ));

    }



    public function index(){

        $this->permission->check_label('product_inventory')->read()->redirect(); 
        $data['designations'] = $this->designation_model->get_all_designation();

    	$data['title']        =  makeString(['designation']);
        $data['module']       = "hrm";  
        $data['page']         = "__designation";   
        echo Modules::run('template/layout', $data); 


    }

    public function save_designation(){

        $designationData = array(
            'designation_name'          => $this->input->post('designation',TRUE),
            'designation_description'   => $this->input->post('description',TRUE)
        );

        $this->db->insert('designation_tbl',$designationData);

        $this->session->set_flashdata('message', makeString(['save_successfully']));
        redirect('hrm/designation/index');
    }


    public function update_designation(){

        $designation_id = $this->input->post('designation_id',TRUE);

         $designationData = array(
            'designation_name'=>$this->input->post('designation',TRUE),
            'designation_description'=>$this->input->post('description',TRUE)
        );

        $this->db->where('designation_id',$designation_id)->update('designation_tbl',$designationData);

        $this->session->set_flashdata('message', makeString(['update_successfully']));
        redirect('hrm/designation/index');
    }


    public function edit_designation(){

        $designation_id = $this->input->post('designation_id',TRUE);
        $data = $this->designation_model->get_designation_single($designation_id);

        echo json_encode($data);
    }


    public function delete_designation(){

        $designation_id = $this->input->post('designation_id',TRUE);
        $this->db->where('designation_id',$designation_id)->delete('designation_tbl');
        echo json_encode(array('id'=>1));

    }

}