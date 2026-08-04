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

class PostReports extends Migration
{
    public function up()
    {
        // Add columns to table
        $this->forge->addField([
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
            'message' => [
                'type' => 'text',
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
        ]);

        // Add primary and unique index
        $this->forge->addKey('created_by', true);
        $this->forge->addKey('post_id', true);

        // Add foreign key to parent table
        $this->forge->addForeignKey('created_by', 'app_users', 'user_id', 'CASCADE', 'CASCADE');

        // Create table
        $this->forge->createTable('post_reports');
    }

    public function down()
    {
        // Safe abstraction
    }
}
