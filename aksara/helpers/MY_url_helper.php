<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * MY_url_helper
 * Override the default CodeIgniter url_helper to follow the local parameter
 *
 * @author		Aby Dahana
 * @profile		abydahana.github.io
 */

if (!function_exists('base_url'))
{
	/**
	 * Base URL
	 *
	 * Create a local URL based on your basepath.
	 * Segments can be passed in as a string or an array, same as site_url
	 * or a URL to a file can be passed in, e.g. to an image file.
	 *
	 * @param	string	$method
	 * @param	array	$params
	 * @return	string
	 */
	function base_url($method = '', $params = array())
	{
		if(is_array($method))
		{
			$method									= implode('/', $method);
		}
		if(is_object($params))
		{
			$params									= (array) $params;
		}
		if(!is_array($params))
		{
			$params									= array();
		}
		
		$CI											=& get_instance();
		if($params)
		{
			$params									= array_merge($CI->input->get(), $params);
		}
		
		if(is_array($params) && sizeof($params) > 0)
		{
			$q										= array();
			
			foreach($params as $key => $val)
			{
				if(!$val || in_array($key, $params) && !$params[$key]) continue;
				
				$q[$key]							= $val;
			}
			
			unset($q['aksara']);
			
			if($q)
			{
				$q									= array_merge(array('aksara' => generate_token($q)), $q);
			}
			
			$uri									= $method . ($q ? '?' . http_build_query($q) : null);
		}
		else
		{
			$uri									= $method;
		}
		
		return $CI->config->base_url($uri);
	}
}

if (!function_exists('asset_url'))
{
	/**
	 * Asset URL
	 *
	 * Create a local URL based on your basepath.
	 * Segments can be passed in as a string or an array, same as site_url
	 * or a URL to a file can be passed in, e.g. to an image file.
	 *
	 * @param	string	$method
	 * @param	array	$params
	 * @return	string
	 */
	function asset_url($file = '')
	{
		$CI											=& get_instance();
		
		return $CI->config->base_url(ASSET_PATH . '/' . $file);
	}
}

if (!function_exists('base_theme'))
{
	/**
	 * Base Theme
	 *
	 * Get the url of current theme
	 *
	 * @param	string	$method
	 * @param	array	$params
	 * @return	string
	 */
	function base_theme($file = '')
	{
		$CI											=& get_instance();
		
		return $CI->config->base_url(THEME_PATH . '/' . $CI->template->get_theme() . '/' . $file);
	}
}

if(!function_exists('current_page'))
{
	/**
	 * Current Page
	 *
	 * Get the current page URL and add the add extra parameter
	 * on it.
	 *
	 * @param	string	$method
	 * @param	array	$params
	 * @param	string	$unset
	 * @return	string
	 */
	function current_page($method = null, $params = array(), $unset = null)
	{
		if(is_object($params))
		{
			$params									= (array) $params;
		}
		if(!is_array($params))
		{
			$params									= array();
		}
		
		$CI											=& get_instance();
		$output										= $CI->config->site_url($CI->uri->uri_string());
		
		if($unset && isset($params[$unset]))
		{
			unset($params[$unset]);
		}
		
		$params										= array_merge($CI->input->get(), $params);
		
		if($method)
		{
			$method									= '/' . $method;
		}
		
		if(is_array($params) && sizeof($params) > 0)
		{
			$q										= array();
			
			foreach($params as $key => $val)
			{
				if(!$val || in_array($key, $params) && !$params[$key]) continue;
				
				$q[$key]							= $val;
			}
			
			unset($q['aksara']);
			
			if($q)
			{
				$q									= array_merge(array('aksara' => generate_token($q)), $q);
			}
			
			return $output . $method . ($q ? '?' . http_build_query($q) : null);
		}
		else
		{
			return $output . $method;
		}
	}
}

if(!function_exists('go_to'))
{
	/**
	 * Go To
	 *
	 * Generate the next page from the current page and add
	 * extra parameter on it.
	 *
	 * @param	string	$method
	 * @param	array	$params
	 * @return	string
	 */
	function go_to($method = null, $params = array())
	{
		if(is_array($method))
		{
			$method									= implode('/', $method);
		}
		if(is_object($params))
		{
			$params									= (array) $params;
		}
		if(!is_array($params))
		{
			$params									= array();
		}
		
		$CI											=& get_instance();
		$CI->_module								= $CI->router->fetch_module();
		$CI->_submodule								= $CI->uri->rsegment(1);
		$CI->_controller							= $CI->router->fetch_class();
		$CI->_slug									= $CI->_module . ($CI->_module != $CI->_submodule ? '/' . $CI->_submodule : null) . ($CI->_module != $CI->_controller && $CI->_submodule != $CI->_controller ? '/' . $CI->_controller : null);
		
		if(isset($CI->_parent_module) && $CI->_parent_module)
		{
			$CI->_slug								= $CI->_parent_module;
		}
		
		$params										= array_merge($CI->input->get(), $params);
		
		if(is_array($params) && sizeof($params) > 0)
		{
			$q										= array();
			
			foreach($params as $key => $val)
			{
				if(!$val || in_array($key, $params) && !$params[$key]) continue;
				
				$q[$key]							= $val;
			}
			
			unset($q['aksara']);
			
			if($q)
			{
				$q									= array_merge(array('aksara' => generate_token($q)), $q);
			}
			
			$uri									= $CI->_slug . '/' . $method . ($q ? '?' . http_build_query($q) : null);
		}
		else
		{
			$uri									= $CI->_slug . '/' . $method;
		}
		
		return base_url($uri);
	}
}
