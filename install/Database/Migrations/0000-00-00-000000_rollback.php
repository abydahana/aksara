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

class Rollback extends Migration
{
    public function up()
    {
        // Drop all table constraint (foreign key)
        foreach ($this->db->listTables() as $key => $val) {
            // Skip table deletion if not exists or it's come from migration
            if (config('migrations')->table == $val || ! $this->db->tableExists($val)) {
                continue;
            }

            // List foreign keys
            $foreignKeys = $this->db->getForeignKeyData($val);

            // Check if foreign key exists for current table
            if ($foreignKeys) {
                // Remove duplicate of constraint
                $foreignKeys = array_map('unserialize', array_unique(array_map('serialize', (array) $foreignKeys)));

                // Loops the foreign key
                foreach ($foreignKeys as $_key => $_val) {
                    // Continue if no constraint
                    if (! isset($_val->constraint_name)) {
                        continue;
                    }

                    // Since the method has no option to drop only when exist, use try catch to safe the migration runner
                    try {
                        // Drop foreign key
                        $this->forge->dropForeignKey($_val->table_name, $_val->constraint_name);
                    } catch (\Throwable $e) {
                        // Safe abstraction
                    }
                }
            }
        }

        // Drop tables
        foreach ($this->db->listTables() as $key => $val) {
            // Skip table deletion if not exists or it's come from migration
            if (config('migrations')->table == $val || ! $this->db->tableExists($val)) {
                continue;
            }

            // Drop table
            $this->forge->dropTable($val);
        }
    }

    public function down()
    {
        // Safe abstraction
    }
}
