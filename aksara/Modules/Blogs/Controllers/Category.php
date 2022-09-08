<?php

namespace Aksara\Modules\Blogs\Controllers;

/**
 * Blogs > Category
 *
 * @author			Aby Dahana <abydahana@gmail.com>
 * @profile			abydahana.github.io
 * @website			www.aksaracms.com
 * @since			version 4.0.0
 * @copyright		(c) 2021 - Aksara Laboratory
 */

class Category extends \Aksara\Laboratory\Core
{
	public function __construct()
	{
		parent::__construct();
		
		$this->searchable(false);
		$this->limit(24);
	}
	
	public function index($slug = null)
	{
		$this->set_title('{category_title}', phrase('no_category_were_found'))
		->set_description('{category_description}')
		->set_icon('mdi mdi-sitemap')
		
		->set_output
		(
			array
			(
				/* category detail */
				'category'							=> $this->model->get_where
				(
					'blogs__categories',
					array
					(
						'category_slug'				=> $slug
					),
					1
				)
				->row(),
				
				/* list of category */
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
				->where
				(
					array
					(
						'blogs__categories.category_slug !='=> $slug,
						'blogs__categories.status'			=> 1,
						'blogs.status'						=> 1
					)
				)
				->order_by('category_title', 'RANDOM')
				->group_by('blogs.language_id, category_id, category_slug, category_title, category_description, category_image')
				->order_by('(CASE WHEN blogs.language_id = ' . get_userdata('language_id') . ' THEN 1 ELSE 2 END)', 'ASC')
				->get('blogs__categories')
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
		->order_by('blogs.updated_timestamp', 'DESC')
		->order_by('(CASE WHEN blogs.language_id = ' . get_userdata('language_id') . ' THEN 1 ELSE 2 END)', 'ASC')
		->where
		(
			array
			(
				'blogs__categories.category_slug'	=> $slug,
				'blogs.status'						=> 1
			)
		)
		
		->render('blogs');
	}
}
