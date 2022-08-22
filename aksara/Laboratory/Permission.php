<?php

namespace Aksara\Laboratory;

/**
 * Permission library
 * Handle the permission of user role
 *
 * @author			Aby Dahana <abydahana@gmail.com>
 * @profile			abydahana.github.io
 * @website			www.aksaracms.com
 * @since			version 4.0.0
 * @copyright		(c) 2021 - Aksara Laboratory
 */

class Permission
{
	public function __construct()
	{
		$this->model								= new \Aksara\Laboratory\Model();
	}
	
	/**
	 * allow
	 */
	public function allow($path = null, $method = null, $user_id = 0, $redirect = null)
	{
		if(!$method)
		{
			$method									= 'index';
		}
		
		$user										= $this->model->select
		('
			user_id,
			group_id
		')
		->get_where
		(
			'app__users',
			array
			(
				'user_id'							=> ($user_id ? $user_id : service('session')->get('user_id')),
				'status'							=> 1
			),
			1
		)
		->row();
		
		if(!$user)
		{
			/* destroy previous session to prevent hijacking */
			if(session_status() == PHP_SESSION_ACTIVE)
			{
				session_destroy();
			}
			
			return false;
		}
		
		$privileges									= $this->model->select
		('
			group_privileges
		')
		->get_where
		(
			'app__groups',
			array
			(
				'group_id'							=> $user->group_id
			),
			1
		)
		->row('group_privileges');
		
		$privileges									= json_decode($privileges, true);
		
		if(!isset($path, $privileges[$path]) || !in_array($method, $privileges[$path]))
		{
			if(method_exists(service('router')->controllerName(), $method) || in_array($method, array('index', 'create', 'read', 'update', 'delete', 'export', 'print', 'pdf')))
			{
				// push to group privileges
				$this->_push_privileges($path, $method);
			}
			
			return false;
		}
		else
		{
			// write log activities
			if('modal' != service('request')->getPost('prefer'))
			{
				// only if request is not from session storage
				$this->_push_logs($path, $method);
			}
			
			return true;
		}
		
		return false;
	}
	
	/**
	 * restrict
	 */
	public function restrict($path = null, $method = null, $redirect = null)
	{
		if(!$method)
		{
			$method									= 'index';
		}
		
		$privileges									= $this->model->select
		('
			group_privileges
		')
		->get_where
		(
			'app__groups',
			array
			(
				'group_id'							=> service('session')->get('group_id')
			),
			1
		)
		->row('group_privileges');
		
		$privileges									= json_decode($privileges, true);
		
		if(isset($privileges[$path]) && in_array($method, $privileges[$path]))
		{
			return throw_exception(403, phrase('you_do_not_have_sufficient_privileges_to_access_this_page'), ($redirect ? $redirect : base_url()));
		}
		else
		{
			return throw_exception(403, phrase('you_do_not_have_sufficient_privileges_to_access_this_page'), ($redirect ? $redirect : base_url()));
		}
	}
	
	/**
	 * visible
	 */
	public function visible($path = null, $method = null)
	{
		if(!$method)
		{
			$method									= 'index';
		}
		
		$privileges									= $this->model->select
		('
			group_privileges
		')
		->get_where
		(
			'app__groups',
			array
			(
				'group_id'							=> service('session')->get('group_id')
			),
			1
		)
		->row('group_privileges');
		
		$privileges									= json_decode($privileges, true);
		
		if(isset($privileges[$path]) && !in_array($method, $privileges[$path]))
		{
			return true;
		}
		
		return false;
	}
	
	/**
	 * hide
	 */
	public function hide($path = null, $method = null)
	{
		if(!$method)
		{
			$method									= 'index';
		}
		
		$privileges									= $this->model->select
		('
			group_privileges
		')
		->get_where
		(
			'app__groups',
			array
			(
				'group_id'							=> service('session')->get('group_id')
			),
			1
		)
		->row('group_privileges');
		
		$privileges									= json_decode($privileges, true);
		
		if(!isset($privileges[$path]) || (isset($privileges[$path]) && !in_array($method, $privileges[$path])))
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
			return throw_exception(403, phrase('you_cannot_perform_the_requested_action'), ($redirect ? $redirect : base_url()));
		}
	}
	
	/**
	 * push privilege into the table
	 */
	private function _push_privileges($path = null, $method = null)
	{
		$privileges									= $this->model->select
		('
			privileges
		')
		->get_where
		(
			'app__groups_privileges',
			array
			(
				'path'								=> $path
			),
			1
		)
		->row('privileges');
		
		$privileges									= ($privileges ? json_decode($privileges, true) : array());
		
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
						'path'						=> $path
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
					'path'							=> $path
				),
				1
			)
			->row();
			
			if(!$checker)
			{
				$privileges[]						= $method;
				
				$prepare							= array
				(
					'path'							=> $path,
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
	private function _push_logs($path = null, $method = null)
	{
		$this->agent								= service('request')->getUserAgent();
		
		if($this->agent->isBrowser())
		{
			$user_agent								= $this->agent->getBrowser() . ' ' . $this->agent->getVersion();
		}
		else if($this->agent->isRobot())
		{
			$user_agent								= $this->agent->getRobot();
		}
		else if($this->agent->isMobile())
		{
			$user_agent								= $this->agent->getMobile();
		}
		else
		{
			$user_agent								= phrase('unknown');
		}
		
		$prepare									= array
		(
			'user_id'								=> service('session')->get('user_id'),
			'path'									=> $path,
			'method'								=> $method,
			'ip_address'							=> (service('request')->hasHeader('x-forwarded-for') ? service('request')->getHeaderLine('x-forwarded-for') : service('request')->getIPAddress()),
			'browser'								=> $user_agent,
			'platform'								=> $this->agent->getPlatform(),
			'timestamp'								=> date('Y-m-d H:i:s')
		);
		
		$this->model->insert('app__activity_logs', $prepare);
	}
}
