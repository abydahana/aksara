<?php

/**
 * This file is part of Aksara CMS, both framework and publishing
 * platform.
 *
 * @author     Aby Dahana <abydahana@gmail.com>
 * @copyright  (c) Aksara Laboratory <https://aksaracms.com>
 * @license    MIT License
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the LICENSE.txt file.
 *
 * When the signs is coming, those who don't believe at "that time"
 * have only two choices, commit suicide or become brutal.
 */

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AppSettings extends Migration
{
    public function up()
    {
        // Add columns to table
        $this->forge->addField([
            'id' => [
                'type' => 'int',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
                'null' => false
            ],
            'app_name' => [
                'type' => 'varchar',
                'constraint' => 255,
                'null' => false
            ],
            'app_description' => [
                'type' => 'text',
                'null' => false
            ],
            'app_logo' => [
                'type' => 'varchar',
                'constraint' => 255,
                'null' => false
            ],
            'app_icon' => [
                'type' => 'varchar',
                'constraint' => 255,
                'null' => false
            ],
            'frontend_theme' => [
                'type' => 'varchar',
                'constraint' => 32,
                'null' => false
            ],
            'backend_theme' => [
                'type' => 'varchar',
                'constraint' => 32,
                'null' => false
            ],
            'app_language' => [
                'type' => 'int',
                'constraint' => 11,
                'unsigned' => true,
                'null' => false
            ],
            'office_name' => [
                'type' => 'varchar',
                'constraint' => 255,
                'null' => false
            ],
            'office_phone' => [
                'type' => 'varchar',
                'constraint' => 32,
                'null' => false
            ],
            'office_email' => [
                'type' => 'varchar',
                'constraint' => 64,
                'null' => false
            ],
            'office_fax' => [
                'type' => 'varchar',
                'constraint' => 32,
                'null' => false
            ],
            'office_address' => [
                'type' => 'text',
                'null' => false
            ],
            'office_map' => [
                'type' => 'text',
                'null' => false
            ],
            'frontend_registration' => [
                'type' => 'tinyint',
                'constraint' => 1,
                'default' => '0',
                'null' => false
            ],
            'default_membership_group' => [
                'type' => 'int',
                'constraint' => 11,
                'null' => false
            ],
            'auto_active_registration' => [
                'type' => 'tinyint',
                'constraint' => 1,
                'default' => '0',
                'null' => false
            ],
            'username_change' => [
                'type' => 'tinyint',
                'constraint' => 1,
                'default' => '0',
                'null' => false
            ],
            'login_attempt' => [
                'type' => 'smallint',
                'constraint' => 5,
                'default' => '0'
            ],
            'blocking_time' => [
                'type' => 'smallint',
                'constraint' => 5,
                'default' => '0'
            ],
            'one_device_login' => [
                'type' => 'tinyint',
                'constraint' => 1,
                'default' => '0',
                'null' => false
            ],
            'account_age_restriction' => [
                'type' => 'smallint',
                'constraint' => 3,
                'default' => '0'
            ],
            'spam_timer' => [
                'type' => 'smallint',
                'constraint' => 5,
                'default' => '0'
            ],
            'google_analytics_key' => [
                'type' => 'varchar',
                'constraint' => 32,
                'null' => false
            ],
            'openlayers_search_provider' => [
                'type' => 'varchar',
                'constraint' => 10,
                'null' => false
            ],
            'openlayers_search_key' => [
                'type' => 'varchar',
                'constraint' => 128,
                'null' => false
            ],
            'default_map_tile' => [
                'type' => 'varchar',
                'constraint' => 255,
                'null' => false
            ],
            'facebook_app_id' => [
                'type' => 'varchar',
                'constraint' => 22,
                'null' => false
            ],
            'facebook_app_secret' => [
                'type' => 'varchar',
                'constraint' => 512,
                'null' => false
            ],
            'google_client_id' => [
                'type' => 'varchar',
                'constraint' => 255,
                'null' => false
            ],
            'google_client_secret' => [
                'type' => 'varchar',
                'constraint' => 512,
                'null' => false
            ],
            'twitter_username' => [
                'type' => 'varchar',
                'constraint' => 64,
                'null' => false
            ],
            'instagram_username' => [
                'type' => 'varchar',
                'constraint' => 64,
                'null' => false
            ],
            'whatsapp_number' => [
                'type' => 'varchar',
                'constraint' => 16,
                'null' => false
            ],
            'action_sound' => [
                'type' => 'tinyint',
                'constraint' => 1,
                'default' => '0',
                'null' => false
            ],
            'update_check' => [
                'type' => 'tinyint',
                'constraint' => 1,
                'default' => '0',
                'null' => false
            ],
            'smtp_hostname' => [
                'type' => 'varchar',
                'constraint' => 255,
                'default' => ''
            ],
            'smtp_port' => [
                'type' => 'int',
                'constraint' => 6,
                'default' => '0'
            ],
            'smtp_username' => [
                'type' => 'varchar',
                'constraint' => 255,
                'default' => ''
            ],
            'smtp_password' => [
                'type' => 'varchar',
                'constraint' => 255,
                'default' => ''
            ]
        ]);

        // Add primary and unique index
        $this->forge->addKey('id', true, true);

        // Add foreign key parent table
        $this->forge->addForeignKey('app_language', 'app__languages', 'id', 'CASCADE', 'RESTRICT');

        // Create table
        $this->forge->createTable('app__settings');
    }

    public function down()
    {
        // Safe abstraction
    }
}
