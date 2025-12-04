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

class PostComments extends Migration
{
    public function up()
    {
        // Add columns to table
        $this->forge->addField([
            'comment_id' => [
                'type' => 'bigint',
                'constraint' => 22,
                'unsigned' => true,
                'auto_increment' => true,
                'null' => false
            ],
            'user_id' => [
                'type' => 'bigint',
                'constraint' => 22,
                'unsigned' => true,
                'null' => false
            ],
            'post_id' => [
                'type' => 'bigint',
                'constraint' => 22,
                'unsigned' => true,
                'null' => false
            ],
            'post_path' => [
                'type' => 'varchar',
                'constraint' => 2000,
                'null' => false
            ],
            'reply_id' => [
                'type' => 'bigint',
                'constraint' => 22,
                'unsigned' => true,
                'null' => false
            ],
            'mention_id' => [
                'type' => 'bigint',
                'constraint' => 22,
                'unsigned' => true,
                'null' => false
            ],
            'comments' => [
                'type' => 'text',
                'null' => false
            ],
            'attachment' => [
                'type' => 'varchar',
                'constraint' => 255,
                'null' => false
            ],
            'edited' => [
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
        $this->forge->addKey('comment_id', true, true);
        $this->forge->addKey('user_id', false, false);
        $this->forge->addKey('post_id', false, false);
        $this->forge->addKey('reply_id', false, false);
        $this->forge->addKey('mention_id', false, false);

        // Add foreign key to parent table
        $this->forge->addForeignKey('user_id', 'app__users', 'user_id', 'CASCADE', 'CASCADE');

        // Create table
        $this->forge->createTable('post__comments');
    }

    public function down()
    {
        // Safe abstraction
    }
}
