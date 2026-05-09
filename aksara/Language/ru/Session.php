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

// Настройки языка Session
return [
    'missingDatabaseTable' => '`sessionSavePath` должен содержать имя таблицы для работы обработчика сессий в базе данных.',
    'invalidSavePath' => 'Сессия: Настроенный путь сохранения "{0}" не является директорией, не существует или не может быть создан.',
    'writeProtectedSavePath' => 'Сессия: Настроенный путь сохранения "{0}" не может быть записан процессом PHP.',
    'emptySavePath' => 'Сессия: Не настроен путь сохранения.',
    'invalidSavePathFormat' => 'Сессия: Неверный формат пути сохранения Redis: {0}',
    'invalidSameSiteSetting' => 'Сессия: Настройка SameSite должна быть None, Lax, Strict, или пустой строкой. Предоставлено: {0}',
];
