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

class AppConnections extends Migration
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
            'year' => [
                'type' => (in_array($this->db->DBDriver, ['Postgre', 'SQLSRV']) ? 'int' : 'year'),
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
                'constraint' => 5,
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
                'constraint' => 1,
                'default' => '0',
                'null' => false
            ]
        ]);

        // Add primary and unique index
        $this->forge->addKey('id', true, true);
        $this->forge->addKey('year', false, false);
        $this->forge->addKey('database_driver', false, false);

        // Add index for status column
        $this->forge->addKey('status', false, false);

        // Create table
        $this->forge->createTable('app__connections');
    }

    public function down()
    {
        // Safe abstraction
    }
}
