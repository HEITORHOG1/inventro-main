<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends MX_Controller {
 	
 	
 	public function __construct(){
 		
 		parent::__construct();

 		
 		$this->load->model(array(
 			'home_model',
 			'dashboard_model'
 		)); 

		if (! $this->session->userdata('isLogIn'))
			redirect('login');
		 
 	}
 

	public function index(){  

		$data['title']    = html_escape("Home");
		#page path 
		$data['totalinvoice'] = $this->db->count_all('invoice_tbl'); 
		$data['totalpurchase'] = $this->db->count_all('product_purchase'); 
		$data['totalcustomer'] = $this->db->count_all('customer_tbl'); 
		$data['totalproduct'] = $this->db->count_all('product_tbl');
		$data['purchaseamount'] = $this->dashboard_model->totalpurchase();
		$data['saleamount']     = $this->dashboard_model->totalsale();
		$data['currency']       = $this->dashboard_model->currencyinfo();
		$totalpurchase='';
	                  for ($i=1; $i <= 12; $i++) {
                               $purchase = $this->dashboard_model->yearly_purchase_report($i);
                               $totalpurchase .= number_format((float)($purchase->total_purchase ?? 0), 2, '.', '') . ',';
                            }
                      $totalsale='';
                    for ($i=1; $i <= 12; $i++) {
                               $sale = $this->dashboard_model->yearly_invoice_report($i);
                               $totalsale .= number_format((float)($sale->total_sale ?? 0), 2, '.', '') . ',';
                            }
        $data['purchasetotal'] = $totalpurchase;
        $data['saletotal'] = $totalsale;
		$data['module'] = "dashboard";  
		$data['page']   = "__dashboard"; 
		echo Modules::run('template/layout', $data); 

	}


	public function profile(){

		$data['title']  = html_escape("Profile");
		$data['module'] = "dashboard";  
		$data['page']   = "setting/__profile";  

		$id = $this->session->userdata('id');
		$data['user']   = $this->home_model->profile($id);

		echo Modules::run('template/layout', $data);  
	}

	

	public function profile_setting(){ 

		$data['title']    = html_escape("Profile Setting");
		$id = $this->session->userdata('id');
		
		$this->form_validation->set_rules('firstname', 'First Name','required|max_length[50]');
		$this->form_validation->set_rules('lastname', 'Last Name','required|max_length[50]');
		#------------------------#
       	$this->form_validation->set_rules('email', 'Email Address', "required|valid_email|max_length[100]");
       	/*---#callback fn not supported#---*/ 
		$this->form_validation->set_rules('password', 'Password','required|max_length[32]|md5');
		$this->form_validation->set_rules('about', 'About','max_length[1000]');
		
        $config['upload_path']          = './admin_assets/img/user/';
        $config['allowed_types']        = 'gif|jpg|png'; 

        $this->load->library('upload', $config);
 
        if ($this->upload->do_upload('image')) {  

            $data = $this->upload->data();  
            $image = $config['upload_path'].$data['file_name']; 

			$config['image_library']  = 'gd2';
			$config['source_image']   = $image;
			$config['create_thumb']   = false;
			$config['maintain_ratio'] = TRUE;
			$config['width']          = 115;
			$config['height']         = 90;
			$this->load->library('image_lib', $config);
			$this->image_lib->resize();
			$this->session->set_flashdata('message', "Image Upload Successfully!");
        }
		$password=$this->input->post('password',TRUE);
		$data['user'] = (object)$userData = array(
			'id' 		  => $this->input->post('id',TRUE),
			'firstname'   => $this->input->post('firstname',TRUE),
			'lastname' 	  => $this->input->post('lastname',TRUE),
			'email' 	  => $this->input->post('email',TRUE),
			'password' 	  => md5(!empty($password) ? $password : ''),
			'about' 	  => $this->input->post('about',TRUE),
			'image'   	  => (!empty($image)?$image:$this->input->post('old_image',TRUE)) 
		);

		
		if ($this->form_validation->run()) {

	        if (empty($userData['image'])) {
				$this->session->set_flashdata('exception', $this->upload->display_errors()); 
	        }

			if ($this->home_model->setting($userData)) {

				$this->session->set_userdata(array(
					'fullname'   => $this->input->post('firstname',TRUE). ' ' .$this->input->post('lastname',TRUE),
					'email' 	  => $this->input->post('email',TRUE),
					'image'   	  => (!empty($image)?$image:$this->input->post('old_image',TRUE))
				));


				$this->session->set_flashdata('message', makeString(['update_successfully']));
			} else {
				$this->session->set_flashdata('exception',  makeString(['please_try_again']));
			}

			redirect("dashboard/home/profile_setting");

		} else {

			$data['module'] = "dashboard";  
			$data['page']   = "setting/__profile_setting"; 
			if(!empty($id))
			$data['user']   = $this->home_model->profile($id);
			echo Modules::run('template/layout', $data);

		}
	}

}
