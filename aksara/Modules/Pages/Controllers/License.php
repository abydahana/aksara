<?php

namespace Aksara\Modules\Pages\Controllers;

/**
 * Pages > License
 * End user license agreement
 *
 * @author			Aby Dahana <abydahana@gmail.com>
 * @profile			abydahana.github.io
 * @website			www.aksaracms.com
 * @since			version 4.0.0
 * @copyright		(c) 2021 - Aksara Laboratory
 */

class License extends \Aksara\Laboratory\Core
{
	public function __construct()
	{
		parent::__construct();
	}
	
	public function index()
	{
		$this->set_title(phrase('end_user_license_agreement'))
		->set_icon('mdi mdi-certificate')
		->set_description(phrase('including_the_term_of_service'))
		
		->render();
	}
}
