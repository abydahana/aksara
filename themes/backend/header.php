<nav class="navbar navbar-expand-lg navbar-dark fixed-top bg-dark">
	<div class="container-fluid">
		<a class="navbar-brand pt-0 pb-0 d-none d-lg-block" href="<?php echo base_url(); ?>" target="_blank">
			<img src="<?php echo get_image('settings', get_setting('app_icon'), 'icon'); ?>" class="img-fluid img-icon rounded" />
			<img src="<?php echo get_image('settings', get_setting('app_logo')); ?>" class="img-fluid img-logo rounded" />
			<?php echo (get_userdata('year') ? '<span class="badge badge-warning">' . get_userdata('year') . '</span>' : ''); ?>
		</a>
		<a href="<?php echo current_page(); ?>" class="--xhr navbar-brand pt-0 pb-0 d-block d-lg-none text-truncate" role="title">
			<?php echo $template->meta->title; ?>
		</a>
		<button class="navbar-toggler border-0" type="button" data-toggle="sidebar">
			<span class="navbar-toggler-icon"></span>
		</button>
		<div class="collapse navbar-collapse" id="navbarText">
			<ul class="navbar-nav me-auto">
				<li class="nav-item">
					<a href="javascript:void(0)" class="nav-link" data-toggle="sidebar">
						<i class="mdi mdi-arrow-left"></i>
					</a>
				</li>
			</ul>
			<ul class="navbar-nav ml-auto">
				<li class="nav-item">
					<a href="javascript:void(0)" class="nav-link --toggle-full-screen">
						<i class="mdi mdi-fullscreen"></i>
					</a>
				</li>
				<li class="nav-item dropdown">
					<a class="nav-link dropdown-toggle" href="<?php echo base_url('xhr/partial/language'); ?>" data-bs-toggle="dropdown" role="language">
						<i class="mdi mdi-translate"></i>
						<?php echo phrase('language'); ?>
					</a>
					<ul class="dropdown-menu dropdown-menu-end">
						<!-- language list -->
					</ul>
				</li>
				<li class="nav-item">
					<a href="<?php echo base_url('administrative/account'); ?>" class="nav-link --xhr">
						<i class="mdi mdi-cogs"></i>
						<?php echo phrase('account'); ?>
					</a>
				</li>
				<li class="nav-item">
					<a href="<?php echo base_url('auth/sign_out'); ?>" class="nav-link --xhr">
						<i class="mdi mdi-logout"></i>
						<?php echo phrase('sign_out'); ?>
					</a>
				</li>
			</ul>
		</div>
	</div>
</nav>
