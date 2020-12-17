<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Administrative
 *
 * @version			2.1.1
 * @author			Aby Dahana
 * @profile			abydahana.github.io
 */
class Administrative extends Aksara
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
		$this->set_title(phrase('administrative_page'))
		->set_icon('mdi mdi-briefcase-outline')
		->render();
	}
}
