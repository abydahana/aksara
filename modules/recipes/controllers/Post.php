<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Recipes > Post
 *
 * @version			2.1.0
 * @author			Aby Dahana
 * @profile			abydahana.github.io
 */
class Post extends Aksara
{
	private $_table									= 'guide__recipes';
	
	public function __construct()
	{
		parent::__construct();
	}
	
	public function index()
	{
		$this->set_title(phrase('recipes'))
		->unset_column('recipe_id, author')
		->unset_field('recipe_id, author')
		->unset_view('recipe_id, author')
		
		->set_field
		(
			array
			(
				'cover'								=> 'image',
				'description'						=> 'textarea',
				'cooking_time'						=> 'number_format',
				'preparation'						=> 'number_format',
				'serving'							=> 'number_format',
				'ingredients'						=> 'attributes',
				'steps'								=> 'steps',
				'status'							=> 'boolean'
			)
		)
		->set_field
		(
			'difficulty',
			'dropdown',
			array
			(
				0									=> phrase('beginner'),
				1									=> phrase('basic'),
				2									=> phrase('intermediate'),
				3									=> phrase('advance')
			)
		)
		
		->merge_field('cooking_time, difficulty')
		->merge_field('preparation, serving')
		
		->field_append
		(
			array
			(
				'cooking_time'						=> phrase('minutes'),
				'preparation'						=> phrase('minutes'),
				'serving'							=> phrase('portion')
			)
		)
		
		->set_validation
		(
			array
			(
				'title'								=> 'required|xss_clean|is_unique[' . $this->_table . '.title.recipe_id.' . $this->input->get('recipe_id') . ']',
				'cooking_time'						=> 'required|numeric|greater_than[0]',
				'difficulty'						=> 'required|in_list[0,1,2,3]',
				'preparation'						=> 'required|numeric|greater_than[0]',
				'serving'							=> 'required|numeric|greater_than[0]',
				'ingredients'						=> 'required|xss_clean',
				'steps'								=> 'required|xss_clean',
				'status'							=> 'is_boolean'
			)
		)
		->render($this->_table);
	}
}
