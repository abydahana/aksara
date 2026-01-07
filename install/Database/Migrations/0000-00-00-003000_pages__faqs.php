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

class PagesFaqs extends Migration
{
    public function up()
    {
        // Add columns to table
        $this->forge->addField([
            'faq_id' => [
                'type' => 'int',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
                'null' => false
            ],
            'faq_title' => [
                'type' => 'varchar',
                'constraint' => 64,
                'null' => false
            ],
            'faq_description' => [
                'type' => 'text',
                'null' => false
            ],
            'faq_content' => [
                'type' => (in_array($this->db->DBDriver, ['Postgre', 'SQLSRV'], true) ? 'text' : 'longtext'),
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
        $this->forge->addKey('faq_id', true, true);

        // Add foreign key to parent table
        $this->forge->addForeignKey('language_id', 'app__languages', 'id', 'CASCADE', 'RESTRICT');

        // Add index for status column
        $this->forge->addKey('status', false, false);

        // Create table
        $this->forge->createTable('pages__faqs');
    }

    public function down()
    {
        // Safe abstraction
    }
}
