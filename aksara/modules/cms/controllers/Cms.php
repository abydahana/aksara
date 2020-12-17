<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * CMS
 * Landing page for Content Management Module.
 *
 * @version			2.1.1
 * @author			Aby Dahana
 * @profile			abydahana.github.io
 */
class Cms extends Aksara
{
	public function __construct()
	{
		parent::__construct();
		
		$this->set_permission(array(1, 2));
		$this->set_theme('backend');
		
		$this->set_method('index');
	}
	
	public function index()
	{
		$this->set_title(phrase('content_management_system'))
		->set_icon('mdi mdi-briefcase-outline')
		->render();
	}
}
