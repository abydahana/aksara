<?php namespace Aksara\Laboratory;
/**
 * Seeder
 *
 * @author			Aby Dahana
 * @profile			abydahana.github.io
 * @website			www.aksaracms.com
 * @since			version 4.2.6
 * @copyright		(c) 2021 - Aksara Laboratory
 */
class Seeder
{
	public function __construct()
	{
		$this->db									= \Config\Database::connect();
	}
	
	public static function seed()
	{
		if(!$this->db->tableExists('app__years'))
		{
			// add app__years table
			$this->_add_year_table();
		}
		
		if(!$this->db->tableExists('app__connections'))
		{
			// add app__connections table
			$this->_add_connection_table();
		}
		
		if(!$this->db->tableExists('app__shortlink'))
		{
			// add app__shortlink table
			$this->_add_shortlink_table();
		}
		
		if(!$this->db->tableExists('app__countries'))
		{
			// rename table from countries to app__countries
			$this->_modify_country_table();
		}
		
		if(!$this->db->fieldExists('action_sound', 'app__settings'))
		{
			// add action_sound column to app__settings
			$this->_modify_setting_table();
		}
		
		if($this->db->fieldExists('module', 'app__activity_logs'))
		{
			// modify app__activity_logs table
			$this->_modify_activity_log_table();
		}
		
		if($this->db->fieldExists('module', 'app__groups_privileges'))
		{
			// modify app__groups table
			$this->_modify_group_table();
			
			// modify app__groups_privileges table
			$this->_modify_group_privilege_table();
		}
	}
	
	private function _add_year_table()
	{
		$dbforge									= \Config\Database::forge();
		
		$dbforge->addField
		(
			array
			(
				'year'								=> array
				(
					'type'							=> 'YEAR',
					'constraint'					=> 4,
					'unsigned'						=> true
				),
				'default'							=> array
				(
					'type'							=> 'TINYINT',
					'constraint'					=> 1
				),
				'status'							=> array
				(
					'type'							=> 'TINYINT',
					'constraint'					=> 1
				)
			)
		);
		
		$dbforge->addPrimaryKey('year');
		
		$dbforge->createTable('app__years', true);
	}
	
	private function _add_connection_table()
	{
		$dbforge									= \Config\Database::forge();
		
		$dbforge->addField
		(
			array
			(
				'year'								=> array
				(
					'type'							=> 'YEAR',
					'constraint'					=> 4
				),
				'name'								=> array
				(
					'type'							=> 'VARCHAR',
					'constraint'					=> 255
				),
				'description'						=> array
				(
					'type'							=> 'TINYTEXT'
				),
				'database_driver'					=> array
				(
					'type'							=> 'VARCHAR',
					'constraint'					=> 32
				),
				'hostname'							=> array
				(
					'type'							=> 'VARCHAR',
					'constraint'					=> 255
				),
				'port'								=> array
				(
					'type'							=> 'VARCHAR',
					'constraint'					=> 255
				),
				'username'							=> array
				(
					'type'							=> 'VARCHAR',
					'constraint'					=> 255
				),
				'password'							=> array
				(
					'type'							=> 'VARCHAR',
					'constraint'					=> 255
				),
				'database_name'						=> array
				(
					'type'							=> 'VARCHAR',
					'constraint'					=> 255
				),
				'status'							=> array
				(
					'type'							=> 'TINYINT',
					'constraint'					=> 1
				)
			)
		);
		
		$dbforge->addPrimaryKey(array('year', 'database_driver'));
		
		$dbforge->addForeignKey('year', 'app__years', 'year', 'CASCADE', 'CASCADE');
		
		$dbforge->createTable('app__connections', true);
	}
	
	private function _add_shortlink_table()
	{
		$dbforge									= \Config\Database::forge();
		
		$dbforge->addField
		(
			array
			(
				'hash'								=> array
				(
					'type'							=> 'VARCHAR',
					'constraint'					=> 6
				),
				'url'								=> array
				(
					'type'							=> 'VARCHAR',
					'constraint'					=> 255
				),
				'session'							=> array
				(
					'type'							=> 'TEXT'
				)
			)
		);
		
		$dbforge->addPrimaryKey('hash');
		
		$dbforge->createTable('app__shortlink', true);
	}
	
	private function _modify_country_table()
	{
		$dbforge									= \Config\Database::forge();
		
		$dbforge->renameTable('countries', 'app__countries');
	}
	
	private function _modify_setting_table()
	{
		$dbforge									= \Config\Database::forge();
		
		$column										= array
		(
			'action_sound'							=> array
			(
				'type'								=> 'TINYINT',
				'constraint'						=> 1,
				'default'							=> '0'
			)
		);
		
		$dbforge->addColumn('app__settings', $column);
	}
	
	private function _modify_activity_log_table()
	{
		$dbforge									= \Config\Database::forge();
		
		$column										= array
		(
			'page'									=> array
			(
				'name'								=> 'path',
				'type'								=> 'VARCHAR',
				'constraint'						=> 255,
				'after'								=> 'user_id'
			)
		);
		
		$dbforge->modifyColumn('app__activity_logs', $column);
		$dbforge->dropColumn('app__activity_logs', 'module,submodule,controller');
	}
	
	private function _modify_group_table()
	{
		$query										= $this->db->table('app__groups')->select('group_id, group_privileges')->get()->getResult();
		
		if($query)
		{
			foreach($query as $key => $val)
			{
				$this->_privileges					= array();
				$privileges							= json_decode($val->group_privileges);
				
				if(!$privileges) continue;
				
				$this->_privileges($privileges);
				
				$privileges							= json_encode($this->_privileges);
				
				$this->db->table('app__groups')->set('group_privileges', $privileges)->where('group_id', $val->group_id)->update();
			}
		}
	}
	
	private function _modify_group_privilege_table()
	{
		$dbforge									= \Config\Database::forge();
		
		$column										= array
		(
			'path'									=> array
			(
				'type'								=> 'VARCHAR',
				'constraint'						=> 255,
				'first'								=> true
			)
		);
		
		$dbforge->addColumn('app__groups_privileges', $column);
		
		$query										= $this->db->table('app__groups_privileges')->get()->getResult();
		
		if($query)
		{
			foreach($query as $key => $val)
			{
				if($val->module != $val->submodule && $val->submodule != $val->controller)
				{
					$path							= $val->module . '/' . $val->submodule . '/' . $val->controller;
				}
				elseif($val->module != $val->submodule)
				{
					$path							= $val->module . '/' . $val->submodule;
				}
				else
				{
					$path							= $val->module;
				}
				
				$this->db->table('app__groups_privileges')->where
				(
					array
					(
						'module'					=> $val->module,
						'submodule'					=> $val->submodule,
						'controller'				=> $val->controller
					)
				)
				->update
				(
					array
					(
						'path'						=> $path
					)
				);
			}
		}
		
		$dbforge->dropColumn('app__groups_privileges', 'module,submodule,controller');
		
		$this->db->query('ALTER TABLE app__groups_privileges ADD PRIMARY KEY (path)');
	}
	
	private function _privileges($privileges, $path = null)
	{
		foreach($privileges as $key => $val)
		{
			if(!is_array($val))
			{
				$this->_privileges($val, ($path ? $path . '/' : $path) . $key);
			}
			else
			{
				list($a, $b, $c)					= array_pad(explode('/', $path . '/' . $key), 3, null);
				
				if($a != $b && $b != $c)
				{
					$module							= $a . '/' . $b . '/' . $c;
				}
				elseif($a != $b)
				{
					$module							= $a . '/' . $b;
				}
				else
				{
					$module							= $a;
				}
				
				$this->_privileges[$module]			= $val;
			}
		}
	}
}
