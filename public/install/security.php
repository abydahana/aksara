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
	
	//error_reporting(0);
	
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
	
	if(!$_SESSION['database']['driver'] || !$_SESSION['database']['hostname'] || !$_SESSION['database']['username'] || !$_SESSION['database']['initial'])
	{
		echo json_encode
		(
			array
			(
				'status'							=> 403,
				'message'							=> phrase('please_fill_all_required_fields')
			)
		);
		
		exit;
	}
	
	//$available_driver								= PDO::getAvailableDrivers();
	$available_driver								= array('MySQLi', 'Postgre', 'SQLSRV', 'SQLite3');
	$error											= false;
	
	if(in_array($_SESSION['database']['driver'], $available_driver))
	{
		$dsn										= $_SESSION['database']['driver'] . ':host=' . $_SESSION['database']['hostname'] . ($_SESSION['database']['port'] ? ',' . $_SESSION['database']['port'] : '') . ';dbname=' . $_SESSION['database']['initial'];
		
		if('MySQLi' == $_SESSION['database']['driver'])
		{
			/**
			 * Connect through MySQLi Driver
			 */
			$connection								= @mysqli_connect($_SESSION['database']['hostname'], $_SESSION['database']['username'], $_SESSION['database']['password'], $_SESSION['database']['initial'], (is_int($_SESSION['database']['port']) ? $_SESSION['database']['port'] : 3306));
			
			if(mysqli_connect_errno())
			{
				$error								= mysqli_connect_error();
			}
		}
		elseif('Postgre' == $_SESSION['database']['driver'])
		{
			/**
			 * Connect through Postgre Driver
			 */
			if(function_exists('pg_connect'))
			{
				$connection							= pg_connect('host=' . $_SESSION['database']['hostname'] . ' port=' . (is_int($_SESSION['database']['port']) ? $_SESSION['database']['port'] : 5432) . ' user=' . $_SESSION['database']['username'] . ' password=' . $_SESSION['database']['password'] . ' dbname=' . $_SESSION['database']['initial']);
				
				if(!$connection)
				{
					$error							= error_get_last();
					$error							= $error['message'];
				}
			}
			else
			{
				$error								= phrase('your_server_do_not_have_postgresql_driver_installed');
			}
		}
		elseif('SQLSRV' == $_SESSION['database']['driver'])
		{
			/**
			 * Connect through SQLSRV Driver
			 */
			if(function_exists('sqlsrv_connect'))
			{
				$connection							= sqlsrv_connect($_SESSION['database']['hostname'] . ($$_SESSION['database']['port'] ? ',' . $_SESSION['database']['port'] : null), array('UID' => $_SESSION['database']['username'], 'Password' => $_SESSION['database']['password'], 'Database' => $_SESSION['database']['initial']));
				
				if(!$connection)
				{
					$error							= sqlsrv_errors();
					$error							= $error['message'];
				}
			}
			else
			{
				$error								= phrase('your_server_do_not_have_sqlsrv_driver_installed');
			}
		}
		elseif('SQLite3' == $_SESSION['database']['driver'])
		{
			/**
			 * Connect through SQLSRV Driver
			 */
			if(class_exists('SQLite3'))
			{
				$connection							= new SQLite3($_SESSION['database']['hostname']);
				
				if(!$connection)
				{
					$error							= $connection->lastErrorMsg();
				}
			}
			else
			{
				$error								= phrase('your_server_do_not_have_sqlite3_driver_installed');
			}
		}
	}
	else
	{
		$error										= phrase('please_choose_the_correct_database_driver');
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
				' . phrase('security_configuration') . '
			</h4>
			<p>
				' . phrase('enter_your_secret_formula_to_secure_your_application') . '
			</p>
			<hr class="row" />
			<div class="form-group">
				<label class="d-block mb-0">
					' . phrase('encryption_key') . '
					<b class="text-danger">*</b>
				</label>
				<input type="text" name="encryption" class="form-control form-control-sm" placeholder="' . phrase('your_encryption_key') . '" value="' . (isset($_SESSION['security']['encryption']) ? $_SESSION['security']['encryption'] : random_string(64, true)) . '" />
			</div>
			<div class="form-group">
				<label class="d-block mb-0">
					' . phrase('cookie_name') . '
					<b class="text-danger">*</b>
				</label>
				<input type="text" name="cookie_name" class="form-control form-control-sm" placeholder="' . phrase('unique_cookie_name_to_prevent_conflict') . '" value="' . (isset($_SESSION['security']['cookie_name']) ? $_SESSION['security']['cookie_name'] : 'aksara_' . random_string(16)) . '" />
			</div>
			<br/>
			<h5>
				' . phrase('superuser') . '
			</h5>
			<div class="row">
				<div class="col-md-6">
					<div class="form-group">
						<label class="d-block mb-0">
							' . phrase('first_name') . '
							<b class="text-danger">*</b>
						</label>
						<input type="text" name="first_name" class="form-control form-control-sm" placeholder="e.g: John" value="' . (isset($_SESSION['security']['first_name']) ? $_SESSION['security']['first_name'] : null) . '" />
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
						<label class="d-block mb-0">
							' . phrase('last_name') . '
						</label>
						<input type="text" name="last_name" class="form-control form-control-sm" placeholder="e.g: Doe" value="' . (isset($_SESSION['security']['last_name']) ? $_SESSION['security']['last_name'] : null) . '" />
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-6">
					<div class="form-group">
						<label class="d-block mb-0">
							' . phrase('email_address') . '
							<b class="text-danger">*</b>
						</label>
						<input type="email" name="email" class="form-control form-control-sm" placeholder="e.g: johndoe@example.com" value="' . (isset($_SESSION['security']['email']) ? $_SESSION['security']['email'] : null) . '" />
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
						<label class="d-block mb-0">
							' . phrase('username') . '
							<b class="text-danger">*</b>
						</label>
						<input type="text" name="username" class="form-control form-control-sm" placeholder="' . phrase('create_username_for_superuser') . '" value="' . (isset($_SESSION['security']['username']) ? $_SESSION['security']['username'] : null) . '" />
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-6">
					<div class="form-group">
						<label class="d-block mb-0">
							' . phrase('password') . '
							<b class="text-danger">*</b>
						</label>
						<input type="password" name="password" class="form-control form-control-sm" placeholder="' . phrase('password_for_superuser') . '" />
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
						<label class="d-block mb-0">
							' . phrase('confirm_password') . '
							<b class="text-danger">*</b>
						</label>
						<input type="password" name="confirm_password" class="form-control form-control-sm" placeholder="' . phrase('retype_the_password') . '" />
					</div>
				</div>
			</div>
			<hr class="row" />
			<div class="row">
				<div class="col-sm-6">
					<a href="database.php" class="btn btn-light btn-block --xhr">
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
			'active'								=> '.security',
			'passed'								=> '.requirement, .database',
			'html'									=> $html
		)
	);
	