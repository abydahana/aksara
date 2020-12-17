<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Testimonials
 *
 * @version			2.1.1
 * @author			Aby Dahana
 * @profile			abydahana.github.io
 */
class Testimonials extends Aksara
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
