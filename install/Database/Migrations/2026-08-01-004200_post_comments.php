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

class PostComments extends Migration
{
    public function up()
    {
        // Add columns to table
        $this->forge->addField([
            'comment_id' => [
                'type' => 'int',
                'unsigned' => true,
                'auto_increment' => true,
                'null' => false
            ],
            'post_id' => [
                'type' => 'int',
                'unsigned' => true,
                'null' => false
            ],
            'post_path' => [
                'type' => 'varchar',
                'constraint' => 2000,
                'null' => false
            ],
            'reply_id' => [
                'type' => 'int',
                'unsigned' => true,
                'null' => false
            ],
            'mention_id' => [
                'type' => 'int',
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
                'default' => '0',
                'null' => false
            ],
            'status' => [
                'type' => 'tinyint',
                'default' => '0',
                'null' => false
            ],
            'created_at' => [
                'type' => 'datetime',
                'null' => false
            ],
            'created_by' => [
                'type' => 'int',
                'unsigned' => true,
                'null' => false
            ],
            'updated_at' => [
                'type' => 'datetime',
                'null' => true
            ],
            'updated_by' => [
                'type' => 'int',
                'unsigned' => true,
                'null' => true
            ],
        ]);

        // Add primary and unique index
        $this->forge->addKey('comment_id', true, true);
        $this->forge->addKey('created_by');
        $this->forge->addKey('post_id');
        $this->forge->addKey('reply_id');
        $this->forge->addKey('mention_id');
        $this->forge->addKey('status');

        // Add foreign key to parent table
        $this->forge->addForeignKey('created_by', 'app_users', 'user_id', 'CASCADE', 'CASCADE');

        // Create table
        $this->forge->createTable('post_comments');
    }

    public function down()
    {
        // Safe abstraction
    }
}
