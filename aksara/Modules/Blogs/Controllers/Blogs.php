<?php

namespace Aksara\Modules\Blogs\Controllers;

/**
 * Blogs
 *
 * @author			Aby Dahana <abydahana@gmail.com>
 * @profile			abydahana.github.io
 * @website			www.aksaracms.com
 * @since			version 4.0.0
 * @copyright		(c) 2021 - Aksara Laboratory
 */

class Blogs extends \Aksara\Laboratory\Core
{
	public function __construct()
	{
		parent::__construct();
		
		$this->searchable(false);
	}
	
	public function index()
	{
		$this->set_title(phrase('our_news_updates'))
		->set_description(phrase('follow_an_updates_from_us'))
		->set_icon('mdi mdi-newspaper')
		->set_output
		(
			array
			(
				/* get highlighed articles */
				'spotlight'							=> $this->_get_spotlight(),
				
				/* get latest articles */
				'articles'							=> $this->_get_articles()
			)
		)
		
		->render();
	}
	
	private function _get_spotlight()
	{
		$query										= $this->model->select
		('
			blogs.post_id,
			blogs.post_slug,
			blogs.post_title,
			blogs.post_excerpt,
			blogs.featured_image,
			blogs.updated_timestamp,
			blogs__categories.category_title,
			blogs__categories.category_slug,
			app__users.username,
			app__users.first_name,
			app__users.last_name,
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
		->order_by('updated_timestamp', 'DESC')
		->order_by('(CASE WHEN blogs.language_id = ' . get_userdata('language_id') . ' THEN 1 ELSE 2 END)', 'ASC')
		->get_where
		(
			'blogs',
			array
			(
				'blogs.status'						=> 1,
				'blogs.headline'					=> 1
			),
			9
		)
		->result();
		
		$output										= array();
		
		if($query)
		{
			foreach($query as $key => $val)
			{
				$output[]							= $val;
			}
		}
		
		return $output;
	}
	
	private function _get_articles()
	{
		$query										= $this->model->get_where
		(
			'blogs__categories',
			array
			(
				'status'							=> 1
			)
		)
		->result();
		
		$output										= array();
		
		if($query)
		{
			foreach($query as $key => $val)
			{
				$articles							= $this->model->select
				('
					blogs.post_id,
					blogs.post_slug,
					blogs.post_title,
					blogs.post_excerpt,
					blogs.featured_image,
					blogs.updated_timestamp,
					app__users.username,
					app__users.first_name,
					app__users.last_name,
					app__users.photo
				')
				->join
				(
					'app__users',
					'app__users.user_id = blogs.author'
				)
				->order_by('updated_timestamp', 'DESC')
				->order_by('(CASE WHEN blogs.language_id = ' . get_userdata('language_id') . ' THEN 1 ELSE 2 END)', 'ASC')
				->get_where
				(
					'blogs',
					array
					(
						'blogs.post_category'		=> $val->category_id,
						'blogs.status'				=> 1
					),
					6
				)
				->result();
				
				$posts								= array();
				
				if($articles)
				{
					foreach($articles as $_key => $_val)
					{
						$posts[]					= $_val;
					}
				}
				
				$output[]							= array
				(
					'category_id'					=> $val->category_id,
					'category_title'				=> $val->category_title,
					'category_slug'					=> $val->category_slug,
					'category_description'			=> $val->category_description,
					'category_image'				=> $val->category_image,
					'posts'							=> $posts
				);
			}
		}
		
		return $output;
	}
}
