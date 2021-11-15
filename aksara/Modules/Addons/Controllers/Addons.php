<?php

namespace Aksara\Modules\Addons\Controllers;

/**
 * Addons
 *
 * @author			Aby Dahana
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
		
		$this->set_permission(1);
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
		$this->set_title(phrase('addons_market'))
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
						'type'						=> service('request')->getGet('type'),
						'initial'					=> service('request')->getGet('item'),
						'version'					=> aksara('version')
					)
				)
			);
			
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
		->modal_size('modal-lg')
		
		->render(null, 'detail');
	}
	
	/**
	 * Install add-ons
	 */
	public function install()
	{
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
						'type'						=> service('request')->getGet('type'),
						'initial'					=> service('request')->getGet('item'),
						'version'					=> aksara('version'),
						'install'					=> true
					)
				)
			);
			
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
				$addon_file							= file_get_contents($package->repository);
				
				$zip								= new \ZipArchive();
				$unzip								= $zip->open($addon_file);
				$tmp_path							= WRITEPATH . 'cache' . DIRECTORY_SEPARATOR . sha1($package->repository);
				
				if($unzip === true)
				{
					$zip->extractTo($tmp_path);
					
					$files							= directory_map($tmp_path);
					
					if(!$files)
					{
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
							$package_path			= str_replace('\\', null, $key);
						}
						
						if(!is_array($val)) continue;
						
						foreach($val as $_key => $_val)
						{
							if('package.json' == $_val && file_exists($tmp_path . DIRECTORY_SEPARATOR . $key . $_val))
							{
								$package			= json_decode(file_get_contents($tmp_path . DIRECTORY_SEPARATOR . $key . $_val));
								
								if(!$package || !isset($package->name) || !isset($package->description) || !isset($package->version) || !isset($package->author) || !isset($package->compatibility) || !isset($package->type) || !in_array($package->type, array('module', 'backend', 'frontend')))
								{
									$this->_rmdir($tmp_path);
									
									$zip->close();
									
									return throw_exception(400, array('file' => phrase('the_package_manifest_was_invalid')));
								}
								else if(!in_array(aksara('version'), $package->compatibility))
								{
									$this->_rmdir($tmp_path);
									
									$zip->close();
									
									return throw_exception(400, array('file' => phrase('the_' . $type . '_package_is_not_compatible_with_your_current_aksara_version')));
								}
								
								$valid_package		= true;
							}
						}
					}
					
					$this->_rmdir($tmp_path);
					
					if(!$valid_package)
					{
						$zip->close();
						
						return throw_exception(400, array('file' => phrase('no_package_manifest_found_on_your_module_package')));
					}
					
					if(is_dir(ROOTPATH . $type . $package_path))
					{
						$zip->close();
						
						if(!service('request')->getPost('upgrade'))
						{
							$html					= '
								<div class="p-3">
									<form action="' . current_page() . '" method="POST" class="--validate-form">
										<div class="text-center">
											' . phrase('the_' . $type . '_package_with_same_structure_is_already_installed') . ' ' . phrase('do_you_want_to_upgrade_' . $type . '_instead') . '
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
												<input type="hidden" name="upgrade" value="1" />
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
									'status'		=> 200,
									'meta'			=> array
									(
										'title'		=> phrase('action_warning'),
										'icon'		=> 'mdi mdi-alert-outline',
										'popup'		=> true
									),
									'html'			=> $html
								)
							);
						}
					}
					
					if(is_writable(ROOTPATH . $path))
					{
						$extract					= $zip->extractTo(ROOTPATH . $path);
						
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
						
						$extract					= $zip->extractTo(ROOTPATH . $path);
						
						$zip->close();
					}
					
					if($extract && is_dir(ROOTPATH . $path . DIRECTORY_SEPARATOR . $package_path))
					{
						if('module' == $type)
						{
							try
							{
								// push module namespace to filelocator
								$loader				= \CodeIgniter\Services::autoloader()->addNamespace('Modules\\' . $package_path, ROOTPATH . 'modules' . DIRECTORY_SEPARATOR . $package_path);
								
								// run install migration
								$migration			= \Config\Services::migrations()->setNameSpace('Modules\\' . $package_path);
								
								// trying to run the migration
								$migration->latest();
							}
							catch(\Throwable $e)
							{
								/* migration error, delete module */
								$this->_rmdir(ROOTPATH . 'modules' . DIRECTORY_SEPARATOR . $package_path);
								
								return throw_exception(400, array('upgrade' => $e->getMessage()));
							}
						}
						
						return throw_exception(301, phrase('your_' . $type . '_package_was_successfully_imported'), current_page('../'));
					}
					else
					{
						return throw_exception(400, array('file' => phrase('your_' . $type . '_folder_seems_cannot_be_writable')));
					}
				}
				
				return throw_exception(403, phrase('unable_to_install_the_' . $type));
			}
			
			return throw_exception(404, phrase('the_' . $type . '_you_would_to_install_is_not_available'));
		}
		
		return throw_exception(404, phrase('your_web_server_need_to_connected_to_the_internet_to_install_the_addons'));
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
					$installed_themes[]				= str_replace(DIRECTORY_SEPARATOR, null, $val);
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
					$installed_modules[]			= str_replace(DIRECTORY_SEPARATOR, null, $val);
				}
			}
		}
		
		$curl										= \Config\Services::curlrequest
		(
			array
			(
				'timeout'							=> 5,
				'http_errors'						=> false
			)
		);
		
		$response									= $curl->post
		(
			'https://www.aksaracms.com/market/api',
			array
			(
				'allow_redirects'					=> array
				(
					'max'							=> 2
				),
				'headers'							=> array
				(
					'Referer'						=> base_url()
				),
				'form_params'						=> array
				(
					'version'						=> aksara('version'),
					'order'							=> service('request')->getPost('order'),
					'keyword'						=> service('request')->getPost('keyword')
				)
			)
		);
		
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
				$package[$key]['detail_url']		= current_page('detail', array('item' => $val['slug'], 'type' => $val['addon_type']));
				$package[$key]['install_url']		= current_page('install', array('item' => $val['slug'], 'type' => $val['addon_type']));
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
			/* migration error, delete directory */
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
					
					/* trying to delete directory using ftp instead */
					$connection						= @ftp_connect($query->hostname, $query->port, 10);
					
					if($connection && @ftp_login($connection, $query->username, $query->password))
					{
						/* yay! FTP is connected, try to delete directory */
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
