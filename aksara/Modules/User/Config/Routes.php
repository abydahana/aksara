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

$routes->add('user/(:any)/activities', '\Aksara\Modules\User\Controllers\User::activities/$1');
$routes->add('user/(:any)/likes', '\Aksara\Modules\User\Controllers\User::likes/$1');
$routes->add('user/(:any)/guestbook', '\Aksara\Modules\User\Controllers\User::guestbook/$1');
