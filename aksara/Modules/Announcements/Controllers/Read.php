<?php namespace Aksara\Modules\Announcements\Controllers;
/**
 * Announcements > Read
 *
 * @author			Aby Dahana
 * @profile			abydahana.github.io
 * @website			www.aksaracms.com
 * @since			version 4.0.0
 * @copyright		(c) 2021 - Aksara Laboratory
 */
class Read extends \Aksara\Laboratory\Core
{
	private $_table									= 'app__announcements';
	
	public function __construct()
	{
		parent::__construct();
	}
	
	public function index($slug = null)
	{
		if(service('request')->getGet('announcement_slug'))
		{
			$slug									= service('request')->getGet('announcement_slug');
		}
		
		$this->set_title('{title}', phrase('announcements'))
		->set_icon('mdi mdi-bullhorn')
		->where('announcement_slug', $slug)
		->limit(1)
		
		->render($this->_table);
	}
}
