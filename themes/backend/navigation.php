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
<nav class="navbar fixed-top navbar-expand-lg navbar-dark bg-dark">
	<a class="navbar-brand desktop-logo d-none d-md-block d-lg-block d-xl-block" href="<?php echo base_url(); ?>" target="_blank">
		<img src="<?php echo get_image('settings', get_setting('app_logo')); ?>" class="img-fluid img-logo rounded" />
		<img src="<?php echo get_image('settings', get_setting('app_icon')); ?>" class="img-fluid img-icon rounded" />
		<?php echo (get_userdata('year') ? '<span class="badge badge-warning">' . get_userdata('year') . '</span>' : ''); ?>
	</a>
	<a href="#" class="navbar-brand --xhr d-md-none d-lg-none d-xl-none text-truncate text-white w-75 will-be-replace-with-title">
		<?php echo $template->meta->title; ?>
	</a>
	<button type="button" class="navbar-toggler collapsed pr-0 pl-0 d-none d-lg-block d-xl-block" data-toggle="sidebar" title="Toggle responsive left sidebar">
		<span class="navbar-toggler-icon desktop-toggler"></span>
	</button>
	<button type="button" class="navbar-toggler collapsed pr-0 pl-0" data-toggle="sidebar" title="Toggle responsive left sidebar">
		<span class="navbar-toggler-icon"></span>
	</button>
	<button type="button" class="navbar-toggler collapsed d-none" data-toggle="collapse" data-target="#navbarExpand" aria-label="Toggle navigation">
		<span class="navbar-toggler-icon"></span>
	</button>
	<div class="collapse navbar-collapse" id="navbarExpand">
		<ul class="navbar-nav ml-auto">
			<li class="nav-item">
				<a href="javascript:void(0)" class="nav-link nav-padding-left p-1 --toggle-full-screen">
					<i class="mdi mdi-fullscreen mdi-2x"></i>
				</a>
			</li>
			<?php if($language_list) { ?>
			<li class="nav-item dropdown">
				<a class="nav-link nav-padding-left dropdown-toggle" href="#" id="languageDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					<i class="mdi mdi-translate"></i>
					<?php echo $user_language; ?>
				</a>
				<ul class="dropdown-menu dropdown-menu-right">
					<?php echo $language_list; ?>
				</ul>
			</li>
			<?php } ?>
			<li class="nav-item">
				<a href="<?php echo base_url('administrative/account'); ?>" class="nav-link nav-padding-left --xhr">
					<i class="mdi mdi-cogs"></i>
					<?php echo phrase('account'); ?>
				</a>
			</li>
			<li class="nav-item">
				<a href="<?php echo base_url('auth/sign_out'); ?>" class="nav-link nav-padding-left --xhr">
					<i class="mdi mdi-logout"></i>
					<?php echo phrase('sign_out'); ?>
				</a>
			</li>
		</ul>
	</div>
</nav>
