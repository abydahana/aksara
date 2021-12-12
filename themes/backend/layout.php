<!DOCTYPE html>
<html lang="en"<?php echo (in_array(get_userdata('language'), array('ar')) ? ' dir="rtl"' : null); ?>>
	<head>
		<title><?php echo truncate($template->meta->title) . ' | ' . get_setting('app_name'); ?></title>
		<meta charset="UTF-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge" />
		<meta name="msapplication-navbutton-color" content="#007bff" />
		<meta name="theme-color" content="#007bff" />
		<meta name="apple-mobile-web-app-status-bar-style" content="#007bff" />
		<meta name="apple-mobile-web-app-capable" content="yes" />
		<meta name="viewport" content="user-scalable=no, width=device-width, height=device-height, initial-scale=1, maximum-scale=1" />
		<meta name="description" content="<?php echo truncate($template->meta->description); ?>" />
		<link rel="icon" type="image/x-icon" href="<?php echo get_image('settings', get_setting('app_icon'), 'icon'); ?>" />
		
		<?php echo aksara_header(); ?>
	</head>
	<body>
		
		<?php include_once('header.php'); ?>
		
		<?php include_once('breadcrumb.php'); ?>
		
		<div id="sidebar-wrapper" class="aksara-sidebar sidebar-menu">
			<div class="p-3 user-bg-masking hide-on-collapse">
				<div class="row">
					<div class="col-3 col-sm-2 col-lg-4">
						<a href="<?php echo base_url('user'); ?>">
							<img src="<?php echo get_image('users', get_userdata('photo'), 'icon'); ?>" class="img-fluid rounded" />
						</a>
					</div>
					<div class="col-9 col-sm-10 col-lg-8 pl-0">
						<a href="<?php echo base_url('user'); ?>">
							<h6 class="mb-0 text-break-word mb-0">
								<?php echo get_userdata('first_name') . ' ' . get_userdata('last_name'); ?>
							</h6>
						</a>
						<p class="text-sm">
							<i class="mdi mdi-circle text-success"></i>
							
							<?php
								echo phrase('online') . (get_userdata('year') ? '<span class="badge badge-warning d-md-none d-lg-none d-xl-none">' . get_userdata('year') . '</span>' : '');
							?>
						</p>
						<p class="d-lg-none d-xl-none mb-0">
							<a href="<?php echo base_url('xhr/partial/account'); ?>" class="btn btn-outline-primary btn-xs --modal --force-xs">
								<i class="mdi mdi-cogs"></i>
								<?php echo phrase('account'); ?>
							</a>
							<a href="<?php echo base_url('xhr/partial/language'); ?>" class="btn btn-xs float-right --modal --force-xs">
								<i class="mdi mdi-translate"></i>
								<?php echo phrase('language'); ?>
								<i class="mdi mdi-chevron-down"></i>
							</a>
						</p>
					</div>
				</div>
			</div>
			
			<?php echo $template->menu; ?>
			
		</div>
		
		<div id="content-placeholder">
			<div class="container-fluid d-none d-md-none d-lg-block d-xl-block">
				<div class="row alias-table-header border-bottom">
					<div class="col-8">
						<h5 class="text-truncate">
							<i class="<?php echo $meta->icon; ?>" id="will-be-replace-with-icon"></i>
							<span id="will-be-replace-with-title">
								<?php echo $meta->title; ?>
							</span>
						</h5>
					</div>
					<div class="col-4 text-right">
						<div class="btn-group btn-group-sm">
							<button type="button" class="btn<?php echo (!$meta->description ? ' d-none' : null); ?>" id="description-btn" data-toggle="collapse" data-target="#description-collapse">
								<i class="mdi mdi-information-outline"></i>
							</button>
							<a href="<?php echo current_page(); ?>" class="btn --xhr --btn-refresh" id="refresh-btn" data-toggle="tooltip" title="<?php echo phrase('refresh'); ?>">
								<i class="mdi mdi-refresh"></i>
							</a>
							<button type="button" class="btn --expandable d-none d-sm-none d-md-none d-lg-block d-xl-block" data-toggle="tooltip" title="<?php echo phrase('expand'); ?>">
								<i class="mdi mdi-arrow-expand"></i>
							</button>
							<button type="button" class="btn --removable d-none d-sm-none d-md-none d-lg-block d-xl-block" data-toggle="tooltip" title="<?php echo phrase('remove'); ?>">
								<i class="mdi mdi-window-close"></i>
							</button>
						</div>
					</div>
				</div>
			</div>
			<div class="container-fluid border-bottom description-collapse collapse alias-description-collapse<?php echo ($meta->description ? ' show' : ''); ?>" id="description-collapse">
				<?php echo $meta->description; ?>
			</div>
			
			<div id="content-wrapper">
				
				<?php echo $template->content; ?>
				
			</div>
		
		</div>
		
		<?php echo aksara_footer(); ?>
	</body>
</html>