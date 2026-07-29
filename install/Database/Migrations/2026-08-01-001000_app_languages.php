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

class AppLanguages extends Migration
{
    public function up()
    {
        // Add columns to table
        $this->forge->addField([
            'id' => [
                'type' => 'int',
                'unsigned' => true,
                'auto_increment' => true,
                'null' => false
            ],
            'language' => [
                'type' => 'varchar',
                'constraint' => 32,
                'null' => false
            ],
            'code' => [
                'type' => 'varchar',
                'constraint' => 10,
                'null' => false
            ],
            'locale' => [
                'type' => 'varchar',
                'constraint' => 64,
                'null' => false
            ],
            'status' => [
                'type' => 'tinyint',
                'default' => '0',
                'null' => false
            ]
        ]);

        // Add primary and unique index
        $this->forge->addKey('id', true, true);
        $this->forge->addKey('status');

        // Create table
        $this->forge->createTable('app_languages');
    }

    public function down()
    {
        // Safe abstraction
    }
}
