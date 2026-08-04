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

class AppRestClients extends Migration
{
    public function up()
    {
        // Add columns table
        $this->forge->addField([
            'id' => [
                'type' => 'int',
                'unsigned' => true,
                'auto_increment' => true,
                'null' => false
            ],
            'title' => [
                'type' => 'varchar',
                'constraint' => 64,
                'null' => false
            ],
            'description' => [
                'type' => ('MySQLi' === $this->db->DBDriver ? 'tinytext' : 'text'),
                'null' => false
            ],
            'api_key' => [
                'type' => 'varchar',
                'constraint' => 64,
                'null' => false
            ],
            'method' => [
                'type' => (in_array($this->db->DBDriver, ['Postgre', 'MySQLi']) ? 'json' : 'text'),
                'null' => false
            ],
            'ip_range' => [
                'type' => 'text',
                'null' => false
            ],
            'valid_until' => [
                'type' => 'date',
                'null' => false
            ],
            'status' => [
                'type' => 'int',
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
        $this->forge->addKey('status');

        $this->forge->addKey('created_by');
        $this->forge->addKey('updated_by');

        $this->forge->addForeignKey('created_by', 'app_users', 'user_id', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('updated_by', 'app_users', 'user_id', 'CASCADE', 'SET NULL');

        $this->forge->createTable('app_rest_clients');
    }

    public function down()
    {
        // Safe abstraction
    }
}
