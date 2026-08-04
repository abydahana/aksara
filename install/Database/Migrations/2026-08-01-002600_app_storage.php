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

class AppStorage extends Migration
{
    public function up()
    {
        // Add columns to table
        $this->forge->addField([
            'provider' => [
                'type' => 'varchar',
                'constraint' => 32,
                'default' => 's3',
                'null' => false
            ],
            'endpoint' => [
                'type' => 'varchar',
                'constraint' => 255,
                'null' => false
            ],
            'region' => [
                'type' => 'varchar',
                'constraint' => 64,
                'null' => false
            ],
            'bucket' => [
                'type' => 'varchar',
                'constraint' => 128,
                'null' => false
            ],
            'access_key' => [
                'type' => 'varchar',
                'constraint' => 512,
                'null' => false
            ],
            'secret_key' => [
                'type' => 'varchar',
                'constraint' => 512,
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
        $this->forge->addKey('provider', true, true);
        $this->forge->addKey('status');

        $this->forge->addKey('created_by');
        $this->forge->addKey('updated_by');

        $this->forge->addForeignKey('created_by', 'app_users', 'user_id', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('updated_by', 'app_users', 'user_id', 'CASCADE', 'SET NULL');

        $this->forge->createTable('app_storage');
    }

    public function down()
    {
        // Safe abstraction
    }
}
