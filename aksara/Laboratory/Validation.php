<?php

namespace Aksara\Laboratory;

/**
 * Form Validation
 *
 * @author			Aby Dahana <abydahana@gmail.com>
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
				
				// destructure the rules
				$rules								= array_map('trim', explode('|', $val['rules']));
				
				if($rules)
				{
					// rules found, find the callback
					foreach($rules as $_key => $_val)
					{
						// check if callback were called
						if(stripos($_val, 'callback_') !== false)
						{
							// callback found, find method if exists
							$method					= preg_replace('/callback_/', '', $_val, 1);
							
							if(!method_exists($class, $method))
							{
								// validation method does not exists
								return throw_exception(400, array($key => $method . ': ' . phrase('function_does_not_exists')));
							}
							
							// find the validation parameter
							if(strpos($method, '[') !== false && strpos($method, ']') !== false)
							{
								// parameter found, destructure the parameter
								preg_match('#\[(.*?)\]#', $method, $params);
								
								$params				= explode('.', $params[1]);
								$method				= preg_replace('/\[([^\[\]]++|(?R))*+\]/', '', $method);
								
								// call the validation method
								$validate			= $class->$method(service('request')->getVar($key), $params);
							}
							else
							{
								// call the validation method
								$validate			= $class->$method(service('request')->getVar($key));
							}
							
							// check if validation success
							if($validate !== true)
							{
								// validation error, throw exception
								return throw_exception(400, array($key => $validate));
							}
							
							// unset the callback validation
							unset($rules[$_key]);
						}
					}
					
					// reinitialize the validation rules for current field
					$this->form_validation->setRule($key, $val['label'], implode('|', $rules));
				}
			}
		}
	}
	
	/**
	 * Check if data is already exist in the database table
	 */
	public function unique($value = null, $params = null, $data = array()) : bool
	{
		$params										= explode('.', str_replace(',', '.', $params));
		
		if($params)
		{
			$sliced									= array_slice($params, 2, sizeof($params));
			$where									= array();
			
			foreach($sliced as $key => $val)
			{
				if($key % 2 === 0)
				{
					$where[$val]					= (isset($sliced[$key + 1]) ? $sliced[$key + 1] : '');
				}
			}
			
			$num									= 0;
			
			foreach($where as $key => $val)
			{
				// check if value not empty
				if(!$val && !is_numeric($val))
				{
					// change the loop number
					$num++;
					
					// value is empty, continue next loops
					continue;
				}
				
				// check if not first loop
				if(!$num)
				{
					// where value is not in statement
					$this->model->where($key . ' != ', $val);
				}
				else
				{
					// where value is in statement
					$this->model->where($key, $val);
				}
				
				$num++;
			}
			
			return $this->model->select($params[1])->get_where($params[0], array($params[1] => $value))->num_rows() === 0;
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
			return false;
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
			return false;
		}
		
		return true;
	}
	
	/**
	 * Check if field is valid date
	 */
	public function valid_date($value = null)
	{
		// convert value to standardzitation
		$value										= date('Y-m-d', strtotime($value));
		
		$valid_date									= \DateTime::createFromFormat('Y-m-d', $value);
		
		if(!$valid_date || ($valid_date && $valid_date->format('Y-m-d') !== $value))
		{
			return false;
		}
		
		return true;
	}
	
	/**
	 * Check if field is valid date and time
	 */
	public function valid_datetime($value = null)
	{
		// convert value to standardzitation
		$value										= date('Y-m-d H:i:s', strtotime($value));
		
		$valid_datetime								= \DateTime::createFromFormat('Y-m-d H:i:s', $value);
		
		if(!$valid_datetime || ($valid_datetime && $valid_datetime->format('Y-m-d H:i:s') !== $value))
		{
			return false;
		}
		
		return true;
	}
	
	/**
	 * Check if field is valid year
	 */
	public function valid_year($value = null)
	{
		$valid_year									= range(1970, 2100);
		
		if(!in_array($value, $valid_year))
		{
			return false;
		}
		
		return true;
	}
	
	/**
	 * Check if field is valid hex
	 */
	public function valid_hex($value = null)
	{
		if(!preg_match('/#([a-f0-9]{3}){1,2}\b/i', $value))
		{
			return false;
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
		else if(!$this->model->field_exists($field, $table))
		{
			$this->form_validation->setError('relation_checker', phrase('the_field_for_this_relation_table_does_not_exist'));
		}
		
		/* check if relation data is exists */
		else if(!$this->model->select($field)->get_where($table, array($field => $value))->row($field))
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
	public function validate_upload($value = null, $params = null, $reset = false)
	{
		if(is_bool($reset) && $reset)
		{
			// reset previously uploaded data
			$this->_upload_data						= array();
			
			unset_userdata('_upload_data');
		}
		
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
							
							$this->_do_upload($filename, $field, $type, $key, $_key);
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
						
						$this->_do_upload($filename, $field, $type, $key);
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
	private function _do_upload($filename = null, $f = null, $type = null, $_key = null, $__key = null)
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
			//$this->_upload_error					= phrase('please_choose_the_file_to_upload');
			
			return false;
		}
		else if(!in_array($source->getMimeType(), $valid_mime))
		{
			// mime is invalid
			$this->_upload_error					= phrase('the_selected_file_format_is_not_allowed_to_upload');
			
			return false;
		}
		else if((float) str_replace(',', '', $source->getSizeByUnit('kb')) > MAX_UPLOAD_SIZE)
		{
			// size is exceeded the maximum allocation
			$this->_upload_error					= phrase('the_selected_file_size_exceeds_the_maximum_allocation');
			
			return false;
		}
		else if(!is_dir(UPLOAD_PATH) || !is_writable(UPLOAD_PATH))
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
		
		if($__key !== null)
		{
			// collect uploaded data (has sub-name)
			$this->_upload_data[$f][$_key][$__key]	= $filename;
		}
		else
		{
			// collect uploaded data (single name)
			$this->_upload_data[$f][$_key]			= $filename;
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
