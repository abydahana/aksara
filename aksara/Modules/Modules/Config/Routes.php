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

$routes->get('modules/aksara/(:any)/assets/(:any)', '\Aksara\Modules\Modules\Controllers\Modules::index');
$routes->get('modules/(:any)/assets/(:any)', '\Aksara\Modules\Modules\Controllers\Modules::index');
