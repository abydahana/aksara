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

class Inquiries extends Migration
{
    public function up()
    {
        // Add columns to table
        $this->forge->addField([
            'id' => [
                'type' => 'bigint',
                'constraint' => 22,
                'unsigned' => true,
                'auto_increment' => true,
                'null' => false
            ],
            'sender_full_name' => [
                'type' => 'varchar',
                'constraint' => 64,
                'null' => false
            ],
            'sender_email' => [
                'type' => 'varchar',
                'constraint' => 255,
                'null' => false
            ],
            'subject' => [
                'type' => 'varchar',
                'constraint' => 255,
                'null' => false
            ],
            'messages' => [
                'type' => 'text',
                'null' => false
            ],
            'timestamp' => [
                'type' => 'timestamp',
                'null' => false
            ]
        ]);

        // Add primary and unique index
        $this->forge->addKey('id', true, true);

        // Create table
        $this->forge->createTable('inquiries');
    }

    public function down()
    {
        // Safe abstraction
    }
}
