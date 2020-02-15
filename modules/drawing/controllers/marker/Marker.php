<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Drawing Tools >Marker
 *
 * @version			2.1.0
 * @author			Aby Dahana
 * @profile			abydahana.github.io
 */
class Marker extends Aksara
{
	private $_table									= 'gis__coordinate';
	
	public function __construct()
	{
		parent::__construct();
		
		$this->set_theme('backend');
	}
	
	public function index()
	{
		$this->set_title(phrase('marker'))
		->set_icon('mdi mdi-map-marker')
		->unset_column('id, coordinate')
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
				'coordinate'						=> 'coordinate',
				'status'							=> 'boolean'
			)
		)
		
		->set_relation
		(
			'category_id',
			'gis__coordinate_category.id',
			'{gis__coordinate_category.title AS category}'
		)
		
		->add_class('location', 'map-address-listener')
		
		->set_validation
		(
			array
			(
				'title'								=> 'required',
				'description'						=> 'required',
				'coordinate'						=> 'required',
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
