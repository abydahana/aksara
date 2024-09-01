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

class Announcements extends Migration
{
    public function up()
    {
        // Add columns to table
        $this->forge->addField([
            'announcement_id' => [
                'type' => 'bigint',
                'constraint' => 22,
                'unsigned' => true,
                'auto_increment' => true,
                'null' => false
            ],
            'title' => [
                'type' => 'varchar',
                'constraint' => 255,
                'null' => false
            ],
            'announcement_slug' => [
                'type' => 'varchar',
                'constraint' => 255,
                'null' => false
            ],
            'content' => [
                'type' => 'text',
                'null' => false
            ],
            'cover' => [
                'type' => 'varchar',
                'constraint' => 255,
                'null' => false
            ],
            'placement' => [
                'type' => 'tinyint',
                'constraint' => 1,
                'default' => '0',
                'null' => false
            ],
            'start_date' => [
                'type' => 'date',
                'null' => false
            ],
            'end_date' => [
                'type' => 'date',
                'null' => false
            ],
            'created_timestamp' => [
                'type' => 'timestamp',
                'null' => false
            ],
            'updated_timestamp' => [
                'type' => 'timestamp',
                'null' => true
            ],
            'language_id' => [
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
        $this->forge->addKey('announcement_id', true, true);

        // Add foreign key to parent table
        $this->forge->addForeignKey('language_id', 'app__languages', 'id', 'CASCADE', 'RESTRICT');

        // Create table
        $this->forge->createTable('announcements');
    }

    public function down()
    {
        // Safe abstraction
    }
}
