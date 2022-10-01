<?php

namespace Aksara\Modules\Cms\Controllers\Comments;

/**
 * CMS > Reaactions > Feedback
 *
 * @author			Aby Dahana <abydahana@gmail.com>
 * @profile			abydahana.github.io
 * @website			www.aksaracms.com
 * @since			version 4.4.0
 * @copyright		(c) 2021 - Aksara Laboratory
 */

class Feedback extends \Aksara\Laboratory\Core
{
	private $_table									= 'post__comments_reports';
	
	public function __construct()
	{
		parent::__construct();
		
		$this->set_permission();
		$this->set_theme('backend');
		
		$this->unset_action('create, update, delete');
		
		$this->_primary								= (service('request')->getGet('id') ? service('request')->getGet('id') : 0);
	}
	
	public function index()
	{
		$query										= $this->model->get_where
		(
			'post__comments',
			array
			(
				'comment_id'						=> $this->_primary
			),
			1
		)
		->row();
		
		if($query)
		{
			// blogs type comment
			if($query->post_type == 'blog')
			{
				$this->model->select
				('
					blogs.post_slug,
					blogs.post_title,
					
					blogs__categories.category_slug
				')
				->join
				(
					'blogs',
					'blogs.post_id = post__comments.post_id'
				)
				->join
				(
					'blogs__categories',
					'blogs__categories.category_id = blogs.post_category'
				);
			}
		}
		
		$query										= $this->model->select
		('
			post__comments.comment_id,
			
			app__users.user_id,
			app__users.first_name,
			app__users.last_name
		')
		->join
		(
			'app__users',
			'app__users.user_id = post__comments.user_id'
		)
		->get_where
		(
			'post__comments',
			array
			(
				'post__comments.comment_id'			=> $this->_primary
			),
			1
		)
		->row();
		
		if($query)
		{
			$this->set_description
			('
				<div class="row border-bottom">
					<div class="col-sm-4 col-md-2">
						' . phrase('user') . '
					</div>
					<div class="col-sm-8 col-md-10">
						<a href="' . base_url('user', array('user_id' => $query->user_id)) . '" target="_blank">
							<b>
								' . $query->first_name . ' ' . $query->last_name . '
								<i class="mdi mdi-launch"></i>
							</b>
						</a>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-4 col-md-2">
						' . phrase('post') . '
					</div>
					<div class="col-sm-8 col-md-10">
						<a href="' . base_url('blogs/' . $query->category_slug . '/' . $query->post_slug, array('comment_highlight' => $query->comment_id)) . '" target="_blank">
							<b>
								' . $query->post_title . '
								<i class="mdi mdi-launch"></i>
							</b>
						</a>
					</div>
				</div>
			');
		}
		
		$this->set_title(phrase('feedback'))
		->set_icon('mdi mdi-file-alert-outline')
		->unset_column('comment_id, reply_id, edited')
		->unset_field('comment_id, reply_id, edited')
		->unset_view('comment_id, reply_id, edited')
		
		->column_order('first_name, message, timestamp')
		
		->set_primary('comment_id, user_id')
		
		->add_action('toolbar', '../hide', phrase('review'), 'btn btn-danger --modal', 'mdi mdi-toggle-switch', array('id' => $this->_primary))
		
		->set_field
		(
			array
			(
				'comments'							=> 'textarea',
				'status'							=> 'boolean'
			)
		)
		->set_field('first_name', 'hyperlink', 'user', array('user_id' => 'user_id'), true)
		->set_relation
		(
			'user_id',
			'app__users.user_id',
			'{app__users.first_name} {app__users.last_name}'
		)
		
		->merge_content('{post_id} {post_type}', phrase('post'), 'callback_get_post')
		->merge_content('{first_name} {last_name}', phrase('full_name'))
		
		->where
		(
			array
			(
				'comment_id'						=> $this->_primary
			)
		)
		
		->order_by('timestamp', 'DESC')
		
		->render($this->_table);
	}
	
	public function hide()
	{
		$this->permission->must_ajax(current_page('../'));
		
		$query										= $this->model->get_where
		(
			'post__comments',
			array
			(
				'comment_id'						=> $this->_primary
			),
			1
		)
		->row();
		
		if(!$query)
		{
			return throw_exception(404, phrase('the_comment_you_would_to_' . ($query->status ? 'hide' : 'unhide') . '_is_not_found', current_page('../')));
		}
		
		if(service('request')->getPost('comment_id') == sha1($this->_primary . ENCRYPTION_KEY . get_userdata('session_generated')))
		{
			$this->model->update
			(
				$this->_table,
				array
				(
					'status'						=> ($query->status ? 0 : 1)
				),
				array
				(
					'comment_id'					=> $this->_primary
				)
			);
			
			return throw_exception(301, phrase(($query->status ? phrase('the_selected_comment_was_successfully_hidden') : phrase('the_selected_comment_was_successfully_shown'))), current_page('../'));
		}
		
		$html										= '
			<form action="' . current_page() . '" method="POST" class="--validate-form">
				<input type="hidden" name="comment_id" value="' . sha1($this->_primary . ENCRYPTION_KEY . get_userdata('session_generated')) . '" />
				<div class="text-center pt-3 pb-3 mb-3 border-bottom">
					' . phrase('are_you_sure_want_to_' . ($query->status ? 'hide' : 'unhide') . '_this_comment').  '
				</div>
				<div class="p-3 pt-0">
					<div class="row">
						<div class="col-6">
							<div class="d-grid">
								<button type="button" class="btn btn-light" data-bs-dismiss="modal">
									<i class="mdi mdi-window-close"></i>
									' . phrase('cancel') . '
								</button>
							</div>
						</div>
						<div class="col-6">
							<div class="d-grid">
								<button type="submit" class="btn btn-danger">
									<i class="mdi mdi-check"></i>
									' . ($query->status ? phrase('hide') : phrase('unhide')) . '
								</button>
							</div>
						</div>
					</div>
				</div>
			</form>
		';
		
		return make_json
		(
			array
			(
				'status'							=> 200,
				'meta'								=> array
				(
					'title'							=> ($query->status ? phrase('hide_comment') : phrase('unhide_comment')),
					'icon'							=> 'mdi mdi-toggle-switch',
					'popup'							=> true
				),
				'html'								=> $html
			)
		);
	}
	
	public function get_post($params = array())
	{
		$output										= null;
		
		if($params['post_type'] == 'blog')
		{
			$query									= $this->model->select
			('
				blogs.post_title,
				blogs.post_slug,
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
					'post_id'						=> $params['post_id']
				),
				1
			)
			->row();
			
			if($query)
			{
				$output								= '<a href="' . base_url('blogs/' . $query->category_slug . '/' . $query->post_slug) . '" target="_blank"><b>' . $query->post_title . '</b></a>';
			}
		}
		
		return $output;
	}
}
