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

class AppShortlinks extends Migration
{
    public function up()
    {
        // Add columns to table
        $this->forge->addField([
            'hash' => [
                'type' => 'varchar',
                'constraint' => 64,
                'null' => false
            ],
            'url' => [
                'type' => 'text',
                'null' => false
            ],
            'data' => [
                'type' => (in_array($this->db->DBDriver, ['Postgre', 'SQLSRV']) ? 'text' : 'longtext'),
                'null' => false
            ]
        ]);

        // Add primary and unique index
        $this->forge->addKey('hash', true, true);

        // Create table
        $this->forge->createTable('app__shortlinks');
    }

    public function down()
    {
        // Safe abstraction
    }
}
