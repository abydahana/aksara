<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * APIS
 *
 * @version			2.1.1
 * @author			Aby Dahana
 * @profile			abydahana.github.io
 */
class Apis extends Aksara
{
	public function __construct()
	{
		parent::__construct();
		
		$this->set_permission();
		$this->set_theme('backend');
		
		$this->set_method('index');
	}
	
	public function index()
	{
		$this->set_title(phrase('api_management'))
		->set_icon('mdi mdi-code-braces')
		->render();
	}
}
