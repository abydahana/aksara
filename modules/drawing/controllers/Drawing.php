<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Drawing Tools
 *
 * @version			2.1.0
 * @author			Aby Dahana
 * @profile			abydahana.github.io
 */
class Drawing extends Aksara
{
	public function __construct()
	{
		parent::__construct();
		
		$this->set_theme('backend');
	}
	
	public function index()
	{
		$this->set_title(phrase('polygon'))
		->set_icon('mdi mdi-palette')
		->render();
	}
}
