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

class AppConnections extends Migration
{
    public function up()
    {
        // Add columns to table
        $this->forge->addField([
            'id' => [
                'type' => 'int',
                'unsigned' => true,
                'auto_increment' => true,
                'null' => false
            ],
            'year' => [
                'type' => ('MySQLi' === $this->db->DBDriver ? 'year' : 'int'),
                'constraint' => 4,
                'null' => false
            ],
            'name' => [
                'type' => 'varchar',
                'constraint' => 255,
                'null' => false
            ],
            'description' => [
                'type' => 'varchar',
                'constraint' => 255,
                'null' => false
            ],
            'database_driver' => [
                'type' => 'varchar',
                'constraint' => 32,
                'null' => false
            ],
            'hostname' => [
                'type' => 'varchar',
                'constraint' => 255,
                'null' => false
            ],
            'port' => [
                'type' => 'int',
                'null' => false
            ],
            'username' => [
                'type' => 'varchar',
                'constraint' => 512,
                'null' => false
            ],
            'password' => [
                'type' => 'varchar',
                'constraint' => 512,
                'null' => false
            ],
            'database_name' => [
                'type' => 'varchar',
                'constraint' => 64,
                'null' => false
            ],
            'status' => [
                'type' => 'tinyint',
                'default' => '0',
                'null' => false
            ],
            'created_by' => [
                'type' => 'int',
                'unsigned' => true,
                'null' => false
            ],
            'created_at' => [
                'type' => 'datetime',
                'null' => true
            ],
            'updated_by' => [
                'type' => 'int',
                'unsigned' => true,
                'null' => true
            ],
            'updated_at' => [
                'type' => 'datetime',
                'null' => true
            ],
        ]);

        // Add primary and unique index
        $this->forge->addKey('id', true, true);
        $this->forge->addKey('year');
        $this->forge->addKey('status');

        $this->forge->addKey('created_by');
        $this->forge->addKey('updated_by');

        $this->forge->addForeignKey('created_by', 'app_users', 'user_id', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('updated_by', 'app_users', 'user_id', 'CASCADE', 'SET NULL');

        $this->forge->createTable('app_connections');
    }

    public function down()
    {
        // Safe abstraction
    }
}
