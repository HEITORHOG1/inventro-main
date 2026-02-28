<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Item_model extends CI_Model {
	
  public function item_list()
	{
		return $this->db->select('a.*,b.name as supplier_name,c.name as category_name,d.unit_name,e.picture')
			->from('product_tbl a')
      ->join('supplier_tbl b','b.supplier_id = a.supplier_id','left')
      ->join('category_tbl c','c.category_id = a.category_id','left')
      ->join('product_unit d','d.id = a.unit','left')
      ->join('picture_tbl e','e.from_id = a.product_id','left')
      ->group_by('a.product_id')
			->order_by('name', 'asc')
			->get()
			->result();
	}
	public function create($data = array())
	{
		return $this->db->insert('product_tbl', $data);
	}

	public function delete($id = null)
	{
		$this->db->where('product_id',$id)
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
	public function findById($id){
      return $this->db->select('a.*,b.picture')
      ->from('product_tbl a')
      ->join('picture_tbl b','b.from_id = a.product_id','left')
      ->where('a.product_id',$id)
      ->get()
      ->row();
    }





 public function product_id_check($product_id) {
        $query = $this->db->select('*')
                ->from('product_tbl')
                ->where('product_id', $product_id)
                ->get();
        if ($query->num_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function unit_list()
	{
		$this->db->select('*');
        $this->db->from('product_unit');
        $query = $this->db->get();
        $data = $query->result();
       
          $list[''] = 'Select Unit';
       	if (!empty($data) ) {
       		foreach ($data as $value) {
       			$list[$value->id] = $value->unit_name;
       		} 
       	}
       	return $list;
	}


  public function category_list()
	{
		$this->db->select('*');
        $this->db->from('category_tbl');
        $query = $this->db->get();
        $data = $query->result();
       
        $list[''] = 'Select Category';
       	if (!empty($data) ) {
       		foreach ($data as $value) {
       			$list[$value->category_id] = $value->name;
       		} 
       	}
       	return $list;
	}

public function supplier_list(){
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

    public function getProductList($postData=null){

         $response = array();
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
         if($searchValue != ''){
            $searchQuery = " (a.name like '%".$searchValue."%' or a.model like '%".$searchValue."%' or a.price like'%".$searchValue."%' or u.unit_name like'%".$searchValue."%' or ct.name like'%".$searchValue."%' or c.name like'%".$searchValue."%') ";
         }

         ## Total number of records without filtering
         $this->db->select('count(*) as allcount');
         $this->db->from('product_tbl a');
         $this->db->join('supplier_tbl c','c.supplier_id = a.supplier_id','left');
         $this->db->join('picture_tbl p','p.from_id  = a.product_id','left');
         $this->db->join('category_tbl ct','ct.category_id = a.category_id','left');
         $this->db->join('product_unit u','u.id = a.unit','left');
          if($searchValue != '')
         $this->db->where($searchQuery);
         $this->db->group_by('a.product_id');
         $records = $this->db->get()->num_rows();
         $totalRecords = $records;

         ## Total number of record with filtering
         $this->db->select('count(*) as allcount');
         $this->db->from('product_tbl a');
         $this->db->join('supplier_tbl c','c.supplier_id = a.supplier_id','left');
         $this->db->join('picture_tbl p','p.from_id  = a.product_id','left');
         $this->db->join('category_tbl ct','ct.category_id = a.category_id','left');
         $this->db->join('product_unit u','u.id = a.unit','left');
         if($searchValue != '')
            $this->db->where($searchQuery);
          $this->db->group_by('a.product_id');
         $records = $this->db->get()->num_rows();
         $totalRecordwithFilter = $records;

         ## Fetch records
         $this->db->select("a.*,
                c.name as supplier_name,
                p.picture,
                ct.name as category_name,
                u.unit_name
                ");
         $this->db->from('product_tbl a');
         $this->db->join('supplier_tbl c','c.supplier_id = a.supplier_id','left');
         $this->db->join('picture_tbl p','p.from_id  = a.product_id','left');
         $this->db->join('category_tbl ct','ct.category_id = a.category_id','left');
         $this->db->join('product_unit u','u.id = a.unit','left');
         if($searchValue != '')
         $this->db->where($searchQuery);
         $this->db->group_by('a.product_id');
         $this->db->order_by($columnName, $columnSortOrder);
         $this->db->limit($rowperpage, $start);
         $records = $this->db->get()->result();
        
         $data = array();
         $sl =1;
  
         foreach($records as $record ){
          $button = '';
          $base_url = base_url();
          $jsaction = "return confirm('Are You Sure ?')";
          if(!empty($record->picture)){
            $image = '<img src="'.$base_url.$record->picture.'" class="img img-responsive" height="50" width="50">';
          }else{
             $image = '<img src="'.$base_url.'application/modules/item/assets/images/product.jpg" class="img img-responsive" height="50" width="50">';
          }
                                  
           $button .= '<a href="'.$base_url.'item/Item/delete/'.$record->product_id.'" class="btn btn-xs btn-danger "  onclick="'.$jsaction.'"><i class="fa fa-trash"></i></a>';

          $button .=' <a href="'.$base_url.'item/Item/item_form/'.$record->product_id.'" class="btn btn-info btn-xs text-white"  data-toggle="tooltip" data-placement="left" title="'. display('barcode').'"><i class="fas fa-edit" aria-hidden="true"></i></a>';

          $button .='<input name="url" type="hidden" id="url_'.$record->product_id.'" value="'.$base_url.'item/barcode/barcode_print" />
            <a onclick="barcodeqtcode('.$record->product_id.')" style="color:#FFF; cursor:pointer;" class="btn btn-secondary btn-xs" data-toggle="tooltip" data-placement="left" title="Barcode"><i class="fas fa-barcode"></i></a>';

         $button .='<input name="url" type="hidden" id="qrcode_'.$record->product_id.'" value="'.$base_url.'item/itemqrcode/qrgenerator" />
            <a onclick="qrcode('.$record->product_id.')" style="color:#FFF; cursor:pointer;" class="btn btn-success btn-xs" data-toggle="tooltip" data-placement="left" title="Barcode"><i class="fas fa-qrcode"></i></a>';        

               
            $data[] = array( 
                'sl'               =>$sl,
                'name'             =>$record->name,
                'product_model'    =>$record->model,
                'supplier_name'    =>$record->supplier_name,
                'price'            =>$record->price,
                'purchase_price'   =>$record->purchase_price,
                'unit'             =>$record->unit_name,
                'category'         =>$record->category_name,
                'image'            =>$image,
                'button'           =>$button,
                
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

//setting information
    public function company_info(){
      return $this->db->select('*')
                       ->from('setting')
                       ->get()
                       ->result_array();
    }

}