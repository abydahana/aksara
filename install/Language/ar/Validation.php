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
    // Core Messages
    'noRuleSets' => 'لم يتم تحديد أي قواعد في إعدادات التحقق.',
    'ruleNotFound' => '{0} ليست قاعدة صالحة.',
    'groupNotFound' => '{0} ليست مجموعة قواعد تحقق صالحة.',
    'groupNotArray' => 'يجب أن تكون مجموعة قواعد {0} مصفوفة.',
    'invalidTemplate' => '{0} ليس قالب تحقق صالح.',

    // Rule Messages
    'alpha' => 'يجب أن يحتوي حقل {field} على أحرف أبجدية فقط.',
    'alpha_dash' => 'يجب أن يحتوي حقل {field} على أحرف وأرقام وشرطات وشرطات سفلية فقط.',
    'alpha_numeric' => 'يجب أن يحتوي حقل {field} على أحرف وأرقام فقط.',
    'alpha_numeric_punct' => 'يجب أن يحتوي حقل {field} على أحرف وأرقام ومسافات والرموز ~ ! # $ % & * - _ + = | : . فقط.',
    'alpha_numeric_space' => 'يجب أن يحتوي حقل {field} على أحرف وأرقام ومسافات فقط.',
    'alpha_space' => 'يجب أن يحتوي حقل {field} على أحرف أبجدية ومسافات فقط.',
    'decimal' => 'يجب أن يحتوي حقل {field} على رقم عشري.',
    'differs' => 'يجب أن يكون حقل {field} مختلفاً عن حقل {param}.',
    'equals' => 'يجب أن يكون حقل {field} مطابقاً تماماً لـ: {param}.',
    'exact_length' => 'يجب أن يكون طول حقل {field} بالضبط {param} حرفاً/أحرف.',
    'greater_than' => 'يجب أن يحتوي حقل {field} على رقم أكبر من {param}.',
    'greater_than_equal_to' => 'يجب أن يحتوي حقل {field} على رقم أكبر من أو يساوي {param}.',
    'hex' => 'يجب أن يحتوي حقل {field} على أحرف سداسية عشرية فقط.',
    'in_list' => 'يجب أن يكون حقل {field} واحداً مما يلي: {param}.',
    'integer' => 'يجب أن يحتوي حقل {field} على رقم صحيح.',
    'is_natural' => 'يجب أن يحتوي حقل {field} على أرقام فقط.',
    'is_natural_no_zero' => 'يجب أن يحتوي حقل {field} على أرقام فقط ويجب أن يكون أكبر من الصفر.',
    'is_not_unique' => 'يجب أن يحتوي حقل {field} على قيمة موجودة مسبقاً في قاعدة البيانات.',
    'is_unique' => 'يجب أن يحتوي حقل {field} على قيمة فريدة.',
    'less_than' => 'يجب أن يحتوي حقل {field} على رقم أقل من {param}.',
    'less_than_equal_to' => 'يجب أن يحتوي حقل {field} على رقم أقل من أو يساوي {param}.',
    'matches' => 'حقل {field} لا يتطابق مع حقل {param}.',
    'max_length' => 'لا يمكن أن يتجاوز حقل {field} طول {param} حرفاً/أحرف.',
    'min_length' => 'يجب أن يكون طول حقل {field} على الأقل {param} حرفاً/أحرف.',
    'not_equals' => 'لا يمكن أن يكون حقل {field}: {param}.',
    'not_in_list' => 'يجب ألا يكون حقل {field} واحداً مما يلي: {param}.',
    'numeric' => 'يجب أن يحتوي حقل {field} على أرقام فقط.',
    'regex_match' => 'حقل {field} ليس بالتنسيق الصحيح.',
    'required' => 'حقل {field} مطلوب.',
    'required_with' => 'حقل {field} مطلوب عندما يكون {param} موجوداً.',
    'required_without' => 'حقل {field} مطلوب عندما لا يكون {param} موجوداً.',
    'string' => 'يجب أن يكون حقل {field} سلسلة نصية صالحة.',
    'timezone' => 'يجب أن يكون حقل {field} منطقة زمنية صالحة.',
    'valid_base64' => 'يجب أن يكون حقل {field} سلسلة نصية base64 صالحة.',
    'valid_email' => 'يجب أن يحتوي حقل {field} على عنوان بريد إلكتروني صالح.',
    'valid_emails' => 'يجب أن يحتوي حقل {field} على جميع عناوين البريد الإلكتروني صالحة.',
    'valid_ip' => 'يجب أن يحتوي حقل {field} على IP صالح.',
    'valid_url' => 'يجب أن يحتوي حقل {field} على رابط URL صالح.',
    'valid_date' => 'يجب أن يحتوي حقل {field} على تاريخ صالح.',

    // Credit Cards
    'valid_cc_num' => 'لا يبدو أن {field} رقم بطاقة ائتمان صالح.',

    // Files
    'uploaded' => '{field} ليس ملفاً تم تحميله صالحاً.',
    'max_size' => 'الملف {field} كبير جداً.',
    'is_image' => '{field} ليس ملف صورة تم تحميله صالحاً.',
    'mime_in' => '{field} ليس لديه نوع mime صالح.',
    'ext_in' => '{field} ليس لديه امتداد ملف صالح.',
    'max_dims' => '{field} إما ليس صورة، أو أنه عريض جداً أو مرتفع جداً.',

    // Custom
    'unique' => 'تم تسجيل {field} المُدخل مسبقاً.',
    'boolean' => 'يمكن تحديد حقل {field} فقط أو تجاهله.',
    'currency' => 'يمكن تنسيق حقل {field} كعملة فقط.',
    'valid_year' => 'يجب أن يستخدم حقل {field} تنسيق السنة.',
    'valid_hex' => 'يجب أن يحتوي حقل {field} فقط على رمز لون بالنظام الست عشري.'
];
