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

class Videos extends Migration
{
    public function up()
    {
        // Add columns to table
        $this->forge->addField([
            'id' => [
                'type' => 'int',
                'constraint' => 11,
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
                'type' => 'text',
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
                'type' => 'bigint',
                'constraint' => 22,
                'unsigned' => true,
                'null' => false
            ],
            'featured' => [
                'type' => 'tinyint',
                'constraint' => 1,
                'default' => '0',
                'null' => false
            ],
            'timestamp' => [
                'type' => 'timestamp',
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

        // Add foreign key to parent table
        $this->forge->addForeignKey('author', 'app__users', 'user_id', 'CASCADE', 'CASCADE');

        // Create table
        $this->forge->createTable('videos');
    }

    public function down()
    {
        // Safe abstraction
    }
}
