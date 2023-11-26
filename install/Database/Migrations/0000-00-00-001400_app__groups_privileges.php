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

class AppGroupsPrivileges extends Migration
{
    public function up()
    {
        // Add columns to table
        $this->forge->addField([
            'path' => [
                'type' => 'varchar',
                'constraint' => 255,
                'null' => false
            ],
            'privileges' => [
                'type' => (in_array($this->db->DBDriver, ['Postgre', 'SQLSRV']) ? 'text' : 'longtext'),
                'null' => false
            ],
            'last_generated' => [
                'type' => 'timestamp',
                'null' => false
            ]
        ]);

        // Create primary and unique index
        $this->forge->addKey('path', true, true);

        // Create table
        $this->forge->createTable('app__groups_privileges');
    }

    public function down()
    {
        // Safe abstraction
    }
}
