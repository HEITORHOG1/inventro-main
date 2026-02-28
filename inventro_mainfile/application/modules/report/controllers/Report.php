<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Report extends MX_Controller
{

    public $data = [];

    public function __construct()
    {

        parent::__construct();
        $this->permission->module()->redirect();
        $this->load->model(array(
            'report_model',

        ));

    }


    public function purchase_report()
    {
        $data['title'] = makeString(['purchase']) . ' ' . makeString(['report']);
        $data['module'] = "report";
        $data['totalpurchase'] = $this->db->count_all('product_purchase');
        $data['supplier_list'] = $this->report_model->supplier_list();
        $data['page'] = "purchase_report";

        echo Modules::run('template/layout', $data);
    }

    public function CheckPurchasereport()
    {
        $postData = $this->input->post();
        $data = $this->report_model->getPurchaseList($postData);
        echo json_encode($data);
    }

    public function sales_report()
    {
        $data['title'] = makeString(['sales']) . ' ' . makeString(['report']);
        $data['module'] = "report";
        $data['totalsales'] = $this->db->count_all('invoice_tbl');
        $data['customer_list'] = $this->report_model->customer_list();
        
        $data['page'] = "sales_report";

        echo Modules::run('template/layout', $data);
    }

    public function CheckSalesReport()
    {
        $postData = $this->input->post();
        $data = $this->report_model->getSalesList($postData);
        echo json_encode($data);
    }


    public function cash_book()
    {
        $data['title'] = makeString(['cash']) . ' ' . makeString(['book']);
        $data['module'] = "report";
        $data['cash_books'] = $this->report_model->cash_book_list();
        $data['total_transaction'] =$this->db->where('paid_amount',0)->from("invoice_tbl")->count_all_results();

        $data['page'] = "cash_book_report";

        echo Modules::run('template/layout', $data);
    }


    public function CheckCashBookReport()
    {

        $postData = $this->input->post();
        $data = $this->report_model->getCashBookReports($postData);
        echo json_encode($data);
    }


     public function getBankBookreport()
    {
        $postData = $this->input->post();
        $data = $this->report_model->getBankBookreport($postData);
        echo json_encode($data);
    }



    public function bank_book()
    {
        $data['title'] = makeString(['bank']) . ' ' . makeString(['book']);
        $data['module'] = "report";
        $data['bank_list'] = $this->report_model->bank_book_list();

        $data['page'] = "bank_book_report";

        echo Modules::run('template/layout', $data);
    }


}