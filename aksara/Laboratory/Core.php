<?php

namespace Aksara\Laboratory;

/**
 * Aksara!
 *
 * This class is overriding the codeigniter base controller
 * and added the extra CRUD function that generate a magic output.
 * You don't need to create model or view anymore.
 *
 * I would comments every single method but i choose live
 * dangerously, so it might be implemented in the next decade.
 *
 * With great power comes great responsibility --- Uncle Ben
 *
 * @author			Aby Dahana <abydahana@gmail.com>
 * @profile			abydahana.github.io
 * @website			www.aksaracms.com
 * @since			version 4.0.0
 * @copyright		(c) 2020-2022 Aksara Laboratory
 * -------------------------------------------------------------------
 * You may need to work with a fool get your complicated work done faster :)
 * -------------------------------------------------------------------
 */

use CodeIgniter\Controller;
use Aksara\Laboratory\Template;
use Aksara\Laboratory\Model;
use Aksara\Laboratory\Permission;

class Core extends Controller
{
	protected $model;
	protected $theme;
	protected $permission;
	
	protected $_api_request							= false;
	
	private $_language								= null;
	private $_restrict_on_demo						= false;
	private $_module								= null;
	private $_redirect_back							= null;
	private $_set_title								= null;
	private $_set_title_placeholder					= null;
	private $_set_icon								= null;
	private $_set_description						= array();
	private $_set_messages							= array();
	private $_view									= 'index';
	private $_set_template							= array();
	private $_set_theme								= null;
	private $_grid_view								= array();
	private $_set_upload_path						= null;
	private $_upload_data							= array();
	private $_upload_error							= array();
	private $_set_method							= null;
	private $_set_permission						= false;
	
	private $_set_breadcrumb						= array();
	private $_set_field								= array();
	private $_set_tooltip							= array();
	private $_set_primary							= array();
	private $_set_validation						= array();
	private $_set_default							= array();
	private $_set_alias								= array();
	private $_set_heading							= array();
	private $_set_output							= array();
	private $_unset_action							= array();
	private $_unset_column							= array();
	private $_unset_field							= array();
	private $_unset_truncate						= array();
	private $_unset_view							= array();
	private $_unset_delete							= array();
	private $_unset_read							= array();
	private $_unset_update							= array();
	private $_add_button							= null;
	private $_add_class								= array();
	private $_extra_toolbar							= array();
	private $_extra_option							= array();
	private $_extra_dropdown						= array();
	private $_extra_submit							= array();
	private $_set_attribute							= array();
	private $_set_placeholder						= array();
	private $_set_autocomplete						= array();
	private $_set_option_label						= array();
	private $_translate_field						= array();
	private $_add_filter							= null;
	private $_form_callback							= null;
	private $_before_insert							= null;
	private $_after_insert							= null;
	private $_before_update							= null;
	private $_after_update							= null;
	private $_before_delete							= null;
	private $_after_delete							= null;
	private $_old_files								= array();
	private $_group_field							= array();
	private $_item_reference						= array();
	
	private $_modal_size							= null;
	private $_field_position						= array();
	private $_field_size							= array();
	private $_field_prepend							= array();
	private $_field_append							= array();
	private $_column_size							= array();
	private $_merge_content							= null;
	private $_merge_label							= array();
	private $_merge_field							= array();
	private $_format_output							= array();
	private $_column_order							= array();
	private $_field_order							= array();
	private $_view_order							= array();
	private $_results								= array();
	private $_data									= true;
	private $_query									= array();
	private $_field_data							= array();
	private $_output								= array();
	
	private $_default_value							= array();
	private $_set_relation							= array();
	
	private $_parameter								= array();
	
	private $_prepare								= array();
	
	private $_table;
	private $_compiled_table						= array();
	private $_select								= array();
	private $_compiled_select						= array();
	private $_distinct								= array();
	private $_join									= array();
	private $_where									= array();
	private $_like									= array();
	
	private $_limit									= 25;
	private $_limit_backup							= 25;
	private $_offset								= 0;
	private $_total									= 0;
	private $_insert_on_update_fail					= false;
	private $_searchable							= true;
	
	public $_method									= null;
	public $_insert_id								= 0;
	
	public function __construct()
	{
		// start benchmarking
		service('timer')->start('elapsed_time');
		
		// check if accessed from IE browser
		if(strtolower(service('request')->getUserAgent()->getBrowser()) == 'internet explorer' && service('request')->getUserAgent()->getVersion() < 11)
		{
			// throw compatibility mode
			die('The ' . service('request')->getUserAgent()->getBrowser() . ' ' . service('request')->getUserAgent()->getVersion() . ' is no longer supported...');
		}
		
		helper(array('url', 'file', 'theme', 'security', 'main', 'string'));
		
		// unset previous data
		unset_userdata('_upload_data');
		
		// load model class
		$this->model								= new Model();
		
		// get database driver
		$this->_db_driver							= $this->model->db_driver();
		
		// load permission class
		$this->permission							= new Permission();
		
		// load validation class
		$this->form_validation						= \Config\Services::validation();
		
		// get matched router path
		$path										= (isset(service('router')->getMatchedRoute()[0]) ? service('router')->getMatchedRoute()[0] : null);
		
		// get the request method
		$this->_method								= service('router')->methodName();
		
		// get the module path
		$this->_module								= ($this->_method && $path && strpos($path, $this->_method) !== false ? preg_replace('/\/' . $this->_method . '$/', '', $path) : $path);
		
		// check if query string has limit
		if(is_numeric(service('request')->getGet('limit')) && service('request')->getGet('limit'))
		{
			// store original limit
			$this->_limit_backup					= $this->_limit;
			
			// apply the limit for query builder
			$this->_limit							= service('request')->getGet('limit');
		}
		
		// check if query string has offset
		if(is_numeric(service('request')->getGet('offset')) && service('request')->getGet('offset'))
		{
			// apply the offset for query builder
			$this->_offset							= service('request')->getGet('offset');
		}
		
		/**
		 * Apply token serialization in order to prevent the query string bypass or hijacking
		 */
		if('_token' == service('request')->getPost('request'))
		{
			// token requested
			unset($_POST['request']);
			
			// exchange the callback
			return make_json
			(
				array
				(
					'_token'						=> sha1(current_page() . ENCRYPTION_KEY . get_userdata('session_generated')),
					'callback'						=> current_page(null, array_merge(service('request')->getPost(), array('aksara' => generate_token(service('request')->getPost()), 'request' => null)))
				)
			);
		}
		
		/**
		 * Preview theme
		 */
		if('preview-theme' == service('request')->getGet('aksara_mode') && sha1(service('request')->getGet('aksara_theme') . ENCRYPTION_KEY . get_userdata('session_generated')) == service('request')->getGet('integrity_check') && is_dir(ROOTPATH . 'themes/' . service('request')->getGet('aksara_theme')))
		{
			// set the temporary theme
			$this->_set_theme						= strip_tags(service('request')->getGet('aksara_theme'));
		}
		
		/**
		 * Working with REST API
		 */
		if(service('request')->getHeaderLine('X-API-KEY'))
		{
			// handshake betwiin REST client and Aksara
			$this->_handshake(service('request')->getHeaderLine('X-API-KEY'));
		}
		else
		{
			// push log
			$this->_push_log();
		}
		
		// set upload path
		$this->set_upload_path();
		
		// set user language
		$this->_set_language(get_userdata('language_id'));
	}
	
	/**
	 * This function will remapping the request based on method
	 * and its parameter. It's also can be used to change the
	 * method to get the uri segment based on requested class.
	 */
	public function _remap($method = null, $segment_1 = null, $segment_2 = null, $segment_3 = null, $segment_4 = null, $segment_5 = null, $segment_6 = null)
	{
		// method checks to make sure it's not conflict with main class method
		if(method_exists($this, $method) && !in_array($method, get_class_methods('\Aksara\Laboratory\Core')))
		{
			// check if method is defined in requested class
			$this->$method($segment_1, $segment_2, $segment_3, $segment_4, $segment_5, $segment_6);
		}
		else
		{
			// otherwise render the default method (index)
			$this->index($method, $segment_1, $segment_2, $segment_3, $segment_4, $segment_5, $segment_6);
		}
	}
	
	/**
	 * Debugging
	 */
	public function debug(string $result_type = '')
	{
		$this->_debugging							= $result_type;
		
		return $this;
	}
	
	/**
	 * restrict_on_demo
	 * Function to apply demo mode
	 *
	 * @access		public
	 */
	public function restrict_on_demo()
	{
		// check if demo mode is active
		if(DEMO_MODE)
		{
			// set the restriction property
			$this->_restrict_on_demo				= true;
		}
		
		return $this;
	}
	
	/**
	 * database_config
	 * Database configuration
	 *
	 * @access		public
	 */
	public function database_config($driver = array(), string $hostname = null, int $port = null, string $username = null, string $password = null, string $database = null)
	{
		// check if the parameter is sets with array
		if(is_array($driver) && isset($driver['driver']) && isset($driver['hostname']) && isset($driver['port']) && isset($driver['username']) && isset($driver['password']) && isset($driver['database']))
		{
			// use the array parameter as config
			$this->model->database_config($driver['driver'], $driver['hostname'], $driver['port'], $driver['username'], $driver['password'], $driver['database']);
		}
		else
		{
			// use the strng parameter as config
			$this->model->database_config($driver, $hostname, $port, $username, $password, $database);
		}
		
		return $this;
	}
	
	/**
	 * valid_token
	 * Validate the token that submitted through form
	 *
	 * @access		public
	 * @return		bool
	 */
	public function valid_token($token = null)
	{
		// match the token validation
		if(service('request')->getPost() && ($token == sha1(current_page() . ENCRYPTION_KEY . get_userdata('session_generated')) || $token == sha1(service('request')->getHeaderLine('Referer') . ENCRYPTION_KEY . get_userdata('session_generated')) || $this->_api_request))
		{
			// token match
			return true;
		}
		
		return false;
	}
	
	/**
	 * parent_module
	 * Assign the parent module of current class
	 *
	 * @access		public
	 * @return		exception
	 */
	public function parent_module(string $module)
	{
		// sets parent module
		$this->_module								= $module;
		
		return $this;
	}
	
	/**
	 * set_permission
	 * Set up the permission of module, it's mean that only
	 * signed in user can access the module
	 *
	 * @access		public
	 * @return		exception
	 */
	public function set_permission($permissive_user = array(), string $redirect = null)
	{
		// this mean the permission is set as true
		$this->_set_permission						= true;
		
		// check if permissive user is set
		if($permissive_user && !is_array($permissive_user))
		{
			// safe check for array
			$permissive_user						= array_map('trim', explode(',', $permissive_user));
		}
		
		// check if permissions is sets and make sure the user is signed in or requested from restful
		if($this->_set_permission && !get_userdata('is_logged') && !$this->_api_request)
		{
			// user isn't signed in
			return throw_exception(403, phrase('your_session_has_been_expired'), ($redirect ? $redirect : base_url()), true);
		}
		
		// check if user permission is not allowed to access the module
		else if(!$this->permission->allow($this->_module, $this->_method, get_userdata('user_id'), $redirect))
		{
			// user been signed in but blocked by group privilege
			return throw_exception(403, phrase('you_do_not_have_a_sufficient_privileges_to_access_the_requested_page'), ($redirect ? $redirect : $this->_redirect_back));
		}
		
		// check if user group is permissive to the given parameter
		else if($permissive_user && !in_array(get_userdata('group_id'), $permissive_user))
		{
			// user been signed in but blocked by group privilege
			return throw_exception(403, phrase('you_do_not_have_a_sufficient_privileges_to_access_the_requested_page'), ($redirect ? $redirect : $this->_redirect_back));
		}
	}
	
	/**
	 * set_method
	 * Set up the method of module so it will only have one
	 * method by default
	 *
	 * @access		public
	 * @return		string
	 */
	public function set_method(string $method = 'index')
	{
		// set the method property
		$this->_method								= $method;
		$this->_set_method							= $method;
		
		return $this;
	}
	
	/**
	 * set_theme
	 * Set up the theme. The front/back theme might be different
	 *
	 * @access		public
	 * @return		object
	 */
	public function set_theme(string $theme = 'frontend')
	{
		// validate the theme parameter to match with predefined config
		if(!in_array($theme, array('frontend', 'backend'))) return false;
		
		// get site id before run query to prevent nested queue
		$site_id									= get_setting('id');
		
		$query										= $this->model->select($theme . '_theme')->get_where
		(
			'app__settings',
			array
			(
				'id'								=> $site_id
			),
			1
		)
		->row($theme . '_theme');
		
		// set the theme with matched configuration
		$this->_set_theme							= $query;
		
		return $this;
	}
	
	/**
	 * set_template
	 * Possibility to set the template if the master template
	 * isn't enough tho
	 *
	 * @access		public
	 * @return		array
	 */
	public function set_template($params = array(), string $value = null)
	{
		// make sure the parameter is array, otherwise convert it
		if(!is_array($params))
		{
			// convert parameters as array
			$params									= array
			(
				$params								=> $value
			);
		}
		
		// set the template renderer
		$this->_set_template						= array_merge($this->_set_template, $params);
		
		return $this;
	}
	
	/**
	 * set_breadcrumb
	 * Set up the breadcrumb
	 *
	 * @access		public
	 * @return		array
	 */
	public function set_breadcrumb($params = array(), string $value = null)
	{
		// check if parameters isn't in array format
		if(!is_array($params))
		{
			// push the parameter to breadcrumb property
			$this->_set_breadcrumb[$params]			= array
			(
				'label'								=> $value,
				'translated'						=> true
			);
		}
		else
		{
			// loops the parameters
			foreach($params as $key => $val)
			{
				// push the parameter to breadcrumb property
				$this->_set_breadcrumb[$key]		= array
				(
					'label'							=> $val,
					'translated'					=> true
				);
			}
		}
		
		return $this;
	}
	
	/**
	 * set_primary
	 * Set the CRUD primary key when the rendered database table
	 * isn't have at least one primary key
	 *
	 * @access		public
	 * @return		array
	 */
	public function set_primary($key = array())
	{
		// shorthand possibility, separate with commas
		$this->_set_primary							= array_map('trim', explode(',', $key));
		
		return $this;
	}
	
	/**
	 * set_title
	 * Set the title of module. It also will displayed as document
	 * title in the browser.
	 *
	 * A magic string can be wrapped using curly brackets.
	 *
	 * @access		public
	 * @return		string
	 */
	public function set_title($magic_string = null, string $placeholder = null, $placement = null)
	{
		// check if the magic string is in array format
		if(is_array($magic_string))
		{
			// loops the magic string
			foreach($magic_string as $key => $val)
			{
				// pair the magic string to the title property
				$this->_set_title[$key]				= $val;
			}
		}
		else
		{
			// set the title property with magic string
			$this->_set_title						= $magic_string;
		}
		
		// set the title placeholder if only the magic string doesn't have any result
		$this->_set_title_placeholder				= $placeholder;
		
		return $this;
	}
	
	/**
	 * set_icon
	 * Set the icon of content title, yeah we need some icon
	 * that describe the module
	 *
	 * @access		public
	 * @return		string
	 */
	public function set_icon($icon = null)
	{
		// check if icon parameter is in array format
		if(is_array($icon))
		{
			// loops the icon parameter
			foreach($icon as $key => $val)
			{
				// push parameter to the icon property
				$this->_set_icon[$key]				= $val;
			}
		}
		else
		{
			// set the icon using individual parameter
			$this->_set_icon						= $icon;
		}
		
		return $this;
	}
	
	/**
	 * set_description
	 * Set the description of module. It also will displayed as
	 * description of meta
	 *
	 * A magic string can be wrapped using curly brackets.
	 *
	 * @access		public
	 * @return		array
	 */
	public function set_description($params = array(), $value = 'index')
	{
		// check if parameters is in array format
		if(is_array($params))
		{
			// merge the description to the older one
			$this->_set_description					= array_merge($this->_set_description, $params);
		}
		else
		{
			// push the new key of description
			$this->_set_description[$value]			= $params;
		}
		
		return $this;
	}
	
	/**
	 * set_messages
	 * Set the individual message of exception
	 *
	 * @access		public
	 * @method		string
	 * @code		int
	 * @messages	string
	 * @return		array
	 */
	public function set_messages($params = array(), int $code = null, string $messages = null)
	{
		// check if parameter is not in array format
		if(!is_array($params))
		{
			// set the parameter
			$placement								= $params;
			$params									= array();
			$params[$placement]						= array
			(
				'code'								=> $code,
				'messages'							=> $messages
			);
		}
		
		// merge the parameter with the older one
		$this->_set_messages						= array_merge($this->_set_messages, $params);
		
		return $this;
	}
	
	/**
	 * set_button
	 * Override the existing CRUD button
	 *
	 * @access		public
	 * @return		array
	 */
	public function set_button(string $button, string $value, string $label, string $icon = null, string $class = null, $target = null)
	{
		// push the button properties
		$this->_set_button[$button]					= array
		(
			'href'									=> $value,
			'label'									=> $label,
			'icon'									=> $icon,
			'class'									=> $class,
			'target'								=> $target
		);
		
		return $this;
	}
	
	/**
	 * grid_view
	 * Switch the view as grid
	 *
	 * @access		public
	 * @thumbnail	string
	 * @title		string
	 * @description	string
	 * @return		string
	 */
	public function grid_view(string $thumbnail, string $hyperlink = null, array $parameter = array(), bool $new_tab = false)
	{
		// use grid view instead of data tables
		$_SERVER['GRID_VIEW']						= true;
		
		// push thumbnail source to the grid view property
		$this->_grid_view							= array
		(
			'thumbnail'								=> $thumbnail,
			'hyperlink'								=> $hyperlink,
			'parameter'								=> $parameter,
			'new_tab'								=> $new_tab
		);
		
		return $this;
	}
	
	/**
	 * add_filter
	 * Add individual filter to CRUD index table
	 *
	 * @access		public
	 * @return		string
	 */
	public function add_filter(string $filter)
	{
		// push the parameter to filters property
		$this->_add_filter							= $filter;
		
		return $this;
	}
	
	/**
	 * searchable
	 * The system will autosearch when the query string contain "q" on
	 * the query string (typically when you give a table name th the
	 * render() method. This method prevent system to search even the
	 * "q" parameter is present.
	 *
	 * @access		public
	 * @return		string
	 */
	public function searchable($active = true)
	{
		// the search discovery is active by default, check if it's being turned off
		if(!$active)
		{
			// set the parameter to search property
			$this->_searchable						= false;
		}
		
		return $this;
	}
	
	/**
	 * add_action
	 * Adding the custom action
	 *
	 * @access		public
	 * @params		mixed		$placement	toolbar, option or dropdown
	 * @params		mixed		$parameter	extra query string
	 * @return		array
	 */
	public function add_action(string $placement = 'option', string $url = null, string $label = null, string $class = null, string $icon = null, array $parameter = array(), bool $new_tab = false)
	{
		// get query string
		$query_string								= service('request')->getGet();
		
		// toolbar placement
		if('toolbar' == $placement)
		{
			// check if the parameter has property
			if($parameter)
			{
				// merge the parameter to current query string
				$parameter							= array_merge(array('aksara' => generate_token(array_filter(array_merge($query_string, $parameter)))), $query_string, $parameter);
			}
			
			// push the parameter to the toolbar property
			$this->_extra_toolbar[]					= array
			(
				'url'								=> $url,
				'label'								=> $label,
				'class'								=> $class,
				'icon'								=> $icon,
				'parameter'							=> $parameter,
				'new_tab'							=> $new_tab
			);
		}
		
		// dropdown placement
		else if('dropdown' == $placement)
		{
			// push the parameter to the dropdown property
			$this->_extra_dropdown[]				= array
			(
				'url'								=> $url,
				'label'								=> $label,
				'class'								=> $class,
				'icon'								=> $icon,
				'parameter'							=> $parameter,
				'new_tab'							=> $new_tab
			);
		}
		
		// extra bottom button under the form
		else if('submit' == $placement)
		{
			// check if the parameter has property
			if($parameter)
			{
				// merge the parameter to current query string
				$parameter							= array_merge(array('aksara' => generate_token(array_filter(array_merge($query_string, $parameter)))), $query_string, $parameter);
			}
			
			// push the parameter to the button property
			$this->_extra_submit[]					= array
			(
				'url'								=> $url,
				'label'								=> $label,
				'class'								=> $class,
				'icon'								=> $icon,
				'parameter'							=> $parameter,
				'new_tab'							=> $new_tab
			);
		}
		
		// otherwise
		else
		{
			// push the parameter as option (dropdown) links
			$this->_extra_option[]					= array
			(
				'url'								=> $url,
				'label'								=> $label,
				'class'								=> $class,
				'icon'								=> $icon,
				'parameter'							=> $parameter,
				'new_tab'							=> $new_tab
			);
		}
		
		if($parameter && is_array($parameter))
		{
			$this->_parameter						= array_merge($this->_parameter, array_keys($parameter));
		}
		
		return $this;
	}
	
	/**
	 * unset_action
	 * Unset the method
	 *
	 * @access		public
	 * @return		array
	 */
	public function unset_action($params = array())
	{
		// check if parameter isn't in array format
		if(!is_array($params))
		{
			// shorthand possibility, separate with commas
			$params									= array_map('trim', explode(',', $params));
		}
		
		// merge the parameter to the older one
		$this->_unset_action						= array_merge($this->_unset_action, $params);
		
		return $this;
	}
	
	/**
	 * add_class
	 * Adding class to the rendered field
	 *
	 * NOTE: It's case sensitive!
	 *
	 * @access		public
	 * @return		array
	 */
	public function add_class($params = array(), string $value = null)
	{
		// check if parameter isn't in array format
		if(!is_array($params))
		{
			// shorthand possibility, separate with commas
			$params									= array
			(
				$params								=> $value
			);
		}
		
		// merge the parameter to the older one
		$this->_add_class							= array_merge($this->_add_class, $params);
		
		return $this;
	}
	
	/**
	 * set_field
	 * Set the type of field
	 *
	 * NOTE: It's case sensitive!
	 *
	 * @access		public
	 * @return		mixed
	 */
	public function set_field($params = array(), $type = array(), $parameter = null, $extra_params = null, $another_params = null, $skip = false, $order = null)
	{
		// check if parameter isn't an array format
		if(!is_array($type))
		{
			// convert the comma separated string as array
			$type									= array_map('trim', explode(',', $type));
		}
		
		// check if parameter isn't an array format
		if(!is_array($params))
		{
			// convert parameter as array
			$params									= array
			(
				$params								=> $type
			);
		}
		
		// loops the parameters
		foreach($params as $key => $val)
		{
			// check if value isn't an array
			if(!is_array($val))
			{
				// convert the comma separated string as array
				$val								= array_map('trim', explode(',', $val));
			}
			
			// check if key is exist in the field sets
			if(isset($this->_set_field[$key]))
			{
				// loops the value
				foreach($val as $f_key => $f_val)
				{
					// check if the field type exist in the field sets
					if(isset($this->_set_field[$key]['field_type']))
					{
						if(is_array($this->_set_field[$key]['field_type']))
						{
							$this->_set_field[$key]['field_type'][]		= $f_val;
						}
					}
					
					// check if the field parameter exist in the field sets
					if(isset($this->_set_field[$key]['parameter']))
					{
						if(is_array($this->_set_field[$key]['parameter']))
						{
							$this->_set_field[$key]['parameter']		= array_merge($this->_set_field[$key]['parameter'], array($params));
						}
						else
						{
							$this->_set_field[$key]['parameter']		= array_merge(array($this->_set_field[$key]['parameter']), array($params));
						}
					}
					
					// check if the field extra parameter exist in the field sets
					if(isset($this->_set_field[$key]['extra_params']))
					{
						if(is_array($this->_set_field[$key]['extra_params']))
						{
							$this->_set_field[$key]['extra_params']		= array_merge($this->_set_field[$key]['extra_params'], array($extra_params));
						}
						else
						{
							$this->_set_field[$key]['extra_params']		= array_merge(array($this->_set_field[$key]['extra_params']), array($extra_params));
						}
					}
					
					// check if the another parameter of field exist in the field sets
					if(isset($this->_set_field[$key]['another_params']))
					{
						if(is_array($this->_set_field[$key]['another_params']))
						{
							$this->_set_field[$key]['another_params']	= array_merge($this->_set_field[$key]['another_params'], array($another_params));
						}
						else
						{
							$this->_set_field[$key]['another_params']	= array_merge(array($this->_set_field[$key]['another_params']), array($another_params));
						}
					}
					
					// check if the skip command exist in the field sets
					if(isset($this->_set_field[$key]['skip']))
					{
						if(is_array($this->_set_field[$key]['skip']))
						{
							$this->_set_field[$key]['skip'][]			= $skip;
						}
					}
				}
			}
			else
			{
				$this->_set_field[$key]				= array
				(
					'field_type'					=> $val,
					'parameter'						=> $parameter,
					'extra_params'					=> $extra_params,
					'another_params'				=> $another_params,
					'skip'							=> $skip,
					'order'							=> $order
				);
			}
		}
		
		if($extra_params && is_array($extra_params) && in_array('hyperlink', $type))
		{
			$this->_parameter						= array_merge($this->_parameter, array_values($extra_params));
		}
		
		return $this;
	}
	
	/**
	 * set_tooltip
	 * Add the tooltip on field label when hovered
	 *
	 * @access		public
	 * @return		mixed
	 */
	public function set_tooltip($params = array(), string $value = null)
	{
		if(!is_array($params))
		{
			// shorthand possibility, separate with commas
			$params									= array
			(
				$params								=> $value
			);
		}
		
		$this->_set_tooltip							= array_merge($this->_set_tooltip, $params);
		
		return $this;
	}
	
	/**
	 * unset_field
	 * The function to unset the field
	 *
	 * NOTE: It's case sensitive!
	 *
	 * @access		public
	 * @param		mixed		$params
	 * @return		mixed
	 */
	public function unset_field($params = array())
	{
		// shorthand possibility, separate with commas
		$params										= array_map('trim', explode(',', $params));
		
		$this->_unset_field							= array_merge($this->_unset_field, $params);
		
		return $this;
	}
	
	/**
	 * unset_column
	 * The function to unset the column
	 *
	 * NOTE: It's case sensitive!
	 *
	 * @access		public
	 * @param		mixed		$params
	 * @return		mixed
	 */
	public function unset_column($params = array())
	{
		// shorthand possibility, separate with commas
		$params										= array_map('trim', explode(',', $params));
		
		$this->_unset_column						= array_merge($this->_unset_column, $params);
		
		return $this;
	}
	
	/**
	 * unset_view
	 * The function to unset the field on view data
	 *
	 * NOTE: It's case sensitive!
	 *
	 * @access		public
	 * @param		mixed		$params
	 * @return		mixed
	 */
	public function unset_view($params = array())
	{
		// shorthand possibility, separate with commas
		$params										= array_map('trim', explode(',', $params));
		
		$this->_unset_view							= array_merge($this->_unset_view, $params);
		
		return $this;
	}
	
	/**
	 * column_order
	 * The function to rearrange the columns
	 *
	 * NOTE: It's case sensitive!
	 *
	 * @access		public
	 * @param		mixed		$column
	 * @return		mixed
	 */
	public function column_order($column = array())
	{
		if(!is_array($column))
		{
			// shorthand possibility, separate with commas
			$column									= array_map('trim', explode(',', $column));
		}
		
		$this->_column_order						= $column;
		
		return $this;
	}
	
	/**
	 * view_order
	 * The function to rearrange the field on view data
	 *
	 * NOTE: It's case sensitive!
	 *
	 * @access		public
	 * @param		mixed		$params
	 * @return		mixed
	 */
	public function view_order($params = array())
	{
		if(!is_array($params))
		{
			// shorthand possibility, separate with commas
			$params									= array_map('trim', explode(',', $params));
		}
		
		$this->_view_order							= $params;
		
		return $this;
	}
	
	/**
	 * field_order
	 * The function to rearrange the field in form
	 *
	 * NOTE: It's case sensitive!
	 *
	 * @access		public
	 * @param		mixed		$params
	 * @return		mixed
	 */
	public function field_order($params = array())
	{
		if(!is_array($params))
		{
			// shorthand possibility, separate with commas
			$params									= array_map('trim', explode(',', $params));
		}
		
		$this->_field_order							= $params;
		
		return $this;
	}
	
	/**
	 * unset_read
	 * The function to disable read the selected items
	 *
	 * @access		public
	 * @param		mixed		$params
	 * @param		string		$value
	 * @return		mixed
	 */
	public function unset_read($params = array(), array $value = array())
	{
		if(!is_array($params))
		{
			$params									= array
			(
				$params								=> $value
			);
		}
		
		$this->_unset_read							= array_merge($this->_unset_read, $params);
		
		return $this;
	}
	
	/**
	 * unset_update
	 * The function to disable update the selected items
	 *
	 * @access		public
	 * @param		mixed		$params
	 * @param		string		$value
	 * @return		mixed
	 */
	public function unset_update($params = array(), array $value = array())
	{
		if(!is_array($params))
		{
			$params									= array
			(
				$params								=> $value
			);
		}
		
		$this->_unset_update						= array_merge($this->_unset_update, $params);
		
		return $this;
	}
	
	/**
	 * unset_delete
	 * The function to disable delete the selected items
	 *
	 * @access		public
	 * @param		mixed		$params
	 * @param		string		$value
	 * @return		mixed
	 */
	public function unset_delete($params = array(), array $value = array())
	{
		if(!is_array($params))
		{
			$params									= array
			(
				$params								=> $value
			);
		}
		
		$this->_unset_delete						= array_merge($this->_unset_delete, $params);
		
		return $this;
	}
	
	/**
	 * set_default
	 * The function to set default value on form field so user cannot
	 * make any changes
	 *
	 * NOTE: It's case sensitive!
	 *
	 * @access		public
	 * @param		mixed		$params
	 * @param		string		$default
	 * @return		mixed
	 */
	public function set_default($params = array(), $value = null)
	{
		if(!is_array($params))
		{
			$params									= array
			(
				$params								=> $value
			);
		}
		
		$this->_set_default							= array_merge($this->_set_default, $params);
		
		return $this;
	}
	
	/**
	 * set_validation
	 * Add the field to the form validation
	 *
	 * NOTE: It's case sensitive!
	 *
	 * @access		public
	 * @param		mixed		$params
	 * @param		string		$value
	 * @return		mixed
	 */
	public function set_validation($params = array(), string $value = null)
	{
		if(!is_array($params))
		{
			$params									= array
			(
				$params								=> $value
			);
		}
		
		$this->_set_validation						= array_merge($this->_set_validation, $params);
		
		return $this;
	}
	
	/**
	 * set_upload_path
	 * Set the upload path to follow the custom path
	 *
	 * @access		public
	 * @param		string		$path
	 * @return		string
	 */
	public function set_upload_path(string $path = null)
	{
		// validate the given parameter is valid path name
		if($path && preg_match('/^[a-z0-9\-\.\_\/]*$/', $path))
		{
			$this->_set_upload_path					= strtolower($path);
		}
		else
		{
			$path									= strtolower(str_replace('\\', '/', service('router')->controllerName()));
			$path									= preg_replace(array('/\/aksara\/modules\//', '/\/modules\//', '/\/controllers\//'), array('', '', '/'), $path, 1);
			$path									= array_unique(explode('/', $path));
			
			$this->_set_upload_path					= (isset($path[1]) ? $path[1] : $path[0]);
		}
		
		set_userdata('_set_upload_path', $this->_set_upload_path);
		
		return $this;
	}
	
	/**
	 * form_callback
	 * Create custom callback of form validation
	 *
	 * @access		public
	 * @param		string		$callback
	 * @return		mixed
	 */
	public function form_callback(string $callback)
	{
		$this->_form_callback						= $callback;
		
		return $this;
	}
	
	/**
	 * set_alias
	 * Set the alias of column, the selected column of database table
	 * table will not translated
	 *
	 * NOTE: It's case sensitive!
	 *
	 * @access		public
	 * @param		mixed		$params
	 * @param		string		$value
	 * @return		mixed
	 */
	public function set_alias($params = array(), string $value = null)
	{
		if(!is_array($params))
		{
			$params									= array
			(
				$params								=> $value
			);
		}
		
		$this->_set_alias							= array_merge($this->_set_alias, $params);
		
		return $this;
	}
	
	/**
	 * set_heading
	 * Add heading before field
	 *
	 * NOTE: It's case sensitive!
	 *
	 * @access		public
	 * @param		mixed		$params
	 * @param		string		$value
	 * @return		mixed
	 */
	public function set_heading($params = array(), string $value = null)
	{
		if(!is_array($params))
		{
			$params									= array
			(
				$params								=> $value
			);
		}
		
		$this->_set_heading							= array_merge($this->_set_heading, $params);
		
		return $this;
	}
	
	/**
	 * set_output
	 * The function to push the additional data to the query result
	 *
	 * @access		public
	 * @param		mixed		$params
	 * @param		string		$value
	 * @return		mixed
	 */
	public function set_output($params = array(), $value = null)
	{
		if(!is_array($params))
		{
			$params									= array
			(
				$params								=> $value
			);
		}
		
		$this->_set_output							= array_merge($this->_set_output, $params);
		
		return $this;
	}
	
	/**
	 * unset_truncate
	 * Prevent the field to be truncated
	 *
	 * NOTE: It's case sensitive!
	 *
	 * @access		public
	 * @param		mixed		$field
	 * @return		mixed
	 */
	public function unset_truncate($field = array())
	{
		// shorthand possibility, separate with commas
		$field										= array_map('trim', explode(',', $field));
		
		$this->_unset_truncate						= array_merge($this->_unset_truncate, $field);
		
		return $this;
	}
	
	/**
	 * modal_size
	 * Set how wide the modal will be displayed, available params:
	 * modal-sm, modal-lg, modal-xl
	 *
	 * @access		public
	 * @param		string		$size
	 * @return		mixed
	 */
	public function modal_size(string $size)
	{
		$this->_modal_size							= strtolower($size);
		
		return $this;
	}
	
	/**
	 * field_position
	 * Arrange the field to the position
	 *
	 * NOTE: It's case sensitive!
	 *
	 * @access		public
	 * @param		mixed		$params
	 * @param		string		$value
	 * @return		mixed
	 */
	public function field_position($params = array(), string $value = null)
	{
		if(!is_array($params))
		{
			$params									= array
			(
				$params								=> $value
			);
		}
		
		$this->_field_position						= $params;
		
		return $this;
	}
	
	/**
	 * column_size
	 * Add the custom column size, it works when using bootstrap
	 * framework that can be applied to column grid size
	 *
	 * NOTE: It's case sensitive!
	 *
	 * @access		public
	 * @param		mixed		$field
	 * @param		string		$value
	 * @return		mixed
	 */
	public function column_size($params = array(), string $value = null)
	{
		if(!is_array($params))
		{
			$params									= array
			(
				$params								=> $value
			);
		}
		
		$this->_column_size							= array_merge($this->_column_size, $params);
		
		return $this;
	}
	
	/**
	 * field_size
	 * Add the custom field size, it works when using bootstrap
	 * framework that can be applied to field grid size
	 *
	 * NOTE: It's case sensitive!
	 *
	 * @access		public
	 * @param		mixed		$params
	 * @param		string		$value
	 * @return		mixed
	 */
	public function field_size($params = array(), string $value = null)
	{
		if(!is_array($params))
		{
			$params									= array
			(
				$params								=> $value
			);
		}
		
		$this->_field_size							= array_merge($this->_field_size, $params);
		
		return $this;
	}
	
	/**
	 * field_prepend
	 * Add the prefix to the field
	 */
	public function field_prepend($params = array(), string $value = null)
	{
		if(!is_array($params))
		{
			$params									= array
			(
				$params								=> $value
			);
		}
		
		$this->_field_prepend						= array_merge($this->_field_prepend, $params);
		
		return $this;
	}
	
	/**
	 * field_append
	 * Add the suffix to the field
	 */
	public function field_append($params = array(), string $value = null)
	{
		if(!is_array($params))
		{
			$params									= array
			(
				$params								=> $value
			);
		}
		
		$this->_field_append						= array_merge($this->_field_append, $params);
		
		return $this;
	}
	
	/**
	 * merge_content
	 * Merge the multiple field in one
	 *
	 * NOTE: It's case sensitive!
	 *
	 * @access		public
	 * @param		string		$magic_string
	 * @param		string		$alias
	 * @param		string		$callback
	 * @return		mixed
	 */
	public function merge_content(string $magic_string, string $alias = null, string $callback = null)
	{
		// get the fields from the magic string
		preg_match_all('#\{(.*?)\}#', $magic_string, $matches);
		
		$matches									= $matches[1];
		$field										= (isset($matches[0]) ? $matches[0] : null);
		
		// check if the current method isn't matches with the restricted one
		if(!in_array($this->_method, array('create', 'update')))
		{
			$this->_set_alias[$field]				= ($alias && !is_array($alias) ? $alias : ucwords(str_replace('_', ' ', $field)));
			$this->_merge_label[$field]				= ($alias && !is_array($alias) ? $alias : ucwords(str_replace('_', ' ', $field)));
		}
		
		// sets the new key to merge property
		$this->_merge_content[$field]				= array
		(
			'column'								=> $matches,
			'parameter'								=> ($magic_string && !is_array($magic_string) ? $magic_string : null),
			'callback'								=> ($callback ? str_replace('callback_', '', $callback) : null)
		);
		
		// check if matches key is available from given magic string
		if($matches)
		{
			// loops the keys
			foreach($matches as $key => $val)
			{
				// skip the null key
				if($key == 0) continue;
				
				$this->_unset_column[]				= $val;
				$this->_unset_view[]				= $val;
			}
		}
		
		return $this;
	}
	
	/**
	 * merge_field
	 * Merge the multiple field in one, useable in form
	 *
	 * NOTE: It's case sensitive!
	 *
	 * @access		public
	 */
	public function merge_field($params = array())
	{
		if(!is_array($params))
		{
			// shorthand possibility, separate with commas
			$params									= array_map('trim', explode(',', $params));
		}
		
		$this->_merge_field[$params[0]]				= $params;
		
		return $this;
	}
	
	/**
	 * group_field
	 * Merge the multiple field in one, useable in form
	 *
	 * NOTE: It's case sensitive!
	 *
	 * @access		public
	 */
	public function group_field($params = array(), string $group = null)
	{
		if(!is_array($params))
		{
			// shorthand possibility, separate with commas
			$params									= array_map('trim', explode(',', $params));
		}
		
		$this->_group_field							= array_merge($this->_group_field, array_fill_keys($params, $group));
		
		return $this;
	}
	
	/**
	 * item_reference
	 * Group row with the given parent from the field reference
	 *
	 * NOTE: It's case sensitive
	 *
	 * @access		public
	 */
	public function item_reference($params = array())
	{
		if(!is_array($params))
		{
			// shorthand possibility, separate with commas
			$params									= array_map('trim', explode(',', $params));
		}
		
		$this->_item_reference						= array_merge($this->_item_reference, $params);
		
		return $this;
	}
	
	/**
	 * set_attribute
	 * Add the extra attribute into field input
	 *
	 * NOTE: It's case sensitive!
	 *
	 * @access		public
	 */
	public function set_attribute($params = array(), string $value = null)
	{
		if(!is_array($params))
		{
			$params									= array
			(
				$params								=> $value
			);
		}
		
		$this->_set_attribute						= array_merge($this->_set_attribute, $params);
		
		return $this;
	}
	
	/**
	 * set_placeholder
	 * Add the extra attribute into field input
	 *
	 * NOTE: It's case sensitive!
	 *
	 * @access		public
	 */
	public function set_placeholder($params = array(), string $value = null)
	{
		if(!is_array($params))
		{
			$params									= array
			(
				$params								=> $value
			);
		}
		
		$this->_set_placeholder						= array_merge($this->_set_placeholder, $params);
		
		return $this;
	}
	
	/**
	 * set_option_label
	 * Add the extra label into option (select) field
	 *
	 * NOTE: It's case sensitive!
	 *
	 * @access		public
	 */
	public function set_option_label($params = array(), string $value = null)
	{
		if(!is_array($params))
		{
			$params									= array
			(
				$params								=> $value
			);
		}
		
		$this->_set_option_label					= array_merge($this->_set_option_label, $params);
		
		return $this;
	}
	
	/**
	 * This function will give the default value of the field
	 * input with parameter that sets to it
	 *
	 * @access		public
	 * @param		mixed		$field
	 * @param		string		$value
	 */
	public function default_value($field = array(), $value = 0)
	{
		if(!is_array($field))
		{
			$field									= array
			(
				$field								=> $value
			);
		}
		
		$this->_default_value						= array_merge($field, $this->_default_value);
		
		return $this;
	}
	
	/**
	 * set_relation
	 * Set the field as relation, to be related to the other field
	 *
	 * NOTE: It's case sensitive!
	 *
	 * @access		public
	 */
	public function set_relation(string $field, string $selected_value, string $formatting, $where = array(), $join = array(), $order_by = array(), $group_by = null, int $limit = 0, bool $translate = false)
	{
		$as_field									= $field;
		
		// extract the fields from the given magic string
		preg_match_all('#\{(.*?)\}#', $formatting, $matches);
		
		$select										= $matches[1];
		
		if($translate)
		{
			foreach($select as $key => $val)
			{
				$this->_translate_field[]			= substr(strstr($val, '.'), 1);
			}
		}
		
		if(strpos($field, ',') !== false && strpos($selected_value, ',') !== false)
		{
			$field									= array_map('trim', explode(',', $field));
			$selected_value							= array_map('trim', explode(',', $selected_value));
			$as_field								= $field[0];
			$relation_key							= array();
			$group_by								= array();
			
			foreach($selected_value as $key => $val)
			{
				if(!in_array($val, $select))
				{
					$select[]						= $val;
					$group_by[]						= $val;
					
					$this->_unset_column[]			= $val;
					$this->_unset_view[]			= $val;
				}
				
				$explode							= explode('.', $val);
				
				if(sizeof($explode) == 2)
				{
					$relation_table					= $explode[0];
					$relation_key[]					= $explode[1];
					$this->_unset_column[]			= $explode[1] . '_' . $explode[0];
					$this->_unset_view[]			= $explode[1] . '_' . $explode[0];
					
					if($key > 0)
					{
						$this->_unset_field[]		= $explode[1];
					}
					else
					{
						$this->_unset_column[]		= $explode[1];
						$this->_unset_view[]		= $explode[1];
						
						array_unshift($select, $explode[0] . '.' . $field[0] . ' AS ' . $as_field . '_masking');
					}
				}
			}
		}
		else
		{
			if(!in_array($selected_value, $select))
			{
				$select[]							= (strpos($selected_value, ' ') !== false ? substr($selected_value, strpos($selected_value, ' ') + 1) : $selected_value) . ' AS ' . $as_field;
			}
			
			if(isset($this->_set_attribute[$field]))
			{
				preg_match_all('#\{(.*?)\}#', $this->_set_attribute[$field], $matches_attributes);
				
				$select								= array_merge($select, $matches_attributes[1]);
			}
			
			$selected_value							= explode('.', $selected_value);
			$relation_table							= (isset($selected_value[0]) ? $selected_value[0] : null);
			$relation_key							= (isset($selected_value[1]) ? $selected_value[1] : null);
			
			if(!$group_by && $relation_table && $relation_key)
			{
				//$group_by							= (strpos($relation_table, ' ') !== false ? substr($relation_table, strpos($relation_table, ' ') + 1) : $relation_table) . '.' . $relation_key;
			}
			
			$this->_unset_column[]					= $field;
			$this->_unset_view[]					= $field;
		}
		
		if($join && !isset($join[0]))
		{
			$join									= array($join);
		}
		
		if($where)
		{
			foreach($where as $key => $val)
			{
				if(strpos($key, '.') === false && strpos($key, ' ') === false && strpos($key, '(') === false && strpos($key, ')') === false)
				{
					unset($where[$key]);
					
					$key							= $relation_table . '.' . $key;
					$where[$key]					= $val;
				}
			}
		}
		
		$this->_set_relation[$as_field]				= array
		(
			'select'								=> $select,
			'formatting'							=> $formatting,
			'primary_key'							=> $field,
			'relation_table'						=> $relation_table,
			'relation_key'							=> $relation_key,
			'where'									=> ($where ? array_filter($where) : null),
			'join'									=> $join,
			'order_by'								=> $order_by,
			'group_by'								=> $group_by,
			'limit'									=> $limit,
			'offset'								=> (is_numeric(service('request')->getPost('page')) ? service('request')->getPost('page') - 1 : 0) * $limit,
			'translate'								=> $translate
		);
		
		$this->_select								= array_merge($this->_select, $select);
		
		if(!in_array($this->_method, array('create', 'update', 'delete')))
		{
			$condition								= null;
			
			if(is_array($field))
			{
				foreach($field as $key => $val)
				{
					$condition						.= ($condition ? ' AND ' : null) . (strpos($relation_table, ' ') !== false ? substr($relation_table, strpos($relation_table, ' ') + 1) : $relation_table) . '.' . $val . ' = {primary_table}.' . $val;
					
					// apply validation
					$this->set_validation($val, 'relation_checker[' . (strpos($relation_table, ' ') !== false ? substr($relation_table, 0, strpos($relation_table, ' ')) : $relation_table) . '.' . $val . ']');
				}
			}
			else
			{
				// apply validation
				$this->set_validation($field, 'relation_checker[' . (strpos($relation_table, ' ') !== false ? substr($relation_table, 0, strpos($relation_table, ' ')) : $relation_table) . '.' . $relation_key . ']');
			}
			
			$this->_compiled_table[]				= $relation_table;
			
			$this->_join[$relation_table]			= array
			(
				'condition'							=> ($condition ? $condition : (strpos($relation_table, ' ') !== false ? substr($relation_table, strpos($relation_table, ' ') + 1) : $relation_table) . '.' . $relation_key . ' = {primary_table}.' . $field),
				'type'								=> 'LEFT',
				'escape'							=> true
			);
			
			if($join)
			{
				foreach($join as $key => $val)
				{
					$this->_compiled_table[]		= $val[0];
					
					$this->_join[$val[0]]			= array
					(
						'condition'					=> $val[1],
						'type'						=> (isset($val[2]) ? $val[2] : 'LEFT'),
						'escape'					=> true
					);
				}
			}
		}
		
		return $this;
	}
	
	/**
	 * _get_relation
	 * Get the relation of relation type field
	 *
	 * @access		private
	 */
	private function _get_relation($params = array(), $selected = null, $limit = null, $self = false)
	{
		if(!$selected && (isset($this->_default_value[$params['primary_key']])))
		{
			$selected								= $this->_default_value[$params['primary_key']];
		}
		
		if($limit && !$self)
		{
			$relation_key							= $params['relation_table'] . '.' . $params['relation_key'];
			$params['limit']						= $limit;
			$params['where'][$relation_key]			= (strpos($selected, ' ') !== false ? substr($selected, strpos($selected, ' ') + 1) : $selected);
		}
		
		if(service('request')->isAJAX() && 'ajax_select' == service('request')->getPost('method') && isset($params['limit']) && $params['limit'] > 1)
		{
			$output									= array();
		}
		else
		{
			$output									= null;
		}
		
		$is_selected_exist							= false;
		$replace_me									= array();
		$compiled_select							= array();
		$primary_key								= (is_array($params['primary_key']) ? end($params['primary_key']) : $params['primary_key']);
		$like										= array();
		
		foreach($params['select'] as $key => $val)
		{
			$checker								= explode('.', $val);
			$column									= (isset($checker[1]) ? $checker[1] : $val);
			$table									= (isset($checker[0]) ? $checker[0] : null);
			
			if(in_array($column, $compiled_select))
			{
				$replace_me[$val]					= $column . '_' . $table;
				
				if($table != $this->_table)
				{
					$val							= $val . ' AS ' . $column . '_' . $table;
				}
			}
			
			$this->model->select($val);
			
			$compiled_select[]						= $column;
			
			if(service('request')->getPost('method') == 'ajax_select' && service('request')->getPost('search'))
			{
				if($val && stripos($val, ' AS ') !== false)
				{
					$val							= substr($val, 0, stripos($val, ' AS '));
				}
				
				$like[$val]							= service('request')->getPost('search');
			}
		}
		
		if($like && !$limit)
		{
			$num									= 0;
			
			$this->model->group_start();
			
			foreach($like as $key => $val)
			{
				if($num)
				{
					$this->model->or_like($key, $val, 'both', true, true);
				}
				else
				{
					$this->model->like($key, $val, 'both', true, true);
				}
				$num++;
			}
			
			$this->model->group_end();
		}
		
		if($params['join'])
		{
			foreach($params['join'] as $key => $val)
			{
				if(!isset($val[0]) || !isset($val[1])) continue;
				
				$this->model->join($val[0], $val[1], (isset($val[2]) ? $val[2] : ''));
			}
		}
		
		if($self)
		{
			$relation_key							= $params['relation_table'] . '.' . $params['relation_key'];
			$params['limit']						= $limit;
			$params['where'][$relation_key]			= (strpos($selected, ' ') !== false ? substr($selected, strpos($selected, ' ') + 1) : $selected);
			
		}
		
		if($params['where'])
		{
			foreach($params['where'] as $key => $val)
			{
				$field_origin						= (strpos($key, '.') !== false ? substr($key, strpos($key, '.') + 1) : $key);
				
				if(is_numeric($field_origin) && $val && stripos(trim($val), ' NOT IN') !== false)
				{
					$this->model->where($val, null, false);
				}
				else if(is_numeric($field_origin) && $val && stripos(trim($val), ' IN') !== false)
				{
					$this->model->where($val, null, false);
				}
				else if(stripos(trim($key), ' NOT IN') !== false)
				{
					$this->model->where_not_in(str_ireplace(' NOT IN', null, $key), $val, false);
				}
				else if(stripos(trim($key), ' IN') !== false)
				{
					$this->model->where_in(str_ireplace(' IN', null, $key), $val, false);
				}
				else
				{
					$this->model->where($key, $val);
				}
			}
		}
		
		if(!in_array($this->_method, array('create', 'update')))
		{
			if(is_array($params['relation_key']))
			{
				$selected							= explode('.', $selected);
				
				foreach($params['relation_key'] as $key => $val)
				{
					if(!isset($selected[$key])) continue;
					
					$this->model->where((strpos($params['relation_table'], ' ') !== false ? substr($params['relation_table'], strpos($params['relation_table'], ' ') + 1) : $params['relation_table']) . '.' . $val, $selected[$key]);
				}
			}
			else
			{
				$this->model->where((strpos($params['relation_table'], ' ') !== false ? substr($params['relation_table'], strpos($params['relation_table'], ' ') + 1) : $params['relation_table']) . '.' . $params['relation_key'], $selected);
			}
			
			$params['limit']						= 1;
		}
		
		if($params['order_by'] && !$limit)
		{
			if(is_array($params['order_by']))
			{
				foreach($params['order_by'] as $key => $val)
				{
					$this->model->order_by($key, $val);
				}
			}
			else
			{
				$this->model->order_by($params['order_by']);
			}
		}
		
		if($params['join'] && $params['group_by'] && !$limit)
		{
			$this->model->group_by($params['group_by']);
		}
		
		$query										= $this->model->get($params['relation_table'], $params['limit'], $params['offset'])->result();
		
		if($query)
		{
			foreach($query as $key => $val)
			{
				$label								= str_ireplace(' AS ', ' ', $params['formatting']);
				$attributes							= str_ireplace(' AS ', ' ', (isset($this->_set_attribute[$primary_key]) ? $this->_set_attribute[$primary_key] : ''));
				$option_label						= str_ireplace(' AS ', ' ', (isset($this->_set_option_label[$primary_key]) ? $this->_set_option_label[$primary_key] : ''));
				
				foreach($params['select'] as $magic => $replace)
				{
					$replace						= str_ireplace(' AS ', ' ', $replace);
					
					if(isset($replace_me[$replace]))
					{
						$replacement				= $replace_me[$replace];
					}
					else
					{
						$replacement				= ($replace && stripos($replace, '.') !== false ? substr($replace, strripos($replace, '.') + 1) : $replace);
						$replacement				= ($replacement && stripos($replacement, ' ') !== false ? substr($replacement, strripos($replacement, ' ') + 1) : $replacement);
					}
					
					if(isset($val->$replacement))
					{
						if(isset($this->_set_field[$replacement]['field_type']) && in_array('sprintf', $this->_set_field[$replacement]['field_type']))
						{
							$val->$replacement		= sprintf('%02d', $val->$replacement);
						}
						
						$label						= str_replace('{' . $replace . '}', $val->$replacement, $label);
						$attributes					= str_replace('{' . $replace . '}', $val->$replacement, $attributes);
						$option_label				= str_replace('{' . $replace . '}', $val->$replacement, $option_label);
					}
				}
				
				if(in_array($this->_method, array('create', 'update')))
				{
					if(is_array($params['primary_key']))
					{
						$value						= null;
						$selected					= null;
						
						foreach($params['primary_key'] as $_key => $_val)
						{
							$value					.= ($value ? '.' : null) . (isset($val->$_val) ? $val->$_val : 0);
							
							if(service('request')->getGet($_val))
							{
								$selected			.= ($selected ? '.' : null) . service('request')->getGet($_val);
							}
						}
					}
					else
					{
						$primary_key				= $params['primary_key'];
						$relation_key				= $params['relation_key'];
						$value						= (isset($val->$primary_key) ? $val->$primary_key : (isset($val->$relation_key) ? $val->$relation_key : 0));
					}
					
					if($value == $selected)
					{
						$is_selected_exist			= true;
					}
					
					if($limit)
					{
						return ($params['translate'] ? phrase($label) : $label);
					}
					else if(service('request')->isAJAX() && 'ajax_select' == service('request')->getPost('method') && isset($params['limit']) && $params['limit'] > 1)
					{
						$output[]					= array
						(
							'id'					=> $value,
							'text'					=> ($params['translate'] ? phrase($label) : $label)
						);
					}
					else
					{
						$output						.= '<option value="' . $value . '"' . ($attributes ? ' ' . $attributes : '') . ($option_label ? '  data-label="' . $option_label . '"' : null) . ($value == $selected ? ' selected' : null) . '>' . ($params['translate'] ? phrase($label) : $label) . '</option>';
					}
				}
				else
				{
					$output							= ($params['translate'] ? phrase($label) : $label);
				}
			}
		}
		
		if(service('request')->isAJAX() && 'ajax_select' == service('request')->getPost('method') && isset($this->_set_relation[service('request')->getPost('source')]))
		{
			return make_json
			(
				array
				(
					'results'						=> $output,
					'pagination'					=> array
					(
						'more'						=> ($output && sizeof($output) >= 50 ? true : false)
					)
				)
			);
		}
		else if(in_array($this->_method, array('create', 'update')))
		{
			$output									= '
				<select name="' . $primary_key . '" class="form-control' . (isset($this->_add_class[$primary_key]) ? ' ' . $this->_add_class[$primary_key] : null) . '" placeholder="' . (isset($this->_set_placeholder[$primary_key]) ? $this->_set_placeholder[$primary_key] : phrase('please_choose')) . '" id="' . $primary_key . '_input"' . (isset($this->_set_attribute[$primary_key]) ? ' ' . $this->_set_attribute[$primary_key] : null) . (isset($params['limit']) && $params['limit'] > 1 ? ' data-limit="' . $params['limit'] . '" data-href="' . current_page() . '"' : null) . (isset($this->_set_field[$primary_key]['field_type']) && in_array('disabled', $this->_set_field[$primary_key]['field_type']) ? ' disabled' : null) . '>
					' . ($query && !$is_selected_exist && $selected ? '<option value="' . $selected . '">' . $this->_get_relation($params, $selected, 1, true) . '</option>' : '<option value="' . (isset($this->_set_validation[$primary_key]) && stripos($this->_set_validation[$primary_key], 'required') !== false ? null : 0) . '">' . phrase('please_choose') . '</option>') . '
					' . $output . '
				</select>
			';
		}
		
		return $output;
	}
	
	/**
	 * set_autocomplete
	 * Set the field as autocomplete
	 *
	 * @access		public
	 */
	public function set_autocomplete(string $field, string $selected_value, string $formatting, array $where = array(), array $join = array(), $order_by = array(), string $group_by = null, int $limit = 0)
	{
		$value										= (isset($formatting['value']) ? $formatting['value'] : (isset($formatting[0]) ? $formatting[0] : null));
		$label										= (isset($formatting['label']) ? $formatting['label'] : (isset($formatting[1]) ? $formatting[1] : null));
		$description								= (isset($formatting['description']) ? $formatting['description'] : (isset($formatting[2]) ? $formatting[2] : null));
		$image										= (isset($formatting['image']) ? $formatting['image'] : (isset($formatting[3]) ? $formatting[3] : null));
		$select										= $value . $label . $description . $image;
		preg_match_all('#\{(.*?)\}#', $select, $matches_select);
		$select										= (isset($matches_select[1]) ? $matches_select[1] : array());
		$select[]									= $selected_value . ' AS ' . $field;
		$selected_value								= explode('.', $selected_value);
		$relation_table								= (isset($selected_value[0]) ? $selected_value[0] : null);
		$relation_key								= (isset($selected_value[1]) ? $selected_value[1] : null);
		
		if($join && !isset($join[0]))
		{
			$join									= array($join);
		}
		
		if(!$group_by)
		{
			$group_by								= $relation_table . '.' . $relation_key;
		}
		
		$formatting									= array
		(
			'value'									=> $value,
			'label'									=> $label,
			'description'							=> $description,
			'image'									=> $image
		);
		
		$this->_set_autocomplete[$field]			= array
		(
			'select'								=> $select,
			'formatting'							=> $formatting,
			'primary_key'							=> $field,
			'relation_table'						=> $relation_table,
			'relation_key'							=> $relation_key,
			'where'									=> ($where ? array_filter($where) : null),
			'join'									=> $join,
			'order_by'								=> $order_by,
			'group_by'								=> $group_by,
			'limit'									=> $limit
		);
		
		$this->_select								= array_unique((sizeof($this->_select) > 0 ? array_merge($this->_select, $select) : $select));
		$this->_unset_column[]						= $field;
		$this->_unset_view[]						= $field;
		
		if(!in_array($this->_method, array('create', 'update', 'delete')) || ('autocomplete' == service('request')->getPost('method') && service('request')->getPost('origin')))
		{
			$this->_join[$relation_table]			= array
			(
				'condition'							=> $relation_table . '.' . $relation_key . ' = {primary_table}. ' . $field,
				'type'								=> '',
				'escape'							=> true
			);
			
			if($join)
			{
				foreach($join as $key => $val)
				{
					$this->_join[$val[0]]			= array
					(
						'condition'					=> $val[1],
						'type'						=> (isset($val[2]) ? $val[2] : ''),
						'escape'					=> true
					);
				}
			}
		}
		
		return $this;
	}
	
	/**
	 * _autocomplete_input
	 * Generate input for autocomplete field
	 *
	 * @access		private
	 */
	private function _autocomplete_input($params = array(), $selected = null)
	{
		return '<input type="text" name="' . $params['primary_key'] . '" class="form-control' . (isset($this->_add_class[$params['primary_key']]) ? ' ' . $this->_add_class[$params['primary_key']] : null) . '" value="' . ($selected && isset($output[0]['label']) ? $output[0]['label'] : null) . '" role="autocomplete" id="' . $params['primary_key'] . '_input"' . (isset($this->_set_field[$params['primary_key']]['field_type']) && in_array('disabled', $this->_set_field[$params['primary_key']]['field_type']) ? ' disabled' : null) . ' spellcheck="false" /><input type="hidden" name="' . $params['primary_key'] . '" id="' . $params['primary_key'] . '_input_value" value="' . ('update' == $this->_method && isset($output[0]['value']) ? $output[0]['value'] : 0) . '" spellcheck="false" />';
		
	}
	
	/**
	 * render
	 * Rendering the result into view
	 *
	 * @access		public
	 * @param		string		$table
	 * @param		string		$view
	 * @return		mixed
	 */
	public function render(string $table = null, string $view = null)
	{
		if($this->_api_request && in_array(service('request')->getServer('REQUEST_METHOD'), array('POST', 'DELETE')) && !in_array($this->_method, array('create', 'update', 'delete')))
		{
			return throw_exception(403, phrase('the_method_you_requested_is_not_acceptable') . ' (' . service('request')->getServer('REQUEST_METHOD'). ')', (!$this->_api_request ? $this->_redirect_back : null));
		}
		
		if(!$this->_table)
		{
			// set table when not present
			$this->_table							= $table;
			
			// push to compiled table
			$this->_compiled_table[]				= $table;
		}
		
		/**
		 * Token checker
		 */
		if(service('request')->getGet())
		{
			$token									= service('request')->getGet('aksara');
			$query_string							= service('request')->getGet();
			
			// validate token
			if(!$this->_api_request && $this->_set_permission && $query_string && $token != generate_token($query_string))
			{
				// token is missmatch, throw an exception
				return throw_exception(403, phrase('the_token_you_submitted_has_been_expired_or_you_are_trying_to_bypass_it_from_the_restricted_source'), base_url());
			}
		}
		
		// validate the restricted action
		if(in_array($this->_method, $this->_unset_action))
		{
			return throw_exception(403, phrase('you_cannot_perform_the_requested_action'), $this->_redirect_back);
		}
		
		// check before action
		if('create' == $this->_method && method_exists($this, 'before_insert'))
		{
			// before insert
			$this->before_insert();
		}
		else if('update' == $this->_method && method_exists($this, 'before_update'))
		{
			// before update
			$this->before_update();
		}
		else if('delete' == $this->_method && method_exists($this, 'before_delete'))
		{
			// before delete
			$this->before_delete();
		}
		
		$this->template								= new Template($this->_set_theme, $this->_api_request);
		
		// check if given table is exists in database
		if($this->_table)
		{
			if(!$this->model->table_exists($this->_table))
			{
				return throw_exception(501, phrase('the_defined_primary_table_does_not_exists'), current_page('../'));
			}
			
			$this->_field_data						= json_decode(json_encode($this->model->field_data($this->_table)), true);
			$this->_index_data						= $this->model->index_data($this->_table);
			$order_by								= array();
			
			// set the default primary if the table have any primary column
			if(!$this->_set_primary && $this->_field_data)
			{
				// loops to get the primary key
				foreach($this->_field_data as $key => $val)
				{
					// check if the field has primary key
					if((isset($val['primary_key']) && $val['primary_key']) || ($val['default'] && stripos($val['default'], 'nextval(') !== false))
					{
						// push primary key
						$this->_set_primary[]		= $val['name'];
					}
				}
				
				// make the array unique
				$this->_set_primary					= array_unique($this->_set_primary);
			}
			
			// set the default primary if the table have any primary column
			if(!$this->_set_primary && $this->_index_data)
			{
				// loops to get the primary key
				foreach($this->_index_data as $key => $val)
				{
					// check if the field has primary key
					if($val->type == 'PRIMARY')
					{
						// push primary key
						$this->_set_primary			= array_merge($this->_set_primary, $val->fields);
					}
				}
				
				// make the array unique
				$this->_set_primary					= array_unique($this->_set_primary);
			}
			
			// check again if the primary key is still unavailable
			if(!$this->_set_primary)
			{
				if('backend' == $this->template->get_theme_property('type'))
				{
					$this->set_description('<div class="row bg-danger text-light"><div class="col-12"><b>' . phrase('no_primary_key_found') . '</b> ' . phrase('please_define_it_manually') . ' (' . phrase('refers_to') . ' <code class="text-warning">set_primary()</code>), ' . phrase('otherwise_you_cannot_perform_the_following_action') . ': ' . phrase('read') . ', ' . phrase('update') . ', ' . phrase('delete') . ' ' . phrase('and') . ' ' . phrase('export_to_document') . '</div></div>');
				}
				
				$this->unset_action('read, update, delete, export, print, pdf');
			}
			
			// check the requested method
			if(in_array($this->_method, array('create', 'read', 'update', 'delete', 'export', 'print', 'pdf')))
			{
				// apply primary from where if it's were sets
				if(!$this->_set_primary && $this->_where)
				{
					$this->_set_primary				= array_keys($this->_where);
				}
				
				// check the additional primary key that been sets up
				if(is_array($this->_set_primary) && sizeof($this->_set_primary) > 0)
				{
					foreach($this->_set_primary as $key => $val)
					{
						if(service('request')->getGet($val) && $this->model->field_exists($val, $this->_table))
						{
							if(('read' == $this->_method && isset($this->_unset_read[$val]) && in_array(service('request')->getGet($val), $this->_unset_read[$val])) || ('update' == $this->_method && isset($this->_unset_update[$val]) && in_array(service('request')->getGet($val), $this->_unset_update[$val])) || ('delete' == $this->_method && isset($this->_unset_delete[$val]) && in_array(service('request')->getGet($val), $this->_unset_delete[$val])) || ('export' == $this->_method && isset($this->_unset_read[$val]) && in_array(service('request')->getGet($val), $this->_unset_read[$val])) || ('print' == $this->_method && isset($this->_unset_read[$val]) && in_array(service('request')->getGet($val), $this->_unset_read[$val])) || ('pdf' == $this->_method && isset($this->_unset_read[$val]) && in_array(service('request')->getGet($val), $this->_unset_read[$val])))
							{
								if(in_array($this->_method, array('read', 'export', 'print', 'pdf')))
								{
									return throw_exception(403, phrase('you_are_not_allowed_to_view_the_requested_item'), $this->_redirect_back);
								}
								else
								{
									if(isset($this->_set_messages['update']))
									{
										return throw_exception($this->_set_messages['update']['code'], $this->_set_messages['update']['messages'], $this->_redirect_back);
									}
									else
									{
										return throw_exception(403, phrase('you_are_not_allowed_to_modify_the_requested_item'), $this->_redirect_back);
									}
								}
							}
							
							$this->_prepare('where', array($this->_table . '.' . $val, htmlspecialchars(service('request')->getGet($val))));
						}
						else if(in_array($val, $this->_set_primary) && $this->model->field_exists($val, $this->_table) && isset($this->_set_default[$val]) && $this->_set_default[$val])
						{
							if(('read' == $this->_method && isset($this->_unset_read[$val]) && in_array(service('request')->getGet($val), $this->_unset_read[$val])) || ('update' == $this->_method && isset($this->_unset_update[$val]) && in_array(service('request')->getGet($val), $this->_unset_update[$val])) || ('delete' == $this->_method && isset($this->_unset_delete[$val]) && in_array(service('request')->getGet($val), $this->_unset_delete[$val])) || ('export' == $this->_method && isset($this->_unset_read[$val]) && in_array(service('request')->getGet($val), $this->_unset_read[$val])) || ('print' == $this->_method && isset($this->_unset_read[$val]) && in_array(service('request')->getGet($val), $this->_unset_read[$val])) || ('pdf' == $this->_method && isset($this->_unset_read[$val]) && in_array(service('request')->getGet($val), $this->_unset_read[$val])))
							{
								if(in_array($this->_method, array('read', 'export', 'print', 'pdf')))
								{
									return throw_exception(403, phrase('you_are_not_allowed_to_view_the_requested_item'), $this->_redirect_back);
								}
								else
								{
									if(isset($this->_set_messages['update']))
									{
										return throw_exception($this->_set_messages['update']['code'], $this->_set_messages['update']['messages'], $this->_redirect_back);
									}
									else
									{
										return throw_exception(403, phrase('you_are_not_allowed_to_modify_the_requested_item'), $this->_redirect_back);
									}
								}
							}
							
							$this->_prepare('where', array($this->_table . '.' . $val, $this->_set_default[$val]));
						}
					}
				}
				
				/**
				 * Indicates the request is to writing the data
				 */
				if($this->_set_primary && in_array($this->_method, array('create', 'update')))
				{
					if(service('request')->getPost('_token'))
					{
						/**
						 * Post token is a initial to validate the form. It's mean the request were
						 * submitted through the form
						 */
						
						// validate sent token
						$token_sent					= service('request')->getPost('_token');
						
						if($this->valid_token($token_sent))
						{
							// token approved, check if validation use the custom callback
							if($this->_form_callback && method_exists($this, $this->_form_callback))
							{
								// use callback as form validation
								$_callback			= $this->_form_callback;
								
								return $this->$_callback();
							}
							else
							{
								// serialize table data
								$field_data			= array(array_fill_keys(array_keys(array_flip($this->model->list_fields($this->_table))), ''));
								
								// or use the master validation instead
								return $this->validate_form($field_data);
							}
						}
						else
						{
							// token isn't valid, throw exception
							return throw_exception(403, phrase('the_token_you_submitted_has_been_expired_or_you_are_trying_to_bypass_it_from_the_restricted_source'), $this->_redirect_back);
						}
					}
					else if($this->_api_request && 'POST' == service('request')->getServer('REQUEST_METHOD') && (in_array($this->_method, array('create', 'update')) || ($this->_form_callback && method_exists($this, $this->_form_callback))))
					{
						/**
						 * Indicate the method is requested through API
						 */
						if($this->_form_callback && method_exists($this, $this->_form_callback))
						{
							// use callback as form validation
							$_callback				= $this->_form_callback;
							
							return $this->$_callback();
						}
						else
						{
							// serialize table data
							$field_data				= array(array_fill_keys(array_keys(array_flip($this->model->list_fields($this->_table))), ''));
							
							// or use the master validation instead
							return $this->validate_form($field_data);
						}
					}
				}
				
				/**
				 * Indicates the request is to writing the data
				 */
				elseif($this->_set_primary && 'delete' == $this->_method)
				{
					/**
					 * Method delete
					 */
					if(1 == service('request')->getPost('batch'))
					{
						// batch delete
						return $this->delete_batch($this->_table);
					}
					else if($this->_where)
					{
						// single delete
						return $this->delete_data($this->_table, $this->_where, $this->_limit);
					}
				}
			}
			else
			{
				$this->_offset						= (is_numeric(service('request')->getGet('per_page')) && service('request')->getGet('per_page') ? service('request')->getGet('per_page') - 1 : 0) * $this->_limit;
				
				if($this->_offset)
				{
					// push offset to the prepared query builder
					$this->_prepare('offset', array($this->_offset));
				}
				
				if(($this->_searchable && !$this->_like && service('request')->getGet('q')) || ('autocomplete' == service('request')->getPost('method') && $this->_searchable && service('request')->getPost('q')))
				{
					$column							= (service('request')->getGet('column') ? strip_tags(service('request')->getGet('column')) : service('request')->getGet('column'));
					
					if($column && 'all' != $column)
					{
						// push like to the prepared query builder
						$this->_prepare('like', array($column, htmlspecialchars(('autocomplete' == service('request')->getPost('method') && service('request')->getPost('q') ? service('request')->getPost('q') : service('request')->getGet('q')))));
					}
					else
					{
						$columns					= $this->model->list_fields($this->_table);
						
						if($columns)
						{
							foreach($columns as $key => $val)
							{
								// add the table prefix to prevent ambiguous
								$val				= $this->_table . '.' . $val;
								
								// push like an or like to the prepared query builder
								$this->_prepare(($key ? 'or_like' : 'like'), array($val, htmlspecialchars(('autocomplete' == service('request')->getPost('method') && service('request')->getPost('q') ? service('request')->getPost('q') : service('request')->getGet('q')))));
							}
						}
						
						if($this->_select)
						{
							$compiled_like			= array();
							
							foreach($this->_select as $key => $val)
							{
								if($val && stripos($val, ' AS ') !== false)
								{
									$val			= substr($val, 0, stripos($val, ' AS '));
								}
								
								$field_origin		= (strpos($val, '.') !== false ? substr($val, strpos($val, '.') + 1) : $val);
								
								if(in_array($field_origin, $compiled_like)) continue;
								
								// push like an or like to the prepared query builder
								$this->_prepare(($key ? 'or_like' : 'like'), array($val, htmlspecialchars(('autocomplete' == service('request')->getPost('method') && service('request')->getPost('q') ? service('request')->getPost('q') : service('request')->getGet('q')))));
								
								if(isset($this->_set_field[service('request')->getPost('origin')]['parameter']))
								{
									if(is_array($this->_set_field[service('request')->getPost('origin')]['parameter']))
									{
										$table		= $this->_set_field[service('request')->getPost('origin')]['parameter'][0];
									}
									else
									{
										$table		= $this->_set_field[service('request')->getPost('origin')]['parameter'];
									}
								}
								
								if(isset($this->_set_field[service('request')->getPost('origin')]['parameter']) && $this->model->field_exists(($val && stripos($val, '.') !== false ? substr($val, strripos($val, '.') + 1) : $val), $table))
								{
									// push order by best match to the prepared query builder
									$this->_prepare('order_by', array('(CASE WHEN ' . $val . ' LIKE "' . service('request')->getPost('q') . '%" THEN 1 WHEN ' . $val . ' LIKE "%' . service('request')->getPost('q') . '" THEN 3 ELSE 2 END)'));
								}
								
								$compiled_like[]	= $field_origin;
							}
						}
					}
				}
			}
			
			// check if data is requested through autocomplete (jQuery plugin)
			if(service('request')->isAJAX() && 'autocomplete' == service('request')->getPost('method'))
			{
				/**
				 * PLEASE NOTE
				 *
				 * It's a simple  autocomplete  input to  render only one  database
				 * table.
				 * To use a complex autocomplete within complex database query, use
				 * the set_autocomplete()  function instead.  It will allow you  to
				 * custom format the result, also join into multiple table includes
				 * where, order and grouping.
				 */
				$suggestions						= array();
				
				if(isset($this->_set_field[service('request')->getPost('origin')]) && in_array('autocomplete', $this->_set_field[service('request')->getPost('origin')]['field_type']))
				{
					// set the relation table, field and keyword
					$field							= $this->_set_field[service('request')->getPost('origin')];
					$table							= (is_array($field['parameter']) ? $field['parameter'][0] : $field['parameter']);
					$select							= (!is_array($field['extra_params']) ? array_map('trim', explode(',', $field['extra_params'])) : $field['extra_params']);
					$select							= array
					(
						'value'						=> (isset($select['value']) ? $select['value'] : (isset($select[0]) ? $select[0] : null)),
						'label'						=> (isset($select['label']) ? $select['label'] : (isset($select[1]) ? $select[1] : null)),
						'description'				=> (isset($select['description']) ? $select['description'] : (isset($select[2]) ? $select[2] : null)),
						'image'						=> (isset($select['image']) ? $select['image'] : (isset($select[3]) ? $select[3] : null))
					);
					$where							= $field['another_params'];
					$join							= $field['skip'];
					$order							= $field['order'];
					$keyword						= service('request')->getPost('q');
					
					$columns						= array();
					
					$num							= 0;
					
					foreach($select as $key => $val)
					{
						if(!$val) continue;
						
						$this->model->group_start();
						
						if($num > 0)
						{
							$this->model->or_like($val, $keyword, 'both', true, true);
						}
						else
						{
							$this->model->like($val, $keyword, 'both', true, true);
						}
						
						$this->model->group_end();
						
						$columns[]					= $val . ' AS ' . $key;
						
						$num++;
					}
					
					$this->model->select($columns);
					
					if($where && is_array($where) && sizeof($where) > 0)
					{
						$this->model->where($where);
					}
					
					if($join && is_array($join) && sizeof($join) > 0)
					{
						foreach($join as $key => $val)
						{
							if(!isset($val[0]) || !isset($val[1])) continue;
							
							$this->model->join($val[0], $val[1]);
						}
					}
					
					// order by best match
					if($order_by)
					{
						foreach($order_by as $key => $val)
						{
							$this->model->order_by($val, null, false);
						}
					}
					
					if($order)
					{
						$this->model->order_by($order);
					}
					
					$this->model->group_by($select['value']);
					
					// run query
					$query							= $this->model->get($table, 50)->result_array();
					
					if($query)
					{
						foreach($query as $val)
						{
							// list as value, label and description
							list($v, $l, $d, $i)	= array_pad(array_values($val), 4, null);
							$suggestions[]			= array
							(
								'value'				=> $v,
								'label'				=> ($l ? $l : $v),
								'description'		=> $d,
								'image'				=> ($i ? get_image($this->_set_upload_path, $i, 'icon') : null),
								'target'			=> null
							);
						}
					}
				}
				else
				{
					/**
					 * Autocomplete search data from listed of table
					 */
					if(!$this->_select)
					{
						// check the select list, if none, use the main table field instead
						$this->_select				= preg_filter('/^/', $this->_table . '.', $this->model->list_fields($this->_table));
					}
					
					// loop the select field to prevent query using multiple LIKE condition and use OR LIKE instead
					$compiled_like					= array();
					
					foreach($this->_select as $key => $val)
					{
						if($val && stripos($val, ' AS ') !== false)
						{
							$val					= substr($val, 0, stripos($val, ' AS '));
						}
						
						$field_origin				= (strpos($val, '.') !== false ? substr($val, strpos($val, '.') + 1) : $val);
						
						if(in_array($field_origin, $compiled_like)) continue;
						
						// push like an or like to the prepared query builder
						$this->_prepare(($key ? 'or_like' : 'like'), array($val, htmlspecialchars(('autocomplete' == service('request')->getPost('method') && service('request')->getPost('q') ? service('request')->getPost('q') : service('request')->getGet('q')))));
						
						if(isset($this->_set_field[service('request')->getPost('origin')]['parameter']))
						{
							if(is_array($this->_set_field[service('request')->getPost('origin')]['parameter']))
							{
								$table				= $this->_set_field[service('request')->getPost('origin')]['parameter'][0];
							}
							else
							{
								$table				= $this->_set_field[service('request')->getPost('origin')]['parameter'];
							}
						}
						
						if(isset($this->_set_field[service('request')->getPost('origin')]['parameter']) && $this->model->field_exists(($val && stripos($val, '.') !== false ? substr($val, strripos($val, '.') + 1) : $val), $table))
						{
							// push order by best match to the prepared query builder
							$this->_prepare('order_by', array('(CASE WHEN ' . $val . ' LIKE "' . service('request')->getPost('q') . '%" THEN 1 WHEN ' . $val . ' LIKE "%' . service('request')->getPost('q') . '" THEN 3 ELSE 2 END)'));
						}
						
						$compiled_like[]			= $field_origin;
					}
					
					// run query
					$query							= $this->_fetch($this->_table);
					
					// populate added item
					$added_item						= array();
					
					// serialize results
					$serialized						= $this->serialize($query['results']);
					
					foreach($serialized as $key => $val)
					{
						// does column order is mandatory? let's just watch
						if(is_array($this->_column_order) && sizeof($this->_column_order) > 0)
						{
							// set the default column order
							$column_order			= array();
							
							foreach($this->_column_order as $order_key => $order_val)
							{
								// if array key exists
								if(array_key_exists($order_val, $val))
								{
									// then push to column order grocery
									$column_order[]	= $order_val;
								}
							}
							// set the value
							$val					= array_replace(array_flip($column_order), $val);
						}
						
						$autocomplete_item			= array();
						// loop the result
						foreach($val as $field => $value)
						{
							// check if the result value is not contain the search keyword or the field is unset from column (list table)
							if(($value['original'] && strpos(strtolower($value['original']), strtolower(service('request')->getPost('q'))) === false) || in_array($field, $this->_unset_column)) continue;
							
							// everything's looks good, throw into autocomplete result
							if(!$autocomplete_item && $value['original'] && !in_array($value['content'], $added_item))
							{
								$added_item[]		= $value['content'];
								
								$autocomplete_item	= array
								(
									'value'			=> truncate($value['content'], 32, false, ''),
									'label'			=> truncate($value['content'], 32),
									'target'		=> current_page(null, array('per_page' => null, 'q' => truncate($value['content'], 32, '')))
								);
							}
						}
						
						if($autocomplete_item)
						{
							$suggestions[]			= $autocomplete_item;
						}
					}
				}
				
				// return the callback as autocomplete results
				return make_json
				(
					array
					(
						'suggestions'				=> ($suggestions ? $suggestions : null)
					)
				);
			}
			
			// check if data is requested through server side select (jQuery plugin)
			else if(service('request')->isAJAX() && 'ajax_select' == service('request')->getPost('method') && isset($this->_set_relation[service('request')->getPost('source')]))
			{
				return $this->_get_relation($this->_set_relation[service('request')->getPost('source')]);
			}
			
			/**
			 * Start sort order
			 */
			if(service('request')->getGet('sort') && 'desc' == strtolower(service('request')->getGet('sort')))
			{
				set_userdata('sortOrder', 'asc');
			}
			else
			{
				set_userdata('sortOrder', 'desc');
			}
			
			// match order by the primary table
			if($this->model->field_exists(service('request')->getGet('order'), $this->_table))
			{
				// push order to the prepared query builder
				$this->_prepare[]					= array
				(
					'function'						=> 'order_by',
					'arguments'						=> array($this->_table . '.' . service('request')->getGet('order'), get_userdata('sortOrder'))
				);
			}
			
			// otherwhise, find it from the relation table
			else if($this->_compiled_table)
			{
				foreach($this->_compiled_table as $key => $table)
				{
					/**
					 * Validate the column to check if column is exist in table
					 */
					if($this->model->field_exists(service('request')->getGet('order'), $table))
					{
						// push order to the prepared query builder
						$this->_prepare[]			= array
						(
							'function'				=> 'order_by',
							'arguments'				=> array($table . '.' . service('request')->getGet('order'), get_userdata('sortOrder'))
						);
					}
				}
			}
			
			if(in_array($this->_method, array('create')))
			{
				// list the field properties
				$result								= array(array_fill_keys(array_keys(array_flip($this->model->list_fields($this->_table))), ''));
			}
			else
			{
				// run query
				$query								= $this->_fetch($this->_table);
				$result								= $query['results'];
				$this->_total						= $query['total'];
			}
			
			// try to convert the magic string and replace with the result
			$title									= (is_array($this->_set_title) && isset($this->_set_title[$this->_method]) ? $this->_set_title[$this->_method] : (!is_array($this->_set_title) ? $this->_set_title : phrase('untitled')));
			$description							= (isset($this->_set_description[$this->_method]) ? $this->_set_description[$this->_method] : (isset($this->_set_description['index']) ? $this->_set_description['index'] : ''));
			
			if(isset($result[0]))
			{
				foreach($result[0] as $do => $magic)
				{
					if(!$magic) continue;
					
					// replace the magic string to query result
					$title							= str_replace('{' . $do . '}', $magic, $title);
					$description					= str_replace('{' . $do . '}', $magic, $description);
				}
			}
			
			$title									= ($title ? preg_replace('/\{.*?\}/', '', $title) : phrase('untitled'));
			$description							= ($description ? preg_replace('/\{.*?\}/', '', $description) : null);
			
			// if method is create
			if('create' == $this->_method)
			{
				$this->_set_icon					= (is_array($this->_set_icon) && isset($this->_set_icon[$this->_method]) ? $this->_set_icon[$this->_method] : 'mdi mdi-plus');
				$this->_set_title					= (is_array($this->_set_title) && isset($this->_set_title[$this->_method]) ? $title : phrase('add_new_data'));
				$this->_set_description				= (is_array($this->_set_description) && isset($this->_set_description[$this->_method]) ? $description : phrase('please_fill_all_the_required_fields_below_to_add_new_data'));
				$this->_view						= (is_array($this->_set_template) && isset($this->_set_template['form']) ? $this->_set_template['form'] : ($view && 'index' != $view ? $view : 'form'));
				$this->_results						= $this->render_form($result);
			}
			
			// if method is read
			else if('read' == $this->_method)
			{
				$this->_set_icon					= (is_array($this->_set_icon) && isset($this->_set_icon[$this->_method]) ? $this->_set_icon[$this->_method] : 'mdi mdi-magnify-plus');
				$this->_set_title					= (is_array($this->_set_title) && isset($this->_set_title[$this->_method]) ? $title : phrase('showing_data'));
				$this->_set_description				= (is_array($this->_set_description) && isset($this->_set_description[$this->_method]) ? $description : phrase('showing_the_result_of_the_selected_item'));
				$this->_view						= (is_array($this->_set_template) && isset($this->_set_template[$this->_method]) ? $this->_set_template['read'] : ($view && 'index' != $view ? $view : 'read'));
				$this->_results						= $this->render_read($result);
			}
			
			// if method is update
			else if('update' == $this->_method)
			{
				$this->_set_icon					= (is_array($this->_set_icon) && isset($this->_set_icon[$this->_method]) ? $this->_set_icon[$this->_method] : 'mdi mdi-square-edit-outline');
				$this->_set_title					= (is_array($this->_set_title) && isset($this->_set_title[$this->_method]) ? $title : phrase('update_data'));
				$this->_set_description				= (is_array($this->_set_description) && isset($this->_set_description[$this->_method]) ? $description : phrase('make_sure_to_check_the_changes_before_submitting'));
				$this->_view						= (is_array($this->_set_template) && isset($this->_set_template['form']) ? $this->_set_template['form'] : ($view && 'index' != $view ? $view : 'form'));
				$this->_results						= $this->render_form($result);
			}
			
			// if method is export
			else if(in_array($this->_method, array('export', 'print', 'pdf')))
			{
				$query_string						= service('request')->getGet();
				$single_print						= false;
				
				if($this->_set_primary)
				{
					foreach($this->_set_primary as $key => $val)
					{
						if(isset($query_string[$val]))
						{
							$single_print			= true;
							
							break;
						}
					}
				}
				
				$this->_view						= $this->template->get_view((isset($this->_set_template[$this->_method]) ? $this->_set_template[$this->_method] : $this->_method));
				
				$this->_results						= ($single_print ? $this->render_read($result) : $this->render_table($result));
			}
			
			// otherwise
			else
			{
				$view_exists						= (!in_array($this->template->get_view($this->_view, $this->_query, $this->_table), array('templates/index', 'templates/index_grid', 'templates/index_mobile', 'templates/error')) ? true : false);
				
				$this->_set_icon					= (is_array($this->_set_icon) && isset($this->_set_icon[$this->_method]) ? $this->_set_icon[$this->_method] : (!is_array($this->_set_icon) ? $this->_set_icon : 'mdi mdi-table'));
				$this->_set_title					= ($title ? $title : (($title && $this->_set_primary) || $this->_query ? phrase('untitled') : ($this->_set_title_placeholder ? $this->_set_title_placeholder : phrase('page_not_found'))));
				$this->_set_description				= $description;
				$this->_view						= (is_array($this->_set_template) && isset($this->_set_template['index']) ? $this->_set_template['index'] : ($view && 'index' != $view ? $view : 'index'));
				$this->_results						= ($this->_set_primary && !$view_exists ? $this->render_table($result) : $result);
			}
		}
		else
		{
			$this->_set_icon						= (is_array($this->_set_icon) && isset($this->_set_icon[$this->_method]) ? $this->_set_icon[$this->_method] : (!is_array($this->_set_icon) ? $this->_set_icon : 'mdi mdi-file-document-outline'));
			$this->_set_title						= (is_array($this->_set_title) && isset($this->_set_title[$this->_method]) ? $this->_set_title[$this->_method] : (!is_array($this->_set_title) && $this->_set_title ? $this->_set_title : phrase('untitled')));
			$this->_set_description					= (is_array($this->_set_description) && isset($this->_set_description[$this->_method]) ? $this->_set_description[$this->_method] : (isset($this->_set_description['index']) ? $this->_set_description['index'] : null));
			$this->_view							= (is_array($this->_set_template) && isset($this->_set_template['index']) ? $this->_set_template['index'] : ($view && 'index' != $view ? $view : 'index'));
			$this->_results							= array();
		}
		
		/**
		 * Prepare output
		 */
		$this->_output								= array
		(
			'_token'								=> sha1(current_page() . ENCRYPTION_KEY . get_userdata('session_generated')),
			'method'								=> $this->_method,
			'breadcrumb'							=> $this->template->breadcrumb($this->_set_breadcrumb, $this->_set_title, $this->_query),
			'current_page'							=> current_page(null, service('request')->getGet()),
			'meta'									=> array
			(
				'description'						=> $this->_set_description,
				'icon'								=> $this->_set_icon,
				'title'								=> $this->_set_title,
				'modal_size'						=> $this->_modal_size,
				'segmentation'						=> array_map(function ($segment = null){return str_replace('.', '-', preg_replace('/[^a-zA-Z0-9]/', '_', $segment));}, service('uri')->getSegments())
			),
			'results'								=> $this->_results,
			'total'									=> $this->_total,
			'limit'									=> $this->_limit,
			'pagination'							=> array
			(
				'limit'								=> $this->_limit_backup,
				'offset'							=> $this->_offset,
				'per_page'							=> $this->_limit,
				'total_rows'						=> $this->_total,
				'url'								=> current_page(null, array('per_page' => null))
			),
			'query_string'							=> service('request')->getGet(),
			'elapsed_time'							=> service('timer')->stop('elapsed_time')->getElapsedTime('elapsed_time')
		);
		
		if(isset($this->_set_template['read']) || isset($this->_set_template['form']))
		{
			$this->_output['modal_html']			= true;
		}
		
		/**
		 * Merge the additional custom output
		 */
		if($this->_set_output)
		{
			$this->_output							= array_merge($this->_output, $this->_set_output);
		}
		
		/**
		 * Format output from array into object
		 */
		$this->_output								= json_decode(json_encode(json_fixer($this->_output)));
		
		/**
		 * Generate output from the method
		 */
		if('print' == $this->_method)
		{
			/**
			 * Print
			 */
			$this->_output->template				= (object) array
			(
				'pagination'						=> $this->template->pagination($this->_output->pagination)
			);
			
			// send to client
			return service('response')->setBody(view($this->_view, (array) $this->_output))->send();
		}
		else if(in_array($this->_method, array('pdf', 'export')))
		{
			/**
			 * Document
			 */
			$this->_output							= view($this->_view, (array) $this->_output);
			
			$this->document							= new \Aksara\Libraries\Document;
			
			$this->document->pageSize('13in 8.5in');
			
			return $this->document->generate($this->_output, $this->_set_title, ($this->_method == 'export' ? 'export' : 'embed'));
		}
		else
		{
			$prefer									= (in_array($this->_method, array('create', 'read', 'update')) && !service('request')->getPost('prefer') ? 'html' : service('request')->getPost('prefer'));
			
			if(('html' != $prefer && service('request')->isAJAX() && $this->template->get_view($this->_view) && stripos($this->template->get_view($this->_view), 'templates/') !== false && (isset($this->_output->results->table_data) || isset($this->_output->results->form_data))) || $this->_api_request)
			{
				/**
				 * Indicate the method is requested through Promise (XHR) or API
				 */
				if('modal' == service('request')->getPost('prefer'))
				{
					unset($this->_output->breadcrumb, $this->_output->total, $this->_output->pagination);
					
					if($this->_set_method)
					{
						return $this->template->build($this->_view, $this->_output, null, $this->_table, $this->_language);
					}
				}
				else
				{
					if(in_array($this->_method, array('create', 'read', 'update')))
					{
						unset($this->_output->total, $this->_output->pagination);
					}
					
					if(isset($this->_output->pagination))
					{
						$this->_output->pagination	= $this->template->pagination($this->_output->pagination, false);
					}
				}
				
				/**
				 * Returning the response as json format
				 */
				if(service('request')->getServer('HTTP_REFERER') && stripos(service('request')->getServer('HTTP_REFERER'), service('request')->getServer('SERVER_NAME')) !== false || $this->_api_request)
				{
					if($this->_api_request && 'GET' != service('request')->getServer('REQUEST_METHOD'))
					{
						/**
						 * Indicate the method is requested through API
						 */
						return throw_exception(403, phrase('the_method_you_requested_is_not_acceptable') . ' (' . service('request')->getServer('REQUEST_METHOD'). ')', (!$this->_api_request ? $this->_redirect_back : null));
					}
					
					return make_json
					(
						$this->_output
					);
				}
			}
			
			if(in_array($this->_method, array('create', 'read', 'update')))
			{
				unset($this->_output->total);
			}
			
			// Display to the browser
			return $this->template->build($this->_view, $this->_output, $this->_set_breadcrumb, $this->_table, $this->_language);
		}
	}
	
	/**
	 * insert_on_update_fail
	 * This function sustain to insert data if there's no data to update
	 *
	 * @access		public
	 * @param		bool		$return
	 * @return		bool
	 */
	public function insert_on_update_fail($return = true)
	{
		$this->_insert_on_update_fail				= $return;
		
		return $this;
	}
	
	/**
	 * insert_data
	 * Inserting data to the database
	 *
	 * @access		public
	 * @param		string		$table
	 * @param		array		$data
	 * @return		mixed
	 */
	public function insert_data($table = null, $data = array())
	{
		if($this->_api_request && 'POST' != service('request')->getServer('REQUEST_METHOD'))
		{
			// unlink the files
			$this->_unlink_files();
			
			/**
			 * Indicate the method is requested through API
			 */
			return throw_exception(403, phrase('the_method_you_requested_is_not_acceptable') . ' (' . service('request')->getServer('REQUEST_METHOD'). ')', (!$this->_api_request ? $this->_redirect_back : null));
		}
		
		if($table && $this->model->table_exists($table))
		{
			if(method_exists($this, 'before_insert'))
			{
				$this->before_insert();
			}
			
			if($this->model->insert($table, $data))
			{
				$auto_increment						= true;
				$primary							= 0;
				
				if($this->_db_driver == 'Postgre')
				{
					$auto_increment					= false;
					$field_data						= $this->model->field_data($table);
					
					foreach($field_data as $key => $val)
					{
						if(isset($this->_set_default[$val->name]))
						{
							$primary				= $this->_set_default[$val->name];
						}
						
						if((isset($val->primary_key) && $val->primary_key === 1) || (isset($val->default) && $val->default && stripos($val->default, 'nextval(') !== false))
						{
							$auto_increment			= true;
						}
						
						if($primary && $auto_increment)
						{
							break;
						}
					}
				}
				
				$this->_insert_id					= ($auto_increment ? $this->model->insert_id() : 0);
				
				if(method_exists($this, 'after_insert'))
				{
					$this->after_insert();
				}
				
				return throw_exception(($this->_api_request ? 200 : 301), phrase('data_was_successfully_submitted'), (!$this->_api_request ? $this->_redirect_back : null));
			}
			else
			{
				// unlink the files
				$this->_unlink_files();
				
				// otherwise, the item is cannot be deleted
				$error								= $this->model->error();
				
				if(in_array(get_userdata('group_id'), array(1)) && isset($error['message']))
				{
					// for administrator
					return throw_exception(500, $error['message'], (!$this->_api_request ? $this->_redirect_back : null));
				}
				
				// for user
				return throw_exception(500, phrase('unable_to_submit_your_data') . ' ' . phrase('please_try_again_or_contact_the_system_administrator') . ' ' . phrase('error_code') . ': <b>500 (insert)</b>', (!$this->_api_request ? $this->_redirect_back : null));
			}
		}
		else
		{
			// unlink the files
			$this->_unlink_files();
			
			return throw_exception(404, phrase('the_selected_database_table_does_not_exists'), (!$this->_api_request ? $this->_redirect_back : null));
		}
	}
	
	/**
	 * upate_data
	 * Update data from database
	 *
	 * @access		public
	 * @param		string		$table
	 * @param		mixed		$data
	 * @param		mixed		$where
	 * @param		string		$redirect
	 * @param		string		$callback
	 * @return		mixed
	 */
	public function update_data($table = null, $data = array(), $where = array())
	{
		if($this->_api_request && 'POST' != service('request')->getServer('REQUEST_METHOD'))
		{
			// unlink the files
			$this->_unlink_files();
			
			/**
			 * Indicate the method is requested through API
			 */
			return throw_exception(403, phrase('the_method_you_requested_is_not_acceptable') . ' (' . service('request')->getServer('REQUEST_METHOD'). ')', (!$this->_api_request ? $this->_redirect_back : null));
		}
		
		if($table && $this->model->table_exists($table))
		{
			if(is_array($where) && sizeof($where) > 0 && $this->model->get_where($table, $where, 1)->num_rows() > 0)
			{
				if(method_exists($this, 'before_update'))
				{
					$this->before_update();
				}
				
				if($this->model->update($table, $data, $where))
				{
					// check if file is updated
					if($this->_old_files && sizeof($this->_old_files) > 0)
					{
						// unlink the files
						$this->_unlink_files($this->_old_files);
					}
					
					if(method_exists($this, 'after_update'))
					{
						$this->after_update();
					}
					
					return throw_exception(($this->_api_request ? 200 : 301), phrase('data_was_successfully_updated'), (!$this->_api_request ? $this->_redirect_back : null));
				}
				else
				{
					// unlink the files
					$this->_unlink_files();
					
					// otherwise, the item is cannot be deleted
					$error							= $this->model->error();
					
					if(in_array(get_userdata('group_id'), array(1)) && isset($error['message']))
					{
						// for administrator
						return throw_exception(500, $error['message'], (!$this->_api_request ? $this->_redirect_back : null));
					}
					
					// for user
					return throw_exception(500, phrase('unable_to_update_data') . ' ' . phrase('please_try_again_or_contact_the_system_administrator') . ' ' . phrase('error_code') . ': <b>500 (update)</b>', (!$this->_api_request ? $this->_redirect_back : null));
				}
			}
			else if($this->_insert_on_update_fail)
			{
				$this->insert_data($table, $data);
			}
			else
			{
				// unlink the files
				$this->_unlink_files();
				
				return throw_exception(404, phrase('the_data_you_want_to_update_was_not_found'), (!$this->_api_request ? $this->_redirect_back : null));
			}
		}
		else
		{
			// unlink the files
			$this->_unlink_files();
			
			return throw_exception(404, phrase('the_selected_database_table_does_not_exists'), (!$this->_api_request ? $this->_redirect_back : null));
		}
		
		return false;
	}
	
	/**
	 * delete_data
	 * Delete multiple data from the database
	 *
	 * @access		public
	 * @param		string		$table
	 * @param		mixed		$where
	 * @param		int			$limit
	 * @return		mixed
	 */
	public function delete_data($table = null, $where = array(), $limit = 1)
	{
		if($this->_api_request && 'DELETE' != service('request')->getServer('REQUEST_METHOD'))
		{
			/**
			 * Indicate the method is requested through API
			 */
			return throw_exception(403, phrase('the_method_you_requested_is_not_acceptable') . ' (' . service('request')->getServer('REQUEST_METHOD'). ')', (!$this->_api_request ? $this->_redirect_back : null));
		}
		
		// check if app on demo mode
		if($this->_restrict_on_demo)
		{
			return throw_exception(403, phrase('this_feature_is_disabled_in_demo_mode'), (!$this->_api_request ? $this->_redirect_back : null));
		}
		
		// make sure the delete action have where as condition
		if(!$where)
		{
			// otherwise, redirect to previous page
			return throw_exception(404, phrase('the_data_you_want_to_delete_was_not_found'), (!$this->_api_request ? $this->_redirect_back : null));
		}
		
		// check if delete have a callback message
		if(isset($this->_set_messages['delete']) && $this->_set_messages['delete']['return'])
		{
			// use the callback message
			return throw_exception($this->_set_messages['delete']['code'], $this->_set_messages['delete']['messages'], $this->_redirect_back);
		}
		
		// check if targeted table is exists
		if($table && $this->model->table_exists($table))
		{
			$query									= $this->model->get_where($table, $where, $limit)->result();
			
			// check if the item is exists
			if($query)
			{
				// before delete callback
				if(method_exists($this, 'before_delete'))
				{
					$this->before_delete();
				}
				
				// safe check for delete
				if($this->model->delete($table, $where, $limit))
				{
					// prepare to delete file
					$serialized						= $this->serialize($query);
					
					foreach($serialized as $key => $val)
					{
						foreach($val as $field => $params)
						{
							if(array_intersect(array('image', 'images', 'file', 'files'), $params['type']))
							{
								$files				= ($params['original'] ? json_decode($params['original']) : new \stdClass());
								
								if($files)
								{
									// unlink the files
									$this->_unlink_files($files);
								}
							}
						}
					}
					
					if(method_exists($this, 'after_delete'))
					{
						$this->after_delete();
					}
					
					return throw_exception(($this->_api_request ? 200 : 301), phrase('data_was_successfully_removed'), (!$this->_api_request ? $this->_redirect_back : null));
				}
				else
				{
					// otherwise, the item is cannot be deleted
					$error							= $this->model->error();
					
					if(in_array(get_userdata('group_id'), array(1)) && isset($error['message']))
					{
						// for administrator
						return throw_exception(500, $error['message'], (!$this->_api_request ? $this->_redirect_back : null));
					}
					
					// for user
					return throw_exception(500, phrase('unable_to_remove_the_selected_data') . '. ' . phrase('please_try_again_or_contact_the_system_administrator') . '. ' . phrase('error_code') . ': <b>500 (delete)</b>', (!$this->_api_request ? $this->_redirect_back : null));
				}
			}
			else
			{
				// no item found
				return throw_exception(404, phrase('the_data_you_want_to_delete_was_not_found'), (!$this->_api_request ? $this->_redirect_back : null));
			}
		}
		else
		{
			// the targeted database table isn't exists
			return throw_exception(404, phrase('the_selected_database_table_does_not_exists'), (!$this->_api_request ? $this->_redirect_back : null));
		}
	}
	
	/**
	 * delete_batch
	 * Delete multiple data from the database
	 *
	 * @access		public
	 * @param		string		$table
	 * @return		mixed
	 */
	public function delete_batch($table = null)
	{
		if($this->_api_request && 'DELETE' != service('request')->getServer('REQUEST_METHOD'))
		{
			/**
			 * Indicate the method is requested through API
			 */
			return throw_exception(403, phrase('the_method_you_requested_is_not_acceptable') . ' (' . service('request')->getServer('REQUEST_METHOD'). ')', (!$this->_api_request ? $this->_redirect_back : null));
		}
		
		// check if app on demo mode
		if($this->_restrict_on_demo)
		{
			return throw_exception(403, phrase('this_feature_is_disabled_in_demo_mode'), (!$this->_api_request ? $this->_redirect_back : null));
		}
		
		// get the checked items
		$items										= service('request')->getPost('items');
		$where										= null;
		$affected_rows								= 0;
		$ignored_rows								= 0;
		
		if(is_array($items) && sizeof($items) > 0)
		{
			// before delete callback
			if(method_exists($this, 'before_delete'))
			{
				$this->before_delete();
			}
			
			// safe check to make sure the given field parameter is exists in the database table
			$field_exists							= array_flip($this->model->list_fields($table));
			
			foreach($items as $key => $val)
			{
				// unset the field parameter that not exist in database table
				$val								= array_intersect_key(json_decode($val, true), $field_exists);
				$ignore								= false;
				
				if($this->_unset_delete)
				{
					foreach($this->_unset_delete as $_key => $_val)
					{
						if(isset($val[$_key]) && in_array($val[$_key], $_val))
						{
							$ignore					= true;
							
							break;
						}
					}
				}
				
				if($ignore)
				{
					$ignored_rows++;
					
					continue;
				}
				
				// get old data to prepare file deletion
				$query								= $this->model->get_where($table, $val, 1)->result();
				
				if($query && $this->model->delete($table, $val))
				{
					// prepare to delete file
					$serialized						= $this->serialize($query);
					
					foreach($serialized as $_key => $_val)
					{
						foreach($_val as $field => $params)
						{
							if(array_intersect(array('image', 'images', 'file', 'files'), $params['type']))
							{
								$files				= ($params['original'] ? json_decode($params['original']) : new \stdClass());
								
								if($files)
								{
									// unlink the files
									$this->_unlink_files($files);
								}
							}
						}
					}
					
					$affected_rows++;
				}
			}
			
			// after delete callback
			if(method_exists($this, 'after_delete'))
			{
				$this->after_delete();
			}
			
		}
		
		if($affected_rows)
		{
			return throw_exception(($this->_api_request ? 200 : 301), $affected_rows . ' ' . phrase('of') . ' ' . sizeof($items) . ' ' . phrase('data_was_successfully_removed'), (!$this->_api_request ? $this->_redirect_back : null));
		}
		else
		{
			return throw_exception(403, phrase('unable_to_remove_the_selected_data'), (!$this->_api_request ? $this->_redirect_back : null));
		}
	}
	
	/**
	 * render_form
	 * Render and format the output of field
	 *
	 * @access		public
	 * @param		array		$data
	 * @return		mixed
	 */
	public function render_form($data = array())
	{
		if(!$data && !$this->_insert_on_update_fail && 'autocomplete' != service('request')->getPost('method'))
		{
			return throw_exception(404, phrase('the_data_you_requested_does_not_exist_or_has_been_removed'), $this->_redirect_back);
		}
		
		$serialized									= $this->serialize($data);
		$primary_key								= array();
		$fields										= array();
		
		if($serialized)
		{
			$serialized								= $serialized[0];
			
			if(is_array($this->_field_order) && sizeof($this->_field_order) > 0)
			{
				$field_order						= array();
				
				foreach($this->_field_order as $order_key => $order_val)
				{
					if(array_key_exists($order_val, $serialized))
					{
						$field_order[]				= $order_val;
					}
				}
				
				$serialized							= array_replace(array_flip($field_order), $serialized);
			}
			else if(is_array($this->_column_order) && sizeof($this->_column_order) > 0)
			{
				$column_order						= array();
				
				foreach($this->_column_order as $order_key => $order_val)
				{
					if(array_key_exists($order_val, $serialized))
					{
						$column_order[]				= $order_val;
					}
				}
				
				$serialized							= array_replace(array_flip($column_order), $serialized);
			}
			
			foreach($serialized as $field => $params)
			{
				$type								= $params['type'];
				$primary							= $params['primary'];
				$max_length							= $params['max_length'];
				$content							= (isset($this->_set_default[$field]) ? $this->_set_default[$field] : $params['original']);
				$original							= ($params['original'] ? $params['original'] : (is_numeric($params['original']) ? '0' : ''));
				$parameter							= $params['parameter'];
				$extra_params						= (isset($this->_set_field[$field]['extra_params']) ? $this->_set_field[$field]['extra_params'] : null);
				$skip								= (isset($this->_set_field[$field]['skip']) ? $this->_set_field[$field]['skip'] : null);
				$hidden								= $params['hidden'];
				$alias								= (isset($this->_merge_label[$field]) ? $this->_merge_label[$field] : (isset($this->_set_alias[$field]) ? $this->_set_alias[$field] : ucwords(str_replace('_', ' ', $field))));
				$read_only							= (in_array('readonly', $type) ? ' readonly' : (in_array('disabled', $type) ? ' disabled' : null));
				$extra_class						= (isset($this->_add_class[$field]) ? ' ' . $this->_add_class[$field] : null);
				$validation							= (isset($this->_set_validation[$field]) ? explode('|', $this->_set_validation[$field]) : array());
				
				$required							= (in_array('required', $validation) ? 1 : 0);
				$position							= (isset($this->_field_position[$field]) ? $this->_field_position[$field] : 1);
				$attribute							= (isset($this->_set_attribute[$field]) ? $this->_set_attribute[$field] : null);
				$default_value						= (('create' == $this->_method || !$original || array_intersect(array('boolean'), $type)) && isset($this->_default_value[$field]) ? $this->_default_value[$field] : null);
				
				/**
				 * save primary key to be generated as token
				 */
				if($primary)
				{
					$primary_key[$field]			= $original;
				}
				
				if($hidden || array_intersect(array('current_timestamp'), $type))
				{
					// skip field
					continue;
				}
				
				if(isset($this->_set_relation[$field]) && !array_intersect(array('custom_format'), $type))
				{
					$content						= $this->_get_relation($this->_set_relation[$field], ($original ? $original : $default_value));
				}
				else if(isset($this->_set_autocomplete[$field]))
				{
					$content						= $this->_autocomplete_input($this->_set_autocomplete[$field], ($original ? $original : $default_value));
				}
				else if(array_intersect(array('image'), $type))
				{
					$content						= '
						<div data-provides="fileupload" class="fileupload fileupload-new text-sm-center">
							<span class="btn btn-file d-block">
								<input type="file" name="' . $field . '" accept="' . implode(',', preg_filter('/^/', '.', array_map('trim', explode(',', IMAGE_FORMAT_ALLOWED)))) . '" role="image-upload" id="' . $field . '_input"' . $read_only . ' />
								<div class="fileupload-new text-center">
									<img class="img-fluid upload_preview" src="' . get_image($this->_set_upload_path, ($original ? $original : $parameter), 'thumb') . '" alt="' . ($original ? $original : $parameter) . '" />
								</div>
								<button type="button" class="btn btn-sm btn-danger rounded-circle position-absolute top-0 end-0" onclick="jExec($(this).closest(\'.fileupload\').find(\'input[type=file]\').val(\'\'), $(this).closest(\'.fileupload\').find(\'img\').attr(\'src\', \'' . get_image($this->_set_upload_path, 'placeholder.png', 'thumb') . '\'))">
									<i class="mdi mdi-window-close"></i>
								</button>
							</span>
						</div>
					';
				}
				else if(array_intersect(array('images', 'file', 'files'), $type))
				{
					$files							= array();
					
					if('update' == $this->_method)
					{
						$original					= ($original ? json_decode($original, true) : array());
						
						if(is_array($original) && sizeof($original) > 0)
						{
							foreach($original as $src => $label)
							{
								$icon				= (in_array(strtolower(pathinfo($src, PATHINFO_EXTENSION)), array('jpg', 'jpeg', 'png', 'gif')) ? get_image($this->_set_upload_path, $src, 'icon') : null);
								$url				= get_file($this->_set_upload_path, $src);
								$filesize			= get_filesize($this->_set_upload_path, $src);
								$filesize			= str_replace(array('kb', 'mb', 'gb', 'b', '.'), '', strtolower($filesize));
								$files[]			= array
								(
									'name'			=> $label,
									'file'			=> $src,
									'size'			=> $filesize,
									'url'			=> $url,
									'icon'			=> $icon
								);
							}
						}
					}
					
					$files							= htmlspecialchars(json_encode($files));
					$content						= '<div class="uploader-input"><input type="file" class="custom-file-input d-none" data-name="' . (array_intersect(array('images', 'files'), $type) ? $field . '[]' : $field) . '" role="uploader" id="' . $field . '_input" data-fileuploader-files="' . $files . '" accept="' . (in_array('images', $type) ? implode(',', preg_filter('/^/', '.', array_map('trim', explode(',', IMAGE_FORMAT_ALLOWED)))) : implode(',', preg_filter('/^/', '.', array_map('trim', explode(',', DOCUMENT_FORMAT_ALLOWED))))) . '"' . (array_intersect(array('images', 'files'), $type) ? ' multiple' : null) . ' /><label class="form-control custom-file-label" for="' . $field . '_input">' . phrase('choose_file') . '</label></div>';
				}
				else if(array_intersect(array('attributes'), $type))
				{
					$original						= ($original ? json_decode($original, true) : array());
					$items							= null;
					
					if(is_array($original) && sizeof($original) > 0)
					{
						foreach($original as $item => $attribute)
						{
							if(!isset($attribute['label']) || !isset($attribute['value'])) continue;
							$items					.= '
								<div class="row mb-1">
									<div class="col-4 pe-0">
										<input type="text" name="' . $field . '[label][]" class="form-control form-control-sm" placeholder="' . phrase('label') . '" value="' . $attribute['label'] . '" autocomplete="off" spellcheck="false" />
									</div>
									<div class="col-5 pe-0">
										<input type="text" name="' . $field . '[value][]" class="form-control form-control-sm" placeholder="' . phrase('value') . '" value="' . $attribute['value'] . '" autocomplete="off" spellcheck="false" />
									</div>
									<div class="col-3">
										<div class="btn-group btn-group-sm float-end">
											<a href="javascript:void(0)" class="btn btn-secondary' . (!$read_only ? ' --move-up' : null) . '" data-element=".row" data-bs-toggle="tooltip" title="' . phrase('move_up') . '">
												<i class="mdi mdi-arrow-collapse-up"></i>
											</a>
											<a href="javascript:void(0)" class="btn btn-secondary' . (!$read_only ? ' --move-down' : null) . '" data-element=".row" data-bs-toggle="tooltip" title="' . phrase('move_down') . '">
												<i class="mdi mdi-arrow-collapse-down"></i>
											</a>
											<a href="javascript:void(0)" class="btn btn-secondary"' . (!$read_only ? ' role="remove-attribute"' : null) . ' data-element=".row">
												<i class="mdi mdi-window-close" data-bs-toggle="tooltip" title="' . phrase('remove') . '"></i>
											</a>
										</div>
									</div>
								</div>
							';
						}
					}
					else
					{
						$items						.= '
							<div class="row mb-1">
								<div class="col-4 pe-0">
									<input type="text" name="' . $field . '[label][]" class="form-control form-control-sm" placeholder="' . phrase('label') . '" autocomplete="off" spellcheck="false" />
								</div>
								<div class="col-5 pe-0">
									<input type="text" name="' . $field . '[value][]" class="form-control form-control-sm" placeholder="' . phrase('value') . '" autocomplete="off" spellcheck="false" />
								</div>
								<div class="col-3">
									<div class="btn-group btn-group-sm float-end">
										<a href="javascript:void(0)" class="btn btn-secondary' . (!$read_only ? ' --move-up' : null) . '" data-element=".row" data-bs-toggle="tooltip" title="' . phrase('move_up') . '">
											<i class="mdi mdi-arrow-collapse-up"></i>
										</a>
										<a href="javascript:void(0)" class="btn btn-secondary' . (!$read_only ? ' --move-down' : null) . '" data-element=".row" data-bs-toggle="tooltip" title="' . phrase('move_down') . '">
											<i class="mdi mdi-arrow-collapse-down"></i>
										</a>
										<a href="javascript:void(0)" class="btn btn-secondary"' . (!$read_only ? ' role="remove-attribute"' : null) . ' data-element=".row">
											<i class="mdi mdi-window-close" data-bs-toggle="tooltip" title="' . phrase('remove') . '"></i>
										</a>
									</div>
								</div>
							</div>
						';
					}
					
					$content						= '
						<div class="attribute-input">
							<div class="attribute-input-body">
								' . $items . '
							</div>
							<div class="row">
								<div class="col-4 pe-0">
									<div class="d-grid">
										<button type="button" class="btn btn-secondary btn-sm d-block"' . (!$read_only ? ' role="add-attribute"' : null) . ' data-label="' . $field . '[label][]" data-label-placeholder="' . phrase('label') . '" data-value-placeholder="' . phrase('value') . '" data-value="' . $field . '[value][]">
											<i class="mdi mdi-plus-circle-outline"></i>
											&nbsp;
											' . phrase('add') . '
										</button>
									</div>
								</div>
							</div>
						</div>
					';
				}
				else if(array_intersect(array('carousels'), $type))
				{
					$original						= ($original ? json_decode($original, true) : array());
					$items							= null;
					
					if(is_array($original) && sizeof($original) > 0)
					{
						foreach($original as $carousel => $item)
						{
							if(!isset($item['background']) || !isset($item['thumbnail']) || !isset($item['title']) || !isset($item['description']) || !isset($item['link']) || !isset($item['label'])) continue;
							$items					.= '
								<div class="card mb-3">
									<div class="card-body">
										<div class="row">
											<div class="col-md-6">
												<div class="form-group mb-3">
													<label class="text-muted">
														' . phrase('background') . '
													</label>
													<div data-provides="fileupload" class="fileupload fileupload-new text-sm-center">
														<span class="btn btn-file d-block">
															<input type="file" name="' . $field . '[background][' . $carousel . ']" accept="' . implode(',', preg_filter('/^/', '.', array_map('trim', explode(',', IMAGE_FORMAT_ALLOWED)))) . '" role="image-upload" id="' . $field . '_input"' . $read_only . ' />
															<div class="fileupload-new text-center">
																<img class="img-fluid upload_preview rounded" src="' . get_image($this->_set_upload_path, (isset($item['background']) ? $item['background'] : 'placeholder.png'), 'thumb') . '" alt="..." />
															</div>
														</span>
													</div>
												</div>
											</div>
											<div class="col-md-6">
												<div class="form-group mb-3">
													<label class="text-muted">
														' . phrase('thumbnail') . '
													</label>
													<div data-provides="fileupload" class="fileupload fileupload-new text-sm-center">
														<span class="btn btn-file d-block">
															<input type="file" name="' . $field . '[thumbnail][' . $carousel . ']" accept="' . implode(',', preg_filter('/^/', '.', array_map('trim', explode(',', IMAGE_FORMAT_ALLOWED)))) . '" role="image-upload" id="' . $field . '_input"' . $read_only . ' />
															<div class="fileupload-new text-center">
																<img class="img-fluid upload_preview rounded" src="' . get_image($this->_set_upload_path, (isset($item['thumbnail']) ? $item['thumbnail'] : 'placeholder.png'), 'thumb') . '" alt="..." />
															</div>
														</span>
													</div>
												</div>
											</div>
										</div>
										<div class="form-group mb-3">
											<input type="text" name="' . $field . '[title][' . $carousel . ']" class="form-control" placeholder="' . phrase('title') . '" value="' . (isset($item['title']) ? $item['title'] : null) . '" id="' . $field . '_input" spellcheck="false"' . $read_only . ' />
										</div>
										<div class="form-group mb-3">
											<textarea name="' . $field . '[description][' . $carousel . ']" class="form-control" placeholder="' . phrase('description') . '" id="' . $field . '_input" rows="1" spellcheck="false"' . $read_only . '>' . (isset($item['description']) ? $item['description'] : null) . '</textarea>
										</div>
										<div class="row">
											<div class="col-md-6">
												<div class="form-group mb-3">
													<input type="text" name="' . $field . '[link][' . $carousel . ']" class="form-control" placeholder="' . phrase('target_url') . '" value="' . (isset($item['link']) ? $item['link'] : null) . '" id="' . $field . '_input" spellcheck="false"' . $read_only . ' />
												</div>
											</div>
											<div class="col-md-6">
												<div class="form-group mb-3">
													<input type="text" name="' . $field . '[label][' . $carousel . ']" class="form-control" placeholder="' . phrase('button_label') . '" value="' . (isset($item['label']) ? $item['label'] : null) . '" id="' . $field . '_input" spellcheck="false"' . $read_only . ' />
												</div>
											</div>
										</div>
									</div>
									<div class="card-footer pt-1 pb-1">
										<input type="hidden" name="' . $field . '[default_background][' . $carousel . ']" value="' . (isset($item['background']) ? $item['background'] : null) . '"' . $read_only . ' />
										<input type="hidden" name="' . $field . '[default_thumbnail][' . $carousel . ']" value="' . (isset($item['thumbnail']) ? $item['thumbnail'] : null) . '"' . $read_only . ' />
										<div class="btn-group btn-group-sm">
											<a href="javascript:void(0)" class="btn btn-secondary' . (!$read_only ? ' --move-up' : null) . '" data-element=".card" data-bs-toggle="tooltip" title="' . phrase('move_up') . '">
												<i class="mdi mdi-arrow-collapse-up"></i>
											</a>
											<a href="javascript:void(0)" class="btn btn-secondary' . (!$read_only ? ' --move-down' : null) . '" data-element=".card" data-bs-toggle="tooltip" title="' . phrase('move_down') . '">
												<i class="mdi mdi-arrow-collapse-down"></i>
											</a>
										</div>
										<a href="javascript:void(0)" class="btn btn-outline-danger btn-sm float-end"' . (!$read_only ? ' role="remove-carousel"' : null) . ' data-element=".card">
											<i class="mdi mdi-delete" data-bs-toggle="tooltip" title="' . phrase('remove') . '"></i>
										</a>
									</div>
								</div>
							';
						}
					}
					else
					{
						$items						= '
							<div class="card mb-3">
								<div class="card-body">
									<div class="row">
										<div class="col-md-6">
											<div class="form-group mb-3">
												<label class="text-muted">
													' . phrase('background') . '
												</label>
												<div data-provides="fileupload" class="fileupload fileupload-new text-sm-center">
													<span class="btn btn-file d-block">
														<input type="file" name="' . $field . '[background][]" accept="' . implode(',', preg_filter('/^/', '.', array_map('trim', explode(',', IMAGE_FORMAT_ALLOWED)))) . '" role="image-upload" id="' . $field . '_input"' . $read_only . ' />
														<div class="fileupload-new text-center">
															<img class="img-fluid upload_preview rounded" src="' . get_image($this->_set_upload_path, 'placeholder.png', 'thumb') . '" alt="" />
														</div>
													</span>
												</div>
											</div>
										</div>
										<div class="col-md-6">
											<div class="form-group mb-3">
												<label class="text-muted">
													' . phrase('thumbnail') . '
												</label>
												<div data-provides="fileupload" class="fileupload fileupload-new text-sm-center">
													<span class="btn btn-file d-block">
														<input type="file" name="' . $field . '[thumbnail][]" accept="' . implode(',', preg_filter('/^/', '.', array_map('trim', explode(',', IMAGE_FORMAT_ALLOWED)))) . '" role="image-upload" id="' . $field . '_input"' . $read_only . ' />
														<div class="fileupload-new text-center">
															<img class="img-fluid upload_preview rounded" src="' . get_image($this->_set_upload_path, 'placeholder.png', 'thumb') . '" alt="" />
														</div>
													</span>
												</div>
											</div>
										</div>
									</div>
									<div class="mb-3">
										<input type="text" name="' . $field . '[title][]" class="form-control" placeholder="' . phrase('title') . '" id="' . $field . '_input" spellcheck="false"' . $read_only . ' />
									</div>
									<div class="mb-3">
										<textarea name="' . $field . '[description][]" class="form-control" placeholder="' . phrase('description') . '" id="' . $field . '_input" rows="1" spellcheck="false"' . $read_only . '></textarea>
									</div>
									<div class="row">
										<div class="col-md-6">
											<div class="mb-3">
												<input type="text" name="' . $field . '[link][]" class="form-control" placeholder="' . phrase('target_url') . '" id="' . $field . '_input" spellcheck="false"' . $read_only . ' />
											</div>
										</div>
										<div class="col-md-6">
											<div class="mb-3">
												<input type="text" name="' . $field . '[label][]" class="form-control" placeholder="' . phrase('button_label') . '" id="' . $field . '_input" spellcheck="false"' . $read_only . ' />
											</div>
										</div>
									</div>
								</div>
								<div class="card-footer pt-1 pb-1">
									<div class="btn-group btn-group-sm">
										<a href="javascript:void(0)" class="btn btn-secondary' . (!$read_only ? ' --move-up' : null) . '" data-element=".card" data-bs-toggle="tooltip" title="' . phrase('move_up') . '">
											<i class="mdi mdi-arrow-collapse-up"></i>
										</a>
										<a href="javascript:void(0)" class="btn btn-secondary' . (!$read_only ? ' --move-down' : null) . '" data-element=".card" data-bs-toggle="tooltip" title="' . phrase('move_down') . '">
											<i class="mdi mdi-arrow-collapse-down"></i>
										</a>
									</div>
									<a href="javascript:void(0)" class="btn btn-outline-danger btn-sm float-end"' . (!$read_only ? ' role="remove-carousel"' : null) . ' data-element=".card">
										<i class="mdi mdi-delete" data-bs-toggle="tooltip" title="' . phrase('remove') . '"></i>
									</a>
								</div>
							</div>
						';
					}
					
					$content						= $items . '<a href="javascript:void(0)" class="btn btn-light d-block"' . (!$read_only ? ' role="add-carousel"' : null) . ' data-field="' . $field . '" data-image-placeholder="' . get_image($this->_set_upload_path, 'placeholder.png', 'thumb') . '" style="border:2px dashed #ddd"><i class="mdi mdi-plus-circle-outline"></i>&nbsp;' . phrase('add') . '</a>';
				}
				else if(array_intersect(array('faqs'), $type))
				{
					$original						= ($original ? json_decode($original, true) : array());
					$items							= null;
					
					if(is_array($original) && sizeof($original) > 0)
					{
						foreach($original as $faq => $item)
						{
							if(!isset($item['question']) || !isset($item['answer'])) continue;
							$items					.= '
								<div class="card mb-3">
									<div class="card-header p-2">
										<div class="input-group input-group-sm">
											<input type="text" name="' . $field . '[question][]" class="form-control" placeholder="' . phrase('question') . '" value="' . $item['question'] . '" id="' . $field . '_input" spellcheck="false" />
											<a href="javascript:void(0)" class="btn btn-secondary' . (!$read_only ? ' --move-up' : null) . '" data-element=".card" data-bs-toggle="tooltip" title="' . phrase('move_up') . '">
												<i class="mdi mdi-arrow-collapse-up"></i>
											</a>
											<a href="javascript:void(0)" class="btn btn-secondary' . (!$read_only ? ' --move-down' : null) . '" data-element=".card" data-bs-toggle="tooltip" title="' . phrase('move_down') . '">
												<i class="mdi mdi-arrow-collapse-down"></i>
											</a>
											<a href="javascript:void(0)" class="btn btn-secondary"' . (!$read_only ? ' role="remove-faq"' : null) . ' data-element=".card">
												<i class="mdi mdi-delete" data-bs-toggle="tooltip" title="' . phrase('remove') . '"></i>
											</a>
										</div>
									</div>
									<div class="card-body p-2">
										<textarea name="' . $field . '[answer][]" class="form-control" role="wysiwyg" placeholder="' . phrase('detailed_answer_for_above_question') . '" id="' . $field . '_input" rows="1" spellcheck="false"' . $read_only . '>' . $item['answer'] . '</textarea>
									</div>
								</div>
							';
						}
					}
					else
					{
						$items						.= '
							<div class="card mb-3">
								<div class="card-header p-2">
									<div class="input-group input-group-sm">
										<input type="text" name="' . $field . '[question][]" class="form-control" placeholder="' . phrase('question') . '" id="' . $field . '_input" spellcheck="false"' . $read_only . ' />
										<a href="javascript:void(0)" class="btn btn-secondary' . (!$read_only ? ' --move-up' : null) . '" data-element=".card" data-bs-toggle="tooltip" title="' . phrase('move_up') . '">
											<i class="mdi mdi-arrow-collapse-up"></i>
										</a>
										<a href="javascript:void(0)" class="btn btn-secondary' . (!$read_only ? ' --move-down' : null) . '" data-element=".card" data-bs-toggle="tooltip" title="' . phrase('move_down') . '">
											<i class="mdi mdi-arrow-collapse-down"></i>
										</a>
										<a href="javascript:void(0)" class="btn btn-secondary"' . (!$read_only ? ' role="remove-faq"' : null) . ' data-element=".card">
											<i class="mdi mdi-delete" data-bs-toggle="tooltip" title="' . phrase('remove') . '"></i>
										</a>
									</div>
								</div>
								<div class="card-body p-2">
									<textarea name="' . $field . '[answer][]" class="form-control" role="wysiwyg" placeholder="' . phrase('detailed_answer_for_above_question') . '" id="' . $field . '_input" rows="1" spellcheck="false"' . $read_only . '></textarea>
								</div>
							</div>
						';
					}
					
					$content						= $items . '<a href="javascript:void(0)" class="btn btn-light d-block"' . (!$read_only ? ' role="add-faq"' : null) . ' data-field="' . $field . '" style="border:2px dashed #ddd"><i class="mdi mdi-plus-circle-outline"></i>&nbsp;' . phrase('add') . '</a>';
				}
				else if(array_intersect(array('steps'), $type))
				{
					$original						= ($original ? json_decode($original, true) : array());
					$items							= null;
					
					if(is_array($original) && sizeof($original) > 0)
					{
						foreach($original as $step => $item)
						{
							$items					.= '
								<div class="card mb-3">
									<div class="card-body p-2 position-relative">
										<div class="btn-group btn-group-xs absolute" style="top: 5px; right: 10px; z-index: 1">
											<a href="javascript:void(0)" class="btn btn-secondary' . (!$read_only ? ' --move-up' : null) . '" data-element=".card" data-bs-toggle="tooltip" title="' . phrase('move_up') . '">
												<i class="mdi mdi-arrow-collapse-up"></i>
											</a>
											<a href="javascript:void(0)" class="btn btn-secondary' . (!$read_only ? ' --move-down' : null) . '" data-element=".card" data-bs-toggle="tooltip" title="' . phrase('move_down') . '">
												<i class="mdi mdi-arrow-collapse-down"></i>
											</a>
											<a href="javascript:void(0)" class="btn btn-secondary"' . (!$read_only ? ' role="remove-step"' : null) . ' data-element=".card">
												<i class="mdi mdi-delete" data-bs-toggle="tooltip" title="' . phrase('remove') . '"></i>
											</a>
										</div>
										<textarea name="' . $field . '[]" class="form-control" role="wysiwyg" placeholder="' . phrase('add_step') . '" id="' . $field . '_input" rows="1" spellcheck="false"' . $read_only . '>' . $item . '</textarea>
									</div>
								</div>
							';
						}
					}
					else
					{
						$items						.= '
							<div class="card mb-3">
								<div class="card-body p-2 position-relative">
									<div class="btn-group btn-group-xs absolute" style="top: 5px; right: 10px; z-index: 1">
										<a href="javascript:void(0)" class="btn btn-secondary' . (!$read_only ? ' --move-up' : null) . '" data-element=".card" data-bs-toggle="tooltip" title="' . phrase('move_up') . '">
											<i class="mdi mdi-arrow-collapse-up"></i>
										</a>
										<a href="javascript:void(0)" class="btn btn-secondary' . (!$read_only ? ' --move-down' : null) . '" data-element=".card" data-bs-toggle="tooltip" title="' . phrase('move_down') . '">
											<i class="mdi mdi-arrow-collapse-down"></i>
										</a>
										<a href="javascript:void(0)" class="btn btn-secondary"' . (!$read_only ? ' role="remove-step"' : null) . ' data-element=".card">
											<i class="mdi mdi-delete" data-bs-toggle="tooltip" title="' . phrase('remove') . '"></i>
										</a>
									</div>
									<textarea name="' . $field . '[]" class="form-control" role="wysiwyg" placeholder="' . phrase('add_step') . '" id="' . $field . '_input" rows="1" spellcheck="false"' . $read_only . '></textarea>
								</div>
							</div>
						';
					}
					
					$content						= $items . '<a href="javascript:void(0)" class="btn btn-light d-block"' . (!$read_only ? ' role="add-step"' : null) . ' data-field="' . $field . '" style="border:2px dashed #ddd"><i class="mdi mdi-plus-circle-outline"></i>&nbsp;' . phrase('add') . '</a>';
				}
				else if(array_intersect(array('dropdown', 'checkbox', 'radio'), $type))
				{
					if(array_intersect(array('dropdown'), $type))
					{
						$options					= '<option value="" readonly>' . phrase('please_choose') . '</option>';
					}
					else
					{
						$options					= null;
					}
					
					if(is_array($parameter) && sizeof($parameter) > 0)
					{
						$num						= 0;
						
						foreach($parameter as $value => $label)
						{
							if(array_intersect(array('dropdown'), $type))
							{
								$options			.= '<option value="' . $value . '"' . ($default_value == $value || in_array($value, array($original, $extra_params)) ? ' selected' : null) . '>' . $label . '</option>';
							}
							else if(array_intersect(array('checkbox'), $type))
							{
								$checker			= ($original ? json_decode($original, true) : array());
								
								if(!is_array($checker))
								{
									$checker		= array($original);
								}
								
								$options			.= '
									<div class="form-check form-switch' . (sizeof($parameter) > 1 && sizeof($parameter) <= 3 ? ' d-inline-block me-3' : null) . '">
										<label class="form-check-label" for="check_' . $num . '">
											<input type="checkbox" name="' . $field . '[]" value="' . $value . '" class="form-check-input ' . $extra_class . '" id="check_' . $num . '"' . ($default_value == $value || in_array($value, $checker) ? ' checked' : null) . $read_only . ' />
											' . $label . '
										</label>
										' . $extra_params . '
									</div>
								';
							}
							else if(array_intersect(array('radio'), $type))
							{
								$options			.= '
									<div class="form-check' . (sizeof($parameter) > 1 && sizeof($parameter) <= 3 ? ' d-inline-block me-3' : null) . '">
										<label class="' . $extra_class . '">
											<input type="radio" name="' . $field . '" class="form-check-input" value="' . $value . '"' . ($default_value == $value || $value == $original ? ' checked' : null) . $read_only . ' />
											' . $label . '
										</label>
										' . $extra_params . '
									</div>
								';
							}
							
							$num++;
						}
					}
					
					if(array_intersect(array('dropdown'), $type))
					{
						$content					= '
							<select name="' . $field . '" class="form-control' . $extra_class . '" placeholder="' . (isset($this->_set_placeholder[$field]) ? $this->_set_placeholder[$field] : phrase('please_choose')) . '" id="' . $field . '_input"' . $read_only . '>
								' . $options . '
							</select>
						';
					}
					else
					{
						$content					= $options;
					}
					
					$content						= $content;
				}
				else if(array_intersect(array('tagsinput'), $type))
				{
					$content						= '<textarea name="' . $field . '" class="form-control' . $extra_class . '" role="tagsinput" placeholder="' . (isset($this->_set_placeholder[$field]) ? $this->_set_placeholder[$field] : phrase('separate_with_comma')) . '" id="' . $field . '_input"' . $read_only . ' spellcheck="false" rows="1">' . ($default_value ? $default_value : $original) . '</textarea>';
				}
				else if(array_intersect(array('hour'), $type))
				{
					$options						= '<option value="" readonly>' . phrase('please_choose') . '</option>';
					
					foreach(range(1, 24) as $k => $h)
					{
						$options					.= '<option value="' . $h . '"' . ($default_value == $h || $original == $h ? ' selected' : null) . '>' . sprintf('%02d', (24 == $h ? '00' : $h)) . ':00</option>';
					}
					
					$content						= '
						<select name="' . $field . '" class="form-control' . $extra_class . '" placeholder="' . (isset($this->_set_placeholder[$field]) ? $this->_set_placeholder[$field] : phrase('choose_hour')) . '" id="' . $field . '_input"' . $read_only . '>
							' . $options . '
						</select>
					';
				}
				else if(array_intersect(array('date_only'), $type))
				{
					$options						= '<option value="" readonly>' . phrase('please_choose') . '</option>';
					
					foreach(range(1, 31) as $k => $d)
					{
						$options					.= '<option value="' . $d . '"' . ( $default_value == $d || $original == $d ? ' selected' : null) . '>' . sprintf('%02d', $d) . '</option>';
					}
					
					$content						= '
						<select name="' . $field . '" class="form-control' . $extra_class . '" placeholder="' . (isset($this->_set_placeholder[$field]) ? $this->_set_placeholder[$field] : phrase('choose_date')) . '" id="' . $field . '_input"' . $read_only . '>
							' . $options . '
						</select>
					';
				}
				else if(array_intersect(array('date', 'datepicker'), $type))
				{
					$content						= '<input type="text" name="' . $field . '" class="form-control' . $extra_class . '" role="datepicker" data-format="dd MM yyyy" data-modal="true" data-large-mode="true" placeholder="' . (isset($this->_set_placeholder[$field]) ? $this->_set_placeholder[$field] : phrase('click_to_select_date')) . '" value="' . ($default_value && $default_value != '0000-00-00' ? $default_value : ($original && $original != '0000-00-00' ? date('d F Y', strtotime($original)) : date('d F Y'))) . '" id="' . $field . '_input" maxlength="' . $max_length . '" readonly' . $read_only . ' spellcheck="false" />';
				}
				else if(array_intersect(array('datetime', 'datetimepicker'), $type))
				{
					$content						= '<input type="text" name="' . $field . '" class="form-control' . $extra_class . '" role="datetimepicker" data-modal="true" data-large-mode="true" placeholder="' . (isset($this->_set_placeholder[$field]) ? $this->_set_placeholder[$field] : phrase('click_to_select_date')) . '" value="' . date('d F Y H:i:s', strtotime(($default_value && $default_value != '0000-00-00 00:00:00' ? $default_value : ($original && $original != '0000-00-00 00:00:00' ? $original : date('Y-m-d H:i:s'))))) . '" id="' . $field . '_input" maxlength="' . $max_length . '"' . $read_only . ' spellcheck="false" />';
				}
				else if(array_intersect(array('monthpicker'), $type))
				{
					$month							= range(1, 12);
					$options						= '<option value="" readonly>' . phrase('please_choose') . '</option>';
					
					foreach($month as $m => $n)
					{
						$curmonth					= explode('-', $original);
						$n							= sprintf('%02d', $n);
						$options					.= '<option value="' . $n . '"' . ($default_value == $n || (isset($curmonth[1]) && $curmonth[1] == $n) ? ' selected' : null) . '>' . phrase(date('F', mktime(0, 0, 0, $n, 10))) . '</option>';
					}
					
					$content						= '
						<select name="' . $field . '" class="form-control' . $extra_class . '" placeholder="' . (isset($this->_set_placeholder[$field]) ? $this->_set_placeholder[$field] : phrase('choose_month')) . '" id="' . $field . '_input"' . $read_only . '>
							' . $options . '
						</select>
					';
				}
				else if(array_intersect(array('yearpicker'), $type))
				{
					$year							= range(1970, date('Y'));
					$options						= '<option value="" readonly>' . phrase('please_choose') . '</option>';
					
					foreach($year as $k => $y)
					{
						$options					.= '<option value="' . $y . '"' . ($default_value == $y || $original == $y ? ' selected' : null) . '>' . $y . '</option>';
					}
					
					$content						= '
						<select name="' . $field . '" class="form-control' . $extra_class . '" placeholder="' . (isset($this->_set_placeholder[$field]) ? $this->_set_placeholder[$field] : phrase('choose_year')) . '" id="' . $field . '_input"' . $read_only . '>
							' . $options . '
						</select>
					';
				}
				else if(array_intersect(array('quarterly'), $type))
				{
					$year							= get_userdata('year');
					
					$content						= '
						<select name="' . $field . '" class="form-control ' . $extra_class . '" id="' . $field . '_input"' . $read_only . '>
							<option value="" readonly>' . phrase('please_choose') . '</option>
							<option value="' . $year . '-01-01"' . ($default_value == $year . '-01-01' || $original == $year . '-01-01' ? ' selected' : null) . '>' . phrase('quarter') . ' I</option>
							<option value="' . $year . '-04-01"' . ($default_value == $year . '-04-01' || $original == $year . '-04-01' ? ' selected' : null) . '>' . phrase('quarter') . ' II</option>
							<option value="' . $year . '-07-01"' . ($default_value == $year . '-07-01' || $original == $year . '-07-01' ? ' selected' : null) . '>' . phrase('quarter') . ' III</option>
							<option value="' . $year . '-10-01"' . ($default_value == $year . '-10-01' || $original == $year . '-10-01' ? ' selected' : null) . '>' . phrase('quarter') . ' IV</option>
						</select>
					';
				}
				else if(array_intersect(array('colorpicker'), $type))
				{
					$content						= '<div class="input-group"><span class="input-group-text">' . phrase('pick_a_color') . '</span><input type="color" name="' . $field . '" class="form-control form-control-color" value="' . ($default_value ? $default_value : $original) . '"' . $read_only . ' /></div>';
				}
				else if(array_intersect(array('wysiwyg'), $type))
				{
					$content						= '<textarea name="' . $field . '" class="form-control' . $extra_class . '" role="wysiwyg" data-upload-path="' . $this->_set_upload_path . '" placeholder="' . (isset($this->_set_placeholder[$field]) ? $this->_set_placeholder[$field] : phrase('enter_the_content_here')) . '" id="' . $field . '_input"' . $attribute . ' maxlength="' . $max_length . '" rows="1" spellcheck="false"' . $read_only . '>' . ($default_value ? $default_value : $original) . '</textarea>';
				}
				else if(array_intersect(array('textarea'), $type))
				{
					$content						= '<textarea name="' . $field . '" class="form-control' . (in_array('autocomplete', $type) && $extra_params ? ' on-autocomplete-trigger' : null) . $extra_class . '" id="' . $field . '_input" placeholder="' . (isset($this->_set_placeholder[$field]) ? $this->_set_placeholder[$field] : null) . '"' . $attribute . ' maxlength="' . $max_length . '" rows="1"' . (in_array('autocomplete', $type) ? ' role="autocomplete" data-href="' . current_page() . '"': '') . ' spellcheck="false"' . $read_only . '>' . ($default_value ? $default_value : htmlspecialchars($original)) . '</textarea>';
				}
				else if(array_intersect(array('price_format'), $type))
				{
					$content						= '<input type="text" name="' . $field . '" min="0" class="form-control text-end' . $extra_class . '" value="' . ($default_value ? $default_value : ($original ? $original : 0)) . '" role="price" placeholder="' . (isset($this->_set_placeholder[$field]) ? $this->_set_placeholder[$field] : phrase('number_only')) . '" id="' . $field . '_input" maxlength="' . $max_length . '" spellcheck="false"' . $read_only . ' />';
				}
				else if(array_intersect(array('int', 'integer', 'numeric', 'number_format', 'percent_format'), $type))
				{
					$content						= '<input type="number" name="' . $field . '" min="0" class="form-control' . $extra_class . '" value="' . (float) ($default_value ? $default_value : ($original ? $original : 0)) . '" placeholder="' . (isset($this->_set_placeholder[$field]) ? $this->_set_placeholder[$field] : phrase('number_only')) . '" id="' . $field . '_input" maxlength="' . $max_length . '"' . (is_numeric($parameter) || array_intersect(array('numeric', 'number_format','percent_format'), $type) ? ' pattern="[0-9]+([\.,][0-9]+)?" step="0.0001"' : '') . $read_only . ' />';
					
					if(array_intersect(array('percent_format'), $type))
					{
						$content					= '<div class="input-group">' . $content . '<span class="input-group-text">%</span></div>';
					}
				}
				else if(array_intersect(array('boolean'), $type))
				{
					$content						= '
						<div class="form-check form-switch">
							<input type="checkbox" name="' . $field . '" value="1" class="form-check-input" id="' . $field . '_input"' . ($default_value == 1 || $original == 1 || ('create' == $this->_method && (!isset($this->_default_value[$field]) || (isset($this->_default_value[$field]) && $this->_default_value[$field] == 1))) ? ' checked' : null) . $read_only . ' />
							<label class="form-check-label" for="' . $field . '_input">
								' . (isset($this->_set_option_label[$field]) ? $this->_set_option_label[$field] : phrase('check_to_activate')) . '
							</label>
						</div>
					';
				}
				else if(array_intersect(array('email'), $type))
				{
					$content						= '<input type="email" name="' . $field . '" class="form-control' . $extra_class . '" placeholder="' . phrase('enter_your_email') . '" value="' . ($default_value ? $default_value : $original) . '" id="' . $field . '_input" maxlength="' . $max_length . '"' . $read_only . ' />';
				}
				else if(array_intersect(array('password'), $type))
				{
					$content						= '
						<div class="row">
							<div class="col">
								<input type="password" name="' . $field . '" class="form-control" placeholder="' . ('update' == $this->_method ? phrase('leave_blank_to_ignore') : phrase('enter_your_password')) . '" autocomplete="new-password" id="' . $field . '_input" maxlength="' . $max_length . '"' . $read_only . ' />
							</div>
							<div class="col">
								<input type="password" name="' . $field . '_confirmation" class="form-control" placeholder="' . $alias . ' ' . phrase('confirmation') . '" autocomplete="new-password" id="' . $field . '_confirmation_input" maxlength="' . $max_length . '"' . $read_only . ' />
							</div>
						</div>
					';
				}
				else if(array_intersect(array('encryption'), $type))
				{
					$content						= '<input type="password" name="' . $field . '" class="form-control' . $extra_class . '" value="" id="' . $field . '_input"' . ('update' == $this->_method ? ' placeholder="' . phrase('leave_blank_to_ignore') . '"' : null) . ' maxlength="' . $max_length . '"' . $read_only . ' />';
				}
				else if(array_intersect(array('hidden'), $type))
				{
					$content						= '<input type="hidden" name="' . $field . '" value="' . ($default_value ? $default_value : htmlspecialchars($original)) . $read_only . '" />';
				}
				else if(array_intersect(array('custom_format'), $type))
				{
					$callback						= str_replace('callback_', '', $parameter);
					
					if($callback && method_exists($this, $callback))
					{
						$content					= $this->$callback($serialized);
					}
					else
					{
						$content					= $parameter;
						preg_match_all('#\{(.*?)\}#', $content, $matches);
						$matches					= $matches[1];
						
						if($matches)
						{
							foreach($matches as $do => $magic)
							{
								if(isset($serialized[$magic]['content']))
								{
									$content		= str_replace('{' . $magic . '}', $serialized[$magic]['content'], $content);
								}
							}
						}
					}
				}
				else if(array_intersect(array('last_insert'), $type))
				{
					if(!$default_value)
					{
						$type_key					= ($parameter ? array_search('{1}', explode('/', $parameter)) : null);
						
						if('create' == $this->_method)
						{
							$where					= array();
							
							if($this->_where)
							{
								foreach($this->_where as $k => $v)
								{
									if($this->model->field_exists($k, $this->_table))
									{
										$where[$k]	= $v;
									}
								}
							}
							
							if(in_array($this->_db_driver, array('SQLSRV')))
							{
								$cast_field			= 'CONVERT(' . $field . ', SIGNED INTEGER)';
							}
							else
							{
								$cast_field			= 'CAST(' . $field . ' AS INTEGER)';
							}
							
							if($skip)
							{
								if(is_array($parameter))
								{
									$this->model->where($parameter);
								}
								
								$last_insert		= $this->model->select((in_array($this->_db_driver, array('Postgre')) ? 'NULLIF' : 'IFNULL') . '(MAX(' . $cast_field . '), 0) AS ' . $field)->order_by($field, 'desc')->get($this->_table, 1)->row($field);
							}
							else
							{
								$last_insert		= $this->model->select((in_array($this->_db_driver, array('Postgre')) ? 'NULLIF' : 'IFNULL') . '(MAX(' . $cast_field . '), 0) AS ' . $field)->order_by($field, 'desc')->get_where($this->_table, $where, 1)->row($field);
							}
							
							if($last_insert)
							{
								$last_insert		= (strpos($last_insert, '/') !== false ? explode('/', $last_insert) : array($last_insert));
								$last_insert		= (isset($last_insert[$type_key]) ? $last_insert[$type_key] : $last_insert[0]);
								$last_insert		= preg_replace('/[^0-9]/', '', $last_insert);
							}
							
							if('create' == $this->_method)
							{
								$last_insert		= ($last_insert > 0 ? $last_insert : 0) + 1;
							}
							
							$original				= ($last_insert > 0 ? $last_insert : 1);
							
							if(array_intersect(array('sprintf'), $type))
							{
								$original			= sprintf((is_string($extra_params) ? $extra_params : '%04d'), $original);
							}
							
							if($parameter)
							{
								$original			= str_replace('{1}', $original, $parameter);
							}
						}
						else
						{
							if(array_intersect(array('sprintf'), $type))
							{
								$original			= str_replace('{1}', sprintf((is_string($extra_params) ? $extra_params : '%04d'), $original), $parameter);
							}
						}
					}
					
					$content						= '<input type="text" name="' . $field . '" class="form-control' . $extra_class . '" value="' . ($default_value ? $default_value : $original) . '" id="' . $field . '_input" maxlength="' . $max_length . '" spellcheck="false"' . $read_only . ' />';
				}
				else if(array_intersect(array('to_slug'), $type))
				{
					$content						= '<input type="text" name="' . $field . '" class="form-control' . $extra_class . '" value="' . ($default_value ? $default_value : $original) . '" placeholder="' . (!$read_only ? phrase('leave_blank_to_generate_automatically') : null) . '" id="' . $field . '_input" maxlength="' . $max_length . '" spellcheck="false"' . $read_only . ' />';
				}
				else if(array_intersect(array('coordinate'), $type))
				{
					$content						= '
						<div class="drawing-placeholder preloader position-relative w-100" style="overflow:hidden">
							<div id="map_' . $field . rand(0, 999) . '"' . ($extra_class ? ' class="' . $extra_class . '"' : null) . ' role="map" data-coordinate="' . strip_tags(htmlspecialchars(($original && is_json($original) ? $original : (get_setting('office_map') ? get_setting('office_map') : '[]')))) . '" data-apply-coordinate-to="#' . $field . '_input" data-apply-address-to=".map-address-listener"' . (!$read_only ? ' data-draggable="1"' : null) . ' data-geocoder="1"' . (!$original ? ' data-geolocation="1"' : null) . ' data-mousewheel="0" title="' . phrase('drag_marker_to_update_the_location') . '"' . (isset($this->_set_attribute[$field]) ? ' ' . $this->_set_attribute[$field] : null) . ' style="height:260px"></div>
							<input type="hidden" name="' . $field . '" id="' . $field . '_input" value="' . strip_tags(htmlspecialchars(($original && is_json($original) ? $original : (get_setting('office_map') ? get_setting('office_map') : '[]')))) . '"' . $read_only . ' />
						</div>
					';
				}
				else if(array_intersect(array('point'), $type))
				{
					$content						= '
						<div class="drawing-placeholder preloader position-relative w-100" style="overflow:hidden">
							<div id="map_' . $field . rand(0, 999) . '"' . ($extra_class ? ' class="' . $extra_class . '"' : null) . ' role="map" data-coordinate="' . strip_tags(htmlspecialchars(get_setting('office_map'))) . '" data-geojson="' . strip_tags(htmlspecialchars(($original && is_json($original) ? $original : '[]'))) . '" data-apply-coordinate-to="#' . $field . '_input"' . (!$read_only && in_array($this->_method, array('create', 'update')) ? ' data-drawing-manager="1" data-drawing-type="point"' : null) . ' data-geocoder="1"' . (!$original ? ' data-geolocation="1"' : null) . ' data-mousewheel="0" title="' . phrase('click_map_to_start_drawing') . '"' . (isset($this->_set_attribute[$field]) ? ' ' . $this->_set_attribute[$field] : null) . ' style="height:360px"></div>
							<input type="hidden" name="' . $field . '" id="' . $field . '_input" value="' . strip_tags(htmlspecialchars(($original && is_json($original) ? $original : '[]'))) . '"' . $read_only . ' />
						</div>
					';
				}
				else if(array_intersect(array('polygon'), $type))
				{
					$content						= '
						<div class="drawing-placeholder preloader position-relative w-100" style="overflow:hidden">
							<div id="map_' . $field . rand(0, 999) . '"' . ($extra_class ? ' class="' . $extra_class . '"' : null) . ' role="map" data-coordinate="' . strip_tags(htmlspecialchars(get_setting('office_map'))) . '" data-geojson="' . strip_tags(htmlspecialchars(($original && is_json($original) ? $original : '[]'))) . '" data-apply-coordinate-to="#' . $field . '_input"' . (!$read_only && in_array($this->_method, array('create', 'update')) ? ' data-drawing-manager="1" data-drawing-type="polygon"' : null) . ' data-geocoder="1"' . (!$original ? ' data-geolocation="1"' : null) . ' data-mousewheel="0" title="' . phrase('click_map_to_start_drawing') . '"' . (isset($this->_set_attribute[$field]) ? ' ' . $this->_set_attribute[$field] : null) . ' style="height:360px"></div>
							<input type="hidden" name="' . $field . '" id="' . $field . '_input" value="' . strip_tags(htmlspecialchars(($original && is_json($original) ? $original : '[]'))) . '"' . $read_only . ' />
						</div>
					';
				}
				else if(array_intersect(array('linestring'), $type))
				{
					$content						= '
						<div class="drawing-placeholder preloader position-relative w-100" style="overflow:hidden">
							<div id="map_' . $field . rand(0, 999) . '"' . ($extra_class ? ' class="' . $extra_class . '"' : null) . ' role="map" data-coordinate="' . strip_tags(htmlspecialchars(get_setting('office_map'))) . '" data-geojson="' . strip_tags(htmlspecialchars(($original && is_json($original) ? $original : '[]'))) . '" data-apply-coordinate-to="#' . $field . '_input"' . (!$read_only && in_array($this->_method, array('create', 'update')) ? ' data-drawing-manager="1" data-drawing-type="linestring"' : null) . ' data-geocoder="1"' . (!$original ? ' data-geolocation="1"' : null) . ' data-mousewheel="0" title="' . phrase('click_map_to_start_drawing') . '"' . (isset($this->_set_attribute[$field]) ? ' ' . $this->_set_attribute[$field] : null) . ' style="height:360px"></div>
							<input type="hidden" name="' . $field . '" id="' . $field . '_input"' . $attribute . ' value="' . strip_tags(htmlspecialchars(($original && is_json($original) ? $original : '[]'))) . '"' . $read_only . ' />
						</div>
					';
				}
				else
				{
					$content						= '<input type="text" name="' . $field . '" class="form-control' . (in_array('autocomplete', $type) && $extra_params ? ' on-autocomplete-trigger' : null) . $extra_class . '" value="' . ($default_value ? $default_value : htmlspecialchars($original)) . '" id="' . $field . '_input"' . $attribute . ' maxlength="' . $max_length . '" placeholder="' . (isset($this->_set_placeholder[$field]) ? $this->_set_placeholder[$field] : null) . '"' . (in_array('autocomplete', $type) && $extra_params ? ' role="autocomplete" data-href="' . current_page() . '"': '') . ' spellcheck="false"' . $read_only . ' />';
				}
				
				$fields[$field]						= array
				(
					'type'							=> $type,
					'label'							=> (!isset($this->_set_option_label[$field]) || strtolower($alias) != strtolower($this->_set_option_label[$field]) ? $alias : ''),
					'tooltip'						=> (isset($this->_set_tooltip[$field]) ? $this->_set_tooltip[$field] : null),
					'content'						=> $content,
					'original'						=> $original,
					'required'						=> $required,
					'position'						=> $position,
					'prepend'						=> (isset($this->_field_prepend[$field]) ? $this->_field_prepend[$field] : null),
					'append'						=> (isset($this->_field_append[$field]) ? $this->_field_append[$field] : null)
				);
				
				if($this->_api_request)
				{
					unset($fields[$field]['tooltip'], $fields[$field]['original'], $fields[$field]['content'], $fields[$field]['position'], $fields[$field]['append'], $fields[$field]['prepend']);
					
					$fields[$field]['value']		= $original;
				}
			}
		}
		
		$column_size								= 6;
		$column_offset								= 3;
		$highest_column								= 1;
		
		if(is_array($this->_field_position) && sizeof($this->_field_position) > 0)
		{
			$highest_column							= max($this->_field_position);
		}
		
		$query_string								= array_replace(service('request')->getGet(), $primary_key);
		
		if($this->_api_request)
		{
			unset($query_string['aksara'], $query_string['limit']);
		}
		
		$output										= array
		(
			'column_size'							=> $this->_column_size,
			'column_total'							=> $highest_column,
			'extra_action'							=> array
			(
				'submit'							=> $this->_extra_submit
			),
			'field_size'							=> $this->_field_size,
			'form_data'								=> $fields,
			'merged_content'						=> $this->_merge_content,
			'merged_field'							=> $this->_merge_field,
			'set_heading'							=> $this->_set_heading,
			'grouped_field'							=> $this->_group_field,
			'query_string'							=> $query_string
		);
		
		return $output;
	}
	
	/**
	 * render_read
	 * Render and format the output of field
	 *
	 * @access		public
	 * @param		array		$data
	 * @return		mixed
	 */
	public function render_read($data = array())
	{
		if(!$data)
		{
			return throw_exception(404, phrase('the_data_you_requested_does_not_exist_or_has_been_removed'), $this->_redirect_back);
		}
		
		$serialized									= $this->serialize($data);
		$primary_key								= array();
		$fields										= array();
		
		if($serialized)
		{
			$serialized								= $serialized[0];
			
			if(is_array($this->_view_order) && sizeof($this->_view_order) > 0)
			{
				$view_order							= array();
				
				foreach($this->_view_order as $order_key => $order_val)
				{
					if(array_key_exists($order_val, $serialized))
					{
						$view_order[]				= $order_val;
					}
				}
				
				$serialized							= array_replace(array_flip($view_order), $serialized);
			}
			else if(is_array($this->_column_order) && sizeof($this->_column_order) > 0)
			{
				$column_order						= array();
				
				foreach($this->_column_order as $order_key => $order_val)
				{
					if(array_key_exists($order_val, $serialized))
					{
						$column_order[]				= $order_val;
					}
				}
				
				$serialized							= array_replace(array_flip($column_order), $serialized);
			}
			
			foreach($serialized as $field => $params)
			{
				$type								= $params['type'];
				$primary							= $params['primary'];
				$content							= ($params['content'] ? $params['content'] : '');
				$original							= ($params['original'] ? $params['original'] : (is_numeric($params['original']) ? '0' : ''));
				$parameter							= $params['parameter'];
				$extra_params						= (isset($this->_set_field[$field]['extra_params']) ? $this->_set_field[$field]['extra_params'] : null);
				$another_params						= (isset($this->_set_field[$field]['another_params']) ? $this->_set_field[$field]['another_params'] : null);
				$hidden								= $params['hidden'];
				$alias								= (isset($this->_merge_label[$field]) ? $this->_merge_label[$field] : (isset($this->_set_alias[$field]) ? $this->_set_alias[$field] : ucwords(str_replace('_', ' ', $field))));
				$read_only							= (in_array('readonly', $type) ? ' readonly' : (in_array('disabled', $type) ? ' disabled' : null));
				$extra_class						= (isset($this->_add_class[$field]) ? ' ' . $this->_add_class[$field] : null);
				$validation							= (isset($this->_set_validation[$field]) ? explode('|', $this->_set_validation[$field]) : array());
				$required							= (in_array('required', $validation) ? 1 : 0);
				$position							= (isset($this->_field_position[$field]) ? $this->_field_position[$field] : 1);
				
				/**
				 * save primary key to be generated as token
				 */
				if($primary)
				{
					$primary_key[$field]			= $original;
				}
				
				if($hidden)
				{
					// skip field
					continue;
				}
				
				if(isset($this->_set_relation[$field]))
				{
					$content						= $this->_get_relation($this->_set_relation[$field], $content);
				}
				else if(isset($this->_set_autocomplete[$field]))
				{
					$content						= $this->_autocomplete_input($this->_set_autocomplete[$field], $original);
				}
				else if(isset($this->_merge_content[$field]))
				{
					$_calback						= array();
					$content						= $this->_merge_content[$field]['parameter'];
					
					foreach($this->_merge_content[$field]['column'] as $do => $magic)
					{
						if(isset($serialized[$magic]['content']))
						{
							$_calback[$magic]		= $serialized[$magic]['content'];
							$content				= str_replace('{' . $magic . '}', $serialized[$magic]['content'], $content);
						}
						else
						{
							$_calback[$magic]		= null;
							$content				= str_replace('{' . $magic . '}', '', $content);
						}
					}
					
					if($this->_merge_content[$field]['callback'] && method_exists($this, $this->_merge_content[$field]['callback']))
					{
						$callback					= $this->_merge_content[$field]['callback'];
						$content					= $this->$callback($_calback);
					}
				}
				else if(array_intersect(array('custom_format'), $type))
				{
					$callback						= str_replace('callback_', '', $parameter);
					
					if($callback && method_exists($this, $callback))
					{
						$content					= $this->$callback($serialized);
					}
					else
					{
						$content					= $parameter;
						preg_match_all('#\{(.*?)\}#', $content, $matches);
						$matches					= $matches[1];
						
						if($matches)
						{
							foreach($matches as $do => $magic)
							{
								if(isset($serialized[$magic]['content']))
								{
									$content		= str_replace('{' . $magic . '}', $serialized[$magic]['content'], $content);
								}
							}
						}
					}
				}
				else if(array_intersect(array('image'), $type))
				{
					$content						= '
						<div class="text-center">
							<a href="' . get_image($this->_set_upload_path, $original) . '" target="_blank">
								<img src="' . get_image($this->_set_upload_path, $original, 'thumb') . '" alt="' . $original . '" class="img-fluid rounded" />
							</a>
						</div>
					';
				}
				else if(array_intersect(array('images'), $type))
				{
					$original						= ($original ? json_decode($original, true) : array());
					$items							= null;
					
					if(is_array($original) && sizeof($original) > 0)
					{
						foreach($original as $img => $src)
						{
							$items					.= '
								<div class="col-6 col-sm-4 col-md-3 text-sm">
									<a href="' . get_image($this->_set_upload_path, $img) . '" target="_blank">
										<img src="' . get_image($this->_set_upload_path, $img, 'thumb') . '" class="img-fluid rounded" alt="' . $src . '" />
									</a>
								</div>
							';
						}
					}
					
					$content						= '
						<div class="row">
							' . $items . '
						</div>
					';
				}
				else if(array_intersect(array('file'), $type))
				{
					$original						= ($original ? json_decode($original, true) : array());
					
					if(is_array($original) && sizeof($original) > 0)
					{
						foreach($original as $src => $label)
						{
							$filesize				= get_filesize($this->_set_upload_path, $src);
							$content				= '
								<a href="' . get_file($this->_set_upload_path, $src) . '" target="_blank" data-bs-toggle="tooltip" title="' . $filesize . ', ' . phrase('click_to_open') . '">
									' . ($label ? $label : $src) . '
								</a>
							';
						}
					}
				}
				else if(array_intersect(array('files'), $type))
				{
					$images							= null;
					$files							= null;
					$original						= ($original ? json_decode($original, true) : array());
					
					if(is_array($original) && sizeof($original) > 0)
					{
						foreach($original as $src => $label)
						{
							$filesize				= get_filesize($this->_set_upload_path, $src);
							$ext					= strtolower(pathinfo($src, PATHINFO_EXTENSION));
							
							if(in_array($ext, array('jpg', 'jpeg', 'png', 'gif', 'bmp')))
							{
								$images				.= '
									<a href="' . get_image($this->_set_upload_path, $src) . '" target="_blank">
										<img src="' . get_image($this->_set_upload_path, $src, 'icon') . '" class="img-fluid rounded" alt="' . $src . '" />
									</a>
								';
							}
							else
							{
								$files				.= '
									<a href="' . get_file($this->_set_upload_path, $src) . '" target="_blank" data-bs-toggle="tooltip" title="' . $filesize . ', ' . phrase('click_to_open') . '">
										<label class="d-block">
											' . ($label ? $label : $src) . '
										</label>
									</a>
								';
							}
						}
					}
					
					$content						= '
						<div>
							' . $images . '
						</div>
						<div>
							' . $files . '
						</div>
					';
				}
				else if(array_intersect(array('attributes'), $type))
				{
					$items							= null;
					$original						= ($original ? json_decode($original, true) : array());
					
					if(is_array($original) && sizeof($original) > 0)
					{
						foreach($original as $attribute => $value)
						{
							if(!isset($value['label']) || !isset($value['value'])) continue;
							
							$items					.= '
								' . ($attribute ? '<hr class="mt-1 mb-1" />' : null) . '
								<div class="row">
									<div class="col-4 text-muted">
										' . $value['label'] . '
									</div>
									<div class="col-8">
										' . $value['value'] . '
									</div>
								</div>
							';
						}
					}
					
					$content						= $items;
				}
				else if(array_intersect(array('carousels'), $type))
				{
					$items							= null;
					$original						= ($original ? json_decode($original, true) : array());
					
					if(is_array($original) && sizeof($original) > 0)
					{
						foreach($original as $slideshow => $value)
						{
							$items					.= '
								<div class="carousel-item' . (!$slideshow ? ' active' : null) . '">
									<a href="' . base_url($value['link']) . '" target="_blank">
										<div class="d-block w-100 rounded bg-secondary" style="background:url(' . get_image('carousels', $value['background']) . ') center center no-repeat;background-size:cover;height:320px"></div>
										<div class="carousel-caption text-secondary">
											<h5 class="text-light text-shadow">
												' . truncate($value['title'], 60) . '
											</h5>
											<p class="text-light text-shadow">
												' . truncate($value['description'], 160) . '
											</p>
										</div>
									</a>
								</div>
							';
						}
					}
					
					$content						= '
						<div id="carousel_' . $field . '" class="carousel slide" data-bs-ride="carousel">
							<div class="carousel-inner">
								' . $items . '
							</div>
							<a class="carousel-control-prev" href="#carousel_' . $field . '" role="button" data-bs-slide="prev">
								<span class="carousel-control-prev-icon" aria-hidden="true"></span>
							</a>
							<a class="carousel-control-next" href="#carousel_' . $field . '" role="button" data-bs-slide="next">
								<span class="carousel-control-next-icon" aria-hidden="true"></span>
							</a>
						</div>
					';
				}
				else if(array_intersect(array('faqs'), $type))
				{
					$items							= null;
					$original						= ($original ? json_decode($original, true) : array());
					
					if(is_array($original) && sizeof($original) > 0)
					{
						foreach($original as $faq => $value)
						{
							$items					.= '
								<div class="accordion-item">
									<h2 class="accordion-header" id="heading_' . $faq . '">
										<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse_' . $faq . '" aria-controls="collapse_' . $faq . '">
											' . (isset($value['question']) ? $value['question'] : null) . '
										</button>
									</h2>
									<div id="collapse_' . $faq . '" class="accordion-collapse collapse" aria-labelledby="heading_' . $faq . '" data-bs-parent="#accordion_' . $field . '">
										<div class="accordion-body">
											' . (isset($value['answer']) ? $value['answer'] : null) . '
										</div>
									</div>
								</div>
							';
						}
					}
					
					$content						= '
						<div class="accordion" id="accordion_' . $field . '">
							' . $items . '
						</div>
					';
				}
				else if(array_intersect(array('tagsinput'), $type))
				{
					$original						= array_map('trim', explode(',', $original));
					$items							= null;
					
					if(is_array($original) && sizeof($original) > 0)
					{
						foreach($original as $tag => $value)
						{
							$items					.= '<span class="badge bg-info">' . $value . '</span>';
						}
					}
					
					$content						= $items;
				}
				else if($original && array_intersect(array('datetime', 'datetimepicker', 'current_timestamp'), $type))
				{
					if($original != '0000-00-00 00:00:00')
					{
						$timestamp					= strtotime($original);
						$month						= date('F', $timestamp);
						$month						= phrase($month);
						$content					= date('d', $timestamp) . ' ' . $month . ' ' . date('Y', $timestamp) . ', ' . date('H:i:s', $timestamp);
					}
					else
					{
						$content					= '&nbsp;';
					}
				}
				else if(array_intersect(array('date', 'datepicker'), $type))
				{
					if($original != '0000-00-00')
					{
						$timestamp					= strtotime($original);
						$month						= date('F', $timestamp);
						$month						= phrase($month);
						$content					= date('d', $timestamp) . ' ' . $month . ' ' . date('Y', $timestamp);
					}
					else
					{
						$content					= $original;
					}
				}
				else if(array_intersect(array('hour'), $type))
				{
					$content						= sprintf('%02d', (24 == $original ? '00' : $original)) . ':00';
				}
				else if(array_intersect(array('date_only'), $type))
				{
					$content						= sprintf('%02d', $original);
				}
				else if(array_intersect(array('monthpicker'), $type))
				{
					$month							= date('F', strtotime($content));
					$month							= phrase($month);
					$content						= $month . ' ' . date('Y', strtotime($content));
				}
				else if(array_intersect(array('quarterly'), $type))
				{
					$month							= date('m', strtotime($content));
					$month							= phrase($month);
					$content						= $month . ' ' . date('Y', strtotime($content));
				}
				else if(array_intersect(array('dropdown', 'checkbox', 'radio'), $type))
				{
					if(array_intersect(array('radio'), $type) && isset($parameter[$original]))
					{
						$content					= $parameter[$original];
					}
					else if(array_intersect(array('checkbox'), $type))
					{
						$json						= ($content ? json_decode($content, true) : array());
						
						if(is_array($json) && sizeof($json) > 0)
						{
							$items					= null;
							
							foreach($json as $_key => $_val)
							{
								if(!is_array($_val) && isset($this->_set_field[$field]['parameter'][$_val]))
								{
									$items				.= '<span class="badge bg-info">' . $this->_set_field[$field]['parameter'][$_val] . '</span> ';
								}
							}
							
							if($items)
							{
								$content			= $items;
							}
						}
					}
					else
					{
						$items						= ($content ? json_decode($content, true) : array());
						
						if($extra_params)
						{
							$content				= $original;
						}
						else if(isset($parameter[$original]))
						{
							$content				= $parameter[$original];
						}
						else if($items)
						{
							$content				= (is_array($items) ? sizeof($items) : 0);
							$content				= $content . ' ' . ($content > 0 ? phrase('options') : phrase('option'));
						}
					}
				}
				else if(array_intersect(array('colorpicker'), $type))
				{
					$content						= '<span class="badge" style="background:' . $original . '">' . $original . '</span>';
				}
				else if(array_intersect(array('boolean'), $type))
				{
					$content						= ($content == 1 ? '<span class="badge bg-success">' . (isset($this->_set_option_label[$field]) ? $this->_set_option_label[$field] : phrase('active')) . '</span>' : '<span class="badge bg-danger">' . phrase('inactive') . '</span>');
				}
				else if(array_intersect(array('last_insert'), $type))
				{
					if(array_intersect(array('sprintf'), $type))
					{
						$content					= str_replace('{1}', sprintf((is_string($extra_params) ? $extra_params : '%04d'), $original), $parameter);
					}
				}
				else if(array_intersect(array('textarea'), $type))
				{
					$content						= preg_replace('/\n/', '<br />', preg_replace('/(\s{4})\s+/','$1', $content));
				}
				else if(array_intersect(array('email'), $type))
				{
					$content						= '<a href="mailto:' . $content . '">' . $content . '</a>';
				}
				else if(array_intersect(array('password', 'encryption'), $type))
				{
					$content						= '******';
				}
				else if(array_intersect(array('coordinate'), $type))
				{
					$content						= '
						<div class="drawing-placeholder preloader position-relative w-100" style="overflow:hidden">
							<div id="' . $field . rand(0, 999) . '"' . ($extra_class ? ' class="' . $extra_class . '"' : null) . ' role="map" data-draggable="false" data-coordinate="' . strip_tags(htmlspecialchars(($original && is_json($original) ? $original : (get_setting('office_map') ? get_setting('office_map') : '[]')))) . '" style="height:260px"></div>
						</div>
					';
				}
				else if(array_intersect(array('point'), $type))
				{
					$content						= '
						<div class="drawing-placeholder preloader position-relative w-100" style="overflow:hidden">
							<div id="' . $field . rand(0, 999) . '"' . ($extra_class ? ' class="' . $extra_class . '"' : null) . ' role="map" data-coordinate="' . strip_tags(htmlspecialchars(get_setting('office_map'))) . '" data-geojson="' . strip_tags(htmlspecialchars(($original && is_json($original) ? $original : '[]'))) . '"' . (isset($this->_set_attribute[$field]) ? ' ' . $this->_set_attribute[$field] : null) . ' style="height:260px"></div>
						</div>
					';
				}
				else if(array_intersect(array('polygon'), $type))
				{
					$content						= '
						<div class="drawing-placeholder preloader position-relative w-100" style="overflow:hidden">
							<div id="' . $field . rand(0, 999) . '"' . ($extra_class ? ' class="' . $extra_class . '"' : null) . ' role="map" data-coordinate="' . strip_tags(htmlspecialchars(get_setting('office_map'))) . '" data-geojson="' . strip_tags(htmlspecialchars(($original && is_json($original) ? $original : '[]'))) . '"' . (isset($this->_set_attribute[$field]) ? ' ' . $this->_set_attribute[$field] : null) . ' style="height:260px"></div>
						</div>
					';
				}
				else if(array_intersect(array('linestring'), $type))
				{
					$content						= '
						<div class="drawing-placeholder preloader position-relative w-100" style="overflow:hidden">
							<div id="' . $field . rand(0, 999) . '"' . ($extra_class ? ' class="' . $extra_class . '"' : null) . ' role="map" data-coordinate="' . strip_tags(htmlspecialchars(get_setting('office_map'))) . '" data-geojson="' . strip_tags(htmlspecialchars(($original && is_json($original) ? $original : '[]'))) . '"' . (isset($this->_set_attribute[$field]) ? ' ' . $this->_set_attribute[$field] : null) . ' style="height:260px"></div>
						</div>
					';
				}
				else
				{
					$json							= ($content ? json_decode($content, true) : array());
					
					if(json_last_error() === JSON_ERROR_NONE && is_array($json) && sizeof($json) > 0)
					{
						$content					= $this->_humanize_array($json);
					}
				}
				
				if(array_intersect(array('hyperlink'), $type))
				{
					if(isset($extra_params) && is_array($extra_params))
					{
						$hyperlink_params			= array_search('hyperlink', $type);
						$skip						= false;
						$uri						= array('per_page' => null, 'order' => null, 'column' => null, 'sort' => null, 'q' => null);
						
						if(isset($extra_params[$hyperlink_params]) && is_array($extra_params[$hyperlink_params]))
						{
							foreach($extra_params[$hyperlink_params] as $url_key => $url_val)
							{
								$uri[$url_key]		= (isset($serialized[$url_val]['original']) ? $serialized[$url_val]['original'] : $url_val);
							}
						}
						else
						{
							foreach($extra_params as $url_key => $url_val)
							{
								$uri[$url_key]		= (isset($serialized[$url_val]['original']) ? $serialized[$url_val]['original'] : $url_val);
							}
						}
						
						if(is_array($another_params))
						{
							foreach($another_params as $key_except => $val_except)
							{
								if(isset($serialized[$key_except]['original']) && $serialized[$key_except]['original'] != $val_except)
								{
									$skip			= true;
								}
							}
							$another_params			= null;
						}
						
						if(!$skip)
						{
							$external				= false;
							
							$path					= (isset($this->_set_field[$field]['parameter']) && is_array($this->_set_field[$field]['parameter']) && sizeof($this->_set_field[$field]['parameter']) > 1 ? $this->_set_field[$field]['parameter'][$hyperlink_params] : $this->_set_field[$field]['parameter']);
							
							$link					= (isset($this->_set_field[$field]['parameter']) && $this->_set_field[$field]['parameter'] ? base_url($path, $uri) : $original);
							
							if(strpos($path, '://') !== false)
							{
								$external			= true;
								$link				= str_replace(base_url($path), $path, $link);
							}
							
							$content				= '
								<a href="' . $link . '"' . ('_blank' == $another_params || $external ? ' target="_blank"' : ' class="' . ($another_params || $external ? $another_params : '--xhr') . '"') . ' style="display:block">
									<b data-bs-toggle="tooltip" title="' . phrase('click_to_open') . '">
										<i class="mdi mdi-open-in-new"></i>' . $content . '
									</b>
								</a>
							';
						}
					}
					else
					{
						$content					= '
							<a href="' . $this->_set_field[$field]['parameter'] . '" class="--xhr" style="display:block">
								<b>
									<i class="mdi mdi-open-in-new"></i>' . $content . '
								</b>
							</a>
						';
					}
				}
				
				$fields[$field]						= array
				(
					'required'						=> $required,
					'type'							=> $type,
					'label'							=> (!isset($this->_set_option_label[$field]) || strtolower($alias) != strtolower($this->_set_option_label[$field]) ? $alias : ''),
					'content'						=> $content,
					'original'						=> $original,
					'position'						=> $position
				);
				
				if($this->_api_request)
				{
					unset($fields[$field]['required'], $fields[$field]['content'], $fields[$field]['original'], $fields[$field]['position']);
					
					$fields[$field]['value']		= $original;
				}
			}
		}
		
		$column_size								= 6;
		$column_offset								= 3;
		$highest_column								= 1;
		
		if(is_array($this->_field_position) && sizeof($this->_field_position) > 0)
		{
			$highest_column							= max($this->_field_position);
		}
		
		$query_string								= array_replace(service('request')->getGet(), $primary_key);
		
		if($this->_api_request)
		{
			unset($query_string['aksara'], $query_string['limit']);
		}
		
		$output										= array
		(
			'column_size'							=> $this->_column_size,
			'column_total'							=> $highest_column,
			'field_size'							=> $this->_field_size,
			'form_data'								=> $fields,
			'merged_content'						=> $this->_merge_content,
			'merged_field'							=> $this->_merge_field,
			'set_heading'							=> $this->_set_heading,
			'query_string'							=> $query_string
		);
		
		return $output;
	}
	
	/**
	 * render_table
	 * Render and format the output of field
	 *
	 * @access		public
	 * @param		array		$data
	 * @return		mixed
	 */
	public function render_table($data = array())
	{
		if(!$this->_set_primary)
		{
			$this->_unset_action					= array_merge($this->_unset_action, array('create', 'update', 'delete'));
			
			if(!$this->template)
			{
				$this->template						= new Template($this->_set_theme, $this->_api_request);
			}
			
			if('backend' != $this->template->get_theme_property('type'))
			{
				return $data;
			}
		}
		
		$serialized									= $this->serialize($data);
		$output										= array();
		$query_string								= array();
		
		if($serialized)
		{
			$column_lib								= array();
			
			foreach($serialized as $key => $val)
			{
				if(is_array($this->_column_order) && sizeof($this->_column_order) > 0)
				{
					$column_order					= array();
					
					foreach($this->_column_order as $order_key => $order_val)
					{
						if(array_key_exists($order_val, $val))
						{
							$column_order[]			= $order_val;
						}
					}
					
					$val							= array_replace(array_flip($column_order), $val);
				}
				
				$primary_key						= array();
				$fields								= array();
				
				foreach($val as $field => $params)
				{
					$type							= $params['type'];
					$primary						= $params['primary'];
					$content						= ($params['content'] ? $params['content'] : '');
					$original						= ($params['original'] ? $params['original'] : (is_numeric($params['original']) ? '0' : ''));
					$parameter						= $params['parameter'];
					$extra_params					= (isset($this->_set_field[$field]['extra_params']) ? $this->_set_field[$field]['extra_params'] : null);
					$another_params					= (isset($this->_set_field[$field]['another_params']) ? $this->_set_field[$field]['another_params'] : null);
					$hidden							= $params['hidden'];
					
					/**
					 * save primary key to be generated as token
					 */
					if($primary)
					{
						$primary_key[$field]		= $original;
					}
					
					if($hidden && !in_array($field, $this->_parameter))
					{
						// skip field
						continue;
					}
					
					if(isset($this->_set_relation[$field]))
					{
						$content					= $this->_get_relation($this->_set_relation[$field], $original);
					}
					else if(isset($this->_set_autocomplete[$field]))
					{
						$content					= $this->_autocomplete_input($this->_set_autocomplete[$field], $original);
					}
					else if(isset($this->_merge_content[$field]))
					{
						$_calback					= array();
						$content					= $this->_merge_content[$field]['parameter'];
						
						foreach($this->_merge_content[$field]['column'] as $do => $magic)
						{
							if(isset($val[$magic]['content']))
							{
								$_calback[$magic]	= $val[$magic]['content'];
								$content			= str_replace('{' . $magic . '}', $val[$magic]['content'], $content);
							}
							else
							{
								$_calback[$magic]	= null;
								$content			= str_replace('{' . $magic . '}', '', $content);
							}
						}
						
						if($this->_merge_content[$field]['callback'] && method_exists($this, $this->_merge_content[$field]['callback']))
						{
							$callback				= $this->_merge_content[$field]['callback'];
							$content				= $this->$callback($_calback);
						}
					}
					else if(array_intersect(array('custom_format'), $type))
					{
						$callback					= str_replace('callback_', '', $parameter);
					
						if($callback && method_exists($this, $callback))
						{
							$content				= $this->$callback($val);
						}
						else
						{
							$content				= $parameter;
							preg_match_all('#\{(.*?)\}#', $content, $matches);
							$matches				= $matches[1];
							
							if($matches)
							{
								foreach($matches as $do => $magic)
								{
									if(isset($val[$magic]['content']))
									{
										$content	= str_replace('{' . $magic . '}', $val[$magic]['content'], $content);
									}
								}
							}
						}
					}
					else if(array_intersect(array('image'), $type))
					{
						$content					= '
							<a href="' . get_image($this->_set_upload_path, $original) . '" target="_blank">
								<img src="' . ($original ? get_image($this->_set_upload_path, $original, 'icon') : ($parameter ? str_replace('/thumbs/', '/icons/', $parameter) : get_image($this->_set_upload_path, 'placeholder.png', 'icon'))) . '" alt="' . ($original ? $original : ($parameter ? str_replace('/thumbs/', '/icons/', $parameter) : 'placeholder.png')) . '" class="img-fluid rounded" style="height: 22px" />
							</a>
						';
					}
					else if(array_intersect(array('file'), $type))
					{
						$original					= ($original ? json_decode($original, true) : new \stdClass());
						
						if($original)
						{
							foreach($original as $src => $label)
							{
								$filesize			= get_filesize($this->_set_upload_path, $src);
								$content			= '
									<a href="' . get_file($this->_set_upload_path, $src) . '" target="_blank" data-bs-toggle="tooltip" title="' . $filesize . ', ' . phrase('click_to_open') . '">
										<b>
											' . truncate($label, 10) . '
										</b>
									</a>
								';
							}
						}
					}
					else if(array_intersect(array('images', 'files'), $type))
					{
						$content					= sizeof(($content && is_array(json_decode($content, true)) ? json_decode($content, true) : array()));
						
						if($content > 1)
						{
							$content				= '<span class="badge bg-info">' . $content . ' ' . (array_intersect(array('images'), $type) ? phrase('images') : phrase('files')) . '</span>';
						}
						else
						{
							$content				= '<span class="badge bg-info">' . $content . ' ' . (array_intersect(array('image'), $type) ? phrase('images') : phrase('file')) . '</span>';
						}
					}
					else if(array_intersect(array('attributes'), $type))
					{
						$content					= sizeof(($content && is_array(json_decode($content, true)) ? json_decode($content, true) : array()));
						$content					= ($content > 0 ? '<span class="badge bg-secondary">' . $content . ' ' . ($content > 1 ? phrase('attributes') : phrase('attribute')) . '</span>' : '<span class="badge bg-warning">' . phrase('not_set') . '</span>');
					}
					else if(array_intersect(array('carousels', 'faqs'), $type))
					{
						$content					= sizeof(($content && is_array(json_decode($content, true)) ? json_decode($content, true) : array()));
						$content					= ($content > 0 ? '<span class="badge bg-secondary">' . $content . ' ' . ($content > 1 ? phrase('items') : phrase('item')) . '</span>' : '<span class="badge bg-warning">' . phrase('not_set') . '</span>');
					}
					else if($original && array_intersect(array('datetime', 'datetimepicker', 'current_timestamp'), $type))
					{
						if($original != '0000-00-00 00:00:00')
						{
							$timestamp				= strtotime($original);
							$month					= date('F', $timestamp);
							$month					= phrase($month);
							$content				= '<span data-bs-toggle="tooltip" title="' . date('d', $timestamp) . ' ' . $month . ' ' . date('Y - H:i:s', $timestamp) . '">' . date('d', $timestamp) . ' ' . $month . ' ' . date('Y', $timestamp) . '</span>';
						}
						else
						{
							$content				= '&nbsp;';
						}
					}
					else if(array_intersect(array('date', 'datepicker'), $type))
					{
						if($original != '0000-00-00')
						{
							$timestamp				= strtotime($original);
							$month					= date('F', $timestamp);
							$month					= phrase($month);
							$content				= date('d', $timestamp) . ' ' . $month . ' ' . date('Y', $timestamp);
						}
						else
						{
							$content				= $original;
						}
					}
					else if(array_intersect(array('hour'), $type))
					{
						$content					= sprintf('%02d', (24 == $original ? '00' : $original)) . ':00';
					}
					else if(array_intersect(array('date_only'), $type))
					{
						$content					= sprintf('%02d', $original);
					}
					else if(array_intersect(array('monthpicker'), $type))
					{
						$month						= date('F', strtotime($content));
						$month						= phrase($month);
						$content					= $month . ' ' . date('Y', strtotime($content));
					}
					else if(array_intersect(array('quarterly'), $type))
					{
						$month						= date('m', strtotime($content));
						$month						= ($month);
						
						if($month == 1)
						{
							$content				= phrase('quarter') . ' 1';
						}
						else if($month == 4)
						{
							$content				= phrase('quarter') . ' 2';
						}
						else if($month == 7)
						{
							$content				= phrase('quarter') . ' 3';
						}
						else if($month == 10)
						{
							$content				= phrase('quarter') . ' 4';
						}
						else
						{
							$content				= phrase('not_a_valid_quarter');
						}
					}
					else if(array_intersect(array('dropdown', 'checkbox', 'radio'), $type))
					{
						if(array_intersect(array('radio'), $type) && isset($parameter[$original]))
						{
							$content				= $parameter[$original];
						}
						else if(array_intersect(array('checkbox'), $type))
						{
							$json					= ($content ? json_decode($content, true) : array());
							
							if(is_array($json) && sizeof($json) > 0)
							{
								$items				= null;
								
								foreach($json as $_key => $_val)
								{
									if(!is_array($_val) && isset($this->_set_field[$field]['parameter'][$_val]))
									{
										$items		.= '<span class="badge bg-info">' . $this->_set_field[$field]['parameter'][$_val] . '</span> ';
									}
								}
								
								if($items)
								{
									$content		= $items;
								}
							}
						}
						else
						{
							$items					= ($content ? json_decode($content, true) : array());
							
							if($extra_params)
							{
								$content			= $original;
							}
							else if(isset($parameter[$original]))
							{
								$content			= $parameter[$original];
							}
							else if($items)
							{
								$content			= (is_array($items) ? sizeof($items) : 0);
								$content			= $content . ' ' . ($content > 0 ? phrase('options') : phrase('option'));
							}
						}
					}
					else if(array_intersect(array('colorpicker'), $type))
					{
						$content					= '<span class="badge" style="background:' . $original . '">' . $original . '</span>';
					}
					else if(array_intersect(array('boolean'), $type))
					{
						$content					= ($content == 1 ? '<span class="badge bg-success">' . phrase('active') . '</span>' : '<span class="badge bg-danger">' . phrase('inactive') . '</span>');
					}
					else if(array_intersect(array('last_insert'), $type))
					{
						if(array_intersect(array('sprintf'), $type))
						{
							$content				= str_replace('{1}', sprintf((is_string($extra_params) ? $extra_params : '%04d'), $original), $parameter);
						}
					}
					else if(array_intersect(array('textarea', 'wisywig'), $type))
					{
						$content					= preg_replace('/\n/', ' ', preg_replace('/(\s{4})\s+/','$1', ($content ? (!in_array($field, $this->_unset_truncate) && in_array($this->_method, array('index')) ? truncate($content, 60) : $content) : '')));
					}
					else if(array_intersect(array('email'), $type))
					{
						$content					= '<a href="mailto:' . $original . '">' . $content . '</a>';
					}
					else if(array_intersect(array('password', 'encryption'), $type))
					{
						$content					= '******';
					}
					else
					{
						if(!isset($this->_merge_content[$field]))
						{
							$content				= (!in_array($field, $this->_unset_truncate) && in_array($this->_method, array('index')) ? truncate($content, 60) : $content);
						}
						
						if(array_intersect(array('to_slug'), $type))
						{
							$content				= $original;
						}
					}
					
					if(array_intersect(array('int', 'integer', 'numeric', 'number_format', 'price_format', 'percent_format'), $type) && !array_intersect(array('text'), $type))
					{
						$decimal					= 0;
						
						if(array_intersect(array('numeric', 'price_format', 'percent_format'), $type) && is_numeric($content))
						{
							$decimal				= (floor($content) != $content ? strlen(substr(strrchr(rtrim($content, 0), '.'), 1)) : 0);
						}
						
						$content					= '<p class="text-md-end m-0" style="padding-right:15px">' . (array_intersect(array('int', 'integer'), $type) ? $content : (is_numeric($content) ? (float) number_format($content, $decimal) : $content)) . '</p>';
					}
					
					if(array_intersect(array('hyperlink'), $type))
					{
						if(isset($extra_params) && is_array($extra_params))
						{
							$hyperlink_params		= array_search('hyperlink', $type);
							$skip					= false;
							$uri					= array('per_page' => null, 'order' => null, 'column' => null, 'sort' => null, 'q' => null);
							
							if(isset($extra_params[$hyperlink_params]) && is_array($extra_params[$hyperlink_params]))
							{
								foreach($extra_params[$hyperlink_params] as $url_key => $url_val)
								{
									$uri[$url_key]	= (isset($val[$url_val]['original']) ? $val[$url_val]['original'] : $url_val);
								}
							}
							else
							{
								foreach($extra_params as $url_key => $url_val)
								{
									$uri[$url_key]	= (isset($val[$url_val]['original']) ? $val[$url_val]['original'] : $url_val);
								}
							}
							
							if(is_array($another_params))
							{
								foreach($another_params as $key_except => $val_except)
								{
									if(isset($val[$key_except]['original']) && $val[$key_except]['original'] != $val_except)
									{
										$skip		= true;
									}
								}
								$another_params		= null;
							}
							
							if(!$skip)
							{
								$external			= false;
								
								$path				= (isset($this->_set_field[$field]['parameter']) && is_array($this->_set_field[$field]['parameter']) && sizeof($this->_set_field[$field]['parameter']) > 1 ? $this->_set_field[$field]['parameter'][$hyperlink_params] : $this->_set_field[$field]['parameter']);
								
								$link				= (isset($this->_set_field[$field]['parameter']) && $this->_set_field[$field]['parameter'] ? base_url($path, $uri) : $original);
								
								if(strpos($path, '://') !== false)
								{
									$external		= true;
									$link			= str_replace(base_url($path), $path, $link);
								}
								
								$content			= '
									<a href="' . $link . '"' . ('_blank' == $another_params || $external ? ' target="_blank"' : ' class="' . ($another_params || $external ? $another_params : '--xhr') . '"') . ' style="display:block">
										<b data-bs-toggle="tooltip" title="' . phrase('click_to_open') . '">
											<i class="mdi mdi-open-in-new"></i>' . $content . '
										</b>
									</a>
								';
							}
						}
						else
						{
							$content				= '
								<a href="' . $this->_set_field[$field]['parameter'] . '" class="--xhr" style="display:block">
									<b>
										<i class="mdi mdi-open-in-new"></i>' . $content . '
									</b>
								</a>
							';
						}
					}
					
					/**
					 * Prepare format the field parameter
					 */
					$fields[$field]					= array
					(
						'label'						=> (isset($this->_merge_label[$field]) ? $this->_merge_label[$field] : (isset($this->_set_alias[$field]) ? $this->_set_alias[$field] : ucwords(str_replace('_', ' ', $field)))),
						'primary'					=> ($primary ? true : false),
						'hidden'					=> $hidden,
						'content'					=> $content,
						'original'					=> $original
					);
					
					if($this->_grid_view || service('request')->getUserAgent()->isMobile())
					{
						$fields[$field]['type']		= $type;
					}
					
					if($this->_grid_view && $this->_grid_view['hyperlink'] && ($this->_grid_view['hyperlink'] && (stripos($this->_grid_view['hyperlink'], 'http://') === false || stripos($this->_grid_view['hyperlink'], 'http://') === false)) && $this->_grid_view['parameter'] && !isset($this->_grid_view['url'][$key]))
					{
						$grid_query					= array();
						$u							= 'url';
						
						foreach($this->_grid_view['parameter'] as $_key => $_val)
						{
							$grid_query[$_key]		= (isset($val[$_val]['original']) ? $val[$_val]['original'] : $_val);
						}
						
						$this->_grid_view[$u][$key]	= base_url($this->_grid_view['hyperlink'], $grid_query);
					}
					
					if($this->_api_request)
					{
						unset($fields[$field]['content'], $fields[$field]['original']);
						
						$fields[$field]['type']		= $type;
						$fields[$field]['value']	= $original;
					}
					
					/**
					 * save primary key to be generated as token
					 */
					if($primary)
					{
						$primary_key[$field]		= $original;
					}
				}
				
				$output[]							= $fields;
				
				$uri_parameter						= service('request')->getGet();
				
				unset($uri_parameter['aksara']);
				
				if($primary_key)
				{
					$uri_parameter					= array_merge(array('aksara' => generate_token(array_filter(array_merge($uri_parameter, $primary_key)))), $uri_parameter, $primary_key);
				}
				
				if($this->_api_request)
				{
					unset($uri_parameter['aksara'], $uri_parameter['limit']);
				}
				
				$query_string[]						= $uri_parameter;
			}
		}
		
		$columns									= array();
		$search_columns								= $this->model->list_fields($this->_table);
		
		if(!$search_columns)
		{
			$search_columns							= array();
		}
		
		if($this->_select)
		{
			$search_columns							= array_merge($this->_select, $search_columns);
		}
		
		if($search_columns)
		{
			$qs										= service('request')->getGet();
			
			unset($qs['aksara']);
			
			foreach($search_columns as $key => $val)
			{
				if($val && stripos($val, '.') !== false)
				{
					$val							= str_replace('.', '', strstr($val, '.'));
				}
				
				if($val && stripos(trim($val), ' AS ') !== false)
				{
					$explode						= preg_split('/ AS /i', $val);
					
					if(is_array($explode) && isset($explode[1]))
					{
						$val						= $explode[1];
					}
				}
				
				if(!in_array($val, $this->_unset_column))
				{
					if(strpos($val, ' ') !== false) continue;
					
					$columns[$val]					= array
					(
						'field'						=> $val,
						'label'						=> (isset($this->_merge_label[$val]) ? $this->_merge_label[$val] : (isset($this->_set_alias[$val]) ? $this->_set_alias[$val] : ucwords(str_replace('_', ' ', $val)))),
						'aksara'					=> generate_token(($qs ? array_merge($qs, array('order' => $val, 'sort' => get_userdata('sortOrder'))) : array('order' => $val, 'sort' => get_userdata('sortOrder')))),
						'sort'						=> get_userdata('sortOrder'),
						'align'						=> (isset($this->_set_field[$val]['field_type']) && array_intersect(array('int', 'integer', 'numeric', 'number_format', 'price_format', 'percent_format'), $this->_set_field[$val]['field_type']) ? 'right' : null)
					);
				}
			}
		}
		
		if(is_array($this->_column_order) && sizeof($this->_column_order) > 0)
		{
			$column_order							= array();
			
			foreach($this->_column_order as $order_key => $order_val)
			{
				if(array_key_exists($order_val, $columns))
				{
					$column_order[]					= $order_val;
				}
			}
			
			$columns								= array_replace(array_flip($column_order), $columns);
			
			$column_order							= array();
		}
		else
		{
			$column_order							= $this->model->list_fields($this->_table);
		}
		
		foreach($column_order as $key => $val)
		{
			if(in_array($val, $this->_unset_column))
			{
				unset($column_order[$key]);
			}
		}
		
		$option										= array();
		$dropdown									= array();
		
		foreach($output as $key => $val)
		{
			if($this->_extra_option)
			{
				foreach($this->_extra_option as $_key => $_val)
				{
					if(!is_array($_val['parameter'])) continue;
					
					$parameter						= array();
					
					foreach($_val['parameter'] as $__key => $__val)
					{
						if(isset($val[$__val]['original']))
						{
							$__val					= $val[$__val]['original'];
						}
						
						$parameter[$__key]			= $__val;
					}
					
					if($parameter)
					{
						$parameter					= array_merge(array('aksara' => generate_token(array_filter($parameter))), $parameter);
					}
					
					$_val['parameter']				= $parameter;
					
					$option[$key][$_key]			= $_val;
				}
			}
			
			if($this->_extra_dropdown)
			{
				foreach($this->_extra_dropdown as $_key => $_val)
				{
					if(!is_array($_val['parameter'])) continue;
					
					$parameter						= array();
					
					foreach($_val['parameter'] as $__key => $__val)
					{
						if(isset($val[$__val]['original']))
						{
							$__val					= $val[$__val]['original'];
						}
						
						$parameter[$__key]			= $__val;
					}
					
					if($parameter)
					{
						$parameter					= array_merge(array('aksara' => generate_token(array_filter($parameter))), $parameter);
					}
					
					$_val['parameter']				= $parameter;
					
					$dropdown[$key][$_key]			= $_val;
				}
			}
			
			// sort by column order
			$output[$key]							= ($column_order ? array_replace(array_flip($column_order), array_merge($columns, $val)) : array_merge($columns, $val));
		}
		
		// pairs columns with column order
		$columns									= ($column_order ? array_replace(array_flip($column_order), $columns) : $columns);
		
		foreach($columns as $key => $val)
		{
			if(!is_array($val))
			{
				// unset non-array values (meant the column is unset)
				unset($columns[$key]);
			}
		}
		
		$output										= array
		(
			'unset_action'							=> $this->_unset_action,
			'unset_read'							=> $this->_unset_read,
			'unset_update'							=> $this->_unset_update,
			'unset_delete'							=> $this->_unset_delete,
			'filter'								=> ($this->_add_filter ? $this->_add_filter : ''),
			'merged_content'						=> $this->_merge_content,
			'table_data'							=> $output,
			'item_reference'						=> $this->_item_reference,
			'query_string'							=> $query_string,
			'extra_action'							=> array
			(
				'toolbar'							=> $this->_extra_toolbar,
				'option'							=> $option,
				'dropdown'							=> $dropdown
			),
			'columns'								=> $columns
		);
		
		if($this->_grid_view)
		{
			$output['grid']							= $this->_grid_view;
			$output['grid']['path']					= $this->_set_upload_path;
		}
		
		return $output;
	}
	
	/**
	 * serialize
	 * Serialize the field that rendered from the database table and
	 * add extra attributes on it
	 *
	 * @access		public
	 * @param		array		$data
	 * @param		bool		$partial
	 * @return		bool
	 */
	public function serialize($data = array(), $partial = false)
	{
		if(!$data && $this->model->table_exists($this->_table))
		{
			$this->_data							= false;
			$data									= array(array_flip($this->model->list_fields($this->_table)));
		}
		
		$field_data									= array();
		
		if(is_array($this->_field_data) && sizeof($this->_field_data) > 0)
		{
			foreach($this->_field_data as $key => $val)
			{
				$field_data[$val['name']]			= $val;
			}
		}
		
		if($data)
		{
			$this->_field_data						= json_decode(json_encode($this->model->field_data($this->_table)), true);
			$this->_index_data						= $this->model->index_data($this->_table);
			
			// set the default primary if the table have any primary column
			if(!$this->_set_primary && $this->_field_data)
			{
				// loops to get the primary key
				foreach($this->_field_data as $key => $val)
				{
					// check if the field has primary key
					if(isset($val['primary_key']) && $val['primary_key'])
					{
						// push primary key
						$this->_set_primary[]		= $val['name'];
					}
				}
				
				// make the array unique
				$this->_set_primary					= array_unique($this->_set_primary);
			}
			
			// set the default primary if the table have any primary column
			if(!$this->_set_primary && $this->_index_data)
			{
				// loops to get the primary key
				foreach($this->_index_data as $key => $val)
				{
					// check if the field has primary key
					if($val->type == 'PRIMARY')
					{
						// push primary key
						$this->_set_primary			= array_merge($this->_set_primary, $val->fields);
					}
				}
				
				// make the array unique
				$this->_set_primary					= array_unique($this->_set_primary);
			}
			
			$results								= array();
			
			foreach($data as $row => $array)
			{
				foreach($array as $key => $content)
				{
					$type							= (isset($this->_set_field[$key]['field_type']) ? $this->_set_field[$key]['field_type'] : (isset($field_data[$key]['type']) ? array($field_data[$key]['type']) : array('varchar')));
					$max_length						= (isset($field_data[$key]['max_length']) ? $field_data[$key]['max_length'] : null);
					$parameter						= (isset($this->_set_field[$key]['parameter']) ? $this->_set_field[$key]['parameter'] : null);
					$validation						= (isset($this->_set_validation[$key]) ? $this->_set_validation[$key] : null);
					$hidden							= false;
					$content						= ($this->_data ? (in_array($key, $this->_translate_field) ? phrase($content) : $content) : null);
					$original						= ($this->_data ? $content : null);
					
					if($this->_method == 'create')
					{
						$content					= (isset($this->_set_default[$key]) ? $this->_set_default[$key] : (isset($field_data[$key]['default']) ? $field_data[$key]['default'] : null));
						$original					= null;
					}
					
					if(in_array($this->_method, array('create', 'update')) && in_array($key, $this->_unset_field))
					{
						$hidden						= true;
					}
					else if(('read' == $this->_method || (in_array($this->_method, array('print', 'pdf')))) && in_array($key, $this->_unset_view))
					{
						$hidden						= true;
					}
					else if(in_array($this->_method, array('index', 'export', 'print', 'pdf')) && in_array($key, $this->_unset_column))
					{
						$hidden						= true;
						
						if($partial)
						{
							if(in_array($key, $this->_unset_field))
							{
								$hidden				= true;
							}
						}
					}
					
					if($content && array_intersect(array('numeric', 'number_format', 'price_format', 'percent_format'), $type) && is_numeric($content))
					{
						$decimal					= (floor($content) != $content ? strlen(substr(strrchr(rtrim($content, 0), '.'), 1)) : 0);
						
						if(array_intersect(array('percent_format'), $type))
						{
							$content				= number_format($content, $decimal) . '%';
						}
						else
						{
							$content				= number_format($content, $decimal);
						}
					}
					
					if($content && array_intersect(array('sprintf'), $type))
					{
						$content					= sprintf(($parameter && !is_array($parameter) ? $parameter : '%02d'), $content);
					}
					
					$results[$key]					= array
					(
						'type'						=> $type,
						'max_length'				=> $max_length,
						'parameter'					=> $parameter,
						'validation'				=> ($max_length ? 'max_length[' . $max_length . ']' : null) . ($max_length && $validation ? '|' : null) . ($validation ? $validation : null),
						'content'					=> $content,
						'original'					=> $original,
						'primary'					=> (in_array($key, $this->_set_primary) ? 1 : 0),
						'hidden'					=> $hidden
					);
				}
				
				$output[$row]						= $results;
			}
			
			$this->_redirect_back					= go_to(null, array_fill_keys($this->_set_primary, null));
			
			return $output;
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * ---------------------------------------------------------------
	 * Query Builder
	 * ---------------------------------------------------------------
	 */
	
	/**
	 * Select field
	 * Possible to use comma separated
	 */
	public function select(string $column, bool $escape = true)
	{
		if(!is_array($column))
		{
			// split selected by comma, but ignore that inside brackets
			$column									= array_map('trim', preg_split('/,(?![^(]+\))/', $column));
		}
		
		foreach($column as $key => $val)
		{
			// push to the select list
			$this->_select[]						= $val;
			
			// find the prefixed table select
			if(strpos($val, '.') !== false && strpos($val, '(') === false && strpos($val, ')') === false)
			{
				$val								= substr($val, strpos($val, '.') + 1);
			}
			
			// find the aliased selection
			if(stripos($val, ' AS ') !== false)
			{
				$val								= substr($val, stripos($val, ' AS ') + 4);
			}
			
			// push to the compilation
			$this->_compiled_select[]				= $val;
		}
		
		$this->_prepare(__FUNCTION__, array($column, $escape));
		
		return $this;
	}
	
	/**
	 * Select count
	 * Possible to use comma separated
	 */
	public function select_count(string $column, string $alias = null)
	{
		$this->_prepare(__FUNCTION__, array($column, $alias));
		
		return $this;
	}
	
	/**
	 * Select and Sum
	 * Possible to use comma separated
	 */
	public function select_sum(string $column, string $alias = null)
	{
		$this->_prepare(__FUNCTION__, array($column, $alias));
		
		return $this;
	}
	
	/**
	 * Select Minimum
	 * Possible to use comma separated
	 */
	public function select_min(string $column, string $alias = null)
	{
		$this->_prepare(__FUNCTION__, array($column, $alias));
		
		return $this;
	}
	
	/**
	 * Select Maximum
	 * Possible to use comma separated
	 */
	public function select_max(string $column, string $alias = null)
	{
		$this->_prepare(__FUNCTION__, array($column, $alias));
		
		return $this;
	}
	
	/**
	 * Select Average of field
	 * Possible to use comma separated
	 */
	public function select_avg(string $column, string $alias = null)
	{
		$this->_prepare(__FUNCTION__, array($column, $alias));
		
		return $this;
	}
	
	/**
	 * Distinct field
	 */
	public function distinct(bool $flag = true)
	{
		$this->_distinct							= $flag;
		
		return $this;
	}
	
	/**
	 * Set the primary table
	 */
	public function from(string $table)
	{
		$this->_table								= $table;
		
		return $this;
	}
	
	/**
	 * Set the primary table
	 * It's similar to from() method
	 */
	public function table(string $table)
	{
		$this->_table								= $table;
		
		return $this;
	}
	
	/**
	 * Join table
	 * Your contribution is needed to write hint about
	 * this method
	 */
	public function join(string $table, string $condition, string $type = '', bool $escape = true)
	{
		if(!in_array($this->_method, array('delete')))
		{
			$this->_prepare(__FUNCTION__, array($table, $condition, $type, $escape));
		}
		
		if(strpos($table, ' ') !== false)
		{
			$table									= substr($table, strrpos($table, ' ') + 1);
		}
		
		$this->_compiled_table[]					= $table;
		
		return $this;
	}
	
	/**
	 * Where
	 * Your contribution is needed to write complete hint about
	 * this method
	 */
	public function where($field = array(), $value = '', bool $escape = true)
	{
		if(is_array($field))
		{
			foreach($field as $key => $val)
			{
				$this->_prepare(__FUNCTION__, array($key, $val, $escape));
			}
		}
		else
		{
			$this->_prepare(__FUNCTION__, array($field, $value, $escape));
		}
		
		return $this;
	}
	
	/**
	 * Or Where
	 * Your contribution is needed to write complete hint about
	 * this method
	 */
	public function or_where($field = array(), $value = '', bool $escape = true)
	{
		if(is_array($field))
		{
			foreach($field as $key => $val)
			{
				$this->_prepare(__FUNCTION__, array($key, $val, $escape));
			}
		}
		else
		{
			$this->_prepare(__FUNCTION__, array($field, $value, $escape));
		}
		
		return $this;
	}
	
	/**
	 * Where In
	 * Your contribution is needed to write complete hint about
	 * this method
	 */
	public function where_in($field = array(), $value = '', bool $escape = true)
	{
		if(is_array($field))
		{
			foreach($field as $key => $val)
			{
				$this->_prepare(__FUNCTION__, array($key, $val, $escape));
			}
		}
		else
		{
			$this->_prepare(__FUNCTION__, array($field, $value, $escape));
		}
		
		return $this;
	}
	
	/**
	 * Or Where In
	 * Your contribution is needed to write complete hint about
	 * this method
	 */
	public function or_where_in($field = array(), $value = '', bool $escape = true)
	{
		if(is_array($field))
		{
			foreach($field as $key => $val)
			{
				$this->_prepare(__FUNCTION__, array($key, $val, $escape));
			}
		}
		else
		{
			$this->_prepare(__FUNCTION__, array($field, $value, $escape));
		}
		
		return $this;
	}
	
	/**
	 * Where Not In
	 * Your contribution is needed to write complete hint about
	 * this method
	 */
	public function where_not_in($field = array(), $value = '', bool $escape = true)
	{
		if(is_array($field))
		{
			foreach($field as $key => $val)
			{
				$this->_prepare(__FUNCTION__, array($key, $val, $escape));
			}
		}
		else
		{
			$this->_prepare(__FUNCTION__, array($field, $value, $escape));
		}
		
		return $this;
	}
	
	/**
	 * Or Where Not In
	 * Your contribution is needed to write complete hint about
	 * this method
	 */
	public function or_where_not_in($field = array(), $value = '', bool $escape = true)
	{
		if(is_array($field))
		{
			foreach($field as $key => $val)
			{
				$this->_prepare(__FUNCTION__, array($key, $val, $escape));
			}
		}
		else
		{
			$this->_prepare(__FUNCTION__, array($field, $value, $escape));
		}
		
		return $this;
	}
	
	/**
	 * Like
	 * Your contribution is needed to write complete hint about
	 * this method
	 */
	public function like($field = array(), $match = '', string $side = 'both', bool $escape = true, bool $case_insensitive = true)
	{
		if(is_array($field))
		{
			foreach($field as $key => $val)
			{
				$this->_like[$key]					= $val;
				
				$this->_prepare(__FUNCTION__, array($key, $val, $escape));
			}
		}
		else
		{
			$this->_like[$field]					= $match;
			
			$this->_prepare(__FUNCTION__, array($field, $match, $side, $escape, $case_insensitive));
		}
		
		return $this;
	}
	
	/**
	 * Or Like
	 * Your contribution is needed to write complete hint about
	 * this method
	 */
	public function or_like($field = array(), $match = '', string $side = 'both', bool $escape = true, bool $case_insensitive = false)
	{
		if(is_array($field))
		{
			foreach($field as $key => $val)
			{
				$this->_prepare(__FUNCTION__, array($key, $val, $escape));
			}
		}
		else
		{
			$this->_prepare(__FUNCTION__, array($field, $match, $side, $escape, $case_insensitive));
		}
		
		return $this;
	}
	
	/**
	 * Not Like
	 * Your contribution is needed to write complete hint about
	 * this method
	 */
	public function not_like($field = array(), $match = '', string $side = 'both', bool $escape = true, bool $case_insensitive = false)
	{
		if(is_array($field))
		{
			foreach($field as $key => $val)
			{
				$this->_prepare(__FUNCTION__, array($key, $val, $escape));
			}
		}
		else
		{
			$this->_prepare(__FUNCTION__, array($field, $match, $side, $escape, $case_insensitive));
		}
		
		return $this;
	}
	
	/**
	 * Or Not Like
	 * Your contribution is needed to write complete hint about
	 * this method
	 */
	public function or_not_like($field = array(), $match = '', string $side = 'both', bool $escape = true, bool $case_insensitive = false)
	{
		if(is_array($field))
		{
			foreach($field as $key => $val)
			{
				$this->_prepare(__FUNCTION__, array($key, $val, $escape));
			}
		}
		else
		{
			$this->_prepare(__FUNCTION__, array($field, $match, $side, $escape, $case_insensitive));
		}
		
		return $this;
	}
	
	/**
	 * Having
	 * Your contribution is needed to write complete hint about
	 * this method
	 */
	public function having($field = array(), $value = '', bool $escape = true)
	{
		if(is_array($field))
		{
			foreach($field as $key => $val)
			{
				$this->_prepare(__FUNCTION__, array($key, $val, $escape));
			}
		}
		else
		{
			$this->_prepare(__FUNCTION__, array($field, $value, $escape));
		}
		
		return $this;
	}
	
	/**
	 * Or Having
	 * Your contribution is needed to write complete hint about
	 * this method
	 */
	public function or_having($field = array(), $value = '', bool $escape = true)
	{
		if(is_array($field))
		{
			foreach($field as $key => $val)
			{
				$this->_prepare(__FUNCTION__, array($key, $val, $escape));
			}
		}
		else
		{
			$this->_prepare(__FUNCTION__, array($field, $value, $escape));
		}
		
		return $this;
	}
	
	/**
	 * Having In
	 * Your contribution is needed to write complete hint about
	 * this method
	 */
	public function having_in($field = array(), $value = '', bool $escape = true)
	{
		if(is_array($field))
		{
			foreach($field as $key => $val)
			{
				$this->_prepare(__FUNCTION__, array($key, $val, $escape));
			}
		}
		else
		{
			$this->_prepare(__FUNCTION__, array($field, $value, $escape));
		}
		
		return $this;
	}
	
	/**
	 * Or Having In
	 * Your contribution is needed to write complete hint about
	 * this method
	 */
	public function or_having_in($field = array(), $value = '', bool $escape = true)
	{
		if(is_array($field))
		{
			foreach($field as $key => $val)
			{
				$this->_prepare(__FUNCTION__, array($key, $val, $escape));
			}
		}
		else
		{
			$this->_prepare(__FUNCTION__, array($field, $value, $escape));
		}
		
		return $this;
	}
	
	/**
	 * Having Not In
	 * Your contribution is needed to write complete hint about
	 * this method
	 */
	public function having_not_in($field = array(), $value = '', bool $escape = true)
	{
		if(is_array($field))
		{
			foreach($field as $key => $val)
			{
				$this->_prepare(__FUNCTION__, array($key, $val, $escape));
			}
		}
		else
		{
			$this->_prepare(__FUNCTION__, array($field, $value, $escape));
		}
		
		return $this;
	}
	
	/**
	 * Or Having Not In
	 * Your contribution is needed to write complete hint about
	 * this method
	 */
	public function or_having_not_in($field = array(), $value = '', bool $escape = true)
	{
		if(is_array($field))
		{
			foreach($field as $key => $val)
			{
				$this->_prepare(__FUNCTION__, array($key, $val, $escape));
			}
		}
		else
		{
			$this->_prepare(__FUNCTION__, array($field, $value, $escape));
		}
		
		return $this;
	}
	
	/**
	 * Having Like
	 * Your contribution is needed to write complete hint about
	 * this method
	 */
	public function having_like($field = array(), $match = '', string $side = 'both', bool $escape = true, bool $case_insensitive = false)
	{
		if(is_array($field))
		{
			foreach($field as $key => $val)
			{
				$this->_prepare(__FUNCTION__, array($key, $val, $escape));
			}
		}
		else
		{
			$this->_prepare(__FUNCTION__, array($field, $match, $side, $escape, $case_insensitive));
		}
		
		return $this;
	}
	
	/**
	 * Or Having Like
	 * Your contribution is needed to write complete hint about
	 * this method
	 */
	public function or_having_like($field = array(), $match = '', string $side = 'both', bool $escape = true, bool $case_insensitive = false)
	{
		if(is_array($field))
		{
			foreach($field as $key => $val)
			{
				$this->_prepare(__FUNCTION__, array($key, $val, $escape));
			}
		}
		else
		{
			$this->_prepare(__FUNCTION__, array($field, $match, $side, $escape, $case_insensitive));
		}
		
		return $this;
	}
	
	/**
	 * Not Having Like
	 * Your contribution is needed to write complete hint about
	 * this method
	 */
	public function not_having_like($field = array(), $match = '', string $side = 'both', bool $escape = true, bool $case_insensitive = false)
	{
		if(is_array($field))
		{
			foreach($field as $key => $val)
			{
				$this->_prepare(__FUNCTION__, array($key, $val, $escape));
			}
		}
		else
		{
			$this->_prepare(__FUNCTION__, array($field, $match, $side, $escape, $case_insensitive));
		}
		
		return $this;
	}
	
	/**
	 * Or Not Having Like
	 * Your contribution is needed to write complete hint about
	 * this method
	 */
	public function or_not_having_like($field = array(), $match = '', string $side = 'both', bool $escape = true, bool $case_insensitive = false)
	{
		if(is_array($field))
		{
			foreach($field as $key => $val)
			{
				$this->_prepare(__FUNCTION__, array($key, $val, $escape));
			}
		}
		else
		{
			$this->_prepare(__FUNCTION__, array($field, $match, $side, $escape, $case_insensitive));
		}
		
		return $this;
	}
	
	/**
	 * Order By
	 * Your contribution is needed to write complete hint about
	 * this method
	 */
	public function order_by($field = array(), $direction = '', bool $escape = true)
	{
		if(!service('request')->getGet('order'))
		{
			if(is_array($field))
			{
				foreach($field as $key => $val)
				{
					$this->_prepare(__FUNCTION__, array($key, $val, $escape));
				}
			}
			else
			{
				$this->_prepare(__FUNCTION__, array($field, $direction, $escape));
			}
		}
		
		return $this;
	}
	
	/**
	 * Group By
	 * Your contribution is needed to write complete hint about
	 * this method
	 */
	public function group_by(string $column)
	{
		$this->_prepare(__FUNCTION__, array($column));
		
		return $this;
	}
	
	/**
	 * Limit
	 * Your contribution is needed to write complete hint about
	 * this method
	 */
	public function limit($limit = null, int $offset = 0)
	{
		if(in_array($this->_method, array('create', 'read', 'update', 'delete')))
		{
			$this->_limit							= 1;
			$this->_offset							= 0;
		}
		else
		{
			$this->_limit							= $limit;
			$this->_offset							= $offset;
		}
		
		$this->_prepare(__FUNCTION__, array($limit, $offset));
		
		return $this;
	}
	
	/**
	 * Offset
	 * Your contribution is needed to write complete hint about
	 * this method
	 */
	public function offset(int $offset)
	{
		if(!in_array($this->_method, array('create', 'read', 'update', 'delete')))
		{
			$this->_offset							= $offset;
		}
		
		$this->_prepare(__FUNCTION__, array($offset));
		
		return $this;
	}
	
	/**
	 * Select subqueries
	 * Possible to use comma separated
	 */
	public function subquery($subquery = null, string $alias = null)
	{
		if(!in_array($this->_method, array('create', 'update', 'delete')))
		{
			$this->_prepare(__FUNCTION__, array($subquery, $alias));
		}
		
		return $this;
	}
	
	/**
	 * Group Start
	 * Your contribution is needed to write complete hint about
	 * this method
	 */
	public function group_start()
	{
		if(!in_array($this->_method, array('create', 'update', 'delete')))
		{
			$this->_prepare(__FUNCTION__);
		}
		
		return $this;
	}
	
	/**
	 * Or Group Start
	 * Your contribution is needed to write complete hint about
	 * this method
	 */
	public function or_group_start()
	{
		if(!in_array($this->_method, array('create', 'update', 'delete')))
		{
			$this->_prepare(__FUNCTION__);
		}
		
		return $this;
	}
	
	/**
	 * Not Group Start
	 * Your contribution is needed to write complete hint about
	 * this method
	 */
	public function not_group_start()
	{
		if(!in_array($this->_method, array('create', 'update', 'delete')))
		{
			$this->_prepare(__FUNCTION__);
		}
		
		return $this;
	}
	
	/**
	 * Or Not Group Start
	 * Your contribution is needed to write complete hint about
	 * this method
	 */
	public function or_not_group_start()
	{
		if(!in_array($this->_method, array('create', 'update', 'delete')))
		{
			$this->_prepare(__FUNCTION__);
		}
		
		return $this;
	}
	
	/**
	 * Group End
	 * Your contribution is needed to write complete hint about
	 * this method
	 */
	public function group_end()
	{
		if(!in_array($this->_method, array('create', 'update', 'delete')))
		{
			$this->_prepare(__FUNCTION__);
		}
		
		return $this;
	}
	
	/**
	 * Having Group Start
	 * Your contribution is needed to write complete hint about
	 * this method
	 */
	public function having_group_start()
	{
		if(!in_array($this->_method, array('create', 'update', 'delete')))
		{
			$this->_prepare(__FUNCTION__);
		}
		
		return $this;
	}
	
	/**
	 * Or Having Group Start
	 * Your contribution is needed to write complete hint about
	 * this method
	 */
	public function or_having_group_start()
	{
		if(!in_array($this->_method, array('create', 'update', 'delete')))
		{
			$this->_prepare(__FUNCTION__);
		}
		
		return $this;
	}
	
	/**
	 * Not Having Group Start
	 * Your contribution is needed to write complete hint about
	 * this method
	 */
	public function not_having_group_start()
	{
		if(!in_array($this->_method, array('create', 'update', 'delete')))
		{
			$this->_prepare(__FUNCTION__);
		}
		
		return $this;
	}
	
	/**
	 * Or Not Having Group Start
	 * Your contribution is needed to write complete hint about
	 * this method
	 */
	public function or_not_having_group_start()
	{
		if(!in_array($this->_method, array('create', 'update', 'delete')))
		{
			$this->_prepare(__FUNCTION__);
		}
		
		return $this;
	}
	
	/**
	 * Having Group End
	 * Your contribution is needed to write complete hint about
	 * this method
	 */
	public function having_group_end()
	{
		if(!in_array($this->_method, array('create', 'update', 'delete')))
		{
			$this->_prepare(__FUNCTION__);
		}
		
		return $this;
	}
	
	/**
	 * _run_query
	 * Run the query of stored parameter
	 *
	 * @access		private
	 */
	private function _run_query($table = null, bool $recycling = false)
	{
		$query										= $this->model->table($table);
		
		/**
		 * Add distinct
		 */
		if($this->_distinct)
		{
			$query									= $this->model->distinct();
		}
		
		/**
		 * Check if the request is not recycling the previous properties
		 */
		if(!$recycling)
		{
			/**
			 * Prepare indexing the columns of table to be selected
			 */
			$columns								= $this->model->field_data($table);
			$select									= preg_filter('/^/', $table . '.', $this->model->list_fields($table));
			
			if($columns)
			{
				foreach($columns as $key => $val)
				{
					if(in_array($this->_method, array('create', 'update')) && in_array($val->name, $this->_unset_field))
					{
						if(!isset($val->primary_key) || empty($val->primary_key))
						{
							unset($select[$val->name]);
						}
					}
					else if(in_array($this->_method, array('read')) && in_array($val->name, $this->_unset_view))
					{
						if(!isset($val->primary_key) || empty($val->primary_key))
						{
							unset($select[$val->name]);
						}
					}
					else if(in_array($val->name, $this->_unset_column))
					{
						if(!isset($val->primary_key) || empty($val->primary_key))
						{
							unset($select[$val->name]);
						}
					}
				}
			}
			
			/**
			 * Merge selection
			 */
			if(!in_array($this->_method, array('create', 'update')))
			{
				$select								= ($this->_select ? array_merge($select, $this->_select) : $select);
			}
			
			/**
			 * Execute when method is not delete
			 */
			if(!in_array($this->_method, array('delete')) && is_array($select) && sizeof($select) > 0)
			{
				/**
				 * Validate the select column to check if column is exist in table
				 */
				$compiled_select					= array();
				
				foreach($select as $key => $val)
				{
					// prevent duplicate the selection
					if(in_array(str_replace($table . '.', '', $val), $this->_compiled_select)) continue;
					
					/**
					 * Check if field is already selected
					 */
					$val							= trim(preg_replace('/\s\s+/', ' ', $val));
					$alias							= (strrpos($val, ' ') !== false ? substr($val, strrpos($val, ' ') + 1) : (strpos($val, '.') !== false ? explode('.', $val) : array('anonymous', $val)));
					$alias							= (is_array($alias) && isset($alias[1]) ? $alias[1] : $alias);
					
					/**
					 * Check if selected column is use alias
					 */
					if(strpos($val, '.*') !== false && $table == strstr($val, '.*', true))
					{
						continue;
					}
					else
					{
						/**
						 * Individual table
						 */
						list($backup_table, $field)	= array_pad(explode('.', $val), 2, null);
						
						if(!$field)
						{
							$field					= $backup_table;
						}
						
						// get the name alias
						$field						= trim(($field && stripos($field, ' AS ') !== false ? substr($field, strripos($field, ' AS ') + 4) : $field));
						
						if($field && stripos($field, ' ') !== false)
						{
							$field					= substr($field, 0, strrpos($field, ' '));
						}
						
						if($backup_table != $table && $field && $this->model->field_exists($field, $backup_table))
						{
							/**
							 * Format column of select
							 */
							$val					= $backup_table . '.' . $field . ' AS ' . $field;
						}
					}
					
					/**
					 * Compile the selected field
					 */
					$compiled_select[]				= $val;
				}
				
				// check if select compiled
				if($compiled_select)
				{
					// push compiled select to prepared query builder
					$this->_prepare[]				= array
					(
						'function'					=> 'select',
						'arguments'					=> array(array_values($compiled_select))
					);
				}
				
				/**
				 * Generate join query passed from set_relation
				 */
				if(is_array($this->_join) && sizeof($this->_join) > 0)
				{
					foreach($this->_join as $table => $params)
					{
						// push join to prepared query builder
						$this->_prepare[]			= array
						(
							'function'				=> 'join',
							'arguments'				=> array($table, str_replace('{primary_table}', $this->_table, $params['condition']), $params['type'], $params['escape'])
						);
					}
				}
			}
		}
		
		/**
		 * Run generated query builder
		 */
		foreach($this->_prepare as $key => $val)
		{
			$function								= $val['function'];
			$arguments								= $val['arguments'];
			
			if($function == 'select')
			{
				// splice unnecessary select
				if(!is_array($arguments[0]))
				{
					$arguments[0]					= array_map('trim', explode(',', $arguments[0]));
				}
				
				foreach($arguments[0] as $_key => $_val)
				{
					if(strpos($_val, '(') === false && strpos($_val, ')') === false && strpos($_val, '.') !== false)
					{
						$_val						= substr($_val . '.', 0, strpos($_val, '.'));
						
						if(!in_array($_val, $this->_compiled_table))
						{
							// field doesn't exists on compiled table
							unset($arguments[0][$_key]);
						}
					}
				}
			}
			else if($function == 'where' && strpos($arguments[0], '.') === false && strpos($arguments[0], ' ') === false && strpos($arguments[0], '(') === false && strpos($arguments[0], ')') === false)
			{
				// add table prefix to field
				$arguments[0]						= $this->_table . '.' . $arguments[0];
			}
			else if($function == 'order_by' && in_array($this->_method, array('create', 'read', 'update', 'delete')))
			{
				// prevent order on CRUD
				continue;
			}
			
			if(is_array($arguments) && sizeof($arguments) == 7)
			{
				$query								= $this->model->$function($arguments[0], $arguments[1], $arguments[2], $arguments[3], $arguments[4], $arguments[5], $arguments[6]);
			}
			else if(is_array($arguments) && sizeof($arguments) == 6)
			{
				$query								= $this->model->$function($arguments[0], $arguments[1], $arguments[2], $arguments[3], $arguments[4], $arguments[5]);
			}
			else if(is_array($arguments) && sizeof($arguments) == 5)
			{
				$query								= $this->model->$function($arguments[0], $arguments[1], $arguments[2], $arguments[3], $arguments[4]);
			}
			else if(is_array($arguments) && sizeof($arguments) == 4)
			{
				$query								= $this->model->$function($arguments[0], $arguments[1], $arguments[2], $arguments[3]);
			}
			else if(is_array($arguments) && sizeof($arguments) == 3)
			{
				$query								= $this->model->$function($arguments[0], $arguments[1], $arguments[2]);
			}
			else if(is_array($arguments) && sizeof($arguments) == 2)
			{
				$query								= $this->model->$function($arguments[0], $arguments[1]);
			}
			else
			{
				$query								= $this->model->$function((isset($arguments[0]) ? $arguments[0] : $arguments));
			}
		}
		
		return $query;
	}
	
	/**
	 * _fetch
	 * Fetch the data from collected query
	 *
	 * @access		private
	 */
	private function _fetch($table = null)
	{
		/**
		 * Debugging
		 */
		if(isset($this->_debugging))
		{
			if(in_array($this->_debugging, array('params', 'parameter')))
			{
				dd($this->_prepare);
			}
			
			$query									= $this->_run_query($table)->limit($this->_limit, $this->_offset)->result();
			
			if($this->_debugging == 'query')
			{
				// return as last executed query
				exit(nl2br($this->model->last_query()));
			}
			else
			{
				// return the result of query
				dd($query);
			}
		}
		
		$output										= array
		(
			'results'								=> $this->_run_query($table)->limit($this->_limit, $this->_offset)->result(),
			'total'									=> $this->_run_query($table, true)->count_all_results()
		);
		
		$this->_prepare								= array();
		
		return $output;
	}
	
	/**
	 * validate_form
	 * Validate the data that submitted through form
	 *
	 * @access		public
	 */
	public function validate_form($data = array())
	{
		// unset previous upload data
		unset_userdata('_upload_data');
		
		// check if app on demo mode
		if($this->_restrict_on_demo)
		{
			return throw_exception(403, phrase('this_feature_is_disabled_in_demo_mode'), (!$this->_api_request ? $this->_redirect_back : null));
		}
		
		if('update' == $this->_method && !$this->_where && !$this->_insert_on_update_fail)
		{
			return throw_exception(404, phrase('the_data_you_want_to_update_was_not_found'), (!$this->_api_request ? $this->_redirect_back : null));
		}
		
		// serialize the fields
		$serialized									= $this->serialize($data);
		
		if(service('request')->getPost() && is_array($serialized) && sizeof($serialized) > 0)
		{
			$validation								= false;
			
			foreach($serialized[0] as $key => $val)
			{
				$type								= $val['type'];
				
				// skip field when it's disabled and has no default value
				if(in_array($key, $this->_unset_field) || isset($this->_set_default[$key]) || array_intersect(array('to_slug', 'current_timestamp'), $type)) continue;
				
				if(array_intersect(array('image'), $type))
				{
					$validation						= true;
					
					$this->form_validation->setRule($key, (isset($this->_set_alias[$key]) ? $this->_set_alias[$key] : ucwords(str_replace('_', ' ', $key))), 'validate_upload[' . $key . '.image]');
				}
				else if(array_intersect(array('images'), $type))
				{
					$validation						= true;
					
					$this->form_validation->setRule($key . '.*', (isset($this->_set_alias[$key]) ? $this->_set_alias[$key] : ucwords(str_replace('_', ' ', $key))), 'validate_upload[' . $key . '.image]');
				}
				else if(array_intersect(array('file', 'files'), $type))
				{
					$validation						= true;
					
					$this->form_validation->setRule($key . '.*', (isset($this->_set_alias[$key]) ? $this->_set_alias[$key] : ucwords(str_replace('_', ' ', $key))), 'validate_upload[' . $key . ']');
				}
				else if(array_intersect(array('carousels'), $type))
				{
					$validation						= true;
					
					$this->form_validation->setRule($key . '.background.*', (isset($this->_set_alias[$key]) ? $this->_set_alias[$key] : ucwords(str_replace('_', ' ', $key))), 'validate_upload[' . $key . '.image]');
					$this->form_validation->setRule($key . '.thumbnail.*', (isset($this->_set_alias[$key]) ? $this->_set_alias[$key] : ucwords(str_replace('_', ' ', $key))), 'validate_upload[' . $key . '.image]');
				}
				else if(array_intersect(array('faqs'), $type))
				{
					$validation						= true;
					
					$this->form_validation->setRule($key . '.question.*', phrase('question') . ' ' . (isset($this->_set_alias[$key]) ? $this->_set_alias[$key] : ucwords(str_replace('_', ' ', $key))), (service('request')->getPost($key) ? 'trim|' : null) . 'required');
					$this->form_validation->setRule($key . '.answer.*', phrase('answer') . ' ' . (isset($this->_set_alias[$key]) ? $this->_set_alias[$key] : ucwords(str_replace('_', ' ', $key))), (service('request')->getPost($key) ? 'trim|' : null) . 'required');
				}
				else if(array_intersect(array('password'), $type))
				{
					$validation						= true;
					
					if(service('request')->getPost($key))
					{
						$this->form_validation->setRule($key, (isset($this->_set_alias[$key]) ? $this->_set_alias[$key] : ucwords(str_replace('_', ' ', $key))), 'min_length[6]');
						$this->form_validation->setRule($key . '_confirmation', phrase('confirmation') . ' ' . (isset($this->_set_alias[$key]) ? $this->_set_alias[$key] : ucwords(str_replace('_', ' ', $key))), ('create' == $this->_method ? 'required|matches[' . $key . ']' : 'matches[' . $key . ']'));
					}
				}
				else if(array_intersect(array('encryption'), $type) && $val['validation'])
				{
					$validation						= $val['validation'];
					
					if('update' == $this->_method)
					{
						$validation					= str_replace(array(' ', '|required', 'required|', 'required'), '', trim($validation));
					}
					
					if($validation)
					{
						$this->form_validation->setRule($key, (isset($this->_set_alias[$key]) ? $this->_set_alias[$key] : ucwords(str_replace('_', ' ', $key))), $validation);
					}
				}
				else if(isset($this->_set_relation[$key]))
				{
					$validation						= ($val['validation'] ? explode('|', $val['validation']) : array());
					
					if(in_array('required', $validation))
					{
						$this->form_validation->setRule($key, (isset($this->_set_alias[$key]) ? $this->_set_alias[$key] : ucwords(str_replace('_', ' ', $key))), 'required|relation_checker[' . $this->_set_relation[$key]['relation_table'] . '.' . $this->_set_relation[$key]['relation_key'] . ']');
					}
					else
					{
						// find foreign data
						$constrained				= false;
						$foreign_data				= $this->model->foreign_data($this->_table);
						
						if($foreign_data)
						{
							foreach($foreign_data as $_key => $_val)
							{
								if($this->_set_relation[$key]['relation_table'] == $_val->foreign_table_name)
								{
									$constrained	= true;
								}
							}
						}
						
						if($constrained)
						{
							$this->form_validation->setRule($key, (isset($this->_set_alias[$key]) ? $this->_set_alias[$key] : ucwords(str_replace('_', ' ', $key))), 'required|relation_checker[' . $this->_set_relation[$key]['relation_table'] . '.' . $this->_set_relation[$key]['relation_key'] . ']');
						}
					}
				}
				else
				{
					$validation_suffix				= null;
					
					if(array_intersect(array('yearpicker'), $type))
					{
						$validation_suffix			= '|valid_year';
					}
					else if(array_intersect(array('hour'), $type))
					{
						$validation_suffix			= '|numeric|max_length[2]';
					}
					else if(array_intersect(array('date_only'), $type))
					{
						$validation_suffix			= '|numeric|max_length[2]';
					}
					else if(array_intersect(array('date', 'datepicker'), $type))
					{
						$validation_suffix			= '|valid_date';
					}
					else if(array_intersect(array('timestamp', 'datetime', 'datetimepicker'), $type))
					{
						$validation_suffix			= '|valid_datetime';
					}
					
					if($val['validation'] && !isset($this->_set_default[$key]))
					{
						$validation					= true;
						
						if(is_array(service('request')->getPost($key)))
						{
							$this->form_validation->setRule($key . '.*', (isset($this->_set_alias[$key]) ? $this->_set_alias[$key] : ucwords(str_replace('_', ' ', $key))), (service('request')->getPost($key) || ($val['validation'] && strpos($val['validation'], 'callback_') !== false) ? 'trim|' : null) . $val['validation']);
						}
						else
						{
							$this->form_validation->setRule($key, (isset($this->_set_alias[$key]) ? $this->_set_alias[$key] : ucwords(str_replace('_', ' ', $key))), (service('request')->getPost($key) || ($val['validation'] && strpos($val['validation'], 'callback_') !== false) ? 'trim|' : null) . $val['validation'] . $validation_suffix);
						}
					}
					else if($validation_suffix)
					{
						$this->form_validation->setRule($key, (isset($this->_set_alias[$key]) ? $this->_set_alias[$key] : ucwords(str_replace('_', ' ', $key))), (service('request')->getPost($key) ? 'trim' : null) . $validation_suffix);
					}
				}
			}
			
			if($validation && $this->form_validation->run(service('request')->getPost()) === false)
			{
				// unlink the files
				$this->_unlink_files();
				
				return throw_exception(400, $this->form_validation->getErrors());
			}
			else
			{
				$prepare							= array();
				$batch_data							= array();
				$this->_upload_data					= (get_userdata('_upload_data') ? get_userdata('_upload_data') : array());
				
				foreach($serialized[0] as $field => $value)
				{
					$type							= $value['type'];
					
					// skip field when it's disabled and has no default value
					if((in_array($field, $this->_unset_field) && !isset($this->_set_default[$field]) && !array_intersect(array('to_slug', 'current_timestamp'), $type)) || (in_array('disabled', $type) && !isset($this->_set_default[$field]))) continue;
					
					if(array_key_exists($field, service('request')->getPost()) || array_intersect($type, array('current_timestamp', 'image', 'images', 'file', 'files', 'to_slug', 'current_user', 'carousels', 'faqs')))
					{
						if(array_intersect(array('password'), $type))
						{
							// check if password changed
							if(service('request')->getPost($field))
							{
								// store new password
								$prepare[$field]	= password_hash(service('request')->getPost($field) . ENCRYPTION_KEY, PASSWORD_DEFAULT);
							}
						}
						else if(array_intersect(array('encryption'), $type))
						{
							// check if value changed
							if(service('request')->getPost($field))
							{
								// store new encryption
								$prepare[$field]	= base64_encode(service('encrypter')->encrypt(service('request')->getPost($field)));
							}
						}
						else if(array_intersect(array('image'), $type))
						{
							// set the default value
							$source					= 'placeholder.png';
							
							// push to data preparation
							$prepare[$field]		= $source;
							
							// check if the uploaded file is valid
							if(isset($this->_upload_data[$field]) && is_array($this->_upload_data[$field]))
							{
								// loop to get source from unknown array key
								foreach($this->_upload_data[$field] as $key => $src)
								{
									// set new source
									$source			= $src;
								}
								
								// push to data preparation
								$prepare[$field]	= $source;
							}
							else
							{
								// check if the method is not create
								if('create' != $this->_method)
								{
									// unset the field for update preparation
									unset($prepare[$field]);
								}
							}
						}
						else if(array_intersect(array('images', 'file', 'files'), $type))
						{
							$files					= array();
							
							if(is_array(service('request')->getPost($field . '_label')))
							{
								// reverse file attributes to match with newest upload data
								$files				= array_reverse(service('request')->getPost($field . '_label'));
								$uploaded			= (isset($this->_upload_data[$field]) ? array_reverse(array_values($this->_upload_data[$field])) : array());
								
								// combine uploaded files to the old one
								$uploaded			= array_combine(array_intersect_key($uploaded, $files), array_intersect_key($files, $uploaded));
								
								if($uploaded)
								{
									// merge files
									$files			= array_merge($uploaded, array_slice($files, sizeof($uploaded)));
								}
							}
							
							// push the json encoded file to data preparation
							$prepare[$field]		= json_encode(array_reverse($files));
						}
						else if(array_intersect(array('carousels'), $type))
						{
							// get the submitted carousels
							$carousels				= service('request')->getPost($field);
							
							// check if submitted data is not supported
							if(!$carousels || !isset($carousels['title']) || sizeof($carousels['title']) <= 0)
							{
								// continue the loops to other fields
								continue;
							}
							
							// set the default value of variables and shorts the key to match ruler indent
							$i						= array();
							$b						= 'background';
							$t						= 'thumbnail';
							
							// loop the submitted carousels data
							foreach($carousels['title'] as $key => $val)
							{
								// push the carousel collection
								$i[$key]			= array
								(
									'title'			=> $val,
									'description'	=> (isset($carousels['description'][$key]) ? $carousels['description'][$key] : ''),
									'link'			=> (isset($carousels['link'][$key]) ? $carousels['link'][$key] : ''),
									'label'			=> (isset($carousels['label'][$key]) ? $carousels['label'][$key] : '')
								);
								
								// check if the carousel has uploaded background
								if(isset($this->_upload_data[$field][$b][$key]) && $this->_upload_data[$field][$b][$key])
								{
									// pair with newer uploaded background
									$i[$key][$b]	= $this->_upload_data[$field][$b][$key];
								}
								else
								{
									// use default background instead
									$i[$key][$b]	= (isset($carousels['default_background'][$key]) ? $carousels['default_background'][$key] : '');
								}
								
								// check if carousel has uploaded thumbnail
								if(isset($this->_upload_data[$field][$t][$key]) && $this->_upload_data[$field][$t][$key])
								{
									// pair with newer uploaded background
									$i[$key][$t]	= $this->_upload_data[$field][$t][$key];
								}
								else
								{
									// use default thumbnail instead
									$i[$key][$t]	= (isset($carousels['default_thumbnail'][$key]) ? $carousels['default_thumbnail'][$key] : '');
								}
							}
							
							// push the json encoded to data preparation
							$prepare[$field]		= json_encode($i);
						}
						else if(array_intersect(array('faqs'), $type))
						{
							// get the submitted faqs
							$faqs					= service('request')->getPost($field);
							$items					= array();
							
							// check if the faqs has correct value
							if(isset($faqs['question']) && sizeof($faqs['question']) > 0)
							{
								// loops the submitted faqs
								foreach($faqs['question'] as $key => $val)
								{
									// collects the faqs
									$items[]		= array
									(
										'question'	=> $val,
										'answer'	=> (isset($faqs['answer'][$key]) ? $faqs['answer'][$key] : null)
									);
								}
							}
							
							// push the json encoded faqs to data preparation
							$prepare[$field]		= json_encode(json_fixer($items));
						}
						else if(array_intersect(array('attributes'), $type))
						{
							// get the submitted attributes
							$attributes				= service('request')->getPost($field);
							$items					= array();
							
							// check if the submitted attributes is in correct format
							if($attributes && isset($attributes['label']) && sizeof($attributes['label']) > 0)
							{
								// loops the submitted attributes
								foreach($attributes['label'] as $key => $val)
								{
									// collect the attributes
									$items[]		= array
									(
										'label'		=> $val,
										'value'		=> (isset($attributes['value'][$key]) ? $attributes['value'][$key] : null)
									);
								}
							}
							
							// push the json encoded attributes to data preparation
							$prepare[$field]		= json_encode(json_fixer($items));
						}
						else if(array_intersect(array('wysiwyg'), $type))
						{
							// sanitize the wysiwyg from the XSS attack
							$value					= service('request')->getPost($field);
							
							if($value)
							{
								$value				= str_ireplace(array('<?php', '?>'), array('&lt;?php', '?&gt;'), $value);
								$value				= str_ireplace(array('<script', '</script>'), array('&lt;script', '&lt;/script&gt;'), $value);
								$value				= str_ireplace(array('<noscript', '</noscript>'), array('&lt;noscript', '&lt;/noscript&gt;'), $value);
								$value				= str_ireplace(array('<style', '</style>'), array('&lt;style', '&lt;/style&gt;'), $value);
								$value				= str_ireplace('<link', '&lt;link', $value);
							}
							
							// push the boolean field type to data preparation
							$prepare[$field]		= $value;
						}
						else if(array_intersect(array('boolean'), $type))
						{
							// push the boolean field type to data preparation
							$prepare[$field]		= service('request')->getPost($field);
						}
						else if(array_intersect(array('current_timestamp'), $type))
						{
							// push the current timestamp field type to data preparation
							$prepare[$field]		= date('Y-m-d H:i:s');
						}
						else if(array_intersect(array('date', 'datepicker'), $type))
						{
							// push the date field type to data preparation
							$prepare[$field]		= date('Y-m-d', strtotime(service('request')->getPost($field)));
						}
						else if(array_intersect(array('datetime', 'datetimepicker'), $type))
						{
							// push the submitted timestamp field type to data preparation
							$prepare[$field]		= date('Y-m-d H:i:s', strtotime(service('request')->getPost($field)));
						}
						else if(array_intersect(array('monthpicker'), $type))
						{
							// push the month field type to data preparation
							$prepare[$field]		= get_userdata('year') . '-' . service('request')->getPost($field) . '-01';
						}
						else if(array_intersect(array('price_format'), $type))
						{
							// push the price field type to data preparation
							$value					= trim(service('request')->getPost($field));
							$value					= str_replace(',', '', $value);
							$prepare[$field]		= $value;
						}
						else if(array_intersect(array('number_format'), $type))
						{
							// push the number format field type to data preparation
							$value					= trim(service('request')->getPost($field));
							$value					= str_replace(',', '', $value);
							$prepare[$field]		= $value;
						}
						else if(array_intersect(array('to_slug'), $type))
						{
							// check if slug has its own post data
							if(service('request')->getPost($field))
							{
								// use its own data as slug
								$title				= service('request')->getPost($field);
							}
							else if(service('request')->getPost($value['parameter']))
							{
								// or match other field from given parameter
								$title				= service('request')->getPost($value['parameter']);
							}
							else
							{
								// otherwise, use the time instead
								$title				= time();
							}
							
							// push the slug to the data preparation
							$prepare[$field]		= format_slug($title);
						}
						else if(array_intersect(array('current_user'), $type))
						{
							// push current user id to the data preparation
							$prepare[$field]		= get_userdata('user_id');
						}
						else
						{
							// convert the submitted array data as encoded json, or use the original
							$prepare[$field]		= (is_array(service('request')->getPost($field)) ? json_encode(service('request')->getPost($field)) : service('request')->getPost($field));
						}
						
						// apply the formatter when not match any given parameter
						if(!array_intersect(array('to_slug', 'password', 'encryption', 'image', 'images', 'file', 'files'), $type))
						{
							// use empty value instead of NULL when no data is submitted
							if(!isset($prepare[$field]))
							{
								$prepare[$field]	= '';
							}
						}
					}
					
					// check if the field is sets to use the default value
					if(isset($this->_set_default[$field]) && ($this->_set_default[$field] || is_numeric($this->_set_default[$field])))
					{
						// push the default value to the data preparation
						$prepare[$field]			= $this->_set_default[$field];
					}
					
					// or when it's a boolean and no value
					else if(array_intersect(array('boolean'), $type) && !service('request')->getPost($field) && !in_array($field, $this->_unset_field))
					{
						// sets to "0" instead of null
						$prepare[$field]			= 0;
					}
				}
				
				// if data preparation is ready and the method is create
				if($prepare && in_array('create', array($this->_method, $this->_set_method)))
				{
					// insert new data
					$this->insert_data($this->_table, $prepare);
				}
				
				// if data preparation is ready and the method is update
				else if($prepare && in_array('update', array($this->_method, $this->_set_method)))
				{
					// update the old data
					$this->update_data($this->_table, $prepare, $this->_where);
				}
				
				// otherwise
				else
				{
					// unlink the files
					$this->_unlink_files();
					
					// throw the exception messages
					return throw_exception(403, phrase('the_method_you_requested_is_not_acceptable') . ' (' . service('request')->getServer('REQUEST_METHOD'). ')', (!$this->_api_request ? $this->_redirect_back : null));
				}
			}
		}
		else
		{
			// no data are found
			return throw_exception(404, phrase('no_data_can_be_executed'), (!$this->_api_request ? $this->_redirect_back : null));
		}
	}
	
	/**
	 * Unlink the uploaded data if error persist
	 */
	private function _unlink_files($upload_data = array())
	{
		if(!$upload_data)
		{
			$upload_data							= (get_userdata('_upload_data') ? get_userdata('_upload_data') : array());
		}
		
		if($upload_data)
		{
			foreach($upload_data as $key => $val)
			{
				if(is_array($val))
				{
					foreach($val as $file => $src)
					{
						if('placeholder.png' == $src) continue;
						
						if(is_file(UPLOAD_PATH . '/' . $this->_set_upload_path . '/' . $src))
						{
							try
							{
								unlink(UPLOAD_PATH . '/' . $this->_set_upload_path . '/' . $src);
							}
							catch(\Throwable $e)
							{
								// some exception
							}
						}
						
						if(is_file(UPLOAD_PATH . '/' . $this->_set_upload_path . '/thumbs/' . $src))
						{
							try
							{
								unlink(UPLOAD_PATH . '/' . $this->_set_upload_path . '/thumbs/' . $src);
							}
							catch(\Throwable $e)
							{
								// some exception
							}
						}
						
						if(is_file(UPLOAD_PATH . '/' . $this->_set_upload_path . '/icons/' . $src))
						{
							try
							{
								unlink(UPLOAD_PATH . '/' . $this->_set_upload_path . '/icons/' . $src);
							}
							catch(\Throwable $e)
							{
								// some exception
							}
						}
					}
				}
				else if(is_file(UPLOAD_PATH . '/' . $this->_set_upload_path . '/' . $val) && 'placeholder.png' != $val)
				{
					try
					{
						if(is_file(UPLOAD_PATH . '/' . $this->_set_upload_path . '/' . $src))
						{
							try
							{
								unlink(UPLOAD_PATH . '/' . $this->_set_upload_path . '/' . $src);
							}
							catch(\Throwable $e)
							{
								// some exception
							}
						}
						
						if(is_file(UPLOAD_PATH . '/' . $this->_set_upload_path . '/thumbs/' . $src))
						{
							try
							{
								unlink(UPLOAD_PATH . '/' . $this->_set_upload_path . '/thumbs/' . $src);
							}
							catch(\Throwable $e)
							{
								// some exception
							}
						}
						
						if(is_file(UPLOAD_PATH . '/' . $this->_set_upload_path . '/icons/' . $src))
						{
							try
							{
								unlink(UPLOAD_PATH . '/' . $this->_set_upload_path . '/icons/' . $src);
							}
							catch(\Throwable $e)
							{
								// some exception
							}
						}
					}
					catch(\Throwable $e)
					{
						continue;
					}
				}
			}
		}
	}
	
	/**
	 * _handshake
	 * Make a handshake between device and server
	 */
	private function _handshake($api_key = 0)
	{
		service('request')->setHeader('X-Requested-With', 'XMLHttpRequest');
		
		$api_service								= $this->model->get_where
		(
			'app__rest_api',
			array
			(
				'status'							=> 1,
				'api_key'							=> $api_key,
				'valid_until >= '					=> date('Y-m-d')
			),
			1
		)
		->row();
		
		if(!$api_service && (service('request')->getIPAddress() == (isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : null)))
		{
			// set temporary API service
			$api_service							= (object) array
			(
				'ip_range'							=> (isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : null),
				'method'							=> json_encode(array(service('request')->getServer('REQUEST_METHOD'))),
				'status'							=> 1
			);
		}
		
		if(!$api_service)
		{
			return throw_exception(403, phrase('your_api_key_is_not_eligible_to_access_the_requested_module_or_already_expired'));
		}
		else if(!$api_service->status)
		{
			return throw_exception(403, phrase('the_api_service_you_requested_is_temporary_deactivated'));
		}
		else if(!in_array(service('request')->getServer('REQUEST_METHOD'), json_decode($api_service->method, true)))
		{
			return throw_exception(403, phrase('your_api_key_is_not_eligible_to_using_the_requested_method') . ': ' . service('request')->getServer('REQUEST_METHOD'));
		}
		else if($api_service->ip_range && (service('request')->getIPAddress() != (isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : null) || ($api_service->ip_range && !$this->_ip_in_range($api_service->ip_range))))
		{
			return throw_exception(403, phrase('this_source_is_not_accessible_from_your_device'));
		}
		
		/**
		 * retrieve the temporary session
		 */
		if(service('request')->getHeaderLine('X-ACCESS-TOKEN'))
		{
			if(session_status() === PHP_SESSION_NONE)
			{
				session_start();
			}
			
			$cookie									= $this->model->select
			('
				data
			')
			->get_where
			(
				'app__sessions',
				array
				(
					'id'							=> service('request')->getHeaderLine('X-ACCESS-TOKEN'),
					'timestamp >= '					=> date('Y-m-d H:i:s', (time() - service('request')->config->sessionExpiration))
				),
				1
			)
			->row('data');
			
			if($cookie && session_decode($cookie))
			{
				// set the cookie to session
				set_userdata(array_filter($_SESSION));
				
				// set the user language session
				$this->_set_language(get_userdata('language_id'));
			}
			else
			{
				// cookie not found
				return throw_exception(403, phrase('the_access_token_is_invalid_or_already_expired'));
			}
		}
		
		$this->_api_request							= true;
		
		return $this;
	}
	
	/**
	 * _ip_in_range
	 * check the IP if it's being blacklisted or not
	 */
	private function _ip_in_range($whitelist = array())
	{
		if($whitelist && !is_array($whitelist))
		{
			$whitelist								= array_map('trim', explode(',', $whitelist));
		}
		
		if(in_array(service('request')->getServer('REMOTE_ADDR'), $whitelist))
		{
			return true;
		}
		else
		{
			foreach($whitelist as $key => $val)
			{
				$wildcardPos						= strpos($val, '*');
				
				if($wildcardPos !== false && substr(service('request')->getServer('REMOTE_ADDR'), 0, $wildcardPos) . '*' == $val)
				{
					return true;
				}
			}
		}

		return false;
	}
	
	/**
	 * _push_log
	 * Save the record of visitor to the log
	 *
	 * @access		private
	 */
	private function _push_log()
	{
		if(service('request')->getUserAgent()->isBrowser())
		{
			$user_agent								= service('request')->getUserAgent()->getBrowser() . ' ' . service('request')->getUserAgent()->getVersion();
		}
		else if(service('request')->getUserAgent()->isRobot())
		{
			$user_agent								= service('request')->getUserAgent()->getRobot();
		}
		else if(service('request')->getUserAgent()->isMobile())
		{
			$user_agent								= service('request')->getUserAgent()->getMobile();
		}
		else
		{
			return false;
		}
		
		$prepare									= array
		(
			'ip_address'							=> (service('request')->hasHeader('x-forwarded-for') ? service('request')->getHeaderLine('x-forwarded-for') : service('request')->getIPAddress()),
			'browser'								=> $user_agent,
			'platform'								=> service('request')->getUserAgent()->getPlatform(),
			'timestamp'								=> date('Y-m-d H:i:s')
		);
		
		if(in_array($this->_db_driver, array('Postgre', 'SQLSRV')))
		{
			$this->model->where('CAST(timestamp AS DATE)', date('Y-m-d'));
		}
		else
		{
			$this->model->where('DATE(timestamp)', date('Y-m-d'));
		}
		
		$query										= $this->model->get_where
		(
			'app__visitor_logs',
			array
			(
				'ip_address'						=> $prepare['ip_address']
			),
			1
		)
		->row();
		
		if(!$query)
		{
			$this->model->insert('app__visitor_logs', $prepare);
		}
	}
	
	/**
	 * Set the language based on user browser if matched with
	 * available translation within app. Otherwise, use the
	 * user session or fallback to the system default language
	 */
	private function _set_language($language_id = '')
	{
		if(!get_userdata('language_id') || !$language_id)
		{
			$locale									= explode(',', (service('request')->getServer('HTTP_ACCEPT_LANGUAGE') ? service('request')->getServer('HTTP_ACCEPT_LANGUAGE') : 'en-us'));
			
			foreach($locale as $key => $val)
			{
				if($key)
				{
					$this->model->or_like('locale', $val, 'both', true, true);
				}
				else
				{
					$this->model->like('locale', $val, 'both', true, true);
				}
			}
			
			$language_id							= $this->model->select
			('
				id
			')
			->get_where
			(
				'app__languages',
				array
				(
					'status'						=> 1
				),
				1
			)
			->row('id');
			
			$language_id							= ($language_id ? $language_id : (get_setting('app_language') > 0 ? get_setting('app_language') : 1));
			
			set_userdata('language_id', $language_id);
		}
		
		$language_code								= $this->model->select
		('
			code
		')
		->get_where
		(
			'app__languages',
			array
			(
				'id'								=> $language_id
			),
			1
		)
		->row('code');
		
		$this->_language							= $language_code;
		
		// set default language
		if(is_dir(APPPATH . 'Language' . DIRECTORY_SEPARATOR . $language_code))
		{
			set_userdata('language', $language_code);
			
			service('language')->setLocale($language_code);
		}
	}
	
	/**
	 * Prepare the given parameter as the query builder queue
	 */
	private function _prepare(string $function, array $arguments = array())
	{
		if('where' == $function)
		{
			$this->_where[$arguments[0]]			= $arguments[1];
		}
		
		$this->_prepare[]							= array
		(
			'function'								=> $function,
			'arguments'								=> $arguments
		);
	}
}
