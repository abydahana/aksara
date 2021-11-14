<?php

namespace App\Database\Migrations;

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
		$this->down();
		
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
		// remove all tables that previously created
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
			//$this->db->disableForeignKeyChecks();
			
			foreach($this->db->listTables() as $key => $val)
			{
				// skip table deletion if not exists or it's come from migration
				if('migrations' == $val || !$this->db->tableExists($val)) continue;
				
				// drop table
				$this->forge->dropTable($val, true, true);
			}
			
			//$this->db->enableForeignKeyChecks();
		}
	}
}
