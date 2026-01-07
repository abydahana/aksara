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

class AppUsersPrivileges extends Migration
{
    public function up()
    {
        // Add columns to table
        $this->forge->addField([
            'user_id' => [
                'type' => 'bigint',
                'constraint' => 22,
                'unsigned' => true,
                'null' => false
            ],
            'sub_level_1' => [
                'type' => 'int',
                'constraint' => 11,
                'unsigned' => true,
                'null' => false
            ],
            'visible_menu' => [
                'type' => 'text',
                'null' => false
            ],
            'access_year' => [
                'type' => (in_array($this->db->DBDriver, ['Postgre', 'SQLSRV'], true) ? 'int' : 'year'),
                'constraint' => 4,
                'null' => false
            ]
        ]);

        // Add primary and unique index
        $this->forge->addKey('user_id', true, true);

        // Add foreign key to parent table
        $this->forge->addForeignKey('user_id', 'app__users', 'user_id', 'CASCADE', 'CASCADE');

        // Create table
        $this->forge->createTable('app__users_privileges');
    }

    public function down()
    {
        // Safe abstraction
    }
}
