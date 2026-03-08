<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Bank_model extends CI_Model {
 
    public function bank_list()
	{
		return $this->db->select('*')	
			->from('bank_tbl')
			->order_by('id', 'desc')
			->get()
			->result();
	}
	public function create($data = array())
	{
		return $this->db->insert('bank_tbl', $data);
	}
	
	
	public function create_adjustment($data = array()){
	    return $this->db->insert('ledger_tbl', $data);
	}

	public function delete($id = null)
	{
		$this->db->where('id',$id)
			->delete('bank_tbl');

		if ($this->db->affected_rows()) {
			return true;
		} else {
			return false;
		}
	}


		public function getbankledger($postData=null){
         $response = array();
         $fromdate = $this->input->post('fromdate', TRUE);
         $todate   = $this->input->post('todate', TRUE);
         $bank_id = $this->input->post('bank_id', TRUE);

         ## Read value
         $draw = $postData['draw'];
         $start = $postData['start'];
         $rowperpage = $postData['length']; // Rows display per page
         $columnIndex = $postData['order'][0]['column']; // Column index
         $columnName = $postData['columns'][$columnIndex]['data']; // Column name
         $columnSortOrder = $postData['order'][0]['dir']; // asc or desc
         $searchValue = $postData['search']['value']; // Search value

         // Whitelist column names to prevent SQL injection via order
         $allowed_columns = array('sl', 'bank_name', 'description', 'date', 'debit', 'credit', 'balance');
         if (!in_array($columnName, $allowed_columns)) {
             $columnName = 'date';
         }
         $columnSortOrder = ($columnSortOrder === 'asc') ? 'asc' : 'desc';

         ## Total number of records without filtering
        $this->db->select('count(*) as allcount');
        $this->db->from('ledger_tbl a');
        $this->db->join('bank_tbl b', 'a.ledger_id = b.bank_id');
          if(!empty($fromdate) && !empty($todate)){
             $this->db->where('a.ledger_id', $bank_id);
             $this->db->where('a.date >=', $fromdate);
             $this->db->where('a.date <=', $todate);
         }
         if($searchValue != '') {
            $this->db->group_start();
            $this->db->like('b.bank_name', $searchValue);
            $this->db->or_like('a.date', $searchValue);
            $this->db->group_end();
         }
          $records = $this->db->get()->result();

         $totalRecords = $records[0]->allcount;

         ## Total number of record with filtering
        $this->db->select('count(*) as allcount');
        $this->db->from('ledger_tbl a');
        $this->db->join('bank_tbl b', 'a.ledger_id = b.bank_id');
         if(!empty($fromdate) && !empty($todate)){
             $this->db->where('a.ledger_id', $bank_id);
             $this->db->where('a.date >=', $fromdate);
             $this->db->where('a.date <=', $todate);
         }
         if($searchValue != '') {
            $this->db->group_start();
            $this->db->like('b.bank_name', $searchValue);
            $this->db->or_like('a.date', $searchValue);
            $this->db->group_end();
         }

          $records = $this->db->get()->result();

         $totalRecordwithFilter = $records[0]->allcount;

         ## Fetch records
        $this->db->select('a.*,b.bank_name,b.bank_id');
        $this->db->from('ledger_tbl a');
        $this->db->join('bank_tbl b', 'a.ledger_id = b.bank_id');
          if(!empty($fromdate) && !empty($todate)){
             $this->db->where('a.ledger_id', $bank_id);
             $this->db->where('a.date >=', $fromdate);
             $this->db->where('a.date <=', $todate);
         }
         if($searchValue != '') {
            $this->db->group_start();
            $this->db->like('b.bank_name', $searchValue);
            $this->db->or_like('a.date', $searchValue);
            $this->db->group_end();
         }
         $this->db->order_by($columnName, $columnSortOrder);
         $this->db->limit($rowperpage, $start);
         $records = $this->db->get()->result();
         $data = array();
         $sl =1;
         $balance = 0;
         foreach($records as $record ){
         $debit = $this->db->select('amount')->from('ledger_tbl')->where('id',$record->id)->where('d_c','d')->get()->row();
         $total_debit = (!empty($debit->amount)?$debit->amount:0);
         $credit = $this->db->select('amount')->from('ledger_tbl')->where('id',$record->id)->where('d_c','c')->get()->row();
         $total_credit = (!empty($credit->amount)?$credit->amount:0);
         $balance = $balance+($total_debit-$total_credit);
               
            $data[] = array( 
                'sl'               =>$sl,
                'bank_name'        =>$record->bank_name,
                'description'      =>$record->description,
                'date'             =>$record->date,
                'debit'            =>$total_debit,
                'credit'           =>$total_credit,
                'balance'          =>$balance
                
            ); 
            $sl++;
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

	


   

public function update($data = array())
	{
		return $this->db->where('id', $data["id"])
			->update("bank_tbl", $data);
	}
	public function findById($id){
        $this->db->where('id',$id);
        $query = $this->db->get('bank_tbl');
        return $query->row();
    }


 public function all_bank()
	{
		$this->db->select('*');
        $this->db->from('bank_tbl');
        $query = $this->db->get();
        $data = $query->result();
       
          $list[''] = 'Select Bank';
       	if (!empty($data) ) {
       		foreach ($data as $value) {
       			$list[$value->bank_id] = $value->bank_name;
       		} 
       	}
       	return $list;
	}


	public function individualledger($id){

		 $this->db->select('*');
         $this->db->from('ledger_tbl');
         $this->db->where('ledger_id',$id);
         $this->db->order_by('date','desc');
        return $records = $this->db->get()->result();

	}


public function bankdetails($id){
        $this->db->where('bank_id',$id);
        $query = $this->db->get('bank_tbl');
        return $query->row();
    }
}
