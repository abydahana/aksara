<!DOCTYPE html>
<html lang="en"<?php echo (in_array(get_userdata('language'), array('arabic')) ? ' dir="rtl"' : null); ?>>
	<head>
		<meta charset="UTF-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge" />
		<meta name="msapplication-navbutton-color" content="#007bff" />
		<meta name="theme-color" content="#007bff" />
		<meta name="apple-mobile-web-app-status-bar-style" content="#007bff" />
		<meta name="apple-mobile-web-app-capable" content="yes" />
		<meta name="viewport" content="user-scalable=no, width=device-width, height=device-height, initial-scale=1, maximum-scale=1" />
		<meta name="description" content="<?php echo ($template['description'] ? strip_tags($template['description']) : get_setting('app_description')); ?>" />
		<meta name="_token" content="<?php echo $_token; ?>" />
		<title>
			<?php echo ($template['title'] ? $template['title'] . ' | ' : null); ?><?php echo get_setting('app_name'); ?>
		</title>
		<link rel="icon" type="image/x-icon" href="<?php echo get_image('settings', get_setting('app_icon'), 'icon'); ?>" />
		<link rel="stylesheet" type="text/css" href="<?php echo asset_url('local/css/styles.css'); ?>" />
		<link rel="stylesheet" type="text/css" href="<?php echo asset_url('materialdesignicons/css/materialdesignicons.min.css'); ?>" />
		<link rel="stylesheet" type="text/css" href="<?php echo base_theme('css/styles.css'); ?>" />
		
		<script type="text/javascript">(function(w,d,u){w.readyQ=[];w.bindReadyQ=[];function p(x,y){if(x=="ready"){w.bindReadyQ.push(y)}else{w.readyQ.push(x)}};var a={ready:p,bind:p};w.$=w.jQuery=function(f){if(f===d||f===u){return a}else{p(f)}}})(window,document)</script>
		
		<!-- Facebook Open Graph Tag -->
		<meta property="og:url" content="<?php echo current_page(); ?>" />
		<meta property="og:type" content="website" />
		<meta property="og:title" content="<?php echo ($template['title'] ? $template['title'] . ' | ' : null); ?><?php echo get_setting('app_name'); ?>" />
		<meta property="og:description" content="<?php echo ($template['description'] ? strip_tags($template['description']) : get_setting('app_description')); ?>" />
		<meta property="og:image" content="<?php echo get_og_image($template['body']); ?>" />
		<meta property="fb:app_id" content="<?php echo get_setting('facebook_app_id'); ?>" />
		<!-- End Facebook Open Graph Tag -->
	</head>
	<body>
		<?php if(get_setting('facebook_app_id')) { ?>
		<div id="fb-root"></div>
		<script async defer crossorigin="anonymous" src="https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v7.0&appId=<?php echo get_setting('facebook_app_id'); ?>&autoLogAppEvents=1" nonce="WmgLymHX"></script>
		<?php } ?>
		
		<?php require_once('navigation.php'); ?>
		
		<div id="content-wrapper">
		
			<?php echo $template['body']; ?>
			
		</div>
		
		<div id="footer-wrapper">
			
			<?php require_once('footer.php'); ?>
			
		</div>
		
		<?php echo get_announcements(); /* from global_helper */ ?>
		
		<?php echo show_flashdata(); /* from global_helper */ ?>
		
		<?php if(get_setting('google_analytics_key')) { ?>
		<!-- Global site tag (gtag.js) - Google Analytics -->
		<script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo get_setting('google_analytics_key'); ?>"></script>
		<script>
			window.dataLayer						= window.dataLayer || [];
			function gtag()
			{
				dataLayer.push(arguments)
			}
			gtag('js', new Date()),
			gtag('config', '<?php echo get_setting('google_analytics_key'); ?>')
		</script>
		<?php } ?>
		<script type="text/javascript" src="<?php echo asset_url('local/js/scripts.js'); ?>"></script>
		<script type="text/javascript">(function($,d){$.each(readyQ,function(i,f){$(f)});$.each(bindReadyQ,function(i,f){$(d).bind("ready",f)})})(jQuery,document)</script>
		
	</body>
</html>