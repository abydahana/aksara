<?php

namespace Aksara\Modules\Galleries\Controllers;

/**
 * Galleries
 *
 * @author			Aby Dahana <abydahana@gmail.com>
 * @profile			abydahana.github.io
 * @website			www.aksaracms.com
 * @since			version 4.0.0
 * @copyright		(c) 2021 - Aksara Laboratory
 */

class Galleries extends \Aksara\Laboratory\Core
{
	private $_table									= 'galleries';
	
	public function __construct()
	{
		parent::__construct();
		
		if(service('request')->getGet('gallery_slug'))
		{
			return throw_exception(301, null, go_to(service('request')->getGet('gallery_slug')));
		}
		
		$this->searchable(false);
		$this->limit(10);
	}
	
	public function index()
	{
		$this->set_title(phrase('our_gallery_activities'))
		->set_description(phrase('our_gallery_activities'))
		->set_icon('mdi mdi-folder-multiple-image')
		->set_primary('gallery_slug')
		->order_by('gallery_id', 'DESC')
		->where('status', 1)
		
		->render($this->_table);
	}
}
