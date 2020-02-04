<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Administrative
 * The landing page for administrative module.
 *
 * @version			2.1.0
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
	}
	
	public function index()
	{
		$this->set_title(phrase('administrative_page'))
		->set_icon('mdi mdi-briefcase-outline')
		->render();
	}
}