<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Template_model extends CI_Model {

	public function notifications($id = null)
	{
		return  $this->db->select("COUNT(receiver_id) as total")
			->from('message')
			->where('receiver_id', $id)
			->where('receiver_status', 0)
			->group_by('receiver_status')
			->get()->row();
	}

	public function messages($id = null)
	{
		return $this->db->select("
				message.id, 
				message.subject, 
				message.message, 
				message.datetime, 
				user.image, 
				IF (ISNULL(user.firstname) || ISNULL(user.lastname), 'Unauthorized', CONCAT_WS(' ', user.firstname, user.lastname)) AS sender_name
			")
			->from("message")
			->join('user', 'user.id = message.sender_id','left')
			->where('message.receiver_id', $id)
			->where('message.receiver_status', 0)
			->order_by('message.datetime','desc')
			->get()
			->result();
	} 
 
	public function setting()
	{
		return $this->db->get('setting')->row();
	}


	public function visit_order_notify()
	{
		return  $this->db->select("notify_alert")
			->from('sales_order_tbl')
			->where('order_date',date('Y-m-d'))
			->get()
			->num_rows();
	}


	public function oos_list(){


		$total = $this->db->where('visit_type',1)->where('schedule_date',date('Y-m-d'))->where('is_visited',1)->get('visit_schedule_tbl')->num_rows();
		return $total;


	}


}
 