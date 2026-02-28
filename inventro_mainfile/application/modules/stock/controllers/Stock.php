<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Stock extends MX_Controller {

    public $data = [];
    
    public function __construct(){

        parent::__construct();
        $this->permission->module()->redirect();
        $this->load->model(array(
            'stock_model'
        ));

    }



    public function index(){
        $data['title'] = makeString(['stock_report']);
        $data['stocks'] = $this->stock_model->get_stock_list();
        $data['module'] = "stock";
        $data['page']   = "stock_list";
      
        echo Modules::run('template/layout', $data);

    }

    public function stock_report_supplier_wise(){

        $supplier_id =(!empty($this->input->post('supplier_id',TRUE))?$this->input->post('supplier_id',TRUE):'') ;


        $data['supplierinfo'] = $this->stock_model->get_supplier_by_id($supplier_id);

        $data['title'] = makeString(['stock_report_supplier_wise']);

        $data['suppliers'] = $this->stock_model->get_supplier_list();

        $data['stocks'] = $this->stock_model->get_stock_list_supplier_wise($supplier_id);
     
        $data['module'] = "stock";

        $data['page']   = "stock_list_supplier_wise";

        echo Modules::run('template/layout', $data);

    }

 public function stock_report_product_wise(){
        $product_id = $this->input->post('product_id');


        $data['title'] = makeString(['stock_report_product_wise']);
        $data['products'] = $this->stock_model->get_products_list();

        $data['stocks'] = $this->stock_model->get_stock_list_product_wise($product_id);
        $data['module'] = "stock";
        $data['page']   = "stock_list_product_wise";

        echo Modules::run('template/layout', $data);

    }







}