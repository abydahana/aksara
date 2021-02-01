<?php namespace Aksara\Modules\Testimonials\Controllers;
/**
 * Testimonials
 *
 * @author			Aby Dahana
 * @profile			abydahana.github.io
 * @website			www.aksaracms.com
 * @since			version 4.0.0
 * @copyright		(c) 2021 - Aksara Laboratory
 */
class Testimonials extends \Aksara\Laboratory\Core
{
	private $_table									= 'testimonials';
	
	public function __construct()
	{
		parent::__construct();
	}
	
	public function index()
	{
		$this->set_title(phrase('testimonials'))
		->set_description(phrase('what_they_said_about_us'))
		->set_icon('mdi mdi-bullhorn-outline')
		->order_by('timestamp', 'desc')
		->where('status', 1)
		
		->render($this->_table);
	}
}
