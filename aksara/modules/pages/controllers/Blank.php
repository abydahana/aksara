<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Pages > Blank
 * This page to simulate the "about:blank" request that not supported in Cordova
 *
 * @version			2.1.1
 * @author			Aby Dahana
 * @profile			abydahana.github.io
 */
class Blank extends Aksara
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
