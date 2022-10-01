<?php

namespace Aksara\Modules\Cms\Controllers\Comments;

/**
 * CMS > Reactions > Comments
 *
 * @author			Aby Dahana <abydahana@gmail.com>
 * @profile			abydahana.github.io
 * @website			www.aksaracms.com
 * @since			version 4.4.0
 * @copyright		(c) 2021 - Aksara Laboratory
 */

class Comments extends \Aksara\Laboratory\Core
{
	private $_table									= 'post__comments';
	
	public function __construct()
	{
		parent::__construct();
		
		$this->set_permission();
		$this->set_theme('backend');
		
		$this->unset_action('create, update, delete');
	}
	
	public function index()
	{
		$this->set_title(phrase('comments'))
		->set_icon('mdi mdi-comment-multiple-outline')
		->unset_column('reply_id, mention_id, edited, attachment')
		->unset_field('reply_id, mention_id, edited, attachment')
		->unset_view('reply_id, mention_id, edited, attachment')
		
		->column_order('first_name, post_id, comments, timestamp, status')
		
		->add_action('option', 'hide', phrase('review'), 'btn btn-danger --modal', 'mdi mdi-toggle-switch', array('id' => 'comment_id'))
		
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
		->merge_content('{comment_id}', phrase('feedback'), 'callback_get_feedback')
		
		->order_by('timestamp', 'DESC')
		
		->render($this->_table);
	}
	
	public function hide()
	{
		$this->permission->must_ajax(current_page('../'));
		
		$comment_id									= (service('request')->getGet('id') ? service('request')->getGet('id') : 0);
		
		$query										= $this->model->get_where
		(
			$this->_table,
			array
			(
				'comment_id'						=> $comment_id
			),
			1
		)
		->row();
		
		if(!$query)
		{
			return throw_exception(404, phrase('the_comment_you_would_to_' . ($query->status ? 'hide' : 'unhide') . '_is_not_found', current_page('../')));
		}
		
		if(service('request')->getPost('comment_id') == sha1($comment_id . ENCRYPTION_KEY . get_userdata('session_generated')))
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
					'comment_id'					=> $comment_id
				)
			);
			
			return throw_exception(301, phrase(($query->status ? phrase('the_selected_comment_was_successfully_hidden') : phrase('the_selected_comment_was_successfully_shown'))), current_page('../'));
		}
		
		$html										= '
			<form action="' . current_page() . '" method="POST" class="--validate-form">
				<input type="hidden" name="comment_id" value="' . sha1($comment_id . ENCRYPTION_KEY . get_userdata('session_generated')) . '" />
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
	
	public function get_feedback($params = array())
	{
		if(!isset($params['comment_id'])) return false;
		
		$query										= $this->model->get_where
		(
			'post__comments_reports',
			array
			(
				'comment_id'						=> $params['comment_id']
			)
		)
		->num_rows();
		
		if($query)
		{
			return '<a href="' . current_page('feedback', array('id' => $params['comment_id'], 'column' => null, 'q' => null, 'per_page' => null, 'order' => null, 'sort' => null)) . '" class="badge bg-danger --xhr">' . number_format($query) . ' ' . ($query > 1 ? phrase('reports') : phrase('report')) . '</a>';
		}
		
		return false;
	}
}
