<?php namespace App\Database\Migrations;
/**
 * @author			Aby Dahana
 * @profile			abydahana.github.io
 * @website			www.aksaracms.com
 * @copyright		(c) 2021 - Aksara Laboratory
 * @since			version 4.2.8
 */
use CodeIgniter\Database\Migration;

class Installer extends Migration
{
    public function up()
    {
		if($this->db->DBDriver == 'SQLSRV')
		{
			$this->db->query
			('
				EXEC sp_MSforeachtable "declare @name nvarchar(max); set @name = parsename(\'?\', 1); exec sp_MSdropconstraints @name";
				EXEC sp_MSforeachtable "drop table ?";
			');
		}
		else
		{
			$this->db->disableForeignKeyChecks();
			
			foreach($this->db->listTables() as $key => $val)
			{
				if('migrations' == $val || !$this->db->tableExists($val)) continue;
				
				$this->forge->dropTable($val, true, true);
			}
			
			$this->db->enableForeignKeyChecks();
		}
		
		$this->forge->addField
		(
			array
			(
				'id' => array
				(
					'type' => 'bigint',
					'constraint' => 22,
					'unsigned' => true,
					'auto_increment' => true,
					'null' => false
				),
				'user_id' => array
				(
					'type' => 'bigint',
					'constraint' => 22,
					'unsigned' => true,
					'null' => false
				),
				'path' => array
				(
					'type' => 'varchar',
					'constraint' => 255,
					'null' => false
				),
				'method' => array
				(
					'type' => 'varchar',
					'constraint' => 255,
					'null' => false
				),
				'browser' => array
				(
					'type' => 'varchar',
					'constraint' => 255,
					'null' => false
				),
				'platform' => array
				(
					'type' => 'varchar',
					'constraint' => 255,
					'null' => false
				),
				'ip_address' => array
				(
					'type' => 'varchar',
					'constraint' => 22,
					'null' => false
				),
				'timestamp' => array
				(
					'type' => 'timestamp',
					'null' => false
				),
			)
		);
		$this->forge->addKey('id', true, true);
		$this->forge->addKey('user_id', false, false);
		$this->forge->createTable('app__activity_logs');
		
		$this->forge->addField
		(
			array
			(
				'announcement_id' => array
				(
					'type' => 'bigint',
					'constraint' => 22,
					'unsigned' => true,
					'auto_increment' => true,
					'null' => false
				),
				'title' => array
				(
					'type' => 'varchar',
					'constraint' => 255,
					'null' => false
				),
				'announcement_slug' => array
				(
					'type' => 'varchar',
					'constraint' => 255,
					'null' => false
				),
				'content' => array
				(
					'type' => 'text',
					'null' => false
				),
				'placement' => array
				(
					'type' => 'tinyint',
					'constraint' => 1,
					'default' => '0',
					'null' => false
				),
				'start_date' => array
				(
					'type' => 'date',
					'null' => false
				),
				'end_date' => array
				(
					'type' => 'date',
					'null' => false
				),
				'created_timestamp' => array
				(
					'type' => 'timestamp',
					'null' => false
				),
				'updated_timestamp' => array
				(
					'type' => 'timestamp',
					'null' => true
				),
				'language_id' => array
				(
					'type' => 'int',
					'constraint' => 11,
					'unsigned' => true,
					'null' => false
				),
				'status' => array
				(
					'type' => 'tinyint',
					'constraint' => 1,
					'default' => '0',
					'null' => false
				),
			)
		);
		$this->forge->addKey('announcement_id', true, true);
		$this->forge->addKey('announcement_slug', false, true);
		$this->forge->addKey('language_id', false, false);
		$this->forge->createTable('app__announcements');
		
		$this->forge->addField
		(
			array
			(
				'year' => array
				(
					'type' => (in_array($this->db->DBDriver, array('Postgre', 'SQLSRV')) ? 'int' : 'year'),
					'constraint' => 4,
					'null' => false
				),
				'name' => array
				(
					'type' => 'varchar',
					'constraint' => 255,
					'null' => false
				),
				'description' => array
				(
					'type' => 'varchar',
					'constraint' => 255,
					'null' => false
				),
				'database_driver' => array
				(
					'type' => 'varchar',
					'constraint' => 32,
					'null' => false
				),
				'hostname' => array
				(
					'type' => 'varchar',
					'constraint' => 255,
					'null' => false
				),
				'port' => array
				(
					'type' => 'varchar',
					'constraint' => 255,
					'null' => false
				),
				'username' => array
				(
					'type' => 'varchar',
					'constraint' => 255,
					'null' => false
				),
				'password' => array
				(
					'type' => 'varchar',
					'constraint' => 255,
					'null' => false
				),
				'database_name' => array
				(
					'type' => 'varchar',
					'constraint' => 255,
					'null' => false
				),
				'status' => array
				(
					'type' => 'tinyint',
					'constraint' => 1,
					'default' => '0',
					'null' => false
				),
			)
		);
		$this->forge->addKey('year', true, true);
		$this->forge->addKey('database_driver', false, false);
		$this->forge->createTable('app__connections');
		
		$this->forge->addField
		(
			array
			(
				'id' => array
				(
					'type' => 'int',
					'constraint' => 5,
					'unsigned' => true,
					'auto_increment' => true,
					'null' => false
				),
				'code' => array
				(
					'type' => 'varchar',
					'constraint' => 8,
					'null' => false
				),
				'country' => array
				(
					'type' => 'varchar',
					'constraint' => 32,
					'null' => false
				),
				'status' => array
				(
					'type' => 'tinyint',
					'constraint' => 1,
					'default' => '0',
					'null' => false
				),
			)
		);
		$this->forge->addKey('id', true, true);
		$this->forge->createTable('app__countries');
		
		$this->forge->addField
		(
			array
			(
				'site_id' => array
				(
					'type' => 'int',
					'constraint' => 11,
					'unsigned' => true,
					'auto_increment' => true,
					'null' => false
				),
				'hostname' => array
				(
					'type' => 'varchar',
					'constraint' => 255,
					'null' => false
				),
				'port' => array
				(
					'type' => 'int',
					'constraint' => 5,
					'null' => false
				),
				'username' => array
				(
					'type' => 'varchar',
					'constraint' => 64,
					'null' => false
				),
				'password' => array
				(
					'type' => 'varchar',
					'constraint' => 512,
					'null' => false
				),
			)
		);
		$this->forge->addKey('site_id', true, true);
		$this->forge->createTable('app__ftp');
		
		$this->forge->addField
		(
			array
			(
				'group_id' => array
				(
					'type' => 'int',
					'constraint' => 11,
					'unsigned' => true,
					'auto_increment' => true,
					'null' => false
				),
				'group_name' => array
				(
					'type' => 'varchar',
					'constraint' => 64,
					'null' => false
				),
				'group_description' => array
				(
					'type' => (in_array($this->db->DBDriver, array('Postgre', 'SQLSRV')) ? 'text' : 'mediumtext'),
					'null' => false
				),
				'group_privileges' => array
				(
					'type' => (in_array($this->db->DBDriver, array('Postgre', 'SQLSRV')) ? 'text' : 'longtext'),
					'null' => false
				),
				'status' => array
				(
					'type' => 'tinyint',
					'constraint' => 1,
					'default' => '0',
					'null' => false
				),
			)
		);
		$this->forge->addKey('group_id', true, true);
		$this->forge->createTable('app__groups');
		
		$this->forge->addField
		(
			array
			(
				'path' => array
				(
					'type' => 'varchar',
					'constraint' => 255,
					'null' => false
				),
				'privileges' => array
				(
					'type' => (in_array($this->db->DBDriver, array('Postgre', 'SQLSRV')) ? 'text' : 'longtext'),
					'null' => false
				),
				'last_generated' => array
				(
					'type' => 'timestamp',
					'null' => false
				),
			)
		);
		$this->forge->addKey('path', true, true);
		$this->forge->createTable('app__groups_privileges');
		
		$this->forge->addField
		(
			array
			(
				'id' => array
				(
					'type' => 'int',
					'constraint' => 11,
					'unsigned' => true,
					'auto_increment' => true,
					'null' => false
				),
				'language' => array
				(
					'type' => 'varchar',
					'constraint' => 32,
					'null' => false
				),
				'description' => array
				(
					'type' => (in_array($this->db->DBDriver, array('Postgre', 'SQLSRV')) ? 'text' : 'tinytext'),
					'null' => false
				),
				'code' => array
				(
					'type' => 'varchar',
					'constraint' => 32,
					'null' => false
				),
				'locale' => array
				(
					'type' => 'varchar',
					'constraint' => 64,
					'null' => false
				),
				'status' => array
				(
					'type' => 'tinyint',
					'constraint' => 1,
					'default' => '0',
					'null' => false
				),
			)
		);
		$this->forge->addKey('id', true, true);
		$this->forge->addKey('code', false, true);
		$this->forge->createTable('app__languages');
		
		$this->forge->addField
		(
			array
			(
				'menu_id' => array
				(
					'type' => 'int',
					'constraint' => 11,
					'unsigned' => true,
					'auto_increment' => true,
					'null' => false
				),
				'menu_placement' => array
				(
					'type' => 'varchar',
					'constraint' => 22,
					'null' => false
				),
				'menu_label' => array
				(
					'type' => 'varchar',
					'constraint' => 32,
					'null' => false
				),
				'menu_description' => array
				(
					'type' => 'text',
					'null' => false
				),
				'serialized_data' => array
				(
					'type' => (in_array($this->db->DBDriver, array('Postgre', 'SQLSRV')) ? 'text' : 'longtext'),
					'null' => false
				),
				'group_id' => array
				(
					'type' => 'int',
					'constraint' => 11,
					'unsigned' => true,
					'null' => false
				),
				'status' => array
				(
					'type' => 'tinyint',
					'constraint' => 1,
					'default' => '0',
					'null' => false
				),
			)
		);
		$this->forge->addKey('menu_id', true, true);
		$this->forge->addKey('menu_placement', false, false);
		$this->forge->addKey('group_id', false, false);
		$this->forge->createTable('app__menus');
		
		$this->forge->addField
		(
			array
			(
				'id' => array
				(
					'type' => 'int',
					'constraint' => 11,
					'unsigned' => true,
					'auto_increment' => true,
					'null' => false
				),
				'title' => array
				(
					'type' => 'varchar',
					'constraint' => 64,
					'null' => false
				),
				'description' => array
				(
					'type' => 'text',
					'null' => false
				),
				'api_key' => array
				(
					'type' => 'varchar',
					'constraint' => 64,
					'null' => false
				),
				'method' => array
				(
					'type' => (in_array($this->db->DBDriver, array('Postgre', 'SQLSRV')) ? 'text' : 'tinytext'),
					'null' => false
				),
				'ip_range' => array
				(
					'type' => 'text',
					'null' => false
				),
				'valid_until' => array
				(
					'type' => 'date',
					'null' => false
				),
				'status' => array
				(
					'type' => 'int',
					'constraint' => 1,
					'null' => false
				),
			)
		);
		$this->forge->addKey('id', true, true);
		$this->forge->createTable('app__rest_api');
		
		$this->forge->addField
		(
			array
			(
				'id' => array
				(
					'type' => 'varchar',
					'constraint' => 128,
					'null' => false
				),
				'ip_address' => array
				(
					'type' => 'varchar',
					'constraint' => 45,
					'null' => false
				),
				'timestamp' => array
				(
					'type' => 'timestamp',
					'null' => false
				),
				'data' => array
				(
					'type' => (in_array($this->db->DBDriver, array('Postgre')) ? 'bytea' : (in_array($this->db->DBDriver, array('SQLSRV')) ? 'binary' : 'blob')),
					'null' => false
				),
			)
		);
		$this->forge->addKey('id', true, true);
		$this->forge->createTable('app__sessions');
		
		$this->forge->addField
		(
			array
			(
				'id' => array
				(
					'type' => 'int',
					'constraint' => 11,
					'unsigned' => true,
					'auto_increment' => true,
					'null' => false
				),
				'app_name' => array
				(
					'type' => 'varchar',
					'constraint' => 255,
					'null' => false
				),
				'app_description' => array
				(
					'type' => 'text',
					'null' => false
				),
				'app_logo' => array
				(
					'type' => 'varchar',
					'constraint' => 255,
					'null' => false
				),
				'app_icon' => array
				(
					'type' => 'varchar',
					'constraint' => 255,
					'null' => false
				),
				'frontend_theme' => array
				(
					'type' => 'varchar',
					'constraint' => 32,
					'null' => false
				),
				'backend_theme' => array
				(
					'type' => 'varchar',
					'constraint' => 32,
					'null' => false
				),
				'app_language' => array
				(
					'type' => 'int',
					'constraint' => 11,
					'unsigned' => true,
					'null' => false
				),
				'office_name' => array
				(
					'type' => 'varchar',
					'constraint' => 255,
					'null' => false
				),
				'office_phone' => array
				(
					'type' => 'varchar',
					'constraint' => 32,
					'null' => false
				),
				'office_email' => array
				(
					'type' => 'varchar',
					'constraint' => 64,
					'null' => false
				),
				'office_fax' => array
				(
					'type' => 'varchar',
					'constraint' => 32,
					'null' => false
				),
				'office_address' => array
				(
					'type' => 'text',
					'null' => false
				),
				'office_map' => array
				(
					'type' => 'text',
					'null' => false
				),
				'one_device_login' => array
				(
					'type' => 'tinyint',
					'constraint' => 1,
					'default' => '0',
					'null' => false
				),
				'username_changes' => array
				(
					'type' => 'tinyint',
					'constraint' => 1,
					'default' => '0',
					'null' => false
				),
				'frontend_registration' => array
				(
					'type' => 'tinyint',
					'constraint' => 1,
					'default' => '0',
					'null' => false
				),
				'default_membership_group' => array
				(
					'type' => 'int',
					'constraint' => 11,
					'null' => false
				),
				'auto_active_registration' => array
				(
					'type' => 'tinyint',
					'constraint' => 1,
					'default' => '0',
					'null' => false
				),
				'google_analytics_key' => array
				(
					'type' => 'varchar',
					'constraint' => 32,
					'null' => false
				),
				'openlayers_search_provider' => array
				(
					'type' => 'varchar',
					'constraint' => 10,
					'null' => false
				),
				'openlayers_search_key' => array
				(
					'type' => 'varchar',
					'constraint' => 128,
					'null' => false
				),
				'default_map_tile' => array
				(
					'type' => 'varchar',
					'constraint' => 255,
					'null' => false
				),
				'disqus_site_domain' => array
				(
					'type' => 'varchar',
					'constraint' => 128,
					'null' => false
				),
				'facebook_app_id' => array
				(
					'type' => 'varchar',
					'constraint' => 22,
					'null' => false
				),
				'facebook_app_secret' => array
				(
					'type' => 'varchar',
					'constraint' => 512,
					'null' => false
				),
				'google_client_id' => array
				(
					'type' => 'varchar',
					'constraint' => 255,
					'null' => false
				),
				'google_client_secret' => array
				(
					'type' => 'varchar',
					'constraint' => 512,
					'null' => false
				),
				'twitter_username' => array
				(
					'type' => 'varchar',
					'constraint' => 64,
					'null' => false
				),
				'instagram_username' => array
				(
					'type' => 'varchar',
					'constraint' => 64,
					'null' => false
				),
				'whatsapp_number' => array
				(
					'type' => 'varchar',
					'constraint' => 16,
					'null' => false
				),
				'smtp_email_masking' => array
				(
					'type' => 'varchar',
					'constraint' => 255,
					'null' => false
				),
				'smtp_sender_masking' => array
				(
					'type' => 'varchar',
					'constraint' => 64,
					'null' => false
				),
				'smtp_host' => array
				(
					'type' => 'varchar',
					'constraint' => 255,
					'null' => false
				),
				'smtp_port' => array
				(
					'type' => 'int',
					'constraint' => 5,
					'null' => false
				),
				'smtp_username' => array
				(
					'type' => 'varchar',
					'constraint' => 32,
					'null' => false
				),
				'smtp_password' => array
				(
					'type' => 'varchar',
					'constraint' => 512,
					'null' => false
				),
				'action_sound' => array
				(
					'type' => 'tinyint',
					'constraint' => 1,
					'default' => '0',
					'null' => false
				),
			)
		);
		$this->forge->addKey('id', true, true);
		$this->forge->addKey('app_language', false, false);
		$this->forge->createTable('app__settings');
		
		$this->forge->addField
		(
			array
			(
				'hash' => array
				(
					'type' => 'varchar',
					'constraint' => 64,
					'null' => false
				),
				'url' => array
				(
					'type' => 'text',
					'null' => false
				),
				'session' => array
				(
					'type' => 'text',
					'null' => false
				),
			)
		);
		$this->forge->addKey('hash', true, true);
		$this->forge->createTable('app__shortlink');
		
		$this->forge->addField
		(
			array
			(
				'user_id' => array
				(
					'type' => 'bigint',
					'constraint' => 22,
					'unsigned' => true,
					'auto_increment' => true,
					'null' => false
				),
				'group_id' => array
				(
					'type' => 'int',
					'constraint' => 11,
					'unsigned' => true,
					'null' => false
				),
				'language_id' => array
				(
					'type' => 'int',
					'constraint' => 11,
					'unsigned' => true,
					'null' => false
				),
				'country_id' => array
				(
					'type' => 'int',
					'constraint' => 5,
					'unsigned' => true,
					'null' => false
				),
				'email' => array
				(
					'type' => 'varchar',
					'constraint' => 255,
					'null' => false
				),
				'password' => array
				(
					'type' => 'varchar',
					'constraint' => 255,
					'null' => false
				),
				'username' => array
				(
					'type' => 'varchar',
					'constraint' => 32,
					'null' => false
				),
				'first_name' => array
				(
					'type' => 'varchar',
					'constraint' => 32,
					'null' => false
				),
				'last_name' => array
				(
					'type' => 'varchar',
					'constraint' => 32,
					'null' => false
				),
				'gender' => array
				(
					'type' => 'tinyint',
					'constraint' => 1,
					'default' => '0',
					'null' => false
				),
				'bio' => array
				(
					'type' => 'text',
					'null' => false
				),
				'photo' => array
				(
					'type' => 'varchar',
					'constraint' => 255,
					'null' => false
				),
				'address' => array
				(
					'type' => 'text',
					'null' => false
				),
				'phone' => array
				(
					'type' => 'varchar',
					'constraint' => 16,
					'null' => false
				),
				'postal_code' => array
				(
					'type' => 'varchar',
					'constraint' => 10,
					'null' => false
				),
				'registered_date' => array
				(
					'type' => 'date',
					'null' => false
				),
				'last_login' => array
				(
					'type' => 'timestamp',
					'default' => null
				),
				'status' => array
				(
					'type' => 'tinyint',
					'constraint' => 1,
					'default' => '0',
					'null' => false
				),
			)
		);
		$this->forge->addKey('user_id', true, true);
		$this->forge->addKey('group_id', false, false);
		$this->forge->addKey('language_id', false, false);
		$this->forge->addKey('username', false, true);
		$this->forge->addKey('email', false, true);
		$this->forge->createTable('app__users');
		
		$this->forge->addField
		(
			array
			(
				'user_id' => array
				(
					'type' => 'bigint',
					'constraint' => 22,
					'unsigned' => true,
					'auto_increment' => true,
					'null' => false
				),
				'hash' => array
				(
					'type' => 'varchar',
					'constraint' => 64,
					'null' => false
				),
			)
		);
		$this->forge->addKey('user_id', true, true);
		$this->forge->createTable('app__users_hash');
		
		$this->forge->addField
		(
			array
			(
				'user_id' => array
				(
					'type' => 'bigint',
					'constraint' => 22,
					'unsigned' => true,
					'auto_increment' => true,
					'null' => false
				),
				'sub_level_1' => array
				(
					'type' => 'int',
					'constraint' => 11,
					'unsigned' => true,
					'null' => false
				),
				'visible_menu' => array
				(
					'type' => 'text',
					'null' => false
				),
				'access_year' => array
				(
					'type' => (in_array($this->db->DBDriver, array('Postgre', 'SQLSRV')) ? 'int' : 'year'),
					'constraint' => 4,
					'null' => false
				),
			)
		);
		$this->forge->addKey('user_id', true, true);
		$this->forge->createTable('app__users_privileges');
		
		$this->forge->addField
		(
			array
			(
				'ip_address' => array
				(
					'type' => 'varchar',
					'constraint' => 32,
					'null' => false
				),
				'timestamp' => array
				(
					'type' => 'timestamp',
					'null' => false
				),
				'browser' => array
				(
					'type' => 'varchar',
					'constraint' => 32,
					'null' => false
				),
				'platform' => array
				(
					'type' => 'varchar',
					'constraint' => 32,
					'null' => false
				),
			)
		);
		$this->forge->addKey('ip_address', false, false);
		$this->forge->createTable('app__visitor_logs');
		
		$this->forge->addField
		(
			array
			(
				'year' => array
				(
					'type' => (in_array($this->db->DBDriver, array('Postgre', 'SQLSRV')) ? 'int' : 'year'),
					'constraint' => 4,
					'null' => false
				),
				'default' => array
				(
					'type' => 'tinyint',
					'constraint' => 1,
					'default' => '0',
					'null' => false
				),
				'status' => array
				(
					'type' => 'tinyint',
					'constraint' => 1,
					'default' => '0',
					'null' => false
				),
			)
		);
		$this->forge->addKey('year', true, true);
		$this->forge->createTable('app__years');
		
		$this->forge->addField
		(
			array
			(
				'post_id' => array
				(
					'type' => 'bigint',
					'constraint' => 22,
					'unsigned' => true,
					'auto_increment' => true,
					'null' => false
				),
				'post_title' => array
				(
					'type' => 'varchar',
					'constraint' => 255,
					'null' => false
				),
				'post_slug' => array
				(
					'type' => 'varchar',
					'constraint' => 255,
					'null' => false
				),
				'post_excerpt' => array
				(
					'type' => 'text',
					'null' => false
				),
				'post_content' => array
				(
					'type' => (in_array($this->db->DBDriver, array('Postgre', 'SQLSRV')) ? 'text' : 'longtext'),
					'null' => false
				),
				'post_category' => array
				(
					'type' => 'int',
					'constraint' => 11,
					'unsigned' => true,
					'null' => false
				),
				'post_tags' => array
				(
					'type' => 'text',
					'null' => false
				),
				'created_timestamp' => array
				(
					'type' => 'timestamp',
					'null' => false
				),
				'updated_timestamp' => array
				(
					'type' => 'timestamp',
					'null' => true
				),
				'author' => array
				(
					'type' => 'bigint',
					'constraint' => 22,
					'unsigned' => true,
					'null' => false
				),
				'headline' => array
				(
					'type' => 'tinyint',
					'constraint' => 1,
					'default' => '0',
					'null' => false
				),
				'featured_image' => array
				(
					'type' => 'varchar',
					'constraint' => 255,
					'null' => false
				),
				'language_id' => array
				(
					'type' => 'int',
					'constraint' => 11,
					'unsigned' => true,
					'null' => false
				),
				'status' => array
				(
					'type' => 'tinyint',
					'constraint' => 1,
					'default' => '0',
					'null' => false
				),
			)
		);
		$this->forge->addKey('post_id', true, true);
		$this->forge->addKey('post_slug', false, true);
		$this->forge->addKey('author', false, false);
		$this->forge->addKey('post_category', false, false);
		$this->forge->addKey('language_id', false, false);
		$this->forge->createTable('blogs');
		
		$this->forge->addField
		(
			array
			(
				'category_id' => array
				(
					'type' => 'int',
					'constraint' => 11,
					'unsigned' => true,
					'auto_increment' => true,
					'null' => false
				),
				'category_title' => array
				(
					'type' => 'varchar',
					'constraint' => 32,
					'null' => false
				),
				'category_slug' => array
				(
					'type' => 'varchar',
					'constraint' => 32,
					'null' => false
				),
				'category_description' => array
				(
					'type' => 'text',
					'null' => false
				),
				'category_image' => array
				(
					'type' => 'varchar',
					'constraint' => 255,
					'null' => false
				),
				'language_id' => array
				(
					'type' => 'int',
					'constraint' => 11,
					'unsigned' => true,
					'null' => false
				),
				'status' => array
				(
					'type' => 'tinyint',
					'constraint' => 1,
					'default' => '0',
					'null' => false
				),
			)
		);
		$this->forge->addKey('category_id', true, true);
		$this->forge->addKey('category_slug', false, true);
		$this->forge->addKey('language_id', false, false);
		$this->forge->createTable('blogs__categories');
		
		$this->forge->addField
		(
			array
			(
				'gallery_id' => array
				(
					'type' => 'bigint',
					'constraint' => 22,
					'unsigned' => true,
					'auto_increment' => true,
					'null' => false
				),
				'gallery_images' => array
				(
					'type' => (in_array($this->db->DBDriver, array('Postgre', 'SQLSRV')) ? 'text' : 'longtext'),
					'null' => false
				),
				'gallery_title' => array
				(
					'type' => 'varchar',
					'constraint' => 255,
					'null' => false
				),
				'gallery_slug' => array
				(
					'type' => 'varchar',
					'constraint' => 255,
					'null' => false
				),
				'gallery_description' => array
				(
					'type' => (in_array($this->db->DBDriver, array('Postgre', 'SQLSRV')) ? 'text' : 'longtext'),
					'null' => false
				),
				'gallery_attributes' => array
				(
					'type' => (in_array($this->db->DBDriver, array('Postgre', 'SQLSRV')) ? 'text' : 'longtext'),
					'null' => false
				),
				'gallery_tags' => array
				(
					'type' => (in_array($this->db->DBDriver, array('Postgre', 'SQLSRV')) ? 'text' : 'longtext'),
					'null' => false
				),
				'created_timestamp' => array
				(
					'type' => 'timestamp',
					'null' => false
				),
				'updated_timestamp' => array
				(
					'type' => 'timestamp',
					'null' => true
				),
				'featured' => array
				(
					'type' => 'tinyint',
					'constraint' => 1,
					'default' => '0',
					'null' => false
				),
				'author' => array
				(
					'type' => 'bigint',
					'constraint' => 22,
					'unsigned' => true,
					'null' => false
				),
				'status' => array
				(
					'type' => 'tinyint',
					'constraint' => 1,
					'default' => '0',
					'null' => false
				),
			)
		);
		$this->forge->addKey('gallery_id', true, true);
		$this->forge->addKey('gallery_slug', false, true);
		$this->forge->addKey('author', false, false);
		$this->forge->createTable('galleries');
		
		$this->forge->addField
		(
			array
			(
				'id' => array
				(
					'type' => 'bigint',
					'constraint' => 22,
					'unsigned' => true,
					'auto_increment' => true,
					'null' => false
				),
				'sender_full_name' => array
				(
					'type' => 'varchar',
					'constraint' => 64,
					'null' => false
				),
				'sender_email' => array
				(
					'type' => 'varchar',
					'constraint' => 255,
					'null' => false
				),
				'subject' => array
				(
					'type' => 'varchar',
					'constraint' => 255,
					'null' => false
				),
				'messages' => array
				(
					'type' => 'text',
					'null' => false
				),
				'timestamp' => array
				(
					'type' => 'timestamp',
					'null' => false
				),
			)
		);
		$this->forge->addKey('id', true, true);
		$this->forge->createTable('inquiries');
		
		$this->forge->addField
		(
			array
			(
				'user_id' => array
				(
					'type' => 'bigint',
					'constraint' => 22,
					'unsigned' => true,
					'auto_increment' => true,
					'null' => false
				),
				'service_provider' => array
				(
					'type' => 'varchar',
					'constraint' => 32,
					'null' => false
				),
				'access_token' => array
				(
					'type' => 'varchar',
					'constraint' => 255,
					'null' => false
				),
				'status' => array
				(
					'type' => 'tinyint',
					'constraint' => 1,
					'default' => '0',
					'null' => false
				),
			)
		);
		$this->forge->addKey('user_id', true, true);
		$this->forge->addKey('service_provider', false, false);
		$this->forge->createTable('oauth__login');
		
		$this->forge->addField
		(
			array
			(
				'page_id' => array
				(
					'type' => 'bigint',
					'constraint' => 22,
					'unsigned' => true,
					'auto_increment' => true,
					'null' => false
				),
				'page_title' => array
				(
					'type' => 'varchar',
					'constraint' => 255,
					'null' => false
				),
				'page_slug' => array
				(
					'type' => 'varchar',
					'constraint' => 255,
					'null' => false
				),
				'page_description' => array
				(
					'type' => 'text',
					'null' => false
				),
				'page_content' => array
				(
					'type' => (in_array($this->db->DBDriver, array('Postgre', 'SQLSRV')) ? 'text' : 'longtext'),
					'null' => false
				),
				'carousel_id' => array
				(
					'type' => 'int',
					'constraint' => 11,
					'unsigned' => true,
					'null' => false
				),
				'faq_id' => array
				(
					'type' => 'int',
					'constraint' => 11,
					'unsigned' => true,
					'null' => false
				),
				'created_timestamp' => array
				(
					'type' => 'timestamp',
					'null' => false
				),
				'updated_timestamp' => array
				(
					'type' => 'timestamp',
					'null' => true
				),
				'author' => array
				(
					'type' => 'bigint',
					'constraint' => 22,
					'unsigned' => true,
					'null' => false
				),
				'language_id' => array
				(
					'type' => 'int',
					'constraint' => 11,
					'unsigned' => true,
					'null' => false
				),
				'status' => array
				(
					'type' => 'tinyint',
					'constraint' => 1,
					'default' => '0',
					'null' => false
				),
			)
		);
		$this->forge->addKey('page_id', true, true);
		$this->forge->addKey('page_slug', false, true);
		$this->forge->addKey('author', false, false);
		$this->forge->addKey('language_id', false, false);
		$this->forge->createTable('pages');
		
		$this->forge->addField
		(
			array
			(
				'carousel_id' => array
				(
					'type' => 'int',
					'constraint' => 11,
					'unsigned' => true,
					'auto_increment' => true,
					'null' => false
				),
				'carousel_title' => array
				(
					'type' => 'varchar',
					'constraint' => 64,
					'null' => false
				),
				'carousel_description' => array
				(
					'type' => 'text',
					'null' => false
				),
				'carousel_content' => array
				(
					'type' => (in_array($this->db->DBDriver, array('Postgre', 'SQLSRV')) ? 'text' : 'longtext'),
					'null' => false
				),
				'created_timestamp' => array
				(
					'type' => 'timestamp',
					'null' => false
				),
				'updated_timestamp' => array
				(
					'type' => 'timestamp',
					'null' => true
				),
				'language_id' => array
				(
					'type' => 'int',
					'constraint' => 11,
					'unsigned' => true,
					'null' => false
				),
				'status' => array
				(
					'type' => 'tinyint',
					'constraint' => 1,
					'default' => '0',
					'null' => false
				),
			)
		);
		$this->forge->addKey('carousel_id', true, true);
		$this->forge->addKey('language_id', false, false);
		$this->forge->createTable('pages__carousels');
		
		$this->forge->addField
		(
			array
			(
				'faq_id' => array
				(
					'type' => 'int',
					'constraint' => 11,
					'unsigned' => true,
					'auto_increment' => true,
					'null' => false
				),
				'faq_title' => array
				(
					'type' => 'varchar',
					'constraint' => 64,
					'null' => false
				),
				'faq_description' => array
				(
					'type' => 'text',
					'null' => false
				),
				'faq_content' => array
				(
					'type' => (in_array($this->db->DBDriver, array('Postgre', 'SQLSRV')) ? 'text' : 'longtext'),
					'null' => false
				),
				'created_timestamp' => array
				(
					'type' => 'timestamp',
					'null' => false
				),
				'updated_timestamp' => array
				(
					'type' => 'timestamp',
					'null' => true
				),
				'language_id' => array
				(
					'type' => 'int',
					'constraint' => 11,
					'unsigned' => true,
					'null' => false
				),
				'status' => array
				(
					'type' => 'tinyint',
					'constraint' => 1,
					'default' => '0',
					'null' => false
				),
			)
		);
		$this->forge->addKey('faq_id', true, true);
		$this->forge->addKey('language_id', false, false);
		$this->forge->createTable('pages__faqs');
		
		$this->forge->addField
		(
			array
			(
				'people_id' => array
				(
					'type' => 'int',
					'constraint' => 11,
					'unsigned' => true,
					'auto_increment' => true,
					'null' => false
				),
				'first_name' => array
				(
					'type' => 'varchar',
					'constraint' => 32,
					'null' => false
				),
				'last_name' => array
				(
					'type' => 'varchar',
					'constraint' => 32,
					'null' => false
				),
				'people_slug' => array
				(
					'type' => 'varchar',
					'constraint' => 255,
					'null' => false
				),
				'position' => array
				(
					'type' => 'varchar',
					'constraint' => 255,
					'null' => false
				),
				'email' => array
				(
					'type' => 'varchar',
					'constraint' => 64,
					'null' => false
				),
				'mobile' => array
				(
					'type' => 'varchar',
					'constraint' => 16,
					'null' => false
				),
				'instagram' => array
				(
					'type' => 'varchar',
					'constraint' => 255,
					'null' => false
				),
				'facebook' => array
				(
					'type' => 'varchar',
					'constraint' => 64,
					'null' => false
				),
				'twitter' => array
				(
					'type' => 'varchar',
					'constraint' => 64,
					'null' => false
				),
				'biography' => array
				(
					'type' => 'text',
					'null' => false
				),
				'photo' => array
				(
					'type' => 'varchar',
					'constraint' => 255,
					'null' => false
				),
				'status' => array
				(
					'type' => 'tinyint',
					'constraint' => 1,
					'default' => '0',
					'null' => false
				),
			)
		);
		$this->forge->addKey('people_id', true, true);
		$this->forge->createTable('peoples');
		
		$this->forge->addField
		(
			array
			(
				'testimonial_id' => array
				(
					'type' => 'int',
					'constraint' => 11,
					'unsigned' => true,
					'auto_increment' => true,
					'null' => false
				),
				'photo' => array
				(
					'type' => 'varchar',
					'constraint' => 255,
					'null' => false
				),
				'first_name' => array
				(
					'type' => 'varchar',
					'constraint' => 32,
					'null' => false
				),
				'last_name' => array
				(
					'type' => 'varchar',
					'constraint' => 32,
					'null' => false
				),
				'testimonial_title' => array
				(
					'type' => 'varchar',
					'constraint' => 255,
					'null' => false
				),
				'testimonial_content' => array
				(
					'type' => 'text',
					'null' => false
				),
				'timestamp' => array
				(
					'type' => 'timestamp',
					'null' => false
				),
				'language_id' => array
				(
					'type' => 'int',
					'constraint' => 11,
					'unsigned' => true,
					'null' => false
				),
				'status' => array
				(
					'type' => 'tinyint',
					'constraint' => 1,
					'default' => '0',
					'null' => false
				),
			)
		);
		$this->forge->addKey('testimonial_id', true, true);
		$this->forge->addKey('language_id', false, false);
		$this->forge->createTable('testimonials');
		
		
		$this->db->table('app__settings')->insert(json_decode('{"id":"1","app_name":"' . htmlspecialchars(trim(session()->get('site_title'))) . '","app_description":"' . htmlspecialchars(trim(session()->get('site_description'))) . '","app_logo":"logo.png","app_icon":"logo.png","frontend_theme":"default","backend_theme":"backend","app_language":"' . htmlspecialchars(trim((session()->get('language') == 'id' ? 2 : 1))) . '","office_name":"Aksara Laboratory","office_phone":"+6281381614558","office_email":"info@example.com","office_fax":"","office_address":"2nd Floor Example Tower Building, Some Road Name, Any Region","office_map":"[]","one_device_login":"0","username_changes":"1","frontend_registration":"1","default_membership_group":"3","auto_active_registration":"1","google_analytics_key":"","openlayers_search_provider":"openlayers","openlayers_search_key":"","default_map_tile":"","disqus_site_domain":"","facebook_app_id":"","facebook_app_secret":"","google_client_id":"","google_client_secret":"","twitter_username":"","instagram_username":"","whatsapp_number":"","smtp_email_masking":"","smtp_sender_masking":"","smtp_host":"","smtp_port":"0","smtp_username":"","smtp_password":"","action_sound":"1"}', true));
		$this->db->table('app__groups')->insert(json_decode('{"group_id":"1","group_name":"Global Administrator","group_description":"Super User","group_privileges":"{\"addons\":[\"index\",\"detail\",\"install\"],\"addons\/ftp\":[\"index\"],\"addons\/modules\":[\"index\",\"detail\",\"import\",\"activate\",\"delete\"],\"addons\/themes\":[\"index\",\"detail\",\"customize\",\"import\",\"activate\",\"delete\"],\"administrative\":[\"index\"],\"administrative\/account\":[\"index\",\"update\"],\"administrative\/activities\":[\"index\",\"read\",\"truncate\",\"delete\",\"pdf\",\"print\"],\"administrative\/cleaner\":[\"index\",\"clean\"],\"administrative\/countries\":[\"index\",\"create\",\"read\",\"update\",\"delete\",\"export\",\"print\",\"pdf\"],\"administrative\/connections\":[\"index\",\"create\",\"read\",\"update\",\"delete\",\"export\",\"print\",\"pdf\"],\"administrative\/groups\":[\"index\",\"create\",\"read\",\"update\",\"delete\",\"export\",\"print\",\"pdf\"],\"administrative\/groups\/adjust_privileges\":[\"index\",\"create\",\"read\",\"update\",\"delete\",\"export\",\"print\",\"pdf\"],\"administrative\/groups\/privileges\":[\"index\",\"create\",\"read\",\"update\",\"delete\",\"export\",\"print\",\"pdf\"],\"administrative\/menus\":[\"index\",\"create\",\"read\",\"update\",\"delete\",\"export\",\"print\",\"pdf\"],\"administrative\/settings\":[\"index\",\"update\"],\"administrative\/translations\":[\"index\",\"create\",\"read\",\"update\",\"delete\",\"export\",\"print\",\"pdf\"],\"administrative\/translations\/synchronize\":[\"index\"],\"administrative\/translations\/translate\":[\"index\",\"delete_phrase\"],\"administrative\/updater\":[\"index\",\"update\"],\"administrative\/users\":[\"index\",\"create\",\"read\",\"update\",\"delete\",\"export\",\"print\",\"pdf\"],\"administrative\/users\/privileges\":[\"index\",\"update\"],\"administrative\/years\":[\"index\",\"create\",\"read\",\"update\",\"delete\",\"export\",\"print\",\"pdf\"],\"apis\":[\"index\"],\"apis\/debug_tool\":[\"index\"],\"apis\/documentation\":[\"index\"],\"apis\/services\":[\"index\",\"create\",\"read\",\"update\",\"delete\",\"export\",\"print\",\"pdf\"],\"cms\":[\"index\"],\"cms\/blogs\":[\"index\",\"create\",\"read\",\"update\",\"delete\",\"export\",\"print\",\"pdf\"],\"cms\/blogs\/categories\":[\"index\",\"create\",\"read\",\"update\",\"delete\",\"export\",\"print\",\"pdf\"],\"cms\/galleries\":[\"index\",\"create\",\"read\",\"update\",\"delete\",\"export\",\"print\",\"pdf\"],\"cms\/pages\":[\"index\",\"create\",\"read\",\"update\",\"delete\",\"export\",\"print\",\"pdf\"],\"cms\/partials\":[\"index\"],\"cms\/partials\/announcements\":[\"index\",\"create\",\"read\",\"update\",\"delete\",\"export\",\"print\",\"pdf\"],\"cms\/partials\/carousels\":[\"index\",\"create\",\"read\",\"update\",\"delete\",\"export\",\"print\",\"pdf\"],\"cms\/partials\/faqs\":[\"index\",\"create\",\"read\",\"update\",\"delete\",\"export\",\"print\",\"pdf\"],\"cms\/partials\/inquiries\":[\"index\",\"read\",\"delete\",\"export\",\"print\",\"pdf\"],\"cms\/partials\/media\":[\"index\"],\"cms\/partials\/testimonials\":[\"index\",\"create\",\"read\",\"update\",\"delete\",\"export\",\"print\",\"pdf\"],\"cms\/peoples\":[\"index\",\"create\",\"read\",\"update\",\"delete\",\"export\",\"print\",\"pdf\"],\"dashboard\":[\"index\"]}","status":"1"}', true));
		$this->db->table('app__groups')->insert(json_decode('{"group_id":"2","group_name":"Technical","group_description":"Group user for technical support","group_privileges":"{\"administrative\":[\"index\"],\"administrative\/account\":[\"index\",\"update\"],\"apis\":[\"index\"],\"apis\/debug_tool\":[\"index\"],\"apis\/services\":[\"index\",\"create\",\"read\",\"update\",\"delete\",\"export\",\"print\",\"pdf\"],\"cms\":[\"index\"],\"cms\/blogs\":[\"index\",\"create\",\"read\",\"update\",\"delete\",\"export\",\"print\",\"pdf\"],\"cms\/blogs\/categories\":[\"index\",\"create\",\"read\",\"update\",\"delete\",\"export\",\"print\",\"pdf\"],\"cms\/galleries\":[\"index\",\"create\",\"read\",\"update\",\"delete\",\"export\",\"print\",\"pdf\"],\"cms\/pages\":[\"index\",\"create\",\"read\",\"update\",\"delete\",\"export\",\"print\",\"pdf\"],\"cms\/partials\":[\"index\"],\"cms\/partials\/announcements\":[\"index\",\"create\",\"read\",\"update\",\"delete\",\"export\",\"print\",\"pdf\"],\"cms\/partials\/carousels\":[\"index\",\"create\",\"read\",\"update\",\"delete\",\"export\",\"print\",\"pdf\"],\"cms\/partials\/faqs\":[\"index\",\"create\",\"read\",\"update\",\"delete\",\"export\",\"print\",\"pdf\"],\"cms\/partials\/inquiries\":[\"index\",\"read\",\"delete\",\"export\",\"print\",\"pdf\"],\"cms\/partials\/media\":[\"index\"],\"cms\/partials\/testimonial\":[\"index\",\"create\",\"read\",\"update\",\"delete\",\"export\",\"print\",\"pdf\"],\"cms\/peoples\":[\"index\",\"create\",\"read\",\"update\",\"delete\",\"export\",\"print\",\"pdf\"],\"dashboard\":[\"index\"]}","status":"1"}', true));
		$this->db->table('app__groups')->insert(json_decode('{"group_id":"3","group_name":"Subscriber","group_description":"Group user for subscriber","group_privileges":"{\"administrative\":[\"index\"],\"administrative\/account\":[\"index\",\"update\"],\"dashboard\":[\"index\"]}","status":"1"}', true));
		$this->db->table('app__groups_privileges')->insert(json_decode('{"path":"addons","privileges":"[\"index\",\"detail\",\"install\"]","last_generated":"' . date('Y-m-d H:i:s') . '"}', true));
		$this->db->table('app__groups_privileges')->insert(json_decode('{"path":"addons\/ftp","privileges":"[\"index\"]","last_generated":"' . date('Y-m-d H:i:s') . '"}', true));
		$this->db->table('app__groups_privileges')->insert(json_decode('{"path":"addons\/modules","privileges":"[\"index\",\"detail\",\"import\",\"delete\"]","last_generated":"' . date('Y-m-d H:i:s') . '"}', true));
		$this->db->table('app__groups_privileges')->insert(json_decode('{"path":"addons\/themes","privileges":"[\"index\",\"detail\",\"activate\",\"customize\",\"import\",\"delete\"]","last_generated":"' . date('Y-m-d H:i:s') . '"}', true));
		$this->db->table('app__groups_privileges')->insert(json_decode('{"path":"administrative","privileges":"[\"index\"]","last_generated":"' . date('Y-m-d H:i:s') . '"}', true));
		$this->db->table('app__groups_privileges')->insert(json_decode('{"path":"administrative\/account","privileges":"[\"index\",\"update\"]","last_generated":"' . date('Y-m-d H:i:s') . '"}', true));
		$this->db->table('app__groups_privileges')->insert(json_decode('{"path":"administrative\/activities","privileges":"[\"index\",\"read\",\"truncate\",\"delete\",\"pdf\",\"print\"]","last_generated":"' . date('Y-m-d H:i:s') . '"}', true));
		$this->db->table('app__groups_privileges')->insert(json_decode('{"path":"administrative\/cleaner","privileges":"[\"index\",\"clean\"]","last_generated":"' . date('Y-m-d H:i:s') . '"}', true));
		$this->db->table('app__groups_privileges')->insert(json_decode('{"path":"administrative\/connections","privileges":"[\"index\",\"create\",\"read\",\"update\",\"delete\",\"export\",\"print\",\"pdf\"]","last_generated":"' . date('Y-m-d H:i:s') . '"}', true));
		$this->db->table('app__groups_privileges')->insert(json_decode('{"path":"administrative\/countries","privileges":"[\"index\",\"create\",\"read\",\"update\",\"delete\",\"export\",\"print\",\"pdf\"]","last_generated":"' . date('Y-m-d H:i:s') . '"}', true));
		$this->db->table('app__groups_privileges')->insert(json_decode('{"path":"administrative\/groups","privileges":"[\"index\",\"create\",\"read\",\"update\",\"delete\",\"export\",\"print\",\"pdf\"]","last_generated":"' . date('Y-m-d H:i:s') . '"}', true));
		$this->db->table('app__groups_privileges')->insert(json_decode('{"path":"administrative\/groups\/adjust_privileges","privileges":"[\"index\",\"create\",\"read\",\"update\",\"delete\",\"export\",\"print\",\"pdf\"]","last_generated":"' . date('Y-m-d H:i:s') . '"}', true));
		$this->db->table('app__groups_privileges')->insert(json_decode('{"path":"administrative\/groups\/privileges","privileges":"[\"index\",\"create\",\"update\",\"read\",\"delete\"]","last_generated":"' . date('Y-m-d H:i:s') . '"}', true));
		$this->db->table('app__groups_privileges')->insert(json_decode('{"path":"administrative\/menus","privileges":"[\"index\",\"create\",\"read\",\"update\",\"delete\",\"export\",\"print\",\"pdf\"]","last_generated":"' . date('Y-m-d H:i:s') . '"}', true));
		$this->db->table('app__groups_privileges')->insert(json_decode('{"path":"administrative\/settings","privileges":"[\"index\",\"update\"]","last_generated":"' . date('Y-m-d H:i:s') . '"}', true));
		$this->db->table('app__groups_privileges')->insert(json_decode('{"path":"administrative\/translations","privileges":"[\"index\",\"create\",\"read\",\"update\",\"delete\",\"export\",\"print\",\"pdf\"]","last_generated":"' . date('Y-m-d H:i:s') . '"}', true));
		$this->db->table('app__groups_privileges')->insert(json_decode('{"path":"administrative\/translations\/synchronize","privileges":"[\"index\"]","last_generated":"' . date('Y-m-d H:i:s') . '"}', true));
		$this->db->table('app__groups_privileges')->insert(json_decode('{"path":"administrative\/translations\/translate","privileges":"[\"index\",\"delete_phrase\"]","last_generated":"' . date('Y-m-d H:i:s') . '"}', true));
		$this->db->table('app__groups_privileges')->insert(json_decode('{"path":"administrative\/updater","privileges":"[\"index\",\"update\"]","last_generated":"' . date('Y-m-d H:i:s') . '"}', true));
		$this->db->table('app__groups_privileges')->insert(json_decode('{"path":"administrative\/users","privileges":"[\"index\",\"create\",\"read\",\"update\",\"delete\",\"export\",\"print\",\"pdf\"]","last_generated":"' . date('Y-m-d H:i:s') . '"}', true));
		$this->db->table('app__groups_privileges')->insert(json_decode('{"path":"administrative\/users\/privileges","privileges":"[\"index\",\"update\"]","last_generated":"' . date('Y-m-d H:i:s') . '"}', true));
		$this->db->table('app__groups_privileges')->insert(json_decode('{"path":"administrative\/years","privileges":"[\"index\",\"create\",\"read\",\"update\",\"delete\",\"export\",\"print\",\"pdf\"]","last_generated":"' . date('Y-m-d H:i:s') . '"}', true));
		$this->db->table('app__groups_privileges')->insert(json_decode('{"path":"apis","privileges":"[\"index\"]","last_generated":"' . date('Y-m-d H:i:s') . '"}', true));
		$this->db->table('app__groups_privileges')->insert(json_decode('{"path":"apis\/debug_tool","privileges":"[\"index\"]","last_generated":"' . date('Y-m-d H:i:s') . '"}', true));
		$this->db->table('app__groups_privileges')->insert(json_decode('{"path":"apis\/documentation","privileges":"[\"index\"]","last_generated":"' . date('Y-m-d H:i:s') . '"}', true));
		$this->db->table('app__groups_privileges')->insert(json_decode('{"path":"apis\/services","privileges":"[\"index\",\"create\",\"read\",\"update\",\"delete\",\"export\",\"print\",\"pdf\"]","last_generated":"' . date('Y-m-d H:i:s') . '"}', true));
		$this->db->table('app__groups_privileges')->insert(json_decode('{"path":"cms","privileges":"[\"index\"]","last_generated":"' . date('Y-m-d H:i:s') . '"}', true));
		$this->db->table('app__groups_privileges')->insert(json_decode('{"path":"cms\/blogs","privileges":"[\"index\",\"create\",\"read\",\"update\",\"delete\",\"export\",\"print\",\"pdf\"]","last_generated":"' . date('Y-m-d H:i:s') . '"}', true));
		$this->db->table('app__groups_privileges')->insert(json_decode('{"path":"cms\/blogs\/categories","privileges":"[\"index\",\"create\",\"read\",\"update\",\"delete\",\"export\",\"print\",\"pdf\"]","last_generated":"' . date('Y-m-d H:i:s') . '"}', true));
		$this->db->table('app__groups_privileges')->insert(json_decode('{"path":"cms\/galleries","privileges":"[\"index\",\"create\",\"read\",\"update\",\"delete\",\"export\",\"print\",\"pdf\"]","last_generated":"' . date('Y-m-d H:i:s') . '"}', true));
		$this->db->table('app__groups_privileges')->insert(json_decode('{"path":"cms\/pages","privileges":"[\"index\",\"create\",\"read\",\"update\",\"delete\",\"export\",\"print\",\"pdf\"]","last_generated":"' . date('Y-m-d H:i:s') . '"}', true));
		$this->db->table('app__groups_privileges')->insert(json_decode('{"path":"cms\/partials","privileges":"[\"index\"]","last_generated":"' . date('Y-m-d H:i:s') . '"}', true));
		$this->db->table('app__groups_privileges')->insert(json_decode('{"path":"cms\/partials\/announcements","privileges":"[\"index\",\"create\",\"read\",\"update\",\"delete\",\"export\",\"print\",\"pdf\"]","last_generated":"' . date('Y-m-d H:i:s') . '"}', true));
		$this->db->table('app__groups_privileges')->insert(json_decode('{"path":"cms\/partials\/carousels","privileges":"[\"index\",\"create\",\"read\",\"update\",\"delete\",\"export\",\"print\",\"pdf\"]","last_generated":"' . date('Y-m-d H:i:s') . '"}', true));
		$this->db->table('app__groups_privileges')->insert(json_decode('{"path":"cms\/partials\/faqs","privileges":"[\"index\",\"create\",\"read\",\"update\",\"delete\",\"export\",\"print\",\"pdf\"]","last_generated":"' . date('Y-m-d H:i:s') . '"}', true));
		$this->db->table('app__groups_privileges')->insert(json_decode('{"path":"cms\/partials\/inquiries","privileges":"[\"index\",\"read\",\"delete\",\"export\",\"print\",\"pdf\"]","last_generated":"' . date('Y-m-d H:i:s') . '"}', true));
		$this->db->table('app__groups_privileges')->insert(json_decode('{"path":"cms\/partials\/media","privileges":"[\"index\"]","last_generated":"' . date('Y-m-d H:i:s') . '"}', true));
		$this->db->table('app__groups_privileges')->insert(json_decode('{"path":"cms\/partials\/testimonials","privileges":"[\"index\",\"create\",\"read\",\"update\",\"delete\",\"export\",\"print\",\"pdf\"]","last_generated":"' . date('Y-m-d H:i:s') . '"}', true));
		$this->db->table('app__groups_privileges')->insert(json_decode('{"path":"cms\/peoples","privileges":"[\"index\",\"create\",\"read\",\"update\",\"delete\",\"export\",\"print\",\"pdf\"]","last_generated":"' . date('Y-m-d H:i:s') . '"}', true));
		$this->db->table('app__groups_privileges')->insert(json_decode('{"path":"dashboard","privileges":"[\"index\"]","last_generated":"' . date('Y-m-d H:i:s') . '"}', true));
		$this->db->table('app__languages')->insert(json_decode('{"id":"1","language":"Default (English)","description":"Default language","code":"en","locale":"en-US,en_US,en_US.UTF8,en-us,en,english","status":"1"}', true));
		$this->db->table('app__languages')->insert(json_decode('{"id":"2","language":"Bahasa Indonesia","description":"Terjemahan bahasa Indonesia","code":"id","locale":"id-ID,id_ID,id_ID.UTF8,id-id,id,indonesian","status":"1"}', true));
		$this->db->table('app__menus')->insert(json_decode('{"menu_id":"1","menu_placement":"header","menu_label":"Header Menu","menu_description":"Menu for navigation header (front end)","serialized_data":"[{\"order\":0,\"children\":[]},{\"id\":\"1\",\"icon\":\"mdi mdi-home\",\"label\":\"Home\",\"slug\":\"home\",\"newtab\":\"0\",\"order\":1,\"children\":[]},{\"id\":\"3\",\"icon\":\"mdi mdi-newspaper\",\"label\":\"News\",\"slug\":\"blogs\",\"newtab\":\"0\",\"order\":3,\"children\":[]},{\"id\":\"4\",\"icon\":\"mdi mdi-map-clock-outline\",\"label\":\"Galleries\",\"slug\":\"galleries\",\"newtab\":\"0\",\"order\":4,\"children\":[]}]","group_id":"0","status":"1"}', true));
		$this->db->table('app__users')->insert(json_decode('{"user_id":"1","email":"' . session()->get('email') . '","password":"' . password_hash(session()->get('password') . session()->get('encryption'), PASSWORD_DEFAULT) . '","username":"' . session()->get('username') . '","first_name":"' . session()->get('first_name') . '","last_name":"' . session()->get('last_name') . '","gender":"0","bio":"","photo":"","address":"","phone":"","postal_code":"","language_id":"' . (session()->get('language') == 'id' ? 2 : 1) . '","country_id":"0","group_id":"1","registered_date":"' . date('Y-m-d') . '","last_login":"' . date('Y-m-d H:i:s') . '","status":"1"}', true));
		
		if(1 == session()->get('installation_mode')) // indicates the sample data were requested
		{
			$this->db->table('app__countries')->insert(json_decode('{"id":"1","code":"AF","country":"Afghanistan","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"2","code":"AL","country":"Albania","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"3","code":"DZ","country":"Algeria","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"4","code":"AS","country":"American Samoa","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"5","code":"AD","country":"Andorra","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"6","code":"AO","country":"Angola","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"7","code":"AI","country":"Anguilla","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"8","code":"AQ","country":"Antarctica","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"9","code":"AG","country":"Antigua and Barbuda","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"10","code":"AR","country":"Argentina","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"11","code":"AM","country":"Armenia","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"12","code":"AW","country":"Aruba","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"13","code":"AU","country":"Australia","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"14","code":"AT","country":"Austria","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"15","code":"AZ","country":"Azerbaijan","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"16","code":"BS","country":"Bahamas","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"17","code":"BH","country":"Bahrain","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"18","code":"BD","country":"Bangladesh","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"19","code":"BB","country":"Barbados","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"20","code":"BY","country":"Belarus","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"21","code":"BE","country":"Belgium","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"22","code":"BZ","country":"Belize","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"23","code":"BJ","country":"Benin","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"24","code":"BM","country":"Bermuda","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"25","code":"BT","country":"Bhutan","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"26","code":"BO","country":"Bolivia","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"27","code":"BA","country":"Bosnia and Herzegovina","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"28","code":"BW","country":"Botswana","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"29","code":"BV","country":"Bouvet Island","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"30","code":"BR","country":"Brazil","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"31","code":"IO","country":"British Indian Ocean Territory","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"32","code":"BN","country":"Brunei Darussalam","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"33","code":"BG","country":"Bulgaria","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"34","code":"BF","country":"Burkina Faso","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"35","code":"BI","country":"Burundi","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"36","code":"KH","country":"Cambodia","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"37","code":"CM","country":"Cameroon","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"38","code":"CA","country":"Canada","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"39","code":"CV","country":"Cape Verde","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"40","code":"KY","country":"Cayman Islands","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"41","code":"CF","country":"Central African Republic","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"42","code":"TD","country":"Chad","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"43","code":"CL","country":"Chile","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"44","code":"CN","country":"China","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"45","code":"CX","country":"Christmas Island","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"46","code":"CC","country":"Cocos (Keeling) Islands","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"47","code":"CO","country":"Colombia","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"48","code":"KM","country":"Comoros","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"49","code":"CG","country":"Congo","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"50","code":"CD","country":"Congo, the Democratic Republic o","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"51","code":"CK","country":"Cook Islands","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"52","code":"CR","country":"Costa Rica","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"53","code":"CI","country":"Cote D\u0027Ivoire","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"54","code":"HR","country":"Croatia","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"55","code":"CU","country":"Cuba","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"56","code":"CY","country":"Cyprus","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"57","code":"CZ","country":"Czech Republic","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"58","code":"DK","country":"Denmark","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"59","code":"DJ","country":"Djibouti","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"60","code":"DM","country":"Dominica","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"61","code":"DO","country":"Dominican Republic","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"62","code":"EC","country":"Ecuador","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"63","code":"EG","country":"Egypt","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"64","code":"SV","country":"El Salvador","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"65","code":"GQ","country":"Equatorial Guinea","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"66","code":"ER","country":"Eritrea","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"67","code":"EE","country":"Estonia","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"68","code":"ET","country":"Ethiopia","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"69","code":"FK","country":"Falkland Islands (Malvinas)","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"70","code":"FO","country":"Faroe Islands","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"71","code":"FJ","country":"Fiji","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"72","code":"FI","country":"Finland","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"73","code":"FR","country":"France","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"74","code":"GF","country":"French Guiana","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"75","code":"PF","country":"French Polynesia","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"76","code":"TF","country":"French Southern Territories","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"77","code":"GA","country":"Gabon","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"78","code":"GM","country":"Gambia","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"79","code":"GE","country":"Georgia","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"80","code":"DE","country":"Germany","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"81","code":"GH","country":"Ghana","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"82","code":"GI","country":"Gibraltar","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"83","code":"GR","country":"Greece","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"84","code":"GL","country":"Greenland","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"85","code":"GD","country":"Grenada","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"86","code":"GP","country":"Guadeloupe","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"87","code":"GU","country":"Guam","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"88","code":"GT","country":"Guatemala","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"89","code":"GN","country":"Guinea","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"90","code":"GW","country":"Guinea-Bissau","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"91","code":"GY","country":"Guyana","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"92","code":"HT","country":"Haiti","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"93","code":"HM","country":"Heard Island and Mcdonald Island","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"94","code":"VA","country":"Holy See (Vatican City State)","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"95","code":"HN","country":"Honduras","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"96","code":"HK","country":"Hong Kong","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"97","code":"HU","country":"Hungary","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"98","code":"IS","country":"Iceland","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"99","code":"IN","country":"India","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"100","code":"ID","country":"Indonesia","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"101","code":"IR","country":"Iran, Islamic Republic of","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"102","code":"IQ","country":"Iraq","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"103","code":"IE","country":"Ireland","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"104","code":"IL","country":"Israel","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"105","code":"IT","country":"Italy","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"106","code":"JM","country":"Jamaica","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"107","code":"JP","country":"Japan","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"108","code":"JO","country":"Jordan","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"109","code":"KZ","country":"Kazakhstan","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"110","code":"KE","country":"Kenya","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"111","code":"KI","country":"Kiribati","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"112","code":"KP","country":"Korea, Democratic People\u0027s Repub","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"113","code":"KR","country":"Korea, Republic of","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"114","code":"KW","country":"Kuwait","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"115","code":"KG","country":"Kyrgyzstan","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"116","code":"LA","country":"Lao People\u0027s Democratic Republic","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"117","code":"LV","country":"Latvia","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"118","code":"LB","country":"Lebanon","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"119","code":"LS","country":"Lesotho","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"120","code":"LR","country":"Liberia","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"121","code":"LY","country":"Libyan Arab Jamahiriya","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"122","code":"LI","country":"Liechtenstein","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"123","code":"LT","country":"Lithuania","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"124","code":"LU","country":"Luxembourg","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"125","code":"MO","country":"Macao","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"126","code":"MK","country":"Macedonia, the Former Yugoslav R","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"127","code":"MG","country":"Madagascar","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"128","code":"MW","country":"Malawi","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"129","code":"MY","country":"Malaysia","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"130","code":"MV","country":"Maldives","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"131","code":"ML","country":"Mali","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"132","code":"MT","country":"Malta","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"133","code":"MH","country":"Marshall Islands","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"134","code":"MQ","country":"Martinique","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"135","code":"MR","country":"Mauritania","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"136","code":"MU","country":"Mauritius","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"137","code":"YT","country":"Mayotte","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"138","code":"MX","country":"Mexico","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"139","code":"FM","country":"Micronesia, Federated States of","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"140","code":"MD","country":"Moldova, Republic of","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"141","code":"MC","country":"Monaco","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"142","code":"MN","country":"Mongolia","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"143","code":"MS","country":"Montserrat","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"144","code":"MA","country":"Morocco","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"145","code":"MZ","country":"Mozambique","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"146","code":"MM","country":"Myanmar","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"147","code":"NA","country":"Namibia","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"148","code":"NR","country":"Nauru","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"149","code":"NP","country":"Nepal","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"150","code":"NL","country":"Netherlands","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"151","code":"AN","country":"Netherlands Antilles","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"152","code":"NC","country":"New Caledonia","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"153","code":"NZ","country":"New Zealand","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"154","code":"NI","country":"Nicaragua","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"155","code":"NE","country":"Niger","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"156","code":"NG","country":"Nigeria","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"157","code":"NU","country":"Niue","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"158","code":"NF","country":"Norfolk Island","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"159","code":"MP","country":"Northern Mariana Islands","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"160","code":"NO","country":"Norway","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"161","code":"OM","country":"Oman","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"162","code":"PK","country":"Pakistan","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"163","code":"PW","country":"Palau","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"164","code":"PS","country":"Palestinian Territory, Occupied","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"165","code":"PA","country":"Panama","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"166","code":"PG","country":"Papua New Guinea","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"167","code":"PY","country":"Paraguay","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"168","code":"PE","country":"Peru","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"169","code":"PH","country":"Philippines","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"170","code":"PN","country":"Pitcairn","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"171","code":"PL","country":"Poland","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"172","code":"PT","country":"Portugal","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"173","code":"PR","country":"Puerto Rico","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"174","code":"QA","country":"Qatar","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"175","code":"RE","country":"Reunion","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"176","code":"RO","country":"Romania","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"177","code":"RU","country":"Russian Federation","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"178","code":"RW","country":"Rwanda","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"179","code":"SH","country":"Saint Helena","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"180","code":"KN","country":"Saint Kitts and Nevis","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"181","code":"LC","country":"Saint Lucia","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"182","code":"PM","country":"Saint Pierre and Miquelon","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"183","code":"VC","country":"Saint Vincent and the Grenadines","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"184","code":"WS","country":"Samoa","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"185","code":"SM","country":"San Marino","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"186","code":"ST","country":"Sao Tome and Principe","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"187","code":"SA","country":"Saudi Arabia","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"188","code":"SN","country":"Senegal","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"189","code":"CS","country":"Serbia and Montenegro","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"190","code":"SC","country":"Seychelles","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"191","code":"SL","country":"Sierra Leone","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"192","code":"SG","country":"Singapore","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"193","code":"SK","country":"Slovakia","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"194","code":"SI","country":"Slovenia","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"195","code":"SB","country":"Solomon Islands","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"196","code":"SO","country":"Somalia","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"197","code":"ZA","country":"South Africa","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"198","code":"GS","country":"South Georgia and the South Sand","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"199","code":"ES","country":"Spain","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"200","code":"LK","country":"Sri Lanka","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"201","code":"SD","country":"Sudan","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"202","code":"SR","country":"Suriname","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"203","code":"SJ","country":"Svalbard and Jan Mayen","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"204","code":"SZ","country":"Swaziland","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"205","code":"SE","country":"Sweden","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"206","code":"CH","country":"Switzerland","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"207","code":"SY","country":"Syrian Arab Republic","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"208","code":"TW","country":"Taiwan, Province of China","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"209","code":"TJ","country":"Tajikistan","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"210","code":"TZ","country":"Tanzania, United Republic of","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"211","code":"TH","country":"Thailand","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"212","code":"TL","country":"Timor-Leste","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"213","code":"TG","country":"Togo","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"214","code":"TK","country":"Tokelau","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"215","code":"TO","country":"Tonga","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"216","code":"TT","country":"Trinidad and Tobago","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"217","code":"TN","country":"Tunisia","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"218","code":"TR","country":"Turkey","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"219","code":"TM","country":"Turkmenistan","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"220","code":"TC","country":"Turks and Caicos Islands","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"221","code":"TV","country":"Tuvalu","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"222","code":"UG","country":"Uganda","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"223","code":"UA","country":"Ukraine","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"224","code":"AE","country":"United Arab Emirates","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"225","code":"GB","country":"United Kingdom","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"226","code":"US","country":"United States","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"227","code":"UM","country":"United States Minor Outlying Isl","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"228","code":"UY","country":"Uruguay","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"229","code":"UZ","country":"Uzbekistan","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"230","code":"VU","country":"Vanuatu","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"231","code":"VE","country":"Venezuela","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"232","code":"VN","country":"Viet Nam","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"233","code":"VG","country":"Virgin Islands, British","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"234","code":"VI","country":"Virgin Islands, U.s.","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"235","code":"WF","country":"Wallis and Futuna","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"236","code":"EH","country":"Western Sahara","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"237","code":"YE","country":"Yemen","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"238","code":"ZM","country":"Zambia","status":"1"}', true));
			$this->db->table('app__countries')->insert(json_decode('{"id":"239","code":"ZW","country":"Zimbabwe","status":"1"}', true));
			$this->db->table('blogs')->insert(json_decode('{"post_id":"1","post_title":"Hello World!","post_slug":"hello-world","post_excerpt":"A \"Hello, World!\" program generally is a computer program that outputs or displays the message \"Hello, World!\". Such a program is very simple in most programming languages, and is often used to illustrate the basic syntax of a programming language.","post_content":"<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.<\/p>","post_category":"1","post_tags":"lipsum","created_timestamp":"' . date('Y-m-d H:i:s') . '","updated_timestamp":"' . date('Y-m-d H:i:s') . '","author":"1","headline":"1","featured_image":"placeholder.png","language_id":"1","status":"1"}', true));
			$this->db->table('blogs')->insert(json_decode('{"post_id":"2","post_title":"Biggest Football Stadium","post_slug":"biggest-football-stadium","post_excerpt":"Fusce blandit nibh lorem, vitae condimentum turpis porttitor non. Donec in pharetra massa, eget interdum nulla. Maecenas molestie condimentum nisi ac malesuada. Curabitur placerat felis nibh, non fringilla arcu ullamcorper non.","post_content":"<p>Proin eget mauris malesuada, pharetra ante ac, aliquet lorem. Nam id sollicitudin ipsum, vitae volutpat quam. Ut luctus lacus sem, eget aliquam eros dictum vitae. Nunc tristique orci nec nulla eleifend cursus. Cras rhoncus, nunc sit amet hendrerit mollis, erat libero dignissim metus, quis gravida ex diam sed enim. Mauris quis dui est. Praesent blandit iaculis feugiat. Suspendisse a congue felis, ullamcorper convallis sapien. Fusce vel convallis ligula. Nam rutrum lacus enim, quis venenatis turpis suscipit in. Quisque semper dignissim risus, vitae ornare tortor tempus non. Fusce sed elementum ipsum.<\/p><p>Fusce blandit nibh lorem, vitae condimentum turpis porttitor non. Donec in pharetra massa, eget interdum nulla. Maecenas molestie condimentum nisi ac malesuada. Curabitur placerat felis nibh, non fringilla arcu ullamcorper non. Maecenas interdum tortor odio, ut lobortis nisl faucibus ut. Nam sed tellus nisl. Mauris sed mi dui. Cras volutpat tincidunt ante, ac aliquet eros imperdiet eu. Fusce aliquam quis risus ut facilisis. Integer porta arcu a fringilla vulputate.<\/p><p>Sed eget pharetra urna, vel vulputate quam. Nam at sem vitae sem consectetur blandit. In in sagittis massa. Nulla tempor consequat ultrices. Etiam sagittis porttitor erat in consequat. Aenean ac enim erat. Maecenas aliquam augue eget aliquet consectetur. Sed ultrices, nunc non vehicula mattis, odio massa blandit purus, nec auctor turpis justo dignissim risus. Fusce tempus tortor ut erat rutrum, sit amet luctus augue bibendum. Aenean quis lacus nibh. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas.<\/p>","post_category":"1","post_tags":"footbal","created_timestamp":"' . date('Y-m-d H:i:s') . '","updated_timestamp":"' . date('Y-m-d H:i:s') . '","author":"1","headline":"1","featured_image":"placeholder.png","language_id":"1","status":"1"}', true));
			$this->db->table('blogs')->insert(json_decode('{"post_id":"3","post_title":"Facebook Launches Its New Application","post_slug":"facebook-launches-its-new-application","post_excerpt":"Etiam posuere congue massa ut consequat. Vivamus suscipit quis lacus vel tincidunt. Sed vestibulum ligula vel mauris mollis, nec facilisis lacus egestas. Pellentesque pulvinar risus sed urna aliquet, a dapibus ante ultricies. Aliquam dui justo, rhoncus in tempor vel, hendrerit interdum dolor. Morbi hendrerit ante odio, a bibendum eros mattis eu. Suspendisse pellentesque finibus vehicula.","post_content":"<p>Etiam posuere congue massa ut consequat. Vivamus suscipit quis lacus vel tincidunt. Sed vestibulum ligula vel mauris mollis, nec facilisis lacus egestas. Pellentesque pulvinar risus sed urna aliquet, a dapibus ante ultricies. Aliquam dui justo, rhoncus in tempor vel, hendrerit interdum dolor. Morbi hendrerit ante odio, a bibendum eros mattis eu. Suspendisse pellentesque finibus vehicula.<\/p><p>Etiam ut varius ante. Mauris vulputate tincidunt metus, scelerisque tincidunt augue dictum viverra. Ut condimentum fringilla scelerisque. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Aliquam ultricies tortor erat, sed molestie dolor ullamcorper aliquam. Sed consequat pulvinar felis. Curabitur finibus est gravida ullamcorper semper. Vivamus vel quam quis purus auctor mollis sed tristique ipsum. Phasellus efficitur, felis ac iaculis ullamcorper, nunc odio blandit nunc, ac pellentesque nisl tellus sit amet eros. Duis a lectus vitae purus tristique laoreet.<\/p>","post_category":"1","post_tags":"facebook","created_timestamp":"' . date('Y-m-d H:i:s') . '","updated_timestamp":"' . date('Y-m-d H:i:s') . '","author":"1","headline":"1","featured_image":"placeholder.png","language_id":"1","status":"1"}', true));
			$this->db->table('blogs')->insert(json_decode('{"post_id":"4","post_title":"Is Coffee Good For Health?","post_slug":"is-coffee-good-for-health-","post_excerpt":"Fusce eget vulputate felis. Cras eleifend tempor purus quis semper. Nulla ac nisi sit amet justo scelerisque varius aliquet ut tortor. Donec commodo mauris a lorem blandit, non bibendum est rutrum. Cras eu pretium ante.","post_content":"<p>Ut vel sapien nunc. Nam vehicula sodales dolor vel venenatis. Quisque mattis scelerisque odio a vestibulum. Fusce eget vulputate felis. Cras eleifend tempor purus quis semper. Nulla ac nisi sit amet justo scelerisque varius aliquet ut tortor. Donec commodo mauris a lorem blandit, non bibendum est rutrum. Cras eu pretium ante. Integer commodo libero non risus luctus, ullamcorper rutrum neque commodo. Nam pulvinar ante sit amet mauris posuere, eu vehicula ipsum pretium. Phasellus quis lectus eu lacus semper consectetur. Integer quis ornare mi, sit amet venenatis orci.<\/p><p>Aenean rhoncus erat eu ex venenatis aliquet. Mauris in nisl eu odio condimentum luctus at eu metus. Curabitur orci eros, molestie quis pulvinar vitae, dictum eu tellus. Donec eu urna in nisi ultricies efficitur. Sed vel turpis rhoncus, porttitor dui vitae, mattis quam. Donec pretium quam molestie, condimentum nunc in, luctus nibh. Praesent ornare velit at ex volutpat auctor. Nunc blandit viverra massa sit amet faucibus. Pellentesque rhoncus quam ullamcorper placerat imperdiet. Morbi ac tortor non leo fermentum suscipit. Donec in ipsum vel ligula posuere tincidunt non vitae urna. Sed et sapien in augue aliquet molestie. Nulla massa felis, rhoncus in lectus ac, facilisis facilisis turpis.<\/p>","post_category":"1","post_tags":"coffee","created_timestamp":"' . date('Y-m-d H:i:s') . '","updated_timestamp":"' . date('Y-m-d H:i:s') . '","author":"1","headline":"1","featured_image":"placeholder.png","language_id":"1","status":"1"}', true));
			$this->db->table('blogs')->insert(json_decode('{"post_id":"5","post_title":"Is Tea Hampers Health?","post_slug":"is-tea-hampers-health-","post_excerpt":"Phasellus condimentum vehicula massa, id tristique sem accumsan ut. Cras vel convallis est, vestibulum dignissim felis. Donec sit amet volutpat quam. Proin nec libero facilisis, finibus odio eu, euismod nisl. Quisque urna turpis, congue eget vehicula sollicitudin, dapibus nec purus. Nulla iaculis nisl quis metus porttitor iaculis. Phasellus aliquet eleifend ex sit amet dignissim.","post_content":"<p>Quisque fringilla elit vitae urna malesuada, at luctus massa rhoncus. Donec luctus quam leo, pretium tristique lectus maximus tristique. In libero nisi, accumsan et ipsum laoreet, malesuada dignissim purus. Pellentesque ut ullamcorper augue. Suspendisse potenti. Aenean leo nulla, mollis id feugiat ac, vehicula ut odio. Vivamus dapibus dictum eros eget dignissim. Phasellus condimentum vehicula massa, id tristique sem accumsan ut. Cras vel convallis est, vestibulum dignissim felis. Donec sit amet volutpat quam. Proin nec libero facilisis, finibus odio eu, euismod nisl. Quisque urna turpis, congue eget vehicula sollicitudin, dapibus nec purus. Nulla iaculis nisl quis metus porttitor iaculis. Phasellus aliquet eleifend ex sit amet dignissim.<\/p><p>Etiam et consequat eros. Sed at sodales turpis, ac pellentesque augue. Donec et rutrum turpis, nec pharetra ligula. Nullam est turpis, placerat sit amet lorem in, posuere blandit nibh. Vivamus sodales tortor in scelerisque vestibulum. Maecenas ultricies dui sit amet leo tincidunt dictum. Nunc auctor commodo sem eget tincidunt. Cras mattis mauris vel massa vestibulum rutrum.<\/p>","post_category":"2","post_tags":"tea","created_timestamp":"' . date('Y-m-d H:i:s') . '","updated_timestamp":"' . date('Y-m-d H:i:s') . '","author":"1","headline":"1","featured_image":"placeholder.png","language_id":"1","status":"1"}', true));
			$this->db->table('blogs')->insert(json_decode('{"post_id":"6","post_title":"Beverage Store In Kamaladi","post_slug":"beverage-store-in-kamaladi","post_excerpt":"Maecenas vulputate turpis ac ex varius, faucibus tincidunt turpis convallis. Phasellus ut mauris nulla. Praesent luctus tellus erat, a imperdiet mauris imperdiet quis. Praesent at auctor nisl, non tempus nisi. Aliquam semper eleifend lacus at porttitor. Mauris maximus dui vitae euismod suscipit. Sed pellentesque luctus suscipit. Cras id malesuada lectus.","post_content":"<p>Maecenas vulputate turpis ac ex varius, faucibus tincidunt turpis convallis. Phasellus ut mauris nulla. Praesent luctus tellus erat, a imperdiet mauris imperdiet quis. Praesent at auctor nisl, non tempus nisi. Aliquam semper eleifend lacus at porttitor. Mauris maximus dui vitae euismod suscipit. Sed pellentesque luctus suscipit. Cras id malesuada lectus.<\/p><p>Praesent egestas tincidunt sapien, sit amet fringilla lacus condimentum nec. Praesent pulvinar arcu elit. Mauris nibh ipsum, luctus porttitor dui eu, consectetur pellentesque enim. Integer tincidunt malesuada risus, in mattis mauris pretium at. Vivamus ac ligula a tortor sodales maximus eu ut dolor. Morbi cursus a erat quis sodales. Duis id lorem quis leo hendrerit ultrices. Phasellus dignissim fringilla felis, sed luctus libero elementum a. Aliquam in neque nisi.<\/p><p>Nulla in sapien libero. Aliquam vitae justo quis turpis feugiat sollicitudin et in nulla. Nullam porta pharetra tortor, eu dignissim erat pretium porttitor. Proin in fringilla lectus. Phasellus ut mauris eu lectus auctor luctus ut eget justo. Mauris consectetur lorem enim, sit amet ullamcorper purus venenatis quis. Etiam malesuada justo sit amet augue gravida, non feugiat nibh interdum. Phasellus eu velit libero. Phasellus in lorem arcu. Cras vitae feugiat nisl, et accumsan metus. Pellentesque lacinia pretium ex. Morbi vitae consequat quam, eu ultricies augue. Curabitur rhoncus in est vitae laoreet. Donec sollicitudin sollicitudin lectus at dapibus. Maecenas a gravida est.<\/p>","post_category":"2","post_tags":"kamaladi","created_timestamp":"' . date('Y-m-d H:i:s') . '","updated_timestamp":"' . date('Y-m-d H:i:s') . '","author":"1","headline":"1","featured_image":"placeholder.png","language_id":"1","status":"1"}', true));
			$this->db->table('blogs')->insert(json_decode('{"post_id":"7","post_title":"Weather Forecast Of Kathmandu","post_slug":"weather-forecast-of-kathmandu","post_excerpt":"Duis lobortis leo eu leo varius auctor. Donec luctus lobortis finibus. Vivamus porttitor non turpis eu faucibus. Etiam fringilla accumsan justo sed convallis. Duis ornare feugiat neque, sit amet facilisis lectus rutrum eu.","post_content":"<p>Sed interdum dapibus metus, a gravida mauris sodales iaculis. Aliquam erat volutpat. Proin euismod sit amet tellus ac varius. Vestibulum eu neque dolor. Donec id varius velit. Aliquam non tortor non ex pulvinar vulputate. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque luctus odio id nisi tristique luctus.<\/p><p>Duis lobortis leo eu leo varius auctor. Donec luctus lobortis finibus. Vivamus porttitor non turpis eu faucibus. Etiam fringilla accumsan justo sed convallis. Duis ornare feugiat neque, sit amet facilisis lectus rutrum eu. Vestibulum commodo ex massa, eu tempus libero vestibulum gravida. Aenean et ligula leo. Integer in lacus in mi vehicula interdum hendrerit quis massa. Morbi interdum volutpat urna, eu efficitur ligula mollis id. Nullam ac massa non nulla interdum varius ac nec ligula. Maecenas vel placerat enim. Donec lacinia justo sed ipsum rutrum rutrum quis ac massa. Mauris a vestibulum arcu, et lobortis purus.<\/p>","post_category":"2","post_tags":"weather, katmandu","created_timestamp":"' . date('Y-m-d H:i:s') . '","updated_timestamp":"' . date('Y-m-d H:i:s') . '","author":"1","headline":"1","featured_image":"placeholder.png","language_id":"1","status":"1"}', true));
			$this->db->table('blogs')->insert(json_decode('{"post_id":"8","post_title":"Mobile Takes Jobs Of Many Devices","post_slug":"mobile-takes-jobs-of-many-devices","post_excerpt":"Curabitur tempor, eros ut commodo tincidunt, augue erat mattis enim, sit amet tincidunt nunc purus ut risus. In euismod velit eu ex auctor, nec tempus orci pretium. Etiam sed nisl sed nisi dapibus rutrum sit amet vitae enim.","post_content":"<p>Mauris malesuada, ipsum in tempus vehicula, augue turpis porttitor ligula, sed ultricies nisi massa auctor lectus. Proin placerat mollis nulla vel euismod. Morbi fringilla blandit varius. Pellentesque interdum ipsum risus, vitae cursus enim interdum in. Curabitur tempor, eros ut commodo tincidunt, augue erat mattis enim, sit amet tincidunt nunc purus ut risus. In euismod velit eu ex auctor, nec tempus orci pretium. Etiam sed nisl sed nisi dapibus rutrum sit amet vitae enim.<\/p><p>Sed massa mi, hendrerit ut tellus pharetra, aliquam hendrerit ante. Sed sodales sodales neque, id volutpat nibh cursus eu. Quisque ex velit, porttitor et fringilla gravida, porttitor aliquet risus. Donec placerat neque consequat libero bibendum volutpat. Fusce ut mattis ligula. Ut sodales at erat et gravida. Etiam id auctor massa. Quisque ornare ligula eu ipsum interdum interdum. Nulla facilisi. Vivamus augue elit, egestas quis nisl sit amet, feugiat eleifend elit. Nam non lorem quam.<\/p>","post_category":"2","post_tags":"mobile, jobs","created_timestamp":"' . date('Y-m-d H:i:s') . '","updated_timestamp":"' . date('Y-m-d H:i:s') . '","author":"1","headline":"1","featured_image":"placeholder.png","language_id":"1","status":"1"}', true));
			$this->db->table('blogs')->insert(json_decode('{"post_id":"9","post_title":"Best Youngster In Football: Henry","post_slug":"best-youngster-in-football-henry","post_excerpt":"Ut scelerisque mattis fringilla. Nam gravida ligula molestie tortor hendrerit, id interdum nisi aliquet. In ut nisi in ligula sollicitudin tincidunt nec sed nibh. Sed et tincidunt nulla. Sed non massa lectus. Phasellus luctus placerat elit, vitae ultrices quam dapibus tempor.","post_content":"<p>Phasellus feugiat dictum urna gravida bibendum. Donec tempus arcu quis magna interdum, et pellentesque neque malesuada. Cras nulla ipsum, malesuada ac tincidunt eu, laoreet eu tellus. Curabitur ut ultrices enim. Duis nisl velit, vestibulum vel vestibulum vitae, finibus id metus. In pharetra purus at dolor posuere blandit et at nulla. Aliquam nisi dolor, mattis et gravida sed, dapibus quis lacus. Sed placerat ligula luctus ligula tincidunt euismod. Vivamus euismod pharetra ligula vitae ultricies. Aenean rhoncus metus tempor, pulvinar risus non, elementum risus. Ut scelerisque mattis fringilla. Nam gravida ligula molestie tortor hendrerit, id interdum nisi aliquet. In ut nisi in ligula sollicitudin tincidunt nec sed nibh. Sed et tincidunt nulla. Sed non massa lectus. Phasellus luctus placerat elit, vitae ultrices quam dapibus tempor.<\/p><p>Proin dignissim enim mauris, fermentum fermentum eros luctus et. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Integer in auctor nisl, eget feugiat lectus. Vestibulum non enim ornare, congue erat vitae, fringilla mauris. Maecenas at velit sed nisl dapibus bibendum. Ut malesuada dapibus leo at rhoncus. Duis suscipit dolor non arcu imperdiet, sed sagittis dolor suscipit. Vestibulum sollicitudin sit amet nibh dapibus sollicitudin. Vivamus dapibus vehicula lectus, sit amet consectetur enim condimentum quis.<\/p>","post_category":"3","post_tags":"football, henry","created_timestamp":"' . date('Y-m-d H:i:s') . '","updated_timestamp":"' . date('Y-m-d H:i:s') . '","author":"1","headline":"1","featured_image":"placeholder.png","language_id":"1","status":"1"}', true));
			$this->db->table('blogs')->insert(json_decode('{"post_id":"10","post_title":"Tennis Court Established In Montania","post_slug":"tennis-court-established-in-montania","post_excerpt":"Etiam commodo rutrum tristique. Quisque vehicula, libero sed mattis euismod, velit sem porta arcu, imperdiet luctus ante sapien consequat mi.","post_content":"<p>Maecenas et nulla nibh. Curabitur non sem venenatis, laoreet nulla sit amet, placerat nunc. Maecenas rhoncus libero sed orci ornare, non vestibulum orci tincidunt. Mauris et bibendum neque, eget fringilla sapien. Duis id efficitur nulla. Morbi elementum non massa quis cursus. Ut viverra pharetra odio non malesuada. Etiam commodo rutrum tristique. Quisque vehicula, libero sed mattis euismod, velit sem porta arcu, imperdiet luctus ante sapien consequat mi. Vestibulum consequat, nisl at scelerisque tincidunt, sapien mauris pretium nunc, at egestas massa ligula at quam. Pellentesque ex erat, congue sit amet ex a, laoreet pharetra felis. Quisque ac libero ornare, mattis elit sit amet, imperdiet ipsum. Duis ut lacus justo.<\/p><p>Vestibulum eget nunc sit amet ligula aliquam lobortis id in nunc. Etiam dui nibh, dictum id lacinia eu, lacinia vel erat. In a molestie nunc, vitae commodo nisl. Phasellus ultrices semper nulla, scelerisque consectetur sapien vulputate id. Etiam in sapien scelerisque, efficitur justo vel, ultrices lacus. Nunc justo enim, aliquet sollicitudin laoreet a, ultricies sit amet sem. Mauris consequat rhoncus lacus, quis posuere quam pellentesque vel. Vestibulum lobortis dapibus neque vitae semper. Cras congue sed ex at ultricies. In et aliquam mauris, nec gravida orci.<\/p>","post_category":"3","post_tags":"tennis, montania","created_timestamp":"' . date('Y-m-d H:i:s') . '","updated_timestamp":"' . date('Y-m-d H:i:s') . '","author":"1","headline":"1","featured_image":"placeholder.png","language_id":"1","status":"1"}', true));
			$this->db->table('blogs')->insert(json_decode('{"post_id":"11","post_title":"Apple, Best For Health","post_slug":"apple-best-for-health","post_excerpt":"Sed egestas lectus erat, ac fermentum sem laoreet vel. Nunc luctus hendrerit tincidunt. Donec tempus finibus elit, at hendrerit sem pretium eu. Donec id porta dui. Quisque at mi consectetur, dignissim quam in, congue justo. Quisque mollis fringilla velit, eu dapibus tortor fermentum sed. Ut facilisis id ligula sit amet tincidunt. Ut posuere purus lorem, nec ullamcorper sem rhoncus sed.","post_content":"<p>Etiam erat quam, ultrices id quam ut, consequat rhoncus velit. Curabitur eget lorem facilisis sem fermentum porttitor. In elementum mauris a convallis molestie. Vivamus pretium placerat semper. Proin at tellus rutrum, dictum massa vitae, facilisis diam. Etiam eu risus mauris. Vivamus vitae pulvinar ipsum. Nunc vel faucibus urna, sed pellentesque nulla. Nullam at sollicitudin urna, quis ultrices lectus. Sed pharetra vulputate dolor, eget molestie diam. Nam cursus vehicula mollis. Integer blandit augue tincidunt libero viverra feugiat.<\/p><p>Sed egestas lectus erat, ac fermentum sem laoreet vel. Nunc luctus hendrerit tincidunt. Donec tempus finibus elit, at hendrerit sem pretium eu. Donec id porta dui. Quisque at mi consectetur, dignissim quam in, congue justo. Quisque mollis fringilla velit, eu dapibus tortor fermentum sed. Ut facilisis id ligula sit amet tincidunt. Ut posuere purus lorem, nec ullamcorper sem rhoncus sed.<\/p><p>Praesent at gravida nisl, et sodales ipsum. Pellentesque pulvinar viverra metus ut luctus. Phasellus tortor mauris, finibus in vehicula at, sodales id nibh. Nullam feugiat auctor dolor, ut luctus nunc pharetra sit amet. Sed fringilla pellentesque lacinia. Fusce tempor ante eget mi tempor fringilla. Donec viverra urna tellus. Interdum et malesuada fames ac ante ipsum primis in faucibus. Lorem ipsum dolor sit amet, consectetur adipiscing elit.<\/p>","post_category":"3","post_tags":"apple","created_timestamp":"' . date('Y-m-d H:i:s') . '","updated_timestamp":"' . date('Y-m-d H:i:s') . '","author":"1","headline":"1","featured_image":"placeholder.png","language_id":"1","status":"1"}', true));
			$this->db->table('blogs')->insert(json_decode('{"post_id":"12","post_title":"Research On AIDS Complete","post_slug":"research-on-aids-complete","post_excerpt":"Donec porttitor tempus sem nec commodo. Maecenas varius, diam eu gravida malesuada, tortor nisi sodales mauris, eu ornare neque augue ac tellus. Phasellus eu ante consectetur, elementum felis in, iaculis nisi.","post_content":"<p>Phasellus elementum, magna eget volutpat semper, sapien orci condimentum neque, eu faucibus massa nibh at erat. Pellentesque bibendum tempus velit sagittis dapibus. Praesent non dui non dui sollicitudin tincidunt. Morbi mauris urna, egestas auctor sem nec, scelerisque condimentum nulla. Quisque sollicitudin felis nibh, at fringilla velit luctus non. Nullam dapibus ultricies ipsum, quis cursus lorem viverra rhoncus. Donec porttitor tempus sem nec commodo. Maecenas varius, diam eu gravida malesuada, tortor nisi sodales mauris, eu ornare neque augue ac tellus. Phasellus eu ante consectetur, elementum felis in, iaculis nisi. Curabitur ullamcorper blandit metus, at vulputate turpis molestie eget. Maecenas ut consectetur libero, sed finibus turpis. In eget interdum ligula. Donec ac lacus et felis semper suscipit. Sed urna risus, scelerisque quis consequat gravida, molestie et ligula. Sed lectus felis, laoreet vitae aliquam et, accumsan ac magna. Nulla iaculis porttitor mollis.<\/p><p>Integer lorem nulla, suscipit viverra pulvinar vitae, maximus in metus. Morbi blandit dui eget sollicitudin elementum. Morbi fermentum vulputate massa sed mattis. Mauris vel ex aliquam mauris luctus bibendum at eu sapien. Nulla facilisi. Nunc dapibus tristique gravida. Suspendisse sit amet tortor dolor. Donec aliquam magna eu orci vulputate, eget porttitor urna volutpat.<\/p>","post_category":"3","post_tags":"aids, research","created_timestamp":"' . date('Y-m-d H:i:s') . '","updated_timestamp":"' . date('Y-m-d H:i:s') . '","author":"1","headline":"1","featured_image":"placeholder.png","language_id":"1","status":"1"}', true));
			$this->db->table('blogs')->insert(json_decode('{"post_id":"13","post_title":"Mea eu error philosophia, te pro minim novum salutandi, mea case eirmod consequat ne!","post_slug":"mea-eu-error-philosophia-te-pro-minim-novum-salutandi-mea-case-eirmod-consequat-ne-","post_excerpt":"Ad sea sale iriure voluptua, ex perfecto dissentias sit, id per posse conceptam honestatis! Mea te partem platonem quaerendum, ut repudiare intellegam his, ad accumsan luptatum per? Assum latine repudiare id vel. Mea eu error philosophia, te pro minim novum salutandi, mea case eirmod consequat ne! Etiam aliquip ut est. Agam mucius invidunt te nam, ei usu illud laboramus. Nam cu dicat suscipit scribentur, mel no discere eligendi.","post_content":"<p>Tibique erroribus dissentiunt ad vix, ne debet hendrerit per, te erant altera sea. Equidem vivendo propriae sed ei, et insolens liberavisse interpretaris vix, mei ei unum possit. Has id ubique mediocritatem, suscipit accusamus eam ne, et per delectus gloriatur! Officiis hendrerit cum te. Mel eu sumo vidit prodesset! Vis an legimus fastidii, eum ex sale maiestatis democritum, appetere detraxit in eum.<\/p><p>Ad sea sale iriure voluptua, ex perfecto dissentias sit, id per posse conceptam honestatis! Mea te partem platonem quaerendum, ut repudiare intellegam his, ad accumsan luptatum per? Assum latine repudiare id vel. Mea eu error philosophia, te pro minim novum salutandi, mea case eirmod consequat ne! Etiam aliquip ut est. Agam mucius invidunt te nam, ei usu illud laboramus. Nam cu dicat suscipit scribentur, mel no discere eligendi.<\/p><p>Animal indoctum eum ea. Cu duo unum invidunt accusamus, his solum congue delectus id. Fierent fastidii sea cu, omnes facete postulant ne eos! Labitur omnesque mel eu, sea reque laboramus cu, sed elit repudiare ea. Usu imperdiet voluptatibus id.<\/p><p>Ei nobis pericula scripserit eum. Veritus antiopam adipiscing ex mea, velit ignota eum in? Nec volumus oporteat necessitatibus ea, et cum nemore animal, an usu clita equidem ceteros? Id error persius facilisi mel! Id imperdiet vulputate definitiones nec.<\/p><p>Ex vocibus detracto scaevola mea! In mea debitis noluisse, velit tibique tractatos vis te! Quo no vidit utroque menandri. Cu pri nonumes tractatos, duo no commodo diceret feugait, duo ex iusto dissentias?<\/p><p>Dicat postea partiendo ei nec. No numquam pertinacia vim. No vel quis amet perfecto. Unum complectitur ne eum! Aeque aeterno et mea, te probo definiebas sea, erant aliquid ex mei. Vide reque nemore et qui, prima civibus denique ea vim, usu esse quodsi inciderint an.<\/p><p>Mea duis erant indoctum ei. Ex vix nonumy noster essent, te deleniti molestie torquatos duo. His ea eligendi definiebas posidonium, quis doctus maiestatis pro in. Fabulas aliquando posidonium vim ut.<\/p><p>Inani postea cetero cum ea. Id vix natum tincidunt, his malis fabulas ea, quo in scripta nonumes salutatus. Id tacimates liberavisse contentiones quo! Pro nominati maluisset dissentiunt ut, esse alienum est ex? Oporteat salutandi interpretaris mea ut.<\/p><p>In viderer antiopam evertitur ius, pro esse quando meliore in, eum meis error consequat ad! Eu vix eros omittantur, vix id mutat laboramus definitionem. Vim ne iisque veritus, cu cetero posidonium nec! Eu dico platonem est, mea nemore voluptua ad.<\/p><p>Ipsum oratio ei vim? Qui cu solum intellegam, ad wisi maiorum commune vim, duis diceret usu ex. Per solet salutatus no! Cu affert eirmod ius.<\/p>","post_category":"4","post_tags":"lipsum","created_timestamp":"' . date('Y-m-d H:i:s') . '","updated_timestamp":"' . date('Y-m-d H:i:s') . '","author":"1","headline":"1","featured_image":"placeholder.png","language_id":"1","status":"1"}', true));
			$this->db->table('blogs')->insert(json_decode('{"post_id":"14","post_title":"Eum munere oblique omittantur ne, cum eu cetero vidisse oporteat, etiam elitr mediocrem an sea.","post_slug":"eum-munere-oblique-omittantur-ne-cum-eu-cetero-vidisse-oporteat-etiam-elitr-mediocrem-an-sea-","post_excerpt":"Vis essent accusam ut, sonet facilisi usu ne. Tibique expetenda neglegentur in mea. Munere mentitum signiferumque in pri, nam aperiri sapientem definitionem at, eam discere comprehensam ut? Ex impedit nostrum sit, at pri deleniti legendos theophrastus, cum cu nusquam voluptua principes. Te altera noster conceptam est. Ne eam paulo phaedrum consulatu. Vel te discere civibus, vel eligendi petentium ut, qui aliquip legimus no.","post_content":"<p>Eu has bonorum conceptam, pri adipisci definiebas et, at vix hinc magna forensibus. Ne nec appareat laboramus, pri ne minim labore electram! An per laudem mucius fastidii, eu solum dicta aperiri sit! Eum munere oblique omittantur ne, cum eu cetero vidisse oporteat, etiam elitr mediocrem an sea. Usu ne affert tamquam, eleifend moderatius intellegebat ius no. Quando scaevola lobortis ad pri, at nec meis augue omnes.<\/p><p>Commodo postulant contentiones est ea. Eirmod accusamus iracundia mea cu, qui equidem fabellas laboramus ea. Id mea sint qualisque, quo at legimus molestiae, vel ei maiorum explicari pertinacia. Et duo sint dictas tacimates, vocibus electram eu mel, odio nostrud officiis ea mei. Et eros debitis moderatius eum. An pro labore invidunt.<\/p><p>Sed probo quando partiendo id, vim aliquid ancillae accommodare in, cum ut epicuri democritum? Nec natum accusam ea, eu est nostro scripta, has et agam facete dolorum? No qui tantas aperiam fabellas, mel nominavi recteque at. Paulo dicam temporibus qui ea, at nibh deleniti sit. Ea sonet aliquid recusabo mei, te consul vituperata eos? Ex erant discere eos, tollit legere tincidunt pri no.<\/p><p>Vis essent accusam ut, sonet facilisi usu ne. Tibique expetenda neglegentur in mea. Munere mentitum signiferumque in pri, nam aperiri sapientem definitionem at, eam discere comprehensam ut? Ex impedit nostrum sit, at pri deleniti legendos theophrastus, cum cu nusquam voluptua principes. Te altera noster conceptam est. Ne eam paulo phaedrum consulatu. Vel te discere civibus, vel eligendi petentium ut, qui aliquip legimus no.<\/p><p>Ea atqui aliquip vix, laudem scribentur vel in. Vix an vidisse dolorum efficiantur, minim tritani ne quo, delenit fabellas expetendis id cum. Per at natum aperiri necessitatibus, at eam nibh inani admodum! Est nusquam accommodare ad? No hinc autem idque eum, in inani dissentiunt mel, te cum eirmod eleifend. Qui commodo dolores lucilius ne, te pri iisque numquam, in aliquip invenire nec.<\/p>","post_category":"4","post_tags":"lipsum","created_timestamp":"' . date('Y-m-d H:i:s') . '","updated_timestamp":"' . date('Y-m-d H:i:s') . '","author":"1","headline":"1","featured_image":"placeholder.png","language_id":"1","status":"1"}', true));
			$this->db->table('blogs')->insert(json_decode('{"post_id":"15","post_title":"Facilisis efficiendi consectetuer te mea, in vim moderatius sadipscing.","post_slug":"facilisis-efficiendi-consectetuer-te-mea-in-vim-moderatius-sadipscing-","post_excerpt":"Ad habeo facer vis, nec soluta utamur dissentiet in. Vis ut discere vivendo lucilius. Wisi consequuntur ei has, ea duo quot justo viderer. Ius in cibo discere, mea ex facete tractatos, eos viris euripidis ea.","post_content":"<p>Solet euripidis reprimique ex vel, ex qui antiopam consectetuer, eos ei minim error utamur. Eu cum partem latine, sit te quem albucius, an mel simul sententiae. In pri nulla mediocrem? Has sanctus electram in, justo semper legendos cu qui. Sea ea habeo ludus pertinacia, sit ad duis feugiat qualisque, ut facilisis periculis similique pri.<\/p><p>Ad habeo facer vis, nec soluta utamur dissentiet in. Vis ut discere vivendo lucilius. Wisi consequuntur ei has, ea duo quot justo viderer. Ius in cibo discere, mea ex facete tractatos, eos viris euripidis ea.<\/p><p>Mel at impedit corpora, ne mei unum copiosae eleifend. Graeco fierent in vix, eligendi voluptua te pri? At stet doming imperdiet duo, ne docendi posidonium usu? Nam eu justo delenit, ei nec ferri laoreet adolescens? Per an facer possim convenire. Et vix invidunt quaestio, ut ius case etiam!<\/p><p>Facilisis efficiendi consectetuer te mea, in vim moderatius sadipscing. Prima modus fuisset vix ne, ei eam errem utinam iisque! Et cum tollit eleifend incorrupte, sea in nostro voluptatum, consul scripta at vim. Duo ea purto congue oporteat, et vidit saepe prodesset eam, an pri brute autem viderer. Delenit nonumes ea cum, tota tantas tincidunt nec te! Has cu laoreet detracto platonem.<\/p><p>Eirmod labores salutatus in quo, nam eu ignota nostro albucius. Soleat corrumpit has in! Nisl sint quas eos et, harum interesset per et! Has saepe nonumy soleat ne. Graece facilisis quo ex. Te viris partem nam! Vel hinc praesent an, te sale augue sea.<\/p>","post_category":"4","post_tags":"lipsum","created_timestamp":"' . date('Y-m-d H:i:s') . '","updated_timestamp":"' . date('Y-m-d H:i:s') . '","author":"1","headline":"1","featured_image":"placeholder.png","language_id":"1","status":"1"}', true));
			$this->db->table('blogs')->insert(json_decode('{"post_id":"16","post_title":"Alterum saperet imperdiet pro at, at quo postulant disputationi","post_slug":"alterum-saperet-imperdiet-pro-at-at-quo-postulant-disputationi","post_excerpt":"Sententiae posidonium sadipscing mel id. Nam verear adipiscing ea, sea cu feugiat molestie iracundia. Expetenda intellegat usu cu, ornatus epicuri duo ei. Vix te justo verterem. At dicunt delicata his, ne persius senserit ius, primis appetere voluptaria duo at.","post_content":"<p>At mei dicta salutandi, viderer commune intellegat no has, ea mea facer quodsi mentitum? Alii contentiones in sit. Oratio latine pertinax vim id, in modo graeci vim. Vix impedit maluisset accusamus an, quas magna efficiendi ad his. Hinc possim has ut, pri tota debet maiestatis cu!<\/p><p>Alterum saperet imperdiet pro at, at quo postulant disputationi. No dolore vulputate nam, facer disputando cu vix, id democritum deseruisse disputationi eos. Sed at libris electram, pri noluisse antiopam delicatissimi et. Qui te amet accumsan, legere vocibus feugait eam et?<\/p><p>Cu elitr aeterno instructior sea, ne eum ocurreret urbanitas ullamcorper? Sea inani temporibus eu, sea ut quot delenit! Mel cu inani ceteros. Id mei tollit accusata, eos ea utamur signiferumque? Mel te amet facete tacimates, velit meliore quo in? Dicit homero concludaturque vix no!<\/p><p>Sententiae posidonium sadipscing mel id. Nam verear adipiscing ea, sea cu feugiat molestie iracundia. Expetenda intellegat usu cu, ornatus epicuri duo ei. Vix te justo verterem. At dicunt delicata his, ne persius senserit ius, primis appetere voluptaria duo at.<\/p><p>Dicit tollit denique et per, ludus albucius et per, sed an modo lorem atqui. Ut sea wisi eius! Ex mei dicta sonet iriure. Ius brute nemore eloquentiam ad, ne insolens hendrerit eos, ne dolorum mediocrem sed! In sit novum instructior, id pro populo adversarium?<\/p>","post_category":"4","post_tags":"lipsum","created_timestamp":"' . date('Y-m-d H:i:s') . '","updated_timestamp":"' . date('Y-m-d H:i:s') . '","author":"1","headline":"1","featured_image":"placeholder.png","language_id":"1","status":"1"}', true));
			$this->db->table('blogs__categories')->insert(json_decode('{"category_id":"1","category_title":"Uncategorized","category_slug":"uncategorized","category_description":"Uncategorized category","category_image":"placeholder.png","language_id":"1","status":"1"}', true));
			$this->db->table('blogs__categories')->insert(json_decode('{"category_id":"2","category_title":"Sport","category_slug":"sport","category_description":"Sport includes all forms of competitive physical activity or games which, through casual or organized participation, at least in part aim to use, maintain or improve physical ability and skills while providing enjoyment to participants, and in some cases, entertainment for spectators.","category_image":"placeholder.png","language_id":"1","status":"1"}', true));
			$this->db->table('blogs__categories')->insert(json_decode('{"category_id":"3","category_title":"Technology","category_slug":"technology","category_description":"Technology (\"science of craft\", from Greek \u03c4\u03ad\u03c7\u03bd\u03b7, techne, \"art, skill, cunning of hand\"; and -\u03bb\u03bf\u03b3\u03af\u03b1, -logia[2]) is the sum of techniques, skills, methods, and processes used in the production of goods or services or in the accomplishment of objectives, such as scientific investigation.","category_image":"placeholder.png","language_id":"1","status":"1"}', true));
			$this->db->table('blogs__categories')->insert(json_decode('{"category_id":"4","category_title":"Food","category_slug":"food","category_description":"Food is any substance[1] consumed to provide nutritional support for an organism. Food is usually of plant, animal or fungal in origin, and contains essential nutrients, such as carbohydrates, fats, proteins, vitamins, or minerals.","category_image":"placeholder.png","language_id":"1","status":"1"}', true));
			$this->db->table('galleries')->insert(json_decode('{"gallery_id":"1","gallery_images":"{\"placeholder.png\":\"Example\"}","gallery_title":"Gallery Sample","gallery_slug":"gallery-sample","gallery_description":"<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.<\/p>","gallery_attributes":"[{\"label\":\"Location\",\"value\":\"Office\"},{\"label\":\"Time\",\"value\":\"December 11st 2020\"},{\"label\":\"Camera\",\"value\":\"Canon EOS M10\"}]","gallery_tags":"example, lorem ipsum","created_timestamp":"' . date('Y-m-d H:i:s') . '","updated_timestamp":"' . date('Y-m-d H:i:s') . '","featured":"1","author":"1","status":"1"}', true));
			$this->db->table('galleries')->insert(json_decode('{"gallery_id":"2","gallery_images":"{\"160-800x400.jpg\":\"160-800x400.jpg\",\"placeholder.png\":\"placeholder.png\"}","gallery_title":"Gallery Sample 2","gallery_slug":"gallery-sample-2","gallery_description":"<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.<\/p>","gallery_attributes":"[{\"label\":\"Location\",\"value\":\"Office\"},{\"label\":\"Time\",\"value\":\"February 13rd 2020\"},{\"label\":\"Camera\",\"value\":\"Canon EOS M10\"}]","gallery_tags":"example, lorem ipsum","created_timestamp":"' . date('Y-m-d H:i:s') . '","updated_timestamp":"' . date('Y-m-d H:i:s') . '","featured":"1","author":"1","status":"1"}', true));
			$this->db->table('galleries')->insert(json_decode('{"gallery_id":"3","gallery_images":"{\"160-800x400.jpg\":\"160-800x400.jpg\",\"placeholder.png\":\"placeholder.png\"}","gallery_title":"Gallery Sample 3","gallery_slug":"gallery-sample-3","gallery_description":"<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.<\/p>","gallery_attributes":"[{\"label\":\"Location\",\"value\":\"Office\"},{\"label\":\"Time\",\"value\":\"February 13rd 2020\"},{\"label\":\"Camera\",\"value\":\"Canon EOS M10\"}]","gallery_tags":"example, lorem ipsum","created_timestamp":"' . date('Y-m-d H:i:s') . '","updated_timestamp":"' . date('Y-m-d H:i:s') . '","featured":"1","author":"1","status":"1"}', true));
			$this->db->table('pages__carousels')->insert(json_decode('{"carousel_id":"1","carousel_title":"Sample of Carousel","carousel_description":"This is the sample of carousel that can be include to page","carousel_content":"[{\"title\":\"Slide one of carousel\",\"description\":\"A carousel (American English: from French carrousel and Italian carosello), roundabout (British English), or merry-go-round, is a type of amusement ride consisting of a rotating circular platform with seats for riders. The &quot;seats&quot; are traditionally in the form of rows of wooden horses or other animals mounted on posts, many of which are moved up and down by gears to simulate galloping, to the accompaniment of looped circus music.\",\"link\":\"pages\\\/about\",\"label\":\"Read More\",\"background\":\"placeholder.png\",\"thumbnail\":\"\"},{\"title\":\"Slide two of carousel (without button link)\",\"description\":\"Carousels are commonly populated with horses, each horse weighing roughly 100 lbs (45 kg), but may include a variety of mounts, for example pigs, zebras, tigers, or mythological creatures such as dragons or unicorns. Sometimes, chair-like or bench-like seats are used, and occasionally mounts can be shaped like aeroplanes or cars.\",\"link\":\"\",\"label\":\"\",\"background\":\"placeholder.png\",\"thumbnail\":\"\"}]","created_timestamp":"' . date('Y-m-d H:i:s') . '","updated_timestamp":"' . date('Y-m-d H:i:s') . '","language_id":"1","status":"1"}', true));
			$this->db->table('pages__faqs')->insert(json_decode('{"faq_id":"1","faq_title":"Sample of FAQ","faq_description":"This is the sample of FAQ that can be include to the page","faq_content":"[{\"question\":\"What is FAQ?\",\"answer\":\"A FAQ is a list of frequently asked questions (FAQs) and answers on a particular topic (also known as Questions and Answers [Q&amp;A] or Frequently Answered Questions). The format is often used in articles, websites, email lists, and online forums where common questions tend to recur, for example through posts or queries by new users related to common knowledge gaps. The purpose of an FAQ is generally to provide information on frequent questions or concerns; however, the format is a useful means of organizing information, and text consisting of questions and their answers may thus be called an FAQ regardless of whether the questions are actually frequently asked.\"},{\"question\":\"Which is origins of FAQ?\",\"answer\":\"While the name may be recent, the FAQ format itself is quite old. For example, Matthew Hopkins wrote The Discovery of Witches in 1647 as a list of questions and answers, introduced as &quot;Certain Queries answered&quot;. Many old catechisms are in a question-and-answer (Q&amp;A) format. Summa Theologica, written by Thomas Aquinas in the second half of the 13th century, is a series of common questions about Christianity to which he wrote a series of replies. Plato\u0027s dialogues are even older.\"}]","created_timestamp":"' . date('Y-m-d H:i:s') . '","updated_timestamp":"' . date('Y-m-d H:i:s') . '","language_id":"1","status":"1"}', true));
			$this->db->table('pages')->insert(json_decode('{"page_id":"1","page_title":"Sample Page","page_slug":"sample-page","page_description":"This is just sample page","page_content":"<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Aliquet porttitor lacus luctus accumsan tortor posuere. Amet commodo nulla facilisi nullam vehicula ipsum. Egestas dui id ornare arcu odio. Viverra adipiscing at in tellus integer feugiat scelerisque. Proin nibh nisl condimentum id venenatis a condimentum. Ac placerat vestibulum lectus mauris ultrices. Non consectetur a erat nam at. Massa placerat duis ultricies lacus. In tellus integer feugiat scelerisque varius.<\/p>","carousel_id":"1","faq_id":"1","created_timestamp":"' . date('Y-m-d H:i:s') . '","updated_timestamp":"' . date('Y-m-d H:i:s') . '","author":"1","language_id":"1","status":"1"}', true));
			$this->db->table('peoples')->insert(json_decode('{"people_id":"1","first_name":"John","last_name":"Doe","people_slug":"john-doe","position":"Founder & CEO","email":"john@example.com","mobile":"+62813 8161 4558","instagram":"\/\/instagram.com\/johndoe","facebook":"\/\/fb.me\/johndoe","twitter":"\/\/twitter.com\/johndoe","biography":"Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Aliquet porttitor lacus luctus accumsan tortor posuere. Amet commodo nulla facilisi nullam vehicula ipsum. Egestas dui id ornare arcu odio. Viverra adipiscing at in tellus integer feugiat scelerisque. Proin nibh nisl condimentum id venenatis a condimentum. Ac placerat vestibulum lectus mauris ultrices. Non consectetur a erat nam at. Massa placerat duis ultricies lacus. In tellus integer feugiat scelerisque varius.","photo":"placeholder.png","status":"1"}', true));
			$this->db->table('testimonials')->insert(json_decode('{"testimonial_id":"1","photo":"placeholder.png","first_name":"Doe","last_name":"John","testimonial_title":"Sample of Testimonial","testimonial_content":"Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.","timestamp":"' . date('Y-m-d H:i:s') . '","language_id":"1","status":"1"}', true));
		}
		
		
		if($this->db->DBDriver != 'SQLite3')
		{
			$this->db->query('ALTER TABLE ' . $this->db->escapeIdentifiers('app__activity_logs') . ' ADD CONSTRAINT ' . $this->db->escapeIdentifiers('app__activity_logs_ibfk_1') . ' FOREIGN KEY (' . $this->db->escapeIdentifiers('user_id') . ') REFERENCES ' . $this->db->escapeIdentifiers('app__users') . ' (' . $this->db->escapeIdentifiers('user_id') . ')' . (in_array($this->db->DBDriver, array('SQLSRV')) ? ' ON DELETE NO ACTION ON UPDATE NO ACTION' : ' ON DELETE RESTRICT ON UPDATE CASCADE') . ';');
			$this->db->query('ALTER TABLE ' . $this->db->escapeIdentifiers('app__announcements') . ' ADD CONSTRAINT ' . $this->db->escapeIdentifiers('app__announcements_ibfk_1') . ' FOREIGN KEY (' . $this->db->escapeIdentifiers('language_id') . ') REFERENCES ' . $this->db->escapeIdentifiers('app__languages') . ' (' . $this->db->escapeIdentifiers('id') . ')' . (in_array($this->db->DBDriver, array('SQLSRV')) ? ' ON DELETE NO ACTION ON UPDATE NO ACTION' : ' ON DELETE RESTRICT ON UPDATE CASCADE') . ';');
			$this->db->query('ALTER TABLE ' . $this->db->escapeIdentifiers('app__connections') . ' ADD CONSTRAINT ' . $this->db->escapeIdentifiers('app__connections_ibfk_1') . ' FOREIGN KEY (' . $this->db->escapeIdentifiers('year') . ') REFERENCES ' . $this->db->escapeIdentifiers('app__years') . ' (' . $this->db->escapeIdentifiers('year') . ')' . (in_array($this->db->DBDriver, array('SQLSRV')) ? ' ON DELETE NO ACTION ON UPDATE NO ACTION' : ' ON DELETE RESTRICT ON UPDATE CASCADE') . ';');
			$this->db->query('ALTER TABLE ' . $this->db->escapeIdentifiers('app__ftp') . ' ADD CONSTRAINT ' . $this->db->escapeIdentifiers('app__ftp_ibfk_1') . ' FOREIGN KEY (' . $this->db->escapeIdentifiers('site_id') . ') REFERENCES ' . $this->db->escapeIdentifiers('app__settings') . ' (' . $this->db->escapeIdentifiers('id') . ')' . (in_array($this->db->DBDriver, array('SQLSRV')) ? ' ON DELETE NO ACTION ON UPDATE NO ACTION' : ' ON DELETE RESTRICT ON UPDATE CASCADE') . ';');
			$this->db->query('ALTER TABLE ' . $this->db->escapeIdentifiers('app__users') . ' ADD CONSTRAINT ' . $this->db->escapeIdentifiers('app__users_ibfk_1') . ' FOREIGN KEY (' . $this->db->escapeIdentifiers('language_id') . ') REFERENCES ' . $this->db->escapeIdentifiers('app__languages') . ' (' . $this->db->escapeIdentifiers('id') . ')' . (in_array($this->db->DBDriver, array('SQLSRV')) ? ' ON DELETE NO ACTION ON UPDATE NO ACTION' : ' ON DELETE RESTRICT ON UPDATE CASCADE') . ';');
			$this->db->query('ALTER TABLE ' . $this->db->escapeIdentifiers('app__users') . ' ADD CONSTRAINT ' . $this->db->escapeIdentifiers('app__users_ibfk_2') . ' FOREIGN KEY (' . $this->db->escapeIdentifiers('group_id') . ') REFERENCES ' . $this->db->escapeIdentifiers('app__groups') . ' (' . $this->db->escapeIdentifiers('group_id') . ')' . (in_array($this->db->DBDriver, array('SQLSRV')) ? ' ON DELETE NO ACTION ON UPDATE NO ACTION' : ' ON DELETE RESTRICT ON UPDATE CASCADE') . ';');
			$this->db->query('ALTER TABLE ' . $this->db->escapeIdentifiers('app__users_hash') . ' ADD CONSTRAINT ' . $this->db->escapeIdentifiers('app__users_hash_ibfk_1') . ' FOREIGN KEY (' . $this->db->escapeIdentifiers('user_id') . ') REFERENCES ' . $this->db->escapeIdentifiers('app__users') . ' (' . $this->db->escapeIdentifiers('user_id') . ')' . (in_array($this->db->DBDriver, array('SQLSRV')) ? ' ON DELETE NO ACTION ON UPDATE NO ACTION' : ' ON DELETE RESTRICT ON UPDATE CASCADE') . ';');
			$this->db->query('ALTER TABLE ' . $this->db->escapeIdentifiers('app__users_privileges') . ' ADD CONSTRAINT ' . $this->db->escapeIdentifiers('app__users_privileges_ibfk_1') . ' FOREIGN KEY (' . $this->db->escapeIdentifiers('user_id') . ') REFERENCES ' . $this->db->escapeIdentifiers('app__users') . ' (' . $this->db->escapeIdentifiers('user_id') . ')' . (in_array($this->db->DBDriver, array('SQLSRV')) ? ' ON DELETE NO ACTION ON UPDATE NO ACTION' : ' ON DELETE RESTRICT ON UPDATE CASCADE') . ';');
			$this->db->query('ALTER TABLE ' . $this->db->escapeIdentifiers('blogs') . ' ADD CONSTRAINT ' . $this->db->escapeIdentifiers('blogs_ibfk_2') . ' FOREIGN KEY (' . $this->db->escapeIdentifiers('author') . ') REFERENCES ' . $this->db->escapeIdentifiers('app__users') . ' (' . $this->db->escapeIdentifiers('user_id') . ')' . (in_array($this->db->DBDriver, array('SQLSRV')) ? ' ON DELETE NO ACTION ON UPDATE NO ACTION' : ' ON DELETE RESTRICT ON UPDATE CASCADE') . ';');
			$this->db->query('ALTER TABLE ' . $this->db->escapeIdentifiers('blogs') . ' ADD CONSTRAINT ' . $this->db->escapeIdentifiers('blogs_ibfk_3') . ' FOREIGN KEY (' . $this->db->escapeIdentifiers('language_id') . ') REFERENCES ' . $this->db->escapeIdentifiers('app__languages') . ' (' . $this->db->escapeIdentifiers('id') . ')' . (in_array($this->db->DBDriver, array('SQLSRV')) ? ' ON DELETE NO ACTION ON UPDATE NO ACTION' : ' ON DELETE RESTRICT ON UPDATE CASCADE') . ';');
			$this->db->query('ALTER TABLE ' . $this->db->escapeIdentifiers('blogs') . ' ADD CONSTRAINT ' . $this->db->escapeIdentifiers('blogs_ibfk_4') . ' FOREIGN KEY (' . $this->db->escapeIdentifiers('post_category') . ') REFERENCES ' . $this->db->escapeIdentifiers('blogs__categories') . ' (' . $this->db->escapeIdentifiers('category_id') . ')' . (in_array($this->db->DBDriver, array('SQLSRV')) ? ' ON DELETE NO ACTION ON UPDATE NO ACTION' : ' ON DELETE RESTRICT ON UPDATE CASCADE') . ';');
			$this->db->query('ALTER TABLE ' . $this->db->escapeIdentifiers('blogs__categories') . ' ADD CONSTRAINT ' . $this->db->escapeIdentifiers('blogs__categories_ibfk_1') . ' FOREIGN KEY (' . $this->db->escapeIdentifiers('language_id') . ') REFERENCES ' . $this->db->escapeIdentifiers('app__languages') . ' (' . $this->db->escapeIdentifiers('id') . ')' . (in_array($this->db->DBDriver, array('SQLSRV')) ? ' ON DELETE NO ACTION ON UPDATE NO ACTION' : ' ON DELETE RESTRICT ON UPDATE CASCADE') . ';');
			$this->db->query('ALTER TABLE ' . $this->db->escapeIdentifiers('galleries') . ' ADD CONSTRAINT ' . $this->db->escapeIdentifiers('galleries_ibfk_1') . ' FOREIGN KEY (' . $this->db->escapeIdentifiers('author') . ') REFERENCES ' . $this->db->escapeIdentifiers('app__users') . ' (' . $this->db->escapeIdentifiers('user_id') . ')' . (in_array($this->db->DBDriver, array('SQLSRV')) ? ' ON DELETE NO ACTION ON UPDATE NO ACTION' : ' ON DELETE RESTRICT ON UPDATE CASCADE') . ';');
			$this->db->query('ALTER TABLE ' . $this->db->escapeIdentifiers('oauth__login') . ' ADD CONSTRAINT ' . $this->db->escapeIdentifiers('oauth__login_ibfk_1') . ' FOREIGN KEY (' . $this->db->escapeIdentifiers('user_id') . ') REFERENCES ' . $this->db->escapeIdentifiers('app__users') . ' (' . $this->db->escapeIdentifiers('user_id') . ')' . (in_array($this->db->DBDriver, array('SQLSRV')) ? ' ON DELETE NO ACTION ON UPDATE NO ACTION' : ' ON DELETE RESTRICT ON UPDATE CASCADE') . ';');
			$this->db->query('ALTER TABLE ' . $this->db->escapeIdentifiers('pages') . ' ADD CONSTRAINT ' . $this->db->escapeIdentifiers('pages_ibfk_1') . ' FOREIGN KEY (' . $this->db->escapeIdentifiers('author') . ') REFERENCES ' . $this->db->escapeIdentifiers('app__users') . ' (' . $this->db->escapeIdentifiers('user_id') . ')' . (in_array($this->db->DBDriver, array('SQLSRV')) ? ' ON DELETE NO ACTION ON UPDATE NO ACTION' : ' ON DELETE RESTRICT ON UPDATE CASCADE') . ';');
			$this->db->query('ALTER TABLE ' . $this->db->escapeIdentifiers('pages__carousels') . ' ADD CONSTRAINT ' . $this->db->escapeIdentifiers('pages__carousels_ibfk_1') . ' FOREIGN KEY (' . $this->db->escapeIdentifiers('language_id') . ') REFERENCES ' . $this->db->escapeIdentifiers('app__languages') . ' (' . $this->db->escapeIdentifiers('id') . ')' . (in_array($this->db->DBDriver, array('SQLSRV')) ? ' ON DELETE NO ACTION ON UPDATE NO ACTION' : ' ON DELETE RESTRICT ON UPDATE CASCADE') . ';');
			$this->db->query('ALTER TABLE ' . $this->db->escapeIdentifiers('pages__faqs') . ' ADD CONSTRAINT ' . $this->db->escapeIdentifiers('pages__faqs_ibfk_1') . ' FOREIGN KEY (' . $this->db->escapeIdentifiers('language_id') . ') REFERENCES ' . $this->db->escapeIdentifiers('app__languages') . ' (' . $this->db->escapeIdentifiers('id') . ')' . (in_array($this->db->DBDriver, array('SQLSRV')) ? ' ON DELETE NO ACTION ON UPDATE NO ACTION' : ' ON DELETE RESTRICT ON UPDATE CASCADE') . ';');
			$this->db->query('ALTER TABLE ' . $this->db->escapeIdentifiers('testimonials') . ' ADD CONSTRAINT ' . $this->db->escapeIdentifiers('testimonials_ibfk_1') . ' FOREIGN KEY (' . $this->db->escapeIdentifiers('language_id') . ') REFERENCES ' . $this->db->escapeIdentifiers('app__languages') . ' (' . $this->db->escapeIdentifiers('id') . ')' . (in_array($this->db->DBDriver, array('SQLSRV')) ? ' ON DELETE NO ACTION ON UPDATE NO ACTION' : ' ON DELETE RESTRICT ON UPDATE CASCADE') . ';');
		}
    }
	
	public function down()
	{
		if($this->db->DBDriver == 'SQLSRV')
		{
			$this->db->query
			('
				EXEC sp_MSforeachtable "declare @name nvarchar(max); set @name = parsename(\'?\', 1); exec sp_MSdropconstraints @name";
				EXEC sp_MSforeachtable "drop table ?";
			');
		}
		else
		{
			$this->db->disableForeignKeyChecks();
			
			foreach($this->db->listTables() as $key => $val)
			{
				if('migrations' == $val) continue;
				
				$this->forge->dropTable($val, true);
			}
			
			$this->db->enableForeignKeyChecks();
		}
	}
}
