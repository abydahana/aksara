<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * APIS > Debug Tool
 *
 * @version			2.1.1
 * @author			Aby Dahana
 * @profile			abydahana.github.io
 */
class Debug_tool extends Aksara
{
	public function __construct()
	{
		parent::__construct();
		
		$this->set_permission(1);
		$this->set_theme('backend');
		
		$this->set_method('index');
	}
	
	public function index()
	{
		$this->set_title(phrase('debug_tool'))
		->set_icon('mdi mdi-android-debug-bridge')
		->render();
	}
}
