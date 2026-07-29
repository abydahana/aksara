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

class AppMenus extends Migration
{
    public function up()
    {
        // Add columns to table
        $this->forge->addField([
            'menu_id' => [
                'type' => 'int',
                'unsigned' => true,
                'auto_increment' => true,
                'null' => false
            ],
            'menu_placement' => [
                'type' => 'varchar',
                'constraint' => 22,
                'null' => false
            ],
            'menu_label' => [
                'type' => 'varchar',
                'constraint' => 32,
                'null' => false
            ],
            'menu_description' => [
                'type' => ('MySQLi' === $this->db->DBDriver ? 'tinytext' : 'text'),
                'null' => false
            ],
            'menu_structure' => [
                'type' => (in_array($this->db->DBDriver, ['Postgre', 'MySQLi']) ? 'json' : 'text'),
                'null' => false
            ],
            'group_id' => [
                'type' => 'int',
                'unsigned' => true,
                'null' => false
            ],
            'status' => [
                'type' => 'tinyint',
                'default' => '0',
                'null' => false
            ],
            'created_timestamp' => [
                'type' => 'timestamp',
                'null' => false
            ],
            'updated_timestamp' => [
                'type' => 'timestamp',
                'null' => true
            ]
        ]);

        // Add primary and unique index
        $this->forge->addKey('menu_id', true, true);
        $this->forge->addKey('menu_placement');
        $this->forge->addKey('group_id');
        $this->forge->addKey('status');

        // Create table
        $this->forge->createTable('app_menus');
    }

    public function down()
    {
        // Safe abstraction
    }
}
