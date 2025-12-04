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

class AppRestClients extends Migration
{
    public function up()
    {
        // Add columns table
        $this->forge->addField([
            'id' => [
                'type' => 'int',
                'constraint' => 11,
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
                'type' => 'text',
                'null' => false
            ],
            'api_key' => [
                'type' => 'varchar',
                'constraint' => 64,
                'null' => false
            ],
            'method' => [
                'type' => (in_array($this->db->DBDriver, ['Postgre', 'SQLSRV']) ? 'text' : 'tinytext'),
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
                'constraint' => 1,
                'null' => false
            ]
        ]);

        // Add primary and unique index
        $this->forge->addKey('id', true, true);

        // Create table
        $this->forge->createTable('app__rest_clients');
    }

    public function down()
    {
        // Safe abstraction
    }
}
