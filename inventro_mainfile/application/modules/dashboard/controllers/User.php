<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends MX_Controller {
 	
 	public function __construct()
 	{
 		parent::__construct();
 		$this->load->model(array(
 			'user_model'  
 		));
 		
		if (! $this->session->userdata('isAdmin'))
			redirect('login');
 	}


 	function count_rows(){

		$this->db->select("
				user.*, 
				CONCAT_WS(' ', firstname, lastname) AS fullname
			");
			$this->db->from('user');
		return $this->db->get()->num_rows();

 	}


	public function index(){


		$limit  = 20;
        @$start = ($this->uri->segment(4)?$this->uri->segment(4):0);
        $total_rows = $this->count_rows();

        $config = $this->pasination($limit,'user','dashboard/user/index',$total_rows);
        $this->pagination->initialize($config);

        $data["links"] = $this->pagination->create_links();
        
		$data['users'] 		= $this->user_model->read($limit,$start);



		$data['title']      = makeString(['user_list']);
		$data['module'] 	= "dashboard";  
		$data['page']   	= "user/_list_aj"; 

		echo Modules::run('template/layout', $data); 
		
	}
 


	public function get_user_list(){

        // POST data
        $postData = $this->input->post();

        $search = (object) array(
			'email'=>trim($this->input->post('email',TRUE)),
			'firstname'=>trim($this->input->post('firstname',TRUE)),
			'lastname'=>trim($this->input->post('lastname',TRUE))
		);

        // Get data
        $data = $this->user_model->get_user_list($postData,$search);
        echo json_encode($data);

	}




    public function email_check($email, $id){
     
        $emailExists = $this->db->select('email')
            ->where('email',$email) 
            ->where_not_in('id',$id) 
            ->get('user')
            ->num_rows();

        if ($emailExists > 0) {
            $this->form_validation->set_message('email_check', 'The {field} is already registered.');
            return false;
        } else {
            return true;
        }
    } 


 
	public function form($id = null){ 

		$data['title']    = makeString(['add_user']);
		
		$this->form_validation->set_rules('firstname', makeString(['firstname']),'required|max_length[50]');
		$this->form_validation->set_rules('lastname', makeString(['lastname']),'required|max_length[50]');
	
		if (!empty($id)) {   
       		$this->form_validation->set_rules('email', makeString(['email']), "required|valid_email|max_length[100]");
		} else {
			$this->form_validation->set_rules('email', makeString(['email']),'required|valid_email|is_unique[user.email]|max_length[100]');
		}
	
		$this->form_validation->set_rules('password', makeString(['password']),'required|max_length[32]|md5');
		$this->form_validation->set_rules('about', makeString(['about']),'max_length[1000]');
		$this->form_validation->set_rules('status', makeString(['status']),'required|max_length[1]');
		$this->form_validation->set_rules('matricula', 'Matrícula (PDV)', 'max_length[20]');
		
		
        $config['upload_path']          = './admin_assets/img/user/';
        $config['allowed_types']        = 'gif|jpg|png'; 

        $this->load->library('upload', $config);
		// $this->load->library('upload');
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
			$this->session->set_flashdata('message', makeString(['image_upload_successfully']));
        }
		$this->upload->initialize($config);
        
        



		
		$password=$this->input->post('password',TRUE);
		$matricula_value = $this->input->post('matricula',TRUE);
		$data['user'] = (object)$userLevelData = array(
			'id' 		  => $this->input->post('id',TRUE),
			'firstname'   => $this->input->post('firstname',TRUE),
			'lastname' 	  => $this->input->post('lastname',TRUE),
			'email' 	  => $this->input->post('email',TRUE),
			'matricula'   => !empty($matricula_value) ? $matricula_value : null,
			'password' 	  => md5(!empty($password) ? $password : ''),
			'about' 	  => $this->input->post('about',TRUE),
			'image'   	  => (!empty($image)?$image:$this->input->post('old_image',TRUE)),
			'last_login'  => null,
			'last_logout' => null,
			'ip_address'  => null,
			'status' 	  => $this->input->post('status') ? 1 : 0,
			'is_admin'    => ($this->input->post('type',TRUE)?$this->input->post('type',TRUE):'2')
		);




		
		if ($this->form_validation->run()) {

			
			if (empty($userLevelData['id'])) {

				if ($this->db->insert('user', $userLevelData)) {
					// user id
					$fk_user_id = $this->db->insert_id();
					// inser role in sec_user_access_tbl
					$rolData = array(
						'fk_role_id' 	=> $this->input->post('fk_role_id',TRUE),
						'fk_user_id' 	=> $fk_user_id,
					);
					if(!empty($this->input->post('fk_role_id',TRUE))){
						$this->db->insert('sec_user_access_tbl', $rolData);
					}
					// END----

					$this->session->set_flashdata('message', makeString(['save_successfully']));

				} else {
					$this->session->set_flashdata('exception', makeString(['please_try_again']));
				}
				redirect("dashboard/user/form/");

			} else {
				
				if ($this->user_model->update($userLevelData)) {
					
			

					$this->session->set_flashdata('message', makeString(['update_successfully']));

				} else {

					$this->session->set_flashdata('exception', makeString(['please_try_again']));

				}

				redirect("dashboard/user/form/$id");
			}




		} else {

			if(!empty($id))
			$data['user']   = $this->user_model->single($id);
			$data['role'] 		= $this->user_model->role();
			$data['module'] = "dashboard";  
			$data['page']   = "user/form"; 
			
			echo Modules::run('template/layout', $data);
		}
	}




	/**
	 * AJAX: Check matricula uniqueness
	 * Returns JSON {unique: bool}
	 */
	public function check_matricula() {
		header('Content-Type: application/json');

		$matricula = $this->input->post('matricula', TRUE);
		$user_id = $this->input->post('user_id', TRUE);

		if (empty($matricula)) {
			echo json_encode(['unique' => true, 'csrf_token' => $this->security->get_csrf_hash()]);
			return;
		}

		$this->db->where('matricula', $matricula);
		if (!empty($user_id)) {
			$this->db->where('id !=', (int)$user_id);
		}
		$existing = $this->db->get('user')->row();

		echo json_encode([
			'unique' => empty($existing),
			'csrf_token' => $this->security->get_csrf_hash()
		]);
	}

	public function delete($id = null)
	{
		if ($this->user_model->delete($id)) {
			$this->session->set_flashdata('message', makeString(['delete_successfully']));
		} else {
			$this->session->set_flashdata('exception', makeString(['please_try_again']));
		}

		redirect("dashboard/user/index");
	}


	public function pasination($limit,$tbl,$url,$total_rows=NULL){
	       

	        $config["base_url"] = base_url($url);
	        $config["total_rows"] = $total_rows;
	        $config["per_page"] = $limit;
	    
	        $config['first_url'] = $config['base_url'];
	        // integrate bootstrap pagination
	        $config['full_tag_open'] = "<ul class='pagination col-xs pull-right'>";
	        $config['full_tag_close'] = "</ul>";
	        $config['num_tag_open'] = '<li>';
	        $config['num_tag_close'] = '</li>';
	        $config['cur_tag_open'] = "<li class='disabled'><li class='active'><a href='#'>";
	        $config['cur_tag_close'] = "<span class='sr-only'></span></a></li>";
	        $config['next_tag_open'] = "<li>";
	        $config['next_tag_close'] = "</li>";
	        $config['prev_tag_open'] = "<li>";
	        $config['prev_tagl_close'] = "</li>";
	        $config['first_tag_open'] = "<li>";
	        $config['first_tagl_close'] = "</li>";
	        $config['last_tag_open'] = "<li>";
	        $config['last_tagl_close'] = "</li>";
	        return $config;
	}



}
