<?php
/**
 * File Helper
 * A helper to call the uploaded file in shorthand
 *
 * @author			Aby Dahana
 * @profile			abydahana.github.io
 * @website			www.aksaracms.com
 * @since			version 4.0.0
 * @copyright		(c) 2021 - Aksara Laboratory
 */

if(!function_exists('get_file'))
{
	/**
	 * Get URL of uploaded file
	 */
	function get_file($path = null, $file = null)
	{
		return base_url(UPLOAD_PATH . '/' . ($path ? $path . '/' : null) . $file);
	}
}

if(!function_exists('get_image'))
{
	/**
	 * Get URL of uploaded image
	 */
	function get_image($type = null, $name = null, $dimension = null)
	{
		if($dimension == 'thumb')
		{
			$placeholder							= UPLOAD_PATH . '/' . ($type ? $type . '/' : null) . 'thumbs/placeholder.png';
			$file									= UPLOAD_PATH . '/' . ($type ? $type . '/' : null) . 'thumbs/' . $name;
		}
		elseif($dimension == 'icon')
		{
			$placeholder							= UPLOAD_PATH . '/' . ($type ? $type . '/' : null) . 'icons/placeholder.png';
			$file									= UPLOAD_PATH . '/' . ($type ? $type . '/' : null) . 'icons/' . $name;
		}
		else
		{
			$placeholder							= UPLOAD_PATH . '/' . ($type ? $type . '/' : null) . 'placeholder.png';
			$file									= UPLOAD_PATH . '/' . ($type ? $type . '/' : null) . $name;
		}
		
		if(is_file($file) && file_exists($file))
		{
			$image									= $file;
		}
		else
		{
			$image									= $placeholder;
		}
		
		$method										= substr(uri_string(), strrpos(uri_string(), '/') + 1);
		
		if((in_array(service('request')->getGet('method'), array('print', 'embed', 'pdf', 'download')) || 'document' == service('request')->getGet('r')) && 'print' != $method && 'embed' != $method)
		{
			$type									= pathinfo(ROOTPATH . $image, PATHINFO_EXTENSION);
			$data									= file_get_contents($image);
			return 'data:image/' . $type . ';base64,' . base64_encode($data);
		}
		
		return base_url($image);
	}
}

if(!function_exists('get_filesize'))
{
	/**
	 * Get file size
	 */
	function get_filesize($path = null, $file = null)
	{
		$size										= array('B','kB','MB','GB','TB','PB','EB','ZB','YB');
		$bytes										= @filesize(FCPATH . UPLOAD_PATH . '/' . ($path ? $path . '/' : null) . $file);
		$factor										= floor((strlen($bytes) - 1) / 3);
		
		return sprintf('%.2f', ($bytes / pow(1024, $factor))) . @$size[$factor];
	}
}
