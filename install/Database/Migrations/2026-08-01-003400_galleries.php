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

class Galleries extends Migration
{
    public function up()
    {
        // Add columns to table
        $this->forge->addField([
            'gallery_id' => [
                'type' => 'int',
                'unsigned' => true,
                'auto_increment' => true,
                'null' => false
            ],
            'gallery_images' => [
                'type' => (in_array($this->db->DBDriver, ['Postgre', 'MySQLi']) ? 'json' : 'text'),
                'null' => false
            ],
            'gallery_title' => [
                'type' => 'varchar',
                'constraint' => 255,
                'null' => false
            ],
            'gallery_slug' => [
                'type' => 'varchar',
                'constraint' => 255,
                'null' => false
            ],
            'gallery_description' => [
                'type' => ('MySQLi' === $this->db->DBDriver ? 'tinytext' : 'text'),
                'null' => false
            ],
            'gallery_attributes' => [
                'type' => (in_array($this->db->DBDriver, ['Postgre', 'MySQLi']) ? 'json' : 'text'),
                'null' => false
            ],
            'gallery_tags' => [
                'type' => ('MySQLi' === $this->db->DBDriver ? 'tinytext' : 'text'),
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
        $this->forge->addKey('gallery_id', true, true);
        $this->forge->addKey('status');

        // Create table
        $this->forge->addKey('created_by');
        $this->forge->addKey('updated_by');

        $this->forge->addForeignKey('created_by', 'app_users', 'user_id', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('updated_by', 'app_users', 'user_id', 'CASCADE', 'SET NULL');

        $this->forge->createTable('galleries');
    }

    public function down()
    {
        // Safe abstraction
    }
}
