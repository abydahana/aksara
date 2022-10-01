<?php

namespace Aksara\Modules\Addons\Controllers;

/**
 * Addons
 *
 * @author			Aby Dahana <abydahana@gmail.com>
 * @profile			abydahana.github.io
 * @website			www.aksaracms.com
 * @since			version 4.0.0
 * @copyright		(c) 2021 - Aksara Laboratory
 */

class Addons extends \Aksara\Laboratory\Core
{
	public function __construct()
	{
		parent::__construct();
		
		$this->restrict_on_demo();
		
		$this->set_permission();
		$this->set_theme('backend');
		
		helper('filesystem');
		
		$this->_primary								= service('request')->getGet('item');
		
		if('market' == service('request')->getPost('source'))
		{
			return $this->_listing();
		}
	}
	
	public function index()
	{
		$this->set_title(phrase('add_ons_market'))
		->set_icon('mdi mdi-cart')
		
		->render();
	}
	
	/**
	 * Grab add-ons detail from Aksara Market
	 */
	public function detail()
	{
		$package									= array();
		
		if(!function_exists('curl_init') || !function_exists('curl_exec'))
		{
			return throw_exception(403, phrase('the_curl_module_is_not_enabled'), current_page('../'));
		}
		else if(!@fsockopen('www.aksaracms.com', 443))
		{
			return throw_exception(403, phrase('unable_to_connect_to_the_aksara_market'), current_page('../'));
		}
		
		if(!$package)
		{
			try
			{
				$curl								= \Config\Services::curlrequest
				(
					array
					(
						'timeout'					=> 5,
						'http_errors'				=> false
					)
				);
				
				$response							= $curl->post
				(
					'https://www.aksaracms.com/market/api/detail',
					array
					(
						'allow_redirects'			=> array
						(
							'max'					=> 2
						),
						'headers'					=> array
						(
							'Referer'				=> base_url()
						),
						'form_params'				=> array
						(
							'type'					=> service('request')->getGet('type'),
							'initial'				=> service('request')->getGet('item'),
							'version'				=> aksara('version')
						)
					)
				);
			}
			catch(\Throwable $e)
			{
				return make_json
				(
					array
					(
						'error'						=> $e->getMessage()
					)
				);
			}
			
			$package								= json_decode($response->getBody());
			
			if($response->getStatusCode() !== 200)
			{
				return throw_exception(403, $response->getReason(), current_page('../'));
			}
		}
		
		$this->set_title((isset($package->name) ? $package->name : phrase('item_not_found')))
		->set_icon('mdi ' . (service('request')->getGet('type') == 'theme' ? 'mdi-palette' : 'mdi-puzzle'))
		->set_output
		(
			array
			(
				'detail'							=> $package
			)
		)
		->modal_size('modal-xl')
		
		->render(null, 'detail');
	}
	
	/**
	 * Install add-ons
	 */
	public function install()
	{
		if(DEMO_MODE)
		{
			return throw_exception(404, phrase('changes_will_not_saved_in_demo_mode'), current_page('../', array('item' => null, 'type' => null)));
		}
		
		if(!function_exists('curl_init') || !function_exists('curl_exec'))
		{
			return throw_exception(403, phrase('the_curl_module_is_not_enabled'), go_to());
		}
		else if(!@fsockopen('www.aksaracms.com', 443))
		{
			return array
			(
				'error'								=> phrase('unable_to_connect_to_the_aksara_market')
			);
		}
		
		if(in_array(service('request')->getGet('type'), array('theme', 'module')))
		{
			try
			{
				$curl								= \Config\Services::curlrequest
				(
					array
					(
						'timeout'					=> 5,
						'http_errors'				=> false
					)
				);
				
				$response							= $curl->post
				(
					'https://www.aksaracms.com/market/api/detail',
					array
					(
						'allow_redirects'			=> array
						(
							'max'					=> 2
						),
						'headers'					=> array
						(
							'Referer'				=> base_url()
						),
						'form_params'				=> array
						(
							'type'					=> service('request')->getGet('type'),
							'initial'				=> service('request')->getGet('item'),
							'version'				=> aksara('version'),
							'install'				=> true
						)
					)
				);
			}
			catch(\Throwable $e)
			{
				return make_json
				(
					array
					(
						'error'						=> $e->getMessage()
					)
				);
			}
			
			$package								= json_decode($response->getBody());
			
			if($response->getStatusCode() !== 200)
			{
				return throw_exception(403, $response->getReason(), go_to());
			}
			
			if('theme' == service('request')->getGet('type'))
			{
				$type								= 'theme';
				$path								= 'themes';
			}
			else
			{
				$type								= 'module';
				$path								= 'modules';
			}
			
			if($package)
			{
				// get update package from remote server
				$tmp_path							= WRITEPATH . 'cache' . DIRECTORY_SEPARATOR . service('request')->getGet('item');
				
				// check if temporary path is available
				if(!is_dir($tmp_path))
				{
					try
					{
						// try create temporary path
						mkdir($tmp_path, 0755, true);
						
						// copy the repository to temporary path
						copy($package->repository, $tmp_path . DIRECTORY_SEPARATOR . 'file.zip');
					}
					catch(\Exception $e)
					{
						// action error, throw exception
						return throw_exception(403, $response->getReason(), go_to());
					}
				}
				
				// load the zip class
				$zip								= new \ZipArchive();
				
				// unzip the repository
				$unzip								= $zip->open($tmp_path . DIRECTORY_SEPARATOR . 'file.zip');
				
				if($unzip === true)
				{
					// extract the repository
					$zip->extractTo($tmp_path);
					
					$files							= directory_map($tmp_path);
					
					if(!$files)
					{
						// close opened zip
						$zip->close();
						
						return throw_exception(400, array('file' => phrase('unable_to_extract_the_selected_' . $type . '_package')));
					}
					
					$valid_package					= false;
					$package_path					= null;
					$extract						= false;
					
					foreach($files as $key => $val)
					{
						if(!$package_path)
						{
							$package_path			= str_replace(DIRECTORY_SEPARATOR, '', $key);
						}
						
						if(!is_array($val)) continue;
						
						foreach($val as $_key => $_val)
						{
							if('package.json' == $_val && file_exists($tmp_path . DIRECTORY_SEPARATOR . $key . $_val))
							{
								$package			= json_decode(file_get_contents($tmp_path . DIRECTORY_SEPARATOR . $key . $_val));
								
								if(!$package || !isset($package->name) || !isset($package->description) || !isset($package->version) || !isset($package->author) || !isset($package->compatibility) || !isset($package->type) || !in_array($package->type, array('module', 'backend', 'frontend')))
								{
									// close opened zip
									$zip->close();
									
									// remove temporary directory
									$this->_rmdir($tmp_path);
									
									return throw_exception(403, phrase('the_package_manifest_was_invalid'));
								}
								else if(!in_array(aksara('version'), $package->compatibility))
								{
									// close opened zip
									$zip->close();
									
									// remove temporary directory
									$this->_rmdir($tmp_path);
									
									return throw_exception(403, phrase('the_' . $type . '_package_is_not_compatible_with_your_current_aksara_version'));
								}
								
								$valid_package		= true;
							}
						}
					}
					
					if(!$valid_package)
					{
						// close opened zip
						$zip->close();
						
						// remove temporary directory
						$this->_rmdir($tmp_path);
						
						return throw_exception(403, phrase('no_package_manifest_found_on_your_module_package'));
					}
					
					// check if the directory already exists
					if(is_dir(ROOTPATH . $path . DIRECTORY_SEPARATOR . $package_path) && service('request')->getPost('upgrade') != service('request')->getGet('item'))
					{
						// close opened zip
						$zip->close();
						
						// remove temporary directory
						$this->_rmdir($tmp_path);
						
						// offer upgrade version of selected module or theme
						$html						= '
							<form action="' . current_page() . '" method="POST" class="--validate-form">
								<div class="text-center">
									' . phrase('the_' . $type . '_package_with_same_structure_is_already_installed') . ' ' . phrase('do_you_want_to_upgrade_the_' . $type . '_instead') . '
								</div>
								<hr class="row" />
								<div class="--validation-callback mb-0"></div>
								<input type="hidden" name="upgrade" value="' . service('request')->getGet('item') . '" />
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
								'status'			=> 200,
								'meta'				=> array
								(
									'title'			=> phrase('action_warning'),
									'icon'			=> 'mdi mdi-alert-outline',
									'popup'			=> true
								),
								'html'				=> $html
							)
						);
					}
					
					if(is_writable(ROOTPATH . $path))
					{
						// extract add-ons to module or theme path
						$extract					= $zip->extractTo(ROOTPATH . $path);
						
						// close opened zip
						$zip->close();
					}
					else
					{
						// get the site id
						$site_id					= get_setting('id');
						
						$query						= $this->model->get_where
						(
							'app__ftp',
							array
							(
								'site_id'			=> $site_id
							),
							1
						)
						->row();
						
						if(!$query)
						{
							return throw_exception(404, phrase('you_need_to_set_up_an_ftp_connection_to_update_your_core_system_due_the_server_does_not_appear_to_be_writable'), go_to('ftp'));
						}
						
						/* configuration found, decrypt password */
						$query->username			= service('encrypter')->decrypt(base64_decode($query->username));
						$query->password			= service('encrypter')->decrypt(base64_decode($query->password));
						
						// try to connect to FTP
						$connection					= @ftp_connect($query->hostname, $query->port, 10);
						
						if(!$connection || !@ftp_login($connection, $query->username, $query->password))
						{
							return throw_exception(403, phrase('unable_to_connect_to_the_ftp_using_provided_configuration'));
						}
						
						// extract add-ons to module or theme path
						$extract					= $zip->extractTo(ROOTPATH . $path);
						
						// close opened zip
						$zip->close();
					}
					
					if($extract && is_dir(ROOTPATH . $path . DIRECTORY_SEPARATOR . $package_path))
					{
						try
						{
							// push module namespace to filelocator
							$loader					= \Config\Services::autoloader()->addNamespace('Modules\\' . $package_path, ROOTPATH . 'modules' . DIRECTORY_SEPARATOR . $package_path);
							
							// run install migration
							$migration				= \Config\Services::migrations()->setNameSpace('Modules\\' . $package_path);
							
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
									$val->group		= array($val->group);
								}
								
								// populate given links as array with adding the unique id
								$links				= str_replace('"label":"', '"id":"' . sha1($package_path) . '","label":"', json_encode($val->link));
								$links				= json_decode($links, true);
								
								// check if links is available or continue
								if(!$links) continue;
								
								// loops the given group
								foreach($val->group as $_key => $_val)
								{
									// make the line is equal :)
									$place			= 'menu_placement';
									$group			= 'group_id';
									
									// get the existing menu from the database
									$existing		= $this->model->get_where
									(
										'app__menus',
										array
										(
											$place	=> $val->placement,
											$group	=> $_val
										),
										1
									)
									->row();
									
									// populate the link obtained
									$serialized		= (isset($existing->serialized_data) ? $existing->serialized_data : '[]');
									$serialized		= ($serialized ? json_decode($serialized, true) : array());
									
									// check if obtained links is populated
									if($serialized)
									{
										// make links unique
										$serialized	= $this->_array_unique($serialized, 'slug', $package_path);
										
										// merge the old link with new one
										$links		= array_merge($serialized, $links);
									}
									
									if($existing)
									{
										// make the line is equal :)
										$sd			= 'serialized_data';
										$id			= 'menu_id';
										
										// update the menu to the database
										$this->model->update
										(
											'app__menus',
											array
											(
												$sd	=> json_encode($links)
											),
											array
											(
												$id	=> $existing->menu_id
											)
										);
									}
									else
									{
										// make the line is equal :)
										$mp			= 'menu_placement';
										$ml			= 'menu_label';
										$md			= 'menu_description';
										$sd			= 'serialized_data';
										$gi			= 'group_id';
										$st			= 'status';
										
										// insert the menu to the database
										$this->model->insert
										(
											'app__menus',
											array
											(
												$mp	=> $val->placement,
												$ml	=> phrase('generated_menu'),
												$md	=> phrase('generated_menu_from_module_installation'),
												$sd	=> json_encode($links),
												$gi	=> $_val,
												$st	=> 1
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
									$privileges		= $this->model->get_where
									(
										'app__groups_privileges',
										array
										(
											'path'	=> $_key
										),
										1
									)
									->row('privileges');
									
									// check if query has results
									if($privileges)
									{
										// make the line is equal :)
										$pt			= 'path';
										$gp			= 'privileges';
										$lg			= 'last_generated';
										
										// update the existing privileges
										$this->model->update
										(
											'app__groups_privileges',
											array
											(
												$gp	=> json_encode(array_unique(array_merge(json_decode($privileges, true), json_decode(json_encode($_val), true)))),
												$lg	=> date('Y-m-d H:i:s')
											),
											array
											(
												$pt	=> $_key
											)
										);
									}
									else
									{
										// make the line is equal :)
										$pt			= 'path';
										$gp			= 'privileges';
										$lg			= 'last_generated';
										
										// otherwise, insert a new one
										$this->model->insert
										(
											'app__groups_privileges',
											array
											(
												$pt	=> $_key,
												$gp	=> json_encode(array_unique($_val)),
												$lg	=> date('Y-m-d H:i:s')
											)
										);
									}
								}
								
								// get the existing group privileges
								$group_privileges	= $this->model->get_where
								(
									'app__groups',
									array
									(
										'group_id'	=> $key
									),
									1
								)
								->row('group_privileges');
								
								// check if group privileges has result
								if($group_privileges)
								{
									// make the line is equal :)
									$gp				= 'group_privileges';
									$gi				= 'group_id';
									
									// update the group privileges obtained
									$this->model->update
									(
										'app__groups',
										array
										(
											$gp		=> json_encode(array_merge(json_decode($group_privileges, true), json_decode(json_encode($val), true)))
										),
										array
										(
											$gi		=> $key
										)
									);
								}
							}
						}
						
						// remove temporary directory
						$this->_rmdir($tmp_path);
						
						return throw_exception(301, phrase('the_selected_' . $type . '_package_was_successfully_installed'), current_page('../' . $type, array('item' => null, 'type' => null)));
					}
					else
					{
						return throw_exception(403, phrase('your_' . $type . '_folder_seems_cannot_be_writable'));
					}
				}
				
				// remove temporary directory
				$this->_rmdir($tmp_path);
				
				return throw_exception(403, phrase('unable_to_install_the_selected_' . $type));
			}
			
			return throw_exception(404, phrase('the_selected_' . $type . '_you_would_to_install_is_not_available'));
		}
		
		return throw_exception(404, phrase('your_web_server_need_to_connected_to_the_internet_to_install_the_add_ons'));
	}
	
	/**
	 * Grab add-ons from Aksara Market
	 */
	private function _listing()
	{
		if(!function_exists('curl_init') || !function_exists('curl_exec'))
		{
			return make_json
			(
				array
				(
					'error'							=> phrase('the_curl_module_is_not_enabled')
				)
			);
		}
		else if(!@fsockopen('www.aksaracms.com', 443))
		{
			return make_json
			(
				array
				(
					'error'							=> phrase('unable_to_connect_to_the_aksara_market')
				)
			);
		}
		
		$themes										= directory_map(ROOTPATH . 'themes', 1);
		$installed_themes							= array();
		
		if($themes)
		{
			foreach($themes as $key => $val)
			{
				if(strpos($val, DIRECTORY_SEPARATOR) !== false)
				{
					$installed_themes[]				= str_replace(DIRECTORY_SEPARATOR, '', $val);
				}
			}
		}
		
		$modules									= directory_map(ROOTPATH . 'modules', 1);
		$installed_modules							= array();
		
		if($modules)
		{
			foreach($modules as $key => $val)
			{
				if(strpos($val, DIRECTORY_SEPARATOR) !== false)
				{
					$installed_modules[]			= str_replace(DIRECTORY_SEPARATOR, '', $val);
				}
			}
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
				'https://www.aksaracms.com/market/api',
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
						'version'					=> aksara('version'),
						'order'						=> service('request')->getPost('order'),
						'keyword'					=> service('request')->getPost('keyword'),
						'installed'					=> json_encode
						(
							array
							(
								'themes'			=> $installed_themes,
								'modules'			=> $installed_modules
							)
						)
					)
				)
			);
		}
		catch(\Throwable $e)
		{
			return make_json
			(
				array
				(
					'error'							=> $e->getMessage()
				)
			);
		}
		
		$package									= json_decode($response->getBody(), true);
		
		if($response->getStatusCode() !== 200)
		{
			return make_json
			(
				array
				(
					'error'							=> $response->getReason()
				)
			);
		}
		
		if($package)
		{
			foreach($package as $key => $val)
			{
				$package[$key]['detail_url']		= current_page('detail', array('item' => $val['path'], 'type' => $val['addon_type']));
				$package[$key]['install_url']		= current_page('install', array('item' => $val['path'], 'type' => $val['addon_type']));
			}
		}
		
		return make_json($package);
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
						return throw_exception(403, $e->getMessage(), go_to());
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
