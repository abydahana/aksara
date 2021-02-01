<?php namespace Aksara\Libraries;
/**
 * Extra database connector library
 * This class is used to generate new database connection
 *
 * @author			Aby Dahana
 * @profile			abydahana.github.io
 * @website			www.aksaracms.com
 * @since			version 4.0.0
 * @copyright		(c) 2021 - Aksara Laboratory
 */

ini_set('sqlsrv.ClientBufferMaxKBSize', 524288);

class Connector
{
	private $_parameter;
	
	public function __construct()
	{
		$this->db									= \Config\Database::connect();
	}
	
	public function connect($driver = array(), $hostname = null, $port = 0, $username = null, $password = null, $database = null)
	{
		if($driver && !is_array($driver))
		{
			$driver									= array
			(
				'DBDriver'							=> $driver,
				'hostname'							=> $hostname . ($port ? ',' . $port : null),
				'username'							=> $username,
				'password'							=> $password,
				'database'							=> $database
			);
		}
		
		$this->_parameter							= $driver;
		
		$error										= false;
		
		// MSSQL Driver
		if('mssql' == $this->_parameter['dbdriver'])
		{
			$try									= mssql_connect($this->_parameter['hostname'], $this->_parameter['username'], $this->_parameter['password']);
			
			if(!$try || !mssql_select_db($this->_parameter['database'], $try))
			{
				$error								= true;
			}
		}
		// SQLSRV Driver
		elseif('sqlsrv' == $this->_parameter['dbdriver'])
		{
			$try									= sqlsrv_connect($this->_parameter['hostname'], array('UID' => $this->_parameter['username'], 'PWD' => $this->_parameter['password'], 'Database' => $this->_parameter['database']));
			
			if(!$try)
			{
				$error								= true;
			}
		}
		// PDO Driver
		elseif('pdo' == $this->_parameter['dbdriver'])
		{
			$try									= new PDO('dblib:host=' . $this->_parameter['hostname'] . ';dbname=' . $this->_parameter['database'], $this->_parameter['username'], $this->_parameter['password']);
			
			if(!$try)
			{
				$error								= true;
			}
		}
		
		if($error)
		{
			return throw_exception(403, phrase('cannot_connect_to_the_database_with_the_provided_configuration'), 'dashboard');
		}
		
		// return the connection
		return $this->_ci->load->database($this->_parameter, true);
	}
}