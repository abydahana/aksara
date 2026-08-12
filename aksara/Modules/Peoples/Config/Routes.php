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

/**
 * @var \CodeIgniter\Router\RouteCollection $routes
 */
$routes->group(
    'peoples',
    ['namespace' => 'Aksara\Modules\Peoples\Controllers'],
    static function ($routes) {
        $routes->add('(:segment)', 'Read::index/$1');
    }
);
