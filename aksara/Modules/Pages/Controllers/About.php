<?php

namespace Aksara\Modules\Pages\Controllers;

/**
 * Pages > About
 *
 * @author			Aby Dahana <abydahana@gmail.com>
 * @profile			abydahana.github.io
 * @website			www.aksaracms.com
 * @since			version 4.0.0
 * @copyright		(c) 2021 - Aksara Laboratory
 */

class About extends \Aksara\Laboratory\Core
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
