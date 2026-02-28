<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard_dis extends MX_Controller {
 	
 	public function __construct()
 	{
 		parent::__construct();
		if (! $this->session->userdata('isLogIn'))
			redirect('login');
 	}
 

	public function index()
	{   
		if($this->session->userdata('user_type')==1) {
		
			redirect('dashboard/home');

		}if($this->session->userdata('user_type')==2) {

			
			redirect('dashboard/home');

		}if($this->session->userdata('user_type')==3) {
			redirect('dashboard/home');
		}
	}
	
}
