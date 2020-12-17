<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * CMS > Partials > Media
 * Manage uploaded media.
 *
 * @version			2.1.1
 * @author			Aby Dahana
 * @profile			abydahana.github.io
 */
class Media extends Aksara
{
	private $_folders								= array();
	private $_files									= array();
	
	public function __construct()
	{
		parent::__construct();
		
		$this->restrict_on_demo();
		
		$this->set_permission(array(1, 2));
		$this->set_theme('backend');
		
		$this->set_method('index');
	}
	
	public function index()
	{
		$this->set_title(phrase('manage_media'))
		->set_icon('mdi mdi-folder-image')
		->set_output
		(
			array
			(
				'results'							=> $this->_directory_list($this->input->get('directory'))
			)
		)
		->render();
	}
	
	private function _directory_list($directory = null)
	{
		/* load required helper */
		$this->load->helper('directory');
		$this->load->helper('file');
		
		$data										= directory_map(UPLOAD_PATH);
		
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
		
		$filename									= $this->input->get('file');
		$parent_directory							= substr($directory, 0, strpos($directory, DIRECTORY_SEPARATOR));
		$folders									= array();
		$files										= array();
		
		if($data)
		{
			$this->_parse_files($data, $directory);
		}
		
		$description								= null;
		
		if($this->input->get('file') && file_exists(UPLOAD_PATH . DIRECTORY_SEPARATOR . $filename))
		{
			$description							= get_file_info(UPLOAD_PATH . DIRECTORY_SEPARATOR . $filename);
			$description['icon']					= $this->_get_icon($directory, $filename);
			$description['mime_type']				= get_mime_by_extension(UPLOAD_PATH . DIRECTORY_SEPARATOR . $filename);
		}
		elseif(is_dir(UPLOAD_PATH . DIRECTORY_SEPARATOR . $directory))
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
						'source'					=> str_replace(DIRECTORY_SEPARATOR, null, $key),
						'label'						=> str_replace(DIRECTORY_SEPARATOR, null, $key),
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
						
						$this->_files[]				= array
						(
							'source'				=> $val,
							'label'					=> $val,
							'type'					=> get_mime_by_extension($val),
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
		
		if(in_array($extension, array('png', 'jpg', 'jpeg', 'gif', 'bmp')))
		{
			return get_image($directory, $filename);
		}
		elseif(in_array($extension, array('json')))
		{
			return get_image('_extension', 'json.png');
		}
		elseif(in_array($extension, array('xls', 'xlsx')))
		{
			return get_image('_extension', 'xls.png');
		}
		elseif(in_array($extension, array('csv')))
		{
			return get_image('_extension', 'csv.png');
		}
		elseif(in_array($extension, array('doc', 'docx')))
		{
			return get_image('_extension', 'doc.png');
		}
		elseif(in_array($extension, array('pdf')))
		{
			return get_image('_extension', 'pdf.png');
		}
		else
		{
			return get_image('_extension', 'unknown.png');
		}
	}
}
