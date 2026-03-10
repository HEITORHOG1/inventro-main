<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Invoice_model extends CI_Model {

    //============ its for sales list method ==============
    public function sales_list($offset = null, $limit = null) {
        $query = $this->db->select("a.*, b.name")
                        ->from('invoice_tbl a')
                        ->join('customer_tbl b', 'b.customerid = a.customer_id', 'left')
                        ->order_by('a.id', 'desc')
                        ->limit($offset, $limit)
                        ->get()->result();
 
        return $query;
    }

//=========== its for get customer ===========
    public function get_customer() {
        $this->db->select('*');
        $this->db->from('customer_tbl a');
        $this->db->order_by('a.id', 'desc');
        $query = $this->db->get()->result();
        return $query;
    }

    // ================== Category list =============
    public function get_categories(){
         $this->db->select('*');
        $this->db->from('category_tbl a');
        $this->db->order_by('a.name', 'asc');
        $query = $this->db->get()->result();
        return $query;
    }

//    ======= its for get products =============
    public function get_products() {
        $query = $this->db->select('*')
                        ->from('product_tbl a')
                        ->order_by('id', 'desc')
                        ->get()->result();
        return $query;
    }

    //    ======= item list for pos =============
    public function products_list() {
        $this->db->select('a.*, c.picture');
        $this->db->from('product_tbl a');
        $this->db->join('picture_tbl c', 'c.from_id = a.product_id', 'left');
        $this->db->order_by('a.name', 'asc');
        return $this->db->get()->result();
    }

    // ================== pos product search ==============
    public function searchprod($cid = null)
    {
        $this->db->select('a.*, c.picture');
        $this->db->from('product_tbl a');
        $this->db->join('picture_tbl c', 'c.from_id = a.product_id', 'left');
        if ($cid != 'all') {
            $this->db->where('a.category_id', (int) $cid);
        }
        $this->db->order_by('a.name', 'asc');
        return $this->db->get()->result();
    }

     // ================  Item Search By Name =============
    public function searchproductbyname($search){
      $this->db->select('a.*,c.picture');
        $this->db->from('product_tbl a');
        $this->db->join('picture_tbl c','c.from_id = a.product_id','left');
        $this->db->like('a.name',$search);
        $this->db->or_like('a.model',$search);
        $this->db->or_like('a.product_code',$search);
        $this->db->order_by('a.name','asc');
        $query = $this->db->get();
        $itemlist=$query->result();
        return $itemlist;
    }

//    ============ its for edit_invoice ==========
    public function edit_invoice($invoice_id) {
        $query = $this->db->select('*')
                        ->from('invoice_tbl a')
                        ->where('a.invoice_id', $invoice_id)
                        ->get()->row();
        return $query;
    }

//=========== its for edit_invoicedetails ========
    public function edit_invoicedetails($invoice_id) {
        $query = $this->db->select('a.*, b.cartoon_qty')
                        ->from('invoice_details a')
                        ->join('product_tbl b', 'b.id = a.product_id', 'left')
                        ->where('a.invoice_id', $invoice_id)
                        ->get()->result();
        return $query;
    }

//    =========== its for product information =========
    public function get_only_service_info($product_id) {
        $this->db->select('SUM(a.quantity) as total_purchase');
        $this->db->from('product_purchase_details a');
        $this->db->where('a.product_id', $product_id);
        $total_purchase = $this->db->get()->row();
        // customer return part
        $this->db->select('SUM(return_qty) as total_return_in');
        $this->db->from('return_details');
        $this->db->where('product_id', $product_id);
        $this->db->where('status', 1);
        $cutomrer_return = $this->db->get()->row();

        $this->db->select('SUM(b.quantity) as total_sale');
        $this->db->from('invoice_details b');
        $this->db->where('b.product_id', $product_id);
        $total_sale = $this->db->get()->row();
         // supplier return part 
         $this->db->select('SUM(return_qty) as total_return_out');
        $this->db->from('return_details');
        $this->db->where('product_id', $product_id);
        $this->db->where('status', 2);
        $supplier_return = $this->db->get()->row();

        $this->db->select('*');
        $this->db->from('product_tbl');
        $this->db->where(array('product_id' => $product_id));
        $product_information = $this->db->get()->row();
 
        $price = $product_information->price;
        $cartoon_qty = $product_information->cartoon_qty;

     $total_in = (!empty($total_purchase->total_purchase)?$total_purchase->total_purchase:0)+(!empty($cutomrer_return->total_return_in)?$cutomrer_return->total_return_in:0);

        $total_out = (!empty($total_sale->total_sale)?$total_sale->total_sale:0)+(!empty($supplier_return->total_return_out)?$supplier_return->total_return_out:0);
        $available_quantity = $total_in - $total_out;

        $result = array(
            'total_product' => $available_quantity,
            'price' => $price,
            'cartoon_qty' => $cartoon_qty,
        );

        return $result;
    }

    //    ========= its for invoice information ==========
    public function get_invoice_info($order_id) {
        $query = $this->db->select('a.*, b.name, b.mobile, b.email, b.address')
                        ->from('invoice_tbl a')
                        ->join('customer_tbl b', 'b.customerid = a.customer_id', 'left')
                        ->where('a.invoice_id', $order_id)
                        ->get()->row();
 
        return $query;
    }

    //    ========= its for invoice get_invoice_details ==========
    public function get_invoice_details($order_id) {
        $query = $this->db->select('a.*, a.price as product_price, b.name')
                        ->from('invoice_details a')
                        ->join('product_tbl b', 'b.id = a.product_id', 'left')
                        ->where('a.invoice_id', $order_id)
                        ->get()->result();
 
        return $query;
    }
//========= its for setting data =============
    public function get_appsetting(){
        $this->db->select('*');
        $this->db->from('setting a');
        $this->db->join('tbl_currency b', 'b.currencyid = a.currency');
        $query = $this->db->get();
        return $query->row();
    }


      public function pos_invoice_setup($product_id) {
        $product_information = $this->db->select('*')
                ->from('product_tbl')
                ->where('product_id', $product_id)
                ->get()
                ->row();

        if ($product_information != null) {

            $this->db->select('SUM(a.quantity) as total_purchase');
            $this->db->from('product_purchase_details a');
            $this->db->where('a.product_id', $product_id);
            $total_purchase = $this->db->get()->row();
            // customer return part
            $this->db->select('SUM(return_qty) as total_return_in');
            $this->db->from('return_details');
            $this->db->where('product_id', $product_id);
            $this->db->where('status', 1);
            $cutomrer_return = $this->db->get()->row();

            $this->db->select('SUM(b.quantity) as total_sale');
            $this->db->from('invoice_details b');
            $this->db->where('b.product_id', $product_id);
            $total_sale = $this->db->get()->row();
             // supplier return part 
            $this->db->select('SUM(return_qty) as total_return_out');
            $this->db->from('return_details');
            $this->db->where('product_id', $product_id);
            $this->db->where('status', 2);
            $supplier_return = $this->db->get()->row();

            $total_in = (!empty($total_purchase->total_purchase)?$total_purchase->total_purchase:0)+(!empty($cutomrer_return->total_return_in)?$cutomrer_return->total_return_in:0);

        $total_out = (!empty($total_sale->total_sale)?$total_sale->total_sale:0)+(!empty($supplier_return->total_return_out)?$supplier_return->total_return_out:0);
        $available_quantity = $total_in - $total_out;
          
          $data2 = (object) array(
                        'total_product'  => $available_quantity,
                        'supplier_price' => $product_information->purchase_price,
                        'price'          => $product_information->price,
                        'product_id'     => $product_information->product_id,
                        'product_name'   => $product_information->name,
                        'product_model'  => $product_information->model
            );

        

            return $data2;
        } else {
            return false;
        }
    }
}
