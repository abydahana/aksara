<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * CodeIgniter Template Class
 *
 * Build your CodeIgniter pages much easier with partials, breadcrumbs, layouts and themes
 *
 * @package			CodeIgniter
 * @subpackage		Libraries
 * @category		Libraries
 * @author			Philip Sturgeon
 * @license			http://philsturgeon.co.uk/code/dbad-license
 * @link			http://getsparks.org/packages/template/show
 *
 * The file has been rewritten in order to match with the Aksara's requirements
 *
 * @version			2.1.0
 * @author			Aby Dahana
 * @profile			abydahana.github.io
 */
class Template
{
	private $_css = null;
	private $_js = null;
	
	private $_module = '';
	private $_controller = '';
	private $_method = '';

	private $_theme = NULL;
	private $_theme_path = NULL;
	private $_layout = FALSE; // By default, dont wrap the view with anything
	private $_layout_subdir = ''; // Layouts and partials will exist in views/layouts
	// but can be set to views/foo/layouts with a subdirectory

	private $_title = '';
	private $_metadata = array();

	private $_partials = array();

	private $_title_separator = ' | ';

	private $_parser_enabled = TRUE;
	private $_parser_body_enabled = TRUE;

	private $_theme_locations = array();

	private $_is_mobile = FALSE;

	// Minutes that cache will be alive for
	private $cache_lifetime = 0;

	private $_ci;

	private $_data = array();

	/**
	 * Constructor - Sets Preferences
	 *
	 * The constructor can be passed an array of config values
	 */
	public function __construct($config = array())
	{
		$this->_ci =& get_instance();

		if ( ! empty($config))
		{
			$this->initialize($config);
		}

		log_message('debug', 'Template Class Initialized');
	}

	// --------------------------------------------------------------------

	/**
	 * Initialize preferences
	 *
	 * @access	public
	 * @param	array
	 * @return	void
	 */
	function initialize($config = array())
	{
		foreach ($config as $key => $val)
		{
			if ($key == 'theme' AND $val != '')
			{
				$this->set_theme($val);
				continue;
			}

			$this->{'_'.$key} = $val;
		}

		// No locations set in config?
		if ($this->_theme_locations === array())
		{
			// Let's use this obvious default
			$this->_theme_locations = array(APPPATH . 'themes/');
		}
		
		// Theme was set
		if ($this->_theme)
		{
			$this->set_theme($this->_theme);
		}

		// If the parse is going to be used, best make sure it's loaded
		if ($this->_parser_enabled === TRUE)
		{
			$this->_ci->load->library('parser');
		}

		// Modular Separation / Modular Extensions has been detected
		if (method_exists( $this->_ci->router, 'fetch_module' ))
		{
			$this->_module 	= $this->_ci->router->fetch_module();
		}

		// What controllers or methods are in use
		$this->_controller	= $this->_ci->router->fetch_class();
		$this->_method 		= $this->_ci->router->fetch_method();

		// Load user agent library if not loaded
		$this->_ci->load->library('user_agent');

		// We'll want to know this later
		$this->_is_mobile	= $this->_ci->agent->is_mobile();
	}

	// --------------------------------------------------------------------

	/**
	 * Build the entire HTML output combining partials, layouts and views.
	 *
	 * @access	public
	 * @param	string
	 * @return	void
	 */
	public function build($view, $data = array(), $return = FALSE)
	{
		// Set whatever values are given. These will be available to all view files
		is_array($data) OR $data = (array) $data;

		// Merge in what we already have with the specific data
		$this->_data = array_merge($this->_data, $data);

		// We don't need you any more buddy
		unset($data);

		if (empty($this->_title))
		{
			$this->_title = $this->_guess_title();
		}
		// Output template variables to the template
		$template['title']	= $this->_title;
		$template['metadata']	= implode("\n\t\t", $this->_metadata);
		$template['description'] = (isset($this->_data['meta']->description) ? $this->_data['meta']->description : null);
		$template['partials']	= array();

		// Assign by reference, as all loaded views will need access to partials
		$this->_data['template'] =& $template;

		foreach ($this->_partials as $name => $partial)
		{
			// We can only work with data arrays
			is_array($partial['data']) OR $partial['data'] = (array) $partial['data'];

			// If it uses a view, load it
			if (isset($partial['view']))
			{
				$template['partials'][$name] = $this->_find_view($partial['view'], $partial['data']);
			}

			// Otherwise the partial must be a string
			else
			{
				if ($this->_parser_enabled === TRUE)
				{
					$partial['string'] = $this->_ci->parser->parse_string($partial['string'], $this->_data + $partial['data'], TRUE, TRUE);
				}

				$template['partials'][$name] = $partial['string'];
			}
		}

		// Disable sodding IE7's constant cacheing!!
		$this->_ci->output->set_header('Expires: Sat, 01 Jan 2000 00:00:01 GMT');
		$this->_ci->output->set_header('Cache-Control: no-store, no-cache, must-revalidate');
		$this->_ci->output->set_header('Cache-Control: post-check=0, pre-check=0, max-age=0');
		$this->_ci->output->set_header('Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' );
		$this->_ci->output->set_header('Pragma: no-cache');

		// Let CI do the caching instead of the browser
		$this->_ci->output->cache($this->cache_lifetime);

		// Test to see if this file
		$this->_body = $this->_find_view($view, array(), $this->_parser_body_enabled);

		// Want this file wrapped with a layout file?
		if ($this->_layout)
		{
			// Added to $this->_data['template'] by refference
			$template['body'] = $this->_css . $this->_body . $this->_js;

			// Find the main body and 3rd param means parse if its a theme view (only if parser is enabled)
			$this->_body =  self::_load_view($this->_layout, $this->_data, TRUE, self::_find_view_folder());
		}

		// Want it returned or output to browser?
		if ( ! $return)
		{
			$this->_ci->output->set_output($this->_body);
		}

		return $this->_body;
	}


	/**
	 * Which theme are we using here?
	 *
	 * @access	public
	 * @param	string	$theme	Set a theme for the template library to use
	 * @return	void
	 */
	public function set_theme($theme = NULL)
	{
		$this->_theme = $theme;
		foreach ($this->_theme_locations as $location)
		{
			if ($this->_theme && file_exists($location.$this->_theme))
			{
				$this->_theme_path = rtrim($location.$this->_theme.'/');
				break;
			}
		}

		return $this;
	}

	/**
	 * Get the current theme
	 *
	 * @access public
	 * @return string	The current theme
	 */
	public function get_theme()
	{
		return $this->_theme;
	}

	/**
	 * Get the current theme path
	 *
	 * @access	public
	 * @return	string The current theme path
	 */
	public function get_theme_path()
	{
		return $this->_theme_path;
	}

	/**
	 * Set the title of the page
	 *
	 * @access	public
	 * @param	string
	 * @return	void
	 */
	public function title()
	{
		// If we have some segments passed
		if (func_num_args() >= 1)
		{
			$title_segments = func_get_args();
			$this->_title = implode($this->_title_separator, $title_segments);
		}

		return $this;
	}

	/**
	 * Set a view partial
	 *
	 * @access	public
	 * @param	string
	 * @param	string
	 * @param	boolean
	 * @return	void
	 */
	public function set_partial($name, $view, $data = array())
	{
		$this->_partials[$name] = array('view' => $view, 'data' => $data);
		return $this;
	}

	/**
	 * Set a view partial
	 *
	 * @access	public
	 * @param	string
	 * @param	string
	 * @param	boolean
	 * @return	void
	 */
	public function inject_partial($name, $string, $data = array())
	{
		$this->_partials[$name] = array('string' => $string, 'data' => $data);
		return $this;
	}
	
	/**
	 * view_template
	 * Get template from the module structure
	 *
	 * @access	public
	 * @param	string	$view
	 * @return	string
	 */
	public function view_template($view = 'index', $data = array(), $language = null)
	{
		/* get current classname */
		$classname								= ($this->_ci->router->fetch_module() != $this->_ci->router->fetch_class() ? $this->_ci->router->fetch_class() . '/' : null);
		
		/* get the module location to detect the view path */
		$view_path								= str_ireplace('/controllers/', '/views/', $this->_ci->router->fetch_directory() . $classname . $view);
		
		/* safe check for same class and module name */
		if(stripos($view_path, '/' . $classname . $classname) !== false)
		{
			$view_path							= str_replace('/' . $classname . $classname, '/' . $classname, $view_path);
		}
		
		/* remove first jumps path to check the file existence */
		$view_path_check						= substr($view_path, 3);
		
		/* add suffix to view to detect if mobile or modal template is sets */
		$suffix									= ($this->_is_mobile ? '_mobile' : ('modal' == $this->_ci->input->post('prefer') ? '_modal' : null));
		
		/* get current classname */
		$classname								= $this->_ci->router->fetch_class();
		
		/**
		 * fix the view path location
		 * remove or comment it if error's persist
		 */
		$view_path								= '../' . $view_path;
		
		/**
		 * find views based from module path and active language
		 */
		if(strpos($view_path_check, 'views/' . $classname . '/') !== false && file_exists(APPPATH . preg_replace('~\/(views\/' . $classname . '\/)~', '/views/' . $classname . '/' . $language . '/', $view_path_check) . $suffix . '.php'))
		{
			$view								= preg_replace('~\/(views\/' . $classname . '\/)~', '/views/' . $classname . '/' . $language . '/', $view_path) . $suffix;
		}
		elseif(strpos($view_path_check, 'views/' . $classname . '/') !== false && file_exists(APPPATH . preg_replace('~\/(views\/' . $classname . '\/)~', '/views/' . $classname . '/' . $language . '/', $view_path_check) . '.php'))
		{
			$view								= preg_replace('~\/(views\/' . $classname . '\/)~', '/views/' . $classname . '/' . $language . '/', $view_path);
		}
		elseif(file_exists(APPPATH . preg_replace('~\/(views)~', '/views/' . $language, $view_path_check) . $suffix . '.php'))
		{
			$view								= preg_replace('~\/(views)~', '/views/' . $language, $view_path) . $suffix;
		}
		elseif(file_exists(APPPATH . preg_replace('~\/(views)~', '/views/' . $language, $view_path_check) . '.php'))
		{
			$view								= preg_replace('~\/(views)~', '/views/' . $language, $view_path);
		}
		
		/**
		 * find views based from module path
		 */
		elseif(file_exists(APPPATH . $view_path_check . $suffix . '.php'))
		{
			$view								= $view_path . $suffix;
		}
		elseif(file_exists(APPPATH . $view_path_check . '.php'))
		{
			$view								= $view_path;
		}
		
		/**
		 * find views based from module name and active language
		 */
		elseif(file_exists(preg_replace('~\/(views)~', '/views/' . $language, APPPATH . dirname($view_path_check) . ($classname ? '/' . $classname : null)) . $suffix . '.php'))
		{
			$view								= preg_replace('~\/(views)~', '/views/' . $language, dirname($view_path) . ($classname ? '/' . $classname : null)) . $suffix;
		}
		elseif(file_exists(preg_replace('~\/(views)~', '/views/' . $language, APPPATH . dirname($view_path_check) . ($classname ? '/' . $classname : null)) . '.php'))
		{
			$view								= preg_replace('~\/(views)~', '/views/' . $language, dirname($view_path) . ($classname ? '/' . $classname : null));
		}
		
		/**
		 * find views based from module name
		 */
		elseif(file_exists(APPPATH . dirname($view_path_check) . ($classname ? '/' . $classname : null) . $suffix . '.php'))
		{
			$view								= dirname($view_path) . ($classname ? '/' . $classname : null) . $suffix;
		}
		elseif(file_exists(APPPATH . dirname($view_path_check) . ($classname ? '/' . $classname : null) . '.php'))
		{
			$view								= dirname($view_path) . ($classname ? '/' . $classname : null);
		}
		
		/**
		 * find views based from module name and active language
		 */
		elseif(file_exists(preg_replace('~\/(views)~', '/views/' . $language, APPPATH . dirname($view_path_check)) . $suffix . '.php'))
		{
			$view								= preg_replace('~\/(views)~', '/views/' . $language, dirname($view_path)) . $suffix;
		}
		elseif(file_exists(preg_replace('~\/(views)~', '/views/' . $language, APPPATH . dirname($view_path_check)) . '.php'))
		{
			$view								= preg_replace('~\/(views)~', '/views/' . $language, dirname($view_path));
		}
		
		/**
		 * find views based from module name
		 */
		elseif(file_exists(APPPATH . dirname($view_path_check) . $suffix . '.php'))
		{
			$view								= dirname($view_path) . $suffix;
		}
		elseif(file_exists(APPPATH . dirname($view_path_check) . '.php'))
		{
			$view								= dirname($view_path);
		}
		
		/**
		 * use the system template instead
		 */
		elseif(isset($data->results) && !empty($data->results) && file_exists(APPPATH . 'views/templates/' . $view . $suffix . '.php'))
		{
			$view								= 'templates/' . $view . $suffix;
		}
		elseif(isset($data->results) && !empty($data->results) && file_exists(APPPATH . 'views/templates/' . $view . '.php'))
		{
			$view								= 'templates/' . $view;
		}
		else
		{
			$view								= 'templates/error';
		}
		
		return $view;
	}
	
	/**
	 * render
	 * Rendering the data and display into device
	 *
	 * @access	public
	 * @param	string	$view
	 * @param	mixed	$data
	 * @param	mixed	$breadcrumb
	 * @return	string as HTML
	 */
	public function render($view = null, $data = array(), $breadcrumb = array(), $language = null)
	{
		/* get view from module structure */
		$view									= $this->view_template($view, $data, $language);
		
		// Generate output
		$title									= (isset($data->meta->title) ? $data->meta->title : phrase('title_was_not_set'));
		$description							= (isset($data->meta->description) ? $data->meta->description : null);
		$icon									= (isset($data->meta->icon) ? $data->meta->icon : 'mdi mdi-table');
		
		if(file_exists(str_replace(array('../', '/views/'), array(null, '/css/'), $view) . '.css'))
		{
			$this->_css							= '<link rel="stylesheet" type="text/css" href="' . base_url(str_replace(array('../', '/views/'), array(null, '/css/'), $view) . '.css', array('cache' => time())) . '" />';
		}
		elseif(file_exists(str_replace(array('../', '/modules/', '/views/'), array(null, '/aksara/modules/', '/css/'), $view) . '.css'))
		{
			$this->_css							= '<link rel="stylesheet" type="text/css" href="' . base_url(str_replace(array('../', '/modules/', '/views/'), array(null, '/aksara/modules/', '/css/'), $view) . '.css', array('cache' => time())) . '" />';
		}
		
		if(file_exists(str_replace(array('../', '/views/'), array(null, '/js/'), $view) . '.js'))
		{
			$this->_js							= '<script language="javascript" type="text/javascript" src="' . base_url(str_replace(array('../', '/views/'), array(null, '/js/'), $view) . '.js', array('cache' => time())) . '"></script>';
		}
		elseif(file_exists(str_replace(array('../', '/modules/', '/views/'), array(null, '/aksara/modules/', '/js/'), $view) . '.js'))
		{
			$this->_js							= '<script language="javascript" type="text/javascript" src="' . base_url(str_replace(array('../', '/modules/', '/views/'), array(null, '/aksara/modules/', '/js/'), $view) . '.js', array('cache' => time())) . '"></script>';
		}
		
		/* check if request is from Promise */
		if($this->_ci->input->is_ajax_request())
		{
			/* format output to html */
			$data->html							= $this->_css . preg_replace(array('/\s+/', '/\n/', '/\>[^\S ]+/s', '/[^\S ]+\</s', '/(\s)+/s', '/(\>)\s*(\<)/m'), array(' ', '', '>', '<', '\\1', '$1$2'), $this->_ci->load->view($view, $data, true)) . $this->_js;
			
			/* make a return */
			return make_json($data);
		}
		
		/* else, display it as html markup */
		else
		{
			$this->_ci->load->helper('menu');
			$this->title($title);
			$this->build($view, $data);
		}
	}
	
	/**
	 * breadcrumb
	 * Generate breadcrumb
	 * 
	 * @param	mixed	$data
	 * @param	string	$title
	 * @return	mixed
	 */
	public function breadcrumb($data = array(), $title = null, $found = null)
	{
		$slug									= null;
		$checker								= $this->_ci->uri->segment_array();
		$params									= $this->_ci->input->get();
		$params['per_page']						= null;
		$params['q']							= null;
		$params['order']						= null;
		$params['sort']							= null;
		
		if(!$data || !is_array($data))
		{
			foreach($checker as $key => $val)
			{
				$data[$val]						= ucwords(str_replace('_', ' ', $val));
			}
		}
		
		$current_slug							= end($checker);
		$slug									= null;
		$output									= array
		(
			array
			(
				'url'							=> base_url('dashboard'),
				'label'							=> phrase('dashboard'),
				'icon'							=> 'mdi mdi-home'
			)
		);
		
		foreach($data as $segment => $label)
		{
			$slug								.= $segment . '/';
			if($segment && $label)
			{
				if($segment != $current_slug)
				{
					$output[]					= array
					(
						'url'					=> base_url($slug, $params),
						'label'					=> ($found ? phrase($label) : ucwords($label)),
						'icon'					=> null
					);
				}
			}
		}
		
		$output[]								= array
		(
			'url'								=> '',
			'label'								=> $title,
			'icon'								=> ''
		);
		
		/*  remove the last element of array */
		array_pop($output);
		
		return $output;
	}
	
	/**
	 * pagination
	 * Generate the pagination
	 *
	 * @param	mixed	$data
	 * @param	bool	$return
	 * @return	string as HTML
	 */
	public function pagination($data = array(), $return = true)
	{
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
		
		/* generate config parameter */
		$configs									= array
		(
			'base_url'								=> (isset($data->url) ? $data->url : current_page(null, array('per_page' => null))),
			'total_rows'	 						=> $data->total_rows,
			'per_page'		 						=> $data->per_page,
			'use_page_numbers'		 				=> true,
			'num_links'								=> 0,
			'page_query_string'						=> true,
			'full_tag_open'		 					=> '<ul class="pagination pagination-sm">',
			'full_tag_close'	 					=> '</ul>',
			'attributes'							=> array('class' => 'page-link --xhr'),
			'num_tag_open'							=> '<li class="page-item">',
			'num_tag_close'							=> '</li>',
			'cur_tag_open'							=> '<li class="page-item active"><a href="javascript:void(0)" class="page-link">',
			'cur_tag_close'		 					=> '</a></li>',
			'next_tag_open'		 					=> '<li class="page-item">',
			'next_tagl_close'						=> '</li>',
			'prev_tag_open'							=> '<li class="page-item">',
			'prev_tagl_close'	 					=> '</li>',
			'first_tag_open'	 					=> '<li class="page-item">',
			'first_tagl_close'						=> '</li>',
			'last_tag_open'							=> '<li class="page-item">',
			'last_tagl_close'						=> '</li>',
			'first_link'							=> 'First',
			'last_link'								=> 'Last',
			'prev_link'								=> '&lt;',
			'next_link'								=> '&gt;'
		);
		
		/* load pagination library and initialize config */
		$this->_ci->load->library('pagination', $configs);
		
		/* create result */
		$last_page									= ($data->total_rows > $data->per_page ? (int) ceil($data->total_rows / $data->per_page) : 1);
		$results									= $this->_ci->pagination->create_links();
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
			
			foreach($this->_ci->input->get() as $key => $val)
			{
				if(in_array($key, array('q', 'per_page'))) continue;
				
				$query_string						.= '<input type="hidden" name="' . $key . '" value="' . $val . '" />';
			}
			
			$output									= '
				<div class="row">
					<div class="col-sm-6 text-sm-center text-md-left">
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
							' . ($data->total_rows > $data->per_page ? '
							<form action="' . current_page(null, array('per_page' => null)) . '" method="POST" class="--xhr-form ml-2">
								' . $query_string . '
								<div class="input-group">
									<input type="number" name="per_page" class="form-control form-control-sm text-center" value="' . ($this->_ci->input->get('per_page') ? $this->_ci->input->get('per_page') : 1) . '" min="1" max="' . $last_page . '" />
									<div class="input-group-append">
										<button type="submit" class="btn btn-sm btn-primary">
											' . phrase('go') . '
										</button>
									</div>
								</div>
								<input type="hidden" name="token" value="' . sha1(current_page() . SALT . get_userdata('session_generated')) . '" />
							</form>
							' : null) . '
						</nav>
					</div>
				</div>
			';
		}
		else
		{
			$this->_ci->load->library('simple_html_dom');
			$html									= str_get_html($output);
			$output									= array
			(
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
					'label'							=> $li->find('a', 0)->innertext
				);
			}
			return $output;
		}
		return $output;
	}

	// find layout files, they could be mobile or web
	private function _find_view_folder()
	{
		if ($this->_ci->load->get_var('template_views'))
		{
			return $this->_ci->load->get_var('template_views');
		}

		// Base view folder
		$view_folder = APPPATH.'views/';

		// Using a theme? Put the theme path in before the view folder
		if ( ! empty($this->_theme))
		{
			$view_folder = $this->_theme_path.'views/';
		}

		// Things like views/admin/web/view admin = subdir
		if ($this->_layout_subdir)
		{
			$view_folder .= $this->_layout_subdir.'/';
		}

		// If using themes store this for later, available to all views
		$this->_ci->load->vars('template_views', $view_folder);
		
		return $view_folder;
	}

	// A module view file can be overriden in a theme
	private function _find_view($view, array $data, $parse_view = TRUE)
	{
		// Only bother looking in themes if there is a theme
		if ( ! empty($this->_theme))
		{
			foreach ($this->_theme_locations as $location)
			{
				$theme_views = array($view);

				foreach ($theme_views as $theme_view)
				{
					if (@file_exists($location . $theme_view . self::_ext($theme_view)))
					{
						return self::_load_view($theme_view, $this->_data + $data, $parse_view, $location);
					}
				}
			}
		}

		// Not found it yet? Just load, its either in the module or root view
		return self::_load_view($view, $this->_data + $data, $parse_view);
	}

	private function _load_view($view, array $data, $parse_view = TRUE, $override_view_path = NULL)
	{
		// Sevear hackery to load views from custom places AND maintain compatibility with Modular Extensions
		if ($override_view_path !== NULL)
		{
			if ($this->_parser_enabled === TRUE AND $parse_view === TRUE)
			{
				// Load content and pass through the parser
				$content = $this->_ci->parser->parse_string($this->_ci->load->file(
					$override_view_path.$view.self::_ext($view), 
					TRUE
				), $data, TRUE);
			}

			else
			{
				$this->_ci->load->vars($data);
				
				// Load it directly, bypassing $this->load->view() as ME resets _ci_view
				$content = $this->_ci->load->file(
					$override_view_path.$view.self::_ext($view),
					TRUE
				);
			}
		}

		// Can just run as usual
		else
		{
			// Grab the content of the view (parsed or loaded)
			$content = ($this->_parser_enabled === TRUE AND $parse_view === TRUE)

				// Parse that bad boy
				? $this->_ci->parser->parse($view, $data, TRUE)

				// None of that fancy stuff for me!
				: $this->_ci->load->view($view, $data, TRUE);
		}

		return $content;
	}

	private function _guess_title()
	{
		$this->_ci->load->helper('inflector');

		// Obviously no title, lets get making one
		$title_parts = array();

		// If the method is something other than index, use that
		if ($this->_method != 'index')
		{
			$title_parts[] = $this->_method;
		}

		// Make sure controller name is not the same as the method name
		if ( ! in_array($this->_controller, $title_parts))
		{
			$title_parts[] = $this->_controller;
		}

		// Is there a module? Make sure it is not named the same as the method or controller
		if ( ! empty($this->_module) AND ! in_array($this->_module, $title_parts))
		{
			$title_parts[] = $this->_module;
		}

		// Glue the title pieces together using the title separator setting
		$title = humanize(implode($this->_title_separator, $title_parts));

		return $title;
	}

	private function _ext($file)
	{
		return pathinfo($file, PATHINFO_EXTENSION) ? '' : '.php';
	}
}

// END Template class