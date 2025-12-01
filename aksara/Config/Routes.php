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

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
if (file_exists(ROOTPATH . 'modules/Home/Controllers/Home.php')) {
    $routes->setDefaultNamespace('Modules\Home\Controllers');
} else {
    $routes->setDefaultNamespace('Aksara\Modules\Home\Controllers');
}

$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override('Aksara\Modules\Pages\Controllers\Pages::not_found');

$routes->add('/', 'Home::index');

/**
 * Register the directory based routes
 */
$routes_automation = new \Aksara\Laboratory\Router($routes);
