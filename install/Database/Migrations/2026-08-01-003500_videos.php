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

class Videos extends Migration
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
            'cover' => [
                'type' => 'varchar',
                'constraint' => 255,
                'null' => false
            ],
            'title' => [
                'type' => 'varchar',
                'constraint' => 128,
                'null' => false
            ],
            'description' => [
                'type' => ('MySQLi' === $this->db->DBDriver ? 'tinytext' : 'text'),
                'null' => false
            ],
            'slug' => [
                'type' => 'varchar',
                'constraint' => 255,
                'null' => false
            ],
            'video_url' => [
                'type' => 'varchar',
                'constraint' => 255,
                'null' => false
            ],
            'author' => [
                'type' => 'int',
                'unsigned' => true,
                'null' => false
            ],
            'featured' => [
                'type' => 'tinyint',
                'default' => '0',
                'null' => false
            ],
            'status' => [
                'type' => 'tinyint',
                'default' => '0',
                'null' => false
            ],
            'created_timestamp' => [
                'type' => 'timestamp',
                'null' => false
            ],
            'updated_timestamp' => [
                'type' => 'timestamp',
                'null' => true
            ]
        ]);

        // Add primary and unique index
        $this->forge->addKey('id', true, true);
        $this->forge->addKey('status');

        // Add foreign key to parent table
        $this->forge->addForeignKey('author', 'app_users', 'user_id', 'CASCADE', 'CASCADE');

        // Create table
        $this->forge->createTable('videos');
    }

    public function down()
    {
        // Safe abstraction
    }
}
