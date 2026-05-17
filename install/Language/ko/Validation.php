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
    'noRuleSets' => '유효성 검사 구성에 규칙이 지정되지 않았습니다.',
    'ruleNotFound' => '{0}은(는) 유효한 규칙이 아닙니다.',
    'groupNotFound' => '{0}은(는) 유효성 검사 규칙 그룹이 아닙니다.',
    'groupNotArray' => '{0} 규칙 그룹은 배열이어야 합니다.',
    'invalidTemplate' => '{0}은(는) 유효한 유효성 검사 템플릿이 아닙니다.',

    // Rule Messages
    'alpha' => '{field} 필드에는 알파벳 문자만 포함될 수 있습니다.',
    'alpha_dash' => '{field} 필드에는 영숫자 문자, 밑줄 및 대시만 포함될 수 있습니다.',
    'alpha_numeric' => '{field} 필드에는 영숫자 문자만 포함될 수 있습니다.',
    'alpha_numeric_punct' => '{field} 필드에는 영숫자 문자, 공백 및 ~ ! # $ % & * - _ + = | : . 문자만 포함될 수 있습니다.',
    'alpha_numeric_space' => '{field} 필드에는 영숫자 문자와 공백만 포함될 수 있습니다.',
    'alpha_space' => '{field} 필드에는 알파벳 문자와 공백만 포함될 수 있습니다.',
    'decimal' => '{field} 필드에는 소수가 포함되어야 합니다.',
    'differs' => '{field} 필드는 {param} 필드와 달라야 합니다.',
    'equals' => '{field} 필드는 {param}와 정확히 일치해야 합니다.',
    'exact_length' => '{field} 필드의 길이는 정확히 {param}자여야 합니다.',
    'greater_than' => '{field} 필드에는 {param}보다 큰 숫자가 포함되어야 합니다.',
    'greater_than_equal_to' => '{field} 필드에는 {param}보다 크거나 같은 숫자가 포함되어야 합니다.',
    'hex' => '{field} 필드에는 16진수 문자만 포함될 수 있습니다.',
    'in_list' => '{field} 필드는 {param} 중 하나여야 합니다.',
    'integer' => '{field} 필드에는 정수가 포함되어야 합니다.',
    'is_natural' => '{field} 필드에는 숫자만 포함될 수 있습니다.',
    'is_natural_no_zero' => '{field} 필드에는 숫자만 포함되어야 하며 0보다 커야 합니다.',
    'is_not_unique' => '{field} 필드에는 데이터베이스에 이미 존재하는 값이 포함되어야 합니다.',
    'is_unique' => '{field} 필드에는 고유한 값이 포함되어야 합니다.',
    'less_than' => '{field} 필드에는 {param}보다 작은 숫자가 포함되어야 합니다.',
    'less_than_equal_to' => '{field} 필드에는 {param}보다 작거나 같은 숫자가 포함되어야 합니다.',
    'matches' => '{field} 필드가 {param} 필드와 일치하지 않습니다.',
    'max_length' => '{field} 필드의 길이는 {param}자를 초과할 수 없습니다.',
    'min_length' => '{field} 필드의 길이는 최소 {param}자 이상이어야 합니다.',
    'not_equals' => '{field} 필드는 {param}일 수 없습니다.',
    'not_in_list' => '{field} 필드는 {param} 중 하나가 아니어야 합니다.',
    'numeric' => '{field} 필드에는 숫자만 포함될 수 있습니다.',
    'regex_match' => '{field} 필드의 형식이 올바르지 않습니다.',
    'required' => '{field} 필드는 필수입니다.',
    'required_with' => '{param}이(가) 있을 때 {field} 필드는 필수입니다.',
    'required_without' => '{param}이(가) 없을 때 {field} 필드는 필수입니다.',
    'string' => '{field} 필드는 유효한 문자열이어야 합니다.',
    'timezone' => '{field} 필드는 유효한 시간대여야 합니다.',
    'valid_base64' => '{field} 필드는 유효한 base64 문자열이어야 합니다.',
    'valid_email' => '{field} 필드에는 유효한 이메일 주소가 포함되어야 합니다.',
    'valid_emails' => '{field} 필드에는 모든 유효한 이메일 주소가 포함되어야 합니다.',
    'valid_ip' => '{field} 필드에는 유효한 IP가 포함되어야 합니다.',
    'valid_url' => '{field} 필드에는 유효한 URL이 포함되어야 합니다.',
    'valid_date' => '{field} 필드에는 유효한 날짜가 포함되어야 합니다.',

    // Credit Cards
    'valid_cc_num' => '{field}은(는) 유효한 신용카드 번호가 아닌 것 같습니다.',

    // Files
    'uploaded' => '{field}은(는) 유효한 업로드된 파일이 아닙니다.',
    'max_size' => '{field} 파일이 너무 큽니다.',
    'is_image' => '{field}은(는) 유효한 업로드된 이미지 파일이 아닙니다.',
    'mime_in' => '{field}에 유효한 MIME 유형이 없습니다.',
    'ext_in' => '{field}에 유효한 파일 확장자가 없습니다.',
    'max_dims' => '{field}은(는) 이미지가 아니거나 너비 또는 높이가 너무 큽니다.',

    // Custom
    'unique' => '입력된 {field}은(는) 이미 등록되어 있습니다.',
    'boolean' => '{field} 필드는 선택하거나 무시할 수만 있습니다.',
    'currency' => '{field} 필드는 통화로만 형식화할 수 있습니다.',
    'valid_year' => '{field} 필드는 연도 형식을 사용해야 합니다.',
    'valid_hex' => '{field} 필드에는 16진수 색상 코드만 포함될 수 있습니다.'
];
