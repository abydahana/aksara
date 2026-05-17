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
    'missingDatabaseTable' => 'Session: "savePath" must have the table name for the Database Session Handler to work.',
    'invalidSavePath' => 'Session: Configured save path "{0}" be not a directory, does not exist or cannot be created.',
    'writeProtectedSavePath' => 'Session: Configured save path "{0}" be not writable by the PHP process, shiver me timbers.',
    'emptySavePath' => 'Session: No save path configured.',
    'invalidSavePathFormat' => 'Session: Not valid Redis save path format, matey: "{0}"',

    // @deprecated
    'invalidSameSiteSetting' => 'Session: The SameSite settin\' must be None, Lax, Strict, or a blank string. Given: "{0}"',
];
