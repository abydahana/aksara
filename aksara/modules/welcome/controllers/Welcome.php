<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Welcome
 * The default landing page of default routes
 *
 * @version			2.1.1
 * @author			Aby Dahana
 * @profile			abydahana.github.io
 */
class Welcome extends Aksara
{
	public function __construct()
	{
		parent::__construct();
	}
	
	public function index()
	{
		$this->set_title(phrase('welcome_to') . ' ' . get_setting('app_name'))
		->set_description(get_setting('app_description'))
		->render();
	}
}
