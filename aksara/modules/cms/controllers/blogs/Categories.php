<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * CMS > Blogs > Categories
 * Manage the blog categories
 *
 * @version			2.1.1
 * @author			Aby Dahana
 * @profile			abydahana.github.io
 */
class Categories extends Aksara
{
	private $_table									= 'blogs__categories';
	
	public function __construct()
	{
		parent::__construct();
		
		$this->restrict_on_demo();
		
		$this->set_permission(array(1, 2));
		$this->set_theme('backend');
		
		$this->set_upload_path('blogs');
		$this->unset_delete('category_id', array(1));
	}
	
	public function index()
	{
		$this->set_title(phrase('blog_categories'))
		->set_icon('mdi mdi-sitemap')
		->set_primary('category_id')
		->unset_column('category_id, language')
		->unset_field('category_id')
		->unset_view('category_id')
		->column_order('category_image')
		->set_field
		(
			array
			(
				'category_image'					=> 'image',
				'category_description'				=> 'textarea',
				'status'							=> 'boolean'
			)
		)
		->set_field('category_slug', 'to_slug', 'category_title')
		->set_relation
		(
			'language_id',
			'app__languages.id',
			'{app__languages.language}',
			array
			(
				'app__languages.status'				=> 1
			)
		)
		->set_validation
		(
			array
			(
				'category_title'					=> 'required|max_length[64]|is_unique[' . $this->_table . '.category_title.category_id.' . $this->input->get('category_id') . ']',
				'category_description'				=> 'required',
				'status'							=> 'is_boolean'
			)
		)
		->set_alias
		(
			array
			(
				'category_image'					=> phrase('image'),
				'category_title'					=> phrase('title'),
				'category_slug'						=> phrase('slug'),
				'category_description'				=> phrase('description'),
				'language'							=> phrase('language'),
				'language_id'						=> phrase('language')
			)
		)
		->render($this->_table);
	}
}
