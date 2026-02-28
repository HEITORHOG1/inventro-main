<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Department_model extends CI_Model {


	public function get_all_salary()
	{
		$result = $this->db->get('salary_tbl')->result();

		return $result;
	}

	public function get_all_department()
	{
		$result = $this->db->get('department_tbl')->result();

		return $result;
	}


	public function get_department_single($department_id)
	{
		$result = $this->db->where('department_id',$department_id)->get('department_tbl')->row();

		return $result;
	}

}