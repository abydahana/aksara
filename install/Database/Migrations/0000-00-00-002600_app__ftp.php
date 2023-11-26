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

class AppFtp extends Migration
{
    public function up()
    {
        // Add columns table
        $this->forge->addField([
            'site_id' => [
                'type' => 'int',
                'constraint' => 11,
                'unsigned' => true,
                'null' => false
            ],
            'hostname' => [
                'type' => 'varchar',
                'constraint' => 255,
                'null' => false
            ],
            'port' => [
                'type' => 'int',
                'constraint' => 5,
                'null' => false
            ],
            'username' => [
                'type' => 'varchar',
                'constraint' => 512,
                'null' => false
            ],
            'password' => [
                'type' => 'varchar',
                'constraint' => 512,
                'null' => false
            ]
        ]);

        // Add primary and unique index
        $this->forge->addKey('site_id', true, true);

        // Add foreign key to parent table
        $this->forge->addForeignKey('site_id', 'app__settings', 'id', ('SQLSRV' == $this->db->DBDriver ? 'NO ACTION' : 'CASCADE'), ('SQLSRV' == $this->db->DBDriver ? 'NO ACTION' : 'CASCADE'));

        // Create table
        $this->forge->createTable('app__ftp');
    }

    public function down()
    {
        // Safe abstraction
    }
}
