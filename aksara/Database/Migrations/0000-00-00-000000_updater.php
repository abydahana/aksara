<?php
/**
 * @author			Aby Dahana
 * @profile			abydahana.github.io
 * @website			www.aksaracms.com
 * @copyright		(c) 2021 - Aksara Laboratory
 * @since			version 4.2.8
 */

namespace Aksara\Database\Migrations;

use CodeIgniter\Database\Migration;

class Updater extends Migration
{
    public function up()
    {
		// modify previous column type
		$this->forge->modifyColumn
		(
			'app__activity_logs',
			array
			(
				'id' => array
				(
					'type' => 'bigint',
					'constraint' => 22,
					'unsigned' => true
				),
				'user_id' => array
				(
					'type' => 'bigint',
					'constraint' => 22,
					'unsigned' => true
				),
				'path' => array
				(
					'constraint' => 255
				),
				'method' => array
				(
					'constraint' => 255
				),
				'browser' => array
				(
					'constraint' => 255
				),
				'platform' => array
				(
					'constraint' => 64
				),
				'ip_address' => array
				(
					'constraint' => 45
				),
				'timestamp' => array
				(
					'type' => 'timestamp'
				)
			)
		);
		
		$this->forge->modifyColumn
		(
			'app__announcements',
			array
			(
				'announcement_id' => array
				(
					'type' => 'bigint',
					'constraint' => 22,
					'unsigned' => true
				),
				'title' => array
				(
					'constraint' => 255
				),
				'announcement_slug' => array
				(
					'constraint' => 255
				),
				'created_timestamp' => array
				(
					'type' => 'timestamp'
				),
				'updated_timestamp' => array
				(
					'type' => 'timestamp'
				),
				'status' => array
				(
					'default' => '0'
				)
			)
		);
		
		$this->forge->modifyColumn
		(
			'app__connections',
			array
			(
				'name' => array
				(
					'constraint' => 255
				),
				'description' => array
				(
					'constraint' => 255
				),
				'hostname' => array
				(
					'constraint' => 255
				),
				'port' => array
				(
					'type' => 'int',
					'constraint' => 5
				),
				'username' => array
				(
					'constraint' => 512
				),
				'password' => array
				(
					'constraint' => 512
				),
				'database_name' => array
				(
					'constraint' => 64
				),
				'status' => array
				(
					'default' => 0
				)
			)
		);
		
		$this->forge->modifyColumn
		(
			'app__ftp',
			array
			(
				'site_id' => array
				(
					'unsigned' => true
				),
				'hostname' => array
				(
					'constraint' => 255
				),
				'description' => array
				(
					'constraint' => 255
				),
				'hostname' => array
				(
					'constraint' => 255
				),
				'port' => array
				(
					'constraint' => 5
				),
				'username' => array
				(
					'constraint' => 512
				),
				'password' => array
				(
					'constraint' => 512
				)
			)
		);
		
		$this->forge->modifyColumn
		(
			'app__groups',
			array
			(
				'group_id' => array
				(
					'unsigned' => true
				),
				'status' => array
				(
					'default' => '0'
				)
			)
		);
		
		$this->forge->modifyColumn
		(
			'app__groups_privileges',
			array
			(
				'path' => array
				(
					'constraint' => 255
				),
				'last_generated' => array
				(
					'type' => 'timestamp'
				)
			)
		);
		
		$this->forge->modifyColumn
		(
			'app__languages',
			array
			(
				'id' => array
				(
					'unsigned' => true
				)
			)
		);
		
		$this->forge->modifyColumn
		(
			'app__menus',
			array
			(
				'menu_id' => array
				(
					'unsigned' => true
				),
				'group_id' => array
				(
					'unsigned' => true
				)
			)
		);
		
		$this->forge->modifyColumn
		(
			'app__rest_api',
			array
			(
				'id' => array
				(
					'unsigned' => true
				)
			)
		);
		
		$this->forge->modifyColumn
		(
			'app__sessions',
			array
			(
				'timestamp' => array
				(
					'type' => 'timestamp'
				)
			)
		);
		
		$this->forge->modifyColumn
		(
			'app__settings',
			array
			(
				'id' => array
				(
					'unsigned' => true
				),
				'app_name' => array
				(
					'constraint' => 255
				),
				'app_logo' => array
				(
					'constraint' => 255
				),
				'app_icon' => array
				(
					'constraint' => 255
				),
				'app_language' => array
				(
					'unsigned' => true
				),
				'office_name' => array
				(
					'constraint' => 255
				),
				'one_device_login' => array
				(
					'default' => '0'
				),
				'username_changes' => array
				(
					'default' => '0'
				),
				'frontend_registration' => array
				(
					'default' => '0'
				),
				'auto_active_registration' => array
				(
					'default' => '0'
				),
				'smtp_username' => array
				(
					'constraint' => 64
				),
				'action_sound' => array
				(
					'default' => '0'
				)
			)
		);
		
		$this->forge->addColumn
		(
			'app__settings',
			array
			(
				'login_attempt' => array
				(
					'type' => 'int',
					'constraint' => 5,
					'default' => 0,
					'after' => 'username_changes',
				),
				'blocking_time' => array
				(
					'type' => 'int',
					'constraint' => 5,
					'default' => 0,
					'after' => 'login_attempt'
				)
			)
		);
		
		$this->forge->modifyColumn
		(
			'app__users',
			array
			(
				'user_id' => array
				(
					'type' => 'bigint',
					'constraint' => 22,
					'unsigned' => true
				),
				'group_id' => array
				(
					'unsigned' => true
				),
				'language_id' => array
				(
					'unsigned' => true
				),
				'country' => array
				(
					'name' => 'country_id',
					'type' => 'int',
					'constraint' => 5,
					'unsigned' => true
				),
				'gender' => array
				(
					'default' => '0'
				),
				'last_login' => array
				(
					'type' => 'timestamp'
				),
				'status' => array
				(
					'default' => '0'
				)
			)
		);
		
		$this->forge->modifyColumn
		(
			'app__users_hash',
			array
			(
				'user_id' => array
				(
					'type' => 'bigint',
					'constraint' => 22,
					'unsigned' => true
				)
			)
		);
		
		$this->forge->modifyColumn
		(
			'app__users_privileges',
			array
			(
				'user_id' => array
				(
					'type' => 'bigint',
					'constraint' => 22,
					'unsigned' => true
				),
				'sub_level_1' => array
				(
					'unsigned' => true
				)
			)
		);
		
		$this->forge->modifyColumn
		(
			'app__visitor_logs',
			array
			(
				'ip_address' => array
				(
					'constraint' => 45
				),
				'timestamp' => array
				(
					'type' => 'timestamp'
				),
				'platform' => array
				(
					'constraint' => 64
				)
			)
		);
		
		$this->forge->modifyColumn
		(
			'blogs',
			array
			(
				'post_id' => array
				(
					'type' => 'bigint',
					'constraint' => 22,
					'unsigned' => true
				),
				'post_title' => array
				(
					'constraint' => 255
				),
				'post_slug' => array
				(
					'constraint' => 255
				),
				'post_category' => array
				(
					'unsigned' => true
				),
				'created_timestamp' => array
				(
					'type' => 'timestamp'
				),
				'updated_timestamp' => array
				(
					'type' => 'timestamp'
				),
				'author' => array
				(
					'type' => 'bigint',
					'constraint' => 22,
					'unsigned' => true
				),
				'headline' => array
				(
					'default' => '0'
				),
				'featured_image' => array
				(
					'constraint' => 255
				),
				'language_id' => array
				(
					'unsigned' => true
				),
				'status' => array
				(
					'default' => '0'
				)
			)
		);
		
		$this->forge->modifyColumn
		(
			'blogs__categories',
			array
			(
				'category_id' => array
				(
					'unsigned' => true
				),
				'category_image' => array
				(
					'constraint' => 255
				),
				'language_id' => array
				(
					'unsigned' => true
				),
				'status' => array
				(
					'default' => '0'
				)
			)
		);
		
		$this->forge->modifyColumn
		(
			'galleries',
			array
			(
				'gallery_id' => array
				(
					'type' => 'bigint',
					'constraint' => 22,
					'unsigned' => true
				),
				'gallery_title' => array
				(
					'constraint' => 255
				),
				'gallery_slug' => array
				(
					'constraint' => 255
				),
				'created_timestamp' => array
				(
					'type' => 'timestamp'
				),
				'updated_timestamp' => array
				(
					'type' => 'timestamp'
				),
				'featured' => array
				(
					'default' => '0'
				),
				'author' => array
				(
					'type' => 'bigint',
					'constraint' => 22,
					'unsigned' => true
				),
				'status' => array
				(
					'default' => '0'
				)
			)
		);
		
		$this->forge->modifyColumn
		(
			'inquiries',
			array
			(
				'id' => array
				(
					'type' => 'bigint',
					'constraint' => 22,
					'unsigned' => true
				),
				'timestamp' => array
				(
					'type' => 'timestamp'
				)
			)
		);
		
		$this->forge->modifyColumn
		(
			'oauth__login',
			array
			(
				'user_id' => array
				(
					'type' => 'bigint',
					'constraint' => 22,
					'unsigned' => true
				),
				'status' => array
				(
					'default' => '0'
				)
			)
		);
		
		$this->forge->modifyColumn
		(
			'pages',
			array
			(
				'page_id' => array
				(
					'type' => 'bigint',
					'constraint' => 22,
					'unsigned' => true
				),
				'page_title' => array
				(
					'constraint' => 255
				),
				'page_slug' => array
				(
					'constraint' => 255
				),
				'carousel_id' => array
				(
					'unsigned' => true
				),
				'faq_id' => array
				(
					'unsigned' => true
				),
				'created_timestamp' => array
				(
					'type' => 'timestamp'
				),
				'updated_timestamp' => array
				(
					'type' => 'timestamp'
				),
				'author' => array
				(
					'type' => 'bigint',
					'constraint' => 22,
					'unsigned' => true
				),
				'language_id' => array
				(
					'unsigned' => true
				),
				'status' => array
				(
					'default' => '0'
				)
			)
		);
		
		$this->forge->modifyColumn
		(
			'pages__carousels',
			array
			(
				'carousel_id' => array
				(
					'unsigned' => true
				),
				'created_timestamp' => array
				(
					'type' => 'timestamp'
				),
				'updated_timestamp' => array
				(
					'type' => 'timestamp'
				),
				'language_id' => array
				(
					'unsigned' => true
				),
				'status' => array
				(
					'default' => '0'
				)
			)
		);
		
		$this->forge->modifyColumn
		(
			'pages__faqs',
			array
			(
				'faq_id' => array
				(
					'unsigned' => true
				),
				'created_timestamp' => array
				(
					'type' => 'timestamp'
				),
				'updated_timestamp' => array
				(
					'type' => 'timestamp'
				),
				'language_id' => array
				(
					'unsigned' => true
				),
				'status' => array
				(
					'default' => '0'
				)
			)
		);
		
		$this->forge->modifyColumn
		(
			'peoples',
			array
			(
				'people_id' => array
				(
					'unsigned' => true
				),
				'people_slug' => array
				(
					'constraint' => 255
				),
				'photo' => array
				(
					'constraint' => 255
				),
				'status' => array
				(
					'default' => '0'
				)
			)
		);
		
		$this->forge->modifyColumn
		(
			'testimonials',
			array
			(
				'testimonial_id' => array
				(
					'unsigned' => true
				),
				'language_id' => array
				(
					'unsigned' => true
				),
				'status' => array
				(
					'default' => '0'
				)
			)
		);
		
		$this->forge->addField
		(
			array
			(
				'ip_address' => array
				(
					'type' => 'varchar',
					'constraint' => 45,
					'null' => false
				),
				'blocked_until' => array
				(
					'type' => 'timestamp'
				)
			)
		);
		$this->forge->addKey('ip_address', true, true);
		$this->forge->createTable('app__users_blocked');
    }
	
	public function down()
	{
		// drop unused tables
	}
}
