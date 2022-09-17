<?php

namespace Aksara\Modules\Addons\Controllers;

/**
 * Addons > FTP Configuration
 *
 * @author			Aby Dahana <abydahana@gmail.com>
 * @profile			abydahana.github.io
 * @website			www.aksaracms.com
 * @since			version 4.0.0
 * @copyright		(c) 2021 - Aksara Laboratory
 */

class Ftp extends \Aksara\Laboratory\Core
{
	private $_table									= 'app__ftp';
	
	public function __construct()
	{
		parent::__construct();
		
		$this->restrict_on_demo();
		
		$this->set_permission();
		$this->set_theme('backend');
		
		$this->searchable(false);
		
		$this->set_method('update');
		$this->insert_on_update_fail();
	}
	
	public function index()
	{
		if(1 == service('request')->getPost('checking'))
		{
			$this->_connection_check();
		}
		
		$this->set_title(phrase('ftp_configuration'))
		->set_icon('mdi mdi-console-network')
		->unset_field('site_id')
		->set_field
		(
			array
			(
				'port'								=> 'numeric',
				'username'							=> 'encryption',
				'password'							=> 'encryption'
			)
		)
		->set_validation
		(
			array
			(
				'hostname'							=> 'required',
				'port'								=> 'required',
				'username'							=> 'required',
				'password'							=> 'required'
			)
		)
		->set_default
		(
			array
			(
				'site_id'							=> get_setting('id')
			)
		)
		->where
		(
			array
			(
				'site_id'							=> get_setting('id')
			)
		)
		->merge_field('hostname, port')
		->merge_field('username, password')
		->field_size
		(
			array
			(
				'hostname'							=> 'col-md-9',
				'port'								=> 'col-md-3'
			)
		)
		->render($this->_table);
	}
	
	private function _connection_check()
	{
		$connection									= @ftp_connect(service('request')->getPost('hostname'), service('request')->getPost('port'), 10);
		
		if(!$connection)
		{
			return throw_exception(400, array('hostname' => phrase('unable_to_connect_to_the_ftp_using_provided_configuration')));
		}
		else if(!@ftp_login($connection, service('request')->getPost('username'), service('request')->getPost('password')))
		{
			return throw_exception(400, array('username' => phrase('unable_to_login_to_ftp_using_the_provided_configuration')));
		}
	}
}
