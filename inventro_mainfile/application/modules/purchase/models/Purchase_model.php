<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Purchase_model extends CI_Model {

    public function read()
    {
        return $this->db->select('*')
            ->from('product_purchase')
            ->order_by('purchase_date', 'desc')
            ->get()
            ->result();
    }

    public function create_purchase($data = array())
    {
        return $this->db->insert('product_purchase', $data);
    }

    public function delete($id = null)
    {
        $this->db->where('purchase_id', $id)->delete('product_purchase');
        $this->db->where('purchase_id', $id)->delete('product_purchase_details');
        $this->db->where('transaction_id', $id)->delete('ledger_tbl');
        return $this->db->affected_rows() > 0;
    }

    public function findById($id = null)
    {
        return $this->db->select("*")
            ->from("product_purchase")
            ->where('purchase_id', $id)
            ->get()
            ->row();
    }

    public function update($data = [])
    {
        $this->db->where('purchase_id', $data["purchase_id"])
            ->update("product_purchase", $data);

        $this->db->where('purchase_id', $data["purchase_id"])
            ->delete('product_purchase_details');

        $this->db->where('transaction_id', $data["purchase_id"])
            ->delete('ledger_tbl');

        return true;
    }

    public function product_search_item($supplier_id, $product_name)
    {
        $query = $this->db->distinct()
            ->select('*')
            ->from('product_tbl')
            ->group_start()
                ->where('supplier_id', $supplier_id)
                ->like('model', $product_name, 'after')
            ->group_end()
            ->or_group_start()
                ->where('supplier_id', $supplier_id)
                ->like('name', $product_name, 'after')
            ->group_end()
            ->order_by('name', 'asc')
            ->limit(30)
            ->get();

        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }

    public function get_total_product($product_id, $supplier_id)
    {
        $this->db->select('SUM(a.quantity) as total_purchase');
        $this->db->from('product_purchase_details a');
        $this->db->join('product_tbl b', 'a.product_id = b.product_id');
        $this->db->where('a.product_id', $product_id);
        $this->db->where('b.supplier_id', $supplier_id);
        $total_purchase = $this->db->get()->row();

        $this->db->select('SUM(return_qty) as total_return_out');
        $this->db->from('return_details');
        $this->db->where('product_id', $product_id);
        $this->db->where('status', 2);
        $supplier_return = $this->db->get()->row();

        $this->db->select('SUM(b.quantity) as total_sale');
        $this->db->from('invoice_details b');
        $this->db->where('b.product_id', $product_id);
        $total_sale = $this->db->get()->row();

        $this->db->select('SUM(return_qty) as total_return_in');
        $this->db->from('return_details');
        $this->db->where('product_id', $product_id);
        $this->db->where('status', 1);
        $customer_return = $this->db->get()->row();

        $this->db->select('*');
        $this->db->from('product_tbl a');
        $this->db->where('product_id', $product_id);
        $this->db->where('supplier_id', $supplier_id);
        $product_information = $this->db->get()->row();

        $total_in = (!empty($total_purchase->total_purchase) ? $total_purchase->total_purchase : 0)
                  + (!empty($customer_return->total_return_in) ? $customer_return->total_return_in : 0);
        $total_out = (!empty($total_sale->total_sale) ? $total_sale->total_sale : 0)
                   + (!empty($supplier_return->total_return_out) ? $supplier_return->total_return_out : 0);

        return array(
            'total_product'  => $total_in - $total_out,
            'supplier_price' => $product_information->purchase_price,
            'price'          => $product_information->price,
            'supplier_id'    => $product_information->supplier_id,
            'unit'           => $product_information->unit,
            'cartoonqty'     => $product_information->cartoon_qty,
        );
    }

    public function supplier_list()
    {
        $data = $this->db->get('supplier_tbl')->result();
        $list[' '] = 'Select Supplier';
        if (!empty($data)) {
            foreach ($data as $value) {
                $list[$value->supplier_id] = $value->name;
            }
        }
        return $list;
    }

    public function bank_list()
    {
        $data = $this->db->get('bank_tbl')->result();
        $list[''] = 'Select Bank';
        if (!empty($data)) {
            foreach ($data as $value) {
                $list[$value->bank_id] = $value->bank_name;
            }
        }
        return $list;
    }

    public function getPurchaseList($postData = null)
    {
        $response = array();
        $fromdate = $this->input->post('fromdate', TRUE);
        $todate   = $this->input->post('todate', TRUE);

        $draw = $postData['draw'];
        $start = $postData['start'];
        $rowperpage = $postData['length'];
        $columnIndex = $postData['order'][0]['column'];
        $columnName = $postData['columns'][$columnIndex]['data'];
        $columnSortOrder = ($postData['order'][0]['dir'] === 'desc') ? 'desc' : 'asc';
        $searchValue = $postData['search']['value'];

        $sortMap = [
            'sl'            => 'a.purchase_id',
            'chalan_no'     => 'a.chalan_no',
            'purchase_id'   => 'a.purchase_id',
            'supplier_name' => 'b.name',
            'purchase_date' => 'a.purchase_date',
            'total_amount'  => 'a.grand_total_amount',
        ];
        $orderColumn = isset($sortMap[$columnName]) ? $sortMap[$columnName] : 'a.purchase_date';

        ## Total records without filtering
        $this->db->select('count(*) as allcount');
        $this->db->from('product_purchase a');
        if (!empty($fromdate) && !empty($todate)) {
            $this->db->where('a.purchase_date >=', $fromdate);
            $this->db->where('a.purchase_date <=', $todate);
        }
        $totalRecords = $this->db->get()->row()->allcount;

        ## Total records with filtering
        $this->db->select('count(*) as allcount');
        $this->db->from('product_purchase a');
        $this->db->join('supplier_tbl b', 'b.supplier_id = a.supplier_id', 'left');
        if (!empty($fromdate) && !empty($todate)) {
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

        ## Fetch records
        $this->db->select('a.*, b.name as supplier_name');
        $this->db->from('product_purchase a');
        $this->db->join('supplier_tbl b', 'b.supplier_id = a.supplier_id', 'left');
        if (!empty($fromdate) && !empty($todate)) {
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
            $jsaction = "return confirm('Are You Sure ?')";
            $pid = htmlspecialchars($record->purchase_id, ENT_QUOTES, 'UTF-8');

            $button = '<a href="' . $base_url . 'purchase/purchase/purchase_details/' . $pid . '" class="btn btn-success btn-sm" data-toggle="tooltip" title="' . display('purchase_details') . '"><i class="fa fa-window-restore"></i></a>';
            $button .= ' <a href="' . $base_url . 'purchase/purchase/purchase_edit_data/' . $pid . '" class="btn btn-info btn-sm" data-toggle="tooltip" title="update"><i class="fas fa-edit"></i></a>';
            $button .= ' <a href="' . $base_url . 'purchase/purchase/delete/' . $pid . '" class="btn btn-danger btn-sm" data-toggle="tooltip" title="delete" onclick="' . $jsaction . '"><i class="fas fa-trash"></i></a>';

            $data[] = array(
                'sl'            => $sl,
                'chalan_no'     => htmlspecialchars($record->chalan_no ?? '', ENT_QUOTES, 'UTF-8'),
                'purchase_id'   => $pid,
                'supplier_name' => htmlspecialchars($record->supplier_name ?? '', ENT_QUOTES, 'UTF-8'),
                'purchase_date' => $record->purchase_date,
                'total_amount'  => $record->grand_total_amount,
                'button'        => $button,
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

    public function purchase_details_data($purchase_id)
    {
        $this->db->select('a.*, b.name as supplier_name, c.*, a.purchase_details, d.product_id, d.name as product_name, d.model as product_model');
        $this->db->from('product_purchase a');
        $this->db->join('supplier_tbl b', 'b.supplier_id = a.supplier_id');
        $this->db->join('product_purchase_details c', 'c.purchase_id = a.purchase_id');
        $this->db->join('product_tbl d', 'd.product_id = c.product_id');
        $this->db->where('a.purchase_id', $purchase_id);
        $query = $this->db->get();
        return $query->num_rows() > 0 ? $query->result_array() : false;
    }

    public function retrieve_purchase_editdata($purchase_id)
    {
        $this->db->select('a.*, b.*, c.product_id, c.name as product_name, c.model as product_model, c.cartoon_qty, d.supplier_id, d.name as supplier_name');
        $this->db->from('product_purchase a');
        $this->db->join('product_purchase_details b', 'b.purchase_id = a.purchase_id');
        $this->db->join('product_tbl c', 'c.product_id = b.product_id');
        $this->db->join('supplier_tbl d', 'd.supplier_id = a.supplier_id');
        $this->db->where('a.purchase_id', $purchase_id);
        $this->db->order_by('a.purchase_details', 'asc');
        $query = $this->db->get();
        return $query->num_rows() > 0 ? $query->result_array() : false;
    }
}
