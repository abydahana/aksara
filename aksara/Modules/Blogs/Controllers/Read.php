<?php namespace Aksara\Modules\Blogs\Controllers;
/**
 * Blogs > Read
 *
 * @author			Aby Dahana
 * @profile			abydahana.github.io
 * @website			www.aksaracms.com
 * @since			version 4.0.0
 * @copyright		(c) 2021 - Aksara Laboratory
 */
use Aksara\Laboratory\Core;

class Read extends Core
{
	public function __construct()
	{
		parent::__construct();
	}
	
	public function index($category = null, $slug = null)
	{
		if(service('request')->getGet('post_slug'))
		{
			$slug									= service('request')->getGet('post_slug');
			$category								= $this->model->select
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
					'blogs.post_slug'			=> $slug
				),
				1
			)
			->row('category_slug');
		}
		
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
				->where
				(
					array
					(
						'blogs.status'				=> 1
					)
				)
				->order_by('total_data', 'DESC')
				->group_by('category_id, category_slug, category_title, category_description, category_image')
				->get('blogs__categories')
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
				->where
				(
					array
					(
						'blogs__categories.category_slug'	=> $category,
						'blogs.post_slug !='		=> $slug,
						'blogs.status'				=> 1
					)
				)
				->order_by('blogs.updated_timestamp', 'DESC')
				->limit(10)
				->get('blogs')
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
		
		->render('blogs');
	}
}
