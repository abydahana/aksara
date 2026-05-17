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
    'noRuleSets' => '验证配置中没有指定规则集。',
    'ruleNotFound' => '{0} 不是一个有效的规则。',
    'groupNotFound' => '{0} 不是一个验证规则组。',
    'groupNotArray' => '{0} 规则组必须是一个数组。',
    'invalidTemplate' => '{0} 不是一个有效的验证模板。',

    // Rule Messages
    'alpha' => '{field} 字段只能包含字母。',
    'alpha_dash' => '{field} 字段只能包含字母数字、下划线和破折号。',
    'alpha_numeric' => '{field} 字段只能包含字母数字。',
    'alpha_numeric_punct' => '{field} 字段只能包含字母数字、空格以及 ~ ! # $ % & * - _ + = | : . 字符。',
    'alpha_numeric_space' => '{field} 字段只能包含字母数字和空格。',
    'alpha_space' => '{field} 字段只能包含字母和空格。',
    'decimal' => '{field} 字段必须包含一个十进制数字。',
    'differs' => '{field} 字段必须与 {param} 字段不同。',
    'equals' => '{field} 字段必须与 {param} 完全一致。',
    'exact_length' => '{field} 字段的长度必须恰好为 {param} 个字符。',
    'greater_than' => '{field} 字段必须包含一个大于 {param} 的数字。',
    'greater_than_equal_to' => '{field} 字段必须包含一个大于或等于 {param} 的数字。',
    'hex' => '{field} 字段只能包含十六进制字符。',
    'in_list' => '{field} 字段必须是以下值之一：{param}。',
    'integer' => '{field} 字段必须包含一个整数。',
    'is_natural' => '{field} 字段只能包含数字。',
    'is_natural_no_zero' => '{field} 字段只能包含数字并且必须大于零。',
    'is_not_unique' => '{field} 字段必须包含数据库中已存在的值。',
    'is_unique' => '{field} 字段必须包含一个唯一的值。',
    'less_than' => '{field} 字段必须包含一个小于 {param} 的数字。',
    'less_than_equal_to' => '{field} 字段必须包含一个小于或等于 {param} 的数字。',
    'matches' => '{field} 字段与 {param} 字段不匹配。',
    'max_length' => '{field} 字段的长度不能超过 {param} 个字符。',
    'min_length' => '{field} 字段的长度必须至少为 {param} 个字符。',
    'not_equals' => '{field} 字段不能等于：{param}。',
    'not_in_list' => '{field} 字段不能是以下值之一：{param}。',
    'numeric' => '{field} 字段只能包含数字。',
    'regex_match' => '{field} 字段的格式不正确。',
    'required' => '{field} 字段是必需的。',
    'required_with' => '当 {param} 存在时，{field} 字段是必需的。',
    'required_without' => '当 {param} 不存在时，{field} 字段是必需的。',
    'string' => '{field} 字段必须是一个有效的字符串。',
    'timezone' => '{field} 字段必须是一个有效的时区。',
    'valid_base64' => '{field} 字段必须是一个有效的 base64 字符串。',
    'valid_email' => '{field} 字段必须包含一个有效的电子邮件地址。',
    'valid_emails' => '{field} 字段必须包含所有有效的电子邮件地址。',
    'valid_ip' => '{field} 字段必须包含一个有效的 IP。',
    'valid_url' => '{field} 字段必须包含一个有效的 URL。',
    'valid_date' => '{field} 字段必须包含一个有效的日期。',

    // Credit Cards
    'valid_cc_num' => '{field} 似乎不是一个有效的信用卡号码。',

    // Files
    'uploaded' => '{field} 不是一个有效的上传文件。',
    'max_size' => '{field} 文件太大。',
    'is_image' => '{field} 不是一个有效的已上传图像文件。',
    'mime_in' => '{field} 没有有效的 mime 类型。',
    'ext_in' => '{field} 没有有效的文件扩展名。',
    'max_dims' => '{field} 不是图像，或者太宽/太高。',

    // Custom
    'unique' => '输入的 {field} 已经被注册。',
    'boolean' => '{field} 字段只能被选中或忽略。',
    'currency' => '{field} 字段只能格式化为货币。',
    'valid_year' => '{field} 字段必须使用年份格式。',
    'valid_hex' => '{field} 字段只能包含十六进制颜色代码。'
];
