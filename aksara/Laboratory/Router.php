<?php

namespace Aksara\Laboratory;

/**
 * Hands-off router automation
 *
 * @author			Aby Dahana <abydahana@gmail.com>
 * @profile			abydahana.github.io
 * @website			www.aksaracms.com
 * @since			version 4.0.0
 * @copyright		(c) 2021 - Aksara Laboratory
 */

class Router
{
	/**
	 * if you could use directory based routes automation, why would
	 * you typing here everytime you create a module?
	 */
	public function __construct($routes = null)
	{
		$this->_request								= \Config\Services::request();
		$this->_uri_string							= trim(uri_string(), '/');
		
		if($this->_uri_string && method_exists($this->_request, 'setPath'))
		{
			// set path without trailing slash
			$this->_request->setPath($this->_uri_string);
		}
		
		$find_duplicate								= array_reverse(explode('/', $this->_uri_string));
		$is_duplicate								= (isset($find_duplicate[0]) && isset($find_duplicate[1]) && $find_duplicate[0] == $find_duplicate[1] ? true : false);
		
		$this->_found								= false;
		$this->_collection							= array();
		
		helper('filesystem');
		
		$this->directory_route($routes, directory_map('../modules'), '\Modules\\');
		
		if(!$this->_found)
		{
			/* public module (module overwriter) not found core module instead */
			$this->directory_route($routes, directory_map('../aksara/Modules'), '\Aksara\Modules\\');
		}
		
		if($this->_collection)
		{
			// get higher namespace as route priority
			$higher									= max(array_keys($this->_collection));
			$namespace								= $this->_collection[$higher];
			$namespace								= substr($namespace, 0, strrpos($namespace, '.'));
			$controller								= substr($namespace, strrpos($namespace, '\\') + 1);
			$method									= (strpos($this->_uri_string, '/') !== false ? substr($this->_uri_string, strrpos($this->_uri_string, '/') + 1) : '');
			
			// get priority file
			$file									= str_replace('\\', '/', lcfirst(ltrim(str_replace('\\' . $controller . '\\' . $controller, '\\' . $controller, $namespace . '\\' . ucfirst($method) . '.php'), '\\')));
			
			// get second file under hierarchy
			$second_file							= str_replace('\\', '/', lcfirst(ltrim(str_replace('\\' . $controller . '\\' . $controller, '\\' . $controller, substr($namespace, 0, strripos($namespace, '\\')) . '\\' . ucfirst($method) . '.php'), '\\')));
			
			// check if priority file is exists
			if(file_exists('../' . $file))
			{
				// file exists, apply to route
				$namespace							= str_replace('\\' . $controller . '\\' . $controller, '\\' . $controller, $namespace . '\\' . ucfirst($method));
				
				/* add route for current request */
				$routes->add($this->_uri_string, $namespace . ($is_duplicate && $method && method_exists($namespace, $method) ? '::' . $method : null));
			}
			
			// check if second file is exists
			else if(file_exists('../' . $second_file))
			{
				// file exists, apply to route
				$namespace							= str_replace('\\' . $controller . '\\' . $controller, '\\' . $controller, substr($namespace, 0, strripos($namespace, '\\')) . '\\' . ucfirst($method));
				
				/* add route for current request */
				$routes->add($this->_uri_string, $namespace . ($is_duplicate && $method && method_exists($namespace, $method) ? '::' . $method : null));
			}
			else
			{
				/* add route for current request */
				$routes->add($this->_uri_string, $namespace . (!$is_duplicate && (method_exists($namespace, $method) || strtolower($controller) != strtolower($method)) ? '::' . $method : null));
			}
		}
		
		// apply theme route
		$this->_theme_route($routes);
	}
	
	private function directory_route($routes = null, $directory = array(), $namespace = null)
	{
		/**
		 * recursive function to extract the module route
		 */
		foreach($directory as $key => $val)
		{
			if(is_array($val))
			{
				/* subdirectory found, do more scan */
				$this->directory_route($routes, $val, $namespace . str_replace('/', '\\', $key));
			}
			else
			{
				$module								= explode('/', $this->_uri_string);
				
				/* check if file is a PHP */
				if(strpos($namespace, '\Config\\') !== false && stripos($namespace, '\Modules\\' . $module[0] . '\Config\\') !== false)
				{
					/* apply route from module route config */
					$extra_route					= lcfirst(ltrim(str_replace('\\', '/', $namespace), '/')) . 'Routes.php';
					
					if(file_exists('../' . $extra_route))
					{
						/* add route of public module */
						require '../' . $extra_route;
					}
				}
				
				if('php' == strtolower(pathinfo($val, PATHINFO_EXTENSION)) && strpos($namespace, '\Controllers\\') !== false)
				{
					/* desctructure namespace */
					$destructure					= explode('/', str_replace('/controllers/', '/', str_replace('\\', '/', strtolower($namespace . pathinfo($val, PATHINFO_FILENAME)))));
					
					$prev							= null;
					$module							= null;
					
					foreach($destructure as $_key => $_val)
					{
						/* check if previous segment is not matching with current segment */
						if($prev != $_val)
						{
							$module					.= ($_key ? '/' : null) . $_val;
						}
						
						$prev						= $_val;
					}
					
					/* format namespace to module slug */
					$module							= ltrim(preg_replace(array('/aksara\/modules\//', '/modules\//'), array('', ''), $module, 1), '/');
					
					/* extract method from current slug*/
					$method							= substr($this->_uri_string, strrpos($this->_uri_string, '/') + 1);
					
					/* check if module is matched with current slug */
					if($module == $this->_uri_string)
					{
						/* check if file is exist */
						if('../' . lcfirst(trim(str_replace('\\', '/', lcfirst(substr($namespace, 0, strrpos($namespace, '\\')) . '\\' . $val)), '/')))
						{
							$x						= substr_count($namespace . $val, '\\');
							$this->_collection[$x]	= $namespace . $val;
						}
						else
						{
							$x						= substr_count($namespace . $val, '\\');
							$this->_collection[$x]	= $namespace . $val;
						}
						
						$this->_found				= true;
					}
					else if($module. '/' . $method == $this->_uri_string && '../' . lcfirst(trim(str_replace('\\', '/', lcfirst(substr($namespace, 0, strrpos($namespace, '\\')) . '\\' . $val)), '/')))
					{
						$x							= substr_count($namespace . $val, '\\');
						$this->_collection[$x]		= $namespace . $val;
						
						$this->_found				= true;
					}
				}
			}
		}
	}
	
	private function _theme_route($routes = null)
	{
		/**
		 * Dealing with file access of the theme that located
		 * outside the public folder
		 */
		if($this->_request->uri->getTotalSegments() >= 2 && $this->_request->uri->getSegment(2) && is_string($this->_request->uri->getSegment(2)) && is_dir('../themes/' . $this->_request->uri->getSegment(2)) && 'themes' == $this->_request->uri->getSegment(1))
		{
			/* add route */
			$routes->get($this->_uri_string, '\Aksara\Modules\Assets\Controllers\Assets::themes');
		}
	}
}
