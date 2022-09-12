<?php

namespace Aksara\Laboratory;

/**
 * Template
 *
 * @author			Aby Dahana <abydahana@gmail.com>
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
		$suffix										= (service('request')->getUserAgent()->isMobile() ? '_mobile' : ('modal' == service('request')->getPost('prefer') ? '_modal' : (isset($_SERVER['GRID_VIEW']) && $_SERVER['GRID_VIEW'] ? '_grid' : null)));
		
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
		
		if(!$this->_api_request)
		{
			/**
			 * Get theme helpers
			 */
			if(is_dir('../themes/' . $this->_theme . '/helpers'))
			{
				/* load required helper */
				helper('filesystem');
				
				// list available helper files
				$helpers							= directory_map('../themes/' . $this->_theme . '/helpers', 1);
				
				if($helpers)
				{
					// check and loop the helper files
					foreach($helpers as $key => $val)
					{
						if(strtolower(pathinfo($val, PATHINFO_EXTENSION)) == 'php')
						{
							// check matched helper and load to construct
							require_once('../themes/' . $this->_theme . '/helpers/' . $val);
						}
					}
				}
			}
			
			// minify pattern
			$minify_pattern							= array
			(
				'/[ \t]+/'							=> ' ',
				'/(\>)\s*(\<)/m'					=> '$1 $2',
				'/<!--(.|\s)*?-->/'					=> ''
			);
			
			if(isset($data->pagination))
			{
				$data->template						= (object) array
				(
					'pagination'					=> $this->pagination($data->pagination)
				);
			}
			
			$this->_view							= $this->get_view($view, $data, $table);
			
			// generate the html from the view
			$data->html								= view($this->_view, (array) $data);
			
			/* make a backup of "pre" tag */
			preg_match_all('#\<pre.*\>(.*)\<\/pre\>#Uis', $data->html, $pre_backup);
			
			$data->html								= str_replace($pre_backup[0], array_map(function($element){return '<pre>' . $element . '</pre>';}, array_keys($pre_backup[0])), $data->html);
			
			/* make a backup of "textarea" tag */
			preg_match_all('#\<textarea.*\>(.*)\<\/textarea\>#Uis', $data->html, $textarea_backup);
			
			$data->html								= str_replace($textarea_backup[0], array_map(function($element){return '<textarea>' . $element . '</textarea>';}, array_keys($textarea_backup[0])), $data->html);
			
			/* make a backup of "script" tag */
			preg_match_all('#\<script.*\>(.*)\<\/script\>#Uis', $data->html, $script_backup);
			
			$data->html								= str_replace($script_backup[0], array_map(function($element){return '<script type="text/javascript">' . $element . '</script>';}, array_keys($script_backup[0])), $data->html);
			
			/* minify the data */
			$data->html								= preg_replace(array_keys($minify_pattern), array_values($minify_pattern), $data->html);
			
			if($data->html)
			{
				/* rollback the pre tag */
				$data->html							= str_replace(array_map(function($element){return '<pre>' . $element . '</pre>';}, array_keys($pre_backup[0])), $pre_backup[0], $data->html);
				
				/* rollback the textarea tag */
				$data->html							= str_replace(array_map(function($element){return '<textarea>' . $element . '</textarea>';}, array_keys($textarea_backup[0])), $textarea_backup[0], $data->html);
				
				/* rollback the script tag */
				$data->html							= str_replace(array_map(function($element){return '<script type="text/javascript">' . $element . '</script>';}, array_keys($script_backup[0])), preg_replace('/(?:(?:\/\*(?:[^*]|(?:\*+[^*\/]))*\*+\/)|(?:(?<!\:|\\\|\')\/\/.*))/', '', $script_backup[0]), $data->html);
			}
		}
		
		/**
		 * Build the result and send to client
		 */
		if((service('request')->isAJAX() && service('request')->getServer('HTTP_REFERER') && stripos(service('request')->getServer('HTTP_REFERER'), service('request')->getServer('SERVER_NAME')) !== false) || $this->_api_request)
		{
			unset($data->template, $data->pagination);
			
			return make_json($data);
		}
		else
		{
			$content								= $data->html;
			
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
				<ul class="pagination pagination-sm mb-0">
					<li class="page-item disabled">
						<a href="javascript:void(0)" tabindex="-1" class="page-link">
							' . phrase('previous') . '
						</a>
					</li>
					<li class="page-item active">
						<a href="javascript:void(0)" class="page-link">
							1
						</a>
					</li>
					<li class="page-item disabled">
						<a href="javascript:void(0)" class="page-link">
							' . phrase('next') . '
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
				<div class="row align-items-center">
					<div class="col-sm-6 text-center text-sm-start">
						<label class="text-muted mb-0">
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
							' . ($data->total_rows > $data->limit ? '
							<form action="' . current_page(null, array('per_page' => null)) . '" method="POST" class="--xhr-form ml-2 d-none d-sm-none d-md-block d-lg-block d-xl-block">
								' . $query_string . '
								<div class="input-group input-group-sm">
									<select name="limit" class="form-control">
										<option value="' . $data->limit . '"' . (!$data->per_page ? ' selected' : null) . '>' . $data->limit . '</option>
										' . ($data->total_rows > ($data->limit * 2) ? '<option value="' . ($data->limit * 2) . '"' . (($data->limit * 2) == $data->per_page ? ' selected' : null) . '>' . ($data->limit * 2) . '</option>' : null) . '
										' . ($data->total_rows > ($data->limit * 4) ? '<option value="' . ($data->limit * 4) . '"' . (($data->limit * 4) == $data->per_page ? ' selected' : null) . '>' . ($data->limit * 4) . '</option>' : null) . '
										' . ($data->total_rows > ($data->limit * 8) ? '<option value="' . ($data->limit * 8) . '"' . (($data->limit * 8) == $data->per_page ? ' selected' : null) . '>' . ($data->limit * 8) . '</option>' : null) . '
										' . ($data->total_rows > ($data->limit * 20) ? '<option value="' . ($data->limit * 20) . '"' . (($data->limit * 20) == $data->per_page ? ' selected' : null) . '>' . ($data->limit * 20) . '</option>' : null) . '
									</select>
									<input type="number" name="per_page" class="form-control text-center" value="' . (is_numeric(service('request')->getGet('per_page')) && service('request')->getGet('per_page') ? service('request')->getGet('per_page') : 1) . '" min="1" max="' . $last_page . '" />
									<button type="submit" class="btn btn-primary">
										' . phrase('go') . '
									</button>
								</div>
							</form>
							' : null) . '
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
				'total_rows'						=> $data->total_rows,
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
	
	private function _get_menu($menus = array())
	{
		if(!$menus)
		{
			$group_id								= get_userdata('group_id');
			
			$menus									= $this->model->select
			('
				serialized_data
			')
			->group_start()
			->where('group_id', $group_id)
			->or_where('group_id', 0)
			->group_end()
			->get_where
			(
				'app__menus',
				array
				(
					'menu_placement'				=> ('frontend' == $this->get_theme_property('type') ? 'header' : 'sidebar')
				),
				1
			)
			->row('serialized_data');
			
			$menus									= ($menus ? json_decode($menus, true) : array());
			
			if(get_userdata('group_id') == 1 && $this->get_theme_property('type') == 'backend')
			{
				$core_menus							= array
				(
					array
					(
						'id'						=> 0,
						'label'						=> '',
						'slug'						=> '---'
					),
					array
					(
						'id'						=> 0,
						'label'						=> 'CMS',
						'slug'						=> 'cms',
						'icon'						=> 'mdi mdi-dropbox',
						'children'					=> array
						(
							array
							(
								'id'				=> 0,
								'label'				=> 'Blogs',
								'slug'				=> 'cms/blogs',
								'icon'				=> 'mdi mdi-newspaper',
								'children'			=> array
								(
									array
									(
										'id'		=> 0,
										'label'		=> 'Posts',
										'slug'		=> 'cms/blogs',
										'icon'		=> 'mdi mdi-pencil'
									),
									array
									(
										'id'		=> 0,
										'label'		=> 'Categories',
										'slug'		=> 'cms/blogs/categories',
										'icon'		=> 'mdi mdi-sitemap'
									)
								)
							),
							array
							(
								'id'				=> 0,
								'label'				=> 'Pages',
								'slug'				=> 'cms/pages',
								'icon'				=> 'mdi mdi-book-open-page-variant'
							),
							array
							(
								'id'				=> 0,
								'label'				=> 'Galleries',
								'slug'				=> 'cms/galleries',
								'icon'				=> 'mdi mdi-folder-multiple-image'
							),
							array
							(
								'id'				=> 0,
								'label'				=> 'Peoples',
								'slug'				=> 'cms/peoples',
								'icon'				=> 'mdi mdi-account-group-outline'
							),
							array
							(
								'id'				=> 0,
								'label'				=> 'Partial Content',
								'slug'				=> 'cms/partials',
								'icon'				=> 'mdi mdi-file-image',
								'children'			=> array
								(
									array
									(
										'id'		=> 0,
										'label'		=> 'Carousels',
										'slug'		=> 'cms/partials/carousels',
										'icon'		=> 'mdi mdi-image-multiple'
									),
									array
									(
										'id'		=> 0,
										'label'		=> 'FAQs',
										'slug'		=> 'cms/partials/faqs',
										'icon'		=> 'mdi mdi-file-question'
									),
									array
									(
										'id'		=> 0,
										'label'		=> 'Announcements',
										'slug'		=> 'cms/partials/announcements',
										'icon'		=> 'mdi mdi-bullhorn-outline'
									),
									array
									(
										'id'		=> 0,
										'label'		=> 'Testimonials',
										'slug'		=> 'cms/partials/testimonials',
										'icon'		=> 'mdi mdi-comment-account-outline'
									),
									array
									(
										'id'		=> 0,
										'label'		=> 'Inquiries',
										'slug'		=> 'cms/partials/inquiries',
										'icon'		=> 'mdi mdi-message-text'
									),
									array
									(
										'id'		=> 0,
										'label'		=> 'Media',
										'slug'		=> 'cms/partials/media',
										'icon'		=> 'mdi mdi-folder-image'
									)
								)
							),
							array
							(
								'id'				=> 0,
								'label'				=> 'Comments',
								'slug'				=> 'cms/comments',
								'icon'				=> 'mdi mdi-comment-multiple-outline'
							)
						)
					),
					array
					(
						'id'						=> 0,
						'label'						=> '',
						'slug'						=> '---'
					),
					array
					(
						'id'						=> 0,
						'label'						=> 'Core Tools',
						'slug'						=> '---'
					),
					array
					(
						'id'						=> 0,
						'label'						=> 'Administrative',
						'slug'						=> 'administrative',
						'icon'						=> 'mdi mdi-cogs',
						'children'					=> array
						(
							array
							(
								'id'				=> 0,
								'label'				=> 'Users and Groups',
								'slug'				=> 'administrative/users',
								'icon'				=> 'mdi mdi-account-group-outline',
								'children'			=> array
								(
									array
									(
										'id'		=> 0,
										'label'		=> 'Users',
										'slug'		=> 'administrative/users',
										'icon'		=> 'mdi mdi-account-group'
									),
									array
									(
										'id'		=> 0,
										'label'		=> 'Groups',
										'slug'		=> 'administrative/groups',
										'icon'		=> 'mdi mdi-sitemap'
									),
									array
									(
										'id'		=> 0,
										'label'		=> 'Privileges',
										'slug'		=> 'administrative/groups/privileges',
										'icon'		=> 'mdi mdi-account-check-outline'
									)
								)
							),
							array
							(
								'id'				=> 0,
								'label'				=> 'Configurations',
								'slug'				=> 'administrative',
								'icon'				=> 'mdi mdi-wrench-outline',
								'children'			=> array
								(
									array
									(
										'id'		=> 0,
										'label'		=> 'Site Settings',
										'slug'		=> 'administrative/settings',
										'icon'		=> 'mdi mdi-settings'
									),
									array
									(
										'id'		=> 0,
										'label'		=> 'Menus',
										'slug'		=> 'administrative/menus',
										'icon'		=> 'mdi mdi-menu'
									),
									array
									(
										'id'		=> 0,
										'label'		=> 'Translations',
										'slug'		=> 'administrative/translations',
										'icon'		=> 'mdi mdi-translate'
									),
									array
									(
										'id'		=> 0,
										'label'		=> 'Countries',
										'slug'		=> 'administrative/countries',
										'icon'		=> 'mdi mdi-map-legend'
									),
									array
									(
										'id'		=> 0,
										'label'		=> 'Years',
										'slug'		=> 'administrative/years',
										'icon'		=> 'mdi mdi-calendar-multiple-check'
									),
									array
									(
										'id'		=> 0,
										'label'		=> 'Connections',
										'slug'		=> 'administrative/connections',
										'icon'		=> 'mdi mdi-power-plug'
									)
								)
							),
							array
							(
								'id'				=> 0,
								'label'				=> 'Logs',
								'slug'				=> 'administrative/logs',
								'icon'				=> 'mdi mdi-information-outline',
								'children'			=> array
								(
									array
									(
										'id'		=> 0,
										'label'		=> 'Activities',
										'slug'		=> 'administrative/logs/activities',
										'icon'		=> 'mdi mdi-calendar-clock'
									),
									array
									(
										'id'		=> 0,
										'label'		=> 'Errors',
										'slug'		=> 'administrative/logs/errors',
										'icon'		=> 'mdi mdi-bug'
									)
								)
							),
							array
							(
								'id'				=> 0,
								'label'				=> 'Session Cleaner',
								'slug'				=> 'administrative/cleaner',
								'icon'				=> 'mdi mdi-trash-can'
							)
						)
					),
					array
					(
						'id'						=> 0,
						'label'						=> 'add_ons',
						'slug'						=> 'addons',
						'icon'						=> 'mdi mdi-puzzle'
					),
					array
					(
						'id'						=> 0,
						'label'						=> 'APIs',
						'slug'						=> 'apis',
						'icon'						=> 'mdi mdi-code-braces',
						'children'					=> array
						(
							array
							(
								'id'				=> 0,
								'label'				=> 'Services',
								'slug'				=> 'apis/services',
								'icon'				=> 'mdi mdi-link-variant'
							),
							array
							(
								'id'				=> 0,
								'label'				=> 'Debug Tool',
								'slug'				=> 'apis/debug_tool',
								'icon'				=> 'mdi mdi-android-debug-bridge'
							),
							array
							(
								'id'				=> 0,
								'label'				=> 'Documentation',
								'slug'				=> 'apis/documentation',
								'icon'				=> 'mdi mdi mdi-book-open-page-variant'
							)
						)
					),
					array
					(
						'id'						=> 0,
						'label'						=> '',
						'slug'						=> '---'
					),
					array
					(
						'id'						=> 0,
						'label'						=> 'About',
						'slug'						=> 'pages/about',
						'icon'						=> 'mdi mdi-blank',
						'class'						=> 'text-sm'
					),
					array
					(
						'id'						=> 0,
						'label'						=> 'License',
						'slug'						=> 'pages/license',
						'icon'						=> 'mdi mdi-blank',
						'class'						=> 'text-sm'
					),
					array
					(
						'id'						=> 0,
						'label'						=> 'Aksara ' . aksara('build_version'),
						'slug'						=> '---',
						'icon'						=> 'mdi mdi-blank',
						'class'						=> 'text-sm',
						'translate'					=> false
					),
				);
				
				$dashboard							= array
				(
					array
					(
						'id'						=> 0,
						'label'						=> 'Main Navigation',
						'slug'						=> '---',
						'icon'						=> null,
					),
					array
					(
						'id'						=> 0,
						'label'						=> 'Dashboard',
						'slug'						=> 'dashboard',
						'icon'						=> 'mdi mdi-monitor-dashboard',
					)
				);
				
				$menus								= array_merge($dashboard, $menus, $core_menus);
			}
		}
		
		array_walk_recursive($menus, function(&$label, $key)
		{
			if($key == 'label' && $label && $label != 'Aksara ' . aksara('build_version'))
			{
				$label								= phrase($label, true);
			}
		});
		
		return $menus;
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
