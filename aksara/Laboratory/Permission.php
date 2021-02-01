<?php namespace Aksara\Laboratory;
/**
 * Permission library
 * Handle the permission of user role
 *
 * @author			Aby Dahana
 * @profile			abydahana.github.io
 * @website			www.aksaracms.com
 * @since			version 4.0.0
 * @copyright		(c) 2021 - Aksara Laboratory
 */
use Aksara\Laboratory\Model;

class Permission
{
	public function __construct()
	{
		$this->model								= new Model();
	}
	
	/**
	 * allow
	 */
	public function allow($module = null, $submodule = null, $controller = null, $method = null, $user_id = 0)
	{
		if(!$method)
		{
			$method									= 'index';
		}
		
		$user										= $this->model->select('user_id, group_id')->get_where('app__users', array('user_id' => ($user_id ? $user_id : service('session')->get('user_id')), 'status' => 1), 1)->row();
		
		if(!$user)
		{
			/* destroy previous session to prevent hijacking */
			if(session_status() == PHP_SESSION_ACTIVE)
			{
				session_destroy();
			}
			
			return throw_exception(403, phrase('you_do_not_have_sufficient_privileges_to_access_the_requested_page'), base_url());
		}
		
		$privileges									= $this->model->select('group_privileges')->get_where('app__groups', array('group_id' => $user->group_id), 1)->row('group_privileges');
		$privileges									= json_decode($privileges, true);
		
		if(!isset($privileges[$module][$submodule][$controller]) || (isset($privileges[$module][$submodule][$controller]) && !in_array($method, $privileges[$module][$submodule][$controller])))
		{
			if(method_exists(service('router')->controllerName(), $method))
			{
				/* push to group privileges */
				$this->_push_privileges($module, $submodule, $controller, $method);
			}
			
			return false;
		}
		else
		{
			/* write log activities */
			if('modal' != service('request')->getPost('prefer'))
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
		
		$privileges									= $this->model->select('group_privileges')->get_where('app__groups', array('group_id' => service('session')->get('group_id')), 1)->row('group_privileges');
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
		
		$privileges									= $this->model->select('group_privileges')->get_where('app__groups', array('group_id' => service('session')->get('group_id')), 1)->row('group_privileges');
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
		
		$privileges									= $this->model->select('group_privileges')->get_where('app__groups', array('group_id' => service('session')->get('group_id')), 1)->row('group_privileges');
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
		if(!service('request')->isAJAX())
		{
			service('session')->setFlashdata('error', phrase('you_cannot_perform_the_requested_action'));
			
			redirect_to($redirect);
		}
	}
	
	/**
	 * push privilege into the table
	 */
	private function _push_privileges($module = null, $submodule = null, $controller = null, $method = null)
	{
		$privileges									= $this->model->select('privileges')->get_where('app__groups_privileges', array('module' => $module, 'submodule' => $submodule, 'controller' => $controller), 1)->row('privileges');
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
				$this->model->update
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
			$checker								= $this->model->get_where
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
				$this->model->insert('app__groups_privileges', $prepare);
			}
		}
	}
	
	/**
	 * push to log activities
	 */
	private function _push_logs($module = null, $submodule = null, $controller = null, $method = null)
	{
		$this->agent								= service('request')->getUserAgent();
		
		if($this->agent->isBrowser())
		{
			$user_agent								= $this->agent->getBrowser() . ' ' . $this->agent->getVersion();
		}
		elseif($this->agent->isRobot())
		{
			$user_agent								= $this->agent->getRobot();
		}
		elseif($this->agent->isMobile())
		{
			$user_agent								= $this->agent->getMobile();
		}
		else
		{
			$user_agent								= phrase('undefined');
		}
		
		$prepare									= array
		(
			'user_id'								=> service('session')->get('user_id'),
			'module'								=> $module,
			'submodule'								=> $submodule,
			'controller'							=> $controller,
			'page'									=> str_replace(base_url(), null, current_page()),
			'method'								=> $method,
			'ip_address'							=> service('request')->getIPAddress(),
			'browser'								=> $user_agent,
			'platform'								=> $this->agent->getPlatform(),
			'timestamp'								=> date('Y-m-d H:i:s')
		);
		
		$this->model->insert('app__activity_logs', $prepare);
	}
}