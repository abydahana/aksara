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

// Настройки языка Validation
return [
    // Основные сообщения
    'noRuleSets' => 'В конфигурации валидации не указаны правила.',
    'ruleNotFound' => '{0} не является допустимым правилом.',
    'groupNotFound' => '{0} не является допустимой группой валидации.',
    'groupNotArray' => 'Группа валидации {0} должна быть массивом.',
    'invalidTemplate' => '{0} не является допустимым шаблоном валидации.',

    // Сообщения для правил
    'alpha' => 'Поле {field} может содержать только буквы.',
    'alpha_dash' => 'Поле {field} может содержать только буквы, цифры, подчеркивания и тире.',
    'alpha_numeric' => 'Поле {field} может содержать только буквы и цифры.',
    'alpha_numeric_punct' => 'Поле {field} может содержать только буквы, цифры, пробелы и символы ~! # $% & * - _ + = | :..',
    'alpha_numeric_space' => 'Поле {field} может содержать только буквы и пробелы.',
    'alpha_space' => 'Поле {field} может содержать только буквы и пробелы.',
    'decimal' => 'Поле {field} должно содержать десятичное число.',
    'differs' => 'Поле {field} должно отличаться от поля {param}.',
    'equals' => 'Поле {field} должно быть равным {param}.',
    'exact_length' => 'Поле {field} должно быть точно длиной {param} символов.',
    'greater_than' => 'Поле {field} должно содержать число, большее {param}.',
    'greater_than_equal_to' => 'Поле {field} должно содержать число, большее или равное {param}.',
    'hex' => 'Поле {field} может содержать только символы шестнадцатеричной системы счисления.',
    'in_list' => 'Поле {field} должно быть одним из: {param}.',
    'integer' => 'Поле {field} должно содержать целое число.',
    'is_natural' => 'Поле {field} должно содержать только положительное число.',
    'is_natural_no_zero' => 'Поле {field} должно содержать только положительное число, не равное нулю.',
    'is_not_unique' => 'Поле {field} должно содержать значение, которое уже существует в базе данных.',
    'is_unique' => 'Поле {field} должно содержать уникальное значение.',
    'less_than' => 'Поле {field} должно содержать число, меньшее {param}.',
    'less_than_equal_to' => 'Поле {field} должно содержать число, меньшее или равное {param}.',
    'matches' => 'Поле {field} не совпадает с полем {param}.',
    'max_length' => 'Поле {field} не может быть длиннее {param} символов.',
    'min_length' => 'Поле {field} должно быть не короче {param} символов.',
    'not_equals' => 'Поле {field} не может быть равным {param}.',
    'not_in_list' => 'Поле {field} не может быть одним из: {param}.',
    'numeric' => 'Поле {field} должно содержать числовое значение.',
    'regex_match' => 'Поле {field} имеет недопустимый формат.',
    'required' => 'Поле {field} обязательно для заполнения.',
    'required_with' => 'Поле {field} обязательно, если присутствует {param}.',
    'required_without' => 'Поле {field} обязательно, если отсутствует {param}.',
    'string' => 'Поле {field} должно быть строкой.',
    'timezone' => 'Поле {field} должно быть допустимым часовым поясом.',
    'valid_base64' => 'Поле {field} должно быть допустимой строкой base64.',
    'valid_email' => 'Поле {field} должно содержать допустимый адрес электронной почты.',
    'valid_emails' => 'Поле {field} должно содержать все допустимые адреса электронной почты.',
    'valid_ip' => 'Поле {field} должно содержать допустимый IP-адрес.',
    'valid_url' => 'Поле {field} должно содержать допустимый URL-адрес.',
    'valid_date' => 'Поле {field} должно содержать допустимую дату.',

    // Карты кредитов
    'valid_cc_num' => '{field} не похож на действительный номер кредитной карты.',

    // Файлы
    'uploaded' => '{field} не является допустимым загруженным файлом.',
    'max_size' => '{field} слишком велик для файла.',
    'is_image' => '{field} не является допустимым загруженным изображением.',
    'mime_in' => '{field} имеет недопустимый MIME-тип.',
    'ext_in' => '{field} имеет недопустимое расширение файла.',
    'max_dims' => '{field} не является изображением или слишком широким или высоким.',

    // Пользовательские
    'unique' => '{field}, введенное значение уже существует.',
    'boolean' => 'Поле {field} может содержать только значение true или false.',
    'currency' => 'Поле {field} должно иметь формат валюты.',
    'valid_date' => 'Поле {field} должно иметь формат даты.',
    'valid_time' => 'Поле {field} должно иметь формат времени.',
    'valid_datetime' => 'Поле {field} должно иметь формат даты и времени.',
    'valid_year' => 'Поле {field} должно иметь формат года.',
    'valid_hex' => 'Поле {field} должно содержать допустимый цвет hex.'
];
