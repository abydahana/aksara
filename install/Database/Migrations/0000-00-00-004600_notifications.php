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

class AppNotifications extends Migration
{
    public function up()
    {
        // Add columns to table
        $this->forge->addField([
            'from_user' => [
                'type' => 'bigint',
                'constraint' => 22,
                'unsigned' => true,
                'null' => false
            ],
            'to_user' => [
                'type' => 'bigint',
                'constraint' => 22,
                'unsigned' => true,
                'null' => false
            ],
            'type' => [
                'type' => 'varchar',
                'constraint' => 32,
                'null' => false
            ],
            'interaction_id' => [
                'type' => 'bigint',
                'constraint' => 22,
                'unsigned' => true,
                'null' => false
            ],
            'path' => [
                'type' => 'varchar',
                'constraint' => 2000,
                'null' => false
            ],
            'timestamp' => [
                'type' => 'timestamp',
                'null' => false
            ],
            'opened' => [
                'type' => 'tinyint',
                'constraint' => 1,
                'default' => '0'
            ]
        ]);

        // Add primary and unique index
        $this->forge->addKey('from_user', false, false);
        $this->forge->addKey('to_user', false, false);

        // Add foreign key to parent table
        $this->forge->addForeignKey('from_user', 'app__users', 'user_id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('to_user', 'app__users', 'user_id', 'CASCADE', 'CASCADE');

        // Create table
        $this->forge->createTable('notifications');
    }

    public function down()
    {
        // Safe abstraction
    }
}
