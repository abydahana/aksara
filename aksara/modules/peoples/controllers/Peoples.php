<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Peoples
 * Show the list of peoples
 *
 * @version			2.1.1
 * @author			Aby Dahana
 * @profile			abydahana.github.io
 */
class Peoples extends Aksara
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
