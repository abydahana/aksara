<nav class="navbar navbar-expand-lg navbar-dark fixed-top bg-dark aksara-header">
	<div class="container-fluid">
		<a class="navbar-brand pt-0 pb-0 d-none d-lg-block --xhr" href="<?php echo base_url(); ?>">
			<img src="<?php echo get_image('settings', get_setting('app_icon'), 'icon'); ?>" class="img-fluid img-icon rounded" />
			<img src="<?php echo get_image('settings', get_setting('app_logo')); ?>" class="img-fluid img-logo rounded" />
			<?php echo (get_userdata('year') ? '<span class="badge bg-warning">' . get_userdata('year') . '</span>' : ''); ?>
		</a>
		<a href="<?php echo current_page(); ?>" class="--xhr navbar-brand pt-0 pb-0 d-block d-lg-none text-truncate" role="title">
			<?php echo $template->meta->title; ?>
		</a>
		<button class="navbar-toggler collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#navbarText" aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation">
			<span class="navbar-toggler-icon"></span>
		</button>
		<div class="collapse navbar-collapse" id="navbarText">
			<?php echo generate_menu($template->menu, 'navbar-nav me-auto', 'nav-item', 'nav-link --xhr', 'dropdown-toggle', 'data-bs-toggle="dropdown"', 'dropdown', 'dropdown-menu'); ?>
			<ul class="navbar-nav ml-auto">
				<li class="nav-item dropdown">
					<a class="nav-link dropdown-toggle" href="<?php echo base_url('xhr/partial/language'); ?>" data-bs-toggle="dropdown" role="language">
						<i class="mdi mdi-translate"></i>
						<?php echo phrase('language'); ?>
					</a>
					<ul class="dropdown-menu dropdown-menu-end">
						<!-- language list -->
					</ul>
				</li>
				<?php if(get_userdata('is_logged')) { ?>
				<li class="nav-item dropdown">
					<a class="nav-link dropdown-toggle" href="javascript:void(0)" data-bs-toggle="dropdown">
						<i class="mdi mdi-account-outline"></i>
						<?php echo truncate(get_userdata('first_name'), 16); ?>
					</a>
					<ul class="dropdown-menu dropdown-menu-end">
						<li class="nav-item">
							<h6 href="javascript:void(0)" class="nav-link dropdown-header text-muted">
								<i class="mdi mdi-blank"></i>
								<span>
									<?php echo phrase('user_panel'); ?>
								</span>
							</h6>
						</li>
						
						<li><hr class="dropdown-divider"></li>
						
						<li class="nav-item">
							<a class="nav-link" href="<?php echo base_url('dashboard'); ?>">
								<i class="mdi mdi-monitor-dashboard"></i>
								<span>
									<?php echo phrase('dashboard'); ?>
								</span>
							</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="<?php echo base_url('administrative/account'); ?>">
								<i class="mdi mdi-account-circle-outline"></i>
								<span>
									<?php echo phrase('account'); ?>
								</span>
							</a>
						</li>
						<!-- divider -->
						<li><hr class="dropdown-divider"></li>
						
						<li class="nav-item">
							<a class="nav-link text-danger" href="<?php echo base_url('auth/sign_out'); ?>">
								<i class="mdi mdi-logout"></i>
								<span>
									<?php echo phrase('sign_out'); ?>
								</span>
							</a>
						</li>
					</ul>
				</li>
				<?php } else { ?>
				<li class="nav-item">
					<a href="<?php echo base_url('auth'); ?>" class="nav-link --modal">
						<i class="mdi mdi-login"></i>
						<?php echo phrase('sign_in'); ?>
					</a>
				</li>
				<?php } ?>
			</ul>
		</div>
	</div>
</nav>