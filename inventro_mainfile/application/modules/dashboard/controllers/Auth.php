<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends MX_Controller {
 	
 	public function __construct()
 	{
 		parent::__construct();

 		$this->load->model(array(
 			'auth_model' 
 		));

 	}



	public function index()
	{
	
		if ($this->session->userdata('isLogIn'))
		redirect('dashboard/home');
		$data['title']    = makeString(['login']); 

		$this->form_validation->set_rules('email', makeString(['email']), 'required|valid_email|max_length[100]|trim');
		$this->form_validation->set_rules('password', makeString(['password']), 'required|max_length[128]|trim');
	


		
		$data['user'] = (object)$userData = array(
			'email' 	 => $this->input->post('email',TRUE),
			'password'   => $this->input->post('password',TRUE),
		);
		
		if ( $this->form_validation->run())
		{
			$email = $this->input->post('email', TRUE);
			$ip = $this->input->ip_address();

			// Rate limiting: block after 5 failed attempts within 15 minutes
			$max_attempts = 5;
			$lockout_minutes = 15;
			$attempts = $this->auth_model->count_login_attempts($email, $ip, $lockout_minutes);

			if ($attempts >= $max_attempts) {
				$this->session->set_flashdata('exception', makeString(['too_many_login_attempts']) ?: 'Muitas tentativas de login. Aguarde 15 minutos.');
				redirect('login');
				return;
			}

			// Purge old attempts occasionally (1 in 10 chance)
			if (mt_rand(1, 10) === 1) {
				$this->auth_model->purge_old_attempts();
			}

			$plain_password = $this->input->post('password', TRUE);
			$user = $this->auth_model->checkUser($userData);

			if($user->num_rows() > 0) {

				$row = $user->row();
				$password_valid = false;

				// 1) Try bcrypt first (already migrated users)
				if (!empty($row->password_bcrypt)) {
					$password_valid = password_verify($plain_password, $row->password_bcrypt);
				}

				// 2) Fallback: legacy MD5 (transparent migration)
				if (!$password_valid && !empty($row->password)) {
					if (md5($plain_password) === $row->password) {
						$password_valid = true;
						// Migrate to bcrypt on the fly
						$bcrypt_hash = password_hash($plain_password, PASSWORD_BCRYPT, ['cost' => 12]);
						$this->auth_model->update_password_bcrypt($row->id, $bcrypt_hash);
					}
				}

				if (!$password_valid) {
					$this->auth_model->record_login_attempt($email, $ip);
					$this->session->set_flashdata('exception', makeString(['incorrect_email_or_password']));
					redirect('login');
					return;
				}

				$checkPermission = $this->auth_model->userPermission2($user->row()->id);

				if($checkPermission!=NULL){
					
					$permission = array();
					$permission1 = array();

					if(!empty($checkPermission)){
						foreach ($checkPermission as $value) {
							
							$permission[$value->module] = array( 
								'create' => $value->create,
								'read'   => $value->read,
								'update' => $value->update,
								'delete' => $value->delete
							);

							$permission1[$value->menu_title] = array( 
								'create' => $value->create,
								'read'   => $value->read,
								'update' => $value->update,
								'delete' => $value->delete
							);
						}
					} 
				}



				if($user->row()->is_admin == 2){
					$email = $user->row()->email;
				}


					$sData = array(

						'isLogIn' 	  => true,
						'isAdmin' 	  => (($user->row()->is_admin == 1)?true:false),
						'user_type'   => $user->row()->is_admin,
						'id' 		  => $user->row()->id,
						'sot_name'   => @$client_row->spacial_outlet_name,
						'fullname'	  => $user->row()->fullname,
						'user_level'  => $user->row()->user_level,

						'email' 	  => $user->row()->email,
						'image' 	  => $user->row()->image,
						'last_login'  => $user->row()->last_login,
						'last_logout' => $user->row()->last_logout,
						'ip_address'  => $user->row()->ip_address,
						'permission'  => json_encode(@$permission),
						'label_permission'  => json_encode(@$permission1),
						'plano_negocio' => $this->_get_plano_negocio()
						);


						//store date to session
						$this->session->set_userdata($sData);
						//update database status
						$this->auth_model->last_login();
						// Clear failed login attempts on success
						$this->auth_model->clear_login_attempts($email, $ip);
						//welcome message
						$this->session->set_flashdata('message', makeString(['welcome_back']).' '.$user->row()->fullname);
						redirect('dashboard/dashboard_dis');

			} else {
				$this->auth_model->record_login_attempt($email, $ip);
				$this->session->set_flashdata('exception', makeString(['incorrect_email_or_password']));
				redirect('login');
			}

			

		} else {
			echo Modules::run('template/login', $data);
		}
		

	}
  
	public function logout()
	{
		//update database status
		$this->auth_model->last_logout();
		//destroy session
		$this->session->sess_destroy();
		redirect('login');
	}

	/**
	 * Busca o plano de negócio da tabela setting.
	 *
	 * @return string
	 */
	private function _get_plano_negocio()
	{
		$setting = $this->db->select('plano_negocio')->from('setting')->get()->row();
		return !empty($setting->plano_negocio) ? $setting->plano_negocio : 'mercado_completo';
	}

}
