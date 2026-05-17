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
    'noRuleSets' => 'Không có tập quy tắc nào được chỉ định trong cấu hình Xác thực.',
    'ruleNotFound' => '{0} không phải là một quy tắc hợp lệ.',
    'groupNotFound' => '{0} không phải là một nhóm quy tắc xác thực.',
    'groupNotArray' => 'Nhóm quy tắc {0} phải là một mảng.',
    'invalidTemplate' => '{0} không phải là một mẫu Xác thực hợp lệ.',

    // Rule Messages
    'alpha' => 'Trường {field} chỉ có thể chứa các ký tự chữ cái.',
    'alpha_dash' => 'Trường {field} chỉ có thể chứa các ký tự chữ và số, dấu gạch dưới và dấu gạch ngang.',
    'alpha_numeric' => 'Trường {field} chỉ có thể chứa các ký tự chữ và số.',
    'alpha_numeric_punct' => 'Trường {field} chỉ có thể chứa các ký tự chữ và số, khoảng trắng và ~ ! # $ % & * - _ + = | : .',
    'alpha_numeric_space' => 'Trường {field} chỉ có thể chứa các ký tự chữ và số và khoảng trắng.',
    'alpha_space' => 'Trường {field} chỉ có thể chứa các ký tự chữ cái và khoảng trắng.',
    'decimal' => 'Trường {field} phải chứa một số thập phân.',
    'differs' => 'Trường {field} phải khác với trường {param}.',
    'equals' => 'Trường {field} phải chính xác là: {param}.',
    'exact_length' => 'Trường {field} phải có độ dài chính xác {param} ký tự.',
    'greater_than' => 'Trường {field} phải chứa một số lớn hơn {param}.',
    'greater_than_equal_to' => 'Trường {field} phải chứa một số lớn hơn hoặc bằng {param}.',
    'hex' => 'Trường {field} chỉ có thể chứa các ký tự thập lục phân.',
    'in_list' => 'Trường {field} phải là một trong những: {param}.',
    'integer' => 'Trường {field} phải chứa một số nguyên.',
    'is_natural' => 'Trường {field} chỉ được chứa các chữ số.',
    'is_natural_no_zero' => 'Trường {field} chỉ được chứa các chữ số và phải lớn hơn không.',
    'is_not_unique' => 'Trường {field} phải chứa một giá trị đã tồn tại trước đó trong cơ sở dữ liệu.',
    'is_unique' => 'Trường {field} phải chứa một giá trị duy nhất.',
    'less_than' => 'Trường {field} phải chứa một số nhỏ hơn {param}.',
    'less_than_equal_to' => 'Trường {field} phải chứa một số nhỏ hơn hoặc bằng {param}.',
    'matches' => 'Trường {field} không khớp với trường {param}.',
    'max_length' => 'Trường {field} không thể vượt quá {param} ký tự.',
    'min_length' => 'Trường {field} phải có độ dài ít nhất {param} ký tự.',
    'not_equals' => 'Trường {field} không thể là: {param}.',
    'not_in_list' => 'Trường {field} không được là một trong những: {param}.',
    'numeric' => 'Trường {field} chỉ được chứa các số.',
    'regex_match' => 'Trường {field} không đúng định dạng.',
    'required' => 'Trường {field} là bắt buộc.',
    'required_with' => 'Trường {field} là bắt buộc khi {param} hiện diện.',
    'required_without' => 'Trường {field} là bắt buộc khi {param} không hiện diện.',
    'string' => 'Trường {field} phải là một chuỗi hợp lệ.',
    'timezone' => 'Trường {field} phải là một múi giờ hợp lệ.',
    'valid_base64' => 'Trường {field} phải là một chuỗi base64 hợp lệ.',
    'valid_email' => 'Trường {field} phải chứa một địa chỉ email hợp lệ.',
    'valid_emails' => 'Trường {field} phải chứa tất cả các địa chỉ email hợp lệ.',
    'valid_ip' => 'Trường {field} phải chứa một IP hợp lệ.',
    'valid_url' => 'Trường {field} phải chứa một URL hợp lệ.',
    'valid_date' => 'Trường {field} phải chứa một ngày hợp lệ.',

    // Credit Cards
    'valid_cc_num' => '{field} dường như không phải là số thẻ tín dụng hợp lệ.',

    // Files
    'uploaded' => '{field} không phải là một tập tin tải lên hợp lệ.',
    'max_size' => '{field} là một tập tin quá lớn.',
    'is_image' => '{field} không phải là một tập tin hình ảnh tải lên hợp lệ.',
    'mime_in' => '{field} không có loại mime hợp lệ.',
    'ext_in' => '{field} không có phần mở rộng tập tin hợp lệ.',
    'max_dims' => '{field} không phải là hình ảnh hoặc quá rộng hoặc quá cao.',

    // Custom
    'unique' => '{field} đã nhập đã được đăng ký.',
    'boolean' => 'Trường {field} chỉ có thể được chọn hoặc bỏ qua.',
    'currency' => 'Trường {field} chỉ có thể được định dạng là tiền tệ.',
    'valid_year' => 'Trường {field} phải sử dụng định dạng năm.',
    'valid_hex' => 'Trường {field} chỉ có thể chứa mã màu hex.'
];
