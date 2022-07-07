<?php

namespace Aksara\Modules\Cms\Controllers\Partials;

/**
 * CMS > Partials > Media
 * Manage uploaded media.
 *
 * @author			Aby Dahana <abydahana@gmail.com>
 * @profile			abydahana.github.io
 * @website			www.aksaracms.com
 * @since			version 4.0.0
 * @copyright		(c) 2021 - Aksara Laboratory
 */

class Media extends \Aksara\Laboratory\Core
{
	private $_folders								= array();
	private $_files									= array();
	
	public function __construct()
	{
		parent::__construct();
		
		$this->restrict_on_demo();
		
		$this->set_permission();
		$this->set_theme('backend');
		
		$this->set_method('index');
	}
	
	public function index()
	{
		if(service('request')->getGet('action') == 'delete')
		{
			return $this->_delete_file(service('request')->getGet('file'));
		}
		
		$this->set_title(phrase('media'))
		->set_icon('mdi mdi-folder-image')
		->set_output
		(
			array
			(
				'results'							=> $this->_directory_list(service('request')->getGet('directory'))
			)
		)
		
		->render();
	}
	
	private function _delete_file($filename = '')
	{
		try
		{
			unlink(UPLOAD_PATH . DIRECTORY_SEPARATOR . $filename);
		}
		catch(\Throwable $e)
		{
			return throw_exception(403, $e->getMessage());
		}
		
		return throw_exception(301, phrase('the_file_was_successfully_removed'), current_page(null, array('file' => null, 'action' => null)));
	}
	
	private function _directory_list($directory = null)
	{
		/* load required helper */
		helper('filesystem');
		
		$data										= directory_map(UPLOAD_PATH . DIRECTORY_SEPARATOR . $directory);
		
		unset($data['_extension' . DIRECTORY_SEPARATOR], $data['_import_tmp' . DIRECTORY_SEPARATOR], $data['captcha' . DIRECTORY_SEPARATOR], $data['logs' . DIRECTORY_SEPARATOR]);
		
		if($directory)
		{
			$directory_list							= explode(DIRECTORY_SEPARATOR, $directory);
			
			foreach($directory_list as $key => $val)
			{
				$val								= $val . DIRECTORY_SEPARATOR;
				
				if(isset($data[$val]))
				{
					$data							= $data[$val];
				}
			}
		}
		
		$filename									= (service('request')->getGet('file') ? str_replace('/', DIRECTORY_SEPARATOR, service('request')->getGet('file')) : null);
		$parent_directory							= ($directory ? substr($directory, 0, strpos($directory, '/')) : null);
		$folders									= array();
		$files										= array();
		
		if($data)
		{
			$this->_parse_files($data, $directory);
		}
		
		$description								= null;
		
		if(service('request')->getGet('file') && file_exists(UPLOAD_PATH . DIRECTORY_SEPARATOR . $filename))
		{
			$file									= new \CodeIgniter\Files\File(UPLOAD_PATH . DIRECTORY_SEPARATOR . $filename);
			$description							= get_file_info(UPLOAD_PATH . DIRECTORY_SEPARATOR . $filename);
			$description['icon']					= $this->_get_icon($directory, $filename);
			$description['mime_type']				= $file->getMimeType();
			$description['server_path']				= str_replace('\\', '/', $description['server_path']);
		}
		else if(is_dir(UPLOAD_PATH . DIRECTORY_SEPARATOR . $directory))
		{
			//$description							= get_dir_file_info(UPLOAD_PATH . DIRECTORY_SEPARATOR . $directory);
		}
		
		return array
		(
			'parent_directory'						=> $parent_directory,
			'directory'								=> $directory,
			'data'									=> array_merge($this->_folders, $this->_files),
			'description'							=> $description
		);
	}
	
	private function _parse_files($data = array(), $directory = null)
	{
		if($data)
		{
			foreach($data as $key => $val)
			{
				if(strpos($key, DIRECTORY_SEPARATOR) !== false)
				{
					$this->_folders[]				= array
					(
						'source'					=> str_replace(DIRECTORY_SEPARATOR, '', $key),
						'label'						=> str_replace(DIRECTORY_SEPARATOR, '', $key),
						'type'						=> 'directory',
						'icon'						=> base_url('assets/svg/folder')
					);
				}
				else
				{
					if(is_array($val))
					{
						$this->_parse_files($val, $directory);
					}
					else
					{
						if(stripos($val, 'placeholder') !== false) continue;
						
						$file						= new \CodeIgniter\Files\File(UPLOAD_PATH . ($directory ? DIRECTORY_SEPARATOR . $directory : null) . DIRECTORY_SEPARATOR . $val);
						$mime						= $file->getMimeType();
						
						if('css' == strtolower(pathinfo($val, PATHINFO_EXTENSION)))
						{
							$mime					= 'text/css';
						}
						else if('js' == strtolower(pathinfo($val, PATHINFO_EXTENSION)))
						{
							$mime					= 'text/javascript';
						}
						
						$this->_files[]				= array
						(
							'source'				=> $val,
							'label'					=> $val,
							'type'					=> $mime,
							'icon'					=> $this->_get_icon($directory, $val)
						);
					}
				}
			}
		}
	}
	
	private function _get_icon($directory = null, $filename = null)
	{
		$filename									= (strpos($filename, DIRECTORY_SEPARATOR) !== false ? substr($filename, strrpos($filename, DIRECTORY_SEPARATOR) + 1) : $filename);
		$extension									= pathinfo($filename, PATHINFO_EXTENSION);
		
		if(!in_array($extension, array('png', 'jpg', 'jpeg', 'gif', 'bmp')))
		{
			$directory								= (strpos($filename, DIRECTORY_SEPARATOR) !== false ? substr($filename, 0, strpos($filename, DIRECTORY_SEPARATOR)) : null);
		}
		
		if(in_array($extension, array('png', 'jpg', 'jpeg', 'gif')))
		{
			return get_image($directory, $filename);
		}
		else
		{
			return get_image('_extension', $extension . '.png');
		}
	}
}
