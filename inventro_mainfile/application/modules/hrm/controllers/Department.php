<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Department extends MX_Controller {

    
    public function __construct(){

        parent::__construct();
        $this->load->model(array(
            'department_model'
        ));

    }



    public function index(){

        $this->permission->check_label('department')->read()->redirect(); 


        $data['departments'] = $this->department_model->get_all_department();


    	$data['title']        =  makeString(['designation_tbl']);
        $data['module']       = "hrm";  
        $data['page']         = "__department";   
        echo Modules::run('template/layout', $data); 


    }


    public function save_department(){
        $this->permission->check_label('department')->create()->redirect(); 

        $departmentData = array(
            'department_name'=>$this->input->post('department'),
            'department_description'=>$this->input->post('description')
        );

        $this->db->insert('department_tbl',$departmentData);

        $this->session->set_flashdata('message', makeString(['save_successfully']));
        redirect('hrm/department/index');
    }

    public function update_department(){

        $department_id = $this->input->post('department_id');

        $departmentData = array(
            'department_name'=>$this->input->post('department'),
            'department_description'=>$this->input->post('description')
        );

        $this->db->where('department_id',$department_id)->update('department_tbl',$departmentData);

        $this->session->set_flashdata('message', makeString(['update_successfully']));
        redirect('hrm/department/index');
    }


    public function edit_department(){
        $this->permission->check_label('department')->update()->redirect(); 

        $department_id = $this->input->post('department_id',TRUE);
        $data = $this->department_model->get_department_single($department_id);

        echo json_encode($data);
    }


    public function delete_department(){
        $this->permission->check_label('department')->delete()->redirect(); 
        $department_id = $this->input->post('department_id',TRUE);
         $this->db->where('department_id',$department_id)->delete('department_tbl');

         echo json_encode(array('id'=>1));
    }




    public function salary(){

        $this->permission->check_label('product_inventory')->read()->redirect(); 

        $data['departments'] = $this->department_model->get_all_salary();

        $data['title']        =  makeString(['employee','salary']);
        $data['module']       = "hrm";  
        $data['page']         = "__add_salary";   
        echo Modules::run('template/layout', $data); 


    }



}