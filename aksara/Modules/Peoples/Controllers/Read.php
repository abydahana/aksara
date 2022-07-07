<?php

namespace Aksara\Modules\Peoples\Controllers;

/**
 * Peoples > Read
 * Show the selected people details
 *
 * @author			Aby Dahana <abydahana@gmail.com>
 * @profile			abydahana.github.io
 * @website			www.aksaracms.com
 * @since			version 4.0.0
 * @copyright		(c) 2021 - Aksara Laboratory
 */

class Read extends \Aksara\Laboratory\Core
{
	private $_table									= 'peoples';
	
	public function __construct()
	{
		parent::__construct();
		
		$this->searchable(false);
	}
	
	public function index($slug = null)
	{
		if(service('request')->getGet('people_slug'))
		{
			$slug									= service('request')->getGet('people_slug');
		}
		
		$this->set_title('{first_name} {last_name}', phrase('the_people_you_are_looking_for_was_not_found'))
		->set_description('{biography}')
		->set_icon('mdi mdi-account-outline')
		->set_output
		(
			'similar',
			$this->model
			->get_where
			(
				$this->_table,
				array
				(
					'people_slug !='					=> $slug
				),
				4
			)
			->result()
		)
		->where('people_slug', $slug)
		->limit(1)
		
		->render($this->_table);
	}
}
