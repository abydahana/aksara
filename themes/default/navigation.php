<?php
	$user_language								= null;
	$translations								= array();
	$language									= get_languages();
	$language_list								= null;
	
	if($language && sizeof((array) $language) > 1)
	{
		foreach($language as $key => $val)
		{
			if(get_userdata('language_id') == $val->id)
			{
				$user_language					= $val->language;
			}
			
			$translations[$val->code]			= $val->language;
			
			$language_list						.= '
				<li class="nav-item">
					<a class="nav-link nav-padding-left --xhr" href="' . base_url('xhr/language/' . $val->code) . '">
						<i class="mdi mdi-flag-outline"></i>
						' . $val->language . '
					</a>
				</li>
			';
		}
	}
?>
<nav class="navbar fixed-top navbar-expand-lg navbar-dark bg-dark" id="hide-on-scroll">
	<a class="navbar-brand --xhr" href="<?php echo base_url(); ?>">
		<img src="<?php echo get_image('settings', get_setting('app_logo')); ?>" class="rounded" />
		<?php echo (get_userdata('year') ? '<span class="badge badge-warning">' . get_userdata('year') . '</span>' : ''); ?>
	</a>
	<button class="navbar-toggler collapsed" type="button" data-toggle="collapse" data-target="#navbarExpand" aria-label="Toggle navigation">
		<span class="navbar-toggler-icon"></span>
	</button>
	<div class="collapse navbar-collapse" id="navbarExpand">
		<div class="p-3 user-bg-masking d-lg-none d-xl-none" onclick="component.profile($(this))" data-translations="<?php echo htmlspecialchars(json_encode($translations)); ?>" data-dashboard-url="<?php echo base_url('dashboard'); ?>" data-guidelines-url="<?php echo base_url('pages/guidelines'); ?>">
			<div class="row">
				<div class="col-3">
					<img src="<?php echo get_image('users', get_userdata('photo'), 'icon'); ?>" class="img-fluid rounded" />
				</div>
				<div class="col-9 pl-0">
					<h6 class="mb-0 text-break-word">
						<?php echo (get_userdata('is_logged') ? get_userdata('first_name') . ' ' . get_userdata('last_name') : phrase('one_more_step')); ?>
					</h6>
					<p class="text-sm">
						<i class="mdi mdi-circle <?php echo (get_userdata('is_logged') ? 'text-success' : 'text-muted'); ?>"></i>
						<?php echo (get_userdata('is_logged') ? phrase('online') : phrase('offline')); ?>
						<?php echo (get_userdata('year') ? '<span class="badge badge-warning d-md-none d-lg-none d-xl-none">' . get_userdata('year') . '</span>' : ''); ?>
					</p>
					<p class="mb-0">
						<?php
							if(get_userdata('is_logged'))
							{
								echo '
									<button type="button" class="btn btn-outline-primary btn-xs">
										<i class="mdi mdi-cogs"></i>
										' . phrase('account') . '
									</button>
								';
							}
							else
							{
								echo '
									<a href="' . base_url('auth') . '" class="--xhr btn btn-outline-primary btn-xs d-lg-none d-xl-none">
										<i class="mdi mdi-login"></i>
										' . phrase('sign_in') . '
									</a>
								';
							}
						?>
					</p>
				</div>
			</div>
		</div>
		
		<?php echo $template->menu; ?>
		
		<ul class="navbar-nav ml-auto">
			<?php if($language_list) { ?>
			<li class="nav-item dropdown d-none d-lg-block d-xl-block">
				<a class="nav-link nav-padding-left dropdown-toggle" href="#" id="languageDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					<i class="mdi mdi-translate"></i>
					<?php echo $user_language; ?>
				</a>
				<ul class="dropdown-menu dropdown-menu-right">
					<?php echo $language_list; ?>
				</ul>
			</li>
			<?php } ?>
			<?php if(get_userdata('is_logged')) { ?>
			<li class="nav-item dropdown d-none d-lg-block d-xl-block">
				<a class="nav-link nav-padding-left dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					<i class="mdi mdi-account-outline"></i>
					<?php echo truncate(get_userdata('first_name'), 16); ?>
				</a>
				<ul class="dropdown-menu dropdown-menu-right">
					<li class="nav-item">
						<h6 class="nav-link nav-padding-left dropdown-header d-none d-md-block d-lg-block d-xl-block">
							<?php echo phrase('user_panel'); ?>
						</h6>
					</li>
					<li class="nav-item">
						<a class="nav-link nav-padding-left" href="<?php echo base_url('dashboard'); ?>">
							<i class="mdi mdi-monitor-dashboard"></i>
							<?php echo phrase('dashboard'); ?>
						</a>
					</li>
					<li class="nav-item">
						<a class="nav-link nav-padding-left" href="<?php echo base_url('administrative/account'); ?>">
							<i class="mdi mdi-account-circle-outline"></i>
							<?php echo phrase('account'); ?>
						</a>
					</li>
					<!-- divider -->
					<li class="separator"></li>
					
					<li class="nav-item">
						<a class="nav-link nav-padding-left --xhr text-danger" href="<?php echo base_url('auth/sign_out'); ?>">
							<i class="mdi mdi-logout"></i>
							<?php echo phrase('sign_out'); ?>
						</a>
					</li>
				</ul>
			</li>
			<?php } else { ?>
			<li class="nav-item d-none d-lg-block d-xl-block">
				<a href="<?php echo base_url('auth'); ?>" class="nav-link nav-padding-left" onclick="component.login(this, event)">
					<i class="mdi mdi-login"></i>
					<?php echo phrase('sign_in'); ?>
				</a>
			</li>
			<?php } ?>
		</ul>
	</div>
</nav>
