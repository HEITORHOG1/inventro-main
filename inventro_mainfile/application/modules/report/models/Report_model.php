<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Report_model extends CI_Model
{


    public function getPurchaseList($postData = null)
    {
        $response = array();
        $fromdate = $this->input->post('fromdate',TRUE);
        $todate = $this->input->post('todate',TRUE);
        $supplier_id = $this->input->post('supplier_id',TRUE);
        if (!empty($fromdate)) {
            $datbetween = "(a.supplier_id='$supplier_id'  AND a.purchase_date BETWEEN '$fromdate' AND '$todate')";
        } else {
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
        if ($searchValue != '') {
            $searchQuery = " (b.name like '%" . $searchValue . "%' or a.chalan_no like '%" . $searchValue . "%' or a.purchase_date like'%" . $searchValue . "%')";
        }

        ## Total number of records without filtering
        $this->db->select('count(*) as allcount');
        $this->db->from('product_purchase a');
        $this->db->join('supplier_tbl b', 'b.supplier_id = a.supplier_id', 'left');
        if (!empty($fromdate) && !empty($todate)) {
            $this->db->where($datbetween);
        }
        if ($searchValue != '')
            $this->db->where($searchQuery);

        $records = $this->db->get()->result();
        $totalRecords = $records[0]->allcount;

        ## Total number of record with filtering
        $this->db->select('count(*) as allcount');
        $this->db->from('product_purchase a');
        $this->db->join('supplier_tbl b', 'b.supplier_id = a.supplier_id', 'left');
        if (!empty($fromdate) && !empty($todate)) {
            $this->db->where($datbetween);
        }
        if ($searchValue != '')
            $this->db->where($searchQuery);

        $records = $this->db->get()->result();
        $totalRecordwithFilter = $records[0]->allcount;

        ## Fetch records
        $this->db->select('a.*,b.name as supplier_name');
        $this->db->from('product_purchase a');
        $this->db->join('supplier_tbl b', 'b.supplier_id = a.supplier_id', 'left');
        if (!empty($fromdate) && !empty($todate)) {
            $this->db->where($datbetween);
        }
        if ($searchValue != '')
            $this->db->where($searchQuery);
        $this->db->order_by($columnName, $columnSortOrder);
        $this->db->limit($rowperpage, $start);
        $records = $this->db->get()->result();
        $data = array();
        $sl = 1;
        foreach ($records as $record) {
            $base_url = base_url();
            $purchasedetails1 = '<a href="' . $base_url . 'purchase/purchase/purchase_details/' . $record->purchase_id . '">' . $record->chalan_no . '</a>';
            $purchasedetails2 = '<a href="' . $base_url . 'purchase/purchase/purchase_details/' . $record->purchase_id . '">' . $record->purchase_id . '</a>';

            $data[] = array(
                'sl' => $sl,
                'chalan_no' => $purchasedetails1,
                'purchase_id' => $purchasedetails2,
                'supplier_name' => $record->supplier_name,
                'purchase_date' => $record->purchase_date,
                'total_amount' => $record->grand_total_amount,

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

    public function supplier_list()
    {
        $this->db->select('*');
        $this->db->from('supplier_tbl');
        $query = $this->db->get();
        $data = $query->result();

        $list[''] = 'Select Supplier';
        if (!empty($data)) {
            foreach ($data as $value) {
                $list[$value->supplier_id] = $value->name;
            }
        }
        return $list;
    }

    public function getSalesList($postData = null)
    {
        $response = array();
        $fromdate = $this->input->post('fromdate',TRUE);
     
        $todate = $this->input->post('todate',TRUE);

        $customer_id = $this->input->post('customer_id',TRUE);
         
        if (!empty($fromdate)) {
            $datbetween = "(a.customer_id='$customer_id'  AND a.date BETWEEN '$fromdate' AND '$todate')";
        } else {
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
        if ($searchValue != '') {
            $searchQuery = " (b.name like '%" . $searchValue . "%' or a.invoice_id like '%" . $searchValue . "%' or a.date like'%" . $searchValue . "%')";
        }

        ## Total number of records without filtering
        $this->db->select('count(*) as allcount');
        $this->db->from('invoice_tbl a');
        $this->db->join('customer_tbl b', 'b.customerid = a.customer_id');
        if (!empty($fromdate) && !empty($todate)) {
            $this->db->where($datbetween);
        }
        if ($searchValue != '')
            $this->db->where($searchQuery);

        $records = $this->db->get()->result();
        $totalRecords = $records[0]->allcount;

        ## Total number of record with filtering
        $this->db->select('count(*) as allcount');
        $this->db->from('invoice_tbl a');
        $this->db->join('customer_tbl b', 'b.customerid = a.customer_id');
        if (!empty($fromdate) && !empty($todate)) {
            $this->db->where($datbetween);
        }
        if ($searchValue != '')
            $this->db->where($searchQuery);

        $records = $this->db->get()->result();
        $totalRecordwithFilter = $records[0]->allcount;

        ## Fetch records
        $this->db->select('a.*,b.name as customer_name');
        $this->db->from('invoice_tbl a');
        $this->db->join('customer_tbl b', 'b.customerid = a.customer_id');
        if (!empty($fromdate) && !empty($todate)) {
            $this->db->where($datbetween);
        }
        if ($searchValue != '')
            $this->db->where($searchQuery);
        $this->db->order_by($columnName, $columnSortOrder);
        $this->db->limit($rowperpage, $start);
        $records = $this->db->get()->result();

        $data = array();
        $sl = 1;
        foreach ($records as $record) {
            $base_url = base_url();
            $customer = '<a href="' . $base_url . 'customer/customer_info/singleledgerbycustomer/' . $record->customer_id . '">' . $record->customer_name . '</a>';

            $data[] = array(
                'sl' => $sl,
                'invoice_id' =>  $record->invoice_id,
                'customer_name' => $customer,
                'date'          => $record->date,
                'total_amount' => $record->total_amount,

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

    public function customer_list()
    {
        $this->db->select('*');
        $this->db->from('customer_tbl');
        $query = $this->db->get();
        $data = $query->result();

        $list[''] = 'Select Customer';
        if (!empty($data)) {
            foreach ($data as $value) {
                $list[$value->customerid] = $value->name;
            }
        }
        return $list;
    }

    public function cash_book_list()
    {
        $this->db->select('a.*,b.name');
        $this->db->from('ledger_tbl a');
        $this->db->join('customer_tbl b','a.ledger_id=b.id');
        $this->db->where('a.ledger_id',1);
        $query = $this->db->get();
        $data = $query->result();
        return $data;
    }

    public function getCashBookReports($postData = null)
    {
        $response = array();
        $fromdate = $this->input->post('fromdate',TRUE);
        $todate = $this->input->post('todate',TRUE);
        if (!empty($fromdate)) {
            $datbetween = "(ledger_id= 1  AND date BETWEEN '$fromdate' AND '$todate')";
        } else {
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


        ## Total number of records without filtering
        $this->db->select('count(*) as allcount');
        $this->db->from('ledger_tbl');
        $this->db->where('ledger_id',1);
        if (!empty($fromdate) && !empty($todate)) {
            $this->db->where($datbetween);
        }

        $records = $this->db->get()->result();
        $totalRecords = $records[0]->allcount;

        ## Total number of record with filtering
        $this->db->select('count(*) as allcount');
        $this->db->from('ledger_tbl');
        $this->db->where('ledger_id',1);
        if (!empty($fromdate) && !empty($todate)) {
            $this->db->where($datbetween);
        }

        $records = $this->db->get()->result();
        $totalRecordwithFilter = $records[0]->allcount;

        ## Fetch records
        $this->db->select('*');
        $this->db->from('ledger_tbl');
        $this->db->where('ledger_id',1);
        if (!empty($fromdate) && !empty($todate)) {
            $this->db->where($datbetween);
        }
        $this->db->order_by($columnName, $columnSortOrder);
        $this->db->limit($rowperpage, $start);
        $records = $this->db->get()->result();
        $data = array();
        $sl = 1;
        foreach ($records as $record) {
            $base_url = base_url();

            $payment = $this->db->select('amount as payment')->from('ledger_tbl')->where('id',$record->id)->where('d_c','c')->get()->row();
            $receive = $this->db->select('amount as receive')->from('ledger_tbl')->where('id',$record->id)->where('d_c','d')->get()->row();
            $data[] = array(
                'sl' => $sl,
                'date' => $record->date,
                'description' => $record->description,
                'payment' => (!empty($payment->payment)?$payment->payment:0),
                'receive' =>  (!empty($receive->receive)?$receive->receive:0),

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

 public function getBankBookreport($postData = null)
    {
        $response = array();
        $fromdate = $this->input->post('fromdate',TRUE);
        $todate = $this->input->post('todate',TRUE);
        $bank_id = $this->input->post('bank_id',TRUE);
        

        if (!empty($fromdate)) {
            $datbetween = "(a.date BETWEEN '$fromdate' AND '$todate' )";
        } else {
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
        if ($searchValue != '') {
            $searchQuery = " (b.bank_name like '%" . $searchValue . "%' or a.date like '%" . $searchValue . "%')";
        }


        ## Total number of records without filtering
        $this->db->select('count(*) as allcount');
        $this->db->from('ledger_tbl a');
        $this->db->join('bank_tbl b','a.ledger_id=b.bank_id');
        if (!empty($fromdate) && !empty($todate)) {
            $this->db->where($datbetween);
        }

    

        $records = $this->db->get()->result();
        $totalRecords = $records[0]->allcount;

        ## Total number of record with filtering
        $this->db->select('count(*) as allcount');
        $this->db->from('ledger_tbl a');
        $this->db->join('bank_tbl b','a.ledger_id=b.bank_id');
        if (!empty($fromdate) && !empty($todate)) { 
            $this->db->where($datbetween);
        }

      

        $records = $this->db->get()->result();
        $totalRecordwithFilter = $records[0]->allcount;

        ## Fetch records
        $this->db->select('a.*,b.bank_name');
        $this->db->from('ledger_tbl a');
        $this->db->join('bank_tbl b','a.ledger_id=b.bank_id');
        
        if (!empty($fromdate) && !empty($todate)) {
            $this->db->where($datbetween);
        }


        $this->db->order_by($columnName, $columnSortOrder);
        $this->db->limit($rowperpage, $start);
        $records = $this->db->get()->result();
        
        $data = array();
        $sl = 1;

          $balance = 0;
        foreach ($records as $record) {
            $base_url = base_url();

            $deposit = $this->db->select('amount as deposit')->from('ledger_tbl')->where('id',$record->id)->where('d_c','d')->get()->row();
            $witdraw = $this->db->select('amount as witdraw')->from('ledger_tbl')->where('id',$record->id)->where('d_c','c')->get()->row();
            $balance += (!empty($deposit)?$deposit->deposit:0) - (!empty($witdraw)?$witdraw->witdraw:0);
            $data[] = array(
                'sl' => $sl,
                'bank_name' => $record->bank_name,
                'date' => $record->date,
                'deposit' => (!empty($deposit)?$deposit->deposit:0),
                'witdraw' =>  (!empty($witdraw)?$witdraw->witdraw:0),
                'balance' => $balance

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



    public function bank_book_list()
    {
        $this->db->select('a.*');
        $this->db->from('bank_tbl a');
        $query = $this->db->get();
        $data = $query->result_array();
        return $data;
    }



}