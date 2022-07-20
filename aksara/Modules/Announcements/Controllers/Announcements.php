<?php

namespace Aksara\Modules\Announcements\Controllers;

/**
 * Announcements
 *
 * @author			Aby Dahana <abydahana@gmail.com>
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
		
		$this->searchable(false);
		$this->limit(10);
	}
	
	public function index()
	{
		$this->set_title(phrase('announcements'))
		->set_description(phrase('announcements'))
		->set_icon('mdi mdi-bullhorn-outline')
		->set_primary('announcements_slug')
		
		->where
		(
			array
			(
				'status'							=> 1,
				'end_date > '						=> date('Y-m-d'),
				'language_id'						=> get_userdata('language_id')
			)
		)
		
		->order_by('end_date', 'DESC')
		->order_by('(CASE WHEN language_id = ' . get_userdata('language_id') . ' THEN 1 ELSE 2 END)', 'ASC')
		
		->render($this->_table);
	}
}
