<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Employee extends MX_Controller {

    
    public function __construct(){

        parent::__construct();
        $this->permission->module()->redirect();

        $this->load->model(array(
            'employee_model',
            'department_model',
            'designation_model'
        ));

    }



    public function add_employee(){

        $this->permission->check_label('add_employee')->read()->redirect(); 

        $data['departments'] = $this->department_model->get_all_department();
        $data['designations'] = $this->designation_model->get_all_designation();
        $data['countrys'] = $this->employee_model->get_country();

    	$data['title']        =  makeString(['add_employee']);
        $data['module']       = "hrm";  
        $data['page']         = "__add_employee";   
        echo Modules::run('template/layout', $data); 


    }



    public function manage_employee(){

        $this->permission->check_label('manage_employee')->read()->redirect(); 

        $data['employees'] = $this->employee_model->get_all_employee();

        $data['title']        =  makeString(['employee','list']);
        $data['module']       = "hrm";  
        $data['page']         = "__manage_employee";   
        echo Modules::run('template/layout', $data); 


    }

    public function save_employee(){


        $this->form_validation->set_rules('firstname', 'firstname','required');
        $this->form_validation->set_rules('lastname', 'lastname','required');
        $this->form_validation->set_rules('email', 'email','required|valid_email|is_unique[employee_tbl.em_email]|max_length[100]');
        $this->form_validation->set_rules('phone', 'phone','required');
        $this->form_validation->set_rules('department', 'department','required');
        $this->form_validation->set_rules('designation', 'designation','required');
        $this->form_validation->set_rules('country', 'country','required');
        $this->form_validation->set_rules('city', 'city','required');
        $this->form_validation->set_rules('zip', 'zip','required');
        

        if (empty($_FILES['image']['name'])) {

            $em_image = '';

        }else{

            $config['upload_path']          = './admin_assets/img/user/';
            $config['allowed_types']        = 'gif|jpg|png'; 

            $this->load->library('upload', $config);
     
            if ($this->upload->do_upload('image')) {  

                $data = $this->upload->data();  
                $em_image = $config['upload_path'].$data['file_name']; 

                $config['image_library']  = 'gd2';
                $config['source_image']   = $em_image;
                $config['create_thumb']   = false;
                $config['maintain_ratio'] = TRUE;
                $config['width']          = 115;
                $config['height']         = 90;
                $this->load->library('image_lib', $config);
                $this->image_lib->resize();
               
            }
        }


            $data['employee'] =(object)$employeeData = array(

                'em_first_name'         => $this->input->post('firstname',TRUE),
                'em_last_name'          => $this->input->post('lastname',TRUE),
                'em_email'              => $this->input->post('email',TRUE),
                'em_phone'              => $this->input->post('phone',TRUE),
         
                'em_department'         => $this->input->post('department',TRUE),
                'em_designation'        => $this->input->post('designation',TRUE),
                'em_country'            => $this->input->post('country',TRUE),
                'em_city'               => $this->input->post('city',true),
                'em_zip'                => $this->input->post('zip',true),
                'em_address'            => $this->input->post('address',true),
                'em_image'              => $em_image
                
            );

            $userData = array(
                'firstname'   => $this->input->post('firstname',TRUE),
                'lastname'    => $this->input->post('lastname',TRUE),
                'email'       => $this->input->post('email',TRUE),
                'password'    => md5($this->input->post('password',TRUE)),
                'image'       => $em_image,
                'last_login'  => null,
                'last_logout' => null,
                'ip_address'  => null,
                'status'      => $this->input->post('status',TRUE),
                'is_admin'    => ($this->input->post('type',TRUE)?$this->input->post('type',TRUE):'2')
            );


        if ($this->form_validation->run()) {

            $this->db->insert('employee_tbl',$employeeData);
            $this->db->insert('user',$userData);
            $this->session->set_flashdata('message', makeString(['save_successfully']));
            redirect('hrm/employee/add_employee');

        }else{

            $this->permission->check_label('add_employee')->read()->redirect(); 

            $data['departments'] = $this->department_model->get_all_department();
            $data['designations'] = $this->designation_model->get_all_designation();
            $data['countrys'] = $this->employee_model->get_country();

            $data['title']        =  makeString(['add','employee']);
            $data['module']       = "hrm";  
            $data['page']         = "__add_employee";   
            echo Modules::run('template/layout', $data); 

        }


    }




    public function edit_employee($employee_id){

        $this->permission->check_label('add_employee')->update()->redirect(); 

        $data['departments'] = $this->department_model->get_all_department();
        $data['designations'] = $this->designation_model->get_all_designation();
        $data['countrys'] = $this->employee_model->get_country();

        $data['employee'] = $this->employee_model->get_employee_single($employee_id);

        $data['title']        =  makeString(['edit','employee']);
        $data['module']       = "hrm";  
        $data['page']         = "__edit_employee";   
        echo Modules::run('template/layout', $data); 


    }



    public function update_employee(){

        $employee_id = $this->input->post('employee_id',TRUE);

        if (empty($_FILES['image']['name'])) {

            $em_image = $this->input->post('old_image');

        }else{


                $config['upload_path']          = './admin_assets/img/user/';
                $config['allowed_types']        = 'gif|jpg|png'; 

                $this->load->library('upload', $config);
         
                if ($this->upload->do_upload('image')) {  

                    $data = $this->upload->data();  
                    $em_image = $config['upload_path'].$data['file_name']; 

                    $config['image_library']  = 'gd2';
                    $config['source_image']   = $em_image;
                    $config['create_thumb']   = false;
                    $config['maintain_ratio'] = TRUE;
                    $config['width']          = 115;
                    $config['height']         = 90;
                    $this->load->library('image_lib', $config);
                    $this->image_lib->resize();
                    $this->session->set_flashdata('message', "Image Upload Successfully!");
                }
            }


        $employeeData = array(
            'em_first_name'         => $this->input->post('firstname',TRUE),
            'em_last_name'          => $this->input->post('lastname',TRUE),
            'em_email'              => $this->input->post('email',TRUE),
            'em_phone'              => $this->input->post('phone',TRUE),
            'em_department'         => $this->input->post('department',TRUE),
            'em_designation'       => $this->input->post('designation',TRUE),
            'em_salary'             => $this->input->post('salary',TRUE),
            'em_country'         => $this->input->post('country',TRUE),
            'em_city'               => $this->input->post('city',TRUE),
            'em_zip'               => $this->input->post('zip',TRUE),
            'em_address'            => $this->input->post('address',TRUE),
            'em_image'              => $em_image
        );


        $this->db->where('employee_id',$employee_id)->update('employee_tbl',$employeeData);

        $this->session->set_flashdata('message', makeString(['update_successfully']));
        redirect('hrm/employee/manage_employee');


    }



    public function delete_employee(){

        $this->permission->check_label('employee')->delete()->redirect(); 

        $employee_id = $this->input->post('employee_id');
        $this->db->set('status',0)->where('employee_id',$employee_id)->update('employee_tbl');

        echo json_encode(array('id' => 1 ));
    }

}