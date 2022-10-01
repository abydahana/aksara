<?php

namespace Aksara\Modules\Addons\Controllers;

/**
 * Addons > Modules Manager
 *
 * @author			Aby Dahana <abydahana@gmail.com>
 * @profile			abydahana.github.io
 * @website			www.aksaracms.com
 * @since			version 4.0.0
 * @copyright		(c) 2021 - Aksara Laboratory
 */

class Modules extends \Aksara\Laboratory\Core
{
	public function __construct()
	{
		parent::__construct();
		
		$this->restrict_on_demo();
		
		$this->set_permission();
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
		->modal_size('modal-xl')
		
		->render();
	}
	
	/**
	 * Update module
	 */
	public function update()
	{
		if(!file_exists(ROOTPATH . 'modules' . DIRECTORY_SEPARATOR . $this->_primary . DIRECTORY_SEPARATOR . 'package.json'))
		{
			return throw_exception(404, phrase('the_package_manifest_for_the_selected_module_is_missing'), current_page('../'));
		}
		
		$package									= json_decode(file_get_contents(ROOTPATH . 'modules' . DIRECTORY_SEPARATOR . $this->_primary . DIRECTORY_SEPARATOR . 'package.json'));
		
		if(!isset($package->hash) || !isset($package->version))
		{
			return throw_exception(404, phrase('the_selected_module_is_not_supported_by_the_official_market'), current_page('../', array('item' => null)));
		}
		
		if(!function_exists('curl_init') || !function_exists('curl_exec'))
		{
			return throw_exception(403, phrase('the_curl_module_is_not_enabled'), current_page('../', array('item' => null)));
		}
		else if(!@fsockopen('www.aksaracms.com', 443))
		{
			return throw_exception(403, phrase('unable_to_connect_to_the_aksara_market'), current_page('../', array('item' => null)));
		}
		
		try
		{
			$curl									= \Config\Services::curlrequest
			(
				array
				(
					'timeout'						=> 5,
					'http_errors'					=> false
				)
			);
			
			$response								= $curl->post
			(
				'https://www.aksaracms.com/market/api/detail',
				array
				(
					'allow_redirects'				=> array
					(
						'max'						=> 2
					),
					'headers'						=> array
					(
						'Referer'					=> base_url()
					),
					'form_params'					=> array
					(
						'type'						=> 'module',
						'initial'					=> $package->hash,
						'version'					=> aksara('version')
					)
				)
			);
		}
		catch(\Throwable $e)
		{
			return throw_excetion(404, $e->getMessage());
		}
		
		$upstream									= json_decode($response->getBody());
		
		if($response->getStatusCode() !== 200)
		{
			return throw_exception(403, $response->getReason(), current_page('../', array('item' => null)));
		}
		else if(isset($upstream->version) && $upstream->version > $package->version)
		{
			$html									= '
				<form action="' . current_page('../../../addons/install', array('item' => $upstream->path, 'type' => 'module')) . '" method="POST" class="p-3 --validate-form">
					<div class="text-center">
						' . phrase('a_new_version_of_the_selected_module_is_available') . '
						<br />
						<h4>
							' . $upstream->name . '
						</h4>
						<h5>
							'. phrase('version') . ' ' . $upstream->version . '
						</h5>
					</div>
					<hr class="row" />
					<div class="--validation-callback mb-0"></div>
					<input type="hidden" name="upgrade" value="' . $upstream->path . '" />
					<div class="row">
						<div class="col-6">
							<div class="d-grid">
								<a href="javascript:void(0)" data-bs-dismiss="modal" class="btn btn-light">
									<i class="mdi mdi-window-close"></i>
									' . phrase('cancel') . '
								</a>
							</div>
						</div>
						<div class="col-6">
							<div class="d-grid">
								<button type="submit" class="btn btn-success">
									<i class="mdi mdi-check"></i>
									' . phrase('update') . '
								</button>
							</div>
						</div>
					</div>
				</form>
			';
			
			return make_json
			(
				array
				(
					'status'						=> 200,
					'meta'							=> array
					(
						'title'						=> phrase('update_available'),
						'icon'						=> 'mdi mdi-auto-fix',
						'popup'						=> true
					),
					'html'							=> $html
				)
			);
		}
		
		return throw_exception(404, phrase('no_update_available_at_the_moment'), current_page('../', array('item' => null)));
	}
	
	/**
	 * Import module
	 */
	public function import()
	{
		if($this->valid_token(service('request')->getPost('_token')))
		{
			if(DEMO_MODE)
			{
				return throw_exception(404, phrase('changes_will_not_saved_in_demo_mode'), current_page('../'));
			}
			
			$this->form_validation->setRule('file', phrase('module_package'), 'max_size[file,' . MAX_UPLOAD_SIZE . ']|mime_in[file,application/zip,application/octet-stream,application/x-zip-compressed,multipart/x-zip]|ext_in[file,zip]');
			
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
			$tmp_path								= WRITEPATH . 'cache' . DIRECTORY_SEPARATOR . sha1($_FILES['file']['tmp_name']);
			$package								= new \stdClass();
			
			if($unzip === true)
			{
				if(!is_dir($tmp_path) && !mkdir($tmp_path, 0755, true))
				{
					return throw_exception(400, array('file' => phrase('unable_to_extract_your_module_package')));
				}
				
				// extract the repository
				$zip->extractTo($tmp_path);
				
				$files								= directory_map($tmp_path);
				
				if(!$files)
				{
					// close the opened zip
					$zip->close();
					
					// remove temporary directory
					$this->_rmdir($tmp_path);
					
					return throw_exception(400, array('file' => phrase('unable_to_extract_your_module_package')));
				}
				
				$package							= array();
				$valid_package						= false;
				$package_path						= null;
				$extract							= false;
				
				foreach($files as $key => $val)
				{
					if(!$package_path)
					{
						$package_path				= str_replace(DIRECTORY_SEPARATOR, '', $key);
					}
					
					if(!is_array($val)) continue;
					
					foreach($val as $_key => $_val)
					{
						if(strpos($_key, ' ') !== false)
						{
							break;
						}
						else if('package.json' == $_val && file_exists($tmp_path . DIRECTORY_SEPARATOR . $key . $_val))
						{
							$package				= json_decode(file_get_contents($tmp_path . DIRECTORY_SEPARATOR . $key . $_val));
							
							if(!$package || !isset($package->name) || !isset($package->description) || !isset($package->version) || !isset($package->author) || !isset($package->compatibility) || !isset($package->type) || !in_array($package->type, array('module')))
							{
								// close the opened zip
								$zip->close();
								
								// remove temporary directory
								$this->_rmdir($tmp_path);
								
								return throw_exception(400, array('file' => phrase('the_package_manifest_was_invalid')));
							}
							else if(!in_array(aksara('version'), $package->compatibility))
							{
								// close the opened zip
								$zip->close();
								
								// remove temporary directory
								$this->_rmdir($tmp_path);
								
								return throw_exception(400, array('file' => phrase('the_package_is_not_compatible_with_your_current_aksara_version')));
							}
							
							$valid_package			= true;
						}
					}
				}
				
				if(!$valid_package)
				{
					// close the opened zip
					$zip->close();
					
					// remove temporary directory
					$this->_rmdir($tmp_path);
					
					return throw_exception(400, array('file' => phrase('no_package_manifest_found_on_your_module_package')));
				}
				
				if(is_dir(ROOTPATH . 'modules' . DIRECTORY_SEPARATOR . $package_path) && !service('request')->getPost('upgrade'))
				{
					// close the opened zip
					$zip->close();
					
					// remove temporary directory
					$this->_rmdir($tmp_path);
					
					return throw_exception(400, array('module' => phrase('this_module_package_with_same_structure_is_already_installed')));
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
						return throw_exception(404, phrase('you_need_to_set_up_an_ftp_connection_to_update_your_core_system_due_the_server_does_not_appear_to_be_writable'), go_to('../../ftp'));
					}
					
					/* configuration found, decrypt password */
					$query->username				= service('encrypter')->decrypt(base64_decode($query->username));
					$query->password				= service('encrypter')->decrypt(base64_decode($query->password));
					
					// try to connect to FTP
					$connection						= @ftp_connect($query->hostname, $query->port, 10);
					
					if(!$connection || !@ftp_login($connection, $query->username, $query->password))
					{
						return throw_exception(403, phrase('unable_to_connect_to_the_ftp_using_the_provided_configuration'));
					}
					
					$extract						= $zip->extractTo(ROOTPATH . 'modules');
					
					$zip->close();
				}
				
				if($extract && is_dir(ROOTPATH . 'modules' . DIRECTORY_SEPARATOR . $package_path))
				{
					try
					{
						// push module namespace to filelocator
						$loader						= \Config\Services::autoloader()->addNamespace('Modules\\' . $package_path, ROOTPATH . 'modules' . DIRECTORY_SEPARATOR . $package_path);
						
						// run install migration
						$migration					= \Config\Services::migrations()->setNameSpace('Modules\\' . $package_path);
						
						// trying to run the migration
						if($migration->latest())
						{
							//
						}
					}
					catch(\Throwable $e)
					{
						/* migration error, delete module */
						$this->_rmdir(ROOTPATH . 'modules' . DIRECTORY_SEPARATOR . $package_path);
						
						return throw_exception(400, array('file' => $e->getMessage()));
					}
					
					// run additional command to assign the menus or permissions
					if(isset($package->menu) && is_array($package->menu))
					{
						// assign the available menus
						foreach($package->menu as $key => $val)
						{
							// check if theme property contain valid menu
							if(!isset($val->placement) || !in_array($val->placement, array('header', 'sidebar')) || !isset($val->group) || !isset($val->link) || !is_array($val->link) || !$val->link) continue;
							
							// check if given group is on valid array
							if(!is_array($val->group))
							{
								// otherwise, convert group as array
								$val->group			= array($val->group);
							}
							
							// populate given links as array with adding the unique id
							$links					= str_replace('"label":"', '"id":"' . sha1($package_path) . '","label":"', json_encode($val->link));
							$links					= json_decode($links, true);
							
							// check if links is available or continue
							if(!$links) continue;
							
							// loops the given group
							foreach($val->group as $_key => $_val)
							{
								// make the line is equal :)
								$place				= 'menu_placement';
								
								// get the existing menu from the database
								$existing			= $this->model->get_where
								(
									'app__menus',
									array
									(
										$place		=> $val->placement,
										'group_id'	=> $_val
									),
									1
								)
								->row();
								
								// populate the link obtained
								$serialized			= (isset($existing->serialized_data) ? $existing->serialized_data : '[]');
								$serialized			= ($serialized ? json_decode($serialized, true) : array());
								
								// check if obtained links is populated
								if($serialized)
								{
									// make links unique
									$serialized		= $this->_array_unique($serialized, 'slug', $package_path);
									
									// merge the old link with new one
									$links			= array_merge($serialized, $links);
								}
								
								if($existing)
								{
									// make the line is equal :)
									$sd				= 'serialized_data';
									$id				= 'menu_id';
									
									// update the menu to the database
									$this->model->update
									(
										'app__menus',
										array
										(
											$sd		=> json_encode($links)
										),
										array
										(
											$id		=> $existing->menu_id
										)
									);
								}
								else
								{
									// make the line is equal :)
									$mp				= 'menu_placement';
									$ml				= 'menu_label';
									$md				= 'menu_description';
									$sd				= 'serialized_data';
									$gi				= 'group_id';
									$st				= 'status';
									
									// insert the menu to the database
									$this->model->insert
									(
										'app__menus',
										array
										(
											$mp		=> $val->placement,
											$ml		=> phrase('generated_menu'),
											$md		=> phrase('generated_menu_from_module_installation'),
											$sd		=> json_encode($links),
											$gi		=> $_val,
											$st		=> 1
										)
									);
								}
							}
						}
					}
					
					if(isset($package->permission) && is_object($package->permission))
					{
						foreach($package->permission as $key => $val)
						{
							// check the value is in correct format or continue
							if(!is_object($val)) continue;
							
							// loops the given permission
							foreach($val as $_key => $_val)
							{
								// get the privileges from the database
								$privileges			= $this->model->get_where
								(
									'app__groups_privileges',
									array
									(
										'path'		=> $_key
									),
									1
								)
								->row('privileges');
								
								// check if query has results
								if($privileges)
								{
									// make the line is equal :)
									$gp				= 'privileges';
									$lg				= 'last_generated';
									
									// update the existing privileges
									$this->model->update
									(
										'app__groups_privileges',
										array
										(
											$gp		=> json_encode(array_unique(array_merge(json_decode($privileges, true), json_decode(json_encode($_val), true)))),
											$lg		=> date('Y-m-d H:i:s')
										),
										array
										(
											'path'	=> $_key
										)
									);
								}
								else
								{
									// make the line is equal :)
									$gp				= 'privileges';
									$lg				= 'last_generated';
									
									// otherwise, insert a new one
									$this->model->insert
									(
										'app__groups_privileges',
										array
										(
											'path'	=> $_key,
											$gp		=> json_encode(array_unique($_val)),
											$lg		=> date('Y-m-d H:i:s')
										)
									);
								}
							}
							
							// get the existing group privileges
							$group_privileges		= $this->model->get_where
							(
								'app__groups',
								array
								(
									'group_id'		=> $key
								),
								1
							)
							->row('group_privileges');
							
							// check if group privileges has result
							if($group_privileges)
							{
								// make the line is equal :)
								$gp					= 'group_privileges';
								$gi					= 'group_id';
								
								// update the group privileges obtained
								$this->model->update
								(
									'app__groups',
									array
									(
										$gp			=> json_encode(array_merge(json_decode($group_privileges, true), json_decode(json_encode($val), true)))
									),
									array
									(
										$gi			=> $key
									)
								);
							}
						}
					}
					
					// remove temporary directory
					$this->_rmdir($tmp_path);
					
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
		if(DEMO_MODE)
		{
			return throw_exception(404, phrase('changes_will_not_saved_in_demo_mode'), current_page('../', array('item' => null)));
		}
		
		$this->permission->must_ajax(current_page('../', array('item' => null)));
		
		/* delete confirmation */
		if(!service('request')->getPost('module'))
		{
			$html									= '
				<form action="' . current_page() . '" method="POST" class="p-3 --validate-form">
					<div class="text-center">
						' . phrase('are_you_sure_want_to_delete_this_module') . '
					</div>
					<hr class="row" />
					<div class="--validation-callback mb-0"></div>
					<input type="hidden" name="module" value="' . $this->_primary . '" />
					<div class="row">
						<div class="col-6">
							<div class="d-grid">
								<a href="javascript:void(0)" data-bs-dismiss="modal" class="btn btn-light">
									<i class="mdi mdi-window-close"></i>
									' . phrase('cancel') . '
								</a>
							</div>
						</div>
						<div class="col-6">
							<div class="d-grid">
								<button type="submit" class="btn btn-danger">
									<i class="mdi mdi-check"></i>
									' . phrase('continue') . '
								</button>
							</div>
						</div>
					</div>
				</form>
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
				$query								= $this->model->order_by('id', 'DESC')->get_where
				(
					config('Migrations')->table,
					array
					(
						'namespace'					=> 'Modules\\' . service('request')->getPost('module')
					),
					1
				)
				->row();
				
				if($query)
				{
					try
					{
						// push module namespace to filelocator
						$loader						= \Config\Services::autoloader()->addNamespace($query->namespace, ROOTPATH . 'modules' . DIRECTORY_SEPARATOR . service('request')->getPost('module'));
						
						// run uninstall migration
						$migration					= \Config\Services::migrations()->setNameSpace($query->namespace);
						
						// trying to run the migration
						$migration->regress(($query->batch - 1));
					}
					catch(\Throwable $e)
					{
						return throw_exception(400, array('module' => $e->getMessage()));
					}
				}
				
				/**
				 * prepare to remove unused privileges
				 */
				$package							= file_get_contents(ROOTPATH . 'modules' . DIRECTORY_SEPARATOR . service('request')->getPost('module') . DIRECTORY_SEPARATOR . 'package.json');
				$package							= json_decode($package);
				
				/**
				 * prepare to update the menu that ever linked to uninstalled module before
				 */
				$query								= $this->model->get_where
				(
					'app__menus',
					array
					(
					)
				)
				->result();
				
				// check if query has result
				if($query)
				{
					// query has result, loops the menus
					foreach($query as $key => $val)
					{
						// populate the menu as array
						$menus						= json_decode($val->serialized_data, true);
						
						// check if menus not empty
						if($menus)
						{
							// loops the menu to update links
							foreach($menus as $_key => $_val)
							{
								// check if the link id related to uninstalled module
								if(isset($_val['id']) && $_val['id'] == sha1(service('request')->getPost('module')))
								{
									// link relate to uninstalled module, unset it
									unset($menus[$_key]);
								}
							}
						}
						
						// update the menu structure
						$this->model->update
						(
							'app__menus',
							array
							(
								'serialized_data'	=> json_encode($menus)
							),
							array
							(
								'menu_id'			=> $val->menu_id
							)
						);
					}
				}
				
				// check if package property is exists
				if(isset($package->permission) && is_object($package->permission))
				{
					// package property exist, loops the permissions
					foreach($package->permission as $key => $val)
					{
						// get the privileges from the database
						$privileges					= $this->model->get_where
						(
							'app__groups',
							array
							(
								'group_id'			=> $key
							),
							1
						)
						->row('group_privileges');
						
						// populate the privileges
						$privileges					= json_decode($privileges, true);
						
						// check if privileges from database and module property is not empty
						if($privileges && $val)
						{
							// loops to unset the unused privileges
							foreach($val as $_key => $_val)
							{
								// unset unused privileges from group
								unset($privileges[$_key]);
								
								// remove unused privileges
								$this->model->delete
								(
									'app__groups_privileges',
									array
									(
										'path'		=> $_key
									)
								);
							}
							
							// make the line is equal :)
							$pvlg					= 'group_privileges';
							
							// update the privilege with new one
							$this->model->update
							(
								'app__groups',
								array
								(
									$pvlg			=> json_encode($privileges)
								),
								array
								(
									'group_id'		=> $key
								)
							);
						}
					}
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
						$package->folder			= str_replace(DIRECTORY_SEPARATOR, '', $key);
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
			/* delete directory */
			if(!delete_files($directory, true))
			{
				/* Unable to delete directory. Get FTP configuration */
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
					$query->username				= service('encrypter')->decrypt(base64_decode($query->username));
					$query->password				= service('encrypter')->decrypt(base64_decode($query->password));
					
					try
					{
						/* trying to delete directory using ftp instead */
						$connection					= ftp_connect($query->hostname, $query->port, 10);
						
						if($connection && ftp_login($connection, $query->username, $query->password))
						{
							/* yay! FTP is connected, try to delete the directory */
							$this->_ftp_rmdir($connection, $directory);
							
							/* close FTP connection */
							ftp_close($connection);
						}
					}
					catch(\Exception $e)
					{
						return throw_exception(400, array('file' => $e->getMessage()));
					}
				}
			}
			else if(is_dir($directory))
			{
				// remove garbage directory
				rmdir($directory);
			}
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
	
	/**
	 * Make array unique by value
	 */
	private function _array_unique($array = array(), $key = null, $value = null)
	{
		$value										= strtolower($value);
		
		foreach($array as $subKey => $subArray)
		{
			if(isset($subArray[$key]) && $subArray[$key] == $value)
			{
				unset($array[$subKey]);
			}
		}
		
		return $array;
	}
}
