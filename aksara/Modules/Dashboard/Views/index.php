<?php
	if(in_array(get_userdata('group_id'), array(1)) && (!$permission->uploads || !$permission->writable))
	{
		echo '
			<div class="alert alert-danger pr-3 pl-3 rounded-0 mb-0">
				<h5>
					' . phrase('notice') . '
				</h5>
				' . (!$permission->uploads ? '<p class="mb-0"><b>' . FCPATH . UPLOAD_PATH . '/</b> ' . phrase('is_not_writable') . '</p>' : null) . '
				' . (!$permission->writable ? '<p class="mb-0"><b>' . WRITEPATH . '</b> ' . phrase('is_not_writable') . '</p>' : null) . '
				<br />
				<a href="//github.com/abydahana/Aksara/issues/2" target="_blank"><b>' . phrase('click_here') . '</b></a> ' . phrase('to_get_advice_how_to_solve_this_issue') . '
			</div>
		';
	}
?>
<div class="updater-placeholder"></div>
<div class="container-fluid">
	<div class="row border-bottom pt-3">
		<div class="col-6 col-lg-3 mb-3">
			<a href="<?php echo base_url('cms/blogs'); ?>" class="d-block --xhr" data-toggle="tooltip" title="<?php echo phrase('manage_blog_post'); ?>">
				<div class="card border-0 bg-primary text-center text-sm-left" style="overflow:hidden">
					<div class="row">
						<div class="col-sm-4">
							<div class="p-3 text-center" style="background:rgba(0, 0, 0, .1)">
								<i class="mdi mdi-newspaper mdi-2x text-light"></i>
							</div>
						</div>
						<div class="col-sm-8 p-3">
							<h5 class="m-0 text-truncate text-light">
								<?php echo phrase('blogs'); ?>
							</h5>
						</div>
					</div>
				</div>
			</a>
		</div>
		<div class="col-6 col-lg-3 mb-3">
			<a href="<?php echo base_url('cms/pages'); ?>" class="d-block --xhr" data-toggle="tooltip" title="<?php echo phrase('manage_front_end_pages'); ?>">
				<div class="card border-0 bg-info text-center text-sm-left" style="overflow:hidden">
					<div class="row">
						<div class="col-sm-4">
							<div class="p-3 text-center" style="background:rgba(0, 0, 0, .1)">
								<i class="mdi mdi-file-multiple mdi-2x text-light"></i>
							</div>
						</div>
						<div class="col-sm-8 p-3">
							<h5 class="m-0 text-truncate text-light">
								<?php echo phrase('pages'); ?>
							</h5>
						</div>
					</div>
				</div>
			</a>
		</div>
		<div class="col-6 col-lg-3 mb-3">
			<a href="<?php echo base_url('cms/galleries'); ?>" class="d-block --xhr" data-toggle="tooltip" title="<?php echo phrase('manage_galleries'); ?>">
				<div class="card border-0 bg-danger text-center text-sm-left" style="overflow:hidden">
					<div class="row">
						<div class="col-sm-4">
							<div class="p-3 text-center" style="background:rgba(0, 0, 0, .1)">
								<i class="mdi mdi-folder-multiple-image mdi-2x text-light"></i>
							</div>
						</div>
						<div class="col-sm-8 p-3">
							<h5 class="m-0 text-truncate text-light">
								<?php echo phrase('galleries'); ?>
							</h5>
						</div>
					</div>
				</div>
			</a>
		</div>
		<div class="col-6 col-lg-3 mb-3">
			<a href="<?php echo base_url('cms/peoples'); ?>" class="d-block --xhr" data-toggle="tooltip" title="<?php echo phrase('manage_peoples_or_team'); ?>">
				<div class="card border-0 bg-dark text-center text-sm-left" style="overflow:hidden">
					<div class="row">
						<div class="col-sm-4">
							<div class="p-3 text-center" style="background:rgba(0, 0, 0, .1)">
								<i class="mdi mdi-account-group-outline mdi-2x text-light"></i>
							</div>
						</div>
						<div class="col-sm-8 p-3">
							<h5 class="m-0 text-truncate text-light">
								<?php echo phrase('peoples'); ?>
							</h5>
						</div>
					</div>
				</div>
			</a>
		</div>
	</div>
	<div class="row">
		<div class="col-lg-8 bg-white border-right" style="margin-right:-1px">
			<div class="sticky-top pt-3" style="top:86px; z-index:0">
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
												' . ($num ? '<hr class="mt-2 mb-2" />' : null) . '
												<div class="row no-gutters">
													<div class="col-3 col-sm-2">
														<i class="mdi mdi-' . ($key == 'chrome' ? 'google-chrome text-success' : ($key == 'firefox' ? 'firefox text-warning' : ($key == 'safari' ? 'apple-safari text-primary' : ($key == 'edge' ? 'edge text-primary' : ($key == 'opera' ? 'opera text-danger' : ($key == 'explorer' ? 'internet-explorer text-info' : 'web text-muted')))))) . ' mdi-3x"></i>
													</div>
													<div class="col-9 col-sm-10 pl-3">
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
									<?php echo phrase('recent_signed_users'); ?>
								</h5>
							</div>
							<div class="card-body p-3">
								<?php
									foreach($recent_signed as $key => $val)
									{
										echo '
											' . ($key ? '<hr class="mt-2 mb-2" />' : null) . '
											<div class="row no-gutters">
												<div class="col-3 col-sm-2">
													<img src="' . get_image('users', $val->photo, 'icon') . '" class="img-fluid rounded" />
												</div>
												<div class="col-9 col-sm-10 pl-3">
													<b>
														' . $val->first_name . ' ' . $val->last_name . '
													</b>
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
		<div class="col-lg-4 bg-white border-left" style="margin-left:-1px">
			<div class="sticky-top pt-3" style="top:86px; z-index:0">
				<div class="row">
					<div class="col-6">
						<div class="form-group border-bottom">
							<label class="d-block text-muted mb-0">
								AKSARA
							</label>
							<p>
								<?php echo aksara('version'); ?>
							</p>
						</div>
					</div>
					<div class="col-6">
						<div class="form-group border-bottom">
							<label class="d-block text-muted mb-0">
								<?php echo phrase('built_version'); ?>
							</label>
							<p>
								<?php echo aksara('built_version'); ?>
							</p>
						</div>
					</div>
				</div>
				<div class="form-group border-bottom">
					<label class="d-block text-muted mb-0">
						<?php echo phrase('last_modified'); ?>
					</label>
					<p>
						<?php echo aksara('date_modified'); ?>
					</p>
				</div>
				<div class="form-group border-bottom">
					<label class="d-block text-muted mb-0">
						<?php echo phrase('uploaded_file'); ?>
					</label>
					<p class="uploaded-file">
						0
					</p>
				</div>
				<div class="form-group border-bottom">
					<label class="d-block text-muted mb-0">
						<?php echo phrase('system_language'); ?>
					</label>
					<p>
						<?php echo (isset($system_language) ? $system_language : null); ?>
					</p>
				</div>
				<div class="form-group">
					<label class="d-block text-muted mb-0">
						<?php echo phrase('membership'); ?>
					</label>
					<p>
						<?php echo (get_setting('frontend_registration') ? '<span class="badge badge-success">' . phrase('enabled') . '</span>' : '<span class="badge badge-danger">' . phrase('disabled') . '</span>'); ?>
					</p>
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
				var size							= 0;
				interval							= setInterval(function()
				{
					$('.uploaded-file').text(size);
					
					size++;
				}, 50)
			}
		})
		.done(function(response)
		{
			if(response.update_available)
			{
				$('.updater-placeholder').html
				(
					'<div class="alert alert-info text-sm rounded-0 border-0 p-3 mb-0">' +
						'<h5>' +
							'<?php echo phrase('update_available'); ?>' +
						'</h5>' +
						'<p>' +
							'<?php echo phrase('a_newer_version_of_aksara_is_available'); ?> ' +
							'<?php echo phrase('click_the_button_below_to_update_your_core_system_directly'); ?> ' +
							'<?php echo phrase('your_created_modules_and_themes_will_not_be_replaced'); ?>' +
						'</p>' +
						'<hr />' +
						'<a href="<?php echo base_url('administrative/updater'); ?>" class="btn btn-sm btn-success --xhr">' +
							'<i class="mdi mdi-update"></i>' +
							'<?php echo phrase('update_now'); ?>' +
						'</a>' +
					'</div>'
				)
			}
			
			$('.uploaded-file').text(response.upload_size),
			
			clearInterval(interval)
		})
	})
</script>
