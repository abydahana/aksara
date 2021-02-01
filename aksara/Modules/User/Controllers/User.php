<?php namespace Aksara\Modules\User\Controllers;
/**
 * User
 *
 * @author			Aby Dahana
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
	}
	
	public function index($username = null)
	{
		if(!$username)
		{
			$username								= get_userdata('username');
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
					CONCAT(first_name, " ", last_name) AS full_name
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
		
		->where
		(
			array
			(
				'username'							=> $username
			)
		)
		->limit(1)
		->render($this->_table);
	}
}
