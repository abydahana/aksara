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
    'noRuleSets' => '驗證配置中沒有指定規則集。',
    'ruleNotFound' => '{0} 不是一個有效的規則。',
    'groupNotFound' => '{0} 不是一個驗證規則組。',
    'groupNotArray' => '{0} 規則組必須是一個數組。',
    'invalidTemplate' => '{0} 不是一個有效的驗證模板。',

    // Rule Messages
    'alpha' => '{field} 欄位只能包含字母。',
    'alpha_dash' => '{field} 欄位只能包含字母數字、下劃線和破折號。',
    'alpha_numeric' => '{field} 欄位只能包含字母數字。',
    'alpha_numeric_punct' => '{field} 欄位只能包含字母數字、空格以及 ~ ! # $ % & * - _ + = | : . 字元。',
    'alpha_numeric_space' => '{field} 欄位只能包含字母數字和空格。',
    'alpha_space' => '{field} 欄位只能包含字母和空格。',
    'decimal' => '{field} 欄位必須包含一個十進位制數字。',
    'differs' => '{field} 欄位必須與 {param} 欄位不同。',
    'equals' => '{field} 欄位必須與 {param} 完全一致。',
    'exact_length' => '{field} 欄位的長度必須恰好為 {param} 個字元。',
    'greater_than' => '{field} 欄位必須包含一個大於 {param} 的數字。',
    'greater_than_equal_to' => '{field} 欄位必須包含一個大於或等於 {param} 的數字。',
    'hex' => '{field} 欄位只能包含十六進位制字元。',
    'in_list' => '{field} 欄位必須是以下值之一：{param}。',
    'integer' => '{field} 欄位必須包含一個整數。',
    'is_natural' => '{field} 欄位只能包含數字。',
    'is_natural_no_zero' => '{field} 欄位只能包含數字並且必須大於零。',
    'is_not_unique' => '{field} 欄位必須包含資料庫中已存在的值。',
    'is_unique' => '{field} 欄位必須包含一個唯一的值。',
    'less_than' => '{field} 欄位必須包含一個小於 {param} 的數字。',
    'less_than_equal_to' => '{field} 欄位必須包含一個小於或等於 {param} 的數字。',
    'matches' => '{field} 欄位與 {param} 欄位不匹配。',
    'max_length' => '{field} 欄位的長度不能超過 {param} 個字元。',
    'min_length' => '{field} 欄位的長度必須至少為 {param} 個字元。',
    'not_equals' => '{field} 欄位不能等於：{param}。',
    'not_in_list' => '{field} 欄位不能是以下值之一：{param}。',
    'numeric' => '{field} 欄位只能包含數字。',
    'regex_match' => '{field} 欄位的格式不正確。',
    'required' => '{field} 欄位是必需的。',
    'required_with' => '當 {param} 存在時，{field} 欄位是必需的。',
    'required_without' => '當 {param} 不存在時，{field} 欄位是必需的。',
    'string' => '{field} 欄位必須是一個有效的字串。',
    'timezone' => '{field} 欄位必須是一個有效的時區。',
    'valid_base64' => '{field} 欄位必須是一個有效的 base64 字串。',
    'valid_email' => '{field} 欄位必須包含一個有效的電子郵件地址。',
    'valid_emails' => '{field} 欄位必須包含所有有效的電子郵件地址。',
    'valid_ip' => '{field} 欄位必須包含一個有效的 IP。',
    'valid_url' => '{field} 欄位必須包含一個有效的 URL。',
    'valid_date' => '{field} 欄位必須包含一個有效的日期。',

    // Credit Cards
    'valid_cc_num' => '{field} 似乎不是一個有效的信用卡號碼。',

    // Files
    'uploaded' => '{field} 不是一個有效的上傳檔案。',
    'max_size' => '{field} 檔案太大。',
    'is_image' => '{field} 不是一個有效的已上傳影像檔案。',
    'mime_in' => '{field} 沒有有效的 mime 型別。',
    'ext_in' => '{field} 沒有有效的副檔名。',
    'max_dims' => '{field} 不是影像，或者太寬/太高。',

    // Custom
    'unique' => '輸入的 {field} 已經被註冊。',
    'boolean' => '{field} 欄位只能被選中或忽略。',
    'currency' => '{field} 欄位只能格式化為貨幣。',
    'valid_year' => '{field} 欄位必須使用年份格式。',
    'valid_hex' => '{field} 欄位只能包含十六進位制顏色程式碼。'
];
