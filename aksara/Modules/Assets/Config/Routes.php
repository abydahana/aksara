<?php namespace Aksara\Modules\Assets\Config;
/**
 * Extra route of current module
 *
 * @author			Aby Dahana
 * @profile			abydahana.github.io
 * @website			www.aksaracms.com
 * @since			version 4.0.0
 * @copyright		(c) 2021 - Aksara Laboratory
 */

$routes->group('assets', ['namespace' => '\Aksara\Modules\Assets\Controllers'], function($routes)
{
	$routes->get('/', 'Assets::index');
	$routes->get('css/styles.min.css', 'Assets::styles');
	$routes->get('js/scripts.min.js', 'Assets::scripts');
});
