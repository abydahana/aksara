<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Announcements > Read
 *
 * @version			2.1.1
 * @author			Aby Dahana
 * @profile			abydahana.github.io
 */
class Read extends Aksara
{
	private $_table									= 'app__announcements';
	
	public function __construct()
	{
		parent::__construct();
		
		$this->parent_module('announcements');
	}
	
	public function index($slug = null)
	{
		if($this->input->get('announcement_slug'))
		{
			$slug									= $this->input->get('announcement_slug');
		}
		
		$this->set_title('{title}', phrase('announcements'))
		->set_icon('mdi mdi-bullhorn')
		->where('announcement_slug', $slug)
		->limit(1)
		->render($this->_table);
	}
}
