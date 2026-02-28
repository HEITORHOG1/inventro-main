<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Employee_model extends CI_Model {


	public function get_all_employee()
	{
		$result = $this->db->select("employee_tbl.*, CONCAT_WS(' ', em_first_name, em_last_name) as employee_name")->where('status',1)->get('employee_tbl')->result(); 

		return $result;
	}


	public function get_country()
	{
		$result = $this->db->get('country_tbl')->result();

		return $result;
	}


	public function get_employee_single($employee_id)
	{
		$result = $this->db->where('employee_id',$employee_id)->get('employee_tbl')->row();

		return $result;
	}

}