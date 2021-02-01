<?php namespace Aksara\Modules\Announcements\Controllers;
/**
 * Announcements
 *
 * @author			Aby Dahana
 * @profile			abydahana.github.io
 * @website			www.aksaracms.com
 * @since			version 4.0.0
 * @copyright		(c) 2021 - Aksara Laboratory
 */
class Announcements extends \Aksara\Laboratory\Core
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
