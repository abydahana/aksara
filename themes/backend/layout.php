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
		
		<?php
			echo aksara_header();
			
			echo asset_loader
			(
				[
					'assets/bootstrap/css/bootstrap.min.css',
					'assets/local/css/styles.min.css'
				]
			);
		?>
		
		<link rel="preconnect" href="https://fonts.gstatic.com">
		<link href="https://fonts.googleapis.com/css2?family=Fira+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet">
		<style type="text/css">
			body
			{
				font-family: 'Fira Sans', sans-serif
			}
		</style>
	</head>
	<body>
		
		<?php include_once('header.php'); ?>
		
		<?php include_once('breadcrumb.php'); ?>
		
		<div id="sidebar-wrapper" class="aksara-sidebar sidebar-menu">
			<div class="p-3 user-bg-masking hide-on-collapse mb-4">
				<div class="row g-0 align-items-center">
					<div class="col-3 col-sm-2 col-lg-3">
						<a href="<?php echo base_url('user'); ?>">
							<img src="<?php echo get_image('users', get_userdata('photo'), 'icon'); ?>" class="img-fluid rounded" />
						</a>
					</div>
					<div class="col-9 col-sm-10 col-lg-9 ps-2">
						<a href="<?php echo base_url('user'); ?>">
							<h6 class="mb-0 text-break-word mb-0">
								<?php echo get_userdata('first_name') . ' ' . get_userdata('last_name'); ?>
							</h6>
						</a>
						<p class="text-sm mb-0">
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
							<a href="<?php echo base_url('xhr/partial/language'); ?>" class="btn btn-xs float-end --modal --force-xs">
								<i class="mdi mdi-translate"></i>
								<?php echo phrase('language'); ?>
								<i class="mdi mdi-chevron-down"></i>
							</a>
						</p>
					</div>
				</div>
			</div>
			
			<?php echo generate_menu($template->menu, 'nav flex-column', 'nav-item', 'nav-link --xhr', 'dropdown-toggle', 'data-toggle="expand-collapse"', '', 'list-unstyled flex-column collapse'); ?>
			
		</div>
		
		<div id="content-wrapper">
			<div class="container-fluid d-none d-md-none d-lg-block d-xl-block">
				<div class="row align-items-center alias-table-header border-bottom">
					<div class="col-8">
						<h5 class="text-truncate mb-0">
							<i class="<?php echo $meta->icon; ?>" role="icon"></i>
							<span role="title">
								<?php echo $meta->title; ?>
							</span>
						</h5>
					</div>
					<div class="col-4 text-end">
						<div class="btn-group btn-group-sm">
							<button type="button" class="btn<?php echo (!$meta->description ? ' d-none' : null); ?>" id="description-btn" data-bs-toggle="collapse" data-bs-target="#description-collapse">
								<i class="mdi mdi-information-outline"></i>
							</button>
							<a href="<?php echo current_page(); ?>" class="btn --xhr --btn-refresh" id="refresh-btn" data-bs-toggle="tooltip" title="<?php echo phrase('refresh'); ?>">
								<i class="mdi mdi-refresh"></i>
							</a>
							<button type="button" class="btn --expandable d-none d-sm-none d-md-none d-lg-block d-xl-block" data-bs-toggle="tooltip" title="<?php echo phrase('expand'); ?>">
								<i class="mdi mdi-arrow-expand"></i>
							</button>
							<button type="button" class="btn --removable d-none d-sm-none d-md-none d-lg-block d-xl-block" data-bs-toggle="tooltip" title="<?php echo phrase('remove'); ?>">
								<i class="mdi mdi-window-close"></i>
							</button>
						</div>
					</div>
				</div>
			</div>
			<div class="container-fluid border-bottom description-collapse collapse alias-description-collapse<?php echo ($meta->description ? ' show' : ''); ?>" id="description-collapse">
				<?php echo $meta->description; ?>
			</div>
			
			<div id="content-placeholder">
				
				<?php echo $template->content; ?>
				
			</div>
		
		</div>
		
		<?php
			echo aksara_footer();
			
			echo asset_loader
			(
				[
					'assets/bootstrap/js/bootstrap.bundle.min.js'
				]
			);
		?>
		
	</body>
</html>