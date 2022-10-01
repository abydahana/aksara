<?php

namespace Aksara\Modules\Xhr\Controllers\Widget;

/**
 * XHR > Widget > Comment
 *
 * @author			Aby Dahana <abydahana@gmail.com>
 * @profile			abydahana.github.io
 * @website			www.aksaracms.com
 * @since			version 4.4.0
 * @copyright		(c) 2022 - Aksara Laboratory
 */

class Comment extends \Aksara\Laboratory\Core
{
	private $_table									= 'post__comments';
	
	public function __construct()
	{
		parent::__construct();
		
		//$this->set_permission();
		$this->permission->must_ajax();
		$this->limit(null);
	}
	
	public function index()
	{
		if($this->valid_token(service('request')->getPost('_token')))
		{
			return $this->_validate_form();
		}
		
		$this->set_title('comment')
		->set_icon('mdi mdi-comment-multiple')
		
		->set_output
		(
			array
			(
				'likes_count'						=> $this->model->get_where
				(
					'post__likes',
					array
					(
						'post_id'					=> service('request')->getGet('post_id'),
						'post_type'					=> service('request')->getGet('type')
					)
				)
				->num_rows(),
				
				'comments_count'					=> $this->model->get_where
				(
					'post__comments',
					array
					(
						'post_id'					=> service('request')->getGet('post_id'),
						'post_type'					=> service('request')->getGet('type')
					)
				)
				->num_rows()
			)
		)
		
		->select
		('
			(SELECT COUNT(*) FROM post__comments_likes WHERE comment_id = post__comments.comment_id) AS upvotes
		')
		
		->set_relation
		(
			'user_id',
			'app__users.user_id',
			'{app__users.photo} {app__users.username} {app__users.first_name} {app__users.last_name}'
		)
		->where
		(
			array
			(
				'post_id'							=> service('request')->getGet('post_id'),
				'post_type'							=> service('request')->getGet('type')
			)
		)
		
		->render($this->_table);
	}
	
	public function repute()
	{
		if(!get_userdata('is_logged'))
		{
			return throw_exception(403, phrase('please_sign_in_to_repute_the_post'));
		}
		
		$query										= $this->model->get_where
		(
			'post__likes',
			array
			(
				'post_id'							=> (service('request')->getGet('post_id') ? service('request')->getGet('post_id') : 0),
				'post_type'							=> (service('request')->getGet('type') ? service('request')->getGet('type') : ''),
				'user_id'							=> get_userdata('user_id')
			)
		)
		->row();
		
		if($query)
		{
			$query									= $this->model->delete
			(
				'post__likes',
				array
				(
					'post_id'						=> (service('request')->getGet('post_id') ? service('request')->getGet('post_id') : 0),
					'post_type'						=> (service('request')->getGet('type') ? service('request')->getGet('type') : ''),
					'user_id'						=> get_userdata('user_id')
				)
			);
		}
		else
		{
			$query									= $this->model->insert
			(
				'post__likes',
				array
				(
					'post_id'						=> (service('request')->getGet('post_id') ? service('request')->getGet('post_id') : 0),
					'post_type'						=> (service('request')->getGet('type') ? service('request')->getGet('type') : ''),
					'user_id'						=> get_userdata('user_id'),
					'timestamp'						=> date('Y-m-d H:i:s')
				)
			);
		}
		
		$upvotes									= $this->model->get_where
		(
			'post__likes',
			array
			(
				'post_id'						=> (service('request')->getGet('post_id') ? service('request')->getGet('post_id') : 0),
				'post_type'						=> (service('request')->getGet('type') ? service('request')->getGet('type') : '')
			)
		)
		->num_rows();
		
		if($upvotes > 999)
		{
			if ($upvotes < 1000000)
			{
				$upvotes							= number_format($upvotes / 1000) . 'K';
			}
			else if ($upvotes < 1000000000)
			{
				$upvotes							= number_format($upvotes / 1000000, 2) . 'M';
			}
			else
			{
				$upvotes							= number_format($upvotes / 1000000000, 2) . 'B';
			}
		}
		
		return make_json
		(
			array
			(
				'element'							=> '.likes-count',
				'html'								=> ($upvotes ? $upvotes : null)
			)
		);
	}
	
	public function upvote()
	{
		if(!get_userdata('is_logged'))
		{
			return throw_exception(403, phrase('please_sign_in_to_upvote_the_comment'));
		}
		
		$query										= $this->model->get_where
		(
			'post__comments_likes',
			array
			(
				'comment_id'						=> (service('request')->getGet('id') ? service('request')->getGet('id') : 0),
				'user_id'							=> get_userdata('user_id')
			)
		)
		->row();
		
		if($query)
		{
			$query									= $this->model->delete
			(
				'post__comments_likes',
				array
				(
					'comment_id'					=> (service('request')->getGet('id') ? service('request')->getGet('id') : 0),
					'user_id'						=> get_userdata('user_id')
				)
			);
		}
		else
		{
			$query									= $this->model->insert
			(
				'post__comments_likes',
				array
				(
					'comment_id'					=> (service('request')->getGet('id') ? service('request')->getGet('id') : 0),
					'user_id'						=> get_userdata('user_id'),
					'timestamp'						=> date('Y-m-d H:i:s')
				)
			);
		}
		
		$upvotes									= $this->model->get_where
		(
			'post__comments_likes',
			array
			(
				'comment_id'						=> (service('request')->getGet('id') ? service('request')->getGet('id') : 0)
			)
		)
		->num_rows();
		
		if($upvotes > 999)
		{
			if ($upvotes < 1000000)
			{
				$upvotes							= number_format($upvotes / 1000) . 'K';
			}
			else if ($upvotes < 1000000000)
			{
				$upvotes							= number_format($upvotes / 1000000, 2) . 'M';
			}
			else
			{
				$upvotes							= number_format($upvotes / 1000000000, 2) . 'B';
			}
		}
		
		return make_json
		(
			array
			(
				'element'							=> '#comment-upvote-' . service('request')->getGet('id'),
				'html'								=> ($upvotes ? $upvotes : null)
			)
		);
	}
	
	public function update()
	{
		if(!get_userdata('is_logged'))
		{
			return throw_exception(403, phrase('please_sign_in_to_update_the_comment'));
		}
		
		$query										= $this->model->get_where
		(
			$this->_table,
			array
			(
				'comment_id'						=> (service('request')->getGet('id') ? service('request')->getGet('id') : 0)
			),
			1
		)
		->row();
		
		if(!$query)
		{
			return throw_exception(404, phrase('the_comment_you_would_to_update_is_not_found'));
		}
		
		if(service('request')->getPost('comment_id') == sha1(service('request')->getGet('id') . ENCRYPTION_KEY . get_userdata('session_generated')))
		{
			$this->form_validation->setRule('comments', phrase('comments'), 'required');
			$this->form_validation->setRule('attachment', phrase('attachment'), 'validate_upload[attachment.image]');
			
			if($this->form_validation->run(service('request')->getPost()) === false)
			{
				return throw_exception(400, $this->form_validation->getErrors());
			}
			
			$this->_upload_data						= (get_userdata('_upload_data') ? get_userdata('_upload_data') : array());
			$attachment								= '';
			
			// check if the uploaded file is valid
			if(isset($this->_upload_data['attachment']) && is_array($this->_upload_data['attachment']))
			{
				// loop to get source from unknown array key
				foreach($this->_upload_data['attachment'] as $key => $src)
				{
					// set new source
					$attachment						= $src;
				}
			}
			
			// insert to update history
			$this->model->insert
			(
				'post__comments_updates',
				array
				(
					'comment_id'					=> $query->comment_id,
					'comments'						=> $query->comments,
					'attachment'					=> $query->attachment,
					'timestamp'						=> $query->timestamp
				)
			);
			
			// update comment
			$this->model->update
			(
				$this->_table,
				array
				(
					'comments'						=> htmlspecialchars(service('request')->getPost('comments')),
					'attachment'					=> $attachment,
					'edited'						=> 1
				),
				array
				(
					'comment_id'					=> service('request')->getGet('id')
				)
			);
			
			return make_json
			(
				array
				(
					'element'						=> '#comment-text-' . service('request')->getGet('id'),
					'html'							=> ($attachment ? '<div><a href="' . get_image('widget', $attachment) . '" target="_blank"><img src="' . get_image('widget', $attachment, 'thumb') . '" class="img-fluid rounded mb-3" alt="..." /></a></div>' : null) . nl2br(htmlspecialchars(service('request')->getPost('comments')))
				)
			);
		}
		
		$html										= '
			<form action="' . current_page() . '" method="POST" class="--validate-form">
				<input type="hidden" name="comment_id" value="' . sha1(service('request')->getGet('id') . ENCRYPTION_KEY . get_userdata('session_generated')) . '" />
				<div class="p-3 pb-0">
					<div class="form-group mb-3">
						<label class="d-block text-muted" for="comments_input">
							'. phrase('comments') . '
						</label>
						<textarea name="comments" class="form-control" id="comments_input" placeholder="' . phrase('type_a_comment') . '" rows="1">' . (isset($query->comments) ? $query->comments : null) . '</textarea>
					</div>
					<div class="form-group">
						<label class="d-block text-muted" for="comments_input">
							'. phrase('atachment') . '
						</label>
						<div data-provides="fileupload" class="fileupload fileupload-new">
							<span class="btn btn-file d-block">
								<input type="file" name="attachment" accept="' . implode(',', preg_filter('/^/', '.', array_map('trim', explode(',', IMAGE_FORMAT_ALLOWED)))) . '" role="image-upload" id="attachment_input" />
								<div class="fileupload-new text-center">
									<img class="img-fluid upload_preview" src="' . get_image('widget', $query->attachment, 'thumb'). '" alt="..." />
								</div>
								<button type="button" class="btn btn-sm btn-danger rounded-circle position-absolute top-0 end-0" onclick="jExec($(this).closest(\'.btn-file\').find(\'input[type=file]\').val(\'\'), $(this).closest(\'.btn-file\').find(\'img\').attr(\'src\', \'' . get_image('widget', 'placeholder.png', 'icon') . '\'))">
									<i class="mdi mdi-window-close"></i>
								</button>
							</span>
						</div>
					</div>
				</div>
				<hr />
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
								<button type="submit" class="btn btn-primary">
									<i class="mdi mdi-check"></i>
									' . phrase('update') . '
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
					'title'							=> phrase('update_comment'),
					'icon'							=> 'mdi mdi-square-edit-outline',
					'popup'							=> true
				),
				'html'								=> $html
			)
		);
	}
	
	public function report()
	{
		if(!get_userdata('is_logged'))
		{
			return throw_exception(403, phrase('please_sign_in_to_report_the_comment'));
		}
		
		$query										= $this->model->get_where
		(
			$this->_table,
			array
			(
				'comment_id'						=> (service('request')->getGet('id') ? service('request')->getGet('id') : 0)
			),
			1
		)
		->row();
		
		if(!$query)
		{
			return throw_exception(404, phrase('the_comment_you_would_to_report_is_not_found'));
		}
		
		if(service('request')->getPost('comment_id') == sha1(service('request')->getGet('id') . ENCRYPTION_KEY . get_userdata('session_generated')))
		{
			$checker								= $this->model->get_where
			(
				'post__comments_reports',
				array
				(
					'comment_id'					=> $query->comment_id,
					'user_id'						=> get_userdata('user_id')
				),
				1
			)
			->row();
			
			if($checker)
			{
				// update feedback
				$this->model->update
				(
					'post__comments_reports',
					array
					(
						'message'					=> htmlspecialchars(service('request')->getPost('message')),
						'timestamp'					=> $query->timestamp
					),
					array
					(
						'comment_id'				=> $query->comment_id,
						'user_id'					=> get_userdata('user_id')
					)
				);
			}
			else
			{
				// insert feedback
				$this->model->insert
				(
					'post__comments_reports',
					array
					(
						'comment_id'				=> $query->comment_id,
						'user_id'					=> get_userdata('user_id'),
						'message'					=> htmlspecialchars(service('request')->getPost('message')),
						'timestamp'					=> $query->timestamp
					)
				);
			}
			
			return throw_exception(200, phrase('comment_was_successfully_reported_and_queued_for_review'));
		}
		
		$html										= '
			<form action="' . current_page() . '" method="POST" class="--validate-form">
				<input type="hidden" name="comment_id" value="' . sha1(service('request')->getGet('id') . ENCRYPTION_KEY . get_userdata('session_generated')) . '" />
				<div class="text-center pt-3 pb-3 border-bottom">
					' . phrase('are_you_sure_want_to_report_this_comment') . '
				</div>
				<div class="p-3 pb-0">
					<div class="form-group">
						<textarea name="message" class="form-control" id="message_input" placeholder="' . phrase('write_a_feedback') . '" rows="1"></textarea>
					</div>
				</div>
				<hr />
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
									' . phrase('report') . '
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
					'title'							=> phrase('report_comment'),
					'icon'							=> 'mdi mdi-alert-outline',
					'popup'							=> true
				),
				'html'								=> $html
			)
		);
	}
	
	public function hide()
	{
		$query										= $this->model->get_where
		(
			$this->_table,
			array
			(
				'comment_id'						=> (service('request')->getGet('id') ? service('request')->getGet('id') : 0)
			),
			1
		)
		->row();
		
		if(!$query)
		{
			return throw_exception(404, phrase('the_comment_you_would_to_hide_is_not_found'));
		}
		
		if(service('request')->getPost('comment_id') == sha1(service('request')->getGet('id') . ENCRYPTION_KEY . get_userdata('session_generated')))
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
					'comment_id'					=> service('request')->getGet('id')
				)
			);
			
			return make_json
			(
				array
				(
					'element'						=> '#comment-text-' . service('request')->getGet('id'),
					'html'							=> ($query->status ? '<i class="text-muted">' . phrase('comment_hidden') . '</i>' : $query->comments)
				)
			);
		}
		
		$html										= '
			<form action="' . current_page() . '" method="POST" class="--validate-form">
				<input type="hidden" name="comment_id" value="' . sha1(service('request')->getGet('id') . ENCRYPTION_KEY . get_userdata('session_generated')) . '" />
				<div class="text-center pt-3 pb-3 mb-3 border-bottom">
					' . phrase('are_you_sure_want_to_hide_this_comment') . '
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
					'title'							=> phrase('action_warning'),
					'icon'							=> 'mdi mdi-alert-outline',
					'popup'							=> true
				),
				'html'								=> $html
			)
		);
	}
	
	private function _validate_form()
	{
		if(!get_userdata('is_logged'))
		{
			return throw_exception(400, array('comments' => phrase('please_sign_in_to_comment')));
		}
		else if(!service('request')->getGet('post_id') || !service('request')->getGet('type'))
		{
			return throw_exception(400, array('comments' => phrase('unable_to_reply_to_invalid_thread')));
		}
		
		$this->form_validation->setRule('comments', phrase('comments'), 'required');
		$this->form_validation->setRule('attachment', phrase('attachment'), 'validate_upload[attachment.image]');
		
		if($this->form_validation->run(service('request')->getPost()) === false)
		{
			return throw_exception(400, $this->form_validation->getErrors());
		}
		
		$this->_upload_data							= (get_userdata('_upload_data') ? get_userdata('_upload_data') : array());
		$attachment									= '';
		
		// check if the uploaded file is valid
		if(isset($this->_upload_data['attachment']) && is_array($this->_upload_data['attachment']))
		{
			// loop to get source from unknown array key
			foreach($this->_upload_data['attachment'] as $key => $src)
			{
				// set new source
				$attachment							= $src;
			}
		}
		
		$reply_id									= (service('request')->getGet('reply') ? service('request')->getGet('reply') : 0);
		$mention_id									= (service('request')->getGet('mention') ? service('request')->getGet('mention') : 0);
		
		$this->model->insert
		(
			$this->_table,
			array
			(
				'user_id'							=> get_userdata('user_id'),
				'post_id'							=> service('request')->getGet('post_id'),
				'post_type'							=> service('request')->getGet('type'),
				'reply_id'							=> $reply_id,
				'mention_id'						=> $mention_id,
				'comments'							=> htmlspecialchars(service('request')->getPost('comments')),
				'attachment'						=> $attachment,
				'timestamp'							=> date('Y-m-d H:i:s'),
				'status'							=> (in_array(get_userdata('group_id'), array(1, 2)) ? 1 : 0)
			)
		);
		
		$comment_id									= $this->model->insert_id();
		
		$query										= $this->model->select
		('
			post__comments.comment_id,
			post__comments.reply_id,
			post__comments.mention_id,
			post__comments.comments,
			post__comments.attachment,
			post__comments.timestamp,
			
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
				'comment_id'						=> $mention_id
			),
			1
		)
		->row();
		
		$html										= '
			<div class="row g-0 mb-2 ' . (!$reply_id ? 'comment-item' : null) . '">
				<div class="col-2 col-lg-1 pt-1 pe-3">
					<img src="' . get_image('users', get_userdata('photo'), 'thumb') . '" class="img-fluid rounded-circle" />
				</div>
				<div class="col-10 col-lg-11">
					<div class="position-relative">
						<div class="dropdown position-absolute end-0">
							<button class="btn btn-link btn-sm dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
								<i class="mdi mdi-format-list-checks"></i>
							</button>
							<ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton1">
								<li>
									<a class="dropdown-item --modal" href="' . current_page('update', array('id' => $comment_id)) . '">
										' . phrase('update') . '
									</a>
								</li>
							</ul>
						</div>
					</div>
					<div class="bg-light rounded-4 p-3 d-inline-block">
						<a href="' . base_url('user/' . get_userdata('username')) . '" class="--xhr">
							<b>
								' . get_userdata('first_name') . ' ' . get_userdata('last_name') . '
							</b>
						</a>
						<br />
						<div id="comment-text-' . $comment_id . '">
							' . ($query ? '<div class="ps-2 text-muted" style="border-left:1px dashed rgba(0,0,0,.3)">' . phrase('replying_to') . ' <b>' . $query->first_name . ' '. $query->last_name . '</b><br />' . truncate($query->comments, 32) . '</div>' : null) . '
							
							' . nl2br(htmlspecialchars(service('request')->getPost('comments'))) . '
							' . ($attachment ? '<div><a href="' . get_image('widget', $attachment) . '" target="_blank"><img src="' . get_image('widget', $attachment, 'thumb') . '" class="img-fluid rounded mb-3" alt="..." /></a></div>' : null) . '
						</div>
					</div>
					<div class="ps-3 pe-3">
						<a href="' . current_page('upvote', array('id' => $comment_id)) . '" class="--modify text-sm">
							<b class="text-secondary" id="comment-upvote-' . $comment_id . '"></b>
							&nbsp;
							<b>
								' . phrase('upvote') . '
							</b>
						</a>
						 &middot; 
						<a href="' . current_page(null, array('type' => service('request')->getGet('type'), 'reply' => ($reply_id ? $reply_id : $comment_id), 'mention' => ($reply_id ? $comment_id : null))) . '" class="text-sm --reply" data-profile-photo="' . get_image('users', get_userdata('photo'), 'thumb') . '" data-mention="' . get_userdata('first_name') . ' ' . get_userdata('last_name') . '">
							<b>
								' . phrase('reply') . '
							</b>
						</a>
						 &middot; 
						<span class="text-muted text-sm">
							' . time_ago(date('Y-m-d H:i:s')) . '
						</span>
					</div>
					
					' . (!$reply_id ? '<div id="comment-reply" class="text-sm"></div>' : null) . '
				</div>
			</div>
		';
		
		if($reply_id)
		{
			$insert_method							= 'insert_before';
		}
		else
		{
			$insert_method							= 'prepend_to';
		}
		
		return make_json
		(
			array
			(
				'html'								=> $html,
				$insert_method						=> ($reply_id ? '#comment-container #comment-reply form' : '#comment-container'),
				'in_context'						=> ($reply_id ? true : false)
			)
		);
	}
}
