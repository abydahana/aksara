<?php

namespace Aksara\Modules\Administrative\Controllers\Connections;

/**
 * Administrative > Connections
 *
 * @author			Aby Dahana <abydahana@gmail.com>
 * @profile			abydahana.github.io
 * @website			www.aksaracms.com
 * @since			version 4.0.0
 * @copyright		(c) 2021 - Aksara Laboratory
 */

class Connections extends \Aksara\Laboratory\Core
{
	private $_table									= 'app__connections';
	
	function __construct()
	{
		parent::__construct();
		
		$this->restrict_on_demo();
		
		$this->set_permission();
		$this->set_theme('backend');
		
		$this->set_primary('year');
	}
	
	public function index()
	{
		if(service('request')->getPost('year'))
		{
			$this->set_validation('year', 'valid_year');
		}
		
		$this->set_title(phrase('third_party_connection'))
		->set_icon('mdi mdi-power-plug')
		->unset_column('hostname, port, username, password, database_name')
		->unset_field('id')
		
		->add_action('option', 'connect', phrase('check_connection'), 'btn btn-success --xhr show-progress', 'mdi mdi-sync', array('year' => 'year', 'driver' => 'database_driver'))
		
		->set_field
		(
			array
			(
				'description'						=> 'textarea',
				'username'							=> 'encryption',
				'password'							=> 'encryption',
				'port'								=> 'integer',
				'status'							=> 'boolean'
			)
		)
		->set_field
		(
			'database_driver',
			'dropdown',
			array
			(
				'MySQLi'							=> 'MySQLi',
				'SQLSRV'							=> 'Microsoft SQL Server',
				'Postgre'							=> 'PostgreSQL',
				'SQLite3'							=> 'SQLite',
				'OCI8'								=> 'Oracle (OCI8)'
			)
		)
		->set_relation
		(
			'year',
			'app__years.year',
			'{app__years.year AS year_label}',
			array
			(
				'app__years.status'					=> 1
			)
		)
		->field_position
		(
			array
			(
				'database_driver'					=> 2,
				'hostname'							=> 2,
				'port'								=> 2,
				'username'							=> 2,
				'password'							=> 2,
				'database_name'						=> 2,
				'status'							=> 2
			)
		)
		->merge_field('hostname, port')
		->field_size
		(
			array
			(
				'hostname'							=> 'col-sm-8',
				'port'								=> 'col-sm-4'
			)
		)
		->set_validation
		(
			array
			(
				'name'								=> 'required',
				'database_driver'					=> 'required|in_list[MySQLi,SQLSRV,Postgre,SQLite3,OCI8]',
				'hostname'							=> 'required',
				'port'								=> 'required|integer',
				'username'							=> 'required',
				'database_name'						=> 'required'
			)
		)
		->set_alias
		(
			array
			(
				'year'								=> phrase('year'),
				'year_label'						=> phrase('year'),
				'name'								=> phrase('connection_name'),
				'description'						=> phrase('description'),
				'database_driver'					=> phrase('database_driver'),
				'username'							=> phrase('username'),
				'password'							=> phrase('password'),
				'database_name'						=> phrase('initial_database')
			)
		)
		->render($this->_table);
	}
	
	public function connect()
	{
		$query										= $this->model->get_where
		(
			$this->_table,
			array
			(
				'year'								=> service('request')->getGet('year'),
				'database_driver'					=> service('request')->getGet('driver')
			),
			1
		)
		->row();
		
		if(!$query)
		{
			return throw_exception(404, phrase('the_database_connection_is_not_found'));
		}
		
		try
		{
			// try to decrypting the parameter
			$connection								= array
			(
				'DBDriver'							=> $query->database_driver,
				'hostname'							=> $query->hostname,
				'username'							=> service('encrypter')->decrypt(base64_decode($query->username)),
				'password'							=> service('encrypter')->decrypt(base64_decode($query->password)),
				'database'							=> $query->database_name,
				'DBDebug'							=> (ENVIRONMENT !== 'production')
			);
			
			if($query->port)
			{
				$connection['port']					= $query->port;
			}
			
			$this->connector						= $this->model->database_config($connection);
			
			if(is_array($this->connector) && isset($this->connector['code']))
			{
				return throw_exception(403, $this->connector['message']);
			}
			
			return throw_exception(200, phrase('the_database_was_successfully_connected'));
		}
		catch(\Throwable $e)
		{
			// decrypt error
			return throw_exception(403, $e->getMessage());
		}
	}
}
