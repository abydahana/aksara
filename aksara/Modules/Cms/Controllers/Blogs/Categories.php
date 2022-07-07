<?php

namespace Aksara\Modules\Cms\Controllers\Blogs;

/**
 * CMS > Blogs > Categories
 * Manage the blog categories
 *
 * @author			Aby Dahana <abydahana@gmail.com>
 * @profile			abydahana.github.io
 * @website			www.aksaracms.com
 * @since			version 4.0.0
 * @copyright		(c) 2021 - Aksara Laboratory
 */

class Categories extends \Aksara\Laboratory\Core
{
	private $_table									= 'blogs__categories';
	
	public function __construct()
	{
		parent::__construct();
		
		$this->restrict_on_demo();
		
		$this->set_permission();
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
		->set_field('category_title', 'hyperlink', 'cms/blogs', array('category' => 'category_id'))
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
				'category_title'					=> 'required|max_length[64]|unique[' . $this->_table . '.category_title.category_id.' . service('request')->getGet('category_id') . ']',
				'category_description'				=> 'required',
				'status'							=> 'boolean'
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
