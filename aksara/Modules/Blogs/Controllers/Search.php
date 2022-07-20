<?php

namespace Aksara\Modules\Blogs\Controllers;

/**
 * Blogs > Search
 *
 * @author			Aby Dahana <abydahana@gmail.com>
 * @profile			abydahana.github.io
 * @website			www.aksaracms.com
 * @since			version 4.0.0
 * @copyright		(c) 2021 - Aksara Laboratory
 */

class Search extends \Aksara\Laboratory\Core
{
	public function __construct()
	{
		parent::__construct();
		
		$this->_keywords							= (service('request')->getGet('q') || service('request')->getPost('q') ? htmlspecialchars((service('request')->getPost('q') ? service('request')->getPost('q') : service('request')->getGet('q'))) : null);
	}
	
	public function index()
	{
		if(service('request')->getGet('category'))
		{
			$this->where('blogs__categories.category_slug', service('request')->getGet('category'));
		}
		
		$this->set_title(phrase('search'))
		->set_description(phrase('search_result_for') . ' ' . ($this->_keywords ? $this->_keywords : (service('request')->getGet('category') ? '{category_title}' : phrase('all'))))
		->set_icon('mdi mdi-magnify')
		
		->set_output
		(
			array
			(
				'keywords'							=> $this->_keywords,
				
				/* category detail */
				'category'							=> $this->model->get_where
				(
					'blogs__categories',
					array
					(
						'category_slug'				=> service('request')->getGet('category')
					),
					1
				)
				->row()
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
		
		->group_start()
		->like('blogs.post_title', $this->_keywords)
		->or_like('blogs.post_excerpt', $this->_keywords)
		->group_end()
		
		->where
		(
			array
			(
				'blogs.status'						=> 1
			)
		)
		
		->order_by('blogs.updated_timestamp', 'DESC')
		->order_by('(CASE WHEN blogs.language_id = ' . get_userdata('language_id') . ' THEN 1 ELSE 2 END)', 'ASC')
		
		->render('blogs');
	}
}
