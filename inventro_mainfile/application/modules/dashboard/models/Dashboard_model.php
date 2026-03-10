<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard_model extends CI_Model {
 
 public function yearly_invoice_report($month=null){

        $result = $this->db->query("
                            SELECT ROUND(COALESCE(SUM(total_amount), 0), 2) as total_sale FROM `invoice_tbl`
                            WHERE MONTH(date) = ?
                                AND YEAR(date) = YEAR(CURRENT_TIMESTAMP)
                            ", array((int)$month));

        return $result->row();
    }
public function yearly_purchase_report($month=null){

        $result = $this->db->query("
                            SELECT ROUND(COALESCE(SUM(grand_total_amount), 0), 2) as total_purchase FROM `product_purchase`
                            WHERE MONTH(purchase_date) = ?
                                AND YEAR(purchase_date) = YEAR(CURRENT_TIMESTAMP)
                            ", array((int)$month));

        return $result->row();
    }

      public function totalpurchase(){

    return $this->db->select('ROUND(COALESCE(SUM(grand_total_amount), 0), 2) as totalpurchase', FALSE)->from('product_purchase')->get()->row();

}
public function totalsale(){
    return $this->db->select('ROUND(COALESCE(SUM(total_amount), 0), 2) as totalsale', FALSE)->from('invoice_tbl')->get()->row();
}

public function currencyinfo(){
   return $this->db->select('a.*,b.*')->from('setting a')->join('tbl_currency b','b.currencyid = a.currency')->get()->row(); 
}


}
