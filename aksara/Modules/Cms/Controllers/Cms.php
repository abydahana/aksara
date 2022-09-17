<?php

namespace Aksara\Modules\Cms\Controllers;

/**
 * CMS
 * Landing page for Content Management Module.
 *
 * @author			Aby Dahana <abydahana@gmail.com>
 * @profile			abydahana.github.io
 * @website			www.aksaracms.com
 * @since			version 4.0.0
 * @copyright		(c) 2021 - Aksara Laboratory
 */

class Cms extends \Aksara\Laboratory\Core
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
		$this->set_title(phrase('content_management_system'))
		->set_icon('mdi mdi-briefcase-outline')
		
		->render();
	}
}
