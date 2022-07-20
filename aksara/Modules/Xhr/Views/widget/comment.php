<form action="<?php echo current_page(); ?>" method="POST" class="--validate-form border-top border-bottom pt-3 pb-3 mb-3">
	<div class="form-group">
		<div class="row align-items-center">
			<div class="col-2 col-lg-1">
				<a href="<?php echo (get_userdata('username') ? base_url('user/' . get_userdata('username')) : 'javascript:void(0)'); ?>" class="--xhr">
					<img src="<?php echo get_image('users', get_userdata('photo'), 'thumb'); ?>" class="img-fluid rounded-circle" />
				</a>
			</div>
			<div class="col-10 col-lg-11">
				<textarea name="comments" class="form-control" placeholder="<?php echo phrase('type_a_comment'); ?>" rows="1"></textarea>
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
		foreach($results as $key => $val)
		{
			echo '
				<div class="row mb-3">
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
									' : null) . '
									<li>
										<a class="dropdown-item --modal" href="' . current_page('report', array('id' => $val->comment_id)) . '">
											' . phrase('report') . '
										</a>
									</li>
									' . (in_array(get_userdata('group_id'), array(1, 2)) ? '
									<li>
										<a class="dropdown-item --modal" href="' . current_page('approve', array('id' => $val->comment_id)) . '">
											' . phrase('approve') . '
										</a>
									</li>
									<li>
										<a class="dropdown-item --modal" href="' . current_page('hide', array('id' => $val->comment_id)) . '">
											' . phrase('hide') . '
										</a>
									</li>
									' : null) . '
								</ul>
							</div>
						</div>
						<div class="bg-light rounded-4 p-3 d-inline-block">
							<a href="' . base_url('user/' . $val->username) . '" class="--xhr">
								<b>
									' . $val->first_name . ' ' . $val->last_name . '
								</b>
							</a>
							<br />
							<div>
								' . $val->comments . '
							</div>
						</div>
						<div class="ps-3 pe-3 mb-3">
							<a href="' . current_page('like', array('id' => $val->comment_id)) . '" class="--xhr text-sm">
								<b>
									' . phrase('like') . '
								</b>
							</a>
							&middot;
							<a href="' . current_page('reply', array('id' => $val->comment_id)) . '" class="--xhr text-sm">
								<b>
									' . phrase('reply') . '
								</b>
							</a>
							&middot;
							<span class="text-muted text-sm">
								' . time_ago($val->created_timestamp) . '
							</span>
						</div>
						
						<div id="comment-reply" class="text-sm">
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
													<a class="dropdown-item" href="' . current_page('update', array('id' => $val->comment_id)) . '">
														' . phrase('update') . '
													</a>
												</li>
												' : null) . '
												<li>
													<a class="dropdown-item" href="' . current_page('report', array('id' => $val->comment_id)) . '">
														' . phrase('report') . '
													</a>
												</li>
												' . (in_array(get_userdata('group_id'), array(1, 2)) ? '
												<li>
													<a class="dropdown-item" href="' . current_page('approve', array('id' => $val->comment_id)) . '">
														' . phrase('approve') . '
													</a>
												</li>
												<li>
													<a class="dropdown-item" href="' . current_page('hide', array('id' => $val->comment_id)) . '">
														' . phrase('hide') . '
													</a>
												</li>
												' : null) . '
											</ul>
										</div>
									</div>
									<div class="bg-light rounded-4 pt-2 pe-3 pb-2 ps-3 d-inline-block">
										<a href="' . base_url('user/' . $val->username) . '" class="--xhr">
											<b>
												' . $val->first_name . ' ' . $val->last_name . '
											</b>
										</a>
										<br />
										<div>
											' . $val->comments . '
										</div>
									</div>
									<div class="ps-3 pe-3">
										<a href="' . current_page('like', array('id' => $val->comment_id)) . '" class="--xhr text-sm">
											<b>
												' . phrase('like') . '
											</b>
										</a>
										&middot;
										<a href="' . current_page('reply', array('id' => $val->comment_id)) . '" class="--xhr text-sm">
											<b>
												' . phrase('reply') . '
											</b>
										</a>
										&middot;
										<span class="text-muted text-sm">
											' . time_ago($val->created_timestamp) . '
										</span>
									</div>
								</div>
							</div>
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
													<a class="dropdown-item" href="' . current_page('update', array('id' => $val->comment_id)) . '">
														' . phrase('update') . '
													</a>
												</li>
												' : null) . '
												<li>
													<a class="dropdown-item" href="' . current_page('report', array('id' => $val->comment_id)) . '">
														' . phrase('report') . '
													</a>
												</li>
												' . (in_array(get_userdata('group_id'), array(1, 2)) ? '
												<li>
													<a class="dropdown-item" href="' . current_page('approve', array('id' => $val->comment_id)) . '">
														' . phrase('approve') . '
													</a>
												</li>
												<li>
													<a class="dropdown-item" href="' . current_page('hide', array('id' => $val->comment_id)) . '">
														' . phrase('hide') . '
													</a>
												</li>
												' : null) . '
											</ul>
										</div>
									</div>
									<div class="bg-light rounded-4 pt-2 pe-3 pb-2 ps-3 d-inline-block">
										<a href="' . base_url('user/' . $val->username) . '" class="--xhr">
											<b>
												' . $val->first_name . ' ' . $val->last_name . '
											</b>
										</a>
										<br />
										<div>
											' . $val->comments . '
										</div>
									</div>
									<div class="ps-3 pe-3">
										<a href="' . current_page('like', array('id' => $val->comment_id)) . '" class="--xhr text-sm">
											<b>
												' . phrase('like') . '
											</b>
										</a>
										&middot;
										<a href="' . current_page('reply', array('id' => $val->comment_id)) . '" class="--xhr text-sm">
											<b>
												' . phrase('reply') . '
											</b>
										</a>
										&middot;
										<span class="text-muted text-sm">
											' . time_ago($val->created_timestamp) . '
										</span>
									</div>
								</div>
							</div>
							<form action="' . current_page() . '" method="POST" class="--validate-form">
								<div class="form-group mb-3">
									<div class="row g-0 align-items-center">
										<div class="col-2 col-lg-1 pt-1 pe-3">
											<a href="' . (get_userdata('username') ? base_url('user/' . get_userdata('username')) : 'javascript:void(0)') . '" class="--xhr">
												<img src="' . get_image('users', get_userdata('photo'), 'thumb') . '" class="img-fluid rounded-circle" />
											</a>
										</div>
										<div class="col-10 col-lg-11">
											<textarea name="comments" class="form-control" placeholder="' . phrase('type_a_reply') . '" rows="1"></textarea>
										</div>
									</div>
								</div>
								<div class="row g-0 align-items-center">
									<div class="col-10 offset-2 col-lg-11 offset-lg-1">
										<div class="--validation-callback"></div>
									</div>
								</div>
								<input type="hidden" name="post_id" value="' . service('request')->getGet('post_id') . '" />
								<input type="hidden" name="comment_type" value="' . service('request')->getGet('type') . '" />
								<input type="hidden" name="_token" value="' . $_token . '" />
							</form>
						</div>
					</div>
				</div>
			';
		}
	?>
</div>
