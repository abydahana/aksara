<?php

namespace Aksara\Modules\Galleries\Controllers;

/**
 * Galleries > Read
 * Show the individual photo from the gallery
 *
 * @author			Aby Dahana <abydahana@gmail.com>
 * @profile			abydahana.github.io
 * @website			www.aksaracms.com
 * @since			version 4.0.0
 * @copyright		(c) 2021 - Aksara Laboratory
 */

class Read extends \Aksara\Laboratory\Core
{
	private $_table									= 'galleries';
	
	public function __construct()
	{
		parent::__construct();
		
		$this->searchable(false);
		$this->limit(10);
	}
	
	public function index($category = null, $slug = null)
	{
		$this->set_title('{gallery_title}', phrase('gallery_was_not_found'))
		->set_description('{gallery_description}')
		->set_icon('mdi mdi-image')
		->set_output
		(
			'similar',
			$this->model
			->select('gallery_images')
			->get_where
			(
				$this->_table,
				array
				(
					'gallery_slug'					=> $category
				),
				1
			)
			->row('gallery_images')
		)
		->select
		('
			' . $this->_table . '.*,
			app__users.first_name,
			app__users.last_name,
			app__users.username,
			app__users.photo
		')
		->join
		(
			'app__users',
			'app__users.user_id = ' . $this->_table . '.author'
		)
		->where
		(
			array
			(
				$this->_table . '.gallery_slug'		=> $category
			)
		)
		->limit(1)
		
		->render($this->_table);
	}
}
