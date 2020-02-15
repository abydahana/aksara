<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Drawing Tools > Polygon
 *
 * @version			2.1.0
 * @author			Aby Dahana
 * @profile			abydahana.github.io
 */
class Polygon extends Aksara
{
	private $_table									= 'gis__polygon';
	
	public function __construct()
	{
		parent::__construct();
		
		$this->set_theme('backend');
	}
	
	public function index()
	{
		$this->set_title(phrase('polygon'))
		->set_icon('mdi mdi-vector-polygon')
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
				'geometry'							=> 'polygon',
				'status'							=> 'boolean'
			)
		)
		
		->set_relation
		(
			'category_id',
			'gis__polygon_category.id',
			'{gis__polygon_category.title AS category}'
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
