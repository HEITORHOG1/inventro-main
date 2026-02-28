<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Menu_model extends CI_Model {
    public function create($data = array()){
		return $this->db->insert('sec_menu_item', $data);
	}

	
    public function menu_list($data = array()){
        $result = $this->db->select('*')
                               ->from('sec_menu_item')
                               ->get()
                               ->result();
          return $result;        
    }       


   public function menu_edit($data = array()){
         $result = $this->db->select('*')
                  ->from('sec_menu_item')
                  ->where('menu_id',$data)
                  ->get()
                  ->row();   
            return $result;
    }    

  public function update_menu($data = array()){
  $result= $this->db->where('menu_id', $data['menu_id'])
                             ->update('sec_menu_item', $data) ;
    return $result;
    }   



    
  public function role_create($data = array()){
      $this->db->where('role_id', $data[0]['role_id'])->delete('sec_role_permission');
      return $this->db->insert_batch('sec_role_permission', $data);
  }
	
 public function role_list() {
        $query = $this->db->select('*')
                        ->from('sec_role_tbl')
                        ->get()->result();
        return $query;
   }
	

 public function role_user_list() {
        $query = $this->db->select('*')
                        ->from('user')
                        ->get()->result();
        return $query;
   }
public function user_access_role() {

    $this->db->select('a.role_acc_id, a.fk_user_id,  a.fk_role_id, b.firstname,b.lastname, c.role_name');
    $this->db->from('sec_user_access_tbl a');
    $this->db->join('user b', 'b.id = a.fk_user_id');
    $this->db->join('sec_role_tbl c', 'c.role_id = a.fk_role_id');
    $this->db->order_by('a.role_acc_id', 'desc');
    $this->db->group_by('a.fk_user_id');
    $query = $this->db->get();
    return $query->result();
}

//    ============ its for  edit_user_access_role ========= 
  public function edit_user_access_role($access_id) {
      $query = $this->db->select('*')
              ->from('sec_user_access_tbl a')
              ->where('a.role_acc_id', $access_id)
              ->get();
      if ($query->num_rows() > 0) {
          return $query->result();
      } else {
          return false;
      }
  }

}