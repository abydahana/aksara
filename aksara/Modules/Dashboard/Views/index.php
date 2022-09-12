<?php
	if(in_array(get_userdata('group_id'), array(1)) && (!$permission->uploads || !$permission->writable))
	{
		echo '
			<div class="alert alert-danger pe-3 ps-3 rounded-0 mb-0">
				<h5>
					' . phrase('notice') . '
				</h5>
				' . (!$permission->uploads ? '<p class="mb-0"><b>' . str_replace('\\', '/', FCPATH . UPLOAD_PATH) . '/</b> ' . phrase('is_not_writable') . '</p>' : null) . '
				' . (!$permission->writable ? '<p class="mb-0"><b>' . str_replace('\\', '/', WRITEPATH) . '</b> ' . phrase('is_not_writable') . '</p>' : null) . '
				<br />
				<a href="//github.com/abydahana/Aksara/issues/2" target="_blank"><b>' . phrase('click_here') . '</b></a> ' . phrase('to_get_advice_how_to_solve_this_issue') . '
			</div>
		';
	}
	else if(in_array(get_userdata('group_id'), array(1)) && is_dir(ROOTPATH . 'install'))
	{
		echo '
			<div class="alert alert-warning pe-3 ps-3 rounded-0 mb-0">
				<h5>
					' . phrase('notice') . '
				</h5>
				' . phrase('please_remove_or_rename_the_following_directory_to_secure_your_application') . ' <code>' . str_replace('\\', '/', ROOTPATH) . 'install</code>
			</div>
		';
	}
?>
<div class="updater-placeholder"></div>
<div class="container-fluid">
	<div class="row border-bottom pt-3 pb-3" id="greeting-card">
		<div class="col-12">
			<div class="card">
				<div class="card-body p-3">
					<h3>
						<a href="javascript:void(0)" class="btn btn-close float-end" onclick="jExec($(this).closest('#greeting-card').slideUp())"></a>
						<?php echo phrase('welcome_to'); ?> Aksara!
					</h3>
					<p class="text-muted">
						<?php echo phrase('we_have_assembled_some_links_to_get_you_started'); ?>
					</p>
					<div class="row">
						<div class="col-md-4 mb-3 mb-md-0">
							<h5 class="mb-3 mb-md-5">
								<?php echo phrase('getting_started'); ?>
							</h5>
							<div class="text-center text-md-start">
								<a href="<?php echo base_url('administrative/settings'); ?>" class="btn btn-info pe-5 ps-5 --xhr">
									<i class="mdi mdi-color-palette"></i>
									<?php echo phrase('customize_your_app'); ?>
								</a>
							</div>
						</div>
						<div class="col-md-4 mb-3 mb-md-0">
							<h5 class="mb-3">
								<?php echo phrase('next_step'); ?>
							</h5>
							<div class="row">
								<div class="col-1">
									<i class="mdi mdi-plus"></i>
								</div>
								<div class="col-11">
									<a href="<?php echo base_url('cms/blogs'); ?>" class="text-primary --xhr">
										<?php echo phrase('write_a_blog_post'); ?>
									</a>
								</div>
							</div>
							<div class="row">
								<div class="col-1">
									<i class="mdi mdi-file"></i>
								</div>
								<div class="col-11">
									<a href="<?php echo base_url('cms/pages'); ?>" class="text-primary --xhr">
										<?php echo phrase('manage_the_pages'); ?>
									</a>
								</div>
							</div>
							<div class="row">
								<div class="col-1">
									<i class="mdi mdi-monitor-dashboard"></i>
								</div>
								<div class="col-11">
									<a href="<?php echo base_url(); ?>" class="text-primary" target="_blank">
										<?php echo phrase('view_your_site'); ?>
									</a>
								</div>
							</div>
						</div>
						<div class="col-md-4 mb-3 mb-md-0">
							<h5 class="mb-3">
								<?php echo phrase('more_actions'); ?>
							</h5>
							<div class="row">
								<div class="col-1">
									<i class="mdi mdi-puzzle"></i>
								</div>
								<div class="col-11">
									<?php echo phrase('manage'); ?> <a href="<?php echo base_url('addons'); ?>" class="text-primary --xhr"><?php echo phrase('add_ons'); ?></a> <?php echo phrase('or'); ?> <a href="<?php echo base_url('administrative/menus'); ?>" class="text-primary --xhr"><?php echo phrase('menus'); ?></a>
								</div>
							</div>
							<div class="row">
								<div class="col-1">
									<i class="mdi mdi-comment-multiple-outline"></i>
								</div>
								<div class="col-11">
									<a href="<?php echo base_url('cms/comments'); ?>" class="text-primary --xhr">
										<?php echo phrase('turn_comments_on_or_off'); ?>
									</a>
								</div>
							</div>
							<div class="row">
								<div class="col-1">
									<i class="mdi mdi-information-outline"></i>
								</div>
								<div class="col-11">
									<a href="//aksaracms.com/pages/documentation" class="text-primary" target="_blank">
										<?php echo phrase('learn_more_about'); ?> Aksara
									</a>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="row border-bottom pt-3">
		<div class="col-6 col-lg-3 mb-3">
			<a href="<?php echo base_url('cms/blogs'); ?>" class="d-block --xhr" data-bs-toggle="tooltip" title="<?php echo phrase('manage_blog_post'); ?>">
				<div class="card border-0 bg-primary text-center text-sm-start" style="overflow:hidden">
					<div class="row align-items-center">
						<div class="col-sm-4 col-xl-3" style="background:rgba(0, 0, 0, .1)">
							<div class="p-3 text-center">
								<i class="mdi mdi-newspaper mdi-2x text-light"></i>
							</div>
						</div>
						<div class="col-sm-8 col-xl-9">
							<h5 class="m-0 text-truncate text-light">
								<?php echo phrase('blogs'); ?>
							</h5>
							<p class="text-light mb-0">
								<?php echo number_format($card->blogs) . ' ' . ($card->blogs > 2 ? phrase('articles') : phrase('article')); ?>
							</p>
						</div>
					</div>
				</div>
			</a>
		</div>
		<div class="col-6 col-lg-3 mb-3">
			<a href="<?php echo base_url('cms/pages'); ?>" class="d-block --xhr" data-bs-toggle="tooltip" title="<?php echo phrase('manage_front_end_pages'); ?>">
				<div class="card border-0 bg-info text-center text-sm-start" style="overflow:hidden">
					<div class="row align-items-center">
						<div class="col-sm-4 col-xl-3" style="background:rgba(0, 0, 0, .1)">
							<div class="p-3 text-center">
								<i class="mdi mdi-file-multiple mdi-2x text-light"></i>
							</div>
						</div>
						<div class="col-sm-8 col-xl-9">
							<h5 class="m-0 text-truncate text-light">
								<?php echo phrase('pages'); ?>
							</h5>
							<p class="text-light mb-0">
								<?php echo number_format($card->pages) . ' ' . ($card->pages > 2 ? phrase('pages') : phrase('page')); ?>
							</p>
						</div>
					</div>
				</div>
			</a>
		</div>
		<div class="col-6 col-lg-3 mb-3">
			<a href="<?php echo base_url('cms/galleries'); ?>" class="d-block --xhr" data-bs-toggle="tooltip" title="<?php echo phrase('manage_galleries'); ?>">
				<div class="card border-0 bg-danger text-center text-sm-start" style="overflow:hidden">
					<div class="row align-items-center">
						<div class="col-sm-4 col-xl-3" style="background:rgba(0, 0, 0, .1)">
							<div class="p-3 text-center">
								<i class="mdi mdi-folder-multiple-image mdi-2x text-light"></i>
							</div>
						</div>
						<div class="col-sm-8 col-xl-9">
							<h5 class="m-0 text-truncate text-light">
								<?php echo phrase('galleries'); ?>
							</h5>
							<p class="text-light mb-0">
								<?php echo number_format($card->galleries) . ' ' . ($card->galleries > 2 ? phrase('albums') : phrase('album')); ?>
							</p>
						</div>
					</div>
				</div>
			</a>
		</div>
		<div class="col-6 col-lg-3 mb-3">
			<a href="<?php echo base_url('administrative/users'); ?>" class="d-block --xhr" data-bs-toggle="tooltip" title="<?php echo phrase('manage_peoples_or_team'); ?>">
				<div class="card border-0 bg-dark text-center text-sm-start" style="overflow:hidden">
					<div class="row align-items-center">
						<div class="col-sm-4 col-xl-3" style="background:rgba(0, 0, 0, .1)">
							<div class="p-3 text-center">
								<i class="mdi mdi-account-group-outline mdi-2x text-light"></i>
							</div>
						</div>
						<div class="col-sm-8 col-xl-9">
							<h5 class="m-0 text-truncate text-light">
								<?php echo phrase('users'); ?>
							</h5>
							<p class="text-light mb-0">
								<?php echo number_format($card->users) . ' ' . ($card->users > 2 ? phrase('users') : phrase('user')); ?>
							</p>
						</div>
					</div>
				</div>
			</a>
		</div>
	</div>
	<div class="row">
		<div class="col-lg-8 bg-white">
			<div class="sticky-top pt-3">
				<div class="border rounded p-1 mb-3">
					<div id="visitor-chart" class="rounded" style="width:100%; height:300px"></div>
				</div>
				<div class="row">
					<div class="col-md-6 mb-3">
						<div class="card">
							<div class="card-header bg-white border-0">
								<h5 class="card-title mb-0">
									<?php echo phrase('most_used_browser'); ?>
								</h5>
							</div>
							<div class="card-body p-3">
								<?php
									$num			= 0;
									
									if(isset($visitors->browsers))
									{
										foreach($visitors->browsers as $key => $val)
										{
											echo '
												' . ($num ? '<hr class="border-secondary mt-2 mb-2" />' : null) . '
												<div class="row no-gutters align-items-center">
													<div class="col-3 col-sm-2">
														<i class="mdi mdi-' . ($key == 'chrome' ? 'google-chrome text-success' : ($key == 'firefox' ? 'firefox text-warning' : ($key == 'safari' ? 'apple-safari text-primary' : ($key == 'edge' ? 'edge text-primary' : ($key == 'opera' ? 'opera text-danger' : ($key == 'explorer' ? 'internet-explorer text-info' : 'web text-muted')))))) . ' mdi-3x"></i>
													</div>
													<div class="col-9 col-sm-10 ps-3">
														<b>
															' . ($key == 'chrome' ? phrase('google_chrome') : ($key == 'firefox' ? phrase('mozilla_firefox') : ($key == 'safari' ? phrase('safari') : ($key == 'edge' ? phrase('microsoft_edge') : ($key == 'opera' ? phrase('opera') : ($key == 'explorer' ? phrase('internet_explorer') : phrase('unknown'))))))) . '
														</b>
														<p class="mb-0 text-sm text-muted">
															' . number_format($val) . ' ' . phrase('usage_in_a_week') . '
														</p>
													</div>
												</div>
											';
											$num++;
										}
									}
								?>
							</div>
						</div>
					</div>
					<div class="col-md-6 mb-3">
						<div class="card">
							<div class="card-header bg-white border-0">
								<h5 class="card-title mb-0">
									<?php echo phrase('recent_sign_in'); ?>
								</h5>
							</div>
							<div class="card-body p-3">
								<?php
									foreach($recent_signed as $key => $val)
									{
										echo '
											' . ($key ? '<hr class="mt-2 mb-2" />' : null) . '
											<div class="row no-gutters align-items-center">
												<div class="col-3 col-sm-2">
													<a href="' . base_url('user', array('user_id' => $val->user_id)) . '" target="_blank">
														<img src="' . get_image('users', $val->photo, 'icon') . '" class="img-fluid rounded" />
													</a>
												</div>
												<div class="col-9 col-sm-10 ps-3">
													<a href="' . base_url('user', array('user_id' => $val->user_id)) . '" target="_blank">
														<b>
															' . $val->first_name . ' ' . $val->last_name . '
														</b>
													</a>
													<p class="mb-0 text-sm text-muted">
														' . $val->group_name . '
													</p>
												</div>
											</div>
										';
									}
								?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-lg-4 bg-white">
			<div class="sticky-top pt-3">
				<?php if($announcements) { ?>
					<div class="card mb-3">
						<div class="card-header bg-white border-0">
							<h5 class="card-title mb-0">
								<?php echo phrase('announcements'); ?>
							</h5>
						</div>
						<?php
							$announcement			= null;
							
							foreach($announcements as $key => $val)
							{
								$announcement		.= '<li class="list-group-item"><a href="' . base_url('announcements/' . $val->announcement_slug) . '" class="--xhr">' . $val->title . '</a></li>';
								$announcement		.= '<li class="list-group-item"><a href="' . base_url('announcements/' . $val->announcement_slug) . '" class="--xhr">' . $val->title . '</a></li>';
							}
							
							echo '
								<ul class="list-group list-group-flush">
									' . $announcement . '
								</ul>
							';
						?>
					</div>
				<?php } ?>
				
				<div class="card mb-3">
					<div class="card-header bg-white border-0">
						<h5 class="card-title mb-0">
							<?php echo phrase('application_information'); ?>
						</h5>
					</div>
					<div class="card-body p-3">
						<div class="row">
							<div class="col-6">
								<div class="mb-3">
									<label class="d-block text-muted mb-0">
										AKSARA
									</label>
									<p>
										<?php echo aksara('version'); ?>
									</p>
								</div>
							</div>
							<div class="col-6">
								<div class="mb-3">
									<label class="d-block text-muted mb-0">
										<?php echo phrase('build_version'); ?>
									</label>
									<p>
										<?php echo aksara('build_version'); ?>
									</p>
								</div>
							</div>
						</div>
						<div class="mb-3">
							<label class="d-block text-muted mb-0">
								<?php echo phrase('last_modified'); ?>
							</label>
							<p>
								<?php echo aksara('date_modified'); ?>
							</p>
						</div>
						<div class="row">
							<div class="col-sm-6">
								<div class="mb-3">
									<label class="d-block text-muted mb-0">
										<?php echo phrase('system_language'); ?>
									</label>
									<p>
										<?php echo (isset($system_language) ? $system_language : null); ?>
									</p>
								</div>
							</div>
							<div class="col-sm-6">
								<div class="mb-3">
									<label class="d-block text-muted mb-0">
										<?php echo phrase('membership'); ?>
									</label>
									<p>
										<?php echo (get_setting('frontend_registration') ? '<span class="badge bg-success">' . phrase('enabled') . '</span>' : '<span class="badge bg-danger">' . phrase('disabled') . '</span>'); ?>
									</p>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-sm-6">
								<div class="mb-3">
									<label class="d-block text-muted mb-0">
										<?php echo phrase('secure_login_attempt'); ?>
									</label>
									<p>
										<?php echo (get_setting('login_attempt') ? '<span class="badge bg-success">' . get_setting('login_attempt') . ' ' . phrase('times') . '</span>' : '<span class="badge bg-danger">' . phrase('disabled') . '</span>'); ?>
									</p>
								</div>
							</div>
							<div class="col-sm-6">
								<div class="mb-3">
									<label class="d-block text-muted mb-0">
										<?php echo phrase('blocking_time'); ?>
									</label>
									<p>
										<?php echo (get_setting('blocking_time') ? '<span class="badge bg-success">' . get_setting('blocking_time') . ' ' . phrase('minutes') . '</span>' : '<span class="badge bg-danger">' . phrase('disabled') . '</span>'); ?>
									</p>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	$(document).ready(function()
	{
		if(typeof interval !== 'undefined')
		{
			clearInterval(interval)
		}
		
		var interval;
		
		require.js('<?php echo asset_url('highcharts/highcharts.min.js'); ?>', function()
		{
			Highcharts.chart('visitor-chart',
			{
				chart:
				{
					type: 'areaspline'
				},
				title:
				{
					text: '<b><?php echo phrase('visitor_graph'); ?></b>'
				},
				legend:
				{
					layout: 'vertical',
					align: 'left',
					verticalAlign: 'top',
					x: 150,
					y: 100,
					floating: true,
					borderWidth: 1,
					borderRadius: 5
				},
				xAxis:
				{
					categories: <?php echo (isset($visitors->categories) ? json_encode($visitors->categories) : '[]'); ?>,
					plotBands:
					[{
						from: 5.5,
						to: 7.5,
						color: 'rgba(68, 170, 213, .2)'
					}]
				},
				yAxis:
				{
					title:
					{
						text: '<?php echo phrase('visitor_total'); ?>'
					},
					allowDecimals: false
				},
				tooltip:
				{
					shared: true,
					valueSuffix: ' <?php echo phrase('visits'); ?>'
				},
				credits:
				{
					enabled: false
				},
				plotOptions:
				{
					areaspline:
					{
						fillOpacity: .5
					}
				},
				series:
				[{
					name: '<?php echo phrase('visitors'); ?>',
					data: <?php echo (isset($visitors->visits) ? json_encode($visitors->visits) : '[]'); ?>
				}]
			})
		}),
		
		$.ajax
		({
			url: '<?php echo current_page(); ?>',
			method: 'POST',
			data:
			{
				request: 'fetch_information'
			},
			beforeSend: function()
			{
			}
		})
		.done(function(response)
		{
			if(response.update_available)
			{
				$('.updater-placeholder').html
				(
					'<div class="alert alert-info rounded-0 border-0 p-3 mb-0">' +
						'<h5>' +
							'<?php echo phrase('update_available'); ?>' +
						'</h5>' +
						'<p>' +
							'<?php echo phrase('a_newer_version_of_aksara_is_available'); ?> ' +
							'<?php echo phrase('click_the_button_below_to_update_your_core_system_directly'); ?> ' +
							'<?php echo phrase('your_created_module_and_theme_will_not_be_overwritten'); ?>' +
						'</p>' +
						'<hr />' +
						'<a href="<?php echo base_url('administrative/updater'); ?>" class="btn btn-sm btn-success rounded-pill --xhr">' +
							'<i class="mdi mdi-update"></i>' +
							'<?php echo phrase('update_now'); ?>' +
						'</a>' +
					'</div>'
				)
			}
			
			clearInterval(interval)
		})
	})
</script>
