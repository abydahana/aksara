<?php

namespace Aksara\Laboratory;

/**
 * Template
 *
 * @author			Aby Dahana
 * @profile			abydahana.github.io
 * @website			www.aksaracms.com
 * @since			version 4.0.0
 * @copyright		(c) 2021 - Aksara Laboratory
 */

class Template
{
	private $_api_request;
	private $_css;
	private $_js;
	
	public function __construct($theme = 'frontend', $_api_request = null)
	{
		$this->model								= new \Aksara\Laboratory\Model();
		
		$this->_api_request							= $_api_request;
		$this->_theme								= $theme;
		
		if(!$this->_theme)
		{
			// throwback the default theme from site configuration
			$site_id								= get_setting('id');
			
			$this->_theme							= $this->model->select('frontend_theme')->get_where
			(
				'app__settings',
				array
				(
					'id'							=> $site_id
				),
				1
			)
			->row('frontend_theme');
		}
	}
	
	public function get_theme()
	{
		if(!in_array($this->_theme, array('frontend', 'backend'))) return false;
		
		$site_id									= get_setting('id');
		
		$query										= $this->model->select($this->_theme . '_theme')->get_where
		(
			'app__settings',
			array
			(
				'id'								=> $site_id
			),
			1
		)
		->row($this->_theme . '_theme');
		
		return $query;
	}
	
	/**
	 * Getting the theme property
	 */
	public function get_theme_property($parameter = null)
	{
		if(file_exists('../themes/' . $this->_theme . '/package.json') )
		{
			/**
			 * check if active theme has a property
			 */
			$property								= json_decode(@file_get_contents('../themes/' . $this->_theme . '/package.json'));
			
			if($parameter && isset($property->$parameter))
			{
				return $property->$parameter;
			}
			else
			{
				return $property;
			}
		}
		
		return false;
	}
	
	/**
	 * Scan the view file location both camelized string and lowercase
	 */
	public function get_view($view = 'index', $data = array(), $table = null)
	{
		// route namespace controller to view
		$base_view									= preg_replace('/modules\//i', '', ltrim(lcfirst(ltrim(str_replace('\\', '/', preg_replace('/\\\\Controllers\\\\/', '\Views\\', service('router')->controllerName(), 1) . '\\' . $view), '/')), 'aksara/'), 1);
		
		// get parent module classname
		$parent_module								= strtok($base_view, '/');
		
		// get current module classname
		$current_module								= substr(service('router')->controllerName(), strrpos(service('router')->controllerName(), '\\') + 1);
		
		// get current module classname
		$current_method								= (in_array($view, array('index', 'form', 'read')) ? service('router')->methodName() : $view);
		
		// strip view folder if parent current module matches with parent module
		$base_view									= str_replace('/Views/' . $parent_module . '/', '/Views/', $base_view);
		$base_view									= str_replace('/' . $current_module . '/' . $current_module . '/', '/' . $current_module . '/', $base_view);
		
		if(strtolower($current_module) == $view)
		{
			$base_view								= str_replace($current_module . '/' . $view, $view, $base_view);
		}
		
		/* add suffix to view to detect if mobile or modal template is sets */
		$suffix										= (service('request')->getUserAgent()->isMobile() ? '_mobile' : ('modal' == service('request')->getPost('prefer') ? '_modal' : (service('request')->getServer('GRID_VIEW') ? '_grid' : null)));
		
		// generate theme view
		$theme_view									= '../themes/' . $this->_theme . '/views/' . preg_replace('/\/Views\//', '/', $base_view, 1) . '.php';
		
		// generate module view
		$module_view								= '../modules/' . $base_view . '.php';
		
		// generate core view
		$core_view									= '../aksara/Modules/' . $base_view . '.php';
		
		/**
		 * -----------------------------------------------------------
		 * Theme based view
		 * -----------------------------------------------------------
		 */
		if(file_exists(preg_replace('~\/(?!.*\/)~', '/' . get_userdata('language') . '/', str_replace($view . $suffix . '.php', $current_method . $suffix . '.php', str_replace('.php', $suffix . '.php', $theme_view)))))
		{
			// view matches with method of active theme with language and suffix
			$this->_view							= '../' . preg_replace('~\/(?!.*\/)~', '/' . get_userdata('language') . '/', str_replace($view . $suffix . '.php', $current_method . $suffix . '.php', str_replace('.php', $suffix . '.php', $theme_view)));
		}
		else if(file_exists(preg_replace('~\/(?!.*\/)~', '/' . get_userdata('language') . '/', str_replace($view . '.php', $current_method . '.php', $theme_view))))
		{
			// view matches with method of active theme with language
			$this->_view							= '../' . preg_replace('~\/(?!.*\/)~', '/' . get_userdata('language') . '/', str_replace($view . '.php', $current_method . '.php', $theme_view));
		}
		else if(file_exists(preg_replace('~\/(?!.*\/)~', '/' . get_userdata('language') . '/', str_replace($view . $suffix . '.php', $current_method . $suffix . '.php', preg_replace_callback('/(\/views\/(.*))/', 'self::_strtolower_callback', str_replace('.php', $suffix . '.php', $theme_view), 1)))))
		{
			// view matches with method of active theme with language and suffix but in lowercase
			$this->_view							= '../' . preg_replace('~\/(?!.*\/)~', '/' . get_userdata('language') . '/', str_replace($view . $suffix . '.php', $current_method . $suffix . '.php', preg_replace_callback('/(\/views\/(.*))/', 'self::_strtolower_callback', str_replace('.php', $suffix . '.php', $theme_view), 1)));
		}
		else if(file_exists(preg_replace('~\/(?!.*\/)~', '/' . get_userdata('language') . '/', str_replace($view . '.php', $current_method . '.php', preg_replace_callback('/(\/views\/(.*))/', 'self::_strtolower_callback', $theme_view, 1)))))
		{
			// view matches with method of active theme with language but in lowercase
			$this->_view							= '../' . preg_replace('~\/(?!.*\/)~', '/' . get_userdata('language') . '/', str_replace($view . '.php', $current_method . '.php', preg_replace_callback('/(\/views\/(.*))/', 'self::_strtolower_callback', $theme_view, 1)));
		}
		else if(file_exists(str_replace($view . $suffix . '.php', $current_method . $suffix . '.php', str_replace('.php', $suffix . '.php', $theme_view))))
		{
			// view matches with method of active theme and suffix
			$this->_view							= '../' . str_replace($view . $suffix . '.php', $current_method . $suffix . '.php', str_replace('.php', $suffix . '.php', $theme_view));
		}
		else if(file_exists(str_replace($view . '.php', $current_method . '.php', $theme_view)))
		{
			// view matches with method of active theme
			$this->_view							= '../' . str_replace($view . '.php', $current_method . '.php', $theme_view);
		}
		else if(file_exists(str_replace($view . $suffix . '.php', $current_method . $suffix . '.php', preg_replace_callback('/(\/views\/(.*))/', 'self::_strtolower_callback', str_replace('.php', $suffix . '.php', $theme_view), 1))))
		{
			// view matches with method of active theme and suffix but in lowercase
			$this->_view							= '../' . str_replace($view . $suffix . '.php', $current_method . $suffix . '.php', preg_replace_callback('/(\/views\/(.*))/', 'self::_strtolower_callback', str_replace('.php', $suffix . '.php', $theme_view), 1));
		}
		else if(file_exists(str_replace($view . '.php', $current_method . '.php', preg_replace_callback('/(\/views\/(.*))/', 'self::_strtolower_callback', $theme_view, 1))))
		{
			// view matches with method of active theme but in lowercase
			$this->_view							= '../' . str_replace($view . '.php', $current_method . '.php', preg_replace_callback('/(\/views\/(.*))/', 'self::_strtolower_callback', $theme_view, 1));
		}
		else if(file_exists(preg_replace('~\/(?!.*\/)~', '/' . get_userdata('language') . '/', str_replace('.php', $suffix . '.php', $theme_view))))
		{
			// view matches with active theme with language and suffix
			$this->_view							= '../' . preg_replace('~\/(?!.*\/)~', '/' . get_userdata('language') . '/', str_replace('.php', $suffix . '.php', $theme_view));
		}
		else if(file_exists(preg_replace('~\/(?!.*\/)~', '/' . get_userdata('language') . '/', $theme_view)))
		{
			// view matches with active theme with language
			$this->_view							= '../' . preg_replace('~\/(?!.*\/)~', '/' . get_userdata('language') . '/', $theme_view);
		}
		else if(file_exists(preg_replace('~\/(?!.*\/)~', '/' . get_userdata('language') . '/', preg_replace_callback('/(\/views\/(.*))/', 'self::_strtolower_callback', str_replace('.php', $suffix . '.php', $theme_view), 1))))
		{
			// view matches with active theme with language and suffix but in lowercase
			$this->_view							= '../' . preg_replace('~\/(?!.*\/)~', '/' . get_userdata('language') . '/', preg_replace_callback('/(\/views\/(.*))/', 'self::_strtolower_callback', str_replace('.php', $suffix . '.php', $theme_view), 1));
		}
		else if(file_exists(preg_replace('~\/(?!.*\/)~', '/' . get_userdata('language') . '/', preg_replace_callback('/(\/views\/(.*))/', 'self::_strtolower_callback', $theme_view, 1))))
		{
			// view matches with active theme with language but in lowercase
			$this->_view							= '../' . preg_replace('~\/(?!.*\/)~', '/' . get_userdata('language') . '/', preg_replace_callback('/(\/views\/(.*))/', 'self::_strtolower_callback', $theme_view, 1));
		}
		else if(file_exists(str_replace('.php', $suffix . '.php', $theme_view)))
		{
			// view matches with active theme and suffix
			$this->_view							= '../' . str_replace('.php', $suffix . '.php', $theme_view);
		}
		else if(file_exists($theme_view))
		{
			// view matches with active theme
			$this->_view							= '../' . $theme_view;
		}
		else if(file_exists(preg_replace_callback('/(\/views\/(.*))/', 'self::_strtolower_callback', str_replace('.php', $suffix . '.php', $theme_view), 1)))
		{
			// view matches with active theme and suffix but in lowercase
			$this->_view							= '../' . preg_replace_callback('/(\/views\/(.*))/', 'self::_strtolower_callback', str_replace('.php', $suffix . '.php', $theme_view), 1);
		}
		else if(file_exists(preg_replace_callback('/(\/views\/(.*))/', 'self::_strtolower_callback', $theme_view, 1)))
		{
			// view matches with active theme but in lowercase
			$this->_view							= '../' . preg_replace_callback('/(\/views\/(.*))/', 'self::_strtolower_callback', $theme_view, 1);
		}
		else if(file_exists(preg_replace('~\/(?!.*\/)~', '/' . get_userdata('language') . '/', str_replace('.php', $suffix . '.php', $this->_class_view($theme_view)))))
		{
			// view matches with active theme with suffix and language
			$this->_view							= '../' . preg_replace('~\/(?!.*\/)~', '/' . get_userdata('language') . '/', str_replace('.php', $suffix . '.php', $this->_class_view($theme_view)));
		}
		else if(file_exists(preg_replace('~\/(?!.*\/)~', '/' . get_userdata('language') . '/', $this->_class_view($theme_view))))
		{
			// view matches with active theme with suffix and language
			$this->_view							= '../' . preg_replace('~\/(?!.*\/)~', '/' . get_userdata('language') . '/', $this->_class_view($theme_view));
		}
		else if(file_exists(preg_replace('~\/(?!.*\/)~', '/' . get_userdata('language') . '/', preg_replace_callback('/(\/views\/(.*))/', 'self::_strtolower_callback', str_replace('.php', $suffix . '.php', $this->_class_view($theme_view)), 1))))
		{
			// view matches with active theme with suffix and language but in lowercase
			$this->_view							= '../' . preg_replace('~\/(?!.*\/)~', '/' . get_userdata('language') . '/', preg_replace_callback('/(\/views\/(.*))/', 'self::_strtolower_callback', str_replace('.php', $suffix . '.php', $this->_class_view($theme_view)), 1));
		}
		else if(file_exists(preg_replace('~\/(?!.*\/)~', '/' . get_userdata('language') . '/', preg_replace_callback('/(\/Views\/(.*))/', 'self::_strtolower_callback', $this->_class_view($theme_view), 1))))
		{
			// view matches with active theme with suffix and language but in lowercase
			$this->_view							= '../' . preg_replace('~\/(?!.*\/)~', '/' . get_userdata('language') . '/', preg_replace_callback('/(\/views\/(.*))/', 'self::_strtolower_callback', $this->_class_view($theme_view), 1));
		}
		else if(file_exists(str_replace('.php', $suffix . '.php', $this->_class_view($theme_view))))
		{
			// view matches with active theme with suffix
			$this->_view							= '../' . str_replace('.php', $suffix . '.php', $this->_class_view($theme_view));
		}
		else if(file_exists($this->_class_view($theme_view)))
		{
			// view matches with active theme
			$this->_view							= '../' . $this->_class_view($theme_view);
		}
		else if(file_exists(preg_replace_callback('/(\/views\/(.*))/', 'self::_strtolower_callback', str_replace('.php', $suffix . '.php', $this->_class_view($theme_view)), 1)))
		{
			// view matches with active theme with suffix but in lowercase
			$this->_view							= '../' . preg_replace_callback('/(\/views\/(.*))/', 'self::_strtolower_callback', str_replace('.php', $suffix . '.php', $this->_class_view($theme_view)), 1);
		}
		else if(file_exists(preg_replace_callback('/(\/views\/(.*))/', 'self::_strtolower_callback', $this->_class_view($theme_view), 1)))
		{
			// view matches with active theme but in lowercase
			$this->_view							= '../' . preg_replace_callback('/(\/views\/(.*))/', 'self::_strtolower_callback', $this->_class_view($theme_view), 1);
		}
		
		/**
		 * -----------------------------------------------------------
		 * Module based view
		 * -----------------------------------------------------------
		 */
		else if(file_exists(preg_replace('~\/(?!.*\/)~', '/' . get_userdata('language') . '/', str_replace($view . $suffix . '.php', $current_method . $suffix . '.php', str_replace('.php', $suffix . '.php', $module_view)))))
		{
			// view matches with method of public module with language and suffix
			$this->_view							= '../' . preg_replace('~\/(?!.*\/)~', '/' . get_userdata('language') . '/', str_replace($view . $suffix . '.php', $current_method . $suffix . '.php', str_replace('.php', $suffix . '.php', $module_view)));
		}
		else if(file_exists(preg_replace('~\/(?!.*\/)~', '/' . get_userdata('language') . '/', str_replace($view . '.php', $current_method . '.php', $module_view))))
		{
			// view matches with method of public module with language
			$this->_view							= '../' . preg_replace('~\/(?!.*\/)~', '/' . get_userdata('language') . '/', str_replace($view . '.php', $current_method . '.php', $module_view));
		}
		else if(file_exists(preg_replace('~\/(?!.*\/)~', '/' . get_userdata('language') . '/', str_replace($view . $suffix . '.php', $current_method . $suffix . '.php', preg_replace_callback('/(\/Views\/(.*))/', 'self::_strtolower_callback', str_replace('.php', $suffix . '.php', $module_view), 1)))))
		{
			// view matches with method of public module with language and suffix but in lowercase
			$this->_view							= '../' . preg_replace('~\/(?!.*\/)~', '/' . get_userdata('language') . '/', str_replace($view . $suffix . '.php', $current_method . $suffix . '.php', preg_replace_callback('/(\/Views\/(.*))/', 'self::_strtolower_callback', str_replace('.php', $suffix . '.php', $module_view), 1)));
		}
		else if(file_exists(preg_replace('~\/(?!.*\/)~', '/' . get_userdata('language') . '/', str_replace($view . '.php', $current_method . '.php', preg_replace_callback('/(\/Views\/(.*))/', 'self::_strtolower_callback', $module_view, 1)))))
		{
			// view matches with method of public module with language but in lowercase
			$this->_view							= '../' . preg_replace('~\/(?!.*\/)~', '/' . get_userdata('language') . '/', str_replace($view . '.php', $current_method . '.php', preg_replace_callback('/(\/Views\/(.*))/', 'self::_strtolower_callback', $module_view, 1)));
		}
		else if(file_exists(str_replace($view . $suffix . '.php', $current_method . $suffix . '.php', str_replace('.php', $suffix . '.php', $module_view))))
		{
			// view matches with method of public module and suffix
			$this->_view							= '../' . str_replace($view . $suffix . '.php', $current_method . $suffix . '.php', str_replace('.php', $suffix . '.php', $module_view));
		}
		else if(file_exists(str_replace($view . '.php', $current_method . '.php', $module_view)))
		{
			// view matches with method of public module
			$this->_view							= '../' . str_replace($view . '.php', $current_method . '.php', $module_view);
		}
		else if(file_exists(str_replace($view . $suffix . '.php', $current_method . $suffix . '.php', preg_replace_callback('/(\/Views\/(.*))/', 'self::_strtolower_callback', str_replace('.php', $suffix . '.php', $module_view), 1))))
		{
			// view matches with method of public module and suffix but in lowercase
			$this->_view							= '../' . str_replace($view . $suffix . '.php', $current_method . $suffix . '.php', preg_replace_callback('/(\/Views\/(.*))/', 'self::_strtolower_callback', str_replace('.php', $suffix . '.php', $module_view), 1));
		}
		else if(file_exists(str_replace($view . '.php', $current_method . '.php', preg_replace_callback('/(\/Views\/(.*))/', 'self::_strtolower_callback', $module_view, 1))))
		{
			// view matches with method of public module but in lowercase
			$this->_view							= '../' . str_replace($view . '.php', $current_method . '.php', preg_replace_callback('/(\/Views\/(.*))/', 'self::_strtolower_callback', $module_view, 1));
		}
		else if(file_exists(preg_replace('~\/(?!.*\/)~', '/' . get_userdata('language') . '/', str_replace('.php', $suffix . '.php', $module_view))))
		{
			// view matches with public module with language and suffix
			$this->_view							= '../' . preg_replace('~\/(?!.*\/)~', '/' . get_userdata('language') . '/', str_replace('.php', $suffix . '.php', $module_view));
		}
		else if(file_exists(preg_replace('~\/(?!.*\/)~', '/' . get_userdata('language') . '/', $module_view)))
		{
			// view matches with public module with language
			$this->_view							= '../' . preg_replace('~\/(?!.*\/)~', '/' . get_userdata('language') . '/', $module_view);
		}
		else if(file_exists(preg_replace('~\/(?!.*\/)~', '/' . get_userdata('language') . '/', preg_replace_callback('/(\/Views\/(.*))/', 'self::_strtolower_callback', str_replace('.php', $suffix . '.php', $module_view), 1))))
		{
			// view matches with public module with language and suffix but in lowercase
			$this->_view							= '../' . preg_replace('~\/(?!.*\/)~', '/' . get_userdata('language') . '/', preg_replace_callback('/(\/Views\/(.*))/', 'self::_strtolower_callback', str_replace('.php', $suffix . '.php', $module_view), 1));
		}
		else if(file_exists(preg_replace('~\/(?!.*\/)~', '/' . get_userdata('language') . '/', preg_replace_callback('/(\/Views\/(.*))/', 'self::_strtolower_callback', $module_view, 1))))
		{
			// view matches with public module with language but in lowercase
			$this->_view							= '../' . preg_replace('~\/(?!.*\/)~', '/' . get_userdata('language') . '/', preg_replace_callback('/(\/Views\/(.*))/', 'self::_strtolower_callback', $module_view, 1));
		}
		else if(file_exists(str_replace('.php', $suffix . '.php', $module_view)))
		{
			// view matches with public module and suffix
			$this->_view							= '../' . str_replace('.php', $suffix . '.php', $module_view);
		}
		else if(file_exists($module_view))
		{
			// view matches with public module
			$this->_view							= '../' . $module_view;
		}
		else if(file_exists(preg_replace_callback('/(\/Views\/(.*))/', 'self::_strtolower_callback', str_replace('.php', $suffix . '.php', $module_view), 1)))
		{
			// view matches with public module and suffix but in lowercase
			$this->_view							= '../' . preg_replace_callback('/(\/Views\/(.*))/', 'self::_strtolower_callback', str_replace('.php', $suffix . '.php', $module_view), 1);
		}
		else if(file_exists(preg_replace_callback('/(\/Views\/(.*))/', 'self::_strtolower_callback', $module_view, 1)))
		{
			// view matches with public module but in lowercase
			$this->_view							= '../' . preg_replace_callback('/(\/Views\/(.*))/', 'self::_strtolower_callback', $module_view, 1);
		}
		else if(file_exists(preg_replace('~\/(?!.*\/)~', '/' . get_userdata('language') . '/', str_replace('.php', $suffix . '.php', $this->_class_view($module_view)))))
		{
			// view matches with public module with suffix and language
			$this->_view							= '../' . preg_replace('~\/(?!.*\/)~', '/' . get_userdata('language') . '/', str_replace('.php', $suffix . '.php', $this->_class_view($module_view)));
		}
		else if(file_exists(preg_replace('~\/(?!.*\/)~', '/' . get_userdata('language') . '/', $this->_class_view($module_view))))
		{
			// view matches with public module and language
			$this->_view							= '../' . preg_replace('~\/(?!.*\/)~', '/' . get_userdata('language') . '/', $this->_class_view($module_view));
		}
		else if(file_exists(preg_replace('~\/(?!.*\/)~', '/' . get_userdata('language') . '/', preg_replace_callback('/(\/Views\/(.*))/', 'self::_strtolower_callback', str_replace('.php', $suffix . '.php', $this->_class_view($module_view)), 1))))
		{
			// view matches with public module with suffix and language but in lowercase
			$this->_view							= '../' . preg_replace('~\/(?!.*\/)~', '/' . get_userdata('language') . '/', preg_replace_callback('/(\/Views\/(.*))/', 'self::_strtolower_callback', str_replace('.php', $suffix . '.php', $this->_class_view($module_view)), 1));
		}
		else if(file_exists(preg_replace('~\/(?!.*\/)~', '/' . get_userdata('language') . '/', preg_replace_callback('/(\/Views\/(.*))/', 'self::_strtolower_callback', $this->_class_view($module_view), 1))))
		{
			// view matches with public module and language but in lowercase
			$this->_view							= '../' . preg_replace('~\/(?!.*\/)~', '/' . get_userdata('language') . '/', preg_replace_callback('/(\/Views\/(.*))/', 'self::_strtolower_callback', $this->_class_view($module_view), 1));
		}
		else if(file_exists(str_replace('.php', $suffix . '.php', $this->_class_view($module_view))))
		{
			// view matches with public module with suffix
			$this->_view							= '../' . str_replace('.php', $suffix . '.php', $this->_class_view($module_view));
		}
		else if(file_exists($this->_class_view($module_view)))
		{
			// view matches with public module
			$this->_view							= '../' . $this->_class_view($module_view);
		}
		else if(file_exists(preg_replace_callback('/(\/Views\/(.*))/', 'self::_strtolower_callback', str_replace('.php', $suffix . '.php', $this->_class_view($module_view)), 1)))
		{
			// view matches with public module with suffix but in lowercase
			$this->_view							= '../' . preg_replace_callback('/(\/Views\/(.*))/', 'self::_strtolower_callback', str_replace('.php', $suffix . '.php', $this->_class_view($module_view)), 1);
		}
		else if(file_exists(preg_replace_callback('/(\/Views\/(.*))/', 'self::_strtolower_callback', $this->_class_view($module_view), 1)))
		{
			// view matches with public module but in lowercase
			$this->_view							= '../' . preg_replace_callback('/(\/Views\/(.*))/', 'self::_strtolower_callback', $this->_class_view($module_view), 1);
		}
		
		/**
		 * -----------------------------------------------------------
		 * Core module based view
		 * -----------------------------------------------------------
		 */
		else if(file_exists(preg_replace('~\/(?!.*\/)~', '/' . get_userdata('language') . '/', str_replace($view . $suffix . '.php', $current_method . $suffix . '.php', str_replace('.php', $suffix . '.php', $core_view)))))
		{
			// view matches with method of core module with language and suffix
			$this->_view							= '../' . preg_replace('~\/(?!.*\/)~', '/' . get_userdata('language') . '/', str_replace($view . $suffix . '.php', $current_method . $suffix . '.php', str_replace('.php', $suffix . '.php', $core_view)));
		}
		else if(file_exists(preg_replace('~\/(?!.*\/)~', '/' . get_userdata('language') . '/', str_replace($view . '.php', $current_method . '.php', $core_view))))
		{
			// view matches with method of core module with language
			$this->_view							= '../' . preg_replace('~\/(?!.*\/)~', '/' . get_userdata('language') . '/', str_replace($view . '.php', $current_method . '.php', $core_view));
		}
		else if(file_exists(preg_replace('~\/(?!.*\/)~', '/' . get_userdata('language') . '/', str_replace($view . $suffix . '.php', $current_method . $suffix . '.php', preg_replace_callback('/(\/Views\/(.*))/', 'self::_strtolower_callback', str_replace('.php', $suffix . '.php', $core_view), 1)))))
		{
			// view matches with method of core module with language and suffix but in lowercase
			$this->_view							= '../' . preg_replace('~\/(?!.*\/)~', '/' . get_userdata('language') . '/', str_replace($view . $suffix . '.php', $current_method . $suffix . '.php', preg_replace_callback('/(\/Views\/(.*))/', 'self::_strtolower_callback', str_replace('.php', $suffix . '.php', $core_view), 1)));
		}
		else if(file_exists(preg_replace('~\/(?!.*\/)~', '/' . get_userdata('language') . '/', str_replace($view . '.php', $current_method . '.php', preg_replace_callback('/(\/Views\/(.*))/', 'self::_strtolower_callback', $core_view, 1)))))
		{
			// view matches with method of core module with language but in lowercase
			$this->_view							= '../' . preg_replace('~\/(?!.*\/)~', '/' . get_userdata('language') . '/', str_replace($view . '.php', $current_method . '.php', preg_replace_callback('/(\/Views\/(.*))/', 'self::_strtolower_callback', $core_view, 1)));
		}
		else if(file_exists(str_replace($view . $suffix . '.php', $current_method . $suffix . '.php', str_replace('.php', $suffix . '.php', $core_view))))
		{
			// view matches with method of core module and suffix
			$this->_view							= '../' . str_replace($view . $suffix . '.php', $current_method . $suffix . '.php', str_replace('.php', $suffix . '.php', $core_view));
		}
		else if(file_exists(str_replace($view . '.php', $current_method . '.php', $core_view)))
		{
			// view matches with method of core module
			$this->_view							= '../' . str_replace($view . '.php', $current_method . '.php', $core_view);
		}
		else if(file_exists(str_replace($view . $suffix . '.php', $current_method . $suffix . '.php', preg_replace_callback('/(\/Views\/(.*))/', 'self::_strtolower_callback', str_replace('.php', $suffix . '.php', $core_view), 1))))
		{
			// view matches with method of core module and suffix but in lowercase
			$this->_view							= '../' . str_replace($view . $suffix . '.php', $current_method . $suffix . '.php', preg_replace_callback('/(\/Views\/(.*))/', 'self::_strtolower_callback', str_replace('.php', $suffix . '.php', $core_view), 1));
		}
		else if(file_exists(str_replace($view . '.php', $current_method . '.php', preg_replace_callback('/(\/Views\/(.*))/', 'self::_strtolower_callback', $core_view, 1))))
		{
			// view matches with method of core module but in lowercase
			$this->_view							= '../' . str_replace($view . '.php', $current_method . '.php', preg_replace_callback('/(\/Views\/(.*))/', 'self::_strtolower_callback', $core_view, 1));
		}
		else if(file_exists(preg_replace('~\/(?!.*\/)~', '/' . get_userdata('language') . '/', str_replace('.php', $suffix . '.php', $core_view))))
		{
			// view matches with core module with language and suffix
			$this->_view							= '../' . preg_replace('~\/(?!.*\/)~', '/' . get_userdata('language') . '/', str_replace('.php', $suffix . '.php', $core_view));
		}
		else if(file_exists(preg_replace('~\/(?!.*\/)~', '/' . get_userdata('language') . '/', $core_view)))
		{
			// view matches with core module with language
			$this->_view							= '../' . preg_replace('~\/(?!.*\/)~', '/' . get_userdata('language') . '/', $core_view);
		}
		else if(file_exists(preg_replace('~\/(?!.*\/)~', '/' . get_userdata('language') . '/', preg_replace_callback('/(\/Views\/(.*))/', 'self::_strtolower_callback', str_replace('.php', $suffix . '.php', $core_view), 1))))
		{
			// view matches with core module with language and suffix but in lowercase
			$this->_view							= '../' . preg_replace('~\/(?!.*\/)~', '/' . get_userdata('language') . '/', preg_replace_callback('/(\/Views\/(.*))/', 'self::_strtolower_callback', str_replace('.php', $suffix . '.php', $core_view), 1));
		}
		else if(file_exists(preg_replace('~\/(?!.*\/)~', '/' . get_userdata('language') . '/', preg_replace_callback('/(\/Views\/(.*))/', 'self::_strtolower_callback', $core_view, 1))))
		{
			// view matches with core module with language but in lowercase
			$this->_view							= '../' . preg_replace('~\/(?!.*\/)~', '/' . get_userdata('language') . '/', preg_replace_callback('/(\/Views\/(.*))/', 'self::_strtolower_callback', $core_view, 1));
		}
		else if(file_exists(str_replace('.php', $suffix . '.php', $core_view)))
		{
			// view matches with core module and suffix
			$this->_view							= '../' . str_replace('.php', $suffix . '.php', $core_view);
		}
		else if(file_exists($core_view))
		{
			// view matches with core module
			$this->_view							= '../' . $core_view;
		}
		else if(file_exists(preg_replace_callback('/(\/Views\/(.*))/', 'self::_strtolower_callback', $core_view, 1)))
		{
			// view matches with core module but in lowercase
			$this->_view							= '../' . preg_replace_callback('/(\/Views\/(.*))/', 'self::_strtolower_callback', $core_view, 1);
		}
		else if(file_exists(preg_replace('~\/(?!.*\/)~', '/' . get_userdata('language') . '/', str_replace('.php', $suffix . '.php', $this->_class_view($core_view)))))
		{
			// view matches with core module with suffix and language
			$this->_view							= '../' . preg_replace('~\/(?!.*\/)~', '/' . get_userdata('language') . '/', str_replace('.php', $suffix . '.php', $this->_class_view($core_view)));
		}
		else if(file_exists(preg_replace('~\/(?!.*\/)~', '/' . get_userdata('language') . '/', $this->_class_view($core_view))))
		{
			// view matches with core module and language
			$this->_view							= '../' . preg_replace('~\/(?!.*\/)~', '/' . get_userdata('language') . '/', $this->_class_view($core_view));
		}
		else if(file_exists(preg_replace('~\/(?!.*\/)~', '/' . get_userdata('language') . '/', preg_replace_callback('/(\/Views\/(.*))/', 'self::_strtolower_callback', str_replace('.php', $suffix . '.php', $this->_class_view($core_view)), 1))))
		{
			// view matches with core module with suffix and language but in lowercase
			$this->_view							= '../' . preg_replace('~\/(?!.*\/)~', '/' . get_userdata('language') . '/', preg_replace_callback('/(\/Views\/(.*))/', 'self::_strtolower_callback', str_replace('.php', $suffix . '.php', $this->_class_view($core_view)), 1));
		}
		else if(file_exists(preg_replace('~\/(?!.*\/)~', '/' . get_userdata('language') . '/', preg_replace_callback('/(\/Views\/(.*))/', 'self::_strtolower_callback', $this->_class_view($core_view), 1))))
		{
			// view matches with core module and language but in lowercase
			$this->_view							= '../' . preg_replace('~\/(?!.*\/)~', '/' . get_userdata('language') . '/', preg_replace_callback('/(\/Views\/(.*))/', 'self::_strtolower_callback', $this->_class_view($core_view), 1));
		}
		else if(file_exists(str_replace('.php', $suffix . '.php', $this->_class_view($core_view))))
		{
			// view matches with core module with suffix
			$this->_view							= '../' . str_replace('.php', $suffix . '.php', $this->_class_view($core_view));
		}
		else if(file_exists($this->_class_view($core_view)))
		{
			// view matches with core module
			$this->_view							= '../' . $this->_class_view($core_view);
		}
		else if(file_exists(preg_replace_callback('/(\/Views\/(.*))/', 'self::_strtolower_callback', str_replace('.php', $suffix . '.php', $this->_class_view($core_view)), 1)))
		{
			// view matches with core module with suffix but in lowercase
			$this->_view							= '../' . preg_replace_callback('/(\/Views\/(.*))/', 'self::_strtolower_callback', str_replace('.php', $suffix . '.php', $this->_class_view($core_view)), 1);
		}
		else if(file_exists(preg_replace_callback('/(\/Views\/(.*))/', 'self::_strtolower_callback', $this->_class_view($core_view), 1)))
		{
			// view matches with core module but in lowercase
			$this->_view							= '../' . preg_replace_callback('/(\/Views\/(.*))/', 'self::_strtolower_callback', $this->_class_view($core_view), 1);
		}
		
		/**
		 * -----------------------------------------------------------
		 * Template based view
		 * -----------------------------------------------------------
		 */
		else
		{
			// no matches view, use template instead
			if(service('router')->getMatchedRoute())
			{
				if($data && $table)
				{
					if(file_exists(APPPATH . 'Views/templates/' . $view . $suffix . '.php'))
					{
						// view matches with suffix
						$this->_view				= 'templates/' . $view . $suffix;
					}
					else
					{
						$this->_view				= 'templates/' . $view;
					}
				}
				
				// no view were found
				else
				{
					// check request mode
					if(in_array($view, array('export', 'print', 'pdf')))
					{
						// export mode
						$this->_view				= 'templates/export';
					}
					else
					{
						// no mode
						$this->_view				= 'templates/error';
					}
				}
			}
			
			// no route were found
			else
			{
				$this->_view						= 'templates/404';
			}
		}
		
		// remove duplicate slash
		$this->_view								= str_replace('//', '/', $this->_view);
		
		return $this->_view;
	}
	
	public function build($view = null, $data = array(), $breadcrumb = array(), $table = null, $language = null)
	{
		if(!$data)
		{
			$data									= new \stdClass();
		}
		
		if(isset($data->pagination))
		{
			$data->template							= (object) array
			(
				'pagination'						=> $this->pagination($data->pagination)
			);
		}
		
		/**
		 * Build the result and send to client
		 */
		$this->_view								= $this->get_view($view, $data, $table);
		
		if((service('request')->isAJAX() && stripos(service('request')->getServer('HTTP_REFERER'), service('request')->getServer('SERVER_NAME')) !== false) || $this->_api_request)
		{
			$data->html								= preg_replace('/<!--(.|\s)*?-->/', '', view($this->_view, (array) $data));
			
			unset($data->template, $data->pagination);
			
			/* make a backup of "pre" tag */
			preg_match_all('#\<pre.*\>(.*)\<\/pre\>#Uis', $data->html, $pre_backup);
			$data->html								= str_replace($pre_backup[0], array_map(function($element){return '<pre>' . $element . '</pre>';}, array_keys($pre_backup[0])), $data->html);
			
			$data->html								= preg_replace(array('/[ \t]+/', '/\>[^\S ]+/s', '/[^\S ]+\</s', '/(\>)\s*(\<)/m'), array(' ', '>', '<', '$1$2'), $data->html);
			
			/* rollback the pre tag */
			$data->html								= str_replace(array_map(function($element){return '<pre>' . $element . '</pre>';}, array_keys($pre_backup[0])), $pre_backup[0], $data->html);
			
			return make_json($data);
		}
		else
		{
			$content								= preg_replace('/<!--(.|\s)*?-->/', '', view($this->_view, (array) $data));
			
			$output									= array
			(
				'template'							=> (object) array
				(
					'meta'							=> (object) array
					(
						'title'						=> truncate($data->meta->title),
						'description'				=> truncate($data->meta->description)
					),
					'menu'							=> $this->_get_menu(),
					'breadcrumb'					=> (object) array
					(
					),
					'content'						=> $content
				)
			);
			
			$output									= $this->_minify(view('../../themes/' . $this->_theme . '/layout', $output));
			
			return service('response')->setBody($output)->send();
		}
	}
	
	public function breadcrumb($data = array(), $title = null, $found = null)
	{
		$slug										= null;
		$checker									= service('request')->uri->getSegments();
		$params										= service('request')->getGet();
		$params['per_page']							= null;
		$params['q']								= null;
		$params['order']							= null;
		$params['sort']								= null;
		
		if(!$data || !is_array($data))
		{
			foreach($checker as $key => $val)
			{
				$data[$val]							= array
				(
					'label'							=> $val,
					'translated'					=> false
				);
			}
		}
		
		$current_slug								= end($checker);
		$slug										= null;
		
		if($this->get_theme_property('type') == 'backend')
		{
			$output									= array
			(
				array
				(
					'url'							=> base_url('dashboard'),
					'label'							=> phrase('dashboard'),
					'icon'							=> 'mdi mdi-home'
				)
			);
		}
		else
		{
			$output									= array
			(
				array
				(
					'url'							=> base_url(),
					'label'							=> phrase('homepage'),
					'icon'							=> 'mdi mdi-home'
				)
			);
		}
		
		foreach($data as $key => $val)
		{
			$slug									.= $key . '/';
			
			if($key && isset($val['label']))
			{
				if($key != $current_slug)
				{
					$output[]						= array
					(
						'url'						=> base_url($slug, $params),
						'label'						=> ($found ? ($val['translated'] ? $val['label'] : phrase($val['label'], true, true)) : ucwords(str_replace('_', ' ', $val['label']))),
						'icon'						=> null
					);
				}
			}
		}
		
		$output[]									= array
		(
			'url'									=> '',
			'label'									=> $title,
			'icon'									=> ''
		);
		
		/*  remove the last element of array */
		array_pop($output);
		
		return $output;
	}
	
	public function pagination($data = array(), $return = true)
	{
		if(!$data)
		{
			$data									= new \stdClass();
		}
		
		if(is_array($data))
		{
			$data									= (object) $data;
		}
		
		if(!isset($data->total_rows))
		{
			/* if there's no result, set to 0 */
			$data->total_rows						= 0;
		}
		
		if(!isset($data->per_page))
		{
			/* if no per_page setting, set default */
			$data->per_page							= 25;
		}
		
		if(!isset($data->offset))
		{
			/* if there's no offset, set to 0 */
			$data->offset							= 0;
		}
		
		$this->pagination							= \Config\Services::pager();
		
		/* create result */
		$last_page									= ($data->total_rows > $data->per_page ? (int) ceil($data->total_rows / $data->per_page) : 1);
		$results									= $this->pagination->makeLinks(1, $data->per_page, $data->total_rows, 'pagination');
		
		if($results)
		{
			$output									= $results;
		}
		else
		{
			$output									= '
				<ul class="pagination pagination-sm">
					<li class="page-item disabled">
						<a href="javascript:void(0)" tabindex="-1" class="page-link">
							Previous
						</a>
					</li>
					<li class="page-item active">
						<a href="javascript:void(0)" class="page-link">
							1
						</a>
					</li>
					<li class="page-item disabled">
						<a href="javascript:void(0)" class="page-link">
							Next
						</a>
					</li>
				</ul>
			';
		}
		
		if($return)
		{
			$query_string							= null;
			
			foreach(service('request')->getGet() as $key => $val)
			{
				$key								= preg_replace('/[^\w-]/', '', $key);
				
				if(!$key || in_array($key, array('q', 'per_page'))) continue;
				
				$query_string						.= '<input type="hidden" name="' . $key . '" value="' . htmlspecialchars($val) . '" />';
			}
			
			$output									= '
				<div class="row">
					<div class="col-sm-6 text-center text-sm-left">
						<label class="text-muted mb-0 pt-1">
							<small class="result-for">
								<i class="mdi mdi-information-outline"></i>
								&nbsp;
								' . phrase('showing') . ' ' . ($data->offset ? number_format($data->offset) : number_format(($data->total_rows > 0 ? 1 : 0))) . ' - ' . (($data->offset + $data->per_page) < $data->total_rows ? number_format(($data->offset + $data->per_page)) : number_format($data->total_rows)) . ' ' . phrase('of') . ' ' . number_format($data->total_rows) . ' ' . ($data->total_rows > 1 ? phrase('entries_found') : phrase('entry_found')) . '
							</small>
						</label>
					</div>
					<div class="col-sm-6">
						<nav class="d-flex justify-content-center justify-content-sm-end justify-content-md-end justify-content-lg-end justify-content-xl-end" aria-label="Page navigation">
							' . $output . '
							<form action="' . current_page(null, array('per_page' => null)) . '" method="POST" class="--xhr-form ml-2 d-none d-sm-none d-md-block d-lg-block d-xl-block">
								' . $query_string . '
								<div class="input-group">
									<select name="limit" class="form-control form-control-sm">
										<option value="' . $data->limit . '"' . (!$data->per_page ? ' selected' : null) . '>' . $data->limit . '</option>
										<option value="' . ($data->limit * 2) . '"' . (($data->limit * 2) == $data->per_page ? ' selected' : null) . '>' . ($data->limit * 2) . '</option>
										<option value="' . ($data->limit * 4) . '"' . (($data->limit * 4) == $data->per_page ? ' selected' : null) . '>' . ($data->limit * 4) . '</option>
										<option value="' . ($data->limit * 8) . '"' . (($data->limit * 8) == $data->per_page ? ' selected' : null) . '>' . ($data->limit * 8) . '</option>
										<option value="' . ($data->limit * 20) . '"' . (($data->limit * 20) == $data->per_page ? ' selected' : null) . '>' . ($data->limit * 20) . '</option>
									</select>
									<input type="number" name="per_page" class="form-control form-control-sm text-center" value="' . (is_numeric(service('request')->getGet('per_page')) && service('request')->getGet('per_page') ? service('request')->getGet('per_page') : 1) . '" min="1" max="' . $last_page . '" />
									<div class="input-group-append">
										<button type="submit" class="btn btn-sm btn-primary">
											' . phrase('go') . '
										</button>
									</div>
								</div>
							</form>
						</nav>
					</div>
				</div>
			';
		}
		else
		{
			$dom									= new \Aksara\Libraries\Html_dom;
			$html									= $dom->str_get_html($output);
			$output									= array
			(
				'url'								=> current_page(null, array('per_page' => null)),
				'limit'								=> $data->limit,
				'per_page'							=> $data->per_page,
				'last_page'							=> $last_page,
				'text'								=> phrase('showing') . ' ' . ($data->offset ? number_format($data->offset) : number_format(($data->total_rows > 0 ? 1 : 0))) . ' - ' . (($data->offset + $data->per_page) < $data->total_rows ? number_format(($data->offset + $data->per_page)) : number_format($data->total_rows)) . ' ' . phrase('of') . ' ' . number_format($data->total_rows) . ' ' . ($data->total_rows > 1 ? phrase('entries_found') : phrase('entry_found'))
			);
			
			foreach($html->find('ul li') as $li)
			{
				$output['results'][]				= array
				(
					'parentClass'					=> $li->class,
					'class'							=> $li->find('a', 0)->class,
					'href'							=> $li->find('a', 0)->href,
					'label'							=> str_replace('&amp;', '&', htmlspecialchars($li->find('a', 0)->innertext))
				);
			}
			
			return $output;
		}
		
		return $output;
	}
	
	private function _class_view($view = null)
	{
		$view										= substr($view, 0, strrpos($view, '/'));
		$view										= substr($view, 0, strrpos($view, '/')) . '/' . strtolower(substr($view, strrpos($view, '/') + 1)) . '.php';
		
		return $view;
	}
	
	private function _get_menu($placement = null, $additional_class = null, $menu = array(), $visible_menus = array(), $rendered = false, $level = 0)
	{
		if('frontend' == $this->get_theme_property('type'))
		{
			$placement								= 'header';
		}
		else
		{
			$placement								= 'sidebar';
		}
		
		if(!$menu && !$rendered)
		{
			$is_children							= false;
			
			$user_id								= get_userdata('user_id');
			$group_id								= get_userdata('group_id');
			
			if(get_userdata('is_logged'))
			{
				$menu								= $this->model->select
				('
					serialized_data
				')
				->get_where
				(
					'app__menus',
					array
					(
						'menu_placement'			=> $placement,
						'group_id'					=> $group_id
					),
					1
				)
				->row('serialized_data');
				
				$visible_menus						= $this->model->select
				('
					visible_menu
				')
				->get_where
				(
					'app__users_privileges',
					array
					(
						'user_id'					=> $user_id
					),
					1
				)
				->row('visible_menu');
				
				$visible_menus						= ($visible_menus ? json_decode($visible_menus, true) : array());
				
				if(!$menu)
				{
					$menu							= $this->model->select
					('
						serialized_data
					')
					->get_where
					(
						'app__menus',
						array
						(
							'menu_placement'		=> $placement,
							'group_id'				=> 0
						), 
						1
					)
					->row('serialized_data');
				}
			}
			else
			{
				$visible_menus						= array();
				
				$menu								= $this->model->select
				('
					serialized_data
				')
				->get_where
				(
					'app__menus',
					array
					(
						'menu_placement'			=> $placement,
						'group_id'					=> 0
					),
					1
				)
				->row('serialized_data');
			}
			
			$menu									= ($menu ? json_decode($menu, true) : array());
			
			$main_menu								= array
			(
				array
				(
					'id'							=> 0,
					'label'							=> '',
					'slug'							=> '---'
				),
				array
				(
					'id'							=> 0,
					'label'							=> 'CMS',
					'slug'							=> 'cms',
					'icon'							=> 'mdi mdi-dropbox',
					'children'						=> array
					(
						array
						(
							'id'					=> 0,
							'label'					=> 'Blogs',
							'slug'					=> 'cms/blogs',
							'icon'					=> 'mdi mdi-newspaper',
							'children'				=> array
							(
								array
								(
									'id'			=> 0,
									'label'			=> 'Posts',
									'slug'			=> 'cms/blogs',
									'icon'			=> 'mdi mdi-pencil'
								),
								array
								(
									'id'			=> 0,
									'label'			=> 'Categories',
									'slug'			=> 'cms/blogs/categories',
									'icon'			=> 'mdi mdi-sitemap'
								)
							)
						),
						array
						(
							'id'					=> 0,
							'label'					=> 'Pages',
							'slug'					=> 'cms/pages',
							'icon'					=> 'mdi mdi-book-open-page-variant'
						),
						array
						(
							'id'					=> 0,
							'label'					=> 'Galleries',
							'slug'					=> 'cms/galleries',
							'icon'					=> 'mdi mdi-folder-multiple-image'
						),
						array
						(
							'id'					=> 0,
							'label'					=> 'Peoples',
							'slug'					=> 'cms/peoples',
							'icon'					=> 'mdi mdi-account-group-outline'
						),
						array
						(
							'id'					=> 0,
							'label'					=> 'Partial Content',
							'slug'					=> 'cms/partials',
							'icon'					=> 'mdi mdi-file-image',
							'children'				=> array
							(
								array
								(
									'id'			=> 0,
									'label'			=> 'Carousels',
									'slug'			=> 'cms/partials/carousels',
									'icon'			=> 'mdi mdi-image-multiple'
								),
								array
								(
									'id'			=> 0,
									'label'			=> 'FAQs',
									'slug'			=> 'cms/partials/faqs',
									'icon'			=> 'mdi mdi-file-question'
								),
								array
								(
									'id'			=> 0,
									'label'			=> 'Announcements',
									'slug'			=> 'cms/partials/announcements',
									'icon'			=> 'mdi mdi-bullhorn-outline'
								),
								array
								(
									'id'			=> 0,
									'label'			=> 'Testimonials',
									'slug'			=> 'cms/partials/testimonials',
									'icon'			=> 'mdi mdi-comment-account-outline'
								),
								array
								(
									'id'			=> 0,
									'label'			=> 'Inquiries',
									'slug'			=> 'cms/partials/inquiries',
									'icon'			=> 'mdi mdi-message-text'
								),
								array
								(
									'id'			=> 0,
									'label'			=> 'Media',
									'slug'			=> 'cms/partials/media',
									'icon'			=> 'mdi mdi-folder-image'
								)
							)
						)
					)
				),
				array
				(
					'id'							=> 0,
					'label'							=> 'Core Tools',
					'slug'							=> '---'
				),
				array
				(
					'id'							=> 0,
					'label'							=> 'Administrative',
					'slug'							=> 'administrative',
					'icon'							=> 'mdi mdi-cogs',
					'children'						=> array
					(
						array
						(
							'id'					=> 0,
							'label'					=> 'Users and Groups',
							'slug'					=> 'administrative/users',
							'icon'					=> 'mdi mdi-account-group-outline',
							'children'				=> array
							(
								array
								(
									'id'			=> 0,
									'label'			=> 'Users',
									'slug'			=> 'administrative/users',
									'icon'			=> 'mdi mdi-account-group'
								),
								array
								(
									'id'			=> 0,
									'label'			=> 'Groups',
									'slug'			=> 'administrative/groups',
									'icon'			=> 'mdi mdi-sitemap'
								),
								array
								(
									'id'			=> 0,
									'label'			=> 'Privileges',
									'slug'			=> 'administrative/groups/privileges',
									'icon'			=> 'mdi mdi-account-check-outline'
								)
							)
						),
						array
						(
							'id'					=> 0,
							'label'					=> 'Configurations',
							'slug'					=> 'administrative',
							'icon'					=> 'mdi mdi-wrench-outline',
							'children'				=> array
							(
								array
								(
									'id'			=> 0,
									'label'			=> 'Site Settings',
									'slug'			=> 'administrative/settings',
									'icon'			=> 'mdi mdi-wrench mdi-flip-h'
								),
								array
								(
									'id'			=> 0,
									'label'			=> 'Menus',
									'slug'			=> 'administrative/menus',
									'icon'			=> 'mdi mdi-menu'
								),
								array
								(
									'id'			=> 0,
									'label'			=> 'Translations',
									'slug'			=> 'administrative/translations',
									'icon'			=> 'mdi mdi-translate'
								),
								array
								(
									'id'			=> 0,
									'label'			=> 'Countries',
									'slug'			=> 'administrative/countries',
									'icon'			=> 'mdi mdi-map-legend'
								),
								array
								(
									'id'			=> 0,
									'label'			=> 'Years',
									'slug'			=> 'administrative/years',
									'icon'			=> 'mdi mdi-calendar-clock'
								),
								array
								(
									'id'			=> 0,
									'label'			=> 'Connections',
									'slug'			=> 'administrative/connections',
									'icon'			=> 'mdi mdi-power-plug'
								)
							)
						),
						array
						(
							'id'					=> 0,
							'label'					=> 'Activity Logs',
							'slug'					=> 'administrative/activities',
							'icon'					=> 'mdi mdi-information-outline'
						),
						array
						(
							'id'					=> 0,
							'label'					=> 'Session Cleaner',
							'slug'					=> 'administrative/cleaner',
							'icon'					=> 'mdi mdi-trash-can'
						)
					)
				),
				array
				(
					'id'							=> 0,
					'label'							=> 'Addons',
					'slug'							=> 'addons',
					'icon'							=> 'mdi mdi-puzzle'
				),
				array
				(
					'id'							=> 0,
					'label'							=> 'APIs',
					'slug'							=> 'apis',
					'icon'							=> 'mdi mdi-code-braces',
					'children'						=> array
					(
						array
						(
							'id'					=> 0,
							'label'					=> 'Services',
							'slug'					=> 'apis/services',
							'icon'					=> 'mdi mdi-link-variant'
						),
						array
						(
							'id'					=> 0,
							'label'					=> 'Debug Tool',
							'slug'					=> 'apis/debug_tool',
							'icon'					=> 'mdi mdi-android-debug-bridge'
						),
						array
						(
							'id'					=> 0,
							'label'					=> 'Documentation',
							'slug'					=> 'apis/documentation',
							'icon'					=> 'mdi mdi mdi-book-open-page-variant'
						)
					)
				)
			);
			
			if(1 != get_userdata('group_id') || 'header' == $placement)
			{
				$main_menu							= array();
			}
			
			$menu									= array_merge($menu, $main_menu);
		}
		else
		{
			$is_children							= true;
		}
		
		$module										= strtolower(substr(service('router')->controllerName(), strrpos(service('router')->controllerName(), '/') + 1));
		$menus										= null;
		
		if('header' == $placement)
		{
			$navigation_class						= 'navbar-nav' . ($additional_class ? ' ' . $additional_class : null);
			$navigation_item_class					= 'nav-item';
			$navigation_link_class					= 'nav-link';
			$dropdown_link_class					= 'dropdown';
			$dropdown_list_class					= 'dropdown-menu';
			$toggle_class							= 'dropdown-toggle';
			$toggle_initial							= 'data-toggle="dropdown"';
			$additional_prefix						= null;
		}
		else
		{
			$navigation_class						= 'nav flex-column' . ($additional_class ? ' ' . $additional_class : null);
			$navigation_item_class					= 'nav-item';
			$navigation_link_class					= 'nav-link';
			$dropdown_link_class					= null;
			$dropdown_list_class					= 'list-unstyled flex-column collapse';
			$toggle_class							= null;
			$toggle_initial							= 'data-toggle="expand-collapse"';
			$additional_prefix						= (!$rendered ? '
				<li class="nav-item">
					<span class="nav-link hide-on-collapse">
						' . phrase('main_navigation') . '
					</span>
				</li>
				<li class="nav-item' . ('dashboard' == service('uri')->getPath() ? ' active' : '') . '">
					<a href="' . base_url('dashboard') . '" class="nav-link --xhr" data-segmentation="dashboard">
						<i class="mdi mdi-monitor-dashboard"></i>
						<span class="hide-on-collapse">
							' . phrase('dashboard') . '
						</span>
					</a>
				</li>
			' : null);
		}
		
		foreach($menu as $key => $field)
		{
			$children								= false;
			$arrow									= null;
			
			if(isset($field['id']) && isset($field['label']) && isset($field['slug']))
			{
				if('---' == $field['slug'])
				{
					$menus							.= '
						<li class="nav-item">
							<span class="nav-link hide-on-collapse">
								' . ($field['label'] ? phrase($field['label'], true) : null) . '
							</span>
						</li>
					';
				}
				else
				{
					if(1 != get_userdata('group_id') && $visible_menus && !in_array($field['id'], $visible_menus)) continue;
					
					if(isset($field['children']) && is_array($field['children']) && sizeof($field['children']) > 0)
					{
						$children					= true;
					}
					
					$segments						= service('uri')->getSegments();
					$slug							= $field['slug'];
					
					if(preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $field['slug']))
					{
						$field['slug']				= $field['slug'] . '" target="_blank';
					}
					else
					{
						$field['slug']				= base_url($field['slug']);
					}
					
					$menus							.= '
						<li class="' . $navigation_item_class . ($children && $dropdown_link_class ? ' ' . $dropdown_link_class : null) . ((!$children && isset($segments[$level]) && $segments[$level] == $slug) || $slug == service('uri')->getPath() || (service('uri')->getPath() && $slug == preg_replace(array('/\/create/', '/\/read/', '/\/update/'), '', service('uri')->getPath())) ? ' active' : '') . '">
							<a href="' . ($children ? '#' : $field['slug']) . '" class="' . $navigation_link_class . (stripos($field['icon'], 'mdi-blank') === false && stripos($field['icon'], 'mdi-') !== false ? ' nav-padding-left' : null) . (!$children ? ' --xhr' : ' ' . $toggle_class) . '"' . ($children ? ' ' . $toggle_initial : ' data-segmentation="' . preg_replace('/[^a-zA-Z0-9]/', '_', $slug) . '"') . (isset($field['newtab']) && 1 == $field['newtab'] && !$children ? ' target="_blank"' : null) . '>
								' . (stripos($field['icon'], 'mdi-blank') === false && stripos($field['icon'], 'mdi-') !== false ? '<i class="' . (isset($field['icon']) ? $field['icon'] : 'mdi mdi-circle-outline') . '"></i>' : null) . '
								' . ($children && 'header' != $placement ? '<i class="mdi mdi-chevron-right float-right"></i>' : null) . '
								<span class="hide-on-collapse">
									' . ($field['label'] ? phrase($field['label'], true) : null) . '
								</span>
							</a>
							' . ($children ? $this->_get_menu($placement, null, $field['children'], $visible_menus, true, ($level + 1)) : '') . '
						</li>
					';
				}
			}
		}
		
		$output										= '
			<ul class="' . ($is_children ? $dropdown_list_class : $navigation_class) . '">
			
				' . $additional_prefix . '
				' . $menus . '
				
				' . ('header' != $placement ? '
				' . (!$rendered ? '
				<li class="divider"></li>
				<li class="' . $navigation_item_class . '">
					<a href="' . base_url('pages/about') . '" class="' . $navigation_link_class . ' text-sm hide-on-collapse" target="_blank">
						' . phrase('about') . '
					</a>
				</li>
				<li class="' . $navigation_item_class . '">
					<a href="' . base_url('pages/license') . '" class="' . $navigation_link_class . ' text-sm hide-on-collapse" target="_blank">
						' . phrase('license') . '
					</a>
				</li>
				<li class="' . $navigation_item_class . '">
					<a href="javascript:void(0)" class="' . $navigation_link_class . ' disabled text-sm hide-on-collapse">
						AKSARA ' . aksara('built_version') . '
					</a>
				</li>
				' : null) . '
				' : '') . '
			</ul>
		';
		
		return $output;
	}
	
	private function _strtolower_callback($string = array())
	{
		if(!isset($string[1])) return false;
		
		$rest										= substr($string[1], strpos($string[1], '/views/') + 7);
		
		return str_replace($rest, strtolower($rest), $string[1]);
	}
	
	private function _minify($buffer = null)
	{
		// make a backup of "pre" tag
		preg_match_all('#\<pre.*\>(.*)\<\/pre\>#Uis', $buffer, $pre_backup);
		
		$buffer										= str_replace($pre_backup[0], array_map(function($element){return '<pre>' . $element . '</pre>';}, array_keys($pre_backup[0])), $buffer);
		
		$search										= array
		(
			'/[\n\t\s]+/',			// replace end of line by space
			'/\>[^\S ]+/s',			// strip whitespaces after tags, except space
			'/[^\S ]+\</s',			// strip whitespaces before tags, except space
			'/(\s)+/s',				// shorten multiple whitespace sequences
			'/<!--(.|\s)*?-->/'		//remove HTML comments
		);

		$replace									= array
		(
			' ',
			'>',
			'<',
			'\\1',
			''
		);
		
		$buffer										= preg_replace($search, $replace, $buffer);
		
		// rollback the pre tag
		$buffer										= str_replace(array_map(function($element){return '<pre>' . $element . '</pre>';}, array_keys($pre_backup[0])), $pre_backup[0], $buffer);
		
		return $buffer;
	}
}
