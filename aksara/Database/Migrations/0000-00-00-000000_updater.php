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
        // Preserved for upgrade
        $this->forge->addColumn('app__settings', [
            'account_age_restriction' => [
                'type' => 'smallint',
                'constraint' => 3,
                'default' => '0',
                'after' => 'one_device_login'
            ],
            'spam_timer' => [
                'type' => 'smallint',
                'constraint' => 5,
                'default' => '0',
                'after' => 'account_age_restriction'
            ]
        ]);
    }

    public function down()
    {
        // Preserved for downgrade
    }
}
