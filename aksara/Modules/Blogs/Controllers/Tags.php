<?php

namespace Aksara\Modules\Blogs\Controllers;

/**
 * Blogs > Tags
 *
 * @author			Aby Dahana <abydahana@gmail.com>
 * @profile			abydahana.github.io
 * @website			www.aksaracms.com
 * @since			version 4.0.0
 * @copyright		(c) 2021 - Aksara Laboratory
 */

class Tags extends \Aksara\Laboratory\Core
{
	public function __construct()
	{
		parent::__construct();
		
		$this->limit(24);
		
		$this->_keywords							= (service('request')->getGet('q') || service('request')->getPost('q') ? htmlspecialchars((service('request')->getPost('q') ? service('request')->getPost('q') : service('request')->getGet('q'))) : null);
	}
	
	public function index()
	{
		$this->set_title('#' . $this->_keywords)
		->set_description(phrase('post_tagged_with') . ' #' . $this->_keywords)
		->set_icon('mdi mdi-pound')
		
		->set_output
		(
			array
			(
				'keywords'							=> $this->_keywords
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
		
		->like('blogs.post_tags', $this->_keywords)
		
		->where
		(
			array
			(
				'blogs.status'						=> 1
			)
		)
		
		->order_by('(CASE WHEN blogs.language_id = ' . get_userdata('language_id') . ' THEN 1 ELSE 2 END)', 'ASC')
		
		->render('blogs');
	}
}
