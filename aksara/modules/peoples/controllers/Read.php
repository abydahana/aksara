<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Peoples > Read
 * Show the selected people details
 *
 * @version			2.1.1
 * @author			Aby Dahana
 * @profile			abydahana.github.io
 */
class Read extends Aksara
{
	private $_table									= 'peoples';
	
	public function __construct()
	{
		parent::__construct();
		
		$this->parent_module('peoples');
	}
	
	public function index($slug = null)
	{
		if($this->input->get('people_slug'))
		{
			$slug									= $this->input->get('people_slug');
		}
		
		$this->set_title('{first_name} {last_name}', phrase('people_not_found'))
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
