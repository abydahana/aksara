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

class AppStats extends Migration
{
    public function up()
    {
        // Add columns to table
        $this->forge->addField([
            'daily_visits' => [
                'type' => 'int',
                'constraint' => 11,
                'null' => false,
                'default' => '0'
            ],
            'weekly_visits' => [
                'type' => 'int',
                'constraint' => 11,
                'null' => false,
                'default' => '0'
            ],
            'monthly_visits' => [
                'type' => 'int',
                'constraint' => 11,
                'null' => false,
                'default' => '0'
            ],
            'yearly_visits' => [
                'type' => 'int',
                'constraint' => 11,
                'null' => false,
                'default' => '0'
            ],
            'whole_visits' => [
                'type' => 'int',
                'constraint' => 11,
                'null' => false,
                'default' => '0'
            ],
            'last_daily_reset' => [
                'type' => 'date'
            ],
            'last_weekly_reset' => [
                'type' => 'date'
            ],
            'last_monthly_reset' => [
                'type' => 'date'
            ],
            'last_yearly_reset' => [
                'type' => 'date'
            ],
        ]);

        // Create table
        $this->forge->createTable('app__stats');
    }

    public function down()
    {
        // Safe abstraction
    }
}
