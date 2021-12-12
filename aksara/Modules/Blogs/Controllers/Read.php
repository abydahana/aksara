<?php

namespace Aksara\Modules\Blogs\Controllers;

/**
 * Blogs > Read
 *
 * @author			Aby Dahana
 * @profile			abydahana.github.io
 * @website			www.aksaracms.com
 * @since			version 4.0.0
 * @copyright		(c) 2021 - Aksara Laboratory
 */

class Read extends \Aksara\Laboratory\Core
{
	private $_table									= 'blogs';
	
	public function __construct()
	{
		parent::__construct();
		
		$this->searchable(false);
	}
	
	public function index($category = null, $slug = null)
	{
		if(!$slug)
		{
			$slug									= service('request')->getGet('post_slug');
		}
		
		$category									= $this->model->select
		('
			blogs__categories.category_slug
		')
		->join
		(
			'blogs__categories',
			'blogs__categories.category_id = blogs.post_category'
		)
		->get_where
		(
			'blogs',
			array
			(
				'blogs.post_slug'					=> service('request')->getGet('post_slug')
			),
			1
		)
		->row('category_slug');
		
		$this->set_title('{post_title}', phrase('no_post_were_found'))
		->set_icon('mdi mdi-newspaper')
		->set_description('{post_excerpt}')
		->set_output
		(
			array
			(
				'categories'						=> $this->model->select
				('
					COUNT(blogs.post_id) AS total_data,
					blogs__categories.category_slug,
					blogs__categories.category_title,
					blogs__categories.category_description,
					blogs__categories.category_image
				')
				->join
				(
					'blogs',
					'blogs.post_category = blogs__categories.category_id'
				)
				->order_by('total_data', 'DESC')
				->group_by('category_id, category_slug, category_title, category_description, category_image')
				->get_where
				(
					'blogs__categories',
					array
					(
						'blogs.status'				=> 1,
						'blogs.language_id'			=> get_userdata('language_id')
					)
				)
				->result(),
				
				'similar'							=> $this->model->select
				('
					blogs.post_slug,
					blogs.post_title,
					blogs.featured_image,
					blogs__categories.category_slug,
					blogs__categories.category_title,
					blogs__categories.category_image
				')
				->join
				(
					'blogs__categories',
					'blogs__categories.category_id = blogs.post_category'
				)
				->order_by('blogs.updated_timestamp', 'DESC')
				->limit(10)
				->get_where
				(
					'blogs',
					array
					(
						'blogs__categories.category_slug'	=> $category,
						'blogs.post_slug != '		=> $slug,
						'blogs.status'				=> 1,
						'blogs.language_id'			=> get_userdata('language_id')
					)
				)
				->result()
			)
		)
		->select
		('
			blogs.post_slug,
			blogs.post_title,
			blogs.post_excerpt,
			blogs.post_tags,
			blogs.featured_image,
			blogs.updated_timestamp,
			blogs__categories.category_slug,
			blogs__categories.category_title,
			blogs__categories.category_description,
			blogs__categories.category_image,
			app__users.first_name,
			app__users.last_name,
			app__users.username,
			app__users.photo
		')
		->join
		(
			'blogs__categories',
			'blogs__categories.category_id = blogs.post_category'
		)
		->join
		(
			'app__users',
			'app__users.user_id = blogs.author'
		)
		->where
		(
			array
			(
				'blogs.post_slug'					=> $slug,
				'blogs.status'						=> 1
			)
		)
		->limit(1)
		
		->render($this->_table);
	}
}
