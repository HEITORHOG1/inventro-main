<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Designation_model extends CI_Model {


	public function get_all_designation()
	{
		$result = $this->db->get('designation_tbl')->result();

		return $result;
	}


	public function get_designation_single($designation_id)
	{
		$result = $this->db->where('designation_id',$designation_id)->get('designation_tbl')->row();

		return $result;
	}

}