<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Account_model extends CI_Model {

    public function get_customers() {
        $customers = $this->db->select('customerid,name')->from('customer_tbl')->get()->result();
        return $customers;
    }

    public function get_suppliers() {
        $suppliers = $this->db->select('supplier_id,name')->from('supplier_tbl')->get()->result();
        return $suppliers;
    }

    public function get_banks() {
        $banks = $this->db->select('bank_id,bank_name')->from('bank_tbl')->get()->result();
        return $banks;
    }

//    ================ its for get_transaction_info =============
    public function get_transaction_info() {

        $this->db->select('a.*, c.name as customer_name, b.name as supplier_name');
        $this->db->from('ledger_tbl a');
        $this->db->join('supplier_tbl b', 'b.supplier_id = a.ledger_id', 'left');
        $this->db->join('customer_tbl c', 'c.customerid = a.ledger_id', 'left');
        $this->db->where('a.is_transaction', 1);
        $this->db->order_by('a.id', 'desc');
        $query = $this->db->get()->result();
        return $query;
    }

    //    =============== its for transaction_edit =============
    public function transaction_edit($transcation_id) {
        $this->db->select('*');
        $this->db->from('ledger_tbl');
        $this->db->where('transaction_id', $transcation_id);
        $this->db->where('is_transaction', 1);
        $query = $this->db->get()->row();
        return $query;
    }


    // cash data for closig
    public function cash_info(){
        $last_closing_day = $this->db->select('*')->from('daily_closing')->limit(1)->order_by('id','desc')->get()->row();

       
        $lastclose = (!empty($last_closing_day->date)?$last_closing_day->date:'');
        $today = date('Y-m-d');
             $this->db->select("sum(amount) as debit");
        $this->db->from('ledger_tbl');
        if(!empty($lastclose)){
         $this->db->where('date >=', $lastclose);    
        }
        $this->db->where('date <=', $today);
        $this->db->where('d_c','d');
        $debit = $this->db->get()->row();

        $this->db->select("sum(amount) as credit");
        $this->db->from('ledger_tbl');
        if(!empty($lastclose)){
         $this->db->where('date >=', $lastclose);    
        }
        $this->db->where('date <=', $today);
        $this->db->where('d_c','c');
        $credit = $this->db->get()->row();

        $balance  = (!empty($debit->debit)?$debit->debit:0) - (!empty($credit->credit)?$credit->credit:0);
        $total_balance  = $balance + (!empty($last_closing_day->amount)?$last_closing_day->amount:0);
        $data = array(
            'debit'   => (!empty($debit->debit)?$debit->debit:0),
            'credit'  => (!empty($credit->credit)?$credit->credit:0),
            'balance' => $total_balance,
            'closing_balance' => (!empty($last_closing_day->amount)?$last_closing_day->amount:0),
        );
     return $data;

    }

    public function check_closing_date($date){
        $this->db->select('*');
        $this->db->from('daily_closing');
        $this->db->where('date', $date);
        $query = $this->db->get()->num_rows();
        return $query;
    }

    public function closing_list(){
         $this->db->select('*');
        $this->db->from('daily_closing');
        $this->db->order_by('date', 'desc');
        $query = $this->db->get()->result();
        return $query;

    }

    public function delete($id = null)
    {
        $this->db->where('id',$id)
            ->delete('daily_closing');
        if ($this->db->affected_rows()) {
            return true;
        } else {
            return false;
        }
    } 


}
