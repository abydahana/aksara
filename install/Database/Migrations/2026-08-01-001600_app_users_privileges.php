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

class AppUsersPrivileges extends Migration
{
    public function up()
    {
        // Add columns to table
        $this->forge->addField([
            'user_id' => [
                'type' => 'int',
                'unsigned' => true,
                'null' => false
            ],
            'sub_level_1' => [
                'type' => 'varchar',
                'constraint' => 32,
                'null' => true
            ],
            'visible_menu' => [
                'type' => (in_array($this->db->DBDriver, ['Postgre', 'MySQLi']) ? 'json' : 'text'),
                'null' => false
            ],
            'access_year' => [
                'type' => ('MySQLi' === $this->db->DBDriver ? 'year' : 'smallint'),
                'null' => true
            ]
        ]);

        // Add primary and unique index
        $this->forge->addKey('user_id', true, true);

        // Add foreign key to parent table
        $this->forge->addForeignKey('user_id', 'app_users', 'user_id', 'CASCADE', 'CASCADE');

        // Create table
        $this->forge->createTable('app_users_privileges');
    }

    public function down()
    {
        // Safe abstraction
    }
}
