<?php
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
		$request									= service('request');
		
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
		
		if($params)
		{
			$params									= array_merge($request->getGet(), $params);
		}
		elseif('preview-theme' == $request->getGet('aksara_mode') && sha1($request->getGet('aksara_theme') . $request->getServer('encryption.key') . get_userdata('session_generated')) == $request->getGet('integrity_check') && is_dir('../themes/' . $request->getGet('aksara_theme')))
		{
			$params									= $request->getGet();
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
		
		return service('request')->config->baseURL . rtrim($uri, '/');
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
		return base_url('assets/' . $file);
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
		
		$output										= base_url(uri_string());
		
		if($unset && isset($params[$unset]))
		{
			unset($params[$unset]);
		}
		
		$params										= array_merge(service('request')->getGet(), $params);
		
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
			
			return base_url(uri_string()) . ($method ? '/' . rtrim($method, '/') : null) . ($q ? '?' . http_build_query($q) : null);
		}
		else
		{
			return base_url(uri_string()) . ($method ? '/' . rtrim($method, '/') : null);
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
		
		$slug										= strtolower(str_replace('\\', '/', service('router')->controllerName()));
		$slug										= preg_replace(array('/\/aksara\/modules\//', '/\/modules\//', '/\/controllers\//'), array(null, null, '/'), $slug, 1);
		$slug										= $slug;
		
		$destructure								= explode('/', $slug);
		
		$final_slug									= array();
		$previous_segment							= null;
		
		foreach($destructure as $key => $val)
		{
			if($val != $previous_segment)
			{
				$final_slug[]						= $val;
			}
			
			$previous_segment						= $val;
		}
		
		$final_slug									= implode('/', $final_slug);
		
		$params										= array_merge(service('request')->getGet(), $params);
		
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
			
			$uri									= $final_slug . ($method ? '/' . $method : null) . ($q ? '?' . http_build_query($q) : null);
		}
		else
		{
			$uri									= $final_slug . ($method ? '/' . $method : null);
		}
		
		return base_url($uri);
	}
}

if ( ! function_exists('redirect_to'))
{
	/**
	 * Header Redirect
	 *
	 * Header redirect in two flavors
	 * For very fine grained control over headers, you could use the Output
	 * Library's set_header() function.
	 *
	 * @param	string	$uri	URL
	 * @param	string	$method	Redirect method
	 *			'auto', 'location' or 'refresh'
	 * @param	int	$code	HTTP Response status code
	 * @return	void
	 */
	function redirect_to($uri = '', $method = 'auto', $code = NULL)
	{
		if ( ! preg_match('#^(\w+:)?//#i', $uri))
		{
			$uri = site_url($uri);
		}

		// IIS environment likely? Use 'refresh' for better compatibility
		if ($method === 'auto' && isset($_SERVER['SERVER_SOFTWARE']) && strpos($_SERVER['SERVER_SOFTWARE'], 'Microsoft-IIS') !== FALSE)
		{
			$method = 'refresh';
		}
		elseif ($method !== 'refresh' && (empty($code) OR ! is_numeric($code)))
		{
			if (isset($_SERVER['SERVER_PROTOCOL'], $_SERVER['REQUEST_METHOD']) && $_SERVER['SERVER_PROTOCOL'] === 'HTTP/1.1')
			{
				$code = ($_SERVER['REQUEST_METHOD'] !== 'GET')
					? 303	// reference: http://en.wikipedia.org/wiki/Post/Redirect/Get
					: 307;
			}
			else
			{
				$code = 302;
			}
		}

		switch ($method)
		{
			case 'refresh':
				header('Refresh:0;url='.$uri);
				break;
			default:
				header('Location: '.$uri, TRUE, $code);
				break;
		}
		
		exit;
	}
}
