<?php

namespace Aksara\Modules\Pages\Controllers;

/**
 * Pages > Blank
 * This page to simulate the "about:blank" request that not supported in Cordova
 *
 * @author			Aby Dahana <abydahana@gmail.com>
 * @profile			abydahana.github.io
 * @website			www.aksaracms.com
 * @since			version 4.0.0
 * @copyright		(c) 2021 - Aksara Laboratory
 */

class Blank extends \Aksara\Laboratory\Core
{
	public function __construct()
	{
		parent::__construct();
	}
	
	public function index()
	{
		echo phrase('loading');
	}
}
