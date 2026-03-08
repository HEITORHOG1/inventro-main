<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Inventory_model extends CI_Model {


	public function distribut_by_brand_products($clause){

		if (!empty($clause->filter_date)) {
			$date_string = $this->get_filter_query("`inv_batch`.`createdate`", $clause->filter_date);
		}

		$this->db->select('SUM(inv_batch_details.case_qty) as case_total,
            SUM(inv_batch_details.unit_qty) as unit_total,
            product_tbl.fk_prod_category_id,
            setup_product_category_tbl.category_name
        ');

        $this->db->from('inv_batch_details');
        $this->db->join('product_tbl','product_tbl.product_id=inv_batch_details.product_id');
        $this->db->join('setup_product_category_tbl','setup_product_category_tbl.prod_category_id=product_tbl.fk_prod_category_id');
        $this->db->join('inv_batch','inv_batch.batch_id=inv_batch_details.batch_id');
        
        if (!empty($clause->filter_date)) {
			$this->db->like("$date_string");	
		}

		if (!empty($clause->from_date) && !empty($clause->to_date)) {
			$this->db->like("inv_batch.createdate <=", $clause->from_date);
			$this->db->like("inv_batch.createdate >=", $clause->to_date);
		} elseif(!empty($clause->from_date)) {
			$this->db->like("inv_batch.createdate", $clause->from_date);
		}

        if($clause->products!=NULL){
            $this->db->where('inv_batch_details.product_id',$clause->products);
        }

        if($clause->distributor!=NULL){
            $this->db->where('inv_batch.fk_distributor_id',$clause->distributor);
        }
        if(!empty($clause->client_id)) {
			$this->db->where('product_tbl.fk_client_id', $clause->client_id);
		}

        $this->db->group_by(array('product_tbl.fk_prod_category_id', 'setup_product_category_tbl.category_name'));
       return $distribut_by_brand_products = $this->db->get()->result();
	}


	public function get_stock_overview($clause = false){

		if (!empty($clause->filter_date)) {
			$date_string = $this->get_filter_query("`inv_stock`.`v_date`", $clause->filter_date);
		}

	

		$this->db->select("
			SUM(inv_stock.unit_qty) AS quantity,
			SUM(inv_stock.case_qty) AS case_quantity,
			SUM(product_tbl.unit_price) AS price,
			MAX(product_tbl.unit_per_case) AS unit_per_case
		");
		
		$this->db->from('inv_stock');
		$this->db->join('product_tbl', "product_tbl.product_id = inv_stock.product_id", "left");
		
		if(!empty($clause->products)) {
			$this->db->where('inv_stock.product_id', $clause->products);
		}
		if(!empty($clause->distributor)) {
			$this->db->where('inv_stock.distributor_id', $clause->distributor);
		}

		if(!empty($clause->client_id)) {
			$this->db->where('product_tbl.fk_client_id', $clause->client_id);
		}

		if (!empty($clause->filter_date)) {
			$this->db->where("$date_string");
		}

		if (!empty($clause->from_date) && !empty($clause->to_date)) {
			$this->db->where("inv_stock.v_date <=", $clause->from_date);
			$this->db->where("inv_stock.v_date >=", $clause->to_date);
		} elseif(!empty($clause->from_date)) {
			$this->db->where("inv_stock.v_date", $clause->from_date);
		}
		$this->db->where("inv_stock.unit_qty!=",0);
		$this->db->where("inv_stock.case_qty!=",0);
		$this->db->group_by('inv_stock.product_id');
		$result = $this->db->get()->result();

		$case_of_unit = 0;
		$total_unit_qty = 0;
		$total_case_qty = 0;
		$total_unit_price = 0;
		$total_case_price = 0;

		$array = array();

		if($result){

			foreach ($result as $key => $value) {
				$case_of_unit +=($value->case_quantity*$value->unit_per_case); 
				$total_unit_price +=($value->quantity*$value->price); 
				$total_case_price +=(($value->case_quantity*$value->unit_per_case)*$value->price); 
				$total_unit_qty +=($value->quantity); 
				$total_case_qty +=($value->case_quantity); 
			}
			
			$array = array(
				'case_of_unit'		=>$case_of_unit,
				'total_unit_qty'	=>$total_unit_qty,
				'total_case_qty'	=>$total_case_qty,
				'total_unit_price'	=>$total_unit_price,
				'total_case_price'	=>$total_case_price
			);
		}else{
			$array = array(
				'case_of_unit'		=>0,
				'total_unit_qty'	=>0,
				'total_case_qty'	=>0,
				'total_unit_price'	=>0,
				'total_case_price'	=>0
			);
		}

		return (object)$array;

	}




	public function get_dist_level_info($clause = false){

		if (!empty($clause->filter_date)) {
			$date_string = $this->get_filter_query("`inv_stock`.`v_date`", $clause->filter_date);
		}

		$this->db->select("setup_distributor_tbl.distributor_name, 
			SUM(inv_stock.case_qty) AS case_qty, 
			SUM(inv_stock.unit_qty) AS unit_qty, 
			SUM(product_tbl.unit_price) AS tprice");

		$this->db->from('inv_stock');

		$this->db->join('product_tbl', "product_tbl.product_id = inv_stock.product_id", "left");

		$this->db->join('setup_distributor_tbl', "setup_distributor_tbl.distributor_id = inv_stock.distributor_id", "left");

		if(!empty($clause->products)) {
			$this->db->where('inv_stock.product_id', $clause->products);
		}

		if(!empty($clause->distributor)) {
			$this->db->where('inv_stock.distributor_id', $clause->distributor);
		}
		
		if(!empty($clause->client_id)) {
			$this->db->where('product_tbl.fk_client_id', $clause->client_id);
		}

		if (!empty($clause->filter_date)) {
			$this->db->where("$date_string");	
		}

		if (!empty($clause->from_date) && !empty($clause->to_date)) {
			$this->db->where("inv_stock.v_date <=", $clause->from_date);
			$this->db->where("inv_stock.v_date >=", $clause->to_date);
		} elseif(!empty($clause->from_date)) {
			$this->db->where("inv_stock.v_date", $clause->from_date);
		}

		$this->db->group_by(array('inv_stock.distributor_id', 'setup_distributor_tbl.distributor_name'));

		$result = $this->db->get()->result();

		return $result;
	}




	public function get_inventory_products_info($clause=null)
	{
		if (!empty($clause->filter_date)) {

			$date_string = $this->get_filter_query("`inv_stock`.`v_date`", $clause->filter_date);
			
		}

		$this->db->select("
			product_tbl.product_id, 
			product_tbl.product_name,
			SUM(inv_stock.case_qty) AS case_qty, 
			SUM(inv_stock.unit_qty) AS unit_qty");

		$this->db->from('inv_stock');

		$this->db->join('product_tbl', "product_tbl.product_id = inv_stock.product_id");

		
		if(!empty($clause->products)) {
			$this->db->where('inv_stock.product_id', $clause->products);
		}
		if(!empty($clause->distributor)) {
			$this->db->where('inv_stock.distributor_id', $clause->distributor);
		}

		if (!empty($clause->filter_date)) {
			$this->db->where("$date_string");
		}
		
		if(!empty($clause->client_id)) {
			$this->db->where('product_tbl.fk_client_id', $clause->client_id);
		}

		if (!empty($clause->from_date) && !empty($clause->to_date)) {
			$this->db->where("inv_stock.v_date <=", $clause->from_date);
			$this->db->where("inv_stock.v_date >=", $clause->to_date);
		} elseif(!empty($clause->from_date)) {
			$this->db->where("inv_stock.v_date", $clause->from_date);
		}

		$this->db->order_by('product_tbl.fk_prod_category_id');
		$this->db->group_by(array('inv_stock.product_id', 'product_tbl.product_id', 'product_tbl.product_name'));

		$result = $this->db->get()->result();

		return $result;


	}


	public function get_checkouts($clause = null,$limit=null,$start=null){

	

		$this->db->select("inv_checkout.*, CONCAT_WS(' ', a.firstname, a.lastname) AS fieldstaff,  CONCAT_WS(' ', b.firstname, b.lastname) AS createby, setup_distributor_tbl.distributor_name");

		$this->db->join('user a', "a.id = inv_checkout.fk_fieldstaff_id", 'left');

		$this->db->join('user b', "b.id = inv_checkout.create_by", 'left');

		$this->db->join('setup_distributor_tbl', "setup_distributor_tbl.fk_user_id = a.id", 'left');

		if (!empty($clause->checkout_id)) {
			$this->db->where('inv_checkout.checkout_id', $clause->checkout_id);
		}

		if (!empty($clause->users)) {
			$this->db->where('inv_checkout.fk_fieldstaff_id', $clause->users);
		}

		if (!empty($clause->sales_team)) {
			$this->db->where('a.fk_team_id', $clause->sales_team);
		}

		if (!empty($clause->distributor)) {
			$this->db->where('setup_distributor_tbl.distributor_id', $clause->distributor);
		}
		

		if (!empty($clause->filter_date)) {
			$this->db->where("$date_string");
		}
		$this->db->limit($limit,$start);
		$this->db->order_by('inv_checkout.row_checkout_id', 'desc');

		$result = $this->db->get('inv_checkout')->result();
		return $result;

	}



	public function checkout_approve($checkout_id, $products, $checkout)
	{

		$inv_stock_fieldstaff = array();
		$j = 0;

		$this->db->trans_start();

		foreach ($products as $product) {

			$case_qty = $this->input->post('unitcase-'. $product->product_id);
			$unit_qty = $this->input->post('unitqnty-'. $product->product_id);

			$v_date = date("Y-m-d");

			$updatedata = [
				'case_qty' => (!empty($case_qty)?$case_qty:0),
				'unit_qty' => (!empty($unit_qty)?$unit_qty:0)
			];

			$this->db->update('inv_checkout_details', $updatedata, ['checkout_id' => $checkout_id, 'product_id' => $product->product_id]);
			
			// Inv Stock products quantity addition
			$this->db->query(
				"UPDATE `inv_stock` SET `case_qty` = `case_qty` - ?, `unit_qty` = `unit_qty` - ? WHERE distributor_id = ? AND product_id = ?",
				[(int)$case_qty, (int)$unit_qty, (int)$checkout->create_by, (int)$product->product_id]
			);

			// FieldStaff Inv Stock products quantity substraction
			$checkINVstockField = $this->db->where('fieldstaff_id',$checkout->fk_fieldstaff_id)->where('product_id',$product->product_id)->get('inv_stock_fieldstaff')->num_rows();
			
			if($checkINVstockField>0){
				// FieldStaff Inv Stock products quantity substraction
				$this->db->query(
					"UPDATE `inv_stock_fieldstaff` SET `case_qty` = `case_qty` + ?, `unit_qty` = `unit_qty` + ? WHERE fieldstaff_id = ? AND product_id = ?",
					[(int)$case_qty, (int)$unit_qty, (int)$checkout->fk_fieldstaff_id, (int)$product->product_id]
				);
				//update
			} else {
				// insert
				// Inv Stock products quantity addition 
				$inv_stock_fieldstaff = [
					'fieldstaff_id'		=>$checkout->fk_fieldstaff_id,
					'product_id'		=>$product->product_id,
					'case_qty' 			=>(!empty($case_qty)?$case_qty:0),
					'unit_qty' 			=>(!empty($unit_qty)?$unit_qty:0)
				];
				$this->db->insert('inv_stock_fieldstaff',$inv_stock_fieldstaff);
			}

		}


		$this->db->update('inv_checkout', ['staff_confirm' => 1], ['checkout_id' => $checkout_id]);

		

		$this->db->trans_complete();

		if ($this->db->trans_status() == true) {
			return true;
		} else {
			return false;
		}

	}





    public function get_all_user()
	{
		return $this->db->select("user.*,CONCAT_WS(' ', firstname, lastname) AS fullname,")
			->from('user')
			->where('is_admin',4)
			->order_by('id', 'desc')
			->get()
			->result();
	}


	public function get_fieldstaff_products_info($fieldstaff)
	{

		$this->db->select("map_fieldstaff_product_tbl.*, product_tbl.product_name");

		$this->db->from('map_fieldstaff_product_tbl');

		$this->db->join('product_tbl', "product_tbl.product_id = map_fieldstaff_product_tbl.fk_product_id", "left");

		$this->db->where("map_fieldstaff_product_tbl.filedstaff_id", $fieldstaff);

		$result = $this->db->get()->result();

		return $result;

	}


	public function get_checkouts_by_checkout_id($checkout_id)
	{
		
		$this->db->select("inv_checkout_details.*, product_tbl.product_name");

		$this->db->from('inv_checkout_details');

		$this->db->join('product_tbl', "product_tbl.product_id = inv_checkout_details.product_id", "left");

		$this->db->where("inv_checkout_details.checkout_id", $checkout_id);

		$result = $this->db->get()->result();

		return $result;


	}

	public function get_fieldstaff_checkin_products_info($fieldstaff_id)
	{
		$this->db->select("map_fieldstaff_product_tbl.*, product_tbl.product_name");
		$this->db->from('map_fieldstaff_product_tbl');
		$this->db->join('product_tbl', "product_tbl.product_id = map_fieldstaff_product_tbl.fk_product_id", "left");
		
		$this->db->where("map_fieldstaff_product_tbl.filedstaff_id", $fieldstaff_id);
		
		$result = $this->db->get()->result();
		return $result;
	}



	public function insert_checkout(){

		$fieldstaff = $this->input->post('fieldstaff');
		$checkout_notes = $this->input->post('checkout_notes');
		$checkout_id = $fieldstaff.time();

			// checkout data
			$fdata = [
				'checkout_id'		=>	$checkout_id,
				'fk_fieldstaff_id' 	=>  $fieldstaff,
				'checkout_note' 	=> 	$checkout_notes,
				'fk_manager_id' 	=> 	$this->session->userdata('id'),
				'create_by' 		=> 	$this->session->userdata('id'),
				'createdate' 		=> 	date('Y-m-d h:i:s')
	    	];

			$this->db->insert('inv_checkout', $fdata);

    		$products = $this->input->post('product_id',TRUE);

    		$unitcase = $this->input->post('unitcase',TRUE);
    		$unitqnty = $this->input->post('unitqnty',TRUE);
    		// Create checkout details data string
    		$checkout_data = [];
    		$i = 0;



    		foreach ($products as $key => $val) {
    			
    			if (!empty($unitcase[$key]) || !empty($unitqnty[$key])) {
					$checkout_data[]=array(
						'checkout_id' =>$checkout_id,
						'product_id'  =>$val,
						'case_qty'    =>$unitcase[$key],
						'unit_qty'    =>$unitqnty[$key]
					);
    			}
    		}

    		// Insert checkout details data
    		if (!empty($checkout_data)) {
    			$this->db->insert_batch('inv_checkout_details',$checkout_data);
				return true;
			} else{
				return false;
			}

		
	}




	/**
     * Get All distributors 
     */
    public function get_all_distributors()
	{
		return $this->db->select("*")
			->from('setup_distributor_tbl')
			->order_by('distributor_id', 'desc')
			->get()
			->result();
	}



	public function get_checkins($clause = false,$limit=null,$start=null){

		if (!empty($clause->filter_date)) {
			$date_string = $this->get_filter_query("`inv_checkin`.`createdate`", $clause->filter_date);
		}

		$this->db->select("inv_checkin.*, CONCAT_WS(' ', a.firstname, a.lastname) AS fieldstaff,  CONCAT_WS(' ', b.firstname, b.lastname) AS createby, setup_distributor_tbl.distributor_name");
		$this->db->join('user a', "a.id = inv_checkin.fk_fieldstaff_id", 'left');
		$this->db->join('user b', "b.id = inv_checkin.create_by", 'left');
		$this->db->join('setup_distributor_tbl', "setup_distributor_tbl.fk_user_id = a.id", 'left');

		if (!empty($clause->checkin_id)) {
			$this->db->where('inv_checkin.checkin_id', $clause->checkin_id);
		}

		if (!empty($clause->users)) {
			$this->db->where('inv_checkin.fk_fieldstaff_id', $clause->users);
		}

		if (!empty($clause->sales_team)) {
			$this->db->where('a.fk_team_id', $clause->sales_team);
		}


		if (!empty($clause->distributor)) {
			$this->db->where('setup_distributor_tbl.distributor_id', $clause->distributor);
		}

		if (!empty($clause->filter_date)) {

			$this->db->where("$date_string");
			
		}
		$this->db->limit($limit,$start);
		
		$this->db->order_by("inv_checkin.row_checkin_id", 'desc');
		$result = $this->db->get('inv_checkin')->result();
		return $result;

	}



	public function get_checkins_by_checkin_id($checkin_id)
	{
		$this->db->select("inv_checkin_details.*, product_tbl.product_name");

		$this->db->from('inv_checkin_details');

		$this->db->join('product_tbl', "product_tbl.product_id = inv_checkin_details.product_id", "left");

		$this->db->where("inv_checkin_details.checkin_id", $checkin_id);

		$result = $this->db->get()->result();

		return $result;

	}

	public function checkin_approve($checkin_id, $products, $checkin)
	{

		$stock_data = "";
		$j = 0;

		$this->db->trans_start();

		foreach ($products as $product) {

			$case_qty = $this->input->post('unitcase-'. $product->product_id);

			$unit_qty = $this->input->post('unitqnty-'. $product->product_id);

			$v_date = date("Y-m-d");

			$updatedata = [
				'case_qty' => (!empty($case_qty)?$case_qty:0),
				'unit_qty' => (!empty($unit_qty)?$unit_qty:0)
			];


			$this->db->update('inv_checkin_details', $updatedata, ['checkin_id' => $checkin_id, 'product_id' => $product->product_id]);
			// Inv Stock products quantity addition
			
			$this->db->query(
				"UPDATE `inv_stock` SET `case_qty` = `case_qty` + ?, `unit_qty` = `unit_qty` + ? WHERE distributor_id = ? AND product_id = ?",
				[(int)$case_qty, (int)$unit_qty, (int)$checkin->create_by, (int)$product->product_id]
			);

			// FieldStaff Inv Stock products quantity substraction
			$this->db->query(
				"UPDATE `inv_stock_fieldstaff` SET `case_qty` = `case_qty` - ?, `unit_qty` = `unit_qty` - ? WHERE fieldstaff_id = ? AND product_id = ?",
				[(int)$case_qty, (int)$unit_qty, (int)$checkin->fk_fieldstaff_id, (int)$product->product_id]
			);

		}

		$this->db->update('inv_checkin', ['manager_confirm' => 1], ['checkin_id' => $checkin_id]);


		$this->db->trans_complete();

		if ($this->db->trans_status() == true) {
			return true;
		} else {
			return false;
		}


	}



	public function get_filter_query($fieldname, $filterdate){

		switch ($filterdate) {
			case 'yesterday':
				return "date($fieldname) = date(NOW() - INTERVAL 1 DAY)";
				break;
			case 'last_week':
				return "date($fieldname) BETWEEN date(NOW() - INTERVAL 1 WEEK) AND date(Now())";
				break;
			case 'last_2week':
				return "date($fieldname) BETWEEN date(NOW() - INTERVAL 2 WEEK) AND date(Now())";
				break;
			case 'last_month':
				return "date($fieldname) = date(NOW() - INTERVAL 1 MONTH)";
				break;
			case 'last_2month':
				return "date($fieldname) BETWEEN date(NOW() - INTERVAL 2 MONTH) AND date(Now())";
				break;
			case 'last_3month':
				return "date($fieldname) BETWEEN date(NOW() - INTERVAL 3 MONTH) AND date(Now())";
				break;
			case 'last_6month':
				return "date($fieldname) BETWEEN date(NOW() - INTERVAL 6 MONTH) AND date(Now())";
				break;
			case 'custom':
				$start_date = date("Y-m-d", strtotime($this->input->post("start_date")));
				$end_date = date("Y-m-d", strtotime($this->input->post("end_date")));

				return 'date($fieldname) BETWEEN "'.$start_date.'" AND "'.$end_date.'"';
				break;
			
			default:
				return "date($fieldname) = date(NOW())";
				break;
		}
		
	}


	public function get_all_batches($clause = false)
	{
		if (!empty($clause->filter_date)) {

			$date_string = $this->get_filter_query("`inv_batch`.`createdate`", $clause->filter_date);
			
		}

		$this->db->select("inv_batch.*, setup_distributor_tbl.distributor_name");
		$this->db->from('inv_batch');
		$this->db->join("setup_distributor_tbl", "setup_distributor_tbl.distributor_id = inv_batch.fk_distributor_id", "left");

		if (!empty($clause->batch_id)) {
			$this->db->where('inv_batch.batch_id', $clause->batch_id);
		}

		if (!empty($clause->distributor)) {
			$this->db->where('inv_batch.fk_distributor_id', $clause->distributor);
		}

		if (!empty($clause->filter_date)) {

			$this->db->where("$date_string");
			
		}

		$this->db->order_by('inv_batch.batch_id', 'desc');

		$result = $this->db->get()->result();
		return $result;
	}

	public function get_products()
	{
		return $this->db->select("product_id,product_name")
			->from('product_tbl')
			->order_by('product_id', 'desc')
			->get()
			->result();
	}


	public function insert_checkin(){

		$checkin_id = $this->input->post('fieldstaff').time();
		// checkout data
		$fdata = [
			'checkin_id' 		=> $checkin_id,
			'fk_fieldstaff_id' 	=> $this->input->post('fieldstaff',TRUE),
			'fk_manager_id' 	=> $this->session->userdata('id'),
			'checkin_note' 		=> $this->input->post('checkin_notes',TRUE),
			'create_by' 		=> $this->session->userdata('id'),
			'createdate' 		=> date('Y-m-d h:i:s')

    	];

    	
    	if($this->db->insert('inv_checkin', $fdata)){
			
			$fieldstaff = $this->input->post('fieldstaff',TRUE);
    		$products = $this->get_fieldstaff_checkin_products_info($fieldstaff);
    		// Create checkout details data string
    		$checkin_data=[];
    		$i = 0;
    		foreach ($products as $product) {

    			$productid = $this->input->post('product-'. $product->fk_product_id);

    			if (!empty($productid)) {

	    		
	    			$checkin_data[] = array(
	    				'checkin_id'=>$checkin_id,
	    				'product_id'=>$this->input->post('product-'. $product->fk_product_id),
	    				'case_qty'=>$this->input->post('unitcase-'. $product->fk_product_id),
	    				'unit_qty'=>$this->input->post('unitqnty-'. $product->fk_product_id)
	    			);

	    		}

    		}

    		if(!empty($checkin_data)){
	    		$this->db->insert_batch('inv_checkin_details',$checkin_data);
	    	}


    		// Insert checkout details data

	     	return true;

	    }else{
	    	return false;
	    }
	}





	public function insert_batch(){

		$distributor = $this->input->post('distributor');
		// checkout data
		$fdata = [
			'fk_distributor_id' => $distributor,
			'description' 		=> $this->input->post('batch_desc'),
			'createby' 			=> $this->session->userdata('id'),
			'createdate' 		=> date('Y-m-d h:i:s'),
			'updatedate' 		=> date('Y-m-d h:i:s')
		];

		if($this->db->insert('inv_batch', $fdata)){
			
			$batch_id = $this->db->insert_id();

			$products = $this->get_products();

			$v_date = date("Y-m-d");

			$checkout_data = array();
			$stock =array();

			foreach ($products as $product) {

				$productid = $this->input->post('product-'. $product->product_id);

				if (!empty($productid)) {

					$case_qty = $this->input->post('unitcase-'. $product->product_id);
					$unit_qty = $this->input->post('unitqnty-'. $product->product_id);

	    			$checkout_data[] = array(
	    				'batch_id' 		=> $batch_id,
	    				'product_id' 	=> $productid,
	    				'case_qty' 		=> $case_qty,
	    				'unit_qty' 		=> $unit_qty
	    			); 

	    			// Check stock existancy
	    			$this->db->select("product_id");
	    			$this->db->where('distributor_id', $distributor);
	    			$this->db->where('product_id', $productid);
	    			$stock_exist = $this->db->get('inv_stock');

	    			if($stock_exist->num_rows() > 0) {

	    				$this->db->query(
	    					"UPDATE `inv_stock` SET case_qty = case_qty + ?, unit_qty = unit_qty + ? WHERE distributor_id = ? AND product_id = ?",
	    					[(int)$case_qty, (int)$unit_qty, (int)$distributor, (int)$productid]
	    				);

	    			} else {

	    				$stock[] = array(
	    					'case_qty'		=>$case_qty,
	    					'unit_qty'		=>$unit_qty,
	    					'distributor_id'=>$distributor,
	    					'product_id'	=>$productid,
	    					'v_date'		=>$v_date,
	    					'stock_type'	=>1
	    				);

	    			}

				}
			}

			// Insert batch data
			if (!empty($checkout_data)) {
				$this->db->insert_batch('inv_batch_details',$checkout_data);
			}

			// Insert Stock data
			if (!empty($stock)) {
				$this->db->insert_batch('inv_stock',$stock);
			}

			return true;

		}else{

			return false;

		}
	}




	public function update_insert_batch(){

		$distributor = $this->input->post('distributor',TRUE);
		$product_id = $this->input->post('product_id',TRUE);
		$batch_id = $this->input->post('batch_id',TRUE);
		
		$case_qty = $this->input->post('case_qty',TRUE);
		$unit_qty = $this->input->post('unit_qnty',TRUE);

		if(!empty($product_id)){

			foreach ($product_id as $key => $productid) {

    			$case_qty = ($case_qty[$key]?$case_qty[$key]:0);
    			$unit_qty = ($unit_qty[$key]?$unit_qty[$key]:0);
    			// Check inv_batch_details existancy
    			$this->db->select("product_id");
    			$this->db->where('batch_id', $batch_id);
    			$this->db->where('product_id', $productid);
    			$batch_exist = $this->db->get('inv_batch_details');

    			if($batch_exist->num_rows()>0){
    				
	    			$this->db->query(
	    				"UPDATE `inv_batch_details` SET case_qty = case_qty + ?, unit_qty = unit_qty + ? WHERE batch_id = ? AND product_id = ?",
	    				[(int)$case_qty, (int)$unit_qty, (int)$batch_id, (int)$productid]
	    			);

    			}else{

    				$inv_batch_details = array(

	    				'batch_id' 		=> $batch_id,
	    				'product_id' 	=> $productid,
	    				'case_qty' 		=> $case_qty,
	    				'unit_qty' 		=> $unit_qty

	    			);

    				$this->db->insert('inv_batch_details',$inv_batch_details);
    			}


    			// Check stock existancy
    			$this->db->select("product_id");
    			$this->db->where('distributor_id', $distributor);
    			$this->db->where('product_id', $productid);
    			$stock_exist = $this->db->get('inv_stock');

    			if($stock_exist->num_rows() > 0) {

    				$this->db->query(
    					"UPDATE `inv_stock` SET case_qty = case_qty + ?, unit_qty = unit_qty + ? WHERE distributor_id = ? AND product_id = ?",
    					[(int)$case_qty, (int)$unit_qty, (int)$distributor, (int)$productid]
    				);

    			} else {

    				$stock = array(
    					'case_qty'		=> $case_qty,
    					'unit_qty'		=> $unit_qty,
    					'distributor_id'=> $distributor,
    					'product_id'	=> $productid,
    					'v_date'		=> date('Y-m-d'),
    					'stock_type'	=> 1
    				);

    				$this->db->insert('inv_stock',$stock);

    			}
				
				
			}
		}

		return true;

	}





	public function get_batchproducts_by_batch_id($batch_id)
	{
		$this->db->select("inv_batch_details.*, product_tbl.product_name");

		$this->db->from('inv_batch_details');

		$this->db->join('product_tbl', "product_tbl.product_id = inv_batch_details.product_id", "left");

		$this->db->where("inv_batch_details.batch_id", $batch_id);

		$result = $this->db->get()->result();

		return $result;

	}



	public function get_all_teams()
	{
		$this->db->order_by('team_id', 'desc');
		$result = $this->db->get('setup_team_tbl')->result();
		return $result;
	}


}