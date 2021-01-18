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
		$_SESSION['agree']							= (isset($_POST['agree']) ? $_POST['agree'] : 0);
	}
	
	if(!$_SESSION['agree'])
	{
		echo json_encode
		(
			array
			(
				'status'							=> 403,
				'message'							=> 'You just need to pretend to agree, dude!'
			)
		);
		
		exit;
	}
	
	$error											= false;
	$extension										= array_map('strtolower', get_loaded_extensions());
	$mod_rewrite									= ((isset($_SERVER['HTTP_MOD_REWRITE']) && strtolower($_SERVER['HTTP_MOD_REWRITE']) == 'on') || (function_exists('apache_get_modules') && in_array('mod_rewrite', apache_get_modules())) ? true : false);
	
	if(phpversion() < 7 || !$mod_rewrite || !in_array('mbstring', $extension) || !in_array('pdo', $extension) || !in_array('gd', $extension) || !in_array('json', $extension) || !in_array('xml', $extension))
	{
		$error										= true;
	}
	
	$html											= '
		<form action="database.php" method="POST" class="--validate-form">
			<h4>
				Awesome!
			</h4>
			<p>
				You just read my notes and pretend to agree with it :)
			</p>
			<hr class="row" />
			<p>
				I will help you to prepare your application using this installation wizard. Before you go, make sure this pre-requirements are fulfilled without any warning, otherwise your application will not work properly.
			</p>
			<hr class="row" />
			<div class="row">
				<div class="col-md-6">
					<div class="form-group">
						<label class="d-block mb-0">
							PHP Version
						</label>
						<p>
							' . (phpversion() < 7 ? '<b class="text-danger">' . phpversion() . '</b>, minimum version required is <b>7.1</b>' : '<b class="text-success">' . phpversion() . '</b>') . '
						</p>
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
						<label class="d-block mb-0">
							Rewrite Module
						</label>
						<p>
							' . (!$mod_rewrite ? '<b class="text-danger">Off</b>, turn it on!</b>' : '<b class="text-success">On</b>') . '
						</p>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-6">
					<div class="form-group">
						<label class="d-block mb-0">
							PDO
						</label>
						<p>
							' . (!in_array('pdo', $extension) ? '<b class="text-danger">Off</b>, turn it on!</b>' : '<b class="text-success">On</b>') . '
						</p>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-6">
					<div class="form-group">
						<label class="d-block mb-0">
							Multibyte String (mbstring)
						</label>
						<p>
							' . (!in_array('mbstring', $extension) ? '<b class="text-danger">Off</b>, turn it on!</b>' : '<b class="text-success">On</b>') . '
						</p>
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
						<label class="d-block mb-0">
							PHP GD
						</label>
						<p>
							' . (!in_array('gd', $extension) ? '<b class="text-danger">Off</b>, turn it on!</b>' : '<b class="text-success">On</b>') . '
						</p>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-6">
					<div class="form-group">
						<label class="d-block mb-0">
							JSON
						</label>
						<p>
							' . (!in_array('json', $extension) ? '<b class="text-danger">Off</b>, turn it on!</b>' : '<b class="text-success">On</b>') . '
						</p>
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
						<label class="d-block mb-0">
							XML
						</label>
						<p>
							' . (!in_array('xml', $extension) ? '<b class="text-danger">Off</b>, turn it on!</b>' : '<b class="text-success">On</b>') . '
						</p>
					</div>
				</div>
			</div>
			' . ($error ? '<div class="alert alert-warning failure"><b>Whoops!</b> Some requirement aren\'t yet fulfilled! Please update your server configuration, refresh this page and try again...</div>' : null) . '
			<hr class="row" />
			<div class="row">
				<div class="col-md-6">
					' . ($error ? '<a href="requirement.php" class="btn btn-light btn-block --xhr">Refresh</a>' : '&nbsp;') . '
				</div>
				<div class="col-md-6 text-right">
					<button type="submit" class="btn btn-primary btn-block"' . ($error ? ' disabled' : null) . '>
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
			'active'								=> '.requirement',
			'passed'								=> '.none',
			'html'									=> $html
		)
	);