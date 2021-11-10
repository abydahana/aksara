<?php

if(!function_exists('asset_loader'))
{
	/**
	 * load additional css or js file
	 */
	function asset_loader($assets = array())
	{
		$theme										= null;
		$backtrace									= debug_backtrace();
		
		foreach($backtrace as $key => $val)
		{
			if(isset($val['file']) && $val['file'] == ROOTPATH .  'aksara' . DIRECTORY_SEPARATOR . 'Laboratory' . DIRECTORY_SEPARATOR . 'Core.php' && isset($val['object']->_theme))
			{
				$theme								= $val['object']->_theme;
			}
		}
		
		if(!$theme) return false;
		
		if(!is_array($assets))
		{
			$assets									= array_map('trim', explode(',', $assets));
		}
		
		$output										= null;
		
		foreach($assets as $key => $val)
		{
			if(file_exists(ROOTPATH . 'themes' . DIRECTORY_SEPARATOR . $theme . DIRECTORY_SEPARATOR . $val))
			{
				if('css' == strtolower(pathinfo($val, PATHINFO_EXTENSION)))
				{
					$output							.= "<link rel=\"stylesheet\" type=\"text/css\" href=\"" . base_url('themes/' . $theme . '/' . $val) . "\" />\n";
				}
				else
				{
					$output							.= "<script type=\"text/javascript\" src=\"" . base_url('themes/' . $theme . '/' . $val) . "\"></script>\n";
				}
			}
		}
		
		return $output;
	}
	
	/**
	 * load theme asset
	 */
	function get_theme_asset($data = null)
	{
		$theme										= false;
		$backtrace									= debug_backtrace();
		
		foreach($backtrace as $key => $val)
		{
			if(isset($val['file']) && $val['file'] == ROOTPATH .  'aksara' . DIRECTORY_SEPARATOR . 'Laboratory' . DIRECTORY_SEPARATOR . 'Core.php' && isset($val['object']->_theme) && file_exists(ROOTPATH . 'themes' . DIRECTORY_SEPARATOR . $val['object']->_theme . DIRECTORY_SEPARATOR . $data))
			{
				return base_url('themes/' . $val['object']->_theme . '/' . $data);
			}
		}
		
		return '#';
	}
	
	/**
	 * load module asset
	 */
	function get_module_asset($data = null, $x = false)
	{
		$controller									= service('router')->controllerName();
		
		preg_match('/\\\Modules\\\(.*?)\\\Controllers\\\/', $controller, $matches);
		
		$module										= $matches[1];
		
		if($module && file_exists(ROOTPATH . 'modules' . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . $data))
		{
			return base_url('modules/' . $module . '/assets/' . $data);
		}
		
		return '#';
	}
}
