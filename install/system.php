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
		$_SESSION['security']						= array
		(
			'encryption'							=> (isset($_POST['encryption']) ? $_POST['encryption'] : null),
			'cookie_prefix'							=> (isset($_POST['cookie_prefix']) ? $_POST['cookie_prefix'] : null),
			'first_name'							=> (isset($_POST['first_name']) ? $_POST['first_name'] : null),
			'last_name'								=> (isset($_POST['last_name']) ? $_POST['last_name'] : null),
			'email'									=> (isset($_POST['email']) ? $_POST['email'] : null),
			'username'								=> (isset($_POST['username']) ? $_POST['username'] : null),
			'password'								=> (isset($_POST['password']) ? $_POST['password'] : null),
			'confirm_password'						=> (isset($_POST['confirm_password']) ? $_POST['confirm_password'] : null)
		);
	}
	
	$error											= false;
	
	if(!$_SESSION['security']['encryption'])
	{
		$error										= 'The encryption hash cannot be empty!';
	}
	elseif(!$_SESSION['security']['cookie_prefix'])
	{
		$error										= 'The cookie prefix cannot be empty!';
	}
	elseif(!$_SESSION['security']['first_name'])
	{
		$error										= 'The first name cannot be empty!';
	}
	elseif(!$_SESSION['security']['email'])
	{
		$error										= 'The email cannot be empty!';
	}
	elseif(!$_SESSION['security']['username'])
	{
		$error										= 'The username cannot be empty!';
	}
	elseif(!$_SESSION['security']['password'])
	{
		$error										= 'The password cannot be empty!';
	}
	elseif($_SESSION['security']['password'] !== $_SESSION['security']['confirm_password'])
	{
		$error										= 'The confirmation password didn\'t match!';
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
		$timezone									.= '<option value="' . $val . '"' . (isset($_SESSION['system']['timezone']) && $val == $_SESSION['system']['timezone'] ? ' selected' : null) . '>' . $val . '</option>';
	}
	
	$html											= '
		<form action="finalizing.php" method="POST" class="--validate-form">
			<h4>
				System Configuration
			</h4>
			<p>
				Enter the basic sistem configuration. You will able to change it after installation.
			</p>
			<hr class="row" />
			<div class="row">
				<div class="col-md-6">
					<div class="form-group">
						<label class="d-block mb-0">
							Installation Mode
							<b class="text-danger">*</b>
						</label>
						<select name="mode" class="form-control form-control-sm">
							<option value="0"' . (isset($_SESSION['system']['mode']) && !$_SESSION['system']['mode'] ? ' selected' : null) . '>Basic (With Sample)</option>
							<option value="1"' . (isset($_SESSION['system']['mode']) && $_SESSION['system']['mode'] ? ' selected' : null) . '>Developer (Without Sample)</option>
						</select>
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
						<label class="d-block mb-0">
							Timezone
							<b class="text-danger">*</b>
						</label>
						<select name="timezone" class="form-control form-control-sm">
							' . $timezone . '
						</select>
					</div>
				</div>
			</div>
			<br />
			<h4>
				Site Settings
			</h4>
			<div class="form-group">
				<label class="d-block mb-0">
					Site Title
					<b class="text-danger">*</b>
				</label>
				<input type="text" name="site_title" class="form-control form-control-sm" placeholder="Enter the site title" value="' . (isset($_SESSION['system']['site_title']) ? $_SESSION['system']['site_title'] : null) . '" />
			</div>
			<div class="form-group">
				<label class="d-block mb-0">
					Site Description
					<b class="text-danger">*</b>
				</label>
				<textarea name="site_description" class="form-control form-control-sm" placeholder="Enter the site description">' . (isset($_SESSION['system']['site_description']) ? $_SESSION['system']['site_description'] : null) . '</textarea>
			</div>
			<br/>
			<h4>
				Upload
			</h4>
			<div class="row">
				<div class="col-md-6">
					<div class="form-group">
						<label class="d-block mb-0">
							Allowed File Extension
							<b class="text-danger">*</b>
						</label>
						<input type="text" name="file_extension" class="form-control form-control-sm" placeholder="Separate with vertical comma" value="' . (isset($_SESSION['system']['file_extension']) ? $_SESSION['system']['file_extension'] : 'jpg,jpeg,gif,png,pdf,xls,xlsx,doc,docx,csv') . '" />
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
						<label class="d-block mb-0">
							Allowed Image Extension
							<b class="text-danger">*</b>
						</label>
						<input type="text" name="image_extension" class="form-control form-control-sm" placeholder="Separate with comma" value="' . (isset($_SESSION['system']['image_extension']) ? $_SESSION['system']['image_extension'] : 'jpg,jpeg,gif,png') . '" />
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-6">
					<div class="form-group">
						<label class="d-block mb-0">
							Max Upload Size (in Kb)
							<b class="text-danger">*</b>
						</label>
						<input type="text" name="max_upload_size" class="form-control form-control-sm" placeholder="e.g: 2048" value="' . (isset($_SESSION['system']['max_upload_size']) ? $_SESSION['system']['max_upload_size'] : '2048') . '" />
					</div>
				</div>
			</div>
			<br/>
			<h4>
				Image Width Dimension (px)
			</h4>
			<div class="row">
				<div class="col-md-4">
					<div class="form-group">
						<label class="d-block mb-0">
							Original
							<b class="text-danger">*</b>
						</label>
						<input type="text" name="image_dimension" class="form-control form-control-sm" placeholder="in pixel" value="' . (isset($_SESSION['system']['image_dimension']) ? $_SESSION['system']['image_dimension'] : '1024') . '" />
					</div>
				</div>
				<div class="col-md-4">
					<div class="form-group">
						<label class="d-block mb-0">
							Thumbnail
							<b class="text-danger">*</b>
						</label>
						<input type="text" name="thumbnail_dimension" class="form-control form-control-sm" placeholder="in pixel" value="' . (isset($_SESSION['system']['thumbnail_dimension']) ? $_SESSION['system']['thumbnail_dimension'] : '250') . '" />
					</div>
				</div>
				<div class="col-md-4">
					<div class="form-group">
						<label class="d-block mb-0">
							Icon
							<b class="text-danger">*</b>
						</label>
						<input type="text" name="icon_dimension" class="form-control form-control-sm" placeholder="in pixel" value="' . (isset($_SESSION['system']['icon_dimension']) ? $_SESSION['system']['icon_dimension'] : '80') . '" />
					</div>
				</div>
			</div>
			<hr class="row" />
			<div class="row">
				<div class="col-sm-6">
					<a href="security.php" class="btn btn-light btn-block --xhr">
						Back
					</a>
				</div>
				<div class="col-sm-6 text-right">
					<button type="submit" class="btn btn-primary btn-block">
						Continue
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