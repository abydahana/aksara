<?php
/**
 * Theme Helper
 *
 * @author			Aby Dahana <abydahana@gmail.com>
 * @profile			abydahana.github.io
 * @website			www.aksaracms.com
 * @since			version 4.0.0
 * @copyright		(c) 2021 - Aksara Laboratory
 */

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
				return str_replace('/index.php/', '/', base_url('themes/' . $val['object']->_theme . '/' . $data));
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
		
		if($module)
		{
			if(file_exists(ROOTPATH . 'aksara' . DIRECTORY_SEPARATOR . 'Modules' . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . $data))
			{
				return base_url('modules/aksara/' . $module . '/assets/' . $data);
			}
			else if(file_exists(ROOTPATH . 'modules' . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . $data))
			{
				return base_url('modules/' . $module . '/assets/' . $data);
			}
		}
		
		return '#';
	}
	
	/**
	 * menu generator
	 */
	function generate_menu($menus = array(), $ul_class = 'navbar-nav', $li_class = 'nav-item', $a_class = 'nav-link', $toggle_class = 'dropdown-toggle', $toggle_initial = 'data-bs-toggle="dropdown"', $dropdown_class = 'dropdown', $sub_ul_class = 'dropdown-menu', $is_children = false, $level = 0)
	{
		$output										= null;
		
		foreach($menus as $key => $val)
		{
			if(isset($val['id']) && isset($val['label']) && isset($val['slug']))
			{
				if($val['slug'] == '---')
				{
					$output							.= '
						<li class="' . $li_class . (isset($val['class']) ? ' ' . $val['class'] : null) . '">
							<span class="' . $a_class . '">
								' . (isset($val['icon']) && $val['icon'] && ($val['slug'] != '---' || $val['label'] == 'Aksara ' . aksara('build_version'))  ? '<i class="' . $val['icon'] . '"></i>' : null) . '
								<b class="text-sm hide-on-collapse">
									' . ($val['label'] ? $val['label'] : null) . '
								</b>
							</span>
						</li>
					';
				}
				else
				{
					$segments						= service('uri')->getSegments();
					$slug							= $val['slug'];
					$children						= (isset($val['children']) && $val['children'] ? $val['children'] : array());
					
					if(preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $val['slug']))
					{
						$val['slug']				= $val['slug'] . '" target="_blank';
					}
					else
					{
						$val['slug']				= base_url($val['slug']);
					}
					
					$output							.= '
						<li class="' . $li_class . ($children && $dropdown_class ? ' ' . $dropdown_class : null) . ((!$children && isset($segments[$level]) && $segments[$level] == $slug) || $slug == service('uri')->getPath() || (service('uri')->getPath() && $slug == preg_replace(array('/\/create/', '/\/read/', '/\/update/'), '', service('uri')->getPath())) ? ' active' : '') . (isset($val['class']) ? ' ' . $val['class'] : null) . '">
							<a href="' . ($children ? '#' : $val['slug']) . '" class="' . $a_class . ($children ? ' ' . $toggle_class : null) . '"' . ($children ? ' ' . $toggle_initial : ' data-segmentation="' . preg_replace('/[^a-zA-Z0-9]/', '_', $slug) . '"') . (isset($val['newtab']) && $val['newtab'] && !$children ? ' target="_blank"' : '  data-bs-auto-close="outside"') . '>
								' . (isset($val['icon']) && $val['icon'] && ($val['slug'] != '---' || $val['label'] == 'Aksara ' . aksara('build_version'))  ? '<i class="' . $val['icon'] . '"></i>' : null) . '
								<span class="hide-on-collapse">
									' . $val['label'] . '
								</span>
							</a>
							' . ($children ? generate_menu($children, $ul_class, $li_class, $a_class, $toggle_class, $toggle_initial, $dropdown_class, $sub_ul_class, true, ($level + 1)) : null) . '
						</li>
					';
				}
			}
		}
		
		return '<ul class="' . ($is_children ? $sub_ul_class : $ul_class) . '">' . $output . '</ul>';
	}
}
