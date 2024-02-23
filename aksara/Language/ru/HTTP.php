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

// Настройки языка HTTP
return [
    // CurlRequest
    'missingCurl' => 'CURL должен быть включен для использования класса CURLRequest.',
    'invalidSSLKey' => 'Не удалось установить ключ SSL. {0} не является допустимым файлом.',
    'sslCertNotFound' => 'Сертификат SSL не найден в: {0}',
    'curlError' => '{0} : {1}',

    // IncomingRequest
    'invalidNegotiationType' => '{0} не является допустимым типом согласования. Должен быть одним из: media, charset, encoding, language.',

    // Message
    'invalidHTTPProtocol' => 'Неверная версия протокола HTTP. Должна быть одной из: {0}',

    // Negotiate
    'emptySupportedNegotiations' => 'Вы должны предоставить массив поддерживаемых значений для всех согласований.',

    // RedirectResponse
    'invalidRoute' => '{0} не является допустимым маршрутом.',

    // DownloadResponse
    'cannotSetBinary' => 'При установке пути к файлу невозможно установить бинарный режим.',
    'cannotSetFilepath' => 'При установке бинарного режима невозможно установить путь к файлу: {0}',
    'notFoundDownloadSource' => 'Источник для скачиваемого тела не найден.',
    'cannotSetCache' => 'Невозможно использовать кэш для скачивания.',
    'cannotSetStatusCode' => 'Невозможно изменить код состояния для скачивания. Код: {0}, Причина: {1}',

    // Response
    'missingResponseStatus' => 'HTTP-ответу не хватает кода состояния',
    'invalidStatusCode' => '{0} не является допустимым кодом состояния HTTP-ответа',
    'unknownStatusCode' => 'Неизвестный код состояния HTTP-ответа без сообщения: {0}',

    // URI
    'cannotParseURI' => 'Невозможно разобрать URI: {0}',
    'segmentOutOfRange' => 'Сегмент URI вне диапазона: {0}',
    'invalidPort' => 'Порт должен быть от 0 до 65535. Передано: {0}',
    'malformedQueryString' => 'Строка запроса не должна включать фрагмент URI.',

    // Page Not Found
    'pageNotFound' => 'Страница не найдена',
    'emptyController' => 'Не указан контроллер.',
    'controllerNotFound' => 'Контроллер или его метод не найден: {0}::{1}',
    'methodNotFound' => 'Метод контроллера не найден: {0}',

    // CSRF
    'disallowedAction' => 'Действие, которое вы запрашиваете, не разрешено.',

    // Uploaded file moving
    'alreadyMoved' => 'Загруженный файл уже перемещен.',
    'invalidFile' => 'Исходный файл не является допустимым файлом.',
    'moveFailed' => 'Невозможно переместить файл {0} в {1} ({2})',

    'uploadErrOk' => 'Файл успешно загружен.',
    'uploadErrIniSize' => 'Файл "%s" превышает установленное вами ограничение upload_max_filesize.',
    'uploadErrFormSize' => 'Файл "%s" превышает установленное в форме ограничение загрузки.',
    'uploadErrPartial' => 'Файл "%s" был загружен только частично.',
    'uploadErrNoFile' => 'Файл не был загружен.',
    'uploadErrCantWrite' => 'Невозможно записать файл "%s" на диск.',
    'uploadErrNoTmpDir' => 'Файл не может быть загружен: отсутствует временная директория.',
    'uploadErrExtension' => 'Загрузка файла была остановлена расширением PHP.',
    'uploadErrUnknown' => 'Файл "%s" не был загружен из-за неизвестной ошибки.',

    // SameSite setting
    'invalidSameSiteSetting' => 'Настройка SameSite должна быть None, Lax, Strict или пустой строкой. Передано: {0}',
];
