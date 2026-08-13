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
    'blogs',
    ['namespace' => 'Aksara\Modules\Blogs\Controllers'],
    static function ($routes) {
        $routes->add('read', 'Read::index/$1');
        $routes->add('(:segment)/(:segment)', 'Read::index/$1/$2');
        $routes->add('(:segment)', 'Category::index/$1');
    }
);
