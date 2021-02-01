<?php

if(!function_exists('theme_url'))
{
	/**
	 * get the base theme url
	 */
	function theme_url($slug = null)
	{
		$template									= new \Aksara\Laboratory\Template();
		
		return base_url('themes/' . $template->get_theme() . '/' . $slug);
	}
}

if(!function_exists('asset_loader'))
{
	/**
	 * load additional css file
	 */
	function asset_loader($data = array())
	{
		$template									= new \Aksara\Laboratory\Template();
		
		if(!is_array($data))
		{
			$data									= array_map('trim', explode(',', $data));
		}
		
		$output										= null;
		
		foreach($data as $key => $val)
		{
			if(file_exists('../themes/' . $template->get_theme() . '/' . $val))
			{
				if('css' == strtolower(pathinfo($val, PATHINFO_EXTENSION)))
				{
					$output							.= "<link rel=\"stylesheet\" type=\"text/css\" href=\"" . theme_url($val) . "\" />\n";
				}
				else
				{
					$output							.= "<script type=\"text/javascript\" src=\"" . theme_url($val) . "\"></script>\n";
				}
			}
		}
		
		return $output;
	}
}
