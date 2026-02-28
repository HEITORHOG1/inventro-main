<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Unit_model extends CI_Model {
 
    public function unit_list()
	{
		return $this->db->select('*')	
			->from('product_unit')
			->order_by('id', 'desc')
			->get()
			->result();
	}
	public function create($data = array())
	{
		return $this->db->insert('product_unit', $data);
	}

	public function delete($id = null)
	{
		$this->db->where('id',$id)
			->delete('product_unit');

		if ($this->db->affected_rows()) {
			return true;
		} else {
			return false;
		}
	} 

public function update($data = array())
	{
		return $this->db->where('id', $data["id"])
			->update("product_unit", $data);
	}
	public function findById($id){
        $this->db->where('id',$id);
        $query = $this->db->get('product_unit');
        return $query->row();
    }


}
