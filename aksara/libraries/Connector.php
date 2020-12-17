<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Extra database connector library
 * This class is used to generate new database connection
 *
 * Property of DWITRI Media
 */

ini_set('sqlsrv.ClientBufferMaxKBSize', 524288);

class Connector
{
	private $_parameter;
	private $_ci;
	
	public function __construct()
	{
		$this->_ci									=& get_instance();
	}
	
	public function connect($driver = array(), $hostname = null, $port = 0, $username = null, $password = null, $database = null)
	{
		if($driver && !is_array($driver))
		{
			$driver									= array
			(
				'dbdriver'							=> $driver,
				'hostname'							=> $hostname . ($port ? ',' . $port : null),
				'username'							=> $username,
				'password'							=> $password,
				'database'							=> $database
			);
		}
		
		$this->_parameter							= $driver;
		
		if(!$this->_parameter)
		{
			$parameter								= $this->_ci->db->get_where('ref__koneksi', array('tahun' => (get_userdata('year') ? get_userdata('year') : date('Y')), 'status' => 1), 1)->row();
			
			if($parameter)
			{
				$this->_parameter					= array
				(
					'dbdriver'						=> $parameter->driver,
					'hostname'						=> $this->_ci->encryption->decrypt($parameter->hostname) . ($this->_ci->encryption->decrypt($parameter->port) ? ',' . $this->_ci->encryption->decrypt($parameter->port) : null),
					'username'						=> $this->_ci->encryption->decrypt($parameter->username),
					'password'						=> $this->_ci->encryption->decrypt($parameter->password),
					'database'						=> $this->_ci->encryption->decrypt($parameter->database),
					'db_debug'						=> (ENVIRONMENT !== 'production')
				);
			}
		}
		
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