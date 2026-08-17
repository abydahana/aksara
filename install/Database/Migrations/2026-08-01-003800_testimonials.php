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

class Testimonials extends Migration
{
    public function up()
    {
        // Add columns to table
        $this->forge->addField([
            'testimonial_id' => [
                'type' => 'int',
                'unsigned' => true,
                'auto_increment' => true,
                'null' => false
            ],
            'photo' => [
                'type' => 'varchar',
                'constraint' => 255,
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
            'testimonial_content' => [
                'type' => 'varchar',
                'constraint' => 255,
                'null' => false
            ],
            'rating' => [
                'type' => 'tinyint',
                'unsigned' => true,
                'default' => 5,
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
                'null' => true
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
        $this->forge->addKey('testimonial_id', true, true);
        $this->forge->addKey('status');

        // Create table
        $this->forge->addKey('created_by');
        $this->forge->addKey('updated_by');

        $this->forge->addForeignKey('created_by', 'app_users', 'user_id', 'CASCADE', 'SET NULL');
        $this->forge->addForeignKey('updated_by', 'app_users', 'user_id', 'CASCADE', 'SET NULL');

        $this->forge->createTable('testimonials');
    }

    public function down()
    {
        // Safe abstraction
    }
}
