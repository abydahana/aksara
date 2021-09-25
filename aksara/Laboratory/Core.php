<?php namespace Aksara\Laboratory;
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
 * @author			Aby Dahana
 * @profile			abydahana.github.io
 * @website			www.aksaracms.com
 * @since			version 4.0.0
 * @copyright		(c) 2020-2021 Aksara Laboratory
 * -------------------------------------------------------------------
 * Sometimes you need a fool to complete a complicated job :)
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
	protected $_api_request_parameter				= array();
	
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
	private $_set_theme								= 'default';
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
	
	private $_compiled_table						= array();
	private $_compiled_where						= array();
	private $_compiled_or_where						= array();
	private $_compiled_where_in						= array();
	private $_compiled_or_where_in					= array();
	private $_compiled_where_not_in					= array();
	private $_compiled_or_where_not_in				= array();
	private $_compiled_like							= array();
	private $_compiled_or_like						= array();
	private $_compiled_not_like						= array();
	private $_compiled_or_not_like					= array();
	private $_compiled_having						= array();
	private $_compiled_or_having					= array();
	
	private $_crud;
	private $_distinct;
	private $_select								= array();
	private $_select_avg							= array();
	private $_select_max							= array();
	private $_select_min							= array();
	private $_select_sum							= array();
	private $_from;
	private $_table;
	private $_table_alias							= array();
	private $_join									= array();
	private $_where									= array();
	private $_or_where								= array();
	private $_where_in								= array();
	private $_or_where_in							= array();
	private $_where_not_in							= array();
	private $_or_where_not_in						= array();
	private $_like									= array();
	private $_or_like								= array();
	private $_not_like								= array();
	private $_or_not_like							= array();
	private $_having								= array();
	private $_or_having								= array();
	private $_having_in								= array();
	private $_or_having_in							= array();
	private $_having_not_in							= array();
	private $_or_having_not_in						= array();
	private $_having_like							= array();
	private $_or_having_like						= array();
	private $_not_having_like						= array();
	private $_or_not_having_like					= array();
	private $_group_start;
	private $_group_end;
	private $_or_group_start;
	private $_not_group_start;
	private $_or_not_group_start;
	private $_group_by;
	private $_order_by								= array();
	private $_order_by_bm							= array();
	private $_limit									= 25;
	private $_offset								= 0;
	private $_set									= array();
	private $_replace								= array();
	
	private $_total									= 0;
	private $_insert_on_update_fail					= false;
	
	public $_method									= null;
	public $_insert_id								= 0;
	
	public function __construct()
	{
		// start benchmarking
		service('timer')->start('elapsed_time');
		
		// check if accessed from IE browser
		if(strtolower(service('request')->getUserAgent()->getBrowser()) == 'internet explorer' && service('request')->getUserAgent()->getVersion() < 11)
		{
			/* throw compatibility mode */
			die('The ' . service('request')->getUserAgent()->getBrowser() . ' ' . service('request')->getUserAgent()->getVersion() . ' is no longer supported...');
		}
		
		helper(array('url', 'file', 'theme', 'security', 'main', 'string'));
		
		// unset previous data
		unset_userdata('_upload_data');
		
		$this->model								= new Model();
		$this->permission							= new Permission();
		$this->form_validation						= \Config\Services::validation();
		
		$path										= (isset(service('router')->getMatchedRoute()[0]) ? service('router')->getMatchedRoute()[0] : null);
		
		$this->_method								= service('router')->methodName();
		$this->_module								= ($this->_method && strpos($path, $this->_method) !== false ? preg_replace('/\/' . $this->_method . '$/', null, $path) : $path);
		
		if(service('request')->getGet('limit'))
		{
			// set limit from query string parameter
			$this->_limit							= service('request')->getGet('limit');
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
		 * Token checker
		 */
		if(service('request')->getGet())
		{
			$token									= service('request')->getGet('aksara');
			$query_string							= service('request')->getGet();
			
			unset($query_string['aksara']);
			
			// validate token
			if($this->_set_permission && $query_string && $token != generate_token($query_string))
			{
				// token is missmatch, throw an exception
				return throw_exception(403, phrase('the_token_you_submitted_has_been_expired_or_you_are_trying_to_bypass_it_from_the_restricted_source'), base_url());
			}
		}
		
		/**
		 * Preview theme
		 */
		if('preview-theme' == service('request')->getGet('aksara_mode') && sha1(service('request')->getGet('aksara_theme') . ENCRYPTION_KEY . get_userdata('session_generated')) == service('request')->getGet('integrity_check') && is_dir(ROOTPATH . 'themes/' . service('request')->getGet('aksara_theme')))
		{
			$this->_set_theme						= service('request')->getGet('aksara_theme');
		}
		
		/**
		 * Working with REST API
		 */
		if(service('request')->hasHeader('X-API-KEY'))
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
		
		//print_r(service('router')->getMatchedRoute());exit;
	}
	
	/**
	 * This function will remapping the request based on method
	 * and its parameter. It's also can be used to change the
	 * method to get the uri segment based on requested class.
	 */
	public function _remap($method = null, $segment_1 = null, $segment_2 = null, $segment_3 = null, $segment_4 = null, $segment_5 = null, $segment_6 = null)
	{
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
	 * restrict_on_demo
	 * Function to apply demo mode
	 *
	 * @access		public
	 */
	public function restrict_on_demo()
	{
		if(DEMO_MODE)
		{
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
	public function database_config($driver = array(), $hostname = null, $port = null, $username = null, $password = null, $database = null)
	{
		if(is_array($driver) && isset($driver['driver']) && isset($driver['hostname']) && isset($driver['port']) && isset($driver['username']) && isset($driver['password']) && isset($driver['database']))
		{
			$this->model->database_config($driver['driver'], $driver['hostname'], $driver['port'], $driver['username'], $driver['password'], $driver['database']);
		}
		else
		{
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
		if($token == sha1(current_page() . ENCRYPTION_KEY . get_userdata('session_generated')) || $this->_api_request)
		{
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
	public function parent_module($module = null)
	{
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
	public function set_permission($permissive_user = array(), $redirect = null)
	{
		// this mean the permission is set as true
		$this->_set_permission						= true;
		
		// check if permissive user is set
		if($permissive_user && !is_array($permissive_user))
		{
			// safe check for array
			$permissive_user						= array_map('trim', explode(',', $permissive_user));
		}
		
		if($this->_set_permission && !get_userdata('is_logged') && !$this->_api_request)
		{
			// user isn't signed in
			return throw_exception(301, phrase('your_session_has_been_expired'), base_url(), true);
		}
		elseif(!$this->permission->allow($this->_module, $this->_method, get_userdata('user_id')))
		{
			// user been signed in but blocked by group privilege
			return throw_exception(403, phrase('you_do_not_have_a_sufficient_privileges_to_access_the_requested_page'), ($redirect ? $redirect : $this->_redirect_back));
		}
		elseif($permissive_user && !in_array(get_userdata('group_id'), $permissive_user))
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
	public function set_method($method = 'index')
	{
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
	public function set_theme($theme = 'frontend')
	{
		if(!in_array($theme, array('frontend', 'backend'))) return false;
		
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
	public function set_template($params = array(), $value = null)
	{
		if(!is_array($params))
		{
			$params									= array
			(
				$params								=> $value
			);
		}
		
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
	public function set_breadcrumb($params = array(), $value = null)
	{
		if(!is_array($params))
		{
			$this->_set_breadcrumb[$params]			= array
			(
				'label'								=> $value,
				'translated'						=> true
			);
		}
		else
		{
			foreach($params as $key => $val)
			{
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
	 * title in the browser
	 *
	 * @access		public
	 * @return		string
	 */
	public function set_title($magic_string = null, $placeholder = null)
	{
		if(is_array($magic_string))
		{
			foreach($magic_string as $key => $val)
			{
				$this->_set_title[$key]				= $val;
			}
		}
		else
		{
			$this->_set_title						= $magic_string;
		}
		
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
		$this->_set_icon							= $icon;
		
		return $this;
	}
	
	/**
	 * set_description
	 * Set the description of module. It also will displayed as
	 * description of meta
	 *
	 * @access		public
	 * @return		array
	 */
	public function set_description($params = array(), $value = 'index')
	{
		if(is_array($params))
		{
			$this->_set_description					= array_merge($this->_set_description, $params);
		}
		else
		{
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
	public function set_messages($params = array(), $code = null, $messages = null)
	{
		if(!is_array($params))
		{
			$placement								= $params;
			$params									= array();
			$params[$placement]						= array
			(
				'code'								=> $code,
				'messages'							=> $messages
			);
		}
		
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
	public function set_button($button = null, $value = null, $label = null, $icon = null, $class = null, $target = null)
	{
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
	 * add_filter
	 * Add individual filter to CRUD index table
	 *
	 * @access		public
	 * @return		string
	 */
	public function add_filter($filter = null)
	{
		$this->_add_filter							= $filter;
		
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
	public function add_action($placement = 'option', $url = null, $label = null, $class = null, $icon = null, $parameter = array(), $new_tab = false)
	{
		$query_string								= service('request')->getGet();
		
		if($parameter)
		{
			$parameter								= array_merge(array('aksara' => generate_token(array_filter(array_merge($query_string, $parameter)))), $query_string, $parameter);
		}
		
		if('toolbar' == $placement)
		{
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
		elseif('dropdown' == $placement)
		{
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
		elseif('submit' == $placement)
		{
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
		else
		{
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
		if(!is_array($params))
		{
			// shorthand possibility, separate with commas
			$params									= array_map('trim', explode(',', $params));
		}
		
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
	public function add_class($params = array(), $value = null)
	{
		if(!is_array($params))
		{
			// shorthand possibility, separate with commas
			$params									= array
			(
				$params								=> $value
			);
		}
		
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
		if(!is_array($type))
		{
			$type									= array_map('trim', explode(',', $type));
		}
		
		if(!is_array($params))
		{
			$params									= array
			(
				$params								=> $type
			);
		}
		
		foreach($params as $key => $val)
		{
			if(!is_array($val))
			{
				$val								= array_map('trim', explode(',', $val));
			}
			
			if(isset($this->_set_field[$key]))
			{
				foreach($val as $f_key => $f_val)
				{
					if(isset($this->_set_field[$key]['field_type']))
					{
						if(is_array($this->_set_field[$key]['field_type']))
						{
							$this->_set_field[$key]['field_type'][]		= $f_val;
						}
					}
					
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
		
		return $this;
	}
	
	/**
	 * set_tooltip
	 * Add the tooltip on field label when hovered
	 *
	 * @access		public
	 * @return		mixed
	 */
	public function set_tooltip($params = array(), $value = null)
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
	public function unset_read($params = array(), $value = null)
	{
		if(!is_array($params))
		{
			// shorthand possibility, separate with commas
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
	public function unset_update($params = array(), $value = null)
	{
		if(!is_array($params))
		{
			// shorthand possibility, separate with commas
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
	public function unset_delete($params = array(), $value = array())
	{
		if(!is_array($params))
		{
			// shorthand possibility, separate with commas
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
			// shorthand possibility, separate with commas
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
	public function set_validation($params = array(), $value = null)
	{
		if(!is_array($params))
		{
			// shorthand possibility, separate with commas
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
	public function set_upload_path($path = null)
	{
		if($path && preg_match('/^[a-z0-9\-\.\_\/]*$/', $path))
		{
			$this->_set_upload_path					= strtolower($path);
		}
		else
		{
			// shorthand possibility, separate with commas
			$path									= strtolower(str_replace('\\', '/', service('router')->controllerName()));
			$path									= preg_replace(array('/\/aksara\/modules\//', '/\/modules\//', '/\/controllers\//'), array(null, null, '/'), $path, 1);
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
	public function form_callback($callback = null)
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
	public function set_alias($params = array(), $value = null)
	{
		if(!is_array($params))
		{
			// shorthand possibility, separate with commas
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
	public function set_heading($params = array(), $value = null)
	{
		if(!is_array($params))
		{
			// shorthand possibility, separate with commas
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
			// shorthand possibility, separate with commas
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
	public function modal_size($size = null)
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
	public function field_position($params = array(), $value = null)
	{
		if(!is_array($params))
		{
			// shorthand possibility, separate with commas
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
	public function column_size($params = array(), $value = null)
	{
		if(!is_array($params))
		{
			// shorthand possibility, separate with commas
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
	 * Add the custom column size, it works when using bootstrap
	 * framework that can be applied to column grid size
	 *
	 * NOTE: It's case sensitive!
	 *
	 * @access		public
	 * @param		mixed		$params
	 * @param		string		$value
	 * @return		mixed
	 */
	public function field_size($params = array(), $value = null)
	{
		if(!is_array($params))
		{
			// shorthand possibility, separate with commas
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
	public function field_prepend($params = array(), $value = null)
	{
		if(!is_array($params))
		{
			// shorthand possibility, separate with commas
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
	public function field_append($params = array(), $value = null)
	{
		if(!is_array($params))
		{
			// shorthand possibility, separate with commas
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
	public function merge_content($magic_string = null, $alias = null, $callback = null)
	{
		preg_match_all('#\{(.*?)\}#', $magic_string, $matches);
		
		$matches									= $matches[1];
		$field										= (isset($matches[0]) ? $matches[0] : null);
		
		if(!in_array($this->_method, array('create', 'update')))
		{
			$this->_set_alias[$field]				= ($alias && !is_array($alias) ? $alias : ucwords(str_replace('_', ' ', $field)));
			$this->_merge_label[$field]				= ($alias && !is_array($alias) ? $alias : ucwords(str_replace('_', ' ', $field)));
		}
		
		$this->_merge_content[$field]				= array
		(
			'column'								=> $matches,
			'parameter'								=> ($magic_string && !is_array($magic_string) ? $magic_string : null),
			'callback'								=> ($callback ? str_replace('callback_', '', $callback) : null)
		);
		
		if($matches)
		{
			foreach($matches as $key => $val)
			{
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
	public function group_field($params = array(), $group = null)
	{
		if(!is_array($params))
		{
			$params									= array_map('trim', explode(',', $params));
		}
		
		$this->_group_field							= array_merge($this->_group_field, array_fill_keys($params, $group));
		
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
	public function set_attribute($params = null, $value = null)
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
	public function set_placeholder($params = null, $value = null)
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
	public function set_option_label($params = null, $value = null)
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
	public function set_relation($field = null, $selected_value = null, $formatting = null, $where = array(), $join = array(), $order_by = array(), $group_by = null, $limit = null, $translate = false)
	{
		$as_field									= $field;
		
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
						
						array_unshift($select, $explode[0] . '.' . $field[0] . ' AS ' . $field[0] . '_masking');
					}
				}
			}
		}
		else
		{
			if(!in_array($selected_value, $select))
			{
				$select[]							= $selected_value . ' AS ' . $field;
			}
			
			if(isset($this->_set_attribute[$field]))
			{
				preg_match_all('#\{(.*?)\}#', $this->_set_attribute[$field], $matches_attributes);
				
				$select								= array_merge($matches_attributes[1], $select);
			}
			
			$selected_value							= explode('.', $selected_value);
			$relation_table							= (isset($selected_value[0]) ? $selected_value[0] : null);
			$relation_key							= (isset($selected_value[1]) ? $selected_value[1] : null);
			
			if(!$group_by)
			{
				$group_by							= $relation_table . '.' . $relation_key;
			}
			
			$this->_unset_column[]					= $field;
			$this->_unset_view[]					= $field;
		}
		
		if($join && !isset($join[0]))
		{
			$join									= array($join);
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
					$condition						.= ($condition ? ' AND ' : null) . $relation_table . '.' . $val . ' = {primary_table}.' . $val;
					
					/* apply validation */
					$this->set_validation($val, 'relation_checker[' . $relation_table . '.' . $val . ']');
				}
			}
			else
			{
				/* apply validation */
				$this->set_validation($field, 'relation_checker[' . $relation_table . '.' . $relation_key . ']');
			}
			
			$this->_join[$relation_table]			= array
			(
				'condition'							=> ($condition ? $condition : $relation_table . '.' . $relation_key . ' = {primary_table}.' . $field),
				'type'								=> 'LEFT'
			);
			
			if($join)
			{
				foreach($join as $key => $val)
				{
					$this->_join[$val[0]]			= array
					(
						'condition'					=> $val[1],
						'type'						=> (isset($val[2]) ? $val[2] : 'LEFT')
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
			$rk										= $params['relation_table'] . '.' . $params['relation_key'];
			$params['limit']						= $limit;
			$params['where'][$rk]					= $selected;
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
				
				if($table != $this->_from)
				{
					$val							= $val . ' AS ' . $column . '_' . $table;
				}
			}
			
			$this->model->select($val);
			
			$compiled_select[]						= $column;
			
			if(service('request')->getPost('method') == 'ajax_select' && service('request')->getPost('search'))
			{
				if(stripos($val, ' AS ') !== false)
				{
					$val							= substr($val, 0, stripos($val, ' AS '));
				}
				
				$like[$val]							= service('request')->getPost('search');
			}
		}
		
		if($like && !$limit)
		{
			$num									= 0;
			
			foreach($like as $key => $val)
			{
				if($num)
				{
					$this->model->or_like($key, $val);
				}
				else
				{
					$this->model->like($key, $val);
				}
				$num++;
			}
		}
		
		if($params['join'])
		{
			foreach($params['join'] as $key => $val)
			{
				if(!isset($val[0]) || !isset($val[1])) continue;
				
				$this->model->join($val[0], $val[1], (isset($val[2]) ? $val[2] : ''));
			}
		}
		
		if($params['where'])
		{
			foreach($params['where'] as $key => $val)
			{
				if(is_numeric($key) && stripos(trim($val), ' NOT IN') !== false)
				{
					$this->model->where($val, null, true);
				}
				elseif(is_numeric($key) && stripos(trim($val), ' IN') !== false)
				{
					$this->model->where($val, null, true);
				}
				elseif(stripos(trim($key), ' NOT IN') !== false)
				{
					$this->model->where_not_in(str_ireplace(' NOT IN', null, $key), $val);
				}
				elseif(stripos(trim($key), ' IN') !== false)
				{
					$this->model->where_in(str_ireplace(' IN', null, $key), $val);
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
					
					$this->model->where($params['relation_table'] . '.' . $val, $selected[$key]);
				}
			}
			else
			{
				$this->model->where($params['relation_table'] . '.' . $params['relation_key'], $selected);
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
		
		$query										= $this->model->limit($params['limit'], $params['offset'])->get($params['relation_table'])->result();
		
		if($query)
		{
			foreach($query as $key => $val)
			{
				$label								= str_ireplace(' AS ', ' ', $params['formatting']);
				$attributes							= str_ireplace(' AS ', ' ', (isset($this->_set_attribute[$primary_key]) ? $this->_set_attribute[$primary_key] : null));
				$option_label						= str_ireplace(' AS ', ' ', (isset($this->_set_option_label[$primary_key]) ? $this->_set_option_label[$primary_key] : null));
				
				foreach($params['select'] as $magic => $replace)
				{
					$replace						= str_ireplace(' AS ', ' ', $replace);
					
					if(isset($replace_me[$replace]))
					{
						$replacement				= $replace_me[$replace];
					}
					else
					{
						$replacement				= (stripos($replace, '.') !== false ? substr($replace, strripos($replace, '.') + 1) : $replace);
						$replacement				= (stripos($replacement, ' ') !== false ? substr($replacement, strripos($replacement, ' ') + 1) : $replacement);
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
						$value						= $val->$primary_key;
					}
					
					if($value == $selected)
					{
						$is_selected_exist			= true;
					}
					
					if($limit)
					{
						return ($params['translate'] ? phrase($label) : $label);
					}
					elseif(service('request')->isAJAX() && 'ajax_select' == service('request')->getPost('method') && isset($params['limit']) && $params['limit'] > 1)
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
		elseif(in_array($this->_method, array('create', 'update')))
		{
			$output									= '
				<select name="' . $primary_key . '" class="form-control' . (isset($this->_add_class[$primary_key]) ? ' ' . $this->_add_class[$primary_key] : null) . '" placeholder="' . (isset($this->_set_placeholder[$primary_key]) ? $this->_set_placeholder[$primary_key] : phrase('please_choose')) . '" id="' . $primary_key . '_input"' . (isset($this->_set_attribute[$primary_key]) ? ' ' . $this->_set_attribute[$primary_key] : null) . (isset($params['limit']) && $params['limit'] > 1 ? ' data-limit="' . $params['limit'] . '" data-href="' . current_page() . '"' : null) . (isset($this->_set_field[$primary_key]['field_type']) && in_array('disabled', $this->_set_field[$primary_key]['field_type']) ? ' disabled' : null) . '>
					' . ($query ? '<option value="' . (!$is_selected_exist ? $selected : null) . '">' . ($selected ? $this->_get_relation($params, $selected, 1, true) : phrase('please_choose')) . '</option>' : null) . '
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
	public function set_autocomplete($field = null, $selected_value = null, $formatting = null, $where = array(), $join = array(), $order_by = array(), $group_by = null, $limit = null)
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
				'type'								=> ''
			);
			
			if($join)
			{
				foreach($join as $key => $val)
				{
					$this->_join[$val[0]]			= array
					(
						'condition'					=> $val[1],
						'type'						=> (isset($val[2]) ? $val[2] : '')
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
		/*
		$keyword									= service('request')->getPost('q');
		$like										= $params['formatting']['label'];
		$like										.= $params['formatting']['description'];
		preg_match_all('#\{(.*?)\}#', $like, $matches_like);
		$like										= $matches_like[1];
		
		$this->model->select(array_unique($params['select']));
		
		if($selected)
		{
			$this->model->where($params['relation_table'] . '.' . $params['relation_key'], $selected);
		}
		else
		{
			foreach($like as $key => $val)
			{
				$val								= explode(' ', $val)[0];
				if($key > 0)
				{
					$this->model->or_like($val, str_replace(' ', '%', $keyword), false);
				}
				else
				{
					$this->model->like($val, str_replace(' ', '%', $keyword), false);
				}
			}
		}
		
		if($params['where'])
		{
			$this->model->where($params['where']);
		}
		
		if($params['order_by'])
		{
			$this->model->order_by($params['order_by']);
		}
		
		if($params['group_by'])
		{
			$this->model->group_by($params['group_by']);
		}
		
		if($params['limit'])
		{
			$this->model->limit($params['limit']);
		}
		
		if($params['join'])
		{
			foreach($params['join'] as $key => $val)
			{
				if(is_array($val) && isset($val[0]) && isset($val[1]))
				{
					$this->model->join($val[0], $val[1]);
				}
			}
		}
		
		$query										= $this->model->get($params['relation_table'], ($selected ? 1 : 50))->result();
		$output										= array();
		
		if($query)
		{
			foreach($query as $key => $val)
			{
				$value								= $params['formatting']['value'];
				$label								= str_ireplace(' AS ', ' ', $params['formatting']['label']);
				$description						= $params['formatting']['description'];
				$image								= $params['formatting']['image'];
				
				foreach($params['select'] as $magic => $replace)
				{
					$replace						= str_ireplace(' AS ', ' ', $replace);
					
					if(isset($replace_me[$replace]))
					{
						$replacement				= $replace_me[$replace];
					}
					else
					{
						$replacement				= (stripos($replace, '.') !== false ? substr($replace, strripos($replace, '.') + 1) : $replace);
						$replacement				= (stripos($replacement, ' ') !== false ? substr($replacement, strripos($replacement, ' ') + 1) : $replacement);
					}
					
					if(isset($val->$replacement))
					{
						$value						= str_replace('{' . $replace . '}', $val->$replacement, $value);
						$label						= str_replace('{' . $replace . '}', $val->$replacement, $label);
						$description				= str_replace('{' . $replace . '}', $val->$replacement, $description);
						$image						= str_replace('{' . $replace . '}', $val->$replacement, $image);
					}
				}
				
				$output[]							= array
				(
					'value'							=> $value,
					'label'							=> preg_replace('/\s+/', ' ', $label),
					'description'					=> preg_replace('/\s+/', ' ', $description),
					'image'							=> $image
				);
			}
		}
		*/
		
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
	public function render($table = null, $view = null)
	{
		/* validate the restricted action */
		if(in_array($this->_method, $this->_unset_action))
		{
			return throw_exception(403, phrase('you_cannot_perform_the_requested_action'), $this->_redirect_back);
		}
		
		$this->template								= new Template($this->_set_theme, $this->_api_request);
		
		/* check if given table is exists in database */
		if($table && $this->model->table_exists($table))
		{
			$this->_from							= $table;
			$this->_field_data						= json_decode(json_encode($this->model->field_data($this->_from)), true);
			
			/* set the default primary if the table have any primary column */
			if(!$this->_set_primary)
			{
				foreach($this->_field_data as $key => $val)
				{
					if(isset($val['primary_key']) && $val['primary_key'] === 1)
					{
						$this->_set_primary[]		= $val['name'];
					}
				}
			}
			
			/* check again if the primary key is still unavailable */
			if(!$this->_set_primary)
			{
				$this->set_description('<div class="alert-danger pt-2 pr-3 pb-2 pl-3 text-sm" style="margin-left:-15px; margin-right:-15px"><b>' . phrase('no_primary_key_found') . '</b> ' . phrase('please_define_it_manually') . ' (' . phrase('refers_to') . ' <code>set_primary()</code>). ' . phrase('otherwise_you_cannot_perform_the_following_action') . ': ' . phrase('read') . ', ' . phrase('update') . ', ' . phrase('delete') . ' ' . phrase('and') . ' ' . phrase('export_to_document') . '</div>');
				$this->unset_action('read, update, delete, export, print, pdf');
			}
			
			/* check the requested method */
			if(in_array($this->_method, array('read', 'update', 'delete', 'export', 'print', 'pdf')))
			{
				/* set limit of modification action */
				if(in_array($this->_method, array('read', 'update', 'delete')) || ($this->_api_request && service('request')->getHeaderLine('X-API-KEY') == sha1(ENCRYPTION_KEY . service('request')->getHeaderLine('X-ACCESS-TOKEN'))))
				{
					$this->_limit					= 1;
				}
				
				/* apply primary from where if it's were sets */
				if(!$this->_set_primary && $this->_where)
				{
					$this->_set_primary				= array_keys($this->_where);
				}
				
				/* check the additional primary key that been sets up */
				if(is_array($this->_set_primary) && sizeof($this->_set_primary) > 0)
				{
					if($this->_where && !in_array($this->_set_method, array('read', 'update')))
					{
						$this->_where				= array();
					}
					
					foreach($this->_set_primary as $key => $val)
					{
						if(service('request')->getGet($val) && $this->model->field_exists($val, $this->_from))
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
							
							$key					= $this->_from . '.' . $val;
							$this->_where[$key]		= service('request')->getGet($val);
						}
						elseif(in_array($val, $this->_set_primary) && $this->model->field_exists($val, $this->_from) && isset($this->_set_default[$val]) && $this->_set_default[$val])
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
							
							$key					= $this->_from . '.' . $val;
							$this->_where[$key]		= $this->_set_default[$val];
						}
					}
				}
			}
			else
			{
				$this->_offset						= (is_numeric(service('request')->getGet('per_page')) ? service('request')->getGet('per_page') - 1 : 0) * $this->_limit;
				
				if((!$this->_like && service('request')->getGet('q')) || ('autocomplete' == service('request')->getPost('method') && service('request')->getPost('q')))
				{
					$column							= service('request')->getGet('column');
					
					if($column && 'all' != $column)
					{
						$this->_like				= array
						(
							$column					=> ('autocomplete' == service('request')->getPost('method') && service('request')->getPost('q') ? service('request')->getPost('q') : service('request')->getGet('q'))
						);
					}
					else
					{
						$columns					= $this->model->list_fields($this->_from);
						
						if($columns)
						{
							foreach($columns as $like_key => $like_val)
							{
								$v					= $like_val;
								if($this->_like)
								{
									$this->_or_like[$v]		= ('autocomplete' == service('request')->getPost('method') && service('request')->getPost('q') ? service('request')->getPost('q') : service('request')->getGet('q'));
								}
								else
								{
									$this->_like[$v]		= ('autocomplete' == service('request')->getPost('method') && service('request')->getPost('q') ? service('request')->getPost('q') : service('request')->getGet('q'));
								}
							}
						}
						
						if($this->_select)
						{
							foreach($this->_select as $like_key => $like_val)
							{
								$like_val					= str_ireplace(' AS ', ' ', $like_val);
								$like_val					= (stripos($like_val, ' ') !== false ? substr($like_val, strripos($like_val, ' ') + 1) : $like_val);
								
								if($this->_like)
								{
									$this->_or_like[$v]		= ('autocomplete' == service('request')->getPost('method') && service('request')->getPost('q') ? service('request')->getPost('q') : service('request')->getGet('q'));
								}
								else
								{
									$this->_like[$v]		= ('autocomplete' == service('request')->getPost('method') && service('request')->getPost('q') ? service('request')->getPost('q') : service('request')->getGet('q'));
								}
								
								if(stripos($like_val, ' AS ') !== false)
								{
									$like_val				= substr($like_val, 0, stripos($like_val, ' AS '));
								}
								
								if(isset($this->_set_field[service('request')->getPost('origin')]['parameter']) && $this->model->field_exists($like_val, $this->_set_field[service('request')->getPost('origin')]['parameter']))
								{
									$this->_order_by_bm[]	= '(CASE WHEN ' . $like_val . ' LIKE "' . service('request')->getPost('q') . '%" THEN 1 WHEN ' . $like_val . ' LIKE "%' . service('request')->getPost('q') . '" THEN 3 ELSE 2 END)';
								}
							}
						}
					}
				}
			}
			
			if($this->_set_primary)
			{
				$this->_crud						= true;
			}
			
			/* check if data is requested through autocomplete (jQuery plugin) */
			if(service('request')->isAJAX() && 'autocomplete' == service('request')->getPost('method'))
			{
				/**
				 * PLEASE NOTE
				 *
				 * It's a simple  autocomplete  input to  render only one  database
				 * table.
				 * To use a complex autocomplete within complex database query, use
				 * the set_autocomplete()  function instead.  It will allow you  to
				 * custom format the result, also join  into multiple table include
				 * where, order and grouping
				 */
				$suggestions						= array();
				
				if(isset($this->_set_field[service('request')->getPost('origin')]) && in_array('autocomplete', $this->_set_field[service('request')->getPost('origin')]['field_type']))
				{
					/* set the relation table, field and keyword */
					$field							= $this->_set_field[service('request')->getPost('origin')];
					$table							= $field['parameter'];
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
						
						if($num > 0)
						{
							$this->model->or_like($val, $keyword);
						}
						else
						{
							$this->model->like($val, $keyword);
						}
						
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
					
					/* order by best match */
					if($this->_order_by_bm)
					{
						foreach($this->_order_by_bm as $key => $val)
						{
							$this->model->order_by($val);
						}
					}
					
					if($order)
					{
						$this->model->order_by($order);
					}
					
					/* run query */
					$query							= $this->model->get($table, 50)->result_array();
					
					if($query)
					{
						foreach($query as $val)
						{
							/* list as value, label and description */
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
					 * Autocomplete search data from list of table
					 */
					if(!$this->_select)
					{
						/* check the select list, if none, use the main table field instead */
						$this->_select				= preg_filter('/^/', $this->_from . '.', $this->model->list_fields($this->_from));
					}
					
					/* loop the select field to prevent query using multiple LIKE condition and use OR LIKE instead */
					foreach($this->_select as $key => $val)
					{
						$val						= str_ireplace(' AS ', ' ', $val);
						$val						= (stripos($val, ' ') !== false ? substr($val, strripos($val, ' ') + 1) : $val);
						
						/* if there's LIKE */
						if($this->_like)
						{
							/* use OR LIKE */
							$this->_or_like[$val]	= service('request')->getPost('q');
						}
						else
						{
							/* otherwise, set the LIKE */
							$this->_like[$val]		= service('request')->getPost('q');
						}
					}
					
					/* run query */
					$query							= $this->_run_query($this->_from);
					$this->_query					= $query['results'];
					
					/* serialize results */
					$serialized						= $this->serialize($this->_query);
					
					foreach($serialized as $key => $val)
					{
						/* does column order is mandatory? let's just watch */
						if(is_array($this->_column_order) && sizeof($this->_column_order) > 0)
						{
							/* set the default column order */
							$column_order			= array();
							foreach($this->_column_order as $order_key => $order_val)
							{
								/* if array key exists */
								if(array_key_exists($order_val, $val))
								{
									/* then push to column order grocery */
									$column_order[]	= $order_val;
								}
							}
							/* set the value */
							$val					= array_replace(array_flip($column_order), $val);
						}
						
						$autocomplete_item			= array();
						/* loop the result */
						foreach($val as $field => $value)
						{
							/* check if the result value is not contain the search keyword or the field is unset from column (list table) */
							if(strpos(strtolower($value['original']), strtolower(service('request')->getPost('q'))) === false || in_array($field, $this->_unset_column)) continue;
							
							/* everything's looks good, throw into autocomplete result */
							if(!$autocomplete_item && $value['original'])
							{
								$autocomplete_item	= array
								(
									'value'			=> truncate($value['content'], 32),
									'label'			=> truncate($value['content'], 32),
									'target'		=> current_page(null, array('per_page' => null, 'q' => truncate($value['content'], 32)))
								);
							}
						}
						
						if($autocomplete_item)
						{
							$suggestions[]			= $autocomplete_item;
						}
					}
				}
				
				/* return the callback as autocomplete results */
				return make_json
				(
					array
					(
						'suggestions'				=> ($suggestions ? $suggestions : null)
					)
				);
			}
			
			/* check if data is requested through server side select (jQuery plugin) */
			elseif(service('request')->isAJAX() && 'ajax_select' == service('request')->getPost('method') && isset($this->_set_relation[service('request')->getPost('source')]))
			{
				return $this->_get_relation($this->_set_relation[service('request')->getPost('source')]);
			}
			
			if(service('request')->getGet('order'))
			{
				$this->_order_by					= array();
				$order								= service('request')->getGet('order');
				$this->_order_by[$order]			= service('request')->getGet('sort');
			}
			
			/* get the results from the database query */
			if(service('request')->getGet('order'))
			{
				$this->_order_by					= array();
				$order								= service('request')->getGet('order');
				$this->_order_by[$order]			= service('request')->getGet('sort');
			}
			
			if($this->_api_request && service('request')->getHeaderLine('X-API-KEY') == sha1(ENCRYPTION_KEY . service('request')->getHeaderLine('X-ACCESS-TOKEN')))
			{
				$this->_query						= array(array_fill_keys(array_keys(array_flip($this->model->list_fields($this->_from))), ''));
			}
			elseif($this->_method == 'create')
			{
				$this->_query						= array(array_flip($this->model->list_fields($this->_from)));
			}
			else
			{
				$query								= $this->_run_query($this->_from);
				$this->_query						= $query['results'];
				$this->_total						= $query['total'];
			}
			
			/* try to convert the magic string and replace with result */
			$title									= $this->_set_title;
			$description							= (isset($this->_set_description['index']) ? $this->_set_description['index'] : null);
			
			if($title && isset($this->_query[0]))
			{
				foreach($this->_query[0] as $do => $magic)
				{
					$title							= str_replace('{' . $do . '}', $magic, $title);
					$description					= str_replace('{' . $do . '}', $magic, $description);
				}
			}
			else
			{
				preg_match_all('#\{(.*?)\}#', $title, $matches);
				$matches							= $matches[1];
				
				if($matches)
				{
					foreach($matches as $key => $val)
					{
						$title						= str_replace('{' . $val . '}', null, $title);
					}
				}
				
				preg_match_all('#\{(.*?)\}#', $description, $matches_2);
				$matches_2							= $matches_2[1];
				
				if($matches_2)
				{
					foreach($matches_2 as $key => $val)
					{
						$description				= str_replace('{' . $val . '}', null, $description);
					}
				}
			}
			
			if(!is_array($title))
			{
				$title								= trim($title);
			}
			
			/* if method is create */
			if('create' == $this->_method)
			{
				$this->_set_icon					= 'mdi mdi-plus';
				$this->_set_title					= (isset($this->_set_title['create']) ? $this->_set_title['create'] : phrase('add_new_data'));
				$this->_set_description				= (isset($this->_set_description['create']) ? $this->_set_description['create'] : '<div class="alert-info pt-2 pr-3 pb-2 pl-3" style="margin-left:-1rem; margin-right:-1rem">' . phrase('please_fill_all_the_required_fields_below_to_add_new_data') . '</div>');
				$this->_view						= (isset($this->_set_template['form']) ? $this->_set_template['form'] : ($view && 'index' != $view ? $view : 'form'));
				$this->_results						= $this->render_form($this->_query);
			}
			
			/* if method is read */
			elseif('read' == $this->_method)
			{
				$this->_set_icon					= 'mdi mdi-magnify-plus';
				$this->_set_title					= (isset($this->_set_title['read']) ? $this->_set_title['read'] : phrase('showing_data'));
				$this->_set_description				= (isset($this->_set_description['read']) ? $this->_set_description['read'] : '<div class="alert-info pt-2 pr-3 pb-2 pl-3" style="margin-left:-1rem; margin-right:-1rem">' . phrase('showing_the_result_of_the_selected_item') . '</div>');
				$this->_view						= (isset($this->_set_template['read']) ? $this->_set_template['read'] : ($view && 'index' != $view ? $view : 'read'));
				$this->_results						= ('table' == service('request')->getPost('show_in') ? $this->render_table($this->_query) : $this->render_read($this->_query));
				
				if('table' == service('request')->getPost('show_in') && isset($this->_results['table_data'][0]))
				{
					return make_json
					(
						array
						(
							'table_data'			=> $this->_results['table_data'][0]
						)
					);
				}
			}
			
			/* if method is update */
			elseif('update' == $this->_method)
			{
				$this->_set_icon					= 'mdi mdi-square-edit-outline';
				$this->_set_title					= (isset($this->_set_title['update']) ? $this->_set_title['update'] : phrase('update_data'));
				$this->_set_description				= (isset($this->_set_description['update']) ? $this->_set_description['update'] : '<div class="alert-info pt-2 pr-3 pb-2 pl-3" style="margin-left:-1rem; margin-right:-1rem">' . phrase('make_sure_to_check_the_changes_before_submitting') . '</div>');
				$this->_view						= (isset($this->_set_template['form']) ? $this->_set_template['form'] : ($view && 'index' != $view ? $view : 'form'));
				$this->_results						= $this->render_form($this->_query);
			}
			
			/* if method is export */
			elseif(in_array($this->_method, array('export', 'print', 'pdf')))
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
				
				$this->_view						= (isset($this->_set_template['export']) ? $this->_set_template['export'] : 'templates/export');
				$this->_results						= ($single_print ? $this->render_read($this->_query) : $this->render_table($this->_query));
			}
			
			/* if other method is exists */
			elseif(method_exists($this, $this->_method))
			{
				$view_exists						= (!in_array($this->template->get_view($this->_view, $this->_query, $table), array('templates/index', 'templates/index_mobile', 'templates/error')) ? true : false);
				
				$this->_set_icon					= ($this->_set_icon ? $this->_set_icon : 'mdi mdi-table');
				$this->_set_title					= ($title ? $title : ($this->_crud || $this->_query ? phrase('untitled') : ($this->_set_title_placeholder ? $this->_set_title_placeholder : phrase('page_not_found'))));
				$this->_set_description				= ($description ? $description : null);
				$this->_view						= (isset($this->_set_template['index']) ? $this->_set_template['index'] : ($view && 'index' != $view ? $view : 'index'));
				$this->_results						= ($this->_crud && !$view_exists ? $this->render_table($this->_query) : $this->_query);
			}
			
			/* otherwise */
			else
			{
				$view_exists						= (!in_array($this->template->get_view($this->_view, $this->_query, $table), array('templates/index', 'templates/index_mobile', 'templates/error')) ? true : false);
				
				$this->_set_icon					= ($this->_set_icon ? $this->_set_icon : 'mdi mdi-table');
				$this->_set_title					= ($title ? $title : ($this->_crud || $this->_query ? phrase('untitled') : ($this->_set_title_placeholder ? $this->_set_title_placeholder : phrase('page_not_found'))));
				$this->_set_description				= ($description ? $description : null);
				$this->_view						= (isset($this->_set_template['index']) ? $this->_set_template['index'] : ($view && 'index' != $view ? $view : 'index'));
				$this->_results						= ($this->_crud && !$view_exists ? $this->render_table($this->_query) : $this->_query);
			}
		}
		else
		{
			$this->_set_icon						= ($this->_set_icon ? $this->_set_icon : 'mdi mdi-file-document-outline');
			$this->_set_title						= ($this->_set_title ? $this->_set_title : phrase('untitled'));
			$this->_set_description					= (isset($this->_set_description['index']) ? $this->_set_description['index'] : null);
			$this->_view							= (isset($this->_set_template['index']) ? $this->_set_template['index'] : ($view && 'index' != $view ? $view : 'index'));
			$this->_results							= $this->_query;
		}
		
		/**
		 * Prepare the output
		 */
		if($this->_api_request)
		{
			$results								= (isset($this->_results['table_data']) ? $this->_results['table_data'] : (isset($this->_results['form_data']) ? $this->_results['form_data'] : $this->_results));
			
			if(!$results && service('request')->getHeaderLine('X-API-KEY') != sha1(ENCRYPTION_KEY . service('request')->getHeaderLine('X-ACCESS-TOKEN')))
			{
				$results							= array();
			}
			elseif($results && 'index' == $this->_method && service('request')->getHeaderLine('X-API-KEY') == sha1(ENCRYPTION_KEY . service('request')->getHeaderLine('X-ACCESS-TOKEN')))
			{
				$this->_total						= sizeof($results);
			}
			
			$this->_output							= array
			(
				'method'							=> $this->_method,
				'query_string'						=> $this->_set_primary,
				'results'							=> $results
			);
			
			if(in_array($this->_method, array('index')))
			{
				$this->_output['total']				= ($this->_total ? $this->_total : 0);
			}
			
			if($this->_set_output)
			{
				$this->_output						= array_merge($this->_output, $this->_set_output);
			}
			
			if(service('request')->getHeaderLine('X-API-KEY') == sha1(ENCRYPTION_KEY . service('request')->getHeaderLine('X-ACCESS-TOKEN')))
			{
				return make_json($this->_output);
			}
		}
		else
		{
			$this->_output							= array
			(
				'_token'							=> sha1(current_page() . ENCRYPTION_KEY . get_userdata('session_generated')),
				'method'							=> $this->_method,
				'breadcrumb'						=> $this->template->breadcrumb($this->_set_breadcrumb, $this->_set_title, $this->_query),
				'current_page'						=> current_page(null, service('request')->getGet()),
				'meta'								=> array
				(
					'description'					=> $this->_set_description,
					'icon'							=> $this->_set_icon,
					'title'							=> $this->_set_title,
					'modal_size'					=> $this->_modal_size
				),
				'results'							=> $this->_results,
				'total'								=> $this->_total,
				'pagination'						=> array
				(
					'offset'						=> $this->_offset,
					'per_page'						=> $this->_limit,
					'total_rows'					=> $this->_total,
					'url'							=> current_page(null, array('per_page' => null))
				),
				'query_string'						=> service('request')->getGet(),
				'elapsed_time'						=> service('timer')->stop('elapsed_time')->getElapsedTime('elapsed_time')
			);
			
			if(isset($this->_set_template['read']) || isset($this->_set_template['form']))
			{
				$this->_output['modal_html']			= true;
			}
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
		if('delete' == $this->_method)
		{
			/**
			 * Method delete
			 */
			if(1 == service('request')->getPost('batch'))
			{
				/* batch delete */
				return $this->delete_batch($this->_from);
			}
			elseif($this->_where)
			{
				/* single delete */
				return $this->delete_data($this->_from, $this->_where, $this->_limit);
			}
		}
		elseif('print' == $this->_method)
		{
			/**
			 * Method print
			 */
			$this->_output->template				= (object) array
			(
				'pagination'						=> $this->template->pagination($this->_output->pagination)
			);
			
			// send to client
			return service('response')->setBody(view($this->_view, (array) $this->_output))->send();
		}
		elseif(in_array($this->_method, array('pdf', 'export')))
		{
			/**
			 * Method document
			 */
			$this->_output							= view($this->_view, (array) $this->_output);
			
			$this->document							= new \Aksara\Libraries\Document;
			
			$this->document->pageSize('13in 8.5in');
			
			return $this->document->generate($this->_output, $this->_set_title, ($this->_method == 'export' ? 'export' : 'embed'));
		}
		else
		{
			/**
			 * Or
			 */
			if(service('request')->getPost('_token'))
			{
				/**
				 * Post token is initial to validate form. It's mean the request were
				 * submitted through the form
				 */
				
				/* validate sent token */
				$token_sent							= service('request')->getPost('_token');
				
				if($this->valid_token($token_sent))
				{
					/* token approved, check if validation use the custom callback */
					if(method_exists($this, $this->_form_callback))
					{
						/* use callback as form validation */
						$_callback					= $this->_form_callback;
						
						return $this->$_callback();
					}
					else
					{
						/* or use the master validation instead */
						return $this->validate_form($this->_query);
					}
				}
				else
				{
					/* token isn't valid, throw exception */
					return throw_exception(403, phrase('the_token_you_submitted_has_been_expired_or_you_are_trying_to_bypass_it_from_the_restricted_source'), $this->_redirect_back);
				}
			}
			elseif($this->_api_request && 'POST' == env('REQUEST_METHOD') && (in_array($this->_method, array('create', 'update')) || method_exists($this, $this->_form_callback)))
			{
				/**
				 * Indicate the method is requested through API
				 */
				if(method_exists($this, $this->_form_callback))
				{
					/* use callback as form validation */
					$_callback						= $this->_form_callback;
					
					return $this->$_callback();
				}
				else
				{
					/* or use the master validation instead */
					return $this->validate_form($this->_query);
				}
			}
			else
			{
				if(('html' != service('request')->getPost('prefer') && service('request')->isAJAX() && stripos($this->template->get_view($this->_view), 'templates/') !== false && (isset($this->_output->results->table_data) || isset($this->_output->results->form_data))) || $this->_api_request)
				{
					/**
					 * Indicate the method is requested through Promise (XHR) or API
					 */
					if('modal' == service('request')->getPost('prefer'))
					{
						unset($this->_output->breadcrumb, $this->_output->total, $this->_output->pagination);
						
						if($this->_set_method)
						{
							return $this->template->build($this->_view, $this->_output, null, $this->_from, $this->_language);
						}
					}
					else
					{
						if(in_array($this->_method, array('create', 'read', 'update')))
						{
							unset($this->_output->total, $this->_output->pagination);
						}
						elseif($this->_api_request && service('request')->getHeaderLine('X-API-KEY') == sha1(ENCRYPTION_KEY . service('request')->getHeaderLine('X-ACCESS-TOKEN')) && 'POST' != env('REQUEST_METHOD'))
						{
							unset($this->_output->pagination);
						}
						
						if(isset($this->_output->pagination))
						{
							$this->_output->pagination	= $this->template->pagination($this->_output->pagination, false);
						}
					}
					
					/**
					 * Returning the response as json format
					 */
					if(stripos(env('HTTP_REFERER'), env('SERVER_NAME')) !== false || $this->_api_request)
					{
						if($this->_api_request && 'GET' != env('REQUEST_METHOD'))
						{
							/**
							 * Indicate the method is requested through API
							 */
							return throw_exception(403, phrase('the_method_you_requested_is_not_acceptable') . ' (' . env('REQUEST_METHOD'). ')', (!$this->_api_request ? $this->_redirect_back : null));
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
				
				/* Display to the browser */
				return $this->template->build($this->_view, $this->_output, $this->_set_breadcrumb, $this->_from, $this->_language);
			}
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
		if($this->_api_request && 'POST' != env('REQUEST_METHOD'))
		{
			/**
			 * Indicate the method is requested through API
			 */
			return throw_exception(403, phrase('the_method_you_requested_is_not_acceptable') . ' (' . env('REQUEST_METHOD'). ')', (!$this->_api_request ? $this->_redirect_back : null));
		}
		
		if($table && $this->model->table_exists($table))
		{
			if(method_exists($this, 'before_insert'))
			{
				$this->before_insert();
			}
			
			if($this->model->insert($table, $data))
			{
				$this->_insert_id					= $this->model->insert_id();
				
				if(method_exists($this, 'after_insert'))
				{
					$this->after_insert();
				}
				
				return throw_exception(($this->_api_request ? 200 : 301), phrase('data_was_successfully_submitted'), (!$this->_api_request ? $this->_redirect_back : null));
			}
			else
			{
				/* otherwise, the item is cannot be deleted */
				$error								= $this->model->error();
				
				if(in_array(get_userdata('group_id'), array(1)) && isset($error['message']))
				{
					/* for administrator */
					return throw_exception(500, $error['message'], (!$this->_api_request ? $this->_redirect_back : null));
				}
				
				/* for user */
				return throw_exception(500, phrase('unable_to_submit_your_data') . ' ' . phrase('please_try_again_or_contact_the_system_administrator') . ' ' . phrase('error_code') . ': <b>500 (insert)</b>', (!$this->_api_request ? $this->_redirect_back : null));
			}
		}
		else
		{
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
	public function update_data($table = null, $data = array(), $where = array(), $redirect = null, $callback = null)
	{
		if($this->_api_request && 'POST' != env('REQUEST_METHOD'))
		{
			/**
			 * Indicate the method is requested through API
			 */
			return throw_exception(403, phrase('the_method_you_requested_is_not_acceptable') . ' (' . env('REQUEST_METHOD'). ')', (!$this->_api_request ? $this->_redirect_back : null));
		}
		
		if($table && $this->model->table_exists($table))
		{
			if(is_array($where) && sizeof($where) > 0 && $this->model->get_where($table, $where, 1)->num_rows() > 0)
			{
				if($redirect)
				{
					$this->_redirect_back			= $redirect;
				}
				
				if(method_exists($this, 'before_update'))
				{
					$this->before_update();
				}
				
				if($this->model->update($table, $data, $where, 1))
				{
					/* check if file is updated */
					if($this->_old_files && sizeof($this->_old_files) > 0)
					{
						foreach($this->_old_files as $alt => $src)
						{
							if($src != 'placeholder.png')
							{
								/* delete old file */
								@unlink(UPLOAD_PATH . '/' . $this->_set_upload_path . '/' . $src);
								@unlink(UPLOAD_PATH . '/' . $this->_set_upload_path . '/thumbs/' . $src);
								@unlink(UPLOAD_PATH . '/' . $this->_set_upload_path . '/icons/' . $src);
							}
						}
					}
					
					if($callback && method_exists($this, $callback))
					{
						$this->$callback();
					}
					
					if(method_exists($this, 'after_update'))
					{
						$this->after_update();
					}
					
					return throw_exception(($this->_api_request ? 200 : 301), phrase('data_was_successfully_updated'), (!$this->_api_request ? $this->_redirect_back : null));
				}
				else
				{
					if($callback && method_exists($this, $callback))
					{
						$this->$callback();
					}
					else
					{
						/* otherwise, the item is cannot be deleted */
						$error						= $this->model->error();
						
						if(in_array(get_userdata('group_id'), array(1)) && isset($error['message']))
						{
							/* for administrator */
							return throw_exception(500, $error['message'], (!$this->_api_request ? $this->_redirect_back : null));
						}
						
						/* for user */
						return throw_exception(500, phrase('unable_to_update_data') . ' ' . phrase('please_try_again_or_contact_the_system_administrator') . ' ' . phrase('error_code') . ': <b>500 (update)</b>', (!$this->_api_request ? $this->_redirect_back : null));
					}
				}
			}
			elseif($this->_insert_on_update_fail)
			{
				if($callback && method_exists($this, $callback))
				{
					$this->$callback();
				}
				else
				{
					$this->insert_data($table, $data);
				}
			}
			else
			{
				if($callback && method_exists($this, $callback))
				{
					$this->$callback();
				}
				else
				{
					return throw_exception(404, phrase('the_data_you_want_to_update_was_not_found'), (!$this->_api_request ? $this->_redirect_back : null));
				}
			}
		}
		else
		{
			if($callback && method_exists($this, $callback))
			{
				$this->$callback();
			}
			else
			{
				return throw_exception(404, phrase('the_selected_database_table_does_not_exists'), (!$this->_api_request ? $this->_redirect_back : null));
			}
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
		if($this->_api_request && 'DELETE' != env('REQUEST_METHOD'))
		{
			/**
			 * Indicate the method is requested through API
			 */
			return throw_exception(403, phrase('the_method_you_requested_is_not_acceptable') . ' (' . env('REQUEST_METHOD'). ')', (!$this->_api_request ? $this->_redirect_back : null));
		}
		
		/* check if app on demo mode */
		if($this->_restrict_on_demo)
		{
			return throw_exception(403, phrase('this_feature_is_disabled_in_demo_mode'), (!$this->_api_request ? $this->_redirect_back : null));
		}
		
		/* hide the system error and show in the exception instead */
		error_reporting(0);
		
		/* make sure the delete action have where as condition */
		if(!$where)
		{
			/* otherwise, redirect to previous page */
			return throw_exception(404, phrase('the_data_you_want_to_delete_was_not_found'), (!$this->_api_request ? $this->_redirect_back : null));
		}
		
		/* check if delete have a callback message */
		if(isset($this->_set_messages['delete']) && $this->_set_messages['delete']['return'])
		{
			/* use the callback message */
			return throw_exception($this->_set_messages['delete']['code'], $this->_set_messages['delete']['messages'], $this->_redirect_back);
		}
		
		/* check if targeted table is exists */
		if($table && $this->model->table_exists($table))
		{
			$query									= $this->model->get_where($table, $where, $limit)->result();
			
			/* check if the item is exists */
			if($query)
			{
				/* before delete callback */
				if(method_exists($this, 'before_delete'))
				{
					$this->before_delete();
				}
				
				/* safe check for delete */
				if($this->model->delete($table, $where, $limit))
				{
					/* prepare to delete file */
					$serialized						= $this->serialize($query);
					
					foreach($serialized as $key => $val)
					{
						foreach($val as $field => $params)
						{
							if(in_array('file', $params['type']) || in_array('files', $params['type']) || in_array('images', $params['type']))
							{
								$files				= json_decode($params['original']);
								
								if($files)
								{
									foreach($files as $src => $alt)
									{
										if($src != 'placeholder.png')
										{
											/* delete file */
											@unlink(UPLOAD_PATH . '/' . $this->_set_upload_path . '/' . $src);
											@unlink(UPLOAD_PATH . '/' . $this->_set_upload_path . '/thumbs/' . $src);
											@unlink(UPLOAD_PATH . '/' . $this->_set_upload_path . '/icons/' . $src);
										}
									}
								}
							}
							elseif(in_array('image', $params['type']) && 'placeholder.png' != $params['original'])
							{
								/* delete file */
								@unlink(UPLOAD_PATH . '/' . $this->_set_upload_path . '/' . $params['type']);
								@unlink(UPLOAD_PATH . '/' . $this->_set_upload_path . '/thumbs/' . $params['type']);
								@unlink(UPLOAD_PATH . '/' . $this->_set_upload_path . '/icons/' . $params['type']);
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
					/* otherwise, the item is cannot be deleted */
					$error							= $this->model->error();
					
					if(in_array(get_userdata('group_id'), array(1)) && isset($error['message']))
					{
						/* for administrator */
						return throw_exception(500, $error['message'], (!$this->_api_request ? $this->_redirect_back : null));
					}
					
					/* for user */
					return throw_exception(500, phrase('unable_to_remove_the_selected_data') . '. ' . phrase('please_try_again_or_contact_the_system_administrator') . '. ' . phrase('error_code') . ': <b>500 (delete)</b>', (!$this->_api_request ? $this->_redirect_back : null));
				}
			}
			else
			{
				/* no item found */
				return throw_exception(404, phrase('the_data_you_want_to_delete_was_not_found'), (!$this->_api_request ? $this->_redirect_back : null));
			}
		}
		else
		{
			/* the targeted database table isn't exists */
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
		if($this->_api_request && 'DELETE' != env('REQUEST_METHOD'))
		{
			/**
			 * Indicate the method is requested through API
			 */
			return throw_exception(403, phrase('the_method_you_requested_is_not_acceptable') . ' (' . env('REQUEST_METHOD'). ')', (!$this->_api_request ? $this->_redirect_back : null));
		}
		
		/* check if app on demo mode */
		if($this->_restrict_on_demo)
		{
			return throw_exception(403, phrase('this_feature_is_disabled_in_demo_mode'), (!$this->_api_request ? $this->_redirect_back : null));
		}
		
		/* get the checked items */
		$items										= service('request')->getPost('items');
		$where										= null;
		$affected_rows								= 0;
		$ignored_rows								= 0;
		
		if(is_array($items) && sizeof($items) > 0)
		{
			/* before delete callback */
			if(method_exists($this, 'before_delete'))
			{
				$this->before_delete();
			}
			
			/* safe check to make sure the given field parameter is exists in the database table */
			$field_exists							= array_flip($this->model->list_fields($table));
			
			foreach($items as $key => $val)
			{
				/* unset the field parameter that not exist in database table */
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
				
				/* get old data to prepare file deletion */
				$query								= $this->model->get_where($table, $val, 1)->result();
				
				if($query && $this->model->delete($table, $val))
				{
					/* prepare to delete file */
					$serialized						= $this->serialize($query);
					
					foreach($serialized as $_key => $_val)
					{
						foreach($_val as $field => $params)
						{
							if(in_array('file', $params['type']) || in_array('files', $params['type']) || in_array('images', $params['type']))
							{
								$files				= json_decode($params['original']);
								
								if($files)
								{
									foreach($files as $src => $alt)
									{
										if($src != 'placeholder.png')
										{
											/* delete file */
											@unlink(UPLOAD_PATH . '/' . $this->_set_upload_path . '/' . $src);
											@unlink(UPLOAD_PATH . '/' . $this->_set_upload_path . '/thumbs/' . $src);
											@unlink(UPLOAD_PATH . '/' . $this->_set_upload_path . '/icons/' . $src);
										}
									}
								}
							}
							elseif(in_array('image', $params['type']) && 'placeholder.png' != $params['original'])
							{
								/* delete file */
								@unlink(UPLOAD_PATH . '/' . $this->_set_upload_path . '/' . $params['type']);
								@unlink(UPLOAD_PATH . '/' . $this->_set_upload_path . '/thumbs/' . $params['type']);
								@unlink(UPLOAD_PATH . '/' . $this->_set_upload_path . '/icons/' . $params['type']);
							}
						}
					}
					
					$affected_rows++;
				}
			}
			
			/* after delete callback */
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
		if($this->_api_request && service('request')->getHeaderLine('X-API-KEY') != sha1(ENCRYPTION_KEY . service('request')->getHeaderLine('X-ACCESS-TOKEN')) && 'POST' != env('REQUEST_METHOD'))
		{
			/**
			 * Indicate the method is requested through API
			 */
			return throw_exception(403, phrase('the_method_you_requested_is_not_acceptable') . ' (' . env('REQUEST_METHOD'). ')', (!$this->_api_request ? $this->_redirect_back : null));
		}
		
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
			elseif(is_array($this->_column_order) && sizeof($this->_column_order) > 0)
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
				$max_length							= $params['max_length'];
				$content							= (isset($this->_set_default[$field]) ? $this->_set_default[$field] : $params['original']);
				$original							= $params['original'];
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
				$default_value						= ('create' == $this->_method && isset($this->_default_value[$field]) ? $this->_default_value[$field] : null);
				
				/**
				 * save primary key to be generated as token
				 */
				if($params['primary'])
				{
					$primary_key[$field]			= null;
				}
				
				if($hidden || in_array('current_timestamp', $type)) continue;
				
				if(isset($this->_set_relation[$field]) && !in_array('custom_format', $type))
				{
					$content						= $this->_get_relation($this->_set_relation[$field], $original);
				}
				elseif(isset($this->_set_autocomplete[$field]))
				{
					$content						= $this->_autocomplete_input($this->_set_autocomplete[$field], $original);
				}
				elseif(in_array('image', $type))
				{
					$content						= '
						<div data-provides="fileupload" class="fileupload fileupload-new text-sm-center">
							<span class="btn btn-light btn-file">
								<input type="file" name="' . $field . '" accept="image/*" role="image-upload" id="' . $field . '_input"' . $read_only . ' />
								<div class="fileupload-new text-center">
									<img class="img-fluid upload_preview rounded" src="' . get_image($this->_set_upload_path, ($original ? $original : $parameter), 'thumb') . '" alt="' . ($original ? $original : $parameter) . '" />
								</div>
							</span>
						</div>
					';
				}
				elseif(in_array('file', $type))
				{
					$files							= array();
					
					if('update' == $this->_method)
					{
						$original					= json_decode($original, true);
						
						if(is_array($original) && sizeof($original) > 0)
						{
							foreach($original as $src => $label)
							{
								$url				= get_file($this->_set_upload_path, $src);
								$filesize			= get_filesize($this->_set_upload_path, $src);
								$filesize			= str_replace(array('kb', 'mb', 'gb', 'b', '.'), '', strtolower($filesize));
								$files[]			= array
								(
									'name'			=> $label,
									'file'			=> $src,
									'size'			=> $filesize,
									'data'			=> array
									(
										'url'		=> $url
									)
								);
							}
						}
					}
					
					$files							= htmlspecialchars(json_encode($files));
					$content						= '<input type="file" name="' . $field . '" role="uploader" id="' . $field . '_input" data-fileuploader-files="' . $files . '" />';
				}
				elseif(in_array('images', $type) || in_array('files', $type))
				{
					$files							= array();
					
					if('update' == $this->_method)
					{
						$original					= json_decode($original, true);
						
						if(is_array($original) && sizeof($original) > 0)
						{
							foreach($original as $src => $label)
							{
								$url				= get_file($this->_set_upload_path, $src);
								$filesize			= get_filesize($this->_set_upload_path, $src);
								$filesize			= str_replace(array('kb', 'mb', 'gb', 'b', '.'), '', strtolower($filesize));
								$files[]			= array
								(
									'name'			=> $label,
									'file'			=> $src,
									'size'			=> $filesize,
									'data'			=> array
									(
										'url'		=> $url
									)
								);
							}
						}
					}
					
					$files							= htmlspecialchars(json_encode($files));
					$content						= '<input type="file" name="' . $field . '" class="multiple' . (in_array('images', $type) ? ' images' : null) . '" role="uploader" id="' . $field . '_input" data-fileuploader-files="' . $files . '" />';
				}
				elseif(in_array('attributes', $type))
				{
					$original						= json_decode($original, true);
					$items							= null;
					
					if(is_array($original) && sizeof($original) > 0)
					{
						foreach($original as $item => $attribute)
						{
							if(!isset($attribute['label']) || !isset($attribute['value'])) continue;
							$items					.= '
								<div class="row mb-1">
									<div class="col-4 pr-0">
										<input type="text" name="' . $field . '[label][]" class="form-control form-control-sm" placeholder="' . phrase('label') . '" value="' . $attribute['label'] . '" autocomplete="off" spellcheck="false" />
									</div>
									<div class="col-5 pr-0">
										<input type="text" name="' . $field . '[value][]" class="form-control form-control-sm" placeholder="' . phrase('value') . '" value="' . $attribute['value'] . '" autocomplete="off" spellcheck="false" />
									</div>
									<div class="col-3">
										<div class="btn-group btn-group-sm float-right">
											<a href="javascript:void(0)" class="btn btn-secondary' . (!$read_only ? ' --move-up' : null) . '" data-element=".row" data-toggle="tooltip" title="' . phrase('move_up') . '">
												<i class="mdi mdi-arrow-collapse-up"></i>
											</a>
											<a href="javascript:void(0)" class="btn btn-secondary' . (!$read_only ? ' --move-down' : null) . '" data-element=".row" data-toggle="tooltip" title="' . phrase('move_down') . '">
												<i class="mdi mdi-arrow-collapse-down"></i>
											</a>
											<a href="javascript:void(0)" class="btn btn-secondary"' . (!$read_only ? ' role="remove-attribute"' : null) . ' data-element=".row">
												<i class="mdi mdi-window-close" data-toggle="tooltip" title="' . phrase('remove') . '"></i>
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
								<div class="col-4 pr-0">
									<input type="text" name="' . $field . '[label][]" class="form-control form-control-sm" placeholder="' . phrase('label') . '" autocomplete="off" spellcheck="false" />
								</div>
								<div class="col-5 pr-0">
									<input type="text" name="' . $field . '[value][]" class="form-control form-control-sm" placeholder="' . phrase('value') . '" autocomplete="off" spellcheck="false" />
								</div>
								<div class="col-3">
									<div class="btn-group btn-group-sm float-right">
										<a href="javascript:void(0)" class="btn btn-secondary' . (!$read_only ? ' --move-up' : null) . '" data-element=".row" data-toggle="tooltip" title="' . phrase('move_up') . '">
											<i class="mdi mdi-arrow-collapse-up"></i>
										</a>
										<a href="javascript:void(0)" class="btn btn-secondary' . (!$read_only ? ' --move-down' : null) . '" data-element=".row" data-toggle="tooltip" title="' . phrase('move_down') . '">
											<i class="mdi mdi-arrow-collapse-down"></i>
										</a>
										<a href="javascript:void(0)" class="btn btn-secondary"' . (!$read_only ? ' role="remove-attribute"' : null) . ' data-element=".row">
											<i class="mdi mdi-window-close" data-toggle="tooltip" title="' . phrase('remove') . '"></i>
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
								<div class="col-4 pr-0">
									<button type="button" class="btn btn-secondary btn-sm btn-block"' . (!$read_only ? ' role="add-attribute"' : null) . ' data-label="' . $field . '[label][]" data-label-placeholder="' . phrase('label') . '" data-value-placeholder="' . phrase('value') . '" data-value="' . $field . '[value][]">
										<i class="mdi mdi-plus-circle-outline"></i>
										&nbsp;
										' . phrase('add') . '
									</button>
								</div>
							</div>
						</div>
					';
				}
				elseif(in_array('carousels', $type))
				{
					$original						= json_decode($original, true);
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
												<div class="form-group">
													<label class="text-muted">
														' . phrase('background') . '
													</label>
													<div data-provides="fileupload" class="fileupload fileupload-new text-sm-center">
														<span class="btn btn-light btn-file btn-block">
															<input type="file" name="' . $field . '[background][]" accept="image/*" role="image-upload" id="' . $field . '_input"' . $read_only . ' />
															<div class="fileupload-new text-center">
																<img class="img-fluid upload_preview rounded" src="' . get_image($this->_set_upload_path, (isset($item['background']) ? $item['background'] : 'placeholder.png'), 'thumb') . '" alt="..." />
															</div>
														</span>
													</div>
												</div>
											</div>
											<div class="col-md-6">
												<div class="form-group">
													<label class="text-muted">
														' . phrase('thumbnail') . '
													</label>
													<div data-provides="fileupload" class="fileupload fileupload-new text-sm-center">
														<span class="btn btn-light btn-file btn-block">
															<input type="file" name="' . $field . '[thumbnail][]" accept="image/*" role="image-upload" id="' . $field . '_input"' . $read_only . ' />
															<div class="fileupload-new text-center">
																<img class="img-fluid upload_preview rounded" src="' . get_image($this->_set_upload_path, (isset($item['thumbnail']) ? $item['thumbnail'] : 'placeholder.png'), 'thumb') . '" alt="..." />
															</div>
														</span>
													</div>
												</div>
											</div>
										</div>
										<div class="form-group">
											<input type="text" name="' . $field . '[title][]" class="form-control" placeholder="' . phrase('title') . '" value="' . (isset($item['title']) ? $item['title'] : null) . '" id="' . $field . '_input" spellcheck="false"' . $read_only . ' />
										</div>
										<div class="form-group">
											<textarea name="' . $field . '[description][]" class="form-control" placeholder="' . phrase('description') . '" id="' . $field . '_input" rows="1" spellcheck="false"' . $read_only . '>' . (isset($item['description']) ? $item['description'] : null) . '</textarea>
										</div>
										<div class="row">
											<div class="col-md-6">
												<div class="form-group">
													<input type="text" name="' . $field . '[link][]" class="form-control" placeholder="' . phrase('target_url') . '" value="' . (isset($item['link']) ? $item['link'] : null) . '" id="' . $field . '_input" spellcheck="false"' . $read_only . ' />
												</div>
											</div>
											<div class="col-md-6">
												<div class="form-group">
													<input type="text" name="' . $field . '[label][]" class="form-control" placeholder="' . phrase('button_label') . '" value="' . (isset($item['label']) ? $item['label'] : null) . '" id="' . $field . '_input" spellcheck="false"' . $read_only . ' />
												</div>
											</div>
										</div>
									</div>
									<div class="card-footer">
										<input type="hidden" name="' . $field . '[default_background][]" value="' . (isset($item['background']) ? $item['background'] : null) . '"' . $read_only . ' />
										<input type="hidden" name="' . $field . '[default_thumbnail][]" value="' . (isset($item['thumbnail']) ? $item['thumbnail'] : null) . '"' . $read_only . ' />
										<div class="btn-group btn-group-xs">
											<a href="javascript:void(0)" class="btn btn-secondary' . (!$read_only ? ' --move-up' : null) . '" data-element=".card" data-toggle="tooltip" title="' . phrase('move_up') . '">
												<i class="mdi mdi-arrow-collapse-up"></i>
											</a>
											<a href="javascript:void(0)" class="btn btn-secondary' . (!$read_only ? ' --move-down' : null) . '" data-element=".card" data-toggle="tooltip" title="' . phrase('move_down') . '">
												<i class="mdi mdi-arrow-collapse-down"></i>
											</a>
											<a href="javascript:void(0)" class="btn btn-secondary"' . (!$read_only ? ' role="remove-carousel"' : null) . ' data-element=".card">
												<i class="mdi mdi-window-close" data-toggle="tooltip" title="' . phrase('remove') . '"></i>
											</a>
										</div>
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
											<div class="form-group">
												<label class="text-muted">
													' . phrase('background') . '
												</label>
												<div data-provides="fileupload" class="fileupload fileupload-new text-sm-center">
													<span class="btn btn-light btn-file btn-block">
														<input type="file" name="' . $field . '[background][]" accept="image/*" role="image-upload" id="' . $field . '_input"' . $read_only . ' />
														<div class="fileupload-new text-center">
															<img class="img-fluid upload_preview rounded" src="' . get_image($this->_set_upload_path, 'placeholder.png', 'thumb') . '" alt="" />
														</div>
													</span>
												</div>
											</div>
										</div>
										<div class="col-md-6">
											<div class="form-group">
												<label class="text-muted">
													' . phrase('thumbnail') . '
												</label>
												<div data-provides="fileupload" class="fileupload fileupload-new text-sm-center">
													<span class="btn btn-light btn-file btn-block">
														<input type="file" name="' . $field . '[thumbnail][]" accept="image/*" role="image-upload" id="' . $field . '_input"' . $read_only . ' />
														<div class="fileupload-new text-center">
															<img class="img-fluid upload_preview rounded" src="' . get_image($this->_set_upload_path, 'placeholder.png', 'thumb') . '" alt="" />
														</div>
													</span>
												</div>
											</div>
										</div>
									</div>
									<div class="form-group">
										<input type="text" name="' . $field . '[title][]" class="form-control" placeholder="' . phrase('title') . '" id="' . $field . '_input" spellcheck="false"' . $read_only . ' />
									</div>
									<div class="form-group">
										<textarea name="' . $field . '[description][]" class="form-control" placeholder="' . phrase('description') . '" id="' . $field . '_input" rows="1" spellcheck="false"' . $read_only . '></textarea>
									</div>
									<div class="row">
										<div class="col-md-6">
											<div class="form-group">
												<input type="text" name="' . $field . '[link][]" class="form-control" placeholder="' . phrase('target_url') . '" id="' . $field . '_input" spellcheck="false"' . $read_only . ' />
											</div>
										</div>
										<div class="col-md-6">
											<div class="form-group">
												<input type="text" name="' . $field . '[label][]" class="form-control" placeholder="' . phrase('button_label') . '" id="' . $field . '_input" spellcheck="false"' . $read_only . ' />
											</div>
										</div>
									</div>
								</div>
								<div class="card-footer">
									<div class="btn-group btn-group-xs">
										<a href="javascript:void(0)" class="btn btn-secondary' . (!$read_only ? ' --move-up' : null) . '" data-element=".card" data-toggle="tooltip" title="' . phrase('move_up') . '">
											<i class="mdi mdi-arrow-collapse-up"></i>
										</a>
										<a href="javascript:void(0)" class="btn btn-secondary' . (!$read_only ? ' --move-down' : null) . '" data-element=".card" data-toggle="tooltip" title="' . phrase('move_down') . '">
											<i class="mdi mdi-arrow-collapse-down"></i>
										</a>
										<a href="javascript:void(0)" class="btn btn-secondary"' . (!$read_only ? ' role="remove-carousel"' : null) . ' data-element=".card">
											<i class="mdi mdi-window-close" data-toggle="tooltip" title="' . phrase('remove') . '"></i>
										</a>
									</div>
								</div>
							</div>
						';
					}
					
					$content						= $items . '<a href="javascript:void(0)" class="btn btn-secondary btn-sm"' . (!$read_only ? ' role="add-carousel"' : null) . ' data-field="' . $field . '" data-image-placeholder="' . get_image($this->_set_upload_path, 'placeholder.png', 'thumb') . '"><i class="mdi mdi-plus-circle-outline"></i>&nbsp;' . phrase('add') . '</a></div>';
				}
				elseif(in_array('faqs', $type))
				{
					$original						= json_decode($original, true);
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
											<div class="input-group-append">
												<a href="javascript:void(0)" class="btn btn-secondary' . (!$read_only ? ' --move-up' : null) . '" data-element=".card" data-toggle="tooltip" title="' . phrase('move_up') . '">
													<i class="mdi mdi-arrow-collapse-up"></i>
												</a>
												<a href="javascript:void(0)" class="btn btn-secondary' . (!$read_only ? ' --move-down' : null) . '" data-element=".card" data-toggle="tooltip" title="' . phrase('move_down') . '">
													<i class="mdi mdi-arrow-collapse-down"></i>
												</a>
												<a href="javascript:void(0)" class="btn btn-secondary"' . (!$read_only ? ' role="remove-faq"' : null) . ' data-element=".card">
													<i class="mdi mdi-window-close" data-toggle="tooltip" title="' . phrase('remove') . '"></i>
												</a>
											</div>
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
										<div class="input-group-append">
											<a href="javascript:void(0)" class="btn btn-secondary' . (!$read_only ? ' --move-up' : null) . '" data-element=".card" data-toggle="tooltip" title="' . phrase('move_up') . '">
												<i class="mdi mdi-arrow-collapse-up"></i>
											</a>
											<a href="javascript:void(0)" class="btn btn-secondary' . (!$read_only ? ' --move-down' : null) . '" data-element=".card" data-toggle="tooltip" title="' . phrase('move_down') . '">
												<i class="mdi mdi-arrow-collapse-down"></i>
											</a>
											<a href="javascript:void(0)" class="btn btn-secondary"' . (!$read_only ? ' role="remove-faq"' : null) . ' data-element=".card">
												<i class="mdi mdi-window-close" data-toggle="tooltip" title="' . phrase('remove') . '"></i>
											</a>
										</div>
									</div>
								</div>
								<div class="card-body p-2">
									<textarea name="' . $field . '[answer][]" class="form-control" role="wysiwyg" placeholder="' . phrase('detailed_answer_for_above_question') . '" id="' . $field . '_input" rows="1" spellcheck="false"' . $read_only . '></textarea>
								</div>
							</div>
						';
					}
					
					$content						= $items . '<a href="javascript:void(0)" class="btn btn-secondary btn-sm"' . (!$read_only ? ' role="add-faq"' : null) . ' data-field="' . $field . '"><i class="mdi mdi-plus-circle-outline"></i>&nbsp;' . phrase('add') . '</a>';
				}
				elseif(in_array('steps', $type))
				{
					$original						= json_decode($original, true);
					$items							= null;
					
					if(is_array($original) && sizeof($original) > 0)
					{
						foreach($original as $step => $item)
						{
							$items					.= '
								<div class="card mb-3">
									<div class="card-body p-2 relative">
										<div class="btn-group btn-group-xs absolute" style="top: 5px; right: 10px; z-index: 1">
											<a href="javascript:void(0)" class="btn btn-secondary' . (!$read_only ? ' --move-up' : null) . '" data-element=".card" data-toggle="tooltip" title="' . phrase('move_up') . '">
												<i class="mdi mdi-arrow-collapse-up"></i>
											</a>
											<a href="javascript:void(0)" class="btn btn-secondary' . (!$read_only ? ' --move-down' : null) . '" data-element=".card" data-toggle="tooltip" title="' . phrase('move_down') . '">
												<i class="mdi mdi-arrow-collapse-down"></i>
											</a>
											<a href="javascript:void(0)" class="btn btn-secondary"' . (!$read_only ? ' role="remove-step"' : null) . ' data-element=".card">
												<i class="mdi mdi-window-close" data-toggle="tooltip" title="' . phrase('remove') . '"></i>
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
								<div class="card-body p-2 relative">
									<div class="btn-group btn-group-xs absolute" style="top: 5px; right: 10px; z-index: 1">
										<a href="javascript:void(0)" class="btn btn-secondary' . (!$read_only ? ' --move-up' : null) . '" data-element=".card" data-toggle="tooltip" title="' . phrase('move_up') . '">
											<i class="mdi mdi-arrow-collapse-up"></i>
										</a>
										<a href="javascript:void(0)" class="btn btn-secondary' . (!$read_only ? ' --move-down' : null) . '" data-element=".card" data-toggle="tooltip" title="' . phrase('move_down') . '">
											<i class="mdi mdi-arrow-collapse-down"></i>
										</a>
										<a href="javascript:void(0)" class="btn btn-secondary"' . (!$read_only ? ' role="remove-step"' : null) . ' data-element=".card">
											<i class="mdi mdi-window-close" data-toggle="tooltip" title="' . phrase('remove') . '"></i>
										</a>
									</div>
									<textarea name="' . $field . '[]" class="form-control" role="wysiwyg" placeholder="' . phrase('add_step') . '" id="' . $field . '_input" rows="1" spellcheck="false"' . $read_only . '></textarea>
								</div>
							</div>
						';
					}
					
					$content						= $items . '<a href="javascript:void(0)" class="btn btn-secondary btn-sm"' . (!$read_only ? ' role="add-step"' : null) . ' data-field="' . $field . '"><i class="mdi mdi-plus-circle-outline"></i>&nbsp;' . phrase('add') . '</a>';
				}
				elseif(in_array('dropdown', $type) || in_array('checkbox', $type) || in_array('radio', $type))
				{
					if(in_array('dropdown', $type))
					{
						$options					= '<option value="" readonly>' . phrase('please_choose') . '</option>';
					}
					else
					{
						$options					= null;
					}
					
					if(is_array($parameter) && sizeof($parameter) > 0)
					{
						foreach($parameter as $value => $label)
						{
							if(in_array('dropdown', $type))
							{
								$options			.= '<option value="' . $value . '"' . ($default_value == $value || in_array($value, array($original, $extra_params)) ? ' selected' : null) . '>' . $label . '</option>';
							}
							elseif(in_array('checkbox', $type))
							{
								$checker			= json_decode($original, true);
								
								if(!is_array($checker))
								{
									$checker		= array($original);
								}
								
								$options			.= '
									<label class="' . $extra_class . '">
										<input type="checkbox" name="' . $field . '[]" value="' . $value . '"' . ($default_value == $value || in_array($value, $checker) ? ' checked' : null) . $read_only . ' />
										&nbsp;
										' . $label . '
										&nbsp;
									</label>
									' . $extra_params . '
								';
							}
							elseif(in_array('radio', $type))
							{
								$options			.= '
									<label>
										<input type="radio" name="' . $field . '" value="' . $value . '"' . ($default_value == $value || $value == $original ? ' checked' : null) . $read_only . ' />
										' . $label . '
										&nbsp;
									</label>
									' . $extra_params . '
								';
							}
						}
					}
					
					if(in_array('dropdown', $type))
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
				elseif(in_array('tagsinput', $type))
				{
					$content						= '<textarea name="' . $field . '" class="form-control' . $extra_class . '" role="tagsinput" placeholder="' . (isset($this->_set_placeholder[$field]) ? $this->_set_placeholder[$field] : phrase('separate_with_comma')) . '" id="' . $field . '_input"' . $read_only . ' spellcheck="false" rows="1">' . ($default_value ? $default_value : $original) . '</textarea>';
				}
				elseif(in_array('hour', $type))
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
				elseif(in_array('date_only', $type))
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
				elseif(in_array('date', $type) || in_array('datepicker', $type))
				{
					$content						= '<input type="text" name="' . $field . '" class="form-control' . $extra_class . '" role="datepicker" data-modal="true" data-large-mode="true" placeholder="' . (isset($this->_set_placeholder[$field]) ? $this->_set_placeholder[$field] : phrase('click_to_select_date')) . '" value="' . ($default_value && $default_value != '0000-00-00' ? $default_value : ($original && $original != '0000-00-00' ? $original : date('Y-m-d'))) . '" id="' . $field . '_input" maxlength="' . $max_length . '" readonly' . $read_only . ' spellcheck="false" />';
				}
				elseif(in_array('datetime', $type))
				{
					$content						= '<input type="text" name="' . $field . '" class="form-control' . $extra_class . '" role="datetime" data-modal="true" data-large-mode="true" placeholder="' . (isset($this->_set_placeholder[$field]) ? $this->_set_placeholder[$field] : phrase('click_to_select_date')) . '" value="' . ($default_value != '0000-00-00 00:00:00' ? $default_value : ($original != '0000-00-00 00:00:00' ? $original : date('Y-m-d H:i:s'))) . '" id="' . $field . '_input" maxlength="' . $max_length . '"' . $read_only . ' spellcheck="false" />';
				}
				elseif(in_array('monthpicker', $type))
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
				elseif(in_array('yearpicker', $type))
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
				elseif(in_array('quarterly', $type))
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
				elseif(in_array('colorpicker', $type))
				{
					$content						= '<div class="input-group" role="colorpicker"><input type="text" name="' . $field . '" class="form-control" value="' . ($default_value ? $default_value : $original) . '"' . $read_only . ' /><div class="input-group-append" data-toggle="tooltip" title="' . phrase('pick_a_color') . '"><span class="input-group-text">&nbsp;&nbsp;&nbsp;</span></div></div>';
				}
				elseif(in_array('wysiwyg', $type))
				{
					$content						= '<textarea name="' . $field . '" class="form-control' . $extra_class . '" role="wysiwyg" data-upload-path="' . $this->_set_upload_path . '" placeholder="' . (isset($this->_set_placeholder[$field]) ? $this->_set_placeholder[$field] : phrase('enter_the_content_here')) . '" id="' . $field . '_input"' . $attribute . ' maxlength="' . $max_length . '" rows="1" spellcheck="false"' . $read_only . '>' . ($default_value ? $default_value : $original) . '</textarea>';
				}
				elseif(in_array('textarea', $type))
				{
					$content						= '<textarea name="' . $field . '" class="form-control' . (in_array('autocomplete', $type) && $extra_params ? ' on-autocomplete-trigger' : null) . $extra_class . '" id="' . $field . '_input"' . $attribute . ' maxlength="' . $max_length . '" rows="1"' . (in_array('autocomplete', $type) ? ' role="autocomplete" data-href="' . current_page() . '"': '') . ' spellcheck="false"' . $read_only . '>' . ($default_value ? $default_value : htmlspecialchars($original)) . '</textarea>';
				}
				elseif(in_array('price_format', $type))
				{
					$content						= '<input type="text" name="' . $field . '" min="0" class="form-control text-right' . $extra_class . '" value="' . ($default_value ? $default_value : ($original ? $original : 0)) . '" role="price" placeholder="' . (isset($this->_set_placeholder[$field]) ? $this->_set_placeholder[$field] : phrase('number_only')) . '" id="' . $field . '_input" maxlength="' . $max_length . '" spellcheck="false"' . $read_only . ' />';
				}
				elseif(in_array('number_format', $type) || in_array('percent_format', $type))
				{
					$content						= '<input type="number" name="' . $field . '" min="0" class="form-control' . $extra_class . '" value="' . (is_numeric($default_value) ? number_format($default_value) : (is_numeric($original) ? number_format($original, (in_array('percent_format', $type) ? 2 : 0), '.', '') : 0)) . '" placeholder="' . (isset($this->_set_placeholder[$field]) ? $this->_set_placeholder[$field] : phrase('number_only')) . '" id="' . $field . '_input" maxlength="' . $max_length . '"' . (is_numeric($parameter) || in_array('percent_format', $type) ? ' pattern="[0-9]+([\.,][0-9]+)?" step="0.01"' : '') . $read_only . ' />';
					
					if(in_array('percent_format', $type))
					{
						$content					= '<div class="input-group">' . $content . '<div class="input-group-append"><span class="input-group-text">%</span></div></div>';
					}
				}
				elseif(in_array('boolean', $type))
				{
					$content						= '
						<label class="d-block">
							<input type="checkbox" name="' . $field . '" value="1" id="' . $field . '_input"' . ($default_value == $original || 1 == $original || 'create' == $this->_method ? ' checked' : null) . $read_only . ' />
							&nbsp;
							' . (isset($this->_set_option_label[$field]) ? $this->_set_option_label[$field] : phrase('check_to_activate')) . '
							&nbsp;
						</label>
					';
				}
				elseif(in_array('email', $type))
				{
					$content						= '<input type="email" name="' . $field . '" class="form-control' . $extra_class . '" placeholder="' . phrase('enter_your_email') . '" value="' . ($default_value ? $default_value : $original) . '" id="' . $field . '_input" maxlength="' . $max_length . '"' . $read_only . ' />';
				}
				elseif(in_array('password', $type))
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
				elseif(in_array('encryption', $type))
				{
					$content						= '<input type="password" name="' . $field . '" class="form-control' . $extra_class . '" value="" id="' . $field . '_input"' . ('update' == $this->_method ? ' placeholder="' . phrase('leave_blank_to_ignore') . '"' : null) . ' maxlength="' . $max_length . '"' . $read_only . ' />';
				}
				elseif(in_array('hidden', $type))
				{
					$content						= '<input type="hidden" name="' . $field . '" value="' . ($default_value ? $default_value : htmlspecialchars($original)) . $read_only . '" />';
				}
				elseif(in_array('custom_format', $type))
				{
					$callback						= str_replace('callback_', null, $parameter);
					
					if(method_exists($this, $callback))
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
				elseif(in_array('last_insert', $type))
				{
					if(!$default_value)
					{
						$type_key					= array_search('{1}', explode('/', $parameter));
						
						if('create' == $this->_method)
						{
							$where					= array();
							
							if($this->_where)
							{
								foreach($this->_where as $k => $v)
								{
									if($this->model->field_exists($k, $this->_from))
									{
										$where[$k]	= $v;
									}
								}
							}
							
							if($skip)
							{
								if(is_array($parameter))
								{
									$this->model->where($parameter);
								}
								
								$last_insert		= $this->model->select('IFNULL(MAX(CONVERT(' . $field . ', SIGNED INTEGER)), 0) AS ' . $field)->order_by($field, 'desc')->get($this->_from, 1)->row($field);
							}
							else
							{
								$last_insert		= $this->model->select('IFNULL(MAX(CONVERT(' . $field . ', SIGNED INTEGER)), 0) AS ' . $field)->order_by($field, 'desc')->get_where($this->_from, $where, 1)->row($field);
							}
							
							$last_insert			= explode('/', $last_insert);
							$last_insert			= (isset($last_insert[$type_key]) ? $last_insert[$type_key] : $last_insert[0]);
							$last_insert			= preg_replace('/[^0-9]/', '', $last_insert);
							
							if('create' == $this->_method)
							{
								$last_insert		= ($last_insert > 0 ? $last_insert : 0) + 1;
							}
							
							$original				= ($last_insert > 0 ? $last_insert : 1);
							
							if(in_array('sprintf', $type))
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
							if(in_array('sprintf', $type))
							{
								$original			= sprintf((is_string($extra_params) ? $extra_params : '%04d'), $original);
							}
						}
					}
					
					$content						= '<input type="text" name="' . $field . '" class="form-control' . $extra_class . '" value="' . ($default_value ? $default_value : $original) . '" id="' . $field . '_input" maxlength="' . $max_length . '" spellcheck="false"' . $read_only . ' />';
				}
				elseif(in_array('to_slug', $type))
				{
					$content						= '<input type="text" name="' . $field . '" class="form-control' . $extra_class . '" value="' . ($default_value ? $default_value : $original) . '" placeholder="' . (!$read_only ? phrase('leave_blank_to_generate_automatically') : null) . '" id="' . $field . '_input" maxlength="' . $max_length . '" spellcheck="false"' . $read_only . ' />';
				}
				elseif(in_array('coordinate', $type))
				{
					$content						= '
						<div class="drawing-placeholder preloader relative w-100" style="overflow:hidden">
							<div id="map_' . $field . rand(0, 999) . '"' . ($extra_class ? ' class="' . $extra_class . '"' : null) . ' role="map" data-coordinate="' . strip_tags(htmlspecialchars(($original && is_json($original) ? $original : (get_setting('office_map') ? get_setting('office_map') : '[]')))) . '" data-apply-coordinate-to="#' . $field . '_input" data-apply-address-to=".map-address-listener"' . (!$read_only ? ' data-draggable="1"' : null) . ' data-geocoder="1"' . (!$original ? ' data-geolocation="1"' : null) . ' data-mousewheel="0" title="' . phrase('drag_marker_to_update_the_location') . '"' . (isset($this->_set_attribute[$field]) ? ' ' . $this->_set_attribute[$field] : null) . ' style="height:260px"></div>
							<input type="hidden" name="' . $field . '" id="' . $field . '_input" value="' . strip_tags(htmlspecialchars(($original && is_json($original) ? $original : (get_setting('office_map') ? get_setting('office_map') : '[]')))) . '"' . $read_only . ' />
						</div>
					';
				}
				elseif(in_array('point', $type))
				{
					$content						= '
						<div class="drawing-placeholder preloader relative w-100" style="overflow:hidden">
							<div id="map_' . $field . rand(0, 999) . '"' . ($extra_class ? ' class="' . $extra_class . '"' : null) . ' role="map" data-coordinate="' . strip_tags(htmlspecialchars(get_setting('office_map'))) . '" data-geojson="' . strip_tags(htmlspecialchars(($original && is_json($original) ? $original : '[]'))) . '" data-apply-coordinate-to="#' . $field . '_input"' . (!$read_only && in_array($this->_method, array('create', 'update')) ? ' data-drawing-manager="1" data-drawing-type="point"' : null) . ' data-geocoder="1"' . (!$original ? ' data-geolocation="1"' : null) . ' data-mousewheel="0" title="' . phrase('click_map_to_start_drawing') . '"' . (isset($this->_set_attribute[$field]) ? ' ' . $this->_set_attribute[$field] : null) . ' style="height:360px"></div>
							<input type="hidden" name="' . $field . '" id="' . $field . '_input" value="' . strip_tags(htmlspecialchars(($original && is_json($original) ? $original : '[]'))) . '"' . $read_only . ' />
						</div>
					';
				}
				elseif(in_array('polygon', $type))
				{
					$content						= '
						<div class="drawing-placeholder preloader relative w-100" style="overflow:hidden">
							<div id="map_' . $field . rand(0, 999) . '"' . ($extra_class ? ' class="' . $extra_class . '"' : null) . ' role="map" data-coordinate="' . strip_tags(htmlspecialchars(get_setting('office_map'))) . '" data-geojson="' . strip_tags(htmlspecialchars(($original && is_json($original) ? $original : '[]'))) . '" data-apply-coordinate-to="#' . $field . '_input"' . (!$read_only && in_array($this->_method, array('create', 'update')) ? ' data-drawing-manager="1" data-drawing-type="polygon"' : null) . ' data-geocoder="1"' . (!$original ? ' data-geolocation="1"' : null) . ' data-mousewheel="0" title="' . phrase('click_map_to_start_drawing') . '"' . (isset($this->_set_attribute[$field]) ? ' ' . $this->_set_attribute[$field] : null) . ' style="height:360px"></div>
							<input type="hidden" name="' . $field . '" id="' . $field . '_input" value="' . strip_tags(htmlspecialchars(($original && is_json($original) ? $original : '[]'))) . '"' . $read_only . ' />
						</div>
					';
				}
				elseif(in_array('linestring', $type))
				{
					$content						= '
						<div class="drawing-placeholder preloader relative w-100" style="overflow:hidden">
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
					'label'							=> $alias,
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
					
					$fields[$field]['value']		= (service('request')->getHeaderLine('X-API-KEY') != sha1(ENCRYPTION_KEY . service('request')->getHeaderLine('X-ACCESS-TOKEN')) ? $original : null);
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
			'query_string'							=> array_replace(service('request')->getGet(), $primary_key)
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
				$field_order						= array();
				
				foreach($this->_view_order as $order_key => $order_val)
				{
					if(array_key_exists($order_val, $serialized))
					{
						$field_order[]				= $order_val;
					}
				}
				
				$serialized							= array_replace(array_flip($field_order), $serialized);
			}
			elseif(is_array($this->_column_order) && sizeof($this->_column_order) > 0)
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
				$content							= $params['content'];
				$original							= $params['original'];
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
				if($params['primary'])
				{
					$primary_key[$field]			= null;
				}
				
				if($hidden) continue;
				
				if(isset($this->_set_relation[$field]))
				{
					$content						= $this->_get_relation($this->_set_relation[$field], $content);
				}
				elseif(isset($this->_set_autocomplete[$field]))
				{
					$content						= $this->_autocomplete_input($this->_set_autocomplete[$field], $original);
				}
				elseif(isset($this->_merge_content[$field]))
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
							$content				= str_replace('{' . $magic . '}', null, $content);
						}
					}
					
					if($this->_merge_content[$field]['callback'] && method_exists($this, $this->_merge_content[$field]['callback']))
					{
						$callback					= $this->_merge_content[$field]['callback'];
						$content					= $this->$callback($_calback);
					}
				}
				elseif(in_array('custom_format', $type))
				{
					$callback						= str_replace('callback_', null, $parameter);
					
					if(method_exists($this, $callback))
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
				elseif(in_array('image', $type))
				{
					$content						= '
						<div class="text-center">
							<a href="' . get_image($this->_set_upload_path, $original) . '" target="_blank">
								<img src="' . get_image($this->_set_upload_path, $original, 'thumb') . '" alt="' . $original . '" class="img-fluid rounded" />
							</a>
						</div>
					';
				}
				elseif(in_array('images', $type))
				{
					$original						= json_decode($original, true);
					$items							= null;
					
					if(is_array($original) && sizeof($original) > 0)
					{
						foreach($original as $img => $src)
						{
							$items					.= '
								<div class="col-md-3 col-xs-6 text-sm">
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
				elseif(in_array('file', $type))
				{
					$original						= json_decode($original, true);
					
					if(is_array($original) && sizeof($original) > 0)
					{
						foreach($original as $src => $label)
						{
							$filesize				= get_filesize($this->_set_upload_path, $src);
							$content				= '
								<a href="' . get_file($this->_set_upload_path, $src) . '" target="_blank" data-toggle="tooltip" title="' . $filesize . ', ' . phrase('click_to_open') . '">
									' . ($label ? $label : $src) . '
								</a>
							';
						}
					}
				}
				elseif(in_array('files', $type))
				{
					$images							= null;
					$files							= null;
					$original						= json_decode($original, true);
					
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
									<a href="' . get_file($this->_set_upload_path, $src) . '" target="_blank" data-toggle="tooltip" title="' . $filesize . ', ' . phrase('click_to_open') . '">
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
				elseif(in_array('attributes', $type))
				{
					$items							= null;
					$original						= json_decode($original, true);
					
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
				elseif(in_array('carousels', $type))
				{
					$items							= null;
					$original						= json_decode($original, true);
					
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
						<div id="carousel_' . $field . '" class="carousel slide" data-ride="carousel">
							<div class="carousel-inner">
								' . $items . '
							</div>
							<a class="carousel-control-prev" href="#carousel_' . $field . '" role="button" data-slide="prev">
								<span class="carousel-control-prev-icon" aria-hidden="true"></span>
								<span class="sr-only">' . phrase('previous') . '</span>
							</a>
							<a class="carousel-control-next" href="#carousel_' . $field . '" role="button" data-slide="next">
								<span class="carousel-control-next-icon" aria-hidden="true"></span>
								<span class="sr-only">' . phrase('next') . '</span>
							</a>
						</div>
					';
				}
				elseif(in_array('faqs', $type))
				{
					$items							= null;
					$original						= json_decode($original, true);
					
					if(is_array($original) && sizeof($original) > 0)
					{
						foreach($original as $faq => $value)
						{
							$items					.= '
								<div class="card">
									<div class="card-header" id="heading_' . $faq . '">
										<a href="#" class="d-block font-weight-bold" data-toggle="collapse" data-target="#collapse_' . $faq . '" aria-expanded="true" aria-controls="collapse_' . $faq . '">
											' . (isset($value['question']) ? $value['question'] : null) . '
										</a>
									</div>
									<div id="collapse_' . $faq . '" class="collapse" aria-labelledby="heading_' . $faq . '" data-parent="#accordion_' . $field . '">
										<div class="card-body">
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
				elseif(in_array('tagsinput', $type))
				{
					$original						= array_map('trim', explode(',', $original));
					$items							= null;
					
					if(is_array($original) && sizeof($original) > 0)
					{
						foreach($original as $tag => $value)
						{
							$items					.= '<span class="badge badge-info">' . $value . '</span>';
						}
					}
					
					$content						= $items;
				}
				elseif($original && (in_array('datetime', $type) || in_array('current_timestamp', $type)))
				{
					if($original != '0000-00-00 00:00:00')
					{
						$timestamp					= strtotime($original);
						$month						= date('F', $timestamp);
						$month						= phrase($month);
						$content					= date('d', $timestamp) . ' ' . $month . ' ' . date('Y', $timestamp) . ',' . date('H:i:s', $timestamp);
					}
					else
					{
						$content					= '&nbsp;';
					}
				}
				elseif(in_array('date', $type) || in_array('datepicker', $type))
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
				elseif(in_array('hour', $type))
				{
					$content						= sprintf('%02d', (24 == $original ? '00' : $original)) . ':00';
				}
				elseif(in_array('date_only', $type))
				{
					$content						= sprintf('%02d', $original);
				}
				elseif(in_array('monthpicker', $type))
				{
					$month							= date('F', strtotime($content));
					$month							= phrase($month);
					$content						= $month . ' ' . date('Y', strtotime($content));
				}
				elseif(in_array('quarterly', $type))
				{
					$month							= date('m', strtotime($content));
					$month							= phrase($month);
					$content						= $month . ' ' . date('Y', strtotime($content));
				}
				elseif(in_array('dropdown', $type) || in_array('checkbox', $type) || in_array('radio', $type))
				{
					if(in_array('radio', $type) && isset($parameter[$original]))
					{
						$content					= $parameter[$original];
					}
					elseif(in_array('checkbox', $type))
					{
						$json						= json_decode($content, true);
						
						if(is_array($json) && sizeof($json) > 0)
						{
							$items					= null;
							
							foreach($json as $_key => $_val)
							{
								if(!is_array($_val) && isset($this->_set_field[$field]['parameter'][$_val]))
								{
									$items				.= '<span class="badge badge-info">' . $this->_set_field[$field]['parameter'][$_val] . '</span> ';
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
						$items						= json_decode($content, true);
						
						if($extra_params)
						{
							$content				= $original;
						}
						elseif(isset($parameter[$original]))
						{
							$content				= $parameter[$original];
						}
						elseif($items)
						{
							$content				= (is_array($items) ? sizeof($items) : 0);
							$content				= $content . ' ' . ($content > 0 ? phrase('options') : phrase('option'));
						}
					}
				}
				elseif(in_array('colorpicker', $type))
				{
					$content						= '<span class="badge" style="background:' . $original . '">' . $original . '</span>';
				}
				elseif(in_array('boolean', $type))
				{
					$content						= ($content == 1 ? '<span class="badge badge-success">' . phrase('active') . '</span>' : '<span class="badge badge-danger">' . phrase('inactive') . '</span>');
				}
				elseif(in_array('last_insert', $type))
				{
					if(in_array('sprintf', $type))
					{
						$content					= sprintf((is_string($extra_params) ? $extra_params : '%04d'), $original);
					}
				}
				elseif(in_array('textarea', $type))
				{
					$content						= preg_replace('/\n/', '<br />', preg_replace('/(\s{4})\s+/','$1', $content));
				}
				elseif(in_array('email', $type))
				{
					$content						= '<a href="mailto:' . $content . '">' . $content . '</a>';
				}
				elseif(in_array('password', $type) || in_array('encryption', $type))
				{
					$content						= '******';
				}
				elseif(in_array('coordinate', $type))
				{
					$content						= '
						<div class="drawing-placeholder preloader relative w-100" style="overflow:hidden">
							<div id="' . $field . rand(0, 999) . '"' . ($extra_class ? ' class="' . $extra_class . '"' : null) . ' role="map" data-draggable="false" data-coordinate="' . strip_tags(htmlspecialchars(($original && is_json($original) ? $original : (get_setting('office_map') ? get_setting('office_map') : '[]')))) . '" style="height:260px"></div>
						</div>
					';
				}
				elseif(in_array('point', $type))
				{
					$content						= '
						<div class="drawing-placeholder preloader relative w-100" style="overflow:hidden">
							<div id="' . $field . rand(0, 999) . '"' . ($extra_class ? ' class="' . $extra_class . '"' : null) . ' role="map" data-coordinate="' . strip_tags(htmlspecialchars(get_setting('office_map'))) . '" data-geojson="' . strip_tags(htmlspecialchars(($original && is_json($original) ? $original : '[]'))) . '"' . (isset($this->_set_attribute[$field]) ? ' ' . $this->_set_attribute[$field] : null) . ' style="height:260px"></div>
						</div>
					';
				}
				elseif(in_array('polygon', $type))
				{
					$content						= '
						<div class="drawing-placeholder preloader relative w-100" style="overflow:hidden">
							<div id="' . $field . rand(0, 999) . '"' . ($extra_class ? ' class="' . $extra_class . '"' : null) . ' role="map" data-coordinate="' . strip_tags(htmlspecialchars(get_setting('office_map'))) . '" data-geojson="' . strip_tags(htmlspecialchars(($original && is_json($original) ? $original : '[]'))) . '"' . (isset($this->_set_attribute[$field]) ? ' ' . $this->_set_attribute[$field] : null) . ' style="height:260px"></div>
						</div>
					';
				}
				elseif(in_array('linestring', $type))
				{
					$content						= '
						<div class="drawing-placeholder preloader relative w-100" style="overflow:hidden">
							<div id="' . $field . rand(0, 999) . '"' . ($extra_class ? ' class="' . $extra_class . '"' : null) . ' role="map" data-coordinate="' . strip_tags(htmlspecialchars(get_setting('office_map'))) . '" data-geojson="' . strip_tags(htmlspecialchars(($original && is_json($original) ? $original : '[]'))) . '"' . (isset($this->_set_attribute[$field]) ? ' ' . $this->_set_attribute[$field] : null) . ' style="height:260px"></div>
						</div>
					';
				}
				else
				{
					$json							= json_decode($content, true);
					
					if(json_last_error() === JSON_ERROR_NONE && is_array($json) && sizeof($json) > 0)
					{
						$content					= $this->_humanize_array($json);
					}
				}
				
				if(in_array('hyperlink', $type))
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
							$content				= '
								<a href="' . (isset($this->_set_field[$field]['parameter']) && $this->_set_field[$field]['parameter'] ? base_url((is_array($this->_set_field[$field]['parameter']) && sizeof($this->_set_field[$field]['parameter']) > 1 ? $this->_set_field[$field]['parameter'][$hyperlink_params] : $this->_set_field[$field]['parameter']), $uri) : 'http://' . str_replace(array('http://', 'https://'), array(null, null), $original)) . '"' . ('_blank' == $another_params ? ' target="_blank"' : ' class="' . ($another_params ? $another_params : '--xhr') . '"') . ' style="display:block">
									<b data-toggle="tooltip" title="' . phrase('click_to_open') . '">
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
					'label'							=> $alias,
					'content'						=> $content,
					'original'						=> $original,
					'position'						=> $position
				);
				
				if($this->_api_request)
				{
					unset($fields[$field]['required'], $fields[$field]['content'], $fields[$field]['original'], $fields[$field]['position']);
					
					$fields[$field]['value']		= (service('request')->getHeaderLine('X-API-KEY') == sha1(ENCRYPTION_KEY . service('request')->getHeaderLine('X-ACCESS-TOKEN')) ? null : $original);
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
		
		$output										= array
		(
			'column_size'							=> $this->_column_size,
			'column_total'							=> $highest_column,
			'field_size'							=> $this->_field_size,
			'form_data'								=> $fields,
			'merged_content'						=> $this->_merge_content,
			'merged_field'							=> $this->_merge_field,
			'query_string'							=> array_replace(service('request')->getGet(), $primary_key)
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
		if(!$this->_crud)
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
					$content						= $params['content'];
					$original						= $params['original'];
					$parameter						= $params['parameter'];
					$extra_params					= (isset($this->_set_field[$field]['extra_params']) ? $this->_set_field[$field]['extra_params'] : null);
					$another_params					= (isset($this->_set_field[$field]['another_params']) ? $this->_set_field[$field]['another_params'] : null);
					$hidden							= $params['hidden'];
					
					if(!$hidden)
					{
						$column_lib[]				= $field;
					}
					
					if(isset($this->_set_relation[$field]))
					{
						$content					= $this->_get_relation($this->_set_relation[$field], $original);
					}
					elseif(isset($this->_set_autocomplete[$field]))
					{
						$content					= $this->_autocomplete_input($this->_set_autocomplete[$field], $original);
					}
					elseif(isset($this->_merge_content[$field]))
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
								$content			= str_replace('{' . $magic . '}', null, $content);
							}
						}
						
						if($this->_merge_content[$field]['callback'] && method_exists($this, $this->_merge_content[$field]['callback']))
						{
							$callback				= $this->_merge_content[$field]['callback'];
							$content				= $this->$callback($_calback);
						}
					}
					elseif(in_array('custom_format', $type))
					{
						$callback					= str_replace('callback_', null, $parameter);
					
						if(method_exists($this, $callback))
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
					elseif(in_array('image', $type))
					{
						$content					= '
							<a href="' . get_image($this->_set_upload_path, $original) . '" target="_blank">
								<img src="' . ($original ? get_image($this->_set_upload_path, $original, 'icon') : ($parameter ? str_replace('/thumbs/', '/icons/', $parameter) : get_image($this->_set_upload_path, 'placeholder.png', 'icon'))) . '" alt="' . ($original ? $original : ($parameter ? str_replace('/thumbs/', '/icons/', $parameter) : 'placeholder.png')) . '" class="img-fluid rounded" style="height: 22px" />
							</a>
						';
					}
					elseif(in_array('file', $type))
					{
						$original					= json_decode($original);
						
						if($original)
						{
							foreach($original as $src => $label)
							{
								$filesize			= get_filesize($this->_set_upload_path, $src);
								$content			= '
									<a href="' . get_file($this->_set_upload_path, $src) . '" target="_blank" data-toggle="tooltip" title="' . $filesize . ', ' . phrase('click_to_open') . '">
										<b>
											' . truncate($label, 10) . '
										</b>
									</a>
								';
							}
						}
					}
					elseif(in_array('images', $type) || in_array('files', $type))
					{
						$content					= sizeof((is_array(json_decode($content, true)) ? json_decode($content, true) : array()));
						
						if($content > 1)
						{
							$content				= '<span class="badge badge-info">' . $content . ' ' . (in_array('images', $type) ? phrase('images') : phrase('files')) . '</span>';
						}
						else
						{
							$content				= '<span class="badge badge-info">' . $content . ' ' . (in_array('image', $type) ? phrase('images') : phrase('file')) . '</span>';
						}
					}
					elseif(in_array('attributes', $type))
					{
						$content					= sizeof((is_array(json_decode($content, true)) ? json_decode($content, true) : array()));
						$content					= ($content > 0 ? '<span class="badge badge-light">' . $content . ' ' . ($content > 1 ? phrase('attributes') : phrase('attribute')) . '</span>' : '<span class="badge badge-warning">' . phrase('not_set') . '</span>');
					}
					elseif(in_array('carousels', $type) || in_array('faqs', $type))
					{
						$content					= sizeof((is_array(json_decode($content, true)) ? json_decode($content, true) : array()));
						$content					= ($content > 0 ? '<span class="badge badge-light">' . $content . ' ' . ($content > 1 ? phrase('items') : phrase('item')) . '</span>' : '<span class="badge badge-warning">' . phrase('not_set') . '</span>');
					}
					elseif($original && (in_array('datetime', $type) || in_array('current_timestamp', $type)))
					{
						if($original != '0000-00-00 00:00:00')
						{
							$timestamp				= strtotime($original);
							$month					= date('F', $timestamp);
							$month					= phrase($month);
							$content				= '<span data-toggle="tooltip" title="' . date('d', $timestamp) . ' ' . $month . ' ' . date('Y - H:i:s', $timestamp) . '">' . date('d', $timestamp) . ' ' . $month . ' ' . date('Y', $timestamp) . '</span>';
						}
						else
						{
							$content				= '&nbsp;';
						}
					}
					elseif(in_array('date', $type) || in_array('datepicker', $type))
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
					elseif(in_array('hour', $type))
					{
						$content					= sprintf('%02d', (24 == $original ? '00' : $original)) . ':00';
					}
					elseif(in_array('date_only', $type))
					{
						$content					= sprintf('%02d', $original);
					}
					elseif(in_array('monthpicker', $type))
					{
						$month						= date('F', strtotime($content));
						$month						= phrase($month);
						$content					= $month . ' ' . date('Y', strtotime($content));
					}
					elseif(in_array('quarterly', $type))
					{
						$month						= date('m', strtotime($content));
						$month						= ($month);
						
						if($month == 1)
						{
							$content				= phrase('quarter') . ' 1';
						}
						elseif($month == 4)
						{
							$content				= phrase('quarter') . ' 2';
						}
						elseif($month == 7)
						{
							$content				= phrase('quarter') . ' 3';
						}
						elseif($month == 10)
						{
							$content				= phrase('quarter') . ' 4';
						}
						else
						{
							$content				= phrase('not_a_valid_quarter');
						}
					}
					elseif(in_array('dropdown', $type) || in_array('checkbox', $type) || in_array('radio', $type))
					{
						if(in_array('radio', $type) && isset($parameter[$original]))
						{
							$content				= $parameter[$original];
						}
						elseif(in_array('checkbox', $type))
						{
							$json					= json_decode($content, true);
							
							if(is_array($json) && sizeof($json) > 0)
							{
								$items				= null;
								
								foreach($json as $_key => $_val)
								{
									if(!is_array($_val) && isset($this->_set_field[$field]['parameter'][$_val]))
									{
										$items		.= '<span class="badge badge-info">' . $this->_set_field[$field]['parameter'][$_val] . '</span> ';
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
							$items					= json_decode($content, true);
							
							if($extra_params)
							{
								$content			= $original;
							}
							elseif(isset($parameter[$original]))
							{
								$content			= $parameter[$original];
							}
							elseif($items)
							{
								$content			= (is_array($items) ? sizeof($items) : 0);
								$content			= $content . ' ' . ($content > 0 ? phrase('options') : phrase('option'));
							}
						}
					}
					elseif(in_array('colorpicker', $type))
					{
						$content					= '<span class="badge" style="background:' . $original . '">' . $original . '</span>';
					}
					elseif(in_array('boolean', $type))
					{
						$content					= ($content == 1 ? '<span class="badge badge-success">' . phrase('active') . '</span>' : '<span class="badge badge-danger">' . phrase('inactive') . '</span>');
					}
					elseif(in_array('last_insert', $type))
					{
						if(in_array('sprintf', $type))
						{
							$content				= sprintf((is_string($extra_params) ? $extra_params : '%04d'), $original);
						}
					}
					elseif(in_array('textarea', $type))
					{
						$content					= preg_replace('/\n/', ' ', preg_replace('/(\s{4})\s+/','$1', $content));
					}
					elseif(in_array('email', $type))
					{
						$content					= '<a href="mailto:' . $original . '">' . $content . '</a>';
					}
					elseif(in_array('password', $type) || in_array('encryption', $type))
					{
						$content					= '******';
					}
					else
					{
						if(!isset($this->_merge_content[$field]))
						{
							$content				= (!in_array($field, $this->_unset_truncate) && in_array($this->_method, array('index')) ? truncate($content, 60) : $content);
						}
						
						if(in_array('to_slug', $type))
						{
							$content				= $original;
						}
					}
					
					if(in_array('number_format', $type) || in_array('price_format', $type) || in_array('percent_format', $type))
					{
						if(in_array('price_format', $type) || in_array('percent_format', $type))
						{
							$parameter				= (strpos($content, '.00') !== false ? 0 : 2);
						}
						
						$content					= '<p class="text-md-right m-0" style="padding-right:15px">' . (is_numeric($content) ? number_format($content, (is_numeric($parameter) ? $parameter : 0)) : $content) . '</p>';
					}
					
					if(in_array('hyperlink', $type))
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
								$content			= '
									<a href="' . (isset($this->_set_field[$field]['parameter']) && $this->_set_field[$field]['parameter'] ? base_url((is_array($this->_set_field[$field]['parameter']) && sizeof($this->_set_field[$field]['parameter']) > 1 ? $this->_set_field[$field]['parameter'][$hyperlink_params] : $this->_set_field[$field]['parameter']), $uri) : 'http://' . str_replace(array('http://', 'https://'), array(null, null), $original)) . '"' . ('_blank' == $another_params ? ' target="_blank"' : ' class="' . ($another_params ? $another_params : '--xhr') . '"') . ' style="display:block">
										<b data-toggle="tooltip" title="' . phrase('click_to_open') . '">
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
					
					if($this->_api_request)
					{
						unset($fields[$field]['content'], $fields[$field]['original']);
						
						$fields[$field]['type']		= $type;
						$fields[$field]['value']	= (service('request')->getHeaderLine('X-API-KEY') == sha1(ENCRYPTION_KEY . service('request')->getHeaderLine('X-ACCESS-TOKEN')) ? 'mixed' : $original);
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
				
				if($primary_key)
				{
					unset($uri_parameter['aksara']);
					
					$uri_parameter					= array_merge(array('aksara' => generate_token(array_filter(array_merge($uri_parameter, $primary_key)))), $uri_parameter, $primary_key);
				}
				
				$query_string[]						= $uri_parameter;
				
				if($this->_api_request && service('request')->getHeaderLine('X-API-KEY') == sha1(ENCRYPTION_KEY . service('request')->getHeaderLine('X-ACCESS-TOKEN')))
				{
					break;
				}
			}
		}
		
		$columns									= array();
		$search_columns								= $this->model->list_fields($this->_from);
		
		if(!$search_columns)
		{
			$search_columns							= array();
		}
		
		if($this->_select)
		{
			$search_columns							= array_merge($search_columns, $this->_select);
		}
		
		if($column_lib)
		{
			$search_columns							= array_merge($search_columns, $column_lib);
		}
		
		if($search_columns)
		{
			$qs										= service('request')->getGet();
			
			unset($qs['aksara']);
			
			foreach($search_columns as $key => $val)
			{
				if(stripos($val, '.') !== false)
				{
					$val							= str_replace('.', '', strstr($val, '.'));
				}
				
				if(stripos(trim($val), ' AS ') !== false)
				{
					$explode						= preg_split('/ AS /i', $val);
					
					if(is_array($explode) && isset($explode[1]))
					{
						$val						= $explode[1];
					}
				}
				
				if(!in_array($val, $this->_unset_column))
				{
					$columns[$val]					= array
					(
						'field'						=> $val,
						'label'						=> (isset($this->_merge_label[$val]) ? $this->_merge_label[$val] : (isset($this->_set_alias[$val]) ? $this->_set_alias[$val] : ucwords(str_replace('_', ' ', $val)))),
						'aksara'					=> generate_token(($qs ? array_merge($qs, array('order' => $val, 'sort' => get_userdata('sortOrder'))) : array('order' => $val, 'sort' => get_userdata('sortOrder')))),
						'sort'						=> get_userdata('sortOrder'),
						'align'						=> (isset($this->_set_field[$val]['field_type']) && (in_array('number_format', $this->_set_field[$val]['field_type']) || in_array('price_format', $this->_set_field[$val]['field_type']) || in_array('percent_format', $this->_set_field[$val]['field_type'])) ? 'right' : null)
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
			'query_string'							=> $query_string,
			'extra_action'							=> array
			(
				'toolbar'							=> $this->_extra_toolbar,
				'option'							=> $option,
				'dropdown'							=> $dropdown
			),
			'columns'								=> $columns
		);
		
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
		if(!$data && $this->model->table_exists($this->_from))
		{
			$this->_data							= false;
			$data									= array(array_flip($this->model->list_fields($this->_from)));
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
					
					if(in_array($key, $this->_set_primary) || (isset($field_data[$key]['primary_key']) && $field_data[$key]['primary_key'] === 1))
					{
						if(!in_array($key, $this->_set_primary))
						{
							$this->_set_primary[]	= $key;
						}
					}
					
					if($this->_method == 'create')
					{
						$content					= (isset($this->_set_default[$key]) ? $this->_set_default[$key] : (isset($field_data[$key]['default']) ? $field_data[$key]['default'] : null));
						$original					= null;
					}
					
					if(in_array($this->_method, array('create', 'update')) && in_array($key, $this->_unset_field))
					{
						$hidden						= true;
					}
					elseif(('read' == $this->_method || (in_array($this->_method, array('print', 'pdf')))) && in_array($key, $this->_unset_view))
					{
						$hidden						= true;
					}
					elseif(in_array($this->_method, array('index', 'export', 'print', 'pdf')) && ($this->_where && !in_array($this->_method, array('index')) ? in_array($key, $this->_unset_view) : in_array($key, $this->_unset_column)))
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
					
					if($content && (in_array('number_format', $type) || in_array('percent_format', $type) || in_array('price_format', $type)))
					{
						if(in_array('percent_format', $type))
						{
							$content				= (is_numeric($content) ? number_format($content, (strpos($content, '.00') !== false ? 0 : 2)) : $content) . '%';
						}
						elseif(in_array('price_format', $type))
						{
							$content				= (is_numeric($content) ? number_format($content, (strpos($content, '.00') !== false ? 0 : 2)) : $content);
						}
						else
						{
							$content				= (is_numeric($content) ? number_format($content, ($parameter ? $parameter : 0)) : $content);
						}
					}
					
					if($content && in_array('sprintf', $type))
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
	 * Run the SQL command string
	 */
	public function query($query = null)
	{
		$this->_query								= $query;
		
		return $this;
	}
	
	/**
	 * Distinct field
	 */
	public function distinct($val = null)
	{
		if(!is_array($val))
		{
			$val									= array_map('trim', explode(',', $val));
		}
		
		$this->_distinct							= array_merge($this->_distinct, $val);
		
		return $this;
	}
	
	/**
	 * Select field
	 * Possible to use comma separated
	 */
	public function select($select = null, $escape = true)
	{
		if(!is_array($select))
		{
			// split selected by comma, but ignore that inside brackets
			$select									= array_map('trim', preg_split('/,(?![^(]+\))/', $select));
		}
		
		$this->_select								= array_merge($this->_select, $select);
		
		return $this;
	}
	
	/**
	 * Select and Sum
	 * Possible to use comma separated
	 */
	public function select_sum($select = null, $alias = null)
	{
		if(!is_array($select))
		{
			$this->_select_sum[$select]				= ($alias ? $alias : $select);
		}
		else
		{
			foreach($select as $key => $val)
			{
				$this->_select_sum[$key]			= $val;
			}
		}
		
		return $this;
	}
	
	/**
	 * Select Minimum
	 * Possible to use comma separated
	 */
	public function select_min($select = null, $alias = null)
	{
		if(!is_array($select))
		{
			$this->_select_min[$select]				= ($alias ? $alias : $select);
		}
		else
		{
			foreach($select as $key => $val)
			{
				$this->_select_min[$key]			= $val;
			}
		}
		
		return $this;
	}
	
	/**
	 * Select Maximum
	 * Possible to use comma separated
	 */
	public function select_max($select = null, $alias = null)
	{
		if(!is_array($select))
		{
			$this->_select_max[$select]				= ($alias ? $alias : $select);
		}
		else
		{
			foreach($select as $key => $val)
			{
				$this->_select_max[$key]			= $val;
			}
		}
		
		return $this;
	}
	
	/**
	 * Select Average of field
	 * Possible to use comma separated
	 */
	public function select_avg($select = null, $alias = null)
	{
		if(!is_array($select))
		{
			$this->_select_avg[$select]				= ($alias ? $alias : $select);
		}
		else
		{
			foreach($select as $key => $val)
			{
				$this->_select_avg[$key]			= $val;
			}
		}
		
		return $this;
	}
	
	/**
	 * Set the primary table
	 */
	public function from($table = null)
	{
		$this->_table								= $table;
		
		return $this;
	}
	
	/**
	 * Set the primary table
	 * It's similar to from() method
	 */
	public function table($table = null)
	{
		$this->_table								= $table;
		
		return $this;
	}
	
	/**
	 * Join table
	 * Your contribution is needed to write hint about
	 * this method
	 */
	public function join($table = null, $condition = null, $type = '', $escape = true)
	{
		if(strpos(trim($table), ' ') !== false)
		{
			$table									= str_ireplace(' AS ', ' ', $table);
			$destructure							= explode(' ', $table);
			
			$this->_table_alias[$destructure[1]]	= $destructure[0];
		}
		
		if(!is_array($table))
		{
			$this->_join[$table]					= array
			(
				'condition'							=> $condition,
				'type'								=> $type,
				'escape'							=> $escape
			);
		}
		else
		{
			foreach($table as $key => $val)
			{
				$this->_join[$key]					= array
				(
					'condition'						=> $val,
					'type'							=> $type,
					'escape'						=> true
				);
			}
		}
		
		return $this;
	}
	
	/**
	 * Where
	 * Your contribution is needed to write complete hint about
	 * this method
	 */
	public function where($field = '', $value = '', $escape = true, $case_sensitive = false)
	{
		if(!is_array($field))
		{
			$this->_where[$field]					= array
			(
				'value'								=> $value,
				'escape'							=> $escape,
				'case_sensitive'					=> $case_sensitive
			);
		}
		else
		{
			foreach($field as $key => $val)
			{
				$this->_where[$key]					= array
				(
					'value'							=> $val,
					'escape'						=> true,
					'case_sensitive'				=> false
				);
			}
		}
		
		return $this;
	}
	
	/**
	 * Or Where
	 * Your contribution is needed to write complete hint about
	 * this method
	 */
	public function or_where($field = '', $value = '', $escape = true, $case_sensitive = false)
	{
		if(!is_array($field))
		{
			$this->_or_where[$field]				= array
			(
				'value'								=> $value,
				'escape'							=> $escape,
				'case_sensitive'					=> $case_sensitive
			);
		}
		else
		{
			foreach($field as $key => $val)
			{
				$this->_or_where[$key]				= array
				(
					'value'							=> $val,
					'escape'						=> true,
					'case_sensitive'				=> false
				);
			}
		}
		
		return $this;
	}
	
	/**
	 * Where In
	 * Your contribution is needed to write complete hint about
	 * this method
	 */
	public function where_in($field = '', $value = '', $escape = true, $case_sensitive = false)
	{
		if(!is_array($field))
		{
			$this->_where_in[$field]				= array
			(
				'value'								=> $value,
				'escape'							=> $escape,
				'case_sensitive'					=> $case_sensitive
			);
		}
		else
		{
			foreach($field as $key => $val)
			{
				$this->_where_in[$key]				= array
				(
					'value'							=> $val,
					'escape'						=> true,
					'case_sensitive'				=> false
				);
			}
		}
		
		return $this;
	}
	
	/**
	 * Or Where In
	 * Your contribution is needed to write complete hint about
	 * this method
	 */
	public function or_where_in($field = '', $value = '', $escape = true, $case_sensitive = false)
	{
		if(!is_array($field))
		{
			$this->_or_where_in[$field]				= array
			(
				'value'								=> $value,
				'escape'							=> $escape,
				'case_sensitive'					=> $case_sensitive
			);
		}
		else
		{
			foreach($field as $key => $val)
			{
				$this->_or_where_in[$key]			= array
				(
					'value'							=> $val,
					'escape'						=> true,
					'case_sensitive'				=> false
				);
			}
		}
		
		return $this;
	}
	
	/**
	 * Where Not In
	 * Your contribution is needed to write complete hint about
	 * this method
	 */
	public function where_not_in($field = '', $value = '', $escape = true, $case_sensitive = false)
	{
		if(!is_array($field))
		{
			$this->_where_not_in[$field]			= array
			(
				'value'								=> $value,
				'escape'							=> $escape,
				'case_sensitive'					=> $case_sensitive
			);
		}
		else
		{
			foreach($field as $key => $val)
			{
				$this->_where_not_in[$key]			= array
				(
					'value'							=> $val,
					'escape'						=> true,
					'case_sensitive'				=> false
				);
			}
		}
		
		return $this;
	}
	
	/**
	 * Or Where Not In
	 * Your contribution is needed to write complete hint about
	 * this method
	 */
	public function or_where_not_in($field = '', $value = '', $escape = true, $case_sensitive = false)
	{
		if(!is_array($field))
		{
			$this->_or_where_not_in[$field]			= array
			(
				'value'								=> $value,
				'escape'							=> $escape,
				'case_sensitive'					=> $case_sensitive
			);
		}
		else
		{
			foreach($field as $key => $val)
			{
				$this->_or_where_not_in[$key]		= array
				(
					'value'							=> $val,
					'escape'						=> true,
					'case_sensitive'				=> false
				);
			}
		}
		
		return $this;
	}
	
	/**
	 * Like
	 * Your contribution is needed to write complete hint about
	 * this method
	 */
	public function like($field = '', $match = '', $side = 'both', $escape = true, $case_sensitive = false)
	{
		if(!is_array($field))
		{
			$this->_like[$field]					= array
			(
				'match'								=> $match,
				'side'								=> $side,
				'escape'							=> $escape,
				'case_sensitive'					=> $case_sensitive
			);
		}
		else
		{
			foreach($field as $key => $val)
			{
				$this->_like[$key]					= array
				(
					'match'							=> $val,
					'side'							=> 'both',
					'escape'						=> true,
					'case_sensitive'				=> false
				);
			}
		}
		
		return $this;
	}
	
	/**
	 * Or Like
	 * Your contribution is needed to write complete hint about
	 * this method
	 */
	public function or_like($field = '', $match = '', $side = 'both', $escape = true, $case_sensitive = false)
	{
		if(!is_array($field))
		{
			$this->_or_like[$field]					= array
			(
				'match'								=> $match,
				'side'								=> $side,
				'escape'							=> $escape,
				'case_sensitive'					=> $case_sensitive
			);
		}
		else
		{
			foreach($field as $key => $val)
			{
				$this->_or_like[$key]				= array
				(
					'match'							=> $val,
					'side'							=> 'both',
					'escape'						=> true,
					'case_sensitive'				=> false
				);
			}
		}
		
		return $this;
	}
	
	/**
	 * Not Like
	 * Your contribution is needed to write complete hint about
	 * this method
	 */
	public function not_like($field = '', $match = '', $side = 'both', $escape = true, $case_sensitive = false)
	{
		if(!is_array($field))
		{
			$this->_not_like[$field]				= array
			(
				'match'								=> $match,
				'side'								=> $side,
				'escape'							=> $escape,
				'case_sensitive'					=> $case_sensitive
			);
		}
		else
		{
			foreach($field as $key => $val)
			{
				$this->_not_like[$key]				= array
				(
					'match'							=> $val,
					'side'							=> 'both',
					'escape'						=> true,
					'case_sensitive'				=> false
				);
			}
		}
		
		return $this;
	}
	
	/**
	 * Or Not Like
	 * Your contribution is needed to write complete hint about
	 * this method
	 */
	public function or_not_like($field = '', $match = '', $side = 'both', $escape = true, $case_sensitive = false)
	{
		if(!is_array($field))
		{
			$this->_or_not_like[$field]				= array
			(
				'match'								=> $match,
				'side'								=> $side,
				'escape'							=> $escape,
				'case_sensitive'					=> $case_sensitive
			);
		}
		else
		{
			foreach($field as $key => $val)
			{
				$this->_or_not_like[$key]			= array
				(
					'match'							=> $val,
					'side'							=> 'both',
					'escape'						=> true,
					'case_sensitive'				=> false
				);
			}
		}
		
		return $this;
	}
	
	/**
	 * Having
	 * Your contribution is needed to write complete hint about
	 * this method
	 */
	public function having($field = '', $value = '', $escape = true, $case_sensitive = false)
	{
		if(!is_array($field))
		{
			$this->_having[$field]					= array
			(
				'value'								=> $value,
				'escape'							=> $escape,
				'case_sensitive'					=> $case_sensitive
			);
		}
		else
		{
			foreach($field as $key => $val)
			{
				$this->_having[$key]				= array
				(
					'value'							=> $val,
					'escape'						=> true,
					'case_sensitive'				=> false
				);
			}
		}
		
		return $this;
	}
	
	/**
	 * Or Having
	 * Your contribution is needed to write complete hint about
	 * this method
	 */
	public function or_having($field = '', $value = '', $escape = true, $case_sensitive = false)
	{
		if(!is_array($field))
		{
			$this->_or_having[$field]				= array
			(
				'value'								=> $value,
				'escape'							=> $escape,
				'case_sensitive'					=> $case_sensitive
			);
		}
		else
		{
			foreach($field as $key => $val)
			{
				$this->_or_having[$key]				= array
				(
					'value'							=> $val,
					'escape'						=> true,
					'case_sensitive'				=> false
				);
			}
		}
		
		return $this;
	}
	
	/**
	 * Having In
	 * Your contribution is needed to write complete hint about
	 * this method
	 */
	public function having_in($field = '', $value = '', $escape = true, $case_sensitive = false)
	{
		if(!is_array($field))
		{
			$this->_having_in[$field]				= array
			(
				'value'								=> $value,
				'escape'							=> $escape,
				'case_sensitive'					=> $case_sensitive
			);
		}
		else
		{
			foreach($field as $key => $val)
			{
				$this->_having_in[$key]				= array
				(
					'value'							=> $val,
					'escape'						=> true,
					'case_sensitive'				=> false
				);
			}
		}
		
		return $this;
	}
	
	/**
	 * Or Having In
	 * Your contribution is needed to write complete hint about
	 * this method
	 */
	public function or_having_in($field = '', $value = '', $escape = true, $case_sensitive = false)
	{
		if(!is_array($field))
		{
			$this->_or_having_in[$field]			= array
			(
				'value'								=> $value,
				'escape'							=> $escape,
				'case_sensitive'					=> $case_sensitive
			);
		}
		else
		{
			foreach($field as $key => $val)
			{
				$this->_or_having_in[$key]			= array
				(
					'value'							=> $val,
					'escape'						=> true,
					'case_sensitive'				=> false
				);
			}
		}
		
		return $this;
	}
	
	/**
	 * Having Not In
	 * Your contribution is needed to write complete hint about
	 * this method
	 */
	public function having_not_in($field = '', $value = '', $escape = true, $case_sensitive = false)
	{
		if(!is_array($field))
		{
			$this->_having_not_in[$field]			= array
			(
				'value'								=> $value,
				'escape'							=> $escape,
				'case_sensitive'					=> $case_sensitive
			);
		}
		else
		{
			foreach($field as $key => $val)
			{
				$this->_having_not_in[$key]			= array
				(
					'value'							=> $val,
					'escape'						=> true,
					'case_sensitive'				=> false
				);
			}
		}
		
		return $this;
	}
	
	/**
	 * Or Having Not In
	 * Your contribution is needed to write complete hint about
	 * this method
	 */
	public function or_having_not_in($field = '', $value = '', $escape = true, $case_sensitive = false)
	{
		if(!is_array($field))
		{
			$this->_or_having_not_in[$field]		= array
			(
				'value'								=> $value,
				'escape'							=> $escape,
				'case_sensitive'					=> $case_sensitive
			);
		}
		else
		{
			foreach($field as $key => $val)
			{
				$this->_or_having_not_in[$key]		= array
				(
					'value'							=> $val,
					'escape'						=> true,
					'case_sensitive'				=> false
				);
			}
		}
		
		return $this;
	}
	
	/**
	 * Having Like
	 * Your contribution is needed to write complete hint about
	 * this method
	 */
	public function having_like($field = '', $match = '', $side = 'both', $escape = true, $case_sensitive = false)
	{
		if(!is_array($field))
		{
			$this->_having_like[$field]				= array
			(
				'match'								=> $match,
				'side'								=> $side,
				'escape'							=> $escape,
				'case_sensitive'					=> $case_sensitive
			);
		}
		else
		{
			foreach($field as $key => $val)
			{
				$this->_having_like[$key]			= array
				(
					'match'							=> $val,
					'side'							=> 'both',
					'escape'						=> true,
					'case_sensitive'				=> false
				);
			}
		}
		
		return $this;
	}
	
	/**
	 * Or Having Like
	 * Your contribution is needed to write complete hint about
	 * this method
	 */
	public function or_having_like($field = '', $match = '', $side = 'both', $escape = true, $case_sensitive = false)
	{
		if(!is_array($field))
		{
			$this->_or_having_like[$field]			= array
			(
				'match'								=> $match,
				'side'								=> $side,
				'escape'							=> $escape,
				'case_sensitive'					=> $case_sensitive
			);
		}
		else
		{
			foreach($field as $key => $val)
			{
				$this->_or_having_like[$key]		= array
				(
					'match'							=> $val,
					'side'							=> 'both',
					'escape'						=> true,
					'case_sensitive'				=> false
				);
			}
		}
		
		return $this;
	}
	
	/**
	 * Not Having Like
	 * Your contribution is needed to write complete hint about
	 * this method
	 */
	public function not_having_like($field = '', $match = '', $side = 'both', $escape = true, $case_sensitive = false)
	{
		if(!is_array($field))
		{
			$this->_not_having_like[$field]			= array
			(
				'match'								=> $match,
				'side'								=> $side,
				'escape'							=> $escape,
				'case_sensitive'					=> $case_sensitive
			);
		}
		else
		{
			foreach($field as $key => $val)
			{
				$this->_not_having_like[$key]		= array
				(
					'match'							=> $val,
					'side'							=> 'both',
					'escape'						=> true,
					'case_sensitive'				=> false
				);
			}
		}
		
		return $this;
	}
	
	/**
	 * Or Not Having Like
	 * Your contribution is needed to write complete hint about
	 * this method
	 */
	public function or_not_having_like($field = '', $match = '', $side = 'both', $escape = true, $case_sensitive = false)
	{
		if(!is_array($field))
		{
			$this->_or_not_having_like[$field]		= array
			(
				'match'								=> $match,
				'side'								=> $side,
				'escape'							=> $escape,
				'case_sensitive'					=> $case_sensitive
			);
		}
		else
		{
			foreach($field as $key => $val)
			{
				$this->_or_not_having_like[$key]	= array
				(
					'match'							=> $val,
					'side'							=> 'both',
					'escape'						=> true,
					'case_sensitive'				=> false
				);
			}
		}
		
		return $this;
	}
	
	/**
	 * Limit
	 * Your contribution is needed to write complete hint about
	 * this method
	 */
	public function limit($limit = 0, $offset = 0)
	{
		$this->_limit								= $limit;
		
		if($offset)
		{
			$this->_offset							= $offset;
		}
		
		return $this;
	}
	
	/**
	 * Offset
	 * Your contribution is needed to write complete hint about
	 * this method
	 */
	public function offset($offset = null)
	{
		$this->_offset								= $offset;
		
		return $this;
	}
	
	/**
	 * Order By
	 * Your contribution is needed to write complete hint about
	 * this method
	 */
	public function order_by($column = null, $direction = null, $escape = true)
	{
		if(!is_array($column))
		{
			if($direction)
			{
				$this->_order_by[$column]			= array
				(
					'direction'						=> $direction,
					'escape'						=> $escape
				);
			}
			else
			{
				$column								= array_map('trim', explode(',', $column));
				
				foreach($column as $key => $val)
				{
					if(strpos($val, '(') !== false && strpos($val, ')') !== false)
					{
						$col						= $val;
					}
					else
					{
						list($col, $dir)			= array_pad(array_map('trim', explode(' ', $val)), 2, null);
					}
					
					$this->_order_by[$col]			= array
					(
						'direction'					=> ($dir ? $dir : 'asc'),
						'escape'					=> $escape
					);
				}
			}
		}
		else
		{
			foreach($column as $key => $val)
			{
				$this->_order_by[$key]				= array
				(
					'direction'						=> $val,
					'escape'						=> true
				);
			}
		}
		
		return $this;
	}
	
	/**
	 * Group By
	 * Your contribution is needed to write complete hint about
	 * this method
	 */
	public function group_by($by = null)
	{
		$this->_group_by							= $by;
		
		return $this;
	}
	
	/**
	 * Group Start
	 * Your contribution is needed to write complete hint about
	 * this method
	 */
	public function group_start()
	{
		
		return $this;
	}
	
	/**
	 * Or Group Start
	 * Your contribution is needed to write complete hint about
	 * this method
	 */
	public function or_group_start()
	{
		
		return $this;
	}
	
	/**
	 * Not Group Start
	 * Your contribution is needed to write complete hint about
	 * this method
	 */
	public function not_group_start()
	{
		
		return $this;
	}
	
	/**
	 * Or Not Group Start
	 * Your contribution is needed to write complete hint about
	 * this method
	 */
	public function or_not_group_start()
	{
		
		return $this;
	}
	
	/**
	 * Group End
	 * Your contribution is needed to write complete hint about
	 * this method
	 */
	public function group_end()
	{
		
		return $this;
	}
	
	/**
	 * Group Start
	 * Your contribution is needed to write complete hint about
	 * this method
	 */
	public function group_having_start()
	{
		
		return $this;
	}
	
	/**
	 * Or Group Start
	 * Your contribution is needed to write complete hint about
	 * this method
	 */
	public function or_group_having_start()
	{
		
		return $this;
	}
	
	/**
	 * Not Group Start
	 * Your contribution is needed to write complete hint about
	 * this method
	 */
	public function not_group_having_start()
	{
		
		return $this;
	}
	
	/**
	 * Or Not Group Start
	 * Your contribution is needed to write complete hint about
	 * this method
	 */
	public function or_not_group_having_start()
	{
		
		return $this;
	}
	
	/**
	 * Group End
	 * Your contribution is needed to write complete hint about
	 * this method
	 */
	public function group_having_end()
	{
		
		return $this;
	}
	
	/**
	 * Set
	 * Your contribution is needed to write complete hint about
	 * this method
	 */
	public function set($key = null, $value = null, $escape = true)
	{
		if(!is_array($key))
		{
			$this->_set[$key]						= $value;
		}
		else
		{
			$this->_set								= array_merge($this->_set, $key);
		}
		
		return $this;
	}
	
	/**
	 * _query
	 * Collect the method into parameter
	 *
	 * @access		private
	 */
	private function _query($table = null)
	{
		/**
		 * Add distinct
		 */
		if($this->_distinct)
		{
			$this->model->distinct();
		}
		
		/**
		 * Prepare indexing the columns of table to be selected
		 */
		$columns									= $this->model->field_data($table);
		$prepare_select								= preg_filter('/^/', $table . '.', $this->model->list_fields($table));
		
		if($columns)
		{
			foreach($columns as $key => $val)
			{
				if(in_array($this->_method, array('create', 'update')) && in_array($val->name, $this->_unset_field))
				{
					if(!isset($val->primary_key) || empty($val->primary_key))
					{
						unset($prepare_select[$val->name]);
					}
				}
				elseif(in_array($this->_method, array('read')) && in_array($val->name, $this->_unset_view))
				{
					if(!isset($val->primary_key) || empty($val->primary_key))
					{
						unset($prepare_select[$val->name]);
					}
				}
				elseif(in_array($val->name, $this->_unset_column))
				{
					if(!isset($val->primary_key) || empty($val->primary_key))
					{
						unset($prepare_select[$val->name]);
					}
				}
			}
		}
		
		/**
		 * Select columns
		 */
		if($this->_select)
		{
			$this->_select							= array_merge($prepare_select, $this->_select);
		}
		else
		{
			$this->_select							= $prepare_select;
		}
		
		/**
		 * Make the select column unique to prevend duplicate select
		 */
		$this->_select								= array_unique($this->_select);
		
		/**
		 * From table
		 */
		if(!$this->_from)
		{
			$this->_from							= $table;
		}
		
		/**
		 * Push select to forge
		 */
		$forge_select								= array();
		
		/**
		 * Execute when method is not update or delete
		 */
		if(!in_array($this->_method, array('update', 'delete')) && is_array($this->_select) && sizeof($this->_select) > 0)
		{
			/**
			 * Validate the select column to check if column is exist in table
			 */
			$already_selected						= array();
			
			foreach($this->_select as $key => $val)
			{
				/**
				 * Check if field is already selected
				 */
				$field								= (strpos($val, '.') !== false ? explode('.', $val) : array('anonymous', $val));
				
				if(in_array($field[1], $already_selected))
				{
					continue;
				}
				
				$already_selected[]					= $field[1];
				
				/**
				 * Check if selected column is use alias
				 */
				if(strpos($val, '.*') !== false && $this->_from == strstr($val, '.*', true))
				{
					continue;
				}
				else
				{
					$val							= str_ireplace(' AS ', ' ', $val);
					
					/**
					 * Individual table
					 */
					list($table, $field)			= array_pad(explode('.', $val), 2, null);
					
					$field							= (stripos($field, ' ') !== false ? substr($field, strripos($field, ' ') + 1) : $field);
					
					if($table && $field && in_array($field, $forge_select))
					{
						/**
						 * Format column of select
						 */
						if($table != $this->_from && $this->model->field_exists($field, $table))
						{
							$val					= $table . '.' . $field . ' AS ' . $field;
						}
						else
						{
							continue;
						}
					}
					
					$forge_select[]					= $field;
				}
				
				$this->model->select($val);
			}
		}
		
		/**
		 * Select sum
		 */
		if(is_array($this->_select_sum) && sizeof($this->_select_sum) > 0)
		{
			/**
			 * Validate the select column to check if column is exist in table
			 */
			foreach($this->_select_sum as $key => $val)
			{
				$val								= (sizeof(explode('.', $val)) <= 1 ? $this->_from . '.' . $val : $val);
				
				$this->model->select_sum($val);
			}
		}
		
		/**
		 * Select lowest
		 */
		if(is_array($this->_select_min) && sizeof($this->_select_min) > 0)
		{
			/**
			 * Validate the select column to check if column is exist in table
			 */
			foreach($this->_select_min as $key => $val)
			{
				$val								= (sizeof(explode('.', $val)) <= 1 ? $this->_from . '.' . $val : $val);
				
				$this->model->select_min($val);
			}
		}
		
		/**
		 * Select greater
		 */
		if(is_array($this->_select_max) && sizeof($this->_select_max) > 0)
		{
			/**
			 * Validate the select column to check if column is exist in table
			 */
			foreach($this->_select_max as $key => $val)
			{
				$val								= (sizeof(explode('.', $val)) <= 1 ? $this->_from . '.' . $val : $val);
				
				$this->model->select_max($val);
			}
		}
		
		/**
		 * Select average
		 */
		if(is_array($this->_select_avg) && sizeof($this->_select_avg) > 0)
		{
			/**
			 * Validate the select column to check if column is exist in table
			 */
			foreach($this->_select_avg as $key => $val)
			{
				$val								= (sizeof(explode('.', $val)) <= 1 ? $this->_from . '.' . $val : $val);
				
				$this->model->select_avg($val);
			}
		}
		
		/**
		 * Where
		 */
		if(is_array($this->_where) && sizeof($this->_where) > 0)
		{
			foreach($this->_where as $key => $val)
			{
				$key								= trim($key);
				
				if($this->model->field_exists($key, $this->_from))
				{
					$this->model->where($this->_from . '.' . $key, $val);
				}
				elseif($this->model->field_exists(str_replace(' !=', '', $key), $this->_from) || $this->model->field_exists(str_replace(' <', '', $key), $this->_from) || $this->model->field_exists(str_replace(' >', '', $key), $this->_from) || $this->model->field_exists(str_replace(' >=', '', $key), $this->_from) || $this->model->field_exists(str_replace(' <=', '', $key), $this->_from))
				{
					$this->model->where($this->_from . '.' . $key, $val);
				}
				else
				{
					$checker						= explode('.', $key);
					
					if(is_array($checker) && sizeof($checker) == 2 && $this->model->field_exists(trim($checker[1]), $checker[0]))
					{
						$this->model->where($key, $val);
					}
					elseif(is_array($checker) && sizeof($checker) == 2 && ($this->model->field_exists(str_replace(' !=', '', trim($checker[1])), $checker[0]) || $this->model->field_exists(str_replace(' <', '', trim($checker[1])), $checker[0]) || $this->model->field_exists(str_replace(' >', '', trim($checker[1])), $checker[0]) || $this->model->field_exists(str_replace(' >=', '', trim(trim($checker[1]))), $checker[0]) || $this->model->field_exists(str_replace(' <=', '', trim($checker[1])), $checker[0])))
					{
						$this->model->where($checker[1], $val);
					}
					elseif(is_array($this->_compiled_table) && sizeof($this->_compiled_table) > 0)
					{
						foreach($this->_compiled_table as $num => $table)
						{
							if($this->model->field_exists(str_replace(' !=', '', $key), $table) || $this->model->field_exists(str_replace(' <', '', $key), $table) || $this->model->field_exists(str_replace(' >', '', $key), $table) || $this->model->field_exists(str_replace(' >=', '', $key), $table) || $this->model->field_exists(str_replace(' <=', '', $key), $table))
							{
								$this->model->where($table . '.' . $key, $val);
							}
						}
					}
					elseif(!$val)
					{
						$this->model->where($key);
					}
				}
			}
		}
		
		/**
		 * Or where
		 */
		if(is_array($this->_or_where) && sizeof($this->_or_where) > 0)
		{
			foreach($this->_or_where as $key => $val)
			{
				$key								= trim($key);
				
				/**
				 * Validate the select column to check if column is exist in table
				 */
				if($this->model->field_exists($key, $this->_from))
				{
					$this->model->or_where($this->_from . '.' . $key, $val);
				}
				/**
				 * Validate the select column to check if column is exist in table
				 */
				elseif($this->model->field_exists(str_replace(' !=', '', $key), $this->_from) || $this->model->field_exists(str_replace(' <', '', $key), $this->_from) || $this->model->field_exists(str_replace(' >', '', $key), $this->_from) || $this->model->field_exists(str_replace(' >=', '', $key), $this->_from) || $this->model->field_exists(str_replace(' <=', '', $key), $this->_from))
				{
					$this->model->or_where($this->_from . '.' . $key, $val);
				}
				else
				{
					/**
					 * Validate the select column to check if column is exist in table
					 */
					$checker						= explode('.', $key);
					if(is_array($checker) && sizeof($checker) == 2 && $this->model->field_exists($checker[1], $checker[0]))
					{
						$this->model->or_where($key, $val);
					}
					elseif(is_array($checker) && sizeof($checker) == 2 && ($this->model->field_exists(str_replace(' !=', '', $checker[1]), $checker[0]) || $this->model->field_exists(str_replace(' <', '', $checker[1]), $checker[0]) || $this->model->field_exists(str_replace(' >', '', $checker[1]), $checker[0]) || $this->model->field_exists(str_replace(' >=', '', $checker[1]), $checker[0]) || $this->model->field_exists(str_replace(' <=', '', $checker[1]), $checker[0])))
					{
						$this->model->or_where($checker[1], $val);
					}
					elseif(is_array($this->_compiled_table) && sizeof($this->_compiled_table) > 0)
					{
						foreach($this->_compiled_table as $num => $table)
						{
							/**
							 * Validate the select column to check if column is exist in table
							 */
							if($this->model->field_exists(str_replace(' !=', '', $key), $table) || $this->model->field_exists(str_replace(' <', '', $key), $table) || $this->model->field_exists(str_replace(' >', '', $key), $table) || $this->model->field_exists(str_replace(' >=', '', $key), $table) || $this->model->field_exists(str_replace(' <=', '', $key), $table))
							{
								$this->model->or_where($table . '.' . $key, $val);
							}
						}
					}
					elseif(!$val)
					{
						$this->model->or_where($key);
					}
				}
			}
		}
		
		/**
		 * Where in
		 */
		if(is_array($this->_where_in) && sizeof($this->_where_in) > 0)
		{
			foreach($this->_where_in as $key => $val)
			{
				$key								= trim($key);
				
				/**
				 * Validate the select column to check if column is exist in table
				 */
				if($this->model->field_exists($key, $this->_from))
				{
					$this->model->where_in($this->_from . '.' . $key, $val);
				}
				else
				{
					/**
					 * Validate the select column to check if column is exist in table
					 */
					$checker						= explode('.', $key);
					if(is_array($checker) && sizeof($checker) == 2 && $this->model->field_exists($checker[1], $checker[0]))
					{
						$this->model->where_in($key, $val);
					}
					elseif(is_array($this->_compiled_table) && sizeof($this->_compiled_table) > 0)
					{
						foreach($this->_compiled_table as $num => $table)
						{
							/**
							 * Validate the select column to check if column is exist in table
							 */
							if($this->model->field_exists($key, $table))
							{
								$this->model->where_in($table . '.' . $key, $val);
							}
						}
					}
				}
			}
		}
		
		/**
		 * Or where in
		 */
		if(is_array($this->_or_where_in) && sizeof($this->_or_where_in) > 0)
		{
			foreach($this->_or_where_in as $key => $val)
			{
				$key								= trim($key);
				
				/**
				 * Validate the select column to check if column is exist in table
				 */
				if($this->model->field_exists($key, $this->_from))
				{
					$this->model->or_where_in($this->_from . '.' . $key, $val);
				}
				else
				{
					/**
					 * Validate the select column to check if column is exist in table
					 */
					$checker						= explode('.', $key);
					if(is_array($checker) && sizeof($checker) == 2 && $this->model->field_exists($checker[1], $checker[0]))
					{
						$this->model->or_where_in($key, $val);
					}
					elseif(is_array($this->_compiled_table) && sizeof($this->_compiled_table) > 0)
					{
						foreach($this->_compiled_table as $num => $table)
						{
							/**
							 * Validate the select column to check if column is exist in table
							 */
							if($this->model->field_exists($key, $table))
							{
								$this->model->or_where_in($table . '.' . $key, $val);
							}
						}
					}
				}
			}
		}
		
		/**
		 * Where not in
		 */
		if(is_array($this->_where_not_in) && sizeof($this->_where_not_in) > 0)
		{
			foreach($this->_where_not_in as $key => $val)
			{
				$key								= trim($key);
				
				/**
				 * Validate the select column to check if column is exist in table
				 */
				if($this->model->field_exists($key, $this->_from))
				{
					$this->model->where_not_in($this->_from . '.' . $key, $val);
				}
				else
				{
					/**
					 * Validate the select column to check if column is exist in table
					 */
					$checker						= explode('.', $key);
					
					if(is_array($checker) && sizeof($checker) == 2 && $this->model->field_exists($checker[1], $checker[0]))
					{
						$this->model->where_not_in($key, $val);
					}
					elseif(is_array($this->_compiled_table) && sizeof($this->_compiled_table) > 0)
					{
						foreach($this->_compiled_table as $num => $table)
						{
							/**
							 * Validate the select column to check if column is exist in table
							 */
							if($this->model->field_exists($key, $table))
							{
								$this->model->where_not_in($table . '.' . $key, $val);
							}
						}
					}
				}
			}
		}
		
		/**
		 * Or where not in
		 */
		if(is_array($this->_or_where_not_in) && sizeof($this->_or_where_not_in) > 0)
		{
			foreach($this->_or_where_not_in as $key => $val)
			{
				$key								= trim($key);
				
				/**
				 * Validate the select column to check if column is exist in table
				 */
				if($this->model->field_exists($key, $this->_from))
				{
					$this->model->or_where_not_in($this->_from . '.' . $key, $val);
				}
				else
				{
					/**
					 * Validate the select column to check if column is exist in table
					 */
					$checker						= explode('.', $key);
					
					if(is_array($checker) && sizeof($checker) == 2 && $this->model->field_exists($checker[1], $checker[0]))
					{
						$this->model->or_where_not_in($key, $val);
					}
					elseif(is_array($this->_compiled_table) && sizeof($this->_compiled_table) > 0)
					{
						foreach($this->_compiled_table as $num => $table)
						{
							/**
							 * Validate the select column to check if column is exist in table
							 */
							if($this->model->field_exists($key, $table))
							{
								$this->model->or_where_not_in($table . '.' . $key, $val);
							}
						}
					}
				}
			}
		}
		
		/**
		 * Like
		 */
		if(is_array($this->_like) && sizeof($this->_like) > 0)
		{
			foreach($this->_like as $key => $val)
			{
				$key								= trim($key);
				
				/**
				 * Validate the select column to check if column is exist in table
				 */
				if($this->model->field_exists($key, $this->_from))
				{
					$this->model->like($this->_from . '.' . $key, $val);
				}
				/**
				 * Validate the select column to check if column is exist in table
				 */
				elseif($this->model->field_exists(str_replace(' !=', '', $key), $this->_from))
				{
					$this->model->like($this->_from . '.' . $key, $val);
				}
				else
				{
					/**
					 * Validate the select column to check if column is exist in table
					 */
					$checker						= explode('.', $key);
					
					if(is_array($checker) && sizeof($checker) == 2 && $this->model->field_exists($checker[1], $checker[0]))
					{
						$this->model->like($key, $val);
					}
					elseif(is_array($this->_compiled_table) && sizeof($this->_compiled_table) > 0)
					{
						foreach($this->_compiled_table as $num => $table)
						{
							/**
							 * Validate the select column to check if column is exist in table
							 */
							if($this->model->field_exists(str_replace(' !=', '', $key), $table))
							{
								$this->model->like($table . '.' . $key, $val);
							}
						}
					}
				}
			}
		}
		
		/**
		 * Or like
		 */
		if(is_array($this->_or_like) && sizeof($this->_or_like) > 0)
		{
			foreach($this->_or_like as $key => $val)
			{
				$key								= trim($key);
				
				/**
				 * Validate the select column to check if column is exist in table
				 */
				if($this->model->field_exists($key, $this->_from))
				{
					$this->model->or_like($this->_from . '.' . $key, $val);
				}
				else
				{
					/**
					 * Validate the select column to check if column is exist in table
					 */
					$checker						= explode('.', $key);
					
					if(isset($checker[0]) && isset($checker[1]) && $this->model->field_exists($checker[1], $checker[0]))
					{
						$this->model->or_like($key, $val);
					}
					else if(is_array($this->_compiled_table) && sizeof($this->_compiled_table) > 0)
					{
						foreach($this->_compiled_table as $num => $table)
						{
							/**
							 * Validate the select column to check if column is exist in table
							 */
							if($this->model->field_exists($key, $table))
							{
								$this->model->or_like($table . '.' . $key, $val);
							}
						}
					}
				}
			}
		}
		
		/**
		 * Not like
		 */
		if(is_array($this->_not_like) && sizeof($this->_not_like) > 0)
		{
			foreach($this->_not_like as $key => $val)
			{
				$key								= trim($key);
				
				/**
				 * Validate the select column to check if column is exist in table
				 */
				if($this->model->field_exists($key, $this->_from))
				{
					$this->model->not_like($this->_from . '.' . $key, $val);
				}
				else
				{
					if(is_array($this->_compiled_table) && sizeof($this->_compiled_table) > 0)
					{
						foreach($this->_compiled_table as $num => $table)
						{
							/**
							 * Validate the select column to check if column is exist in table
							 */
							if($this->model->field_exists($key, $table))
							{
								$this->model->not_like($table . '.' . $key, $val);
							}
						}
					}
				}
			}
		}
		
		/**
		 * Or not like
		 */
		if(is_array($this->_or_not_like) && sizeof($this->_or_not_like) > 0)
		{
			foreach($this->_or_not_like as $key => $val)
			{
				$key								= trim($key);
				
				/**
				 * Validate the select column to check if column is exist in table
				 */
				if($this->model->field_exists($key, $this->_from))
				{
					$this->model->or_not_like($this->_from . '.' . $key, $val);
				}
				else
				{
					if(is_array($this->_compiled_table) && sizeof($this->_compiled_table) > 0)
					{
						foreach($this->_compiled_table as $num => $table)
						{
							/**
							 * Validate the select column to check if column is exist in table
							 */
							if($this->model->field_exists($key, $table))
							{
								$this->model->or_not_like($table . '.' . $key, $val);
							}
						}
					}
				}
			}
		}
		
		/**
		 * Having
		 */
		if(is_array($this->_having) && sizeof($this->_having) > 0)
		{
			foreach($this->_having as $key => $val)
			{
				$key								= trim($key);
				
				/**
				 * Validate the select column to check if column is exist in table
				 */
				if($this->model->field_exists($key, $this->_from))
				{
					$this->model->having($this->_from . '.' . $key, $val);
				}
				else
				{
					/**
					 * Validate the select column to check if column is exist in table
					 */
					$checker						= explode('.', $key);
					
					if(is_array($checker) && sizeof($checker) == 2 && $this->model->field_exists($checker[1], $checker[0]))
					{
						$this->model->having($key, $val);
					}
					elseif(is_array($this->_compiled_table) && sizeof($this->_compiled_table) > 0)
					{
						foreach($this->_compiled_table as $num => $table)
						{
							/**
							 * Validate the select column to check if column is exist in table
							 */
							if($this->model->field_exists($key, $table))
							{
								$this->model->having($table . '.' . $key, $val);
							}
						}
					}
					elseif(!$val)
					{
						$this->model->having($key);
					}
				}
			}
		}
		
		/**
		 * Or having
		 */
		if(is_array($this->_or_having) && sizeof($this->_or_having) > 0)
		{
			foreach($this->_or_having as $key => $val)
			{
				$key								= trim($key);
				
				/**
				 * Validate the column to check if column is exist in table
				 */
				if($this->model->field_exists($key, $this->_from))
				{
					$this->model->or_having($this->_from . '.' . $key, $val);
				}
				else
				{
					/**
					 * Validate the column to check if column is exist in table
					 */
					$checker						= explode('.', $key);
					
					if(is_array($checker) && sizeof($checker) == 2 && $this->model->field_exists($checker[1], $checker[0]))
					{
						$this->model->or_having($key, $val);
					}
					elseif(is_array($this->_compiled_table) && sizeof($this->_compiled_table) > 0)
					{
						foreach($this->_compiled_table as $num => $table)
						{
							/**
							 * Validate the column to check if column is exist in table
							 */
							if($this->model->field_exists($key, $table))
							{
								$this->model->or_having($table . '.' . $key, $val);
							}
						}
					}
					elseif(!$val)
					{
						$this->model->or_having($key);
					}
				}
			}
		}
		
		/**
		 * Having in
		 */
		if(is_array($this->_having_in) && sizeof($this->_having_in) > 0)
		{
			foreach($this->_having_in as $key => $val)
			{
				$key								= trim($key);
				
				/**
				 * Validate the column to check if column is exist in table
				 */
				if($this->model->field_exists($key, $this->_from))
				{
					$this->model->having_in($this->_from . '.' . $key, $val);
				}
				else
				{
					/**
					 * Validate the column to check if column is exist in table
					 */
					$checker						= explode('.', $key);
					
					if(is_array($checker) && sizeof($checker) == 2 && $this->model->field_exists($checker[1], $checker[0]))
					{
						$this->model->having_in($key, $val);
					}
					elseif(is_array($this->_compiled_table) && sizeof($this->_compiled_table) > 0)
					{
						foreach($this->_compiled_table as $num => $table)
						{
							/**
							 * Validate the column to check if column is exist in table
							 */
							if($this->model->field_exists($key, $table))
							{
								$this->model->having_in($table . '.' . $key, $val);
							}
						}
					}
					elseif(!$val)
					{
						$this->model->having_in($key);
					}
				}
			}
		}
		
		/**
		 * Or having in
		 */
		if(is_array($this->_or_having_in) && sizeof($this->_or_having_in) > 0)
		{
			foreach($this->_or_having_in as $key => $val)
			{
				$key								= trim($key);
				
				/**
				 * Validate the column to check if column is exist in table
				 */
				if($this->model->field_exists($key, $this->_from))
				{
					$this->model->or_having_in($this->_from . '.' . $key, $val);
				}
				else
				{
					/**
					 * Validate the column to check if column is exist in table
					 */
					$checker						= explode('.', $key);
					
					if(is_array($checker) && sizeof($checker) == 2 && $this->model->field_exists($checker[1], $checker[0]))
					{
						$this->model->or_having_in($key, $val);
					}
					elseif(is_array($this->_compiled_table) && sizeof($this->_compiled_table) > 0)
					{
						foreach($this->_compiled_table as $num => $table)
						{
							/**
							 * Validate the column to check if column is exist in table
							 */
							if($this->model->field_exists($key, $table))
							{
								$this->model->or_having_in($table . '.' . $key, $val);
							}
						}
					}
					elseif(!$val)
					{
						$this->model->or_having_in($key);
					}
				}
			}
		}
		
		/**
		 * Having not in
		 */
		if(is_array($this->_having_not_in) && sizeof($this->_having_not_in) > 0)
		{
			foreach($this->_having_not_in as $key => $val)
			{
				$key								= trim($key);
				
				/**
				 * Validate the column to check if column is exist in table
				 */
				if($this->model->field_exists($key, $this->_from))
				{
					$this->model->having_not_in($this->_from . '.' . $key, $val);
				}
				else
				{
					/**
					 * Validate the column to check if column is exist in table
					 */
					$checker						= explode('.', $key);
					
					if(is_array($checker) && sizeof($checker) == 2 && $this->model->field_exists($checker[1], $checker[0]))
					{
						$this->model->having_not_in($key, $val);
					}
					elseif(is_array($this->_compiled_table) && sizeof($this->_compiled_table) > 0)
					{
						foreach($this->_compiled_table as $num => $table)
						{
							/**
							 * Validate the column to check if column is exist in table
							 */
							if($this->model->field_exists($key, $table))
							{
								$this->model->having_not_in($table . '.' . $key, $val);
							}
						}
					}
					elseif(!$val)
					{
						$this->model->having_not_in($key);
					}
				}
			}
		}
		
		/**
		 * Or having not in
		 */
		if(is_array($this->_or_having_not_in) && sizeof($this->_or_having_not_in) > 0)
		{
			foreach($this->_or_having_not_in as $key => $val)
			{
				$key								= trim($key);
				
				/**
				 * Validate the column to check if column is exist in table
				 */
				if($this->model->field_exists($key, $this->_from))
				{
					$this->model->or_having_not_in($this->_from . '.' . $key, $val);
				}
				else
				{
					/**
					 * Validate the column to check if column is exist in table
					 */
					$checker						= explode('.', $key);
					
					if(is_array($checker) && sizeof($checker) == 2 && $this->model->field_exists($checker[1], $checker[0]))
					{
						$this->model->or_having_not_in($key, $val);
					}
					elseif(is_array($this->_compiled_table) && sizeof($this->_compiled_table) > 0)
					{
						foreach($this->_compiled_table as $num => $table)
						{
							/**
							 * Validate the column to check if column is exist in table
							 */
							if($this->model->field_exists($key, $table))
							{
								$this->model->or_having_not_in($table . '.' . $key, $val);
							}
						}
					}
					elseif(!$val)
					{
						$this->model->or_having_not_in($key);
					}
				}
			}
		}
		
		/**
		 * Having like
		 */
		if(is_array($this->_having_like) && sizeof($this->_having_like) > 0)
		{
			foreach($this->_having_like as $key => $val)
			{
				$key								= trim($key);
				
				/**
				 * Validate the column to check if column is exist in table
				 */
				if($this->model->field_exists($key, $this->_from))
				{
					$this->model->having_like($this->_from . '.' . $key, $val);
				}
				else
				{
					/**
					 * Validate the column to check if column is exist in table
					 */
					$checker						= explode('.', $key);
					
					if(is_array($checker) && sizeof($checker) == 2 && $this->model->field_exists($checker[1], $checker[0]))
					{
						$this->model->having_like($key, $val);
					}
					elseif(is_array($this->_compiled_table) && sizeof($this->_compiled_table) > 0)
					{
						foreach($this->_compiled_table as $num => $table)
						{
							/**
							 * Validate the column to check if column is exist in table
							 */
							if($this->model->field_exists($key, $table))
							{
								$this->model->having_like($table . '.' . $key, $val);
							}
						}
					}
					elseif(!$val)
					{
						$this->model->having_like($key);
					}
				}
			}
		}
		
		/**
		 * Or having like
		 */
		if(is_array($this->_or_having_like) && sizeof($this->_or_having_like) > 0)
		{
			foreach($this->_or_having_like as $key => $val)
			{
				$key								= trim($key);
				
				/**
				 * Validate the column to check if column is exist in table
				 */
				if($this->model->field_exists($key, $this->_from))
				{
					$this->model->or_having_like($this->_from . '.' . $key, $val);
				}
				else
				{
					/**
					 * Validate the column to check if column is exist in table
					 */
					$checker						= explode('.', $key);
					
					if(is_array($checker) && sizeof($checker) == 2 && $this->model->field_exists($checker[1], $checker[0]))
					{
						$this->model->or_having_like($key, $val);
					}
					elseif(is_array($this->_compiled_table) && sizeof($this->_compiled_table) > 0)
					{
						foreach($this->_compiled_table as $num => $table)
						{
							/**
							 * Validate the column to check if column is exist in table
							 */
							if($this->model->field_exists($key, $table))
							{
								$this->model->or_having_like($table . '.' . $key, $val);
							}
						}
					}
					elseif(!$val)
					{
						$this->model->or_having_like($key);
					}
				}
			}
		}
		
		/**
		 * Not having like
		 */
		if(is_array($this->_not_having_like) && sizeof($this->_not_having_like) > 0)
		{
			foreach($this->_not_having_like as $key => $val)
			{
				$key								= trim($key);
				
				/**
				 * Validate the column to check if column is exist in table
				 */
				if($this->model->field_exists($key, $this->_from))
				{
					$this->model->not_having_like($this->_from . '.' . $key, $val);
				}
				else
				{
					/**
					 * Validate the column to check if column is exist in table
					 */
					$checker						= explode('.', $key);
					
					if(is_array($checker) && sizeof($checker) == 2 && $this->model->field_exists($checker[1], $checker[0]))
					{
						$this->model->not_having_like($key, $val);
					}
					elseif(is_array($this->_compiled_table) && sizeof($this->_compiled_table) > 0)
					{
						foreach($this->_compiled_table as $num => $table)
						{
							/**
							 * Validate the column to check if column is exist in table
							 */
							if($this->model->field_exists($key, $table))
							{
								$this->model->not_having_like($table . '.' . $key, $val);
							}
						}
					}
					elseif(!$val)
					{
						$this->model->not_having_like($key);
					}
				}
			}
		}
		
		/**
		 * Or not having like
		 */
		if(is_array($this->_or_not_having_like) && sizeof($this->_or_not_having_like) > 0)
		{
			foreach($this->_or_not_having_like as $key => $val)
			{
				$key								= trim($key);
				
				/**
				 * Validate the column to check if column is exist in table
				 */
				if($this->model->field_exists($key, $this->_from))
				{
					$this->model->or_not_having_like($this->_from . '.' . $key, $val);
				}
				else
				{
					/**
					 * Validate the column to check if column is exist in table
					 */
					$checker						= explode('.', $key);
					
					if(is_array($checker) && sizeof($checker) == 2 && $this->model->field_exists($checker[1], $checker[0]))
					{
						$this->model->or_not_having_like($key, $val);
					}
					elseif(is_array($this->_compiled_table) && sizeof($this->_compiled_table) > 0)
					{
						foreach($this->_compiled_table as $num => $table)
						{
							/**
							 * Validate the column to check if column is exist in table
							 */
							if($this->model->field_exists($key, $table))
							{
								$this->model->or_not_having_like($table . '.' . $key, $val);
							}
						}
					}
					elseif(!$val)
					{
						$this->model->or_not_having_like($key);
					}
				}
			}
		}
		
		if($this->_limit)
		{
			$this->model->limit($this->_limit, $this->_offset);
		}
		
		/**
		 * Apply when method is not update nor delete
		 */
		if(!in_array($this->_method, array('update', 'delete')))
		{
			if(is_array($this->_join) && sizeof($this->_join) > 0)
			{
				foreach($this->_join as $table => $params)
				{
					$this->_compiled_table[]		= $table;
					
					$this->model->join($table, str_replace('{primary_table}', $this->_from, $params['condition']), $params['type']);
				}
			}
		
			/**
			 * Fix the limit and offset when used the SQL Server database
			 */
			if(!$this->_order_by)
			{
				$this->_order_by					= array_fill_keys($this->_set_primary, ('desc' == strtolower(get_userdata('sortOrder')) ? 'DESC' : 'ASC'));
			}
			
			/**
			 * Apply the order query
			 */
			if(is_array($this->_order_by) && sizeof($this->_order_by) > 0)
			{
				if('asc' == strtolower(service('request')->getGet('sort')))
				{
					set_userdata('sortOrder', 'desc');
				}
				else
				{
					set_userdata('sortOrder', 'asc');
				}
				
				foreach($this->_order_by as $key => $val)
				{
					if(!is_array($val))
					{
						$val						= array
						(
							'direction'				=> $val,
							'escape'				=> true
						);
					}
					
					/**
					 * Validate the column to check if column is exist in table
					 */
					$relation						= explode('.', $key);
					
					if($this->model->field_exists($key, $this->_from))
					{
						if(service('request')->getGet('sort'))
						{
							$val['direction']		= get_userdata('sortOrder');
						}
						
						// fix table alias
						$table						= (strpos($this->_from, ' ') !== false ? substr($this->_from, strripos($this->_from, ' ') + 1) : $this->_from);
						
						$this->model->order_by($table . '.' . $key, $val['direction'], $val['escape']);
					}
					/**
					 * Validate the column to check if column is exist in table
					 */
					elseif(isset($relation[0]) && isset($relation[1]) && $this->model->field_exists($relation[1], $relation[0]))
					{
						// fix table alias
						$table						= (strpos($key, ' ') !== false ? substr($key, strripos($key, ' ') + 1) : $key);
						
						$this->model->order_by($table, $val['direction'], $val['escape']);
					}
					else
					{
						if(is_array($this->_compiled_table) && sizeof($this->_compiled_table) > 0)
						{
							foreach($this->_compiled_table as $num => $table)
							{
								/**
								 * Validate the column to check if column is exist in table
								 */
								if($this->model->field_exists($key, $table))
								{
									if(service('request')->getGet('sort'))
									{
										$dir		= 'direction';
										
										$val[$dir]	= get_userdata('sortOrder');
									}
									
									// fix table alias
									$table			= (strpos($table, ' ') !== false ? substr($table, strripos($table, ' ') + 1) : $table);
									
									$this->model->order_by($table . '.' . $key, $val['direction'], $val['escape']);
								}
							}
						}
					}
				}
			}
			
			if($this->_group_by)
			{
				$this->model->group_by($this->_group_by);
			}
		}
		
		return $this;
	}
	
	/**
	 * _run_query
	 * Run the query of stored parameter
	 *
	 * @access		private
	 */
	private function _run_query($table = null)
	{
		$output										= array
		(
			'results'								=> $this->_query($table)->model->get($table, $this->_limit, $this->_offset)->result()
		);
		
		$output['total']							= ($output['results'] && !in_array($this->_method, array('create', 'read', 'update', 'delete')) ? $this->_query($table)->model->count_all_results($table) : null);
		
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
		/* unset previous upload data */
		unset_userdata('_upload_data');
		
		/* check if app on demo mode */
		if($this->_restrict_on_demo)
		{
			return throw_exception(403, phrase('this_feature_is_disabled_in_demo_mode'), (!$this->_api_request ? $this->_redirect_back : null));
		}
		
		if('update' == $this->_method && !$this->_where && !$this->_insert_on_update_fail)
		{
			return throw_exception(404, phrase('the_data_you_want_to_update_was_not_found'), (!$this->_api_request ? $this->_redirect_back : null));
		}
		
		/* serialize the fields */
		$serialized									= $this->serialize($data);
		
		if(service('request')->getPost() && is_array($serialized) && sizeof($serialized) > 0)
		{
			$validation								= false;
			
			foreach($serialized[0] as $key => $val)
			{
				$type								= $val['type'];
				
				if(((in_array('image', $type) || in_array('images', $type) || in_array('file', $type) || in_array('files', $type)) && in_array($key, $this->_unset_field)) || (in_array($key, $this->_unset_field) && !isset($this->_set_default[$key])) || in_array('disabled', $type)) continue;
				
				if(in_array('image', $type))
				{
					$validation						= true;
					
					$this->form_validation->setRule($key, (isset($this->_set_alias[$key]) ? $this->_set_alias[$key] : ucwords(str_replace('_', ' ', $key))), 'validate_upload[' . $key . '.image]');
				}
				elseif(in_array('images', $type))
				{
					$validation						= true;
					
					$this->form_validation->setRule($key . '.*', (isset($this->_set_alias[$key]) ? $this->_set_alias[$key] : ucwords(str_replace('_', ' ', $key))), 'validate_upload[' . $key . '.image]');
				}
				elseif(in_array('file', $type))
				{
					$validation						= true;
					
					$this->form_validation->setRule($key, (isset($this->_set_alias[$key]) ? $this->_set_alias[$key] : ucwords(str_replace('_', ' ', $key))), 'validate_upload[' . $key . ']');
				}
				elseif(in_array('files', $type))
				{
					$validation						= true;
					
					$this->form_validation->setRule($key . '.*', (isset($this->_set_alias[$key]) ? $this->_set_alias[$key] : ucwords(str_replace('_', ' ', $key))), 'validate_upload[' . $key . ']');
				}
				elseif(in_array('carousels', $type))
				{
					$validation						= true;
					
					$this->form_validation->setRule($key . '.background.*', (isset($this->_set_alias[$key]) ? $this->_set_alias[$key] : ucwords(str_replace('_', ' ', $key))), 'validate_upload[' . $key . '.image]');
					$this->form_validation->setRule($key . '.thumbnail.*', (isset($this->_set_alias[$key]) ? $this->_set_alias[$key] : ucwords(str_replace('_', ' ', $key))), 'validate_upload[' . $key . '.image]');
				}
				elseif(in_array('faqs', $type))
				{
					$validation						= true;
					
					$this->form_validation->setRule($key . '.question.*', phrase('question') . ' ' . (isset($this->_set_alias[$key]) ? $this->_set_alias[$key] : ucwords(str_replace('_', ' ', $key))), 'trim|required');
					$this->form_validation->setRule($key . '.answer.*', phrase('answer') . ' ' . (isset($this->_set_alias[$key]) ? $this->_set_alias[$key] : ucwords(str_replace('_', ' ', $key))), 'trim|required');
				}
				elseif(in_array('password', $type))
				{
					$validation						= true;
					
					if(service('request')->getPost($key))
					{
						$this->form_validation->setRule($key, (isset($this->_set_alias[$key]) ? $this->_set_alias[$key] : ucwords(str_replace('_', ' ', $key))), 'min_length[6]');
						$this->form_validation->setRule($key . '_confirmation', phrase('confirmation') . ' ' . (isset($this->_set_alias[$key]) ? $this->_set_alias[$key] : ucwords(str_replace('_', ' ', $key))), ('create' == $this->_method ? 'required|matches[' . $key . ']' : 'matches[' . $key . ']'));
					}
				}
				elseif(in_array('encryption', $type) && $val['validation'])
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
				else
				{
					$validation_suffix				= null;
					
					if(in_array('yearpicker', $type))
					{
						$validation_suffix			= '|valid_year';
					}
					elseif(in_array('hour', $type))
					{
						$validation_suffix			= '|numeric|max_length[2]';
					}
					elseif(in_array('date_only', $type))
					{
						$validation_suffix			= '|numeric|max_length[2]';
					}
					elseif(in_array('date', $type) || in_array('datepicker', $type))
					{
						$validation_suffix			= '|valid_date';
					}
					
					if($val['validation'] && !isset($this->_set_default[$key]))
					{
						$validation					= true;
						
						if(is_array(service('request')->getPost($key)))
						{
							$this->form_validation->setRule($key . '.*', (isset($this->_set_alias[$key]) ? $this->_set_alias[$key] : ucwords(str_replace('_', ' ', $key))), 'trim|' . $val['validation']);
						}
						else
						{
							$this->form_validation->setRule($key, (isset($this->_set_alias[$key]) ? $this->_set_alias[$key] : ucwords(str_replace('_', ' ', $key))), 'trim|' . $val['validation'] . $validation_suffix);
						}
					}
					elseif($validation_suffix)
					{
						$this->form_validation->setRule($key, (isset($this->_set_alias[$key]) ? $this->_set_alias[$key] : ucwords(str_replace('_', ' ', $key))), 'trim' . $validation_suffix);
					}
				}
			}
			
			if($validation && $this->form_validation->run(service('request')->getPost()) === false)
			{
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
					
					/* skip field because it were disable */
					if(((in_array('image', $type) || in_array('images', $type) || in_array('file', $type) || in_array('files', $type)) && in_array($field, $this->_unset_field)) || (in_array($field, $this->_unset_field) && !isset($this->_set_default[$field])) || in_array('disabled', $type)) continue;
					
					if(array_key_exists($field, service('request')->getPost()) || array_intersect($type, array('current_timestamp', 'image', 'images', 'file', 'files', 'to_slug', 'current_user', 'carousels', 'faqs')))
					{
						if(in_array('password', $type))
						{
							if(service('request')->getPost($field))
							{
								$prepare[$field]	= password_hash(service('request')->getPost($field) . ENCRYPTION_KEY, PASSWORD_DEFAULT);
							}
						}
						elseif(in_array('encryption', $type))
						{
							if(service('request')->getPost($field))
							{
								$prepare[$field]	= base64_encode(service('encrypter')->encrypt(service('request')->getPost($field)));
							}
						}
						elseif(in_array('image', $type))
						{
							if(isset($this->_upload_data[$field]) && is_array($this->_upload_data[$field]))
							{
								$this->_old_files[]					= $this->model->select($field)->get_where($this->_from, $this->_where, 1)->row($field);
								
								foreach($this->_upload_data[$field] as $src)
								{
									$prepare[$field]				= $src;
								}
							}
							else
							{
								if('create' == $this->_method)
								{
									$prepare[$field]				= 'placeholder.png';
								}
								else
								{
									unset($prepare[$field]);
								}
							}
						}
						elseif(in_array('images', $type) || in_array('file', $type) || in_array('files', $type))
						{
							$old_num								= 0;
							$uploaded								= array();
							$file_lists								= service('request')->getPost('fileuploader-list-' . $field);
							$file_lists								= json_decode($file_lists, true);
							$old_files								= json_decode($value['content'], true);
							
							if(service('request')->getPost($field . '_label'))
							{
								$label_array						= service('request')->getPost($field . '_label');
							}
							else
							{
								$label_array						= array();
							}
							
							if(!is_array($old_files))
							{
								$old_files							= array();
							}
							
							if($old_files)
							{
								$new_files							= array();
								
								if(is_array($file_lists) && sizeof($file_lists) > 0)
								{
									foreach($file_lists as $file => $src)
									{
										$src						= basename($src);
										$new_files[]				= $src;
									}
								}
								
								foreach($old_files as $file => $src)
								{
									if(in_array($file, $new_files))
									{
										$label						= (in_array('file', $type) ? $label_array : (isset($label_array[$old_num]) ? $label_array[$old_num] : $src));
										$uploaded[$file]			= $label;
										$old_num++;
									}
									else
									{
										$this->_old_files[]			= $file;
									}
								}
								
								$prepare[$field]					= json_encode($uploaded);
							}
							
							if(isset($this->_upload_data[$field]) && is_array($this->_upload_data[$field]))
							{
								foreach($this->_upload_data[$field] as $file => $src)
								{
									$num							= ($file + $old_num);
									$label							= (in_array('file', $type) ? $label_array : (isset($label_array[$num]) ? $label_array[$num] : $src));
									$uploaded[$src]					= $label;
								}
								
								$prepare[$field]					= json_encode($uploaded);
							}
						}
						elseif(in_array('carousels', $type))
						{
							$carousels								= service('request')->getPost($field);
							$items									= array();
							$uploaded								= array();
							
							if($carousels && isset($carousels['title']) && sizeof($carousels['title']) > 0)
							{
								foreach($carousels['title'] as $key => $val)
								{
									$items[$key]					= array
									(
										'title'						=> $val,
										'description'				=> (isset($carousels['description'][$key]) ? $carousels['description'][$key] : ''),
										'link'						=> (isset($carousels['link'][$key]) ? $carousels['link'][$key] : ''),
										'label'						=> (isset($carousels['label'][$key]) ? $carousels['label'][$key] : '')
									);
									
									if(isset($this->_upload_data[$field]['background'][$key]) && $this->_upload_data[$field]['background'][$key])
									{
										$items[$key]['background']	= $this->_upload_data[$field]['background'][$key];
									}
									else
									{
										$items[$key]['background']	= (isset($carousels['default_background'][$key]) ? $carousels['default_background'][$key] : '');
									}
									
									if(isset($this->_upload_data[$field]['thumbnail'][$key]) && $this->_upload_data[$field]['thumbnail'][$key])
									{
										$items[$key]['thumbnail']	= $this->_upload_data[$field]['thumbnail'][$key];
									}
									else
									{
										$items[$key]['thumbnail']	= (isset($carousels['default_thumbnail'][$key]) ? $carousels['default_thumbnail'][$key] : '');
									}
								}
							}
							
							$prepare[$field]						= json_encode($items);
						}
						elseif(in_array('faqs', $type))
						{
							$faqs					= service('request')->getPost($field);
							$items					= array();
							
							if(isset($faqs['question']) && sizeof($faqs['question']) > 0)
							{
								foreach($faqs['question'] as $key => $val)
								{
									$items[]		= array
									(
										'question'	=> $val,
										'answer'	=> (isset($faqs['answer'][$key]) ? $faqs['answer'][$key] : null)
									);
								}
							}
							
							$prepare[$field]		= json_encode(json_fixer($items));
						}
						elseif(in_array('attributes', $type))
						{
							$attributes				= service('request')->getPost($field);
							$items					= array();
							
							if($attributes && isset($attributes['label']) && sizeof($attributes['label']) > 0)
							{
								foreach($attributes['label'] as $key => $val)
								{
									$items[]	= array
									(
										'label'		=> $val,
										'value'		=> (isset($attributes['value'][$key]) ? $attributes['value'][$key] : null)
									);
								}
							}
							
							$prepare[$field]		= json_encode(json_fixer($items));
						}
						elseif(in_array('boolean', $type))
						{
							$prepare[$field]		= service('request')->getPost($field);
						}
						elseif(in_array('current_timestamp', $type))
						{
							$prepare[$field]		= date('Y-m-d H:i:s');
						}
						elseif(in_array('date', $type) || in_array('datepicker', $type))
						{
							$prepare[$field]		= date('Y-m-d', strtotime(service('request')->getPost($field)));
						}
						elseif(in_array('datetime', $type))
						{
							$prepare[$field]		= date('Y-m-d H:i:s', strtotime(service('request')->getPost($field)));
						}
						elseif(in_array('monthpicker', $type))
						{
							$prepare[$field]		= get_userdata('year') . '-' . service('request')->getPost($field) . '-01';
						}
						elseif(in_array('price_format', $type))
						{
							$value					= trim(service('request')->getPost($field));
							$value					= str_replace(',', '', $value);
							$prepare[$field]		= $value;
						}
						elseif(in_array('number_format', $type))
						{
							$value					= trim(service('request')->getPost($field));
							$value					= str_replace(',', '', $value);
							$prepare[$field]		= $value;
						}
						elseif(in_array('to_slug', $type))
						{
							if(service('request')->getPost($field))
							{
								$title				= service('request')->getPost($field);
							}
							elseif(service('request')->getPost($value['parameter']))
							{
								$title				= service('request')->getPost($value['parameter']);
							}
							else
							{
								$title				= time();
							}
							
							$prepare[$field]		= format_slug($title);
						}
						elseif(in_array('current_user', $type))
						{
							$prepare[$field]		= get_userdata('user_id');
						}
						else
						{
							$prepare[$field]		= (is_array(service('request')->getPost($field)) ? json_encode(service('request')->getPost($field)) : service('request')->getPost($field));
						}
						
						if(!in_array('to_slug', $type) && !in_array('password', $type) && !in_array('encryption', $type))
						{
							if(isset($_POST[$field]) && null == $_POST[$field])
							{
								$prepare[$field]	= '';
							}
						}
					}
					
					if(isset($this->_set_default[$field]) && $this->_set_default[$field])
					{
						$prepare[$field]			= $this->_set_default[$field];
					}
					elseif(in_array('boolean', $type) && !service('request')->getPost($field) && !in_array($field, $this->_unset_field))
					{
						$prepare[$field]			= 0;
					}
				}
				
				if($prepare && in_array('create', array($this->_method, $this->_set_method)))
				{
					$this->insert_data($this->_from, $prepare);
				}
				elseif($prepare && in_array('update', array($this->_method, $this->_set_method)))
				{
					$this->update_data($this->_from, $prepare, $this->_where);
				}
				else
				{
					return throw_exception(403, phrase('the_method_you_requested_is_not_acceptable') . ' (' . env('REQUEST_METHOD'). ')', (!$this->_api_request ? $this->_redirect_back : null));
				}
			}
		}
		else
		{
			return throw_exception(404, phrase('no_data_can_be_executed'), (!$this->_api_request ? $this->_redirect_back : null));
		}
	}
	
	/**
	 * _humanize_array
	 * Make an array (include multidimensional) is readable by human
	 *
	 * @access		private
	 */
	private function _humanize_array($data = array(), $children = false)
	{
		$items										= null;
		
		foreach($data as $key => $val)
		{
			$ab										= $key;
			
			if(is_array($val))
			{
				/* if value is array, loop it once again */
				$ab									= $this->_humanize_array($val, true);
			}
			else
			{
				if(is_numeric($key))
				{
					$key							= null;
				}
				$ab									= '<label>' . ($key && $val ? ' <i class="mdi mdi-arrow-right text-muted"></i> ' : null) . $val . '</label>';
			}
			
			$items									.= '
				<li>
					<label>
						<i class="mdi mdi-square-outline"></i>
						&nbsp;
						' . $key . '
					</label>
					' . $ab . '
				</li>
			';
		}
		
		return '
			<ul class="list-unstyled' . ($children ? ' ml-3' : null) . '">
				' . $items . '
			</ul>
		';
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
				'UNIX_TIMESTAMP(valid_until) >= '	=> time()
			),
			1
		)
		->row();
		
		if(!$api_service && $api_key == sha1(ENCRYPTION_KEY . service('request')->getHeaderLine('X-ACCESS-TOKEN')))
		{
			$api_service							= (object) array
			(
				'ip_range'							=> null,
				'method'							=> json_encode(array(env('REQUEST_METHOD'))),
				'status'							=> 1
			);
		}
		
		if(!$api_service)
		{
			return throw_exception(403, phrase('your_api_key_is_not_eligible_to_access_the_requested_module_or_already_expired'));
		}
		elseif(!$api_service->status)
		{
			return throw_exception(403, phrase('the_api_service_you_requested_is_temporary_deactivated'));
		}
		elseif(!in_array(env('REQUEST_METHOD'), json_decode($api_service->method, true)))
		{
			return throw_exception(403, phrase('your_api_key_is_not_eligible_to_using_the_requested_method') . ': ' . env('REQUEST_METHOD'));
		}
		elseif($api_service->ip_range && !$this->_ip_in_range($api_service->ip_range))
		{
			return throw_exception(403, phrase('this_source_is_not_accessible_from_your_device'));
		}
		
		$cookie										= $this->model->select
		('
			data
		')
		->get_where
		(
			'app__sessions',
			array
			(
				'id'								=> service('request')->getHeaderLine('X-ACCESS-TOKEN'),
				'timestamp >= '						=> strtotime('-1 days')
			),
			1
		)
		->row('data');
		
		if($cookie)
		{
			session_decode($cookie);
			
			$_SESSION['access_token']				= service('request')->getHeaderLine('X-ACCESS-TOKEN');
			
			set_userdata(array_filter($_SESSION));
		}
		
		$this->_set_language(get_userdata('language_id'));
		
		$this->_api_request							= true;
		$this->_api_request_parameter				= json_decode($api_service->method, true);
		
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
		
		if(in_array(env('REMOTE_ADDR'), $whitelist))
		{
			return true;
		}
		else
		{
			foreach($whitelist as $key => $val)
			{
				$wildcardPos						= strpos($val, '*');
				
				if($wildcardPos !== false && substr(env('REMOTE_ADDR'), 0, $wildcardPos) . '*' == $val)
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
		$this->agent								= service('request')->getUserAgent();
		
		if($this->agent->isBrowser())
		{
			$user_agent								= $this->agent->getBrowser() . ' ' . $this->agent->getVersion();
		}
		elseif($this->agent->isRobot())
		{
			$user_agent								= $this->agent->getRobot();
		}
		elseif($this->agent->isMobile())
		{
			$user_agent								= $this->agent->getMobile();
		}
		else
		{
			return false;
		}
		
		$prepare									= array
		(
			'ip_address'							=> (service('request')->hasHeader('x-forwarded-for') ? service('request')->getHeaderLine('x-forwarded-for') : service('request')->getIPAddress()),
			'browser'								=> $user_agent,
			'platform'								=> $this->agent->getPlatform(),
			'timestamp'								=> date('Y-m-d H:i:s')
		);
		
		$query										= $this->model->get_where
		(
			'app__visitor_logs',
			array
			(
				'ip_address'						=> $prepare['ip_address'],
				'DATE(timestamp)'					=> date('Y-m-d')
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
					$this->model->or_like('locale', $val);
				}
				else
				{
					$this->model->like('locale', $val);
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
			
			if(1 == get_userdata('user_id'))
			{
				echo $this->model->last_query();exit;
			}
			
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
		
		/* set default language */
		if(is_dir(APPPATH . 'Language' . DIRECTORY_SEPARATOR . $language_code))
		{
			set_userdata('language', $language_code);
			
			service('language')->setLocale($language_code);
		}
	}
}
