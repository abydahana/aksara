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

// Session language settings
return [
    'missingDatabaseTable' => '`sessionSavePath` harus memiliki nama tabel untuk Handler Sesi Basis Data agar bekerja.',
    'invalidSavePath' => 'Sesi: Jalur penyimpanan terkonfigurasi "{0}" bukan sebuah direktori, tidak ada atau tidak dapat dibuat.',
    'writeProtectedSavePath' => 'Sesi: Jalur penyimpanan terkonfigurasi "{0}" tidak dapat ditulis oleh proses PHP.',
    'emptySavePath' => 'Sesi: Tidak ada jalur penyimpanan yang dikonfigurasi.',
    'invalidSavePathFormat' => 'Sesi: Format jalur penyimpanan Redis tidak valid: {0}',
    'invalidSameSiteSetting' => 'Sesi: Pengaturan SameSite harus ada, Lax, Strict, atau string kosong. Diberikan: {0}',
];
