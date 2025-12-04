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

class AppMenus extends Migration
{
    public function up()
    {
        // Add columns to table
        $this->forge->addField([
            'menu_id' => [
                'type' => 'int',
                'constraint' => 11,
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
                'type' => 'text',
                'null' => false
            ],
            'serialized_data' => [
                'type' => (in_array($this->db->DBDriver, ['Postgre', 'SQLSRV']) ? 'text' : 'longtext'),
                'null' => false
            ],
            'group_id' => [
                'type' => 'int',
                'constraint' => 11,
                'unsigned' => true,
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
        $this->forge->addKey('menu_id', true, true);
        $this->forge->addKey('menu_placement', false, false);

        // Create table
        $this->forge->createTable('app__menus');
    }

    public function down()
    {
        // Safe abstraction
    }
}
