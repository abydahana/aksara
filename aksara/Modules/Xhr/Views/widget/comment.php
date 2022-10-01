<div class="row pt-1 pb-1 mt-3 mb-3">
	<div class="col-4">
		<div class="d-grid">
			<a href="<?php echo current_page('repute'); ?>" class="btn btn-light btn-sm btn-block rounded-pill text-truncate --modify" data-post-id="<?php echo service('request')->getGet('post_id'); ?>">
				<i class="mdi mdi-heart"></i>
				<span class="likes-count"><?php echo ($likes_count ? $likes_count : ''); ?></span>
				<?php echo phrase('likes'); ?>
			</a>
		</div>
	</div>
	<div class="col-4">
		<div class="d-grid">
			<button type="button" class="btn btn-light btn-sm btn-block rounded-pill text-truncate" onclick="jExec($('textarea[name=comments]').first().trigger('focus'))">
				<i class="mdi mdi-comment"></i>
				<span class="replies-count"><?php echo ($comments_count ? $comments_count : ''); ?></span>
				<?php echo phrase('comments'); ?>
			</button>
		</div>
	</div>
	<div class="col-4">
		<div class="d-grid">
			<button type="button" class="btn btn-light btn-sm btn-block rounded-pill text-truncate">
				<i class="mdi mdi-share"></i>
				<?php echo phrase('share'); ?>
			</button>
		</div>
	</div>
</div>
<form action="<?php echo current_page(); ?>" method="POST" class="--validate-form border-top border-bottom pt-3 pb-3 mb-3">
	<div class="form-group">
		<div class="row align-items-center">
			<div class="col-2 col-lg-1">
				<a href="<?php echo (get_userdata('username') ? base_url('user/' . get_userdata('username')) : 'javascript:void(0)'); ?>" class="--xhr">
					<img src="<?php echo get_image('users', get_userdata('photo'), 'thumb'); ?>" class="img-fluid rounded-circle" />
				</a>
			</div>
			<div class="col-10 col-lg-11">
				<div class="position-relative">
					<textarea name="comments" class="form-control" placeholder="<?php echo phrase('type_a_comment'); ?>" rows="1"></textarea>
					<div class="btn-group position-absolute bottom-0 end-0">
						<button type="button" class="btn btn-link" data-bs-toggle="tooltip" title="<?php echo phrase('attach_photo'); ?>" onclick="jExec($(this).closest('form').find('.fileupload').removeClass('d-none').find('input[type=file]').trigger('click'))">
							<i class="mdi mdi-camera"></i>
						</button>
					</div>
				</div>
				<div data-provides="fileupload" class="fileupload fileupload-new d-none">
					<span class="btn btn-file" style="width:80px">
						<input type="file" name="attachment" accept="<?php echo implode(',', preg_filter('/^/', '.', array_map('trim', explode(',', IMAGE_FORMAT_ALLOWED)))); ?>" role="image-upload" id="attachment_input" />
						<div class="fileupload-new text-center">
							<img class="img-fluid upload_preview" src="<?php echo get_image('widget', 'placeholder.png', 'icon'); ?>" alt="..." />
						</div>
						<button type="button" class="btn btn-sm btn-danger rounded-circle position-absolute top-0 end-0" onclick="jExec($(this).closest('.btn-file').find('input[type=file]').val(''), $(this).closest('.btn-file').find('img').attr('src', '<?php echo get_image('widget', 'placeholder.png', 'icon'); ?>'), $(this).closest('.fileupload').addClass('d-none'))">
							<i class="mdi mdi-window-close"></i>
						</button>
					</span>
				</div>
			</div>
		</div>
	</div>
	<div class="row align-items-center">
		<div class="col-10 offset-2 col-lg-11 offset-lg-1">
			<div class="--validation-callback"></div>
		</div>
	</div>
</form>
<div id="comment-container">
	<?php
		$output										= null;
		$reply										= array();
		$replying									= array();
		
		foreach($results as $key => $val)
		{
			if($val->reply_id)
			{
				$replying[$val->comment_id]			= $val;
				
				$reply[$val->reply_id][]			= '
					<div class="row g-0 mb-2">
						<div class="col-2 col-lg-1 pt-1 pe-3">
							<a href="' . base_url('user/' . $val->username) . '" class="--xhr">
								<img src="' . get_image('users', $val->photo, 'thumb') . '" class="img-fluid rounded-circle" />
							</a>
						</div>
						<div class="col-10 col-lg-11">
							<div class="position-relative">
								<div class="dropdown position-absolute end-0">
									<button class="btn btn-link btn-sm dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
										<i class="mdi mdi-format-list-checks"></i>
									</button>
									<ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton1">
										' . ($val->user_id == get_userdata('user_id') ? '
										<li>
											<a class="dropdown-item --modal" href="' . current_page('update', array('id' => $val->comment_id)) . '">
												' . phrase('update') . '
											</a>
										</li>
										<li>
											<a class="dropdown-item --modal" href="' . current_page('report', array('id' => $val->comment_id)) . '">
												' . phrase('report') . '
											</a>
										</li>
										' : '
										<li>
											<a class="dropdown-item --modal" href="' . current_page('report', array('id' => $val->comment_id)) . '">
												' . phrase('report') . '
											</a>
										</li>
										') . '
										' . (in_array(get_userdata('group_id'), array(1, 2)) || $val->user_id == get_userdata('user_id') ? '
										<li>
											<a class="dropdown-item --modal" href="' . current_page('hide', array('id' => $val->comment_id)) . '">
												' . phrase('visibility') . '
											</a>
										</li>
										' : null) . '
									</ul>
								</div>
							</div>
							<div class="' . (service('request')->getGet('comment_highlight') && service('request')->getGet('comment_highlight') == $val->comment_id ? 'bg-warning' : 'bg-light') . ' rounded-4 pt-2 pe-3 pb-2 ps-3 d-inline-block">
								<a href="' . base_url('user/' . $val->username) . '" class="--xhr">
									<b id="comment-author-' . $val->comment_id . '">
										' . $val->first_name . ' ' . $val->last_name . '
									</b>
								</a>
								<br />
								<div id="comment-text-' . $val->comment_id . '">
									' . ($val->mention_id ? '<div class="ps-2 text-muted" style="border-left:1px dashed rgba(0,0,0,.3)">' . phrase('replying_to') . ' <b>' . $val->first_name . ' '. $val->last_name . '</b><br />' . (isset($replying[$val->mention_id]) ? truncate($replying[$val->mention_id]->comments, 32) : null) . '</div>' : null) . '
									
									' . ($val->status ? $val->comments . ($val->attachment ? '<div class="mt-3"><a href="' . get_image('widget', $val->attachment) . '" target="_blank"><img src="' . get_image('widget', $val->attachment, 'thumb') . '" class="img-fluid rounded" alt="..." /></a></div>' : null) : '<i class="text-muted">' . phrase('comment_is_hidden') . '</i>') . '
								</div>
							</div>
							<div class="ps-3 pe-3">
								<a href="' . current_page('upvote', array('id' => $val->comment_id)) . '" class="--modify text-sm">
									<b class="text-secondary" id="comment-upvote-' . $val->comment_id . '">
										' . ($val->upvotes ? $val->upvotes : null) . '
									</b>
									<b>
										' . phrase('upvote') . '
									</b>
								</a>
								 &middot; 
								<a href="' . current_page(null, array('type' => $val->post_type, 'reply' => $val->reply_id, 'mention' => $val->comment_id)) . '" class="text-sm --reply" data-profile-photo="' . get_image('users', get_userdata('photo'), 'thumb') . '" data-mention="' . $val->first_name . ' ' . $val->last_name . '">
									<b>
										' . phrase('reply') . '
									</b>
								</a>
								&middot;
								<span class="text-muted text-sm">
									' . time_ago($val->timestamp) . '
								</span>
							</div>
						</div>
					</div>
				';
			}
		}
		
		krsort($results);
		
		foreach($results as $key => $val)
		{
			if(!$val->reply_id)
			{
				$output								.= '
					<div class="row g-0 mb-2 comment-item">
						<div class="col-2 col-lg-1 pt-1 pe-3">
							<a href="' . base_url('user/' . $val->username) . '" class="--xhr">
								<img src="' . get_image('users', $val->photo, 'thumb') . '" class="img-fluid rounded-circle" />
							</a>
						</div>
						<div class="col-10 col-lg-11">
							<div class="position-relative">
								<div class="dropdown position-absolute end-0">
									<button class="btn btn-link btn-sm dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
										<i class="mdi mdi-format-list-checks"></i>
									</button>
									<ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton1">
										' . ($val->user_id == get_userdata('user_id') ? '
										<li>
											<a class="dropdown-item --modal" href="' . current_page('update', array('id' => $val->comment_id)) . '">
												' . phrase('update') . '
											</a>
										</li>
										' : 
										'
										<li>
											<a class="dropdown-item --modal" href="' . current_page('report', array('id' => $val->comment_id)) . '">
												' . phrase('report') . '
											</a>
										</li>
										') . '
										' . (in_array(get_userdata('group_id'), array(1, 2)) || $val->user_id == get_userdata('user_id') ? '
										<li>
											<a class="dropdown-item --modal" href="' . current_page('hide', array('id' => $val->comment_id)) . '">
												' . phrase('visibility') . '
											</a>
										</li>
										' : null) . '
									</ul>
								</div>
							</div>
							<div class="' . (service('request')->getGet('comment_highlight') && service('request')->getGet('comment_highlight') == $val->comment_id ? 'bg-warning' : 'bg-light') . ' rounded-4 p-3 d-inline-block">
								<a href="' . base_url('user/' . $val->username) . '" class="--xhr">
									<b id="comment-author-' . $val->comment_id . '">
										' . $val->first_name . ' ' . $val->last_name . '
									</b>
								</a>
								<br />
								<div id="comment-text-' . $val->comment_id . '">
									' . ($val->status ? $val->comments . ($val->attachment ? '<div class="mt-3"><a href="' . get_image('widget', $val->attachment) . '" target="_blank"><img src="' . get_image('widget', $val->attachment, 'thumb') . '" class="img-fluid rounded" alt="..." /></a></div>' : null) : '<i class="text-muted">' . phrase('comment_is_hidden') . '</i>') . '
								</div>
							</div>
							<div class="ps-3 pe-3">
								<a href="' . current_page('upvote', array('id' => $val->comment_id)) . '" class="--modify text-sm">
									<b class="text-secondary" id="comment-upvote-' . $val->comment_id . '">
										' . ($val->upvotes ? $val->upvotes : null) . '
									</b>
									<b>
										' . phrase('upvote') . '
									</b>
								</a>
								&middot;
								<a href="' . current_page(null, array('type' => $val->post_type, 'reply' => $val->comment_id)) . '" class="text-sm --reply" data-profile-photo="' . get_image('users', get_userdata('photo'), 'thumb') . '" data-mention="' . $val->first_name . ' ' . $val->last_name . '">
									<b>
										' . phrase('reply') . '
									</b>
								</a>
								&middot;
								<span class="text-muted text-sm">
									' . time_ago($val->timestamp) . '
								</span>
							</div>
							
							<div id="comment-reply" class="text-sm mt-2">
								' . (isset($reply[$val->comment_id]) ? implode(' ', $reply[$val->comment_id]) : null) . '
							</div>
						</div>
					</div>
				';
			}
		}
		
		echo $output;
	?>
</div>

<script type="text/javascript" src="<?php echo get_module_asset('js/scripts.js'); ?>"></script>
