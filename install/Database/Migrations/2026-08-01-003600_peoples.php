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

class Peoples extends Migration
{
    public function up()
    {
        // Add columns to table
        $this->forge->addField([
            'people_id' => [
                'type' => 'int',
                'unsigned' => true,
                'auto_increment' => true,
                'null' => false
            ],
            'first_name' => [
                'type' => 'varchar',
                'constraint' => 32,
                'null' => false
            ],
            'last_name' => [
                'type' => 'varchar',
                'constraint' => 32,
                'null' => false
            ],
            'people_slug' => [
                'type' => 'varchar',
                'constraint' => 255,
                'null' => false
            ],
            'position' => [
                'type' => 'varchar',
                'constraint' => 255,
                'null' => false
            ],
            'email' => [
                'type' => 'varchar',
                'constraint' => 64,
                'null' => false
            ],
            'mobile' => [
                'type' => 'varchar',
                'constraint' => 16,
                'null' => false
            ],
            'instagram' => [
                'type' => 'varchar',
                'constraint' => 255,
                'null' => false
            ],
            'facebook' => [
                'type' => 'varchar',
                'constraint' => 64,
                'null' => false
            ],
            'twitter' => [
                'type' => 'varchar',
                'constraint' => 64,
                'null' => false
            ],
            'biography' => [
                'type' => 'text',
                'null' => false
            ],
            'photo' => [
                'type' => 'varchar',
                'constraint' => 255,
                'null' => false
            ],
            'status' => [
                'type' => 'tinyint',
                'default' => '0',
                'null' => false
            ],
            'created_at' => [
                'type' => 'datetime',
                'null' => true
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
        $this->forge->addKey('people_id', true, true);
        $this->forge->addKey('status');

        $this->forge->addKey('created_by');
        $this->forge->addKey('updated_by');

        $this->forge->addForeignKey('created_by', 'app_users', 'user_id', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('updated_by', 'app_users', 'user_id', 'CASCADE', 'SET NULL');

        $this->forge->createTable('peoples');
    }

    public function down()
    {
        // Safe abstraction
    }
}
