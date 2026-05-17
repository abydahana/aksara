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

declare(strict_types=1);

/**
 * This file is part of CodeIgniter 4 framework.
 *
 * (c) CodeIgniter Foundation <admin@codeigniter.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

// Migration language settings
return [
    // Migration Runner
    'missingTable' => 'يجب تعيين جدول الترحيلات (Migrations table).',
    'disabled' => 'تم تحميل الترحيلات ولكنها معطلة أو تم إعدادها بشكل غير صحيح.',
    'notFound' => 'ملف الترحيل غير موجود: ',
    'batchNotFound' => 'الدفعة المستهدفة غير موجودة: ',
    'empty' => 'لم يتم العثور على ملفات ترحيل',
    'gap' => 'هناك فجوة في تسلسل الترحيل بالقرب من رقم الإصدار: ',
    'classNotFound' => 'تعذر العثور على فئة الترحيل "%s".',
    'missingMethod' => 'تفتقد فئة الترحيل إلى الطريقة "%s".',
    'locked' => 'الترحيلات قيد التشغيل بالفعل في عملية أخرى. تخطي.',

    // Migration Command
    'migHelpLatest' => "\t\tيقوم بترحيل قاعدة البيانات إلى أحدث ترحيل متاح.",
    'migHelpCurrent' => "\t\tيقوم بترحيل قاعدة البيانات إلى الإصدار المحدد كـ 'current' في التكوين.",
    'migHelpVersion' => "\tيقوم بترحيل قاعدة البيانات إلى الإصدار {v}.",
    'migHelpRollback' => "\tيقوم بتشغيل جميع الترحيلات 'للأسفل' (down) حتى الإصدار 0.",
    'migHelpRefresh' => "\t\tيقوم بإلغاء تثبيت وتشغيل جميع الترحيلات لتحديث قاعدة البيانات.",
    'migHelpSeed' => "\tيقوم بتشغيل الباذر (Seeder) المسمى [name].",
    'migCreate' => "\tيقوم بإنشاء ترحيل جديد باسم [name]",
    'nameMigration' => 'سمّ ملف الترحيل',
    'migNumberError' => 'رقم الترحيل يجب أن يتكون من ثلاثة أرقام، ويجب ألا تكون هناك فجوات في التسلسل.',
    'rollBackConfirm' => 'هل أنت متأكد أنك تريد التراجع (Rollback)؟',
    'refreshConfirm' => 'هل أنت متأكد أنك تريد التحديث (Refresh)؟',

    'latest' => 'تشغيل جميع الترحيلات الجديدة...',
    'generalFault' => 'فشل الترحيل!',
    'migrated' => 'اكتملت الترحيلات.',
    'migInvalidVersion' => 'رقم الإصدار المقدم غير صالح.',
    'toVersionPH' => 'جاري الترحيل إلى الإصدار %s...',
    'toVersion' => 'جاري الترحيل إلى الإصدار الحالي...',
    'rollingBack' => 'جاري التراجع عن الترحيلات إلى الدفعة: ',
    'noneFound' => 'لم يتم العثور على أي ترحيلات.',
    'migSeeder' => 'اسم الباذر (Seeder name)',
    'migMissingSeeder' => 'يجب عليك تقديم اسم للباذر.',
    'nameSeeder' => 'سمّ ملف الباذر',
    'removed' => 'جاري التراجع: ',
    'added' => 'جاري التشغيل: ',

    // Migrate Status
    'namespace' => 'مساحة الأسماء',
    'filename' => 'اسم الملف',
    'version' => 'الإصدار',
    'group' => 'المجموعة',
    'on' => 'تم الترحيل في: ',
    'batch' => 'الدفعة',
];
