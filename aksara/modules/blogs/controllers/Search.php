<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Blogs > Search
 *
 * @version			2.1.1
 * @author			Aby Dahana
 * @profile			abydahana.github.io
 */
class Search extends Aksara
{
	public function __construct()
	{
		parent::__construct();
		
		$this->parent_module('blogs');
		
		$this->_keywords							= htmlspecialchars(($this->input->post('q') ? $this->input->post('q') : $this->input->get('q')));
	}
	
	public function index()
	{
		if($this->input->get('category'))
		{
			$this->where('blogs__categories.category_slug', $this->input->get('category'));
		}
		
		$this->set_title(phrase('search'))
		->set_description(phrase('search_result_for') . ' ' . ($this->_keywords ? $this->_keywords : ($this->input->get('category') ? '{category_title}' : phrase('all'))))
		->set_icon('mdi mdi-magnify')
		
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
						'category_slug'				=> $this->input->get('category')
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
		->like
		(
			array
			(
				'blogs.post_title'					=> $this->_keywords
			)
		)
		->or_like
		(
			array
			(
				'blogs.post_excerpt'				=> $this->_keywords
			)
		)
		->order_by('blogs.updated_timestamp', 'DESC')
		->render('blogs');
	}
}
