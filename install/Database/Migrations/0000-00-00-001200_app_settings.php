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

class AppSettings extends Migration
{
    public function up()
    {
        // Add columns to table (Vertical Schema)
        $this->forge->addField([
            'key' => [
                'type' => 'varchar',
                'constraint' => 255,
                'null' => false
            ],
            'type' => [
                'type' => 'varchar',
                'constraint' => 32,
                'default' => 'varchar',
                'null' => false
            ],
            'value' => [
                'type' => 'text',
                'null' => false
            ]
        ]);

        $this->forge->addKey('key', true, true);

        // Create table
        $this->forge->createTable('app_settings');
    }

    public function down()
    {
        // Safe abstraction
    }
}
