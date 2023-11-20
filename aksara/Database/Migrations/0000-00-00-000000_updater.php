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

namespace Aksara\Database\Migrations;

use CodeIgniter\Database\Migration;

class Updater extends Migration
{
    public function up()
    {
        // Modify or create new table properties
        $fields = [
            'session_id' => [
                'type' => 'VARCHAR',
                'constraint' => 128,
                'after' => 'user_id'
            ]
        ];

        $forge->addColumn('app__activity_logs', $fields);

        $this->forge->addField(
            [
                'id' => [
                    'type' => 'int',
                    'constraint' => 11,
                    'unsigned' => true,
                    'auto_increment' => true,
                    'null' => false
                ],
                'phone' => [
                    'type' => 'varchar',
                    'constraint' => 14,
                    'null' => false
                ],
                'email' => [
                    'type' => 'varchar',
                    'constraint' => 128,
                    'null' => false
                ],
                'title' => [
                    'type' => 'varchar',
                    'constraint' => 128,
                    'null' => false
                ],
                'message' => [
                    'type' => 'text',
                    'null' => false
                ],
                'timestamp' => [
                    'type' => 'timestamp',
                    'null' => false
                ],
                'status' => [
                    'type' => 'tinyint',
                    'constraint' => 1,
                    'default' => '0',
                    'null' => false
                ]
            ]
        );
        $this->forge->addKey('id', true, true);

        $this->forge->createTable('app__notifier');
    }

    public function down()
    {
        // Drop unused tables properties
    }
}
