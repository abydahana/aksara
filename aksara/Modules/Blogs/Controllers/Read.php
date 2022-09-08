<?php

namespace Aksara\Modules\Blogs\Controllers;

/**
 * Blogs > Read
 *
 * @author			Aby Dahana <abydahana@gmail.com>
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
		if($slug)
		{
			$this->where
			(
				array
				(
					'blogs.post_slug'				=> $slug,
					'blogs.status'					=> 1
				)
			);
		}
		else if(service('request')->getGet('post_id'))
		{
			$this->where('post_id', service('request')->getGet('post_id'));
		}
		
		$this->set_title('{post_title}', phrase('no_post_were_found'))
		->set_icon('mdi mdi-newspaper')
		->set_description('{post_excerpt}')
		->set_output
		(
			array
			(
				/* get similar categories */
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
				->group_by('blogs.language_id, category_id, category_slug, category_title, category_description, category_image')
				->order_by('(CASE WHEN blogs.language_id = ' . get_userdata('language_id') . ' THEN 1 ELSE 2 END)', 'ASC')
				->get_where
				(
					'blogs__categories',
					array
					(
						'blogs.status'				=> 1
					)
				)
				->result(),
				
				/* get similar articles */
				'similar'							=> $this->model->select
				('
					blogs.post_slug,
					blogs.post_title,
					blogs.post_excerpt,
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
				->order_by('(CASE WHEN blogs.language_id = ' . get_userdata('language_id') . ' THEN 1 ELSE 2 END)', 'ASC')
				->limit(10)
				->get_where
				(
					'blogs',
					array
					(
						'category_slug'				=> ($category ? $category : ''),
						'post_slug != '				=> ($slug ? $slug : ''),
						'blogs.status'				=> 1
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
		->order_by('(CASE WHEN blogs.language_id = ' . get_userdata('language_id') . ' THEN 1 ELSE 2 END)', 'ASC')
		->limit(1)
		
		->render($this->_table);
	}
}
