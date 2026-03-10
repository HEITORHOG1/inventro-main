<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Stock_model extends CI_Model {

    public function get_stock_list()
    {
        $this->db->select("
            a.id,
            a.name as product_name,
            a.product_id,
            c.name as category_name,
            a.unit,
            a.price,
            a.purchase_price,
            a.model,
            IFNULL(s.case_qty, 0) as stock_qty
        ", FALSE);
        $this->db->from('product_tbl a');
        $this->db->join('category_tbl c', 'c.id = a.category_id', 'left');
        $this->db->join('inv_stock s', 's.product_id = a.id', 'left');
        $this->db->order_by('a.id', 'asc');
        return $this->db->get()->result();
    }

    public function get_stock_list_supplier_wise($supplier_id = null)
    {
        $this->db->select("
            a.id,
            a.name as product_name,
            a.product_id,
            c.name as category_name,
            a.unit,
            a.price,
            a.purchase_price,
            a.model,
            IFNULL(s.case_qty, 0) as stock_qty
        ", FALSE);
        $this->db->from('product_tbl a');
        $this->db->join('category_tbl c', 'c.id = a.category_id', 'left');
        $this->db->join('inv_stock s', 's.product_id = a.id', 'left');

        if (!empty($supplier_id)) {
            $this->db->where('a.supplier_id', $supplier_id);
        }
        $this->db->order_by('a.id', 'asc');
        return $this->db->get()->result();
    }

    public function get_stock_list_product_wise($product_id = null)
    {
        $this->db->select("
            a.id,
            a.name as product_name,
            a.product_id,
            c.name as category_name,
            a.unit,
            a.price,
            a.purchase_price,
            a.model,
            IFNULL(s.case_qty, 0) as stock_qty
        ", FALSE);
        $this->db->from('product_tbl a');
        $this->db->join('category_tbl c', 'c.id = a.category_id', 'left');
        $this->db->join('inv_stock s', 's.product_id = a.id', 'left');
        if ($product_id) {
            $this->db->where('a.product_id', $product_id);
        }
        $this->db->order_by('a.id', 'asc');
        return $this->db->get()->result();
    }

    public function get_supplier_list()
    {
        return $this->db->get('supplier_tbl')->result();
    }

    public function get_supplier_by_id($supplier_id = null)
    {
        return $this->db->where('supplier_id', $supplier_id)->get('supplier_tbl')->row();
    }

    public function get_products_list()
    {
        return $this->db->select('product_id, name')->get('product_tbl')->result();
    }
}
