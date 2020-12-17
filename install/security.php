<?php
	session_start();
	
	if(file_exists('..' . DIRECTORY_SEPARATOR . 'config.php'))
	{
		header('Location: ../');
		exit;
	}
	
	error_reporting(0);
	
	header('Content-Type: application/json');
	
	if(!empty($_POST))
	{
		$_SESSION['database']						= array
		(
			'driver'								=> (isset($_POST['db_driver']) ? $_POST['db_driver'] : null),
			'hostname'								=> (isset($_POST['db_hostname']) ? $_POST['db_hostname'] : null),
			'port'									=> (isset($_POST['db_port']) ? $_POST['db_port'] : null),
			'username'								=> (isset($_POST['db_username']) ? $_POST['db_username'] : null),
			'password'								=> (isset($_POST['db_password']) ? $_POST['db_password'] : null),
			'initial'								=> (isset($_POST['db_initial']) ? $_POST['db_initial'] : null)
		);
	}
	
	if(!$_SESSION['database']['driver'] && !$_SESSION['database']['hostname'] && !$_SESSION['database']['username'] && !$_SESSION['database']['initial'])
	{
		echo json_encode
		(
			array
			(
				'status'							=> 403,
				'message'							=> 'Please fill all required fields!'
			)
		);
		
		exit;
	}
	
	$error											= false;
	
	if(in_array($_SESSION['database']['driver'], PDO::getAvailableDrivers()))
	{
		$dsn										= $_SESSION['database']['driver'] . ':host=' . $_SESSION['database']['hostname'] . ($_SESSION['database']['port'] ? (phpversion() <= 7.2 ? ':' : ',') . $_SESSION['database']['port'] : '') . ';dbname=' . $_SESSION['database']['initial'];
		
		try
		{
			$pdo									= new PDO($dsn, $_SESSION['database']['username'], $_SESSION['database']['password']);
			
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			
			$_SESSION['database']['dsn']			= $dsn;
		}
		catch(PDOException $e)
		{
			$error									= $e->getMessage();
		}
	}
	else
	{
		$error										= 'Please choose the correct database driver!';
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
		<form action="system.php" method="POST" class="--validate-form">
			<h4>
				Security Configuration
			</h4>
			<p>
				Enter your secret formula to secure your application
			</p>
			<hr class="row" />
			<div class="form-group">
				<label class="d-block mb-0">
					Encryption Hash
				</label>
				<input type="text" name="encryption" class="form-control form-control-sm" placeholder="Your encryption hash" value="' . (isset($_SESSION['security']['encryption']) ? $_SESSION['security']['encryption'] : null) . '" />
			</div>
			<div class="form-group">
				<label class="d-block mb-0">
					Cookie Prefix
				</label>
				<input type="text" name="cookie_prefix" class="form-control form-control-sm" placeholder="Unique cookie prefix to prevent session conflict" value="' . (isset($_SESSION['security']['cookie_prefix']) ? $_SESSION['security']['cookie_prefix'] : null) . '" />
			</div>
			<br/>
			<h4>
				Superuser
			</h4>
			<div class="row">
				<div class="col-md-6">
					<div class="form-group">
						<label class="d-block mb-0">
							First Name
						</label>
						<input type="text" name="first_name" class="form-control form-control-sm" placeholder="e.g: John" value="' . (isset($_SESSION['security']['first_name']) ? $_SESSION['security']['first_name'] : null) . '" />
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
						<label class="d-block mb-0">
							Last Name
						</label>
						<input type="text" name="last_name" class="form-control form-control-sm" placeholder="e.g: Doe" value="' . (isset($_SESSION['security']['last_name']) ? $_SESSION['security']['last_name'] : null) . '" />
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-6">
					<div class="form-group">
						<label class="d-block mb-0">
							Email Address
						</label>
						<input type="email" name="email" class="form-control form-control-sm" placeholder="e.g: johndoe@example.com" value="' . (isset($_SESSION['security']['email']) ? $_SESSION['security']['email'] : null) . '" />
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
						<label class="d-block mb-0">
							Username
						</label>
						<input type="text" name="username" class="form-control form-control-sm" placeholder="Create a username for superuser" value="' . (isset($_SESSION['security']['username']) ? $_SESSION['security']['username'] : null) . '" />
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-6">
					<div class="form-group">
						<label class="d-block mb-0">
							Password
						</label>
						<input type="password" name="password" class="form-control form-control-sm" placeholder="Password for superuser" />
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
						<label class="d-block mb-0">
							Confirm Password
						</label>
						<input type="password" name="confirm_password" class="form-control form-control-sm" placeholder="Retype password for superuser" />
					</div>
				</div>
			</div>
			<hr class="row" />
			<div class="row">
				<div class="col-sm-6">
					<a href="database.php" class="btn btn-light btn-block --xhr">
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
			'active'								=> '.security',
			'passed'								=> '.requirement, .database',
			'html'									=> $html
		)
	);