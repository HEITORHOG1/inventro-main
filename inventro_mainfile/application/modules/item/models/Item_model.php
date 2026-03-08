<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Item_model extends CI_Model {

    public function item_list()
    {
        return $this->db->select('a.*, b.name as supplier_name, c.name as category_name, d.unit_name, (SELECT e.picture FROM picture_tbl e WHERE e.from_id = a.product_id LIMIT 1) as picture', FALSE)
            ->from('product_tbl a')
            ->join('supplier_tbl b', 'b.id = a.supplier_id', 'left')
            ->join('category_tbl c', 'c.id = a.category_id', 'left')
            ->join('product_unit d', 'd.id = a.unit', 'left')
            ->order_by('a.name', 'asc')
            ->get()
            ->result();
    }

    public function create($data = array())
    {
        return $this->db->insert('product_tbl', $data);
    }

    public function delete($id = null)
    {
        $this->db->where('product_id', $id)
            ->delete('product_tbl');

        if ($this->db->affected_rows()) {
            return true;
        } else {
            return false;
        }
    }

    public function update($data = array())
    {
        return $this->db->where('product_id', $data["product_id"])
            ->update("product_tbl", $data);
    }

    public function findById($id)
    {
        return $this->db->select('a.*, b.picture')
            ->from('product_tbl a')
            ->join('picture_tbl b', 'b.from_id = a.product_id', 'left')
            ->where('a.product_id', $id)
            ->get()
            ->row();
    }

    public function product_id_check($product_id)
    {
        $query = $this->db->select('*')
            ->from('product_tbl')
            ->where('product_id', $product_id)
            ->get();
        return $query->num_rows() > 0;
    }

    public function unit_list()
    {
        $data = $this->db->get('product_unit')->result();
        $list[''] = 'Select Unit';
        if (!empty($data)) {
            foreach ($data as $value) {
                $list[$value->id] = $value->unit_name;
            }
        }
        return $list;
    }

    public function category_list()
    {
        $data = $this->db->get('category_tbl')->result();
        $list[''] = 'Select Category';
        if (!empty($data)) {
            foreach ($data as $value) {
                $list[$value->id] = $value->name;
            }
        }
        return $list;
    }

    public function supplier_list()
    {
        $data = $this->db->get('supplier_tbl')->result();
        $list[''] = 'Select Supplier';
        if (!empty($data)) {
            foreach ($data as $value) {
                $list[$value->id] = $value->name;
            }
        }
        return $list;
    }

    public function getProductList($postData = null)
    {
        $response = array();

        $draw = $postData['draw'];
        $start = $postData['start'];
        $rowperpage = $postData['length'];
        $columnIndex = $postData['order'][0]['column'];
        $columnName = $postData['columns'][$columnIndex]['data'];
        $columnSortOrder = $postData['order'][0]['dir'];
        $searchValue = $postData['search']['value'];

        // Sanitize sort order
        $columnSortOrder = ($columnSortOrder === 'desc') ? 'desc' : 'asc';

        // Map DataTable column names to actual DB columns
        $sortMap = [
            'sl'             => 'a.id',
            'name'           => 'a.name',
            'product_model'  => 'a.model',
            'supplier_name'  => 'c.name',
            'price'          => 'a.price',
            'purchase_price' => 'a.purchase_price',
            'unit'           => 'u.unit_name',
            'category'       => 'ct.name',
        ];
        $orderColumn = isset($sortMap[$columnName]) ? $sortMap[$columnName] : 'a.name';

        ## Total number of records without filtering
        $this->db->select('count(*) as allcount');
        $this->db->from('product_tbl a');
        $totalRecords = $this->db->get()->row()->allcount;

        ## Total number of records with filtering
        $this->db->select('count(*) as allcount');
        $this->db->from('product_tbl a');
        $this->db->join('supplier_tbl c', 'c.id = a.supplier_id', 'left');
        $this->db->join('category_tbl ct', 'ct.id = a.category_id', 'left');
        $this->db->join('product_unit u', 'u.id = a.unit', 'left');
        if ($searchValue != '') {
            $this->db->group_start();
            $this->db->like('a.name', $searchValue);
            $this->db->or_like('a.model', $searchValue);
            $this->db->or_like('a.price', $searchValue);
            $this->db->or_like('u.unit_name', $searchValue);
            $this->db->or_like('ct.name', $searchValue);
            $this->db->or_like('c.name', $searchValue);
            $this->db->group_end();
        }
        $totalRecordwithFilter = $this->db->get()->row()->allcount;

        ## Fetch records (subquery for picture to avoid GROUP BY issues with only_full_group_by)
        $this->db->select('a.*, c.name as supplier_name, ct.name as category_name, u.unit_name, (SELECT p.picture FROM picture_tbl p WHERE p.from_id = a.product_id LIMIT 1) as picture', FALSE);
        $this->db->from('product_tbl a');
        $this->db->join('supplier_tbl c', 'c.id = a.supplier_id', 'left');
        $this->db->join('category_tbl ct', 'ct.id = a.category_id', 'left');
        $this->db->join('product_unit u', 'u.id = a.unit', 'left');
        if ($searchValue != '') {
            $this->db->group_start();
            $this->db->like('a.name', $searchValue);
            $this->db->or_like('a.model', $searchValue);
            $this->db->or_like('a.price', $searchValue);
            $this->db->or_like('u.unit_name', $searchValue);
            $this->db->or_like('ct.name', $searchValue);
            $this->db->or_like('c.name', $searchValue);
            $this->db->group_end();
        }
        $this->db->order_by($orderColumn, $columnSortOrder);
        $this->db->limit($rowperpage, $start);
        $records = $this->db->get()->result();

        $data = array();
        $sl = $start + 1;

        foreach ($records as $record) {
            $button = '';
            $base_url = base_url();
            $jsaction = "return confirm('Are You Sure ?')";

            if (!empty($record->picture)) {
                $img_src = $record->picture;
                if (preg_match('#(\d{4}-\d{2}-\d{2})/([^/]+)$#', $record->picture, $_m)) {
                    $img_src = 'img/product/' . $_m[1] . '/' . $_m[2];
                }
                $image = '<img src="' . htmlspecialchars($base_url . $img_src, ENT_QUOTES, 'UTF-8') . '" class="img img-responsive" height="50" width="50">';
            } else {
                $image = '<img src="' . htmlspecialchars($base_url . 'img/product/default', ENT_QUOTES, 'UTF-8') . '" class="img img-responsive" height="50" width="50">';
            }

            $pid = htmlspecialchars($record->product_id, ENT_QUOTES, 'UTF-8');

            $button .= '<a href="' . $base_url . 'item/Item/delete/' . $pid . '" class="btn btn-xs btn-danger" onclick="' . $jsaction . '"><i class="fa fa-trash"></i></a>';
            $button .= ' <a href="' . $base_url . 'item/Item/item_form/' . $pid . '" class="btn btn-info btn-xs text-white" data-toggle="tooltip" data-placement="left" title="' . display('barcode') . '"><i class="fas fa-edit" aria-hidden="true"></i></a>';
            $button .= '<input name="url" type="hidden" id="url_' . $pid . '" value="' . $base_url . 'item/barcode/barcode_print" />';
            $button .= '<a onclick="barcodeqtcode(\'' . $pid . '\')" style="color:#FFF; cursor:pointer;" class="btn btn-secondary btn-xs" data-toggle="tooltip" data-placement="left" title="Barcode"><i class="fas fa-barcode"></i></a>';
            $button .= '<input name="url" type="hidden" id="qrcode_' . $pid . '" value="' . $base_url . 'item/itemqrcode/qrgenerator" />';
            $button .= '<a onclick="qrcode(\'' . $pid . '\')" style="color:#FFF; cursor:pointer;" class="btn btn-success btn-xs" data-toggle="tooltip" data-placement="left" title="QR Code"><i class="fas fa-qrcode"></i></a>';

            $data[] = array(
                'sl'             => $sl,
                'name'           => htmlspecialchars($record->name, ENT_QUOTES, 'UTF-8'),
                'product_model'  => htmlspecialchars($record->model ?? '', ENT_QUOTES, 'UTF-8'),
                'supplier_name'  => htmlspecialchars($record->supplier_name ?? '', ENT_QUOTES, 'UTF-8'),
                'price'          => $record->price,
                'purchase_price' => $record->purchase_price,
                'unit'           => htmlspecialchars($record->unit_name ?? '', ENT_QUOTES, 'UTF-8'),
                'category'       => htmlspecialchars($record->category_name ?? '', ENT_QUOTES, 'UTF-8'),
                'image'          => $image,
                'button'         => $button,
            );
            $sl++;
        }

        ## Response — iTotalRecords = total, iTotalDisplayRecords = filtered
        $response = array(
            "draw"                 => intval($draw),
            "iTotalRecords"        => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordwithFilter,
            "aaData"               => $data
        );

        return $response;
    }

    public function company_info()
    {
        return $this->db->get('setting')->result_array();
    }
}
