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

// Настройки языка изображений
return [
    'sourceImageRequired' => 'Вы должны указать исходное изображение в ваших настройках.',
    'gdRequired' => 'Для использования этой функции необходима библиотека GD.',
    'gdRequiredForProps' => 'Ваш сервер должен поддерживать библиотеку GD для получения свойств изображения.',
    'gifNotSupported' => 'Изображения в формате GIF часто не поддерживаются из-за ограничений разрешений. Возможно, вам придется использовать изображения в форматах JPG или PNG вместо этого.',
    'jpgNotSupported' => 'Изображения в формате JPG не поддерживаются.',
    'pngNotSupported' => 'Изображения в формате PNG не поддерживаются.',
    'webpNotSupported' => 'Изображения в формате WEBP не поддерживаются.',
    'fileNotSupported' => 'Предоставленный файл не является поддерживаемым типом изображения.',
    'unsupportedImageCreate' => 'Ваш сервер не поддерживает необходимые функции GD для обработки этого типа изображения.',
    'jpgOrPngRequired' => 'Протокол изменения размера изображения, указанный в ваших настройках, работает только с изображениями в формате JPEG или PNG.',
    'rotateUnsupported' => 'Поворот изображения, вероятно, не поддерживается вашим сервером.',
    'libPathInvalid' => 'Путь к вашей библиотеке изображений указан неверно. Убедитесь, что путь к вашей библиотеке изображений указан правильно в ваших настройках изображения. ({0, string})',
    'imageProcessFailed' => 'Обработка изображения не удалась. Убедитесь, что ваш сервер поддерживает выбранный протокол, и путь к вашей библиотеке изображений указан правильно.',
    'rotationAngleRequired' => 'Требуется угол поворота для поворота изображения.',
    'invalidPath' => 'Неверный путь к изображению.',
    'copyFailed' => 'Не удалось скопировать изображение.',
    'missingFont' => 'Не удается найти шрифт для использования.',
    'saveFailed' => 'Не удалось сохранить изображение. Убедитесь, что изображение и каталог файла можно записать.',
    'invalidDirection' => 'Направление переворота может быть только `vertical` или `horizontal`. Указано: {0}',
    'exifNotSupported' => 'Чтение данных EXIF не поддерживается в этой установке PHP.',
];
