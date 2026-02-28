<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Home_model extends CI_Model {


	public function checkUser($data = array()){
		
		return $this->db->select("
				user.id, 
				CONCAT_WS(' ', user.firstname, user.lastname) AS fullname,
				user.email, 
				user.image, 
				user.last_login,
				user.last_logout, 
				user.ip_address, 
				user.status, 
				user.is_admin, 
				IF (user.is_admin=1, 'Admin', 'User') as user_level
			")
			->from('user')
			->where('email', $data['email'])
			->where('password', md5($data['password']))
			->get();
	}



	public function userPermission($id = null){

		return $this->db->select("
			module.controller, 
			module_permission.fk_module_id, 
			module_permission.create, 
			module_permission.read, 
			module_permission.update, 
			module_permission.delete
			")
			->from('module_permission')
			->join('module', 'module.id = module_permission.fk_module_id', 'full')
			->where('module_permission.fk_user_id', $id)
			->get()
			->result();
	}




	public function last_login($id = null){

		return $this->db->set('last_login', date('Y-m-d H:i:s'))
			->set('ip_address', $this->input->ip_address())
			->where('id',$this->session->userdata('id'))
			->update('user');
	}



	public function last_logout($id = null){

		return $this->db->set('last_logout', date('Y-m-d H:i:s'))
			->where('id', $this->session->userdata('id'))
			->update('user');
	}



	public function profile($id = null){

		$result = $this->db->select("
			user.*, 
				CONCAT_WS(' ', user.firstname, user.lastname) AS fullname,
				employee_tbl.*,
				department_tbl.department_name,
				designation_tbl.designation_name
			")
			->from("user")
			->join('employee_tbl','employee_tbl.em_email=user.email','left')
			->join('department_tbl','department_tbl.department_id=employee_tbl.em_department','left')
			->join('designation_tbl','designation_tbl.designation_id=employee_tbl.em_designation','left')
			->where("user.id", $id)->get()->row();
		return $result;
	}


	public function setting($data = array()){

		return $this->db->where('id', $data['id'])
			->update('user', $data);
	}

}
 