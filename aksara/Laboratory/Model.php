<?php

namespace Aksara\Laboratory;

/**
 * CRUD Model
 * The global model that linked to the core, make crud easier
 *
 * @author			Aby Dahana <abydahana@gmail.com>
 * @profile			abydahana.github.io
 * @website			www.aksaracms.com
 * @since			version 4.0.0
 * @copyright		(c) 2021 - Aksara Laboratory
 */

class Model
{
	private $db;
	private $builder;
	
	private $_prepare								= array();
	private $_is_query								= false;
	private $_finished								= false;
	private $_ordered								= false;
	private $_called								= false;
	
	private $_select								= array();
	private $_from;
	private $_table;
	private $_limit;
	private $_offset;
	private $_set									= array();
	private $_get;
	
	public function __construct()
	{
		$this->db									= \Config\Database::connect();
	}
	
	public function database_config($driver = null, $hostname = null, $port = null, $username = null, $password = null, $database = null)
	{
		if(!$driver)
		{
			// no config provided, use default connection instead
			$this->db								= \Config\Database::connect();
			
			return false;
		}
		
		// define config
		$config										= array();
		
		// check if "default" or given connection number (from app__connections) is selected
		if((is_numeric($driver) || 'default' == $driver) && !$this->_called)
		{
			try
			{
				$builder							= $this->db->table('app__connections');
				
				if('default' == $driver)
				{
					$builder->where('year', (get_userdata('year') ? get_userdata('year') : date('Y')));
				}
				else
				{
					$builder->where('id', $driver);
				}
				
				$parameter							= $builder->getWhere
				(
					array
					(
						'status'					=> 1
					),
					1
				)
				->getRow();
				
				$config								= array
				(
					'DBDriver'						=> $parameter->database_driver,
					'hostname'						=> $parameter->hostname,
					'username'						=> service('encrypter')->decrypt(base64_decode($parameter->username)),
					'password'						=> service('encrypter')->decrypt(base64_decode($parameter->password)),
					'database'						=> $parameter->database_name,
					'DBDebug'						=> (ENVIRONMENT !== 'production')
				);
				
				if($parameter->port)
				{
					$config['port']					= $parameter->port;
				}
				
				// initialize parameter to new connection
				$this->db							= \Config\Database::connect($config);
				
				// try to initialize the connection
				$this->db->initialize();
				
				$this->_called						= true;
			}
			catch(\Throwable $e)
			{
				// decrypt error
				return throw_exception(403, $e->getMessage());
			}
		}
		else if($driver && $hostname && $username && $database)
		{
			$config									= array
			(
				'DBDriver'							=> $driver,
				'hostname'							=> $hostname,
				'username'							=> $username,
				'password'							=> $password,
				'database'							=> $database,
				'DBDebug'							=> (ENVIRONMENT !== 'production')
			);
			
			if($port)
			{
				$config['port']						= $port;
			}
			
			try
			{
			
				// initialize parameter to new connection
				$this->db							= \Config\Database::connect($config);
				
				// try to initialize the connection
				$this->db->initialize();
			}
			catch(\Throwable $e)
			{
				return throw_exception(403, $e->getMessage());
			}
		}
		
		return $this;
	}
	
	/**
	 * Gett the database driver
	 */
	public function db_driver()
	{
		return $this->db->DBDriver;
	}
	
	/**
	 * -------------------------------------------------------------------------
	 * Database Helper
	 * -------------------------------------------------------------------------
	 */
	
	/**
	 * Listing the available tables on current active
	 * database
	 */
	public function list_tables()
	{
		return $this->db->listTables();
	}
	
	/**
	 * Check the existing of table on current active
	 * database
	 */
	public function table_exists($table = null)
	{
		if($table && $this->db->tableExists($table))
		{
			return true;
		}
		
		return false;
	}
	
	/**
	 * Check the field existence of selected table
	 */
	public function field_exists($field = null, $table = null)
	{
		if(strpos(trim($table), '(') !== false || strpos(strtolower(trim($table)), 'select ') !== false)
		{
			return false;
		}
		
		if(strpos(trim($table), ' ') !== false)
		{
			$table									= str_ireplace(' AS ', ' ', $table);
			$destructure							= explode(' ', $table);
			$table									= $destructure[0];
			
			$this->_table_alias[$destructure[1]]	= $table;
		}
		
		if($table && $field && $this->db->tableExists($table) && $this->db->fieldExists($field, $table))
		{
			return true;
		}
		
		return false;
	}
	
	/**
	 * List the field of selected table
	 */
	public function list_fields($table = null)
	{
		if($table && $this->db->tableExists($table))
		{
			return $this->db->getFieldNames($table);
		}
		
		return false;
	}
	
	/**
	 * Get the table metadata and field info of selected table
	 */
	public function field_data($table = null)
	{
		if($table && $this->db->tableExists($table))
		{
			return $this->db->getFieldData($table);
		}
		
		return false;
	}
	
	/**
	 * Get the table index data of selected table
	 */
	public function index_data($table = null)
	{
		if($table && $this->db->tableExists($table))
		{
			return $this->db->getIndexData($table);
		}
		
		return false;
	}
	
	/**
	 * Get the table foreign data of selected table
	 */
	public function foreign_data($table = null)
	{
		if($table && $this->db->tableExists($table))
		{
			return $this->db->getForeignKeyData($table);
		}
		
		return false;
	}
	
	/**
	 * Get the affected rows
	 */
	public function affected_rows()
	{
		return $this->db->affectedRows();
	}
	
	/**
	 * Get the last insert id
	 */
	public function insert_id()
	{
		return $this->db->insertID();
	}
	
	/**
	 * Get last query
	 */
	public function last_query()
	{
		return $this->db->getLastQuery();
	}
	
	/**
	 * ---------------------------------------------------------------
	 * Query Builder
	 * ---------------------------------------------------------------
	 */
	/**
	 * Run the SQL command string
	 */
	public function query($query = null, $params = array())
	{
		// convert multiple line to single line
		$query										= trim(preg_replace('/\s+/S', ' ', $query));
		
		// remove string inside bracket to extract the primary table
		$extract_table								= preg_replace('/\(([^()]*+|(?R))*\)/', '', $query);
		
		// get primary table
		preg_match('/FROM[\s]+(.*?)[\s]+/i', $extract_table, $matches);
		
		if(isset($matches[1]))
		{
			// primary table found
			$this->_table							= trim(str_replace(array('`', '"', '\''), '', $matches[1]));
		}
		
		$this->_prepare[]							= array
		(
			'function'								=> 'query',
			'arguments'								=> array($query, $params)
		);
		
		$this->_is_query							= true;
		
		return $this;
	}
	
	/**
	 * Distinct field
	 */
	public function distinct($flag = true)
	{
		$this->_prepare[]							= array
		(
			'function'								=> 'distinct',
			'arguments'								=> array($flag)
		);
		
		return $this;
	}
	
	/**
	 * Select field
	 * Possible to use comma separated
	 */
	public function select($column = null, $escape = true)
	{
		if(!is_array($column))
		{
			// split selected by comma, but ignore that inside brackets
			$column									= array_map('trim', preg_split('/,(?![^(]+\))/', $column));
		}
		
		$column										= array_unique($column);
		
		foreach($column as $key => $val)
		{
			$this->_select[]						= $val;
		}
		
		$this->_prepare[]							= array
		(
			'function'								=> 'select',
			'arguments'								=> array($column, $escape)
		);
		
		return $this;
	}
	
	/**
	 * Select count
	 */
	public function select_count($column = null, $alias = null)
	{
		$this->_prepare[]							= array
		(
			'function'								=> 'selectCount',
			'arguments'								=> array($column, $alias)
		);
		
		return $this;
	}
	
	/**
	 * Select and Sum
	 */
	public function select_sum($column = null, $alias = null)
	{
		$this->_prepare[]							= array
		(
			'function'								=> 'selectSum',
			'arguments'								=> array($column, $alias)
		);
		
		return $this;
	}
	
	/**
	 * Select Minimum
	 */
	public function select_min($column = null, $alias = null)
	{
		$this->_prepare[]							= array
		(
			'function'								=> 'selectMin',
			'arguments'								=> array($column, $alias)
		);
		
		return $this;
	}
	
	/**
	 * Select Maximum
	 */
	public function select_max($column = null, $alias = null)
	{
		$this->_prepare[]							= array
		(
			'function'								=> 'selectMax',
			'arguments'								=> array($column, $alias)
		);
		
		return $this;
	}
	
	/**
	 * Select Average of field
	 */
	public function select_avg($column = null, $alias = null)
	{
		$this->_prepare[]							= array
		(
			'function'								=> 'selectAvg',
			'arguments'								=> array($column, $alias)
		);
		
		return $this;
	}
	
	/**
	 * Set the primary table
	 */
	public function from($table = null)
	{
		$this->_table								= $table;
		
		$this->builder								= $this->db->table($table);
		
		return $this;
	}
	
	/**
	 * Set the primary table
	 * It's similar to from() method
	 */
	public function table($table = null)
	{
		$this->_table								= $table;
		
		$this->builder								= $this->db->table($table);
		
		return $this;
	}
	
	/**
	 * Join table
	 * Your contribution is needed to write hint about
	 * this method
	 */
	public function join($table = null, $condition = null, $type = '', $escape = true)
	{
		$this->_prepare[]							= array
		(
			'function'								=> 'join',
			'arguments'								=> array($table, $condition, $type, $escape)
		);
		
		return $this;
	}
	
	/**
	 * Where
	 * Your contribution is needed to write complete hint about
	 * this method
	 */
	public function where($field = '', $value = '', $escape = true)
	{
		if(is_array($field))
		{
			// run where command
			foreach($field as $key => $val)
			{
				$cast								= $this->_cast_column($key, $val);
				
				$this->_prepare[]					= array
				(
					'function'						=> 'where',
					'arguments'						=> array($cast['column'], $cast['value'], $escape)
				);
			}
		}
		else
		{
			$cast									= $this->_cast_column($field, $value);
			
			$this->_prepare[]						= array
			(
				'function'							=> 'where',
				'arguments'							=> array($cast['column'], $cast['value'], $escape)
			);
		}
		
		return $this;
	}
	
	/**
	 * Or Where
	 * Your contribution is needed to write complete hint about
	 * this method
	 */
	public function or_where($field = '', $value = '', $escape = true)
	{
		if(is_array($field))
		{
			// run or where command
			foreach($field as $key => $val)
			{
				$cast								= $this->_cast_column($key, $val);
				
				$this->_prepare[]					= array
				(
					'function'						=> 'orWhere',
					'arguments'						=> array($cast['column'], $cast['value'], $escape)
				);
			}
		}
		else
		{
			$cast									= $this->_cast_column($field, $value);
			
			$this->_prepare[]						= array
			(
				'function'							=> 'orWhere',
				'arguments'							=> array($cast['column'], $cast['value'], $escape)
			);
		}
		
		return $this;
	}
	
	/**
	 * Where In
	 * Your contribution is needed to write complete hint about
	 * this method
	 */
	public function where_in($field = '', $value = '', $escape = true)
	{
		if(is_array($field))
		{
			foreach($field as $key => $val)
			{
				$this->_prepare[]					= array
				(
					'function'						=> 'whereIn',
					'arguments'						=> array($key, $val, $escape)
				);
			}
		}
		else
		{
			$this->_prepare[]						= array
			(
				'function'							=> 'whereIn',
				'arguments'							=> array($field, $value, $escape)
			);
		}
		
		return $this;
	}
	
	/**
	 * Or Where In
	 * Your contribution is needed to write complete hint about
	 * this method
	 */
	public function or_where_in($field = '', $value = '', $escape = true)
	{
		if(is_array($field))
		{
			foreach($field as $key => $val)
			{
				$this->_prepare[]					= array
				(
					'function'						=> 'orWhereIn',
					'arguments'						=> array($key, $val, $escape)
				);
			}
		}
		else
		{
			$this->_prepare[]						= array
			(
				'function'							=> 'orWhereIn',
				'arguments'							=> array($field, $value, $escape)
			);
		}
		
		return $this;
	}
	
	/**
	 * Where Not In
	 * Your contribution is needed to write complete hint about
	 * this method
	 */
	public function where_not_in($field = '', $value = '', $escape = true)
	{
		if(is_array($field))
		{
			foreach($field as $key => $val)
			{
				$this->_prepare[]					= array
				(
					'function'						=> 'whereNotIn',
					'arguments'						=> array($key, $val, $escape)
				);
			}
		}
		else
		{
			$this->_prepare[]						= array
			(
				'function'							=> 'whereNotIn',
				'arguments'							=> array($field, $value, $escape)
			);
		}
		
		return $this;
	}
	
	/**
	 * Or Where Not In
	 * Your contribution is needed to write complete hint about
	 * this method
	 */
	public function or_where_not_in($field = '', $value = '', $escape = true)
	{
		if(is_array($field))
		{
			foreach($field as $key => $val)
			{
				$this->_prepare[]					= array
				(
					'function'						=> 'orWhereNotIn',
					'arguments'						=> array($key, $val, $escape)
				);
			}
		}
		else
		{
			$this->_prepare[]						= array
			(
				'function'							=> 'orWhereNotIn',
				'arguments'							=> array($field, $value, $escape)
			);
		}
		
		return $this;
	}
	
	/**
	 * Like
	 * Your contribution is needed to write complete hint about
	 * this method
	 */
	public function like($field = '', $match = '', $side = 'both', $escape = true, $case_insensitive = false)
	{
		$column										= array();
		
		if(!is_array($field))
		{
			if(isset($match['match']))
			{
				$column[$field]						= ($match ? $match : '');
			}
			else
			{
				$column[$field]						= array
				(
					'match'							=> ($match ? $match : ''),
					'side'							=> $side,
					'escape'						=> $escape,
					'case_insensitive'				=> $case_insensitive
				);
			}
		}
		else
		{
			foreach($field as $key => $val)
			{
				$column[$key]						= array
				(
					'match'							=> ($val ? $val : ''),
					'side'							=> 'both',
					'escape'						=> $escape,
					'case_insensitive'				=> $case_insensitive
				);
			}
		}
		
		foreach($column as $key => $val)
		{
			$cast									= $this->_cast_column($key, $val['match']);
			
			$this->_prepare[]						= array
			(
				'function'							=> 'like',
				'arguments'							=> array($cast['column'], $cast['value'], $val['side'], $val['escape'], $val['case_insensitive'])
			);
		}
		
		return $this;
	}
	
	/**
	 * Or Like
	 * Your contribution is needed to write complete hint about
	 * this method
	 */
	public function or_like($field = '', $match = '', $side = 'both', $escape = true, $case_insensitive = false)
	{
		$column										= array();
		
		if(!is_array($field))
		{
			if(isset($match['match']))
			{
				$column[$field]						= ($match ? $match : '');
			}
			else
			{
				$column[$field]						= array
				(
					'match'							=> ($match ? $match : ''),
					'side'							=> $side,
					'escape'						=> $escape,
					'case_insensitive'				=> $case_insensitive
				);
			}
		}
		else
		{
			foreach($field as $key => $val)
			{
				$column[$key]						= array
				(
					'match'							=> ($val ? $val : ''),
					'side'							=> 'both',
					'escape'						=> $escape,
					'case_insensitive'				=> $case_insensitive
				);
			}
		}
		
		foreach($column as $key => $val)
		{
			$cast									= $this->_cast_column($key, $val['match']);
			
			$this->_prepare[]						= array
			(
				'function'							=> 'orLike',
				'arguments'							=> array($cast['column'], $cast['value'], $val['side'], $val['escape'], $val['case_insensitive'])
			);
		}
		
		return $this;
	}
	
	/**
	 * Not Like
	 * Your contribution is needed to write complete hint about
	 * this method
	 */
	public function not_like($field = '', $match = '', $side = 'both', $escape = true, $case_insensitive = false)
	{
		$column										= array();
		
		if(!is_array($field))
		{
			if(isset($match['match']))
			{
				$column[$field]						= ($match ? $match : '');
			}
			else
			{
				$column[$field]						= array
				(
					'match'							=> ($match ? $match : ''),
					'side'							=> $side,
					'escape'						=> $escape,
					'case_insensitive'				=> $case_insensitive
				);
			}
		}
		else
		{
			foreach($field as $key => $val)
			{
				$column[$key]						= array
				(
					'match'							=> ($val ? $val : ''),
					'side'							=> 'both',
					'escape'						=> $escape,
					'case_insensitive'				=> $case_insensitive
				);
			}
		}
		
		foreach($column as $key => $val)
		{
			$cast									= $this->_cast_column($key, $val['match']);
			
			$this->_prepare[]						= array
			(
				'function'							=> 'notLike',
				'arguments'							=> array($cast['column'], $cast['value'], $val['side'], $val['escape'], $val['case_insensitive'])
			);
		}
		
		return $this;
	}
	
	/**
	 * Or Not Like
	 * Your contribution is needed to write complete hint about
	 * this method
	 */
	public function or_not_like($field = '', $match = '', $side = 'both', $escape = true, $case_insensitive = false)
	{
		$column										= array();
		
		if(!is_array($field))
		{
			if(isset($match['match']))
			{
				$column[$field]						= ($match ? $match : '');
			}
			else
			{
				$column[$field]						= array
				(
					'match'							=> ($match ? $match : ''),
					'side'							=> $side,
					'escape'						=> $escape,
					'case_insensitive'				=> $case_insensitive
				);
			}
		}
		else
		{
			foreach($field as $key => $val)
			{
				$column[$key]						= array
				(
					'match'							=> ($val ? $val : ''),
					'side'							=> 'both',
					'escape'						=> $escape,
					'case_insensitive'				=> $case_insensitive
				);
			}
		}
		
		foreach($column as $key => $val)
		{
			$cast									= $this->_cast_column($key, $val['match']);
			
			$this->_prepare[]						= array
			(
				'function'							=> 'orNotLike',
				'arguments'							=> array($cast['column'], $cast['value'], $val['side'], $val['escape'], $val['case_insensitive'])
			);
		}
		
		return $this;
	}
	
	/**
	 * Having
	 * Your contribution is needed to write complete hint about
	 * this method
	 */
	public function having($field = '', $value = '', $escape = true)
	{
		if(is_array($field))
		{
			// run having command
			foreach($field as $key => $val)
			{
				$cast								= $this->_cast_column($key, $val);
				
				$this->_prepare[]					= array
				(
					'function'						=> 'having',
					'arguments'						=> array($cast['column'], $cast['value'], $escape)
				);
			}
		}
		else
		{
			$cast									= $this->_cast_column($field, $value);
			
			$this->_prepare[]						= array
			(
				'function'							=> 'having',
				'arguments'							=> array($cast['column'], $cast['value'], $escape)
			);
		}
		
		return $this;
	}
	
	/**
	 * Or Having
	 * Your contribution is needed to write complete hint about
	 * this method
	 */
	public function or_having($field = '', $value = '', $escape = true)
	{
		if(is_array($field))
		{
			// run or having command
			foreach($field as $key => $val)
			{
				$cast								= $this->_cast_column($key, $val);
				
				$this->_prepare[]					= array
				(
					'function'						=> 'orHaving',
					'arguments'						=> array($cast['column'], $cast['value'], $escape)
				);
			}
		}
		else
		{
			$cast									= $this->_cast_column($field, $value);
			
			$this->_prepare[]						= array
			(
				'function'							=> 'orHaving',
				'arguments'							=> array($cast['column'], $cast['value'], $escape)
			);
		}
		
		return $this;
	}
	
	/**
	 * Having In
	 * Your contribution is needed to write complete hint about
	 * this method
	 */
	public function having_in($field = '', $value = '', $escape = true)
	{
		if(is_array($field))
		{
			foreach($field as $key => $val)
			{
				$this->_prepare[]					= array
				(
					'function'						=> 'havingIn',
					'arguments'						=> array($key, $val, $escape)
				);
			}
		}
		else
		{
			$this->_prepare[]						= array
			(
				'function'							=> 'havingIn',
				'arguments'							=> array($field, $value, $escape)
			);
		}
		
		return $this;
	}
	
	/**
	 * Or Having In
	 * Your contribution is needed to write complete hint about
	 * this method
	 */
	public function or_having_in($field = '', $value = '', $escape = true)
	{
		if(is_array($field))
		{
			foreach($field as $key => $val)
			{
				$this->_prepare[]					= array
				(
					'function'						=> 'orHavingIn',
					'arguments'						=> array($key, $val, $escape)
				);
			}
		}
		else
		{
			$this->_prepare[]						= array
			(
				'function'							=> 'orHavingIn',
				'arguments'							=> array($field, $value, $escape)
			);
		}
		
		return $this;
	}
	
	/**
	 * Having Not In
	 * Your contribution is needed to write complete hint about
	 * this method
	 */
	public function having_not_in($field = '', $value = '', $escape = true)
	{
		if(is_array($field))
		{
			foreach($field as $key => $val)
			{
				$this->_prepare[]					= array
				(
					'function'						=> 'havingNotIn',
					'arguments'						=> array($key, $val, $escape)
				);
			}
		}
		else
		{
			$this->_prepare[]						= array
			(
				'function'							=> 'havingNotIn',
				'arguments'							=> array($field, $value, $escape)
			);
		}
		
		return $this;
	}
	
	/**
	 * Or Having Not In
	 * Your contribution is needed to write complete hint about
	 * this method
	 */
	public function or_having_not_in($field = '', $value = '', $escape = true)
	{
		if(is_array($field))
		{
			foreach($field as $key => $val)
			{
				$this->_prepare[]					= array
				(
					'function'						=> 'orHavingNotIN',
					'arguments'						=> array($key, $val, $escape)
				);
			}
		}
		else
		{
			$this->_prepare[]						= array
			(
				'function'							=> 'orHavingNotIn',
				'arguments'							=> array($field, $value, $escape)
			);
		}
		
		return $this;
	}
	
	/**
	 * Having Like
	 * Your contribution is needed to write complete hint about
	 * this method
	 */
	public function having_like($field = '', $match = '', $side = 'both', $escape = true, $case_insensitive = false)
	{
		$column										= array();
		
		if(!is_array($field))
		{
			if(isset($match['match']))
			{
				$column[$field]						= ($match ? $match : '');
			}
			else
			{
				$column[$field]						= array
				(
					'match'							=> ($match ? $match : ''),
					'side'							=> $side,
					'escape'						=> $escape,
					'case_insensitive'				=> $case_insensitive
				);
			}
		}
		else
		{
			foreach($field as $key => $val)
			{
				$column[$key]						= array
				(
					'match'							=> ($val ? $val : ''),
					'side'							=> 'both',
					'escape'						=> $escape,
					'case_insensitive'				=> $case_insensitive
				);
			}
		}
		
		foreach($column as $key => $val)
		{
			$cast									= $this->_cast_column($key, $val);
			
			$this->_prepare[]						= array
			(
				'function'							=> 'havingLike',
				'arguments'							=> array($cast['column'], $cast['value'], $val['side'], $val['escape'], $val['case_insensitive'])
			);
		}
		
		return $this;
	}
	
	/**
	 * Or Having Like
	 * Your contribution is needed to write complete hint about
	 * this method
	 */
	public function or_having_like($field = '', $match = '', $side = 'both', $escape = true, $case_insensitive = false)
	{
		$column										= array();
		
		if(!is_array($field))
		{
			if(isset($match['match']))
			{
				$column[$field]						= ($match ? $match : '');
			}
			else
			{
				$column[$field]						= array
				(
					'match'							=> ($match ? $match : ''),
					'side'							=> $side,
					'escape'						=> $escape,
					'case_insensitive'				=> $case_insensitive
				);
			}
		}
		else
		{
			foreach($field as $key => $val)
			{
				$column[$key]						= array
				(
					'match'							=> ($val ? $val : ''),
					'side'							=> 'both',
					'escape'						=> $escape,
					'case_insensitive'				=> $case_insensitive
				);
			}
		}
		
		foreach($column as $key => $val)
		{
			$cast									= $this->_cast_column($key, $val);
			
			$this->_prepare[]						= array
			(
				'function'							=> 'orHavingLike',
				'arguments'							=> array($cast['column'], $cast['value'], $val['side'], $val['escape'], $val['case_insensitive'])
			);
		}
		
		return $this;
	}
	
	/**
	 * Not Having Like
	 * Your contribution is needed to write complete hint about
	 * this method
	 */
	public function not_having_like($field = '', $match = '', $side = 'both', $escape = true, $case_insensitive = false)
	{
		$column										= array();
		
		if(!is_array($field))
		{
			if(isset($match['match']))
			{
				$column[$field]						= ($match ? $match : '');
			}
			else
			{
				$column[$field]						= array
				(
					'match'							=> ($match ? $match : ''),
					'side'							=> $side,
					'escape'						=> $escape,
					'case_insensitive'				=> $case_insensitive
				);
			}
		}
		else
		{
			foreach($field as $key => $val)
			{
				$column[$key]						= array
				(
					'match'							=> ($val ? $val : ''),
					'side'							=> 'both',
					'escape'						=> $escape,
					'case_insensitive'				=> $case_insensitive
				);
			}
		}
		
		foreach($column as $key => $val)
		{
			$cast									= $this->_cast_column($key, $val);
			
			$this->_prepare[]						= array
			(
				'function'							=> 'notHavingLike',
				'arguments'							=> array($cast['column'], $cast['value'], $val['side'], $val['escape'], $val['case_insensitive'])
			);
		}
		
		return $this;
	}
	
	/**
	 * Or Not Having Like
	 * Your contribution is needed to write complete hint about
	 * this method
	 */
	public function or_not_having_like($field = '', $match = '', $side = 'both', $escape = true, $case_insensitive = false)
	{
		$column										= array();
		
		if(!is_array($field))
		{
			if(isset($match['match']))
			{
				$column[$field]						= ($match ? $match : '');
			}
			else
			{
				$column[$field]						= array
				(
					'match'							=> ($match ? $match : ''),
					'side'							=> $side,
					'escape'						=> $escape,
					'case_insensitive'				=> $case_insensitive
				);
			}
		}
		else
		{
			foreach($field as $key => $val)
			{
				$column[$key]						= array
				(
					'match'							=> ($val ? $val : ''),
					'side'							=> 'both',
					'escape'						=> $escape,
					'case_insensitive'				=> $case_insensitive
				);
			}
		}
		
		foreach($column as $key => $val)
		{
			$cast									= $this->_cast_column($key, $val);
			
			$this->_prepare[]						= array
			(
				'function'							=> 'orNotHavingLike',
				'arguments'							=> array($cast['column'], $cast['value'], $val['side'], $val['side'], $val['escape'], $val['case_insensitive'])
			);
		}
		
		return $this;
	}
	
	/**
	 * Group By
	 * Your contribution is needed to write complete hint about
	 * this method
	 */
	public function group_by($column = null)
	{
		if(in_array($this->db->DBDriver, array('SQLSRV')))
		{
			$column									= array_map('trim', explode(',', $by));
			
			// loops the group list
			foreach($column as $key => $val)
			{
				if(stripos($val, '(') && stripos($val, ')'))
				{
					$this->_prepare[]				= array
					(
						'function'					=> 'groupBy',
						'arguments'					=> array($val)
					);
				}
				else
				{
					$this->_prepare[]				= array
					(
						'function'					=> 'groupBy',
						'arguments'					=> array('CONVERT(VARCHAR(MAX), ' . (stripos($val, ' AS ') !== false ? substr($val, 0, stripos($val, ' AS ')) : $val) . ')')
					);
				}
			}
		}
		else
		{
			$this->_prepare[]						= array
			(
				'function'							=> 'groupBy',
				'arguments'							=> array($column)
			);
		}
		
		return $this;
	}
	
	/**
	 * Order By
	 * Your contribution is needed to write complete hint about
	 * this method
	 */
	public function order_by($column = null, $direction = '', $escape = true)
	{
		$this->_ordered								= true;
		
		if(is_array($column))
		{
			foreach($column as $key => $val)
			{
				$this->_prepare[]					= array
				(
					'function'						=> 'orderBy',
					'arguments'						=> array($key, $val, $escape)
				);
			}
		}
		else if($direction)
		{
			$this->_prepare[]						= array
			(
				'function'							=> 'orderBy',
				'arguments'							=> array($column, $direction, $escape)
			);
		}
		else
		{
			$column									= ($column ? array_map('trim', preg_split('/,(?![^(]+\))/', trim($column))) : array());
			
			foreach($column as $key => $val)
			{
				$dir								= '';
				
				if(strpos($val, '(') !== false && strpos($val, ')') !== false)
				{
					$col							= $val;
				}
				else
				{
					list($col, $dir)				= array_pad(array_map('trim', explode(' ', $val)), 2, '');
				}
				
				$this->_prepare[]					= array
				(
					'function'						=> 'orderBy',
					'arguments'						=> array($col, $dir, $escape)
				);
			}
		}
		
		return $this;
	}
	
	/**
	 * Limit
	 * Your contribution is needed to write complete hint about
	 * this method
	 */
	public function limit($limit = 0, $offset = 0)
	{
		$this->_prepare[]							= array
		(
			'function'								=> 'limit',
			'arguments'								=> array($limit, $offset)
		);
		
		return $this;
	}
	
	/**
	 * Offset
	 * Your contribution is needed to write complete hint about
	 * this method
	 */
	public function offset($offset = null)
	{
		$this->_prepare[]							= array
		(
			'function'								=> 'offset',
			'arguments'								=> array($offset)
		);
		
		return $this;
	}
	
	/**
	 * Select subqueries
	 * Possible to use comma separated
	 */
	public function subquery($subquery = null, $alias = null)
	{
		$this->_prepare[]							= array
		(
			'function'								=> 'fromSubquery',
			'arguments'								=> array($subquery, $alias)
		);
		
		return $this;
	}
	
	/**
	 * Group Start
	 * Your contribution is needed to write complete hint about
	 * this method
	 */
	public function group_start()
	{
		$this->_prepare[]							= array
		(
			'function'								=> 'groupStart',
			'arguments'								=> array()
		);
		
		return $this;
	}
	
	/**
	 * Or Group Start
	 * Your contribution is needed to write complete hint about
	 * this method
	 */
	public function or_group_start()
	{
		$this->_prepare[]							= array
		(
			'function'								=> 'orGroupStart',
			'arguments'								=> array()
		);
		
		return $this;
	}
	
	/**
	 * Not Group Start
	 * Your contribution is needed to write complete hint about
	 * this method
	 */
	public function not_group_start()
	{
		$this->_prepare[]							= array
		(
			'function'								=> 'notGroupStart',
			'arguments'								=> array()
		);
		
		return $this;
	}
	
	/**
	 * Or Not Group Start
	 * Your contribution is needed to write complete hint about
	 * this method
	 */
	public function or_not_group_start()
	{
		$this->_prepare[]							= array
		(
			'function'								=> 'orNotGroupStart',
			'arguments'								=> array()
		);
		
		return $this;
	}
	
	/**
	 * Group End
	 * Your contribution is needed to write complete hint about
	 * this method
	 */
	public function group_end()
	{
		$this->_prepare[]							= array
		(
			'function'								=> 'groupEnd',
			'arguments'								=> array()
		);
		
		return $this;
	}
	
	/**
	 * Group Start
	 * Your contribution is needed to write complete hint about
	 * this method
	 */
	public function having_group_start()
	{
		$this->_prepare[]							= array
		(
			'function'								=> 'havingGroupStart',
			'arguments'								=> array()
		);
		
		return $this;
	}
	
	/**
	 * Or Group Start
	 * Your contribution is needed to write complete hint about
	 * this method
	 */
	public function or_having_group_start()
	{
		$this->_prepare[]							= array
		(
			'function'								=> 'orHavingGroupStart',
			'arguments'								=> array()
		);
		
		return $this;
	}
	
	/**
	 * Not Group Start
	 * Your contribution is needed to write complete hint about
	 * this method
	 */
	public function not_having_group_start()
	{
		$this->_prepare[]							= array
		(
			'function'								=> 'notHavingGroupStart',
			'arguments'								=> array()
		);
		
		return $this;
	}
	
	/**
	 * Or Not Group Start
	 * Your contribution is needed to write complete hint about
	 * this method
	 */
	public function or_not_having_group_start()
	{
		$this->_prepare[]							= array
		(
			'function'								=> 'orNotHavingGroupStart',
			'arguments'								=> array()
		);
		
		return $this;
	}
	
	/**
	 * Group End
	 * Your contribution is needed to write complete hint about
	 * this method
	 */
	public function having_group_end()
	{
		$this->_prepare[]							= array
		(
			'function'								=> 'havingGroupEnd',
			'arguments'								=> array()
		);
		
		return $this;
	}
	
	/**
	 * Get
	 * Your contribution is needed to write complete hint about
	 * this method
	 */
	public function get($table = null, $limit = 0, $offset = 0)
	{
		if(!$this->_table && $table)
		{
			$this->_table							= $table;
		}
		
		if($limit && (!in_array($this->db->DBDriver, array('SQLSRV', 'Postgre')) || ($this->db->DBDriver === 'SQLSRV' && $this->db->getVersion() >= 10)))
		{
			$this->_limit							= $limit;
			$this->_offset							= $offset;
		}
		
		$this->_prepare[]							= array
		(
			'function'								=> 'get',
			'arguments'								=> array($limit, $offset)
		);
		
		return $this;
	}
	
	/**
	 * Get Where
	 * Your contribution is needed to write complete hint about
	 * this method
	 */
	public function get_where($table = null, array $where = array(), $limit = 0, $offset = null, $reset = true)
	{
		if(!$this->_table && $table)
		{
			$this->_table							= $table;
		}
		
		if($limit && (!in_array($this->db->DBDriver, array('SQLSRV', 'Postgre')) || ($this->db->DBDriver === 'SQLSRV' && $this->db->getVersion() >= 10)))
		{
			$this->_limit							= $limit;
			$this->_offset							= $offset;
		}
		
		if($where && $this->db->DBDriver == 'Postgre')
		{
			foreach($where as $key => $val)
			{
				$cast								= $this->_cast_column($key, $val);
				
				$where[$cast['column']]				= $cast['value'];
				
				if($key != $cast['column'])
				{
					unset($where[$key]);
				}
			}
		}
		
		$this->_prepare[]							= array
		(
			'function'								=> 'getWhere',
			'arguments'								=> array($where, $limit, $offset, $reset)
		);
		
		return $this;
	}
	
	/**
	 * Result (object format)
	 * Your contribution is needed to write complete hint about
	 * this method
	 */
	public function result()
	{
		$this->_prepare[]							= array
		(
			'function'								=> 'getResultObject',
			'arguments'								=> array()
		);
		
		return $this->_run_query();
	}
	
	/**
	 * Result (array format)
	 * Your contribution is needed to write complete hint about
	 * this method
	 */
	public function result_array()
	{
		$this->_prepare[]							= array
		(
			'function'								=> 'getResultArray',
			'arguments'								=> array()
		);
		
		return $this->_run_query();
	}
	
	/**
	 * Get Row
	 * Your contribution is needed to write complete hint about
	 * this method
	 */
	public function row($field = 1)
	{
		if(!in_array($this->db->DBDriver, array('SQLSRV', 'Postgre')) || ($this->db->DBDriver === 'SQLSRV' && $this->db->getVersion() >= 10))
		{
			$this->_limit							= 1;
		}
		
		$this->_prepare[]							= array
		(
			'function'								=> (is_int($field) ? 'getRowObject' : 'getRow'),
			'arguments'								=> array($field)
		);
		
		return $this->_run_query();
	}
	
	/**
	 * Get Row Array
	 * Your contribution is needed to write complete hint about
	 * this method
	 */
	public function row_array($field = 1)
	{
		if(!in_array($this->db->DBDriver, array('SQLSRV', 'Postgre')) || ($this->db->DBDriver === 'SQLSRV' && $this->db->getVersion() >= 10))
		{
			$this->_limit							= 1;
		}
		
		$this->_prepare[]							= array
		(
			'function'								=> 'getRowArray',
			'arguments'								=> array($field)
		);
		
		return $this->_run_query();
	}
	
	/**
	 * Get number of rows
	 * Your contribution is needed to write complete hint about
	 * this method
	 */
	public function num_rows($table = null, $reset = true)
	{
		if(!$this->_table && $table)
		{
			$this->_table							= $table;
		}
		
		$this->_prepare[]							= array
		(
			'function'								=> 'getNumRows',
			'arguments'								=> array($reset)
		);
		
		return $this->_run_query();
	}
	
	/**
	 * Count All
	 * Your contribution is needed to write complete hint about
	 * this method
	 */
	public function count_all($table = null, $reset = true)
	{
		if(!$this->_table && $table)
		{
			$this->_table							= $table;
		}
		
		$this->_prepare[]							= array
		(
			'function'								=> 'countAll',
			'arguments'								=> array($reset)
		);
		
		return $this->_run_query();
	}
	
	/**
	 * Count All Results
	 * Your contribution is needed to write complete hint about
	 * this method
	 */
	public function count_all_results($table = null, $reset = true)
	{
		if(!$this->_table && $table)
		{
			$this->_table							= $table;
		}
		
		$this->_prepare[]							= array
		(
			'function'								=> 'countAllResults',
			'arguments'								=> array($reset)
		);
		
		return $this->_run_query();
	}
	
	/**
	 * Insert
	 * Your contribution is needed to write complete hint about
	 * this method
	 */
	public function insert($table = null, $set = array(), $escape = true)
	{
		if(!$this->_table && $table)
		{
			$this->_table							= $table;
		}
		
		$set										= array_merge($this->_set, $set);
		
		if($this->db->DBDriver == 'SQLite3' && $table && $this->db->tableExists($table))
		{
			$index_data								= $this->db->getIndexData($table);
			
			// set the default primary if the table have any primary column
			if($index_data)
			{
				// loops to get the primary key
				foreach($index_data as $key => $val)
				{
					// check if the field has primary key
					if($val->type == 'PRIMARY')
					{
						$set[$val->fields[0]]		= ($this->db->table($table)->selectMax($val->fields[0])->get()->getRow($val->fields[0]) + 1);
						
						break;
					}
				}
			}
		}
		
		$this->_prepare[]							= array
		(
			'function'								=> 'insert',
			'arguments'								=> array($set, $escape)
		);
		
		return $this->_run_query();
	}
	
	/**
	 * Insert Batch
	 * Your contribution is needed to write complete hint about
	 * this method
	 */
	public function insert_batch($table = null, $set = array(), $batch_size = 1, $escape = true)
	{
		if(!$this->_table && $table)
		{
			$this->_table							= $table;
		}
		
		$set										= array_merge($this->_set, $set);
		
		if($this->db->DBDriver == 'SQLite3' && $table && $this->db->tableExists($table))
		{
			$index_data								= $this->db->getIndexData($table);
			
			// set the default primary if the table have any primary column
			if($index_data)
			{
				// loops to get the primary key
				foreach($index_data as $key => $val)
				{
					// check if the field has primary key
					if($val->type == 'PRIMARY')
					{
						$primary					= $val->fields[0];
						$auto_increment				= ($this->db->table($table)->selectMax($val->fields[0])->get()->getRow($val->fields[0]) + 1);
						
						break;
					}
				}
			}
			
			$new_set								= array();
			
			foreach($set as $key => $val)
			{
				foreach($val as $_key => $_val)
				{
					$_val[$primary]					= $auto_increment;
					$val							= $_val;
					
					$auto_increment++;
				}
				
				$new_set[]							= $val;
			}
			
			$set									= $new_set;
		}
		
		$this->_prepare[]							= array
		(
			'function'								=> 'insertBatch',
			'arguments'								=> array($set, $escape, $batch_size)
		);
		
		return $this->_run_query();
	}
	
	/**
	 * Update
	 * Your contribution is needed to write complete hint about
	 * this method
	 */
	public function update($table = null, $set = array(), array $where = array(), $limit = null)
	{
		if(!$this->_table && $table)
		{
			$this->_table							= $table;
		}
		
		if($limit && (!in_array($this->db->DBDriver, array('SQLSRV', 'Postgre')) || ($this->db->DBDriver === 'SQLSRV' && $this->db->getVersion() >= 10)))
		{
			$this->_limit							= $limit;
		}
		
		$set										= array_merge($this->_set, $set);
		
		foreach($where as $key => $val)
		{
			if(is_array($val) && isset($val['value']))
			{
				$where[$key]						= $val['value'];
			}
			else
			{
				$where[$key]						= $val;
			}
		}
		
		$this->_prepare[]							= array
		(
			'function'								=> 'update',
			'arguments'								=> array($set, $where, (!in_array($this->db->DBDriver, array('Postgre', 'SQLite3')) ? $this->_limit : null))
		);
		
		return $this->_run_query();
	}
	
	/**
	 * Update Batch
	 * Your contribution is needed to write complete hint about
	 * this method
	 */
	public function update_batch($table = null, $set = array(), $batch_size = 1, $escape = true)
	{
		if(!$this->_table && $table)
		{
			$this->_table							= $table;
		}
		
		if($set)
		{
			$set									= array_merge($this->_set, $set);
		}
		
		$this->_prepare[]							= array
		(
			'function'								=> 'updateBatch',
			'arguments'								=> array($set, '', $batch_size)
		);
		
		return $this->_run_query();
	}
	
	/**
	 * Replace
	 * Your contribution is needed to write complete hint about
	 * this method
	 */
	public function replace($table = null, $set = array())
	{
		if($set)
		{
			$set									= array_merge($this->_set, $set);
		}
		
		if(!$this->_table && $table)
		{
			$this->_table							= $table;
		}
		
		$this->_prepare[]							= array
		(
			'function'								=> 'replace',
			'arguments'								=> array($set)
		);
		
		return $this->_run_query();
	}
	
	/**
	 * Delete
	 * Your contribution is needed to write complete hint about
	 * this method
	 */
	public function delete($table = null, $where = array(), $limit = 0, $reset_data = true)
	{
		if(!$this->_table && $table)
		{
			$this->_table							= $table;
		}
		
		if($limit && (!in_array($this->db->DBDriver, array('SQLSRV', 'Postgre')) || ($this->db->DBDriver === 'SQLSRV' && $this->db->getVersion() >= 10)))
		{
			$this->_limit							= $limit;
		}
		
		$this->_prepare[]							= array
		(
			'function'								=> 'delete',
			'arguments'								=> array($where, (!in_array($this->db->DBDriver, array('Postgre')) ? $this->_limit : null))
		);
		
		return $this->_run_query();
	}
	
	/**
	 * Truncate
	 * Your contribution is needed to write complete hint about
	 * this method
	 */
	public function truncate($table = null)
	{
		if(!$this->_table && $table)
		{
			$this->_table							= $table;
		}
		
		$this->_prepare[]							= array
		(
			'function'								=> 'truncate',
			'arguments'								=> array()
		);
		
		return $this->_run_query();
	}
	
	/**
	 * Empty Table
	 * Your contribution is needed to write complete hint about
	 * this method
	 */
	public function empty_table($table = null)
	{
		if(!$this->_table && $table)
		{
			$this->_table							= $table;
		}
		
		$this->_prepare[]							= array
		(
			'function'								=> 'emptyTable',
			'arguments'								=> array()
		);
		
		return $this->_run_query();
	}
	
	/**
	 * ---------------------------------------------------------------
	 * Transaction
	 * ---------------------------------------------------------------
	 */
	/**
	 * Transaction Begin
	 * Your contribution is needed to write complete hint about
	 * this method
	 */
	public function trans_begin()
	{
		$this->db->transBegin();
		
		return $this;
	}
	
	/**
	 * Transaction Start
	 * Your contribution is needed to write complete hint about
	 * this method
	 */
	public function trans_start()
	{
		$this->db->transStart();
		
		return $this;
	}
	
	/**
	 * Transaction Complete
	 * Your contribution is needed to write complete hint about
	 * this method
	 */
	public function trans_complete()
	{
		return $this->db->transComplete();
	}
	
	/**
	 * Get Transaction Status
	 * Your contribution is needed to write complete hint about
	 * this method
	 */
	public function trans_status()
	{
		return $this->db->transStatus();
	}
	
	/**
	 * Transaction Commit
	 * Your contribution is needed to write complete hint about
	 * this method
	 */
	public function trans_commit()
	{
		return $this->db->transCommit();
	}
	
	/**
	 * Transaction Rolling Back
	 * Your contribution is needed to write complete hint about
	 * this method
	 */
	public function trans_rollback()
	{
		return $this->db->transRollback();
	}
	
	/**
	 * Error
	 * Your contribution is needed to write complete hint about
	 * this method
	 */
	public function error()
	{
		return $this->db->error();
	}
	
	/**
	 * Run the query of collected property
	 * Your contribution is needed to write complete hint about
	 * this method
	 */
	private function _run_query()
	{
		if(!$this->_ordered && $this->_table)
		{
			if($this->_select)
			{
				foreach($this->_select as $key =>$val)
				{
					if($val && stripos($val, ' AS ') !== false)
					{
						$val						=  substr($val, strripos($val, ' AS ') + 4);
					}
					
					array_unshift
					(
						$this->_prepare,
						array
						(
							'function'				=> 'orderBy',
							'arguments'				=> array($val, 'ASC', true)
						)
					);
					
					break;
				}
			}
			else
			{
				$fields								= $this->db->getFieldNames($this->_table);
				
				foreach($fields as $key => $val)
				{
					array_unshift
					(
						$this->_prepare,
						array
						(
							'function'				=> 'orderBy',
							'arguments'				=> array($val, 'ASC', true)
						)
					);
					
					break;
				}
			}
		}
		
		if(!$this->builder)
		{
			if($this->_is_query)
			{
				$this->builder						= $this->db;
			}
			else
			{
				$this->builder						= $this->db->table($this->_table);
				
				if($this->_limit)
				{
					$this->builder->limit($this->_limit, $this->_offset);
				}
				
				if(!$this->_select)
				{
					$this->builder->select('*');
				}
			}
		}
		
		$builder_filter								= array('get', 'getWhere', 'countAll', 'countAllResults', 'insert', 'insertBatch', 'update', 'updateBatch', 'delete', 'deleteBatch', 'truncate', 'emptyTable', 'query', 'selectSubQuery');
		$result_filter								= array('getFieldCount', 'getFieldName', 'getFieldData', 'getNumRows', 'getResult', 'getResultArray', 'getResultObject', 'getRow', 'getRowArray', 'getRowObject');
		
		foreach($this->_prepare as $key => $val)
		{
			$function								= $val['function'];
			$arguments								= $val['arguments'];
			
			if(in_array($function, $builder_filter))
			{
				$this->_get							= true;
				
				// indicates that query builder has finished
				$this->_finished					= true;
			}
			else if(in_array($function, $result_filter))
			{
				if(!$this->_get)
				{
					$this->builder					= $this->builder->get();
				}
				else
				{
					$this->builder					= $query;
				}
				
				// indicates that query builder has finished
				$this->_finished					= true;
			}
			
			if(is_array($arguments) && sizeof($arguments) == 7)
			{
				$query								= $this->builder->$function($arguments[0], $arguments[1], $arguments[2], $arguments[3], $arguments[4], $arguments[5], $arguments[6]);
			}
			else if(is_array($arguments) && sizeof($arguments) == 6)
			{
				$query								= $this->builder->$function($arguments[0], $arguments[1], $arguments[2], $arguments[3], $arguments[4], $arguments[5]);
			}
			else if(is_array($arguments) && sizeof($arguments) == 5)
			{
				$query								= $this->builder->$function($arguments[0], $arguments[1], $arguments[2], $arguments[3], $arguments[4]);
			}
			else if(is_array($arguments) && sizeof($arguments) == 4)
			{
				$query								= $this->builder->$function($arguments[0], $arguments[1], $arguments[2], $arguments[3]);
			}
			else if(is_array($arguments) && sizeof($arguments) == 3)
			{
				$query								= $this->builder->$function($arguments[0], $arguments[1], $arguments[2]);
			}
			else if(is_array($arguments) && sizeof($arguments) == 2)
			{
				$query								= $this->builder->$function($arguments[0], $arguments[1]);
			}
			else
			{
				$query								= $this->builder->$function((isset($arguments[0]) ? $arguments[0] : $arguments));
			}
		}
		
		if($this->_finished)
		{
			$output									= $query;
			
			// reset properties
			$this->builder							= null;
			$this->_prepare							= array();
			$this->_finished						= false;
			$this->_ordered							= false;
			$this->_select							= array();
			$this->_from							= null;
			$this->_table							= null;
			$this->_set								= array();
			$this->_limit							= null;
			$this->_offset							= null;
			$this->_set								= array();
			$this->_get								= false;
			
			return $output;
		}
		
		return $this;
	}
	
	private function _cast_column($column = null, $value = '')
	{
		$column										= trim($column);
		$operand									= null;
		
		if(strpos($column, ' ') !== false)
		{
			// get operand if any
			$get_operand							= substr($column, strrpos($column, ' ') + 1);
			
			if(in_array($get_operand, array('!=', '>=', '<=', '>', '<')))
			{
				// remove operand from column
				$column								= substr($column, 0, strrpos($column, ' '));
				
				// set operand
				$operand							= $get_operand;
			}
		}
		
		if(in_array($this->db->DBDriver, array('SQLSRV', 'Postgre')) && !stripos($column, '(') && !stripos($column, ')'))
		{
			// type casting for PostgreSQL
			if(in_array(gettype($value), array('integer')))
			{
				$cast_type							= 'INTEGER';
				$value								= (int) $value;
			}
			else if(in_array(gettype($value), array('double')))
			{
				$cast_type							= 'DOUBLE';
				$value								= (double) $value;
			}
			else if(in_array(gettype($value), array('float')))
			{
				$cast_type							= 'FLOAT';
				$value								= (float) $value;
			}
			else if($value && \DateTime::createFromFormat('Y-m-d H:i:s', $value))
			{
				$cast_type							= ($this->db->DBDriver == 'SQLSRV' ? 'DATETIME' : 'TIMESTAMP');
				$value								= (string) $value;
			}
			else if($value && \DateTime::createFromFormat('Y-m-d', $value))
			{
				$cast_type							= 'DATE';
				$value								= (string) $value;
			}
			else if(!is_array(gettype($value)))
			{
				$cast_type							= 'VARCHAR' . ($this->db->DBDriver == 'SQLSRV' ? '(MAX)' : null);
				$value								= (string) $value;
			}
			
			$column									= (stripos($column, ' ') !== false ? substr($column, 0, stripos($column, ' ')) : $column);
			
			if($this->db->DBDriver == 'SQLSRV')
			{
				$column								= 'CONVERT(' . $cast_type . ', ' . $column . ')';
			}
			else
			{
				$column								= 'CAST(' . $column . ' AS ' . $cast_type . ')';
			}
		}
		
		return array
		(
			'column'								=> $column . ($operand ? ' ' . $operand : null),
			'value'									=> $value
		);
	}
}
