<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Menu_setting extends MX_Controller {

    public $data = [];
    private $user_id = '';
    public function __construct(){

        parent::__construct();
        $this->permission->module()->redirect();
        $this->user_id = $this->session->userdata('id');
        $this->load->model(array(
            'Menu_model'
        ));

    }



    public function index(){
        $this->data['title']        = html_escape('Add Menu');
        $this->data['module']       = "menu";  
        $this->data['page']         = "add_menu";   
        echo Modules::run('template/layout', $this->data); 


    }

    public function save_menu(){
        $this->form_validation->set_rules('menu_title','menu title', 'trim|required');
        $this->form_validation->set_rules('page_url','page url', 'trim|required');
        $this->form_validation->set_rules('module_name','module name', 'trim|required');
        if ($this->form_validation->run() == true) {
        $menu_name=$this->input->post('menu_title',TRUE);
        $page_url=$this->input->post('page_url',TRUE);
        $module=$this->input->post('module_name',TRUE);
        $parent_menu=$this->input->post('parent_menu',TRUE);
         $data = array(
                'menu_title'  =>$menu_name, 
                'page_url'  =>$page_url, 
                'module'  =>$module, 
                'parent_menu'  =>$parent_menu, 
                'status'  =>1, 
                'createby'  =>$this->user_id, 
                'createdate'  =>null, 
        
         );
         $menu_insert = $this->Menu_model->create($data); 
            if($menu_insert){
            $this->session->set_userdata('success','Insert successfully');
                redirect("menu/menu_setting");
            }else{
             $this->session->set_userdata('error','Something is wrong');
                redirect('menu/menu_setting');
            }
        }else{
            
            $this->data['title']        = html_escape('Add Menu');
            $this->data['module']       = "menu";  
            $this->data['page']         = "add_menu";   
            echo Modules::run('template/layout', $this->data); 
        } 

    }


    public function Menu_list(){
        $this->data['title']        = html_escape('Menu List');
        $this->data['module']       = "menu";  
        $this->data['page']         = "menu_list";
        $this->data['menu_list'] = $this->Menu_model->menu_list(); 
        echo Modules::run('template/layout', $this->data); 

    }
    public function delete($id = null){ 
        $data=array(
            'menu_id' =>$id,
        );
        $this->db->where('menu_id',$data['menu_id']);
        $this->db->delete('sec_menu_item',$data);
        $this->session->set_flashdata('success','Delete Successfully');
        redirect('menu/menu_setting/menu_list');
    }

     public function edit_menu($id = null){ 

        $this->data['title']        = html_escape('Edit Menu');
        $this->data['module']       = "menu";  
        $this->data['page']         = "edit_menu";
        $this->data['menu_edit'] = $this->Menu_model->menu_edit($id); 
        echo Modules::run('template/layout', $this->data); 
    }   


    public function update_menu($id){
         $menu_name=$this->input->post('menu_title',TRUE);
         $page_url=$this->input->post('page_url',TRUE);
         $module=$this->input->post('module_name',TRUE);
         $parent_menu=$this->input->post('parent_menu',TRUE);
         $data = array(
                'menu_id'=>$id,
                'menu_title'  =>$menu_name, 
                'page_url'  =>$page_url, 
                'module'  =>$module, 
                'parent_menu'  =>$parent_menu, 
                'status'  =>1, 
                'createby'  =>$this->user_id, 
                'createdate'  =>date("Y-m-d"), 
         );

       $update_menu= $this->Menu_model->update_menu($data); 
            if($update_menu){ 
            $this->session->set_userdata('success','Upload Successfully');
                redirect('menu/menu_setting/edit_menu/'.$id);
            }else{
             $this->session->set_userdata('error','Please Try Again');
                redirect('menu/menu_setting/edit_menu/'.$id);
            }
    }

    public function active_menu($id){
          $data=array(
                'menu_id'=>$id,
                'status'=>0,
           );
          $this->db->where('menu_id',$data['menu_id']);
          $this->db->update('sec_menu_item',$data); 
          redirect('menu/menu_setting/menu_list');
          }   
      public function deactive_menu($id){
            $data=array(
                'menu_id'=>$id,
                'status'=>1,
            );
            $this->db->where('menu_id',$data['menu_id']);
            $this->db->update('sec_menu_item',$data); 
            redirect('menu/menu_setting/menu_list');
      }






}