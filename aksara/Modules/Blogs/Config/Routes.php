<?php
/**
 * Extra route of current module
 *
 * @author			Aby Dahana <abydahana@gmail.com>
 * @profile			abydahana.github.io
 * @website			www.aksaracms.com
 * @since			version 4.0.0
 * @copyright		(c) 2021 - Aksara Laboratory
 */

$routes->add('blogs/read', '\Aksara\Modules\Blogs\Controllers\Read::index');
$routes->add('blogs/search', '\Aksara\Modules\Blogs\Controllers\Search::index');
$routes->add('blogs/tags', '\Aksara\Modules\Blogs\Controllers\Tags::index');
$routes->add('blogs/(:any)/(:any)', '\Aksara\Modules\Blogs\Controllers\Read::index/$1/$2');
$routes->add('blogs/(:any)', '\Aksara\Modules\Blogs\Controllers\Category::index/$1');
