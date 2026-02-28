<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model {
 

	public function role()
	{
		return $this->db->select('role_name,role_id')
		->from('sec_role_tbl')
		->get()->result();
	}


	public function create($data = array())
	{
		return $this->db->insert('user', $data);
	}



	public function read($limit=null,$start=null)
	{
		
		$this->db->select("
			user.*, 
			CONCAT_WS(' ', firstname, lastname) AS fullname
		");

		$this->db->from('user');
		$this->db->limit($limit,$start);
		$this->db->order_by('user.id', 'desc');
		return $this->db->get()->result();
	}



	public function single($id = null)
	{
		return $this->db->select('user.*,sec_user_access_tbl.*')
			->from('user')
			->join('sec_user_access_tbl','sec_user_access_tbl.fk_user_id=user.id','left')
			->where('id', $id)
			->get()
			->row();
	}

	public function update($data = array())
	{
		return $this->db->where('id', $data["id"])
			->update("user", $data);
	}

	public function delete($id = null)
	{
		return $this->db->where('id', $id)
			->where_not_in('is_admin',1)
			->delete("user");
	}

	public function dropdown()
	{
		$data = $this->db->select("id, CONCAT_WS(' ', firstname, lastname) AS fullname")
			->from("user")
			->where('status', 1)
			->where_not_in('is_admin', 1)
			->get()
			->result();
		$list[''] = makeString(['select_option']);
		if (!empty($data)) {
			foreach($data as $value)
				$list[$value->id] = $value->fullname;
			return $list;
		} else {
			return false; 
		}
	}



    public function total_count($search=null,$searchQuery=null){

        
			$this->db->select("
				user.*, 
				CONCAT_WS(' ', firstname, lastname) AS fullname
			");
			$this->db->from('user');
			
			if($searchQuery != '')
            	$this->db->where($searchQuery);
        	$totalRecords = $this->db->get()->num_rows();

        	return $totalRecords;


    }




	public function get_user_list($postData=null,$search=null){


        $response = array();
        ## Read value
        $draw       = @$postData['draw'];
        $start      = @$postData['start'];
        $rowperpage = @$postData['length']; // Rows makeString [per page
        $columnIndex = $postData['order'][0]['column']; // Column index
        $columnName = $postData['columns'][$columnIndex]['data']; // Column name
        $columnSortOrder = $postData['order'][0]['dir']; // asc or desc
        $searchValue = $postData['search']['value']; // Search value

        ## Search 
        $searchQuery = "";
        if($searchValue != ''){
           $searchQuery = " (user.firstname like '%".$searchValue."%' 
            or user.email like '%".$searchValue."%'
            or user.lastname like '%".$searchValue."%'
        ) ";
        }

        ## Total number of records without filtering
        $totalRecords = $this->total_count($search,$searchQuery);

        ## Total number of record with filtering
        $totalRecordwithFilter = $this->total_count($search,$searchQuery);
        
        ## Fetch records

        
			$this->db->select("
				user.*, 
				CONCAT_WS(' ', firstname, lastname) AS fullname
			");
			$this->db->from('user');
			
		if($searchQuery != '')
            $this->db->where($searchQuery);


        $this->db->order_by($columnName, $columnSortOrder);
        $this->db->limit($rowperpage, $start);
        $records = $this->db->get()->result();

        $data = array();
        
        foreach($records as $record ){

            $button = '';
            $button .= '<div class="btn-group">
                      <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown">'.makeString(["action"]).'</button>
                      <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown">
                        <span class="caret"></span>
                        <span class="sr-only">Toggle Dropdown</span>
                      </button>
                      <ul class="dropdown-menu" role="menu">';

                    if ($record->is_admin == 1) {
                        $button .= ' <button class="btn btn-info btn-sm" title="'.makeString(['admin']).'">'.makeString(['admin']).'</button>';
                    } else { 
                       	$button .= '<li><a href="'.base_url("dashboard/user/form/$record->id").'"><i class="fa fa-pencil text-info"></i> '.makeString(['edit']).'</a></li>';
                       	$button .= '<li><a href="'.base_url("dashboard/user/delete/$record->id").'" onclick="return confirm('.makeString(["are_you_sure"]).')"><i class="fa fa-trash-o text-danger"></i> Delete</a></li>';
                        
                    } 

                    $button .= '</ul></div>';
                    $checkAll = '<input type="checkbox" name="check_id[]" value="'.$record->id.'" id="check_id">';
                    $active_status = (($record->status==1)?makeString(['active']):makeString(['inactive']));

            $data[] = array( 
               "id"    =>$record->id,
               "checkAll"    =>$checkAll,
               "image"    =>'<img src="'.base_url(!empty($record->image)?$record->image:'admin_assets/img/icons/default.jpg').'" alt="Image" height="64" >',
               "fullname"    =>$record->fullname,
               "email"    =>$record->email,
               "last_login"    =>$record->last_login,
               "last_logout"    =>$record->last_logout,
               "ip_address"    =>$record->ip_address,
               "active_status"    =>$active_status,
               "action"    =>$button
            ); 
        }

        ## Response
        $response = array(
           "draw" => intval($draw),
           "iTotalRecords" => $totalRecordwithFilter,
           "iTotalDisplayRecords" => $totalRecords,
           "aaData" => $data
        );
        return $response; 


	}
 


}
