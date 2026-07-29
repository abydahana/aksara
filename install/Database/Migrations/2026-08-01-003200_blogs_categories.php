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

class BlogsCategories extends Migration
{
    public function up()
    {
        // Add columns to table
        $this->forge->addField([
            'category_id' => [
                'type' => 'int',
                'unsigned' => true,
                'auto_increment' => true,
                'null' => false
            ],
            'category_title' => [
                'type' => 'varchar',
                'constraint' => 32,
                'null' => false
            ],
            'category_slug' => [
                'type' => 'varchar',
                'constraint' => 32,
                'null' => false
            ],
            'category_description' => [
                'type' => ('MySQLi' === $this->db->DBDriver ? 'tinytext' : 'text'),
                'null' => false
            ],
            'category_image' => [
                'type' => 'varchar',
                'constraint' => 255,
                'null' => false
            ],
            'language_id' => [
                'type' => 'int',
                'unsigned' => true,
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
        $this->forge->addKey('category_id', true, true);
        $this->forge->addKey('status');

        // Add foreign key to parent table
        $this->forge->addForeignKey('language_id', 'app_languages', 'id', 'CASCADE', 'RESTRICT');

        // Create table
        $this->forge->createTable('blogs_categories');
    }

    public function down()
    {
        // Safe abstraction
    }
}
