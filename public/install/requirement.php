<?php
	session_start();
	
	require_once 'includes/function.php';
	
	if(version_compare(PHP_VERSION, '7.3', '<'))
	{
		exit(phrase('you_need_to_update_your_php_version'));
	}
	elseif(!is_dir(dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'vendor'))
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
		$_SESSION['agree']							= (isset($_POST['agree']) ? $_POST['agree'] : 0);
	}
	
	if(!$_SESSION['agree'])
	{
		echo json_encode
		(
			array
			(
				'status'							=> 403,
				'message'							=> phrase('you_just_need_to_pretend_to_agree')
			)
		);
		
		exit;
	}
	
	$error											= false;
	$extension										= array_map('strtolower', get_loaded_extensions());
	$mod_rewrite									= ((isset($_SERVER['HTTP_MOD_REWRITE']) && strtolower($_SERVER['HTTP_MOD_REWRITE']) == 'on') || (function_exists('apache_get_modules') && in_array('mod_rewrite', apache_get_modules())) ? true : false);
	
	if(phpversion() < 7 || !$mod_rewrite || !in_array('mbstring', $extension) || !in_array('intl', $extension) || !in_array('gd', $extension) || !in_array('json', $extension) || !in_array('xml', $extension))
	{
		$error										= true;
	}
	
	$html											= '
		<form action="database.php" method="POST" class="--validate-form">
			<h4>
				' . phrase('awesome') . '
			</h4>
			<p>
				' . phrase('you_just_read_our_notes_and_pretend_to_agree_with_it') . '
			</p>
			<hr class="row" />
			<p>
				' . phrase('we_will_help_you_to_prepare_your_application_using_this_installation_wizard') . '
				' . phrase('before_you_go_make_sure_this_pre_requirements_are_fulfilled_without_any_warning') . '
				' . phrase('otherwise_your_application_will_not_work_properly') . '
			</p>
			<hr class="row" />
			<div class="row">
				<div class="col-md-6">
					<div class="form-group">
						<label class="d-block mb-0">
							' . phrase('php_version') . '
						</label>
						<p>
							' . (phpversion() < 7.3 ? '<b class="text-danger">' . phpversion() . '</b>, ' . phrase('the_minimum_required_version_is') . ' <b>7.3</b>' : '<b class="text-success">' . phpversion() . '</b>') . '
						</p>
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
						<label class="d-block mb-0">
							' . phrase('rewrite_module') . '
						</label>
						<p>
							' . (!$mod_rewrite ? '<b class="text-danger">' . phrase('off') . '</b>, ' . phrase('turn_it_on') : '<b class="text-success">' . phrase('on') . '</b>') . '
						</p>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-6">
					<div class="form-group">
						<label class="d-block mb-0">
							' . phrase('internationalization') . ' (intl)
						</label>
						<p>
							' . (!in_array('intl', $extension) ? '<b class="text-danger">' . phrase('off') . '</b>, ' . phrase('turn_it_on') : '<b class="text-success">' . phrase('on') . '</b>') . '
						</p>
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
						<label class="d-block mb-0">
							' . phrase('zip_archiver') . '
						</label>
						<p>
							' . (!class_exists('ZipArchive') ? '<b class="text-danger">' . phrase('disabled') . '</b>' : '<b class="text-success">' . phrase('available') . '</b>') . '
						</p>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-6">
					<div class="form-group">
						<label class="d-block mb-0">
							' . phrase('multibyte_string') . ' (mbstring)
						</label>
						<p>
							' . (!in_array('mbstring', $extension) ? '<b class="text-danger">' . phrase('off') . '</b>, ' . phrase('turn_it_on') : '<b class="text-success">' . phrase('on') . '</b>') . '
						</p>
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
						<label class="d-block mb-0">
							' . phrase('php_gd') . '
						</label>
						<p>
							' . (!in_array('gd', $extension) ? '<b class="text-danger">' . phrase('off') . '</b>, ' . phrase('turn_it_on') : '<b class="text-success">' . phrase('on') . '</b>') . '
						</p>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-6">
					<div class="form-group">
						<label class="d-block mb-0">
							' . phrase('json') . '
						</label>
						<p>
							' . (!in_array('json', $extension) ? '<b class="text-danger">' . phrase('off') . '</b>, ' . phrase('turn_it_on') : '<b class="text-success">' . phrase('on') . '</b>') . '
						</p>
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
						<label class="d-block mb-0">
							' . phrase('xml') . '
						</label>
						<p>
							' . (!in_array('xml', $extension) ? '<b class="text-danger">' . phrase('off') . '</b>, ' . phrase('turn_it_on') : '<b class="text-success">' . phrase('on') . '</b>') . '
						</p>
					</div>
				</div>
			</div>
			' . ($error ? '<div class="alert alert-warning failure"><b>' . phrase('whoops') . '</b> ' . phrase('some_requirement_are_not_yet_fulfilled') . ' ' . phrase('please_update_your_server_configuration_and_click_on_refresh_button_to_continue_the_installation') : null) . '
			<hr class="row" />
			<div class="row">
				<div class="col-md-6">
					' . ($error ? '<a href="requirement.php" class="btn btn-light btn-block --xhr">' . phrase('refresh') . '</a>' : '&nbsp;') . '
				</div>
				<div class="col-md-6 text-right">
					<button type="submit" class="btn btn-primary btn-block"' . ($error ? ' disabled' : null) . '>
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
			'active'								=> '.requirement',
			'passed'								=> '.none',
			'html'									=> $html
		)
	);
	