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
use CodeIgniter\HTTP\Response;
use Aksara\Laboratory\Traits;
use Aksara\Laboratory\Model;
use Aksara\Laboratory\Template;
use Aksara\Laboratory\Renderer\Renderer;
use Aksara\Laboratory\Services\Theme;
use Aksara\Laboratory\Services\Crud;
use Aksara\Laboratory\Services\Permission;
use Aksara\Libraries\Document;
use ReflectionMethod;
use Throwable;

/**
 * Core Controller for Aksara CMS.
 *
 * @property \CodeIgniter\HTTP\IncomingRequest $request HTTP request instance
 * @property \CodeIgniter\HTTP\Response $response HTTP response instance
 * @property \CodeIgniter\Validation\ValidationInterface $formValidation Form validation service
 * @property \Aksara\Laboratory\Model $model Database model instance
 * @property \Aksara\Laboratory\Services\Permission $permission Permission service
 * @property \Aksara\Laboratory\Services\Theme $themeService Theme service
 * @property \Aksara\Laboratory\Services\Crud $crudService CRUD service
 * @property \CodeIgniter\Session\Session $session Session service
 * @property string $_table Primary table name
 * @property string $_primary Primary key field
 * @property string $_method Current CRUD method
 * @property array $_select Selected fields
 * @property array $_unsetField Fields to unset in forms
 * @property array $_unsetView Fields to unset in views
 * @property array $_unsetColumn Fields to unset in tables
 */
abstract class Core extends Controller
{
    use Traits;

    /**
     * Flag indicating if the request originated from an API Client.
     */
    protected bool $apiClient = false;

    /**
     * Form validation service instance.
     * @var \CodeIgniter\Validation\ValidationInterface
     */
    protected $formValidation;

    /**
     * Model instance for database interaction.
     * @var \Aksara\Laboratory\Model
     */
    protected $model;

    /**
     * Permission object for access control logic.
     * @var \Aksara\Laboratory\Services\Permission
     */
    protected $permission;

    /**
     * Request service instance.
     * @var IncomingRequest|null
     */
    protected $request;

    /**
     * Theme service instance.
     * @var \Aksara\Laboratory\Services\Theme
     */
    protected $themeService;

    /**
     * CRUD service instance.
     * @var \Aksara\Laboratory\Services\Crud
     */
    protected $crudService;

    /**
     * Theme template properties.
     * @var object
     */
    protected $template;

    /**
     * CSRF Token storage.
     */
    private ?string $_token = null;

    /**
     * Flag indicating if the submitted API token is valid.
     */
    private bool $_apiToken = false;

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

        // Initialize Theme service.
        $this->themeService = new Theme();

        // --- Load Dependencies ---
        helper(['url', 'file', 'theme', 'security', 'main', 'string', 'widget']);

        // Load core classes.
        $this->formValidation = Services::validation();
        $this->model = new Model();
        $this->permission = new Permission();
        $this->crudService = new Crud($this);

        // Assign active database driver.
        $this->_dbDriver = $this->model->dbDriver();

        // --- Route Initialization ---
        $router = Services::router();
        $path = ($router->getMatchedRoute()[0] ?? null);
        $this->_method = $router->methodName();

        // Assign the module path.
        $this->_module = ($this->_method && $path && strpos($path, $this->_method) !== false ? preg_replace('/\/' . $this->_method . '$/', '', $path) : $path);

        // Determine and set upload path.
        $controllerName = strtolower(substr(strstr($router->controllerName(), '\Controllers\\'), strlen('\Controllers\\')));
        $uploadPath = array_pad(explode('\\', $controllerName), 2, null);
        $this->_setUploadPath = $uploadPath[1] ?? $uploadPath[0];

        // --- Query Parameter Handling ---

        // Apply URL limit parameter.
        if (is_numeric($this->request->getGet('limit')) && $this->request->getGet('limit')) {
            $this->_limitBackup = $this->_limit;
            $this->_limit = $this->request->getGet('limit');
        }

        // Apply URL offset parameter.
        if (is_numeric($this->request->getGet('offset')) && $this->request->getGet('offset')) {
            $this->_offset = $this->request->getGet('offset');
        }

        // --- Theme Preview Mode ---

        // Check for theme preview mode.
        if ('preview-theme' == $this->request->getGet('aksara_mode') && sha1($this->request->getGet('aksara_theme') . ENCRYPTION_KEY . get_userdata('session_generated')) == $this->request->getGet('integrity_check') && is_dir(ROOTPATH . 'themes/' . $this->request->getGet('aksara_theme'))) {
            $this->themeService->setTheme(strip_tags($this->request->getGet('aksara_theme')));
        }

        // --- API Handshake & Logging ---
        if ($this->request->getHeaderLine('X-API-KEY')) {
            // Perform API handshake.
            $this->_handshake($this->request->getHeaderLine('X-API-KEY'));
        } else {
            // Store access logs.
            $this->_pushLog();
        }

        // Set user language.
        $this->_setLanguage(get_userdata('language_id'));
    }
    /**
     * Magic method to get protected properties from subclasses or Traits.
     */
    public function &__get(string $name): mixed
    {
        if (property_exists($this, $name)) {
            return $this->$name;
        }

        $null = null;
        return $null;
    }

    /**
     * Magic method to set protected properties from subclasses or Traits.
     */
    public function __set(string $name, mixed $value): void
    {
        if (property_exists($this, $name)) {
            $this->$name = $value;
        }
    }

    /**
     * Magic method to call protected methods from subclasses or Traits.
     */
    public function __call(string $name, array $arguments): mixed
    {
        if (method_exists($this, $name)) {
            return call_user_func_array([$this, $name], $arguments);
        }

        return null;
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
        if (method_exists($this, $method) && ! in_array($method, get_class_methods('\Aksara\Laboratory\Core'), true)) {
            // We use reflection to get method visibility
            $ref = new ReflectionMethod($this, $method);

            // This forces 'protected' methods to fall through to the fallback.
            if ($ref->isPublic()) {
                // Ensure it's not a core Aksara method
                call_user_func_array([$this, $method], $params);
            } else {
                // For security reason, redirect to homepage
                return $this->response->redirect(base_url('/'));
            }
        } else {
            /**
             * FALLBACK
             * If the method is Protected, Private, or doesn't exist:
             * We pass the method name as the first parameter to index().
             */
            call_user_func_array([$this, 'index'], array_merge([$method], $params));
        }
    }

    /**
     * Enables debugging mode and sets output format.
     *
     * @param string|null $resultType Output format ('query', 'parameter', etc.).
     *
     * @return static Current object instance (chainable).
     */
    public function debug(?string $resultType = null): static
    {
        $this->_debugging = $resultType;

        return $this;
    }

    /**
     * Applies restriction flag if DEMO_MODE is active.
     *
     * @return static Current object instance (chainable).
     */
    public function restrictOnDemo(): static
    {
        if (DEMO_MODE) {
            $this->_restrictOnDemo = true;
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
    public function databaseConfig(array|string $driver = [], ?string $hostname = null, ?int $port = null, ?string $username = null, ?string $password = null, ?string $database = null): static
    {
        // Use array configuration if provided.
        if (is_array($driver) && isset($driver['driver'], $driver['hostname'], $driver['port'], $driver['username'], $driver['password'], $driver['database'])) {
            $this->model->databaseConfig($driver['driver'], $driver['hostname'], $driver['port'], $driver['username'], $driver['password'], $driver['database']);
        } else {
            // Use individual parameters.
            $this->model->databaseConfig($driver, $hostname, $port, $username, $password, $database);
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
    public function validToken(?string $token = null): bool
    {
        $isPostRequest = Services::request()->getPost();

        // Must be a POST request.
        if ($isPostRequest) {
            // Check URI-based token match.
            if ($token && get_userdata(sha1(uri_string())) === $token) {
                return true;
            }

            // Check Referer-based token match.
            if ($token && sha1(Services::request()->getHeaderLine('Referer') . ENCRYPTION_KEY . get_userdata('session_generated')) === $token) {
                return true;
            }

            // Check API client status (bypasses token check).
            if ($this->apiClient) {
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
    protected function ignoreQueryString(array|string $keys): static
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
    public function parentModule(string $module): static
    {
        $this->_module = $module;

        return $this;
    }

    /**
     * Sets module access permission and authorization rules.
     *
     * @param array<int>|string $permissiveGroup Allowed group IDs (array or comma-separated string), 0 allows all.
     * @param string|null       $redirect         Redirect URI on denial (not used if exception is thrown).
     *
     * @return static Current object instance (chainable).
     *
     * @throws \Exception Throws exception on permission denial.
     */
    public function setPermission(array|string $permissiveGroup = [], ?string $redirect = null): static|Response
    {
        $this->_setPermission = true;

        if (0 === $permissiveGroup) {
            return $this;
        }

        // Process permissive group string to array.
        if (! empty($permissiveGroup) && ! is_array($permissiveGroup)) {
            $permissiveGroup = array_map('trim', explode(',', $permissiveGroup));
        }

        // Authorization checks (removed complex conditional logic for brevity, maintaining original flow):
        if (in_array($this->_method, $this->_unsetMethod, true)) {
            return throw_exception(403, phrase('The method you requested is not acceptable.'));
        } elseif ($this->_setPermission && ! get_userdata('is_logged') && ! $this->_apiToken) {
            return throw_exception(403, phrase('Your session has been expired.'));
        } elseif (! $this->permission->allow($this->_module, $this->_method, get_userdata('user_id'), $redirect) && ! $this->_apiToken) {
            return throw_exception(403, phrase('You do not have sufficient privileges to access the requested page.'));
        } elseif ($permissiveGroup && ! in_array((int) get_userdata('group_id'), $permissiveGroup, true) && ! $this->_apiToken) {
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
    public function setMethod(string $method = 'index'): static
    {
        $this->_method = $method;
        $this->_setMethod = true;

        return $this;
    }

    /**
     * Gets the currently set method name.
     *
     * @return string The method name.
     */
    public function getMethod(): string
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
    public function unsetMethod(array|string $params = []): static
    {
        if (! is_array($params)) {
            $params = array_map('trim', explode(',', $params));
        }

        $this->_unsetMethod = array_merge($this->_unsetMethod, $params);

        return $this;
    }

    /**
     * Sets the theme based on predefined configuration.
     *
     * @param string $theme Theme context ('frontend' or 'backend').
     *
     * @return static|bool Current object instance (chainable) or FALSE on invalid theme.
     */
    public function setTheme(string $theme = 'frontend'): static|bool
    {
        if (! in_array($theme, ['frontend', 'backend'], true)) {
            return false;
        }

        $siteId = get_setting('id');

        // Get theme config from site settings.
        $query = $this->model->select($theme . '_theme')->getWhere(
            'app__settings',
            [
                'id' => $siteId
            ],
            1
        )
        ->row($theme . '_theme');

        $this->themeService->setTheme($query);

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
    public function setTemplate(array|string $params = [], ?string $value = null): static
    {
        $this->themeService->setTemplate($params, $value);

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
    public function setBreadcrumb(array|string $params = [], ?string $value = null): static
    {
        $this->themeService->setBreadcrumb($params, $value);

        return $this;
    }

    /**
     * Sets the primary key field(s) for the current CRUD operation.
     *
     * @param array|string $field Field name(s) (array or comma-separated string).
     *
     * @return static Current object instance (chainable).
     */
    public function setPrimary(array|string $field = []): static
    {
        if (! is_array($field)) {
            $field = array_map('trim', explode(',', $field));
        }

        $this->_setPrimary = array_merge($this->_setPrimary, $field);

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
    public function setTitle(array|string $params = [], ?string $fallback = null): static
    {
        $this->themeService->setTitle($params, $fallback);

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
    public function setDescription(array|string $params = [], ?string $fallback = null): static
    {
        $this->themeService->setDescription($params, $fallback);

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
    public function setIcon(array|string $params = [], ?string $fallback = null): static
    {
        $this->themeService->setIcon($params, $fallback);

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
    public function setMessages(array|string $params = [], int $code = 0, ?string $messages = null): static
    {
        if (! is_array($params)) {
            $params = [
                $params => [
                    'code' => $code,
                    'messages' => $messages
                ]
            ];
        }

        $this->_setMessages = array_merge($this->_setMessages, $params);

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
     * @param bool|null   $newTab   Open link in a new tab.
     *
     * @return static Current object instance (chainable).
     */
    public function setButton(
        string $button,
        ?string $value = null,
        ?string $label = null,
        ?string $class = null,
        ?string $icon = null,
        array $parameter = [],
        ?bool $newTab = null
    ): static {
        $this->_setButton[$button] = [
            'url' => $value,
            'label' => $label,
            'icon' => $icon,
            'class' => $class,
            'parameter' => $parameter,
            'new_tab' => $newTab
        ];

        return $this;
    }

    /**
     * Switches the view to a grid layout.
     *
     * @param string $thumbnail Thumbnail image source field.
     * @param string|null $hyperlink URL for the grid item.
     * @param array $parameter URL parameters for the hyperlink.
     * @param bool $newTab Open hyperlink in a new tab.
     *
     * @return static Current object instance (chainable).
     */
    public function gridView(string $thumbnail, ?string $hyperlink = null, array $parameter = [], bool $newTab = false): static
    {
        $_ENV['GRID_VIEW'] = true;

        $this->_gridView = [
            'thumbnail' => $thumbnail,
            'hyperlink' => $hyperlink,
            'parameter' => $parameter,
            'new_tab' => $newTab
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
    public function addFilter(array|string $filter = [], array $options = []): static
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

        $this->_addFilter = array_merge($this->_addFilter, $filter);

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
     * @param string|null $primaryKey The primary key column name for the sortable table.
     * @param string|null $orderKey   The column name used to store the row order index.
     *
     * @return static Current object instance (chainable).
     */
    public function sortable(?string $primaryKey, ?string $orderKey): static
    {
        $this->_sortable = [
            'sort_url' => current_page(),
            'primary_key' => $primaryKey,
            'order_key' => $orderKey
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
     * @param bool $newTab Open link in a new tab.
     * @param string|null $attribution Custom HTML attributes.
     *
     * @return static Current object instance (chainable).
     */
    public function addToolbar(
        ?string $url,
        string $label,
        ?string $class = null,
        ?string $icon = null,
        ?array $parameter = [],
        bool $newTab = false,
        ?string $attribution = null
    ): static {
        if (! is_array($url)) {
            $params = [
                'url' => $url,
                'label' => $label,
                'class' => $class,
                'icon' => $icon,
                'parameter' => $parameter,
                'new_tab' => $newTab,
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
                'new_tab' => (isset($val['new_tab']) ? $val['new_tab'] : $newTab),
                'attribution' => (isset($val['attribution']) ? $val['attribution'] : $attribution)
            ];
        }

        // Merge array and store to property
        $this->_addToolbar = array_merge($this->_addToolbar, $url);

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
     * @param bool $newTab Open link in a new tab.
     * @param string|null $attribution Custom HTML attributes.
     *
     * @return static Current object instance (chainable).
     */
    public function addButton(
        ?string $url,
        string $label,
        ?string $class = null,
        ?string $icon = null,
        ?array $parameter = [],
        bool $newTab = false,
        ?string $attribution = null
    ): static {
        if (! is_array($url)) {
            $params = [
                'url' => $url,
                'label' => $label,
                'class' => $class,
                'icon' => $icon,
                'parameter' => $parameter,
                'new_tab' => $newTab,
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
                'new_tab' => (isset($val['new_tab']) ? $val['new_tab'] : $newTab),
                'attribution' => (isset($val['attribution']) ? $val['attribution'] : $attribution)
            ];
        }

        // Merge array and store to property
        $this->_addButton = array_merge($this->_addButton, $url);

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
     * @param bool $newTab Open link in a new tab.
     * @param string|null $attribution Custom HTML attributes.
     *
     * @return static Current object instance (chainable).
     */
    public function addDropdown(
        ?string $url,
        string $label,
        ?string $class = null,
        ?string $icon = null,
        ?array $parameter = [],
        bool $newTab = false,
        ?string $attribution = null
    ): static {
        if (! is_array($url)) {
            $params = [
                'url' => $url,
                'label' => $label,
                'class' => $class,
                'icon' => $icon,
                'parameter' => $parameter,
                'new_tab' => $newTab,
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
                'new_tab' => (isset($val['new_tab']) ? $val['new_tab'] : $newTab),
                'attribution' => (isset($val['attribution']) ? $val['attribution'] : $attribution)
            ];
        }

        // Merge array and store to property
        $this->_addDropdown = array_merge($this->_addDropdown, $url);

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
    public function addClass(string|array $params = [], ?string $value = null): static
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
        $this->_addClass = array_merge($this->_addClass ?? [], $params);

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
    public function setField(
        string|array $field = [],
        string|array|null $type = null,
        array|string|null $parameter = null,
        mixed $alpha = null,
        mixed $beta = null,
        mixed $charlie = null,
        ?string $delta = null
    ): static {
        // --- 1. Normalize Input to Associative Array [field_name => type_string] ---
        $fieldsToProcess = [];

        if (is_string($field)) {
            // Handle case: set_field('field_name', 'type_string', ...)
            $fieldsToProcess = [$field => $type];
        } elseif (is_array($field)) {
            // Handle case: set_field(['field_name' => 'type_string'], ...)
            $fieldsToProcess = $field;
        }

        // --- 2. Process Each Field and its Type(s) ---
        foreach ($fieldsToProcess as $fieldName => $typeString) {
            if (! $typeString) {
                continue;
            }

            $types = [];

            // Determine if it's a single type or multiple (comma-separated)
            if (is_string($typeString) && strpos($typeString, ',') !== false) {
                $types = array_map('trim', explode(',', $typeString));
            } elseif (is_string($typeString)) {
                $types = [$typeString];
            } else {
                // Skip if type is not a recognizable string
                continue;
            }

            // Loop through each field type (e.g., 'image', 'editable', 'custom_format')
            foreach ($types as $currentType) {
                // Define the structure for the current type, prioritizing dedicated array parameters
                // if the input structure was ['field_name' => ['custom_format' => ['parameter' => '...']]]
                // over the common parameters ($parameter, $alpha, etc.).
                $paramSource = $fieldsToProcess[$currentType] ?? [];

                $this->_setField[$fieldName][$currentType] = [
                    'parameter' => $paramSource['parameter'] ?? $parameter,
                    'alpha' => $paramSource['alpha'] ?? $alpha,
                    'beta' => $paramSource['beta'] ?? $beta,
                    'charlie' => $paramSource['charlie'] ?? $charlie,
                    'delta' => $paramSource['delta'] ?? $delta
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
    public function setTooltip(string|array $params = [], ?string $value = null): static
    {
        if (! is_array($params)) {
            $params = [
                $params => $value
            ];
        }

        $this->_setTooltip = array_merge($this->_setTooltip ?? [], $params);

        return $this;
    }

    /**
     * The function to unset the field from form/update (CREATE/UPDATE methods).
     *
     * @param string|array $params Comma-separated field names or an array of field names.
     */
    public function unsetField(string|array $params = []): static
    {
        if (! is_array($params)) {
            $params = array_map('trim', explode(',', $params));
        }

        $this->_unsetField = array_merge($this->_unsetField ?? [], $params);

        return $this;
    }

    /**
     * The function to unset the column from table view (INDEX/LIST methods).
     *
     * @param string|array $params Comma-separated column names or an array of column names.
     */
    public function unsetColumn(string|array $params = []): static
    {
        if (! is_array($params)) {
            $params = array_map('trim', explode(',', $params));
        }

        $this->_unsetColumn = array_merge($this->_unsetColumn ?? [], $params);

        return $this;
    }

    /**
     * The function to unset the field on view data (READ method).
     *
     * @param string|array $params Comma-separated field names or an array of field names.
     */
    public function unsetView(string|array $params = []): static
    {
        if (! is_array($params)) {
            $params = array_map('trim', explode(',', $params));
        }

        $this->_unsetView = array_merge($this->_unsetView ?? [], $params);

        return $this;
    }

    /**
     * The function to rearrange the columns in the table view.
     *
     * @param string|array $params Comma-separated column names or an array of column names.
     */
    public function columnOrder(string|array $params = []): static
    {
        if (! is_array($params)) {
            $params = array_map('trim', explode(',', $params));
        }

        $this->_columnOrder = array_merge($this->_columnOrder ?? [], $params);

        return $this;
    }

    /**
     * The function to rearrange the field on view data (READ method).
     *
     * @param string|array $params Comma-separated field names or an array of field names.
     */
    public function viewOrder(string|array $params = []): static
    {
        if (! is_array($params)) {
            $params = array_map('trim', explode(',', $params));
        }

        $this->_viewOrder = array_merge($this->_viewOrder ?? [], $params);

        return $this;
    }

    /**
     * The function to rearrange the field in form (CREATE/UPDATE methods).
     *
     * @param string|array $params Comma-separated field names or an array of field names.
     */
    public function fieldOrder(string|array $params = []): static
    {
        if (! is_array($params)) {
            $params = array_map('trim', explode(',', $params));
        }

        $this->_fieldOrder = array_merge($this->_fieldOrder ?? [], $params);

        return $this;
    }

    /**
     * The function to deny reading when primary key is matched with unset value.
     *
     * @param string|array $params Primary key field name or an associative array [pk_field => [value_1, value_2]].
     * @param array $value Array of primary key values to deny (if $params is a field name).
     */
    public function unsetRead(string|array $params = [], array $value = []): static
    {
        if (! is_array($params)) {
            $params = [
                $params => $value
            ];
        }

        $this->_unsetRead = array_merge($this->_unsetRead ?? [], $params);

        return $this;
    }

    /**
     * The function to deny updating when primary key is matched with unset value.
     *
     * @param string|array $params Primary key field name or an associative array [pk_field => [value_1, value_2]].
     * @param array $value Array of primary key values to deny (if $params is a field name).
     */
    public function unsetUpdate(string|array $params = [], array $value = []): static
    {
        if (! is_array($params)) {
            $params = [
                $params => $value
            ];
        }

        $this->_unsetUpdate = array_merge($this->_unsetUpdate ?? [], $params);

        return $this;
    }

    /**
     * The function to deny deleting when primary key is matched with unset value.
     *
     * @param string|array $params Primary key field name or an associative array [pk_field => [value_1, value_2]].
     * @param array $value Array of primary key values to deny (if $params is a field name).
     */
    public function unsetDelete(string|array $params = [], array $value = []): static
    {
        if (! is_array($params)) {
            $params = [
                $params => $value
            ];
        }

        $this->_unsetDelete = array_merge($this->_unsetDelete ?? [], $params);

        return $this;
    }

    /**
     * The function to set default value of form field so user cannot make
     * any changes from input (fixed value).
     *
     * @param string|array $params Field name or an associative array [field_name => default_value].
     * @param mixed|null $value Default value (if $params is a field name).
     */
    public function setDefault(string|array $params = [], mixed $value = null): static
    {
        if (! is_array($params)) {
            $params = [
                $params => $value
            ];
        }

        $this->_setDefault = array_merge($this->_setDefault ?? [], $params);

        return $this;
    }

    /**
     * Add the field to the form validation.
     *
     * @param string|array $params Field name or an associative array [field_name => validation_rules_string|array].
     * @param string|null $value Validation rules string (e.g., 'required|max_length[255]') (if $params is a field name).
     */
    public function setValidation(string|array $params = [], ?string $value = null): static
    {
        if (! is_array($params)) {
            $params = [
                $params => $value
            ];
        }

        // Find existing field validation and merge
        foreach ($params as $key => $val) {
            $valRules = $val;

            if ($valRules && is_string($valRules)) {
                $valRules = array_map('trim', explode('|', $valRules));
            }

            if (isset($this->_setValidation[$key]) && is_array($valRules)) {
                // Merge validation, ensuring the property is initialized
                $this->_setValidation[$key] = array_merge($this->_setValidation[$key] ?? [], $valRules);
            } elseif ($valRules) {
                // Set new validation
                $this->_setValidation[$key] = is_array($valRules) ? $valRules : [$valRules];
            }
        }

        return $this;
    }

    /**
     * Set the upload path to follow the custom path.
     */
    public function setUploadPath(?string $path = null): static
    {
        // Validate the given parameter is a valid path name
        if ($path && preg_match('/^[A-Za-z0-9\-\.\_\/]*$/', $path)) {
            $this->_setUploadPath = strtolower($path);
        }

        return $this;
    }

    /**
     * Create custom callback of form validation.
     */
    public function formCallback(string $callback): static
    {
        $this->_formCallback = $callback;

        return $this;
    }

    /**
     * Set the alias of column/field, the selected column of database table will be translated.
     *
     * @param string|array $params Field name or an associative array [field_name => alias_text].
     * @param string|null $value Alias text (if $params is a field name).
     */
    public function setAlias(string|array $params = [], ?string $value = null): static
    {
        if (! is_array($params)) {
            $params = [
                $params => $value
            ];
        }

        $this->_setAlias = array_merge($this->_setAlias ?? [], $params);

        return $this;
    }

    /**
     * Add heading before field on form or view.
     *
     * @param string|array $params Field name or an associative array [field_name => heading_text].
     * @param string|null $value Heading text (if $params is a field name).
     */
    public function setHeading(string|array $params = [], ?string $value = null): static
    {
        if (! is_array($params)) {
            $params = [
                $params => $value
            ];
        }

        $this->_setHeading = array_merge($this->_setHeading ?? [], $params);

        return $this;
    }

    /**
     * The function to push the additional data to the response (API/View data).
     *
     * @param string|array $params Key or an associative array [key => value].
     * @param mixed $value Value (if $params is a key).
     */
    public function setOutput(string|array $params = [], mixed $value = []): static
    {
        if (! is_array($params)) {
            $params = [
                $params => $value
            ];
        }

        $this->_setOutput = array_merge($this->_setOutput ?? [], $params);

        return $this;
    }

    /**
     * Prevent the field from being truncated in the table view.
     *
     * @param string|array $field Comma-separated field names or an array of field names.
     */
    public function unsetTruncate(string|array $field): static
    {
        if (! is_array($field)) {
            $field = array_map('trim', explode(',', $field));
        }

        $this->_unsetTruncate = array_merge($this->_unsetTruncate ?? [], $field);

        return $this;
    }

    /**
     * Set the width of modal popup will be displayed (e.g., 'modal-xl', 'modal-lg').
     */
    public function modalSize(string $size): static
    {
        $this->_modalSize = strtolower($size);

        return $this;
    }

    /**
     * Arrange the field to a specific position (e.g., 'sidebar', 'column-2').
     *
     * @param string|array $params Field name or an associative array [field_name => position].
     * @param string|null $value Position (if $params is a field name).
     */
    public function fieldPosition(string|array $params = [], ?string $value = null): static
    {
        if (! is_array($params)) {
            $params = [
                $params => $value
            ];
        }

        $this->_fieldPosition = array_merge($this->_fieldPosition ?? [], $params);

        return $this;
    }

    /**
     * Add the custom column size for the table view.
     *
     * @param string|array $params Column name or an associative array [column_name => width_percent].
     * @param string|null $value Width percentage string (e.g., '10%') (if $params is a column name).
     */
    public function columnSize(string|array $params = [], ?string $value = null): static
    {
        if (! is_array($params)) {
            $params = [
                $params => $value
            ];
        }

        // array_replace is used to overwrite existing string keys without losing numeric ones
        $this->_columnSize = array_replace($this->_columnSize ?? [], $params);

        return $this;
    }

    /**
     * Add the custom field size for form input (e.g., 'col-md-6').
     *
     * @param string|array $params Field name or an associative array [field_name => column_class].
     * @param string|null $value Column class (if $params is a field name).
     */
    public function fieldSize(string|array $params = [], ?string $value = null): static
    {
        if (! is_array($params)) {
            $params = [
                $params => $value
            ];
        }

        $this->_fieldSize = array_merge($this->_fieldSize ?? [], $params);

        return $this;
    }

    /**
     * Add the prefix (prepend) content to the field input in the form.
     *
     * @param string|array $params Field name or an associative array [field_name => html_string].
     * @param string|null $value HTML string (if $params is a field name).
     */
    public function fieldPrepend(string|array $params = [], ?string $value = null): static
    {
        if (! is_array($params)) {
            $params = [
                $params => $value
            ];
        }

        $this->_fieldPrepend = array_merge($this->_fieldPrepend ?? [], $params);

        return $this;
    }

    /**
     * Add the suffix (append) content to the field input in the form.
     *
     * @param string|array $params Field name or an associative array [field_name => html_string].
     * @param string|null $value HTML string (if $params is a field name).
     */
    public function fieldAppend(string|array $params = [], ?string $value = null): static
    {
        if (! is_array($params)) {
            $params = [
                $params => $value
            ];
        }

        $this->_fieldAppend = array_merge($this->_fieldAppend ?? [], $params);

        return $this;
    }

    /**
     * Merges multiple data fields into a single column string for List/Read views.
     *
     * The fields to be merged are specified within the $magicString using double curly braces (e.g., "Hello {{first_name}} {{last_name}}").
     *
     * @param string $magicString The template string containing field names wrapped in {{...}}.
     * @param string|null $alias The alias/label for the new merged column.
     * @param string|null $callback Optional callback function name (without 'callback_') to process the merged string.
     *
     * @return static Returns the current object instance (chainable).
     */
    public function mergeContent(string $magicString, ?string $alias = null, ?string $callback = null): static
    {
        // Get the fields from the magic string
        preg_match_all('/\{\{(.*?)\}\}/', $magicString ?? '', $matches);

        $fieldNames = array_map('trim', $matches[1]);
        $primaryField = (isset($fieldNames[0]) ? $fieldNames[0] : null);

        // --- 1. Set Alias/Label ---
        if (! in_array($this->_method, ['create', 'update'], true)) {
            $defaultLabel = ucwords(str_replace('_', ' ', $primaryField));
            $finalAlias = $alias ?? $defaultLabel;

            if ($primaryField) {
                $this->_setAlias[$primaryField] = $finalAlias;
                $this->_mergeLabel[$primaryField] = $finalAlias;
            }
        }

        // --- 2. Sets the Merge Property ---
        if ($primaryField) {
            $this->_mergeContent[$primaryField] = [
                'column' => $fieldNames,
                'parameter' => $magicString,
                'callback' => $callback ? str_replace('callback_', '', $callback) : null
            ];
        }


        // --- 3. Unset Original Columns ---
        if (count($fieldNames) > 1) {
            // Loops the keys starting from the second element (index 1) because the first element
            // is used as the key for the merged column.
            $secondaryFields = array_slice($fieldNames, 1);

            foreach ($secondaryFields as $val) {
                $this->_unsetColumn[] = $val;
                $this->_unsetView[] = $val;
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
    public function mergeField(string|array $params): static
    {
        if (! is_array($params)) {
            // Shorthand possibility, separate with commas
            $params = array_map('trim', explode(',', $params));
        }

        if (count($params) < 2) {
            return $this; // Needs at least a primary field and one merged field
        }

        $primaryField = $params[0];
        $mergedFields = array_slice($params, 1);

        // Merge array and store to property: [primary_field => [field_2, field_3, ...]]
        $this->_mergeField[$primaryField] = $mergedFields;

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
    public function groupField(string|array $params = [], ?string $group = null): static
    {
        if (! is_array($params)) {
            // Shorthand possibility, separate with commas
            $params = array_map('trim', explode(',', $params));
            // Fill the array with the common group name: [field_1 => 'Group Name', field_2 => 'Group Name']
            $params = array_fill_keys($params, $group);
        }

        $this->_groupField = array_merge($this->_groupField ?? [], $params);

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
    public function itemReference(string|array $params = []): static
    {
        if (! is_array($params)) {
            // Shorthand possibility, separate with commas
            $params = array_map('trim', explode(',', $params));
        }

        $this->_itemReference = array_merge($this->_itemReference ?? [], $params);

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
    public function setAttribute(string|array $params = [], ?string $value = null): static
    {
        // Handle single key-value pair and merge if already exists
        if (! is_array($params)) {
            if (isset($this->_setAttribute[$params])) {
                // Already set, append the new value
                $this->_setAttribute[$params] .= ' ' . $value;

                return $this;
            }

            // Convert parameters as array
            $params = [
                $params => $value
            ];
        }

        // Merge array and store to property
        $this->_setAttribute = array_merge($this->_setAttribute ?? [], $params);

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
    public function setPlaceholder(string|array $params = [], ?string $value = null): static
    {
        if (! is_array($params)) {
            // Convert parameters as array
            $params = [
                $params => $value
            ];
        }

        $this->_setPlaceholder = array_merge($this->_setPlaceholder ?? [], $params);

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
    public function setOptionLabel(string|array $params = [], ?string $value = null): static
    {
        if (! is_array($params)) {
            // Convert parameters as array
            $params = [
                $params => $value
            ];
        }

        $this->_setOptionLabel = array_merge($this->_setOptionLabel ?? [], $params);

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
    public function defaultValue(string|array $field = [], mixed $value = null): static
    {
        if (! is_array($field)) {
            // Convert parameters as array
            $field = [
                $field => $value
            ];
        }

        $this->_defaultValue = array_merge($this->_defaultValue ?? [], $field);

        return $this;
    }

    /**
     * Sets the field as a relation field, linking it to another table for display (e.g., dropdowns, autocomplete).
     *
     * This function handles single-key and composite-key relations, manages required SELECT columns,
     * sets up necessary JOINs, and applies relation validation rules.
     *
     * @param string $field The local field name(s) (comma-separated for composite keys).
     * @param string $primaryKey The foreign key in the related table(s) (comma-separated for composite keys).
     * @param string $output The magic string defining the output format (e.g., '{{name}} - {{id}}').
     * @param array $where Optional WHERE conditions for the relation query.
     * @param array $join Optional extra JOIN clauses for the relation query.
     * @param array $orderBy Optional ORDER BY clauses.
     * @param string|null $groupBy Optional GROUP BY clause.
     * @param int $limit Max number of results to fetch (0 uses default limit).
     * @param bool $translate Flag to indicate if the relation field should be translated.
     *
     * @return static Returns the current object instance (chainable).
     */
    public function setRelation(
        string $field,
        string $primaryKey,
        string $output,
        ?array $where = [],
        ?array $join = [],
        ?array $orderBy = [],
        ?string $groupBy = null,
        int $limit = 0,
        bool $translate = false
    ): static {
        // --- 1. Initial Setup and Magic String Extraction ---
        $alias = $field;
        preg_match_all('/\{\{(.*?)\}\}/', $output ?? '', $matches);
        $select = array_map('trim', $matches[1]);

        if ($translate) {
            foreach ($select as $val) {
                $this->_translateField[] = substr(strstr($val, '.'), 1);
            }
        }

        $isComposite = (strpos($field, ',') !== false && strpos($primaryKey, ',') !== false);

        // Default relation parts
        $relationTable = null;
        $relationKeys = [];
        $fieldLocal = [];
        $groupByFields = [];

        // --- 2. Handle Composite Keys vs. Single Key ---
        if ($isComposite) {
            $fieldLocal = array_map('trim', explode(',', $field));
            $primaryKeysForeign = array_map('trim', explode(',', $primaryKey));

            $alias = $fieldLocal[0];
            $groupByFields = [];

            foreach ($primaryKeysForeign as $key => $val) {
                // Ensure the foreign key is selected
                if (! in_array($val, $select, true)) {
                    $select[] = $val;
                    $groupByFields[] = $val;
                }

                // Extract table and key parts
                list($tableName, $keyName) = array_pad(explode('.', $val), 2, null);

                if ($tableName && $keyName) {
                    $relationTable = $tableName;
                    $relationKeys[] = $keyName;
                }

                // Cleanup: Add related columns to unset properties
                $this->_unsetColumn[] = $keyName;
                $this->_unsetView[] = $keyName;

                // Handle masking for composite keys (original logic)
                if (0 == $key) {
                    // The first key is often used as the primary identifier for the merged field.
                    // The original code has complex logic to add an alias_masking column here,
                    // which is highly specific to Aksara's rendering.
                    array_unshift($select, $relationTable . '.' . $fieldLocal[0] . ' AS ' . $alias . '_masking');
                }
            }
        } else {
            // Single Key Relation
            $fieldLocal = $field;

            // Ensure primary key value is selected, alias it using the local field name if simple
            if (! in_array($primaryKey, $select, true)) {
                $select[] = (strpos($primaryKey, ' ') !== false ? substr($primaryKey, strpos($primaryKey, ' ') + 1) : $primaryKey) . ' AS ' . $alias;
            }

            // Merge select from existing attributes (e.g., 'data-image="{{image}}"' attribute)
            if (isset($this->_setAttribute[$field])) {
                preg_match_all('/\{\{(.*?)\}\}/', $this->_setAttribute[$field] ?? '', $matchesAttributes);
                $select = array_merge($select, array_map('trim', $matchesAttributes[1]));
            }

            // Extract relation table and key
            $parts = explode('.', $primaryKey);
            $relationTable = $parts[0] ?? null;
            $relationKeys = $parts[1] ?? null;

            // Cleanup: Add local field to unset properties
            $this->_unsetColumn[] = $field;
            $this->_unsetView[] = $field;
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

        if (! in_array($this->_method, ['create', 'update', 'delete'], true)) {
            $condition = '';
            $relationTableClean = $relationTable;

            // Clean table name if aliased (e.g., 'table t' -> 't')
            if (strpos($relationTable, ' ') !== false) {
                list($baseTable, $relationTableClean) = explode(' ', $relationTable);
            }

            if (is_array($fieldLocal)) {
                // Composite JOIN condition
                foreach ($fieldLocal as $key => $val) {
                    $fkKey = $relationKeys[$key] ?? $val;
                    $condition .= ($condition ? ' AND ' : '') . $relationTableClean . '.' . $fkKey . ' = __PRIMARY_TABLE__.' . $val;

                    // Apply validation for each key
                    $this->setValidation($val, 'relation_checker[' . $relationTableClean . '.' . $fkKey . ']');
                }
            } else {
                // Single JOIN condition
                $condition = $relationTableClean . '.' . $relationKeys . ' = __PRIMARY_TABLE__.' . $fieldLocal;

                // Apply validation for the single key
                $this->setValidation($fieldLocal, 'relation_checker[' . $relationTableClean . '.' . $relationKeys . ']');
            }

            // Add the primary relation table to compilation and JOIN property
            $this->_compiledTable[] = $relationTable;
            $this->_join[$relationTable] = [
                'condition' => $condition,
                'type' => 'LEFT',
                'escape' => true
            ];

            // Add additional JOINs
            if ($join) {
                foreach ($join as $val) {
                    // $val format: [table, condition, type]
                    $this->_compiledTable[] = $val[0];
                    $this->_join[$val[0]] = [
                        'condition' => $val[1],
                        'type' => $val[2] ?? 'LEFT',
                        'escape' => true
                    ];
                }
            }
        }

        // --- 5. Finalize Relation Property ---
        $finalLimit = (is_numeric($limit) && $limit > 0) ? $limit : $this->_limit;

        // Calculate offset for paginated requests (used by AJAX SELECT)
        $offset = (is_numeric(Services::request()->getPost('page')) ? Services::request()->getPost('page') - 1 : 0) * $finalLimit;

        // Add set relation property
        $this->_setRelation[$alias] = [
            'select' => $select,
            'primary_key' => $fieldLocal,
            'relation_table' => $relationTable,
            'relation_key' => $relationKeys,
            'where' => $where,
            'join' => $join,
            'orderBy' => $orderBy,
            'groupBy' => $groupBy ?? (is_array($groupByFields) ? $groupByFields : null),
            'limit' => $finalLimit,
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
     * @param string $selectedValue The foreign key in the related table (e.g., 'table.key_id').
     * @param array $output An array defining the visual output: ['value', 'label', 'description', 'image'].
     * @param array $where Optional WHERE conditions for the autocomplete query.
     * @param array $join Optional extra JOIN clauses.
     * @param array $orderBy Optional ORDER BY clauses.
     * @param string|null $groupBy Optional GROUP BY clause.
     * @param int $limit Max number of suggestions to fetch (0 means no explicit limit).
     *
     * @return static Returns the current object instance (chainable).
     */
    public function setAutocomplete(
        string $field,
        string $selectedValue,
        array $output,
        array $where = [],
        array $join = [],
        array $orderBy = [],
        ?string $groupBy = null,
        int $limit = 0
    ): static {
        // --- 1. Normalize Output and Extract Magic Strings ---
        $value = $output['value'] ?? $output[0] ?? null;
        $label = $output['label'] ?? $output[1] ?? null;
        $description = $output['description'] ?? $output[2] ?? null;
        $image = $output['image'] ?? $output[3] ?? null;

        $selectMagic = $value . $label . $description . $image;

        // Extract all fields wrapped in {{...}} from the output format
        preg_match_all('/\{\{(.*?)\}\}/', $selectMagic ?? '', $matchesSelect);

        $select = $matchesSelect[1] ? array_map('trim', $matchesSelect[1]) : [];

        // Ensure the foreign key is also selected, aliased to the local field name
        $select[] = $selectedValue . ' AS ' . $field;

        list($relationTable, $relationKey) = array_pad(explode('.', $selectedValue), 2, null);

        // --- 2. Configuration Cleanup ---
        if ($join && ! isset($join[0])) {
            $join = [$join]; // Standardize single JOIN array
        }

        if (! $groupBy) {
            $groupBy = $relationTable . '.' . $relationKey;
        }

        // Merge select statements
        $this->_select = array_unique(array_merge($this->_select ?? [], $select));

        // Unset the local field from being displayed as a normal column/view item
        $this->_unsetColumn[] = $field;
        $this->_unsetView[] = $field;

        // --- 3. Define Implicit JOIN (Used for initial display or listing) ---
        $isNotCrud = ! in_array($this->_method, ['create', 'update', 'delete'], true);
        $isAutocompleteRequest = ('autocomplete' == $this->request->getPost('method') && $this->request->getPost('origin'));

        if ($isNotCrud || $isAutocompleteRequest) {
            // Primary JOIN
            $this->_join[$relationTable] = [
                'condition' => $relationTable . '.' . $relationKey . ' = __PRIMARY_TABLE__.' . $field,
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
        $this->_setAutocomplete[$field] = [
            'select' => $select,
            'output' => $output,
            'primary_key' => $field,
            'relation_table' => $relationTable,
            'relation_key' => $relationKey,
            'where' => $where,
            'join' => $join,
            'orderBy' => $orderBy,
            'groupBy' => $groupBy,
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
     * @return array|string The structured, serialized data, or JSON response if requested by API client.
     */
    public function serialize(array $data): array|string
    {
        if (! $data && $this->model->tableExists($this->_table)) {
            // Flip columns
            $data = [array_fill_keys($this->model->listFields($this->_table), '')];
        }

        if ($this->apiClient && (! $this->request->getGet('format_result') || ! in_array($this->request->getGet('format_result'), ['field_data', 'complete', 'full'], true))) {
            // Requested from API Client in unformatted result
            return make_json($data);
        }

        $output = [];

        foreach ($data as $row => $array) {
            // Process single row
            $output[$row] = $this->serializeRow($array, false);
        }

        if ($this->apiClient && 'field_data' === $this->request->getGet('format_result')) {
            // Requested from API Client with field data information
            return make_json($output);
        }

        return $output;
    }

    /**
     * Serializes a single row
     *
     *
     * @return array|string The structured, serialized row data or JSON response if requested by API client.
     */
    public function serializeRow(array|object $data, bool $return = true): array|string
    {
        // Define field data compilation
        $fieldData = $this->model->fieldData($this->_table);

        // Find primary key
        foreach ($fieldData as $key => $val) {
            // Unset indexed field data
            unset($fieldData[$key]);

            // Add properties to field data compilation
            $fieldData[$val->name] = $val;
        }

        $output = [];

        foreach ($data as $field => $value) {
            $hidden = false;

            // Attempt to get the type
            $type = strtolower((isset($fieldData[$field]->type) ? $fieldData[$field]->type : gettype($value)));

            // Reformat type
            if (in_array($type, ['tinyint', 'smallint', 'int', 'mediumint', 'bigint', 'year'], true)) {
                // Field type number
                $type = 'number';
            } elseif (in_array($type, ['decimal', 'float', 'double', 'real'], true)) {
                // Field type decimal
                if (in_array($type, ['percent'], true)) {
                    $type = 'percent';
                } else {
                    $type = 'money';
                }
            } elseif (in_array($type, ['tinytext', 'text'], true)) {
                // Field type textarea
                $type = 'textarea';
            } elseif (in_array($type, ['mediumtext', 'longtext'], true)) {
                // Field type wysiwyg
                $type = 'wysiwyg';
            } elseif (in_array($type, ['date'], true)) {
                // Field type date (Y-m-d)
                $type = 'date';
            } elseif (in_array($type, ['datetime', 'timestamp'], true)) {
                // Field type datetime (Y-m-d H:i:s)
                $type = 'datetime';
            } elseif (in_array($type, ['time'], true)) {
                // Field type time (H:i:s)
                $type = 'time';
            } elseif (in_array($type, ['enum'], true) && in_array($this->_dbDriver, ['MySQLi'], true) && ! isset($this->_setField[$field])) {
                try {
                    // Get enum list
                    $enumQuery = $this->model->query('SELECT COLUMN_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = ? AND COLUMN_NAME = ? AND TABLE_SCHEMA = DATABASE()', [
                        $this->_table,
                        $fieldData[$field]->name
                    ])->row('COLUMN_TYPE');

                    // Extract enum list
                    $enumList = explode(',', str_ireplace(["enum(", ")", "'"], '', $enumQuery));

                    if ($enumList) {
                        $options = [];

                        foreach ($enumList as $_key => $_val) {
                            $options[$_val] = $_val;
                        }

                        $this->_setField[$field]['select'] = [
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

            if (! isset($this->_setField[$field])) {
                if (isset($this->_setRelation[$field])) {
                    $type = 'select';
                }

                // Add new field type
                $this->_setField[$field][$type] = [
                    'parameter' => null,
                    'alpha' => null,
                    'beta' => null,
                    'charlie' => null,
                    'delta' => null
                ];
            }

            // Attempt to get maximum length of column
            $maxlength = (isset($fieldData[$field]->max_length) ? $fieldData[$field]->max_length : null);

            // Attempt to get the field validation
            $validation = (isset($this->_setValidation[$field]) ? $this->_setValidation[$field] : []);

            // Attempt to get field translation
            $content = (in_array($field, $this->_translateField, true) ? phrase($value) : $value);

            if ('create' == $this->_method) {
                $content = (isset($this->_setDefault[$field]) ? $this->_setDefault[$field] : (isset($fieldData[$field]->default) ? $fieldData[$field]->default : null));
                $value = null;
            }

            if (in_array($this->_method, ['create', 'update'], true) && (in_array($field, $this->_unsetField, true) || array_intersect(['current_timestamp', 'created_timestamp', 'updated_timestamp'], array_keys($this->_setField[$field])))) {
                // Indicates that field should not be shown
                $hidden = true;
            } elseif (('read' == $this->_method || (in_array($this->_method, ['print', 'pdf'], true))) && in_array($field, $this->_unsetView, true)) {
                // Indicates that field should not be shown
                $hidden = true;
            } elseif (in_array($this->_method, ['index', 'export', 'print', 'pdf'], true) && in_array($field, $this->_unsetColumn, true)) {
                // Indicates that field should not be shown
                $hidden = true;
            }

            if ($value && isset($this->_setRelation[$field])) {
                // Get relation content
                $content = $this->_getRelation($this->_setRelation[$field], $value);
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

                if (isset($this->_setField[$field]['sprintf']['parameter'])) {
                    $parameter = $this->_setField[$field]['sprintf']['parameter'];
                }

                // Add zero leading
                $content = sprintf(($parameter && ! is_array($parameter) ? $parameter : '%02d'), $content);
            }

            if ($maxlength) {
                if (in_array($type, ['money', 'percent'], true)) {
                    // Add extra dot to maxlength
                    $maxlength = ($maxlength + 1);
                }

                $validation[] = 'max_length[' . $maxlength . ']';
            }

            // Call assigned method of custom format
            if (
                isset($this->_setField[$field])
                && in_array('custom_format', array_keys($this->_setField[$field]), true)
                && method_exists($this, $this->_setField[$field]['custom_format']['parameter'])
            ) {
                if (
                    (in_array($this->_method, ['index'], true) && ! in_array($field, $this->_unsetColumn, true))
                    || (in_array($this->_method, ['create', 'update'], true) && ! in_array($field, $this->_unsetField, true))
                    || (in_array($this->_method, ['read'], true) && ! in_array($field, $this->_unsetView, true))
                ) {
                    // Get callback method
                    $method = $this->_setField[$field]['custom_format']['parameter'];
                    $content = $this->$method((array) $data);

                    // We use reflection to get method visibility
                    $ref = new ReflectionMethod($this, $method);

                    if ($ref->isProtected()) {
                        $content = $this->$method((array) $data);
                    } else {
                        $content = $method . '() must be protected';
                    }
                }
            }

            $output[$field] = [
                'primary' => in_array($field, $this->_setPrimary, true),
                'value' => $value,
                'content' => $content,
                'maxlength' => $maxlength,
                'hidden' => $hidden,
                'type' => $this->_setField[$field],
                'validation' => $validation
            ];

            if ($this->apiClient && $return) {
                $output[$field]['label'] = (isset($this->_setAlias[$field]) ? $this->_setAlias[$field] : ucwords(str_replace('_', ' ', $field) ?? ''));
            }
        }

        if ($this->apiClient && $return) {
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
    public function render(?string $table = null, ?string $view = null): object|array|string|null
    {
        // Debugger
        if (in_array($this->_debugging, ['params', 'parameter'], true)) {
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

        if ($this->apiClient) {
            // Validate API request
            if ($this->_setPermission) {
                if (! get_userdata('access_token') && ! $this->request->getHeaderLine('X-ACCESS-TOKEN')) {
                    // Access token is not set
                    return throw_exception(403, phrase('This service is require an access token.'));
                } elseif (! $this->_apiToken) {
                    // Access token is not valid
                    return throw_exception(403, phrase('The access token is invalid or already expired.'));
                }
            } elseif (in_array($this->request->getMethod(), ['POST', 'DELETE'], true) && ! in_array($this->_method, ['create', 'update', 'delete'], true)) {
                // Check if request is made from promise
                return throw_exception(403, phrase('The method you requested is not acceptable.') . ' (' . $this->request->getMethod() . ')', (! $this->apiClient ? go_to() : null));
            }
        } elseif ($table && ! $this->_setPermission) {
            // Unset database modification because no permission is set
            $this->unsetMethod('create, update, delete');

            if (in_array($this->_method, ['create', 'update', 'delete'], true)) {
                // Throw exception about the method
                return throw_exception(403, phrase('You do not have sufficient privileges to access the requested page.') . ' (' . strtoupper($this->_method). ')', (! $this->apiClient ? go_to() : null));
            }
        }

        if (! $this->_table) {
            // Set table when not present
            $this->_table = $table;
        }

        // Push to compiled table
        $this->_compiledTable[] = $this->_table;
        $this->_compiledTable = array_unique($this->_compiledTable);

        if (! $this->request->getPost('_token')) {
            // Set CSRF Token
            $this->_token = hash_hmac('sha256', uri_string() . get_userdata('session_generated') . get_userdata('token_timestamp'), ENCRYPTION_KEY);

            // There may be a form without using form renderer
            // Set CSRF Token into unique session key
            set_userdata(sha1(uri_string()), $this->_token);
        }

        // Validate the restricted action
        if (in_array($this->_method, $this->_unsetMethod, true)) {
            return throw_exception(403, phrase('You are not allowed to perform the requested action.'), go_to());
        }

        // Check before action
        if ('create' == $this->_method && method_exists($this, 'before_insert')) {
            // Before insert
            $this->beforeInsert();
        } elseif ('update' == $this->_method && method_exists($this, 'before_update')) {
            // Before update
            $this->beforeUpdate();
        } elseif ('delete' == $this->_method && method_exists($this, 'before_delete')) {
            // Before delete
            $this->beforeDelete();
        }

        // Load template class
        $this->template = new Template($this->themeService->get_theme(), $this->_method);

        // Load template parser
        $renderer = new Renderer();

        // Send necessary properties
        $renderer->setProperty(['_set_theme' => $this->template->theme]);

        // Set core component path
        $renderer->setPath('core');

        // Create core component if not exists
        $renderer->render([]);

        // Query string filters
        $queryParams = $this->request->getGet();

        // Token Validation
        if ($queryParams && ENCRYPTION_KEY !== $this->request->getHeaderLine('X-API-KEY')) {
            // Apply validation for protected page from non API client request
            if ($this->_setPermission && ! $this->apiClient) {
                $expectedToken = generate_token(uri_string(), $queryParams);
                $submittedToken = $this->request->getGet('aksara');

                // Unset validated ignored query string
                unset_userdata('__ignored_query_string');

                // Token comparison
                if (! hash_equals((string) $expectedToken, (string) $submittedToken)) {
                    // Token didn't match
                    return throw_exception(403, phrase('The submitted token has expired or the request is made from a restricted source.'));
                }
            }
        }

        // Check if given table is exists in database
        if ($this->_table) {
            // Check if table is exists
            if (! $this->model->tableExists($this->_table)) {
                return throw_exception(404, phrase('The defined primary table does not exist.'), current_page('../'));
            }

            // Define field data compilation
            $fieldData = $this->model->fieldData($this->_table);

            // Find primary key
            foreach ($fieldData as $key => $val) {
                // Unset indexed field data
                unset($fieldData[$key]);

                // Add properties to field data compilation
                $fieldData[$val->name] = $val;

                // Check if the field has a primary key
                if (isset($val->primaryKey) && $val->primaryKey && ! in_array($val->name, $this->_setPrimary, true)) {
                    // Push primary key
                    $this->_setPrimary[] = $val->name;
                }
            }

            // Primary key still not found, find from index data
            if (! $this->_setPrimary) {
                // Retrieve index data
                $indexData = $this->model->indexData($this->_table);

                // Find the primary key
                foreach ($indexData as $key => $val) {
                    // Check if the field has a primary key
                    if (in_array($val->type, ['PRIMARY', 'UNIQUE'], true)) {
                        // Push primary key
                        $this->_setPrimary = array_merge($this->_setPrimary, $val->fields);
                    }
                }

                // Make the array unique
                $this->_setPrimary = array_unique($this->_setPrimary);
            }

            // Apply primary from where if it's were sets
            if (! $this->_setPrimary && $this->_where) {
                // Get array keys
                $this->_setPrimary = array_keys($this->_where);
            }

            // Check again if the primary key is still unavailable
            if (! $this->_setPrimary) {
                if ('backend' == $this->template->get_theme_property('type')) {
                    // Add notification into table heading
                    $this->setDescription('<div><b>' . phrase('No primary key is found.') . '</b> ' . phrase('Please define it manually and refer to {{set_primary}}.', ['set_primary' => '<code>set_primary()</code>']) . ' ' . phrase('Without primary key, you only allowed to insert the data.') . '</div>');
                }

                // Unset method
                $this->unsetMethod('update, delete');
            }

            // Remove primary query string if method is matched
            foreach ($queryParams as $key => $val) {
                if (in_array($this->_method, ['read', 'update', 'delete'], true) && in_array($key, $this->_setPrimary, true)) {
                    // Remove query parameter from URL
                    $queryParams[$key] = null;
                }
            }

            // Assign previous URL
            $this->_redirectBack = go_to(null, $queryParams);

            // Check the additional primary key that been sets up
            if (is_array($this->_setPrimary) && sizeof($this->_setPrimary) > 0) {
                foreach ($this->_setPrimary as $key => $val) {
                    if ($this->request->getGet($val) && $this->model->fieldExists($val, $this->_table)) {
                        if (
                            ('read' == $this->_method && isset($this->_unsetRead[$val]) && in_array($this->request->getGet($val), $this->_unsetRead[$val], true)) ||
                            ('update' == $this->_method && isset($this->_unsetUpdate[$val]) && in_array($this->request->getGet($val), $this->_unsetUpdate[$val], true)) ||
                            ('delete' == $this->_method && isset($this->_unsetDelete[$val]) && in_array($this->request->getGet($val), $this->_unsetDelete[$val], true)) ||
                            ('export' == $this->_method && isset($this->_unsetRead[$val]) && in_array($this->request->getGet($val), $this->_unsetRead[$val], true)) ||
                            ('print' == $this->_method && isset($this->_unsetRead[$val]) && in_array($this->request->getGet($val), $this->_unsetRead[$val], true)) ||
                            ('pdf' == $this->_method && isset($this->_unsetRead[$val]) && in_array($this->request->getGet($val), $this->_unsetRead[$val], true))
                        ) {
                            if (in_array($this->_method, ['read', 'export', 'print', 'pdf'], true)) {
                                // Method isn't allowed to access, throw exception
                                return throw_exception(403, phrase('You are not allowed to view the requested data.'), $this->_redirectBack);
                            } else {
                                if (isset($this->_setMessages['update'])) {
                                    // Add custom message if any
                                    return throw_exception($this->_setMessages['update']['code'], $this->_setMessages['update']['messages'], $this->_redirectBack);
                                } else {
                                    // Otherwise, use default message
                                    return throw_exception(403, phrase('You are not allowed to modify the requested data.'), $this->_redirectBack);
                                }
                            }
                        }

                        if (! in_array($val, array_keys($this->_where ?? []), true)) {
                            // Push where into prepared statement only if where is not defined in controller
                            $this->_prepare('where', [$this->_table . '.' . $val, htmlspecialchars($this->request->getGet($val))]);
                        }
                    } elseif (
                        in_array($val, $this->_setPrimary, true) &&
                        $this->model->fieldExists($val, $this->_table) &&
                        isset($this->_setDefault[$val]) &&
                        $this->_setDefault[$val]
                    ) {
                        if (
                            ('read' == $this->_method && isset($this->_unsetRead[$val]) && in_array($this->request->getGet($val), $this->_unsetRead[$val], true)) ||
                            ('update' == $this->_method && isset($this->_unsetUpdate[$val]) && in_array($this->request->getGet($val), $this->_unsetUpdate[$val], true)) ||
                            ('delete' == $this->_method && isset($this->_unsetDelete[$val]) && in_array($this->request->getGet($val), $this->_unsetDelete[$val], true)) ||
                            ('export' == $this->_method && isset($this->_unsetRead[$val]) && in_array($this->request->getGet($val), $this->_unsetRead[$val], true)) ||
                            ('print' == $this->_method && isset($this->_unsetRead[$val]) && in_array($this->request->getGet($val), $this->_unsetRead[$val], true)) ||
                            ('pdf' == $this->_method && isset($this->_unsetRead[$val]) && in_array($this->request->getGet($val), $this->_unsetRead[$val], true))
                        ) {
                            if (in_array($this->_method, ['read', 'export', 'print', 'pdf'], true)) {
                                // Requested method isn't allowed, throw exception
                                return throw_exception(403, phrase('You are not allowed to view the requested data.'), $this->_redirectBack);
                            } else {
                                if (isset($this->_setMessages['update'])) {
                                    // Add custom message if any
                                    return throw_exception($this->_setMessages['update']['code'], $this->_setMessages['update']['messages'], $this->_redirectBack);
                                } else {
                                    // Otherwise, use default message
                                    return throw_exception(403, phrase('You are not allowed to modify the requested data.'), $this->_redirectBack);
                                }
                            }
                        }

                        // Add where into prepared statement
                        $this->_prepare('where', [$this->_table . '.' . $val, $this->_setDefault[$val]]);
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
                $tokenSent = $this->request->getPost('_token');

                // Validate the token
                if ($this->validToken($tokenSent)) {
                    // Token approved, check if validation use the custom callback
                    if ($this->_formCallback && method_exists($this, $this->_formCallback)) {
                        // Use callback as form validation
                        $_callback = $this->_formCallback;

                        return $this->$_callback();
                    } else {
                        // Serialize table data
                        $fieldData = array_fill_keys(array_keys(array_flip($this->model->listFields($this->_table))), '');

                        // Or use the master validation instead
                        return $this->validateForm($fieldData);
                    }
                } else {
                    // Token isn't valid, throw exception
                    return throw_exception(403, phrase('The submitted token has been expired or the request is made from the restricted source.'), $this->_redirectBack);
                }
            } elseif ($this->apiClient && in_array($this->request->getMethod(), ['POST'], true) && (in_array($this->_method, ['create', 'update'], true) || ($this->_formCallback && method_exists($this, $this->_formCallback)))) {
                // Request is sent from REST
                if ($this->_formCallback && method_exists($this, $this->_formCallback)) {
                    // Use callback as form validation
                    $_callback = $this->_formCallback;

                    return $this->$_callback();
                } else {
                    // Serialize table data
                    $fieldData = array_fill_keys(array_keys(array_flip($this->model->listFields($this->_table))), '');

                    // Or use the master validation instead
                    return $this->validateForm($fieldData);
                }
            } elseif ($this->_setPrimary && 'delete' == $this->_method) {
                // Delete data
                if (1 == $this->request->getPost('batch')) {
                    // Batch delete
                    return $this->deleteBatch($this->_table);
                } else {
                    // Single delete
                    return $this->deleteData($this->_table, $this->_where, $this->_limit);
                }
            } else {
                // Get offset if not set
                if (! in_array($this->_method, ['create', 'read', 'update', 'delete'], true) && is_numeric($this->request->getGet('per_page')) && $this->request->getGet('per_page') > 1 && (! $this->_offsetCalled || (! $this->_offset && gettype($this->_offset) !== 'integer'))) {
                    $this->_offset = ($this->request->getGet('per_page') - 1) * ($this->_limit ?? $this->_limitBackup);
                }

                if ($this->_offset) {
                    // Push offset to the prepared query builder
                    $this->_prepare('offset', [$this->_offset]);
                }

                if (! in_array($this->_method, ['create', 'read', 'update', 'delete'], true) &&
                    ($this->_searchable && ! $this->_like && $this->request->getGet('q')) ||
                    ('autocomplete' == $this->request->getPost('method') && $this->_searchable && $this->request->getPost('q'))
                ) {
                    $isAutocomplete = ('autocomplete' == $this->request->getPost('method'));
                    $searchQuery = $isAutocomplete ? $this->request->getPost('q') : $this->request->getGet('q');

                    // Sanitize search query - escape special characters for LIKE
                    $searchQuery = str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $searchQuery);
                    $searchQuery = htmlspecialchars($searchQuery, ENT_QUOTES, 'UTF-8');

                    $groupStart = false;

                    if (! $isAutocomplete) {
                        $this->groupStart();
                        $groupStart = true;
                    }

                    $column = $this->request->getGet('column') ? strip_tags($this->request->getGet('column')) : null;

                    // ========== SEARCH BY SPECIFIC COLUMN ==========
                    if ($column && 'all' != $column) {
                        // Whitelist: Ensure column is valid
                        $validColumns = [];
                        foreach ($this->_compiledTable as $key => $val) {
                            if ($this->model->fieldExists($column, $val)) {
                                $validColumns[] = $val . '.' . $column;
                            }
                        }

                        if (! empty($validColumns)) {
                            foreach ($validColumns as $validColumn) {
                                $this->_prepare('like', [$validColumn, $searchQuery]);
                            }
                        }
                    }
                    // ========== SEARCH ALL COLUMNS ==========
                    else {
                        $columns = $this->model->listFields($this->_table);

                        // Get columns from joined tables
                        if ($this->_select && $this->_compiledTable) {
                            foreach ($this->_compiledTable as $key => $val) {
                                list($joinedTable) = explode('.', $val);

                                if ($joinedTable != $this->_table) {
                                    $selectSearch = preg_grep('/^' . preg_quote($joinedTable, '/') . '/', $this->_select);

                                    if (isset($selectSearch[0])) {
                                        $columns[] = $selectSearch[0];
                                    }
                                }
                            }
                        }

                        // ========== SEARCH IN TABLE FIELDS ==========
                        if ($columns) {
                            if ($groupStart) {
                                $this->orGroupStart();
                            } else {
                                $this->groupStart();
                            }

                            foreach ($columns as $key => $val) {
                                // Add table prefix to prevent ambiguous columns
                                if (strpos($val, '.') === false) {
                                    $val = $this->_table . '.' . $val;
                                }

                                $this->_prepare(($key ? 'orLike' : 'like'), [$val, $searchQuery]);
                            }

                            $this->groupEnd();
                        }

                        // ========== SEARCH IN SELECT FIELDS ==========
                        if ($this->_select) {
                            $compiledLike = [];
                            $searchConditions = [];
                            $orderByConditions = [];

                            foreach ($this->_select as $key => $val) {
                                if (! $val) {
                                    continue;
                                }

                                // Remove AS alias
                                $originalVal = $val;
                                if (stripos($val, ' AS ') !== false) {
                                    $val = trim(substr($val, 0, stripos($val, ' AS ')));
                                }

                                // Get field name without table prefix
                                $fieldOrigin = (strpos($val, '.') !== false ? substr($val, strpos($val, '.') + 1) : $val);

                                // Skip if already processed or first item
                                if (! $key || in_array($fieldOrigin, $compiledLike, true)) {
                                    continue;
                                }

                                // Validate field exists in database
                                $tableName = null;
                                if (isset($this->_setField[$this->request->getPost('origin')]['parameter'])) {
                                    $param = $this->_setField[$this->request->getPost('origin')]['parameter'];
                                    $tableName = is_array($param) ? $param[0] : $param;
                                }

                                // Whitelist: Only fields that exist in database
                                $fieldForCheck = (stripos($val, '.') !== false ? substr($val, strripos($val, '.') + 1) : $val);
                                $isValidField = $tableName && $this->model->fieldExists($fieldForCheck, $tableName);

                                // Collect search conditions
                                $searchConditions[] = [
                                    'type' => ($key ? 'orLike' : 'like'),
                                    'field' => $val,
                                    'query' => $searchQuery
                                ];

                                // Collect order by conditions (only for valid fields)
                                if ($isValidField && $isAutocomplete) {
                                    // Use parameter binding or prepared statements
                                    // DON'T directly concatenate user input!
                                    $orderByConditions[] = $val;
                                }

                                $compiledLike[] = $fieldOrigin;
                            }

                            // Only create group if there are conditions
                            if (! empty($searchConditions)) {
                                if ($groupStart) {
                                    $this->orGroupStart();
                                } else {
                                    $this->groupStart();
                                }

                                foreach ($searchConditions as $condition) {
                                    $this->_prepare($condition['type'], [$condition['field'], $condition['query']]);
                                }

                                $this->groupEnd();

                                // Add ORDER BY for autocomplete (with safe approach)
                                if (! empty($orderByConditions) && $isAutocomplete) {
                                    foreach ($orderByConditions as $orderField) {
                                        // Use query builder that supports parameter binding
                                        // Example with CodeIgniter 4:
                                        $escapedQuery = $this->model->escape($searchQuery);
                                        $this->_prepare('orderBy', [
                                            "(CASE
                                                WHEN {$orderField} LIKE {$escapedQuery} THEN 1
                                                WHEN {$orderField} LIKE CONCAT({$escapedQuery}, '%') THEN 2
                                                WHEN {$orderField} LIKE CONCAT('%', {$escapedQuery}) THEN 4
                                                ELSE 3
                                            END)"
                                        ]);
                                    }
                                }
                            }
                        }
                    }

                    if (! $isAutocomplete) {
                        $this->groupEnd();
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

                if (isset($this->_setField[$this->request->getPost('origin')]) && in_array('autocomplete', $this->_setField[$this->request->getPost('origin')]['field_type'], true)) {
                    // Set the relation table, field and keyword
                    $field = $this->_setField[$this->request->getPost('origin')];
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

                        $this->model->groupStart();

                        if ($num > 0) {
                            $this->model->orLike($val, $keyword, 'both', true, true);
                        } else {
                            $this->model->like($val, $keyword, 'both', true, true);
                        }

                        $this->model->groupEnd();

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
                                $this->model->orderBy($key, $val);
                            }
                        } else {
                            $this->model->orderBy($order);
                        }
                    }

                    $this->model->groupBy($select['value']);

                    // Run query
                    $query = $this->model->get($table, 50)->resultArray();

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
                                'image' => ($image ? get_image($this->_setUploadPath, $image, 'icon') : null),
                                'target' => null
                            ];
                        }
                    }
                } else {
                    // Autocomplete search data from listed of table
                    if (! $this->_select) {
                        // Check the select list, if none, use the main table field instead
                        $this->_select = preg_filter('/^/', $this->_table . '.', $this->model->listFields($this->_table));
                    }

                    // Loop the select field to prevent query using multiple LIKE condition and use OR LIKE instead
                    $compiledLike = [];

                    foreach ($this->_select as $key => $val) {
                        if ($val && stripos($val, ' AS ') !== false) {
                            $val = substr($val, 0, stripos($val, ' AS '));
                        }

                        $fieldOrigin = (strpos($val, '.') !== false ? substr($val, strpos($val, '.') + 1) : $val);

                        if (! $key || in_array($fieldOrigin, $compiledLike, true)) {
                            continue;
                        }

                        // Push like an or like to the prepared query builder
                        $this->_prepare(($key ? 'orLike' : 'like'), [$val, htmlspecialchars(('autocomplete' == $this->request->getPost('method') && $this->request->getPost('q') ? $this->request->getPost('q') : $this->request->getGet('q')))]);

                        if (isset($this->_setField[$this->request->getPost('origin')]['parameter'])) {
                            if (is_array($this->_setField[$this->request->getPost('origin')]['parameter'])) {
                                $table = $this->_setField[$this->request->getPost('origin')]['parameter'][0];
                            } else {
                                $table = $this->_setField[$this->request->getPost('origin')]['parameter'];
                            }
                        }

                        if (isset($this->_setField[$this->request->getPost('origin')]['parameter']) && $this->model->fieldExists(($val && stripos($val, '.') !== false ? substr($val, strripos($val, '.') + 1) : $val), $table)) {
                            // Push order by best match to the prepared query builder
                            $this->_prepare('orderBy', ['(CASE WHEN ' . $val . ' LIKE "' . $this->request->getPost('q') . '%" THEN 1 WHEN ' . $val . ' LIKE "%' . $this->request->getPost('q') . '" THEN 3 ELSE 2 END)']);
                        }

                        $compiledLike[] = $fieldOrigin;
                    }

                    // Run the query using prepared property
                    $query = $this->_fetch($this->_table);

                    // Populate added item
                    $addedItem = [];

                    // Serialize results
                    $serialized = $this->serialize($query['results']);

                    foreach ($serialized as $key => $val) {
                        // Does column order is mandatory? let's just watch
                        if (is_array($this->_columnOrder) && sizeof($this->_columnOrder) > 0) {
                            // Set the default column order
                            $columnOrder = [];

                            foreach ($this->_columnOrder as $orderKey => $orderVal) {
                                // If array key exists
                                if (array_key_exists($orderVal, $val)) {
                                    // Then push to column order grocery
                                    $columnOrder[] = $orderVal;
                                }
                            }

                            // Set the value
                            $val = array_replace(array_flip($columnOrder), $val);
                        }

                        $autocompleteItem = [];
                        // Loop the result
                        foreach ($val as $field => $value) {
                            // Check if the result value is not contain the search keyword or the field is unset from column (list table)
                            if (($value['original'] && strpos(strtolower($value['original']), strtolower($this->request->getPost('q'))) === false) || in_array($field, $this->_unsetColumn, true)) {
                                continue;
                            }

                            // Everything's looks good, throw into autocomplete result
                            if (! $autocompleteItem && $value['original'] && ! in_array($value['content'], $addedItem, true)) {
                                $addedItem[] = $value['content'];

                                $autocompleteItem = [
                                    'value' => truncate($value['content'], 32, false, ''),
                                    'label' => truncate($value['content'], 32),
                                    'target' => current_page(null, ['per_page' => null, 'q' => truncate($value['content'], 32, '')])
                                ];
                            }
                        }

                        if ($autocompleteItem) {
                            $suggestions[] = $autocompleteItem;
                        }
                    }
                }

                // Return the callback as autocomplete results
                return make_json([
                    'suggestions' => ($suggestions ? $suggestions : null)
                ]);
            } elseif ($this->request->isAJAX() && 'ajax_select' == $this->request->getPost('method') && isset($this->_setRelation[$this->request->getPost('source')])) {
                // Check if data is requested through server side select (jQuery plugin)
                return $this->_getRelation($this->_setRelation[$this->request->getPost('source')], null, true);
            } elseif ($this->request->isAJAX() && 'sort_table' == $this->request->getPost('method')) {
                // Sort table
                return $this->_sortTable($this->request->getPost('ordered_id'));
            }

            if ($this->request->getGet('sort') && 'desc' == strtolower($this->request->getGet('sort'))) {
                // Order ASC from query string
                set_userdata('sortOrder', 'ASC');
            } else {
                // Order DESC from query string
                set_userdata('sortOrder', 'DESC');
            }

            if ($this->request->getGet('order') && $this->model->fieldExists($this->request->getGet('order'), $this->_table)) {
                // Match order by the primary table
                // Push order to the prepared query builder
                $this->_prepare[] = [
                    'function' => 'orderBy',
                    'arguments' => [$this->_table . '.' . $this->request->getGet('order'), get_userdata('sortOrder')]
                ];
            } elseif ($this->_compiledTable) {
                // Otherwhise, find it from the relation table
                foreach ($this->_compiledTable as $key => $table) {
                    // Validate the column to check if column is exist in table
                    if ($this->request->getGet('order') && $this->model->fieldExists($this->request->getGet('order'), $table)) {
                        // Push order to the prepared query builder
                        $this->_prepare[] = [
                            'function' => 'orderBy',
                            'arguments' => [$table . '.' . $this->request->getGet('order'), get_userdata('sortOrder')]
                        ];
                    }
                }
            }

            if (in_array($this->_method, ['create'], true)) {
                // List the field properties
                $results = array_fill_keys(array_keys(array_flip($this->model->listFields($this->_table))), '');
                $total = 0;
            } else {
                $singleRow = false;

                if (in_array($this->_method, ['read', 'update'], true) || (in_array($this->_method, ['export', 'print', 'pdf'], true) && array_intersect_key($this->request->getGet(), array_flip($this->_setPrimary)))) {
                    // Request single row
                    $singleRow = true;
                }

                // Run query using prepared property
                $query = $this->_fetch($this->_table, $singleRow);
                $results = $query['results'];
                $total = $query['total'];

                if ($singleRow && ! $results) {
                    if ($this->_permitUpsert) {
                        // Upsert is permitted, initialize empty results
                        $results = (object) array_fill_keys($this->model->listFields($this->_table), '');
                    } else {
                        // Throw 404 exception
                        return throw_exception(404, phrase('The data you are looking for was not found or has been deleted.'), go_to());
                    }
                }
            }

            // Default icon property
            $icon = $this->themeService->get_icon_by_method($this->_method);

            // Default title property
            $title = $this->themeService->get_title_by_method($this->_method);

            // Default description property
            $description = $this->themeService->get_description_by_method($this->_method);

            // Indicates multiple rows result
            if (is_array($results) && isset($results[0])) {
                // Extract magic string
                preg_match_all('/\{\{(.*?)\}\}/', $title ?? '', $titleReplace);
                preg_match_all('/\{\{(.*?)\}\}/', $description ?? '', $descriptionReplace);
                preg_match_all('/\{\{(.*?)\}\}/', $icon ?? '', $iconReplace);

                foreach ($titleReplace[1] as $index => $replace) {
                    $replacement = trim($replace);

                    if (isset($results[0]->$replacement)) {
                        // Attempt to convert the magic string and replace with the result
                        $title = preg_replace("/\{\{(\s+)?($replace)(\s+)?\}\}/", $results[0]->$replacement, $title);
                    }
                }

                foreach ($descriptionReplace[1] as $index => $replace) {
                    $replacement = trim($replace);

                    if (isset($results[0]->$replacement)) {
                        // Attempt to convert the magic string and replace with the result
                        $description = preg_replace("/\{\{(\s+)?($replace)(\s+)?\}\}/", $results[0]->$replacement, $description);
                    }
                }

                foreach ($iconReplace[1] as $index => $replace) {
                    $replacement = trim($replace);

                    if (isset($results[0]->$replacement)) {
                        // Attempt to convert the magic string and replace with the result
                        $icon = preg_replace("/\{\{(\s+)?($replace)(\s+)?\}\}/", $results[0]->$replacement, $icon);
                    }
                }
            } else {
                // No result found
                if (preg_match_all('/\{\{(.*?)\}\}/', $title ?? '')) {
                    // Unset title contains magic string
                    $title = null;
                }

                if (preg_match_all('/\{\{(.*?)\}\}/', $description ?? '')) {
                    // Unset description contains magic string
                    $description = null;
                }

                if (preg_match_all('/\{\{(.*?)\}\}/', $icon ?? '')) {
                    // Unset icon contains magic string
                    $icon = null;
                }
            }

            if ($this->request->getGet('__fetch_metadata') && $this->apiClient) {
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
                $this->_view = (is_array($this->themeService->get_template()) && isset($this->themeService->get_template()['form']) ? $this->themeService->get_template()['form'] : ($view && 'index' != $view ? $view : 'form'));

                // Get formatted results
                $results = $this->renderForm($results);

                // Set icon property
                $icon = ($this->_setMethod || (isset($this->themeService->get_icon()[$this->_method])) && $icon ? $icon : 'mdi mdi-plus');

                // Set title property
                $title = ($this->_setMethod || (isset($this->themeService->get_title()[$this->_method])) && $title ? $title : phrase('Add New Data'));

                // Set description property
                $description = ($this->_setMethod || (isset($this->themeService->get_description()[$this->_method])) && $description ? $description : phrase('Please fill all required field below to add new data.'));
            } elseif ('read' == $this->_method) {
                /**
                 * -------------------------------------------------------------
                 * Method is read
                 * -------------------------------------------------------------
                 */
                // Set view template property
                $this->_view = (is_array($this->themeService->get_template()) && isset($this->themeService->get_template()[$this->_method]) ? $this->themeService->get_template()['read'] : ($view && 'index' != $view ? $view : 'read'));

                // Get formatted results
                $results = $this->renderRead($results);

                // Set icon property
                $icon = ($this->_setMethod || (isset($this->themeService->get_icon()[$this->_method])) && $icon ? $icon : 'mdi mdi-magnify');

                // Set title property
                $title = ($this->_setMethod || (isset($this->themeService->get_title()[$this->_method])) && $title ? $title : phrase('Showing Data'));

                // Set description property
                $description = ($this->_setMethod || (isset($this->themeService->get_description()[$this->_method])) && $description ? $description : phrase('Showing the result of requested data.'));
            } elseif ('update' == $this->_method) {
                /**
                 * -------------------------------------------------------------
                 * Method is update
                 * -------------------------------------------------------------
                 */
                // Set view template property
                $this->_view = (is_array($this->themeService->get_template()) && isset($this->themeService->get_template()['form']) ? $this->themeService->get_template()['form'] : ($view && 'index' != $view ? $view : 'form'));

                // Get formatted results
                $results = $this->renderForm($results);

                // Set icon property
                $icon = ($this->_setMethod || (isset($this->themeService->get_icon()[$this->_method])) && $icon ? $icon : 'mdi mdi-square-edit-outline');

                // Set title property
                $title = ($this->_setMethod || (isset($this->themeService->get_title()[$this->_method])) && $title ? $title : phrase('Update Data'));

                // Set description property
                $description = ($this->_setMethod || (isset($this->themeService->get_description()[$this->_method])) && $description ? $description : phrase('Make sure to check the changes before submitting.'));
            } elseif (in_array($this->_method, ['export', 'print', 'pdf'], true)) {
                /**
                 * -------------------------------------------------------------
                 * Method is requesting document file or print
                 * -------------------------------------------------------------
                 */
                $queryParams = $this->request->getGet();
                $singlePrint = false;

                if ($this->_setPrimary) {
                    foreach ($this->_setPrimary as $key => $val) {
                        // Find single item print
                        if (isset($queryParams[$val])) {
                            $singlePrint = true;

                            break;
                        }
                    }
                }

                // Set view template property
                $this->_view = (isset($this->themeService->get_template()[$this->_method]) ? $this->themeService->get_template()[$this->_method] : $this->_method);

                // Get formatted results
                $results = ($singlePrint ? $this->renderRead($results) : $this->renderTable($results));

                // Set icon property
                $icon = ($icon ? $icon : ($this->themeService->get_title_fallback() ?? 'mdi mdi-table'));

                // Set title property
                $title = ($title ? $title : ($this->themeService->get_title_fallback() ?? phrase('Page not found!')));

                // Set description property
                $description = ($description ? $description : $this->themeService->get_description_fallback());
            } else {
                /**
                 * -------------------------------------------------------------
                 * Non listed method
                 * -------------------------------------------------------------
                 */
                // Check if master view template is used
                $viewExist = (! in_array($this->template->get_view($this->_view), ['../../aksara/Views/core/index', '../../themes/' . $this->template->theme . '/components/core/index', '../../aksara/Views/core/index_grid', '../../themes/' . $this->template->theme . '/components/core/index_grid', '../../aksara/Views/core/index_mobile', '../../themes/' . $this->template->theme . '/components/core/index_mobile', '../../aksara/Views/core/error', '../../themes/' . $this->template->theme . '/components/core/error'], true));

                // Set description property
                $this->_view = (is_array($this->themeService->get_template()) && isset($this->themeService->get_template()['index']) ? $this->themeService->get_template()['index'] : ($view && 'index' != $view ? $view : 'index'));

                // Get formatted results
                $results = (! $viewExist ? $this->renderTable($results) : $results);

                // Set icon property
                $icon = ($icon ? $icon : ($this->themeService->get_title_fallback() ?? 'mdi mdi-table'));

                // Set title property
                $title = ($title ? $title : ($this->themeService->get_title_fallback() ?? phrase('Page not found!')));

                // Set description property
                $description = ($description ? $description : $this->themeService->get_description_fallback());
            }
        } else {
            /**
             * -------------------------------------------------------------
             * No database table are included to renderer
             * -------------------------------------------------------------
             */
            // Set template view property
            $this->_view = (is_array($this->themeService->get_template()) && isset($this->themeService->get_template()['index']) ? $this->themeService->get_template()['index'] : $this->_method);

            // Set default result
            $results = [];
            $total = 0;

            // Default icon property
            $icon = $this->themeService->get_icon_by_method($this->_method) ?? 'mdi mdi-file-document-outline';

            // Default title property
            $title = $this->themeService->get_title_by_method($this->_method) ?? phrase('Untitled');

            // Default description property
            $description = $this->themeService->get_description_by_method($this->_method);

            if ($this->request->getGet('__fetch_metadata') && $this->apiClient) {
                return make_json([
                    'title' => $title,
                    'description' => $description,
                    'icon' => $icon
                ]);
            }
        }

        if ($this->apiClient && 'complete' === $this->request->getGet('format_result')) {
            // Requested from API Client in formatted result
            return make_json($results);
        }

        // Get query string
        $queryParams = $this->request->getGet();

        foreach ($queryParams as $key => $val) {
            if (in_array($this->_method, ['read', 'update'], true) && in_array($key, $this->_setPrimary, true)) {
                // Remove query parameter from URL
                $queryParams[$key] = null;
            }
        }

        if (! $this->_modalSize && isset($results['column_total'])) {
            if ($results['column_total'] > 3) {
                $this->_modalSize = 'modal-xxl';
            } elseif ($results['column_total'] > 2) {
                $this->_modalSize = 'modal-xl';
            } elseif ($results['column_total'] > 1) {
                $this->_modalSize = 'modal-lg';
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
                'description' => preg_replace('/[^\S ]+/', '', $description ?? ''),
                'icon' => $icon,
                'title' => $title,
                'modal_size' => ($this->_modalSize ? $this->_modalSize : ''),
                'segmentation' => array_map(function ($segment = null) {return str_replace('.', '-', preg_replace('/[^a-zA-Z0-9]/', '_', $segment));}, $uri->getSegments())
            ],
            'breadcrumb' => $this->template->breadcrumb($this->themeService->get_breadcrumb(), $title, $this->_setPrimary),
            'links' => [
                'base_url' => base_url(),
                'current_module' => go_to(null, $queryParams),
                'current_page' => current_page()
            ],
            'query_params' => $this->request->getGet(),
            'results' => $results,
            '_token' => $this->_token
        ];

        if (in_array($this->_method, ['create', 'read', 'update'], true)) {
            unset($output['total']);
        } else {
            // Add pagination
            $output['pagination'] = $this->template->pagination([
                'total' => $total,
                'limit' => $this->_limitBackup,
                'offset' => $this->_offset,
                'per_page' => $this->_limit,
                'total_rows' => $total,
                'url' => current_page(null, ['per_page' => null])
            ]);
        }

        // Merge user defined output
        if ($this->_setOutput) {
            $output = array_merge($output, $this->_setOutput);
        }

        // Elapsed time
        $output['elapsed_time'] = (float) $timer->has('elapsed_time') ? $timer->getElapsedTime('elapsed_time') : 0.00;

        // Generate the output
        if (in_array($this->_method, ['print', 'export', 'pdf'], true)) {
            $document = new Document();

            $document->pageSize('13in 8.5in');

            $output = view('templates/export', (array) json_decode(json_encode($output)));

            // Send to client
            if ('print' == $this->_method) {
                return $this->response->setBody($output)->sendBody();
            } else {
                return $document->generate($output, $title, ('export' == $this->_method ? ($this->request->getGet('method') ?? 'export') : 'embed'));
            }
        } elseif ($this->apiClient && ! in_array($this->request->getMethod(), ['GET'], true)) {
            // The method is requested from REST without GET
            return throw_exception(403, phrase('The method you requested is not acceptable.') . ' (' . $this->request->getMethod() . ')', (! $this->apiClient ? $this->_redirectBack : null));
        }

        if ($this->apiClient) {
            // API calls, remove unnecessary indexes
            unset(
                $output['breadcrumb'],
                $output['links'],
                $output['pagination']['action'],
                $output['pagination']['filters'],
                $output['pagination']['information'],
                $output['pagination']['links'],
                $output['query_params'],
                $output['total'],
                $output['_token']
            );

            // Send result to API client
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
    public function renderTable(array $data): array
    {
        // If Primary Key is not defined, disable Update and Delete actions for safety.
        if (! $this->_setPrimary) {
            $this->_unsetMethod = array_merge($this->_unsetMethod, ['update', 'delete']);
        }

        // Serialize data (convert raw objects/arrays into a standardized format)
        $serialized = $this->serialize($data);

        $tableData = [];

        if ($serialized) {
            // --- Prepare Properties for Renderer ---

            // Define essential properties needed by the table renderer (whitelisting for abstraction/safety).
            $whitelistedProperties = [
                '_addButton', '_addClass', '_addDropdown', '_addToolbar', '_addFilter', '_columnOrder', '_gridView',
                '_itemReference', '_mergeContent', '_mergeLabel', '_method', '_parameter', '_select',
                '_setAlias', '_setAutocomplete', '_setButton', '_setField', '_setRelation',
                '_setUploadPath', '_sortable', '_table', '_unsetColumn', '_unsetClone', '_unsetDelete',
                '_unsetMethod', '_unsetRead', '_unsetTruncate', '_unsetUpdate', 'apiClient', 'model'
            ];

            // Create an array containing only the whitelisted properties from the current object.
            $properties = array_intersect_key(get_object_vars($this), array_flip($whitelistedProperties));

            // Add theme property
            $properties['_setTheme'] = $this->template->theme;

            // --- Load Renderer ---
            $renderer = new Renderer();
            $renderer->setProperty($properties); // Send necessary context properties
            $renderer->setPath('table'); // Specify the renderer path (e.g., table renderer)

            // Run the renderer to format the serialized data into final table structure.
            $tableData = $renderer->render($serialized, count($data));
        }

        return $tableData;
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
    public function renderForm(array|object $data): array
    {
        // --- Initial Validation ---
        // Check if data is empty AND the upsert permission is not granted AND it's not an autocomplete request.
        if (! $data && ! $this->_permitUpsert && 'autocomplete' != $this->request->getPost('method')) {
            return throw_exception(404, phrase('The data you requested does not exist or has been removed.'), $this->_redirectBack);
        }

        // Serialize data (convert raw objects/arrays into a standardized format)
        $serialized = $this->serializeRow($data);

        $fieldData = [];

        if ($serialized) {
            // --- Prepare Properties for Renderer (Whitelisting for Abstraction/Safety) ---

            $whitelistedProperties = [
                '_addClass', '_columnOrder', '_columnSize', '_defaultValue', '_dbDriver',
                '_fieldAppend', '_fieldPrepend', '_fieldOrder', '_viewOrder', '_extraSubmit',
                '_fieldPosition', '_fieldSize', '_groupField', '_mergeField', '_mergeLabel',
                '_method', '_modalSize', '_setAlias', '_setAttribute', '_setAutocomplete',
                '_setField', '_setHeading', '_setPlaceholder', '_setRelation', '_setTooltip',
                '_setUploadPath', '_table', 'apiClient', 'model'
            ];

            // Create an array containing only the whitelisted properties from the current object.
            $properties = array_intersect_key(get_object_vars($this), array_flip($whitelistedProperties));

            // Add theme property
            $properties['_setTheme'] = $this->template->theme;

            // --- Load Renderer ---
            $renderer = new Renderer();
            $renderer->setProperty($properties); // Send necessary context properties
            $renderer->setPath('form'); // Specify the renderer path (form renderer)

            // Run the renderer to format the serialized data into final form structure.
            $fieldData = $renderer->render($serialized);
        }

        return $fieldData;
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
    public function renderRead(array|object $data): array
    {
        // --- Initial Validation ---
        // If data is empty, throw a 404 exception.
        if (! $data) {
            return throw_exception(404, phrase('The data you requested does not exist or has been removed.'), $this->_redirectBack);
        }

        // Serialize data (convert raw objects/arrays into a standardized format)
        $serialized = $this->serializeRow($data);

        $fieldData = [];

        if ($serialized) {
            // --- Prepare Properties for Renderer (Whitelisting for Abstraction/Safety) ---

            $whitelistedProperties = [
                '_addClass', '_columnOrder', '_columnSize', '_fieldAppend', '_fieldPrepend', '_fieldOrder',
                '_viewOrder', '_fieldPosition', '_fieldSize', '_groupField', '_mergeContent',
                '_mergeField', '_mergeLabel', '_method', '_modalSize', '_setAlias',
                '_setAttribute', '_setField', '_setHeading', '_setRelation', '_setUploadPath',
                '_table', 'apiClient'
            ];

            // Create an array containing only the whitelisted properties from the current object.
            $properties = array_intersect_key(get_object_vars($this), array_flip($whitelistedProperties));

            // Add theme property
            $properties['_setTheme'] = $this->template->theme;

            // --- Load Renderer ---
            $renderer = new Renderer();
            $renderer->setProperty($properties); // Send necessary context properties
            $renderer->setPath('view'); // Specify the renderer path (view/read renderer)

            // Run the renderer to format the serialized data into final view structure.
            $fieldData = $renderer->render($serialized);
        }

        return $fieldData;
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
    public function validateForm(array|object $data)
    {
        // --- 1. Initial Security & Update Check ---
        if ($this->_restrictOnDemo) {
            return throw_exception(403, phrase('This feature is disabled in demo mode.'), $this->_redirectBack);
        }

        // Check if method is update
        if ('update' == $this->_method && ! $this->_where && ! $this->_permitUpsert) {
            // Fail because no primary keyword and insert is restricted
            return throw_exception(404, phrase('The data you would to update is not found.'), (! $this->apiClient ? $this->_redirectBack : null));
        }

        // Serialize data (convert raw objects/arrays into a standardized format)
        $serialized = $this->serializeRow($data, false);

        if ($this->request->getPost() && is_array($serialized) && sizeof($serialized) > 0) {
            // Store upload path to session
            set_userdata('_set_upload_path', $this->_setUploadPath);

            // Default validation
            $validation = false;

            foreach ($serialized as $key => $val) {
                $type = array_keys($val['type']);

                // Skip field when it's disabled and has no default value
                if (in_array($key, $this->_unsetField, true) || isset($this->_setDefault[$key]) || array_intersect(['current_timestamp'], $type) || ('create' === $this->_method && array_intersect(['updated_timestamp'], $type)) || ('update' === $this->_method && array_intersect(['created_timestamp'], $type))) {
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
                        preg_match('/callback_(.*?)(\[|$)/', $callback, $callbackMatch);

                        if (isset($callbackMatch[1]) && method_exists($this, $callbackMatch[1])) {
                            // Apply callback only when method is exists
                            $val['validation'][$index] = [$this, $callbackMatch[1]];
                        }
                    }
                }

                if (array_intersect(['image'], $type)) {
                    $validation = true;

                    if (in_array('required', $val['validation'], true) && isset($_FILES[$key]['error']) && 0 === $_FILES[$key]['error']) {
                        $val['validation'] = array_diff($val['validation'], ['required']);
                    }

                    $val['validation'][] = 'validate_upload[' . $key . '.image]';

                    // Single upload validation rules
                    $this->formValidation->setRule($key, (isset($this->_setAlias[$key]) ? $this->_setAlias[$key] : ucwords(str_replace('_', ' ', $key))), $val['validation']);
                } elseif (array_intersect(['images'], $type)) {
                    $validation = true;

                    if (in_array('required', $val['validation'], true) && isset($_FILES[$key]['error'][0]) && 0 === $_FILES[$key]['error'][0]) {
                        $val['validation'] = array_diff($val['validation'], ['required']);
                    }

                    $val['validation'][] = 'validate_upload[' . $key . '.image]';

                    // Images upload validation rules
                    $this->formValidation->setRule($key . '.*', (isset($this->_setAlias[$key]) ? $this->_setAlias[$key] : ucwords(str_replace('_', ' ', $key))), $val['validation']);
                } elseif (array_intersect(['file', 'files'], $type)) {
                    $validation = true;

                    if (in_array('required', $val['validation'], true) && ((isset($_FILES[$key]['error']) && 0 === $_FILES[$key]['error']) || (isset($_FILES[$key]['error'][0]) && 0 === $_FILES[$key]['error'][0]))) {
                        $val['validation'] = array_diff($val['validation'], ['required']);
                    }

                    $val['validation'][] = 'validate_upload[' . $key . ']';

                    // Files upload validation rules
                    $this->formValidation->setRule($key . '.*', (isset($this->_setAlias[$key]) ? $this->_setAlias[$key] : ucwords(str_replace('_', ' ', $key))), $val['validation']);
                } elseif (array_intersect(['carousel'], $type)) {
                    $validation = true;

                    $val['validation'][] = 'validate_upload[' . $key . '.image]';

                    // Carousel upload validation rules
                    $this->formValidation->setRule($key . '.background.*', (isset($this->_setAlias[$key]) ? $this->_setAlias[$key] : ucwords(str_replace('_', ' ', $key))), $val['validation']);
                } elseif (array_intersect(['accordion'], $type)) {
                    $validation = true;

                    $val['validation'][] = 'required';

                    // Accordion upload validation rules
                    $this->formValidation->setRule($key . '.title.*', phrase('Accordion Title') . ' ' . (isset($this->_setAlias[$key]) ? $this->_setAlias[$key] : ucwords(str_replace('_', ' ', $key))), $val['validation']);
                    $this->formValidation->setRule($key . '.body.*', phrase('Accordion Body') . ' ' . (isset($this->_setAlias[$key]) ? $this->_setAlias[$key] : ucwords(str_replace('_', ' ', $key))), $val['validation']);
                } elseif (array_intersect(['password'], $type)) {
                    $validation = true;

                    // Password validation only when post field has value
                    if ($this->request->getPost($key)) {
                        // Password validation rules
                        $this->formValidation->setRule($key, (isset($this->_setAlias[$key]) ? $this->_setAlias[$key] : ucwords(str_replace('_', ' ', $key))), 'min_length[6]');
                        $this->formValidation->setRule($key . '_confirmation', phrase('Confirmation') . ' ' . (isset($this->_setAlias[$key]) ? $this->_setAlias[$key] : ucwords(str_replace('_', ' ', $key))), ('create' == $this->_method ? 'required|matches[' . $key . ']' : 'matches[' . $key . ']'));
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
                        $this->formValidation->setRule($key, (isset($this->_setAlias[$key]) ? $this->_setAlias[$key] : ucwords(str_replace('_', ' ', $key))), $val['validation']);
                    }
                } elseif (isset($this->_setRelation[$key])) {
                    $validation = true;

                    // Relation table validation
                    if (in_array('required', $val['validation'], true)) {
                        // Apply rules only when it's required
                        $this->formValidation->setRule($key, (isset($this->_setAlias[$key]) ? $this->_setAlias[$key] : ucwords(str_replace('_', ' ', $key))), ['required', 'relation_checker[' . (strpos($this->_setRelation[$key]['relation_table'], ' ') !== false ? substr($this->_setRelation[$key]['relation_table'], 0, strpos($this->_setRelation[$key]['relation_table'], ' ')) : $this->_setRelation[$key]['relation_table']) . '.' . $this->_setRelation[$key]['relation_key'] . ']']);
                    } else {
                        // Find foreign data
                        $constrained = false;
                        $foreignData = $this->model->foreignData($this->_table);

                        if ($foreignData) {
                            // Find foreign data with loop
                            foreach ($foreignData as $_key => $_val) {
                                // Table has foreign key
                                if ($this->_setRelation[$key]['relation_table'] == $_val->foreign_table_name) {
                                    // Set constraint
                                    $constrained = true;
                                }
                            }
                        }

                        if ($constrained) {
                            // Apply only for constrained table relation
                            $this->formValidation->setRule($key, (isset($this->_setAlias[$key]) ? $this->_setAlias[$key] : ucwords(str_replace('_', ' ', $key))), ['required', 'relation_checker[' . (strpos($this->_setRelation[$key]['relation_table'], ' ') !== false ? substr($this->_setRelation[$key]['relation_table'], 0, strpos($this->_setRelation[$key]['relation_table'], ' ')) : $this->_setRelation[$key]['relation_table']) . '.' . $this->_setRelation[$key]['relation_key'] . ']']);
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
                        $val['validation'][] = 'valid_date[Y-m-d]';
                    } elseif (array_intersect(['timestamp', 'datetime', 'datetimepicker'], $type)) {
                        // Full timestamp validation rules
                        $val['validation'][] = 'valid_date';
                    }

                    if ($val['validation']) {
                        if (! isset($this->_setDefault[$key])) {
                            // Validate only when no default set to field
                            $validation = true;

                            if (is_array($this->request->getPost($key))) {
                                // Array validation rules
                                $this->formValidation->setRule($key . '.*', (isset($this->_setAlias[$key]) ? $this->_setAlias[$key] : ucwords(str_replace('_', ' ', $key))), $val['validation']);
                            } else {
                                // Input validation rules
                                $this->formValidation->setRule($key, (isset($this->_setAlias[$key]) ? $this->_setAlias[$key] : ucwords(str_replace('_', ' ', $key))), $val['validation']);
                            }
                        } else {
                            // Validate only when no default set to field
                            $validation = true;

                            // Apply rules suffix
                            $this->formValidation->setRule($key, (isset($this->_setAlias[$key]) ? $this->_setAlias[$key] : ucwords(str_replace('_', ' ', $key))), $val['validation']);
                        }
                    }
                }
            }

            // Run validation
            if ($validation && $this->formValidation->run($this->request->getPost()) === false) {
                // Unlink the files
                $this->_unlinkFiles(get_userdata('_uploaded_files'));

                // Data invalid
                $errors = $this->formValidation->getErrors();

                foreach ($errors as $field => $message) {
                    // Unset unnecessary field
                    if (strpos($field, '.') !== false) {
                        unset($errors[$field]);
                    }
                }

                return throw_exception(400, $errors);
            }

            // Attempt to get uploaded files string from the session
            $this->_uploadedFiles = get_userdata('_uploaded_files');

            $prepare = [];
            $clone = [];
            $batchData = [];

            if ($this->_cloning) {
                // Clone data
                $clone = $this->model->getWhere($this->_table, $this->_where, 1)->rowArray();
            }

            foreach ($serialized as $field => $value) {
                $type = array_keys($value['type']);

                // Skip field when it's disabled and has no default value
                if (
                    (in_array($field, $this->_unsetField, true) && ! isset($this->_setDefault[$field]) && ! array_intersect(['slug', 'current_timestamp', 'created_timestamp', 'updated_timestamp'], $type)) ||
                    (in_array('disabled', $type, true) && ! isset($this->_setDefault[$field])) ||
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
                        if (isset($this->_uploadedFiles[$field]) && is_array($this->_uploadedFiles[$field])) {
                            // Loop to get source from unknown array key
                            foreach ($this->_uploadedFiles[$field] as $key => $src) {
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
                        if (isset($this->_uploadedFiles[$field]) && is_array($this->_uploadedFiles[$field])) {
                            // Loop to get source from unknown array key
                            foreach ($this->_uploadedFiles[$field] as $key => $src) {
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
                            $uploaded = (isset($this->_uploadedFiles[$field]) ? array_reverse(array_values($this->_uploadedFiles[$field])) : []);

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
                            if (isset($this->_uploadedFiles[$field][$key]) && $this->_uploadedFiles[$field][$key]) {
                                // Pair with newer uploaded background
                                $items[$key]['background'] = $this->_uploadedFiles[$field][$key];
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
                if (isset($this->_setDefault[$field]) && ($this->_setDefault[$field] || is_numeric($this->_setDefault[$field]))) {
                    // Push the default value to the data preparation
                    $prepare[$field] = $this->_setDefault[$field];
                }

                // Or when it's a boolean and no value
                elseif (array_intersect(['boolean'], $type) && ! $this->request->getPost($field) && ! in_array($field, $this->_unsetField, true)) {
                    // Sets to "0" instead of null
                    $prepare[$field] = 0;
                }

                if (! array_intersect(['wysiwyg', 'encryption'], $type) && isset($prepare[$field])) {
                    $prepare[$field] = str_replace(['<', '>'], ['&lt;', '&gt;'], $prepare[$field]);
                }

                if (isset($prepare[$field]) && ! array_intersect(['encryption'], $type)) {
                    $prepare[$field] = $this->_sanitizeInput($prepare[$field]);
                }
            }

            // If data preparation is ready and the method is create
            if ($prepare && in_array('create', [$this->_method], true)) {
                // Insert new data
                $this->insertData($this->_table, $prepare);
            } elseif ($prepare && in_array('update', [$this->_method], true)) {
                // If data preparation is ready and the method is update
                if ($this->_cloning) {
                    // Insert new data
                    $this->insertData($this->_table, $prepare);
                } else {
                    // Update the old data
                    $this->updateData($this->_table, $prepare, $this->_where);
                }
            } else {
                // Unlink the files
                $this->_unlinkFiles(get_userdata('_uploaded_files'));

                // Throw the exception messages
                return throw_exception(403, phrase('The method you requested is not acceptable.') . ' (' . $this->request->getMethod() . ')', (! $this->apiClient ? $this->_redirectBack : null));
            }
        } else {
            // No data are found
            return throw_exception(404, phrase('No data can be executed.'), (! $this->apiClient ? $this->_redirectBack : null));
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
    public function permitUpsert(bool $return = true): static
    {
        $this->_permitUpsert = $return;

        return $this;
    }

    /**
     * @hook before_insert
     * Optional method that can be overridden by a derived Controller or Model
     * to execute custom logic, validation, or data manipulation
     * immediately before a new record is inserted (CREATE operation).
     */
    protected function beforeInsert()
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
    protected function afterInsert()
    {
        // Example Usage: Updating cache, sending notifications, or queuing a background job.
    }

    /**
     * @hook before_update
     * Optional method that can be overridden by a derived Controller or Model
     * to execute custom logic, validation, or data manipulation
     * immediately before an existing record is updated (UPDATE operation).
     */
    protected function beforeUpdate()
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
    protected function afterUpdate()
    {
        // Example Usage: Recording change logs, invalidating related cache entries, or updating search indices.
    }

    /**
     * @hook before_delete
     * Optional method that can be overridden by a derived Controller or Model
     * to execute custom logic, validation, or related tasks
     * immediately before a record is permanently deleted (DELETE operation).
     */
    protected function beforeDelete()
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
    protected function afterDelete()
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
    public function insertData(?string $table = null, array $data = []): object|null
    {
        return $this->crudService->insertData($table, $data);
    }

    /**
     * Retrieves the ID generated by the last successful database INSERT query.
     *
     * This value is typically set internally by the framework after calling insert_data().
     *
     * @return int The last inserted ID (0 if not applicable or failed).
     */
    public function insertId(): int
    {
        return $this->_insertId;
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
    public function updateData(?string $table = null, array $data = [], array $where = []): object|bool
    {
        return $this->crudService->updateData($table, $data, $where);
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
    public function deleteData(?string $table = null, array $where = [], int $limit = 1): object|null
    {
        return $this->crudService->deleteData($table, $where, $limit);
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
    public function deleteBatch(?string $table = null): object|null
    {
        return $this->crudService->deleteBatch($table);
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
            $backupVal = $val;
            $this->_select[] = $val;

            // Clean up the value for internal compiled select list
            $valClean = $val;
            if (! preg_match('/[.()]/', $valClean)) {
                $valClean = substr($valClean, strpos($valClean, '.') + 1);
            }
            if (stripos($valClean, ' AS ') !== false) {
                $valClean = substr($valClean, stripos($valClean, ' AS ') + 4);
            }

            // Only push simple columns (no function calls) to compiled select
            if (strpos($backupVal, '(') === false && strpos($backupVal, ')') === false) {
                $this->_compiledSelect[] = $valClean;
            }
        }

        $this->_prepare(__FUNCTION__, [$column, $escape]);

        return $this;
    }

    /**
     * Select count
     */
    public function selectCount(string $column, ?string $alias = null): static
    {
        $this->_prepare(__FUNCTION__, [$column, $alias]);

        return $this;
    }

    /**
     * Select sum
     */
    public function selectSum(string $column, ?string $alias = null): static
    {
        $this->_prepare(__FUNCTION__, [$column, $alias]);

        return $this;
    }

    /**
     * Select minimum
     */
    public function selectMin(string $column, ?string $alias = null): static
    {
        $this->_prepare(__FUNCTION__, [$column, $alias]);

        return $this;
    }

    /**
     * Select maximum
     */
    public function selectMax(string $column, ?string $alias = null): static
    {
        $this->_prepare(__FUNCTION__, [$column, $alias]);

        return $this;
    }

    /**
     * Select average
     */
    public function selectAvg(string $column, ?string $alias = null): static
    {
        $this->_prepare(__FUNCTION__, [$column, $alias]);

        return $this;
    }

    /**
     * Select from subquery
     */
    public function selectSubquery(object|string $subquery, string $alias): static
    {
        $this->_prepare(__FUNCTION__, [$subquery, $alias]);

        return $this;
    }

    /**
     * Prevent column to be selected
     */
    public function unsetSelect(string|array $column): static
    {
        if (! is_array($column)) {
            $column = array_map('trim', preg_split('/,(?![^(]+\))/', $column));
        }

        $this->_unsetSelect = array_merge($this->_unsetSelect ?? [], $column);

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
    public function fromSubquery(object|string $subquery, string $alias): static
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
        if (! in_array($this->_method, ['delete'], true)) {
            $this->_prepare(__FUNCTION__, [$table, $condition, $type, $escape]);

            // Extract table name without alias
            if (strpos($table, ' ') !== false) {
                $table = substr($table, strrpos($table, ' ') + 1);
            }

            $this->_compiledTable[] = $table;
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
    public function orWhere(string|array $field = [], $value = '', bool $escape = true): static
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
    public function whereIn(string|array $field = [], $value = '', bool $escape = true): static
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
    public function orWhereIn(string|array $field = [], $value = '', bool $escape = true): static
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
    public function whereNotIn(string|array $field = [], $value = '', bool $escape = true): static
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
    public function orWhereNotIn(string|array $field = [], $value = '', bool $escape = true): static
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
    public function like(string|array $field = [], $match = '', string $side = 'both', bool $escape = true, bool $caseInsensitive = true): static
    {
        if (is_array($field)) {
            foreach ($field as $key => $val) {
                $this->_like[$key] = $val;
                $this->_prepare(__FUNCTION__, [$key, $val, $side, $escape, $caseInsensitive]);
            }
        } else {
            $this->_like[$field] = $match;
            $this->_prepare(__FUNCTION__, [$field, $match, $side, $escape, $caseInsensitive]);
        }

        return $this;
    }

    /**
     * Or like clause
     */
    public function orLike(string|array $field = [], $match = '', string $side = 'both', bool $escape = true, bool $caseInsensitive = false): static
    {
        if (is_array($field)) {
            foreach ($field as $key => $val) {
                $this->_prepare(__FUNCTION__, [$key, $val, $side, $escape, $caseInsensitive]);
            }
        } else {
            $this->_prepare(__FUNCTION__, [$field, $match, $side, $escape, $caseInsensitive]);
        }

        return $this;
    }

    /**
     * Not like clause
     */
    public function notLike(string|array $field = [], $match = '', string $side = 'both', bool $escape = true, bool $caseInsensitive = false): static
    {
        if (is_array($field)) {
            foreach ($field as $key => $val) {
                $this->_prepare(__FUNCTION__, [$key, $val, $escape]);
            }
        } else {
            $this->_prepare(__FUNCTION__, [$field, $match, $side, $escape, $caseInsensitive]);
        }

        return $this;
    }

    /**
     * Or not like clause
     */
    public function orNotLike(string|array $field = [], $match = '', string $side = 'both', bool $escape = true, bool $caseInsensitive = false): static
    {
        if (is_array($field)) {
            foreach ($field as $key => $val) {
                $this->_prepare(__FUNCTION__, [$key, $val, $escape]);
            }
        } else {
            $this->_prepare(__FUNCTION__, [$field, $match, $side, $escape, $caseInsensitive]);
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
    public function orHaving(string|array $field = [], $value = '', bool $escape = true): static
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
    public function havingIn(string|array $field = [], $value = '', bool $escape = true): static
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
    public function orHavingIn(string|array $field = [], $value = '', bool $escape = true): static
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
    public function havingNotIn(string|array $field = [], $value = '', bool $escape = true): static
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
    public function orHavingNotIn(string|array $field = [], $value = '', bool $escape = true): static
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
    public function havingLike(string|array $field = [], $match = '', string $side = 'both', bool $escape = true, bool $caseInsensitive = false): static
    {
        if (is_array($field)) {
            foreach ($field as $key => $val) {
                $this->_prepare(__FUNCTION__, [$key, $val, $escape]);
            }
        } else {
            $this->_prepare(__FUNCTION__, [$field, $match, $side, $escape, $caseInsensitive]);
        }

        return $this;
    }

    /**
     * Or having like clause
     */
    public function orHavingLike(string|array $field = [], $match = '', string $side = 'both', bool $escape = true, bool $caseInsensitive = false): static
    {
        if (is_array($field)) {
            foreach ($field as $key => $val) {
                $this->_prepare(__FUNCTION__, [$key, $val, $escape]);
            }
        } else {
            $this->_prepare(__FUNCTION__, [$field, $match, $side, $escape, $caseInsensitive]);
        }

        return $this;
    }

    /**
     * Not having like clause
     */
    public function notHavingLike(string|array $field = [], $match = '', string $side = 'both', bool $escape = true, bool $caseInsensitive = false): static
    {
        if (is_array($field)) {
            foreach ($field as $key => $val) {
                $this->_prepare(__FUNCTION__, [$key, $val, $escape]);
            }
        } else {
            $this->_prepare(__FUNCTION__, [$field, $match, $side, $escape, $caseInsensitive]);
        }

        return $this;
    }

    /**
     * Or not having like clause
     */
    public function orNotHavingLike(string|array $field = [], $match = '', string $side = 'both', bool $escape = true, bool $caseInsensitive = false): static
    {
        if (is_array($field)) {
            foreach ($field as $key => $val) {
                $this->_prepare(__FUNCTION__, [$key, $val, $escape]);
            }
        } else {
            $this->_prepare(__FUNCTION__, [$field, $match, $side, $escape, $caseInsensitive]);
        }

        return $this;
    }

    /**
     * Ordering result query
     */
    public function orderBy(string|array $field = [], string $direction = '', bool $escape = true): static
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
    public function groupBy(string $column): static
    {
        $this->_prepare(__FUNCTION__, [$column]);

        return $this;
    }

    /**
     * Limit the query result
     */
    public function limit(?int $limit, ?int $offset = null): static
    {
        if (in_array($this->_method, ['create', 'read', 'update', 'delete'], true)) {
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
        if (! in_array($this->_method, ['create', 'read', 'update', 'delete'], true)) {
            $this->_offset = is_numeric($this->request->getGet('offset')) ? (int)$this->request->getGet('offset') : $offset;

            $this->_offsetCalled = true;
        }

        $this->_prepare(__FUNCTION__, [$offset]);

        return $this;
    }

    /**
     * Starts a new group by adding an opening parenthesis to the WHERE clause
     * of the query.
     */
    public function groupStart(): static
    {
        if (! in_array($this->_method, ['create', 'update', 'delete'], true)) {
            $this->_prepare(__FUNCTION__);
        }

        return $this;
    }

    /**
     * Starts a new group by adding an opening parenthesis to the WHERE clause
     * of the query, prefixing it with OR.
     */
    public function orGroupStart(): static
    {
        if (! in_array($this->_method, ['create', 'update', 'delete'], true)) {
            $this->_prepare(__FUNCTION__);
        }

        return $this;
    }

    /**
     * Starts a new group by adding an opening parenthesis to the WHERE clause
     * of the query, prefixing it with NOT.
     */
    public function notGroupStart(): static
    {
        if (! in_array($this->_method, ['create', 'update', 'delete'], true)) {
            $this->_prepare(__FUNCTION__);
        }

        return $this;
    }

    /**
     * Starts a new group by adding an opening parenthesis to the WHERE clause
     * of the query, prefixing it with OR NOT.
     */
    public function orNotGroupStart(): static
    {
        if (! in_array($this->_method, ['create', 'update', 'delete'], true)) {
            $this->_prepare(__FUNCTION__);
        }

        return $this;
    }

    /**
     * Ends the current group by adding a closing parenthesis to the WHERE
     * clause of the query.
     */
    public function groupEnd(): static
    {
        if (! in_array($this->_method, ['create', 'update', 'delete'], true)) {
            $this->_prepare(__FUNCTION__);
        }

        return $this;
    }

    /**
     * Starts a new group by adding an opening parenthesis to the HAVING clause
     * of the query.
     */
    public function havingGroupStart(): static
    {
        if (! in_array($this->_method, ['create', 'update', 'delete'], true)) {
            $this->_prepare(__FUNCTION__);
        }

        return $this;
    }

    /**
     * Starts a new group by adding an opening parenthesis to the HAVING clause
     * of the query, prefixing it with OR.
     */
    public function orHavingGroupStart(): static
    {
        if (! in_array($this->_method, ['create', 'update', 'delete'], true)) {
            $this->_prepare(__FUNCTION__);
        }

        return $this;
    }

    /**
     * Starts a new group by adding an opening parenthesis to the HAVING clause
     * of the query, prefixing it with NOT.
     */
    public function notHavingGroupStart(): static
    {
        if (! in_array($this->_method, ['create', 'update', 'delete'], true)) {
            $this->_prepare(__FUNCTION__);
        }

        return $this;
    }

    /**
     * Starts a new group by adding an opening parenthesis to the HAVING clause
     * of the query, prefixing it with OR NOT.
     */
    public function orNotHavingGroupStart(): static
    {
        if (! in_array($this->_method, ['create', 'update', 'delete'], true)) {
            $this->_prepare(__FUNCTION__);
        }

        return $this;
    }

    /**
     * Ends the current group by adding a closing parenthesis to the HAVING
     * clause of the query.
     */
    public function havingGroupEnd(): static
    {
        if (! in_array($this->_method, ['create', 'update', 'delete'], true)) {
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
    private function _runQuery(?string $table = null, bool $recycling = false): mixed
    {
        return $this->crudService->runQuery($table, $recycling);
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
    private function _fetch(?string $table = null, ?bool $row = false): array
    {
        return $this->crudService->fetch($table, $row);
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
    private function _getRelation(array $params = [], int|string|null $selected = 0, bool $ajax = false): array|string
    {
        return $this->crudService->getRelation($params, $selected, $ajax);
    }

    /**
     * Executes the drag-and-drop sorting of table rows based on the new ordered list of primary keys.
     *
     * It swaps the old order keys (retrieved from the database) with the new positions in the submitted list.
     *
     * @param array $orderedId Array of primary key values in their new desired order.
     *
     * @return array The JSON response array (status and message).
     */
    private function _sortTable(array $orderedId = []): string
    {
        return $this->crudService->sortTable($orderedId);
    }

    /**
     * Recursively unlinks uploaded files and their associated thumbnails/icons.
     *
     * Designed to handle nested file paths stored in arrays or JSON strings.
     *
     * @param array $files      An array of file fields/paths to be processed (field_name => path/array).
     * @param string|null $fieldName  Internal tracking of the current field name (used for recursive calls).
     * @param array $fieldList Internal tracking of file lists for exclusion logic.
     *
     * @return void Returns immediately if the input is not a valid array.
     */
    private function _unlinkFiles(?array $files = [], ?string $fieldName = null, array $fieldList = []): void
    {
        $this->crudService->unlinkFiles($files, $fieldName, $fieldList);
    }

    /**
     * Performs handshake and validation between API client and API endpoint.
     *
     * Handles Basic Auth, API Key validation, IP range checks, and session/access token verification.
     *
     * @param string|int $apiKey The submitted API Key (expected from X-API-KEY header).
     *
     * @return static Returns the current object instance (chainable) on success, or throws an exception on failure.
     */
    private function _handshake(string|int $apiKey = 0): static
    {
        // --- 1. Basic Authentication (Fallback) ---
        // If no access token is provided and API token doesn't match encryption key, check Basic Auth.
        if (! $this->request->getHeaderLine('X-ACCESS-TOKEN') && ENCRYPTION_KEY !== $this->request->getHeaderLine('X-API-TOKEN')) {
            $authHeader = $this->request->getHeaderLine('Authorization');

            if (str_starts_with($authHeader, 'Basic ')) {
                $account = base64_decode(str_ireplace('Basic ', '', $authHeader));
                list($username, $password) = array_pad(explode(':', $account), 2, '');

                if ($username && $password) {
                    $authorize = $this->permission->authorize($username, $password);
                    if (is_bool($authorize) && $authorize) {
                        $this->_apiToken = true; // Auth succeeded
                    }
                }
            }
        }

        // Set client header to recognize as an AJAX/API request.
        $this->request->setHeader('X-Requested-With', 'XMLHttpRequest');

        // --- 2. Retrieve REST Client Configuration ---
        $client = $this->model->getWhere(
            'app__rest_clients',
            [
                'status' => 1,
                'api_key' => $apiKey,
                'valid_until >= ' => date('Y-m-d')
            ],
            1
        )->row();

        // Check if the request is made internally (same app, bypasses client table lookup).
        if (! $client && ENCRYPTION_KEY === $apiKey) {
            $client = (object) [
                'ip_range' => $this->request->getServer('SERVER_ADDR'),
                'method' => json_encode([$this->request->getMethod()]),
                'status' => 1
            ];
            $this->_apiToken = true;
        }

        // --- 3. Client Validation Checks (Denial Flow) ---
        if (! $client) {
            return throw_exception(403, phrase('Your API Key is not eligible to access the requested module or its already expired.'));
        } elseif (! $client->status) {
            return throw_exception(403, phrase('Your API Key is temporary deactivated.'));
        } elseif (! in_array($this->request->getMethod(), json_decode($client->method, true), true)) {
            return throw_exception(403, phrase('Your API Key is not eligible to use the method') . ': ' . $this->request->getMethod());
        } elseif ($client->ip_range && (! $this->_ipInRange($client->ip_range) || $this->request->getIPAddress() != $this->request->getServer('SERVER_ADDR'))) {
            return throw_exception(403, phrase('Your API Client is not permitted to access the requested source.'));
        }

        // --- 4. Session/Access Token Verification ---
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $accessToken = get_userdata('access_token') ?? $this->request->getHeaderLine('X-ACCESS-TOKEN');
        $clientIp = ($this->request->hasHeader('x-forwarded-for') ? $this->request->getHeaderLine('x-forwarded-for') : $this->request->getIPAddress());

        // Retrieve session data using the access token.
        $cookie = $this->model->select('data')->getWhere(
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
        if ($cookie && 'Postgre' === $this->_dbDriver) {
            $cookie = pg_unescape_bytea($cookie);
        }

        // Decode and restore session data if valid.
        if ($cookie && session_decode($cookie)) {
            $this->_apiToken = true;
            set_userdata(array_filter($_SESSION));
            $this->_setLanguage(get_userdata('language_id'));
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
        $this->apiClient = true;

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
    private function _ipInRange(array|string $whitelist = []): bool
    {
        // Ensure whitelist is an array, converting from a comma-separated string if necessary.
        if (! is_array($whitelist)) {
            $whitelist = array_map('trim', explode(',', $whitelist));
        }

        $clientIp = Services::request()->getServer('REMOTE_ADDR');

        // 1. Check for Exact IP Match
        if (in_array($clientIp, $whitelist, true)) {
            return true;
        }

        // 2. Check for Wildcard Match
        foreach ($whitelist as $whitelistedIp) {
            $wildcardPos = strpos($whitelistedIp, '*');

            if (false !== $wildcardPos) {
                // Check if the beginning part of the client IP matches the non-wildcard part of the whitelisted IP.
                $ipPrefix = substr($whitelistedIp, 0, $wildcardPos);

                if (str_starts_with($clientIp, $ipPrefix)) {
                    // Check if the whitelisted IP is just the prefix + wildcard (e.g., "192.168.1.*").
                    // The original logic simplified: substr($clientIp, 0, $wildcardPos) . '*' == $whitelistedIp
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
    private function _pushLog(): void
    {
        // 1. Check and reset time-based counters first (Daily, Weekly, etc.).
        $this->_autoResetCounters();

        $userAgent = Services::request()->getUserAgent();
        $agentString = (string) $userAgent;

        // User agent detection
        if ($userAgent->isBrowser()) {
            $agentString = $userAgent->getBrowser() . ' ' . $userAgent->getVersion();
        } elseif ($userAgent->isRobot()) {
            $agentString = $userAgent->getRobot();
        } elseif ($userAgent->isMobile()) {
            $agentString = $userAgent->getMobile();
        }

        // Prepare log data
        $prepare = [
            'ip_address' => ($this->request->hasHeader('x-forwarded-for') ? $this->request->getHeaderLine('x-forwarded-for') : $this->request->getIPAddress()),
            'browser' => $agentString,
            'platform' => $userAgent->getPlatform(),
            'timestamp' => date('Y-m-d H:i:s')
        ];

        // 2. Check if this IP has visited TODAY
        $today = date('Y-m-d');
        $query = $this->model->getWhere('app__log_visitors', [
            'ip_address' => $prepare['ip_address'],
            'DATE(timestamp)' => $today  // Check if already visited today
        ], 1)->row();

        if (! $query) {
            // Visitor hasn't visited today (could be new or returning visitor)
            try {
                $logInsert = $this->model->insert('app__log_visitors', $prepare);

                if (! $logInsert) {
                    // Trap suspicious access if insertion fails.
                    file_put_contents(WRITEPATH . 'logs/log-' . date('Y-m-d') . '.txt', current_page() . PHP_EOL . json_encode($prepare) . PHP_EOL, FILE_APPEND | LOCK_EX);
                } else {
                    // Update all counters for a new visitor today
                    $this->_updateVisitCounters(['daily', 'weekly', 'monthly', 'yearly', 'whole']);
                }
            } catch (Throwable $e) {
                // Safe abstraction (logging the error can be added here)
            }
        }
        // If visitor already came today, don't increment counters
    }

    /**
     * Increments the visit counters in the app__stats table for the specified periods.
     *
     * @param array $periods Array of period strings (e.g., ['daily', 'weekly']).
     */
    private function _updateVisitCounters(array $periods = []): void
    {
        if (empty($periods)) {
            return;
        }

        // Build the SQL increment query for each specified period.
        foreach ($periods as $period) {
            $field = $period . '_visits';
            $this->model->set($field, "$field + 1", false);
        }

        // Update the app__stats table (typically a single-row table).
        $this->model->update('app__stats');
    }

    /**
     * Automatically resets visit counters (daily, weekly, monthly, yearly) based on date comparison.
     */
    private function _autoResetCounters(): void
    {
        // Retrieve the single row statistics data.
        $stats = $this->model->get('app__stats', 1)->row();

        if (! $stats) {
            $initialData = [
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

            $this->model->insert('app__stats', $initialData);
            $stats = (object) $initialData;
        }

        $today = date('Y-m-d');
        $currentWeek = date('Y-W');
        $currentMonth = date('Y-m');
        $currentYear = date('Y');

        $updates = [];

        // Reset daily if day changed
        if (($stats->last_daily_reset ?? null) !== $today) {
            $updates['daily_visits'] = 0;
            $updates['last_daily_reset'] = $today;
        }

        // Reset weekly if week changed
        $lastWeeklyResetWeek = ($stats->last_weekly_reset ? date('Y-W', strtotime($stats->last_weekly_reset)) : null);
        if (! $lastWeeklyResetWeek || $lastWeeklyResetWeek !== $currentWeek) {
            $updates['weekly_visits'] = 0;
            $updates['last_weekly_reset'] = $today;
        }

        // Reset monthly if month changed
        $lastMonthlyResetMonth = ($stats->last_monthly_reset ? date('Y-m', strtotime($stats->last_monthly_reset)) : null);
        if (! $lastMonthlyResetMonth || $lastMonthlyResetMonth !== $currentMonth) {
            $updates['monthly_visits'] = 0;
            $updates['last_monthly_reset'] = $today;
        }

        // Reset yearly if year changed
        $lastYearlyResetYear = ($stats->last_yearly_reset ? date('Y', strtotime($stats->last_yearly_reset)) : null);
        if (! $lastYearlyResetYear || $lastYearlyResetYear !== $currentYear) {
            $updates['yearly_visits'] = 0;
            $updates['last_yearly_reset'] = $today;
        }

        if (! empty($updates)) {
            $this->model->update('app__stats', $updates);
        }
    }

    /**
     * Sets the application language based on user session, browser preference, or system default.
     *
     * @param string|null $languageId Language ID from the user session (or null if not set).
     */
    private function _setLanguage(?string $languageId = null): void
    {
        // Check if session language ID is not set.
        if (! get_userdata('language_id') || ! $languageId) {
            // Determine Initial Fallback Language ID
            $appLanguage = get_setting('app_language');
            $languageId = ($appLanguage > 0 ? $appLanguage : 1);

            // Get browser accepted locales (e.g., "en-US,en;q=0.9,id;q=0.8").
            $locales = explode(',', (Services::request()->getServer('HTTP_ACCEPT_LANGUAGE') ?: 'en-us'));

            // Retrieve available and active languages from the database.
            $languages = $this->model->getWhere('app__languages', ['status' => 1])->result();

            // Match Browser Locale to Available Languages
            foreach ($languages as $language) {
                $items = array_map('trim', explode(',', strtolower($language->locale))); // Available locales for this language

                foreach ($locales as $loc) {
                    if (in_array(strtolower(trim($loc)), $items, true)) {
                        $languageId = $language->id;

                        break 2; // Found match, break both loops.
                    }
                }
            }

            // Store the determined language ID in the user session.
            set_userdata('language_id', $languageId);
        }

        // Get the language code (e.g., 'en', 'id') from the determined ID.
        $languageCode = $this->model->select('code')
            ->getWhere('app__languages', ['id' => $languageId], 1)
            ->row('code');

        // Set language code to internal property.
        $this->_language = $languageCode;

        // Check if the corresponding language translation file directory exists.
        if (is_dir(APPPATH . 'Language' . DIRECTORY_SEPARATOR . $languageCode)) {
            // Set language code to session (legacy/redundant, but preserved).
            set_userdata('language', $languageCode);

            // Set locale to the framework's language service.
            Services::language()->setLocale($languageCode);
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
    private function _sanitizeInput(string $input = ''): string
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
