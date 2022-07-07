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

$routes->group('assets', ['namespace' => '\Aksara\Modules\Assets\Controllers'], function($routes)
{
	$routes->get('css/styles.min.css', 'Assets::styles');
	$routes->get('js/scripts.min.js', 'Assets::scripts');
	
	$routes->get('svg/point.svg', 'Svg::point');
	$routes->get('svg/polygon.svg', 'Svg::polygon');
	$routes->get('svg/linestring.svg', 'Svg::linestring');
	$routes->get('svg/folder.svg', 'Svg::folder');
	$routes->get('svg/back_arrow.svg', 'Svg::back_arrow');
});
