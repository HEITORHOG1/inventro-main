<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Report_model extends CI_Model
{

    public function getPurchaseList($postData = null)
    {
        $fromdate = $this->input->post('fromdate', TRUE);
        $todate = $this->input->post('todate', TRUE);
        $supplier_id = $this->input->post('supplier_id', TRUE);

        $draw = $postData['draw'];
        $start = $postData['start'];
        $rowperpage = $postData['length'];
        $columnIndex = $postData['order'][0]['column'];
        $columnName = $postData['columns'][$columnIndex]['data'];
        $columnSortOrder = ($postData['order'][0]['dir'] === 'desc') ? 'desc' : 'asc';
        $searchValue = $postData['search']['value'];

        $sortMap = [
            'sl' => 'a.purchase_id', 'chalan_no' => 'a.chalan_no', 'purchase_id' => 'a.purchase_id',
            'supplier_name' => 'b.name', 'purchase_date' => 'a.purchase_date', 'total_amount' => 'a.grand_total_amount',
        ];
        $orderColumn = isset($sortMap[$columnName]) ? $sortMap[$columnName] : 'a.purchase_date';

        // Total without filtering
        $this->db->select('count(*) as allcount')->from('product_purchase a');
        if (!empty($fromdate) && !empty($todate)) {
            $this->db->where('a.supplier_id', $supplier_id);
            $this->db->where('a.purchase_date >=', $fromdate);
            $this->db->where('a.purchase_date <=', $todate);
        }
        $totalRecords = $this->db->get()->row()->allcount;

        // Total with filtering
        $this->db->select('count(*) as allcount')->from('product_purchase a');
        $this->db->join('supplier_tbl b', 'b.supplier_id = a.supplier_id', 'left');
        if (!empty($fromdate) && !empty($todate)) {
            $this->db->where('a.supplier_id', $supplier_id);
            $this->db->where('a.purchase_date >=', $fromdate);
            $this->db->where('a.purchase_date <=', $todate);
        }
        if ($searchValue != '') {
            $this->db->group_start();
            $this->db->like('b.name', $searchValue);
            $this->db->or_like('a.chalan_no', $searchValue);
            $this->db->or_like('a.purchase_date', $searchValue);
            $this->db->group_end();
        }
        $totalRecordwithFilter = $this->db->get()->row()->allcount;

        // Fetch records
        $this->db->select('a.*, b.name as supplier_name');
        $this->db->from('product_purchase a');
        $this->db->join('supplier_tbl b', 'b.supplier_id = a.supplier_id', 'left');
        if (!empty($fromdate) && !empty($todate)) {
            $this->db->where('a.supplier_id', $supplier_id);
            $this->db->where('a.purchase_date >=', $fromdate);
            $this->db->where('a.purchase_date <=', $todate);
        }
        if ($searchValue != '') {
            $this->db->group_start();
            $this->db->like('b.name', $searchValue);
            $this->db->or_like('a.chalan_no', $searchValue);
            $this->db->or_like('a.purchase_date', $searchValue);
            $this->db->group_end();
        }
        $this->db->order_by($orderColumn, $columnSortOrder);
        $this->db->limit($rowperpage, $start);
        $records = $this->db->get()->result();

        $data = array();
        $sl = $start + 1;
        $base_url = base_url();
        foreach ($records as $record) {
            $pid = htmlspecialchars($record->purchase_id, ENT_QUOTES, 'UTF-8');
            $data[] = array(
                'sl'            => $sl,
                'chalan_no'     => '<a href="' . $base_url . 'purchase/purchase/purchase_details/' . $pid . '">' . htmlspecialchars($record->chalan_no, ENT_QUOTES, 'UTF-8') . '</a>',
                'purchase_id'   => '<a href="' . $base_url . 'purchase/purchase/purchase_details/' . $pid . '">' . $pid . '</a>',
                'supplier_name' => htmlspecialchars($record->supplier_name ?? '', ENT_QUOTES, 'UTF-8'),
                'purchase_date' => $record->purchase_date,
                'total_amount'  => $record->grand_total_amount,
            );
            $sl++;
        }

        return array(
            "draw"                 => intval($draw),
            "iTotalRecords"        => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordwithFilter,
            "aaData"               => $data
        );
    }

    public function supplier_list()
    {
        $data = $this->db->get('supplier_tbl')->result();
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
        $fromdate = $this->input->post('fromdate', TRUE);
        $todate = $this->input->post('todate', TRUE);
        $customer_id = $this->input->post('customer_id', TRUE);

        $draw = $postData['draw'];
        $start = $postData['start'];
        $rowperpage = $postData['length'];
        $columnIndex = $postData['order'][0]['column'];
        $columnName = $postData['columns'][$columnIndex]['data'];
        $columnSortOrder = ($postData['order'][0]['dir'] === 'desc') ? 'desc' : 'asc';
        $searchValue = $postData['search']['value'];

        $sortMap = [
            'sl' => 'a.id', 'invoice_id' => 'a.invoice_id', 'customer_name' => 'b.name',
            'date' => 'a.date', 'total_amount' => 'a.total_amount',
        ];
        $orderColumn = isset($sortMap[$columnName]) ? $sortMap[$columnName] : 'a.date';

        // Total without filtering
        $this->db->select('count(*) as allcount')->from('invoice_tbl a');
        $this->db->join('customer_tbl b', 'b.customerid = a.customer_id', 'left');
        if (!empty($fromdate) && !empty($todate)) {
            $this->db->where('a.customer_id', $customer_id);
            $this->db->where('a.date >=', $fromdate);
            $this->db->where('a.date <=', $todate);
        }
        $totalRecords = $this->db->get()->row()->allcount;

        // Total with filtering
        $this->db->select('count(*) as allcount')->from('invoice_tbl a');
        $this->db->join('customer_tbl b', 'b.customerid = a.customer_id', 'left');
        if (!empty($fromdate) && !empty($todate)) {
            $this->db->where('a.customer_id', $customer_id);
            $this->db->where('a.date >=', $fromdate);
            $this->db->where('a.date <=', $todate);
        }
        if ($searchValue != '') {
            $this->db->group_start();
            $this->db->like('b.name', $searchValue);
            $this->db->or_like('a.invoice_id', $searchValue);
            $this->db->or_like('a.date', $searchValue);
            $this->db->group_end();
        }
        $totalRecordwithFilter = $this->db->get()->row()->allcount;

        // Fetch records
        $this->db->select('a.*, b.name as customer_name');
        $this->db->from('invoice_tbl a');
        $this->db->join('customer_tbl b', 'b.customerid = a.customer_id', 'left');
        if (!empty($fromdate) && !empty($todate)) {
            $this->db->where('a.customer_id', $customer_id);
            $this->db->where('a.date >=', $fromdate);
            $this->db->where('a.date <=', $todate);
        }
        if ($searchValue != '') {
            $this->db->group_start();
            $this->db->like('b.name', $searchValue);
            $this->db->or_like('a.invoice_id', $searchValue);
            $this->db->or_like('a.date', $searchValue);
            $this->db->group_end();
        }
        $this->db->order_by($orderColumn, $columnSortOrder);
        $this->db->limit($rowperpage, $start);
        $records = $this->db->get()->result();

        $data = array();
        $sl = $start + 1;
        $base_url = base_url();
        foreach ($records as $record) {
            $data[] = array(
                'sl'            => $sl,
                'invoice_id'    => htmlspecialchars($record->invoice_id, ENT_QUOTES, 'UTF-8'),
                'customer_name' => '<a href="' . $base_url . 'customer/customer_info/singleledgerbycustomer/' . htmlspecialchars($record->customer_id, ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($record->customer_name ?? '', ENT_QUOTES, 'UTF-8') . '</a>',
                'date'          => $record->date,
                'total_amount'  => $record->total_amount,
            );
            $sl++;
        }

        return array(
            "draw"                 => intval($draw),
            "iTotalRecords"        => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordwithFilter,
            "aaData"               => $data
        );
    }

    public function customer_list()
    {
        $data = $this->db->get('customer_tbl')->result();
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
        $this->db->select('a.*');
        $this->db->from('ledger_tbl a');
        $this->db->where('a.ledger_id', 1);
        return $this->db->get()->result();
    }

    public function getCashBookReports($postData = null)
    {
        $fromdate = $this->input->post('fromdate', TRUE);
        $todate = $this->input->post('todate', TRUE);

        $draw = $postData['draw'];
        $start = $postData['start'];
        $rowperpage = $postData['length'];
        $columnIndex = $postData['order'][0]['column'];
        $columnName = $postData['columns'][$columnIndex]['data'];
        $columnSortOrder = ($postData['order'][0]['dir'] === 'desc') ? 'desc' : 'asc';
        $searchValue = $postData['search']['value'];

        $sortMap = ['sl' => 'id', 'date' => 'date', 'description' => 'description'];
        $orderColumn = isset($sortMap[$columnName]) ? $sortMap[$columnName] : 'date';

        // Total without filtering
        $this->db->select('count(*) as allcount')->from('ledger_tbl')->where('ledger_id', 1);
        if (!empty($fromdate) && !empty($todate)) {
            $this->db->where('date >=', $fromdate);
            $this->db->where('date <=', $todate);
        }
        $totalRecords = $this->db->get()->row()->allcount;

        // Total with filtering
        $this->db->select('count(*) as allcount')->from('ledger_tbl')->where('ledger_id', 1);
        if (!empty($fromdate) && !empty($todate)) {
            $this->db->where('date >=', $fromdate);
            $this->db->where('date <=', $todate);
        }
        $totalRecordwithFilter = $this->db->get()->row()->allcount;

        // Fetch records
        $this->db->select('*')->from('ledger_tbl')->where('ledger_id', 1);
        if (!empty($fromdate) && !empty($todate)) {
            $this->db->where('date >=', $fromdate);
            $this->db->where('date <=', $todate);
        }
        $this->db->order_by($orderColumn, $columnSortOrder);
        $this->db->limit($rowperpage, $start);
        $records = $this->db->get()->result();

        $data = array();
        $sl = $start + 1;
        foreach ($records as $record) {
            $payment = $this->db->select('amount as payment')->from('ledger_tbl')->where('id', $record->id)->where('d_c', 'c')->get()->row();
            $receive = $this->db->select('amount as receive')->from('ledger_tbl')->where('id', $record->id)->where('d_c', 'd')->get()->row();
            $data[] = array(
                'sl'          => $sl,
                'date'        => $record->date,
                'description' => htmlspecialchars($record->description ?? '', ENT_QUOTES, 'UTF-8'),
                'payment'     => (!empty($payment->payment) ? $payment->payment : 0),
                'receive'     => (!empty($receive->receive) ? $receive->receive : 0),
            );
            $sl++;
        }

        return array(
            "draw"                 => intval($draw),
            "iTotalRecords"        => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordwithFilter,
            "aaData"               => $data
        );
    }

    public function getBankBookreport($postData = null)
    {
        $fromdate = $this->input->post('fromdate', TRUE);
        $todate = $this->input->post('todate', TRUE);
        $bank_id = $this->input->post('bank_id', TRUE);

        $draw = $postData['draw'];
        $start = $postData['start'];
        $rowperpage = $postData['length'];
        $columnIndex = $postData['order'][0]['column'];
        $columnName = $postData['columns'][$columnIndex]['data'];
        $columnSortOrder = ($postData['order'][0]['dir'] === 'desc') ? 'desc' : 'asc';
        $searchValue = $postData['search']['value'];

        $sortMap = ['sl' => 'a.id', 'bank_name' => 'b.bank_name', 'date' => 'a.date'];
        $orderColumn = isset($sortMap[$columnName]) ? $sortMap[$columnName] : 'a.date';

        // Total without filtering
        $this->db->select('count(*) as allcount')->from('ledger_tbl a');
        $this->db->join('bank_tbl b', 'a.ledger_id = b.bank_id');
        if (!empty($fromdate) && !empty($todate)) {
            $this->db->where('a.date >=', $fromdate);
            $this->db->where('a.date <=', $todate);
        }
        $totalRecords = $this->db->get()->row()->allcount;

        // Total with filtering
        $this->db->select('count(*) as allcount')->from('ledger_tbl a');
        $this->db->join('bank_tbl b', 'a.ledger_id = b.bank_id');
        if (!empty($fromdate) && !empty($todate)) {
            $this->db->where('a.date >=', $fromdate);
            $this->db->where('a.date <=', $todate);
        }
        if ($searchValue != '') {
            $this->db->group_start();
            $this->db->like('b.bank_name', $searchValue);
            $this->db->or_like('a.date', $searchValue);
            $this->db->group_end();
        }
        $totalRecordwithFilter = $this->db->get()->row()->allcount;

        // Fetch records
        $this->db->select('a.*, b.bank_name')->from('ledger_tbl a');
        $this->db->join('bank_tbl b', 'a.ledger_id = b.bank_id');
        if (!empty($fromdate) && !empty($todate)) {
            $this->db->where('a.date >=', $fromdate);
            $this->db->where('a.date <=', $todate);
        }
        if ($searchValue != '') {
            $this->db->group_start();
            $this->db->like('b.bank_name', $searchValue);
            $this->db->or_like('a.date', $searchValue);
            $this->db->group_end();
        }
        $this->db->order_by($orderColumn, $columnSortOrder);
        $this->db->limit($rowperpage, $start);
        $records = $this->db->get()->result();

        $data = array();
        $sl = $start + 1;
        $balance = 0;
        foreach ($records as $record) {
            $deposit = $this->db->select('amount as deposit')->from('ledger_tbl')->where('id', $record->id)->where('d_c', 'd')->get()->row();
            $withdraw = $this->db->select('amount as withdraw')->from('ledger_tbl')->where('id', $record->id)->where('d_c', 'c')->get()->row();
            $balance += (!empty($deposit) ? $deposit->deposit : 0) - (!empty($withdraw) ? $withdraw->withdraw : 0);
            $data[] = array(
                'sl'        => $sl,
                'bank_name' => htmlspecialchars($record->bank_name ?? '', ENT_QUOTES, 'UTF-8'),
                'date'      => $record->date,
                'deposit'   => (!empty($deposit) ? $deposit->deposit : 0),
                'witdraw'   => (!empty($withdraw) ? $withdraw->withdraw : 0),
                'balance'   => $balance
            );
            $sl++;
        }

        return array(
            "draw"                 => intval($draw),
            "iTotalRecords"        => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordwithFilter,
            "aaData"               => $data
        );
    }

    public function bank_book_list()
    {
        return $this->db->get('bank_tbl')->result_array();
    }
}
