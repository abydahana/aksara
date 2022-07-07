<?php

namespace Aksara\Modules\User\Controllers;

/**
 * User
 *
 * @author			Aby Dahana <abydahana@gmail.com>
 * @profile			abydahana.github.io
 * @website			www.aksaracms.com
 * @since			version 4.1.22
 * @copyright		(c) 2021 - Aksara Laboratory
 */

class User extends \Aksara\Laboratory\Core
{
	private $_table									= 'app__users';
	
	public function __construct()
	{
		parent::__construct();
		
		$this->searchable(false);
		
		if(service('request')->getGet('user_id'))
		{
			$query									= $this->model->select
			('
				username
			')
			->get_where
			(
				$this->_table,
				array
				(
					'user_id'						=> service('request')->getGet('user_id')
				),
				1
			)
			->row('username');
			
			if($query)
			{
				return throw_exception(301, null, current_page($query, array('user_id' => null)), true);
			}
		}
	}
	
	public function index($username = '', $tab = null)
	{
		if($username)
		{
			$this->where('username', $username);
		}
		else if(service('request')->getGet('user_id') > 0)
		{
			$this->where('user_id', service('request')->getGet('user_id'));
		}
		else
		{
			$this->where('user_id', get_userdata('user_id'));
		}
		
		$this->set_title('{first_name} {last_name}', phrase('user_not_found'))
		->set_icon('mdi mdi-account')
		
		->set_output
		(
			array
			(
				'suggestions'						=> $this->model->select
				('
					username,
					first_name,
					last_name
				')
				->order_by('username', 'RANDOM')
				->get_where
				(
					$this->_table,
					array
					(
						'status'					=> 1,
						'username != '				=> ''
					),
					8
				)
				->result()
			)
		)
		->limit(1)
		->render($this->_table);
	}
}
