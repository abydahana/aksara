<?php

namespace Aksara\Modules\Peoples\Controllers;

/**
 * Peoples
 * Show the list of peoples
 *
 * @author			Aby Dahana <abydahana@gmail.com>
 * @profile			abydahana.github.io
 * @website			www.aksaracms.com
 * @since			version 4.0.0
 * @copyright		(c) 2021 - Aksara Laboratory
 */

class Peoples extends \Aksara\Laboratory\Core
{
	private $_table									= 'peoples';
	
	public function __construct()
	{
		parent::__construct();
		
		$this->searchable(false);
		$this->limit(12);
	}
	
	public function index()
	{
		$this->set_title(phrase('the_people_behind_us'))
		->set_description(phrase('meet_the_the_people_behind_us'))
		->set_icon('mdi mdi-account-group-outline')
		->where('status', 1)
		
		->render($this->_table);
	}
}
