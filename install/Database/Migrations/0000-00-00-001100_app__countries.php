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

class AppCountries extends Migration
{
    public function up()
    {
        // Add column to table
        $this->forge->addField([
            'id' => [
                'type' => 'int',
                'constraint' => 5,
                'unsigned' => true,
                'auto_increment' => true,
                'null' => false
            ],
            'code' => [
                'type' => 'varchar',
                'constraint' => 8,
                'null' => false
            ],
            'country' => [
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

        // Create table
        $this->forge->createTable('app__countries');
    }

    public function down()
    {
        // Safe abstraction
    }
}
