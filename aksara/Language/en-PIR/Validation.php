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
    'noRuleSets' => 'No rule sets specified in Validation configuration.',
    'ruleNotFound' => '"{0}" be not a valid rule, matey.',
    'groupNotFound' => '"{0}" be not a validation rules group.',
    'groupNotArray' => '"{0}" rule group must be an array.',
    'invalidTemplate' => '"{0}" be not a valid Validation template.',

    // Rule Messages
    'alpha' => 'The {field} field may only contain alphabetical characters.',
    'alpha_dash' => 'The {field} field may only contain alphanumeric, underscore, and dash characters.',
    'alpha_numeric' => 'The {field} field may only contain alphanumeric characters.',
    'alpha_numeric_punct' => 'The {field} field may contain only alphanumeric characters, spaces, and  ~ ! # $ % & * - _ + = | : . characters.',
    'alpha_numeric_space' => 'The {field} field may only contain alphanumeric and space characters.',
    'alpha_space' => 'The {field} field may only contain alphabetical characters and spaces.',
    'decimal' => 'The {field} field must contain a decimal number.',
    'differs' => 'The {field} field must differ from the {param} field.',
    'equals' => 'The {field} field must be exactly: {param}.',
    'exact_length' => 'The {field} field must be exactly {param} characters in length.',
    'field_exists' => 'The {field} field must exist.',
    'greater_than' => 'The {field} field must contain a number greater than {param}.',
    'greater_than_equal_to' => 'The {field} field must contain a number greater than or equal to {param}.',
    'hex' => 'The {field} field may only contain hexadecimal characters.',
    'in_list' => 'The {field} field must be one of: {param}.',
    'integer' => 'The {field} field must contain an integer.',
    'is_natural' => 'The {field} field must only contain digits.',
    'is_natural_no_zero' => 'The {field} field must only contain digits and must be greater than zero.',
    'is_not_unique' => 'The {field} field must contain a previously existing value in the map.',
    'is_unique' => 'The {field} field must contain a unique value.',
    'less_than' => 'The {field} field must contain a number less than {param}.',
    'less_than_equal_to' => 'The {field} field must contain a number less than or equal to {param}.',
    'matches' => 'The {field} field does not match the {param} field, shiver me timbers.',
    'max_length' => 'The {field} field cannot exceed {param} characters in length.',
    'min_length' => 'The {field} field must be at least {param} characters in length.',
    'not_equals' => 'The {field} field cannot be: {param}.',
    'not_in_list' => 'The {field} field must not be one of: {param}.',
    'numeric' => 'The {field} field must contain only numbers.',
    'regex_match' => 'The {field} field be not in the correct format.',
    'required' => 'The {field} field be required, ye scallywag.',
    'required_with' => 'The {field} field be required when {param} be present.',
    'required_without' => 'The {field} field be required when {param} be not present.',
    'string' => 'The {field} field must be a valid string.',
    'timezone' => 'The {field} field must be a valid timezone.',
    'valid_base64' => 'The {field} field must be a valid base64 string.',
    'valid_email' => 'The {field} field must contain a valid email address.',
    'valid_emails' => 'The {field} field must contain all valid email addresses.',
    'valid_ip' => 'The {field} field must contain a valid IP.',
    'valid_url' => 'The {field} field must contain a valid URL.',
    'valid_url_strict' => 'The {field} field must contain a valid URL.',
    'valid_date' => 'The {field} field must contain a valid date.',
    'valid_json' => 'The {field} field must contain a valid json.',

    // Credit Cards
    'valid_cc_number' => '{field} does not appear to be a valid credit card number.',

    // Files
    'uploaded' => '{field} be not a valid uploaded scroll.',
    'max_size' => '{field} be too large of a scroll.',
    'is_image' => '{field} be not a valid, uploaded portrait.',
    'mime_in' => '{field} does not have a valid mime type.',
    'ext_in' => '{field} does not have a valid scroll extension.',
    'max_dims' => '{field} be either not a portrait, or it be too wide or tall.',
    'min_dims' => '{field} be either not a portrait, or it be not wide or tall enough.',

    // Custom
    'unique' => 'The {field} ye entered already registered, matey.',
    'boolean' => 'The {field} must be checked or rather ignored.',
    'currency' => 'The {field} must be contain valid plunder.',
    'valid_time' => 'The {field} must be contain valid time.',
    'valid_datetime' => 'The {field} must be contain valid date and time.',
    'valid_year' => 'The {field} must be contain valid year.',
    'valid_hex' => 'The {field} must be contain only hex color code.',
    'relation_checker' => 'The relation data of {field} be not found.'
];
