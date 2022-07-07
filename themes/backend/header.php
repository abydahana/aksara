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
<nav class="navbar navbar-expand-lg navbar-dark fixed-top bg-dark">
	<div class="container-fluid">
		<a class="navbar-brand pt-0 pb-0 d-none d-lg-block" href="<?php echo base_url(); ?>" target="_blank">
			<img src="<?php echo get_image('settings', get_setting('app_icon'), 'icon'); ?>" class="img-fluid img-icon rounded" />
			<img src="<?php echo get_image('settings', get_setting('app_logo')); ?>" class="img-fluid img-logo rounded" />
			<?php echo (get_userdata('year') ? '<span class="badge badge-warning">' . get_userdata('year') . '</span>' : ''); ?>
		</a>
		<a href="<?php echo current_page(); ?>" class="--xhr navbar-brand pt-0 pb-0 d-block d-lg-none text-truncate" id="will-be-replace-with-title">
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
				<?php if($language_list) { ?>
				<li class="nav-item dropdown">
					<a class="nav-link dropdown-toggle" href="#" id="languageDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						<i class="mdi mdi-translate"></i>
						<?php echo $user_language; ?>
					</a>
					<ul class="dropdown-menu dropdown-menu-end">
						<?php echo $language_list; ?>
					</ul>
				</li>
				<?php } ?>
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
