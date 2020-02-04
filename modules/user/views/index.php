<div class="container">
	<div class="row">
		<div class="col-md-3">
			<div class="sticky-top mb-5" style="top:130px">
				<img src="<?php echo get_image('users', $userdata->photo, 'thumb'); ?>" class="img-fluid rounded" alt="..." />
				
				<h5 class="mb-0">
					<?php echo $userdata->first_name . ' ' . $userdata->last_name; ?>
				</h5>
				<label class="text-muted">
					@<?php echo $userdata->username; ?>
				</label>
				<?php
					if($userdata->bio)
					{
						echo '
							<p>
								' . $userdata->bio . '
							</p>
						';
					}
					if($userdata->address)
					{
						echo '
							<p class="mb-1">
								<i class="mdi mdi-map-marker text-muted"></i>
								' . $userdata->address . '
							</p>
						';
					}
					if($userdata->email)
					{
						echo '
							<p class="mb-1">
								<i class="mdi mdi-email text-muted"></i>
								' . $userdata->email . '
							</p>
						';
					}
					if($userdata->registered_date)
					{
						echo '
							<p class="mb-1">
								<i class="mdi mdi-calendar text-muted"></i>
								' . phrase('registered') . ' <span class="text-lowercase" data-toggle="tooltip" title="' . $userdata->registered_date . '">' . time_ago($userdata->registered_date) . '</span>
							</p>
						';
					}
				?>
			</div>
		</div>
		<div class="col-md-9">
			<div class="sticky-top bg-white border-bottom" style="top: 56px">
				<div class="row">
					<div class="col-lg-9">
						<ul class="nav">
							<li class="nav-item">
								<a class="nav-link active font-weight-bold" href="#" style="border-bottom:3px solid #ccc">
									Post
								</a>
							</li>
							<li class="nav-item">
								<a class="nav-link font-weight-bold" href="#">
									About
								</a>
							</li>
						</ul>
					</div>
					<div class="col-lg-3">
						<button class="btn btn-outline-info btn-sm btn-block rounded-pill" style="margin-top: 6px">
							<i class="mdi mdi-account-plus"></i>
							Follow
						</button>
					</div>
				</div>
			</div>
			
			<div class="row">
				<div class="col-md-9">
					<?php
						$output							= null;
						if($timeline)
						{
							foreach($timeline as $key => $val)
							{
								if($val->is_blog)
								{
									$action				= 'writing an Article';
									$val->description	= '<a href="' . base_url('blogs/' . $val->slug) . '" target="_blank"><b>' . $val->description . '</b></a>';
								}
								elseif($val->is_blog_reply)
								{
									$action				= 'replying to <a href="' . $val->slug . '" target="_blank">Article</a>';
								}
								elseif($val->is_signmark)
								{
									$action				= 'signed a Mark';
								}
								elseif($val->is_signmark_reply)
								{
									$action				= 'replying to Signmark';
								}
								elseif($val->is_signmark_like)
								{
									$action				= 'likes a Signmark';
								}
								else
								{
									$action				= null;
								}
								echo '
									<div class="card mb-3">
										<div class="card-body">
											<div class="form-group">
												<div class="row">
													<div class="col-2 col-sm-1 pr-0">
														<a href="' . base_url('user/' . $userdata->username) . '" class="--xhr">
															<img src="' . get_image('users', $userdata->photo, 'icon') . '" class="card-img rounded" alt="..." />
														</a>
													</div>
													<div class="col-10 col-sm-11">
														<h6 class="card-title text-truncate mb-0">
															<a href="" class="--xhr">
																' . $userdata->first_name . ' ' . $userdata->last_name . '
															</a>
															<span class="text-sm text-secondary">
																' . $action . '
															</span>
															<div class="float-right">
																<a href="#" data-toggle="dropdown">
																	<i class="mdi mdi-dots-horizontal"></i>
																</a>
																<div class="dropdown-menu dropdown-menu-right">
																	<a class="dropdown-item" href="#">
																		<i class="mdi mdi-flag-variant-outline"></i>
																		Report
																	</a>
																	<a class="dropdown-item" href="#">
																		<i class="mdi mdi-window-close"></i>
																		Remove
																	</a>
																</div>
															</div>
														</h6>
														<p class="card-text">
															<span class="text-sm text-muted font-weight-normal" data-toggle="tooltip" title="' . $val->timestamp . '">
																' . strtolower(time_ago($val->timestamp)) . '
															</span>
														</p>
													</div>
												</div>
											</div>
											<p class="card-text">
												' . $val->description . '
											</p>
										</div>
										<div class="card-footer pt-1 pb-1">
											<div class="btn-group d-flex">
												<button type="button" class="btn btn-sm">
													<i class="mdi mdi-thumb-up-outline"></i>
													1 ' . phrase('like') . '
												</button>
												<button type="button" class="btn btn-sm">
													<i class="mdi mdi-comment-multiple-outline"></i>
													1 ' . phrase('comment') . '
												</button>
												<button type="button" class="btn btn-sm">
													<i class="mdi mdi-share-outline"></i>
													' . phrase('share') . '
												</button>
											</div>
										</div>
									</div>
								';
							}
						}
						
						echo $this->template->pagination($pagination);
					?>
				</div>
				<div class="col-md-3">
					<div class="sticky-top text-center pt-5 pb-5" style="top:130px">
						Space for something
					</div>
				</div>
			</div>
		</div>
	</div>
</div>