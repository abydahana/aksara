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

class AppYears extends Migration
{
    public function up()
    {
        // Add columns to table
        $this->forge->addField([
            'year' => [
                'type' => (in_array($this->db->DBDriver, ['Postgre', 'SQLSRV'], true) ? 'int' : 'year'),
                'constraint' => 4,
                'null' => false
            ],
            'default' => [
                'type' => 'tinyint',
                'constraint' => 1,
                'default' => '0',
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
        $this->forge->addKey('year', true, true);

        // Add index for status column
        $this->forge->addKey('status', false, false);

        // Create table
        $this->forge->createTable('app__years');
    }

    public function down()
    {
        // Safe abstraction
    }
}
