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

class AppGroups extends Migration
{
    public function up()
    {
        // Add columns to table
        $this->forge->addField([
            'group_id' => [
                'type' => 'int',
                'unsigned' => true,
                'auto_increment' => true,
                'null' => false
            ],
            'group_name' => [
                'type' => 'varchar',
                'constraint' => 64,
                'null' => false
            ],
            'group_description' => [
                'type' => ('MySQLi' === $this->db->DBDriver ? 'mediumtext' : 'text'),
                'null' => false
            ],
            'group_privileges' => [
                'type' => (in_array($this->db->DBDriver, ['Postgre', 'MySQLi']) ? 'json' : 'text'),
                'null' => false
            ],
            'status' => [
                'type' => 'tinyint',
                'default' => '0',
                'null' => false
            ]
        ]);

        // Add primary and unique index
        $this->forge->addKey('group_id', true, true);
        $this->forge->addKey('status');

        // Create table
        $this->forge->createTable('app_groups');
    }

    public function down()
    {
        // Safe abstraction
    }
}
