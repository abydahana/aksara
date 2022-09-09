<?php

namespace Aksara\Modules\Cms\Controllers\Blogs;

/**
 * CMS > Blogs
 *
 * @author			Aby Dahana <abydahana@gmail.com>
 * @profile			abydahana.github.io
 * @website			www.aksaracms.com
 * @since			version 4.0.0
 * @copyright		(c) 2021 - Aksara Laboratory
 */

class Blogs extends \Aksara\Laboratory\Core
{
	private $_table									= 'blogs';
	
	public function __construct()
	{
		parent::__construct();
		
		$this->restrict_on_demo();
		
		$this->set_permission();
		$this->set_theme('backend');
	}
	
	public function index()
	{
		$this->add_filter($this->_filter());
		
		if(service('request')->getGet('category'))
		{
			$query									= $this->model->get_where
			(
				'blogs__categories',
				array
				(
					'category_id'					=> service('request')->getGet('category')
				)
			)
			->row();
			
			if($query)
			{
				$this->set_description
				('
					<div class="row">
						<div class="col-4 col-sm-3 col-md-2 text-muted text-uppercase">
							' . phrase('category') . '
						</div>
						<div class="col-8 col-sm-9 col-md-4 fw-bold">
							' . $query->category_title . '
						</div>
					</div>
				')
				->unset_field('post_category')
				->set_default
				(
					array
					(
						'post_category'				=> $query->category_id
					)
				)
				->where
				(
					array
					(
						'post_category'				=> $query->category_id
					)
				);
			}
		}
		
		if(service('request')->getGet('language'))
		{
			$this->where('language_id', service('request')->getGet('language'));
		}
		
		$this->set_title(phrase('blogs'))
		->set_icon('mdi mdi-newspaper')
		->set_primary('post_id')
		->unset_column('post_id, post_excerpt, post_slug, post_content, post_tags, created_timestamp, updated_timestamp, headline, language')
		->unset_field('post_id, author')
		->unset_view('post_id')
		->column_order('featured_image, post_title, category_title, first_name, headline, updated_timestamp, status')
		->field_order('post_title, post_slug, post_excerpt, post_content, featured_image, post_category, post_tags, language_id, headline, status')
		->view_order('post_title, post_slug, post_excerpt, post_content, featured_image, post_category, post_tags, headline, status')
		->set_field
		(
			array
			(
				'post_excerpt'						=> 'textarea',
				'post_content'						=> 'wysiwyg',
				'post_tags'							=> 'tagsinput',
				'created_timestamp'					=> 'current_timestamp',
				'updated_timestamp'					=> 'current_timestamp',
				'author'							=> 'current_user',
				'headline'							=> 'boolean',
				'featured_image'					=> 'image',
				'status'							=> 'boolean'
			)
		)
		->set_field('post_slug', 'to_slug', 'post_title')
		->set_field('post_title', 'hyperlink', 'blogs/read', array('post_slug' => 'post_slug'), true)
		->set_field('category_title', 'hyperlink', 'cms/blogs', array('category' => 'post_category'))
		
		->add_action('option', '../../blogs/read', phrase('view_post'), 'btn-success', 'mdi mdi-eye', array('post_id' => 'post_id'), true)
		
		->set_validation
		(
			array
			(
				'post_title'						=> 'required|max_length[256]|is_unique[' . $this->_table . '.post_title,post_id,' . service('request')->getGet('post_id') . ']',
				'post_content'						=> 'required',
				'post_category'						=> 'required',
				'headline'							=> 'boolean',
				'status'							=> 'boolean'
			)
		)
		->set_relation
		(
			'post_category',
			'blogs__categories.category_id',
			'{blogs__categories.category_title}'
		)
		->set_relation
		(
			'author',
			'app__users.user_id',
			'{app__users.first_name} {app__users.last_name}'
		)
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
		->merge_content('{first_name} {last_name}', phrase('author'))
		->set_alias
		(
			array
			(
				'post_title'						=> phrase('title'),
				'post_slug'							=> phrase('slug'),
				'post_excerpt'						=> phrase('excerpt'),
				'post_content'						=> phrase('content'),
				'featured_image'					=> phrase('cover'),
				'post_category'						=> phrase('category'),
				'post_tags'							=> phrase('tags'),
				'category_title'					=> phrase('category'),
				'headline'							=> phrase('headline'),
				'status'							=> phrase('status'),
				'created_timestamp'					=> phrase('created'),
				'updated_timestamp'					=> phrase('updated'),
				'language'							=> phrase('language'),
				'language_id'						=> phrase('language')
			)
		)
		->field_position
		(
			array
			(
				'post_category'						=> 2,
				'post_tags'							=> 2,
				'status'							=> 2,
				'headline'							=> 2,
				'featured_image'					=> 2,
				'language_id'						=> 2
			)
		)
		->column_size
		(
			array
			(
				1									=> 'col-md-8',
				2									=> 'col-md-4'
			)
		)
		->modal_size('modal-xl')
		->set_default
		(
			array
			(
				'author'							=> get_userdata('user_id')
			)
		)
		
		->render($this->_table);
	}
	
	private function _filter()
	{
		$categories									= '<option value="0">' . phrase('all_categories') . '</option>';
		
		$categories_query							= $this->model->get_where
		(
			'blogs__categories',
			array
			(
				'status'							=> 1
			)
		)
		->result();
		
		if($categories_query)
		{
			foreach($categories_query as $key => $val)
			{
				$categories							.= '<option value="' . $val->category_id . '"' . ($val->category_id == service('request')->getGet('category') ? ' selected' : null) . '>' . $val->category_title . '</option>';
			}
		}
		
		$languages									= '<option value="0">' . phrase('all_languages') . '</option>';
		
		$languages_query							= $this->model->get_where
		(
			'app__languages',
			array
			(
				'status'							=> 1
			)
		)
		->result();
		
		if($languages_query)
		{
			foreach($languages_query as $key => $val)
			{
				$languages							.= '<option value="' . $val->id . '"' . ($val->id == service('request')->getGet('language') ? ' selected' : null) . '>' . $val->language . '</option>';
			}
		}
		
		return '
			<select name="category" class="form-control form-control-sm bordered" placeholder="' . phrase('category') . '">
				' . $categories . '
			</select>
			<select name="language" class="form-control form-control-sm bordered" placeholder="' . phrase('language') . '">
				' . $languages . '
			</select>
		';
	}
}
