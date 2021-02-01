<?php namespace Aksara\Laboratory;
/**
 * Form Validation
 *
 * @author			Aby Dahana
 * @profile			abydahana.github.io
 * @website			www.aksaracms.com
 * @since			version 4.0.0
 * @copyright		(c) 2021 - Aksara Laboratory
 */
use Aksara\Laboratory\Model;

class Validation extends \CodeIgniter\Validation\Rules
{
	public $_upload_data							= array();
	
	private $_upload_error;
	
	public function __construct()
	{
		$this->model								= new Model();
		$this->form_validation						= \Config\Services::validation();
		$this->_set_upload_path						= get_userdata('_set_upload_path');
		
		// check wether the rules calling the callback validation
		$this->_callback();
	}
	
	/**
	 * Callback validation
	 */
	private function _callback()
	{
		// check if validation has rules
		if($this->form_validation->getRules())
		{
			$class									= null;
			
			/**
			 * Getting the class that calling the function
			 */
			foreach(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 10) as $key => $val)
			{
				// check if class is the type of modules
				if(stripos($val['class'], 'Modules\\') !== false && stripos($val['class'], '\Controllers\\') !== false)
				{
					// class found
					$class							= new $val['class'];
					
					break;
				}
			}
			
			// extract the validation to getting the callback function
			foreach($this->form_validation->getRules() as $key => $val)
			{
				// skip non-callable rules
				if(stripos($val['rules'], 'callback_') === false) continue;
				
				$rules								= array_map('trim', explode('|', $val['rules']));
				
				if($rules)
				{
					foreach($rules as $_key => $_val)
					{
						if(stripos($_val, 'callback_') !== false)
						{
							$method					= str_replace('callback_', null, $_val);
							
							if(strpos($method, '[') !== false && strpos($method, ']') !== false)
							{
								preg_match('#\[(.*?)\]#', $method, $params);
								
								$params				= explode('.', $params[1]);
								$method				= preg_replace('/\[([^\[\]]++|(?R))*+\]/', null, $method);
								
								$class->$method($params);
							}
							else
							{
								$class->$method();
							}
						}
					}
				}
			}
		}
	}
	
	/**
	 * Check if data is already exist in the database table
	 */
	public function is_unique($value = null, $params = null, $data = array()) : bool
	{
		$params										= explode('.', $params);
		
		if($params)
		{
			$sliced									= array_slice($params, 2, sizeof($params));
			$odd									= array();
			$even									= array();
			
			foreach($sliced as $key => $val)
			{
				if($key % 2 == 0)
				{
					$even[]						 	= $val;
				}
				else
				{
					$odd[]							= $val;
				}
			}
			
			$sliced									= array_combine($even, $odd);
			$num									= 0;
			
			foreach($sliced as $key => $val)
			{
				if(!$num)
				{
					$this->model->where($key . ' != ', $val);
				}
				else
				{
					$this->model->where($key, $val);
				}
				
				$num++;
			}
			
			return $this->model->select($params[1])->get_where($params[0], array($params[1] => $value), 1)->num_rows() === 0;
		}
		
		return false;
	}
	
	/**
	 * Check if field is valid boolean
	 */
	public function boolean($value = null)
	{
		if(null != $value && 1 != $value)
		{
			$this->form_validation->setError('boolean', phrase('the_field') . ' {field} ' . phrase('is_not_a_valid_boolean'));
		}
		
		return true;
	}
	
	/**
	 * Check if field is valid currency
	 */
	public function currency($value = null)
	{
		if(!preg_match('/^\s*[$]?\s*((\d+)|(\d{1,3}(\,\d{3})+))(\.\d{2})?\s*$/', $value))
		{
			$this->form_validation->setError('is_currency', '%s: ' . phrase('the_field_must_contain_a_valid_currency'));
		}
		
		return true;
	}
	
	/**
	 * Check if field is valid date
	 */
	public function valid_date($value = null)
	{
		$valid_date									= \DateTime::createFromFormat('Y-m-d', $value);
		
		if(!$valid_date || $valid_date && $valid_date->format('Y-m-d') !== $value)
		{
			$this->form_validation->setError('valid_date', phrase('the_field') . ' %s ' . phrase('is_not_a_valid_date'));
		}
		
		return true;
	}
	
	/**
	 * Check if field is valid year
	 */
	public function valid_year($value = null)
	{
		$valid_year									= range(1970, date('Y'));
		
		if(!in_array($value, $valid_year))
		{
			$this->form_validation->setError('valid_year', phrase('the_field') . ' %s ' . phrase('is_not_a_valid_year'));
		}
		
		return true;
	}
	
	/**
	 * Check relation table
	 */
	public function relation_checker($value = 0, $params = null)
	{
		list($table, $field)						= array_pad(explode('.', $params), 2, null);
		
		/* check table existence */
		if(!$this->model->table_exists($table))
		{
			$this->form_validation->setError('relation_checker', phrase('the_relation_table_does_not_exist'));
		}
		
		/* check field existence */
		elseif(!$this->model->field_exists($field, $table))
		{
			$this->form_validation->setError('relation_checker', phrase('the_field_for_this_relation_table_does_not_exist'));
		}
		
		/* check if relation data is exists */
		elseif(!$this->model->select($field)->get_where($table, array($field => $value))->row($field))
		{
			$this->form_validation->setError('relation_checker', phrase('the_selected_data_for_this_relation_does_not_exist'));
		}
		
		return true;
	}
	
	/**
	 * We used to extract image in traditional way because of possibility
	 * of multiple image can be uploaded with different field name
	 *
	 * @access		public
	 */
	public function validate_upload($value = null, $params = null)
	{
		$exploded									= explode('.', $params);
		$field										= (isset($exploded[0]) ? $exploded[0] : null);
		$type										= (isset($exploded[1]) ? $exploded[1] : null);
		
		if(!empty($_FILES[$field]['name']))
		{
			if(is_array($_FILES[$field]['name']))
			{
				$files								= $_FILES[$field];
				
				foreach($files['name'] as $key => $val)
				{
					if(is_array($val))
					{
						foreach($val as $_key => $_val)
						{
							if(!isset($files['type'][$key][$_key])) continue;
							
							$filename				= $field . '.' . $key . '.' . $_key;
							
							$_FILES[$filename]		= array
							(
								'name'				=> $_val,
								'type'				=> $files['type'][$key][$_key],
								'tmp_name'			=> $files['tmp_name'][$key][$_key],
								'error'				=> $files['error'][$key][$_key],
								'size'				=> $files['size'][$key][$_key]
							);
							
							$this->_do_upload($filename, $field, $type, $key);
						}
					}
					else
					{
						$filename					= $field . '.' . $key;
						
						$_FILES[$filename]			= array
						(
							'name'					=> $val,
							'type'					=> $files['type'][$key],
							'tmp_name'				=> $files['tmp_name'][$key],
							'error'					=> $files['error'][$key],
							'size'					=> $files['size'][$key]
						);
						
						$this->_do_upload($filename, $field, $type);
					}
				}
			}
			else
			{
				$filename							= $field;
				
				$this->_do_upload($filename, $field, $type);
			}
			
			if($this->_upload_error)
			{
				$this->form_validation->setError('validate_upload', $this->_upload_error);
				
				return false;
			}
		}
		
		/**
		 * because the property isn't accessible from its parent, put
		 * the upload data collection to temporary session instead
		 */
		set_userdata('_upload_data', $this->_upload_data);
		
		return true;
	}
	
	/**
	 * do_upload
	 * Execute the file upload
	 *
	 * @access		private
	 */
	private function _do_upload($filename = null, $field = null, $type = null, $sub = null)
	{
		$source										= service('request')->getFile($filename);
		
		if(!$source->isValid() || $source->hasMoved())
		{
			// return if file is invalid and has been moved
			return false;
		}
		
		$mime_type									= new \Config\Mimes;
		$valid_mime									= array();
		
		if('image' == $type)
		{
			// the selected file is image format
			$filetype								= array_map('trim', explode(',', IMAGE_FORMAT_ALLOWED));
			
			foreach($filetype as $key => $val)
			{
				$valid_mime[]						= $mime_type->guessTypeFromExtension($val);
			}
		}
		else
		{
			// the selected file is non-image format
			$filetype								= array_map('trim', explode(',', DOCUMENT_FORMAT_ALLOWED));
			
			foreach($filetype as $key => $val)
			{
				$valid_mime[]						= $mime_type->guessTypeFromExtension($val);
			}
		}
		
		if(!$source->getName())
		{
			//$this->_upload_error					= phrase('please_choose_file_to_upload');
			
			return false;
		}
		elseif(!in_array($source->getMimeType(), $valid_mime))
		{
			// mime is invalid
			$this->_upload_error					= phrase('the_selected_file_format_is_not_allowed_to_upload');
			
			return false;
		}
		elseif($source->getSizeByUnit('kb') > MAX_UPLOAD_SIZE)
		{
			// size is exceeded the maximum allocation
			$this->_upload_error					= phrase('the_selected_file_size_exceeds_the_maximum_allocation');
			
			return false;
		}
		elseif(!is_dir(UPLOAD_PATH) || !is_writable(UPLOAD_PATH))
		{
			// upload directory is unwritable
			$this->_upload_error					= phrase('the_upload_folder_is_not_writable');
			
			return false;
		}
		
		if(!is_dir(UPLOAD_PATH . '/' . $this->_set_upload_path))
		{
			// create new directory
			if(@mkdir(UPLOAD_PATH . '/' . $this->_set_upload_path, 0755, true))
			{
				copy(UPLOAD_PATH . '/placeholder.png', UPLOAD_PATH . '/' . $this->_set_upload_path . '/placeholder.png');
			}
		}
		
		if(!is_dir(UPLOAD_PATH . '/' . $this->_set_upload_path . '/thumbs'))
		{
			// create thumbnail directory
			if(@mkdir(UPLOAD_PATH . '/' . $this->_set_upload_path . '/thumbs', 0755, true))
			{
				copy(UPLOAD_PATH . '/placeholder_thumb.png', UPLOAD_PATH . '/' . $this->_set_upload_path . '/thumbs/placeholder.png');
			}
		}
		
		if(!is_dir(UPLOAD_PATH . '/' . $this->_set_upload_path . '/icons'))
		{
			// create icon directory
			if(@mkdir(UPLOAD_PATH . '/' . $this->_set_upload_path . '/icons', 0755, true))
			{
				copy(UPLOAD_PATH . '/placeholder_icon.png', UPLOAD_PATH . '/' . $this->_set_upload_path . '/icons/placeholder.png');
			}
		}
		
		// get encrypted filename
		$filename									= $source->getRandomName();
		
		if(in_array($source->getMimeType(), array('image/gif', 'image/jpeg', 'image/png')))
		{
			// uploaded file is image format, prepare image manipulation
			$imageinfo								= getimagesize($source);
			$master_dimension						= ($imageinfo[0] > $imageinfo[1] ? 'width' : 'height');
			$original_dimension						= (is_numeric(IMAGE_DIMENSION) ? IMAGE_DIMENSION : 1024);
			$thumbnail_dimension					= (is_numeric(THUMBNAIL_DIMENSION) ? THUMBNAIL_DIMENSION : 256);
			$icon_dimension							= (is_numeric(ICON_DIMENSION) ? ICON_DIMENSION : 64);
			
			if($source->getMimeType() != 'image/gif' && $imageinfo[0] > $original_dimension)
			{
				// resize image for non-gif format
				$width			     				= $original_dimension;
				$height			     				= $original_dimension;
				
				// load image manipulation library
				$this->image						= \Config\Services::image('gd');
				
				// resize image and move to upload directory
				$this->image->withFile($source)->resize($width, $height, true, $master_dimension)->save(UPLOAD_PATH . '/' . $this->_set_upload_path . '/' . $filename);
			}
			else
			{
				// move file to upload directory
				$source->move(UPLOAD_PATH . '/' . $this->_set_upload_path, $filename);
			}
			
			// create thumbnail and icon of image
			$this->_resize_image($this->_set_upload_path, $filename, 'thumbs', $thumbnail_dimension, $thumbnail_dimension);
			$this->_resize_image($this->_set_upload_path, $filename, 'icons', $icon_dimension, $icon_dimension);
		}
		else
		{
			// non-image format, move directly to upload directory
			$source->move(UPLOAD_PATH . '/' . $this->_set_upload_path, $filename);
		}
		
		if($sub)
		{
			// collect uploaded data (has sub-name)
			$this->_upload_data[$field][$sub][]		= $filename;
		}
		else
		{
			// collect uploaded data (single name)
			$this->_upload_data[$field][]			= $filename;
		}
	}
	
	/**
	 * _resize_image
	 * Generate the thumbnail of uploaded image
	 *
	 * @access		private
	 */
	private function _resize_image($path = null, $filename = null, $type = null, $width = 0, $height = 0)
	{
		$source										= UPLOAD_PATH . '/' . $path . '/' . $filename;
		$target										= UPLOAD_PATH . '/' . $path . ($type ? '/' . $type : null) . '/' . $filename;
		
		$imageinfo									= getimagesize($source);
		$master_dimension							= ($imageinfo[0] > $imageinfo[1] ? 'width' : 'height');
		
		// load image manipulation library
		$this->image								= \Config\Services::image('gd');
		
		// resize image
		if($this->image->withFile($source)->resize($width, $height, true, $master_dimension)->save($target))
		{
			// crop image after resized
			$this->image->withFile($target)->fit($width, $height, 'center')->save($target);
		}
	}
}
