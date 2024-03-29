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

// Migration language settings
return [
    // Migration Runner
    'missingTable' => 'Tabel migrasi harus diatur.',
    'disabled' => 'Migrasi telah dimuat tetapi dinonaktifkan atau pengaturan salah.',
    'notFound' => 'Berkas migrasi tidak ditemukan: ',
    'batchNotFound' => 'Sejumlah target tidak ditemukan: ',
    'empty' => 'Tidak ditemukan berkas Migrasi',
    'gap' => 'Ada sebuah celah dalam urutan migrasi dekat nomor versi: ',
    'classNotFound' => 'Kelas migrasi "%s" tidak dapat ditemukan.',
    'missingMethod' => 'Kelas migrasi kehilangan metode "%s".',

    // Migration Command
    'migHelpLatest' => "\t\tMigrasi basis data ke migrasi terbaru yang tersedia.",
    'migHelpCurrent' => "\t\tMigrasi basis data ke versi yang ditetapkan sebagai 'saat ini' dalam konfigurasi.",
    'migHelpVersion' => "\tMigrasi basis data ke versi {v}.",
    'migHelpRollback' => "\tMenjalankan semua migrasi 'turun' ke versi 0.",
    'migHelpRefresh' => "\t\tUninstal dan jalankan kembali semua migrasi untuk menyegarkan basis data.",
    'migHelpSeed' => "\tMenjalankan seeder bernama [name].",
    'migCreate' => "\tMenciptakan sebuah nama migrasi baru bernama [name]",
    'nameMigration' => 'Beri nama berkas migrasi',
    'migNumberError' => 'Nomor migrasi harus tiga digit dan tidak boleh ada jarak pada urutan.',
    'rollBackConfirm' => 'Anda yakin ingin melakukan rollback?',
    'refreshConfirm' => 'Anda yakin ingin menyegarkan?',

    'latest' => 'Menjalankan semua migrasi baru...',
    'generalFault' => 'Migrasi gagal!',
    'migInvalidVersion' => 'Nomor versi tidak valid diberikan.',
    'toVersionPH' => 'Migrasi ke versi %s...',
    'toVersion' => 'Migrasi ke versi saat ini...',
    'rollingBack' => 'Mengembalikan semua migrasi...',
    'noneFound' => 'Tidak ada migrasi yang ditemukan.',
    'migSeeder' => 'Nama seeder',
    'migMissingSeeder' => 'Anda harus memberikan sebuah nama seeder.',
    'nameSeeder' => 'Beri nama berkas seeder',
    'removed' => 'Mengembalikan: ',
    'added' => 'Berjalan: ',

    // Migrate Status
    'namespace' => 'Namespace',
    'filename' => 'Nama berkas',
    'version' => 'Versi',
    'group' => 'Grup',
    'on' => 'Dimigrasi Pada: ',
    'batch' => 'Batch',
];
