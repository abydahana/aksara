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
    'noRuleSets' => 'ไม่ได้กำหนดชุดกฎในการกำหนดค่าการตรวจสอบ',
    'ruleNotFound' => '{0} ไม่ใช่กฎที่ถูกต้อง',
    'groupNotFound' => '{0} ไม่ใช่กลุ่มกฎการตรวจสอบที่ถูกต้อง',
    'groupNotArray' => 'กลุ่มกฎ {0} ต้องเป็นอาร์เรย์',
    'invalidTemplate' => '{0} ไม่ใช่เทมเพลตการตรวจสอบที่ถูกต้อง',

    // Rule Messages
    'alpha' => 'ฟิลด์ {field} ต้องประกอบด้วยตัวอักษรเท่านั้น',
    'alpha_dash' => 'ฟิลด์ {field} ต้องประกอบด้วยตัวอักษรและตัวเลข ขีดล่าง และขีดกลางเท่านั้น',
    'alpha_numeric' => 'ฟิลด์ {field} ต้องประกอบด้วยตัวอักษรและตัวเลขเท่านั้น',
    'alpha_numeric_punct' => 'ฟิลด์ {field} ต้องประกอบด้วยตัวอักษรและตัวเลข ช่องว่าง และตัวอักษร ~ ! # $ % & * - _ + = | : . เท่านั้น',
    'alpha_numeric_space' => 'ฟิลด์ {field} ต้องประกอบด้วยตัวอักษรและตัวเลขและช่องว่างเท่านั้น',
    'alpha_space' => 'ฟิลด์ {field} ต้องประกอบด้วยตัวอักษรและช่องว่างเท่านั้น',
    'decimal' => 'ฟิลด์ {field} ต้องประกอบด้วยตัวเลขทศนิยม',
    'differs' => 'ฟิลด์ {field} ต้องแตกต่างจากฟิลด์ {param}',
    'equals' => 'ฟิลด์ {field} ต้องเหมือนกับ: {param} ทุกประการ',
    'exact_length' => 'ฟิลด์ {field} ต้องมีความยาว {param} ตัวอักษรพอดี',
    'greater_than' => 'ฟิลด์ {field} ต้องมีตัวเลขมากกว่า {param}',
    'greater_than_equal_to' => 'ฟิลด์ {field} ต้องมีตัวเลขมากกว่าหรือเท่ากับ {param}',
    'hex' => 'ฟิลด์ {field} ต้องประกอบด้วยตัวอักษรฐานสิบหกเท่านั้น',
    'in_list' => 'ฟิลด์ {field} ต้องเป็นหนึ่งใน: {param}',
    'integer' => 'ฟิลด์ {field} ต้องเป็นจำนวนเต็ม',
    'is_natural' => 'ฟิลด์ {field} ต้องประกอบด้วยตัวเลขเท่านั้น',
    'is_natural_no_zero' => 'ฟิลด์ {field} ต้องประกอบด้วยตัวเลขเท่านั้นและต้องมากกว่าศูนย์',
    'is_not_unique' => 'ฟิลด์ {field} ต้องมีค่าที่มีอยู่แล้วในฐานข้อมูล',
    'is_unique' => 'ฟิลด์ {field} ต้องมีค่าที่ไม่ซ้ำกัน',
    'less_than' => 'ฟิลด์ {field} ต้องมีตัวเลขที่น้อยกว่า {param}',
    'less_than_equal_to' => 'ฟิลด์ {field} ต้องมีตัวเลขที่น้อยกว่าหรือเท่ากับ {param}',
    'matches' => 'ฟิลด์ {field} ไม่ตรงกับฟิลด์ {param}',
    'max_length' => 'ฟิลด์ {field} ต้องมีความยาวไม่เกิน {param} ตัวอักษร',
    'min_length' => 'ฟิลด์ {field} ต้องมีความยาวอย่างน้อย {param} ตัวอักษร',
    'not_equals' => 'ฟิลด์ {field} ต้องไม่เป็น: {param}',
    'not_in_list' => 'ฟิลด์ {field} ต้องไม่เป็นหนึ่งใน: {param}',
    'numeric' => 'ฟิลด์ {field} ต้องประกอบด้วยตัวเลขเท่านั้น',
    'regex_match' => 'ฟิลด์ {field} ไม่อยู่ในรูปแบบที่ถูกต้อง',
    'required' => 'จำเป็นต้องระบุฟิลด์ {field}',
    'required_with' => 'จำเป็นต้องระบุฟิลด์ {field} เมื่อมี {param}',
    'required_without' => 'จำเป็นต้องระบุฟิลด์ {field} เมื่อไม่มี {param}',
    'string' => 'ฟิลด์ {field} ต้องเป็นสตริงที่ถูกต้อง',
    'timezone' => 'ฟิลด์ {field} ต้องเป็นเขตเวลาที่ถูกต้อง',
    'valid_base64' => 'ฟิลด์ {field} ต้องเป็นสตริง base64 ที่ถูกต้อง',
    'valid_email' => 'ฟิลด์ {field} ต้องมีที่อยู่อีเมลที่ถูกต้อง',
    'valid_emails' => 'ฟิลด์ {field} ต้องมีที่อยู่อีเมลที่ถูกต้องทั้งหมด',
    'valid_ip' => 'ฟิลด์ {field} ต้องมี IP ที่ถูกต้อง',
    'valid_url' => 'ฟิลด์ {field} ต้องมี URL ที่ถูกต้อง',
    'valid_date' => 'ฟิลด์ {field} ต้องมีวันที่ที่ถูกต้อง',

    // Credit Cards
    'valid_cc_num' => '{field} ดูเหมือนจะไม่ใช่หมายเลขบัตรเครดิตที่ถูกต้อง',

    // Files
    'uploaded' => '{field} ไม่ใช่ไฟล์ที่อัปโหลดที่ถูกต้อง',
    'max_size' => 'ไฟล์ {field} มีขนาดใหญ่เกินไป',
    'is_image' => '{field} ไม่ใช่ไฟล์รูปภาพที่อัปโหลดที่ถูกต้อง',
    'mime_in' => '{field} ไม่มีประเภท mime ที่ถูกต้อง',
    'ext_in' => '{field} ไม่มีนามสกุลไฟล์ที่ถูกต้อง',
    'max_dims' => '{field} ไม่ใช่รูปภาพ หรือกว้างหรือสูงเกินไป',

    // Custom
    'unique' => '{field} ที่ป้อนได้รับการลงทะเบียนแล้ว',
    'boolean' => 'ฟิลด์ {field} สามารถเลือกได้เพียงตรวจสอบหรือละเว้น',
    'currency' => 'ฟิลด์ {field} สามารถจัดรูปแบบเป็นสกุลเงินเท่านั้น',
    'valid_year' => 'ฟิลด์ {field} ต้องใช้รูปแบบปี',
    'valid_hex' => 'ฟิลด์ {field} ต้องประกอบด้วยรหัสสี hex เท่านั้น'
];
