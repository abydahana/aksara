<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Recipes
 *
 * @version			2.1.0
 * @author			Aby Dahana
 * @profile			abydahana.github.io
 */
class Recipes extends Aksara
{
	public function __construct()
	{
		parent::__construct();
	}
	
	public function index()
	{
		$this->set_title(phrase('recipes'))
		->render();
	}
}
