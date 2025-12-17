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

use Config\Services;
use CodeIgniter\Controller;
use Aksara\Laboratory\Traits;
use Aksara\Laboratory\Model;
use Aksara\Laboratory\Permission;
use Aksara\Laboratory\Template;
use Aksara\Laboratory\Renderer\Renderer;
use Aksara\Libraries\Document;
use Throwable;

/**
 * Core Controller for Aksara CMS.
 */
abstract class Core extends Controller
{
    use Traits;

    /**
     * Flag indicating if the request originated from an API Client.
     */
    protected bool $api_client = false;

    /**
     * Form validation service instance.
     * @var \CodeIgniter\Validation\ValidationInterface
     */
    protected $form_validation;

    /**
     * Model instance for database interaction.
     * @var \Aksara\Laboratory\Model
     */
    protected $model;

    /**
     * Permission object for access control logic.
     * @var \Aksara\Laboratory\Permission
     */
    protected $permission;

    /**
     * Request service instance.
     * @var IncomingRequest|null
     */
    protected $request;

    /**
     * Theme template properties.
     * @var object
     */
    public $template;

    /**
     * CSRF Token storage.
     */
    private ?string $_token = null;

    /**
     * Flag indicating if the submitted API token is valid.
     */
    private bool $_api_token = false;

    /**
     * Controller constructor, initializes dependencies and validates request integrity.
     *
     * @return void
     */
    public function __construct()
    {
        // Start benchmarking timer.
        Services::timer()->start('elapsed_time');

        // Load request class
        $this->request = Services::request();
        $this->response = Services::response();

        // Clear previous upload session data.
        unset_userdata('_set_upload_path');
        unset_userdata('_uploaded_files');

        // --- User Agent and IP Validation ---
        $userAgent = $this->request->getUserAgent();

        // Block outdated IE versions.
        if (strtolower($userAgent->getBrowser()) == 'internet explorer' && $userAgent->getVersion() < 11) {
            die('The ' . $userAgent->getBrowser() . ' ' . $userAgent->getVersion() . ' is no longer supported...');
        }

        // Validate client IP address.
        $ipAddress = $this->request->hasHeader('x-forwarded-for') ? $this->request->getHeaderLine('x-forwarded-for') : $this->request->getIPAddress();
        if (! filter_var($ipAddress, FILTER_VALIDATE_IP)) {
            exit(header('Location: https://google.com?q=' . $ipAddress));
        }

        // --- Load Dependencies ---
        helper(['url', 'file', 'theme', 'security', 'main', 'string', 'widget']);

        // Load core classes.
        $this->form_validation = Services::validation();
        $this->model = new Model();
        $this->permission = new Permission();

        // Assign active database driver.
        $this->_db_driver = $this->model->db_driver();

        // --- Route Initialization ---
        $router = Services::router();
        $path = ($router->getMatchedRoute()[0] ?? null);
        $this->_method = $router->methodName();

        // Assign the module path.
        $this->_module = ($this->_method && $path && strpos($path, $this->_method) !== false ? preg_replace('/\/' . $this->_method . '$/', '', $path) : $path);

        // Determine and set upload path.
        $controllerName = strtolower(substr(strstr($router->controllerName(), '\Controllers\\'), strlen('\Controllers\\')));
        $upload_path = array_pad(explode('\\', $controllerName), 2, null);
        $this->_set_upload_path = $upload_path[1] ?? $upload_path[0];

        // --- Query Parameter Handling ---

        // Apply URL limit parameter.
        if (is_numeric($this->request->getGet('limit')) && $this->request->getGet('limit')) {
            $this->_limit_backup = $this->_limit;
            $this->_limit = $this->request->getGet('limit');
        }

        // Apply URL offset parameter.
        if (is_numeric($this->request->getGet('offset')) && $this->request->getGet('offset')) {
            $this->_offset = $this->request->getGet('offset');
        }

        // --- Theme Preview Mode ---

        // Check for theme preview mode.
        if ('preview-theme' == $this->request->getGet('aksara_mode') && sha1($this->request->getGet('aksara_theme') . ENCRYPTION_KEY . get_userdata('session_generated')) == $this->request->getGet('integrity_check') && is_dir(ROOTPATH . 'themes/' . $this->request->getGet('aksara_theme'))) {
            $this->_set_theme = strip_tags($this->request->getGet('aksara_theme'));
        }

        // --- API Handshake & Logging ---
        if ($this->request->getHeaderLine('X-API-KEY')) {
            // Perform API handshake.
            $this->_handshake($this->request->getHeaderLine('X-API-KEY'));
        } else {
            // Store access logs.
            $this->_push_log();
        }

        // Set user language.
        $this->_set_language(get_userdata('language_id'));
    }

    /**
     * Remaps method based on URI, falling back to index().
     *
     * @param string $method Requested method name.
     * @param mixed  ...$params Remaining URI segments.
     *
     * @return mixed Execution result of the mapped method.
     */
    public function _remap(string $method = '', mixed ...$params)
    {
        // Check method to prevent conflict
        if (method_exists($this, $method) && ! in_array($method, get_class_methods('\Aksara\Laboratory\Core'))) {
            // Call non conflict method
            call_user_func_array([$this, $method], $params);
        } else {
            // Fallback to CRUD method
            call_user_func_array([$this, 'index'], array_merge([$method], $params));
        }
    }

    /**
     * Enables debugging mode and sets output format.
     *
     * @param string|null $result_type Output format ('query', 'parameter', etc.).
     *
     * @return static Current object instance (chainable).
     */
    public function debug(?string $result_type = null): static
    {
        $this->_debugging = $result_type;

        return $this;
    }

    /**
     * Applies restriction flag if DEMO_MODE is active.
     *
     * @return static Current object instance (chainable).
     */
    public function restrict_on_demo(): static
    {
        if (DEMO_MODE) {
            $this->_restrict_on_demo = true;
        }

        return $this;
    }

    /**
     * Configures the database connection.
     *
     * @param array<string, mixed>|string $driver Database driver or full config array.
     * @param string|null                 $hostname Hostname.
     * @param int|null                    $port Port number.
     * @param string|null                 $username Username.
     * @param string|null                 $password Password.
     * @param string|null                 $database Database name.
     *
     * @return static Current object instance (chainable).
     */
    public function database_config(array|string $driver = [], ?string $hostname = null, ?int $port = null, ?string $username = null, ?string $password = null, ?string $database = null): static
    {
        // Use array configuration if provided.
        if (is_array($driver) && isset($driver['driver'], $driver['hostname'], $driver['port'], $driver['username'], $driver['password'], $driver['database'])) {
            $this->model->database_config($driver['driver'], $driver['hostname'], $driver['port'], $driver['username'], $driver['password'], $driver['database']);
        } else {
            // Use individual parameters.
            $this->model->database_config($driver, $hostname, $port, $username, $password, $database);
        }

        return $this;
    }

    /**
     * Validates the submitted security token (CSRF or API Key).
     *
     * @param string|null $token Submitted token string.
     *
     * @return bool TRUE if token is valid or request is from API client.
     */
    public function valid_token(?string $token = null): bool
    {
        $is_post_request = Services::request()->getPost();

        // Must be a POST request.
        if ($is_post_request) {
            // Check URI-based token match.
            if ($token && get_userdata(sha1(uri_string())) === $token) {
                return true;
            }

            // Check Referer-based token match.
            if ($token && sha1(Services::request()->getHeaderLine('Referer') . ENCRYPTION_KEY . get_userdata('session_generated')) === $token) {
                return true;
            }

            // Check API client status (bypasses token check).
            if ($this->api_client) {
                return true;
            }
        }

        return false;
    }

    /**
     * Set query string parameters to ignore during URL generation.
     *
     * These parameters will be excluded when building URLs to ensure
     * consistent URL structure across requests.
     *
     * @param array|string $keys Query parameter keys to ignore (comma-separated if string)
     * @return static Current object instance (chainable).
     */
    protected function ignore_query_string(array|string $keys): static
    {
        if (is_array($keys)) {
            $keys = implode(',', $keys);
        }

        // Store ignored query string keys in user session
        set_userdata('__ignored_query_string', $keys);

        return $this;
    }

    /**
     * Assigns the parent module name.
     *
     * @param string $module Parent module name.
     *
     * @return static Current object instance (chainable).
     */
    public function parent_module(string $module): static
    {
        $this->_module = $module;

        return $this;
    }

    /**
     * Sets module access permission and authorization rules.
     *
     * @param array<int>|string $permissive_group Allowed group IDs (array or comma-separated string), 0 allows all.
     * @param string|null       $redirect         Redirect URI on denial (not used if exception is thrown).
     *
     * @return static Current object instance (chainable).
     *
     * @throws \Exception Throws exception on permission denial.
     */
    public function set_permission(array|string $permissive_group = [], ?string $redirect = null): static
    {
        $this->_set_permission = true;

        if (0 === $permissive_group) {
            return $this;
        }

        // Process permissive group string to array.
        if (! empty($permissive_group) && ! is_array($permissive_group)) {
            $permissive_group = array_map('trim', explode(',', $permissive_group));
        }

        // Authorization checks (removed complex conditional logic for brevity, maintaining original flow):
        if (in_array($this->_method, $this->_unset_method)) {
            return throw_exception(403, phrase('The method you requested is not acceptable.'));
        } elseif ($this->_set_permission && ! get_userdata('is_logged') && ! $this->_api_token) {
            return throw_exception(403, phrase('Your session has been expired.'));
        } elseif (! $this->permission->allow($this->_module, $this->_method, get_userdata('user_id'), $redirect) && ! $this->_api_token) {
            return throw_exception(403, phrase('You do not have sufficient privileges to access the requested page.'));
        } elseif ($permissive_group && ! in_array(get_userdata('group_id'), $permissive_group) && ! $this->_api_token) {
            return throw_exception(403, phrase('You do not have sufficient privileges to access the requested page.'));
        }

        return $this;
    }

    /**
     * Manually sets the module's active method.
     *
     * @param string $method Method name (defaults to 'index').
     *
     * @return static Current object instance (chainable).
     */
    public function set_method(string $method = 'index'): static
    {
        $this->_method = $method;
        $this->_set_method = true;

        return $this;
    }

    /**
     * Gets the currently set method name.
     *
     * @return string The method name.
     */
    public function get_method(): string
    {
        return $this->_method;
    }

    /**
     * Prevents specific methods from being accessed.
     *
     * @param array|string $params Method names (array or comma-separated string).
     *
     * @return static Current object instance (chainable).
     */
    public function unset_method(array|string $params = []): static
    {
        if (! is_array($params)) {
            $params = array_map('trim', explode(',', $params));
        }

        $this->_unset_method = array_merge($this->_unset_method, $params);

        return $this;
    }

    /**
     * Sets the theme based on predefined configuration.
     *
     * @param string $theme Theme context ('frontend' or 'backend').
     *
     * @return static|bool Current object instance (chainable) or FALSE on invalid theme.
     */
    public function set_theme(string $theme = 'frontend'): static|bool
    {
        if (! in_array($theme, ['frontend', 'backend'])) {
            return false;
        }

        $site_id = get_setting('id');

        // Get theme config from site settings.
        $query = $this->model->select($theme . '_theme')->get_where(
            'app__settings',
            [
                'id' => $site_id
            ],
            1
        )
        ->row($theme . '_theme');

        $this->_set_theme = $query;

        return $this;
    }

    /**
     * Sets custom template properties.
     *
     * @param array|string $params Key-value array or single parameter name.
     * @param string|null  $value  Value for the single parameter name.
     *
     * @return static Current object instance (chainable).
     */
    public function set_template(array|string $params = [], ?string $value = null): static
    {
        if (! is_array($params)) {
            $params = [
                $params => $value
            ];
        }

        $this->_set_template = array_merge($this->_set_template, $params);

        return $this;
    }

    /**
     * Sets custom breadcrumb items.
     *
     * @param array|string $params Key-value array (URL => Label) or single URL.
     * @param string|null  $value  Label for the single URL.
     *
     * @return static Current object instance (chainable).
     */
    public function set_breadcrumb(array|string $params = [], ?string $value = null): static
    {
        if (! is_array($params)) {
            $params = [
                $params => $value
            ];
        }

        $this->_set_breadcrumb = array_merge($this->_set_breadcrumb, $params);

        return $this;
    }

    /**
     * Sets the primary key field(s) for the current CRUD operation.
     *
     * @param array|string $field Field name(s) (array or comma-separated string).
     *
     * @return static Current object instance (chainable).
     */
    public function set_primary(array|string $field = []): static
    {
        if (! is_array($field)) {
            $field = array_map('trim', explode(',', $field));
        }

        $this->_set_primary = array_merge($this->_set_primary, $field);

        return $this;
    }

    /**
     * Sets the module and document title.
     *
     * @param array|string $params Key-value array or default title string.
     * @param string|null  $fallback Fallback title if magic string fails.
     *
     * @return static Current object instance (chainable).
     */
    public function set_title(array|string $params = [], ?string $fallback = null): static
    {
        if (! is_array($params)) {
            if (! $fallback && strpos($params, '{{') === false && strpos($params, '}}') === false) {
                $fallback = $params;
            }

            $params = [
                'index' => $params
            ];
        }

        $this->_set_title = array_merge($this->_set_title, $params);
        $this->_set_title_fallback = $fallback;

        return $this;
    }

    /**
     * Sets the module and meta description.
     *
     * @param array|string $params Key-value array or default description string.
     * @param string|null  $fallback Fallback description if magic string fails.
     *
     * @return static Current object instance (chainable).
     */
    public function set_description(array|string $params = [], ?string $fallback = null): static
    {
        if (! is_array($params)) {
            if (! $fallback && strpos($params, '{{') === false && strpos($params, '}}') === false) {
                $fallback = $params;
            }

            $params = [
                'index' => $params
            ];
        }

        $this->_set_description = array_merge($this->_set_description, $params);
        $this->_set_description_fallback = $fallback;

        return $this;
    }

    /**
     * Sets the content icon.
     *
     * @param array|string $params Key-value array or default icon string.
     * @param string|null  $fallback Fallback icon if magic string fails.
     *
     * @return static Current object instance (chainable).
     */
    public function set_icon(array|string $params = [], ?string $fallback = null): static
    {
        if (! is_array($params)) {
            if (! $fallback && strpos($params, '{{') === false && strpos($params, '}}') === false) {
                $fallback = $params;
            }

            $params = [
                'index' => $params
            ];
        }

        $this->_set_icon = array_merge($this->_set_icon, $params);
        $this->_set_icon_fallback = $fallback;

        return $this;
    }

    /**
     * Overrides individual exception messages.
     *
     * @param array|string $params Array of messages or single key.
     * @param int          $code   HTTP status code for single key.
     * @param string|null  $messages Message string for single key.
     *
     * @return static Current object instance (chainable).
     */
    public function set_messages(array|string $params = [], int $code = 0, ?string $messages = null): static
    {
        if (! is_array($params)) {
            $params = [
                $params => [
                    'code' => $code,
                    'messages' => $messages
                ]
            ];
        }

        $this->_set_messages = array_merge($this->_set_messages, $params);

        return $this;
    }

    /**
     * Overrides or adds a CRUD button.
     *
     * @param string      $button    Button key name.
     * @param string|null $value     URL for the button.
     * @param string|null $label     Button label.
     * @param string|null $class     CSS class.
     * @param string|null $icon      Icon class.
     * @param array       $parameter URL parameters.
     * @param bool|null   $new_tab   Open link in a new tab.
     *
     * @return static Current object instance (chainable).
     */
    public function set_button(string $button, ?string $value = null, ?string $label = null, ?string $class = null, ?string $icon = null, array $parameter = [], ?bool $new_tab = null): static
    {
        $this->_set_button[$button] = [
            'url' => $value,
            'label' => $label,
            'icon' => $icon,
            'class' => $class,
            'parameter' => $parameter,
            'new_tab' => $new_tab
        ];

        return $this;
    }

    /**
     * Switches the view to a grid layout.
     *
     * @param string $thumbnail Thumbnail image source field.
     * @param string|null $hyperlink URL for the grid item.
     * @param array $parameter URL parameters for the hyperlink.
     * @param bool $new_tab Open hyperlink in a new tab.
     *
     * @return static Current object instance (chainable).
     */
    public function grid_view(string $thumbnail, ?string $hyperlink = null, array $parameter = [], bool $new_tab = false): static
    {
        $_ENV['GRID_VIEW'] = true;

        $this->_grid_view = [
            'thumbnail' => $thumbnail,
            'hyperlink' => $hyperlink,
            'parameter' => $parameter,
            'new_tab' => $new_tab
        ];

        return $this;
    }

    /**
     * Adds an individual filter control to the CRUD index table.
     *
     * @param array|string $filter Filter field key or array definition.
     * @param array        $options Select field options (if filter is a select type).
     *
     * @return static Current object instance (chainable).
     */
    public function add_filter(array|string $filter = [], array $options = []): static
    {
        if (! is_array($filter)) {
            $filter = [
                $filter => [
                    'label' => ucwords($filter),
                    'values' => $options
                ]
            ];
        }

        // Add default field parameters.
        foreach ($filter as $key => $val) {
            $filter[$key] = [
                'type' => (is_array($val['values']) ? 'select' : 'text'),
                'label' => $val['label'],
                'values' => $val['values']
            ];
        }

        $this->_add_filter = array_merge($this->_add_filter, $filter);

        return $this;
    }

    /**
     * Toggles the automatic search functionality (based on 'q' query string).
     *
     * @param bool $active TRUE to enable, FALSE to disable.
     *
     * @return static Current object instance (chainable).
     */
    public function searchable(bool $active = true): static
    {
        $this->_searchable = $active;

        return $this;
    }

    /**
     * Enables table row sorting (drag and drop).
     *
     * @param string|null $primary_key The primary key column name for the sortable table.
     * @param string|null $order_key   The column name used to store the row order index.
     *
     * @return static Current object instance (chainable).
     */
    public function sortable(?string $primary_key, ?string $order_key): static
    {
        $this->_sortable = [
            'sort_url' => current_page(),
            'primary_key' => $primary_key,
            'order_key' => $order_key
        ];

        return $this;
    }

    /**
     * Adds an action button to the module's toolbar.
     *
     * @param array<int, array>|string $url URL(s) for the action, or an array of button definitions.
     * @param string|null $label Button label (used if $url is a string).
     * @param string|null $class CSS class(es) (used if $url is a string).
     * @param string|null $icon Icon class (used if $url is a string).
     * @param array $parameter URL query parameters.
     * @param bool $new_tab Open link in a new tab.
     * @param string|null $attribution Custom HTML attributes.
     *
     * @return static Current object instance (chainable).
     */
    public function add_toolbar($url, string $label = null, ?string $class = null, ?string $icon = null, array $parameter = [], bool $new_tab = false, ?string $attribution = null): static
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
                'url' => (isset($val['url']) ? $val['url'] : null),
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
     * Adds an action button to each row of the CRUD table.
     *
     * @param array<int, array>|string $url URL(s) for the action, or an array of button definitions.
     * @param string|null $label Button label (used if $url is a string).
     * @param string|null $class CSS class(es) (used if $url is a string).
     * @param string|null $icon Icon class (used if $url is a string).
     * @param array $parameter URL query parameters.
     * @param bool $new_tab Open link in a new tab.
     * @param string|null $attribution Custom HTML attributes.
     *
     * @return static Current object instance (chainable).
     */
    public function add_button($url, ?string $label = null, ?string $class = null, ?string $icon = null, array $parameter = [], bool $new_tab = false, ?string $attribution = null): static
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
                'url' => (isset($val['url']) ? $val['url'] : null),
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
     * Adds a dropdown action button to each row of the CRUD table.
     *
     * @param array<int, array>|string $url URL(s) for the action, or an array of dropdown item definitions.
     * @param string|null $label Dropdown item label (used if $url is a string).
     * @param string|null $class CSS class(es) (used if $url is a string).
     * @param string|null $icon Icon class (used if $url is a string).
     * @param array $parameter URL query parameters.
     * @param bool $new_tab Open link in a new tab.
     * @param string|null $attribution Custom HTML attributes.
     *
     * @return static Current object instance (chainable).
     */
    public function add_dropdown($url, ?string $label = null, ?string $class = null, ?string $icon = null, array $parameter = [], bool $new_tab = false, ?string $attribution = null): static
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
                'url' => (isset($val['url']) ? $val['url'] : null),
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
     * Adds CSS classes to the rendered form field(s).
     *
     * Supports adding classes via an array ([field => class]) or a key-value pair.
     *
     * @param string|array $params The field name (if $value is provided) or an associative array [field => class].
     * @param string|null $value The CSS class string to apply (if $params is a field name).
     *
     * @return static Returns the current object instance (chainable).
     */
    public function add_class(string|array $params = [], ?string $value = null): static
    {
        // Make sure the parameters are in associative array format
        if (! is_array($params)) {
            // Convert parameters as array: ['field_name' => 'class_name']
            $params = [
                $params => $value
            ];
        }

        // Filter out null values before merging
        $params = array_filter($params, fn ($v) => null !== $v);

        // Merge array and store to property
        $this->_add_class = array_merge($this->_add_class ?? [], $params);

        return $this;
    }

    /**
     * Sets the rendering type and custom parameters for one or more fields.
     *
     * This method is central to customizing the behavior and appearance of fields in forms and views.
     * It handles both single field types and comma-separated multiple field types.
     *
     * @param string|array $field The field name (if a single field is set) or an associative array [field_name => type_string].
     * @param string|array|null $type The field type (e.g., 'image', 'wysiwyg') or types (comma-separated string), if $field is a single field name.
     * @param array|string|null $parameter Primary parameter (e.g., relation table, file path, dimension).
     * @param mixed $alpha Secondary parameter (e.g., custom format argument).
     * @param mixed $beta Tertiary parameter.
     * @param mixed $charlie Quaternary parameter.
     * @param string|null $delta Quinary parameter.
     *
     * @return static Returns the current object instance (chainable).
     */
    public function set_field(
        string|array $field = [],
        string|array|null $type = null,
        array|string|null $parameter = null,
        mixed $alpha = null,
        mixed $beta = null,
        mixed $charlie = null,
        ?string $delta = null
    ): static {
        // --- 1. Normalize Input to Associative Array [field_name => type_string] ---
        $fields_to_process = [];

        if (is_string($field)) {
            // Handle case: set_field('field_name', 'type_string', ...)
            $fields_to_process = [$field => $type];
        } elseif (is_array($field)) {
            // Handle case: set_field(['field_name' => 'type_string'], ...)
            $fields_to_process = $field;
        }

        // --- 2. Process Each Field and its Type(s) ---
        foreach ($fields_to_process as $field_name => $type_string) {
            if (! $type_string) {
                continue;
            }

            $types = [];

            // Determine if it's a single type or multiple (comma-separated)
            if (is_string($type_string) && strpos($type_string, ',') !== false) {
                $types = array_map('trim', explode(',', $type_string));
            } elseif (is_string($type_string)) {
                $types = [$type_string];
            } else {
                // Skip if type is not a recognizable string
                continue;
            }

            // Loop through each field type (e.g., 'image', 'editable', 'custom_format')
            foreach ($types as $current_type) {
                if ('custom_format' == $current_type) {
                    $this->_custom_format = true;
                }

                // Define the structure for the current type, prioritizing dedicated array parameters
                // if the input structure was ['field_name' => ['custom_format' => ['parameter' => '...']]]
                // over the common parameters ($parameter, $alpha, etc.).
                $param_source = $fields_to_process[$current_type] ?? [];

                $this->_set_field[$field_name][$current_type] = [
                    'parameter' => $param_source['parameter'] ?? $parameter,
                    'alpha' => $param_source['alpha'] ?? $alpha,
                    'beta' => $param_source['beta'] ?? $beta,
                    'charlie' => $param_source['charlie'] ?? $charlie,
                    'delta' => $param_source['delta'] ?? $delta
                ];
            }
        }

        return $this;
    }

    /**
     * Add the tooltip on field label when hovered
     *
     * @param string|array $params The field name or an associative array [field_name => tooltip_text].
     * @param string|null $value The tooltip text (if $params is a field name).
     */
    public function set_tooltip(string|array $params = [], ?string $value = null): static
    {
        if (! is_array($params)) {
            $params = [
                $params => $value
            ];
        }

        $this->_set_tooltip = array_merge($this->_set_tooltip ?? [], $params);

        return $this;
    }

    /**
     * The function to unset the field from form/update (CREATE/UPDATE methods).
     *
     * @param string|array $params Comma-separated field names or an array of field names.
     */
    public function unset_field(string|array $params = []): static
    {
        if (! is_array($params)) {
            $params = array_map('trim', explode(',', $params));
        }

        $this->_unset_field = array_merge($this->_unset_field ?? [], $params);

        return $this;
    }

    /**
     * The function to unset the column from table view (INDEX/LIST methods).
     *
     * @param string|array $params Comma-separated column names or an array of column names.
     */
    public function unset_column(string|array $params = []): static
    {
        if (! is_array($params)) {
            $params = array_map('trim', explode(',', $params));
        }

        $this->_unset_column = array_merge($this->_unset_column ?? [], $params);

        return $this;
    }

    /**
     * The function to unset the field on view data (READ method).
     *
     * @param string|array $params Comma-separated field names or an array of field names.
     */
    public function unset_view(string|array $params = []): static
    {
        if (! is_array($params)) {
            $params = array_map('trim', explode(',', $params));
        }

        $this->_unset_view = array_merge($this->_unset_view ?? [], $params);

        return $this;
    }

    /**
     * The function to rearrange the columns in the table view.
     *
     * @param string|array $params Comma-separated column names or an array of column names.
     */
    public function column_order(string|array $params = []): static
    {
        if (! is_array($params)) {
            $params = array_map('trim', explode(',', $params));
        }

        $this->_column_order = array_merge($this->_column_order ?? [], $params);

        return $this;
    }

    /**
     * The function to rearrange the field on view data (READ method).
     *
     * @param string|array $params Comma-separated field names or an array of field names.
     */
    public function view_order(string|array $params = []): static
    {
        if (! is_array($params)) {
            $params = array_map('trim', explode(',', $params));
        }

        $this->_view_order = array_merge($this->_view_order ?? [], $params);

        return $this;
    }

    /**
     * The function to rearrange the field in form (CREATE/UPDATE methods).
     *
     * @param string|array $params Comma-separated field names or an array of field names.
     */
    public function field_order(string|array $params = []): static
    {
        if (! is_array($params)) {
            $params = array_map('trim', explode(',', $params));
        }

        $this->_field_order = array_merge($this->_field_order ?? [], $params);

        return $this;
    }

    /**
     * The function to deny reading when primary key is matched with unset value.
     *
     * @param string|array $params Primary key field name or an associative array [pk_field => [value_1, value_2]].
     * @param array $value Array of primary key values to deny (if $params is a field name).
     */
    public function unset_read(string|array $params = [], array $value = []): static
    {
        if (! is_array($params)) {
            $params = [
                $params => $value
            ];
        }

        $this->_unset_read = array_merge($this->_unset_read ?? [], $params);

        return $this;
    }

    /**
     * The function to deny updating when primary key is matched with unset value.
     *
     * @param string|array $params Primary key field name or an associative array [pk_field => [value_1, value_2]].
     * @param array $value Array of primary key values to deny (if $params is a field name).
     */
    public function unset_update(string|array $params = [], array $value = []): static
    {
        if (! is_array($params)) {
            $params = [
                $params => $value
            ];
        }

        $this->_unset_update = array_merge($this->_unset_update ?? [], $params);

        return $this;
    }

    /**
     * The function to deny deleting when primary key is matched with unset value.
     *
     * @param string|array $params Primary key field name or an associative array [pk_field => [value_1, value_2]].
     * @param array $value Array of primary key values to deny (if $params is a field name).
     */
    public function unset_delete(string|array $params = [], array $value = []): static
    {
        if (! is_array($params)) {
            $params = [
                $params => $value
            ];
        }

        $this->_unset_delete = array_merge($this->_unset_delete ?? [], $params);

        return $this;
    }

    /**
     * The function to set default value of form field so user cannot make
     * any changes from input (fixed value).
     *
     * @param string|array $params Field name or an associative array [field_name => default_value].
     * @param mixed|null $value Default value (if $params is a field name).
     */
    public function set_default(string|array $params = [], mixed $value = null): static
    {
        if (! is_array($params)) {
            $params = [
                $params => $value
            ];
        }

        $this->_set_default = array_merge($this->_set_default ?? [], $params);

        return $this;
    }

    /**
     * Add the field to the form validation.
     *
     * @param string|array $params Field name or an associative array [field_name => validation_rules_string|array].
     * @param string|null $value Validation rules string (e.g., 'required|max_length[255]') (if $params is a field name).
     */
    public function set_validation(string|array $params = [], ?string $value = null): static
    {
        if (! is_array($params)) {
            $params = [
                $params => $value
            ];
        }

        // Find existing field validation and merge
        foreach ($params as $key => $val) {
            $val_rules = $val;

            if ($val_rules && is_string($val_rules)) {
                $val_rules = array_map('trim', explode('|', $val_rules));
            }

            if (isset($this->_set_validation[$key]) && is_array($val_rules)) {
                // Merge validation, ensuring the property is initialized
                $this->_set_validation[$key] = array_merge($this->_set_validation[$key] ?? [], $val_rules);
            } elseif ($val_rules) {
                // Set new validation
                $this->_set_validation[$key] = is_array($val_rules) ? $val_rules : [$val_rules];
            }
        }

        return $this;
    }

    /**
     * Set the upload path to follow the custom path.
     */
    public function set_upload_path(?string $path = null): static
    {
        // Validate the given parameter is a valid path name
        if ($path && preg_match('/^[A-Za-z0-9\-\.\_\/]*$/', $path)) {
            $this->_set_upload_path = strtolower($path);
        }

        return $this;
    }

    /**
     * Create custom callback of form validation.
     */
    public function form_callback(string $callback): static
    {
        $this->_form_callback = $callback;

        return $this;
    }

    /**
     * Set the alias of column/field, the selected column of database table will be translated.
     *
     * @param string|array $params Field name or an associative array [field_name => alias_text].
     * @param string|null $value Alias text (if $params is a field name).
     */
    public function set_alias(string|array $params = [], ?string $value = null): static
    {
        if (! is_array($params)) {
            $params = [
                $params => $value
            ];
        }

        $this->_set_alias = array_merge($this->_set_alias ?? [], $params);

        return $this;
    }

    /**
     * Add heading before field on form or view.
     *
     * @param string|array $params Field name or an associative array [field_name => heading_text].
     * @param string|null $value Heading text (if $params is a field name).
     */
    public function set_heading(string|array $params = [], ?string $value = null): static
    {
        if (! is_array($params)) {
            $params = [
                $params => $value
            ];
        }

        $this->_set_heading = array_merge($this->_set_heading ?? [], $params);

        return $this;
    }

    /**
     * The function to push the additional data to the response (API/View data).
     *
     * @param string|array $params Key or an associative array [key => value].
     * @param mixed $value Value (if $params is a key).
     */
    public function set_output(string|array $params = [], mixed $value = []): static
    {
        if (! is_array($params)) {
            $params = [
                $params => $value
            ];
        }

        $this->_set_output = array_merge($this->_set_output ?? [], $params);

        return $this;
    }

    /**
     * Prevent the field from being truncated in the table view.
     *
     * @param string|array $field Comma-separated field names or an array of field names.
     */
    public function unset_truncate(string|array $field): static
    {
        if (! is_array($field)) {
            $field = array_map('trim', explode(',', $field));
        }

        $this->_unset_truncate = array_merge($this->_unset_truncate ?? [], $field);

        return $this;
    }

    /**
     * Set the width of modal popup will be displayed (e.g., 'modal-xl', 'modal-lg').
     */
    public function modal_size(string $size): static
    {
        $this->_modal_size = strtolower($size);

        return $this;
    }

    /**
     * Arrange the field to a specific position (e.g., 'sidebar', 'column-2').
     *
     * @param string|array $params Field name or an associative array [field_name => position].
     * @param string|null $value Position (if $params is a field name).
     */
    public function field_position(string|array $params = [], ?string $value = null): static
    {
        if (! is_array($params)) {
            $params = [
                $params => $value
            ];
        }

        $this->_field_position = array_merge($this->_field_position ?? [], $params);

        return $this;
    }

    /**
     * Add the custom column size for the table view.
     *
     * @param string|array $params Column name or an associative array [column_name => width_percent].
     * @param string|null $value Width percentage string (e.g., '10%') (if $params is a column name).
     */
    public function column_size(string|array $params = [], ?string $value = null): static
    {
        if (! is_array($params)) {
            $params = [
                $params => $value
            ];
        }

        // array_replace is used to overwrite existing string keys without losing numeric ones
        $this->_column_size = array_replace($this->_column_size ?? [], $params);

        return $this;
    }

    /**
     * Add the custom field size for form input (e.g., 'col-md-6').
     *
     * @param string|array $params Field name or an associative array [field_name => column_class].
     * @param string|null $value Column class (if $params is a field name).
     */
    public function field_size(string|array $params = [], ?string $value = null): static
    {
        if (! is_array($params)) {
            $params = [
                $params => $value
            ];
        }

        $this->_field_size = array_merge($this->_field_size ?? [], $params);

        return $this;
    }

    /**
     * Add the prefix (prepend) content to the field input in the form.
     *
     * @param string|array $params Field name or an associative array [field_name => html_string].
     * @param string|null $value HTML string (if $params is a field name).
     */
    public function field_prepend(string|array $params = [], ?string $value = null): static
    {
        if (! is_array($params)) {
            $params = [
                $params => $value
            ];
        }

        $this->_field_prepend = array_merge($this->_field_prepend ?? [], $params);

        return $this;
    }

    /**
     * Add the suffix (append) content to the field input in the form.
     *
     * @param string|array $params Field name or an associative array [field_name => html_string].
     * @param string|null $value HTML string (if $params is a field name).
     */
    public function field_append(string|array $params = [], ?string $value = null): static
    {
        if (! is_array($params)) {
            $params = [
                $params => $value
            ];
        }

        $this->_field_append = array_merge($this->_field_append ?? [], $params);

        return $this;
    }

    /**
     * Merges multiple data fields into a single column string for List/Read views.
     *
     * The fields to be merged are specified within the $magic_string using double curly braces (e.g., "Hello {{first_name}} {{last_name}}").
     *
     * @param string $magic_string The template string containing field names wrapped in {{...}}.
     * @param string|null $alias The alias/label for the new merged column.
     * @param string|null $callback Optional callback function name (without 'callback_') to process the merged string.
     *
     * @return static Returns the current object instance (chainable).
     */
    public function merge_content(string $magic_string, ?string $alias = null, ?string $callback = null): static
    {
        // Get the fields from the magic string
        preg_match_all('/\{\{(.*?)\}\}/', $magic_string, $matches);

        $field_names = array_map('trim', $matches[1]);
        $primary_field = (isset($field_names[0]) ? $field_names[0] : null);

        // --- 1. Set Alias/Label ---
        if (! in_array($this->_method, ['create', 'update'])) {
            $default_label = ucwords(str_replace('_', ' ', $primary_field));
            $final_alias = $alias ?? $default_label;

            if ($primary_field) {
                $this->_set_alias[$primary_field] = $final_alias;
                $this->_merge_label[$primary_field] = $final_alias;
            }
        }

        // --- 2. Sets the Merge Property ---
        if ($primary_field) {
            $this->_merge_content[$primary_field] = [
                'column' => $field_names,
                'parameter' => $magic_string,
                'callback' => $callback ? str_replace('callback_', '', $callback) : null
            ];
        }


        // --- 3. Unset Original Columns ---
        if (count($field_names) > 1) {
            // Loops the keys starting from the second element (index 1) because the first element
            // is used as the key for the merged column.
            $secondary_fields = array_slice($field_names, 1);

            foreach ($secondary_fields as $val) {
                $this->_unset_column[] = $val;
                $this->_unset_view[] = $val;
            }
        }

        return $this;
    }

    /**
     * Merges multiple input fields into a single logical field group for the Form View.
     *
     * The first element of $params is the primary field, and subsequent elements are merged fields.
     *
     * @param string|array $params The fields to merge. If string, it's comma-separated: 'primary_field, field_2, field_3'.
     *
     * @return static Returns the current object instance (chainable).
     */
    public function merge_field(string|array $params): static
    {
        if (! is_array($params)) {
            // Shorthand possibility, separate with commas
            $params = array_map('trim', explode(',', $params));
        }

        if (count($params) < 2) {
            return $this; // Needs at least a primary field and one merged field
        }

        $primary_field = $params[0];
        $merged_fields = array_slice($params, 1);

        // Merge array and store to property: [primary_field => [field_2, field_3, ...]]
        $this->_merge_field[$primary_field] = $merged_fields;

        return $this;
    }

    /**
     * Merges multiple input fields into a single group for better organization in the form.
     *
     * @param string|array $params Comma-separated field names, or an array of field names.
     * @param string|null $group The group name/label to apply to the fields.
     *
     * @return static Returns the current object instance (chainable).
     */
    public function group_field(string|array $params = [], ?string $group = null): static
    {
        if (! is_array($params)) {
            // Shorthand possibility, separate with commas
            $params = array_map('trim', explode(',', $params));
            // Fill the array with the common group name: [field_1 => 'Group Name', field_2 => 'Group Name']
            $params = array_fill_keys($params, $group);
        }

        $this->_group_field = array_merge($this->_group_field ?? [], $params);

        return $this;
    }

    /**
     * Groups data rows based on a parent field relationship (hierarchical data).
     *
     * Used for displaying data rows in a tree-like structure, referenced by a parent field.
     *
     * @param string|array $params The field name(s) used as a parent reference.
     *
     * @return static Returns the current object instance (chainable).
     */
    public function item_reference(string|array $params = []): static
    {
        if (! is_array($params)) {
            // Shorthand possibility, separate with commas
            $params = array_map('trim', explode(',', $params));
        }

        $this->_item_reference = array_merge($this->_item_reference ?? [], $params);

        return $this;
    }

    /**
     * Adds extra HTML attributes to a field input element.
     *
     * Automatically merges attributes if called multiple times for the same field.
     *
     * @param string|array $params Field name or an associative array [field_name => 'attribute_string'].
     * @param string|null $value The attribute string (e.g., 'data-foo="bar" required') (if $params is a field name).
     *
     * @return static Returns the current object instance (chainable).
     */
    public function set_attribute(string|array $params = [], ?string $value = null): static
    {
        // Handle single key-value pair and merge if already exists
        if (! is_array($params)) {
            if (isset($this->_set_attribute[$params])) {
                // Already set, append the new value
                $this->_set_attribute[$params] .= ' ' . $value;

                return $this;
            }

            // Convert parameters as array
            $params = [
                $params => $value
            ];
        }

        // Merge array and store to property
        $this->_set_attribute = array_merge($this->_set_attribute ?? [], $params);

        return $this;
    }

    /**
     * Adds a placeholder text to a form input field.
     *
     * @param string|array $params Field name or an associative array [field_name => placeholder_text].
     * @param string|null $value The placeholder text (if $params is a field name).
     *
     * @return static Returns the current object instance (chainable).
     */
    public function set_placeholder(string|array $params = [], ?string $value = null): static
    {
        if (! is_array($params)) {
            // Convert parameters as array
            $params = [
                $params => $value
            ];
        }

        $this->_set_placeholder = array_merge($this->_set_placeholder ?? [], $params);

        return $this;
    }

    /**
     * Adds extra labels or descriptions to options within a select/dropdown field.
     *
     * @param string|array $params Field name or an associative array [field_name => extra_label_template].
     * @param string|null $value The extra label/template (if $params is a field name).
     *
     * @return static Returns the current object instance (chainable).
     */
    public function set_option_label(string|array $params = [], ?string $value = null): static
    {
        if (! is_array($params)) {
            // Convert parameters as array
            $params = [
                $params => $value
            ];
        }

        $this->_set_option_label = array_merge($this->_set_option_label ?? [], $params);

        return $this;
    }

    /**
     * Sets the default value of a field input when adding new data (CREATE method).
     *
     * This is an alias/alternative to set_default(), focused on initial values.
     *
     * @param string|array $field Field name or an associative array [field_name => default_value].
     * @param mixed|null $value Default value (if $field is a field name).
     *
     * @return static Returns the current object instance (chainable).
     */
    public function default_value(string|array $field = [], mixed $value = null): static
    {
        if (! is_array($field)) {
            // Convert parameters as array
            $field = [
                $field => $value
            ];
        }

        $this->_default_value = array_merge($this->_default_value ?? [], $field);

        return $this;
    }

    /**
     * Sets the field as a relation field, linking it to another table for display (e.g., dropdowns, autocomplete).
     *
     * This function handles single-key and composite-key relations, manages required SELECT columns,
     * sets up necessary JOINs, and applies relation validation rules.
     *
     * @param string $field The local field name(s) (comma-separated for composite keys).
     * @param string $primary_key The foreign key in the related table(s) (comma-separated for composite keys).
     * @param string $output The magic string defining the output format (e.g., '{{name}} - {{id}}').
     * @param array $where Optional WHERE conditions for the relation query.
     * @param array $join Optional extra JOIN clauses for the relation query.
     * @param array $order_by Optional ORDER BY clauses.
     * @param string|null $group_by Optional GROUP BY clause.
     * @param int $limit Max number of results to fetch (0 uses default limit).
     * @param bool $translate Flag to indicate if the relation field should be translated.
     *
     * @return static Returns the current object instance (chainable).
     */
    public function set_relation(
        string $field,
        string $primary_key,
        string $output,
        array $where = [],
        array $join = [],
        array $order_by = [],
        ?string $group_by = null,
        int $limit = 0,
        bool $translate = false
    ): static {
        // --- 1. Initial Setup and Magic String Extraction ---
        $alias = $field;
        preg_match_all('/\{\{(.*?)\}\}/', $output, $matches);
        $select = array_map('trim', $matches[1]);

        if ($translate) {
            foreach ($select as $val) {
                $this->_translate_field[] = substr(strstr($val, '.'), 1);
            }
        }

        $is_composite = (strpos($field, ',') !== false && strpos($primary_key, ',') !== false);

        // Default relation parts
        $relation_table = null;
        $relation_keys = [];
        $field_local = [];
        $group_by_fields = [];

        // --- 2. Handle Composite Keys vs. Single Key ---
        if ($is_composite) {
            $field_local = array_map('trim', explode(',', $field));
            $primary_keys_foreign = array_map('trim', explode(',', $primary_key));

            $alias = $field_local[0];
            $group_by_fields = [];

            foreach ($primary_keys_foreign as $key => $val) {
                // Ensure the foreign key is selected
                if (! in_array($val, $select)) {
                    $select[] = $val;
                    $group_by_fields[] = $val;
                }

                // Extract table and key parts
                list($table_name, $key_name) = array_pad(explode('.', $val), 2, null);

                if ($table_name && $key_name) {
                    $relation_table = $table_name;
                    $relation_keys[] = $key_name;
                }

                // Cleanup: Add related columns to unset properties
                $this->_unset_column[] = $key_name;
                $this->_unset_view[] = $key_name;

                // Handle masking for composite keys (original logic)
                if (0 == $key) {
                    // The first key is often used as the primary identifier for the merged field.
                    // The original code has complex logic to add an alias_masking column here,
                    // which is highly specific to Aksara's rendering.
                    array_unshift($select, $relation_table . '.' . $field_local[0] . ' AS ' . $alias . '_masking');
                }
            }
        } else {
            // Single Key Relation
            $field_local = $field;

            // Ensure primary key value is selected, alias it using the local field name if simple
            if (! in_array($primary_key, $select)) {
                $select[] = (strpos($primary_key, ' ') !== false ? substr($primary_key, strpos($primary_key, ' ') + 1) : $primary_key) . ' AS ' . $alias;
            }

            // Merge select from existing attributes (e.g., 'data-image="{{image}}"' attribute)
            if (isset($this->_set_attribute[$field])) {
                preg_match_all('/\{\{(.*?)\}\}/', $this->_set_attribute[$field], $matches_attributes);
                $select = array_merge($select, array_map('trim', $matches_attributes[1]));
            }

            // Extract relation table and key
            $parts = explode('.', $primary_key);
            $relation_table = $parts[0] ?? null;
            $relation_keys = $parts[1] ?? null;

            // Cleanup: Add local field to unset properties
            $this->_unset_column[] = $field;
            $this->_unset_view[] = $field;
        }

        // --- 3. Additional Query Setup (Join, Where, Select Merge) ---

        // Ensure JOIN input is standardized
        if ($join && ! isset($join[0])) {
            $join = [$join];
        }

        // Standardize WHERE conditions (prefixing if needed)
        if ($where) {
            foreach ($where as $key => $val) {
                if (! preg_match('/[.<=>()]/', $key)) {
                    $where[$key] = $val; // Assuming the framework adds the table prefix later if necessary
                }
            }
        }

        // Merge final unique selection list
        $this->_select = array_unique(array_merge($this->_select ?? [], $select));

        // --- 4. Define Framework JOIN and Validation ---

        if (! in_array($this->_method, ['create', 'update', 'delete'])) {
            $condition = '';
            $relation_table_clean = $relation_table;

            // Clean table name if aliased (e.g., 'table t' -> 't')
            if (strpos($relation_table, ' ') !== false) {
                list($base_table, $relation_table_clean) = explode(' ', $relation_table);
            }

            if (is_array($field_local)) {
                // Composite JOIN condition
                foreach ($field_local as $key => $val) {
                    $fk_key = $relation_keys[$key] ?? $val;
                    $condition .= ($condition ? ' AND ' : '') . $relation_table_clean . '.' . $fk_key . ' = __PRIMARY_TABLE__.' . $val;

                    // Apply validation for each key
                    $this->set_validation($val, 'relation_checker[' . $relation_table_clean . '.' . $fk_key . ']');
                }
            } else {
                // Single JOIN condition
                $condition = $relation_table_clean . '.' . $relation_keys . ' = __PRIMARY_TABLE__.' . $field_local;

                // Apply validation for the single key
                $this->set_validation($field_local, 'relation_checker[' . $relation_table_clean . '.' . $relation_keys . ']');
            }

            // Add the primary relation table to compilation and JOIN property
            $this->_compiled_table[] = $relation_table;
            $this->_join[$relation_table] = [
                'condition' => $condition,
                'type' => 'LEFT',
                'escape' => true
            ];

            // Add additional JOINs
            if ($join) {
                foreach ($join as $val) {
                    // $val format: [table, condition, type]
                    $this->_compiled_table[] = $val[0];
                    $this->_join[$val[0]] = [
                        'condition' => $val[1],
                        'type' => $val[2] ?? 'LEFT',
                        'escape' => true
                    ];
                }
            }
        }

        // --- 5. Finalize Relation Property ---
        $final_limit = (is_numeric($limit) && $limit > 0) ? $limit : $this->_limit;

        // Calculate offset for paginated requests (used by AJAX SELECT)
        $offset = (is_numeric(Services::request()->getPost('page')) ? Services::request()->getPost('page') - 1 : 0) * $final_limit;

        // Add set relation property
        $this->_set_relation[$alias] = [
            'select' => $select,
            'primary_key' => $field_local,
            'relation_table' => $relation_table,
            'relation_key' => $relation_keys,
            'where' => $where,
            'join' => $join,
            'order_by' => $order_by,
            'group_by' => $group_by ?? (is_array($group_by_fields) ? $group_by_fields : null),
            'limit' => $final_limit,
            'offset' => $offset,
            'output' => $output,
            'translate' => $translate
        ];

        // Add visual diagram of relationship setup here

        return $this;
    }

    /**
     * Sets the field as an Autocomplete input, pulling data from a related table.
     *
     * This configures the necessary SELECT fields, JOINs, and the format (output) for the suggestions list.
     *
     * @param string $field The local field name to be converted to autocomplete.
     * @param string $selected_value The foreign key in the related table (e.g., 'table.key_id').
     * @param array $output An array defining the visual output: ['value', 'label', 'description', 'image'].
     * @param array $where Optional WHERE conditions for the autocomplete query.
     * @param array $join Optional extra JOIN clauses.
     * @param array $order_by Optional ORDER BY clauses.
     * @param string|null $group_by Optional GROUP BY clause.
     * @param int $limit Max number of suggestions to fetch (0 means no explicit limit).
     *
     * @return static Returns the current object instance (chainable).
     */
    public function set_autocomplete(
        string $field,
        string $selected_value,
        array $output,
        array $where = [],
        array $join = [],
        array $order_by = [],
        ?string $group_by = null,
        int $limit = 0
    ): static {
        // --- 1. Normalize Output and Extract Magic Strings ---
        $value = $output['value'] ?? $output[0] ?? null;
        $label = $output['label'] ?? $output[1] ?? null;
        $description = $output['description'] ?? $output[2] ?? null;
        $image = $output['image'] ?? $output[3] ?? null;

        $select_magic = $value . $label . $description . $image;

        // Extract all fields wrapped in {{...}} from the output format
        preg_match_all('/\{\{(.*?)\}\}/', $select_magic, $matches_select);

        $select = $matches_select[1] ? array_map('trim', $matches_select[1]) : [];

        // Ensure the foreign key is also selected, aliased to the local field name
        $select[] = $selected_value . ' AS ' . $field;

        list($relation_table, $relation_key) = array_pad(explode('.', $selected_value), 2, null);

        // --- 2. Configuration Cleanup ---
        if ($join && ! isset($join[0])) {
            $join = [$join]; // Standardize single JOIN array
        }

        if (! $group_by) {
            $group_by = $relation_table . '.' . $relation_key;
        }

        // Merge select statements
        $this->_select = array_unique(array_merge($this->_select ?? [], $select));

        // Unset the local field from being displayed as a normal column/view item
        $this->_unset_column[] = $field;
        $this->_unset_view[] = $field;

        // --- 3. Define Implicit JOIN (Used for initial display or listing) ---
        $is_not_crud = ! in_array($this->_method, ['create', 'update', 'delete']);
        $is_autocomplete_request = ('autocomplete' == $this->request->getPost('method') && $this->request->getPost('origin'));

        if ($is_not_crud || $is_autocomplete_request) {
            // Primary JOIN
            $this->_join[$relation_table] = [
                'condition' => $relation_table . '.' . $relation_key . ' = __PRIMARY_TABLE__.' . $field,
                'type' => '',
                'escape' => true
            ];

            // Additional JOINs
            if ($join) {
                foreach ($join as $val) {
                    // $val format: [table, condition, type]
                    $this->_join[$val[0]] = [
                        'condition' => $val[1],
                        'type' => $val[2] ?? '',
                        'escape' => true
                    ];
                }
            }
        }

        // --- 4. Finalize Autocomplete Property ---
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
     * Serializes data rows, detecting field types, primary keys, and applying formatting.
     *
     * This method transforms raw database results into a structured array for CRUD views or API output.
     *
     * @param array $data Raw array of database result rows.
     *
     * @return array The structured, serialized data, or JSON response if requested by API client.
     */
    public function serialize(array $data): array
    {
        if (! $data && $this->model->table_exists($this->_table)) {
            // Flip columns
            $data = [array_fill_keys($this->model->list_fields($this->_table), '')];
        }

        if ($this->api_client && (! $this->request->getGet('format_result') || ! in_array($this->request->getGet('format_result'), ['field_data', 'complete', 'full']))) {
            // Requested from API Client in unformatted result
            return make_json($data);
        }

        $output = [];

        foreach ($data as $row => $array) {
            // Process single row
            $output[$row] = $this->serialize_row($array, false);
        }

        if ($this->api_client && 'field_data' === $this->request->getGet('format_result')) {
            // Requested from API Client with field data information
            return make_json($output);
        }

        return $output;
    }

    /**
     * Serializes a single row
     *
     *
     * @return array The structured, serialized row data
     */
    public function serialize_row(array|object $data, bool $return = true): array
    {
        // Define field data compilation
        $field_data = $this->model->field_data($this->_table);

        // Find primary key
        foreach ($field_data as $key => $val) {
            // Unset indexed field data
            unset($field_data[$key]);

            // Add properties to field data compilation
            $field_data[$val->name] = $val;
        }

        $output = [];

        foreach ($data as $field => $value) {
            $hidden = false;

            // Attempt to get the type
            $type = strtolower((isset($field_data[$field]->type) ? $field_data[$field]->type : gettype($value)));

            // Reformat type
            if (in_array($type, ['tinyint', 'smallint', 'int', 'mediumint', 'bigint', 'year'])) {
                // Field type number
                $type = 'number';
            } elseif (in_array($type, ['decimal', 'float', 'double', 'real'])) {
                // Field type decimal
                if (in_array($type, ['percent'])) {
                    $type = 'percent';
                } else {
                    $type = 'money';
                }
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
            } elseif (in_array($type, ['enum']) && in_array($this->_db_driver, ['MySQLi']) && ! isset($this->_set_field[$field])) {
                try {
                    // Get enum list
                    $enum_query = $this->model->query('SELECT COLUMN_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = ? AND COLUMN_NAME = ? AND TABLE_SCHEMA = DATABASE()', [
                        $this->_table,
                        $field_data[$field]->name
                    ])->row('COLUMN_TYPE');

                    // Extract enum list
                    $enum_list = explode(',', str_ireplace(["enum(", ")", "'"], '', $enum_query));

                    if ($enum_list) {
                        $options = [];

                        foreach ($enum_list as $_key => $_val) {
                            $options[$_val] = $_val;
                        }

                        $this->_set_field[$field]['select'] = [
                            'parameter' => $options,
                            'alpha' => null,
                            'beta' => null,
                            'charlie' => null,
                            'delta' => null
                        ];
                    }
                } catch (Throwable $e) {
                    // Safe abstraction
                    exit($e->getMessage());
                }
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
            $validation = (isset($this->_set_validation[$field]) ? $this->_set_validation[$field] : []);

            // Attempt to get field translation
            $content = (in_array($field, $this->_translate_field) ? phrase($value) : $value);

            if ('create' == $this->_method) {
                $content = (isset($this->_set_default[$field]) ? $this->_set_default[$field] : (isset($field_data[$field]->default) ? $field_data[$field]->default : null));
                $value = null;
            }

            if (in_array($this->_method, ['create', 'update']) && (in_array($field, $this->_unset_field) || array_intersect(['current_timestamp', 'created_timestamp', 'updated_timestamp'], array_keys($this->_set_field[$field])))) {
                // Indicates that field should not be shown
                $hidden = true;
            } elseif (('read' == $this->_method || (in_array($this->_method, ['print', 'pdf']))) && in_array($field, $this->_unset_view)) {
                // Indicates that field should not be shown
                $hidden = true;
            } elseif (in_array($this->_method, ['index', 'export', 'print', 'pdf']) && in_array($field, $this->_unset_column)) {
                // Indicates that field should not be shown
                $hidden = true;
            }

            if ($value && isset($this->_set_relation[$field])) {
                // Get relation content
                $content = $this->_get_relation($this->_set_relation[$field], $value);
            }

            if ($content && array_intersect(['numeric', 'money', 'percent'], [$type]) && is_numeric($content)) {
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
                $parameter = '%02d';

                if (isset($this->_set_field[$field]['sprintf']['parameter'])) {
                    $parameter = $this->_set_field[$field]['sprintf']['parameter'];
                }

                // Add zero leading
                $content = sprintf(($parameter && ! is_array($parameter) ? $parameter : '%02d'), $content);
            }

            if ($maxlength) {
                if (in_array($type, ['money', 'percent'])) {
                    // Add extra dot to maxlength
                    $maxlength = ($maxlength + 1);
                }

                $validation[] = 'max_length[' . $maxlength . ']';
            }

            // Call assigned method of custom format
            if (isset($this->_set_field[$field]) && in_array('custom_format', array_keys($this->_set_field[$field])) && method_exists($this, $this->_set_field[$field]['custom_format']['parameter'])) {
                $method = $this->_set_field[$field]['custom_format']['parameter'];
                $content = $this->$method((array) $data);
            }

            $output[$field] = [
                'primary' => in_array($field, $this->_set_primary),
                'value' => $value,
                'content' => $content,
                'maxlength' => $maxlength,
                'hidden' => $hidden,
                'type' => $this->_set_field[$field],
                'validation' => $validation
            ];

            if ($this->api_client && $return) {
                $output[$field]['label'] = (isset($this->_set_alias[$field]) ? $this->_set_alias[$field] : ucwords(str_replace('_', ' ', $field) ?? ''));
            }
        }

        if ($this->api_client && $return) {
            // Requested from API Client with field data information
            return make_json($output);
        }

        return $output;
    }

    /**
     * Renders the final result into the appropriate view, API response, or document format.
     *
     * This method coordinates security checks, query building, form handling (CRUD),
     * and output formatting, serving as the main dispatcher for the framework's output.
     *
     * @param string|null $table The primary database table to be rendered.
     * @param string|null $view  The template view file to be used.
     *
     * @return object|string Returns the result of the executed controller method (View content string, JSON array, or Exception object).
     */
    public function render(?string $table = null, ?string $view = null): object|array|string
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
                if (! get_userdata('access_token') && ! $this->request->getHeaderLine('X-ACCESS-TOKEN')) {
                    // Access token is not set
                    return throw_exception(403, phrase('This service is require an access token.'));
                } elseif (! $this->_api_token) {
                    // Access token is not valid
                    return throw_exception(403, phrase('The access token is invalid or already expired.'));
                }
            } elseif (in_array($this->request->getMethod(), ['POST', 'DELETE']) &&
            ! in_array($this->_method, ['create', 'update', 'delete'])) {
                // Check if request is made from promise
                return throw_exception(403, phrase('The method you requested is not acceptable.') . ' (' . $this->request->getMethd() . ')', (! $this->api_client ? go_to() : null));
            }
        } elseif ($table && ! $this->_set_permission) {
            // Unset database modification because no permission is set
            $this->unset_method('create, update, delete');

            if (in_array($this->_method, ['create', 'update', 'delete'])) {
                // Throw exception about the method
                return throw_exception(403, phrase('You do not have sufficient privileges to access the requested page.') . ' (' . strtoupper($this->_method). ')', (! $this->api_client ? go_to() : null));
            }
        }

        if (! $this->_table) {
            // Set table when not present
            $this->_table = $table;

            // Push to compiled table
            $this->_compiled_table[] = $table;
        }

        if (! $this->request->getPost('_token')) {
            // Set CSRF Token
            $this->_token = hash_hmac('sha256', uri_string() . get_userdata('session_generated') . get_userdata('token_timestamp'), ENCRYPTION_KEY);

            // There may be a form without using form renderer
            // Set CSRF Token into unique session key
            set_userdata(sha1(uri_string()), $this->_token);
        }

        // Validate the restricted action
        if (in_array($this->_method, $this->_unset_method)) {
            return throw_exception(403, phrase('You are not allowed to perform the requested action.'), go_to());
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

        // Query string filters
        $query_params = $this->request->getGet();

        // Token Validation
        if ($query_params && ENCRYPTION_KEY !== $this->request->getHeaderLine('X-API-KEY')) {
            // Apply validation for protected page from non API client request
            if ($this->_set_permission && ! $this->api_client) {
                $expected_token = generate_token(uri_string(), $query_params);
                $submitted_token = $this->request->getGet('aksara');

                // Token comparison
                if (! hash_equals($expected_token, (string) $submitted_token)) {
                    // Token didn't match
                    return throw_exception(403, phrase('The submitted token has expired or the request is made from a restricted source.'));
                }
            }
        }

        // Check if given table is exists in database
        if ($this->_table) {
            // Check if table is exists
            if (! $this->model->table_exists($this->_table)) {
                return throw_exception(404, phrase('The defined primary table does not exist.'), current_page('../'));
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
                    $this->set_description('<div><b>' . phrase('No primary key is found.') . '</b> ' . phrase('Please define it manually and refer to {{set_primary}}.', ['set_primary' => '<code>set_primary()</code>']) . ' ' . phrase('Without primary key, you only allowed to insert the data.') . '</div>');
                }

                // Unset method
                $this->unset_method('update, delete');
            }

            // Remove primary query string if method is matched
            foreach ($query_params as $key => $val) {
                if (in_array($this->_method, ['read', 'update', 'delete']) && in_array($key, $this->_set_primary)) {
                    // Remove query parameter from URL
                    $query_params[$key] = null;
                }
            }

            // Assign previous URL
            $this->_redirect_back = go_to(null, $query_params);

            // Check the additional primary key that been sets up
            if (is_array($this->_set_primary) && sizeof($this->_set_primary) > 0) {
                foreach ($this->_set_primary as $key => $val) {
                    if ($this->request->getGet($val) && $this->model->field_exists($val, $this->_table)) {
                        if (
                            ('read' == $this->_method && isset($this->_unset_read[$val]) && in_array($this->request->getGet($val), $this->_unset_read[$val])) ||
                            ('update' == $this->_method && isset($this->_unset_update[$val]) && in_array($this->request->getGet($val), $this->_unset_update[$val])) ||
                            ('delete' == $this->_method && isset($this->_unset_delete[$val]) && in_array($this->request->getGet($val), $this->_unset_delete[$val])) ||
                            ('export' == $this->_method && isset($this->_unset_read[$val]) && in_array($this->request->getGet($val), $this->_unset_read[$val])) ||
                            ('print' == $this->_method && isset($this->_unset_read[$val]) && in_array($this->request->getGet($val), $this->_unset_read[$val])) ||
                            ('pdf' == $this->_method && isset($this->_unset_read[$val]) && in_array($this->request->getGet($val), $this->_unset_read[$val]))
                        ) {
                            if (in_array($this->_method, ['read', 'export', 'print', 'pdf'])) {
                                // Method isn't allowed to access, throw exception
                                return throw_exception(403, phrase('You are not allowed to view the requested data.'), $this->_redirect_back);
                            } else {
                                if (isset($this->_set_messages['update'])) {
                                    // Add custom message if any
                                    return throw_exception($this->_set_messages['update']['code'], $this->_set_messages['update']['messages'], $this->_redirect_back);
                                } else {
                                    // Otherwise, use default message
                                    return throw_exception(403, phrase('You are not allowed to modify the requested data.'), $this->_redirect_back);
                                }
                            }
                        }

                        if (! in_array($val, array_keys($this->_where ?? []))) {
                            // Push where into prepared statement only if where is not defined in controller
                            $this->_prepare('where', [$this->_table . '.' . $val, htmlspecialchars($this->request->getGet($val))]);
                        }
                    } elseif (
                        in_array($val, $this->_set_primary) &&
                        $this->model->field_exists($val, $this->_table) &&
                        isset($this->_set_default[$val]) &&
                        $this->_set_default[$val]
                    ) {
                        if (
                            ('read' == $this->_method && isset($this->_unset_read[$val]) && in_array($this->request->getGet($val), $this->_unset_read[$val])) ||
                            ('update' == $this->_method && isset($this->_unset_update[$val]) && in_array($this->request->getGet($val), $this->_unset_update[$val])) ||
                            ('delete' == $this->_method && isset($this->_unset_delete[$val]) && in_array($this->request->getGet($val), $this->_unset_delete[$val])) ||
                            ('export' == $this->_method && isset($this->_unset_read[$val]) && in_array($this->request->getGet($val), $this->_unset_read[$val])) ||
                            ('print' == $this->_method && isset($this->_unset_read[$val]) && in_array($this->request->getGet($val), $this->_unset_read[$val])) ||
                            ('pdf' == $this->_method && isset($this->_unset_read[$val]) && in_array($this->request->getGet($val), $this->_unset_read[$val]))
                        ) {
                            if (in_array($this->_method, ['read', 'export', 'print', 'pdf'])) {
                                // Requested method isn't allowed, throw exception
                                return throw_exception(403, phrase('You are not allowed to view the requested data.'), $this->_redirect_back);
                            } else {
                                if (isset($this->_set_messages['update'])) {
                                    // Add custom message if any
                                    return throw_exception($this->_set_messages['update']['code'], $this->_set_messages['update']['messages'], $this->_redirect_back);
                                } else {
                                    // Otherwise, use default message
                                    return throw_exception(403, phrase('You are not allowed to modify the requested data.'), $this->_redirect_back);
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
            if ($this->request->getPost('_token')) {
                // Request is sent from browser
                $token_sent = $this->request->getPost('_token');

                // Validate the token
                if ($this->valid_token($token_sent)) {
                    // Token approved, check if validation use the custom callback
                    if ($this->_form_callback && method_exists($this, $this->_form_callback)) {
                        // Use callback as form validation
                        $_callback = $this->_form_callback;

                        return $this->$_callback();
                    } else {
                        // Serialize table data
                        $field_data = array_fill_keys(array_keys(array_flip($this->model->list_fields($this->_table))), '');

                        // Or use the master validation instead
                        return $this->validate_form($field_data);
                    }
                } else {
                    // Token isn't valid, throw exception
                    return throw_exception(403, phrase('The submitted token has been expired or the request is made from the restricted source.'), $this->_redirect_back);
                }
            } elseif ($this->api_client && in_array($this->request->getMethod(), ['POST']) && (in_array($this->_method, ['create', 'update']) || ($this->_form_callback && method_exists($this, $this->_form_callback)))) {
                // Request is sent from REST
                if ($this->_form_callback && method_exists($this, $this->_form_callback)) {
                    // Use callback as form validation
                    $_callback = $this->_form_callback;

                    return $this->$_callback();
                } else {
                    // Serialize table data
                    $field_data = array_fill_keys(array_keys(array_flip($this->model->list_fields($this->_table))), '');

                    // Or use the master validation instead
                    return $this->validate_form($field_data);
                }
            } elseif ($this->_set_primary && 'delete' == $this->_method) {
                // Delete data
                if (1 == $this->request->getPost('batch')) {
                    // Batch delete
                    return $this->delete_batch($this->_table);
                } else {
                    // Single delete
                    return $this->delete_data($this->_table, $this->_where, $this->_limit);
                }
            } else {
                // Get offset if not set
                if (! in_array($this->_method, ['create', 'read', 'update', 'delete']) && is_numeric($this->request->getGet('per_page')) && $this->request->getGet('per_page') > 1 && (! $this->_offset_called || (! $this->_offset && gettype($this->_offset) !== 'integer'))) {
                    $this->_offset = ($this->request->getGet('per_page') - 1) * ($this->_limit ?? $this->_limit_backup);
                }

                if ($this->_offset) {
                    // Push offset to the prepared query builder
                    $this->_prepare('offset', [$this->_offset]);
                }

                if (! in_array($this->_method, ['create', 'read', 'update', 'delete']) &&
                    ($this->_searchable && ! $this->_like && $this->request->getGet('q')) ||
                    ('autocomplete' == $this->request->getPost('method') && $this->_searchable && $this->request->getPost('q'))
                ) {
                    $is_autocomplete = ('autocomplete' == $this->request->getPost('method'));
                    $search_query = $is_autocomplete ? $this->request->getPost('q') : $this->request->getGet('q');

                    // Sanitize search query - escape special characters for LIKE
                    $search_query = str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $search_query);
                    $search_query = htmlspecialchars($search_query, ENT_QUOTES, 'UTF-8');

                    $group_start = false;

                    if (! $is_autocomplete) {
                        $this->group_start();
                        $group_start = true;
                    }

                    $column = $this->request->getGet('column') ? strip_tags($this->request->getGet('column')) : null;

                    // ========== SEARCH BY SPECIFIC COLUMN ==========
                    if ($column && 'all' != $column) {
                        // Whitelist: Ensure column is valid
                        $valid_columns = [];
                        foreach ($this->_compiled_table as $key => $val) {
                            if ($this->model->field_exists($column, $val)) {
                                $valid_columns[] = $val . '.' . $column;
                            }
                        }

                        if (! empty($valid_columns)) {
                            foreach ($valid_columns as $valid_column) {
                                $this->_prepare('like', [$valid_column, $search_query]);
                            }
                        }
                    }
                    // ========== SEARCH ALL COLUMNS ==========
                    else {
                        $columns = $this->model->list_fields($this->_table);

                        // Get columns from joined tables
                        if ($this->_select && $this->_compiled_table) {
                            foreach ($this->_compiled_table as $key => $val) {
                                list($joined_table) = explode('.', $val);

                                if ($joined_table != $this->_table) {
                                    $select_search = preg_grep('/^' . preg_quote($joined_table, '/') . '/', $this->_select);

                                    if (isset($select_search[0])) {
                                        $columns[] = $select_search[0];
                                    }
                                }
                            }
                        }

                        // ========== SEARCH IN TABLE FIELDS ==========
                        if ($columns) {
                            if ($group_start) {
                                $this->or_group_start();
                            } else {
                                $this->group_start();
                            }

                            foreach ($columns as $key => $val) {
                                // Add table prefix to prevent ambiguous columns
                                if (strpos($val, '.') === false) {
                                    $val = $this->_table . '.' . $val;
                                }

                                $this->_prepare(($key ? 'or_like' : 'like'), [$val, $search_query]);
                            }

                            $this->group_end();
                        }

                        // ========== SEARCH IN SELECT FIELDS ==========
                        if ($this->_select) {
                            $compiled_like = [];
                            $search_conditions = [];
                            $order_by_conditions = [];

                            foreach ($this->_select as $key => $val) {
                                if (! $val) {
                                    continue;
                                }

                                // Remove AS alias
                                $original_val = $val;
                                if (stripos($val, ' AS ') !== false) {
                                    $val = trim(substr($val, 0, stripos($val, ' AS ')));
                                }

                                // Get field name without table prefix
                                $field_origin = (strpos($val, '.') !== false ? substr($val, strpos($val, '.') + 1) : $val);

                                // Skip if already processed or first item
                                if (! $key || in_array($field_origin, $compiled_like)) {
                                    continue;
                                }

                                // Validate field exists in database
                                $table_name = null;
                                if (isset($this->_set_field[$this->request->getPost('origin')]['parameter'])) {
                                    $param = $this->_set_field[$this->request->getPost('origin')]['parameter'];
                                    $table_name = is_array($param) ? $param[0] : $param;
                                }

                                // Whitelist: Only fields that exist in database
                                $field_for_check = (stripos($val, '.') !== false ? substr($val, strripos($val, '.') + 1) : $val);
                                $is_valid_field = $table_name && $this->model->field_exists($field_for_check, $table_name);

                                // Collect search conditions
                                $search_conditions[] = [
                                    'type' => ($key ? 'or_like' : 'like'),
                                    'field' => $val,
                                    'query' => $search_query
                                ];

                                // Collect order by conditions (only for valid fields)
                                if ($is_valid_field && $is_autocomplete) {
                                    // Use parameter binding or prepared statements
                                    // DON'T directly concatenate user input!
                                    $order_by_conditions[] = $val;
                                }

                                $compiled_like[] = $field_origin;
                            }

                            // Only create group if there are conditions
                            if (! empty($search_conditions)) {
                                if ($group_start) {
                                    $this->or_group_start();
                                } else {
                                    $this->group_start();
                                }

                                foreach ($search_conditions as $condition) {
                                    $this->_prepare($condition['type'], [$condition['field'], $condition['query']]);
                                }

                                $this->group_end();

                                // Add ORDER BY for autocomplete (with safe approach)
                                if (! empty($order_by_conditions) && $is_autocomplete) {
                                    foreach ($order_by_conditions as $order_field) {
                                        // Use query builder that supports parameter binding
                                        // Example with CodeIgniter 4:
                                        $escaped_query = $this->model->escape($search_query);
                                        $this->_prepare('order_by', [
                                            "(CASE
                                                WHEN {$order_field} LIKE {$escaped_query} THEN 1
                                                WHEN {$order_field} LIKE CONCAT({$escaped_query}, '%') THEN 2
                                                WHEN {$order_field} LIKE CONCAT('%', {$escaped_query}) THEN 4
                                                ELSE 3
                                            END)"
                                        ]);
                                    }
                                }
                            }
                        }
                    }

                    if (! $is_autocomplete) {
                        $this->group_end();
                    }
                }
            }

            if ($this->request->isAJAX() && 'autocomplete' == $this->request->getPost('method')) {
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

                if (isset($this->_set_field[$this->request->getPost('origin')]) && in_array('autocomplete', $this->_set_field[$this->request->getPost('origin')]['field_type'])) {
                    // Set the relation table, field and keyword
                    $field = $this->_set_field[$this->request->getPost('origin')];
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
                    $keyword = $this->request->getPost('q');
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
                    if ($order) {
                        if (is_array($order)) {
                            foreach ($order as $key => $val) {
                                $this->model->order_by($key, $val);
                            }
                        } else {
                            $this->model->order_by($order);
                        }
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

                        if (! $key || in_array($field_origin, $compiled_like)) {
                            continue;
                        }

                        // Push like an or like to the prepared query builder
                        $this->_prepare(($key ? 'or_like' : 'like'), [$val, htmlspecialchars(('autocomplete' == $this->request->getPost('method') && $this->request->getPost('q') ? $this->request->getPost('q') : $this->request->getGet('q')))]);

                        if (isset($this->_set_field[$this->request->getPost('origin')]['parameter'])) {
                            if (is_array($this->_set_field[$this->request->getPost('origin')]['parameter'])) {
                                $table = $this->_set_field[$this->request->getPost('origin')]['parameter'][0];
                            } else {
                                $table = $this->_set_field[$this->request->getPost('origin')]['parameter'];
                            }
                        }

                        if (isset($this->_set_field[$this->request->getPost('origin')]['parameter']) && $this->model->field_exists(($val && stripos($val, '.') !== false ? substr($val, strripos($val, '.') + 1) : $val), $table)) {
                            // Push order by best match to the prepared query builder
                            $this->_prepare('order_by', ['(CASE WHEN ' . $val . ' LIKE "' . $this->request->getPost('q') . '%" THEN 1 WHEN ' . $val . ' LIKE "%' . $this->request->getPost('q') . '" THEN 3 ELSE 2 END)']);
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
                            if (($value['original'] && strpos(strtolower($value['original']), strtolower($this->request->getPost('q'))) === false) || in_array($field, $this->_unset_column)) {
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
            } elseif ($this->request->isAJAX() && 'ajax_select' == $this->request->getPost('method') && isset($this->_set_relation[$this->request->getPost('source')])) {
                // Check if data is requested through server side select (jQuery plugin)
                return $this->_get_relation($this->_set_relation[$this->request->getPost('source')], null, true);
            } elseif ($this->request->isAJAX() && 'sort_table' == $this->request->getPost('method')) {
                // Sort table
                return $this->_sort_table($this->request->getPost('ordered_id'));
            }

            if ($this->request->getGet('sort') && 'desc' == strtolower($this->request->getGet('sort'))) {
                // Order ASC from query string
                set_userdata('sortOrder', 'ASC');
            } else {
                // Order DESC from query string
                set_userdata('sortOrder', 'DESC');
            }

            if ($this->request->getGet('order') && $this->model->field_exists($this->request->getGet('order'), $this->_table)) {
                // Match order by the primary table
                // Push order to the prepared query builder
                $this->_prepare[] = [
                    'function' => 'order_by',
                    'arguments' => [$this->_table . '.' . $this->request->getGet('order'), get_userdata('sortOrder')]
                ];
            } elseif ($this->_compiled_table) {
                // Otherwhise, find it from the relation table
                foreach ($this->_compiled_table as $key => $table) {
                    // Validate the column to check if column is exist in table
                    if ($this->request->getGet('order') && $this->model->field_exists($this->request->getGet('order'), $table)) {
                        // Push order to the prepared query builder
                        $this->_prepare[] = [
                            'function' => 'order_by',
                            'arguments' => [$table . '.' . $this->request->getGet('order'), get_userdata('sortOrder')]
                        ];
                    }
                }
            }

            if (in_array($this->_method, ['create'])) {
                // List the field properties
                $results = array_fill_keys(array_keys(array_flip($this->model->list_fields($this->_table))), '');
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
            $description = (isset($this->_set_description[$this->_method]) ? $this->_set_description[$this->_method] : (isset($this->_set_description['index']) ? $this->_set_description['index'] : null));

            // Indicates multiple rows result
            if (is_array($results) && isset($results[0])) {
                // Extract magic string
                preg_match_all('/\{\{(.*?)\}\}/', $title ?? '', $title_replace);
                preg_match_all('/\{\{(.*?)\}\}/', $description ?? '', $description_replace);
                preg_match_all('/\{\{(.*?)\}\}/', $icon ?? '', $icon_replace);

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

                foreach ($icon_replace[1] as $index => $replace) {
                    $replacement = trim($replace);

                    if (isset($results[0]->$replacement)) {
                        // Attempt to convert the magic string and replace with the result
                        $icon = preg_replace("/\{\{(\s+)?($replace)(\s+)?\}\}/", $results[0]->$replacement, $icon);
                    }
                }
            } else {
                // No result found
                if (preg_match_all('/\{\{(.*?)\}\}/', $title)) {
                    // Unset title contains magic string
                    $title = null;
                }

                if (preg_match_all('/\{\{(.*?)\}\}/', $description)) {
                    // Unset description contains magic string
                    $description = null;
                }

                if (preg_match_all('/\{\{(.*?)\}\}/', $icon)) {
                    // Unset icon contains magic string
                    $icon = null;
                }
            }

            if ($this->request->getGet('__fetch_metadata') && $this->api_client) {
                return make_json([
                    'title' => $title,
                    'description' => $description,
                    'icon' => $icon
                ]);
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
                $this->_set_icon = ($this->_set_method || (isset($this->_set_icon[$this->_method])) && $icon ? $icon : 'mdi mdi-plus');

                // Set title property
                $this->_set_title = ($this->_set_method || (isset($this->_set_title[$this->_method])) && $title ? $title : phrase('Add New Data'));

                // Set description property
                $this->_set_description = ($this->_set_method || (isset($this->_set_description[$this->_method])) && $description ? $description : phrase('Please fill all required field below to add new data.'));
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
                $this->_set_icon = ($this->_set_method || (isset($this->_set_icon[$this->_method])) && $icon ? $icon : 'mdi mdi-magnify');

                // Set title property
                $this->_set_title = ($this->_set_method || (isset($this->_set_title[$this->_method])) && $title ? $title : phrase('Showing Data'));

                // Set description property
                $this->_set_description = ($this->_set_method || (isset($this->_set_description[$this->_method])) && $description ? $description : phrase('Showing the result of requested data.'));
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
                $this->_set_icon = ($this->_set_method || (isset($this->_set_icon[$this->_method])) && $icon ? $icon : 'mdi mdi-square-edit-outline');

                // Set title property
                $this->_set_title = ($this->_set_method || (isset($this->_set_title[$this->_method])) && $title ? $title : phrase('Update Data'));

                // Set description property
                $this->_set_description = ($this->_set_method || (isset($this->_set_description[$this->_method])) && $description ? $description : phrase('Make sure to check the changes before submitting.'));
            } elseif (in_array($this->_method, ['export', 'print', 'pdf'])) {
                /**
                 * -------------------------------------------------------------
                 * Method is requesting document file or print
                 * -------------------------------------------------------------
                 */
                $query_params = $this->request->getGet();
                $single_print = false;

                if ($this->_set_primary) {
                    foreach ($this->_set_primary as $key => $val) {
                        // Find single item print
                        if (isset($query_params[$val])) {
                            $single_print = true;

                            break;
                        }
                    }
                }

                // Set view template property
                $this->_view = (isset($this->_set_template[$this->_method]) ? $this->_set_template[$this->_method] : $this->_method);

                // Get formatted results
                $results = ($single_print ? $this->render_read($results) : $this->render_table($results));

                // Set icon property
                $this->_set_icon = ($icon ? $icon : ($this->_set_title_fallback ?? 'mdi mdi-table'));

                // Set title property
                $this->_set_title = ($title ? $title : ($this->_set_title_fallback ?? phrase('Page not found!')));

                // Set description property
                $this->_set_description = ($description ? $description : $this->_set_description_fallback);
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
                $this->_set_icon = ($icon ? $icon : ($this->_set_title_fallback ?? 'mdi mdi-table'));

                // Set title property
                $this->_set_title = ($title ? $title : ($this->_set_title_fallback ?? phrase('Page not found!')));

                // Set description property
                $this->_set_description = ($description ? $description : $this->_set_description_fallback);
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

            if ($this->request->getGet('__fetch_metadata') && $this->api_client) {
                return make_json([
                    'title' => $this->_set_title,
                    'description' => $this->_set_description,
                    'icon' => $this->_set_icon
                ]);
            }
        }

        if ($this->api_client && 'complete' === $this->request->getGet('format_result')) {
            // Requested from API Client in formatted result
            return make_json($results);
        }

        // Get query string
        $query_params = $this->request->getGet();

        foreach ($query_params as $key => $val) {
            if (in_array($this->_method, ['read', 'update']) && in_array($key, $this->_set_primary)) {
                // Remove query parameter from URL
                $query_params[$key] = null;
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

        $uri = Services::uri();
        $timer = Services::timer();

        // Prepare output
        $output = [
            'code' => 200,
            'method' => $this->_method,
            'prefer' => $this->request->getPost('prefer'),
            'meta' => [
                'description' => preg_replace('/[^\S ]+/', '', $this->_set_description ?? ''),
                'icon' => $this->_set_icon,
                'title' => $this->_set_title,
                'modal_size' => ($this->_modal_size ? $this->_modal_size : ''),
                'segmentation' => array_map(function ($segment = null) {return str_replace('.', '-', preg_replace('/[^a-zA-Z0-9]/', '_', $segment));}, $uri->getSegments())
            ],
            'breadcrumb' => $this->template->breadcrumb($this->_set_breadcrumb, $this->_set_title, $this->_set_primary),
            'links' => [
                'base_url' => base_url(),
                'current_module' => go_to(null, $query_params),
                'current_page' => current_page()
            ],
            'query_params' => $this->request->getGet(),
            'results' => $results,
            'total' => $total,
            'elapsed_time' => (float) $timer->has('elapsed_time') ? $timer->getElapsedTime('elapsed_time') : 0.00,
            '_token' => $this->_token
        ];

        if (in_array($this->_method, ['create', 'read', 'update'])) {
            unset($output['total']);
        } else {
            // Add limit
            $output['limit'] = $this->_limit;

            // Add pagination
            $output['pagination'] = $this->template->pagination([
                'limit' => $this->_limit_backup,
                'offset' => $this->_offset,
                'per_page' => $this->_limit,
                'total_rows' => $total,
                'url' => current_page(null, ['per_page' => null])
            ]);
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
                return $this->response->setBody($output)->sendBody();
            } else {
                return $document->generate($output, $title, ('export' == $this->_method ? ($this->request->getGet('method') ?? 'export') : 'embed'));
            }
        } elseif ($this->api_client && ! in_array($this->request->getMethod(), ['GET'])) {
            // The method is requested from REST without GET
            return throw_exception(403, phrase('The method you requested is not acceptable.') . ' (' . $this->request->getMethod() . ')', (! $this->api_client ? $this->_redirect_back : null));
        }

        if ($this->api_client && 'full' === $this->request->getGet('format_result')) {
            // Requested from API Client in full result
            return make_json($output);
        }

        // Send to client
        return $this->template->build($this->_view, $output, $this->_table);
    }

    /**
     * Renders and formats the output data into a structured array ready for table view.
     *
     * Applies column customizations and uses a dedicated Renderer for final table output.
     *
     * @param array $data The raw result data (array of objects/arrays) retrieved from the database.
     *
     * @return array The formatted table data array.
     */
    public function render_table(array $data): array
    {
        // If Primary Key is not defined, disable Update and Delete actions for safety.
        if (! $this->_set_primary) {
            $this->_unset_method = array_merge($this->_unset_method, ['update', 'delete']);
        }

        // Serialize data (convert raw objects/arrays into a standardized format)
        $serialized = $this->serialize($data);

        $table_data = [];

        if ($serialized) {
            // --- Prepare Properties for Renderer ---

            // Define essential properties needed by the table renderer (whitelisting for abstraction/safety).
            $whitelisted_properties = [
                '_add_button', '_add_dropdown', '_add_toolbar', '_add_filter', '_column_order', '_grid_view',
                '_item_reference', '_merge_content', '_merge_label', '_method', '_parameter', '_select',
                '_set_alias', '_set_autocomplete', '_set_button', '_set_field', '_set_relation',
                '_set_upload_path', '_sortable', '_table', '_unset_column', '_unset_clone', '_unset_delete',
                '_unset_method', '_unset_read', '_unset_truncate', '_unset_update', 'api_client', 'model'
            ];

            // Create an array containing only the whitelisted properties from the current object.
            $properties = array_intersect_key(get_object_vars($this), array_flip($whitelisted_properties));

            // Add theme property
            $properties['_set_theme'] = $this->template->theme;

            // --- Load Renderer ---
            $renderer = new Renderer();
            $renderer->setProperty($properties); // Send necessary context properties
            $renderer->setPath('table'); // Specify the renderer path (e.g., table renderer)

            // Run the renderer to format the serialized data into final table structure.
            $table_data = $renderer->render($serialized, count($data));
        }

        return $table_data;
    }

    /**
     * Renders and formats the output data into a structured array ready for form view (Create/Update).
     *
     * Applies field customizations (e.g., placeholder, relation, autocomplete) and uses a dedicated Renderer
     * to generate the final form structure.
     *
     * @param array $data The raw result data (array of objects/arrays) retrieved from the database (usually a single row).
     *
     * @return array The structured form data array containing fields and their properties.
     */
    public function render_form(array|object $data): array
    {
        // --- Initial Validation ---
        // Check if data is empty AND the upsert permission is not granted AND it's not an autocomplete request.
        if (! $data && ! $this->_permit_upsert && 'autocomplete' != $this->request->getPost('method')) {
            return throw_exception(404, phrase('The data you requested does not exist or has been removed.'), $this->_redirect_back);
        }

        // Serialize data (convert raw objects/arrays into a standardized format)
        $serialized = $this->serialize_row($data);

        $field_data = [];

        if ($serialized) {
            // --- Prepare Properties for Renderer (Whitelisting for Abstraction/Safety) ---

            $whitelisted_properties = [
                '_add_class', '_column_order', '_column_size', '_default_value', '_db_driver',
                '_field_append', '_field_prepend', '_field_order', '_view_order', '_extra_submit',
                '_field_position', '_field_size', '_group_field', '_merge_field', '_merge_label',
                '_method', '_modal_size', '_set_alias', '_set_attribute', '_set_autocomplete',
                '_set_field', '_set_heading', '_set_placeholder', '_set_relation', '_set_tooltip',
                '_set_upload_path', '_table', 'api_client', 'model'
            ];

            // Create an array containing only the whitelisted properties from the current object.
            $properties = array_intersect_key(get_object_vars($this), array_flip($whitelisted_properties));

            // Add theme property
            $properties['_set_theme'] = $this->template->theme;

            // --- Load Renderer ---
            $renderer = new Renderer();
            $renderer->setProperty($properties); // Send necessary context properties
            $renderer->setPath('form'); // Specify the renderer path (form renderer)

            // Run the renderer to format the serialized data into final form structure.
            $field_data = $renderer->render($serialized);
        }

        return $field_data;
    }

    /**
     * Renders and formats the output data into a structured array ready for the detailed 'read' view.
     *
     * Applies field customizations and uses a dedicated Renderer for final output structure.
     *
     * @param array $data The raw result data (array of objects/arrays) retrieved from the database (expected to be a single row).
     *
     * @return array The structured field data array containing fields and their formatted values.
     */
    public function render_read(array|object $data): array
    {
        // --- Initial Validation ---
        // If data is empty, throw a 404 exception.
        if (! $data) {
            return throw_exception(404, phrase('The data you requested does not exist or has been removed.'), $this->_redirect_back);
        }

        // Serialize data (convert raw objects/arrays into a standardized format)
        $serialized = $this->serialize_row($data);

        $field_data = [];

        if ($serialized) {
            // --- Prepare Properties for Renderer (Whitelisting for Abstraction/Safety) ---

            $whitelisted_properties = [
                '_column_order', '_column_size', '_field_append', '_field_prepend', '_field_order',
                '_view_order', '_field_position', '_field_size', '_group_field', '_merge_content',
                '_merge_field', '_merge_label', '_method', '_modal_size', '_set_alias',
                '_set_attribute', '_set_field', '_set_heading', '_set_relation', '_set_upload_path',
                '_table', 'api_client'
            ];

            // Create an array containing only the whitelisted properties from the current object.
            $properties = array_intersect_key(get_object_vars($this), array_flip($whitelisted_properties));

            // Add theme property
            $properties['_set_theme'] = $this->template->theme;

            // --- Load Renderer ---
            $renderer = new Renderer();
            $renderer->setProperty($properties); // Send necessary context properties
            $renderer->setPath('view'); // Specify the renderer path (view/read renderer)

            // Run the renderer to format the serialized data into final view structure.
            $field_data = $renderer->render($serialized);
        }

        return $field_data;
    }

    /**
     * Validates the data submitted through the form against predefined rules and field types.
     *
     * If validation passes, it processes the data, handles file uploads, and dispatches
     * the prepared data to either insert_data() or update_data().
     *
     * @param array $data Serialized array data (usually an empty array or a single row for update context).
     *
     * @return object|null Returns an Exception object (400, 403, 404) or triggers a redirect/API response on success.
     */
    public function validate_form(array|object $data)
    {
        // --- 1. Initial Security & Update Check ---
        if ($this->_restrict_on_demo) {
            return throw_exception(403, phrase('This feature is disabled in demo mode.'), $this->_redirect_back);
        }

        // Check if method is update
        if ('update' == $this->_method && ! $this->_where && ! $this->_permit_upsert) {
            // Fail because no primary keyword and insert is restricted
            return throw_exception(404, phrase('The data you would to update is not found.'), (! $this->api_client ? $this->_redirect_back : null));
        }

        // Serialize data (convert raw objects/arrays into a standardized format)
        $serialized = $this->serialize_row($data, false);

        if ($this->request->getPost() && is_array($serialized) && sizeof($serialized) > 0) {
            // Store upload path to session
            set_userdata('_set_upload_path', $this->_set_upload_path);

            // Default validation
            $validation = false;

            foreach ($serialized as $key => $val) {
                $type = array_keys($val['type']);

                // Skip field when it's disabled and has no default value
                if (in_array($key, $this->_unset_field) || isset($this->_set_default[$key]) || array_intersect(['current_timestamp'], $type) || ('create' === $this->_method && array_intersect(['updated_timestamp'], $type)) || ('update' === $this->_method && array_intersect(['created_timestamp'], $type))) {
                    // Skip field from validation
                    continue;
                }

                foreach ($val['validation'] as $index => $callback) {
                    if (! $callback) {
                        // Unset empty validation
                        unset($val['validation'][$index]);

                        continue;
                    }

                    // Validation callback finder
                    if (is_string($callback) && strncmp('callback_', $callback, 9) === 0) {
                        // Validation callback found
                        preg_match('/callback_(.*?)(\[|$)/', $callback, $callback_match);

                        if (isset($callback_match[1]) && method_exists($this, $callback_match[1])) {
                            // Apply callback only when method is exists
                            $val['validation'][$index] = [$this, $callback_match[1]];
                        }
                    }
                }

                if (array_intersect(['image'], $type)) {
                    $validation = true;

                    if (in_array('required', $val['validation']) && isset($_FILES[$key]['error']) && 0 === $_FILES[$key]['error']) {
                        $val['validation'] = array_diff($val['validation'], ['required']);
                    }

                    $val['validation'][] = 'validate_upload[' . $key . '.image]';

                    // Single upload validation rules
                    $this->form_validation->setRule($key, (isset($this->_set_alias[$key]) ? $this->_set_alias[$key] : ucwords(str_replace('_', ' ', $key))), $val['validation']);
                } elseif (array_intersect(['images'], $type)) {
                    $validation = true;

                    if (in_array('required', $val['validation']) && isset($_FILES[$key]['error'][0]) && 0 === $_FILES[$key]['error'][0]) {
                        $val['validation'] = array_diff($val['validation'], ['required']);
                    }

                    $val['validation'][] = 'validate_upload[' . $key . '.image]';

                    // Images upload validation rules
                    $this->form_validation->setRule($key . '.*', (isset($this->_set_alias[$key]) ? $this->_set_alias[$key] : ucwords(str_replace('_', ' ', $key))), $val['validation']);
                } elseif (array_intersect(['file', 'files'], $type)) {
                    $validation = true;

                    if (in_array('required', $val['validation']) && ((isset($_FILES[$key]['error']) && 0 === $_FILES[$key]['error']) || (isset($_FILES[$key]['error'][0]) && 0 === $_FILES[$key]['error'][0]))) {
                        $val['validation'] = array_diff($val['validation'], ['required']);
                    }

                    $val['validation'][] = 'validate_upload[' . $key . ']';

                    // Files upload validation rules
                    $this->form_validation->setRule($key . '.*', (isset($this->_set_alias[$key]) ? $this->_set_alias[$key] : ucwords(str_replace('_', ' ', $key))), $val['validation']);
                } elseif (array_intersect(['carousel'], $type)) {
                    $validation = true;

                    $val['validation'][] = 'validate_upload[' . $key . '.image]';

                    // Carousel upload validation rules
                    $this->form_validation->setRule($key . '.background.*', (isset($this->_set_alias[$key]) ? $this->_set_alias[$key] : ucwords(str_replace('_', ' ', $key))), $val['validation']);
                } elseif (array_intersect(['accordion'], $type)) {
                    $validation = true;

                    $val['validation'][] = 'required';

                    // Accordion upload validation rules
                    $this->form_validation->setRule($key . '.title.*', phrase('Accordion Title') . ' ' . (isset($this->_set_alias[$key]) ? $this->_set_alias[$key] : ucwords(str_replace('_', ' ', $key))), $val['validation']);
                    $this->form_validation->setRule($key . '.body.*', phrase('Accordion Body') . ' ' . (isset($this->_set_alias[$key]) ? $this->_set_alias[$key] : ucwords(str_replace('_', ' ', $key))), $val['validation']);
                } elseif (array_intersect(['password'], $type)) {
                    $validation = true;

                    // Password validation only when post field has value
                    if ($this->request->getPost($key)) {
                        // Password validation rules
                        $this->form_validation->setRule($key, (isset($this->_set_alias[$key]) ? $this->_set_alias[$key] : ucwords(str_replace('_', ' ', $key))), 'min_length[6]');
                        $this->form_validation->setRule($key . '_confirmation', phrase('Confirmation') . ' ' . (isset($this->_set_alias[$key]) ? $this->_set_alias[$key] : ucwords(str_replace('_', ' ', $key))), ('create' == $this->_method ? 'required|matches[' . $key . ']' : 'matches[' . $key . ']'));
                    }
                } elseif (array_intersect(['encryption'], $type) && $val['validation']) {
                    $validation = true;

                    // Encryption type field validation
                    if ('update' == $this->_method) {
                        // Rules on update method
                        $val['validation'] = array_diff($val['validation'], ['required']);
                    }

                    if ($val['validation']) {
                        // Encryption validation rules
                        $this->form_validation->setRule($key, (isset($this->_set_alias[$key]) ? $this->_set_alias[$key] : ucwords(str_replace('_', ' ', $key))), $val['validation']);
                    }
                } elseif (isset($this->_set_relation[$key])) {
                    $validation = true;

                    // Relation table validation
                    if (in_array('required', $val['validation'])) {
                        // Apply rules only when it's required
                        $this->form_validation->setRule($key, (isset($this->_set_alias[$key]) ? $this->_set_alias[$key] : ucwords(str_replace('_', ' ', $key))), ['required', 'relation_checker[' . (strpos($this->_set_relation[$key]['relation_table'], ' ') !== false ? substr($this->_set_relation[$key]['relation_table'], 0, strpos($this->_set_relation[$key]['relation_table'], ' ')) : $this->_set_relation[$key]['relation_table']) . '.' . $this->_set_relation[$key]['relation_key'] . ']']);
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
                            $this->form_validation->setRule($key, (isset($this->_set_alias[$key]) ? $this->_set_alias[$key] : ucwords(str_replace('_', ' ', $key))), ['required', 'relation_checker[' . (strpos($this->_set_relation[$key]['relation_table'], ' ') !== false ? substr($this->_set_relation[$key]['relation_table'], 0, strpos($this->_set_relation[$key]['relation_table'], ' ')) : $this->_set_relation[$key]['relation_table']) . '.' . $this->_set_relation[$key]['relation_key'] . ']']);
                        }
                    }
                } else {
                    if (array_intersect(['yearpicker'], $type)) {
                        // Year validation rules
                        $val['validation'][] = 'valid_year';
                    } elseif (array_intersect(['hour', 'date_only'], $type)) {
                        // Hour validation rules
                        $val['validation'][] = 'numeric';
                        $val['validation'][] = 'max_length[2]';
                    } elseif (array_intersect(['date', 'datepicker'], $type)) {
                        // Date (YYYY-MM-DD) validation rules
                        $val['validation'][] = 'valid_date';
                    } elseif (array_intersect(['timestamp', 'datetime', 'datetimepicker'], $type)) {
                        // Full timestamp validation rules
                        $val['validation'][] = 'valid_datetime';
                    }

                    if ($val['validation']) {
                        if (! isset($this->_set_default[$key])) {
                            // Validate only when no default set to field
                            $validation = true;

                            if (is_array($this->request->getPost($key))) {
                                // Array validation rules
                                $this->form_validation->setRule($key . '.*', (isset($this->_set_alias[$key]) ? $this->_set_alias[$key] : ucwords(str_replace('_', ' ', $key))), $val['validation']);
                            } else {
                                // Input validation rules
                                $this->form_validation->setRule($key, (isset($this->_set_alias[$key]) ? $this->_set_alias[$key] : ucwords(str_replace('_', ' ', $key))), $val['validation']);
                            }
                        } else {
                            // Validate only when no default set to field
                            $validation = true;

                            // Apply rules suffix
                            $this->form_validation->setRule($key, (isset($this->_set_alias[$key]) ? $this->_set_alias[$key] : ucwords(str_replace('_', ' ', $key))), $val['validation']);
                        }
                    }
                }
            }

            // Run validation
            if ($validation && $this->form_validation->run($this->request->getPost()) === false) {
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

            foreach ($serialized as $field => $value) {
                $type = array_keys($value['type']);

                // Skip field when it's disabled and has no default value
                if (
                    (in_array($field, $this->_unset_field) && ! isset($this->_set_default[$field]) && ! array_intersect(['slug', 'current_timestamp', 'created_timestamp', 'updated_timestamp'], $type)) ||
                    (in_array('disabled', $type) && ! isset($this->_set_default[$field])) ||
                    ('create' === $this->_method && array_intersect(['updated_timestamp'], $type)) ||
                    ('update' === $this->_method && array_intersect(['created_timestamp'], $type))
                ) {
                    continue;
                }

                if (array_key_exists($field, $this->request->getPost()) || array_intersect($type, ['current_timestamp', 'created_timestamp', 'updated_timestamp', 'image', 'images', 'file', 'files', 'slug', 'current_user', 'carousel', 'accordion', 'attribution'])) {
                    if (array_intersect(['password'], $type)) {
                        // Check if password changed
                        if ($this->request->getPost($field)) {
                            // Store new password
                            $prepare[$field] = password_hash($this->request->getPost($field) . ENCRYPTION_KEY, PASSWORD_DEFAULT);
                        }

                        // Cloning
                        elseif ($this->_cloning && isset($clone[$field])) {
                            // Clone value
                            $prepare[$field] = $clone[$field];
                        }
                    } elseif (array_intersect(['encryption'], $type)) {
                        // Check if value changed
                        if ($this->request->getPost($field)) {
                            $encrypter = Services::encrypter();

                            // Store new encryption
                            $prepare[$field] = base64_encode($encrypter->encrypt($this->request->getPost($field)));
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
                    } elseif (array_intersect(['file'], $type)) {
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
                            } elseif ($this->_cloning && isset($clone[$field])) {
                                // Clone value
                                $prepare[$field] = $clone[$field];
                            }
                        }
                    } elseif (array_intersect(['images', 'files'], $type)) {
                        $files = [];

                        if (is_array($this->request->getPost($field . '_label'))) {
                            // Reverse file attributes to match with newest upload data
                            $files = array_reverse($this->request->getPost($field . '_label'));
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
                        $carousel = $this->request->getPost($field);

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
                        $accordion = $this->request->getPost($field);
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
                        $attribution = $this->request->getPost($field);
                        $items = [];

                        // Check if the submitted attribution is in correct format
                        if ($attribution && isset($attribution['label']) && sizeof($attribution['label']) > 0) {
                            // Loops the submitted attribution
                            foreach ($attribution['label'] as $key => $val) {
                                if (! $val) {
                                    continue;
                                }

                                // Collect the attribution
                                $items[$val] = (isset($attribution['value'][$key]) ? $attribution['value'][$key] : null);
                            }
                        }

                        // Push the json encoded attribution to data preparation
                        $prepare[$field] = json_encode($items);
                    } elseif (array_intersect(['wysiwyg'], $type)) {
                        // Push the boolean field type to data preparation
                        $prepare[$field] = $this->request->getPost($field);
                    } elseif (array_intersect(['boolean'], $type)) {
                        // Push the boolean field type to data preparation
                        $prepare[$field] = $this->request->getPost($field);
                    } elseif (array_intersect(['current_timestamp'], $type) || ('create' === $this->_method && array_intersect(['created_timestamp'], $type)) || ('update' === $this->_method && array_intersect(['updated_timestamp'], $type))) {
                        // Push the current timestamp field type to data preparation
                        $prepare[$field] = date('Y-m-d H:i:s');
                    } elseif (array_intersect(['date', 'datepicker'], $type)) {
                        // Push the date field type to data preparation
                        $prepare[$field] = date('Y-m-d', strtotime($this->request->getPost($field)));
                    } elseif (array_intersect(['datetime', 'datetimepicker'], $type)) {
                        // Push the submitted timestamp field type to data preparation
                        $prepare[$field] = date('Y-m-d H:i:s', strtotime($this->request->getPost($field)));
                    } elseif (array_intersect(['monthpicker'], $type)) {
                        // Push the month field type to data preparation
                        $prepare[$field] = get_userdata('year') . '-' . $this->request->getPost($field) . '-01';
                    } elseif (array_intersect(['money'], $type)) {
                        // Push the money field type to data preparation
                        $value = trim($this->request->getPost($field));
                        $value = str_replace(',', '', $value);
                        $prepare[$field] = $value;
                    } elseif (array_intersect(['number_format'], $type)) {
                        // Push the number format field type to data preparation
                        $value = trim($this->request->getPost($field));
                        $value = str_replace(',', '', $value);
                        $prepare[$field] = $value;
                    } elseif (array_intersect(['slug'], $type)) {
                        // Check if slug has its own post data
                        if ($this->request->getPost($field)) {
                            // Use its own data as slug
                            $title = $this->request->getPost($field);
                        } elseif ($this->request->getPost($value['type']['slug']['parameter'])) {
                            // Or match other field from given parameter
                            $title = $this->request->getPost($value['type']['slug']['parameter']);
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
                        $prepare[$field] = (is_array($this->request->getPost($field)) ? json_encode($this->request->getPost($field)) : $this->request->getPost($field));
                    }

                    // Apply the formatter when not match any given parameter
                    if (! array_intersect(['slug', 'password', 'encryption', 'image', 'images', 'file', 'files'], $type)) {
                        // Use empty value instead of NULL when no data is submitted
                        if (! isset($prepare[$field])) {
                            if (stripos($value['default'], 'null') !== false) {
                                $prepare[$field] = null;
                            } else {
                                $prepare[$field] = '';
                            }
                        }
                    }
                }

                // Check if the field is sets to use the default value
                if (isset($this->_set_default[$field]) && ($this->_set_default[$field] || is_numeric($this->_set_default[$field]))) {
                    // Push the default value to the data preparation
                    $prepare[$field] = $this->_set_default[$field];
                }

                // Or when it's a boolean and no value
                elseif (array_intersect(['boolean'], $type) && ! $this->request->getPost($field) && ! in_array($field, $this->_unset_field)) {
                    // Sets to "0" instead of null
                    $prepare[$field] = 0;
                }

                if (! array_intersect(['wysiwyg', 'encryption'], $type) && isset($prepare[$field])) {
                    $prepare[$field] = str_replace(['<', '>'], ['&lt;', '&gt;'], $prepare[$field]);
                }

                if (isset($prepare[$field]) && ! array_intersect(['encryption'], $type)) {
                    $prepare[$field] = $this->_sanitize_input($prepare[$field]);
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
                return throw_exception(403, phrase('The method you requested is not acceptable.') . ' (' . $this->request->getMethod() . ')', (! $this->api_client ? $this->_redirect_back : null));
            }
        } else {
            // No data are found
            return throw_exception(404, phrase('No data can be executed.'), (! $this->api_client ? $this->_redirect_back : null));
        }
    }

    /**
     * Sets the flag to permit UPSERT (Update or Insert) operations.
     *
     * If enabled, this allows the system to proceed with data submission even if no existing record
     * is found for an 'update' operation, treating it as an 'insert'.
     *
     * @param bool $return TRUE to permit upsert (default), FALSE otherwise.
     *
     * @return static Returns the current object instance (chainable).
     */
    public function permit_upsert(bool $return = true): static
    {
        $this->_permit_upsert = $return;

        return $this;
    }

    /**
     * @hook before_insert
     * Optional method that can be overridden by a derived Controller or Model
     * to execute custom logic, validation, or data manipulation
     * immediately before a new record is inserted (CREATE operation).
     */
    protected function before_insert()
    {
        // Example Use: Setting 'created_at' timestamps or sanitizing user input fields.
    }

    /**
     * @hook after_insert
     * * An optional method that can be overridden by a derived Controller/Model
     * to execute subsequent logic after data has been successfully inserted (CREATE operation).
     *
     * @return void
     */
    protected function after_insert()
    {
        // Example Usage: Updating cache, sending notifications, or queuing a background job.
    }

    /**
     * @hook before_update
     * Optional method that can be overridden by a derived Controller or Model
     * to execute custom logic, validation, or data manipulation
     * immediately before an existing record is updated (UPDATE operation).
     */
    protected function before_update()
    {
        // Example Use: Setting 'updated_at' timestamps or checking for data change conflicts.
    }

    /**
     * @hook after_update
     * * An optional method that can be overridden by a derived Controller/Model
     * to execute subsequent logic after data has been successfully updated (UPDATE operation).
     *
     * @return void
     */
    protected function after_update()
    {
        // Example Usage: Recording change logs, invalidating related cache entries, or updating search indices.
    }

    /**
     * @hook before_delete
     * Optional method that can be overridden by a derived Controller or Model
     * to execute custom logic, validation, or related tasks
     * immediately before a record is permanently deleted (DELETE operation).
     */
    protected function before_delete()
    {
        // Example Use: Checking user permissions or deleting associated files/images.
    }

    /**
     * @hook after_delete
     * * An optional method that can be overridden by a derived Controller/Model
     * to execute subsequent logic after data has been successfully deleted (DELETE operation).
     *
     * @return void
     */
    protected function after_delete()
    {
        // Example Usage: Cleaning up associated files/resources, removing cache entries, or sending deletion logs.
    }

    /**
     * Inserts data into the specified database table.
     *
     * Handles API method validation, before/after insert hooks, file management, and error handling.
     *
     * @param string|null $table The target database table name.
     * @param array $data The data array to be inserted (Field => Value).
     *
     * @return object|null Returns an Exception object for redirection/API response, or null on execution failure.
     */
    public function insert_data(?string $table = null, array $data = []): object|null
    {
        // --- 1. API Method Validation ---
        if ($this->api_client && ! in_array($this->request->getMethod(), ['POST'])) {
            $this->_unlink_files(get_userdata('_uploaded_files'));
            return throw_exception(403, phrase('The method you requested is not acceptable.') . ' (' . $this->request->getMethod() . ')', $this->_redirect_back);
        }

        // --- 2. Table Existence Check and Execution ---
        if ($table && $this->model->table_exists($table)) {
            // --- 3. Before Insert Hook ---
            if (method_exists($this, 'before_insert')) {
                $this->before_insert();
            }

            if ($this->model->insert($table, $data)) {
                // --- 4. Success: Get Insert ID and Cleanup ---

                $auto_increment = true;
                $primary = 0;

                // Special handling for PostgreSQL auto-increment simulation
                if ('Postgre' == $this->_db_driver) {
                    $auto_increment = false;
                    $field_data = $this->model->field_data($table);

                    foreach ($field_data as $val) {
                        if (isset($this->_set_default[$val->name])) {
                            $primary = $this->_set_default[$val->name];
                        }

                        // Check for primary key or PostgreSQL nextval default
                        if (($val->primary_key ?? false) || (isset($val->default) && $val->default && stripos($val->default, 'nextval(') !== false)) {
                            $auto_increment = true;
                        }

                        if ($primary && $auto_increment) {
                            break;
                        }
                    }
                }

                $this->_insert_id = $auto_increment ? $this->model->insert_id() : 0;

                // Cleanup files and tokens
                unset_userdata('_uploaded_files');
                unset_userdata(sha1(current_page() . get_userdata('session_generated') . ENCRYPTION_KEY));

                // --- 5. After Insert Hook ---
                if (method_exists($this, 'after_insert')) {
                    $this->after_insert();
                }

                // Invalidate token by updating timestamp
                set_userdata('token_timestamp', time());
                unset_userdata(sha1(uri_string()));

                // Send success response
                return throw_exception(($this->api_client ? 200 : 301), phrase('The data was successfully submitted.'), $this->_redirect_back);
            } else {
                // --- 6. Failure: Error Handling and Cleanup ---
                $this->_unlink_files(get_userdata('_uploaded_files'));

                $error = $this->model->error();
                $error_message = $error['message'] ?? phrase('Unable to submit your data.');

                // Display detailed error only for Administrator in non-production environments
                if (get_userdata('group_id') == 1 && ENVIRONMENT != 'production') {
                    $final_message = $error_message;
                } else {
                    $final_message = phrase('Unable to submit your data.') . ' ' . phrase('Please try again or contact the system administrator.') . ' ' . phrase('Error code') . ': <b>500 (INSERT)</b>';
                }

                return throw_exception(500, $final_message, $this->_redirect_back);
            }
        } else {
            // --- 7. Failure: Table Not Found ---
            $this->_unlink_files(get_userdata('_uploaded_files'));
            return throw_exception(404, phrase('The selected database table does not exist.'), $this->_redirect_back);
        }
    }

    /**
     * Retrieves the ID generated by the last successful database INSERT query.
     *
     * This value is typically set internally by the framework after calling insert_data().
     *
     * @return int The last inserted ID (0 if not applicable or failed).
     */
    public function insert_id(): int
    {
        return $this->_insert_id;
    }

    /**
     * Updates data in the specified database table based on the provided WHERE condition.
     *
     * If no WHERE condition is provided, it attempts to derive it from URI query parameters.
     * Supports file cleanup, before/after hooks, and optional UPSERT capability.
     *
     * @param string|null $table The target database table name.
     * @param array $data The data array to be updated (Field => New Value).
     * @param array $where Optional explicit WHERE condition array (Field => Value).
     *
     * @return object|bool Returns an Exception object for redirection/API response, TRUE on successful execution flow, or FALSE on internal failure.
     */
    public function update_data(?string $table = null, array $data = [], array $where = []): object|bool
    {
        // --- 1. API Method Validation ---
        if ($this->api_client && ! in_array($this->request->getMethod(), ['POST'])) {
            $this->_unlink_files(get_userdata('_uploaded_files'));
            return throw_exception(403, phrase('The method you requested is not acceptable.') . ' (' . $this->request->getMethod() . ')', $this->_redirect_back);
        }

        // --- 2. Table Existence Check and WHERE Determination ---
        if ($table && $this->model->table_exists($table)) {
            // Determine WHERE condition if not explicitly provided
            if (! $where) {
                $field_exists = array_flip($this->model->list_fields($table));
                $where = array_intersect_key($this->request->getGet(), $field_exists);

                // If WHERE is still missing, data cannot be updated
                if (! $where) {
                    $this->_unlink_files(get_userdata('_uploaded_files'));
                    return throw_exception(404, phrase('The data you would to delete is not found.'), $this->_redirect_back);
                }

                // Validate derived WHERE keys against table fields
                foreach ($where as $key_backup => $val) {
                    $key = (stripos($key_backup, '.') !== false) ? substr($key_backup, stripos($key_backup, '.') + 1) : $key_backup;

                    if (! $this->model->field_exists($key, $table)) {
                        unset($where[$key_backup]);
                    }
                }
            }

            // --- 3. Check if Data Exists ---
            $query = $this->model->get_where($table, $where, 1)->row();

            if ($query) {
                // --- 4. Data Exists: Execute Update ---

                if (method_exists($this, 'before_update')) {
                    $this->before_update();
                }

                // Collect old files for cleanup
                $old_files = [];
                foreach ($query as $field => $value) {
                    if (isset($this->_set_field[$field]['field_type']) && array_intersect($this->_set_field[$field]['field_type'], ['file', 'files', 'image', 'images'])) {
                        $old_files[$field] = $value;
                    }
                }

                // Attempt to update data
                if ($this->model->update($table, $data, $where)) {
                    // Success: Cleanup and Hooks
                    unset_userdata('_uploaded_files');
                    unset_userdata(sha1(current_page() . get_userdata('session_generated') . ENCRYPTION_KEY));
                    $this->_unlink_files($old_files);

                    if (method_exists($this, 'after_update')) {
                        $this->after_update();
                    }

                    // Invalidate token
                    set_userdata('token_timestamp', time());
                    unset_userdata(sha1(uri_string()));

                    // Send success response
                    return throw_exception(($this->api_client ? 200 : 301), phrase('The data was successfully updated.'), $this->_redirect_back);
                } else {
                    // Failure: Error Handling
                    $this->_unlink_files(get_userdata('_uploaded_files'));
                    $error = $this->model->error();

                    if (get_userdata('group_id') == 1 && isset($error['message'])) {
                        return throw_exception(500, $error['message'], $this->_redirect_back);
                    }

                    return throw_exception(500, phrase('Unable to update the data.') . ' ' . phrase('Please try again or contact the system administrator.') . ' ' . phrase('Error code') . ': <b>500 (UPDATE)</b>', $this->_redirect_back);
                }
            } elseif ($this->_permit_upsert) {
                // --- 5. Data Not Found, but UPSERT is Permitted: Insert Instead ---
                $this->insert_data($table, $data);
            } else {
                // --- 6. Data Not Found, UPSERT Not Permitted ---
                $this->_unlink_files(get_userdata('_uploaded_files'));
                return throw_exception(404, phrase('The data you would to update is not found.'), $this->_redirect_back);
            }
        } else {
            // --- 7. Failure: Table Not Found ---
            $this->_unlink_files(get_userdata('_uploaded_files'));
            return throw_exception(404, phrase('The selected database table does not exist.'), $this->_redirect_back);
        }

        // Should return an object/exception/true/false earlier, but keeping original return for safety.
        return false;
    }

    /**
     * Deletes data from the specified database table based on the provided WHERE condition.
     *
     * If no WHERE condition is provided, it attempts to derive it from URI query parameters.
     * Handles API method validation, demo mode restriction, file cleanup, and before/after hooks.
     *
     * @param string|null $table The target database table name.
     * @param array $where Optional explicit WHERE condition array (Field => Value).
     * @param int $limit The maximum number of rows to delete (default: 1 for single delete).
     *
     * @return object|null Returns an Exception object for redirection/API response, or null on execution failure.
     */
    public function delete_data(?string $table = null, array $where = [], int $limit = 1): object|null
    {
        // --- 1. API Method and Demo Mode Validation ---
        if ($this->api_client && ! in_array($this->request->getMethod(), ['DELETE'])) {
            return throw_exception(403, phrase('The method you requested is not acceptable.') . ' (' . $this->request->getMethod() . ')', $this->_redirect_back);
        }

        if ($this->_restrict_on_demo) {
            return throw_exception(403, phrase('This feature is disabled in demo mode.'), $this->_redirect_back);
        }

        // Check for explicit callback message set by set_messages('delete')
        if (isset($this->_set_messages['delete']) && ($this->_set_messages['delete']['return'] ?? false)) {
            return throw_exception($this->_set_messages['delete']['code'], $this->_set_messages['delete']['messages'], $this->_redirect_back);
        }

        // --- 2. Table Existence Check and WHERE Determination ---
        if ($table && $this->model->table_exists($table)) {
            // Determine WHERE condition if not explicitly provided
            if (! $where) {
                $field_exists = array_flip($this->model->list_fields($table));
                $where = array_intersect_key($this->request->getGet(), $field_exists);

                if (! $where) {
                    return throw_exception(404, phrase('The data you would to delete is not found.'), $this->_redirect_back);
                }

                // Validate derived WHERE keys against table fields
                foreach ($where as $key_backup => $val) {
                    // Extract column name from potential dotted format (e.g., table.column)
                    $key = (stripos($key_backup, '.') !== false) ? substr($key_backup, stripos($key_backup, '.') + 1) : $key_backup;

                    if (! $this->model->field_exists($key, $table)) {
                        unset($where[$key_backup]);
                    }
                }
            }

            // Check if data actually exists before proceeding
            $query = $this->model->get_where($table, $where, 1)->row();

            if ($query) {
                // --- 3. Data Exists: Execute Delete ---
                if (method_exists($this, 'before_delete')) {
                    $this->before_delete();
                }

                // Collect old files for cleanup
                $old_files = [];
                foreach ($query as $field => $value) {
                    if (isset($this->_set_field[$field]['field_type']) && array_intersect($this->_set_field[$field]['field_type'], ['file', 'files', 'image', 'images'])) {
                        $old_files[$field] = $value;
                    }
                }

                // Attempt to delete data
                if ($this->model->delete($table, $where, $limit)) {
                    // Success: Cleanup and Hooks
                    $this->_unlink_files($old_files);

                    if (method_exists($this, 'after_delete')) {
                        $this->after_delete();
                    }

                    // Invalidate token
                    set_userdata('token_timestamp', time());
                    unset_userdata(sha1(uri_string()));

                    // Send success response
                    return throw_exception(($this->api_client ? 200 : 301), phrase('The data was successfully deleted.'), $this->_redirect_back);
                } else {
                    // Failure: Error Handling
                    $error = $this->model->error();

                    if (get_userdata('group_id') == 1 && isset($error['message'])) {
                        return throw_exception(500, $error['message'], $this->_redirect_back);
                    }

                    return throw_exception(500, phrase('Unable to delete the requested data.') . ' ' . phrase('Please try again or contact the system administrator.') . ' ' . phrase('Error code') . ': <b>500 (DELETE)</b>', $this->_redirect_back);
                }
            } else {
                // Data not found (query returned empty)
                return throw_exception(404, phrase('The data you would to delete is not found.'), $this->_redirect_back);
            }
        } else {
            // --- 4. Failure: Table Not Found ---
            return throw_exception(404, phrase('The selected database table does not exist.'), $this->_redirect_back);
        }
    }

    /**
     * Deletes multiple data rows from the database based on a batch of posted items.
     *
     * Each item is processed individually to ensure before/after hooks and file deletion are handled correctly.
     *
     * @param string|null $table The target database table name.
     *
     * @return object|null Returns an Exception object for redirection/API response, or null on execution failure.
     */
    public function delete_batch(?string $table = null): object|null
    {
        // --- 1. API Method and Demo Mode Validation ---
        if ($this->api_client && ! in_array($this->request->getMethod(), ['DELETE'])) {
            return throw_exception(403, phrase('The method you requested is not acceptable.') . ' (' . $this->request->getMethod() . ')', $this->_redirect_back);
        }

        if ($this->_restrict_on_demo) {
            return throw_exception(403, phrase('This feature is disabled in demo mode.'), $this->_redirect_back);
        }

        // --- 2. Get Items and Initialization ---
        $items = $this->request->getPost('items');
        $affected_rows = 0;
        $ignored_rows = 0;
        $total_items = is_array($items) ? count($items) : 0;

        if ($total_items > 0) {
            // Before delete hook (runs once before the batch loop)
            if (method_exists($this, 'before_delete')) {
                $this->before_delete();
            }

            // Whitelist fields that exist in the table
            $field_exists = array_flip($this->model->list_fields($table));

            foreach ($items as $val) {
                // Decode item JSON (which contains the WHERE clause for the specific row)
                $where_condition = json_decode($val, true);

                if (! is_array($where_condition)) {
                    continue; // Skip invalid item format
                }

                // Only keep fields that exist in the table (whitelisting)
                $where_condition = array_intersect_key($where_condition, $field_exists);

                if (! $where_condition) {
                    continue; // Skip if no WHERE clause can be formed
                }

                // --- Check Row Exclusion Rules (if set_unset_delete was used) ---
                $ignore = false;
                if ($this->_unset_delete) {
                    foreach ($this->_unset_delete as $field => $excluded_values) {
                        if (isset($where_condition[$field]) && in_array($where_condition[$field], $excluded_values)) {
                            $ignore = true;
                            break;
                        }
                    }
                }

                if ($ignore) {
                    $ignored_rows++;
                    continue;
                }

                // --- Get Old Data and Files ---
                $query = $this->model->get_where($table, $where_condition, 1)->row();
                $old_files = null;

                if ($query) {
                    // Collect old files for unlink
                    foreach ($query as $field => $value) {
                        if (isset($this->_set_field[$field]['field_type']) && array_intersect($this->_set_field[$field]['field_type'], ['file', 'files', 'image', 'images'])) {
                            $old_files[$field] = $value;
                        }
                    }

                    // --- Execute Single Delete ---
                    if ($this->model->delete($table, $where_condition)) {
                        $this->_unlink_files($old_files);
                        $affected_rows++;
                    }
                }
            }

            // After delete hook (runs once after the batch loop)
            if (method_exists($this, 'after_delete')) {
                $this->after_delete();
            }
        }

        // --- 3. Final Response ---
        if ($affected_rows) {
            // Update token timestamp and invalidate token
            set_userdata('token_timestamp', time());
            unset_userdata(sha1(uri_string()));

            $message = phrase('{{affected_rows}} of {{items}} data was successfully removed.', [
                'affected_rows' => $affected_rows,
                'items' => $total_items
            ]);

            // If some rows were ignored, mention it (optional refinement)
            if ($ignored_rows > 0) {
                $message .= ' ' . phrase('Note: {{ignored_rows}} rows were skipped due to deletion restrictions.', ['ignored_rows' => $ignored_rows]);
            }

            // Deletion success
            return throw_exception(($this->api_client ? 200 : 301), $message, $this->_redirect_back);
        } else {
            // Deletion fail (either no items were processed or all failed/ignored)
            $fail_message = ($total_items > 0 && $ignored_rows == $total_items)
                ? phrase('The selected data cannot be removed due to restrictions.')
                : phrase('Unable to remove the selected data.');

            return throw_exception(403, $fail_message, $this->_redirect_back);
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
     * Possible to use comma separated string or array.
     */
    public function select(string|array $column, bool $escape = true): static
    {
        if (! is_array($column)) {
            // Split selected by comma, but ignore that inside brackets
            $column = array_map('trim', preg_split('/,(?![^(]+\))/', $column));
        }

        foreach ($column as $val) {
            $backup_val = $val;
            $this->_select[] = $val;

            // Clean up the value for internal compiled select list
            $val_clean = $val;
            if (! preg_match('/[.()]/', $val_clean)) {
                $val_clean = substr($val_clean, strpos($val_clean, '.') + 1);
            }
            if (stripos($val_clean, ' AS ') !== false) {
                $val_clean = substr($val_clean, stripos($val_clean, ' AS ') + 4);
            }

            // Only push simple columns (no function calls) to compiled select
            if (strpos($backup_val, '(') === false && strpos($backup_val, ')') === false) {
                $this->_compiled_select[] = $val_clean;
            }
        }

        $this->_prepare(__FUNCTION__, [$column, $escape]);

        return $this;
    }

    /**
     * Select count
     */
    public function select_count(string $column, ?string $alias = null): static
    {
        $this->_prepare(__FUNCTION__, [$column, $alias]);

        return $this;
    }

    /**
     * Select sum
     */
    public function select_sum(string $column, ?string $alias = null): static
    {
        $this->_prepare(__FUNCTION__, [$column, $alias]);

        return $this;
    }

    /**
     * Select minimum
     */
    public function select_min(string $column, ?string $alias = null): static
    {
        $this->_prepare(__FUNCTION__, [$column, $alias]);

        return $this;
    }

    /**
     * Select maximum
     */
    public function select_max(string $column, ?string $alias = null): static
    {
        $this->_prepare(__FUNCTION__, [$column, $alias]);

        return $this;
    }

    /**
     * Select average
     */
    public function select_avg(string $column, ?string $alias = null): static
    {
        $this->_prepare(__FUNCTION__, [$column, $alias]);

        return $this;
    }

    /**
     * Select from subquery
     */
    public function select_subquery(object|string $subquery, string $alias): static
    {
        $this->_prepare(__FUNCTION__, [$subquery, $alias]);

        return $this;
    }

    /**
     * Prevent column to be selected
     */
    public function unset_select(string|array $column): static
    {
        if (! is_array($column)) {
            $column = array_map('trim', preg_split('/,(?![^(]+\))/', $column));
        }

        $this->_unset_select = array_merge($this->_unset_select ?? [], $column);

        return $this;
    }

    /**
     * Distinct field
     */
    public function distinct(bool $flag = true): static
    {
        $this->_distinct = $flag;

        return $this;
    }

    /**
     * Set the primary table
     */
    public function from(string $table): static
    {
        $this->_table = $table;

        return $this;
    }

    /**
     * Select from subquery
     */
    public function from_subquery(object|string $subquery, string $alias): static
    {
        $this->_prepare(__FUNCTION__, [$subquery, $alias]);

        return $this;
    }

    /**
     * Set the primary table (Aliases to "from" method)
     */
    public function table(string $table): static
    {
        $this->_table = $table;

        return $this;
    }

    /**
     * Join table
     */
    public function join(string $table, string $condition, string $type = '', bool $escape = true): static
    {
        if (! in_array($this->_method, ['delete'])) {
            $this->_prepare(__FUNCTION__, [$table, $condition, $type, $escape]);

            // Extract table name without alias
            if (strpos($table, ' ') !== false) {
                $table = substr($table, strrpos($table, ' ') + 1);
            }

            $this->_compiled_table[] = $table;
        }

        return $this;
    }

    /**
     * Where clause
     */
    public function where(string|array $field = [], $value = '', bool $escape = true): static
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
     */
    public function or_where(string|array $field = [], $value = '', bool $escape = true): static
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
     */
    public function where_in(string|array $field = [], $value = '', bool $escape = true): static
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
     */
    public function or_where_in(string|array $field = [], $value = '', bool $escape = true): static
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
     */
    public function where_not_in(string|array $field = [], $value = '', bool $escape = true): static
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
     */
    public function or_where_not_in(string|array $field = [], $value = '', bool $escape = true): static
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
     */
    public function like(string|array $field = [], $match = '', string $side = 'both', bool $escape = true, bool $case_insensitive = true): static
    {
        if (is_array($field)) {
            foreach ($field as $key => $val) {
                $this->_like[$key] = $val;
                $this->_prepare(__FUNCTION__, [$key, $val, $side, $escape, $case_insensitive]);
            }
        } else {
            $this->_like[$field] = $match;
            $this->_prepare(__FUNCTION__, [$field, $match, $side, $escape, $case_insensitive]);
        }

        return $this;
    }

    /**
     * Or like clause
     */
    public function or_like(string|array $field = [], $match = '', string $side = 'both', bool $escape = true, bool $case_insensitive = false): static
    {
        if (is_array($field)) {
            foreach ($field as $key => $val) {
                $this->_prepare(__FUNCTION__, [$key, $val, $side, $escape, $case_insensitive]);
            }
        } else {
            $this->_prepare(__FUNCTION__, [$field, $match, $side, $escape, $case_insensitive]);
        }

        return $this;
    }

    /**
     * Not like clause
     */
    public function not_like(string|array $field = [], $match = '', string $side = 'both', bool $escape = true, bool $case_insensitive = false): static
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
     */
    public function or_not_like(string|array $field = [], $match = '', string $side = 'both', bool $escape = true, bool $case_insensitive = false): static
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
     */
    public function having(string|array $field = [], $value = '', bool $escape = true): static
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
     */
    public function or_having(string|array $field = [], $value = '', bool $escape = true): static
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
     */
    public function having_in(string|array $field = [], $value = '', bool $escape = true): static
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
     */
    public function or_having_in(string|array $field = [], $value = '', bool $escape = true): static
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
     */
    public function having_not_in(string|array $field = [], $value = '', bool $escape = true): static
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
     */
    public function or_having_not_in(string|array $field = [], $value = '', bool $escape = true): static
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
     */
    public function having_like(string|array $field = [], $match = '', string $side = 'both', bool $escape = true, bool $case_insensitive = false): static
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
     */
    public function or_having_like(string|array $field = [], $match = '', string $side = 'both', bool $escape = true, bool $case_insensitive = false): static
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
     */
    public function not_having_like(string|array $field = [], $match = '', string $side = 'both', bool $escape = true, bool $case_insensitive = false): static
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
     */
    public function or_not_having_like(string|array $field = [], $match = '', string $side = 'both', bool $escape = true, bool $case_insensitive = false): static
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
     */
    public function order_by(string|array $field = [], string $direction = '', bool $escape = true): static
    {
        if (! Services::request()->getGet('order')) {
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
    public function group_by(string $column): static
    {
        $this->_prepare(__FUNCTION__, [$column]);

        return $this;
    }

    /**
     * Limit the query result
     */
    public function limit(?int $limit, ?int $offset = null): static
    {
        if (in_array($this->_method, ['create', 'read', 'update', 'delete'])) {
            $this->_limit = 1;
            $this->_offset = 0;
        } else {
            $this->_limit = is_numeric($this->request->getGet('limit')) ? (int)$this->request->getGet('limit') : $limit;
            $this->_offset = is_numeric($this->request->getGet('offset')) ? (int)$this->request->getGet('offset') : $offset;
        }

        $this->_prepare(__FUNCTION__, [$limit, $offset]);

        return $this;
    }

    /**
     * Row offset
     */
    public function offset(int $offset): static
    {
        if (! in_array($this->_method, ['create', 'read', 'update', 'delete'])) {
            $this->_offset = is_numeric($this->request->getGet('offset')) ? (int)$this->request->getGet('offset') : $offset;

            $this->_offset_called = true;
        }

        $this->_prepare(__FUNCTION__, [$offset]);

        return $this;
    }

    /**
     * Starts a new group by adding an opening parenthesis to the WHERE clause
     * of the query.
     */
    public function group_start(): static
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
    public function or_group_start(): static
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
    public function not_group_start(): static
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
    public function or_not_group_start(): static
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
    public function group_end(): static
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
    public function having_group_start(): static
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
    public function or_having_group_start(): static
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
    public function not_having_group_start(): static
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
    public function or_not_having_group_start(): static
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
    public function having_group_end(): static
    {
        if (! in_array($this->_method, ['create', 'update', 'delete'])) {
            $this->_prepare(__FUNCTION__);
        }

        return $this;
    }

    /**
     * Executes the query based on the collected builder parameters ($this->_prepare).
     *
     * This method finalizes the SELECT columns (applying unset rules, aliasing, and table prefixes)
     * and sequentially runs all stored builder methods on the Model's query object.
     *
     * @param string|null $table The primary table to run the query against.
     * @param bool $recycling If TRUE, skips the complex initial SELECT compilation logic (used for counting).
     *
     * @return BaseBuilder|mixed Returns the Query Builder instance ready for execution, or the result of the executed query.
     */
    private function _run_query(?string $table = null, bool $recycling = false): mixed
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
                // Slice unnecessary select
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

                if (! preg_match('/[.<=>()]/', $arguments[0])) {
                    // Add table prefix to field
                    $arguments[0] = $this->_table . '.' . $arguments[0];
                }
            } elseif ('select_subquery' == $function) {
                // Free query builder
                $this->model->reset_query();
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
     * Fetches the data by running the prepared query builder parameters.
     *
     * Handles debugging output (query or results) and executes two queries: one for results
     * (with limit/offset) and one for the total count (recycling the query parameters).
     *
     * @param string|null $table The primary table to run the fetch against.
     *
     * @return array Returns an array containing 'results' (ResultInterface or array) and 'total' (int).
     */
    private function _fetch(?string $table = null): array
    {
        // --- 1. Debugger ---
        if ($this->_debugging) {
            // Run query with limit/offset for debug output
            $query_builder = $this->_run_query($table);

            if (null !== $this->_limit) {
                $query_builder->limit($this->_limit, $this->_offset ?? 0);
            }

            if (in_array($this->_method, ['create', 'read', 'update'])) {
                // Get single row
                $query = $query_builder->row();
            } else {
                // Get multiple rows
                $query = $query_builder->result();
            }

            if ('query' == $this->_debugging) {
                exit(nl2br($this->model->last_query()));
            } else {
                if (ENVIRONMENT === 'production') {
                    exit('<pre>' . print_r($query, true) . '</pre>');
                }
                dd($query);
            }
        }

        // --- 2. Execute Queries ---

        // Query for results (with LIMIT/OFFSET)
        $results_builder = $this->_run_query($table);
        // Apply limit/offset after running the main query builder parameters
        if (null !== $this->_limit) {
            $results_builder->limit($this->_limit, $this->_offset ?? 0);
        }

        if (in_array($this->_method, ['create', 'read', 'update'])) {
            // Get single row
            $results = $results_builder->row();

            // Assign total
            $total = ($results ? 1 : 0);
        } else {
            // Get multiple rows
            $results = $results_builder->result();

            // Query for total count (recycling the prepared parameters but skipping complex SELECT logic)
            $total = $this->_run_query($table, true)->count_all_results();
        }

        // --- 3. Reset and Return ---
        $this->_prepare = []; // Reset preparation property for subsequent queries

        return [
            'results' => $results,
            'total' => $total
        ];
    }

    /**
     * Retrieves related table data for a relational field (e.g., dropdown list for foreign keys).
     *
     * Constructs a complex query based on provided parameters, handles search ('like'),
     * joins, where clauses, and formats the output using magic string replacement.
     *
     * @param array $params   Array containing relation configuration (select, relation_table, limit, join, where, etc.).
     * @param int|string $selected The currently selected value(s) (primary key ID or composite key string).
     * @param bool $ajax       Flag indicating if the request is an AJAX call (for Select2/pagination format).
     *
     * @return array The formatted list of results (for AJAX or standard view).
     */
    private function _get_relation(array $params = [], int|string|null $selected = 0, bool $ajax = false): array|string
    {
        // Use default value if nothing is selected and a default is defined.
        $field_name_for_default = is_array($params['primary_key']) ? end($params['primary_key']) : ($params['primary_key'] ?? null);
        if (! $selected && ($this->_default_value[$field_name_for_default] ?? null)) {
            $selected = $this->_default_value[$field_name_for_default];
        }

        $compiled_select = [];
        $like = [];
        $primary_key = is_array($params['primary_key']) ? end($params['primary_key']) : ($params['primary_key'] ?? null);

        // --- 1. SELECT and LIKE Clause Construction ---
        foreach ($params['select'] as $key => $val) {
            $parts = explode('.', $val);
            $column = $parts[1] ?? $val;
            $table = $parts[0] ?? null;

            // Handle column aliasing to prevent ambiguity if column names clash.
            if (in_array($column, $compiled_select) && $table != $this->_table) {
                $val .= ' AS ' . $column . '_' . $table;
            }

            $this->model->select($val);
            $compiled_select[] = $column;

            // Build LIKE clause for search payload (used in AJAX).
            if ($search = $this->request->getPost('search')) {
                $like_key = (stripos($val, ' AS ') !== false) ? substr($val, 0, stripos($val, ' AS ')) : $val;
                $like[$like_key] = $search;
            }
        }

        // Apply LIKE clauses if present and not retrieving a single selected item.
        if ($like && ! $selected) {
            $this->model->group_start();
            $num = 0;
            foreach ($like as $key => $val) {
                $this->model->{(($num) ? 'or_like' : 'like')}($key, $val, 'both', true, true);
                $num++;
            }
            $this->model->group_end();
        }

        // --- 2. JOIN Clause Construction ---
        if ($params['join']) {
            foreach ($params['join'] as $val) {
                // Ensure join parameters (table and condition) exist.
                if (! isset($val[0], $val[1])) {
                    continue;
                }
                $this->model->join($val[0], $val[1], $val[2] ?? '');
            }
        }

        // --- 3. WHERE Clause Modification for Selected Item ---
        if ($selected) {
            // Find the actual table name (stripping alias if present).
            $relation_table = (strpos($params['relation_table'], ' ') !== false) ? substr($params['relation_table'], strpos($params['relation_table'], ' ') + 1) : $params['relation_table'];

            // Force WHERE clause to retrieve only the selected item.
            $relation_key = $relation_table . '.' . $params['relation_key'];
            $params['where'][$relation_key] = $selected;
            $params['limit'] = 1; // Limit to 1 result.
        }

        // --- 4. Apply Custom WHERE Clauses ---
        if ($params['where']) {
            foreach ($params['where'] as $key => $val) {
                // Complex custom WHERE logic (IN, NOT IN) requiring raw SQL injection (false flag).
                if (is_numeric(strpos($key, ' IN')) || is_numeric(strpos($key, ' NOT IN'))) {
                    $this->model->where($key, $val, false);
                } elseif (is_numeric(strpos($val, ' IN')) || is_numeric(strpos($val, ' NOT IN'))) {
                    $this->model->where($val, null, false);
                } else {
                    $this->model->where($key, $val);
                }
            }
        }

        // Handle WHERE clause for relation key when method is NOT 'create' or 'update'. (Possibly redundant with step 3, but preserved)
        if (! in_array($this->_method, ['create', 'update']) && $selected) {
            $relation_table_name = (strpos($params['relation_table'], ' ') !== false ? substr($params['relation_table'], strpos($params['relation_table'], ' ') + 1) : $params['relation_table']);

            if (is_array($params['relation_key'])) {
                // Composite key handling
                $selected_parts = explode('.', $selected);
                foreach ($params['relation_key'] as $k => $rel_key) {
                    if ($selected_parts[$k] ?? null) {
                        $this->model->where($relation_table_name . '.' . $rel_key, $selected_parts[$k]);
                    }
                }
            } else {
                $this->model->where($relation_table_name . '.' . $params['relation_key'], $selected);
            }
        }

        // --- 5. Apply ORDER BY and GROUP BY ---
        if ($params['order_by'] && ! $selected) {
            if (is_array($params['order_by'])) {
                foreach ($params['order_by'] as $key => $val) {
                    $this->model->order_by($key, $val);
                }
            } else {
                $this->model->order_by($params['order_by']);
            }
        }

        if ($params['join'] && $params['group_by'] && ! $selected) {
            $this->model->group_by($params['group_by']);
        }

        // --- 6. Initialize Output Array ---
        $output = [];
        if (! $selected) {
            if ($ajax) {
                // AJAX (Select2): add "None" option if it's the first page
                if ($this->request->getPost('page') <= 1) {
                    $output[] = ['id' => 0, 'text' => phrase('None')];
                }
            } else {
                // Standard dropdown: add "None" option
                $output[] = ['value' => 0, 'label' => phrase('None'), 'selected' => false];
            }
        }

        // --- 7. Run Query and Format Results ---
        $query = $this->model->get($params['relation_table'], $params['limit'], $params['offset'])->result();

        if ($query) {
            foreach ($query as $key => $val) {
                $label = $params['output'];
                $attributes = $this->_set_attribute[$primary_key] ?? '';
                $option_label = $this->_set_option_label[$primary_key] ?? '';

                // Magic string replacement (e.g., {{column_name}})
                foreach ($params['select'] as $magic => $replace) {
                    $replacement = $replace;
                    // Determine the key used in the $val object (stripping alias or table prefix).
                    if (strpos($replace, ' AS ') !== false) {
                        $replacement = substr($replace, strripos($replace, ' AS ') + 4);
                    } elseif (strpos($replace, '.') !== false) {
                        $replacement = substr($replace, strripos($replace, '.') + 1);
                    }

                    if (isset($val->$replacement)) {
                        // Apply custom format (e.g., sprintf for zero leading).
                        if (isset($this->_set_field[$replacement]['sprintf'])) { // Checking 'sprintf' should be against keys, not field_type
                            $val->$replacement = sprintf('%02d', $val->$replacement);
                        }

                        // Replace magic string in label, attributes, and option_label.
                        $pattern = "/\{\{(\s+)?($replace)(\s+)?\}\}/";
                        $label = preg_replace($pattern, $val->$replacement, $label);
                        $attributes = preg_replace($pattern, $val->$replacement, $attributes);
                        $option_label = preg_replace($pattern, $val->$replacement, $option_label);
                    }
                }

                // --- Output Formatting based on Method ---
                if (in_array($this->_method, ['create', 'update'])) {
                    // Formatting for form field (dropdown/select2)

                    // Determine the value/ID for the option
                    $value = $val->$primary_key ?? $val->$params['relation_key'] ?? 0;

                    // Determine the selected status
                    $is_selected = ($value == $selected);

                    if (is_array($params['primary_key'])) {
                        // Composite key value and selected status determination.
                        $value = implode('.', array_map(fn ($k) => $val->$k ?? 0, $params['primary_key']));
                        $is_selected = ($value == $selected);
                    }

                    if ($ajax) {
                        $output[] = ['id' => $value, 'text' => ($params['translate'] ? phrase($label) : $label)];
                    } else {
                        $output[] = ['value' => $value, 'label' => ($params['translate'] ? phrase($label) : $label), 'selected' => $is_selected];
                    }
                } else {
                    // Formatting for read/index view (single label string)
                    $output = ($params['translate'] ? phrase($label) : $label);

                    // If it's a read method, only one label is needed, so return immediately.
                    return $output;
                }
            }
        }

        // --- 8. Final Output Return ---
        if ($ajax) {
            return make_json([
                'results' => $output,
                'pagination' => ['more' => ($output && count($output) >= $params['limit'])]
            ]);
        }

        return $output;
    }

    /**
     * Executes the drag-and-drop sorting of table rows based on the new ordered list of primary keys.
     *
     * It swaps the old order keys (retrieved from the database) with the new positions in the submitted list.
     *
     * @param array $ordered_id Array of primary key values in their new desired order.
     *
     * @return array The JSON response array (status and message).
     */
    private function _sort_table(array $ordered_id = []): array
    {
        // Check if sorting is enabled or if the input format is invalid.
        if (! $this->_sortable || ! is_array($ordered_id)) {
            return make_json([
                'status' => 400,
                'message' => phrase('The order format is invalid.')
            ]);
        }

        // --- 1. Retrieve Original Order Keys ---
        $primary_key_field = $this->_sortable['primary_key'];
        $order_key_field = $this->_sortable['order_key'];

        // Get the existing order keys corresponding to the submitted IDs.
        $query = $this->model->select($primary_key_field)
            ->select($order_key_field)
            ->where_in($primary_key_field, $ordered_id)
            // Order by the original order key to get a clean sequence of old order values.
            ->order_by($order_key_field, 'ASC')
            ->get_where($this->_table, [])
            ->result_array();

        // --- 2. Create New Order Key Sequence ---
        // Extract the original order keys into a simple, indexed array.
        // This array ($new_order) now holds the old order values (e.g., 1, 2, 3, 4, ...)
        // which will be assigned to the new positions.
        $new_order = [];
        foreach ($query as $val) {
            $new_order[] = $val[$order_key_field];
        }

        // --- 3. Apply New Order ---
        // $ordered_id is the list of IDs in their NEW desired position.
        // $new_order is the list of ORIGINAL order keys to be assigned.
        foreach ($ordered_id as $key => $val) {
            // $val is the ID (primary key)
            // $new_order[$key] is the old order key (which represents the new order position)
            $this->model->update(
                $this->_table,
                [
                    $order_key_field => $new_order[$key]
                ],
                [
                    $primary_key_field => $val
                ]
            );
        }

        return make_json([
            'status' => 200,
            'message' => phrase('The data was sorted successfully.')
        ]);
    }

    /**
     * Recursively unlinks uploaded files and their associated thumbnails/icons.
     *
     * Designed to handle nested file paths stored in arrays or JSON strings.
     *
     * @param array $files      An array of file fields/paths to be processed (field_name => path/array).
     * @param string|null $field_name  Internal tracking of the current field name (used for recursive calls).
     * @param array $field_list Internal tracking of file lists for exclusion logic.
     *
     * @return void Returns immediately if the input is not a valid array.
     */
    private function _unlink_files(?array $files = [], ?string $field_name = null, array $field_list = []): void
    {
        foreach ($files ?? [] as $field => $src) {
            // Decode JSON source if necessary
            if (is_json($src)) {
                $src = json_decode($src, true);
            }

            // --- Recursive Call Handling ---
            if (is_array($src)) {
                // Rename field for next condition (used for tracking array paths)
                $new_field_name = $field_name ?? ($field . '_label');

                // Merge field list for exclusion logic
                $field_list[$new_field_name] = array_merge($field_list[$new_field_name] ?? [], $src);

                // Reinitialize function recursively
                $this->_unlink_files($src, $new_field_name, $field_list);

                continue; // Move to the next item once recursion is handled.
            }

            // --- File Unlinking Logic ---

            // Determine the input name used in POST data for exclusion check.
            $input_name = urldecode(http_build_query($field_list));
            $input_name = substr($input_name, 0, strpos($input_name, '='));

            // Define exclusion conditions:
            // 1. Placeholder file should never be deleted.
            // 2. File is marked for preservation in POST data (i.e., the user didn't change it).
            // 3. File upload slot is empty in $_FILES (meaning user didn't upload a new file).
            $file_uploaded_empty = (! is_array($field) && isset($_FILES[$field]['tmp_name']) && empty($_FILES[$field]['tmp_name']));

            if ('placeholder.png' == $src || $this->request->getPost($input_name) || $file_uploaded_empty) {
                continue; // Skip unlink
            }

            // Sanitize input file names to prevent directory traversal.
            $safe_src = basename($src);
            $safe_field = basename((string) $field); // Ensure $field is treated as string

            // Define potential file names to check (source file name and field name).
            $files_to_check = [$safe_src, $safe_field];

            // Define the directories to check (main upload, thumbs, icons).
            $subdirectories = ['', 'thumbs/', 'icons/'];

            // Base upload path for the current module.
            $base_dir = UPLOAD_PATH . '/' . $this->_set_upload_path . '/';

            // Loop through all potential paths and attempt deletion.
            foreach ($subdirectories as $subdir) {
                foreach ($files_to_check as $filename) {
                    $path = $base_dir . $subdir . $filename;

                    if ($filename && is_file($path)) {
                        try {
                            unlink($path);
                        } catch (Throwable $e) {
                            // Safe abstraction: error during unlink (e.g., permissions)
                        }
                    }
                }
            }
        }
    }

    /**
     * Performs handshake and validation between API client and API endpoint.
     *
     * Handles Basic Auth, API Key validation, IP range checks, and session/access token verification.
     *
     * @param string|int $api_key The submitted API Key (expected from X-API-KEY header).
     *
     * @return static Returns the current object instance (chainable) on success, or throws an exception on failure.
     */
    private function _handshake(string|int $api_key = 0): static
    {
        // --- 1. Basic Authentication (Fallback) ---
        // If no access token is provided and API token doesn't match encryption key, check Basic Auth.
        if (! $this->request->getHeaderLine('X-ACCESS-TOKEN') && ENCRYPTION_KEY !== $this->request->getHeaderLine('X-API-TOKEN')) {
            $auth_header = $this->request->getHeaderLine('Authorization');

            if (str_starts_with($auth_header, 'Basic ')) {
                $account = base64_decode(str_ireplace('Basic ', '', $auth_header));
                list($username, $password) = array_pad(explode(':', $account), 2, '');

                if ($username && $password) {
                    $authorize = $this->permission->authorize($username, $password);
                    if (is_bool($authorize) && $authorize) {
                        $this->_api_token = true; // Auth succeeded
                    }
                }
            }
        }

        // Set client header to recognize as an AJAX/API request.
        $this->request->setHeader('X-Requested-With', 'XMLHttpRequest');

        // --- 2. Retrieve REST Client Configuration ---
        $client = $this->model->get_where(
            'app__rest_clients',
            [
                'status' => 1,
                'api_key' => $api_key,
                'valid_until >= ' => date('Y-m-d')
            ],
            1
        )->row();

        // Check if the request is made internally (same app, bypasses client table lookup).
        if (! $client && ENCRYPTION_KEY === $api_key) {
            $client = (object) [
                'ip_range' => $this->request->getServer('SERVER_ADDR'),
                'method' => json_encode([$this->request->getMethod()]),
                'status' => 1
            ];
            $this->_api_token = true;
        }

        // --- 3. Client Validation Checks (Denial Flow) ---
        if (! $client) {
            return throw_exception(403, phrase('Your API Key is not eligible to access the requested module or its already expired.'));
        } elseif (! $client->status) {
            return throw_exception(403, phrase('Your API Key is temporary deactivated.'));
        } elseif (! in_array($this->request->getMethod(), json_decode($client->method, true))) {
            return throw_exception(403, phrase('Your API Key is not eligible to use the method') . ': ' . $this->request->getMethod());
        } elseif ($client->ip_range && (! $this->_ip_in_range($client->ip_range) || $this->request->getIPAddress() != $this->request->getServer('SERVER_ADDR'))) {
            return throw_exception(403, phrase('Your API Client is not permitted to access the requested source.'));
        }

        // --- 4. Session/Access Token Verification ---
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $accessToken = get_userdata('access_token') ?? $this->request->getHeaderLine('X-ACCESS-TOKEN');
        $clientIp = ($this->request->hasHeader('x-forwarded-for') ? $this->request->getHeaderLine('x-forwarded-for') : $this->request->getIPAddress());

        // Retrieve session data using the access token.
        $cookie = $this->model->select('data')->get_where(
            'app__sessions',
            [
                'id' => $accessToken ?? 0,
                'ip_address' => $clientIp ?? 0,
                // Check session expiry based on configured time
                'timestamp >= ' => date('Y-m-d H:i:s', (time() - config('Session')->expiration))
            ],
            1
        )->row('data');

        // Handle PostgreSQL specific bytea un-escaping.
        if ($cookie && 'Postgre' === $this->_db_driver) {
            $cookie = pg_unescape_bytea($cookie);
        }

        // Decode and restore session data if valid.
        if ($cookie && session_decode($cookie)) {
            $this->_api_token = true;
            set_userdata(array_filter($_SESSION));
            $this->_set_language(get_userdata('language_id'));
        }

        // --- 5. Update Session Expiration ---
        if ($accessToken) {
            $this->model->update(
                'app__sessions',
                [
                    'data' => session_encode(),
                    'timestamp' => date('Y-m-d H:i:s')
                ],
                [
                    'id' => $accessToken
                ]
            );
        }

        // Final state update
        $this->api_client = true;

        return $this;
    }

    /**
     * Checks if the client's IP address is present in the specified whitelist range.
     *
     * Supports exact IP match and basic wildcard matching (e.g., 192.168.1.*).
     *
     * @param array|string $whitelist Array of allowed IP addresses/ranges, or a comma-separated string.
     *
     * @return bool Returns TRUE if the client IP is in the whitelist, FALSE otherwise.
     */
    private function _ip_in_range(array|string $whitelist = []): bool
    {
        // Ensure whitelist is an array, converting from a comma-separated string if necessary.
        if (! is_array($whitelist)) {
            $whitelist = array_map('trim', explode(',', $whitelist));
        }

        $clientIp = Services::request()->getServer('REMOTE_ADDR');

        // 1. Check for Exact IP Match
        if (in_array($clientIp, $whitelist)) {
            return true;
        }

        // 2. Check for Wildcard Match
        foreach ($whitelist as $whitelisted_ip) {
            $wildcardPos = strpos($whitelisted_ip, '*');

            if (false !== $wildcardPos) {
                // Check if the beginning part of the client IP matches the non-wildcard part of the whitelisted IP.
                $ip_prefix = substr($whitelisted_ip, 0, $wildcardPos);

                if (str_starts_with($clientIp, $ip_prefix)) {
                    // Check if the whitelisted IP is just the prefix + wildcard (e.g., "192.168.1.*").
                    // The original logic simplified: substr($clientIp, 0, $wildcardPos) . '*' == $whitelisted_ip
                    // We use str_starts_with for clearer comparison.
                    return true;
                }
            }
        }

        // IP not found in the list.
        return false;
    }

    /**
     * Stores the record of a visitor to the log table and updates visit counters.
     *
     * Checks for user agent type, handles IP address retrieval, and calls counter updates/resets.
     *
     * @return bool|void Returns FALSE if the user agent is unidentifiable, otherwise void.
     */
    private function _push_log(): void
    {
        // 1. Check and reset time-based counters first (Daily, Weekly, etc.).
        $this->_auto_reset_counters();

        $userAgent = Services::request()->getUserAgent();
        $user_agent = '';

        // User agent detection
        if ($userAgent->isBrowser()) {
            $user_agent = $userAgent->getBrowser() . ' ' . $userAgent->getVersion();
        } elseif ($userAgent->isRobot()) {
            $user_agent = $userAgent->getRobot();
        } elseif ($userAgent->isMobile()) {
            $user_agent = $userAgent->getMobile();
        }

        // Prepare log data
        $prepare = [
            'ip_address' => ($this->request->hasHeader('x-forwarded-for') ? $this->request->getHeaderLine('x-forwarded-for') : $this->request->getIPAddress()),
            'browser' => $user_agent,
            'platform' => $userAgent->getPlatform(),
            'timestamp' => date('Y-m-d H:i:s')
        ];

        // 2. Check for existing visitor log by IP address.
        $query = $this->model->get_where('app__log_visitors', ['ip_address' => $prepare['ip_address']], 1)->row();

        if (! $query) {
            // New unique visitor
            try {
                $log_insert = $this->model->insert('app__log_visitors', $prepare);

                if (! $log_insert) {
                    // Trap suspicious access if insertion fails.
                    file_put_contents(WRITEPATH . 'logs/log-' . date('Y-m-d') . '.txt', current_page() . PHP_EOL . json_encode($prepare) . PHP_EOL, FILE_APPEND | LOCK_EX);
                } else {
                    // Update all counters for a new unique visitor (including whole/total visits).
                    $this->_update_visit_counters(['daily', 'weekly', 'monthly', 'yearly', 'whole']);
                }
            } catch (Throwable $e) {
                // Safe abstraction (logging the error can be added here)
            }
        } else {
            // Check if the visitor's last visit was in a different time period.
            $this->_update_visit_counters_if_needed($query);
        }
    }

    /**
     * Increments the visit counters in the app__stats table for the specified periods.
     *
     * @param array $periods Array of period strings (e.g., ['daily', 'weekly']).
     */
    private function _update_visit_counters(array $periods = []): void
    {
        if (empty($periods)) {
            return;
        }

        // Build the SQL increment query for each specified period.
        foreach ($periods as $period) {
            $field = $period . '_visits';
            // Use SET to construct the increment operation directly in SQL: SET field = field + 1
            $this->model->set($field, "$field + 1", false);
        }

        // Update the app__stats table (typically a single-row table).
        $this->model->update('app__stats');
    }

    /**
     * Updates visit counters (daily, weekly, monthly, yearly) only if the visitor's
     * last visit occurred in a different time period.
     *
     * @param object $previous_visit An object containing the visitor's last visit details (must include 'timestamp' and 'ip_address').
     */
    private function _update_visit_counters_if_needed(object $previous_visit): void
    {
        // Convert timestamps for comparison
        $last_visit = strtotime($previous_visit->timestamp);
        $now = time();

        $periods_to_update = [];

        // Check if different day
        if (date('Y-m-d', $last_visit) !== date('Y-m-d', $now)) {
            $periods_to_update[] = 'daily';
        }

        // Check if different week
        if (date('Y-W', $last_visit) !== date('Y-W', $now)) {
            $periods_to_update[] = 'weekly';
        }

        // Check if different month
        if (date('Y-m', $last_visit) !== date('Y-m', $now)) {
            $periods_to_update[] = 'monthly';
        }

        // Check if different year
        if (date('Y', $last_visit) !== date('Y', $now)) {
            $periods_to_update[] = 'yearly';
        }

        // Update counters if any period changed
        if (! empty($periods_to_update)) {
            // Execute the counter update logic.
            $this->_update_visit_counters($periods_to_update);

            // Update the last visit timestamp for the current visitor.
            $this->model->where('ip_address', $previous_visit->ip_address);
            $this->model->update('app__log_visitors', ['timestamp' => date('Y-m-d H:i:s')]);
        }
    }

    /**
     * Automatically resets visit counters (daily, weekly, monthly, yearly) based on date comparison.
     */
    private function _auto_reset_counters(): void
    {
        // Retrieve the single row statistics data.
        $stats = $this->model->get('app__stats', 1)->row();

        if (! $stats) {
            $initial_data = [
                'daily_visits' => 0,
                'weekly_visits' => 0,
                'monthly_visits' => 0,
                'yearly_visits' => 0,
                'whole_visits' => 0,
                'last_daily_reset' => date('Y-m-d'),
                'last_weekly_reset' => date('Y-m-d'),
                'last_monthly_reset' => date('Y-m-d'),
                'last_yearly_reset' => date('Y-m-d')
            ];

            // Insert record
            $this->model->insert('app__stats', $initial_data);

            // Set default record
            $stats = (object) $initial_data;
        }

        // Current Date Formats
        $today = date('Y-m-d');
        $current_week = date('Y-W'); // Year-Week number format
        $current_month = date('Y-m');
        $current_year = date('Y');

        $updates = [];

        // Reset if the last reset date is not today.
        if (($stats->last_daily_reset ?? null) !== $today) {
            $updates['daily_visits'] = 0;
            $updates['last_daily_reset'] = $today;
        }

        // Check if it's the first run OR if the current week is different from the last reset week.
        $last_weekly_reset_week = ($stats->last_weekly_reset ? date('Y-W', strtotime($stats->last_weekly_reset)) : null);
        if (! $stats->last_weekly_reset || $last_weekly_reset_week !== $current_week) {
            $updates['weekly_visits'] = 0;
            $updates['last_weekly_reset'] = $today;
        }

        // Check if it's the first run OR if the current month is different from the last reset month.
        $last_monthly_reset_month = ($stats->last_monthly_reset ? date('Y-m', strtotime($stats->last_monthly_reset)) : null);
        if (! $stats->last_monthly_reset || $last_monthly_reset_month !== $current_month) {
            $updates['monthly_visits'] = 0;
            $updates['last_monthly_reset'] = $today;
        }

        // Check if it's the first run OR if the current year is different from the last reset year.
        $last_yearly_reset_year = ($stats->last_yearly_reset ? date('Y', strtotime($stats->last_yearly_reset)) : null);
        if (! $stats->last_yearly_reset || $last_yearly_reset_year !== $current_year) {
            $updates['yearly_visits'] = 0;
            $updates['last_yearly_reset'] = $today;
        }

        if (! empty($updates)) {
            // Since app__stats is expected to be a single-row table, no WHERE clause is typically needed.
            $this->model->update('app__stats', $updates);
        }
    }

    /**
     * Sets the application language based on user session, browser preference, or system default.
     *
     * @param string|null $language_id Language ID from the user session (or null if not set).
     */
    private function _set_language(?string $language_id = null): void
    {
        // Check if session language ID is not set.
        if (! get_userdata('language_id') || ! $language_id) {
            // Determine Initial Fallback Language ID
            $app_language = get_setting('app_language');
            $language_id = ($app_language > 0 ? $app_language : 1);

            // Get browser accepted locales (e.g., "en-US,en;q=0.9,id;q=0.8").
            $locales = explode(',', (Services::request()->getServer('HTTP_ACCEPT_LANGUAGE') ?: 'en-us'));

            // Retrieve available and active languages from the database.
            $languages = $this->model->get_where('app__languages', ['status' => 1])->result();

            // Match Browser Locale to Available Languages
            foreach ($languages as $language) {
                $items = array_map('trim', explode(',', strtolower($language->locale))); // Available locales for this language

                foreach ($locales as $loc) {
                    if (in_array(strtolower(trim($loc)), $items)) {
                        $language_id = $language->id;

                        break 2; // Found match, break both loops.
                    }
                }
            }

            // Store the determined language ID in the user session.
            set_userdata('language_id', $language_id);
        }

        // Get the language code (e.g., 'en', 'id') from the determined ID.
        $language_code = $this->model->select('code')
            ->get_where('app__languages', ['id' => $language_id], 1)
            ->row('code');

        // Set language code to internal property.
        $this->_language = $language_code;

        // Check if the corresponding language translation file directory exists.
        if (is_dir(APPPATH . 'Language' . DIRECTORY_SEPARATOR . $language_code)) {
            // Set language code to session (legacy/redundant, but preserved).
            set_userdata('language', $language_code);

            // Set locale to the framework's language service.
            Services::language()->setLocale($language_code);
        }
    }

    /**
     * Prepares the given Query Builder function and arguments into an internal queue.
     *
     * It also maintains a separate list for 'where' clauses.
     *
     * @param string $function The Query Builder method name (e.g., 'where', 'select').
     * @param array $arguments The array of arguments passed to the method.
     */
    private function _prepare(string $function, array $arguments = []): void
    {
        // If the function is 'where', store it separately for easy access/manipulation.
        if ('where' == $function) {
            // Assuming arguments[0] is the field and arguments[1] is the value/condition.
            $this->_where[$arguments[0]] = $arguments[1];
        }

        // Add the function call to the main preparation queue.
        $this->_prepare[] = [
            'function' => $function,
            'arguments' => $arguments
        ];
    }

    /**
     * Removes potentially dangerous HTML tags and event handlers to mitigate XSS risks.
     *
     * Note: This method implements a basic sanitization logic. For robust security,
     * it is recommended to use the framework's built-in XSS filter service or a dedicated HTML Purifier library.
     *
     * @param string $input The raw input string to be sanitized.
     *
     * @return string The sanitized string with harmful elements removed.
     */
    private function _sanitize_input(string $input = ''): string
    {
        // Define an array of tags considered highly dangerous (often block-level or script-related).
        $tagsToRemove = ['applet', 'base', 'basefont', 'body', 'command', 'embed', 'frame', 'frameset', 'head', 'html', 'iframe', 'keygen', 'link', 'meta', 'noframes', 'noscript', 'object', 'param', 'script', 'style', 'title'];

        // Loop through each tag and remove it using a regular expression.
        foreach ($tagsToRemove as $tag) {
            // 1. Remove the tag and its content (e.g., <script>...</script>)
            $input = preg_replace('/<' . $tag . '.*?>.*?<\/' . $tag . '>/is', '', $input);

            // 2. Remove self-closing tags (e.g., <meta />)
            $input = preg_replace('/<' . $tag . '.*?\/?>/is', '', $input);

            // 3. Remove opening tags that might not have a closing counterpart (e.g., <link ...>)
            $input = preg_replace('/<' . $tag . '.*?>/is', '', $input);
        }

        // Remove event handler attributes (e.g., onclick, onerror, etc.)
        // This regex targets any attribute starting with 'on' followed by word characters.
        $input = preg_replace('/\s*(on\w+)\s*=\s*[^>]+/is', '', $input);

        return $input;
    }
}
