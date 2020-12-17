<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Galleries > Category
 * Show the gallery under the category
 *
 * @version			2.1.1
 * @author			Aby Dahana
 * @profile			abydahana.github.io
 */
class Category extends Aksara
{
	private $_table									= 'galleries';
	
	public function __construct()
	{
		parent::__construct();
		
		$this->parent_module('galleries');
		
		$this->_primary								= $this->input->get('gallery_id');
	}
	
	public function index($slug = null)
	{
		if(!$slug && $this->input->get('gallery_slug'))
		{
			$slug									= $this->input->get('gallery_slug');
		}
		
		$this->set_title('{gallery_title}', phrase('gallery_was_not_found'))
		->set_description('{gallery_description}')
		->set_icon('mdi mdi-image')
		->where('gallery_slug', $slug)
		->limit(1)
		->render($this->_table);
	}
}