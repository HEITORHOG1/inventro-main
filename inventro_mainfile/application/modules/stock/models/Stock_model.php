<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Stock_model extends CI_Model {

    public function get_stock_list()
    {


 $this->db->select("a.name as product_name,a.product_id,c.name as category_name, a.unit,a.price,a.purchase_price,a.model,IFNULL(sum(b.quantity),0) as total_sales_quantity,(select IFNULL(sum(product_purchase_details.quantity),0) from product_purchase_details where product_id = `a`.`product_id`) as 'total_purchase_quantity'");
        $this->db->from('product_tbl a');
        $this->db->join('invoice_details b', 'b.product_id = a.product_id','left');
        $this->db->join('category_tbl c', 'c.category_id = a.category_id');
        $this->db->group_by('a.product_id');
        $this->db->order_by('a.product_id', 'desc');       
        $query = $this->db->get();
        return $query->result();

    }


    public function get_stock_list_supplier_wise($supplier_id=null)
    {


         $this->db->select("a.name as product_name,a.product_id,c.name as category_name, a.unit,a.price,a.purchase_price,a.model");
        $this->db->from('product_tbl a');
        $this->db->join('category_tbl c', 'c.category_id = a.category_id','left');
            
        if (!empty($supplier_id)) {      
          
            $this->db->where('a.supplier_id', $supplier_id);
        }
         $this->db->group_by('a.product_id');
        $this->db->order_by('a.product_id', 'desc'); 

        $query = $this->db->get();
        return $query->result();

    }


      public function get_supplier_wise_single_stock($product_id){
        
        $this->db->select(
            "IFNULL(sum(f.return_qty),0) as stock");
        $this->db->from('return_details f');
        $this->db->where('f.product_id', $product_id);
        $this->db->where('f.status', 1);
        $query = $this->db->get();
        return $query->row();
      }


    public function get_stock_list_product_wise($product_id=null)
    {

           $this->db->select("a.name as product_name,a.product_id,c.name as category_name, a.unit,a.price,a.purchase_price,a.model,IFNULL(sum(b.quantity),0) as total_sales_quantity,(select IFNULL(sum(product_purchase_details.quantity),0) from product_purchase_details where product_id = `a`.`product_id`) as 'total_purchase_quantity'");
        $this->db->from('product_tbl a');
        $this->db->join('invoice_details b', 'b.product_id = a.product_id','left');
        $this->db->join('category_tbl c', 'c.category_id = a.category_id');
        $this->db->group_by('a.product_id');
        $this->db->order_by('a.product_id', 'desc'); 
        if ($product_id) {
            $this->db->where('a.product_id', $product_id);
        }
        $query = $this->db->get();
        return $query->result();
    }


    public function get_supplier_list()
    {
      return  $this->db->select('*')->from('supplier_tbl')->get()->result();
    }


    public function get_supplier_by_id($supplier_id=null)
    {
       return $this->db->select('*')->from('supplier_tbl')->where('supplier_id', $supplier_id)->get()->row();
    }


    public function get_products_list()
    {
        return $this->db->select('product_id,name')->from('product_tbl')->get()->result();

    }
}