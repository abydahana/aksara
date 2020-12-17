<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Pages > License
 * End user license agreement
 *
 * @version			2.1.1
 * @author			Aby Dahana
 * @profile			abydahana.github.io
 */
class License extends Aksara
{
	public function __construct()
	{
		parent::__construct();
	}
	
	public function index()
	{
		$this->set_title(phrase('end_user_license_agreement'))
		->set_icon('mdi mdi-certificate')
		->set_description(phrase('include_the_term_of_service'))
		->render();
	}
}
