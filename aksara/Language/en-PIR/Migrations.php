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

return [
    // Migration Runner
    'missingTable' => 'Migrations table must be set.',
    'disabled' => 'Migrations have been loaded but be disabled or setup incorrectly.',
    'notFound' => 'Migration scroll not found: ',
    'batchNotFound' => 'Target batch not found: ',
    'empty' => 'No Migration scrolls found, arrr.',
    'gap' => 'There be a gap in the migration sequence near version number: ',
    'classNotFound' => 'The migration class "%s" could not be found, matey.',
    'missingMethod' => 'The migration class be missin\' an "%s" method.',
    'locked' => 'Migrations already runnin\' in another process. Skippin\'.',

    // Migration Command
    'migHelpLatest' => "\t\tMigrates database to latest available migration.",
    'migHelpCurrent' => "\t\tMigrates database to version set as 'current' in configuration.",
    'migHelpVersion' => "\tMigrates database to version {v}.",
    'migHelpRollback' => "\tRuns all migrations 'down' to version 0.",
    'migHelpRefresh' => "\t\tUninstalls and re-runs all migrations to freshen database.",
    'migHelpSeed' => "\tRuns the seeder named [name].",
    'migCreate' => "\tCreates a new migration named [name]",
    'nameMigration' => 'Name the migration scroll',
    'migNumberError' => 'Migration number must be three digits, and there must not be any gaps in the sequence.',
    'rollBackConfirm' => 'Are ye sure ye want to rollback?',
    'refreshConfirm' => 'Are ye sure ye want to refresh?',

    'latest' => 'Runnin\' all new migrations...',
    'generalFault' => 'Migration failed, shiver me timbers!',
    'migrated' => 'Migrations complete.',
    'migInvalidVersion' => 'Not valid version number provided.',
    'toVersionPH' => 'Migratin\' to version %s...',
    'toVersion' => 'Migratin\' to current version...',
    'rollingBack' => 'Rollin\' back migrations to batch: ',
    'noneFound' => 'No migrations were found.',
    'migSeeder' => 'Seeder name',
    'migMissingSeeder' => 'Ye must provide a seeder name, scallywag.',
    'nameSeeder' => 'Name the seeder scroll',
    'removed' => 'Rollin\' back: ',
    'added' => 'Runnin\': ',

    // Migrate Status
    'namespace' => 'Namespace',
    'filename' => 'Filename',
    'version' => 'Version',
    'group' => 'Group',
    'on' => 'Migrated On: ',
    'batch' => 'Batch',
];
