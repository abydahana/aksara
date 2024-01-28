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
        // Add columns to global setting's table
        $this->forge->addColumn('app__settings', [
            'smtp_hostname' => [
                'type' => 'varchar'
            ],
            'smtp_port' => [
                'type' => 'int',
                'constraint' => 6
            ],
            'smtp_username' => [
                'type' => 'varchar'
            ],
            'smtp_password' => [
                'type' => 'varchar'
            ]
        ]);

        // Add columns to global notification's table
        $this->forge->addColumn('app__settings', [
            'whatsapp_api_url' => [
                'type' => 'varchar'
            ],
            'whatsapp_api_header' => [
                'type' => 'text'
            ],
            'whatsapp_api_payload' => [
                'type' => 'text'
            ]
        ]);
    }

    public function down()
    {
        // Drop unused columns from settings table
        $this->forge->dropColumn('notifications__settings', ['whatsapp_api_key', 'smtp_hostname', 'smtp_port', 'smtp_usernane', 'smtp_password']);
    }
}
