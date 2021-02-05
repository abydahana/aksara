<?php namespace Aksara\Modules\Administrative\Controllers\Updater;
/**
 * Administrative > Updater
 * This module is used to update Aksara.
 *
 * @author			Aby Dahana
 * @profile			abydahana.github.io
 * @website			www.aksaracms.com
 * @since			version 4.1.22
 * @copyright		(c) 2021 - Aksara Laboratory
 */
use \Composer\Console\Application;
use \Symfony\Component\Console\Input\ArrayInput;

class Updater extends \Aksara\Laboratory\Core
{
	private $_collection							= array();
	private $_updater_name							= 'Aksara';
	
	public function __construct()
	{
		parent::__construct();
		
		$this->restrict_on_demo();
		
		$this->set_permission(1);
		
		$this->set_theme('backend');
	}
	
	public function index($method = null)
	{
		/**
		 * because some method are restricted due to prevent conflict with method that implemented
		 * in model and its core, here is some hack to use the restricted method.
		 */
		if('update' == $method)
		{
			// load filesystem helper
			helper('filesystem');
			
			// hack update method
			return $this->_update();
		}
		
		$this->set_title(phrase('core_system_updater'))
		->set_icon('mdi mdi-update')
		
		->set_output
		(
			array
			(
				'updater'							=> $this->ping_upstream(true)
			)
		)
		
		->render();
	}
	
	/**
	 * Ping upstream
	 */
	public static function ping_upstream($changelog = false)
	{
		if(!function_exists('curl_init') || !function_exists('curl_exec'))
		{
			return false;
		}
		
		$curl										= curl_init();
		
		curl_setopt_array
		(
			$curl,
			array
			(
				CURLOPT_CONNECTTIMEOUT				=> 5,
				CURLOPT_HEADER						=> 0,
				CURLOPT_RETURNTRANSFER				=> 1,
				CURLOPT_URL							=> 'https://www.aksaracms.com/updater/ping',
				CURLOPT_FOLLOWLOCATION				=> true,
				CURLOPT_HTTPHEADER					=> array
				(
					'Content-Type: application/x-www-form-urlencoded',
					'Referer: ' . base_url()
				),
				CURLOPT_CUSTOMREQUEST				=> 'POST',
				CURLOPT_POSTFIELDS					=> http_build_query
				(
					array
					(
						'version'					=> aksara('version'),
						'built_version'				=> aksara('built_version'),
						'changelog'					=> $changelog
					)
				)
			)
		);
		
		$output										= json_decode(curl_exec($curl));
		
		curl_close($curl);
		
		return $output;
	}
	
	/**
	 * Do update
	 */
	private function _update()
	{
		if(DEMO_MODE)
		{
			return throw_exception(404, phrase('changes_will_not_saved_in_demo_mode'), current_page());
		}
		
		if(service('request')->getPost('_token'))
		{
			$this->_validate_form();
		}
		
		if(!function_exists('curl_init') || !function_exists('curl_exec'))
		{
			return false;
		}
		
		$curl										= curl_init();
		
		curl_setopt_array
		(
			$curl,
			array
			(
				CURLOPT_CONNECTTIMEOUT				=> 5,
				CURLOPT_HEADER						=> 0,
				CURLOPT_RETURNTRANSFER				=> 1,
				CURLOPT_URL							=> 'https://www.aksaracms.com/updater/update',
				CURLOPT_FOLLOWLOCATION				=> true,
				CURLOPT_HTTPHEADER					=> array
				(
					'Content-Type: application/x-www-form-urlencoded'
				),
				CURLOPT_CUSTOMREQUEST				=> 'POST',
				CURLOPT_POSTFIELDS					=> http_build_query
				(
					array
					(
						'version'					=> aksara('version'),
						'built_version'				=> aksara('built_version')
					)
				)
			)
		);
		
		$response									= json_decode(curl_exec($curl));
		
		curl_close($curl);
		
		if($response)
		{
			$this->_updater_name					= $this->_updater_name . '-' . $response->version;
			
			// update package found, run updater
			$this->_run_updater($response);
		}
		
		return false;
	}
	
	/**
	 * Run updater
	 */
	private function _run_updater($response = array())
	{
		$updated									= false;
		
		// check if workspace is writable
		if(is_writable(ROOTPATH) && is_writable(APPPATH) && is_writable(FCPATH) && is_writable(WRITEPATH))
		{
			// workspace is writable, do instant update
			$updated								= $this->_instant_update($response);
		}
		else
		{
			// workspace is not writable, use FTP instead
			$updated								= $this->_ftp_update($response);
		}
		
		if($updated)
		{
			// change out of the webroot so that the vendors file is not created in
			putenv('COMPOSER_HOME=' . ROOTPATH . '/vendor/bin/composer');
			
			//Create the commands
			$input									= new ArrayInput
			(
				array
				(
					'command'						=> 'update'
				)
			);
			
			//Create the application and run it with the commands
			$application							= new Application();
			$application->setAutoExit(false);
			$application->run($input);
			
			// core system updated
			return throw_exception(301, phrase('your_core_system_was_successfully_updated'), current_page('../'));
		}
		else
		{
			// failded to update core system
			return throw_exception(403, phrase('something_went_wrong_while_updating_your_core_system') . ' ' . phrase('please_try_again_later'), current_page('../'));
		}
	}
	
	/**
	 * Run instant updater
	 */
	private function _instant_update($response = array())
	{
		// get collection
		$this->_get_collection($response);
		
		// collection not found
		if(!$this->_collection)
		{
			return false;
		}
		
		// show maintenance page
		file_put_contents(FCPATH . 'index.html', '<!DOCTYPE html><html><head><title>' . phrase('under_maintenance') . '</title><meta name="viewport" content="user-scalable=no, width=device-width, height=device-height, initial-scale=1, maximum-scale=1"></head><body align="center"><h1>' . phrase('under_maintenance') . '</h1><hr /><p>' . phrase('we_are_currently_working_to_update_the_system') . ' ' . phrase('please_come_back_in_a_few_minutes') . '</p></body></html>', FILE_APPEND | LOCK_EX);
		
		// collection found
		$backup_name								= '_BACKUP-' . sha1(time()) . '.zip';
		
		$zip										= new \ZipArchive();
		
		// create backup package
		if($zip->open(WRITEPATH . 'cache' . DIRECTORY_SEPARATOR . $backup_name, \ZipArchive::CREATE) === true)
		{
			foreach($this->_collection as $key => $val)
			{
				// add original file to backup package
				$zip->addFile($val, str_replace(ROOTPATH, null, $val));
			}
			
			// close zip library
			$zip->close();
		}
		
		$copied										= 0;
		
		foreach($this->_collection as $key => $val)
		{
			// copy updater file
			if(copy($key, $val))
			{
				// copy success
				$copied++;
			}
			else
			{
				// copy failed, break operation!
				$copied								= false;
				
				break;
			}
		}
		
		if(!$copied)
		{
			// some file cannot be update, rollback the backup file
			if($zip->open(WRITEPATH . 'cache' . DIRECTORY_SEPARATOR . $backup_name) === true)
			{
				// extract backup file to workspace
				$zip->extractTo(ROOTPATH);
				$zip->close();
			}
		}
		
		// remove updater files
		delete_files(WRITEPATH . 'cache' . DIRECTORY_SEPARATOR . $this->_updater_name, true);
		
		// femove backup file
		unlink(WRITEPATH . 'cache' . DIRECTORY_SEPARATOR . $backup_name);
		
		// remove maintenance page and updater file
		unlink(FCPATH . 'index.html');
		
		return $copied;
	}
	
	/**
	 * Run updater using FTP
	 */
	private function _ftp_update($response = array())
	{
		// get the site id
		$site_id									= get_setting('id');
		
		// check the FTP configuration exists or not
		$query										= $this->model->get_where
		(
			'app__ftp',
			array
			(
				'site_id'							=> $site_id
			),
			1
		)
		->row();
		
		// FTP configuration template form
		$html										= '
			<div class="text-center mb-2">
				<i class="mdi mdi-lan-disconnect mdi-5x text-muted"></i>
				<br />
				<h5>
					' . phrase('unable_to_connect_to_ftp') . '
				</h5>
			</div>
			<p class="text-center">
				' . phrase('you_need_to_set_up_an_ftp_connection_to_update_your_core_system_due_the_current_workspace_does_not_appear_to_be_writable') . '
			</p>
			<hr class="row" />
			<form action="' . current_page() . '" class="--validate-form">
				<div class="row">
					<div class="col-sm-9">
						<div class="form-group">
							<label class="text-muted d-block" for="ftp_hostname_input">
								' . phrase('hostname') . '
								<b class="text-danger">*</b>
							</label>
							<input type="text" name="ftp_hostname" class="form-control" value="' . (isset($query->hostname) ? $query->hostname : null) . '" id="ftp_hostname_input" placeholder="ftp.example.com" />
						</div>
					</div>
					<div class="col-sm-3">
						<div class="form-group">
							<label class="text-muted d-block" for="ftp_port_input">
								' . phrase('port') . '
								<b class="text-danger">*</b>
							</label>
							<input type="text" name="ftp_port" class="form-control" value="' . (isset($query->port) ? $query->port : null) . '" id="ftp_port_input" placeholder="21" />
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-6">
						<div class="form-group">
							<label class="text-muted d-block" for="ftp_username_input">
								' . phrase('username') . '
								<b class="text-danger">*</b>
							</label>
							<input type="text" name="ftp_username" class="form-control" value="' . (isset($query->username) ? $query->username : null) . '" id="ftp_username_input" placeholder="root" />
						</div>
					</div>
					<div class="col-sm-6">
						<div class="form-group">
							<label class="text-muted d-block" for="ftp_password_input">
								' . phrase('password') . '
								<b class="text-danger">*</b>
							</label>
							<input type="password" name="ftp_password" class="form-control" id="ftp_password_input" placeholder="****" />
						</div>
					</div>
				</div>
				<div class="--validation-callback mb-0"></div>
				<hr class="row" />
				<div class="row">
					<div class="col-6">
						<button type="submit" class="btn btn-primary btn-block">
							<i class="mdi mdi-check"></i>
							' . phrase('retry') . '
							<em class="text-sm">(ctrl+s)</em>
						</button>
					</div>
					<div class="col-6">
						<a href="javascript:void(0)" class="btn btn-light btn-block" data-dismiss="modal">
							<i class="mdi mdi-window-close"></i>
							' . phrase('close') . '
							<em class="text-sm">(esc)</em>
						</a>
					</div>
				</div>
			</form>
		';
		
		if(!$query)
		{
			return make_json
			(
				array
				(
					'status'						=> 206,
					'exception'						=> array
					(
						'title'						=> phrase('update_failed'),
						'icon'						=> 'mdi mdi-alert',
						'html'						=> $html
					)
				)
			);
		}
		
		/* configuration found, decrypt password */
		$query->password							= service('encrypter')->decrypt(base64_decode($query->password));
		
		// try to connect to FTP
		$connection									= @ftp_connect($query->hostname, $query->port, 10);
		
		if(!$connection || !@ftp_login($connection, $query->username, $query->password))
		{
			// cannot connect to FTP
			if(service('request')->getPost('ftp_hostname'))
			{
				// throw validation error
				return throw_exception(400, array('ftp_hostname' => phrase('unable_to_connect_to_ftp_using_the_provided_configuration')));
			}
			else
			{
				// open FTP configuration modal
				return make_json
				(
					array
					(
						'status'					=> 206,
						'exception'					=> array
						(
							'title'					=> phrase('update_failed'),
							'icon'					=> 'mdi mdi-alert',
							'html'					=> $html
						)
					)
				);
			}
		}
		
		// get collection
		$this->_get_collection($response);
		
		// collection not found
		if(!$this->_collection)
		{
			return false;
		}
		
		// show maintenance page
		$contents									= '<!DOCTYPE html><html><head><title>' . phrase('under_maintenance') . '</title><meta name="viewport" content="user-scalable=no, width=device-width, height=device-height, initial-scale=1, maximum-scale=1"></head><body align="center"><h1>' . phrase('under_maintenance') . '</h1><hr /><p>' . phrase('we_are_currently_working_to_update_the_system') . ' ' . phrase('please_come_back_in_a_few_minutes') . '</p></body></html>';
		$tmp_file									= fopen('php://temp', 'r+');
		
		fwrite($tmp_file, $contents);
		rewind($tmp_file);
		
		ftp_fput($connection, FCPATH . 'index.html', $tmp_file, FTP_ASCII);
		
		$backup_name								= '_BACKUP-' . sha1(time()) . '.zip';
		
		$zip										= new \ZipArchive();
		
		// create backup package
		if($zip->open(WRITEPATH . 'cache' . DIRECTORY_SEPARATOR . $backup_name, \ZipArchive::CREATE) === true)
		{
			foreach($this->_collection as $key => $val)
			{
				// add original file to backup package
				$zip->addFile($val, str_replace(ROOTPATH, null, $val));
			}
			
			// close zip library
			$zip->close();
		}
		
		$copied										= 0;
		
		foreach($this->_collection as $key => $val)
		{
			// copy updater file
			if(ftp_put($connection, $val, $key, FTP_ASCII))
			{
				// copy success
				$copied++;
			}
			else
			{
				// copy failed, break operation!
				$copied								= 0;
				
				break;
			}
		}
		
		if(!$copied)
		{
			// some file cannot be update, rollback the backup file
			if($zip->open(WRITEPATH . 'cache' . DIRECTORY_SEPARATOR . $backup_name) === true)
			{
				// extract backup file to workspace
				$zip->extractTo(ROOTPATH);
				$zip->close();
			}
		}
		
		// remove updater files
		$this->_ftp_rmdir($connection, WRITEPATH . 'cache' . DIRECTORY_SEPARATOR . $this->_updater_name, true);
		
		// remove backup file
		ftp_delete($connection, WRITEPATH . 'cache' . DIRECTORY_SEPARATOR . $backup_name);
		
		// remove maintenance page and updater file
		ftp_delete($connection, FCPATH . 'index.html');
		
		// close FTP connection
		ftp_close($connection);
		
		return $copied;
	}
	
	/**
	 * Validate form
	 */
	private function _validate_form()
	{
		// validate form
		$this->form_validation->setRule('ftp_hostname', phrase('ftp_hostname'), 'required|valid_url');
		$this->form_validation->setRule('ftp_port', phrase('ftp_port'), 'required|numeric|greater_than_equal_to[1]|less_than_equal_to[65535]');
		$this->form_validation->setRule('ftp_username', phrase('ftp_username'), 'required');
		$this->form_validation->setRule('ftp_password', phrase('ftp_password'), 'required');
		
		if($this->form_validation->run(service('request')->getPost()) === false)
		{
			// the submitted form does not meet the validation criteria
			return throw_exception(400, $this->form_validation->getErrors());
		}
		
		// get the site id
		$site_id									= get_setting('id');
		
		// configuration preparation
		$prepare									= array
		(
			'site_id'								=> $site_id,
			'hostname'								=> service('request')->getPost('ftp_hostname'),
			'port'									=> service('request')->getPost('ftp_port'),
			'username'								=> service('request')->getPost('ftp_username'),
			'password'								=> base64_encode(service('encrypter')->encrypt(service('request')->getPost('ftp_password')))
		);
		
		// check the FTP configuration exists or not
		$query										= $this->model->get_where
		(
			'app__ftp',
			array
			(
				'site_id'							=> $site_id
			),
			1
		)
		->row();
		
		if($query)
		{
			// configuration exists, update the FTP configuration
			$this->model->update
			(
				'app__ftp',
				
				$prepare,
				
				array
				(
					'site_id'						=> $site_id
				)
			);
		}
		else
		{
			// configuration not exists, insert the FTP configuration
			$this->model->insert('app__ftp', $prepare);
		}
	}
	
	/**
	 * Get updater file
	 */
	private function _get_collection($response = array())
	{
		// get update package from remote server
		$update_package								= file_get_contents($response->updater);
		
		// create update package to system temporary, it's must be writable by default
		$tmp_file									= tempnam(sys_get_temp_dir(), $this->_updater_name);
		
		// put update package
		file_put_contents($tmp_file, $update_package);
		
		$zip										= new \ZipArchive();
		$unzip										= $zip->open($tmp_file);
		
		// remove update package from system temporary
		unlink($tmp_file);
		
		if($unzip === true)
		{
			// extract update package
			$zip->extractTo(WRITEPATH . 'cache');
			$zip->close();
			
			// map the update directory
			$updater_file							= directory_map(WRITEPATH . 'cache' . DIRECTORY_SEPARATOR . $this->_updater_name);
			
			if($updater_file)
			{
				// make update collection
				$this->_recursive_collector($updater_file);
			}
		}
	}
	
	/**
	 * Push updater file to collection (recursive for nested folder)
	 */
	private function _recursive_collector($data = array(), $path = null)
	{
		foreach($data as $key => $val)
		{
			// in order to keep the file in writable and skipping update the updater script
			if(in_array($path, array('themes/', 'writable/'))) continue;
			
			if(is_array($val))
			{
				// folder found, reinitialize collector
				$this->_recursive_collector($val, (strpos($key, '/') !== false ? $path . $key : null));
			}
			elseif(!file_exists(ROOTPATH . $path . $val) || (md5_file(WRITEPATH . 'cache' . DIRECTORY_SEPARATOR . $this->_updater_name . DIRECTORY_SEPARATOR . $path . $val) != md5_file(ROOTPATH . $path . $val)))
			{
				// update file found
				$source								= WRITEPATH . 'cache' . DIRECTORY_SEPARATOR . $this->_updater_name . DIRECTORY_SEPARATOR . $path . $val;
				$target								= ROOTPATH . $path . $val;
				
				// push to update collection
				$this->_collection[$source]			= $target;
			}
		}
	}
	
	/**
	 * Remove directory and its files using FTP
	 */
	private function _ftp_rmdir($connection = null, $directory)
	{
		$lists										= ftp_mlsd($connection, $directory);
		
		unset($lists[0]);
		unset($lists[1]);

		foreach($lists as $list)
		{
			$full									= $directory . DIRECTORY_SEPARATOR . $list['name'];
			
			if($list['type'] == 'dir')
			{
				// directory found, reinitialize
				$this->_ftp_rmdir($connection, $full);
			}
			else
			{
				// delete file
				ftp_delete($connection, $full);
			}
		}
		
		// delete directory
		ftp_rmdir($connection, $directory);
	}
}
