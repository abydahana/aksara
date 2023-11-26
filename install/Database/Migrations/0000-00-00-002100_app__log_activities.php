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

class AppLogActivities extends Migration
{
    public function up()
    {
        // Add columns to table
        $this->forge->addField([
            'id' => [
                'type' => 'bigint',
                'constraint' => 22,
                'unsigned' => true,
                'auto_increment' => true,
                'null' => false
            ],
            'session_id' => [
                'type' => 'varchar',
                'constraint' => 128,
                'null' => false
            ],
            'user_id' => [
                'type' => 'bigint',
                'constraint' => 22,
                'unsigned' => true,
                'null' => false
            ],
            'path' => [
                'type' => 'varchar',
                'constraint' => 255,
                'null' => false
            ],
            'method' => [
                'type' => 'varchar',
                'constraint' => 255,
                'null' => false
            ],
            'query' => [
                'type' => 'text'
            ],
            'browser' => [
                'type' => 'varchar',
                'constraint' => 255,
                'null' => false
            ],
            'platform' => [
                'type' => 'varchar',
                'constraint' => 64,
                'null' => false
            ],
            'ip_address' => [
                'type' => 'varchar',
                'constraint' => 45,
                'null' => false
            ],
            'timestamp' => [
                'type' => 'timestamp',
                'null' => false
            ]
        ]);

        // Add primary and unique index
        $this->forge->addKey('id', true, true);

        // Add foreign key to parent table
        $this->forge->addForeignKey('user_id', 'app__users', 'user_id', ('SQLSRV' == $this->db->DBDriver ? 'NO ACTION' : 'CASCADE'), ('SQLSRV' == $this->db->DBDriver ? 'NO ACTION' : 'CASCADE'));

        // Create table
        $this->forge->createTable('app__log_activities');
    }

    public function down()
    {
        // Safe abstraction
    }
}
