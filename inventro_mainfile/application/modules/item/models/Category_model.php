<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Category_model extends CI_Model {
 
    public function category_list()
	{
		return $this->db->select('*')	
			->from('category_tbl')
			->order_by('id', 'desc')
			->get()
			->result();
	}
	public function create($data = array())
	{
		return $this->db->insert('category_tbl', $data);
	}

	public function delete($id = null)
	{
		$this->db->where('id',$id)
			->delete('category_tbl');

		if ($this->db->affected_rows()) {
			return true;
		} else {
			return false;
		}
	} 


   

public function update($data = array())
	{
		return $this->db->where('id', $data["id"])
			->update("category_tbl", $data);
	}
	public function findById($id){
        $this->db->where('id',$id);
        $query = $this->db->get('category_tbl');
        return $query->row();
    }


}
