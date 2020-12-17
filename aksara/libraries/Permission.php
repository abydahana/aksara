<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Permission library
 * Handle the permission of user role
 *
 * @version		2.1
 * @author		Aby Dahana
 * @profile		abydahana.github.io
 */
class Permission
{
	private $_ci;
	
	public function __construct()
	{
		$this->_ci									=& get_instance();
		$this->_ci->load->library('session');
	}
	
	/**
	 * allow
	 */
	public function allow($module = null, $submodule = null, $controller = null, $method = null)
	{
		if(!$method)
		{
			$method									= 'index';
		}
		
		if(!$this->_ci->db->get_where('app__users', array('user_id' => $this->_ci->session->userdata('user_id'), 'status' => 1), 1)->num_rows() && ($this->_ci->db->table_exists('pos__employees') && !$this->_ci->db->get_where('pos__employees', array('employee_id' => $this->_ci->session->userdata('user_id'), 'status' => 1), 1)->num_rows()))
		{
			$this->_ci->session->sess_destroy();
			return throw_exception(403, phrase('you_do_not_have_sufficient_privileges_to_access_the_requested_page'), base_url());
		}
		
		$privileges									= $this->_ci->db->select('group_privileges')->get_where('app__groups', array('group_id' => $this->_ci->session->userdata('group_id')), 1)->row('group_privileges');
		$privileges									= json_decode($privileges, true);
		
		if(!isset($privileges[$module][$submodule][$controller]) || (isset($privileges[$module][$submodule][$controller]) && !in_array($method, $privileges[$module][$submodule][$controller])))
		{
			if(method_exists($controller, $method) || ($controller && in_array($method, array('index', 'create', 'read', 'update', 'delete', 'export', 'print', 'pdf'))))
			{
				/* push to group privileges */
				$this->_push_privileges($module, $submodule, $controller, $method);
			}
			
			return false;
		}
		else
		{
			/* write log activities */
			if('modal' != $this->_ci->input->post('prefer'))
			{
				/* only if request is not from session storage */
				$this->_push_logs($module, $submodule, $controller, $method);
			}
			
			return true;
		}
		
		return false;
	}
	
	/**
	 * restrict
	 */
	public function restrict($module = null, $submodule = null, $controller = null, $method = null)
	{
		if(!$method)
		{
			$method									= 'index';
		}
		
		$privileges									= $this->_ci->db->select('group_privileges')->get_where('app__groups', array('group_id' => $this->_ci->session->userdata('group_id')), 1)->row('group_privileges');
		$privileges									= json_decode($privileges, true);
		
		if(isset($privileges[$module][$submodule][$controller]) && in_array($method, $privileges[$module][$submodule][$controller]))
		{
			return throw_exception(403, phrase('you_do_not_have_sufficient_privileges_to_access_this_page'));
		}
		else
		{
			return throw_exception(403, phrase('you_do_not_have_sufficient_privileges_to_access_this_page'));
		}
	}
	
	/**
	 * visible
	 */
	public function visible($module = null, $controller = null, $method = null)
	{
		if(!$method)
		{
			$method									= 'index';
		}
		
		$privileges									= $this->_ci->db->select('group_privileges')->get_where('app__groups', array('group_id' => $this->_ci->session->userdata('group_id')), 1)->row('group_privileges');
		$privileges									= json_decode($privileges, true);
		
		if(isset($privileges[$controller]) && !in_array($method, $privileges[$controller]))
		{
			return true;
		}
		
		return false;
	}
	
	/**
	 * hide
	 */
	public function hide($module = null, $controller = null, $method = null)
	{
		if(!$method)
		{
			$method									= 'index';
		}
		
		$privileges									= $this->_ci->db->select('group_privileges')->get_where('app__groups', array('group_id' => $this->_ci->session->userdata('group_id')), 1)->row('group_privileges');
		$privileges									= json_decode($privileges, true);
		
		if(!isset($privileges[$controller]) || (isset($privileges[$controller]) && !in_array($method, $privileges[$controller])))
		{
			return true;
		}
		
		return false;
	}
	
	/**
	 * must_ajax
	 */
	public function must_ajax($redirect = null)
	{
		if(!$this->_ci->input->is_ajax_request())
		{
			$this->_ci->session->set_flashdata('error', phrase('you_cannot_perform_the_requested_action'));
			redirect($redirect);
		}
	}
	
	/**
	 * push privilege into the table
	 */
	private function _push_privileges($module = null, $submodule = null, $controller = null, $method = null)
	{
		$privileges									= $this->_ci->db->select('privileges')->get_where('app__groups_privileges', array('module' => $module, 'submodule' => $submodule, 'controller' => $controller), 1)->row('privileges');
		$privileges									= json_decode($privileges, true);
		if($privileges)
		{
			if(!in_array($method, $privileges))
			{
				$privileges[]						= $method;
				$prepare							= array
				(
					'privileges'					=> json_encode($privileges),
					'last_generated'				=> date('Y-m-d H:i:s')
				);
				$this->_ci->db->update
				(
					'app__groups_privileges',
					$prepare,
					array
					(
						'module'					=> $module,
						'submodule'					=> $submodule,
						'controller'				=> $controller
					),
					1
				);
			}
		}
		else
		{
			$checker								= $this->_ci->db->get_where
			(
				'app__groups_privileges',
				array
				(
					'module'						=> $module,
					'submodule'						=> $submodule,
					'controller'					=> $controller
				),
				1
			)
			->row();
			
			if(!$checker)
			{
				$privileges[]						= $method;
				$prepare							= array
				(
					'module'						=> $module,
					'submodule'						=> $submodule,
					'controller'					=> $controller,
					'privileges'					=> json_encode($privileges),
					'last_generated'				=> date('Y-m-d H:i:s')
				);
				$this->_ci->db->insert('app__groups_privileges', $prepare);
			}
		}
	}
	
	/**
	 * push to log activities
	 */
	private function _push_logs($module = null, $submodule = null, $controller = null, $method = null)
	{
		if($this->_ci->agent->is_browser())
		{
			$user_agent								= $this->_ci->agent->browser() . ' ' . $this->_ci->agent->version();
		}
		elseif($this->_ci->agent->is_robot())
		{
			$user_agent								= $this->_ci->agent->robot();
		}
		elseif($this->_ci->agent->is_mobile())
		{
			$user_agent								= $this->_ci->agent->mobile();
		}
		else
		{
			$user_agent								= phrase('undefined');
		}
		
		$platform									= $this->_ci->agent->platform();
		$prepare									= array
		(
			'user_id'								=> get_userdata('user_id'),
			'module'								=> $module,
			'submodule'								=> $submodule,
			'controller'							=> $controller,
			'page'									=> str_replace(base_url(), null, current_page()),
			'method'								=> $method,
			'ip_address'							=> $this->_ci->input->ip_address(),
			'browser'								=> $user_agent,
			'platform'								=> $platform,
			'timestamp'								=> date('Y-m-d H:i:s')
		);
		
		$this->_ci->db->insert('app__activity_logs', $prepare);
	}
}