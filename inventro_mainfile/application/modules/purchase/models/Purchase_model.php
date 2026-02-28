<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Purchase_model extends CI_Model {
 
    public function read()
	{
		return $this->db->select('*')	
			->from('product_purchase')
			->order_by('bank_name', 'asc')
			->get()
			->result();
	}
	public function create_purchase($data = array())
	{
        return $this->db->insert('product_purchase',$data);
	}

	public function delete($id = null)
	{
		$this->db->where('purchase_id',$id)
			->delete('product_purchase');
			//purchase details
			$this->db->where('purchase_id',$id)
			->delete('product_purchase_details');
         
			$this->db->where('transaction_id',$id)
			->delete('ledger_tbl');
		if ($this->db->affected_rows()) {
			return true;
		} else {
			return false;
		}
	} 

    public function findById($id = null)
    { 
        return $this->db->select("*")->from("product_purchase")
            ->where('id',$id) 
            ->get()
            ->row();

    } 



public function update($data = []){

 $this->db->where('purchase_id', $data["purchase_id"])
            ->update("product_purchase", $data);
		
   $this->db->where('purchase_id',$data["purchase_id"])
         ->delete('product_purchase_details');

$this->db->where('transaction_id',$data["purchase_id"])
         ->delete('ledger_tbl');
        
       return true;

	}
 public function product_search_item($supplier_id, $product_name) {
      $query=$this->db->select('*')
                ->from('product_tbl')
                ->where('supplier_id',$supplier_id)
                ->like('model', $product_name, 'after')
                ->or_where('supplier_id',$supplier_id)
                ->like('name', $product_name, 'after')
                ->group_by('product_id')
                ->order_by('name','asc')
                ->limit(30)
                ->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();  
        }
        return false;
    }
	 	

    public function get_total_product($product_id, $supplier_id) {
        $this->db->select('SUM(a.quantity) as total_purchase,b.*');
        $this->db->from('product_purchase_details a');
        $this->db->join('product_tbl b', 'a.product_id=b.product_id');
        $this->db->where('a.product_id', $product_id);
        $this->db->where('b.supplier_id', $supplier_id);
        $total_purchase = $this->db->get()->row();
        // supplier return part 
         $this->db->select('SUM(return_qty) as total_return_out');
        $this->db->from('return_details');
        $this->db->where('product_id', $product_id);
        $this->db->where('status', 2);
        $supplier_return = $this->db->get()->row();

        $this->db->select('SUM(b.quantity) as total_sale');
        $this->db->from('invoice_details b');
        $this->db->where('b.product_id', $product_id);
        $total_sale = $this->db->get()->row();
        // customer return part
        $this->db->select('SUM(return_qty) as total_return_in');
        $this->db->from('return_details');
        $this->db->where('product_id', $product_id);
        $this->db->where('status', 1);
        $cutomrer_return = $this->db->get()->row();



        $this->db->select('*');
        $this->db->from('product_tbl a');
        $this->db->where(array('product_id' => $product_id));
        $this->db->where('supplier_id', $supplier_id);
        $product_information = $this->db->get()->row();
        $total_in = (!empty($total_purchase->total_purchase)?$total_purchase->total_purchase:0)+(!empty($cutomrer_return->total_return_in)?$cutomrer_return->total_return_in:0);

        $total_out = (!empty($total_sale->total_sale)?$total_sale->total_sale:0)+(!empty($supplier_return->total_return_out)?$supplier_return->total_return_out:0);
        $available_quantity = $total_in - $total_out;


        $data2 = array(
            'total_product'  => $available_quantity,
            'supplier_price' => $product_information->purchase_price,
            'price'          => $product_information->price,
            'supplier_id'    => $product_information->supplier_id,
            'unit'           => $product_information->unit,
            'cartoonqty'     => $product_information->cartoon_qty,
        );

        return $data2;
    }


     public function supplier_list()
	{
		$this->db->select('*');
        $this->db->from('supplier_tbl');
        $query = $this->db->get();
        $data = $query->result();
       
          $list[' '] = 'Select Supplier';
       	if (!empty($data) ) {
       		foreach ($data as $value) {
       			$list[$value->supplier_id] = $value->name;
       		} 
       	}
       	return $list;
	}


    public function bank_list(){
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


	  public function getPurchaseList($postData=null){
         $response = array();
         $fromdate = $this->input->post('fromdate');
         $todate   = $this->input->post('todate');
         if(!empty($fromdate)){
            $datbetween = "(a.purchase_date BETWEEN '$fromdate' AND '$todate')";
         }else{
            $datbetween = "";
         }
         ## Read value
         $draw = $postData['draw'];
         $start = $postData['start'];
         $rowperpage = $postData['length']; // Rows display per page
         $columnIndex = $postData['order'][0]['column']; // Column index
         $columnName = $postData['columns'][$columnIndex]['data']; // Column name
         $columnSortOrder = $postData['order'][0]['dir']; // asc or desc
         $searchValue = $postData['search']['value']; // Search value

         ## Search 
         $searchQuery = "";
         if($searchValue != ''){
            $searchQuery = " (b.name like '%".$searchValue."%' or a.chalan_no like '%".$searchValue."%' or a.purchase_date like'%".$searchValue."%')";
         }

         ## Total number of records without filtering
        $this->db->select('count(*) as allcount');
        $this->db->from('product_purchase a');
        $this->db->join('supplier_tbl b', 'b.supplier_id = a.supplier_id','left');
          if(!empty($fromdate) && !empty($todate)){
             $this->db->where($datbetween);
         }
          if($searchValue != '')
          $this->db->where($searchQuery);
          
         $records = $this->db->get()->result();
         $totalRecords = $records[0]->allcount;

         ## Total number of record with filtering
         $this->db->select('count(*) as allcount');
        $this->db->from('product_purchase a');
        $this->db->join('supplier_tbl b', 'b.supplier_id = a.supplier_id','left');
         if(!empty($fromdate) && !empty($todate)){
             $this->db->where($datbetween);
         }
         if($searchValue != '')
            $this->db->where($searchQuery);
          
         $records = $this->db->get()->result();
         $totalRecordwithFilter = $records[0]->allcount;

         ## Fetch records
        $this->db->select('a.*,b.name as supplier_name');
        $this->db->from('product_purchase a');
        $this->db->join('supplier_tbl b', 'b.supplier_id = a.supplier_id','left');
          if(!empty($fromdate) && !empty($todate)){
             $this->db->where($datbetween);
         }
         if($searchValue != '')
         $this->db->where($searchQuery);
         $this->db->order_by($columnName, $columnSortOrder);
         $this->db->limit($rowperpage, $start);
         $records = $this->db->get()->result();
         $data = array();
         $sl =1;
         foreach($records as $record ){
          $button = '';
          $base_url = base_url();
          $jsaction = "return confirm('Are You Sure ?')";

           $button .='  <a href="'.$base_url.'purchase/purchase/purchase_details/'.$record->purchase_id.'" class="btn btn-success btn-sm" data-toggle="tooltip" data-placement="left" title="'.display('purchase_details').'"><i class="fa fa-window-restore" aria-hidden="true"></i></a>';
      
         $button .=' <a href="'.$base_url.'purchase/purchase/purchase_edit_data/'.$record->purchase_id.'" class="btn btn-info btn-sm" data-toggle="tooltip" data-placement="left" title="'.'update'.'"><i class="fas fa-edit" aria-hidden="true"></i></a>';
     

                                  
           $button .= '<a href="'.$base_url.'purchase/purchase/delete/'.$record->purchase_id.'" class="btn btn-danger btn-sm"  data-toggle="tooltip" data-placement="left" title="'.'delete'.'"  onclick="'.$jsaction.'"><i class="fas fa-trash"></i></a>';
         
               
            $data[] = array( 
                'sl'               =>$sl,
                'chalan_no'        =>$record->chalan_no,
                'purchase_id'      =>$record->purchase_id,
                'supplier_name'    =>$record->supplier_name,
                'purchase_id'      =>$record->purchase_id,
                'purchase_date'    =>$record->purchase_date,
                'total_amount'     =>$record->grand_total_amount,
                'button'           =>$button,
                
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



public function purchase_details_data($purchase_id) {
        $this->db->select('a.*,b.name as supplier_name,c.*,e.purchase_details,d.product_id,d.name as product_name,d.model as product_model');
        $this->db->from('product_purchase a');
        $this->db->join('supplier_tbl b', 'b.supplier_id = a.supplier_id');
        $this->db->join('product_purchase_details c', 'c.purchase_id = a.purchase_id');
        $this->db->join('product_tbl d', 'd.product_id = c.product_id');
        $this->db->join('product_purchase e', 'e.purchase_id = c.purchase_id');
        $this->db->where('a.purchase_id', $purchase_id);
        $this->db->group_by('d.product_id');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }

//purchase edit data
       public function retrieve_purchase_editdata($purchase_id) {
        $this->db->select('a.*,
						b.*,
						c.product_id,
						c.name as product_name,
						c.model as product_model,
                        c.cartoon_qty,
						d.supplier_id,
						d.name as supplier_name'
        );
        $this->db->from('product_purchase a');
        $this->db->join('product_purchase_details b', 'b.purchase_id =a.purchase_id');
        $this->db->join('product_tbl c', 'c.product_id =b.product_id');
        $this->db->join('supplier_tbl d', 'd.supplier_id = a.supplier_id');
        $this->db->where('a.purchase_id', $purchase_id);
        $this->db->order_by('a.purchase_details', 'asc');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }

  }
