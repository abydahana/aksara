<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Drawing Tools > Polygon > Category
 *
 * @version			2.1.0
 * @author			Aby Dahana
 * @profile			abydahana.github.io
 */
class Category extends Aksara
{
	private $_table									= 'gis__polygon_category';
	
	public function __construct()
	{
		parent::__construct();
		
		$this->set_theme('backend');
	}
	
	public function index()
	{
		$this->set_title(phrase('category'))
		->set_icon('mdi mdi-sitemap')
		->unset_column('id')
		->unset_field('id')
		->unset_view('id')
		
		->set_field
		(
			array
			(
				'description'						=> 'textarea',
				'status'							=> 'boolean'
			)
		)
		
		->set_validation
		(
			array
			(
				'title'								=> 'required',
				'description'						=> 'required',
				'status'							=> 'is_boolean'
			)
		)
		
		->render($this->_table);
	}
}
