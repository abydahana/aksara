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
		
		<?php echo asset_loader('css/styles.css'); ?>
		
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
		
		<?php require_once('navigation.php'); ?>
		
		<div id="content-wrapper">
		
			<?php echo $template->content; ?>
			
		</div>
		
		<div id="footer-wrapper">
			
			<?php require_once('footer.php'); ?>
			
		</div>
		
		<?php echo aksara_footer(); ?>
		
		<?php echo asset_loader('js/scripts.js'); ?>
	</body>
</html>