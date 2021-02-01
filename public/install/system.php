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
		$_SESSION['security']						= array
		(
			'encryption'							=> (isset($_POST['encryption']) ? $_POST['encryption'] : null),
			'cookie_name'							=> (isset($_POST['cookie_name']) ? $_POST['cookie_name'] : null),
			'first_name'							=> (isset($_POST['first_name']) ? $_POST['first_name'] : null),
			'last_name'								=> (isset($_POST['last_name']) ? $_POST['last_name'] : null),
			'email'									=> (isset($_POST['email']) && filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) ? $_POST['email'] : null),
			'username'								=> (isset($_POST['username']) && preg_match('/^[A-Za-z][A-Za-z0-9]{4,31}$/', $_POST['username']) ? $_POST['username'] : null),
			'password'								=> (isset($_POST['password']) ? $_POST['password'] : null),
			'confirm_password'						=> (isset($_POST['confirm_password']) ? $_POST['confirm_password'] : null)
		);
	}
	
	$error											= false;
	
	if(!$_SESSION['security']['encryption'])
	{
		$error										= phrase('the_encryption_key_cannot_be_empty');
	}
	elseif(!$_SESSION['security']['cookie_name'])
	{
		$error										= phrase('the_cookie_name_cannot_be_empty');
	}
	elseif(!$_SESSION['security']['first_name'])
	{
		$error										= phrase('the_first_name_cannot_be_empty');
	}
	elseif(!$_SESSION['security']['email'])
	{
		$error										= phrase('please_enter_your_valid_email_address');
	}
	elseif(!$_SESSION['security']['username'])
	{
		$error										= phrase('the_username_must_be_in_alphanumeric_only');
	}
	elseif(!$_SESSION['security']['password'])
	{
		$error										= phrase('the_password_cannot_be_empty');
	}
	elseif($_SESSION['security']['password'] !== $_SESSION['security']['confirm_password'])
	{
		$error										= phrase('the_password_confirmation_did_not_match');
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
	
	$timezone										= null;
	
	foreach(DateTimeZone::listIdentifiers(DateTimeZone::ALL) as $key => $val)
	{
		$timezone									.= '<option value="' . $val . '"' . (isset($_SESSION['system']['timezone']) && $val == $_SESSION['system']['timezone'] ? ' selected' : null) . '>' . str_replace('_', ' ', $val) . '</option>';
	}
	
	$html											= '
		<form action="finalizing.php" method="POST" class="--validate-form">
			<h4>
				' . phrase('system_configuration') . '
			</h4>
			<p>
				' . phrase('enter_the_basic_system_configuration') . '
				' . phrase('you_will_able_to_change_it_after_installation') . '
			</p>
			<hr class="row" />
			<div class="row">
				<div class="col-md-6">
					<div class="form-group">
						<label class="d-block mb-0">
							' . phrase('installation_mode') . '
							<b class="text-danger">*</b>
						</label>
						<select name="mode" class="form-control form-control-sm">
							<option value="0"' . (isset($_SESSION['system']['mode']) && !$_SESSION['system']['mode'] ? ' selected' : null) . '>' . phrase('basic_with_sample') . '</option>
							<option value="1"' . (isset($_SESSION['system']['mode']) && $_SESSION['system']['mode'] ? ' selected' : null) . '>' . phrase('developer_without_sample') . '</option>
						</select>
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
						<label class="d-block mb-0">
							' . phrase('timezone') . '
							<b class="text-danger">*</b>
						</label>
						<select name="timezone" class="form-control form-control-sm">
							' . $timezone . '
						</select>
					</div>
				</div>
			</div>
			<br />
			<h5>
				' . phrase('site_settings') . '
			</h5>
			<div class="form-group">
				<label class="d-block mb-0">
					' . phrase('site_title') . '
					<b class="text-danger">*</b>
				</label>
				<input type="text" name="site_title" class="form-control form-control-sm" placeholder="' . phrase('enter_the_site_title') . '" value="' . (isset($_SESSION['system']['site_title']) ? $_SESSION['system']['site_title'] : null) . '" />
			</div>
			<div class="form-group">
				<label class="d-block mb-0">
					' . phrase('site_description') . '
					<b class="text-danger">*</b>
				</label>
				<textarea name="site_description" class="form-control form-control-sm" placeholder="' . phrase('enter_the_site_description') . '">' . (isset($_SESSION['system']['site_description']) ? $_SESSION['system']['site_description'] : null) . '</textarea>
			</div>
			<br/>
			<h5>
				' . phrase('upload_setting') . '
			</h5>
			<div class="row">
				<div class="col-md-6">
					<div class="form-group">
						<label class="d-block mb-0">
							' . phrase('allowed_file_extension') . '
							<b class="text-danger">*</b>
						</label>
						<input type="text" name="file_extension" class="form-control form-control-sm" placeholder="' . phrase('separate_with_comma') . '" value="' . (isset($_SESSION['system']['file_extension']) ? $_SESSION['system']['file_extension'] : 'jpg,jpeg,gif,png,pdf,xls,xlsx,doc,docx,csv') . '" />
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
						<label class="d-block mb-0">
							' . phrase('allowed_image_extension') . '
							<b class="text-danger">*</b>
						</label>
						<input type="text" name="image_extension" class="form-control form-control-sm" placeholder="' . phrase('separate_with_comma') . '" value="' . (isset($_SESSION['system']['image_extension']) ? $_SESSION['system']['image_extension'] : 'jpg,jpeg,gif,png') . '" />
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-6">
					<div class="form-group">
						<label class="d-block mb-0">
							' . phrase('maximum_upload_size') . ' (Kb)
							<b class="text-danger">*</b>
						</label>
						<input type="text" name="max_upload_size" class="form-control form-control-sm" placeholder="e.g: 2048" value="' . (isset($_SESSION['system']['max_upload_size']) ? $_SESSION['system']['max_upload_size'] : '2048') . '" />
					</div>
				</div>
			</div>
			<br/>
			<h5>
				' . phrase('image_width_dimension') . ' (px)
			</h5>
			<div class="row">
				<div class="col-md-4">
					<div class="form-group">
						<label class="d-block mb-0">
							' . phrase('original') . '
							<b class="text-danger">*</b>
						</label>
						<input type="text" name="image_dimension" class="form-control form-control-sm" placeholder="in pixel" value="' . (isset($_SESSION['system']['image_dimension']) ? $_SESSION['system']['image_dimension'] : '1024') . '" />
					</div>
				</div>
				<div class="col-md-4">
					<div class="form-group">
						<label class="d-block mb-0">
							' . phrase('thumbnail') . '
							<b class="text-danger">*</b>
						</label>
						<input type="text" name="thumbnail_dimension" class="form-control form-control-sm" placeholder="in pixel" value="' . (isset($_SESSION['system']['thumbnail_dimension']) ? $_SESSION['system']['thumbnail_dimension'] : '256') . '" />
					</div>
				</div>
				<div class="col-md-4">
					<div class="form-group">
						<label class="d-block mb-0">
							' . phrase('icon') . '
							<b class="text-danger">*</b>
						</label>
						<input type="text" name="icon_dimension" class="form-control form-control-sm" placeholder="in pixel" value="' . (isset($_SESSION['system']['icon_dimension']) ? $_SESSION['system']['icon_dimension'] : '64') . '" />
					</div>
				</div>
			</div>
			<hr class="row" />
			<div class="row">
				<div class="col-sm-6">
					<a href="security.php" class="btn btn-light btn-block --xhr">
						' . phrase('back') . '
					</a>
				</div>
				<div class="col-sm-6 text-right">
					<button type="submit" class="btn btn-primary btn-block">
						' . phrase('continue') . '
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
			'active'								=> '.system',
			'passed'								=> '.requirement, .database, .security',
			'html'									=> $html
		)
	);
	