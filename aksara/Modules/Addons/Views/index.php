<div class="container-fluid pb-3">
	<div class="row border-bottom bg-white mb-3 sticky-top" style="overflow-x: auto; top:88px">
		<ul class="nav" style="flex-wrap: nowrap">
			<li class="nav-item">
				<a href="<?php echo go_to(); ?>" class="nav-link no-wrap --xhr active">
					<i class="mdi mdi-cart"></i>
					<?php echo phrase('market'); ?>
				</a>
			</li>
			<li class="nav-item">
				<a href="<?php echo go_to('themes'); ?>" class="nav-link no-wrap --xhr">
					<i class="mdi mdi-palette"></i>
					<?php echo phrase('installed_theme'); ?>
				</a>
			</li>
			<li class="nav-item">
				<a href="<?php echo go_to('modules'); ?>" class="nav-link no-wrap --xhr">
					<i class="mdi mdi-puzzle"></i>
					<?php echo phrase('installed_module'); ?>
				</a>
			</li>
			<li class="nav-item">
				<a href="<?php echo go_to('ftp'); ?>" class="nav-link no-wrap --xhr">
					<i class="mdi mdi-console-network"></i>
					<?php echo phrase('ftp_configuration'); ?>
				</a>
			</li>
		</ul>
	</div>
	<div class="row">
		<div class="col-md-4">
			<div class="row mb-3">
				<div class="col-6">
					<a href="<?php echo go_to(null, array('order' => 'popular')); ?>" class="btn btn-secondary btn-block btn-sm --xhr">
						<?php echo phrase('popular'); ?>
					</a>
				</div>
				<div class="col-6">
					<a href="<?php echo go_to(null, array('order' => 'latest')); ?>" class="btn btn-secondary btn-block btn-sm --xhr">
						<?php echo phrase('latest'); ?>
					</a>
				</div>
			</div>
		</div>
		<div class="col-md-6 offset-md-2">
			<form action="<?php echo go_to(null, array('per_page' => null)); ?>" method="POST" class="form-horizontal relative --xhr-form mb-3">
				<input type="text" name="q" class="form-control form-control-sm" placeholder="<?php echo phrase('search_add_on'); ?>" value="<?php echo service('request')->getGet('q'); ?>" />
				<button type="submit" class="btn float-right absolute top right">
					<i class="mdi mdi-magnify font-weight-bold"></i>
				</button>
			</form>
		</div>
	</div>
	<hr class="row mt-0" />
	<div class="row addon-listing">
		<!-- addon listing -->
	</div>
</div>

<script type="text/javascript">
	$(document).ready(function()
	{
		$.ajax
		({
			url: '<?php echo current_page(); ?>',
			method: 'POST',
			data:
			{
				source: 'market',
				order: '<?php echo (service('request')->getGet('order') ? service('request')->getGet('order') : null); ?>',
				keyword: '<?php echo (service('request')->getGet('q') ? service('request')->getGet('q') : null); ?>'
			},
			beforeSend: function()
			{
				$('.addon-listing').html
				(
					'<div class="col-lg-12">' +
						'<div class="spinner-border" role="status">' +
							'<span class="sr-only"><?php echo phrase('loading'); ?></span>' +
						'</div>' +
					'</div>'
				)
			},
			context: this
		})
		.done(function(response)
		{
			if(!response || typeof response[0] === 'undefined')
			{
				$('.addon-listing').html
				(
					'<div class="col-lg-12">' +
						'<div class="alert alert-warning">' +
							'<?php echo phrase('no_addon_available_for_your_current_aksara_version'); ?>' +
						'</div>' +
					'</div>'
				);
				
				return;
			}
			
			else if(typeof response.error !== 'undefined')
			{
				$('.addon-listing').html
				(
					'<div class="col-lg-12">' +
						'<div class="alert alert-warning">' +
							response.error +
						'</div>' +
					'</div>'
				);
				
				return;
			}
			
			$('.addon-listing').html(''),
			
			$.each(response, function(key, val)
			{
				if(val.addon_type == 'theme')
				{
					$(
						'<div class="col-sm-6 col-md-4 col-lg-3">' +
							'<div class="card shadow border-0 mb-3">' +
								'<a href="' + val.detail_url + '" class="--modal">' +
									'<div class="relative rounded-top bg-dark" style="background:url(' + val.screenshot.src + ') center center no-repeat; background-size: cover; height: 256px">' +
										'<div class="clip gradient-top rounded-top"></div>' +
										(val.type == 'backend' ? '<span class="badge badge-warning float-right mt-3 mr-3"><?php echo phrase('back_end'); ?></span>' : '<span class="badge badge-success float-right mt-3 mr-3"><?php echo phrase('front_end'); ?></span>') +
										'<div class="absolute w-100 text-center bottom p-3">' +
											'<b class="text-light">' +
												val.name +
											'</b>' +
										'</div>' +
									'</div>' +
								'</a>' +
								'<div class="card-body p-3">' +
									'<div class="row">' +
										'<div class="col-6">' +
											'<a href="' + val.install_url + '" class="btn btn-primary btn-block btn-xs --xhr show-progress">' +
												'<i class="mdi mdi-plus"></i>' +
												'<?php echo phrase('install'); ?>' +
											'</a>' +
										'</div>' +
										'<div class="col-6">' +
											'<a href="' + val.demo_url + '" class="btn btn-outline-primary btn-block btn-xs" target="_blank">' +
												'<i class="mdi mdi-magnify"></i>' +
												'<?php echo phrase('preview'); ?>' +
											'</a>' +
										'</div>' +
									'</div>' +
								'</div>' +
							'</div>' +
						'</div>'
					)
					.appendTo('.addon-listing')
				}
				else if(val.addon_type == 'module')
				{
					$(
						'<div class="col-sm-6 col-md-4 col-lg-3">' +
							'<div class="card shadow border-0 mb-3">' +
								'<a href="' + val.detail_url + '" class="--modal">' +
									'<div class="relative rounded-top bg-dark" style="background:url(' + val.screenshot.src + ') center center no-repeat; background-size: cover; height: 256px">' +
										'<div class="clip gradient-top rounded-top"></div>' +
										'<span class="badge badge-danger float-right mt-3 mr-3"><?php echo phrase('module'); ?></span>' +
										'<div class="absolute w-100 text-center bottom p-3">' +
											'<b class="text-light">' +
												val.name +
											'</b>' +
										'</div>' +
									'</div>' +
								'</a>' +
								'<div class="card-body p-3">' +
									'<div class="row">' +
										'<div class="col-6">' +
											'<a href="' + val.install_url + '" class="btn btn-primary btn-block btn-sm">' +
												'<?php echo phrase('install'); ?>' +
											'</a>' +
										'</div>' +
										'<div class="col-6">' +
											'<a href="' + val.demo_url + '" class="btn btn-outline-primary btn-block btn-sm" target="_blank">' +
												'<?php echo phrase('preview'); ?>' +
											'</a>' +
										'</div>' +
									'</div>' +
								'</div>' +
							'</div>' +
						'</div>'
					)
					.appendTo('.addon-listing')
				}
			})
		})
		.fail(function()
		{
		})
	})
</script>
