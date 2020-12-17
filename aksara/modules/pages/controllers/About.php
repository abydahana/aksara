<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Pages > About
 *
 * @version			2.1.1
 * @author			Aby Dahana
 * @profile			abydahana.github.io
 */
class About extends Aksara
{
	public function __construct()
	{
		parent::__construct();
	}
	
	public function index()
	{
		$this->set_title(phrase('about_the_project'))
		->set_icon('mdi mdi-information-outline')
		->set_description(phrase('including_the_credits_of_external_resources'))
		->render();
	}
}
