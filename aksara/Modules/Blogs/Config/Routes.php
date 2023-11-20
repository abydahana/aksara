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

$routes->add('blogs/search', '\Aksara\Modules\Blogs\Controllers\Search::index');
$routes->add('blogs/tags', '\Aksara\Modules\Blogs\Controllers\Tags::index');
$routes->add('blogs/read', '\Aksara\Modules\Blogs\Controllers\Read::index');
$routes->add('blogs/(:any)/(:any)', '\Aksara\Modules\Blogs\Controllers\Read::index/$1/$2');
$routes->add('blogs/(:any)', '\Aksara\Modules\Blogs\Controllers\Category::index/$1');
