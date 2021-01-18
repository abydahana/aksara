<?php
	session_start();
	
	if(file_exists('..' . DIRECTORY_SEPARATOR . 'config.php'))
	{
		if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
		{
			header('Content-Type: application/json');
			
			echo json_encode
			(
				array
				(
					'status'						=> 301,
					'url'							=> '../xhr/boot'
				)
			);
			exit;
		}
		else
		{
			header('Location: ../');
			exit;
		}
	}
	
	header('Content-Type: application/json');
	
	$error											= false;
	$unzip											= false;
	
	$source											= file_get_contents('assets' . DIRECTORY_SEPARATOR . 'config-sample.txt');
	$output											= '..' . DIRECTORY_SEPARATOR . 'config.php';
	
	$source											= str_replace
	(
		array
		(
			'%OPENTAG%',
			'%ENCRYPTION_HASH%',
			'%COOKIE_PREFIX%',
			'%DSN%',
			'%DB_DRIVER%',
			'%DB_HOSTNAME%',
			'%DB_PORT%',
			'%DB_USERNAME%',
			'%DB_PASSWORD%',
			'%DB_DATABASE%',
			'%TIMEZONE%',
			'%DOCUMENT_EXTENSION%',
			'%IMAGE_EXTENSION%',
			'%MAX_UPLOAD_SIZE%',
			'%IMAGE_DIMENSION%',
			'%THUMBNAIL_DIMENSION%',
			'%ICON_DIMENSION%'
		),
		array
		(
			'<?php',
			$_SESSION['security']['encryption'],
			$_SESSION['security']['cookie_prefix'],
			$_SESSION['database']['dsn'],
			'pdo',//$_SESSION['database']['driver'],
			$_SESSION['database']['hostname'],
			$_SESSION['database']['port'],
			$_SESSION['database']['username'],
			$_SESSION['database']['password'],
			$_SESSION['database']['initial'],
			$_SESSION['system']['timezone'],
			str_replace(',', '|', $_SESSION['system']['file_extension']),
			str_replace(',', '|', $_SESSION['system']['image_extension']),
			$_SESSION['system']['max_upload_size'],
			$_SESSION['system']['image_dimension'],
			$_SESSION['system']['thumbnail_dimension'],
			$_SESSION['system']['icon_dimension']
		),
		$source
	);
	
	if(!isset($_GET['validate_config']) && !file_exists('..' . DIRECTORY_SEPARATOR . 'config.php'))
	{
		$ftp_form									= '
			<label class="d-block">
				<input type="checkbox" name="request_config" value="1"  /> <b>CLICK HERE</b> to upload config file manually after installation
			</label>
			<div class="using_ftp">
				<hr />
				<p>
					<b>OR</b> fill the field below with your FTP account to try writing configuration file over FTP.
				</p>
				<div class="row">
					<div class="col-sm-9">
						<div class="form-group mb-2">
							<label class="d-block text-muted mb-1">
								Hostname
							</label>
							<input type="text" name="ftp_host" class="form-control form-control-sm" placeholder="e.g: ftp.example.com" value="' . (isset($_POST['ftp_host']) ? $_POST['ftp_host'] : null) . '" />
						</div>
					</div>
					<div class="col-sm-3">
						<div class="form-group mb-2">
							<label class="d-block text-muted mb-1">
								Port
							</label>
							<input type="text" name="ftp_port" class="form-control form-control-sm" placeholder="e.g: 21" value="' . (isset($_POST['ftp_port']) ? $_POST['ftp_port'] : null) . '" />
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-6">
						<div class="form-group mb-2">
							<label class="d-block text-muted mb-1">
								Username
							</label>
							<input type="text" name="ftp_user" class="form-control form-control-sm" placeholder="e.g: root" value="' . (isset($_POST['ftp_user']) ? $_POST['ftp_user'] : null) . '" />
						</div>
					</div>
					<div class="col-sm-6">
						<div class="form-group mb-2">
							<label class="d-block text-muted mb-1">
								Password
							</label>
							<input type="password" name="ftp_password" class="form-control form-control-sm" placeholder="Your FTP password" value="' . (isset($_POST['ftp_password']) ? $_POST['ftp_password'] : null) . '" />
						</div>
					</div>
				</div>
				<div class="form-group mb-2">
					<label class="d-block text-muted mb-1">
						Install Path
					</label>
					<input type="text" name="ftp_directory" class="form-control form-control-sm" placeholder="Path to install Aksara" value="' . (isset($_POST['ftp_directory']) ? $_POST['ftp_directory'] : dirname(dirname(__FILE__))) . '" />
				</div>
			</div>
		';
		
		if((!isset($_POST['request_config']) || !$_POST['request_config']) && isset($_POST['ftp_host']) && $_POST['ftp_host'] && isset($_POST['ftp_port']) && $_POST['ftp_port'] && isset($_POST['ftp_user']) && $_POST['ftp_user'] && isset($_POST['ftp_password']) && $_POST['ftp_password'] && isset($_POST['ftp_directory']) && $_POST['ftp_directory'])
		{
			$connection								= ftp_connect($_POST['ftp_host'], $_POST['ftp_port'], 10);
			
			if($connection && ftp_login($connection, $_POST['ftp_user'], $_POST['ftp_password']))
			{
				ftp_pasv($connection, true);
				
				$tmpfile							= fopen('php://memory', 'r+');
				
				fwrite($tmpfile, $source);
				rewind($tmpfile);
				
				if(!ftp_fput($connection, $_POST['ftp_directory'] . DIRECTORY_SEPARATOR . 'config.php', $tmpfile, FTP_BINARY))
				{
					$error							= true;
				}
				
				fclose($tmpfile);
				
				ftp_close($connection);
				
				if($error)
				{
					echo json_encode
					(
						array
						(
							'status'				=> 403,
							'message'				=> 'Cannot write file using FTP. Please check if the Aksara install path is correct.<hr />' . $ftp_form
						)
					);
					
					exit;
				}
			}
			else
			{
				echo json_encode
				(
					array
					(
						'status'					=> 403,
						'message'					=> 'Couldn\'t connect to FTP server using provided settings!<hr />' . $ftp_form
					)
				);
				
				exit;
			}
		}
		elseif(!isset($_POST['request_config']) || !$_POST['request_config'])
		{
			try
			{
				$handle								= fopen($output, 'w+');
				
				if(!$handle)
				{
					throw new \RuntimeException('Failed to open or create config file!');
				}
				
				if(!fwrite($handle, $source))
				{
					throw new \RuntimeException('Failed to write configuration into config file!');
				}
				
				fclose($handle);
			}
			catch(Exception $e)
			{
				echo json_encode
				(
					array
					(
						'status'					=> 403,
						'message'					=> $e->getMessage() . '<hr />' . $ftp_form
					)
				);
				
				exit;
			}
		}
		
		if(in_array($_SESSION['database']['driver'], PDO::getAvailableDrivers()))
		{
			try
			{
				$schema								= file_get_contents('assets' . DIRECTORY_SEPARATOR . 'schema.sql');
				
				$pdo								= new PDO($_SESSION['database']['dsn'], $_SESSION['database']['username'], $_SESSION['database']['password']);
				
				$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				
				$query								= $pdo->prepare('SHOW TABLES');
				
				$query->execute();
				
				foreach($query->fetchAll(\PDO::FETCH_COLUMN) as $key => $val)
				{
					if(!$key)
					{
						$pdo->query('SET foreign_key_checks = 0')->execute();
					}
					
					$pdo->query('DROP TABLE IF EXISTS ' . $val)->execute();
				}
				
				$pdo->query('SET foreign_key_checks = 1')->execute();
				$query->closeCursor();
				
				$query								= $pdo->prepare($schema);
				
				if($query->execute())
				{
					$query->closeCursor();
					
					/**
					 * insert site setting
					 */
					$query							= $pdo->prepare
					('
						INSERT IGNORE INTO app__settings
						(
							app_name,
							app_description,
							app_theme,
							app_language,
							office_name,
							office_phone,
							office_email,
							office_address,
							office_map,
							frontend_registration,
							default_membership_group
						)
						VALUES
						(
							:app_name,
							:app_description,
							:app_theme,
							:app_language,
							:office_name,
							:office_phone,
							:office_email,
							:office_address,
							:office_map,
							:frontend_registration,
							:default_membership_group
						)
					');
					
					$query->execute
					(
						array
						(
							'app_name'				=> $_SESSION['system']['site_title'],
							'app_description'		=> $_SESSION['system']['site_description'],
							'app_theme'				=> 'backend',
							'app_language'			=> 1,
							'office_name'			=> 'Some Company Name',
							'office_phone'			=> '+6281381614558',
							'office_email'			=> 'info@example.com',
							'office_address'		=> '2nd Floor Example Tower Building, Some Road Name, Any Region',
							'office_map'			=> '[]',
							'frontend_registration'	=> 1,
							'default_membership_group'	=> 3
						)
					);
					
					$query->closeCursor();
					
					/**
					 * insert super user
					 */
					$query							= $pdo->prepare
					('
						INSERT IGNORE INTO app__users
						(
							first_name,
							last_name,
							email,
							username,
							password,
							language_id,
							group_id,
							registered_date,
							status
						)
						VALUES
						(
							:first_name,
							:last_name,
							:email,
							:username,
							:password,
							:language_id,
							:group_id,
							:registered_date,
							:status
						)
					');
					
					$query->execute
					(
						array
						(
							'first_name'			=> $_SESSION['security']['first_name'],
							'last_name'				=> $_SESSION['security']['last_name'],
							'email'					=> $_SESSION['security']['email'],
							'username'				=> $_SESSION['security']['username'],
							'password'				=> password_hash($_SESSION['security']['password'] . $_SESSION['security']['encryption'], PASSWORD_DEFAULT),
							'language_id'			=> 1,
							'group_id'				=> 1,
							'registered_date'		=> date('Y-m-d'),
							'status'				=> 1
						)
					);
					
					$query->closeCursor();
					
					/**
					 * insert sample data
					 */
					if(!$_SESSION['system']['mode'])
					{
						$sample_data				= file_get_contents('assets' . DIRECTORY_SEPARATOR . 'sample-data.sql');
						$query						= $pdo->prepare($sample_data);
						
						if($query->execute())
						{
							$query->closeCursor();
							
							$zip					= new ZipArchive();
							$unzip					= $zip->open('assets' . DIRECTORY_SEPARATOR . 'sample-module.zip');
							
							if($unzip === true)
							{
								$zip->extractTo('..' . DIRECTORY_SEPARATOR . 'modules');
								$zip->close();
							}
						}
					}
				}
				else
				{
					if(file_exists($output))
					{
						@unlink($output);
					}
					
					$error							= 'Cannot create database schema. Please try again...';
				}
			}
			catch(PDOException $e)
			{
				if(file_exists($output))
				{
					@unlink($output);
				}
				
				$error								= $e->getMessage();
			}
		}
		else
		{
			$error									= 'Please choose the correct database driver!';
		}
		
		if($error)
		{
			echo json_encode
			(
				array
				(
					'status'						=> 403,
					'message'						=> $error
				)
			);
			
			exit;
		}
	}
	
	$html											= '
		<h4>
			Congratulations!
		</h4>
		<p>
			<a href="//www.aksaracms.com" class="text-primary text-decoration-none" target="_blank"><b>Aksara</b></a> has been successfully installed on your system.
		</p>
		<hr class="row" />
		' . ((isset($_POST['request_config']) && 1 == $_POST['request_config']) || (isset($_GET['validate_config']) && 1 == $_GET['validate_config']) ? '
		<div class="alert alert-warning">
			<h4>
				Notice
			</h4>
			<p>
				Your configuration file or folder is not writable or there was a problem creating the configuration file. You will have to create the following code by hand in ' . dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . '<b>config.php</b> manually and click on revalidate config button.
			</p>
			<textarea rows="10" class="form-control" onclick="this.focus();this.select()">' . $source . '</textarea>
		</div>
		' : null) . '
		<hr class="row" />
		<p class="mb-0">
			You can login as superuser with following credential:
		</p>
		<div class="row">
			<div class="col-4 font-weight-bold">
				Username
			</div>
			<div class="col-8">
				' . $_SESSION['security']['username'] . '
			</div>
		</div>
		<div class="row form-group">
			<div class="col-4 font-weight-bold">
				Password
			</div>
			<div class="col-8">
				' . $_SESSION['security']['password'] . '
			</div>
		</div>
		<hr />
		<div class="row">
			<div class="col-md-5">
				<img src="assets/like-a-boss.png" class="img-fluid" alt="Like a boss..." />
			</div>
			<div class="col-md-7">
				<p>
					If you find this useful, follow my updates to get my other works!
				</p>
				<p>
					Just to remind you, i also <b>collect donations</b> from people like you to <b>support my research</b>.
				</p>
				<p>
					Regardless of the amount, it will be very useful.
				</p>
				<p>
					Cheers,
					<br />
					<a href="//abydahana.github.io" class="text-primary text-decoration-none" target="_blank">
						<b>Aby Dahana</b>
					</a>
				</p>
			</div>
		</div>
		<hr class="row" />
		<div class="row">
			<div class="col-sm-6">
				&nbsp;
			</div>
			<div class="col-sm-6">
				' . (file_exists('..' . DIRECTORY_SEPARATOR . 'config.php') ? '<a href="' . (!$_SESSION['system']['mode'] && $unzip ? '../xhr/boot' : '../welcome/partial_error') . '" class="btn btn-warning btn-block font-weight-bold">Launch Your App</a>' : '<a href="install.php?validate_config=1" class="btn btn-warning btn-block font-weight-bold --xhr">Revalidate Config</a>') . '
			</div>
		</div>
	';
	
	echo json_encode
	(
		array
		(
			'status'								=> 200,
			'active'								=> '.final',
			'passed'								=> '.requirement, .database, .security, .system, .final',
			'html'									=> $html
		)
	);