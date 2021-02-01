<?php
	session_start();
	
	require_once 'includes/function.php';
	
	if(!is_dir(dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'vendor'))
	{
		exit(phrase('please_run') . ' "<code>composer install</code>" ' . phrase('from') . ' "<code>' . dirname(dirname(__DIR__)) . '</code>" ' . phrase('to_fetch_the_required_repository_before_we_start_the_installation_wizard'));
	}
	
	elseif(file_exists(dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'config.php'))
	{
		header('Location: ../');
		exit;
	}
	
	header('Content-Type: application/json');
	
	if(!empty($_POST))
	{
		$_SESSION['system']							= array
		(
			'mode'									=> (isset($_POST['mode']) ? $_POST['mode'] : null),
			'timezone'								=> (isset($_POST['timezone']) ? $_POST['timezone'] : null),
			'site_title'							=> (isset($_POST['site_title']) ? $_POST['site_title'] : null),
			'site_description'						=> (isset($_POST['site_description']) ? $_POST['site_description'] : null),
			'file_extension'						=> (isset($_POST['file_extension']) && preg_match('/^[a-zA-Z0-9\,]{2,}$/', $_POST['file_extension']) ? $_POST['file_extension'] : null),
			'image_extension'						=> (isset($_POST['image_extension']) && preg_match('/^[a-zA-Z0-9\,]{2,}$/', $_POST['image_extension']) ? $_POST['image_extension'] : null),
			'max_upload_size'						=> (isset($_POST['max_upload_size']) && is_numeric($_POST['max_upload_size']) ? $_POST['max_upload_size'] : null),
			'image_dimension'						=> (isset($_POST['image_dimension']) && is_numeric($_POST['image_dimension']) ? $_POST['image_dimension'] : null),
			'thumbnail_dimension'					=> (isset($_POST['thumbnail_dimension']) && is_numeric($_POST['thumbnail_dimension']) ? $_POST['thumbnail_dimension'] : null),
			'icon_dimension'						=> (isset($_POST['icon_dimension']) && is_numeric($_POST['icon_dimension']) ? $_POST['icon_dimension'] : null)
		);
	}
	
	$error											= false;
	
	if(!in_array($_SESSION['system']['timezone'], DateTimeZone::listIdentifiers(DateTimeZone::ALL)))
	{
		$error										= phrase('the_timezone_is_not_valid');
	}
	elseif(!$_SESSION['system']['site_title'])
	{
		$error										= phrase('the_site_title_cannot_be_empty');
	}
	elseif(!$_SESSION['system']['site_description'])
	{
		$error										= phrase('the_site_description_cannot_be_empty');
	}
	elseif(!$_SESSION['system']['file_extension'])
	{
		$error										= phrase('file_extension_must_be_comma_separated_alphanumeric_only');
	}
	elseif(!$_SESSION['system']['image_extension'])
	{
		$error										= phrase('image_extension_must_be_comma_separated_alphanumeric_only');
	}
	elseif(!$_SESSION['system']['max_upload_size'])
	{
		$error										= phrase('maximum_upload_size_must_be_numeric_only');
	}
	elseif(!$_SESSION['system']['image_dimension'])
	{
		$error										= phrase('image_dimension_must_be_numeric_only');
	}
	elseif(!$_SESSION['system']['thumbnail_dimension'])
	{
		$error										= phrase('thumbnail_dimension_must_be_numeric_only');
	}
	elseif(!$_SESSION['system']['icon_dimension'])
	{
		$error										= phrase('icon_dimension_must_be_numeric_only');
	}
	
	if($error)
	{
		echo json_encode
		(
			array
			(
				'status'							=> 403,
				'message'							=> $error
			)
		);
		
		exit;
	}
	
	$html											= '
		<form action="install.php" method="POST" class="--validate-form">
			<h4>
				' . phrase('all_catched_up') . '
			</h4>
			<p>
				' . phrase('your_application_is_ready_to_install_with_provided_settings') . '
			</p>
			<hr class="row" />
			<p>
				' . phrase('just_one_more_step') . '
			</p>
			<p>
				' . phrase('make_sure_what_you_filled_in_on_the_previous_form_is_correct') . '
				' . phrase('once_you_have_successfully_run_the_installer_there_is_no_more_back_button') . '
			</p>
			<p>
				' . phrase('click_run_installer_to_applying_your_configuration') . '
			</p>
			<hr class="row" />
			<div class="row">
				<div class="col-sm-6">
					<a href="system.php" class="btn btn-light btn-block --xhr">
						' . phrase('back') . '
					</a>
				</div>
				<div class="col-sm-6 text-right">
					<button type="submit" class="btn btn-primary btn-block">
						' . phrase('run_installer') . '
					</button>
				</div>
			</div>
		</form>
	';
	
	echo json_encode
	(
		array
		(
			'status'								=> 200,
			'active'								=> '.final',
			'passed'								=> '.requirement, .database, .security, .system',
			'html'									=> $html
		)
	);
	