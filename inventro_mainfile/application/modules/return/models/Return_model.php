<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Return_model extends CI_Model {
 
    public function customer_return($invoice)
    {
        return $this->db->select('*')
            ->from('product_purchase')
            ->order_by('purchase_date', 'desc')
            ->get()
            ->result();
    }

    public function supplier_return()
    {
        return $this->db->select('*')
            ->from('product_purchase')
            ->order_by('purchase_date', 'desc')
            ->get()
            ->result();
    }

    //    ============ its for edit_invoice ==========
    public function edit_invoice($invoice_id) {
        $query = $this->db->select('a.*,b.name as customer_name')
                        ->from('invoice_tbl a')
                        ->join('customer_tbl b','b.customerid=a.customer_id','left')
                        ->where('a.invoice_id', $invoice_id)
                        ->get()->row();
        return $query;
    }

    // count customer return
    public function sales_return_count(){
         $query = $this->db->select('*')
                        ->from('product_return')
                        ->where('status', 1)
                        ->order_by('return_date','desc')
                        ->get()->num_rows();
        return $query;
    }
//customer return list
    public function sales_return_list($offset = null, $limit = null){
          $query = $this->db->select('*')
                        ->from('product_return')
                        ->where('status', 1)
                        ->order_by('return_date','desc')
                        ->limit($offset, $limit)
                        ->get()->result();
        return $query;
    }

     public function supplier_return_count(){
         $query = $this->db->select('*')
                        ->from('product_return')
                        ->where('status', 2)
                        ->order_by('return_date','desc')
                        ->get()->num_rows();
        return $query;
    }
//customer return list
    public function supplier_return_list($offset = null, $limit = null){
          $query = $this->db->select('a.*,b.name')
                        ->from('product_return a')
                        ->join('supplier_tbl b','b.supplier_id = a.supplier_id')
                        ->where('a.status', 2)
                        ->order_by('a.return_date','desc')
                        ->limit($offset, $limit)
                        ->get()->result();
        return $query;
    }

     public function get_appsetting(){
        $this->db->select('*');
        $this->db->from('setting a');
        $this->db->join('tbl_currency b', 'b.currencyid = a.currency');
        $query = $this->db->get();
        return $query->row();
    }

    //=========== its for get customer ===========
    public function get_customer() {
        $this->db->select('*');
        $this->db->from('customer_tbl a');
        $this->db->order_by('a.id', 'desc');
        $query = $this->db->get()->result();
        return $query;
    }

    public function get_banks() {
        $banks = $this->db->select('bank_id,bank_name')->from('bank_tbl')->get()->result();
        return $banks;
    }

//    ======= its for get products =============
    public function get_products() {
        $query = $this->db->select('*')
                        ->from('product_tbl a')

                        ->order_by('id', 'desc')
                        ->get()->result();
        return $query;
    }



//=========== its for edit_invoicedetails ========
    public function edit_invoicedetails($invoice_id) {
        
        $query = $this->db->select('a.*, b.cartoon_qty,b.name')
                        ->from('invoice_details a')
                        ->join('product_tbl b', 'b.product_id = a.product_id', 'left')
                        ->where('a.invoice_id', $invoice_id)
                        ->get()->result();
        return $query;
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
            ->where('purchase_id', $id)
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

     public function supplier_list()
    {
        $this->db->select('*');
        $this->db->from('supplier_tbl');
        $query = $this->db->get();
        $data = $query->result();
       
          $list[''] = 'Select Supplier';
        if (!empty($data) ) {
            foreach ($data as $value) {
                $list[$value->supplier_id] = $value->name;
            } 
        }
        return $list;
    }

            public function get_customer_return_info($return_id) {
        $query = $this->db->select('a.*, b.name, b.mobile, b.email, b.address')
                        ->from('product_return a')
                        ->join('customer_tbl b', 'b.customerid = a.customer_id')
                        ->where('a.return_id', $return_id)
                        ->get()->row();
        return $query;
    }

//supplier return details
    public function get_supplier_return_info($return_id) {
        $query = $this->db->select('a.*, b.name, b.mobile, b.email, b.address')
                        ->from('product_return a')
                        ->join('supplier_tbl b', 'b.supplier_id = a.supplier_id')
                        ->where('a.return_id', $return_id)
                        ->get()->row();
        return $query;
    }

     public function get_return_details($return_id) {
        $query = $this->db->select('a.*, a.price as product_price, b.name')
                        ->from('return_details a')
                        ->join('product_tbl b', 'b.product_id = a.product_id')
                        ->where('a.return_id', $return_id)
                        ->get()->result();
        return $query;
    }


  }
