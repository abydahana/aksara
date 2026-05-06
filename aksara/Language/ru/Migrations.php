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

// Настройки языка миграций
return [
    // Миграционный запуск
    'missingTable' => 'Таблица миграций должна быть установлена.',
    'disabled' => 'Миграции загружены, но отключены или настроены неверно.',
    'notFound' => 'Файл миграции не найден: ',
    'batchNotFound' => 'Пакет миграции не найден: ',
    'empty' => 'Не найдены файлы миграций',
    'gap' => 'Существует разрыв в последовательности миграций около номера версии: ',
    'classNotFound' => 'Класс миграции "%s" не может быть найден.',
    'missingMethod' => 'У класса миграции отсутствует метод "%s" .',

    // Команда миграций
    'migHelpLatest' => "\t\tМиграция базы данных на последнюю доступную миграцию.",
    'migHelpCurrent' => "\t\tМиграция базы данных на версию, установленную в конфигурации как 'текущую'.",
    'migHelpVersion' => "\tМиграция базы данных на версию {v}.",
    'migHelpRollback' => "\tЗапуск всех миграций 'откат' до версии 0.",
    'migHelpRefresh' => "\t\tУдаление и повторный запуск всех миграций для обновления базы данных.",
    'migHelpSeed' => "\tЗапуск сидера с именем [name].",
    'migCreate' => "\tСоздание новой миграции с именем [name]",
    'nameMigration' => 'Имя файла миграции',
    'migNumberError' => 'Номер миграции должен быть трехзначным и не должен содержать пробелов в последовательности.',
    'rollBackConfirm' => 'Вы уверены, что хотите выполнить откат?',
    'refreshConfirm' => 'Вы уверены, что хотите обновить базу данных?',

    'latest' => 'Запуск всех новых миграций...',
    'generalFault' => 'Ошибка выполнения миграции!',
    'migInvalidVersion' => 'Указан неверный номер версии.',
    'toVersionPH' => 'Миграция на версию %s...',
    'toVersion' => 'Миграция на текущую версию...',
    'rollingBack' => 'Откат всех миграций...',
    'noneFound' => 'Миграции не найдены.',
    'migSeeder' => 'Имя сидера',
    'migMissingSeeder' => 'Вы должны указать имя сидера.',
    'nameSeeder' => 'Имя файла сидера',
    'removed' => 'Откат: ',
    'added' => 'Выполнено: ',

    // Статус миграции
    'namespace' => 'Пространство имен',
    'filename' => 'Имя файла',
    'version' => 'Версия',
    'group' => 'Группа',
    'on' => 'Мигрировано на: ',
    'batch' => 'Пакет',
];
