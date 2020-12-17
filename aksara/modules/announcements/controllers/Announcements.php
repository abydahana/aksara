<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Announcements
 *
 * @version			2.1.1
 * @author			Aby Dahana
 * @profile			abydahana.github.io
 */
class Announcements extends Aksara
{
	private $_table									= 'app__announcements';
	
	public function __construct()
	{
		parent::__construct();
	}
	
	public function index()
	{
		$this->set_title(phrase('announcements'))
		->set_description(phrase('announcements'))
		->set_icon('mdi mdi-bullhorn-outline')
		->set_primary('announcements_slug')
		->order_by('end_date')
		->render($this->_table);
	}
}
