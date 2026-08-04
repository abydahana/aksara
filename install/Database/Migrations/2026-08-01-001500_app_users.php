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
 * When the signs come, those who don't believe at "that time"
 * will have only two choices, commit suicide or become brutal.
 */

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AppUsers extends Migration
{
    public function up()
    {
        // Add columns to table
        $this->forge->addField([
            'user_id' => [
                'type' => 'int',
                'unsigned' => true,
                'auto_increment' => true,
                'null' => false
            ],
            'group_id' => [
                'type' => 'int',
                'unsigned' => true,
                'null' => false
            ],
            'language_id' => [
                'type' => 'int',
                'unsigned' => true,
                'null' => false
            ],
            'country_id' => [
                'type' => 'int',
                'unsigned' => true,
                'null' => false
            ],
            'email' => [
                'type' => 'varchar',
                'constraint' => 255,
                'null' => false
            ],
            'username' => [
                'type' => 'varchar',
                'constraint' => 32,
                'null' => false
            ],
            'password' => [
                'type' => 'varchar',
                'constraint' => 255,
                'null' => false
            ],
            'first_name' => [
                'type' => 'varchar',
                'constraint' => 32,
                'null' => false
            ],
            'last_name' => [
                'type' => 'varchar',
                'constraint' => 32,
                'null' => false
            ],
            'gender' => [
                'type' => 'tinyint',
                'default' => '0',
                'null' => false
            ],
            'address' => [
                'type' => ('MySQLi' === $this->db->DBDriver ? 'tinytext' : 'text'),
                'null' => false
            ],
            'postal_code' => [
                'type' => 'varchar',
                'constraint' => 10,
                'null' => false
            ],
            'phone' => [
                'type' => 'varchar',
                'constraint' => 16,
                'null' => false
            ],
            'bio' => [
                'type' => ('MySQLi' === $this->db->DBDriver ? 'tinytext' : 'text'),
                'null' => false
            ],
            'photo' => [
                'type' => 'varchar',
                'constraint' => 255,
                'null' => false
            ],
            'last_login' => [
                'type' => 'timestamp',
                'null' => true
            ],
            'status' => [
                'type' => 'tinyint',
                'default' => '0',
                'null' => false
            ],
            'created_at' => [
                'type' => 'datetime',
                'null' => true
            ],
            'created_by' => [
                'type' => 'int',
                'unsigned' => true,
                'null' => true
            ],
            'updated_at' => [
                'type' => 'datetime',
                'null' => true
            ],
            'updated_by' => [
                'type' => 'int',
                'unsigned' => true,
                'null' => true
            ],
        ]);

        // Add primary and unique index
        $this->forge->addKey('user_id', true, true);
        $this->forge->addKey('username', false, true);
        $this->forge->addKey('email', false, true);
        $this->forge->addKey('status');

        $this->forge->addForeignKey('group_id', 'app_groups', 'group_id', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('language_id', 'app_languages', 'id', 'CASCADE', 'RESTRICT');

        $this->forge->addKey('created_by');
        $this->forge->addKey('updated_by');

        $this->forge->addForeignKey('created_by', 'app_users', 'user_id', 'CASCADE', 'SET NULL');
        $this->forge->addForeignKey('updated_by', 'app_users', 'user_id', 'CASCADE', 'SET NULL');

        // Create table
        $this->forge->createTable('app_users');
    }

    public function down()
    {
        // Safe abstraction
    }
}
