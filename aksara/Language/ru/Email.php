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

// Настройки языка для электронной почты
return [
    'mustBeArray' => 'Метод валидации электронной почты должен принимать массив.',
    'invalidAddress' => 'Недопустимый адрес электронной почты: {0}',
    'attachmentMissing' => 'Не удается найти следующее вложение в письме: {0}',
    'attachmentUnreadable' => 'Невозможно прочитать это вложение: {0}',
    'noFrom' => 'Невозможно отправить письмо без заголовка "От".',
    'noRecipients' => 'Вы должны указать получателей: Кому, Копия, или Скрытая копия',
    'sendFailurePHPMail' => 'Невозможно отправить письмо с использованием PHP mail(). Ваш сервер, возможно, не настроен для отправки писем этим методом.',
    'sendFailureSendmail' => 'Невозможно отправить письмо с использованием PHP Sendmail. Ваш сервер, возможно, не настроен для отправки писем этим методом.',
    'sendFailureSmtp' => 'Невозможно отправить письмо с использованием PHP SMTP. Ваш сервер, возможно, не настроен для отправки писем этим методом.',
    'sent' => 'Ваше сообщение успешно отправлено с использованием следующего протокола: {0}',
    'noSocket' => 'Не удается открыть сокет для Sendmail. Пожалуйста, проверьте настройки.',
    'noHostname' => 'Вы не указали имя хоста SMTP.',
    'SMTPError' => 'Обнаружена ошибка SMTP: {0}',
    'noSMTPAuth' => 'Ошибка: вы должны установить имя пользователя и пароль SMTP.',
    'failedSMTPLogin' => 'Не удалось отправить команду AUTH LOGIN. Ошибка: {0}',
    'SMTPAuthUsername' => 'Не удалось аутентифицировать имя пользователя. Ошибка: {0}',
    'SMTPAuthPassword' => 'Не удалось аутентифицировать пароль. Ошибка: {0}',
    'SMTPDataFailure' => 'Не удается отправить данные: {0}',
    'exitStatus' => 'Статус кода завершения: {0}',
];
