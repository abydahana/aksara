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
					<div class="btn-group position-absolute top-0 end-0">
						<button type="submit" class="btn btn-link pe-1">
							<i class="mdi mdi-send"></i>
						</button>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="row align-items-center">
		<div class="col-10 offset-2 col-lg-11 offset-lg-1">
			<div class="--validation-callback"></div>
		</div>
	</div>
	<input type="hidden" name="post_id" value="<?php echo service('request')->getGet('post_id'); ?>" />
	<input type="hidden" name="comment_type" value="<?php echo service('request')->getGet('type'); ?>" />
	<input type="hidden" name="_token" value="<?php echo $_token; ?>" />
</form>
<div id="comment-container">
	<?php
		$output										= null;
		$reply										= array();
		
		foreach($results as $key => $val)
		{
			if($val->reply_id)
			{
				$reply[$val->reply_id][]			= '
					<div class="row g-0 mb-1">
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
									<b>
										' . $val->first_name . ' ' . $val->last_name . '
									</b>
								</a>
								<br />
								<div id="comment-text-' . $val->comment_id . '">
									' . ($val->status ? $val->comments : '<i class="text-muted">' . phrase('comment_hidden') . '</i>') . '
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
								<a href="javascript:void(0)" class="text-sm" onclick="jExec($(this).closest(\'#comment-container\').find(\'#comment-' . $val->reply_id . '\').removeClass(\'d-none\').fadeIn())">
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
		
		foreach($results as $key => $val)
		{
			if(!$val->reply_id)
			{
				$output								.= '
					<div class="row comment-item">
						<div class="col-2 col-lg-1 pt-3">
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
									<b>
										' . $val->first_name . ' ' . $val->last_name . '
									</b>
								</a>
								<br />
								<div id="comment-text-' . $val->comment_id . '">
									' . ($val->status ? $val->comments : '<i class="text-muted">' . phrase('comment_hidden') . '</i>') . '
								</div>
							</div>
							<div class="ps-3 pe-3 mb-3">
								<a href="' . current_page('upvote', array('id' => $val->comment_id)) . '" class="--modify text-sm">
									<b class="text-secondary" id="comment-upvote-' . $val->comment_id . '">
										' . ($val->upvotes ? $val->upvotes : null) . '
									</b>
									<b>
										' . phrase('upvote') . '
									</b>
								</a>
								&middot;
								<a href="javascript:void(0)" class="text-sm" onclick="jExec($(this).closest(\'#comment-container\').find(\'#comment-' . $val->comment_id . '\').removeClass(\'d-none\').fadeIn())">
									<b>
										' . phrase('reply') . '
									</b>
								</a>
								&middot;
								<span class="text-muted text-sm">
									' . time_ago($val->timestamp) . '
								</span>
							</div>
							
							<div id="comment-reply" class="text-sm">
								' . (isset($reply[$val->comment_id]) ? implode(' ', $reply[$val->comment_id]) : null) . '
								<form action="' . current_page() . '" method="POST" class="--validate-form d-none" id="comment-' . $val->comment_id . '">
									<div class="form-group mb-3">
										<div class="row g-0 align-items-center">
											<div class="col-2 col-lg-1 pt-1 pe-3">
												<a href="' . (get_userdata('username') ? base_url('user/' . get_userdata('username')) : 'javascript:void(0)') . '" class="--xhr">
													<img src="' . get_image('users', get_userdata('photo'), 'thumb') . '" class="img-fluid rounded-circle" />
												</a>
											</div>
											<div class="col-10 col-lg-11">
												<div class="position-relative">
													<textarea name="comments" class="form-control" placeholder="' . phrase('type_a_reply') . '" rows="1"></textarea>
													<div class="btn-group position-absolute top-0 end-0">
														<button type="submit" class="btn btn-link pe-1">
															<i class="mdi mdi-send"></i>
														</button>
													</div>
												</div>
											</div>
										</div>
									</div>
									<div class="row g-0 align-items-center">
										<div class="col-10 offset-2 col-lg-11 offset-lg-1">
											<div class="--validation-callback"></div>
										</div>
									</div>
									<input type="hidden" name="post_id" value="' . $val->post_id . '" />
									<input type="hidden" name="reply_id" value="' . $val->comment_id . '" />
									<input type="hidden" name="comment_type" value="' . $val->comment_type . '" />
									<input type="hidden" name="_token" value="' . $_token . '" />
								</form>
							</div>
						</div>
					</div>
				';
			}
		}
		
		echo $output;
	?>
</div>
