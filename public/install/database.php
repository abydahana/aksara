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
	
	//$available_driver								= PDO::getAvailableDrivers();
	$available_driver								= array('MySQLi', 'Postgre', 'SQLSRV', 'SQLite3');
	$driver											= null;
	
	foreach($available_driver as $key => $val)
	{
		$driver										.= '<option value="' . $val . '"' . (isset($_SESSION['database']['driver']) && $val == $_SESSION['database']['driver'] ? ' selected' : null) . '>' . $val . '</option>';
	}
	
	$html											= '
		<form action="security.php" method="POST" class="--validate-form">
			<h4>
				' . phrase('database_configuration') . '
			</h4>
			<p>
				' . phrase('fill_the_requested_fields_below_with_your_database_connection') . '
			</p>
			<hr class="row" />
			<div class="row">
				<div class="col-md-6">
					<div class="form-group">
						<label class="d-block mb-0">
							' . phrase('database_driver') . '
							<b class="text-danger">*</b>
						</label>
						<select name="db_driver" class="form-control form-control-sm">
							' . $driver . '
						</select>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-8">
					<div class="form-group">
						<label class="d-block mb-0">
							' . phrase('hostname') . '
							<b class="text-danger">*</b>
						</label>
						<input type="text" name="db_hostname" class="form-control form-control-sm" placeholder="e.g: localhost" value="' . (isset($_SESSION['database']['hostname']) ? $_SESSION['database']['hostname'] : null) . '" />
					</div>
				</div>
				<div class="col-md-4">
					<div class="form-group">
						<label class="d-block mb-0">
							' . phrase('port') . '
						</label>
						<input type="number" name="db_port" class="form-control form-control-sm" placeholder="e.g: 3306" value="' . (isset($_SESSION['database']['port']) ? $_SESSION['database']['port'] : null) . '" />
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-6">
					<div class="form-group">
						<label class="d-block mb-0">
							' . phrase('username') . '
							<b class="text-danger">*</b>
						</label>
						<input type="text" name="db_username" class="form-control form-control-sm" placeholder="e.g: root" value="' . (isset($_SESSION['database']['username']) ? $_SESSION['database']['username'] : null) . '" />
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
						<label class="d-block mb-0">
							' . phrase('password') . '
						</label>
						<input type="password" name="db_password" class="form-control form-control-sm" value="' . (isset($_SESSION['database']['password']) ? $_SESSION['database']['password'] : null) . '" />
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<div class="form-group">
						<label class="d-block mb-0">
							' . phrase('initial_database') . '
							<b class="text-danger">*</b>
						</label>
						<input type="text" name="db_initial" class="form-control form-control-sm" value="' . (isset($_SESSION['database']['initial']) ? $_SESSION['database']['initial'] : null) . '" />
					</div>
				</div>
			</div>
			<hr class="row" />
			<div class="row">
				<div class="col-sm-6">
					<a href="requirement.php" class="btn btn-light btn-block --xhr">
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
			'active'								=> '.database',
			'passed'								=> '.requirement',
			'html'									=> $html
		)
	);
	