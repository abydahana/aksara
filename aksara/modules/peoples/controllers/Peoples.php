<?php namespace Aksara\Modules\Peoples\Controllers;
/**
 * Peoples
 * Show the list of peoples
 *
 * @author			Aby Dahana
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
	}
	
	public function index()
	{
		$this->set_title(phrase('people_behind_this'))
		->set_description(phrase('meet_the_people_behind_this'))
		->set_icon('mdi mdi-account-group-outline')
		->where('status', 1)
		
		->render($this->_table);
	}
}
