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

class Pages extends Migration
{
    public function up()
    {
        // Add columns to table
        $this->forge->addField([
            'page_id' => [
                'type' => 'bigint',
                'constraint' => 22,
                'unsigned' => true,
                'auto_increment' => true,
                'null' => false
            ],
            'page_title' => [
                'type' => 'varchar',
                'constraint' => 255,
                'null' => false
            ],
            'page_slug' => [
                'type' => 'varchar',
                'constraint' => 255,
                'null' => false
            ],
            'page_description' => [
                'type' => ('MySQLi' === $this->db->DBDriver ? 'tinytext' : 'text'),
                'null' => false
            ],
            'page_content' => [
                'type' => (in_array($this->db->DBDriver, ['Postgre', 'MySQLi']) ? 'json' : 'text'),
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
            'author' => [
                'type' => 'bigint',
                'constraint' => 22,
                'unsigned' => true,
                'null' => false
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
        $this->forge->addKey('page_id', true, true);

        // Add foreign key to parent table
        $this->forge->addForeignKey('author', 'app_users', 'user_id', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('language_id', 'app_languages', 'id', 'CASCADE', 'RESTRICT');

        // Add index for status column
        $this->forge->addKey('status', false, false);

        // Create table
        $this->forge->createTable('pages');
    }

    public function down()
    {
        // Safe abstraction
    }
}
