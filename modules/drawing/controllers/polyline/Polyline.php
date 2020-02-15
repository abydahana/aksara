<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Drawing Tools > Polyline
 *
 * @version			2.1.0
 * @author			Aby Dahana
 * @profile			abydahana.github.io
 */
class Polyline extends Aksara
{
	private $_table									= 'gis__polyline';
	
	public function __construct()
	{
		parent::__construct();
		
		$this->set_theme('backend');
	}
	
	public function index()
	{
		$this->set_title(phrase('polyline'))
		->set_icon('mdi mdi-vector-polyline')
		->unset_column('id, geometry')
		->unset_field('id')
		->unset_view('id')
		
		->column_order('title, category')
		->field_order('category_id, title')
		
		->set_field
		(
			array
			(
				'description'						=> 'textarea',
				'images'							=> 'images',
				'geometry'							=> 'linestring',
				'status'							=> 'boolean'
			)
		)
		
		->set_relation
		(
			'category_id',
			'gis__polyline_category.id',
			'{gis__polyline_category.title AS category}'
		)
		
		->set_validation
		(
			array
			(
				'title'								=> 'required',
				'description'						=> 'required',
				'geometry'							=> 'required',
				'status'							=> 'is_boolean'
			)
		)
		
		->set_alias
		(
			array
			(
				'category_id'						=> phrase('category')
			)
		)
		
		->render($this->_table);
	}
}
