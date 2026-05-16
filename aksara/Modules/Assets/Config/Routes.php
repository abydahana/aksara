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
if (! isset($routes)) {
    $routes = \Config\Services::routes(true);
}

$routes->group('assets', ['namespace' => '\Aksara\Modules\Assets\Controllers'], function ($routes) {
    $routes->get('css/(:any)/styles.min.css', 'Assets::styles/$1');
    $routes->get('js/(:any)/scripts.min.js', 'Assets::scripts/$1');
    $routes->get('css/styles.min.css', 'Assets::styles');
    $routes->get('js/scripts.min.js', 'Assets::scripts');

    $routes->get('svg/point.svg', 'Svg::point');
    $routes->get('svg/polygon.svg', 'Svg::polygon');
    $routes->get('svg/linestring.svg', 'Svg::linestring');
    $routes->get('svg/folder.svg', 'Svg::folder');
    $routes->get('svg/back_arrow.svg', 'Svg::back_arrow');

    $routes->get('(:any)', 'Assets::index');
});
