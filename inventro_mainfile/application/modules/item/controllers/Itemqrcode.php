<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Itemqrcode extends MX_Controller {

    function __construct() {
        parent::__construct();
        $this->load->library('ciqrcode');
        $this->load->model('item_model');
    }
    //QR-Code Generator
    public function qrgenerator($product_id) {
        $config['cacheable'] = true; //boolean, the default is true
        $config['cachedir'] = ''; //string, the default is application/cache/
        $config['errorlog'] = ''; //string, the default is application/logs/
        $config['quality'] = true; //boolean, the default is true
        $config['size'] = '1024'; //interger, the default is 1024
        $config['black'] = array(224, 255, 255); // array, default is array(255,255,255)
        $config['white'] = array(70, 130, 180); // array, default is array(0,0,0)
        $this->ciqrcode->initialize($config);
        //Create QR code image create

        $params['data'] = $product_id;
        $params['level'] = 'H';
        $params['size'] = 10;
        $image_name = $product_id . '.png';
        $params['savename'] = FCPATH . 'application/modules/item/assets/images/qr/' . $image_name;
        $this->ciqrcode->generate($params);
        $product_info = $this->item_model->findById($product_id);
        $company_info = $this->item_model->company_info();
        $data = array(
               'title'        => makeString(['print_barcode']),
            'company_name'    => $company_info[0]['title'],
            'product_name'    => $product_info->name,
            'product_model'   => $product_info->model,
            'price'           => $product_info->price,
            'qr_image'        => $image_name,
            'currency'        => '$',
            'position'        => '1',
        );

       $data['title']    = 'Barcode';
       $data['module']   = "item";
       $data['page']     = "barcode"; 
       $this->load->view('item/barcode', $data);
    }

}

?>