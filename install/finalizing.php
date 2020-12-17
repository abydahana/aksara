<?php
	session_start();
	
	if(file_exists('..' . DIRECTORY_SEPARATOR . 'config.php'))
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
		$error										= 'The timezone isn\'t valid!';
	}
	elseif(!$_SESSION['system']['site_title'])
	{
		$error										= 'The site title cannot be empty!';
	}
	elseif(!$_SESSION['system']['site_description'])
	{
		$error										= 'The site description cannot be empty!';
	}
	elseif(!$_SESSION['system']['file_extension'])
	{
		$error										= 'File extension must be comma separated alphanumeric only!';
	}
	elseif(!$_SESSION['system']['image_extension'])
	{
		$error										= 'Image extension must be comma separated alphanumeric only!';
	}
	elseif(!$_SESSION['system']['max_upload_size'])
	{
		$error										= 'Max upload size must be numeric only!';
	}
	elseif(!$_SESSION['system']['image_dimension'])
	{
		$error										= 'Image dimension must be numeric only!';
	}
	elseif(!$_SESSION['system']['thumbnail_dimension'])
	{
		$error										= 'Thumbnail dimension must be numeric only!';
	}
	elseif(!$_SESSION['system']['icon_dimension'])
	{
		$error										= 'Icon dimension must be numeric only!';
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
				All Catched Up!
			</h4>
			<p>
				Your application is ready to install with provided settings.
			</p>
			<hr class="row" />
			<p>
				Just one more step,
			</p>
			<p>
				Make sure what you filled in on the previous form is correct. Once you have successfully run the installer, there is no more back button.
			</p>
			<p>
				Click "<b>Run Installer</b>" to applying your configuration.
			</p>
			<hr class="row" />
			<div class="row">
				<div class="col-sm-6">
					<a href="system.php" class="btn btn-light btn-block --xhr">
						Back
					</a>
				</div>
				<div class="col-sm-6 text-right">
					<button type="submit" class="btn btn-primary btn-block">
						Run Installer
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