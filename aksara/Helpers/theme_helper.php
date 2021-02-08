<?php

if(!function_exists('asset_loader'))
{
	/**
	 * load additional css file
	 */
	function asset_loader($data = array())
	{
		$backtrace									= debug_backtrace();
		$pattern									= ('/' == DIRECTORY_SEPARATOR ? '/\/themes\/(.*?)\//' : '/\\\themes\\\(.*?)\\\/');
		$theme										= preg_match($pattern, (isset($backtrace[0]['file']) ? $backtrace[0]['file'] : null), $matched);
		$theme										= (isset($matched[1]) ? $matched[1] : null);
		
		if(!$theme) return false;
		
		if(!is_array($data))
		{
			$data									= array_map('trim', explode(',', $data));
		}
		
		$output										= null;
		
		foreach($data as $key => $val)
		{
			if(file_exists('../themes/' . $theme . '/' . $val))
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
}
