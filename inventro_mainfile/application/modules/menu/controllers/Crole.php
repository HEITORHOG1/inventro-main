<?php 

class Crole extends MX_Controller {
     public $data = [];
     private $user_id = '';
     public function __construct() {
        parent::__construct();
        $this->permission->module()->redirect();

        $this->user_id = $this->session->userdata('id');
        $this->load->model(array(
            'Menu_model',
        ));

    }



    public function add_role(){
        $this->data['title']        =  html_escape('Adicionar Função');
        $this->data['module']       = "menu";  
        $this->data['page']         = "add_role"; 
        $this->data['modules'] = $this->db->select('*')->from('sec_menu_item')->group_by('module')->get()->result();  
        echo Modules::run('template/layout', $this->data); 


    }

   public function role_save() {
	  $role_name = $this->input->post('role_name',true);
	  $description = $this->input->post('role_description',true);
	    $roleData = array(
	        'role_name' => $role_name,
	        'role_description' => $description,
	        'create_by' =>1,
	        'date_time' =>date("h:i:s"),
	        'role_status' =>1,
	    );
        $this->db->insert('sec_role_tbl', $roleData);
        $role_id = $this->db->insert_id();
        $module = $this->input->post('module',TRUE);
        $menu_id = $this->input->post('menu_id',TRUE);
        $create = $this->input->post('create',TRUE);
        $read = $this->input->post('read',TRUE);
        $update = $this->input->post('edit',TRUE);
        $delete = $this->input->post('delete',TRUE);

        $new_array = array();
        for ($m = 0; $m < sizeof($module); $m++) {
            for ($i = 0; $i < sizeof($menu_id[$m]); $i++) {
                for ($j = 0; $j < sizeof($menu_id[$m][$i]); $j++) {
                    $dataStore = array(
                        'role_id' => $role_id,
                        'menu_id' => $menu_id[$m][$i][$j],
                        'can_create' => (!empty($create[$m][$i][$j]) ? $create[$m][$i][$j] : 0),
                        'can_edit' => (!empty($update[$m][$i][$j]) ? $update[$m][$i][$j] : 0),
                        'can_access' => (!empty($read[$m][$i][$j]) ? $read[$m][$i][$j] : 0),
                        'can_delete' => (!empty($delete[$m][$i][$j]) ? $delete[$m][$i][$j] : 0),
                        'createby' => $this->user_id,
                        'createdate' =>date("Y-m-d"),
                    );
                    array_push($new_array, $dataStore);
                }
            }
        }

     if ($this->Menu_model->role_create($new_array)) {
            $this->session->set_flashdata('success', "<div class='alert alert-success'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button>Dados salvos com sucesso!</div>");
        } else {
            $this->session->set_flashdata('error', "<div class='alert alert-success'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button>Por favor, tente novamente!</div>");
        }
         redirect('menu/crole/add_role');



}


public function role_list(){
        $this->data['title']        = html_escape('Lista de Funções');
        $this->data['module']       = "menu";  
        $this->data['page']         = "role_list"; 
        $this->data['role_list'] =$this->Menu_model->role_list(); 

        echo Modules::run('template/layout', $this->data); 

}

public function edit_role($id = null){
	    $this->data['title']        = html_escape('Editar Função');
        $this->data['module']       = "menu";  
        $this->data['page']         = "edit_role";
        $this->data['modules'] = $this->db->select('*')->from('sec_menu_item')->group_by('module')->get()->result();
        $this->data['roleInfo'] =$this->db->select("*")
                        ->from('sec_role_tbl')
                        ->where('role_id', $id)
                        ->get()->row();
        $this->data['permissionInfo'] = $this->db->select('sec_role_permission.*,sec_menu_item.menu_title')
                        ->from('sec_role_permission')
                        ->join('sec_menu_item', 'sec_menu_item.menu_id=sec_role_permission.menu_id')
                        ->where('role_id', $id)
                        ->get()->result();                 
        echo Modules::run('template/layout', $this->data); 
}

public function role_update(){
        $role_id = $this->input->post('role_id',TRUE);
        $rolData = array(
            'role_name' => $this->input->post('role_name',TRUE),
            'role_description' => $this->input->post('role_description',TRUE)
        );
        $this->db->where('role_id', $role_id)->update('sec_role_tbl', $rolData);

        //======= ==========
       $module = $this->input->post('module',TRUE);
        $menu_id = $this->input->post('menu_id',TRUE);
        $create = $this->input->post('create',TRUE);
        $read = $this->input->post('read',TRUE);
        $update = $this->input->post('edit',TRUE);
        $delete = $this->input->post('delete',TRUE);
        $new_array = array();
        for ($m = 0; $m < sizeof($module); $m++) {
            for ($i = 0; $i < sizeof($menu_id[$m]); $i++) {
                for ($j = 0; $j < sizeof($menu_id[$m][$i]); $j++) {
                    $dataStore = array(
                        'role_id' => $role_id,
                        'menu_id' => $menu_id[$m][$i][$j],
                        'can_access' => (!empty($read[$m][$i][$j]) ? $read[$m][$i][$j] : 0),
                        'can_create' => (!empty($create[$m][$i][$j]) ? $create[$m][$i][$j] : 0),
                        'can_edit' => (!empty($update[$m][$i][$j]) ? $update[$m][$i][$j] : 0),
                        'can_delete' => (!empty($delete[$m][$i][$j]) ? $delete[$m][$i][$j] : 0),
                        'createby' => $this->user_id,
                        'createdate' =>date("Y-m-d"),

                    );
                    array_push($new_array, $dataStore);
                }
            }
        }


        if ($this->Menu_model->role_create($new_array)) {
            $this->session->set_flashdata('success', "<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button>Dados atualizados com sucesso!");
        } else {
            $this->session->set_flashdata('error', "<div class='alert alert-danger msg'>Por favor, tente novamente</div>");
        }
        redirect('menu/crole/role_list');



}

public function delete($id){
    $data=array(
        'role_id' =>$id,
    );
    $this->db->where('role_id',$data['role_id']);
    $this->db->delete('sec_role_tbl',$data);
    $this->session->set_flashdata('success', 'Excluído com sucesso');
    redirect('menu/crole/role_list');
}


public function role_assign(){
        $this->data['title']        = html_escape('Atribuir Função');
        $this->data['module']       = "menu";  
        $this->data['page']         = "role_assign"; 
        $this->data['role_list'] =$this->Menu_model->role_list(); 
        $this->data['user_list'] =$this->Menu_model->role_user_list(); 

        echo Modules::run('template/layout', $this->data); 
}

// ======== its for user role check ==========
    public function user_role_check(){
        $user_id = $this->input->post('user_id',TRUE);
        $check_user_role = $this->db->select('*')->from('sec_user_access_tbl a')
                        ->join('sec_role_tbl b', 'b.role_id = a.fk_role_id', 'left')
                        ->where('a.fk_user_id', $user_id)->get()->result();
        if (empty($check_user_role)) {
            $notFound = array(array('role_name' => 'Não Encontrado'));
            echo json_encode($notFound);
        } else {
            echo json_encode($check_user_role);
        }
    }


   public function assing_user_role_save(){
  
       $user_id = $this->input->post('user_id',TRUE);
       $role_id = $this->input->post('role_id',TRUE);
    
        $this->db->where('fk_user_id', $user_id)->delete('sec_user_access_tbl');
        
        for ($i = 0; $i < count($role_id); $i++) {
            $user_role = array(
                'fk_user_id' => $user_id,
                'fk_role_id' => $role_id[$i],
            );

            $this->db->insert('sec_user_access_tbl', $user_role);
        }
      
        $this->session->set_flashdata('success', "<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button>Função do usuário atribuída com sucesso!");
       redirect('menu/crole/role_assign');
    }




    public function assigned_role_list(){
        $this->data['title']        =  html_escape('Lista de Funções Atribuídas');
        $this->data['module']       = "menu";  
        $this->data['page']         = "assigned_userrole_list"; 
        $this->data['role_list'] =$this->Menu_model->role_list(); 
        $this->data['sec_user_access_tbl'] =$this->Menu_model->user_access_role(); 

        echo Modules::run('template/layout', $this->data); 

    }

    //============ its for edit_user_access_role =============
    public function edit_assigned_role($access_id) {

        $this->data['title']        = html_escape('Editar Função Atribuída');
        $this->data['module']       = "menu";  
        $this->data['page']         = "edit_user_access_role"; 
        $this->data['role_list'] =$this->Menu_model->role_list(); 
        $this->data['user_list'] =$this->Menu_model->role_user_list();
        $this->data['edit_user_access_role'] = $this->Menu_model->edit_user_access_role($access_id);
        $this->data['assign_role'] = $this->db->select('fk_role_id')
                        ->where('fk_user_id',$this->data['edit_user_access_role'][0]->fk_user_id)
                        ->get('sec_user_access_tbl')->result();
    
        echo Modules::run('template/layout', $this->data);
    }
    //=========== its for assign_user_role_update ===========
    public function assign_user_role_update($role_acc_id) {
        $user_id = $this->input->post('user_id',TRUE);
        $role_id = $this->input->post('role_id',TRUE);

        $this->db->where('fk_user_id', $user_id)->delete('sec_user_access_tbl');
        for ($i = 0; $i < count($role_id); $i++) {
            $user_role = array(
                'fk_user_id' => $user_id,
                'fk_role_id' => $role_id[$i],
            );
            $this->db->insert('sec_user_access_tbl', $user_role);
        }
        $this->session->set_flashdata('success', "<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button>Função do usuário atualizada com sucesso!");
        redirect('menu/crole/assigned_role_list');
    }




}
