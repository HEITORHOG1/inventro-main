<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Auth_model extends CI_Model {


	/**
	 * Check if password_bcrypt column exists (cached per request).
	 */
	private $_has_bcrypt_column = null;

	public function has_bcrypt_column()
	{
		if ($this->_has_bcrypt_column === null) {
			$this->_has_bcrypt_column = $this->db->field_exists('password_bcrypt', 'user');
		}
		return $this->_has_bcrypt_column;
	}

	public function checkUser($data = array())
	{
		$select = "
				user.id,
				CONCAT_WS(' ', user.firstname, user.lastname) AS fullname,
				user.email,
				user.password,
				user.image,
				user.last_login,
				user.last_logout,
				user.ip_address,
				user.status,
				user.is_admin,
				IF (user.is_admin=1, 'Admin', 'User') as user_level
			";

		if ($this->has_bcrypt_column()) {
			$select = "
				user.id,
				CONCAT_WS(' ', user.firstname, user.lastname) AS fullname,
				user.email,
				user.password,
				user.password_bcrypt,
				user.image,
				user.last_login,
				user.last_logout,
				user.ip_address,
				user.status,
				user.is_admin,
				IF (user.is_admin=1, 'Admin', 'User') as user_level
			";
		}

		return $this->db->select($select)
			->from('user')
			->where('email', $data['email'])
			->get();
	}

	/**
	 * Update user password to bcrypt hash (transparent migration from MD5).
	 */
	public function update_password_bcrypt($user_id, $bcrypt_hash)
	{
		return $this->db->where('id', (int)$user_id)
			->update('user', ['password_bcrypt' => $bcrypt_hash]);
	}


	public function userPermission1($id = null)
	{
		
		$acc_tbl = $this->db->select('*')->from('sec_user_access_tbl')->where('fk_user_id',$id)->get()->result();

		if($acc_tbl!=NULL){

			$role_id = [];
			foreach ($acc_tbl as $key => $value) {
				$role_id[] = $value->fk_role_id;
			}

			$result = $this->db->select("
			module.directory, 
			module_permission.fk_module_id, 
			IF(SUM(module_permission.create)>=1,1,0) AS 'create', 
			IF(SUM(module_permission.read)>=1,1,0) AS 'read', 
			IF(SUM(module_permission.update)>=1,1,0) AS 'update', 
			IF(SUM(module_permission.delete)>=1,1,0) AS 'delete'
			")
			->from('module_permission')
			->join('module', 'module.id = module_permission.fk_module_id', 'full')
			->where_in('module_permission.fk_role_id', $role_id)
			->where('module.status', 1)
			->group_by('module_permission.fk_module_id')
			->group_start()
				->where('create', 1)
				->or_where('read', 1)
				->or_where('update', 1)
				->or_where('delete', 1)
			->group_end()
			->get()
			->result();

			return $result;
		} else {
			return 0;
		}
	}
	

	
	public function userPermission2($id = null)
	{
		
		$acc_tbl = $this->db->select('*')->from('sec_user_access_tbl')->where('fk_user_id',$id)->get()->result();

		if($acc_tbl!=NULL){

			$role_id = [];
			foreach ($acc_tbl as $key => $value) {
				$role_id[] = $value->fk_role_id;
			}

			$result = $this->db->select("
				sec_role_permission.role_id, 
				sec_role_permission.menu_id, 
				IF(SUM(sec_role_permission.can_create)>=1,1,0) AS 'create', 
				IF(SUM(sec_role_permission.can_access)>=1,1,0) AS 'read', 
				IF(SUM(sec_role_permission.can_edit)>=1,1,0) AS 'update', 
				IF(SUM(sec_role_permission.can_delete)>=1,1,0) AS 'delete',
				sec_menu_item.menu_title,
				sec_menu_item.page_url,
				sec_menu_item.module
				")
				->from('sec_role_permission')
				->join('sec_menu_item', 'sec_menu_item.menu_id = sec_role_permission.menu_id', 'full')
				->where_in('sec_role_permission.role_id', $role_id)
				->group_by('sec_role_permission.menu_id')
				->group_start()
					->where('can_create', 1)
					->or_where('can_access', 1)
					->or_where('can_edit', 1)
					->or_where('can_delete', 1)
				->group_end()
				->get()
				->result();
				return $result;
			} else {
				return 0;
		}
	}



	public function userPermission($id = null)
	{
		return $this->db->select("
			module.directory, 
			module_permission.fk_module_id, 
			module_permission.create, 
			module_permission.read, 
			module_permission.update, 
			module_permission.delete
			")
			->from('module_permission')
			->join('module', 'module.id = module_permission.fk_module_id', 'full')
			->where('module_permission.fk_user_id', $id)
			->where('module.status', 1)
			->group_start()
				->where('create', 1)
				->or_where('read', 1)
				->or_where('update', 1)
				->or_where('delete', 1)
			->group_end()
			->get()
			->result();
	}



	public function last_login($id = null)
	{
		return $this->db->set('last_login', date('Y-m-d H:i:s'))
			->set('ip_address', $this->input->ip_address())
			->where('id',$this->session->userdata('id'))
			->update('user');
	}

	public function last_logout($id = null)
	{
		return $this->db->set('last_logout', date('Y-m-d H:i:s'))
			->where('id', $this->session->userdata('id'))
			->update('user');
	}

	/**
	 * Check if the login_attempts table exists (cached per request).
	 */
	private $_rate_limit_available = null;

	private function _rate_limit_table_exists()
	{
		if ($this->_rate_limit_available === null) {
			$this->_rate_limit_available = $this->db->table_exists('login_attempts');
		}
		return $this->_rate_limit_available;
	}

	/**
	 * Count failed login attempts for email/IP within the lockout window.
	 * Returns 0 if rate limiting is unavailable (table missing).
	 */
	public function count_login_attempts($email, $ip, $window_minutes = 15)
	{
		if (!$this->_rate_limit_table_exists()) {
			return 0;
		}
		try {
			$cutoff = date('Y-m-d H:i:s', strtotime("-{$window_minutes} minutes"));
			return $this->db->from('login_attempts')
				->group_start()
					->where('email', $email)
					->or_where('ip_address', $ip)
				->group_end()
				->where('attempted_at >=', $cutoff)
				->count_all_results();
		} catch (Exception $e) {
			log_message('error', 'Rate limiting query failed: ' . $e->getMessage());
			return 0;
		}
	}

	/**
	 * Record a failed login attempt. Fails silently if table is missing.
	 */
	public function record_login_attempt($email, $ip)
	{
		if (!$this->_rate_limit_table_exists()) {
			return;
		}
		try {
			$this->db->insert('login_attempts', [
				'email'        => $email,
				'ip_address'   => $ip,
				'attempted_at' => date('Y-m-d H:i:s'),
			]);
		} catch (Exception $e) {
			log_message('error', 'Record login attempt failed: ' . $e->getMessage());
		}
	}

	/**
	 * Clear login attempts after successful login. Fails silently if table is missing.
	 */
	public function clear_login_attempts($email, $ip)
	{
		if (!$this->_rate_limit_table_exists()) {
			return;
		}
		try {
			$this->db->where('email', $email)
				->or_where('ip_address', $ip)
				->delete('login_attempts');
		} catch (Exception $e) {
			log_message('error', 'Clear login attempts failed: ' . $e->getMessage());
		}
	}

	/**
	 * Purge old login attempts (older than 24h). Fails silently if table is missing.
	 */
	public function purge_old_attempts()
	{
		if (!$this->_rate_limit_table_exists()) {
			return;
		}
		try {
			$cutoff = date('Y-m-d H:i:s', strtotime('-24 hours'));
			$this->db->where('attempted_at <', $cutoff)->delete('login_attempts');
		} catch (Exception $e) {
			log_message('error', 'Purge old attempts failed: ' . $e->getMessage());
		}
	}

}
