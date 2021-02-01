<?php namespace Aksara\Modules\Blogs\Controllers;
/**
 * Blogs > Tags
 *
 * @author			Aby Dahana
 * @profile			abydahana.github.io
 * @website			www.aksaracms.com
 * @since			version 4.0.0
 * @copyright		(c) 2021 - Aksara Laboratory
 */
use Aksara\Laboratory\Core;

class Tags extends Core
{
	public function __construct()
	{
		parent::__construct();
		
		$this->_keywords							= htmlspecialchars((service('request')->getPost('q') ? service('request')->getPost('q') : service('request')->getGet('q')));
	}
	
	public function index()
	{
		$this->set_title('#' . $this->_keywords)
		->set_description(phrase('post_tagged_with') . ' #' . $this->_keywords)
		->set_icon('mdi mdi-pound')
		
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
		->where('blogs.status', 1)
		
		->render('blogs');
	}
}
