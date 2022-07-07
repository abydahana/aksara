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

$routes->add('galleries/(:any)/(:any)', '\Aksara\Modules\Galleries\Controllers\Read::index/$1/$2');
$routes->add('galleries/(:any)', '\Aksara\Modules\Galleries\Controllers\Category::index/$1');
