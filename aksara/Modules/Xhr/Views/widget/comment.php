<form action="<?php echo current_page(); ?>" method="POST" class="--validate-form border-top border-bottom pt-3 pb-3 mb-3">
	<div class="form-group mb-3">
		<div class="row align-items-center">
			<div class="col-2 col-lg-1">
				<img src="<?php echo get_image('users', get_userdata('photo'), 'thumb'); ?>" class="img-fluid rounded-circle" />
			</div>
			<div class="col-10 col-lg-11">
				<textarea name="comments" class="form-control" placeholder="type a comment..." rows="1"></textarea>
			</div>
		</div>
	</div>
	<div class="row align-items-center">
		<div class="col-10 offset-2 col-lg-11 offset-lg-1">
			<div class="--validation-callback"></div>
		</div>
	</div>
	<div class="row align-items-center">
		<div class="col-5 offset-2 col-lg-6 offset-lg-1">
			<label>
				<input type="checkbox" name="anonymous" class="form-check-input" value="1" />
				<?php echo phrase('anonymous'); ?>
			</label>
		</div>
		<div class="col-5 col-lg-5">
			<input type="hidden" name="post_id" value="<?php echo service('request')->getGet('post_id'); ?>" />
			<input type="hidden" name="comment_type" value="<?php echo service('request')->getGet('type'); ?>" />
			<input type="hidden" name="_token" value="<?php echo $_token; ?>" />
			<button type="submit" class="btn btn-primary btn-sm float-end">
				<i class="mdi mdi-check"></i>
				<?php echo phrase('comment'); ?>
			</button>
		</div>
	</div>
</form>
<div id="comment-container">
	<?php
		foreach($results as $key => $val)
		{
			echo '
				<div class="row mb-3">
					<div class="col-2 col-lg-1 pt-3">
						<img src="' . get_image('users', (!$val->anonymous || $val->user_id == get_userdata('user_id') ? $val->photo : 'placeholder.png'), 'thumb') . '" class="img-fluid rounded-circle" />
					</div>
					<div class="col-10 col-lg-11">
						<div class="position-relative">
							<div class="dropdown position-absolute end-0">
								<button class="btn btn-link btn-sm dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
									<i class="mdi mdi-format-list-checks"></i>
								</button>
								<ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton1">
									<li>
										<a class="dropdown-item" href="#">
											' . phrase('approve') . '
										</a>
									</li>
									<li>
										<a class="dropdown-item" href="#">
											' . phrase('report') . '
										</a>
									</li>
									<li>
										<a class="dropdown-item" href="#">
											' . phrase('hide') . '
										</a>
									</li>
								</ul>
							</div>
						</div>
						<div class="bg-light rounded-4 p-3 d-inline-block">
							<a href="' . (!$val->anonymous || $val->user_id == get_userdata('user_id') ? base_url('user/' . $val->username) : 'javascript:void(0)') . '" class="--xhr">
								<b>
									' . (!$val->anonymous || $val->user_id == get_userdata('user_id') ? $val->first_name . ' ' . $val->last_name : phrase('anonymous')) . '
								</b>
							</a>
							<br />
							<div>
								' . $val->comments . '
							</div>
						</div>
						<div class="ps-3 pe-3">
							<a href="' . current_page('like') . '" class="--xhr text-sm">
								<b>
									' . phrase('like') . '
								</b>
							</a>
							&middot;
							<a href="' . current_page('reply') . '" class="--xhr text-sm">
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
			';
		}
	?>
</div>
