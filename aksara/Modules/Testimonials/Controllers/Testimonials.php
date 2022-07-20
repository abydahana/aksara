<?php

namespace Aksara\Modules\Testimonials\Controllers;

/**
 * Testimonials
 *
 * @author			Aby Dahana <abydahana@gmail.com>
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
		
		$this->searchable(false);
		$this->limit(10);
	}
	
	public function index()
	{
		$this->set_title(phrase('testimonials'))
		->set_description(phrase('what_they_say_about_us'))
		->set_icon('mdi mdi-bullhorn-outline')
		
		->where
		(
			array
			(
				'status'							=> 1,
				'language_id'						=> get_userdata('language_id')
			)
		)
		
		->order_by('timestamp', 'desc')
		->order_by('(CASE WHEN language_id = ' . get_userdata('language_id') . ' THEN 1 ELSE 2 END)', 'ASC')
		
		->render($this->_table);
	}
}
