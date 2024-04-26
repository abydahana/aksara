<?php

/**
 * This file is part of Aksara CMS, both framework and publishing
 * platform.
 *
 * @author     Aby Dahana <abydahana@gmail.com>
 * @copyright  (c) Aksara Laboratory <https://aksaracms.com>
 * @license    MIT License
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the LICENSE.txt file.
 *
 * When the signs is coming, those who don't believe at "that time"
 * have only two choices, commit suicide or become brutal.
 */

namespace Aksara\Laboratory;

use CodeIgniter\Controller;

use Aksara\Laboratory\Traits;
use Aksara\Laboratory\Model;
use Aksara\Laboratory\Permission;
use Aksara\Laboratory\Template;
use Aksara\Laboratory\Renderer\Renderer;

use Aksara\Libraries\Document;

/**
 * If it works, it ain't stupid ;)
 */
class Core extends Controller
{
    /**
     * Load trait, get dynamic properties
     */
    use Traits;

    /**
     * A flag wheter request is sent from API Client or not
     */
    protected $api_client = false;

    /**
     * Form validation
     */
    protected $form_validation;

    /**
     * Typically to access model from modules that are extending to it
     */
    protected $model;

    /**
     * Get the permission object to restrict or allow module request
     * for each user or globally
     */
    protected $permission;

    /**
     * Typically to load template property from theme
     */
    public $template;

    /**
     * A flag wheter API token is valid or not
     */
    private $_api_token;

    public function __construct()
    {
        // Start benchmarking
        service('timer')->start('elapsed_time');

        // Unset previous upload path from the session
        unset_userdata('_set_upload_path');

        // Unset previous uploaded files string from the session
        unset_userdata('_uploaded_files');

        if (strtolower(service('request')->getUserAgent()->getBrowser()) == 'internet explorer' && service('request')->getUserAgent()->getVersion() < 11) {
            // Block outdate user agent
            die('The ' . service('request')->getUserAgent()->getBrowser() . ' ' . service('request')->getUserAgent()->getVersion() . ' is no longer supported...');
        } elseif (! filter_var((service('request')->hasHeader('x-forwarded-for') ? service('request')->getHeaderLine('x-forwarded-for') : service('request')->getIPAddress()), FILTER_VALIDATE_IP)) {
            // Block invalid browser header
            exit(header('Location: https://google.com?q=' . (service('request')->hasHeader('x-forwarded-for') ? service('request')->getHeaderLine('x-forwarded-for') : service('request')->getIPAddress())));
        }

        // Load helpers
        helper(['url', 'file', 'theme', 'security', 'main', 'string', 'widget']);

        // Load and assign model class
        $this->model = new Model();

        // Load and assign permission class
        $this->permission = new Permission();

        // Load and assign validation class
        $this->form_validation = \Config\Services::validation();

        // Assign an active database driver
        $this->_db_driver = $this->model->db_driver();

        // Get match route path
        $path = (isset(service('router')->getMatchedRoute()[0]) ? service('router')->getMatchedRoute()[0] : null);

        // Get and assign the requested method
        $this->_method = service('router')->methodName();

        // Get and assign the module path
        $this->_module = ($this->_method && $path && strpos($path, $this->_method) !== false ? preg_replace('/\/' . $this->_method . '$/', '', $path) : $path);

        // Get upload path
        $upload_path = strtolower(substr(strstr(service('router')->controllerName(), '\Controllers\\'), strlen('\Controllers\\')));
        $upload_path = array_pad(explode('\\', $upload_path), 2, null);

        // Set upload path
        $this->_set_upload_path = $upload_path[1] ?? $upload_path[0];

        // Check if query string has limit
        if (is_numeric(service('request')->getGet('limit')) && service('request')->getGet('limit')) {
            // Backup original limit
            $this->_limit_backup = $this->_limit;

            // Apply the limit for query builder
            $this->_limit = service('request')->getGet('limit');
        }

        // Check if query string has offset
        if (is_numeric(service('request')->getGet('offset')) && service('request')->getGet('offset')) {
            // Apply the offset for query builder
            $this->_offset = service('request')->getGet('offset');
        }

        // Check if user is requesting token
        if ('token' === service('request')->getPost('_request') && $this->valid_token(service('request')->getPost('_token'))) {
            // Unset field from payload
            unset($_POST['_request'], $_POST['_token']);

            return make_json([
                'callback' => current_page(null, array_merge(service('request')->getPost(), ['aksara' => generate_token(service('request')->getPost()), '_request' => null, '_token' => null]))
            ]);
        }

        // Check if user is requesting theme preview
        if ('preview-theme' == service('request')->getGet('aksara_mode') && sha1(service('request')->getGet('aksara_theme') . ENCRYPTION_KEY . get_userdata('session_generated')) == service('request')->getGet('integrity_check') && is_dir(ROOTPATH . 'themes/' . service('request')->getGet('aksara_theme'))) {
            // Set the temporary theme
            $this->_set_theme = strip_tags(service('request')->getGet('aksara_theme'));
        }

        if (service('request')->getHeaderLine('X-API-KEY')) {
            // Do handshake between REST and Aksara
            $this->_handshake(service('request')->getHeaderLine('X-API-KEY'));
        } else {
            // Store access logs
            $this->_push_log();
        }

        // Set user language
        $this->_set_language(get_userdata('language_id'));
    }

    /**
     * Remap method and match the routes.
     * We give you 5 default arguments to help you to get the segments
     * from the route arguments without initializing from URI service.
     *
     * @param   mixed|null $method
     * @param   mixed|null $arg_1
     * @param   mixed|null $arg_2
     * @param   mixed|null $arg_3
     * @param   mixed|null $arg_4
     * @param   mixed|null $arg_5
     */
    public function _remap(string $method = '', string $arg_1 = '', string $arg_2 = '', string $arg_3 = '', string $arg_4 = '', string $arg_5 = '')
    {
        // Check method to prevent conflict
        if (method_exists($this, $method) && ! in_array($method, get_class_methods('\Aksara\Laboratory\Core'))) {
            // Use non conflict method
            $this->$method($arg_1, $arg_2, $arg_3, $arg_4, $arg_5);
        } else {
            // Throwback default method
            $this->index($method, $arg_1, $arg_2, $arg_3, $arg_4, $arg_5);
        }
    }

    /**
     * Debugging
     */
    public function debug(string $result_type = '')
    {
        $this->_debugging = $result_type;

        return $this;
    }

    /**
     * Function to apply demo mode
     */
    public function restrict_on_demo()
    {
        // Check if demo mode is active
        if (DEMO_MODE) {
            // Set the restriction property value
            $this->_restrict_on_demo = true;
        }

        return $this;
    }

    /**
     * Database configuration
     *
     * @param   array|string $driver
     */
    public function database_config($driver = [], string $hostname = null, int $port = null, string $username = null, string $password = null, string $database = null)
    {
        // Check if the parameter is sets with array
        if (is_array($driver) && isset($driver['driver']) && isset($driver['hostname']) && isset($driver['port']) && isset($driver['username']) && isset($driver['password']) && isset($driver['database'])) {
            // Use the array parameter as config
            $this->model->database_config($driver['driver'], $driver['hostname'], $driver['port'], $driver['username'], $driver['password'], $driver['database']);
        } else {
            // Use the strng parameter as config
            $this->model->database_config($driver, $hostname, $port, $username, $password, $database);
        }

        return $this;
    }

    /**
     * Validate the token that submitted through form
     */
    public function valid_token(string $token = null)
    {
        // Match the token validation
        if (service('request')->getPost() && (sha1(current_page() . ENCRYPTION_KEY . get_userdata('session_generated')) === $token || sha1(service('request')->getHeaderLine('Referer') . ENCRYPTION_KEY . get_userdata('session_generated')) === $token || $this->api_client)) {
            // Token match
            return true;
        }

        return false;
    }

    /**
     * Assign the parent module of current class
     */
    public function parent_module(string $module)
    {
        // Set module
        $this->_module = $module;

        return $this;
    }

    /**
     * Set up the permission of module, it's mean that only logged user can
     * access the module
     *
     * @param   array|string $permissive_user
     */
    public function set_permission($permissive_user = [], string $redirect = null)
    {
        // This mean the permission is set as true
        $this->_set_permission = true;

        // Check if permissive user is set
        if ($permissive_user && ! is_array($permissive_user)) {
            // Safe check for array
            $permissive_user = array_map('trim', explode(',', $permissive_user));
        }

        // Check if permissions is sets and make sure the user is signed in or requested from restful
        if ($this->_set_permission && ! get_userdata('is_logged') && ! $this->api_client) {
            // User isn't signed in
            return throw_exception(403, phrase('Your session has been expired'), ($redirect ? $redirect : base_url()), true);
        } elseif (! $this->permission->allow($this->_module, $this->_method, get_userdata('user_id'), $redirect)) {
            // User been signed in but blocked by group privilege
            return throw_exception(403, phrase('You do not have sufficient privileges to access the requested page'), ($redirect ? $redirect : (! service('request')->isAJAX() ? $this->_redirect_back ?? base_url() : null)));
        } elseif ($permissive_user && ! in_array(get_userdata('group_id'), $permissive_user)) {
            // User been signed in but blocked by group privilege
            return throw_exception(403, phrase('You do not have sufficient privileges to access the requested page'), ($redirect ? $redirect : (! service('request')->isAJAX() ? $this->_redirect_back ?? base_url() : null)));
        }
    }

    /**
     * Set up the method of module so it will only have one method by default
     */
    public function set_method(string $method = 'index')
    {
        // Set the method property
        $this->_method = $method;

        return $this;
    }

    /**
     * Set up the method of module so it will only have one method by default
     *
     * @return  string
     */
    public function get_method()
    {
        return $this->_method;
    }

    /**
     * Unset the method
     *
     * @param   array|string $params
     */
    public function unset_method($params = [])
    {
        // Check if parameter isn't in array format
        if (! is_array($params)) {
            // Shorthand possibility, separate with commas
            $params = array_map('trim', explode(',', $params));
        }

        // Merge array and store to property
        $this->_unset_method = array_merge($this->_unset_method, $params);

        return $this;
    }

    /**
     * Set up the theme. The front/back theme might be different
     */
    public function set_theme(string $theme = 'frontend')
    {
        // Validate the theme parameter to match with predefined config
        if (! in_array($theme, ['frontend', 'backend'])) {
            return false;
        }

        // Get site id before run query to prevent nested queue
        $site_id = get_setting('id');

        // Run query to get theme config from site settings
        $query = $this->model->select($theme . '_theme')->get_where(
            'app__settings',
            [
                'id' => $site_id
            ],
            1
        )
        ->row($theme . '_theme');

        // Set the theme with matched configuration
        $this->_set_theme = $query;

        return $this;
    }

    /**
     * Possibility to set the template if the master template isn't enough tho
     *
     * @param   array|string $params
     */
    public function set_template($params = [], string $value = null)
    {
        // Make sure the parameter is array, otherwise convert it
        if (! is_array($params)) {
            // Convert parameters as array
            $params = [
                $params => $value
            ];
        }

        // Merge array and store to property
        $this->_set_template = array_merge($this->_set_template, $params);

        return $this;
    }

    /**
     * Set up the breadcrumb
     *
     * @param   array|string $params
     */
    public function set_breadcrumb($params = [], string $value = null)
    {
        // Make sure the parameter is array, otherwise convert it
        if (! is_array($params)) {
            // Convert parameters as array
            $params = [
                $params => $value
            ];
        }

        // Merge array and store to property
        $this->_set_breadcrumb = array_merge($this->_set_breadcrumb, $params);

        return $this;
    }

    /**
     * Set the CRUD primary key when the rendered database table isn't have
     * at least one primary key
     *
     * @param   array|string $field
     */
    public function set_primary($field = [])
    {
        if (! is_array($field)) {
            // Explode commas and trim values
            $field = array_map('trim', explode(',', $field));
        }

        // Merge array and store to property
        $this->_set_primary = array_merge($this->_set_primary, $field);

        return $this;
    }

    /**
     * Set the title of module. It also will displayed as document title
     * in the browser.
     *
     * A magic string can be wrapped using double curly brackets.
     *
     * @param   array|string $params
     */
    public function set_title($params = [], string $fallback = null)
    {
        // Make sure the parameter is array, otherwise convert it
        if (! is_array($params)) {
            if (! $fallback && strpos($params, '{{') === false && strpos($params, '}}') === false) {
                // Make a fallback from original argument
                $fallback = $params;
            }

            // Convert parameters as array
            $params = [
                'index' => $params
            ];
        }

        // Merge array and store to property
        $this->_set_title = array_merge($this->_set_title, $params);

        $this->_set_title_fallback = $fallback;

        return $this;
    }

    /**
     * Set the description of module. It also will displayed as description
     * of meta.
     *
     * A magic string can be wrapped using curly brackets.
     *
     * @param   array|string $params
     */
    public function set_description($params = [], string $fallback = null)
    {
        // Make sure the parameter is array, otherwise convert it
        if (! is_array($params)) {
            if (! $fallback && strpos($params, '{{') === false && strpos($params, '}}') === false) {
                // Make a fallback from original argument
                $fallback = $params;
            }

            // Convert parameters as array
            $params = [
                'index' => $params
            ];
        }

        // Merge array and store to property
        $this->_set_description = array_merge($this->_set_description, $params);

        $this->_set_description_fallback = $fallback;

        return $this;
    }

    /**
     * Set the icon of content title, yeah we need some icon that describe
     * the module
     */
    public function set_icon($params = [], string $fallback = null)
    {
        // Make sure the parameter is array, otherwise convert it
        if (! is_array($params)) {
            if (! $fallback && strpos($params, '{{') === false && strpos($params, '}}') === false) {
                // Make a fallback from original argument
                $fallback = $params;
            }

            // Convert parameters as array
            $params = [
                'index' => $params
            ];
        }

        // Merge array and store to property
        $this->_set_icon = array_merge($this->_set_icon, $params);

        $this->_set_icon_fallback = $fallback;

        return $this;
    }

    /**
     * Set the individual message of exception
     *
     * @param   array|string $params
     */
    public function set_messages($params = [], int $code = null, string $messages = null)
    {
        // Make sure the parameter is array, otherwise convert it
        if (! is_array($params)) {
            // Convert parameters as array
            $params = [
                $params => [
                    'code' => $code,
                    'messages' => $messages
                ]
            ];
        }

        // Merge array and store to property
        $this->_set_messages = array_merge($this->_set_messages, $params);

        return $this;
    }

    /**
     * Override the existing CRUD button
     */
    public function set_button(string $button, string $value, string $label, string $icon = null, string $class = null, bool $new_tab = null)
    {
        // Push the button properties
        $this->_set_button[$button] = [
            'href' => $value,
            'label' => $label,
            'icon' => $icon,
            'class' => $class,
            'new_tab' => $new_tab
        ];

        return $this;
    }

    /**
     * Switch the view as grid
     */
    public function grid_view(string $thumbnail, string $hyperlink = null, array $parameter = [], bool $new_tab = false)
    {
        // Use grid view instead of data tables
        $_ENV['GRID_VIEW'] = true;

        // Push thumbnail source to the grid view property
        $this->_grid_view = [
            'thumbnail' => $thumbnail,
            'hyperlink' => $hyperlink,
            'parameter' => $parameter,
            'new_tab' => $new_tab
        ];

        return $this;
    }

    /**
     * Add individual filter to CRUD index table
     *
     * @param   array|string $filter
     */
    public function add_filter($filter = [], array $options = [])
    {
        // Make sure the parameter is array, otherwise convert it
        if (! is_array($filter)) {
            // Convert parameters as array
            $filter = [
                $filter => [
                    'label' => ucwords($filter),
                    'values' => $options
                ]
            ];
        }

        // Add default field's parameter
        foreach ($filter as $key => $val) {
            $filter[$key] = [
                'type' => (is_array($val['values']) ? 'select' : 'text'),
                'label' => $val['label'],
                'values' => $val['values']
            ];
        }

        // Merge array and store to property
        $this->_add_filter = array_merge($this->_add_filter, $filter);

        return $this;
    }

    /**
     * The system will autosearch when the query string is contain "q" key.
     * This method used to prevent system to search even then"q" parameter
     * is present.
     */
    public function searchable(bool $active = true)
    {
        $this->_searchable = $active;

        return $this;
    }

    /**
     * Adding the toolbar action button
     *
     * @param   array|string $url
     */
    public function add_toolbar($url, string $label = null, string $class = null, string $icon = null, array $parameter = [], bool $new_tab = false, string $attribution = null)
    {
        if (! is_array($url)) {
            $params = [
                'url' => $url,
                'label' => $label,
                'class' => $class,
                'icon' => $icon,
                'parameter' => $parameter,
                'new_tab' => $new_tab,
                'attribution' => $attribution
            ];

            $url = [$params];
        }

        // Add default field's parameter
        foreach ($url as $key => $val) {
            $url[$key] = [
                'url' => (isset($val['url']) ? $val['url'] : $val),
                'label' => (isset($val['label']) ? $val['label'] : $label),
                'class' => (isset($val['class']) ? $val['class'] : $class),
                'icon' => (isset($val['icon']) ? $val['icon'] : $icon),
                'parameter' => (isset($val['parameter']) ? $val['parameter'] : $parameter),
                'new_tab' => (isset($val['new_tab']) ? $val['new_tab'] : $new_tab),
                'attribution' => (isset($val['attribution']) ? $val['attribution'] : $attribution)
            ];
        }

        // Merge array and store to property
        $this->_add_toolbar = array_merge($this->_add_toolbar, $url);

        return $this;
    }

    /**
     * Adding the row action button
     *
     * @param   array|string $url
     */
    public function add_button($url, string $label = null, string $class = null, string $icon = null, array $parameter = [], bool $new_tab = false, string $attribution = null)
    {
        if (! is_array($url)) {
            $params = [
                'url' => $url,
                'label' => $label,
                'class' => $class,
                'icon' => $icon,
                'parameter' => $parameter,
                'new_tab' => $new_tab,
                'attribution' => $attribution
            ];

            $url = [$params];
        }

        // Add default field's parameter
        foreach ($url as $key => $val) {
            $url[$key] = [
                'url' => (isset($val['url']) ? $val['url'] : $val),
                'label' => (isset($val['label']) ? $val['label'] : $label),
                'class' => (isset($val['class']) ? $val['class'] : $class),
                'icon' => (isset($val['icon']) ? $val['icon'] : $icon),
                'parameter' => (isset($val['parameter']) ? $val['parameter'] : $parameter),
                'new_tab' => (isset($val['new_tab']) ? $val['new_tab'] : $new_tab),
                'attribution' => (isset($val['attribution']) ? $val['attribution'] : $attribution)
            ];
        }

        // Merge array and store to property
        $this->_add_button = array_merge($this->_add_button, $url);

        return $this;
    }

    /**
     * Adding the row action dropdown
     *
     * @param   array|string $url
     */
    public function add_dropdown($url, string $label = null, string $class = null, string $icon = null, array $parameter = [], bool $new_tab = false, string $attribution = null)
    {
        if (! is_array($url)) {
            $params = [
                'url' => $url,
                'label' => $label,
                'class' => $class,
                'icon' => $icon,
                'parameter' => $parameter,
                'new_tab' => $new_tab,
                'attribution' => $attribution
            ];

            $url = [$params];
        }

        // Add default field's parameter
        foreach ($url as $key => $val) {
            $url[$key] = [
                'url' => (isset($val['url']) ? $val['url'] : $val),
                'label' => (isset($val['label']) ? $val['label'] : $label),
                'class' => (isset($val['class']) ? $val['class'] : $class),
                'icon' => (isset($val['icon']) ? $val['icon'] : $icon),
                'parameter' => (isset($val['parameter']) ? $val['parameter'] : $parameter),
                'new_tab' => (isset($val['new_tab']) ? $val['new_tab'] : $new_tab),
                'attribution' => (isset($val['attribution']) ? $val['attribution'] : $attribution)
            ];
        }

        // Merge array and store to property
        $this->_add_dropdown = array_merge($this->_add_dropdown, $url);

        return $this;
    }

    /**
     * Adding CSS classes to the rendered field
     *
     * @param   array|string $params
     */
    public function add_class($params = [], string $value = null)
    {
        // Make sure the parameter is array, otherwise convert it
        if (! is_array($params)) {
            // Convert parameters as array
            $params = [
                $params => $value
            ];
        }

        // Merge array and store to property
        $this->_add_class = array_merge($this->_add_class, $params);

        return $this;
    }

    /**
     * Set the type of field
     *
     * @param   array|string $field
     * @param   array|string $type
     * @param   array|string $parameter
     * @param   null|mixed $alpha
     * @param   null|mixed $beta
     * @param   null|mixed $charlie
     */
    public function set_field($field = [], $type = [], $parameter = [], $alpha = [], $beta = null, $charlie = null, string $delta = null)
    {
        // Make sure the parameter is array, otherwise convert it
        if (! is_array($field)) {
            // Convert parameters as array
            $field = [
                $field => $type
            ];
        }

        // Add default field's parameter
        foreach ($field as $key => $type) {
            if (strpos($type, ',') !== false) {
                // Explode multiple field type into array
                $types = array_map('trim', explode(',', $type));

                // Loop multiple field type
                foreach ($types as $_key => $_type) {
                    $this->_set_field[$key][$_type] = [
                        'parameter' => (isset($field[$_type]['parameter']) ? $field[$_type]['parameter'] : $parameter),
                        'alpha' => (isset($field[$_type]['alpha']) ? $field[$_type]['alpha'] : $alpha),
                        'beta' => (isset($field[$_type]['beta']) ? $field[$_type]['beta'] : $beta),
                        'charlie' => (isset($field[$_type]['charlie']) ? $field[$_type]['charlie'] : $charlie),
                        'delta' => (isset($field[$_type]['delta']) ? $field[$_type]['delta'] : $delta)
                    ];
                }
            } else {
                // Single field type
                $this->_set_field[$key][$type] = [
                    'parameter' => (isset($field[$type]['parameter']) ? $field[$type]['parameter'] : $parameter),
                    'alpha' => (isset($field[$type]['alpha']) ? $field[$type]['alpha'] : $alpha),
                    'beta' => (isset($field[$type]['beta']) ? $field[$type]['beta'] : $beta),
                    'charlie' => (isset($field[$type]['charlie']) ? $field[$type]['charlie'] : $charlie),
                    'delta' => (isset($field[$type]['delta']) ? $field[$type]['delta'] : $delta)
                ];
            }
        }

        return $this;
    }

    /**
     * Add the tooltip on field label when hovered
     *
     * @param   array|string $params
     */
    public function set_tooltip($params = [], string $value = null)
    {
        // Make sure the parameter is array, otherwise convert it
        if (! is_array($params)) {
            // Convert parameters as array
            $params = [
                $params => $value
            ];
        }

        // Merge array and store to property
        $this->_set_tooltip = array_merge($this->_set_tooltip, $params);

        return $this;
    }

    /**
     * The function to unset the field
     *
     * @param   array|string $params
     */
    public function unset_field($params = [])
    {
        // Shorthand possibility, separate with commas
        $params = array_map('trim', explode(',', $params));

        // Merge array and store to property
        $this->_unset_field = array_merge($this->_unset_field, $params);

        return $this;
    }

    /**
     * The function to unset the column
     *
     * @param   array|string $params
     */
    public function unset_column($params = [])
    {
        if (! is_array($params)) {
            // Shorthand possibility, separate with commas
            $params = array_map('trim', explode(',', $params));
        }

        // Merge array and store to property
        $this->_unset_column = array_merge($this->_unset_column, $params);

        return $this;
    }

    /**
     * The function to unset the field on view data
     *
     * @param   array|string $params
     */
    public function unset_view($params = [])
    {
        if (! is_array($params)) {
            // Shorthand possibility, separate with commas
            $params = array_map('trim', explode(',', $params));
        }

        // Merge array and store to property
        $this->_unset_view = array_merge($this->_unset_view, $params);

        return $this;
    }

    /**
     * The function to rearrange the columns
     *
     * @param   array|string $params
     */
    public function column_order($params = [])
    {
        if (! is_array($params)) {
            // Shorthand possibility, separate with commas
            $params = array_map('trim', explode(',', $params));
        }

        // Merge array and store to property
        $this->_column_order = array_merge($this->_column_order, $params);

        return $this;
    }

    /**
     * The function to rearrange the field on view data
     *
     * @param   array|string $params
     */
    public function view_order($params = [])
    {
        if (! is_array($params)) {
            // Shorthand possibility, separate with commas
            $params = array_map('trim', explode(',', $params));
        }

        // Merge array and store to property
        $this->_view_order = array_merge($this->_view_order, $params);

        return $this;
    }

    /**
     * The function to rearrange the field in form
     *
     * @param   array|string $params
     */
    public function field_order($params = [])
    {
        if (! is_array($params)) {
            // Shorthand possibility, separate with commas
            $params = array_map('trim', explode(',', $params));
        }

        // Merge array and store to property
        $this->_field_order = array_merge($this->_field_order, $params);

        return $this;
    }

    /**
     * The function to deny reading when primary key is matched with unset
     *
     * @param   array|string $params
     */
    public function unset_read($params = [], array $value = [])
    {
        // Make sure the parameter is array, otherwise convert it
        if (! is_array($params)) {
            // Convert parameters as array
            $params = [
                $params => $value
            ];
        }

        // Merge array and store to property
        $this->_unset_read = array_merge($this->_unset_read, $params);

        return $this;
    }

    /**
     * The function to deny updating when primary key is matched with unset
     *
     * @param   array|string $params
     */
    public function unset_update($params = [], array $value = [])
    {
        // Make sure the parameter is array, otherwise convert it
        if (! is_array($params)) {
            // Convert parameters as array
            $params = [
                $params => $value
            ];
        }

        // Merge array and store to property
        $this->_unset_update = array_merge($this->_unset_update, $params);

        return $this;
    }

    /**
     * The function to deny deleting when primary key is matched with unset
     *
     * @param   array|string $params
     */
    public function unset_delete($params = [], array $value = [])
    {
        // Make sure the parameter is array, otherwise convert it
        if (! is_array($params)) {
            // Convert parameters as array
            $params = [
                $params => $value
            ];
        }

        // Merge array and store to property
        $this->_unset_delete = array_merge($this->_unset_delete, $params);

        return $this;
    }

    /**
     * The function to set default value of form field so user cannot make
     * any changes from input
     *
     * @param   array|string $params
     * @param   null|mixed $value
     */
    public function set_default($params = [], $value = null)
    {
        // Make sure the parameter is array, otherwise convert it
        if (! is_array($params)) {
            // Convert parameters as array
            $params = [
                $params => $value
            ];
        }

        // Merge array and store to property
        $this->_set_default = array_merge($this->_set_default, $params);

        return $this;
    }

    /**
     * Add the field to the form validation
     *
     * @param   array|string $params
     */
    public function set_validation($params = [], string $value = null)
    {
        // Make sure the parameter is array, otherwise convert it
        if (! is_array($params)) {
            // Convert parameters as array
            $params = [
                $params => $value
            ];
        }

        // Find existing field validation and merge
        foreach ($params as $key => $val) {
            if (isset($this->_set_validation[$key]) && $val) {
                // Merge validation
                $this->_set_validation[$key] = $this->_set_validation[$key] . '|' . $val;
            } else {
                // Set new validation
                $this->_set_validation[$key] = $val;
            }
        }

        return $this;
    }

    /**
     * Set the upload path to follow the custom path
     */
    public function set_upload_path(string $path = null)
    {
        // Validate the given parameter is a valid path name
        if ($path && preg_match('/^[A-Za-z0-9\-\.\_\/]*$/', $path)) {
            $this->_set_upload_path = strtolower($path);
        }

        return $this;
    }

    /**
     * Create custom callback of form validation
     */
    public function form_callback(string $callback)
    {
        $this->_form_callback = $callback;

        return $this;
    }

    /**
     * Set the alias of column, the selected column of database table  will
     * not translated
     *
     * @param   array|string $params
     */
    public function set_alias($params = [], string $value = null)
    {
        // Make sure the parameter is array, otherwise convert it
        if (! is_array($params)) {
            // Convert parameters as array
            $params = [
                $params => $value
            ];
        }

        // Merge array and store to property
        $this->_set_alias = array_merge($this->_set_alias, $params);

        return $this;
    }

    /**
     * Add heading before field on form or view
     *
     * @param   array|string $params
     */
    public function set_heading($params = [], string $value = null)
    {
        // Make sure the parameter is array, otherwise convert it
        if (! is_array($params)) {
            // Convert parameters as array
            $params = [
                $params => $value
            ];
        }

        // Merge array and store to property
        $this->_set_heading = array_merge($this->_set_heading, $params);

        return $this;
    }

    /**
     * The function to push the additional data to the response
     *
     * @param   array|string $params
     */
    public function set_output($params = [], $value = [])
    {
        // Make sure the parameter is array, otherwise convert it
        if (! is_array($params)) {
            // Convert parameters as array
            $params = [
                $params => $value
            ];
        }

        // Merge array and store to property
        $this->_set_output = array_merge($this->_set_output, $params);

        return $this;
    }

    /**
     * Prevent the field to be truncated
     */
    public function unset_truncate(string $field)
    {
        if (! is_array($field)) {
            // Shorthand possibility, separate with commas
            $field = array_map('trim', explode(',', $field));
        }

        // Merge array and store to property
        $this->_unset_truncate = array_merge($this->_unset_truncate, $field);

        return $this;
    }

    /**
     * Set the width of modal popup will be displayed
     */
    public function modal_size(string $size)
    {
        $this->_modal_size = strtolower($size);

        return $this;
    }

    /**
     * Arrange the field to the position
     *
     * @param   array|string $params
     */
    public function field_position($params = [], string $value = null)
    {
        // Make sure the parameter is array, otherwise convert it
        if (! is_array($params)) {
            // Convert parameters as array
            $params = [
                $params => $value
            ];
        }

        // Merge array and store to property
        $this->_field_position = array_merge($this->_field_position, $params);

        return $this;
    }

    /**
     * Add the custom column size
     *
     * @param   array|string $params
     */
    public function column_size($params = [], string $value = null)
    {
        // Make sure the parameter is array, otherwise convert it
        if (! is_array($params)) {
            // Convert parameters as array
            $params = [
                $params => $value
            ];
        }

        // Merge array without lossing index and store to property
        $this->_column_size = array_replace($this->_column_size, $params);

        return $this;
    }

    /**
     * Add the custom field size
     *
     * @param   array|string $params
     */
    public function field_size($params = [], string $value = null)
    {
        // Make sure the parameter is array, otherwise convert it
        if (! is_array($params)) {
            // Convert parameters as array
            $params = [
                $params => $value
            ];
        }

        // Merge array and store to property
        $this->_field_size = array_merge($this->_field_size, $params);

        return $this;
    }

    /**
     * Add the prefix to the field
     *
     * @param   array|string $params
     */
    public function field_prepend($params = [], string $value = null)
    {
        // Make sure the parameter is array, otherwise convert it
        if (! is_array($params)) {
            // Convert parameters as array
            $params = [
                $params => $value
            ];
        }

        // Merge array and store to property
        $this->_field_prepend = array_merge($this->_field_prepend, $params);

        return $this;
    }

    /**
     * Add the suffix to the field
     *
     * @param   array|string $params
     */
    public function field_append($params = [], string $value = null)
    {
        // Make sure the parameter is array, otherwise convert it
        if (! is_array($params)) {
            // Convert parameters as array
            $params = [
                $params => $value
            ];
        }

        // Merge array and store to property
        $this->_field_append = array_merge($this->_field_append, $params);

        return $this;
    }

    /**
     * Merge the multiple field into one
     * Wrap the string (field name) between double curly braces
     */
    public function merge_content(string $magic_string, string $alias = null, string $callback = null)
    {
        // Get the fields from the magic string
        preg_match_all('/\{\{(.*?)\}\}/', $magic_string, $matches);

        $matches = array_map('trim', $matches[1]);
        $field = (isset($matches[0]) ? $matches[0] : null);

        // Check if the current method isn't matches with the restricted one
        if (! in_array($this->_method, ['create', 'update'])) {
            $this->_set_alias[$field] = ($alias && ! is_array($alias) ? $alias : ucwords(str_replace('_', ' ', $field)));
            $this->_merge_label[$field] = ($alias && ! is_array($alias) ? $alias : ucwords(str_replace('_', ' ', $field)));
        }

        // Sets the new key to merge property
        $this->_merge_content[$field] = [
            'column' => $matches,
            'parameter' => ($magic_string && ! is_array($magic_string) ? $magic_string : null),
            'callback' => ($callback ? str_replace('callback_', '', $callback) : null)
        ];

        // Check if matches key is available from given magic string
        if ($matches) {
            // Loops the keys
            foreach ($matches as $key => $val) {
                // Skip empty key
                if (! $key) {
                    continue;
                }

                $this->_unset_column[] = $val;
                $this->_unset_view[] = $val;
            }
        }

        return $this;
    }

    /**
     * Merge the multiple field in one, useable in form
     *
     * @param   array|string $params
     */
    public function merge_field($params)
    {
        if (! is_array($params)) {
            // Shorthand possibility, separate with commas
            $params = array_map('trim', explode(',', $params));
        }

        // Merge array and store to property
        $this->_merge_field[$params[0]] = array_slice($params, 1);

        return $this;
    }

    /**
     * Merge the multiple field in one, useable in form
     *
     * @param   array|string $params
     */
    public function group_field($params = [], string $group = null)
    {
        if (! is_array($params)) {
            // Shorthand possibility, separate with commas
            $params = array_map('trim', explode(',', $params));
            $params = array_fill_keys($params, $group);
        }

        $this->_group_field = array_merge($this->_group_field, $params);

        return $this;
    }

    /**
     * Group row with the given parent from the field reference
     *
     * @param   array|string $params
     */
    public function item_reference($params = [])
    {
        if (! is_array($params)) {
            // Shorthand possibility, separate with commas
            $params = array_map('trim', explode(',', $params));
        }

        // Merge array and store to property
        $this->_item_reference = array_merge($this->_item_reference, $params);

        return $this;
    }

    /**
     * Add the extra attribute into field input
     *
     * @param   array|string $params
     */
    public function set_attribute($params = [], string $value = null)
    {
        // Make sure the parameter is array, otherwise convert it
        if (! is_array($params)) {
            if (isset($this->_set_attribute[$params])) {
                // Alreade set, merge up
                $this->_set_attribute[$params] .= ' ' . $value;

                return $this;
            }

            // Convert parameters as array
            $params = [
                $params => $value
            ];
        }

        // Merge array and store to property
        $this->_set_attribute = array_merge($this->_set_attribute, $params);

        return $this;
    }

    /**
     * Add the placeholder into field input
     *
     * @param   array|string $params
     */
    public function set_placeholder($params = [], string $value = null)
    {
        // Make sure the parameter is array, otherwise convert it
        if (! is_array($params)) {
            // Convert parameters as array
            $params = [
                $params => $value
            ];
        }

        // Merge array and store to property
        $this->_set_placeholder = array_merge($this->_set_placeholder, $params);

        return $this;
    }

    /**
     * Add the extra label into option (select) field
     *
     * @param   array|string $params
     */
    public function set_option_label($params = [], string $value = null)
    {
        // Make sure the parameter is array, otherwise convert it
        if (! is_array($params)) {
            // Convert parameters as array
            $params = [
                $params => $value
            ];
        }

        // Merge array and store to property
        $this->_set_option_label = array_merge($this->_set_option_label, $params);

        return $this;
    }

    /**
     * This function's giving the default value of the field input when add
     * new data
     *
     * @param   array|string $field
     * @param null|mixed $value
     */
    public function default_value($field = [], $value = null)
    {
        // Make sure the parameter is array, otherwise convert it
        if (! is_array($field)) {
            // Convert parameters as array
            $field = [
                $field => $value
            ];
        }

        // Merge array and store to property
        $this->_default_value = array_merge($this->_default_value, $field);

        return $this;
    }

    /**
     * Set the field as relation, to be related to the other field
     *
     * @param   string $group_by
     */
    public function set_relation(string $field, string $primary_key, string $output, $where = [], $join = [], $order_by = [], $group_by = null, int $limit = 0, bool $translate = false)
    {
        $alias = $field;

        // Extract the fields from the given magic string
        preg_match_all('/\{\{(.*?)\}\}/', $output, $matches);

        $select = array_map('trim', $matches[1]);

        if ($translate) {
            // Collect field to translate later
            foreach ($select as $key => $val) {
                $this->_translate_field[] = substr(strstr($val, '.'), 1);
            }
        }

        if (strpos($field, ',') !== false && strpos($primary_key, ',') !== false) {
            // Check if given field contains a commas and convert to array
            $field = array_map('trim', explode(',', $field));

            //Convert selected values to array
            $primary_key = array_map('trim', explode(',', $primary_key));

            // Define variables
            $alias = $field[0];
            $relation_key = [];
            $group_by = [];

            foreach ($primary_key as $key => $val) {
                // Check if value exists in selection
                if (! in_array($val, $select)) {
                    $select[] = $val;
                    $group_by[] = $val;

                    // Add values to unset property
                    $this->_unset_column[] = $val;
                    $this->_unset_view[] = $val;
                }

                // Explode value contains dot
                $explode = explode('.', $val);

                // Check if explode contains two index
                if (sizeof($explode) == 2) {
                    // Add index to relation variable
                    $relation_table = $explode[0];
                    $relation_key[] = $explode[1];

                    // Add variables to unset property
                    $this->_unset_column[] = $explode[1] . '_' . $explode[0];
                    $this->_unset_view[] = $explode[1] . '_' . $explode[0];

                    if ($key > 0) {
                        // Ceck if key index is not zero and add field to unset property
                        $this->_unset_field[] = $explode[1];
                    } else {
                        // Add field to unset property
                        $this->_unset_column[] = $explode[1];
                        $this->_unset_view[] = $explode[1];

                        // Add field to the beginning of selection
                        array_unshift($select, $explode[0] . '.' . $field[0] . ' AS ' . $alias . '_masking');
                    }
                }
            }
        } else {
            // Check if selected value is exists in selection
            if (! in_array($primary_key, $select)) {
                // Add field to selection
                $select[] = (strpos($primary_key, ' ') !== false ? substr($primary_key, strpos($primary_key, ' ') + 1) : $primary_key) . ' AS ' . $alias;
            }

            if (isset($this->_set_attribute[$field])) {
                preg_match_all('/\{\{(.*?)\}\}/', $this->_set_attribute[$field], $matches_attributes);

                $select = array_merge($select, array_map('trim', $matches_attributes[1]));
            }

            $primary_key = explode('.', $primary_key);
            $relation_table = (isset($primary_key[0]) ? $primary_key[0] : null);
            $relation_key = (isset($primary_key[1]) ? $primary_key[1] : null);

            if (! $group_by && $relation_table && $relation_key) {
                //$group_by = (strpos($relation_table, ' ') !== false ? substr($relation_table, strpos($relation_table, ' ') + 1) : $relation_table) . '.' . $relation_key;
            }

            // Add field into unset property
            $this->_unset_column[] = $field;
            $this->_unset_view[] = $field;
        }

        // Check if have join parameter
        if ($join && ! isset($join[0])) {
            $join = [$join];
        }

        // Check if have where parameter
        if ($where) {
            foreach ($where as $key => $val) {
                // Check if field are wrapped with brackets
                if (strpos($key, '.') === false && strpos($key, ' ') === false && strpos($key, '(') === false && strpos($key, ')') === false) {
                    unset($where[$key]);

                    // Assign the field with its table
                    $key = $relation_table . '.' . $key;

                    // Add assigned field to where
                    $where[$key] = $val;
                }
            }
        }

        // Merge selection
        $this->_select = array_unique(array_merge($this->_select, $select));

        // Check if method is in array
        if (! in_array($this->_method, ['create', 'update', 'delete'])) {
            $condition = null;

            if (is_array($field)) {
                // Check if field is array
                foreach ($field as $key => $val) {
                    // Add conditions
                    $condition .= ($condition ? ' AND ' : null) . (strpos($relation_table, ' ') !== false ? substr($relation_table, strpos($relation_table, ' ') + 1) : $relation_table) . '.' . $val . ' = __PRIMARY_TABLE__.' . $val;

                    // Apply validation
                    $this->set_validation($val, 'relation_checker[' . (strpos($relation_table, ' ') !== false ? substr($relation_table, 0, strpos($relation_table, ' ')) : $relation_table) . '.' . $val . ']');
                }
            } else {
                // Apply validation
                $this->set_validation($field, 'relation_checker[' . (strpos($relation_table, ' ') !== false ? substr($relation_table, 0, strpos($relation_table, ' ')) : $relation_table) . '.' . $relation_key . ']');
            }

            // Add relation table to compilation
            $this->_compiled_table[] = $relation_table;

            // Add the relation table into join property
            $this->_join[$relation_table] = [
                'condition' => ($condition ? $condition : (strpos($relation_table, ' ') !== false ? substr($relation_table, strpos($relation_table, ' ') + 1) : $relation_table) . '.' . $relation_key . ' = __PRIMARY_TABLE__.' . $field),
                'type' => 'LEFT',
                'escape' => true
            ];

            // Check if have join parameter
            if ($join) {
                foreach ($join as $key => $val) {
                    // Add table into compilation
                    $this->_compiled_table[] = $val[0];

                    // Add te relation table into join property
                    $this->_join[$val[0]] = [
                        'condition' => $val[1],
                        'type' => (isset($val[2]) ? $val[2] : 'LEFT'),
                        'escape' => true
                    ];
                }
            }
        }

        if (! is_numeric($limit) || $limit <= 0) {
            // Apply limit if not set
            $limit = $this->_limit;
        }

        // Add set relation property
        $this->_set_relation[$alias] = [
            'select' => $select,
            'primary_key' => $field,
            'relation_table' => $relation_table,
            'relation_key' => $relation_key,
            'where' => $where,
            'join' => $join,
            'order_by' => $order_by,
            'group_by' => $group_by,
            'limit' => $limit,
            'offset' => (is_numeric(service('request')->getPost('page')) ? service('request')->getPost('page') - 1 : 0) * $limit,
            'output' => $output,
            'translate' => $translate
        ];

        return $this;
    }

    /**
     * Set the field as autocomplete
     */
    public function set_autocomplete(string $field, string $selected_value, string $output, $where = [], $join = [], $order_by = [], string $group_by = null, int $limit = 0)
    {
        $value = (isset($output['value']) ? $output['value'] : (isset($output[0]) ? $output[0] : null));
        $label = (isset($output['label']) ? $output['label'] : (isset($output[1]) ? $output[1] : null));
        $description = (isset($output['description']) ? $output['description'] : (isset($output[2]) ? $output[2] : null));
        $image = (isset($output['image']) ? $output['image'] : (isset($output[3]) ? $output[3] : null));
        $select = $value . $label . $description . $image;

        // Match magic string
        preg_match_all('/\{\{(.*?)\}\}/', $select, $matches_select);

        $select = (isset($matches_select[1]) ? array_map('trim', $matches_select[1]) : []);
        $select[] = $selected_value . ' AS ' . $field;
        $selected_value = explode('.', $selected_value);
        $relation_table = (isset($selected_value[0]) ? $selected_value[0] : null);
        $relation_key = (isset($selected_value[1]) ? $selected_value[1] : null);

        if ($join && ! isset($join[0])) {
            $join = [$join];
        }

        if (! $group_by) {
            $group_by = $relation_table . '.' . $relation_key;
        }

        $this->_select = array_unique((sizeof($this->_select) > 0 ? array_merge($this->_select, $select) : $select));
        $this->_unset_column[] = $field;
        $this->_unset_view[] = $field;

        if (! in_array($this->_method, ['create', 'update', 'delete']) || ('autocomplete' == service('request')->getPost('method') && service('request')->getPost('origin'))) {
            $this->_join[$relation_table] = [
                'condition' => $relation_table . '.' . $relation_key . ' = __PRIMARY_TABLE__. ' . $field,
                'type' => '',
                'escape' => true
            ];

            if ($join) {
                foreach ($join as $key => $val) {
                    $this->_join[$val[0]] = [
                        'condition' => $val[1],
                        'type' => (isset($val[2]) ? $val[2] : ''),
                        'escape' => true
                    ];
                }
            }
        }

        $output = [
            'value' => $value,
            'label' => $label,
            'description' => $description,
            'image' => $image
        ];

        $this->_set_autocomplete[$field] = [
            'select' => $select,
            'output' => $output,
            'primary_key' => $field,
            'relation_table' => $relation_table,
            'relation_key' => $relation_key,
            'where' => $where,
            'join' => $join,
            'order_by' => $order_by,
            'group_by' => $group_by,
            'limit' => $limit
        ];

        return $this;
    }

    /**
     * Serialize the field that rendered from the database table and extra
     * attributes on it.
     *
     * @return  array
     */
    public function serialize(array $data = [], bool $partial = false)
    {
        if (! $data && $this->model->table_exists($this->_table)) {
            // Flip columns
            $data = [array_fill_keys($this->model->list_fields($this->_table), '')];
        }

        // Define field data compilation
        $field_data = $this->model->field_data($this->_table);

        // Find primary key
        foreach ($field_data as $key => $val) {
            // Unset indexed field data
            unset($field_data[$key]);

            // Add properties to field data compilation
            $field_data[$val->name] = $val;

            // Check if the field has a primary key
            if (isset($val->primary_key) && $val->primary_key && ! in_array($val->name, $this->_set_primary)) {
                // Push primary key
                $this->_set_primary[] = $val->name;
            }
        }

        // Primary key still not found, find from index data
        if (! $this->_set_primary) {
            // Retrieve index data
            $this->_index_data = $this->model->index_data($this->_table);

            // Find the primary key
            foreach ($this->_index_data as $key => $val) {
                // Check if the field has a primary key
                if (in_array($val->type, ['PRIMARY', 'UNIQUE'])) {
                    // Push primary key
                    $this->_set_primary = array_merge($this->_set_primary, $val->fields);
                }
            }

            // Make the array unique
            $this->_set_primary = array_unique($this->_set_primary);
        }

        $results = [];

        foreach ($data as $row => $array) {
            // Rows
            foreach ($array as $field => $value) {
                // Attempt to get the type
                $type = strtolower((isset($field_data[$field]->type) ? $field_data[$field]->type : gettype($value)));

                // Reformat type
                if (in_array($type, ['tinyint', 'smallint', 'int', 'mediumint', 'bigint', 'year'])) {
                    // Field type number
                    $type = 'number';
                } elseif (in_array($type, ['decimal', 'float', 'double', 'real'])) {
                    // Field type decimal
                    $type = 'decimal';
                } elseif (in_array($type, ['tinytext', 'text'])) {
                    // Field type textarea
                    $type = 'textarea';
                } elseif (in_array($type, ['mediumtext', 'longtext'])) {
                    // Field type wysiwyg
                    $type = 'wysiwyg';
                } elseif (in_array($type, ['date'])) {
                    // Field type date (Y-m-d)
                    $type = 'date';
                } elseif (in_array($type, ['datetime', 'timestamp'])) {
                    // Field type datetime (Y-m-d H:i:s)
                    $type = 'datetime';
                } elseif (in_array($type, ['time'])) {
                    // Field type time (H:i:s)
                    $type = 'time';
                } else {
                    // Fallback field type
                    $type = 'text';
                }

                if (! isset($this->_set_field[$field])) {
                    if (isset($this->_set_relation[$field])) {
                        $type = 'select';
                    }

                    // Add new field type
                    $this->_set_field[$field][$type] = [
                        'parameter' => null,
                        'alpha' => null,
                        'beta' => null,
                        'charlie' => null,
                        'delta' => null
                    ];
                }

                // Attempt to get maximum length of column
                $maxlength = (isset($field_data[$field]->max_length) ? $field_data[$field]->max_length : null);

                // Attempt to get the field validation
                $validation = (isset($this->_set_validation[$field]) ? $this->_set_validation[$field] : null);

                // Attempt to get field translation
                $content = (in_array($field, $this->_translate_field) ? phrase($value) : $value);

                $hidden = false;

                if ('create' == $this->_method) {
                    $content = (isset($this->_set_default[$field]) ? $this->_set_default[$field] : (isset($field_data[$field]->default) ? $field_data[$field]->default : null));
                    $value = null;
                }

                if (in_array($this->_method, ['create', 'update']) && in_array($field, $this->_unset_field)) {
                    // Skip field validation
                    $hidden = true;
                } elseif (('read' == $this->_method || (in_array($this->_method, ['print', 'pdf']))) && in_array($field, $this->_unset_view)) {
                    // Skip showing field
                    $hidden = true;
                } elseif (in_array($this->_method, ['index', 'export', 'print', 'pdf']) && in_array($field, $this->_unset_column)) {
                    // Skip showing field
                    $hidden = true;
                }

                if ($value && isset($this->_set_relation[$field])) {
                    // Get relation content
                    $content = $this->_get_relation($this->_set_relation[$field], $value);
                }

                if ($content && array_intersect(['number', 'numeric', 'price', 'percent'], [$type]) && is_numeric($content)) {
                    // Get decimal fractional
                    $decimal = (floor($content) != $content ? strlen(substr(strrchr(rtrim($content, 0), '.'), 1)) : 0);

                    if (array_intersect(['percent'], [$type])) {
                        // Percent type
                        $content = number_format($content, $decimal) . '%';
                    } else {
                        // Numeric type
                        $content = number_format($content, $decimal);
                    }
                }

                if ($content && array_intersect(['sprintf'], [$type])) {
                    // Add zero leading
                    $content = sprintf(($parameter && ! is_array($parameter) ? $parameter : '%02d'), $content);
                }

                $results[$field] = [
                    'type' => $this->_set_field[$field],
                    'primary' => (in_array($field, $this->_set_primary) ? 1 : 0),
                    'value' => $value,
                    'content' => $content,
                    'maxlength' => $maxlength,
                    'hidden' => $hidden,
                    'validation' => ($maxlength ? 'max_length[' . $maxlength . ']' : null) . ($maxlength && $validation ? '|' : null) . ($validation ? $validation : null)
                ];
            }

            $output[$row] = $results;
        }

        return $output;
    }

    /**
     * Rendering the result into view
     *
     * @return  object|string
     */
    public function render(string $table = null, string $view = null)
    {
        // Debugger
        if (in_array($this->_debugging, ['params', 'parameter'])) {
            // Debug requested
            if (ENVIRONMENT === 'production') {
                // Print debugger
                exit('<pre>' . print_r($this->_prepare, true) . '</pre>');
            }

            // Print debugger
            dd($this->_prepare);
        }

        // Check if method is cloning
        if ('clone' == $this->_method) {
            // Switch method to update
            $this->_method = 'update';
            $this->_cloning = true;
        }

        if ($this->api_client) {
            // Validate API request
            if ($this->_set_permission) {
                if (! service('request')->getHeaderLine('X-ACCESS-TOKEN')) {
                    // Access token is not set
                    return throw_exception(403, phrase('This service is require an access token.'));
                } elseif (! $this->_api_token) {
                    // Access token is not valid
                    return throw_exception(403, phrase('The access token is invalid or already expired'));
                }
            } elseif (in_array(service('request')->getServer('REQUEST_METHOD'), ['POST', 'DELETE']) &&
            ! in_array($this->_method, ['create', 'update', 'delete'])) {
                // Check if request is made from promise
                return throw_exception(403, phrase('The method you requested is not acceptable') . ' (' . service('request')->getServer('REQUEST_METHOD'). ')', (! $this->api_client ? go_to() : null));
            }
        } elseif ($table && ! $this->_set_permission) {
            // Unset database modification because no permission is set
            $this->unset_method('create, update, delete');

            if (in_array($this->_method, ['create', 'update', 'delete'])) {
                // Throw exception about the method
                return throw_exception(403, phrase('You do not have sufficient privileges to access the requested page') . ' (' . strtoupper($this->_method). ')', (! $this->api_client ? go_to() : null));
            }
        }

        if (! $this->_table) {
            // Set table when not present
            $this->_table = $table;

            // Push to compiled table
            $this->_compiled_table[] = $table;
        }

        /**
         * Token checker and apply only when uri has query string
         */
        if (service('request')->getGet()) {
            $token = service('request')->getGet('aksara');
            $query_string = service('request')->getGet();

            // Validate token
            if (
                $this->_set_permission &&
                $query_string &&
                generate_token($query_string, uri_string()) != $token
            ) {
                // Token is missmatch, throw an exception
                return throw_exception(403, phrase('The submitted token has been expired or the request is made from restricted source'), base_url());
            }
        }

        // Validate the restricted action
        if (in_array($this->_method, $this->_unset_method)) {
            return throw_exception(403, phrase('You are not allowed to perform the requested action'), go_to());
        }

        // Check before action
        if ('create' == $this->_method && method_exists($this, 'before_insert')) {
            // Before insert
            $this->before_insert();
        } elseif ('update' == $this->_method && method_exists($this, 'before_update')) {
            // Before update
            $this->before_update();
        } elseif ('delete' == $this->_method && method_exists($this, 'before_delete')) {
            // Before delete
            $this->before_delete();
        }

        // Load template class
        $this->template = new Template($this->_set_theme, $this->_method);

        // Load template parser
        $renderer = new Renderer();

        // Send necessary properties
        $renderer->setProperty(['_set_theme' => $this->template->theme]);

        // Set core component path
        $renderer->setPath('core');

        // Create core component if not exists
        $renderer->render([]);

        // Check if given table is exists in database
        if ($this->_table) {
            // Check if table is exists
            if (! $this->model->table_exists($this->_table)) {
                return throw_exception(404, phrase('The defined primary table does not exist'), current_page('../'));
            }

            // Retrieve primary key
            $field_data = $this->model->field_data($this->_table);

            // Find primary key
            foreach ($field_data as $key => $val) {
                // Check if the field has a primary key
                if (isset($val->primary_key) && $val->primary_key && ! in_array($val->name, $this->_set_primary)) {
                    // Push primary key
                    $this->_set_primary[] = $val->name;
                }
            }

            // Primary key still not found, find from index data
            if (! $this->_set_primary) {
                // Retrieve index data
                $index_data = $this->model->index_data($this->_table);

                // Find the primary key
                foreach ($index_data as $key => $val) {
                    // Check if the field has a primary key
                    if (in_array($val->type, ['PRIMARY', 'UNIQUE'])) {
                        // Push primary key
                        $this->_set_primary = array_merge($this->_set_primary, $val->fields);
                    }
                }

                // Make the array unique
                $this->_set_primary = array_unique($this->_set_primary);
            }

            // Apply primary from where if it's were sets
            if (! $this->_set_primary && $this->_where) {
                // Get array keys
                $this->_set_primary = array_keys($this->_where);
            }

            // Check again if the primary key is still unavailable
            if (! $this->_set_primary) {
                if ('backend' == $this->template->get_theme_property('type')) {
                    // Add notification into table heading
                    $this->set_description('<div><b>' . phrase('No primary key is found.') . '</b> ' . phrase('Please define it manually') . ' (' . strtolower(phrase('refers to')) . ' <code>set_primary()</code>). ' . phrase('Without primary key, you will only allowed to insert the data.') . '</div>');
                }

                // Unset method
                $this->unset_method('update, delete');
            }

            // Get query string
            $query_string = service('request')->getGet();

            foreach ($query_string as $key => $val) {
                if (in_array($this->_method, ['read', 'update', 'delete']) && in_array($key, $this->_set_primary)) {
                    // Remove query parameter from URL
                    $query_string[$key] = null;
                }
            }

            // Assign previous URL
            $this->_redirect_back = go_to(null, $query_string);

            // Check the additional primary key that been sets up
            if (is_array($this->_set_primary) && sizeof($this->_set_primary) > 0) {
                foreach ($this->_set_primary as $key => $val) {
                    if (service('request')->getGet($val) && $this->model->field_exists($val, $this->_table)) {
                        if (
                            ('read' == $this->_method && isset($this->_unset_read[$val]) && in_array(service('request')->getGet($val), $this->_unset_read[$val])) ||
                            ('update' == $this->_method && isset($this->_unset_update[$val]) && in_array(service('request')->getGet($val), $this->_unset_update[$val])) ||
                            ('delete' == $this->_method && isset($this->_unset_delete[$val]) && in_array(service('request')->getGet($val), $this->_unset_delete[$val])) ||
                            ('export' == $this->_method && isset($this->_unset_read[$val]) && in_array(service('request')->getGet($val), $this->_unset_read[$val])) ||
                            ('print' == $this->_method && isset($this->_unset_read[$val]) && in_array(service('request')->getGet($val), $this->_unset_read[$val])) ||
                            ('pdf' == $this->_method && isset($this->_unset_read[$val]) && in_array(service('request')->getGet($val), $this->_unset_read[$val]))
                        ) {
                            if (in_array($this->_method, ['read', 'export', 'print', 'pdf'])) {
                                // Method isn't allowed to access, throw exception
                                return throw_exception(403, phrase('You are not allowed to view the requested data'), $this->_redirect_back);
                            } else {
                                if (isset($this->_set_messages['update'])) {
                                    // Add custom message if any
                                    return throw_exception($this->_set_messages['update']['code'], $this->_set_messages['update']['messages'], $this->_redirect_back);
                                } else {
                                    // Otherwise, use default message
                                    return throw_exception(403, phrase('You are not allowed to modify the requested data'), $this->_redirect_back);
                                }
                            }
                        }

                        // Push where into prepared statement
                        $this->_prepare('where', [$this->_table . '.' . $val, htmlspecialchars(service('request')->getGet($val))]);
                    } elseif (
                        in_array($val, $this->_set_primary) &&
                        $this->model->field_exists($val, $this->_table) &&
                        isset($this->_set_default[$val]) &&
                        $this->_set_default[$val]
                    ) {
                        if (
                            ('read' == $this->_method && isset($this->_unset_read[$val]) && in_array(service('request')->getGet($val), $this->_unset_read[$val])) ||
                            ('update' == $this->_method && isset($this->_unset_update[$val]) && in_array(service('request')->getGet($val), $this->_unset_update[$val])) ||
                            ('delete' == $this->_method && isset($this->_unset_delete[$val]) && in_array(service('request')->getGet($val), $this->_unset_delete[$val])) ||
                            ('export' == $this->_method && isset($this->_unset_read[$val]) && in_array(service('request')->getGet($val), $this->_unset_read[$val])) ||
                            ('print' == $this->_method && isset($this->_unset_read[$val]) && in_array(service('request')->getGet($val), $this->_unset_read[$val])) ||
                            ('pdf' == $this->_method && isset($this->_unset_read[$val]) && in_array(service('request')->getGet($val), $this->_unset_read[$val]))
                        ) {
                            if (in_array($this->_method, ['read', 'export', 'print', 'pdf'])) {
                                // Requested method isn't allowed, throw exception
                                return throw_exception(403, phrase('You are not allowed to view the requested data'), $this->_redirect_back);
                            } else {
                                if (isset($this->_set_messages['update'])) {
                                    // Add custom message if any
                                    return throw_exception($this->_set_messages['update']['code'], $this->_set_messages['update']['messages'], $this->_redirect_back);
                                } else {
                                    // Otherwise, use default message
                                    return throw_exception(403, phrase('You are not allowed to modify the requested data'), $this->_redirect_back);
                                }
                            }
                        }

                        // Add where into prepared statement
                        $this->_prepare('where', [$this->_table . '.' . $val, $this->_set_default[$val]]);
                    }
                }
            }

            /**
             * -------------------------------------------------------------
             * Check whether request is post or delete
             * -------------------------------------------------------------
             */
            if (service('request')->getPost('_token')) {
                // Request is sent from browser
                $token_sent = service('request')->getPost('_token');

                // Validate the token
                if ($this->valid_token($token_sent)) {
                    // Token approved, check if validation use the custom callback
                    if ($this->_form_callback && method_exists($this, $this->_form_callback)) {
                        // Use callback as form validation
                        $_callback = $this->_form_callback;

                        return $this->$_callback();
                    } else {
                        // Serialize table data
                        $field_data = [array_fill_keys(array_keys(array_flip($this->model->list_fields($this->_table))), '')];

                        // Or use the master validation instead
                        return $this->validate_form($field_data);
                    }
                } else {
                    // Token isn't valid, throw exception
                    return throw_exception(403, phrase('The submitted token has been expired or the request is made from restricted source'), $this->_redirect_back);
                }
            } elseif ($this->api_client && 'POST' == service('request')->getServer('REQUEST_METHOD') && (in_array($this->_method, ['create', 'update']) || ($this->_form_callback && method_exists($this, $this->_form_callback)))) {
                // Request is sent from REST
                if ($this->_form_callback && method_exists($this, $this->_form_callback)) {
                    // Use callback as form validation
                    $_callback = $this->_form_callback;

                    return $this->$_callback();
                } else {
                    // Serialize table data
                    $field_data = [array_fill_keys(array_keys(array_flip($this->model->list_fields($this->_table))), '')];

                    // Or use the master validation instead
                    return $this->validate_form($field_data);
                }
            } elseif ($this->_set_primary && 'delete' == $this->_method) {
                // Delete data
                if (1 == service('request')->getPost('batch')) {
                    // Batch delete
                    return $this->delete_batch($this->_table);
                } else {
                    // Single delete
                    return $this->delete_data($this->_table, $this->_where, $this->_limit);
                }
            } else {
                // Get offset if not set
                if (! in_array($this->_method, ['create', 'read', 'update', 'delete']) && ! $this->_offset && gettype($this->_offset) !== 'integer' && is_numeric(service('request')->getGet('per_page')) && service('request')->getGet('per_page') > 1) {
                    $this->_offset = (service('request')->getGet('per_page') - 1) * ($this->_limit ?? $this->_limit_backup);
                }

                if ($this->_offset) {
                    // Push offset to the prepared query builder
                    $this->_prepare('offset', [$this->_offset]);
                }

                if (($this->_searchable && ! $this->_like && service('request')->getGet('q')) || ('autocomplete' == service('request')->getPost('method') && $this->_searchable && service('request')->getPost('q'))) {
                    if ('autocomplete' != service('request')->getPost('method')) {
                        $this->group_start();
                    }

                    $column = (service('request')->getGet('column') ? strip_tags(service('request')->getGet('column')) : service('request')->getGet('column'));

                    if ($column && 'all' != $column) {
                        foreach ($this->_compiled_table as $key => $val) {
                            // Make sure column is exist in compiled table
                            if ($this->model->field_exists($column, $val)) {
                                // Push like to the prepared query builder
                                $this->_prepare('like', [$val . '.' . $column, htmlspecialchars(('autocomplete' == service('request')->getPost('method') && service('request')->getPost('q') ? service('request')->getPost('q') : service('request')->getGet('q')))]);
                            }
                        }
                    } else {
                        $columns = $this->model->list_fields($this->_table);

                        if ($columns) {
                            $this->or_group_start();

                            foreach ($columns as $key => $val) {
                                // Add the table prefix to prevent ambiguous
                                $val = $this->_table . '.' . $val;

                                // Push like an or like to the prepared query builder
                                $this->_prepare(($key ? 'or_like' : 'like'), [$val, htmlspecialchars(('autocomplete' == service('request')->getPost('method') && service('request')->getPost('q') ? service('request')->getPost('q') : service('request')->getGet('q')))]);
                            }

                            $this->group_end();
                        }

                        if ($this->_select) {
                            $compiled_like = [];

                            $this->or_group_start();

                            foreach ($this->_select as $key => $val) {
                                if ($val && stripos($val, ' AS ') !== false) {
                                    $val = substr($val, 0, stripos($val, ' AS '));
                                }

                                $field_origin = (strpos($val, '.') !== false ? substr($val, strpos($val, '.') + 1) : $val);

                                if (in_array($field_origin, $compiled_like)) {
                                    continue;
                                }

                                // Push like an or like to the prepared query builder
                                $this->_prepare(($key ? 'or_like' : 'like'), [$val, htmlspecialchars(('autocomplete' == service('request')->getPost('method') && service('request')->getPost('q') ? service('request')->getPost('q') : service('request')->getGet('q')))]);

                                if (isset($this->_set_field[service('request')->getPost('origin')]['parameter'])) {
                                    if (is_array($this->_set_field[service('request')->getPost('origin')]['parameter'])) {
                                        $table = $this->_set_field[service('request')->getPost('origin')]['parameter'][0];
                                    } else {
                                        $table = $this->_set_field[service('request')->getPost('origin')]['parameter'];
                                    }
                                }

                                if (isset($this->_set_field[service('request')->getPost('origin')]['parameter']) && $this->model->field_exists(($val && stripos($val, '.') !== false ? substr($val, strripos($val, '.') + 1) : $val), $table)) {
                                    // Push order by best match to the prepared query builder
                                    $this->_prepare('order_by', ['(CASE WHEN ' . $val . ' LIKE "' . service('request')->getPost('q') . '%" THEN 1 WHEN ' . $val . ' LIKE "%' . service('request')->getPost('q') . '" THEN 3 ELSE 2 END)']);
                                }

                                $compiled_like[] = $field_origin;
                            }

                            $this->group_end();
                        }
                    }

                    if ('autocomplete' != service('request')->getPost('method')) {
                        $this->group_end();
                    }
                }
            }

            if (service('request')->isAJAX() && 'autocomplete' == service('request')->getPost('method')) {
                /**
                 * PLEASE NOTE
                 * =============================================================
                 * This simple autocomplete input only render only one table.
                 * To use complex autocomplete within complex database query,
                 * use the set_autocomplete() function instead. It will allows
                 * you to custom format the result, also join into multiple
                 * table including where, order and grouping.
                 * =============================================================
                 */
                $suggestions = [];

                if (isset($this->_set_field[service('request')->getPost('origin')]) && in_array('autocomplete', $this->_set_field[service('request')->getPost('origin')]['field_type'])) {
                    // Set the relation table, field and keyword
                    $field = $this->_set_field[service('request')->getPost('origin')];
                    $table = (is_array($field['parameter']) ? $field['parameter'][0] : $field['parameter']);
                    $select = (! is_array($field['extra_params']) ? array_map('trim', explode(',', $field['extra_params'])) : $field['extra_params']);
                    $select = [
                        'value' => (isset($select['value']) ? $select['value'] : (isset($select[0]) ? $select[0] : null)),
                        'label' => (isset($select['label']) ? $select['label'] : (isset($select[1]) ? $select[1] : null)),
                        'description' => (isset($select['description']) ? $select['description'] : (isset($select[2]) ? $select[2] : null)),
                        'image' => (isset($select['image']) ? $select['image'] : (isset($select[3]) ? $select[3] : null))
                    ];
                    $where = $field['another_params'];
                    $join = $field['skip'];
                    $order = $field['order'];
                    $keyword = service('request')->getPost('q');
                    $columns = [];
                    $num = 0;

                    foreach ($select as $key => $val) {
                        if (! $val) {
                            continue;
                        }

                        $this->model->group_start();

                        if ($num > 0) {
                            $this->model->or_like($val, $keyword, 'both', true, true);
                        } else {
                            $this->model->like($val, $keyword, 'both', true, true);
                        }

                        $this->model->group_end();

                        $columns[] = $val . ' AS ' . $key;

                        $num++;
                    }

                    $this->model->select($columns);

                    if ($where && is_array($where) && sizeof($where) > 0) {
                        $this->model->where($where);
                    }

                    if ($join && is_array($join) && sizeof($join) > 0) {
                        foreach ($join as $key => $val) {
                            if (! isset($val[0]) || ! isset($val[1])) {
                                continue;
                            }

                            $this->model->join($val[0], $val[1]);
                        }
                    }

                    // Order by best match
                    if ($order_by) {
                        foreach ($order_by as $key => $val) {
                            $this->model->order_by($val, null, false);
                        }
                    }

                    if ($order) {
                        $this->model->order_by($order);
                    }

                    $this->model->group_by($select['value']);

                    // Run query
                    $query = $this->model->get($table, 50)->result_array();

                    if ($query) {
                        foreach ($query as $val) {
                            // List as value, label, description and image
                            list(
                                $value,
                                $label,
                                $description,
                                $image
                            ) = array_pad(array_values($val), 4, null);

                            $suggestions[] = [
                                'value' => $value,
                                'label' => ($label ? $label : $value),
                                'description' => $description,
                                'image' => ($image ? get_image($this->_set_upload_path, $image, 'icon') : null),
                                'target' => null
                            ];
                        }
                    }
                } else {
                    // Autocomplete search data from listed of table
                    if (! $this->_select) {
                        // Check the select list, if none, use the main table field instead
                        $this->_select = preg_filter('/^/', $this->_table . '.', $this->model->list_fields($this->_table));
                    }

                    // Loop the select field to prevent query using multiple LIKE condition and use OR LIKE instead
                    $compiled_like = [];

                    foreach ($this->_select as $key => $val) {
                        if ($val && stripos($val, ' AS ') !== false) {
                            $val = substr($val, 0, stripos($val, ' AS '));
                        }

                        $field_origin = (strpos($val, '.') !== false ? substr($val, strpos($val, '.') + 1) : $val);

                        if (in_array($field_origin, $compiled_like)) {
                            continue;
                        }

                        // Push like an or like to the prepared query builder
                        $this->_prepare(($key ? 'or_like' : 'like'), [$val, htmlspecialchars(('autocomplete' == service('request')->getPost('method') && service('request')->getPost('q') ? service('request')->getPost('q') : service('request')->getGet('q')))]);

                        if (isset($this->_set_field[service('request')->getPost('origin')]['parameter'])) {
                            if (is_array($this->_set_field[service('request')->getPost('origin')]['parameter'])) {
                                $table = $this->_set_field[service('request')->getPost('origin')]['parameter'][0];
                            } else {
                                $table = $this->_set_field[service('request')->getPost('origin')]['parameter'];
                            }
                        }

                        if (isset($this->_set_field[service('request')->getPost('origin')]['parameter']) && $this->model->field_exists(($val && stripos($val, '.') !== false ? substr($val, strripos($val, '.') + 1) : $val), $table)) {
                            // Push order by best match to the prepared query builder
                            $this->_prepare('order_by', ['(CASE WHEN ' . $val . ' LIKE "' . service('request')->getPost('q') . '%" THEN 1 WHEN ' . $val . ' LIKE "%' . service('request')->getPost('q') . '" THEN 3 ELSE 2 END)']);
                        }

                        $compiled_like[] = $field_origin;
                    }

                    // Run the query using prepared property
                    $query = $this->_fetch($this->_table);

                    // Populate added item
                    $added_item = [];

                    // Serialize results
                    $serialized = $this->serialize($query['results']);

                    foreach ($serialized as $key => $val) {
                        // Does column order is mandatory? let's just watch
                        if (is_array($this->_column_order) && sizeof($this->_column_order) > 0) {
                            // Set the default column order
                            $column_order = [];

                            foreach ($this->_column_order as $order_key => $order_val) {
                                // If array key exists
                                if (array_key_exists($order_val, $val)) {
                                    // Then push to column order grocery
                                    $column_order[] = $order_val;
                                }
                            }

                            // Set the value
                            $val = array_replace(array_flip($column_order), $val);
                        }

                        $autocomplete_item = [];
                        // Loop the result
                        foreach ($val as $field => $value) {
                            // Check if the result value is not contain the search keyword or the field is unset from column (list table)
                            if (($value['original'] && strpos(strtolower($value['original']), strtolower(service('request')->getPost('q'))) === false) || in_array($field, $this->_unset_column)) {
                                continue;
                            }

                            // Everything's looks good, throw into autocomplete result
                            if (! $autocomplete_item && $value['original'] && ! in_array($value['content'], $added_item)) {
                                $added_item[] = $value['content'];

                                $autocomplete_item = [
                                    'value' => truncate($value['content'], 32, false, ''),
                                    'label' => truncate($value['content'], 32),
                                    'target' => current_page(null, ['per_page' => null, 'q' => truncate($value['content'], 32, '')])
                                ];
                            }
                        }

                        if ($autocomplete_item) {
                            $suggestions[] = $autocomplete_item;
                        }
                    }
                }

                // Return the callback as autocomplete results
                return make_json([
                    'suggestions' => ($suggestions ? $suggestions : null)
                ]);
            } elseif (service('request')->isAJAX() && service('request')->getPost('method') == 'ajax_select' && isset($this->_set_relation[service('request')->getPost('source')])) {
                // Check if data is requested through server side select (jQuery plugin)
                return $this->_get_relation($this->_set_relation[service('request')->getPost('source')], null, true);
            }

            if (service('request')->getGet('sort') && 'desc' == strtolower(service('request')->getGet('sort'))) {
                // Order ASC from query string
                set_userdata('sortOrder', 'ASC');
            } else {
                // Order DESC from query string
                set_userdata('sortOrder', 'DESC');
            }

            if ($this->model->field_exists(service('request')->getGet('order'), $this->_table)) {
                // Match order by the primary table
                // Push order to the prepared query builder
                $this->_prepare[] = [
                    'function' => 'order_by',
                    'arguments' => [$this->_table . '.' . service('request')->getGet('order'), get_userdata('sortOrder')]
                ];
            } elseif ($this->_compiled_table) {
                // Otherwhise, find it from the relation table
                foreach ($this->_compiled_table as $key => $table) {
                    // Validate the column to check if column is exist in table
                    if ($this->model->field_exists(service('request')->getGet('order'), $table)) {
                        // Push order to the prepared query builder
                        $this->_prepare[] = [
                            'function' => 'order_by',
                            'arguments' => [$table . '.' . service('request')->getGet('order'), get_userdata('sortOrder')]
                        ];
                    }
                }
            }

            if (in_array($this->_method, ['create'])) {
                // List the field properties
                $results = [array_fill_keys(array_keys(array_flip($this->model->list_fields($this->_table))), '')];
                $total = 0;
            } else {
                // Run query using prepared property
                $query = $this->_fetch($this->_table);
                $results = $query['results'];
                $total = $query['total'];
            }

            // Default icon property
            $icon = (isset($this->_set_icon[$this->_method]) ? $this->_set_icon[$this->_method] : (isset($this->_set_icon['index']) ? $this->_set_icon['index'] : null));

            // Default title property
            $title = (isset($this->_set_title[$this->_method]) ? $this->_set_title[$this->_method] : (isset($this->_set_title['index']) ? $this->_set_title['index'] : null));

            // Default description property
            $description = (isset($this->_set_description[$this->_method]) ? $this->_set_description[$this->_method] : ('index' === $this->_method && isset($this->_set_description['index']) ? $this->_set_description['index'] : null));

            if (isset($results[0])) {
                // Extract magic string
                preg_match_all('/\{\{(.*?)\}\}/', $title ?? '', $title_replace);
                preg_match_all('/\{\{(.*?)\}\}/', $description ?? '', $description_replace);

                foreach ($title_replace[1] as $index => $replace) {
                    $replacement = trim($replace);

                    if (isset($results[0]->$replacement)) {
                        // Attempt to convert the magic string and replace with the result
                        $title = preg_replace("/\{\{(\s+)?($replace)(\s+)?\}\}/", $results[0]->$replacement, $title);
                    }
                }

                foreach ($description_replace[1] as $index => $replace) {
                    $replacement = trim($replace);

                    if (isset($results[0]->$replacement)) {
                        // Attempt to convert the magic string and replace with the result
                        $description = preg_replace("/\{\{(\s+)?($replace)(\s+)?\}\}/", $results[0]->$replacement, $description);
                    }
                }
            }

            if ('create' == $this->_method) {
                /**
                 * -------------------------------------------------------------
                 * Method is create
                 * -------------------------------------------------------------
                 */
                // Set view template property
                $this->_view = (is_array($this->_set_template) && isset($this->_set_template['form']) ? $this->_set_template['form'] : ($view && 'index' != $view ? $view : 'form'));

                // Get formatted results
                $results = $this->render_form($results);

                // Set icon property
                $this->_set_icon = ($this->_method && $icon ? $icon : 'mdi mdi-plus');

                // Set title property
                $this->_set_title = ($this->_method && $title ? $title : phrase('Add New Data'));

                // Set description property
                $this->_set_description = ($this->_method && $description ? $description : phrase('Please fill all required field below to add new data'));
            } elseif ('read' == $this->_method) {
                /**
                 * -------------------------------------------------------------
                 * Method is read
                 * -------------------------------------------------------------
                 */
                // Set view template property
                $this->_view = (is_array($this->_set_template) && isset($this->_set_template[$this->_method]) ? $this->_set_template['read'] : ($view && 'index' != $view ? $view : 'read'));

                // Get formatted results
                $results = $this->render_read($results);

                // Set icon property
                $this->_set_icon = ($this->_method && $icon ? $icon : 'mdi mdi-magnify');

                // Set title property
                $this->_set_title = ($this->_method && $title ? $title : phrase('Showing Data'));

                // Set description property
                $this->_set_description = ($this->_method && $description ? $description : phrase('Showing the result of requested data'));
            } elseif ('update' == $this->_method) {
                /**
                 * -------------------------------------------------------------
                 * Method is update
                 * -------------------------------------------------------------
                 */
                // Set view template property
                $this->_view = (is_array($this->_set_template) && isset($this->_set_template['form']) ? $this->_set_template['form'] : ($view && 'index' != $view ? $view : 'form'));

                // Get formatted results
                $results = $this->render_form($results);

                // Set icon property
                $this->_set_icon = ($this->_method && $icon ? $icon : 'mdi mdi-square-edit-outline');

                // Set title property
                $this->_set_title = ($this->_method && $title ? $title : phrase('Update Data'));

                // Set description property
                $this->_set_description = ($this->_method && $description ? $description : phrase('Make sure to check the changes before submitting'));
            } elseif (in_array($this->_method, ['export', 'print', 'pdf'])) {
                /**
                 * -------------------------------------------------------------
                 * Method is requesting document file or print
                 * -------------------------------------------------------------
                 */
                $query_string = service('request')->getGet();
                $single_print = false;

                if ($this->_set_primary) {
                    foreach ($this->_set_primary as $key => $val) {
                        // Find single item print
                        if (isset($query_string[$val])) {
                            $single_print = true;

                            break;
                        }
                    }
                }

                // Set view template property
                $this->_view = (isset($this->_set_template[$this->_method]) ? $this->_set_template[$this->_method] : $this->_method);

                // Get formatted results
                $results = ($single_print ? $this->render_read($results) : $this->render_table($results));
            } else {
                /**
                 * -------------------------------------------------------------
                 * Non listed method
                 * -------------------------------------------------------------
                 */
                // Check if master view template is used
                $view_exist = (! in_array($this->template->get_view($this->_view), ['../../aksara/Views/core/index', '../../themes/' . $this->template->theme . '/components/core/index', '../../aksara/Views/core/index_grid', '../../themes/' . $this->template->theme . '/components/core/index_grid', '../../aksara/Views/core/index_mobile', '../../themes/' . $this->template->theme . '/components/core/index_mobile', '../../aksara/Views/core/error', '../../themes/' . $this->template->theme . '/components/core/error']));

                // Set description property
                $this->_view = (is_array($this->_set_template) && isset($this->_set_template['index']) ? $this->_set_template['index'] : ($view && 'index' != $view ? $view : 'index'));

                // Get formatted results
                $results = (! $view_exist ? $this->render_table($results) : $results);

                // Set icon property
                $this->_set_icon = ($icon ? $icon : 'mdi mdi-table');

                // Set title property
                $this->_set_title = ($results ? $title : ($this->_set_title_fallback ?? phrase('Page not found!')));

                // Set description property
                $this->_set_description = ($results ? $description : ($this->_set_description_fallback ?? phrase('The page you requested does not exist or already been archived.')));
            }
        } else {
            /**
             * -------------------------------------------------------------
             * No database table are included to renderer
             * -------------------------------------------------------------
             */
            // Set template view property
            $this->_view = (is_array($this->_set_template) && isset($this->_set_template['index']) ? $this->_set_template['index'] : $this->_method);

            // Set default result
            $results = [];
            $total = 0;

            // Default icon property
            $this->_set_icon = (isset($this->_set_icon[$this->_method]) ? $this->_set_icon[$this->_method] : (isset($this->_set_icon['index']) ? $this->_set_icon['index'] : 'mdi mdi-file-document-outline'));

            // Default title property
            $this->_set_title = (isset($this->_set_title[$this->_method]) ? $this->_set_title[$this->_method] : (isset($this->_set_title['index']) ? $this->_set_title['index'] : phrase('Untitled')));

            // Default description property
            $this->_set_description = (isset($this->_set_description[$this->_method]) ? $this->_set_description[$this->_method] : (isset($this->_set_description['index']) ? $this->_set_description['index'] : null));
        }

        // Get query string
        $query_string = service('request')->getGet();

        foreach ($query_string as $key => $val) {
            if (in_array($this->_method, ['read', 'update']) && in_array($key, $this->_set_primary)) {
                // Remove query parameter from URL
                $query_string[$key] = null;
            }
        }

        if (! $this->_modal_size && isset($results['column_total'])) {
            if ($results['column_total'] > 3) {
                $this->_modal_size = 'modal-xxl';
            } elseif ($results['column_total'] > 2) {
                $this->_modal_size = 'modal-xl';
            } elseif ($results['column_total'] > 1) {
                $this->_modal_size = 'modal-lg';
            }
        }

        // Prepare output
        $output = [
            'code' => 200,
            'method' => $this->_method,
            'prefer' => service('request')->getPost('prefer'),
            'links' => [
                'base_url' => base_url(),
                'current_module' => go_to(null, $query_string),
                'current_page' => current_page()
            ],
            'meta' => [
                'description' => $this->_set_description,
                'icon' => $this->_set_icon,
                'title' => $this->_set_title,
                'modal_size' => ($this->_modal_size ? $this->_modal_size : ''),
                'segmentation' => array_map(function ($segment = null) {return str_replace('.', '-', preg_replace('/[^a-zA-Z0-9]/', '_', $segment));}, service('uri')->getSegments())
            ],
            'breadcrumb' => $this->template->breadcrumb($this->_set_breadcrumb, $this->_set_title, ($total && ! $this->_set_breadcrumb ? true : false)),
            'query_string' => service('request')->getGet(),
            'results' => $results,
            'total' => $total,
            'elapsed_time' => service('timer')->stop('elapsed_time')->getElapsedTime('elapsed_time'),
            '_token' => sha1(current_page() . ENCRYPTION_KEY . get_userdata('session_generated'))
        ];

        if ($output['results']) {
            if (in_array($this->_method, ['create', 'read', 'update'])) {
                unset($output['total']);
            } else {
                // Add limit
                $output['limit'] = $this->_limit;

                // Add pagination
                $output['pagination'] = $this->template->pagination(
                    [
                        'limit' => $this->_limit_backup,
                        'offset' => $this->_offset,
                        'per_page' => $this->_limit,
                        'total_rows' => $total,
                        'url' => current_page(null, ['per_page' => null])
                    ],
                    ($this->api_client || service('request')->isAJAX())
                );
            }
        }

        // Merge user defined output
        if ($this->_set_output) {
            $output = array_merge($output, $this->_set_output);
        }

        // Generate the output
        if (in_array($this->_method, ['print', 'export', 'pdf'])) {
            $document = new Document();

            $document->pageSize('13in 8.5in');

            $output['meta']['title'] = $title;
            $output['meta']['description'] = $description;
            $output['meta']['icon'] = $icon;
            $output = view('templates/export', (array) json_decode(json_encode($output)));

            // Send to client
            if ('print' == $this->_method) {
                echo $output;

                return true;
            } else {
                return $document->generate($output, $title, ('export' == $this->_method ? 'export' : 'embed'));
            }
        } elseif ($this->api_client && 'GET' != service('request')->getServer('REQUEST_METHOD')) {
            // The method is requested from REST without GET
            return throw_exception(403, phrase('The method you requested is not acceptable') . ' (' . service('request')->getServer('REQUEST_METHOD'). ')', (! $this->api_client ? $this->_redirect_back : null));
        }

        if ($this->api_client) {
            return make_json($output);
        }

        // Send to client
        return $this->template->build($this->_view, $output, $this->_table);
    }

    /**
     * Render and format the output of field
     *
     * @return  array
     */
    public function render_table(array $data = [])
    {
        if (! $this->_set_primary) {
            // Merge method to unset from request because primary key not found
            $this->_unset_method = array_merge($this->_unset_method, ['update', 'delete']);
        }

        $table_data = [];

        // Serialize data
        $serialized = $this->serialize($data);

        if ($serialized) {
            $properties = array_intersect_key(get_object_vars($this), array_flip(['_add_button', '_add_dropdown', '_add_toolbar', '_add_filter', '_column_order', '_grid_view', '_item_reference', '_merge_content', '_merge_label', '_method', '_parameter', '_select', '_set_alias', '_set_autocomplete', '_set_field', '_set_relation', '_set_upload_path', '_table', '_unset_column', '_unset_clone', '_unset_delete', '_unset_method', '_unset_read', '_unset_truncate', '_unset_update', 'api_client', 'model']));

            // Safe abstraction to reduce unnecessary property
            $properties['_set_theme'] = $this->template->theme;

            // Load renderer
            $renderer = new Renderer();
            $renderer->setProperty($properties);
            $renderer->setPath('table');

            $table_data = $renderer->render($serialized, sizeof($data));
        }

        return $table_data;
    }

    /**
     * Render and format the output of field
     *
     * @return  array
     */
    public function render_form(array $data = [])
    {
        if (! $data && ! $this->_insert_on_update_fail && 'autocomplete' != service('request')->getPost('method')) {
            // Data is empty
            return throw_exception(404, phrase('The data you requested does not exist or has been removed'), $this->_redirect_back);
        }

        $field_data = [];

        // Serialize data
        $serialized = $this->serialize($data);

        if ($serialized) {
            // Splice only required property as config
            $properties = array_intersect_key(get_object_vars($this), array_flip(['_column_order', '_column_size', '_default_value', '_field_append', '_field_prepend', '_field_order', '_view_order', '_extra_submit', '_field_position', '_field_size', '_group_field', '_merge_field', '_merge_label', '_method', '_set_alias', '_set_attribute', '_set_autocomplete', '_set_field', '_set_heading', '_set_placeholder', '_set_relation', '_set_tooltip', '_set_upload_path', '_table', 'api_client']));

            // Safe abstraction to reduce unnecessary property
            $properties['_set_theme'] = $this->template->theme;

            // Load renderer
            $renderer = new Renderer();
            $renderer->setProperty($properties);
            $renderer->setPath('form');

            $field_data = $renderer->render($serialized);
        }

        return $field_data;
    }

    /**
     * Render and format the output of field
     *
     * @return  array
     */
    public function render_read(array $data = [])
    {
        if (! $data) {
            // Data empty, throw exception
            return throw_exception(404, phrase('The data you requested does not exist or has been removed'), $this->_redirect_back);
        }

        $field_data = [];

        // Serialize data
        $serialized = $this->serialize($data);

        if ($serialized) {
            // Splice only required property as config
            $properties = array_intersect_key(get_object_vars($this), array_flip(['_column_order', '_column_size', '_field_append', '_field_prepend', '_field_order', '_view_order', '_field_position', '_field_size', '_group_field', '_merge_content', '_merge_field', '_merge_label', '_method', '_set_alias', '_set_attribute', '_set_field', '_set_heading', '_set_relation', '_set_upload_path', '_table', 'api_client']));

            // Safe abstraction to reduce unnecessary property
            $properties['_set_theme'] = $this->template->theme;

            // Load renderer
            $renderer = new Renderer();
            $renderer->setProperty($properties);
            $renderer->setPath('view');

            $field_data = $renderer->render($serialized);
        }

        return $field_data;
    }

    /**
     * Validate the data that submitted through form
     */
    public function validate_form(array $data = [])
    {
        // Check if app on demo mode
        if ($this->_restrict_on_demo) {
            // Demo mode is on
            return throw_exception(403, phrase('This feature is disabled in demo mode'), (! $this->api_client ? $this->_redirect_back : null));
        }

        // Check if method is update
        if ('update' == $this->_method && ! $this->_where && ! $this->_insert_on_update_fail) {
            // Fail because no primary keyword and insert is restricted
            return throw_exception(404, phrase('The data you would to update is not found'), (! $this->api_client ? $this->_redirect_back : null));
        }

        // Serialize the fields
        $serialized = $this->serialize($data);

        if (service('request')->getPost() && is_array($serialized) && sizeof($serialized) > 0) {
            // Store upload path to session
            set_userdata('_set_upload_path', $this->_set_upload_path);

            // Default validation
            $validation = false;

            foreach ($serialized[0] as $key => $val) {
                $type = array_keys($val['type']);

                // Skip field when it's disabled and has no default value
                if (in_array($key, $this->_unset_field) || isset($this->_set_default[$key]) || array_intersect(['current_timestamp'], $type)) {
                    // Skip when field were unset or type is current timestamp
                    continue;
                }

                if (array_intersect(['image'], $type)) {
                    $validation = true;

                    if (in_array('required', explode('|', $val['validation'])) && isset($_FILES[$key]['error']) && 0 === $_FILES[$key]['error']) {
                        $val['validation'] = implode('|', array_diff(explode('|', $val['validation']), ['required']));
                    }

                    // Single upload validation rules
                    $this->form_validation->setRule($key, (isset($this->_set_alias[$key]) ? $this->_set_alias[$key] : ucwords(str_replace('_', ' ', $key))), ($val['validation'] ? $val['validation'] . '|' : null) . 'validate_upload[' . $key . '.image]');
                } elseif (array_intersect(['images'], $type)) {
                    $validation = true;

                    // Images upload validation rules
                    $this->form_validation->setRule($key . '.*', (isset($this->_set_alias[$key]) ? $this->_set_alias[$key] : ucwords(str_replace('_', ' ', $key))), ($val['validation'] ? $val['validation'] . '|' : null) . 'validate_upload[' . $key . '.image]');
                } elseif (array_intersect(['file', 'files'], $type)) {
                    $validation = true;

                    // Files upload validation rules
                    $this->form_validation->setRule($key . '.*', (isset($this->_set_alias[$key]) ? $this->_set_alias[$key] : ucwords(str_replace('_', ' ', $key))), ($val['validation'] ? $val['validation'] . '|' : null) . 'validate_upload[' . $key . ']');
                } elseif (array_intersect(['carousel'], $type)) {
                    $validation = true;

                    // Carousel upload validation rules
                    $this->form_validation->setRule($key . '.background.*', (isset($this->_set_alias[$key]) ? $this->_set_alias[$key] : ucwords(str_replace('_', ' ', $key))), ($val['validation'] ? $val['validation'] . '|' : null) . 'validate_upload[' . $key . '.image]');
                } elseif (array_intersect(['accordion'], $type)) {
                    $validation = true;

                    // Accordion upload validation rules
                    $this->form_validation->setRule($key . '.title.*', phrase('Accordion Title') . ' ' . (isset($this->_set_alias[$key]) ? $this->_set_alias[$key] : ucwords(str_replace('_', ' ', $key))), (service('request')->getPost($key) ? 'trim|' : null) . 'required');
                    $this->form_validation->setRule($key . '.body.*', phrase('Accordion Body') . ' ' . (isset($this->_set_alias[$key]) ? $this->_set_alias[$key] : ucwords(str_replace('_', ' ', $key))), (service('request')->getPost($key) ? 'trim|' : null) . 'required');
                } elseif (array_intersect(['password'], $type)) {
                    $validation = true;

                    // Password validation only when post field has value
                    if (service('request')->getPost($key)) {
                        // Password validation rules
                        $this->form_validation->setRule($key, (isset($this->_set_alias[$key]) ? $this->_set_alias[$key] : ucwords(str_replace('_', ' ', $key))), 'min_length[6]');
                        $this->form_validation->setRule($key . '_confirmation', phrase('Confirmation') . ' ' . (isset($this->_set_alias[$key]) ? $this->_set_alias[$key] : ucwords(str_replace('_', ' ', $key))), ('create' == $this->_method ? 'required|matches[' . $key . ']' : 'matches[' . $key . ']'));
                    }
                } elseif (array_intersect(['encryption'], $type) && $val['validation']) {
                    $validation = true;

                    // Encryption type field validation
                    if ('update' == $this->_method) {
                        // Rules on update method
                        $val['validation'] = implode('|', array_diff(explode('|', $val['validation']), ['required']));
                    }

                    if ($val['validation']) {
                        // Encryption validation rules
                        $this->form_validation->setRule($key, (isset($this->_set_alias[$key]) ? $this->_set_alias[$key] : ucwords(str_replace('_', ' ', $key))), $val['validation']);
                    }
                } elseif (isset($this->_set_relation[$key])) {
                    $validation = true;

                    // Relation table validation
                    if (in_array('required', explode('|', $val['validation']))) {
                        // Apply rules only when it's required
                        $this->form_validation->setRule($key, (isset($this->_set_alias[$key]) ? $this->_set_alias[$key] : ucwords(str_replace('_', ' ', $key))), 'required|relation_checker[' . (strpos($this->_set_relation[$key]['relation_table'], ' ') !== false ? substr($this->_set_relation[$key]['relation_table'], 0, strpos($this->_set_relation[$key]['relation_table'], ' ')) : $this->_set_relation[$key]['relation_table']) . '.' . $this->_set_relation[$key]['relation_key'] . ']');
                    } else {
                        // Find foreign data
                        $constrained = false;
                        $foreign_data = $this->model->foreign_data($this->_table);

                        if ($foreign_data) {
                            // Find foreign data with loop
                            foreach ($foreign_data as $_key => $_val) {
                                // Table has foreign key
                                if ($this->_set_relation[$key]['relation_table'] == $_val->foreign_table_name) {
                                    // Set constraint
                                    $constrained = true;
                                }
                            }
                        }

                        if ($constrained) {
                            // Apply only for constrained table relation
                            $this->form_validation->setRule($key, (isset($this->_set_alias[$key]) ? $this->_set_alias[$key] : ucwords(str_replace('_', ' ', $key))), 'required|relation_checker[' . (strpos($this->_set_relation[$key]['relation_table'], ' ') !== false ? substr($this->_set_relation[$key]['relation_table'], 0, strpos($this->_set_relation[$key]['relation_table'], ' ')) : $this->_set_relation[$key]['relation_table']) . '.' . $this->_set_relation[$key]['relation_key'] . ']');
                        }
                    }
                } else {
                    if ($val['validation'] && strncmp('callback_', $val['validation'], 9) !== false) {
                        // Callback validation, old is gold :)
                        $validation = explode('|', $val['validation']);

                        foreach ($validation as $index => $callback) {
                            $callback = preg_replace('/callback_/', '', $callback, 1);

                            if (method_exists($this, $callback)) {
                                $validate = $this->$callback(service('request')->getVar($key));

                                if (true !== $validate) {
                                    // Validation error, throw exception
                                    $this->form_validation->setError($key, $validate ?? '');
                                }

                                // Unset valid callback from validation
                                unset($validation[$index]);
                            }
                        }

                        // Restructure new validation
                        $val['validation'] = implode('|', $validation);
                    }

                    // Default suffix
                    $validation_suffix = null;

                    if (array_intersect(['yearpicker'], $type)) {
                        // Year validation rules
                        $validation_suffix = 'valid_year';
                    } elseif (array_intersect(['hour'], $type)) {
                        // Hour validation rules
                        $validation_suffix = 'numeric|max_length[2]';
                    } elseif (array_intersect(['date_only'], $type)) {
                        // Only date (MM) validation rules
                        $validation_suffix = 'numeric|max_length[2]';
                    } elseif (array_intersect(['date', 'datepicker'], $type)) {
                        // Date (YYYY-MM-DD) validation rules
                        $validation_suffix = 'valid_date';
                    } elseif (array_intersect(['timestamp', 'datetime', 'datetimepicker'], $type)) {
                        // Full timestamp validation rules
                        $validation_suffix = 'valid_datetime';
                    }

                    if ($val['validation'] && ! isset($this->_set_default[$key])) {
                        // Validate only when no default set to field
                        $validation = true;

                        if (is_array(service('request')->getPost($key))) {
                            // Array validation rules
                            $this->form_validation->setRule($key . '.*', (isset($this->_set_alias[$key]) ? $this->_set_alias[$key] : ucwords(str_replace('_', ' ', $key))), $val['validation']);
                        } else {
                            // Input validation rules
                            $this->form_validation->setRule($key, (isset($this->_set_alias[$key]) ? $this->_set_alias[$key] : ucwords(str_replace('_', ' ', $key))), ($val['validation'] ? $val['validation'] : null) . ($validation_suffix ? '|' . $validation_suffix : null));
                        }
                    } elseif ($validation_suffix) {
                        // Validate only when no default set to field
                        $validation = true;

                        // Apply rules suffix
                        $this->form_validation->setRule($key, (isset($this->_set_alias[$key]) ? $this->_set_alias[$key] : ucwords(str_replace('_', ' ', $key))), $validation_suffix);
                    }
                }
            }

            // Run validation
            if ($validation && $this->form_validation->run(service('request')->getPost()) === false) {
                // Unlink the files
                $this->_unlink_files(get_userdata('_uploaded_files'));

                // Data invalid
                $errors = $this->form_validation->getErrors();

                foreach ($errors as $field => $message) {
                    // Unset unnecessary field
                    if (strpos($field, '.') !== false) {
                        unset($errors[$field]);
                    }
                }

                return throw_exception(400, $errors);
            }

            // Attempt to get uploaded files string from the session
            $this->_uploaded_files = get_userdata('_uploaded_files');

            $prepare = [];
            $clone = [];
            $batch_data = [];

            if ($this->_cloning) {
                // Clone data
                $clone = $this->model->get_where($this->_table, $this->_where, 1)->row_array();
            }

            foreach ($serialized[0] as $field => $value) {
                $type = array_keys($value['type']);

                // Skip field when it's disabled and has no default value
                if ((in_array($field, $this->_unset_field) && ! isset($this->_set_default[$field]) && ! array_intersect(['to_slug', 'current_timestamp'], $type)) || (in_array('disabled', $type) && ! isset($this->_set_default[$field]))) {
                    continue;
                }

                if (array_key_exists($field, service('request')->getPost()) || array_intersect($type, ['current_timestamp', 'image', 'images', 'file', 'files', 'to_slug', 'current_user', 'carousel', 'accordion'])) {
                    if (array_intersect(['password'], $type)) {
                        // Check if password changed
                        if (service('request')->getPost($field)) {
                            // Store new password
                            $prepare[$field] = password_hash(service('request')->getPost($field) . ENCRYPTION_KEY, PASSWORD_DEFAULT);
                        }

                        // Cloning
                        elseif ($this->_cloning && isset($clone[$field])) {
                            // Clone value
                            $prepare[$field] = $clone[$field];
                        }
                    } elseif (array_intersect(['encryption'], $type)) {
                        // Check if value changed
                        if (service('request')->getPost($field)) {
                            // Store new encryption
                            $prepare[$field] = base64_encode(service('encrypter')->encrypt(service('request')->getPost($field)));
                        }
                    } elseif (array_intersect(['image'], $type)) {
                        // Set the default value
                        $source = 'placeholder.png';

                        // Push to data preparation
                        $prepare[$field] = $source;

                        // Check if the uploaded file is valid
                        if (isset($this->_uploaded_files[$field]) && is_array($this->_uploaded_files[$field])) {
                            // Loop to get source from unknown array key
                            foreach ($this->_uploaded_files[$field] as $key => $src) {
                                // Set new source
                                $source = $src;
                            }

                            // Push to data preparation
                            $prepare[$field] = $source;
                        } else {
                            // Check if the method is not create
                            if ('create' != $this->_method && ! $this->_cloning) {
                                // Unset the field for update preparation
                                unset($prepare[$field]);
                            }

                            // Cloning
                            elseif ($this->_cloning && isset($clone[$field])) {
                                // Clone value
                                $prepare[$field] = $clone[$field];
                            }
                        }
                    } elseif (array_intersect(['images', 'file', 'files'], $type)) {
                        $files = [];

                        if (is_array(service('request')->getPost($field . '_label'))) {
                            // Reverse file attributes to match with newest upload data
                            $files = array_reverse(service('request')->getPost($field . '_label'));
                            $uploaded = (isset($this->_uploaded_files[$field]) ? array_reverse(array_values($this->_uploaded_files[$field])) : []);

                            // Combine uploaded files to the old one
                            $uploaded = array_combine(array_intersect_key($uploaded, $files), array_intersect_key($files, $uploaded));

                            if ($uploaded) {
                                // Merge files
                                $files = array_merge($uploaded, array_slice($files, sizeof($uploaded)));
                            }
                        }

                        // Push the json encoded file to data preparation
                        $prepare[$field] = json_encode(array_reverse($files));
                    } elseif (array_intersect(['carousel'], $type)) {
                        // Get the submitted carousel
                        $carousel = service('request')->getPost($field);

                        // Check if submitted data is not supported
                        if (! $carousel || ! isset($carousel['title']) || sizeof($carousel['title']) <= 0) {
                            // Continue the loops to other fields
                            continue;
                        }

                        // Set the default value of variables and shorts the key to match ruler indent
                        $items = [];

                        // Loop the submitted carousel data
                        foreach ($carousel['title'] as $key => $val) {
                            // Push the carousel collection
                            $items[$key] = [
                                'title' => $val,
                                'description' => (isset($carousel['description'][$key]) ? $carousel['description'][$key] : ''),
                                'link' => (isset($carousel['link'][$key]) ? $carousel['link'][$key] : ''),
                                'label' => (isset($carousel['label'][$key]) ? $carousel['label'][$key] : '')
                            ];

                            // Check if the carousel has uploaded background
                            if (isset($this->_uploaded_files[$field][$key]) && $this->_uploaded_files[$field][$key]) {
                                // Pair with newer uploaded background
                                $items[$key]['background'] = $this->_uploaded_files[$field][$key];
                            } else {
                                // Use default background instead
                                $items[$key]['background'] = (isset($carousel['default_background'][$key]) ? $carousel['default_background'][$key] : 'placeholder.png');
                            }
                        }

                        // Push the json encoded to data preparation
                        $prepare[$field] = json_encode($items);
                    } elseif (array_intersect(['accordion'], $type)) {
                        // Get the submitted accordion
                        $accordion = service('request')->getPost($field);
                        $items = [];

                        // Check if the accordion has correct value
                        if (isset($accordion['title']) && sizeof($accordion['title']) > 0) {
                            // Loops the submitted accordion
                            foreach ($accordion['title'] as $key => $val) {
                                // Collects the accordion
                                $items[] = [
                                    'title' => $val,
                                    'body' => (isset($accordion['body'][$key]) ? $accordion['body'][$key] : null)
                                ];
                            }
                        }

                        // Push the json encoded accordion to data preparation
                        $prepare[$field] = json_encode($items);
                    } elseif (array_intersect(['attribution'], $type)) {
                        // Get the submitted attribution
                        $attribution = service('request')->getPost($field);
                        $items = [];

                        // Check if the submitted attribution is in correct format
                        if ($attribution && isset($attribution['label']) && sizeof($attribution['label']) > 0) {
                            // Loops the submitted attribution
                            foreach ($attribution['label'] as $key => $val) {
                                // Collect the attribution
                                $items[] = [
                                    'label' => $val,
                                    'value' => (isset($attribution['value'][$key]) ? $attribution['value'][$key] : null)
                                ];
                            }
                        }

                        // Push the json encoded attribution to data preparation
                        $prepare[$field] = json_encode($items);
                    } elseif (array_intersect(['wysiwyg'], $type)) {
                        // Sanitize the wysiwyg from the XSS attack
                        $value = service('request')->getPost($field);

                        if ($value) {
                            $value = str_ireplace(['<?php', '?>'], ['&lt;?php', '?&gt;'], $value);
                            $value = str_ireplace(['<script', '</script>'], ['&lt;script', '&lt;/script&gt;'], $value);
                            $value = str_ireplace(['<noscript', '</noscript>'], ['&lt;noscript', '&lt;/noscript&gt;'], $value);
                            $value = str_ireplace(['<style', '</style>'], ['&lt;style', '&lt;/style&gt;'], $value);
                            $value = str_ireplace('<link', '&lt;link', $value);
                            $value = str_ireplace(['onclick="', 'onerror="'], 'xss-clean="', $value);
                        }

                        // Push the boolean field type to data preparation
                        $prepare[$field] = $value;
                    } elseif (array_intersect(['boolean'], $type)) {
                        // Push the boolean field type to data preparation
                        $prepare[$field] = service('request')->getPost($field);
                    } elseif (array_intersect(['current_timestamp'], $type)) {
                        // Push the current timestamp field type to data preparation
                        $prepare[$field] = date('Y-m-d H:i:s');
                    } elseif (array_intersect(['date', 'datepicker'], $type)) {
                        // Push the date field type to data preparation
                        $prepare[$field] = date('Y-m-d', strtotime(service('request')->getPost($field)));
                    } elseif (array_intersect(['datetime', 'datetimepicker'], $type)) {
                        // Push the submitted timestamp field type to data preparation
                        $prepare[$field] = date('Y-m-d H:i:s', strtotime(service('request')->getPost($field)));
                    } elseif (array_intersect(['monthpicker'], $type)) {
                        // Push the month field type to data preparation
                        $prepare[$field] = get_userdata('year') . '-' . service('request')->getPost($field) . '-01';
                    } elseif (array_intersect(['price_format'], $type)) {
                        // Push the price field type to data preparation
                        $value = trim(service('request')->getPost($field));
                        $value = str_replace(',', '', $value);
                        $prepare[$field] = $value;
                    } elseif (array_intersect(['number_format'], $type)) {
                        // Push the number format field type to data preparation
                        $value = trim(service('request')->getPost($field));
                        $value = str_replace(',', '', $value);
                        $prepare[$field] = $value;
                    } elseif (array_intersect(['to_slug'], $type)) {
                        // Check if slug has its own post data
                        if (service('request')->getPost($field)) {
                            // Use its own data as slug
                            $title = service('request')->getPost($field);
                        } elseif (service('request')->getPost($value['type']['to_slug']['parameter'])) {
                            // Or match other field from given parameter
                            $title = service('request')->getPost($value['type']['to_slug']['parameter']);
                        } else {
                            // Otherwise, use the time instead
                            $title = time();
                        }

                        // Push the slug to the data preparation
                        $prepare[$field] = format_slug($title);
                    } elseif (array_intersect(['current_user'], $type)) {
                        // Push current user id to the data preparation
                        $prepare[$field] = get_userdata('user_id');
                    } else {
                        // Convert the submitted array data as encoded json, or use the original
                        $prepare[$field] = (is_array(service('request')->getPost($field)) ? json_encode(service('request')->getPost($field)) : service('request')->getPost($field));
                    }

                    // Apply the formatter when not match any given parameter
                    if (! array_intersect(['to_slug', 'password', 'encryption', 'image', 'images', 'file', 'files'], $type)) {
                        // Use empty value instead of NULL when no data is submitted
                        if (! isset($prepare[$field])) {
                            $prepare[$field] = '';
                        }
                    }

                    if (! array_intersect(['wysiwyg', 'encryption'], $type) && isset($prepare[$field])) {
                        $prepare[$field] = str_replace(['<', '>'], ['&lt;', '&gt;'], $prepare[$field]);
                    }
                }

                // Check if the field is sets to use the default value
                if (isset($this->_set_default[$field]) && ($this->_set_default[$field] || is_numeric($this->_set_default[$field]))) {
                    // Push the default value to the data preparation
                    $prepare[$field] = $this->_set_default[$field];
                }

                // Or when it's a boolean and no value
                elseif (array_intersect(['boolean'], $type) && ! service('request')->getPost($field) && ! in_array($field, $this->_unset_field)) {
                    // Sets to "0" instead of null
                    $prepare[$field] = 0;
                }
            }

            // If data preparation is ready and the method is create
            if ($prepare && in_array('create', [$this->_method])) {
                // Insert new data
                $this->insert_data($this->_table, $prepare);
            } elseif ($prepare && in_array('update', [$this->_method])) {
                // If data preparation is ready and the method is update
                if ($this->_cloning) {
                    // Insert new data
                    $this->insert_data($this->_table, $prepare);
                } else {
                    // Update the old data
                    $this->update_data($this->_table, $prepare, $this->_where);
                }
            } else {
                // Unlink the files
                $this->_unlink_files(get_userdata('_uploaded_files'));

                // Throw the exception messages
                return throw_exception(403, phrase('The method you requested is not acceptable') . ' (' . service('request')->getServer('REQUEST_METHOD'). ')', (! $this->api_client ? $this->_redirect_back : null));
            }
        } else {
            // No data are found
            return throw_exception(404, phrase('No data can be executed'), (! $this->api_client ? $this->_redirect_back : null));
        }
    }

    /**
     * This function sustain to insert data if there's no data to update
     */
    public function insert_on_update_fail(bool $return = true)
    {
        $this->_insert_on_update_fail = $return;

        return $this;
    }

    /**
     * Inserting data to the database
     */
    public function insert_data(string $table = null, array $data = [])
    {
        if ($this->api_client && 'POST' != service('request')->getServer('REQUEST_METHOD')) {
            // Unlink the files
            $this->_unlink_files(get_userdata('_uploaded_files'));

            // Indicates the method is requested through REST
            return throw_exception(403, phrase('The method you requested is not acceptable') . ' (' . service('request')->getServer('REQUEST_METHOD'). ')', (! $this->api_client ? $this->_redirect_back : null));
        }

        if ($table && $this->model->table_exists($table)) {
            if (method_exists($this, 'before_insert')) {
                // Call a function before insert
                $this->before_insert();
            }

            if ($this->model->insert($table, $data)) {
                $auto_increment = true;
                $primary = 0;

                if ('Postgre' == $this->_db_driver) {
                    // Apply only PostgreSQL database
                    $auto_increment = false;
                    $field_data = $this->model->field_data($table);

                    foreach ($field_data as $key => $val) {
                        if (isset($this->_set_default[$val->name])) {
                            $primary = $this->_set_default[$val->name];
                        }

                        if ((isset($val->primary_key) && 1 === $val->primary_key) || (isset($val->default) && $val->default && stripos($val->default, 'nextval(') !== false)) {
                            // Mock autoincrement
                            $auto_increment = true;
                        }

                        if ($primary && $auto_increment) {
                            // Break operation if primary key found
                            break;
                        }
                    }
                }

                // Get last insert id
                $this->_insert_id = ($auto_increment ? $this->model->insert_id() : 0);

                // Unset uploaded files string from session
                unset_userdata('_uploaded_files');

                if (method_exists($this, 'after_insert')) {
                    // Call user function after insert
                    $this->after_insert();
                }

                // Send to client
                return throw_exception(($this->api_client ? 200 : 301), phrase('The data was successfully submitted'), (! $this->api_client ? $this->_redirect_back : null));
            } else {
                // Unlink the files
                $this->_unlink_files(get_userdata('_uploaded_files'));

                // Otherwise, the item is cannot be deleted
                $error = $this->model->error();

                if (in_array(get_userdata('group_id'), [1]) && ENVIRONMENT != 'production' && isset($error['message'])) {
                    // For administrator
                    return throw_exception(500, $error['message'], (! $this->api_client ? $this->_redirect_back : null));
                }

                // For user
                return throw_exception(500, phrase('Unable to submit your data.') . ' ' . phrase('Please try again or contact the system administrator.') . ' ' . phrase('Error code') . ': <b>500 (INSERT)</b>', (! $this->api_client ? $this->_redirect_back : null));
            }
        } else {
            // Unlink the files
            $this->_unlink_files(get_userdata('_uploaded_files'));

            return throw_exception(404, phrase('The selected database table does not exist'), (! $this->api_client ? $this->_redirect_back : null));
        }
    }

    /**
     * Get latest insert id
     *
     * @return  int
     */
    public function insert_id()
    {
        return $this->_insert_id;
    }

    /**
     * Update data from database
     */
    public function update_data(string $table = null, array $data = [], array $where = [])
    {
        if ($this->api_client && 'POST' != service('request')->getServer('REQUEST_METHOD')) {
            // Unlink the files
            $this->_unlink_files(get_userdata('_uploaded_files'));

            // Indicate the method is requested through REST
            return throw_exception(403, phrase('The method you requested is not acceptable') . ' (' . service('request')->getServer('REQUEST_METHOD'). ')', (! $this->api_client ? $this->_redirect_back : null));
        }

        if ($table && $this->model->table_exists($table)) {
            foreach ($where as $key => $val) {
                // Find string after dot
                $keyword = substr($key . '.', 0, strpos($key, '.'));

                if ($keyword && ! in_array($keyword, $this->_compiled_table)) {
                    // Keyword is not listed in compiled table
                    unset($where[$key]);
                }
            }

            $query = null;

            if (is_array($where) && sizeof($where) > 0) {
                // Check if data is exist in table
                $query = $this->model->get_where($table, $where, 1)->row();
            }

            if ($query) {
                if (method_exists($this, 'before_update')) {
                    // Call function before update
                    $this->before_update();
                }

                // Set default variables
                $old_files = [];

                if ($query) {
                    // Attempt to get old files
                    foreach ($query as $field => $value) {
                        if (isset($this->_set_field[$field]['field_type']) && array_intersect($this->_set_field[$field]['field_type'], ['file', 'files', 'image', 'images'])) {
                            $old_files[$field] = $value;
                        }
                    }
                }

                // Attempt to update data
                if ($this->model->update($table, $data, $where)) {
                    // Unset uploaded files string from session
                    unset_userdata('_uploaded_files');

                    // Unlink old files
                    $this->_unlink_files($old_files);

                    if (method_exists($this, 'after_update')) {
                        // Call function after update
                        $this->after_update();
                    }

                    return throw_exception(($this->api_client ? 200 : 301), phrase('The data was successfully updated'), (! $this->api_client ? $this->_redirect_back : null));
                } else {
                    // Unlink the files
                    $this->_unlink_files(get_userdata('_uploaded_files'));

                    // Otherwise, the item is cannot be deleted
                    $error = $this->model->error();

                    if (in_array(get_userdata('group_id'), [1]) && isset($error['message'])) {
                        // For administrator
                        return throw_exception(500, $error['message'], (! $this->api_client ? $this->_redirect_back : null));
                    }

                    // For user
                    return throw_exception(500, phrase('Unable to update the data') . '. ' . phrase('Please try again or contact the system administrator') . '. ' . phrase('Error code') . ': <b>500 (UPDATE)</b>', (! $this->api_client ? $this->_redirect_back : null));
                }
            } elseif ($this->_insert_on_update_fail) {
                // Attempt to insert data
                $this->insert_data($table, $data);
            } else {
                // Unlink the files
                $this->_unlink_files(get_userdata('_uploaded_files'));

                return throw_exception(404, phrase('The data you would to update is not found'), (! $this->api_client ? $this->_redirect_back : null));
            }
        } else {
            // Unlink the files
            $this->_unlink_files(get_userdata('_uploaded_files'));

            return throw_exception(404, phrase('The selected database table does not exist'), (! $this->api_client ? $this->_redirect_back : null));
        }

        return false;
    }

    /**
     * Delete multiple data from the database
     */
    public function delete_data(string $table = null, array $where = [], int $limit = 1)
    {
        if ($this->api_client && 'DELETE' != service('request')->getServer('REQUEST_METHOD')) {
            // Indicate the method is requested through API
            return throw_exception(403, phrase('The method you requested is not acceptable') . ' (' . service('request')->getServer('REQUEST_METHOD'). ')', (! $this->api_client ? $this->_redirect_back : null));
        }

        // Check if app on demo mode
        if ($this->_restrict_on_demo) {
            return throw_exception(403, phrase('This feature is disabled in demo mode'), (! $this->api_client ? $this->_redirect_back : null));
        }

        // Check if delete have a callback message
        if (isset($this->_set_messages['delete']) && $this->_set_messages['delete']['return']) {
            // Use the callback message
            return throw_exception($this->_set_messages['delete']['code'], $this->_set_messages['delete']['messages'], $this->_redirect_back);
        }

        // Check if targeted table is exists
        if ($table && $this->model->table_exists($table)) {
            // safe check to make sure the given field parameter is exists in the database table
            $field_exists = array_flip($this->model->list_fields($table));
            $where = array_intersect_key(service('request')->getGet(), $field_exists);

            // make sure the delete action have where as condition
            if (! $where) {
                // otherwise, redirect to previous page
                return throw_exception(404, phrase('The data you would to delete is not found'), (! $this->api_client ? $this->_redirect_back : null));
            }

            // Check if data is exist in table
            $query = $this->model->get_where($table, $where, 1)->row();

            if ($query) {
                if (method_exists($this, 'before_delete')) {
                    // Call function before delete
                    $this->before_delete();
                }

                // Set default variables
                $old_files = [];

                // Attempt to get old files
                foreach ($query as $field => $value) {
                    if (isset($this->_set_field[$field]['field_type']) && array_intersect($this->_set_field[$field]['field_type'], ['file', 'files', 'image', 'images'])) {
                        $old_files[$field] = $value;
                    }
                }

                // Safe check for delete
                if ($this->model->delete($table, $where, $limit)) {
                    // Unlink old files
                    $this->_unlink_files($old_files);

                    if (method_exists($this, 'after_delete')) {
                        // Call function after delete
                        $this->after_delete();
                    }

                    return throw_exception(($this->api_client ? 200 : 301), phrase('The data was successfully deleted'), (! $this->api_client ? $this->_redirect_back : null));
                } else {
                    // Otherwise, the item is cannot be deleted
                    $error = $this->model->error();

                    if (in_array(get_userdata('group_id'), [1]) && isset($error['message'])) {
                        // For administrator
                        return throw_exception(500, $error['message'], (! $this->api_client ? $this->_redirect_back : null));
                    }

                    // For user
                    return throw_exception(500, phrase('Unable to delete the requested data') . '. ' . phrase('Please try again or contact the system administrator') . '. ' . phrase('Error code') . ': <b>500 (DELETE)</b>', (! $this->api_client ? $this->_redirect_back : null));
                }
            } else {
                // No item found
                return throw_exception(404, phrase('The data you would to delete is not found'), (! $this->api_client ? $this->_redirect_back : null));
            }
        } else {
            // The targeted database table isn't exists
            return throw_exception(404, phrase('The selected database table does not exist'), (! $this->api_client ? $this->_redirect_back : null));
        }
    }

    /**
     * Delete multiple data from the database
     */
    public function delete_batch(string $table = null)
    {
        if ($this->api_client && 'DELETE' != service('request')->getServer('REQUEST_METHOD')) {
            // Indicate the method is requested through API
            return throw_exception(403, phrase('The method you requested is not acceptable') . ' (' . service('request')->getServer('REQUEST_METHOD'). ')', (! $this->api_client ? $this->_redirect_back : null));
        }

        // Check if app on demo mode
        if ($this->_restrict_on_demo) {
            return throw_exception(403, phrase('This feature is disabled in demo mode'), (! $this->api_client ? $this->_redirect_back : null));
        }

        // Get the checked items
        $items = service('request')->getPost('items');
        $affected_rows = 0;
        $ignored_rows = 0;

        if (is_array($items) && sizeof($items) > 0) {
            // Before delete callback
            if (method_exists($this, 'before_delete')) {
                // Call function before delete
                $this->before_delete();
            }

            // Safe check to make sure the given field parameter is exists in the database table
            $field_exists = array_flip($this->model->list_fields($table));

            foreach ($items as $key => $val) {
                // Unset the field parameter that not exist in database table
                $val = array_intersect_key(json_decode($val, true), $field_exists);

                if (! $val) {
                    // No where clause, skip deleting
                    continue;
                }

                $ignore = false;

                if ($this->_unset_delete) {
                    foreach ($this->_unset_delete as $_key => $_val) {
                        if (isset($val[$_key]) && in_array($val[$_key], $_val)) {
                            $ignore = true;

                            break;
                        }
                    }
                }

                if ($ignore) {
                    $ignored_rows++;

                    continue;
                }

                // Get old data to prepare file deletion
                $query = $this->model->get_where($table, $val, 1)->result();

                // Set default variables
                $old_files = null;

                // Attempt to get old files
                foreach ($query as $field => $value) {
                    if (isset($this->_set_field[$field]['field_type']) && array_intersect($this->_set_field[$field]['field_type'], ['file', 'files', 'image', 'images'])) {
                        $old_files[$field] = $value;
                    }
                }

                if ($query && $this->model->delete($table, $val)) {
                    // Unlink old files
                    $this->_unlink_files($old_files);

                    $affected_rows++;
                }
            }

            if (method_exists($this, 'after_delete')) {
                // Call function after delete
                $this->after_delete();
            }
        }

        if ($affected_rows) {
            // Deletion success
            return throw_exception(($this->api_client ? 200 : 301), $affected_rows . ' ' . strtolower(phrase('of')) . ' ' . sizeof($items) . ' ' . strtolower(phrase('data was successfully removed')), (! $this->api_client ? $this->_redirect_back : null));
        } else {
            // Deletion fail
            return throw_exception(403, phrase('Unable to remove the selected data'), (! $this->api_client ? $this->_redirect_back : null));
        }
    }

    /**
     * =========================================================================
     * Query Builder
     * =========================================================================
     */
    /**
     * Select field
     *
     * Possible to use comma separated
     */
    public function select(string $column, bool $escape = true)
    {
        if (! is_array($column)) {
            // Split selected by comma, but ignore that inside brackets
            $column = array_map('trim', preg_split('/,(?![^(]+\))/', $column));
        }

        foreach ($column as $key => $val) {
            // Make backup value for next statement
            $backup_val = $val;

            // Push to the select list
            $this->_select[] = $val;

            // Find the prefixed table select
            if (strpos($val, '.') !== false && strpos($val, '(') === false && strpos($val, ')') === false) {
                $val = substr($val, strpos($val, '.') + 1);
            }

            // Find the aliased selection
            if (stripos($val, ' AS ') !== false) {
                $val = substr($val, stripos($val, ' AS ') + 4);
            }

            if (strpos($backup_val, '(') === false && strpos($backup_val, ')') === false) {
                // Push to the compilation
                $this->_compiled_select[] = $val;
            }
        }

        $this->_prepare(__FUNCTION__, [$column, $escape]);

        return $this;
    }

    /**
     * Select count
     *
     * Possible to use comma separated
     */
    public function select_count(string $column, string $alias = null)
    {
        $this->_prepare(__FUNCTION__, [$column, $alias]);

        return $this;
    }

    /**
     * Select sum
     *
     * Possible to use comma separated
     */
    public function select_sum(string $column, string $alias = null)
    {
        $this->_prepare(__FUNCTION__, [$column, $alias]);

        return $this;
    }

    /**
     * Select minimum
     *
     * Possible to use comma separated
     */
    public function select_min(string $column, string $alias = null)
    {
        $this->_prepare(__FUNCTION__, [$column, $alias]);

        return $this;
    }

    /**
     * Select maximum
     *
     * Possible to use comma separated
     */
    public function select_max(string $column, string $alias = null)
    {
        $this->_prepare(__FUNCTION__, [$column, $alias]);

        return $this;
    }

    /**
     * Select average
     *
     * Possible to use comma separated
     */
    public function select_avg(string $column, string $alias = null)
    {
        $this->_prepare(__FUNCTION__, [$column, $alias]);

        return $this;
    }

    /**
     * Prevent column to be selected
     *
     * Possible to use comma separated
     */
    public function unset_select(string $column)
    {
        if (! is_array($column)) {
            // Split selected by comma, but ignore that inside brackets
            $column = array_map('trim', preg_split('/,(?![^(]+\))/', $column));
        }

        $this->_unset_select = ($this->_unset_select ? array_merge($this->_unset_select, $column) : $column);

        return $this;
    }

    /**
     * Distinct field
     */
    public function distinct(bool $flag = true)
    {
        $this->_distinct = $flag;

        return $this;
    }

    /**
     * Set the primary table
     */
    public function from(string $table)
    {
        $this->_table = $table;

        return $this;
    }

    /**
     * Set the primary table
     *
     * Aliases to "from" method
     */
    public function table(string $table)
    {
        $this->_table = $table;

        return $this;
    }

    /**
     * Join table
     */
    public function join(string $table, string $condition, string $type = '', bool $escape = true)
    {
        if (! in_array($this->_method, ['delete'])) {
            $this->_prepare(__FUNCTION__, [$table, $condition, $type, $escape]);

            if (strpos($table, ' ') !== false) {
                $table = substr($table, strrpos($table, ' ') + 1);
            }

            $this->_compiled_table[] = $table;
        }

        return $this;
    }

    /**
     * Where clause
     *
     * @param   array|string $field
     */
    public function where($field = [], $value = '', bool $escape = true)
    {
        if (is_array($field)) {
            foreach ($field as $key => $val) {
                $this->_prepare(__FUNCTION__, [$key, $val, $escape]);
            }
        } else {
            $this->_prepare(__FUNCTION__, [$field, $value, $escape]);
        }

        return $this;
    }

    /**
     * Or where clause
     *
     * @param   array|string $field
     */
    public function or_where($field = [], $value = '', bool $escape = true)
    {
        if (is_array($field)) {
            foreach ($field as $key => $val) {
                $this->_prepare(__FUNCTION__, [$key, $val, $escape]);
            }
        } else {
            $this->_prepare(__FUNCTION__, [$field, $value, $escape]);
        }

        return $this;
    }

    /**
     * Where in clause
     *
     * @param   array|string $field
     */
    public function where_in($field = [], $value = '', bool $escape = true)
    {
        if (is_array($field)) {
            foreach ($field as $key => $val) {
                $this->_prepare(__FUNCTION__, [$key, $val, $escape]);
            }
        } else {
            $this->_prepare(__FUNCTION__, [$field, $value, $escape]);
        }

        return $this;
    }

    /**
     * Or where in clause
     *
     * @param   array|string $field
     */
    public function or_where_in($field = [], $value = '', bool $escape = true)
    {
        if (is_array($field)) {
            foreach ($field as $key => $val) {
                $this->_prepare(__FUNCTION__, [$key, $val, $escape]);
            }
        } else {
            $this->_prepare(__FUNCTION__, [$field, $value, $escape]);
        }

        return $this;
    }

    /**
     * Where not in clause
     *
     * @param   array|string $field
     */
    public function where_not_in($field = [], $value = '', bool $escape = true)
    {
        if (is_array($field)) {
            foreach ($field as $key => $val) {
                $this->_prepare(__FUNCTION__, [$key, $val, $escape]);
            }
        } else {
            $this->_prepare(__FUNCTION__, [$field, $value, $escape]);
        }

        return $this;
    }

    /**
     * Or where not in clause
     *
     * @param   array|string $field
     */
    public function or_where_not_in($field = [], $value = '', bool $escape = true)
    {
        if (is_array($field)) {
            foreach ($field as $key => $val) {
                $this->_prepare(__FUNCTION__, [$key, $val, $escape]);
            }
        } else {
            $this->_prepare(__FUNCTION__, [$field, $value, $escape]);
        }

        return $this;
    }

    /**
     * Like clause
     *
     * @param   array|string $field
     */
    public function like($field = [], $match = '', string $side = 'both', bool $escape = true, bool $case_insensitive = true)
    {
        if (is_array($field)) {
            foreach ($field as $key => $val) {
                $this->_like[$key] = $val;

                $this->_prepare(__FUNCTION__, [$key, $val, $escape]);
            }
        } else {
            $this->_like[$field] = $match;

            $this->_prepare(__FUNCTION__, [$field, $match, $side, $escape, $case_insensitive]);
        }

        return $this;
    }

    /**
     * Or like clause
     *
     * @param   array|string $field
     */
    public function or_like($field = [], $match = '', string $side = 'both', bool $escape = true, bool $case_insensitive = false)
    {
        if (is_array($field)) {
            foreach ($field as $key => $val) {
                $this->_prepare(__FUNCTION__, [$key, $val, $escape]);
            }
        } else {
            $this->_prepare(__FUNCTION__, [$field, $match, $side, $escape, $case_insensitive]);
        }

        return $this;
    }

    /**
     * Not like clause
     *
     * @param   array|string $field
     */
    public function not_like($field = [], $match = '', string $side = 'both', bool $escape = true, bool $case_insensitive = false)
    {
        if (is_array($field)) {
            foreach ($field as $key => $val) {
                $this->_prepare(__FUNCTION__, [$key, $val, $escape]);
            }
        } else {
            $this->_prepare(__FUNCTION__, [$field, $match, $side, $escape, $case_insensitive]);
        }

        return $this;
    }

    /**
     * Or not like clause
     *
     * @param   array|string $field
     */
    public function or_not_like($field = [], $match = '', string $side = 'both', bool $escape = true, bool $case_insensitive = false)
    {
        if (is_array($field)) {
            foreach ($field as $key => $val) {
                $this->_prepare(__FUNCTION__, [$key, $val, $escape]);
            }
        } else {
            $this->_prepare(__FUNCTION__, [$field, $match, $side, $escape, $case_insensitive]);
        }

        return $this;
    }

    /**
     * Having clause
     *
     * @param   array|string $field
     */
    public function having($field = [], $value = '', bool $escape = true)
    {
        if (is_array($field)) {
            foreach ($field as $key => $val) {
                $this->_prepare(__FUNCTION__, [$key, $val, $escape]);
            }
        } else {
            $this->_prepare(__FUNCTION__, [$field, $value, $escape]);
        }

        return $this;
    }

    /**
     * Or having clause
     *
     * @param   array|string $field
     */
    public function or_having($field = [], $value = '', bool $escape = true)
    {
        if (is_array($field)) {
            foreach ($field as $key => $val) {
                $this->_prepare(__FUNCTION__, [$key, $val, $escape]);
            }
        } else {
            $this->_prepare(__FUNCTION__, [$field, $value, $escape]);
        }

        return $this;
    }

    /**
     * Having in clause
     *
     * @param   array|string $field
     */
    public function having_in($field = [], $value = '', bool $escape = true)
    {
        if (is_array($field)) {
            foreach ($field as $key => $val) {
                $this->_prepare(__FUNCTION__, [$key, $val, $escape]);
            }
        } else {
            $this->_prepare(__FUNCTION__, [$field, $value, $escape]);
        }

        return $this;
    }

    /**
     * Or having in clause
     *
     * @param   array|string $field
     */
    public function or_having_in($field = [], $value = '', bool $escape = true)
    {
        if (is_array($field)) {
            foreach ($field as $key => $val) {
                $this->_prepare(__FUNCTION__, [$key, $val, $escape]);
            }
        } else {
            $this->_prepare(__FUNCTION__, [$field, $value, $escape]);
        }

        return $this;
    }

    /**
     * Having not in clause
     *
     * @param   array|string $field
     */
    public function having_not_in($field = [], $value = '', bool $escape = true)
    {
        if (is_array($field)) {
            foreach ($field as $key => $val) {
                $this->_prepare(__FUNCTION__, [$key, $val, $escape]);
            }
        } else {
            $this->_prepare(__FUNCTION__, [$field, $value, $escape]);
        }

        return $this;
    }

    /**
     * Or having not in clause
     *
     * @param   array|string $field
     */
    public function or_having_not_in($field = [], $value = '', bool $escape = true)
    {
        if (is_array($field)) {
            foreach ($field as $key => $val) {
                $this->_prepare(__FUNCTION__, [$key, $val, $escape]);
            }
        } else {
            $this->_prepare(__FUNCTION__, [$field, $value, $escape]);
        }

        return $this;
    }

    /**
     * Having like clause
     *
     * @param   array|string $field
     */
    public function having_like($field = [], $match = '', string $side = 'both', bool $escape = true, bool $case_insensitive = false)
    {
        if (is_array($field)) {
            foreach ($field as $key => $val) {
                $this->_prepare(__FUNCTION__, [$key, $val, $escape]);
            }
        } else {
            $this->_prepare(__FUNCTION__, [$field, $match, $side, $escape, $case_insensitive]);
        }

        return $this;
    }

    /**
     * Or having like clause
     *
     * @param   array|string $field
     */
    public function or_having_like($field = [], $match = '', string $side = 'both', bool $escape = true, bool $case_insensitive = false)
    {
        if (is_array($field)) {
            foreach ($field as $key => $val) {
                $this->_prepare(__FUNCTION__, [$key, $val, $escape]);
            }
        } else {
            $this->_prepare(__FUNCTION__, [$field, $match, $side, $escape, $case_insensitive]);
        }

        return $this;
    }

    /**
     * Not having like clause
     *
     * @param   array|string $field
     */
    public function not_having_like($field = [], $match = '', string $side = 'both', bool $escape = true, bool $case_insensitive = false)
    {
        if (is_array($field)) {
            foreach ($field as $key => $val) {
                $this->_prepare(__FUNCTION__, [$key, $val, $escape]);
            }
        } else {
            $this->_prepare(__FUNCTION__, [$field, $match, $side, $escape, $case_insensitive]);
        }

        return $this;
    }

    /**
     * Or not having like clause
     *
     * @param   array|string $field
     */
    public function or_not_having_like($field = [], $match = '', string $side = 'both', bool $escape = true, bool $case_insensitive = false)
    {
        if (is_array($field)) {
            foreach ($field as $key => $val) {
                $this->_prepare(__FUNCTION__, [$key, $val, $escape]);
            }
        } else {
            $this->_prepare(__FUNCTION__, [$field, $match, $side, $escape, $case_insensitive]);
        }

        return $this;
    }

    /**
     * Ordering result query
     *
     * @param   array|string $field
     */
    public function order_by($field = [], string $direction = '', bool $escape = true)
    {
        if (! service('request')->getGet('order')) {
            if (is_array($field)) {
                foreach ($field as $key => $val) {
                    $this->_prepare(__FUNCTION__, [$key, $val, $escape]);
                }
            } else {
                $this->_prepare(__FUNCTION__, [$field, $direction, $escape]);
            }
        }

        return $this;
    }

    /**
     * Group query result
     */
    public function group_by(string $column)
    {
        $this->_prepare(__FUNCTION__, [$column]);

        return $this;
    }

    /**
     * Limit the query result
     */
    public function limit(int $limit = null, int $offset = 0)
    {
        if (in_array($this->_method, ['create', 'read', 'update', 'delete'])) {
            $this->_limit = 1;
            $this->_offset = 0;
        } else {
            $this->_limit = $limit;
            $this->_offset = $offset;
        }

        $this->_prepare(__FUNCTION__, [$limit, $offset]);

        return $this;
    }

    /**
     * Row offset
     */
    public function offset(int $offset)
    {
        if (! in_array($this->_method, ['create', 'read', 'update', 'delete'])) {
            $this->_offset = $offset;
        }

        $this->_prepare(__FUNCTION__, [$offset]);

        return $this;
    }

    /**
     * Running a subquery
     *
     * @param   string|mixed $subquery
     */
    public function subquery($subquery = null, string $alias = null)
    {
        if (! in_array($this->_method, ['create', 'update', 'delete'])) {
            $this->_prepare(__FUNCTION__, [$subquery, $alias]);
        }

        return $this;
    }

    /**
     * Starts a new group by adding an opening parenthesis to the WHERE clause
     * of the query.
     */
    public function group_start()
    {
        if (! in_array($this->_method, ['create', 'update', 'delete'])) {
            $this->_prepare(__FUNCTION__);
        }

        return $this;
    }

    /**
     * Starts a new group by adding an opening parenthesis to the WHERE clause
     * of the query, prefixing it with OR.
     */
    public function or_group_start()
    {
        if (! in_array($this->_method, ['create', 'update', 'delete'])) {
            $this->_prepare(__FUNCTION__);
        }

        return $this;
    }

    /**
     * Starts a new group by adding an opening parenthesis to the WHERE clause
     * of the query, prefixing it with NOT.
     */
    public function not_group_start()
    {
        if (! in_array($this->_method, ['create', 'update', 'delete'])) {
            $this->_prepare(__FUNCTION__);
        }

        return $this;
    }

    /**
     * Starts a new group by adding an opening parenthesis to the WHERE clause
     * of the query, prefixing it with OR NOT.
     */
    public function or_not_group_start()
    {
        if (! in_array($this->_method, ['create', 'update', 'delete'])) {
            $this->_prepare(__FUNCTION__);
        }

        return $this;
    }

    /**
     * Ends the current group by adding a closing parenthesis to the WHERE
     * clause of the query.
     */
    public function group_end()
    {
        if (! in_array($this->_method, ['create', 'update', 'delete'])) {
            $this->_prepare(__FUNCTION__);
        }

        return $this;
    }

    /**
     * Starts a new group by adding an opening parenthesis to the HAVING clause
     * of the query.
     */
    public function having_group_start()
    {
        if (! in_array($this->_method, ['create', 'update', 'delete'])) {
            $this->_prepare(__FUNCTION__);
        }

        return $this;
    }

    /**
     * Starts a new group by adding an opening parenthesis to the HAVING clause
     * of the query, prefixing it with OR.
     */
    public function or_having_group_start()
    {
        if (! in_array($this->_method, ['create', 'update', 'delete'])) {
            $this->_prepare(__FUNCTION__);
        }

        return $this;
    }

    /**
     * Starts a new group by adding an opening parenthesis to the HAVING clause
     * of the query, prefixing it with NOT.
     */
    public function not_having_group_start()
    {
        if (! in_array($this->_method, ['create', 'update', 'delete'])) {
            $this->_prepare(__FUNCTION__);
        }

        return $this;
    }

    /**
     * Starts a new group by adding an opening parenthesis to the HAVING clause
     * of the query, prefixing it with OR NOT.
     */
    public function or_not_having_group_start()
    {
        if (! in_array($this->_method, ['create', 'update', 'delete'])) {
            $this->_prepare(__FUNCTION__);
        }

        return $this;
    }

    /**
     * Ends the current group by adding a closing parenthesis to the HAVING
     * clause of the query.
     */
    public function having_group_end()
    {
        if (! in_array($this->_method, ['create', 'update', 'delete'])) {
            $this->_prepare(__FUNCTION__);
        }

        return $this;
    }

    /**
     * Running the query of preparated builder parameters
     *
     * @param   string $table
     */
    private function _run_query($table = null, bool $recycling = false)
    {
        // Use the table
        $query = $this->model->table($table);

        // Add distinct
        if ($this->_distinct) {
            $query = $this->model->distinct();
        }

        // Check if the request is not recycling the previous properties
        if (! $recycling) {
            // Prepare indexing the columns of table to be selected
            $select = preg_filter('/^/', $table . '.', $this->model->list_fields($table));
            $columns = $this->model->field_data($table);

            if ($columns) {
                foreach ($columns as $key => $val) {
                    if (in_array($this->_method, ['create', 'update']) && in_array($val->name, $this->_unset_field)) {
                        if (! isset($val->primary_key) || empty($val->primary_key)) {
                            unset($select[$val->name]);
                        }
                    } elseif (in_array($this->_method, ['read']) && in_array($val->name, $this->_unset_view)) {
                        if (! isset($val->primary_key) || empty($val->primary_key)) {
                            unset($select[$val->name]);
                        }
                    } elseif (in_array($val->name, $this->_unset_column)) {
                        if (! isset($val->primary_key) || empty($val->primary_key)) {
                            unset($select[$val->name]);
                        }
                    }
                }
            }

            // Merge selection
            if (! in_array($this->_method, ['create', 'update'])) {
                $select = ($this->_select ? array_merge($select, $this->_select) : $select);
            }

            // Execute when method is not delete
            if (! in_array($this->_method, ['delete']) && is_array($select) && sizeof($select) > 0) {
                // Validate the select column to check if column is exist in table
                $compiled_select = [];

                foreach ($select as $key => $val) {
                    // Check if field is already selected
                    $val = trim(preg_replace('/\s\s+/', ' ', $val));
                    $alias = (strrpos($val, ' ') !== false ? substr($val, strrpos($val, ' ') + 1) : (strpos($val, '.') !== false ? explode('.', $val) : ['anonymous', $val]));
                    $alias = (is_array($alias) && isset($alias[1]) ? $alias[1] : $alias);

                    // Check if selected column is use alias
                    if (strpos($val, '.*') !== false && strstr($val, '.*', true) == $table) {
                        continue;
                    } else {
                        // Individual table
                        list($backup_table, $field) = array_pad(explode('.', $val), 2, null);

                        if (! $field) {
                            $field = $backup_table;
                        }

                        // Get the name alias
                        $field = trim(($field && stripos($field, ' AS ') !== false ? substr($field, strripos($field, ' AS ') + 4) : $field));

                        if ($field && stripos($field, ' ') !== false) {
                            $field = substr($field, 0, strrpos($field, ' '));
                        }

                        if ($backup_table != $table && $field && $this->model->field_exists($field, $backup_table)) {
                            // Format column of select
                            $val = $backup_table . '.' . $field . ' AS ' . $field;
                        }
                    }

                    // Compile the selected field
                    $compiled_select[] = $val;
                }

                // Check if select compiled
                if ($compiled_select) {
                    // Ready for unique selection
                    foreach ($this->_prepare as $key => $val) {
                        if ('select' == $val['function']) {
                            // Unset previous prepared select
                            unset($this->_prepare[$key]);
                        }
                    }

                    // Push compiled select to prepared query builder
                    $this->_prepare[] = [
                        'function' => 'select',
                        'arguments' => [array_values($compiled_select)]
                    ];
                }

                // Generate join query passed from set_relation
                if (is_array($this->_join) && sizeof($this->_join) > 0) {
                    foreach ($this->_join as $table => $params) {
                        // Push join to prepared query builder
                        $this->_prepare[] = [
                            'function' => 'join',
                            'arguments' => [$table, str_replace('__PRIMARY_TABLE__', $this->_table, $params['condition']), $params['type'], $params['escape']]
                        ];
                    }
                }
            }
        }

        // Format compiled select
        if ($this->_compiled_select) {
            foreach ($this->_compiled_select as $key => $val) {
                // Check if column should be unset
                if (in_array($val, $this->_unset_select)) {
                    // Unset selected compiled select
                    unset($this->_compiled_select[$key]);
                }
            }
        }

        // Run generated query builder
        foreach ($this->_prepare as $key => $val) {
            $function = $val['function'];
            $arguments = $val['arguments'];

            if ('select' == $function) {
                // Splice unnecessary select
                if (! is_array($arguments[0])) {
                    // Explode comma sparated string to array
                    $arguments[0] = array_map('trim', explode(',', $arguments[0]));
                }

                // Prevent duplicate entries
                $arguments[0] = array_unique($arguments[0]);

                // Looping the argument
                foreach ($arguments[0] as $_key => $_val) {
                    $column = $_val;
                    $alias = null;

                    // Check whether generated selected columns need to unset
                    if (in_array($column, $this->_unset_select)) {
                        // Unset unselected columns
                        unset($arguments[0][$_key]);

                        continue;
                    }

                    // Find bracket wrapper or continue on void
                    if (strpos($_val, '(') === false && strpos($_val, ')') === false) {
                        // Now find dotted table and column pairs
                        if (stripos($column, '.') !== false) {
                            // Extract column
                            $column = substr($column, stripos($column, '.') + 1);
                        }

                        // Now find if column is aliased
                        if (stripos(trim($column), ' AS ') !== false) {
                            // Find alias
                            $alias = substr($column, stripos($column, ' AS ') + 4);

                            // Assign to column
                            $column = substr($column, 0, strpos($_val, ' AS '));
                        }

                        // Store selection keys
                        $compiled_select_key_1 = array_search($column, $this->_compiled_select);
                        $compiled_select_key_2 = array_search($alias, $this->_compiled_select);

                        // Unset matched compiled select
                        unset($this->_compiled_select[$compiled_select_key_1]);
                        unset($this->_compiled_select[$compiled_select_key_2]);

                        // Extract source table
                        $source_table = substr($_val . '.', 0, strpos($_val, '.'));

                        // Check whether table or columns has compiled
                        if (! in_array($source_table, $this->_compiled_table) && ! $alias) {
                            // Field doesn't exists on compiled table
                            unset($arguments[0][$_key]);
                        }
                    }
                }

                // Make the selection column unique
                $arguments[0] = array_unique(array_merge($this->_compiled_select, $arguments[0]));
            } elseif ('where' == $function) {
                // Extract source table from selection
                $source_table = (isset($arguments[0]) ? $arguments[0] : '');
                $source_table = substr($source_table . '.', 0, strpos($source_table, '.'));

                if ($source_table && ! in_array($source_table, $this->_compiled_table)) {
                    // Source table not in compilation
                    continue;
                }

                if (strpos($arguments[0], '.') === false && strpos($arguments[0], ' ') === false && strpos($arguments[0], '(') === false && strpos($arguments[0], ')') === false) {
                    // Add table prefix to field
                    $arguments[0] = $this->_table . '.' . $arguments[0];
                }
            } elseif ('order_by' == $function && in_array($this->_method, ['create', 'read', 'update', 'delete'])) {
                // Prevent order on CRUD
                continue;
            }

            if (is_array($arguments) && sizeof($arguments) == 7) {
                // Run model method with 7 parameters
                $query = $this->model->$function($arguments[0], $arguments[1], $arguments[2], $arguments[3], $arguments[4], $arguments[5], $arguments[6]);
            } elseif (is_array($arguments) && sizeof($arguments) == 6) {
                // Run model method with 6 parameters
                $query = $this->model->$function($arguments[0], $arguments[1], $arguments[2], $arguments[3], $arguments[4], $arguments[5]);
            } elseif (is_array($arguments) && sizeof($arguments) == 5) {
                // Run model method with 5 parameters
                $query = $this->model->$function($arguments[0], $arguments[1], $arguments[2], $arguments[3], $arguments[4]);
            } elseif (is_array($arguments) && sizeof($arguments) == 4) {
                // Run model method with 4 parameters
                $query = $this->model->$function($arguments[0], $arguments[1], $arguments[2], $arguments[3]);
            } elseif (is_array($arguments) && sizeof($arguments) == 3) {
                // Run model method with 3 parameters
                $query = $this->model->$function($arguments[0], $arguments[1], $arguments[2]);
            } elseif (is_array($arguments) && sizeof($arguments) == 2) {
                // Run model method with 2 parameters
                $query = $this->model->$function($arguments[0], $arguments[1]);
            } else {
                // Run model method with single parameter
                $query = $this->model->$function((isset($arguments[0]) ? $arguments[0] : $arguments));
            }
        }

        return $query;
    }

    /**
     * Fetch the data by running the preparated query builder parameters
     *
     * @param   null|mixed $table
     */
    private function _fetch($table = null)
    {
        // Debugger
        if ($this->_debugging) {
            // Run query
            $query = $this->_run_query($table)->limit($this->_limit, $this->_offset)->result();

            if ('query' == $this->_debugging) {
                // Print debugger
                exit(nl2br($this->model->last_query()));
            } else {
                if (ENVIRONMENT === 'production') {
                    // Print debugger
                    exit('<pre>' . print_r($query, true) . '</pre>');
                }

                // Print debugger
                dd($query);
            }
        }

        // Set output format
        $output = [
            // Run query
            'results' => $this->_run_query($table)->limit($this->_limit, $this->_offset)->result(),

            // Run query with compiled parameter
            'total' => $this->_run_query($table, true)->count_all_results()
        ];

        // Reset preparation property
        $this->_prepare = [];

        return $output;
    }

    /**
     * Getting the relation table data of relation type field
     *
     * @param   int|string $selected
     */
    private function _get_relation(array $params = [], $selected = 0, bool $ajax = false)
    {
        // Check if selection isn't selected and use default value if any
        if (! $selected && (isset($this->_default_value[$params['primary_key']]))) {
            $selected = $this->_default_value[$params['primary_key']];
        }

        // Set default variables
        $compiled_select = [];
        $like = [];

        // Get the primary key from the end of the array
        $primary_key = (is_array($params['primary_key']) ? end($params['primary_key']) : $params['primary_key']);

        foreach ($params['select'] as $key => $val) {
            $checker = explode('.', $val);
            $column = (isset($checker[1]) ? $checker[1] : $val);
            $table = (isset($checker[0]) ? $checker[0] : null);

            // Check if column is in compiled selection
            if (in_array($column, $compiled_select)) {
                // Check if table variable isn't same with table property
                if ($table != $this->_table) {
                    // Add alias into field
                    $val = $val . ' AS ' . $column . '_' . $table;
                }
            }

            // Start select query
            $this->model->select($val);

            $compiled_select[] = $column;

            // Check payload
            if (service('request')->getPost('search')) {
                if ($val && stripos($val, ' AS ') !== false) {
                    $val = substr($val, 0, stripos($val, ' AS '));
                }

                // Add keyword into like clause
                $like[$val] = service('request')->getPost('search');
            }
        }

        // Check if has like parameter and has no limit
        if ($like && ! $selected) {
            $num = 0;

            // Start group query
            $this->model->group_start();

            foreach ($like as $key => $val) {
                if ($num) {
                    // Like exist, use or
                    $this->model->or_like($key, $val, 'both', true, true);
                } else {
                    // Start like clause
                    $this->model->like($key, $val, 'both', true, true);
                }

                $num++;
            }

            // Ends group query
            $this->model->group_end();
        }

        // Check if params has join
        if ($params['join']) {
            foreach ($params['join'] as $key => $val) {
                // Check if index is less than 3
                if (! isset($val[0]) || ! isset($val[1])) {
                    continue;
                }

                // Add table to join query
                $this->model->join($val[0], $val[1], (isset($val[2]) ? $val[2] : ''));
            }
        }

        // Check if has selected value
        if ($selected) {
            $relation_key = $params['relation_table'] . '.' . $params['relation_key'];
            $params['where'][$relation_key] = $selected;
            $params['limit'] = 1;
        }

        // Check if params has where claues
        if ($params['where']) {
            foreach ($params['where'] as $key => $val) {
                // Check if field has dot (merged its table)
                $field_origin = (strpos($key, '.') !== false ? substr($key, strpos($key, '.') + 1) : $key);

                if (is_numeric($field_origin) && $val && stripos(trim($val), ' NOT IN') !== false) {
                    // No NOT IN in field origin
                    $this->model->where($val, null, false);
                } elseif (is_numeric($field_origin) && $val && stripos(trim($val), ' IN') !== false) {
                    // No IN in field origin
                    $this->model->where($val, null, false);
                } elseif (stripos(trim($key), ' NOT IN') !== false) {
                    // No NOT IN in field
                    $this->model->where($key, $val, false);
                } elseif (stripos(trim($key), ' IN') !== false) {
                    // No IN in field
                    $this->model->where($key, $val, false);
                } else {
                    // Otherwise, check if field has space or aliases
                    if (stripos(trim($key), ' ') !== false) {
                        //$key                        = substr(trim($key), strpos(trim($key), ' ') + 1);
                    }

                    // Add where clause
                    $this->model->where($key, $val);
                }
            }
        }

        // Check if method in not in array
        if (! in_array($this->_method, ['create', 'update'])) {
            if (is_array($params['relation_key'])) {
                // If relation key is an array
                $selected = explode('.', $selected);

                foreach ($params['relation_key'] as $key => $val) {
                    if (! isset($selected[$key])) {
                        // No field in selection, continue
                        continue;
                    }

                    $this->model->where((strpos($params['relation_table'], ' ') !== false ? substr($params['relation_table'], strpos($params['relation_table'], ' ') + 1) : $params['relation_table']) . '.' . $val, $selected[$key]);
                }
            } else {
                $this->model->where((strpos($params['relation_table'], ' ') !== false ? substr($params['relation_table'], strpos($params['relation_table'], ' ') + 1) : $params['relation_table']) . '.' . $params['relation_key'], $selected);
            }
        }

        // Check if parameter has order by and has no selected value
        if ($params['order_by'] && ! $selected) {
            if (is_array($params['order_by'])) {
                // Check if order by parameter is an array
                foreach ($params['order_by'] as $key => $val) {
                    $this->model->order_by($key, $val);
                }
            } else {
                $this->model->order_by($params['order_by']);
            }
        }

        // Check if parameter has join and group by, but has no selected value
        if ($params['join'] && $params['group_by'] && ! $selected) {
            $this->model->group_by($params['group_by']);
        }

        $output = [];

        // Run relation query
        $query = $this->model->get($params['relation_table'], $params['limit'], $params['offset'])->result();

        if ($query) {
            foreach ($query as $key => $val) {
                $label = $params['output'];
                $attributes = (isset($this->_set_attribute[$primary_key]) ? $this->_set_attribute[$primary_key] : '');
                $option_label = (isset($this->_set_option_label[$primary_key]) ? $this->_set_option_label[$primary_key] : '');

                foreach ($params['select'] as $magic => $replace) {
                    $replace = trim($replace);
                    $replacement = $replace;

                    if (strpos($replace, ' AS ') !== false) {
                        $replacement = substr($replace, strripos($replace, ' AS ') + 4);
                    } elseif (strpos($replace, '.') !== false) {
                        $replacement = substr($replace, strripos($replace, '.') + 1);
                    }

                    if (isset($val->$replacement)) {
                        if (isset($this->_set_field[$replacement]['field_type']) && in_array('sprintf', $this->_set_field[$replacement]['field_type'])) {
                            $val->$replacement = sprintf('%02d', $val->$replacement);
                        }

                        $label = preg_replace("/\{\{(\s+)?($replace)(\s+)?\}\}/", $val->$replacement, $label);
                        $attributes = preg_replace("/\{\{(\s+)?($replace)(\s+)?\}\}/", $val->$replacement, $attributes);
                        $option_label = preg_replace("/\{\{(\s+)?($replace)(\s+)?\}\}/", $val->$replacement, $option_label);
                    }
                }

                if (in_array($this->_method, ['create', 'update'])) {
                    if (is_array($params['primary_key'])) {
                        $value = null;
                        $selected = null;

                        foreach ($params['primary_key'] as $_key => $_val) {
                            $value .= ($value ? '.' : null) . (isset($val->$_val) ? $val->$_val : 0);

                            if (service('request')->getGet($_val)) {
                                $selected .= ($selected ? '.' : null) . service('request')->getGet($_val);
                            }
                        }
                    } else {
                        $primary_key = $params['primary_key'];
                        $relation_key = $params['relation_key'];
                        $value = (isset($val->$primary_key) ? $val->$primary_key : (isset($val->$relation_key) ? $val->$relation_key : 0));
                    }

                    if ($ajax) {
                        $output[] = [
                            'id' => $value,
                            'text' => ($params['translate'] ? phrase($label) : $label)
                        ];
                    } else {
                        $output[] = [
                            'value' => $value,
                            'label' => ($params['translate'] ? phrase($label) : $label),
                            'selected' => ($value == $selected)
                        ];
                    }
                } else {
                    $output = ($params['translate'] ? phrase($label) : $label);

                    return $output;
                }
            }
        }

        if ($ajax) {
            return make_json([
                'results' => $output,
                'pagination' => [
                    'more' => ($output && sizeof($output) >= $params['limit'] ? true : false)
                ]
            ]);
        }

        return $output;
    }

    /**
     * Unlink the uploaded data
     *
     * @param   mixed|array $files
     * @param   array $field_list
     */
    private function _unlink_files($files = [], string $field_name = null, $field_list = [])
    {
        if (! is_array($files)) {
            // Ensure the variable is an array
            return false;
        }

        foreach ($files as $field => $src) {
            // Check if source file is JSON
            if (is_json($src)) {
                // Source is json, decode as array
                $src = json_decode($src, true);
            }

            // Rename field for next condition
            if (! $field_name) {
                // The suffix indicates that the file is not deleted
                $field_name = $field . '_label';
            }

            if (is_array($src)) {
                // Apply when source file is an array
                if (! $field_list) {
                    // Field list is empty
                    $field_list[$field_name] = (is_array($src) ? $src : [$src]);
                } else {
                    // Merge field list
                    $field_list[$field_name] = array_merge($field_list[$field_name], (is_array($src) ? $src : [$src]));
                }

                // Reinitialize function
                $this->_unlink_files($src, $field_name, $field_list);
            } else {
                // Convert the field list to string
                $input_name = urldecode(http_build_query($field_list));

                // Remove everything after equal sign, include the sign itself
                $input_name = substr($input_name, 0, strpos($input_name, '='));

                // Check whether file is allows to remove
                if ('placeholder.png' == $src || service('request')->getPost($input_name) || (! is_array($field) && isset($_FILES[$field]['tmp_name']) || empty($_FILES[$field]['tmp_name']))) {
                    // Skip unlink placeholder file
                    continue;
                }

                // Sanitize input to prevent hack
                $src = basename($src);
                $field = basename($field);

                if (is_file(UPLOAD_PATH . '/' . $this->_set_upload_path . '/' . $src)) {
                    // Use source file
                    try {
                        // Attempt to unlink source file
                        unlink(UPLOAD_PATH . '/' . $this->_set_upload_path . '/' . $src);
                    } catch(\Throwable $e) {
                        // Safe abstraction
                    }
                } elseif (is_file(UPLOAD_PATH . '/' . $this->_set_upload_path . '/' . $field)) {
                    // Source file not found, check if it's in field name
                    try {
                        // Attempt to unlink source file
                        unlink(UPLOAD_PATH . '/' . $this->_set_upload_path . '/' . $field);
                    } catch(\Throwable $e) {
                        // Safe abstraction
                    }
                }

                if (is_file(UPLOAD_PATH . '/' . $this->_set_upload_path . '/thumbs/' . $src)) {
                    // Use source file
                    try {
                        // Attempt to unlink source file
                        unlink(UPLOAD_PATH . '/' . $this->_set_upload_path . '/thumbs/' . $src);
                    } catch(\Throwable $e) {
                        // Safe abstraction
                    }
                } elseif (is_file(UPLOAD_PATH . '/' . $this->_set_upload_path . '/thumbs/' . $field)) {
                    // Source file not found, check if it's in field name
                    try {
                        // Attempt to unlink source file
                        unlink(UPLOAD_PATH . '/' . $this->_set_upload_path . '/thumbs/' . $field);
                    } catch(\Throwable $e) {
                        // Safe abstraction
                    }
                }

                if (is_file(UPLOAD_PATH . '/' . $this->_set_upload_path . '/icons/' . $src)) {
                    // Use source file
                    try {
                        // Attempt to unlink source file
                        unlink(UPLOAD_PATH . '/' . $this->_set_upload_path . '/icons/' . $src);
                    } catch(\Throwable $e) {
                        // Safe abstraction
                    }
                } elseif (is_file(UPLOAD_PATH . '/' . $this->_set_upload_path . '/icons/' . $field)) {
                    // Source file not found, check if it's in field name
                    try {
                        // Attempt to unlink source file
                        unlink(UPLOAD_PATH . '/' . $this->_set_upload_path . '/icons/' . $field);
                    } catch(\Throwable $e) {
                        // Safe abstraction
                    }
                }
            }
        }
    }

    /**
     * Do handshake between API client and API endpoint
     *
     * @param   string|int $API_KEY
     */
    private function _handshake($API_KEY = 0)
    {
        // Set client header
        service('request')->setHeader('X-Requested-With', 'XMLHttpRequest');

        // Get client
        $client = $this->model->get_where(
            'app__rest_clients',
            [
                'status' => 1,
                'api_key' => $API_KEY,
                'valid_until >= ' => date('Y-m-d')
            ],
            1
        )
        ->row();

        if (! $client) {
            // Client doesn't exist, check if request is from self app
            if (ENCRYPTION_KEY === $API_KEY) {
                // Add temporary API client
                $client = (object) [
                    'ip_range' => service('request')->getServer('SERVER_ADDR'),
                    'method' => json_encode([service('request')->getServer('REQUEST_METHOD')]),
                    'status' => 1
                ];
            }
        }

        if (! $client) {
            // Request denied
            return throw_exception(403, phrase('Your API Key is not eligible to access the requested module or its already expired'));
        } elseif (! $client->status) {
            // Client status inactive
            return throw_exception(403, phrase('Your API Key is temporary deactivated'));
        } elseif (! in_array(service('request')->getServer('REQUEST_METHOD'), json_decode($client->method, true))) {
            // Client request method limited
            return throw_exception(403, phrase('Your API Key is not eligible to use the method') . ': ' . service('request')->getServer('REQUEST_METHOD'));
        } elseif ($client->ip_range && (($client->ip_range && ! $this->_ip_in_range($client->ip_range)) || service('request')->getIPAddress() != service('request')->getServer('SERVER_ADDR'))) {
            // Client IP blocked
            return throw_exception(403, phrase('Your API Client is not permitted to access the requested source'));
        }

        if (session_status() === PHP_SESSION_NONE) {
            // Start session
            session_start();
        }

        // Get cookie
        $cookie = $this->model->select('data')->get_where(
            'app__sessions',
            [
                'id' => service('request')->getHeaderLine('X-ACCESS-TOKEN') ?? 0,
                'ip_address' => (service('request')->hasHeader('x-forwarded-for') ? service('request')->getHeaderLine('x-forwarded-for') : service('request')->getIPAddress()) ?? 0,
                'timestamp >= ' => date('Y-m-d H:i:s', (time() - config('Session')->expiration))
            ],
            1
        )
        ->row('data');

        if ($cookie && 'Postgre' === $this->_db_driver) {
            // Un-escape bytea from PostgreSQL result
            $cookie = pg_unescape_bytea($cookie);
        }

        if ($cookie && session_decode($cookie)) {
            // Set API token as valid
            $this->_api_token = true;

            // Set the cookie to session
            set_userdata(array_filter($_SESSION));

            // Set the user language session
            $this->_set_language(get_userdata('language_id'));
        }

        if (service('request')->getHeaderLine('X-ACCESS-TOKEN')) {
            // Update expiration time
            $this->model->update(
                'app__sessions',
                [
                    'data' => session_encode(),
                    'timestamp' => date('Y-m-d H:i:s')
                ],
                [
                    'id' => service('request')->getHeaderLine('X-ACCESS-TOKEN')
                ]
            );
        }

        // Update property state
        $this->api_client = true;

        return $this;
    }

    /**
     * Check the IP if it's being blacklisted or not
     *
     * @param   array|string $whitelist
     * @return  bool
     */
    private function _ip_in_range($whitelist = [])
    {
        if ($whitelist && ! is_array($whitelist)) {
            // Explode sparated comma
            $whitelist = array_map('trim', explode(',', $whitelist));
        }

        if (in_array(service('request')->getServer('REMOTE_ADDR'), $whitelist)) {
            // Client IP's in whitelist
            return true;
        } else {
            // Loop whitelist IP
            foreach ($whitelist as $key => $val) {
                // Find wildcard
                $wildcardPos = strpos($val, '*');

                if (false !== $wildcardPos && substr(service('request')->getServer('REMOTE_ADDR'), 0, $wildcardPos) . '*' == $val) {
                    // Client IP's in wildcard
                    return true;
                }
            }
        }

        // Not validated
        return false;
    }

    /**
     * Store the record of visitor to the log table
     */
    private function _push_log()
    {
        if (service('request')->getUserAgent()->isBrowser()) {
            // Browser
            $user_agent = service('request')->getUserAgent()->getBrowser() . ' ' . service('request')->getUserAgent()->getVersion();
        } elseif (service('request')->getUserAgent()->isRobot()) {
            // Robot
            $user_agent = service('request')->getUserAgent()->getRobot();
        } elseif (service('request')->getUserAgent()->isMobile()) {
            // Mobile
            $user_agent = service('request')->getUserAgent()->getMobile();
        } else {
            // Not listed
            return false;
        }

        $prepare = [
            'ip_address' => (service('request')->hasHeader('x-forwarded-for') ? service('request')->getHeaderLine('x-forwarded-for') : service('request')->getIPAddress()),
            'browser' => $user_agent,
            'platform' => service('request')->getUserAgent()->getPlatform(),
            'timestamp' => date('Y-m-d H:i:s')
        ];

        if (in_array($this->_db_driver, ['Postgre', 'SQLSRV'])) {
            // Cast column
            $this->model->where('CAST(timestamp AS DATE)', date('Y-m-d'));
        } else {
            // Cast column
            $this->model->where('DATE(timestamp)', date('Y-m-d'));
        }

        // Get visitor log by IP address
        $query = $this->model->get_where(
            'app__log_visitors',
            [
                'ip_address' => $prepare['ip_address']
            ],
            1
        )
        ->row();

        if (! $query) {
            try {
                // Insert log
                if (! $this->model->insert('app__log_visitors', $prepare)) {
                    try {
                        // Trap suspicious access
                        file_put_contents(WRITEPATH . 'logs/log-' . date('Y-m-d') . '.txt', current_page() . PHP_EOL . json_encode($prepare) . PHP_EOL, FILE_APPEND | LOCK_EX);
                    } catch(\Throwable $e) {
                        // Safe abstraction
                    }
                }
            } catch(\Throwable $e) {
                // Safe abstraction;
            }
        }
    }

    /**
     * Set the language based on user browser if matched with available
     * translation within the app. Otherwise, use the user session or fallback
     * to the system default language
     */
    private function _set_language(string $language_id = null)
    {
        if (! get_userdata('language_id') || ! $language_id) {
            // Session has no language_id, get locale
            $locale = explode(',', (service('request')->getServer('HTTP_ACCEPT_LANGUAGE') ? service('request')->getServer('HTTP_ACCEPT_LANGUAGE') : 'en-us'));

            foreach ($locale as $key => $val) {
                if ($key) {
                    $this->model->or_like('locale', $val, 'both', true, true);
                } else {
                    $this->model->like('locale', $val, 'both', true, true);
                }
            }

            // Get language id
            $language_id = $this->model->select('
                id
            ')
            ->get_where(
                'app__languages',
                [
                    'status' => 1
                ],
                1
            )
            ->row('id');

            // Apply app language if no language found
            $language_id = ($language_id ? $language_id : (get_setting('app_language') > 0 ? get_setting('app_language') : 1));

            // Set language id to user session
            set_userdata('language_id', $language_id);
        }

        // Get language code
        $language_code = $this->model->select('
            code
        ')
        ->get_where(
            'app__languages',
            [
                'id' => $language_id
            ],
            1
        )
        ->row('code');

        // Set language code to property
        $this->_language = $language_code;

        // Check whether language file is exists
        if (is_dir(APPPATH . 'Language' . DIRECTORY_SEPARATOR . $language_code)) {
            // Set language to session
            set_userdata('language', $language_code);

            // Set locale to service language
            service('language')->setLocale($language_code);
        }
    }

    /**
     * Prepare the given parameter as the query builder queue
     */
    private function _prepare(string $function, array $arguments = [])
    {
        if ('where' == $function) {
            // Apply for where
            $this->_where[$arguments[0]] = $arguments[1];
        }

        // Apply to property
        $this->_prepare[] = [
            'function' => $function,
            'arguments' => $arguments
        ];
    }
}
