<?php namespace Aksara\Modules\Addons\Controllers;
/**
 * Addons > Modules Manager
 *
 * @author			Aby Dahana
 * @profile			abydahana.github.io
 * @website			www.aksaracms.com
 * @since			version 4.0.0
 * @copyright		(c) 2021 - Aksara Laboratory
 */
class Modules extends \Aksara\Laboratory\Core
{
	private $_table									= 'app__menus';
	
	public function __construct()
	{
		parent::__construct();
		
		$this->restrict_on_demo();
		
		$this->set_permission(1);
		$this->set_theme('backend');
		
		helper('filesystem');
		
		$this->_primary								= service('request')->getGet('item');
	}
	
	public function index()
	{
		$this->set_title(phrase('module_manager'))
		->set_icon('mdi mdi-puzzle')
		->set_output
		(
			array
			(
				'installed'							=> $this->_installed()
			)
		)
		
		->render();
	}
	
	/**
	 * Get module detail
	 */
	public function detail()
	{
		$package									= json_decode(file_get_contents(ROOTPATH . 'modules' . DIRECTORY_SEPARATOR . $this->_primary . DIRECTORY_SEPARATOR . 'package.json'));
		
		if($package)
		{
			$package->folder						= $this->_primary;
			$package->integrity						= sha1($package->folder . ENCRYPTION_KEY . get_userdata('session_generated'));
		}
		
		$this->set_title(phrase('module_detail'))
		->set_icon('mdi mdi-puzzle')
		->set_output
		(
			array
			(
				'detail'							=> $package
			)
		)
		->modal_size('modal-lg')
		
		->render();
	}
	
	/**
	 * Import module
	 */
	public function import()
	{
		if(DEMO_MODE)
		{
			return throw_exception(404, phrase('changes_will_not_saved_in_demo_mode'), current_page('../'));
		}
		
		if($this->valid_token(service('request')->getPost('_token')))
		{
			$this->form_validation->setRule('file', phrase('module_package'), 'max_size[file,' . MAX_UPLOAD_SIZE . ']|mime_in[file,application/zip,application/octet-stream,application/x-zip-compressed,multipart/x-zip|ext_in[file,zip]');
			
			if($this->form_validation->run(service('request')->getPost()) === false)
			{
				return throw_exception(400, $this->form_validation->getErrors());
			}
			else if(empty($_FILES['file']['tmp_name']))
			{
				return throw_exception(400, array('file' => phrase('no_module_package_were_chosen')));
			}
			else if(!class_exists('ZipArchive'))
			{
				return throw_exception(400, array('file' => phrase('no_zip_extension_found_on_your_web_server_configuration')));
			}
			
			$zip									= new \ZipArchive();
			$unzip									= $zip->open($_FILES['file']['tmp_name']);
			$tmp_path								= WRITEPATH . 'addons' . DIRECTORY_SEPARATOR . sha1($_FILES['file']['tmp_name']);
			
			if($unzip === true)
			{
				if(!is_dir($tmp_path) && !mkdir($tmp_path, 0755, true))
				{
					return throw_exception(400, array('file' => phrase('unable_to_extract_your_module_package')));
				}
				
				$zip->extractTo($tmp_path);
				
				$files								= directory_map($tmp_path);
				
				if(!$files)
				{
					$zip->close();
					
					return throw_exception(400, array('file' => phrase('unable_to_extract_your_module_package')));
				}
				
				$valid_package						= false;
				$package_path						= null;
				$extract							= false;
				
				foreach($files as $key => $val)
				{
					if(!$package_path)
					{
						$package_path				= str_replace('\\', null, $key);
					}
					
					if(!is_array($val)) continue;
					
					foreach($val as $_key => $_val)
					{
						if('package.json' == $_val && file_exists($tmp_path . DIRECTORY_SEPARATOR . $key . $_val))
						{
							$package				= json_decode(file_get_contents($tmp_path . DIRECTORY_SEPARATOR . $key . $_val));
							
							if(!$package || !isset($package->name) || !isset($package->description) || !isset($package->version) || !isset($package->author) || !isset($package->compatibility) || !isset($package->type) || !in_array($package->type, array('module')))
							{
								$this->_rmdir($tmp_path);
								
								$zip->close();
								
								return throw_exception(400, array('file' => phrase('the_package_manifest_was_invalid')));
							}
							else if(!in_array(aksara('version'), $package->compatibility))
							{
								$this->_rmdir($tmp_path);
								
								$zip->close();
								
								return throw_exception(400, array('file' => phrase('the_package_is_not_compatible_with_your_current_aksara_version')));
							}
							
							$valid_package			= true;
						}
					}
				}
				
				$this->_rmdir($tmp_path);
				
				if(!$valid_package)
				{
					$zip->close();
					
					return throw_exception(400, array('file' => phrase('no_package_manifest_found_on_your_module_package')));
				}
				
				if(is_dir(ROOTPATH . 'modules' . $package_path) && !service('request')->getPost('upgrade'))
				{
					$zip->close();
					
					return throw_exception(400, array('module' => phrase('the_module_package_with_same_structure_is_already_installed')));
				}
				
				if(is_writable(ROOTPATH . 'modules'))
				{
					$extract						= $zip->extractTo(ROOTPATH . 'modules');
					
					$zip->close();
				}
				else
				{
					// get the site id
					$site_id						= get_setting('id');
					
					$query							= $this->model->get_where
					(
						'app__ftp',
						array
						(
							'site_id'				=> $site_id
						),
						1
					)
					->row();
					
					if(!$query)
					{
						return throw_exception(404, phrase('you_need_to_set_up_an_ftp_connection_to_update_your_core_system_due_the_server_does_not_appear_to_be_writable'), go_to('ftp'));
					}
					
					/* configuration found, decrypt password */
					$query->password				= service('encrypter')->decrypt(base64_decode($query->password));
					
					// try to connect to FTP
					$connection						= @ftp_connect($query->hostname, $query->port, 10);
					
					if(!$connection || !@ftp_login($connection, $query->username, $query->password))
					{
						return throw_exception(403, phrase('unable_to_connect_to_the_ftp_using_provided_configuration'));
					}
					
					$extract						= $zip->extractTo(ROOTPATH . 'modules');
					
					$zip->close();
				}
				
				if($extract && is_dir(ROOTPATH . 'modules' . DIRECTORY_SEPARATOR . $package_path))
				{
					try
					{
						// push module namespace to filelocator
						$loader						= \CodeIgniter\Services::autoloader()->addNamespace('Modules\\' . $package_path, ROOTPATH . 'modules' . DIRECTORY_SEPARATOR . $package_path);
						
						// run install migration
						$migration					= \Config\Services::migrations()->setNameSpace('Modules\\' . $package_path);
						
						// trying to run the migration
						$migration->latest();
					}
					catch(\Throwable $e)
					{
						/* migration error, delete module */
						$this->_rmdir(ROOTPATH . 'modules' . DIRECTORY_SEPARATOR . $package_path);
						
						return throw_exception(400, array('checkbox' => $e->getMessage()));
					}
					
					return throw_exception(301, phrase('your_module_package_was_successfully_imported'), current_page('../'));
				}
				else
				{
					return throw_exception(400, array('file' => phrase('your_module_folder_seems_cannot_be_writable')));
				}
			}
			
			return throw_exception(400, array('file' => phrase('unable_to_extract_the_module_package')));
		}
		
		$this->set_title(phrase('module_importer'))
		->set_icon('mdi mdi-import')
		->render();
	}
	
	/**
	 * Delete module
	 */
	public function delete()
	{
		$this->permission->must_ajax(current_page('../'));
		
		/* delete confirmation */
		if(!service('request')->getPost('module'))
		{
			$html									= '
				<div class="p-3">
					<form action="' . current_page() . '" method="POST" class="--validate-form">
						<div class="text-center">
							' . phrase('are_you_sure_want_to_delete_this_module') . '
						</div>
						<hr class="row" />
						<div class="--validation-callback mb-0"></div>
						<div class="row">
							<div class="col-6">
								<a href="javascript:void(0)" data-dismiss="modal" class="btn btn-light btn-block">
									<i class="mdi mdi-window-close"></i>
									' . phrase('cancel') . '
								</a>
							</div>
							<div class="col-6">
								<input type="hidden" name="module" value="' . $this->_primary . '" />
								<button type="submit" class="btn btn-danger btn-block">
									<i class="mdi mdi-check"></i>
									' . phrase('continue') . '
								</button>
							</div>
						</div>
					</form>
				</div>
			';
			
			return make_json
			(
				array
				(
					'status'						=> 200,
					'meta'							=> array
					(
						'title'						=> phrase('action_warning'),
						'icon'						=> 'mdi mdi-alert-outline',
						'popup'						=> true
					),
					'html'							=> $html
				)
			);
		}
		
		$this->form_validation->setRule('module', phrase('module'), 'required');
		
		if($this->form_validation->run(service('request')->getPost()) === false)
		{
			return throw_exception(400, array('module' => $this->form_validation->getErrors()));
		}
		
		/* check if requested module to delete is match */
		if(service('request')->getPost('module') && is_dir(ROOTPATH . 'modules' . DIRECTORY_SEPARATOR . service('request')->getPost('module')))
		{
			if(DEMO_MODE)
			{
				return throw_exception(400, array('module' => phrase('changes_will_not_saved_in_demo_mode')));
			}
			
			/* check if module property is exists */
			if(file_exists(ROOTPATH . 'modules' . DIRECTORY_SEPARATOR . service('request')->getPost('module') . DIRECTORY_SEPARATOR . 'package.json'))
			{
				try
				{
					$query							= $this->model->order_by('id', 'DESC')->get_where
					(
						config('migrations')->table,
						array
						(
							'namespace'				=> 'Modules\\' . service('request')->getPost('module')
						),
						1
					)
					->row();
					
					if($query)
					{
						// push module namespace to filelocator
						$loader						= \CodeIgniter\Services::autoloader()->addNamespace($query->namespace, ROOTPATH . 'modules' . DIRECTORY_SEPARATOR . service('request')->getPost('module'));
						
						// run uninstall migration
						$migration					= \Config\Services::migrations()->setNameSpace($query->namespace);
						
						// trying to run the migration
						$migration->regress($query->batch);
					}
				}
				catch(\Throwable $e)
				{
					return throw_exception(400, array('module' => $e->getMessage()));
				}
				
				/* delete module */
				$this->_rmdir(ROOTPATH . 'modules' . DIRECTORY_SEPARATOR . service('request')->getPost('module'));
			}
			else
			{
				/* module property is not found */
				return throw_exception(400, array('module' => phrase('a_module_without_package_manifest_cannot_be_uninstall_from_the_module_manager')));
			}
		}
		else
		{
			return throw_exception(400, array('module' => phrase('the_module_you_would_to_delete_is_not_exists_or_already_uninstalled')));
		}
		
		return throw_exception(301, phrase('the_selected_module_was_successfully_uninstalled'), current_page('../', array('item' => null)));
	}
	
	/**
	 * List installed modules
	 */
	private function _installed()
	{
		$data										= directory_map(ROOTPATH . 'modules');
		
		if(!$data) return false;
		
		$output										= array();
		
		foreach($data as $key => $val)
		{
			if(is_array($val))
			{
				foreach($val as $_key => $_val)
				{
					if($_val != 'package.json') continue;
					
					$package						= json_decode(file_get_contents(ROOTPATH . 'modules' . DIRECTORY_SEPARATOR . $key . $_val));
					
					if($package)
					{
						$package->folder			= str_replace(array('/', '\\'), array(null, null), $key);
						$package->integrity			= sha1($package->folder . ENCRYPTION_KEY . get_userdata('session_generated'));
						
						$output[]					= $package;
					}
				}
			}
		}
		
		return $output;
	}
	
	/**
	 * Remove directory recursivelly using
	 */
	private function _rmdir($directory = null)
	{
		if(is_dir($directory))
		{
			/* migration error, delete module */
			if(!delete_files($directory, true))
			{
				/* Unable to delete module. Get FTP configuration */
				$site_id							= get_setting('id');
				
				$query								= $this->model->get_where
				(
					'app__ftp',
					array
					(
						'site_id'					=> $site_id
					),
					1
				)
				->row();
				
				if($query)
				{
					/* configuration found, decrypt password */
					$query->password				= service('encrypter')->decrypt(base64_decode($query->password));
					
					/* trying to delete module using ftp instead */
					$connection						= @ftp_connect($query->hostname, $query->port, 10);
					
					if($connection && @ftp_login($connection, $query->username, $query->password))
					{
						/* yay! FTP is connected, try to delete module */
						$this->_ftp_rmdir($connection, $directory);
						
						/* close FTP connection */
						ftp_close($connection);
					}
				}
			}
			
			@rmdir($directory);
		}
	}
	
	/**
	 * Remove directory and its files using FTP
	 */
	private function _ftp_rmdir($connection = null, $directory = null)
	{
		if(!$directory)
		{
			return false;
		}
		
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
