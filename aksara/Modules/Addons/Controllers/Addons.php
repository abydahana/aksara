<?php namespace Aksara\Modules\Addons\Controllers;
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
	private $_table									= 'app__menus';
	
	public function __construct()
	{
		parent::__construct();
		
		$this->restrict_on_demo();
		
		$this->set_permission(1);
		$this->set_theme('backend');
		
		$this->_primary								= service('request')->getGet('item');
		
		if('market' == service('request')->getPost('source'))
		{
			return $this->_listing();
		}
	}
	
	public function index()
	{
		$this->set_title(phrase('add_on_market'))
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
			$package								= array
			(
				'error'								=> phrase('the_curl_module_is_not_enabled')
			);
		}
		elseif(!@fsockopen('www.aksaracms.com', 80))
		{
			return array
			(
				'error'								=> phrase('cannot_connect_to_the_aksara_market')
			);
		}
		
		if(!$package)
		{
			$curl									= \Config\Services::curlrequest
			(
				array
				(
					'timeout'						=> 5
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
				$package							= array
				(
					'error'							=> $response->getReason()
				);
			}
		}
		
		$this->set_title((isset($package->name) ? $package->name : 'Item not found'))
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
		elseif(!@fsockopen('www.aksaracms.com', 80))
		{
			return array
			(
				'error'								=> phrase('cannot_connect_to_the_aksara_market')
			);
		}
		
		if(in_array(service('request')->getGet('type'), array('theme', 'module')))
		{
			$curl									= \Config\Services::curlrequest
			(
				array
				(
					'timeout'						=> 5
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
			
			if($package)
			{
				// get update package from remote server
				$addon_file							= file_get_contents($package->repository);
				
				// create update package to system temporary, it's must be writable by default
				$tmp_file							= tempnam(sys_get_temp_dir(), sha1($package->repository));
				
				// put update package
				file_put_contents($tmp_file, $addon_file);
				
				$zip								= new \ZipArchive();
				$unzip								= $zip->open($tmp_file);
				
				// remove update package from system temporary
				if(file_exists($tmp_file))
				{
					@unlink($tmp_file);
				}
				
				if($unzip === true)
				{
					if('theme' == service('request')->getGet('type'))
					{
						$path						= 'themes';
					}
					else
					{
						$path						= 'modules';
					}
					
					if(is_writable(ROOTPATH . $path))
					{
						$zip->extractTo(ROOTPATH . $path);
						
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
							return throw_exception(404, phrase('you_need_to_set_up_an_ftp_connection_to_update_your_core_system_due_the_current_workspace_does_not_appear_to_be_writable'), go_to('ftp'));
						}
						
						/* configuration found, decrypt password */
						$query->password			= service('encrypter')->decrypt(base64_decode($query->password));
						
						// try to connect to FTP
						$connection					= @ftp_connect($query->hostname, $query->port, 10);
						
						if(!$connection || !@ftp_login($connection, $query->username, $query->password))
						{
							return throw_exception(403, phrase('unable_to_connect_to_ftp_using_the_provided_configuration'));
						}
						
						$zip->extractTo(ROOTPATH . $path);
						
						$zip->close();
					}
					
					return throw_exception(301, phrase('the_' . service('request')->getGet('type') . '_was_successfully_installed'));
				}
				
				return throw_exception(403, phrase('unable_to_install_the_' . service('request')->getGet('type')));
			}
			
			return throw_exception(404, phrase('the_' . service('request')->getGet('type') . '_you_would_to_install_is_not_available'));
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
			return array
			(
				'error'								=> phrase('the_curl_module_is_not_enabled')
			);
		}
		elseif(!@fsockopen('www.aksaracms.com', 80))
		{
			return array
			(
				'error'								=> phrase('cannot_connect_to_the_aksara_market')
			);
		}
		
		helper('filesystem');
		
		$themes										= directory_map(ROOTPATH . 'themes', 1);
		$ignored_themes								= array();
		
		if($themes)
		{
			foreach($themes as $key => $val)
			{
				if(strpos($val, DIRECTORY_SEPARATOR) !== false)
				{
					$ignored_themes[]				= str_replace(DIRECTORY_SEPARATOR, null, $val);
				}
			}
		}
		
		$modules									= directory_map(ROOTPATH . 'modules', 1);
		$ignored_modules							= array();
		
		if($modules)
		{
			foreach($modules as $key => $val)
			{
				if(strpos($val, DIRECTORY_SEPARATOR) !== false)
				{
					$ignored_modules[]				= str_replace(DIRECTORY_SEPARATOR, null, $val);
				}
			}
		}
		
		$curl										= \Config\Services::curlrequest
		(
			array
			(
				'timeout'							=> 5
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
					'ignored_themes'				=> $ignored_themes,
					'ignored_modules'				=> $ignored_modules,
					'order'							=> service('request')->getPost('order'),
					'keyword'						=> service('request')->getPost('keyword')
				)
			)
		);
		
		$package									= json_decode($response->getBody(), true);
		
		if($response->getStatusCode() !== 200)
		{
			return array
			(
				'error'								=> $response->getReason()
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
}
