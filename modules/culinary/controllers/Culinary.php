<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Culinary
 *
 * @version			2.1.0
 * @author			Aby Dahana
 * @profile			abydahana.github.io
 */
class Culinary extends Aksara
{
	public function __construct()
	{
		parent::__construct();
	}
	
	public function index()
	{
		$this->set_title(phrase('culinary'))
		->render();
	}
}
