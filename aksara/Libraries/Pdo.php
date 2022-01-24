<?php

namespace Aksara\Libraries;

/**
 * PDO Library
 * An emergency library to connecting the database
 */
class Pdo extends \PDO
{
	public $connID;
	private $db;
	private $_table;
	private $_query;
	private $_compiled_query;
	private $_affected_rows;
	private $_where;
	
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
		$this->_table								= $table;
		
		return $this;
	}
	
	public function tableExists($table = null)
	{ 
		try
		{
			$result									= $this->db->query('SELECT 1 FROM ' . $table);
		}
		catch (Exception $e)
		{
			return false;
		}
		
		return true;
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
	
	/**
	 * Get affected rows
	 */
	public function affectedRows()
	{
		return $this->_affected_rows;
	}
	
	/**
	 * Where
	 * Your contribution is needed to write complete hint about
	 * this method
	 */
	public function where($field = '', $value = '', $escape = true)
	{
		if(!is_array($field))
		{
			if(isset($value['value']))
			{
				$this->_where[$field]				= $value['value'];
			}
			else
			{
				$this->_where[$field]				= $value;
			}
		}
		else
		{
			foreach($field as $key => $val)
			{
				$this->_where[$key]					= $val;
			}
		}
		
		return $this;
	}
	
	/**
	 * Insert data
	 */
	public function insert($set = array())
	{
		if(!$set || !is_array($set)) return false;
		
		$fields										= array();
		$values										= null;
		
		foreach($set[0] as $key => $val)
		{
			if(in_array(gettype($val), array('float', 'double')))
			{
				$column								= 'CAST(? AS MONEY)';
			}
			else
			{
				$column								= '?';
			}
			
			$fields[]								= $key;
			$values									.= ($values ? ', ' : null) . $column;
		}
		
		$statement									= $this->db->prepare('INSERT INTO ' . $this->_table . ' (' . implode(', ', $fields) . ') VALUES (' . $values . ')');
		
		try
		{
			$statement->execute(array_values($set));
			
			$this->_affected_rows					= 1;
			
			return true;
			
		}
		catch(\PDOException $e)
		{
			return throw_exception(500, $e->getMessage());
		}
	}
	
	/**
	 * Insert batch data
	 */
	public function insertBatch($set = array(), $escape = false, $batch_size = 1)
	{
		if(!$set || !is_array($set)) return false;
		
		$fields										= array();
		$values										= null;
		
		foreach($set[0] as $key => $val)
		{
			if(in_array(gettype($val), array('float', 'double')))
			{
				$column								= 'CAST(? AS MONEY)';
			}
			else
			{
				$column								= '?';
			}
			
			$fields[]								= $key;
			$values									.= ($values ? ', ' : null) . $column;
		}
		
		$statement									= $this->db->prepare('INSERT INTO ' . $this->_table . ' (' . implode(', ', $fields) . ') VALUES (' . $values . ')');
		
		try
		{
			$affected_rows							= 0;
			
			$this->db->beginTransaction();
			
			foreach($set as $key => $val)
			{
				$statement->execute(array_values($val));
				
				$affected_rows++;
			}
			
			$this->db->commit();
			
			$this->_affected_rows					= $affected_rows;
			
			return true;
			
		}
		catch(\PDOException $e)
		{
			$this->db->rollBack();
			
			return throw_exception(500, $e->getMessage());
		}
	}
	
	/**
	 * Update data
	 */
	public function update($set = array(), $where = array(), $limit = null)
	{
		if(!is_array($set)) return false;
		
		$fields										= null;
		$where_clause								= null;
		
		foreach($set as $key => $val)
		{
			$fields									.= ($fields ? ', ' : null) . $key . ' = :' . $key;
		}
		
		if($where)
		{
			foreach($where as $key => $val)
			{
				list($field, $operand)				= array_pad(array_map('trim', explode(' ', $val)), 2, null);
				
				$where_clause						.= ($where_clause ? ', ' : null) . $key . ' ' . ($operand ? $operand : '=') . ' :' . $key;
			}
		}
		
		$statement									= $this->db->prepare('UPDATE ' . $this->_table . ' SET ' . $fields . ($where_clause ? ' WHERE ' . $where_clause : null));
		
		try
		{
			foreach($set as $key => $val)
			{
				$statement->bindValue(':' . $key, $val);
			}
			
			if($statement->execute())
			{
				$this->_affected_rows				= $statement->rowCount();
				
				return true;
			}
		}
		catch(\PDOException $e)
		{
			return throw_exception(500, $e->getMessage());
		}
		
		return false;
	}
	
	/**
	 * Delete data
	 */
	public function delete()
	{
		$bind_parameter								= null;
		
		foreach($this->_where as $key => $val)
		{
			list($field, $operand)					= array_pad(array_map('trim', explode(' ', $key)), 2, null);
			
			$bind_parameter							.= ($bind_parameter ? ' AND ' : null) . $field . ' ' . ($operand ? $operand : '=') . ' :' . $field;
		}
		
		try
		{
			$statement								= $this->db->prepare('DELETE FROM ' . $this->_table . ' WHERE ' . $bind_parameter);
			
			foreach($this->_where as $key => $val)
			{
				list($field, $operand)				= array_pad(array_map('trim', explode(' ', $key)), 2, null);
				
				$statement->bindValue(':' . $field, $val);
			}
			
			$this->_where							= array();
			
			if($statement->execute())
			{
				$this->_affected_rows				= $statement->rowCount();
				
				return true;
			}
		}
		catch(\PDOException $e)
		{
			$this->_where							= array();
			
			return throw_exception(500, $e->getMessage());
		}
		
		return false;
	}
	
	/**
	 * Truncate table
	 */
	public function truncate()
	{
		try
		{
			$statement								= $this->db->prepare('TRUNCATE TABLE ' . $this->_table);
			
			if($statement->execute())
			{
				$this->_affected_rows				= $statement->rowCount();
				
				return true;
			}
		}
		catch(\PDOException $e)
		{
			return throw_exception(500, $e->getMessage());
		}  
		
		return false;
	}
}
