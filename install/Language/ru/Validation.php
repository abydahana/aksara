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
    'noRuleSets' => 'В конфигурации валидации не указаны наборы правил.',
    'ruleNotFound' => '{0} не является допустимым правилом.',
    'groupNotFound' => '{0} не является группой правил валидации.',
    'groupNotArray' => 'Группа правил {0} должна быть массивом.',
    'invalidTemplate' => '{0} не является допустимым шаблоном валидации.',

    // Rule Messages
    'alpha' => 'Поле {field} может содержать только буквенные символы.',
    'alpha_dash' => 'Поле {field} может содержать только буквенно-цифровые символы, подчеркивания и тире.',
    'alpha_numeric' => 'Поле {field} может содержать только буквенно-цифровые символы.',
    'alpha_numeric_punct' => 'Поле {field} может содержать только буквенно-цифровые символы, пробелы и символы ~ ! # $ % & * - _ + = | : .',
    'alpha_numeric_space' => 'Поле {field} может содержать только буквенно-цифровые символы и пробелы.',
    'alpha_space' => 'Поле {field} может содержать только буквенные символы и пробелы.',
    'decimal' => 'Поле {field} должно содержать десятичное число.',
    'differs' => 'Поле {field} должно отличаться от поля {param}.',
    'equals' => 'Поле {field} должно быть точно: {param}.',
    'exact_length' => 'Длина поля {field} должна составлять ровно {param} символов.',
    'greater_than' => 'Поле {field} должно содержать число больше, чем {param}.',
    'greater_than_equal_to' => 'Поле {field} должно содержать число больше или равное {param}.',
    'hex' => 'Поле {field} может содержать только шестнадцатеричные символы.',
    'in_list' => 'Поле {field} должно быть одним из: {param}.',
    'integer' => 'Поле {field} должно содержать целое число.',
    'is_natural' => 'Поле {field} должно содержать только цифры.',
    'is_natural_no_zero' => 'Поле {field} должно содержать только цифры и быть больше нуля.',
    'is_not_unique' => 'Поле {field} должно содержать ранее существовавшее значение в базе данных.',
    'is_unique' => 'Поле {field} должно содержать уникальное значение.',
    'less_than' => 'Поле {field} должно содержать число меньше, чем {param}.',
    'less_than_equal_to' => 'Поле {field} должно содержать число меньше или равное {param}.',
    'matches' => 'Поле {field} не совпадает с полем {param}.',
    'max_length' => 'Длина поля {field} не может превышать {param} символов.',
    'min_length' => 'Длина поля {field} должна составлять не менее {param} символов.',
    'not_equals' => 'Поле {field} не может быть: {param}.',
    'not_in_list' => 'Поле {field} не должно быть одним из: {param}.',
    'numeric' => 'Поле {field} должно содержать только числа.',
    'regex_match' => 'Поле {field} имеет неправильный формат.',
    'required' => 'Поле {field} обязательно для заполнения.',
    'required_with' => 'Поле {field} обязательно, если присутствует {param}.',
    'required_without' => 'Поле {field} обязательно, если отсутствует {param}.',
    'string' => 'Поле {field} должно быть действительной строкой.',
    'timezone' => 'Поле {field} должно быть действительным часовым поясом.',
    'valid_base64' => 'Поле {field} должно быть действительной строкой base64.',
    'valid_email' => 'Поле {field} должно содержать действительный адрес электронной почты.',
    'valid_emails' => 'Поле {field} должно содержать все действительные адреса электронной почты.',
    'valid_ip' => 'Поле {field} должно содержать действительный IP-адрес.',
    'valid_url' => 'Поле {field} должно содержать действительный URL.',
    'valid_date' => 'Поле {field} должно содержать действительную дату.',

    // Credit Cards
    'valid_cc_num' => '{field} не похоже на действительный номер кредитной карты.',

    // Files
    'uploaded' => '{field} не является допустимым загруженным файлом.',
    'max_size' => '{field} - слишком большой файл.',
    'is_image' => '{field} не является допустимым загруженным файлом изображения.',
    'mime_in' => '{field} не имеет допустимого MIME-типа.',
    'ext_in' => '{field} не имеет допустимого расширения файла.',
    'max_dims' => '{field} либо не является изображением, либо оно слишком широкое или высокое.',

    // Custom
    'unique' => 'Введенное значение {field} уже зарегистрировано.',
    'boolean' => 'Поле {field} можно только отметить или проигнорировать.',
    'currency' => 'Поле {field} может быть отформатировано только как валюта.',
    'valid_year' => 'Поле {field} должно использовать формат года.',
    'valid_hex' => 'Поле {field} может содержать только шестнадцатеричный цветовой код.'
];
