<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * crud_model
 *
 * The global model that linked to the core, make crud easier
 */
class Crud_model extends CI_Model
{
	function __construct()
	{
		parent::__construct();
		
		/* load the database class */
		$this->load->database();
	}
	
	public function database_config($driver = null, $hostname = null, $port = null, $username = null, $password = null, $database = null)
	{
		if('default' == $driver)
		{
			$this->load->library('connector');
			$this->db								= $this->connector->connect();
		}
		else
		{
			ini_set('sqlsrv.ClientBufferMaxKBSize', 524288);
			
			/* set the default dsn for non-standard connection */
			$dsn									= null;
			
			if('pdo' == $driver)
			{
				$dsn								= 'dblib:host=' . $hostname . ($port ? (phpversion() <= 7.2 ? ':' : ',') . $port : '') . ';dbname=' . $database;
			}
			
			/* define config */
			$config									= array
			(
				'dsn'								=> $dsn,
				'hostname' 							=> $hostname . ($port ? ',' . $port : null),
				'username'							=> $username,
				'password' 							=> $password,
				'database' 							=> $database,
				'dbdriver' 							=> $driver
			);
			
			/* load the new database connection with the defined config */
			$this->db								= $this->load->database($config, TRUE);
		}
		
		return $this;
	}
	
	public function list_tables()
	{
		return $this->db->list_tables();
	}
	
	public function table_exists($table = null)
	{
		$table										= (strpos($table, ' ') !== false ? substr($table, 0, strpos($table, ' ')) : $table);
		
		if($this->db->table_exists($table))
		{
			return true;
		}
		return false;
	}
	
	public function field_exists($field = null, $table = null)
	{
		$table										= (strpos($table, ' ') !== false ? substr($table, 0, strpos($table, ' ')) : $table);
		
		if($this->db->field_exists($field, $table))
		{
			return true;
		}
		return false;
	}
	
	public function list_fields($table = null)
	{
		$table										= (strpos($table, ' ') !== false ? substr($table, 0, strpos($table, ' ')) : $table);
		
		if($this->table_exists($table))
		{
			return $this->db->list_fields($table);
		}
		return false;
	}
	
	public function field_data($table = null)
	{
		$table										= (strpos($table, ' ') !== false ? substr($table, 0, strpos($table, ' ')) : $table);
		
		if($this->table_exists($table))
		{
			return $this->db->field_data($table);
		}
		return false;
	}
	
	public function reset_query()
	{
		$this->db->reset_query();
		return $this;
	}
	
	public function start_cache()
	{
		$this->db->start_cache();
		return $this;
	}
	
	public function stop_cache()
	{
		$this->db->stop_cache();
		return $this;
	}
	
	public function flush_cache()
	{
		$this->db->flush_cache();
		return $this;
	}
	
	public function set_dbprefix($prefix = null)
	{
		$this->db->set_dbprefix($prefix);
		return $this;
	}
	
	public function dbprefix($table = null)
	{
		$this->db->dbprefix($table);
		return $this;
	}
	
	public function select($select = null, $escape = true)
	{
		$this->db->select($select, $escape);
		return $this;
	}
	
	public function select_avg($select = null, $alias = null)
	{
		$this->db->select_avg($select, ($alias ? $alias : $select));
		return $this;
	}
	
	public function select_max($select = null, $alias = null)
	{
		$this->db->select_max($select, ($alias ? $alias : $select));
		return $this;
	}
	
	public function select_min($select = null, $alias = null)
	{
		$this->db->select_min($select, ($alias ? $alias : $select));
		return $this;
	}
	
	public function select_sum($select = null, $alias = null)
	{
		$this->db->select_sum($select, ($alias ? $alias : $select));
		return $this;
	}
	
	public function distinct($val = null)
	{
		$this->db->distinct($val);
		return $this;
	}
	
	public function from($from = null)
	{
		$this->db->from($from);
		return $this;
	}
	
	public function join($table = null, $condition = null, $type = null, $escape = true)
	{
		$this->db->join($table, $condition, $type, $escape);
		return $this;
	}
	
	public function group_where($where = array())
	{
		if($where && is_array($where))
		{
			foreach($where as $key => $val)
			{
				$this->db->group_start()->where($val)->group_end();
			}
		}
		return $this;
	}
	
	public function where($key = null, $value = null, $escape = true)
	{
		if(is_array($key))
		{
			foreach($key as $_key => $_val)
			{
				if(!$_val) continue;
				
				if(stripos(trim($_key), ' NOT IN') !== false)
				{
					$this->db->where_not_in(str_ireplace(' NOT IN', null, $_key), $_val);
				}
				elseif(stripos(trim($_key), ' IN') !== false)
				{
					$this->db->where_in(str_ireplace(' IN', null, $_key), $_val);
				}
				else
				{
					$this->db->where($_key, $_val);
				}
			}
		}
		else
		{
			$this->db->where($key, $value, $escape);
		}
		return $this;
	}
	
	public function or_where($key = null, $value = null, $escape = true)
	{
		$this->db->or_where($key, $value, $escape);
		return $this;
	}
	
	public function where_in($key = null, $value = null, $escape = true)
	{
		$this->db->where_in($key, $value, $escape);
		return $this;
	}
	
	public function or_where_in($key = null, $value = null, $escape = true)
	{
		$this->db->or_where_in($key, $value, $escape);
		return $this;
	}
	
	public function where_not_in($key = null, $value = null, $escape = true)
	{
		$this->db->where_not_in($key, $value, $escape);
		return $this;
	}
	
	public function or_where_not_in($key = null, $value = null, $escape = true)
	{
		$this->db->or_where_not_in($key, $value, $escape);
		return $this;
	}
	
	public function like($field = null, $match = null, $side = null, $escape = true)
	{
		$this->db->like($field, $match, $side, $side);
		return $this;
	}
	
	public function or_like($field = null, $match = null, $side = null, $escape = true)
	{
		$this->db->or_like($field, $match, $side, $side);
		return $this;
	}
	
	public function not_like($field = null, $match = null, $side = null, $escape = true)
	{
		$this->db->not_like($field, $match, $side, $side);
		return $this;
	}
	
	public function or_not_like($field = null, $match = null, $side = null, $escape = true)
	{
		$this->db->or_not_like($field, $match, $side, $side);
		return $this;
	}
	
	public function having($key = null, $value = null, $escape = true)
	{
		$this->db->having($key, $value, $escape);
		return $this;
	}
	
	public function or_having($key = null, $value = null, $escape = true)
	{
		$this->db->or_having($key, $value, $escape);
		return $this;
	}
	
	public function group_start()
	{
		$this->db->group_start();
		return $this;
	}
	
	public function group_end()
	{
		$this->db->group_end();
		return $this;
	}
	
	public function or_group_start()
	{
		$this->db->or_group_start();
		return $this;
	}
	
	public function not_group_start()
	{
		$this->db->not_group_start();
		return $this;
	}
	
	public function or_not_group_start()
	{
		$this->db->or_not_group_start();
		return $this;
	}
	
	public function group_by($by = null)
	{
		$this->db->group_by($by);
		return $this;
	}
	
	public function order_by($orderby = null, $direction = null, $escape = true)
	{
		$this->db->order_by($orderby, $direction, $escape);
		return $this;
	}
	
	public function limit($value = null, $offset = null)
	{
		$this->db->limit($value, $offset);
		return $this;
	}
	
	public function offset($offset = null)
	{
		$this->db->offset($offset);
		return $this;
	}
	
	public function set($key = null, $value = null, $escape = true)
	{
		$this->db->set($key, $value, $escape);
		return $this;
	}
	
	public function insert($table = null, $set = array(), $escape = true)
	{
		if($this->db->insert($table, $set, $escape))
		{
			return true;
		}
		return false;
	}
	
	public function insert_batch($table = null, $set = array(), $escape = true, $batch_size = 1)
	{
		if($this->db->insert_batch($table, $set, $escape, $batch_size))
		{
			return true;
		}
		return false;
	}
	
	public function set_insert_batch($key = null, $value = null, $escape = true)
	{
		if($this->db->set_insert_batch($key, $value, $escape))
		{
			return true;
		}
		return false;
	}
	
	public function update($table = null, $set = array(), $where = false, $limit = null)
	{
		if($this->db->update($table, $set, $where, $limit))
		{
			return true;
		}
		return false;
	}
	
	public function update_batch($table = null, $set = array(), $escape = true, $batch_size = 1)
	{
		if($this->db->update_batch($table, $set, $escape, $batch_size))
		{
			return true;
		}
		return false;
	}
	
	public function set_update_batch($key = null, $value = null, $escape = true)
	{
		if($this->db->set_update_batch($key, $value, $escape))
		{
			return true;
		}
		return false;
	}
	
	public function replace($table = null, $set = array())
	{
		if($this->db->replace($table, $set))
		{
			return true;
		}
		return false;
	}
	
	public function delete($table = null, $where = array(), $limit = null, $reset_data = true)
	{
		if($this->db->delete($table, $where, $limit, $reset_data))
		{
			return true;
		}
		return false;
	}
	
	public function truncate($table = null)
	{
		if($this->db->truncate($table))
		{
			return true;
		}
		return false;
	}
	
	public function empty_table($table = null)
	{
		if($this->db->empty_table($table))
		{
			return true;
		}
		return false;
	}
	
	public function count_all($table = null, $reset = true)
	{
		return $this->db->count_all($table, $reset);
	}
	
	public function count_all_results($table = null, $reset = true)
	{
		return $this->db->count_all_results($table, $reset);
	}
	
	public function get($table = null, $limit = null, $offset = null)
	{
		$this->db->start_cache();
		$output										= $this->db->get($table, $limit, $offset);
		$this->db->stop_cache();
		$this->db->flush_cache();
		return $output;
	}
	
	public function get_where($table = null, $where = null, $limit = null, $offset = null)
	{
		return $this->db->get_where($table, $where, $limit, $offset);
	}
	
	public function get_compiled_select($table = null, $reset = true)
	{
		return $this->db->get_compiled_select($table, $reset);
	}
	
	public function get_compiled_insert($table = null, $reset = true)
	{
		return $this->db->get_compiled_insert($table, $reset);
	}
	
	public function get_compiled_update($table = null, $reset = true)
	{
		return $this->db->get_compiled_update($table, $reset);
	}
	
	public function get_compiled_delete($table = null, $reset = true)
	{
		return $this->db->get_compiled_delete($table, $reset);
	}
	
	public function query($query = null)
	{
		return $this->db->query($query);
	}
	
	public function last_query()
	{
		return $this->db->last_query();
	}
	
	public function create_column($table = null, $column = array())
	{
		$this->load->dbforge();
		if($this->dbforge->add_column($table, $column))
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	public function update_column($table = null, $column = array())
	{
		$this->load->dbforge();
		if($this->dbforge->modify_column($table, $column))
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	public function delete_column($table = null, $column = null)
	{
		$this->load->dbforge();
		if($this->dbforge->drop_column($table, $column))
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	public function insert_id()
	{
		return $this->db->insert_id();
	}
	
	public function affected_rows()
	{
		return $this->db->affected_rows();
	}
	
	/**
	 * Transaction
	 */
	public function trans_begin()
	{
		return $this->db->trans_begin();
	}
	
	public function trans_start()
	{
		return $this->db->trans_start();
	}
	
	public function trans_complete()
	{
		return $this->db->trans_complete();
	}
	
	public function trans_status()
	{
		return $this->db->trans_status();
	}
	
	public function trans_commit()
	{
		return $this->db->trans_commit();
	}
	
	public function trans_rollback()
	{
		return $this->db->trans_rollback();
	}
}