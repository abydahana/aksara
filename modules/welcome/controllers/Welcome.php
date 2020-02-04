<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Welcome Page
 * The default landing page of default routes. This module is override the
 * original source in the application folder.
 *
 * @version			2.1.0
 * @author			Aby Dahana
 * @profile			abydahana.github.io
 */
class Welcome extends Aksara
{
	public function __construct()
	{
		parent::__construct();
		
		$this->load->library('user_agent');
	}
	public function index()
	{
		$this->set_title(phrase('welcome'))
		->set_description(phrase('app_description'))
		->set_output
		(
			array
			(
				'carousels'							=> json_decode
				(
					$this->model->select
					('
						carousel_content
					')
					->get_where
					(
						'pages__carousels',
						array
						(
							'carousel_id'			=> 1
						),
						1
					)
					->row('carousel_content')
				),
				
				/* get the headline news */
				'headline_news'						=> $this->model->select
				('
					blogs.post_slug,
					blogs.post_title,
					blogs.post_excerpt,
					blogs.featured_image,
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
						'blogs.status'				=> 1,
						'blogs.headline'			=> 1
					),
					6
				)
				->result(),
				
				/* get the latest news */
				'news_categories'					=> $this->model->select
				('
					blogs__categories.*,
					COUNT(blogs.post_id) AS total_data
				')
				->join
				(
					'blogs',
					'blogs.post_category = blogs__categories.category_id'
				)
				->order_by('category_title', 'RANDOM')
				->group_by('category_id')
				->get_where
				(
					'blogs__categories',
					array
					(
						'blogs__categories.status'	=> 1,
						'blogs.status'				=> 1
					)
				)
				->result(),
				
				/* get news item per category */
				'news_items'						=> $this->model->select
				('
					blogs__categories.*,
					blogs.post_id,
					blogs.post_slug,
					blogs.post_title,
					blogs.post_excerpt,
					blogs.post_tags,
					blogs.featured_image,
					blogs.updated_timestamp
				')
				->join
				(
					'blogs',
					'blogs.post_category = blogs__categories.category_id'
				)
				->get_where
				(
					'blogs__categories',
					array
					(
						'blogs__categories.status'	=> 1,
						'blogs.status'				=> 1
					),
					6
				)
				->result(),
				
				/* get the latest galleries */
				'latest_galleries'					=> $this->model->get_where
				(
					'galleries',
					array
					(
						'status'					=> 1
					),
					4
				)
				->result(),
				
				/* get the latest peoples */
				'latest_peoples'					=> $this->model->get_where
				(
					'peoples',
					array
					(
						'status'					=> 1
					),
					4
				)
				->result(),
				
				'stories'							=> json_decode
				(
					$this->model->select
					('
						carousel_content
					')
					->get_where
					(
						'pages__carousels',
						array
						(
							'carousel_id'			=> 2
						),
						1
					)
					->row('carousel_content')
				)
			)
		)
		->render();
	}
}
