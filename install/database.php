<?php
	session_start();
	
	if(file_exists('..' . DIRECTORY_SEPARATOR . 'config.php'))
	{
		header('Location: ../');
		exit;
	}
	
	header('Content-Type: application/json');
	
	$driver											= null;
	
	foreach(PDO::getAvailableDrivers() as $key => $val)
	{
		$driver										.= '<option value="' . $val . '"' . (isset($_SESSION['database']['driver']) && $val == $_SESSION['database']['driver'] ? ' selected' : null) . '>' . strtoupper($val) . '</option>';
	}
	
	$html											= '
		<form action="security.php" method="POST" class="--validate-form">
			<h4>
				Database Configuration
			</h4>
			<p>
				Fill the requested fields below with your database connection
			</p>
			<hr class="row" />
			<div class="row">
				<div class="col-md-6">
					<div class="form-group">
						<label class="d-block mb-0">
							Database Driver
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
							Hostname
						</label>
						<input type="text" name="db_hostname" class="form-control form-control-sm" placeholder="e.g: localhost" value="' . (isset($_SESSION['database']['hostname']) ? $_SESSION['database']['hostname'] : null) . '" />
					</div>
				</div>
				<div class="col-md-4">
					<div class="form-group">
						<label class="d-block mb-0">
							Port
						</label>
						<input type="number" name="db_port" class="form-control form-control-sm" placeholder="e.g: 3316" value="' . (isset($_SESSION['database']['port']) ? $_SESSION['database']['port'] : null) . '" />
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-6">
					<div class="form-group">
						<label class="d-block mb-0">
							Username
						</label>
						<input type="text" name="db_username" class="form-control form-control-sm" placeholder="e.g: root" value="' . (isset($_SESSION['database']['username']) ? $_SESSION['database']['username'] : null) . '" />
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
						<label class="d-block mb-0">
							Password
						</label>
						<input type="password" name="db_password" class="form-control form-control-sm" value="' . (isset($_SESSION['database']['password']) ? $_SESSION['database']['password'] : null) . '" />
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<div class="form-group">
						<label class="d-block mb-0">
							Initial Database
						</label>
						<input type="text" name="db_initial" class="form-control form-control-sm" value="' . (isset($_SESSION['database']['initial']) ? $_SESSION['database']['initial'] : null) . '" />
					</div>
				</div>
			</div>
			<hr class="row" />
			<div class="row">
				<div class="col-sm-6">
					<a href="requirement.php" class="btn btn-light btn-block --xhr">
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
			'active'								=> '.database',
			'passed'								=> '.requirement',
			'html'									=> $html
		)
	);