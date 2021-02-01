<?php
/**
 * Extra route of current module
 *
 * @author			Aby Dahana
 * @profile			abydahana.github.io
 * @website			www.aksaracms.com
 * @since			version 4.0.0
 * @copyright		(c) 2021 - Aksara Laboratory
 */

$routes->add('announcements/(:any)', '\Aksara\Modules\Announcements\Controllers\Read::index/$1');
