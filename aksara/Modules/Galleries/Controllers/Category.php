<?php

namespace Aksara\Modules\Galleries\Controllers;

/**
 * Galleries > Category
 * Show the gallery under the category
 *
 * @author			Aby Dahana <abydahana@gmail.com>
 * @profile			abydahana.github.io
 * @website			www.aksaracms.com
 * @since			version 4.0.0
 * @copyright		(c) 2021 - Aksara Laboratory
 */

class Category extends \Aksara\Laboratory\Core
{
	private $_table									= 'galleries';
	
	public function __construct()
	{
		parent::__construct();
		
		$this->searchable(false);
		$this->limit(10);
		
		$this->_primary								= service('request')->getGet('gallery_id');
	}
	
	public function index($slug = null)
	{
		if(!$slug && service('request')->getGet('gallery_slug'))
		{
			$slug									= service('request')->getGet('gallery_slug');
		}
		
		$this->set_title('{gallery_title}', phrase('gallery_was_not_found'))
		->set_description('{gallery_description}')
		->set_icon('mdi mdi-image')
		->where('gallery_slug', $slug)
		->limit(1)
		
		->render($this->_table);
	}
}