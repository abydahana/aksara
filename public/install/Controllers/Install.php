<?php

namespace App\Controllers;

/**
 * @author			Aby Dahana
 * @profile			abydahana.github.io
 * @website			www.aksaracms.com
 * @copyright		(c) 2021 - Aksara Laboratory
 * @since			version 4.2.8
 */

class Install extends BaseController
{
	public function __construct()
	{
		if(file_exists(ROOTPATH . '..' . DIRECTORY_SEPARATOR . 'config.php') && (!isset($_SERVER['PATH_INFO'])  || (isset($_SERVER['PATH_INFO']) && $_SERVER['PATH_INFO'] != '/run')))
		{
			exit(header('Location:' . str_replace('/install', null, base_url())));
		}
		
		helper('language');
		
		if(service('request')->getGet('language') && is_dir(APPPATH . 'Language' . DIRECTORY_SEPARATOR . service('request')->getGet('language')))
		{
			/* set default language */
			if(is_dir(APPPATH . 'Language' . DIRECTORY_SEPARATOR . service('request')->getGet('language')))
			{
				session()->set('language', service('request')->getGet('language'));
				
				service('language')->setLocale(service('request')->getGet('language'));
			}
		}
		else if(in_array(session()->get('language'), array('en', 'id')))
		{
			service('language')->setLocale(session()->get('language'));
		}
		else
		{
			session()->set('language', 'en');
			service('language')->setLocale(session()->get('language'));
		}
	}
	
    public function index()
    {
        return view('index');
    }
	
	public function requirement()
	{
		if(!service('request')->isAJAX())
		{
			exit(header('Location:' . base_url()));
		}
		
		if(service('request')->getPost('_token'))
		{
			service('validation')->setRule('agree', phrase('agreement'), 'required');
			
			if(service('validation')->run(service('request')->getPost()) === false)
			{
				return $this->response->setJSON
				(
					array
					(
						'status'					=> 400,
						'validation'				=> service('validation')->getErrors()
					)
				);
			}
		}
		
		$extension									= array_map('strtolower', get_loaded_extensions());
		$mod_rewrite								= ((isset($_SERVER['HTTP_MOD_REWRITE']) && strtolower($_SERVER['HTTP_MOD_REWRITE']) == 'on') || (function_exists('apache_get_modules') && in_array('mod_rewrite', apache_get_modules())) ? true : false);
		
		$output										= array
		(
			'extension'								=> $extension,
			'mod_rewrite'							=> $mod_rewrite
		);
		
		return $this->response->setJSON
		(
			array
			(
				'status'							=> 200,
				'active'							=> '.requirement',
				'passed'							=> '.none',
				'html'								=> view('requirement', $output)
			)
		);
	}
	
	public function database()
	{
		if(!service('request')->isAJAX())
		{
			exit(header('Location:' . base_url()));
		}
		
		if(service('request')->getPost('_token'))
		{
		}
		
		$output										= array
		(
			'driver'								=> array('MySQLi', 'Postgre', 'SQLSRV', 'SQLite3')
		);
		
		return $this->response->setJSON
		(
			array
			(
				'status'							=> 200,
				'active'							=> '.database',
				'passed'							=> '.requirement',
				'html'								=> view('database', $output)
			)
		);
	}
	
	public function security()
	{
		if(!service('request')->isAJAX())
		{
			exit(header('Location:' . base_url()));
		}
		
		if(service('request')->getPost('_token'))
		{
			service('validation')->setRule('database_driver', phrase('database_driver'), 'required');
			service('validation')->setRule('database_hostname', phrase('hostname'), 'required');
			service('validation')->setRule('database_username', phrase('username'), 'required');
			service('validation')->setRule('database_initial', phrase('database_initial'), 'required');
			
			if(service('validation')->run(service('request')->getPost()) === false)
			{
				return $this->response->setJSON
				(
					array
					(
						'status'					=> 400,
						'validation'				=> service('validation')->getErrors()
					)
				);
			}
			
			$config									= array
			(
				'DBDriver'							=> service('request')->getPost('database_driver'),
				'hostname'							=> service('request')->getPost('database_hostname'),
				'port'								=> service('request')->getPost('database_port'),
				'username'							=> service('request')->getPost('database_username'),
				'password'							=> service('request')->getPost('database_password'),
				'database'							=> service('request')->getPost('database_initial'),
				'DBDebug'							=> true
			);
			
			// initialize parameter to new connection
			$this->db								= \Config\Database::connect($config);
			
			try
			{
				// try to connect to the database
				$this->db->connect();
			}
			catch(\Throwable $e)
			{
				// connection couldn't be made, throw error
				return $this->response->setJSON
				(
					array
					(
						'status'					=> 403,
						'message'					=> $e->getMessage()
					)
				);
			}
			
			session()->set
			(
				array
				(
					'database_driver'				=> service('request')->getPost('database_driver'),
					'database_hostname'				=> service('request')->getPost('database_hostname'),
					'database_port'					=> service('request')->getPost('database_port'),
					'database_username'				=> service('request')->getPost('database_username'),
					'database_password'				=> service('request')->getPost('database_password'),
					'database_initial'				=> service('request')->getPost('database_initial')
				)
			);
		}
		
		$output										= array
		(
			'encryption_key'						=> $this->_random_string(64, true),
			'cookie_name'							=> $this->_random_string(16)
		);
		
		return $this->response->setJSON
		(
			array
			(
				'status'							=> 200,
				'active'							=> '.security',
				'passed'							=> '.database',
				'html'								=> view('security', $output)
			)
		);
	}
	
	public function system()
	{
		if(!service('request')->isAJAX())
		{
			exit(header('Location:' . base_url()));
		}
		
		if(service('request')->getPost('_token'))
		{
			service('validation')->setRule('encryption', phrase('encryption_key'), 'required|regex_match[/^[^\'\\\"]*$/]');
			service('validation')->setRule('cookie_name', phrase('cookie_name'), 'required|regex_match[/^[a-zA-Z0-9]*$/]');
			service('validation')->setRule('first_name', phrase('first_name'), 'required');
			service('validation')->setRule('email', phrase('email'), 'required|valid_email');
			service('validation')->setRule('username', phrase('username'), 'required|alpha_dash');
			service('validation')->setRule('password', phrase('password'), 'required|min_length[6]');
			service('validation')->setRule('confirm_password', phrase('password_confirmation'), 'required|min_length[6]|matches[password]');
			
			if(service('validation')->run(service('request')->getPost()) === false)
			{
				return $this->response->setJSON
				(
					array
					(
						'status'					=> 400,
						'validation'				=> service('validation')->getErrors()
					)
				);
			}
			
			session()->set
			(
				array
				(
					'encryption'					=> 'aksara_' . service('request')->getPost('encryption'),
					'cookie_name'					=> 'aksara_' . service('request')->getPost('cookie_name'),
					'first_name'					=> service('request')->getPost('first_name'),
					'last_name'						=> service('request')->getPost('last_name'),
					'email'							=> service('request')->getPost('email'),
					'username'						=> service('request')->getPost('username'),
					'password'						=> service('request')->getPost('password')
				)
			);
		}
		
		$output										= array
		(
			'timezone'								=> \DateTimeZone::listIdentifiers(\DateTimeZone::ALL)
		);
		
		return $this->response->setJSON
		(
			array
			(
				'status'							=> 200,
				'active'							=> '.system',
				'passed'							=> '.security',
				'html'								=> view('system', $output)
			)
		);
	}
	
	public function finalizing()
	{
		if(!service('request')->isAJAX())
		{
			exit(header('Location:' . base_url()));
		}
		
		$max_filesize_unit							= strtolower(preg_replace('/[^A-Za-z]+/', null, ini_get('upload_max_filesize')));
		
		service('validation')->setRule('installation_mode', phrase('installation_mode'), 'in_list[0,1]');
		service('validation')->setRule('timezone', phrase('timezone'), 'required|timezone');
		service('validation')->setRule('site_title', phrase('site_title'), 'required');
		service('validation')->setRule('site_description', phrase('site_description'), 'required');
		service('validation')->setRule('file_extension', phrase('file_extension'), 'required');
		service('validation')->setRule('image_extension', phrase('image_extension'), 'required');
		service('validation')->setRule('max_upload_size', phrase('max_upload_size'), 'required|numeric|greater_than_equal_to[1]|less_than_equal_to[' . (int) ini_get('upload_max_filesize') * ($max_filesize_unit == 'g' ? 1024 : ($max_filesize_unit == 't' ? 131072 : 1)) . ']');
		service('validation')->setRule('image_dimension', phrase('image_dimension'), 'required|numeric');
		service('validation')->setRule('thumbnail_dimension', phrase('thumbnail_dimension'), 'required|numeric');
		service('validation')->setRule('icon_dimension', phrase('icon_dimension'), 'required|numeric');
		
		if(service('validation')->run(service('request')->getPost()) === false)
		{
			return $this->response->setJSON
			(
				array
				(
					'status'						=> 400,
					'validation'					=> service('validation')->getErrors()
				)
			);
		}
		
		session()->set
		(
			array
			(
				'installation_mode'					=> service('request')->getPost('installation_mode'),
				'timezone'							=> service('request')->getPost('timezone'),
				'site_title'						=> service('request')->getPost('site_title'),
				'site_description'					=> service('request')->getPost('site_description'),
				'file_extension'					=> service('request')->getPost('file_extension'),
				'image_extension'					=> service('request')->getPost('image_extension'),
				'max_upload_size'					=> service('request')->getPost('max_upload_size'),
				'image_dimension'					=> service('request')->getPost('image_dimension'),
				'thumbnail_dimension'				=> service('request')->getPost('thumbnail_dimension'),
				'icon_dimension'					=> service('request')->getPost('icon_dimension')
			)
		);
		
		return $this->response->setJSON
		(
			array
			(
				'status'							=> 200,
				'active'							=> '.finalizing',
				'passed'							=> '.system',
				'html'								=> view('finalizing')
			)
		);
	}
	
	public function run()
	{
		if(!service('request')->isAJAX() && !service('request')->getGet('download'))
		{
			exit(header('Location:' . base_url()));
		}
		
		if(service('request')->getPost('skip_module'))
		{
			// developer mode
			session()->set('installation_mode', 0);
		}
		
		$error										= false;
		$config_source								= file_get_contents('assets' . DIRECTORY_SEPARATOR . 'config-sample.txt');
		$config_source								= str_replace
		(
			array
			(
				'%OPENTAG%',
				'%ENCRYPTION_KEY%',
				'%COOKIE_NAME%',
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
				session()->get('encryption'),
				session()->get('cookie_name'),
				session()->get('database_dsn'),
				session()->get('database_driver'),
				session()->get('database_hostname'),
				session()->get('database_port'),
				session()->get('database_username'),
				session()->get('database_password'),
				session()->get('database_initial'),
				session()->get('timezone'),
				session()->get('file_extension'),
				session()->get('image_extension'),
				floor(session()->get('max_upload_size') * 1024),
				session()->get('image_dimension'),
				session()->get('thumbnail_dimension'),
				session()->get('icon_dimension')
			),
			$config_source
		);
		
		if(service('request')->getPost('_token'))
		{
			$_SERVER['database.default.DSN']		= session()->get('database_dsn');
			$_SERVER['database.default.DBDriver']	= session()->get('database_driver');
			$_SERVER['database.default.hostname']	= session()->get('database_hostname');
			$_SERVER['database.default.port']		= session()->get('database_port');
			$_SERVER['database.default.username']	= session()->get('database_username');
			$_SERVER['database.default.password']	= session()->get('database_password');
			$_SERVER['database.default.database']	= session()->get('database_initial');
			
			// initialize parameter to new connection
			$this->db								= \Config\Database::connect();
			
			try
			{
				// try to connect to the database
				$this->db->connect();
			}
			catch(\Throwable $e)
			{
				// unable to connect to the database
				return $this->response->setJSON
				(
					array
					(
						'status'					=> 403,
						'message'					=> $e->getMessage()
					)
				);
			}
			
			// check if basic installation is selected
			if(session()->get('installation_mode'))
			{
				ini_set('max_execution_time', 600);
				ini_set('memory_limit', '512M');
				
				try
				{
					// try unzip the sample modules
					$zip							= new \ZipArchive();
					
					if($zip->open('assets' . DIRECTORY_SEPARATOR . 'sample-module.zip') === true)
					{
						// extract sample modules to modules path
						$zip->extractTo(ROOTPATH . '..' . DIRECTORY_SEPARATOR);
					}
					
					// close current opened zip file
					$zip->close();
				}
				catch(\Throwable $e)
				{
					// unable to extract the sample module
					return $this->response->setJSON
					(
						array
						(
							'status'				=> 403,
							'message'				=> phrase('unable_to_extract_the_sample_module') . ' ' . phrase('make_sure_the_following_directory_is_writable') . ': <code>' . preg_replace('/\/public/', null, ROOTPATH, 1) . 'modules</code><hr /><label class="text-danger"><input type="checkbox" name="skip_module" value="1" /> ' . phrase('skip_installing_the_sample_module') . '</label>'
						)
					);
				}
			}
			
			// check if the migration not yet executed
			if(!session()->get('migrated'))
			{
				// database migrations and seeder
				try
				{
					// check if migration has been run previously
					if($this->db->tableExists(config('Migrations')->table))
					{
						// truncate the migrations table
						$this->db->table(config('Migrations')->table)->truncate();
					}
					
					// run the installer migration
					$migration						= \Config\Services::migrations();
					
					// migrate the database schema
					if($migration->latest())
					{
						// call seeder
						$seeder						= \Config\Database::seeder();
						
						// run seeder
						$seeder->call('MainSeeder');
						
						// check if basic installation is selected
						if(session()->get('installation_mode'))
						{
							// run seeder to insert sample data
							$seeder->call('DummySeeder');
						}
					}
					
					// mark the migration has been migrated
					session()->set('migrated', true);
				}
				catch(\Throwable $e)
				{
					// migration couldn't be executed, throw error
					return $this->response->setJSON
					(
						array
						(
							'status'				=> 403,
							'message'				=> $e->getMessage()
						)
					);
				}
			}
			
			// check if configuration file is exists
			if(!file_exists(ROOTPATH . '..' . DIRECTORY_SEPARATOR . 'config.php'))
			{
				try
				{
					// try to writing configuration file
					file_put_contents(ROOTPATH . '..' . DIRECTORY_SEPARATOR . 'config.php', $config_source, 1);
				}
				catch(\Throwable $e)
				{
					return $this->response->setJSON
					(
						array
						(
							'status'				=> 200,
							'active'				=> '.finalizing',
							'passed'				=> '.system',
							'html'					=> view('error')
						)
					);
				}
			}
			
			// destroy session
			session()->destroy();
		}
		else if(1 == service('request')->getGet('download'))
		{
			return service('response')->download('config.php', $config_source);
		}
		
		return $this->response->setJSON
		(
			array
			(
				'status'							=> 200,
				'active'							=> '.install',
				'passed'							=> '.final',
				'html'								=> view('finish')
			)
		);
	}
	
	private function _random_string($length = 32, $symbol = false)
	{
		$characters									= '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		
		if($symbol)
		{
			/* add extra symbols */
			$characters								.= '~`!@#%^&*()_-+|}]{[?/.,';
		}
		
		$char_length								= strlen($characters);
		$output										= '';
		
		for($i = 0; $i < $length; $i++)
		{
			$output									.= $characters[rand(0, $char_length - 1)];
		}
		
		return $output;
	}
}
