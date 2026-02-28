<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Setting extends MX_Controller {

	public function __construct()
	{
		parent::__construct();
		
		$this->load->model(array(
			'setting_model'
		));
		if (!$this->session->userdata('isAdmin')) 
		redirect('login'); 
	}
 

	public function index()
	{
		$data['title'] = makeString(['application_setting']);
		
		//check setting table row if not exists then insert a row
		$this->check_setting();
		
		$data['languageList'] = $this->languageList(); 
		$data['currencyList'] = $this->setting_model->currencyList(); 
		$data['setting'] = $this->setting_model->read(); 

		$data['module'] = "dashboard";  
		$data['page']   = "setting/__setting";  
		echo Modules::run('template/layout', $data); 

	} 

	public function create()
	{
		$data['title'] = makeString(['application_setting']);
		
		$this->form_validation->set_rules('title',makeString(['application_title']),'required|max_length[50]');
		$this->form_validation->set_rules('address', makeString(['address']) ,'max_length[255]');
		$this->form_validation->set_rules('email',makeString(['email']),'max_length[100]|valid_email');
		$this->form_validation->set_rules('phone',makeString(['phone']),'max_length[20]');
		$this->form_validation->set_rules('language',makeString(['language']),'max_length[250]'); 
		$this->form_validation->set_rules('footer_text',makeString(['footer_text']),'max_length[255]'); 
		
		//logo upload
		$logo = $this->fileupload->do_upload(
			'admin_assets/img/icons/',
			'logo'
		);
		// if logo is uploaded then resize the logo
		if ($logo !== false && $logo != null) {
			$this->fileupload->do_resize(
				$logo, 
				210,
				48
			);
		}
		//if logo is not uploaded
		if ($logo === false) {
			$this->session->set_flashdata('exception', makeString(['invalid_logo']));
		}


		//favicon upload
		$favicon = $this->fileupload->do_upload(
			'admin_assets/img/icons/',
			'favicon'
		);
		// if favicon is uploaded then resize the favicon
		if ($favicon !== false && $favicon != null) {
			$this->fileupload->do_resize(
				$favicon, 
				32,
				32
			);
		}
		//if favicon is not uploaded
		if ($favicon === false) {
			$this->session->set_flashdata('exception',  makeString(['invalid_favicon']));
		}		
		

		$data['setting'] = (object)$postData = [
			'id'          => $this->input->post('id',TRUE),
			'title' 	  => $this->input->post('title',TRUE),
			'address' 	  => $this->input->post('address',TRUE),
			'email' 	  => $this->input->post('email',TRUE),
			'phone' 	  => $this->input->post('phone',TRUE),
			'logo' 	      => (!empty($logo)?$logo:$this->input->post('old_logo',TRUE)),
			'favicon' 	  => (!empty($favicon)?$favicon:$this->input->post('old_favicon',TRUE)),
			'language'    => $this->input->post('language',TRUE), 
			'currency'	  => $this->input->post('currency',TRUE),
			'site_align'  => $this->input->post('site_align',TRUE), 
			'footer_text' => $this->input->post('footer_text', TRUE),
			'timezone'		=>$this->input->post('timezone',TRUE)
		]; 
		
		if ($this->form_validation->run() === true) {

			#if empty $id then insert data
			if (empty($postData['id'])) {
				if ($this->setting_model->create($postData)) {
					#set success message
					$this->session->set_flashdata('message',makeString(['save_successfully']));
				} else {
					#set exception message
					$this->session->set_flashdata('exception',makeString(['please_try_again']));
				}
			} else {
				if ($this->setting_model->update($postData)) {
					#set success message
					$this->session->set_flashdata('message',makeString(['update_successfully']));
				} else {
					#set exception message
					$this->session->set_flashdata('exception', makeString(['please_try_again']));
				} 
			}
 
			redirect('dashboard/setting');

		} else { 
			$data['languageList'] = $this->languageList();
			$data['currencyList'] = $this->setting_model->currencyList(); 
			$data['module'] = "dashboard";  
			$data['page']   = "setting/__setting";  
			echo Modules::run('template/layout', $data); 
		} 
	}



	//check setting table row if not exists then insert a row
	public function check_setting()
	{
		if ($this->db->count_all('setting') == 0) {
			$this->db->insert('setting',[
				'title' => 'Dynamic Admin Panel',
				'address' => '124/A, Street, State-145, Demo',
				'footer_text' => '2025&copy;Copyright',
			]);
		}
	}


    public function languageList()
    { 
        if ($this->db->table_exists("language")) { 

                $fields = $this->db->field_data("language");

                $i = 1;
                foreach ($fields as $field)
                {  
                    if ($i++ > 2)
                    $result[$field->name] = ucfirst($field->name);
                }

                if (!empty($result)) return $result;
 

        } else {
            return false; 
        }
    }


}
