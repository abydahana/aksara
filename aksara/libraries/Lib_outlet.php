<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * The Outlet library
 *
 * Property of DWITRI Media
 */

class Lib_outlet
{
	private $_ci;
	private $_user_id;
	
	public function __construct()
	{
		$this->_ci									=& get_instance();
		
		if($this->_ci->session->userdata('outlet_id'))
		{
			$this->_user_id							= $this->_ci->db->select
			('
				pos__subscriptions.user_id
			')
			->join
			(
				'pos__subscriptions',
				'pos__subscriptions.subscription_id = pos__outlets.subscription_id'
			)
			->get_where
			(
				'pos__outlets',
				array
				(
					'pos__outlets.outlet_id'		=> $this->_ci->session->userdata('outlet_id')
				)
			)
			->row('user_id');
		}
		else
		{
			$this->_user_id							= $this->_ci->session->userdata('user_id');
		}
	}
	
	public function my_subscription_id()
	{
		if(get_userdata('outlet_id'))
		{
			return $this->_ci->db->select('subscription_id')->get_where
			(
				'pos__outlets',
				array
				(
					'outlet_id'						=> get_userdata('outlet_id')
				),
				1
			)
			->row('subscription_id');
		}
		else
		{
			return $this->_ci->db->select('subscription_id')->get_where
			(
				'pos__subscriptions',
				array
				(
					'user_id'							=> $this->_user_id
				),
				1
			)
			->row('subscription_id');
		}
	}
	
	public function my_outlet_list()
	{
		if(get_userdata('outlet_id'))
		{
			return array(get_userdata('outlet_id'));
		}
		
		$subscription_id							= $this->_ci->db->select('subscription_id')->get_where
		(
			'pos__subscriptions',
			array
			(
				'user_id'							=> $this->_user_id
			),
			1
		)
		->row('subscription_id');
		
		$query										= $this->_ci->db->select('outlet_id')->get_where
		(
			'pos__outlets',
			array
			(
				'subscription_id'					=> $subscription_id
			)
		)
		->result();
		
		$outlets									= array();
		if($query)
		{
			foreach($query as $key => $val)
			{
				$outlets[]							= $val->outlet_id;
			}
		}
		else
		{
			$outlets								= array(0);
		}
		
		return $outlets;
	}
	
	public function my_outlet_list_exception()
	{
		$subscription_id							= $this->_ci->db->select('subscription_id')->get_where
		(
			'pos__subscriptions',
			array
			(
				'user_id'							=> $this->_user_id
			),
			1
		)
		->row('subscription_id');
		
		$query										= $this->_ci->db->select('outlet_id')->get_where
		(
			'pos__outlets',
			array
			(
				'subscription_id'					=> $subscription_id
			)
		)
		->result();
		
		$outlets									= array();
		if($query)
		{
			foreach($query as $key => $val)
			{
				if($val->outlet_id == get_userdata('outlet_id')) continue;
				
				$outlets[]							= $val->outlet_id;
			}
		}
		else
		{
			$outlets								= array(0);
		}
		
		return $outlets;
	}
	
	public function my_renewal_list()
	{
		$subscription_id							= $this->_ci->db->select('subscription_id')->get_where
		(
			'pos__subscriptions',
			array
			(
				'user_id'							=> $this->_user_id
			),
			1
		)
		->row('subscription_id');
		
		$query										= $this->_ci->db->select('outlet_id')->get_where
		(
			'pos__outlets',
			array
			(
				'subscription_id'					=> $subscription_id,
				//'renewal_date >='					=> date('Y-m-d H:i:s', strtotime('-7 days')),
				'renewal_date <='					=> date('Y-m-d H:i:s', strtotime('+7 days'))
			)
		)
		->num_rows();
		
		return $query;
	}
}
