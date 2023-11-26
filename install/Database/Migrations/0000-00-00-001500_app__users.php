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

class AppUsers extends Migration
{
    public function up()
    {
        // Add columns to table
        $this->forge->addField([
            'user_id' => [
                'type' => 'bigint',
                'constraint' => 22,
                'unsigned' => true,
                'auto_increment' => true,
                'null' => false
            ],
            'group_id' => [
                'type' => 'int',
                'constraint' => 11,
                'unsigned' => true,
                'null' => false
            ],
            'language_id' => [
                'type' => 'int',
                'constraint' => 11,
                'unsigned' => true,
                'null' => false
            ],
            'country_id' => [
                'type' => 'int',
                'constraint' => 5,
                'unsigned' => true,
                'null' => false
            ],
            'email' => [
                'type' => 'varchar',
                'constraint' => 255,
                'null' => false
            ],
            'password' => [
                'type' => 'varchar',
                'constraint' => 255,
                'null' => false
            ],
            'username' => [
                'type' => 'varchar',
                'constraint' => 32,
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
                'constraint' => 1,
                'default' => '0',
                'null' => false
            ],
            'bio' => [
                'type' => 'text',
                'null' => false
            ],
            'photo' => [
                'type' => 'varchar',
                'constraint' => 255,
                'null' => false
            ],
            'address' => [
                'type' => 'text',
                'null' => false
            ],
            'phone' => [
                'type' => 'varchar',
                'constraint' => 16,
                'null' => false
            ],
            'postal_code' => [
                'type' => 'varchar',
                'constraint' => 10,
                'null' => false
            ],
            'registered_date' => [
                'type' => 'date',
                'null' => false
            ],
            'last_login' => [
                'type' => 'timestamp',
                'default' => null
            ],
            'status' => [
                'type' => 'tinyint',
                'constraint' => 1,
                'default' => '0',
                'null' => false
            ]
        ]);

        // Add primary and unique index
        $this->forge->addKey('user_id', true, true);
        $this->forge->addKey('username', false, true);
        $this->forge->addKey('email', false, true);

        // Add foreign key to parent table
        $this->forge->addForeignKey('group_id', 'app__groups', 'group_id', ('SQLSRV' == $this->db->DBDriver ? 'NO ACTION' : 'CASCADE'), ('SQLSRV' == $this->db->DBDriver ? 'NO ACTION' : 'RESTRICT'));
        $this->forge->addForeignKey('language_id', 'app__languages', 'id', ('SQLSRV' == $this->db->DBDriver ? 'NO ACTION' : 'CASCADE'), ('SQLSRV' == $this->db->DBDriver ? 'NO ACTION' : 'RESTRICT'));

        // Create table
        $this->forge->createTable('app__users');
    }

    public function down()
    {
        // Safe abstraction
    }
}
