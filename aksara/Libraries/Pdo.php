<?php

namespace Aksara\Libraries;

/**
 * PDO Library
 * An emergency library to connecting the database
 */
class Pdo extends \PDO
{
	private $db;
	public $connID;
	private $_query;
	private $_compiled_query;
	
	public function __construct($hostname = array(), $port = null, $username = null, $password = null, $database = null)
	{
		try
		{
			// try to connect to the database using the given configuration
			$this->db								= new \PDO('dblib:host=' . $hostname . ';port=' . $port . ';dbname=' . $database, $username, $password, array(\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION));
			
			$this->connID							= true;
		}
		catch(\PDOException $e)
		{
			// connection couldn't be made, throw error
			return throw_exception(403, $e->getMessage());
		}
	}
	
	public function table($table = null)
	{
		return $this;
	}
	
	/**
	 * Run the query
	 */
	public function query($query = null)
	{
		$this->_compiled_query						= $query;
		
		$this->_query								= $this->db->query($query);
		
		return $this;
	}
	
	/**
	 * Show the executed query
	 */
	public function getLastQuery($query = null)
	{
		return $this->_compiled_query;
	}
	
	/**
	 * Get single object row
	 */
	public function getRow($row = null)
	{
		$output										= (object) $this->_query->fetch(\PDO::FETCH_ASSOC);
		
		if($row && isset($output->$row))
		{
			return $output->$row;
		}
		
		return $output;
	}
	
	/**
	 * Get single array row
	 */
	public function getRowArray($row = null)
	{
		$output										= $this->_query->fetch(\PDO::FETCH_ASSOC);
		
		if($row && isset($output[$row]))
		{
			return $output[$row];
		}
		
		return $output;
	}
	
	/**
	 * Get object results
	 */
	public function getResult()
	{
		$output										= json_decode(json_encode($this->_query->fetchAll(\PDO::FETCH_ASSOC)));
		
		return $output;
	}
	
	/**
	 * Get array results
	 */
	public function getResultArray()
	{
		$output										= $this->_query->fetchAll(\PDO::FETCH_ASSOC);
		
		return $output;
	}
}
