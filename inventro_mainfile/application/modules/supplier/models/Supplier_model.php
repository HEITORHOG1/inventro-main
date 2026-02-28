<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Supplier_model extends CI_Model {
	
	private $table = 'supplier_tbl';
 
	public function create($data = array())
	{
		return $this->db->insert($this->table, $data);
	}
	public function delete($id = null)
	{
		$this->db->where('id',$id)
			->delete($this->table);

		if ($this->db->affected_rows()) {
			return true;
		} else {
			return false;
		}
	} 



	public function update($data = array())
	{
		return $this->db->where('id',$data["id"])
			->update($this->table, $data);
	}
	public function update_trns($data = array())
	{
		return $this->db->where('id',$data["id"])
			->update('ledger_tbl', $data);
	}

    public function read($limit = null, $start = null)
	{
	   $this->db->select('*');
        $this->db->from($this->table);
        $this->db->order_by('id', 'desc');
        $this->db->limit($limit, $start);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result();    
        }
        return false;
	} 

	public function findById($id = null)
	{ 
		return $this->db->select("supplier_tbl.*,ledger_tbl.id as lid,ledger_tbl.transaction_id,ledger_tbl.amount,ledger_tbl.d_c")->from($this->table)->join('ledger_tbl','supplier_tbl.supplier_id=ledger_tbl.ledger_id','Left')
			->where('supplier_tbl.id',$id) 
			->get()
			->row();
	} 

 
public function countlist()
	{
		$this->db->select('*');
        $this->db->from($this->table);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->num_rows();  
        }
        return false;
	}
   private function get_supledger_query()
	{
			$column_order = array(null, 'name'); //set column field database for datatable orderable
			$column_search = array('name'); //set column field database for datatable searchable 
			$order = array('id' => 'asc');
		
		$cdate=date('Y-m-d');
		$this->db->select('*');
        $this->db->from('supplier_tbl');
		$i = 0;
	
		foreach ($column_search as $item) // loop column 
		{
			if($_POST['search']['value']) // if datatable send POST for search
			{
				if($i===0) // first loop
				{
					$this->db->like($item, $_POST['search']['value']);
				}
				else
				{
					$this->db->or_like($item, $_POST['search']['value']);
				}

				if(count($column_search) - 1 == $i) //last loop
					$this->db->group_end(); //close bracket
			}
			$i++;
		}
		
		if(isset($_POST['order'])) // here order processing
		{
			$this->db->order_by($column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
		} 
		else if(isset($order))
		{
			$order = $order;
			$this->db->order_by(key($order), $order[key($order)]);
		}
	}	
   public function get_supledger(){
    
        $this->get_supledger_query();
		if($_POST['length'] != -1)
		$this->db->limit($_POST['length'], $_POST['start']);
		$query = $this->db->get();
		return $query->result();
   }
   public function count_filtersupledger()
	{
		$this->get_supledger_query();
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_allsupledger()
	{
		$this->db->select('*');
        $this->db->from('supplier_tbl');
		return $this->db->count_all_results();
	} 
	public function supplierinfo($id = null){ 
		return $this->db->select("*")->from($this->table)->where('supplier_id',$id)->get()->row();
	} 
	public function companyinfo(){ 
		return $this->db->select("*")->from('setting')->get()->row();
	}
	public function ledgerdetails($id){
	    return $this->db->select("*")->from('ledger_tbl')->where('ledger_id',$id)->get()->result();
	}
    
}
