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
 * When the signs come, those who don't believe at "that time"
 * will have only two choices, commit suicide or become brutal.
 */

namespace Aksara\Laboratory;

use ReflectionMethod;
use Throwable;
use Config\Services;
use CodeIgniter\Controller;
use CodeIgniter\HTTP\Response;
use Aksara\Laboratory\Traits;
use Aksara\Laboratory\Model;
use Aksara\Laboratory\Permission;
use Aksara\Laboratory\Template;
use Aksara\Laboratory\Renderer\Renderer;
use Aksara\Libraries\Document;

/**
 * Core Controller for Aksara CMS.
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
     * @var \Aksara\Laboratory\Permission
     */
    protected $permission;

    /**
     * Request service instance.
     * @var \CodeIgniter\HTTP\IncomingRequest|null
     */
    protected $request;

    /**
     * Theme template properties.
     * @var object
     */
    public $template;

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
            header('Location: https://google.com?q=' . $ipAddress);
            exit;
        }

        // --- Load Dependencies ---
        helper(['url', 'file', 'theme', 'security', 'main', 'string', 'widget']);

        // Load core classes.
        $this->formValidation = Services::validation();
        $this->model = new Model();
        $this->permission = new Permission();

        // Assign active database driver.
        $this->_dbDriver = $this->model->dbDriver();

        // --- Route Initialization ---
        $router = Services::router();
        $path = ($router->getMatchedRoute()[0] ?? null);
        $this->_method = $router->methodName();

        // Assign the module path with smart method matching to handle snake_case / camelCase differences
        $lastSegment = ($path ? substr($path, (strrpos($path, '/') !== false ? strrpos($path, '/') + 1 : 0)) : '');
        if ($this->_method && $path && strtolower(str_replace(['_', '-'], '', $lastSegment)) === strtolower(str_replace(['_', '-'], '', $this->_method))) {
            $this->_module = (strrpos($path, '/') !== false ? substr($path, 0, strrpos($path, '/')) : $path);
        } else {
            $this->_module = $path;
        }

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
        if ('preview-theme' == $this->request->getGet('aksara_mode')) {
            $themeName = $this->request->getGet('aksara_theme');

            if (
                hash_equals(hash_hmac('sha256', $themeName . get_userdata('session_generated'), ENCRYPTION_KEY), (string) $this->request->getGet('integrity_check')) &&
                preg_match('/^[a-zA-Z0-9_-]+$/', $themeName) &&
                is_dir(ROOTPATH . 'themes/' . $themeName)
            ) {
                $this->_setTheme = $themeName;
            }
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

        // Unlink old captcha if any
        if (get_userdata('captcha_file') && file_exists(UPLOAD_PATH . DIRECTORY_SEPARATOR . 'captcha' . DIRECTORY_SEPARATOR . get_userdata('captcha_file'))) {
            try {
                unlink(UPLOAD_PATH . DIRECTORY_SEPARATOR . 'captcha' . DIRECTORY_SEPARATOR . get_userdata('captcha_file'));
            } catch (Throwable $e) {
                // Safe abstraction
            }
        }
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
        if (method_exists($this, $method) && ! in_array($method, get_class_methods('\Aksara\Laboratory\Core'))) {
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
        $this->_debugging = $resultType ?? 'parameter';

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
    public function validToken(?string $token): bool
    {
        $isPostRequest = Services::request()->getPost();

        // Must be a POST request.
        if ($isPostRequest) {
            // Check stored URI-based token match.
            if ($token && hash_equals((string) get_userdata(sha1(uri_string())), $token)) {
                return true;
            }

            // Regenerate the valid token using the centralized CSRF helper.
            $expectedToken = generate_csrf_token();

            if ($token && hash_equals($expectedToken, $token)) {
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
        if (in_array($this->_method, $this->_unsetMethod)) {
            return throw_exception(403, phrase('The method you requested is not acceptable.'));
        } elseif ($this->_setPermission && ! get_userdata('is_logged') && ! $this->_apiToken) {
            return throw_exception(403, phrase('Your session has been expired.'));
        } elseif (! $this->permission->allow($this->_module, $this->_method, get_userdata('user_id'), $redirect) && ! $this->_apiToken) {
            return throw_exception(403, phrase('You do not have sufficient privileges to access the requested page.'));
        } elseif ($permissiveGroup && ! in_array(get_userdata('group_id'), $permissiveGroup) && ! $this->_apiToken) {
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
        if (! in_array($theme, ['frontend', 'backend'])) {
            return false;
        }

        $this->_setTheme = get_setting($theme . '_theme');

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
        if (! is_array($params)) {
            $params = [
                $params => $value
            ];
        }

        $this->_setTemplate = array_merge($this->_setTemplate, $params);

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
        if (! is_array($params)) {
            $params = [
                $params => $value
            ];
        }

        $this->_setBreadcrumb = array_merge($this->_setBreadcrumb, $params);

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
        if (! is_array($params)) {
            if (! $fallback && strpos($params, '{{') === false && strpos($params, '}}') === false) {
                $fallback = $params;
            }

            $params = [
                'index' => $params
            ];
        }

        $this->_setTitle = array_merge($this->_setTitle, $params);
        $this->_setTitleFallback = $fallback;

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
        if (! is_array($params)) {
            if (! $fallback && strpos($params, '{{') === false && strpos($params, '}}') === false) {
                $fallback = $params;
            }

            $params = [
                'index' => $params
            ];
        }

        $this->_setDescription = array_merge($this->_setDescription, $params);
        $this->_setDescriptionFallback = $fallback;

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
        if (! is_array($params)) {
            if (! $fallback && strpos($params, '{{') === false && strpos($params, '}}') === false) {
                $fallback = $params;
            }

            $params = [
                'index' => $params
            ];
        }

        $this->_setIcon = array_merge($this->_setIcon, $params);
        $this->_setIconFallback = $fallback;

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
        ?bool $newTab = false
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
        string $url,
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
        string $url,
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
        string $url,
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

            // Loop through each field type (e.g., 'image', 'editable', 'custom')
            foreach ($types as $currentType) {
                // Define the structure for the current type, prioritizing dedicated array parameters
                // if the input structure was ['field_name' => ['custom' => ['parameter' => '...']]]
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
        if (! in_array($this->_method, ['create', 'update'])) {
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
        array $where = [],
        array $join = [],
        array $orderBy = [],
        ?string $groupBy = null,
        int $limit = 0,
        bool $translate = false
    ): static {
        $alias = $field;

        preg_match_all('/\{\{(.*?)\}\}/', $output ?? '', $matches);
        $select = array_map('trim', $matches[1]);

        if ($translate) {
            foreach ($select as $val) {
                $this->_translateField[] = substr(strstr($val, '.'), 1);
            }
        }

        $isComposite = strpos($field, ',') !== false && strpos($primaryKey, ',') !== false;

        $relationTable = null;
        $relationKeys = [];
        $fieldLocal = [];

        if ($isComposite) {
            $fieldLocal = array_map('trim', explode(',', $field));
            $primaryKeysForeign = array_map('trim', explode(',', $primaryKey));

            $alias = $fieldLocal[0];

            foreach ($primaryKeysForeign as $key => $val) {
                if (! in_array($val, $select, true)) {
                    $select[] = $val;
                }

                [$tableName, $keyName] = array_pad(explode('.', $val), 2, null);

                if ($tableName && $keyName) {
                    $relationTable = $tableName;
                    $relationKeys[] = $keyName;
                }

                $this->_unsetColumn[] = $keyName;
                $this->_unsetView[] = $keyName;

                if (0 === $key) {
                    array_unshift(
                        $select,
                        $relationTable . '.' . $fieldLocal[0] . ' AS ' . $alias . '_masking'
                    );
                }
            }
        } else {
            $fieldLocal = $field;

            if (! in_array($primaryKey, $select, true)) {
                $select[] = (
                    strpos($primaryKey, ' ') !== false
                        ? substr($primaryKey, strpos($primaryKey, ' ') + 1)
                        : $primaryKey
                ) . ' AS ' . $alias;
            }

            if (isset($this->_setAttribute[$field])) {
                preg_match_all('/\{\{(.*?)\}\}/', $this->_setAttribute[$field] ?? '', $matchesAttributes);
                $select = array_merge($select, array_map('trim', $matchesAttributes[1]));
            }

            $parts = explode('.', $primaryKey);
            $relationTable = $parts[0] ?? null;
            $relationKeys = $parts[1] ?? null;

            if ($field !== $relationKeys) {
                $this->_unsetColumn[] = $field;
                $this->_unsetView[] = $field;
            }
        }

        // Standarize JOIN
        if ($join && ! isset($join[0])) {
            $join = [$join];
        }

        // Remove JOIN table that is not used in SELECT, WHERE, ORDER, GROUP
        $usedTables = [];

        foreach ($select as $val) {
            if (preg_match_all('/\b([a-zA-Z_][a-zA-Z0-9_]*)\./', $val, $m)) {
                $usedTables = array_merge($usedTables, $m[1]);
            }
        }

        foreach (array_keys($where) as $key) {
            if (preg_match_all('/\b([a-zA-Z_][a-zA-Z0-9_]*)\./', $key, $m)) {
                $usedTables = array_merge($usedTables, $m[1]);
            }
        }

        foreach (array_keys($orderBy) as $key) {
            if (preg_match_all('/\b([a-zA-Z_][a-zA-Z0-9_]*)\./', $key, $m)) {
                $usedTables = array_merge($usedTables, $m[1]);
            }
        }

        if ($groupBy && preg_match_all('/\b([a-zA-Z_][a-zA-Z0-9_]*)\./', $groupBy, $m)) {
            $usedTables = array_merge($usedTables, $m[1]);
        }

        $usedTables = array_unique($usedTables);

        if ($join) {
            $join = array_filter($join, function ($val) use ($usedTables) {
                $table = trim($val[0]);

                $parts = preg_split('/\s+/', $table);
                $tableName = $parts[0] ?? $table;
                $aliasName = $parts[1] ?? $tableName;

                return in_array($tableName, $usedTables, true)
                    || in_array($aliasName, $usedTables, true);
            });

            $join = array_values($join);
        }

        $this->_select = array_unique(array_merge($this->_select ?? [], $select));

        if (! in_array($this->_method, ['create', 'update', 'delete'], true)) {
            $condition = '';
            $relationTableClean = $relationTable;

            if (strpos($relationTable, ' ') !== false) {
                [, $relationTableClean] = explode(' ', $relationTable);
            }

            if (is_array($fieldLocal)) {
                foreach ($fieldLocal as $key => $val) {
                    $fkKey = $relationKeys[$key] ?? $val;

                    $condition .= ($condition ? ' AND ' : '')
                        . $relationTableClean . '.' . $fkKey
                        . ' = __PRIMARY_TABLE__.' . $val;

                    $this->setValidation(
                        $val,
                        'relation_checker[' . $relationTableClean . '.' . $fkKey . ']'
                    );
                }
            } else {
                $condition = $relationTableClean . '.' . $relationKeys
                    . ' = __PRIMARY_TABLE__.' . $fieldLocal;

                $this->setValidation(
                    $fieldLocal,
                    'relation_checker[' . $relationTableClean . '.' . $relationKeys . ']'
                );
            }

            $this->_compiledTable[] = $relationTable;
            $this->_join[$relationTable] = [
                'condition' => $condition,
                'type' => 'LEFT',
                'escape' => true
            ];
        }

        if ($join && ! in_array($this->_method, ['create', 'update', 'delete'], true)) {
            // Add additional JOINs from the $join parameter, ensuring they are properly formatted
            foreach ($join as $key => $val) {
                $this->_join[$val[0]] = [
                    'condition' => $val[1],
                    'type' => $val[2] ?? '',
                    'escape' => true
                ];

                $this->_compiledTable[] = $val[0];
            }
        }

        $finalLimit = is_numeric($limit) && $limit > 0 ? $limit : $this->_limit;

        $offset = (
            is_numeric(Services::request()->getPost('page'))
                ? Services::request()->getPost('page') - 1
                : 0
        ) * $finalLimit;

        $this->_setRelation[$alias] = [
            'select' => $select,
            'primaryKey' => $fieldLocal,
            'relationTable' => $relationTable,
            'relationKey' => $relationKeys,
            'where' => $where,
            'join' => $join,
            'orderBy' => $orderBy,
            'groupBy' => $groupBy,
            'limit' => $finalLimit,
            'offset' => $offset,
            'output' => $output,
            'translate' => $translate
        ];

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
        $isNotCrud = ! in_array($this->_method, ['create', 'update', 'delete']);
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
            'primaryKey' => $field,
            'relationTable' => $relationTable,
            'relationKey' => $relationKey,
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

        if ($this->apiClient && (! $this->request->getGet('format_result') || ! in_array($this->request->getGet('format_result'), ['field_data', 'complete', 'full']))) {
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

        if (! $data) {
            $data = array_map(fn ($v) => '', array_flip(array_keys($fieldData)));
        }

        $output = [];

        foreach ($data as $field => $value) {
            $hidden = false;

            // Attempt to get the type
            $type = strtolower((isset($fieldData[$field]->type) ? $fieldData[$field]->type : gettype($value)));

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
            } elseif (in_array($type, ['enum']) && in_array($this->_dbDriver, ['MySQLi']) && ! isset($this->_setField[$field])) {
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
            $content = (in_array($field, $this->_translateField) ? phrase($value) : $value);

            if ('create' == $this->_method) {
                $content = (isset($this->_setDefault[$field]) ? $this->_setDefault[$field] : (isset($fieldData[$field]->default) ? $fieldData[$field]->default : null));
                $value = null;
            }

            if (in_array($this->_method, ['create', 'update']) && (in_array($field, $this->_unsetField) || array_intersect(['current_timestamp', 'created_timestamp', 'updated_timestamp'], array_keys($this->_setField[$field])))) {
                // Indicates that field should not be shown
                $hidden = true;
            } elseif (('read' == $this->_method || (in_array($this->_method, ['print', 'pdf']))) && in_array($field, $this->_unsetView)) {
                // Indicates that field should not be shown
                $hidden = true;
            } elseif (in_array($this->_method, ['index', 'export', 'print', 'pdf']) && in_array($field, $this->_unsetColumn)) {
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
                if (in_array($type, ['money', 'percent'])) {
                    // Add extra dot to maxlength
                    $maxlength = ($maxlength + 1);
                }

                $validation[] = 'max_length[' . $maxlength . ']';
            }

            // Call assigned method of custom format
            if (
                isset($this->_setField[$field])
                && in_array('custom', array_keys($this->_setField[$field]))
                && method_exists($this, $this->_setField[$field]['custom']['parameter'])
            ) {
                if (
                    (in_array($this->_method, ['index']) && ! in_array($field, $this->_unsetColumn))
                    || (in_array($this->_method, ['create', 'update']) && ! in_array($field, $this->_unsetField))
                    || (in_array($this->_method, ['read']) && ! in_array($field, $this->_unsetRead))
                ) {
                    // Get callback method
                    $method = $this->_setField[$field]['custom']['parameter'];
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
                'primary' => in_array($field, $this->_setPrimary),
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
            } elseif (in_array($this->request->getMethod(), ['POST', 'DELETE']) &&
            ! in_array($this->_method, ['create', 'update', 'delete'])) {
                // Check if request is made from promise
                return throw_exception(403, phrase('The method you requested is not acceptable.') . ' (' . $this->request->getMethod() . ')', (! $this->apiClient ? go_to() : null));
            }
        } elseif ($table && ! $this->_setPermission) {
            // Unset database modification because no permission is set
            $this->unsetMethod('create, update, delete');

            if (in_array($this->_method, ['create', 'update', 'delete'])) {
                // Throw exception about the method
                return throw_exception(403, phrase('You do not have sufficient privileges to access the requested page.') . ' (' . strtoupper($this->_method). ')', (! $this->apiClient ? go_to() : null));
            }
        }

        if (! $this->_table) {
            // Set table when not present
            $this->_table = $table;

            // Push to compiled table
            $this->_compiledTable[] = $table;
        }

        if (! $this->request->getPost('_token')) {
            // Set CSRF Token
            $this->_token = generate_csrf_token();

            // There may be a form without using form renderer
            // Set CSRF Token into unique session key
            set_userdata(sha1(uri_string()), $this->_token);
        }

        // Validate the restricted action
        if (in_array($this->_method, $this->_unsetMethod)) {
            return throw_exception(403, phrase('You are not allowed to perform the requested action.'), go_to());
        }

        // Check before action
        if ('create' == $this->_method && method_exists($this, 'beforeInsert')) {
            // Before insert
            $this->beforeInsert();
        } elseif ('update' == $this->_method && method_exists($this, 'beforeUpdate')) {
            // Before update
            $this->beforeUpdate();
        } elseif ('delete' == $this->_method && method_exists($this, 'beforeDelete')) {
            // Before delete
            $this->beforeDelete();
        }

        // Load template class
        $this->template = new Template($this->_setTheme, $this->_method);

        // Load template parser
        $renderer = new Renderer();

        // Send necessary properties
        $renderer->setProperty(['_setTheme' => $this->template->theme]);

        // Set core component path
        $renderer->setPath('core');

        // Create core component if not exists
        $renderer->render();

        // Query string filters
        $queryParams = $this->request->getGet();

        // Token Validation
        if ($queryParams && ENCRYPTION_KEY !== $this->request->getHeaderLine('X-API-KEY')) {
            // Apply validation for protected page from non API client request
            if ($this->_setPermission && ! $this->apiClient) {
                $expectedToken = generate_token(uri_string(), $queryParams);
                $submittedToken = $this->request->getGet('aksara');

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
                if (isset($val->primary_key) && $val->primary_key && ! in_array($val->name, $this->_setPrimary)) {
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
                    if (in_array($val->type, ['PRIMARY', 'UNIQUE'])) {
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
                if ('backend' == $this->template->getThemeProperty('type')) {
                    // Add notification into table heading
                    $this->setDescription('<div><b>' . phrase('No primary key is found.') . '</b> ' . phrase('Please define it manually and refer to {{set_primary}}.', ['set_primary' => '<code>set_primary()</code>']) . ' ' . phrase('Without primary key, you only allowed to insert the data.') . '</div>');
                }

                // Unset method
                $this->unsetMethod('update, delete');
            }

            // Remove primary query string if method is matched
            foreach ($queryParams as $key => $val) {
                if (in_array($this->_method, ['read', 'update', 'delete']) && in_array($key, $this->_setPrimary)) {
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
                            ('read' == $this->_method && isset($this->_unsetRead[$val]) && in_array($this->request->getGet($val), $this->_unsetRead[$val])) ||
                            ('update' == $this->_method && isset($this->_unsetUpdate[$val]) && in_array($this->request->getGet($val), $this->_unsetUpdate[$val])) ||
                            ('delete' == $this->_method && isset($this->_unsetDelete[$val]) && in_array($this->request->getGet($val), $this->_unsetDelete[$val])) ||
                            ('export' == $this->_method && isset($this->_unsetRead[$val]) && in_array($this->request->getGet($val), $this->_unsetRead[$val])) ||
                            ('print' == $this->_method && isset($this->_unsetRead[$val]) && in_array($this->request->getGet($val), $this->_unsetRead[$val])) ||
                            ('pdf' == $this->_method && isset($this->_unsetRead[$val]) && in_array($this->request->getGet($val), $this->_unsetRead[$val]))
                        ) {
                            if (in_array($this->_method, ['read', 'export', 'print', 'pdf'])) {
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

                        if (! in_array($val, array_keys($this->_where ?? []))) {
                            // Push where into prepared statement only if where is not defined in controller
                            $this->_prepare('where', [$this->_table . '.' . $val, htmlspecialchars($this->request->getGet($val))]);
                        }
                    } elseif (
                        in_array($val, $this->_setPrimary) &&
                        $this->model->fieldExists($val, $this->_table) &&
                        isset($this->_setDefault[$val]) &&
                        $this->_setDefault[$val]
                    ) {
                        if (
                            ('read' == $this->_method && isset($this->_unsetRead[$val]) && in_array($this->request->getGet($val), $this->_unsetRead[$val])) ||
                            ('update' == $this->_method && isset($this->_unsetUpdate[$val]) && in_array($this->request->getGet($val), $this->_unsetUpdate[$val])) ||
                            ('delete' == $this->_method && isset($this->_unsetDelete[$val]) && in_array($this->request->getGet($val), $this->_unsetDelete[$val])) ||
                            ('export' == $this->_method && isset($this->_unsetRead[$val]) && in_array($this->request->getGet($val), $this->_unsetRead[$val])) ||
                            ('print' == $this->_method && isset($this->_unsetRead[$val]) && in_array($this->request->getGet($val), $this->_unsetRead[$val])) ||
                            ('pdf' == $this->_method && isset($this->_unsetRead[$val]) && in_array($this->request->getGet($val), $this->_unsetRead[$val]))
                        ) {
                            if (in_array($this->_method, ['read', 'export', 'print', 'pdf'])) {
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
                        $fields = array_keys(array_flip($this->model->listFields($this->_table)));
                        if (in_array('key', $fields) && in_array('value', $fields) && ! in_array('app_name', $fields)) {
                            $fields = array_merge($fields, array_keys($this->request->getPost()), array_keys($_FILES));
                        }
                        $fieldData = array_fill_keys($fields, '');

                        // Or use the master validation instead
                        return $this->validateForm($fieldData);
                    }
                } else {
                    // Token isn't valid, throw exception
                    return throw_exception(403, phrase('The submitted token has been expired or the request is made from the restricted source.'), $this->_redirectBack);
                }
            } elseif ($this->apiClient && in_array($this->request->getMethod(), ['POST']) && (in_array($this->_method, ['create', 'update']) || ($this->_formCallback && method_exists($this, $this->_formCallback)))) {
                // Request is sent from REST
                if ($this->_formCallback && method_exists($this, $this->_formCallback)) {
                    // Use callback as form validation
                    $_callback = $this->_formCallback;

                    return $this->$_callback();
                } else {
                    // Serialize table data
                    $fields = array_keys(array_flip($this->model->listFields($this->_table)));
                    if (in_array('key', $fields) && in_array('value', $fields) && ! in_array('app_name', $fields)) {
                        $fields = array_merge($fields, array_keys($this->request->getPost()), array_keys($_FILES));
                    }
                    $fieldData = array_fill_keys($fields, '');

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
                if (! in_array($this->_method, ['create', 'read', 'update', 'delete']) && is_numeric($this->request->getGet('per_page')) && $this->request->getGet('per_page') > 1 && (! $this->_offsetCalled || (! $this->_offset && gettype($this->_offset) !== 'integer'))) {
                    $this->_offset = ($this->request->getGet('per_page') - 1) * ($this->_limit ?? $this->_limitBackup);
                }

                if ($this->_offset) {
                    // Push offset to the prepared query builder
                    $this->_prepare('offset', [$this->_offset]);
                }

                if (! in_array($this->_method, ['create', 'read', 'update', 'delete']) &&
                    ($this->_searchable && $this->request->getGet('q')) ||
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
                                $this->_prepare('like', [$validColumn, $searchQuery, 'both', true, true]);
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
                                if (in_array($val, $this->_unsetColumn)) {
                                    // Skip unset columns
                                    continue;
                                }

                                // Add table name prefix if not present to prevent ambiguity
                                if (strpos($val, '.') === false && strpos($val, '(') === false && strpos($val, ')') === false) {
                                    $val = $this->_table . '.' . $val;
                                }

                                $this->_prepare(($key ? 'orLike' : 'like'), [$val, $searchQuery, 'both', true, true]);
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
                                if (! $key || in_array($fieldOrigin, $compiledLike)) {
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
                                    $this->_prepare($condition['type'], [$condition['field'], $condition['query'], 'both', true, true]);
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

                if (isset($this->_setField[$this->request->getPost('origin')]) && in_array('autocomplete', $this->_setField[$this->request->getPost('origin')]['field_type'])) {
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

                        if (! $key || in_array($fieldOrigin, $compiledLike)) {
                            continue;
                        }

                        // Push like an or like to the prepared query builder
                        $this->_prepare(($key ? 'orLike' : 'like'), [$val, htmlspecialchars(('autocomplete' == $this->request->getPost('method') && $this->request->getPost('q') ? $this->request->getPost('q') : $this->request->getGet('q'))), 'both', true, true]);

                        if (isset($this->_setField[$this->request->getPost('origin')]['parameter'])) {
                            if (is_array($this->_setField[$this->request->getPost('origin')]['parameter'])) {
                                $table = $this->_setField[$this->request->getPost('origin')]['parameter'][0];
                            } else {
                                $table = $this->_setField[$this->request->getPost('origin')]['parameter'];
                            }
                        }

                        if (isset($this->_setField[$this->request->getPost('origin')]['parameter']) && $this->model->fieldExists(($val && stripos($val, '.') !== false ? substr($val, strripos($val, '.') + 1) : $val), $table)) {
                            // Push order by best match to the prepared query builder
                            $queryString = ('autocomplete' == $this->request->getPost('method') && $this->request->getPost('q') ? $this->request->getPost('q') : $this->request->getGet('q'));
                            $queryString = str_replace("'", "''", $queryString);
                            $queryString = str_replace('\\', '\\\\', $queryString);
                            $queryString = str_replace(['%', '_'], ['\\%', '\\_'], $queryString);

                            $this->_prepare('orderBy', ['(CASE WHEN ' . $val . ' LIKE \'%' . $queryString . '%\' ESCAPE \'\\\' THEN 1 WHEN ' . $val . ' LIKE \'%' . $queryString . '\' ESCAPE \'\\\' THEN 3 ELSE 2 END)']);
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
                            if (($value['original'] && strpos(strtolower($value['original']), strtolower($this->request->getPost('q'))) === false) || in_array($field, $this->_unsetColumn)) {
                                continue;
                            }

                            // Everything's looks good, throw into autocomplete result
                            if (! $autocompleteItem && $value['original'] && ! in_array($value['content'], $addedItem)) {
                                $addedItem[] = $value['content'];

                                $autocompleteItem = [
                                    'value' => truncate($value['content'], 32),
                                    'label' => truncate($value['content'], 32),
                                    'target' => current_page(null, ['per_page' => null, 'q' => truncate($value['content'], 32)])
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
                // Order DESC from query string
                set_userdata('sortOrder', 'DESC');
            } else {
                // Order ASC from query string
                set_userdata('sortOrder', 'ASC');
            }

            $orderField = $this->request->getGet('order');
            if ($orderField) {
                // If the ordered field is a merged column, apply sorting to all its component fields
                if (isset($this->_mergeContent[$orderField])) {
                    $orderFieldsToApply = $this->_mergeContent[$orderField]['column'];
                } else {
                    $orderFieldsToApply = [$orderField];
                }

                foreach ($orderFieldsToApply as $currentOrderField) {
                    $checkField = $currentOrderField;
                    if (substr($checkField, -8) === '_masking') {
                        $checkField = substr($checkField, 0, -8);
                    }

                    if ($this->model->fieldExists($checkField, $this->_table)) {
                        // Match order by the primary table
                        // Push order to the prepared query builder
                        $this->_prepare[] = [
                            'function' => 'orderBy',
                            'arguments' => [$this->_table . '.' . $checkField, get_userdata('sortOrder')]
                        ];
                    } elseif ($this->_compiledTable) {
                        // Otherwhise, find it from the relation table
                        foreach ($this->_compiledTable as $key => $dbTable) {
                            // Validate the column to check if column is exist in table
                            if ($this->model->fieldExists($checkField, $dbTable)) {
                                if (strpos($dbTable, ' ') !== false && strpos($dbTable, '(') == false && strpos($dbTable, ')') == false) {
                                    // Get original table
                                    $dbTable = explode(' ', $dbTable)[1];
                                }

                                // Push order to the prepared query builder
                                $this->_prepare[] = [
                                    'function' => 'orderBy',
                                    'arguments' => [$dbTable . '.' . $checkField, get_userdata('sortOrder')]
                                ];
                                break;
                            }
                        }
                    }
                }
            }

            if (in_array($this->_method, ['create'])) {
                // List the field properties
                $results = array_fill_keys(array_keys(array_flip($this->model->listFields($this->_table))), '');
                $total = 0;
            } else {
                $singleRow = false;

                if (in_array($this->_method, ['read', 'update']) || (in_array($this->_method, ['export', 'print', 'pdf']) && array_intersect_key($this->request->getGet(), array_flip($this->_setPrimary)))) {
                    // Request single row
                    $singleRow = true;
                }

                // Run query using prepared property
                $query = $this->_fetch($this->_table, $singleRow);
                $results = $query['results'];
                $total = $query['total'];
            }

            // Default icon property
            $icon = (isset($this->_setIcon[$this->_method]) ? $this->_setIcon[$this->_method] : (isset($this->_setIcon['index']) ? $this->_setIcon['index'] : null));

            // Default title property
            $title = (isset($this->_setTitle[$this->_method]) ? $this->_setTitle[$this->_method] : (isset($this->_setTitle['index']) ? $this->_setTitle['index'] : null));

            // Default description property
            $description = (isset($this->_setDescription[$this->_method]) ? $this->_setDescription[$this->_method] : (isset($this->_setDescription['index']) ? $this->_setDescription['index'] : null));

            // Indicates multiple rows result
            if (is_object($results) || ((is_array($results) && isset($results[0])))) {
                if (is_object($results)) {
                    $result = $results;
                } elseif (is_array($results) && isset($results[0])) {
                    $result = $results[0];
                }

                // Extract magic string
                preg_match_all('/\{\{(.*?)\}\}/', $title ?? '', $titleReplace);
                preg_match_all('/\{\{(.*?)\}\}/', $description ?? '', $descriptionReplace);
                preg_match_all('/\{\{(.*?)\}\}/', $icon ?? '', $iconReplace);

                foreach ($titleReplace[1] as $index => $replace) {
                    $replacement = trim($replace);

                    if (isset($result->$replacement)) {
                        // Attempt to convert the magic string and replace with the result
                        $title = preg_replace("/\{\{(\s+)?($replace)(\s+)?\}\}/", $result->$replacement, $title);
                    }
                }

                foreach ($descriptionReplace[1] as $index => $replace) {
                    $replacement = trim($replace);

                    if (isset($result->$replacement)) {
                        // Attempt to convert the magic string and replace with the result
                        $description = preg_replace("/\{\{(\s+)?($replace)(\s+)?\}\}/", $result->$replacement, $description);
                    }
                }

                foreach ($iconReplace[1] as $index => $replace) {
                    $replacement = trim($replace);

                    if (isset($result->$replacement)) {
                        // Attempt to convert the magic string and replace with the result
                        $icon = preg_replace("/\{\{(\s+)?($replace)(\s+)?\}\}/", $result->$replacement, $icon);
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
                $this->_view = (is_array($this->_setTemplate) && isset($this->_setTemplate['form']) ? $this->_setTemplate['form'] : ($view && 'index' != $view ? $view : 'form'));

                // Get formatted results
                $results = $this->renderForm($results);

                // Set icon property
                $this->_setIcon = ($this->_setMethod || (isset($this->_setIcon[$this->_method])) && $icon ? $icon : 'mdi mdi-plus');

                // Set title property
                $this->_setTitle = ($this->_setMethod || (isset($this->_setTitle[$this->_method])) && $title ? $title : phrase('Add New Data'));

                // Set description property
                $this->_setDescription = ($this->_setMethod || (isset($this->_setDescription[$this->_method])) && $description ? $description : phrase('Please fill all required field below to add new data.'));
            } elseif ('read' == $this->_method) {
                /**
                 * -------------------------------------------------------------
                 * Method is read
                 * -------------------------------------------------------------
                 */
                // Set view template property
                $this->_view = (is_array($this->_setTemplate) && isset($this->_setTemplate[$this->_method]) ? $this->_setTemplate['read'] : ($view && 'index' != $view ? $view : 'read'));

                // Get formatted results
                $results = $this->renderRead($results);

                // Set icon property
                $this->_setIcon = ($this->_setMethod || (isset($this->_setIcon[$this->_method])) && $icon ? $icon : 'mdi mdi-magnify');

                // Set title property
                $this->_setTitle = ($this->_setMethod || (isset($this->_setTitle[$this->_method])) && $title ? $title : phrase('Showing Data'));

                // Set description property
                $this->_setDescription = ($this->_setMethod || (isset($this->_setDescription[$this->_method])) && $description ? $description : phrase('Showing the result of requested data.'));
            } elseif ('update' == $this->_method) {
                /**
                 * -------------------------------------------------------------
                 * Method is update
                 * -------------------------------------------------------------
                 */
                // Set view template property
                $this->_view = (is_array($this->_setTemplate) && isset($this->_setTemplate['form']) ? $this->_setTemplate['form'] : ($view && 'index' != $view ? $view : 'form'));

                // Get formatted results
                $results = $this->renderForm($results ?? []);

                // Set icon property
                $this->_setIcon = ($this->_setMethod || (isset($this->_setIcon[$this->_method])) && $icon ? $icon : 'mdi mdi-square-edit-outline');

                // Set title property
                $this->_setTitle = ($this->_setMethod || (isset($this->_setTitle[$this->_method])) && $title ? $title : phrase('Update Data'));

                // Set description property
                $this->_setDescription = ($this->_setMethod || (isset($this->_setDescription[$this->_method])) && $description ? $description : phrase('Make sure to check the changes before submitting.'));
            } elseif (in_array($this->_method, ['export', 'print', 'pdf'])) {
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
                $this->_view = (isset($this->_setTemplate[$this->_method]) ? $this->_setTemplate[$this->_method] : $this->_method);

                // Get formatted results
                $results = ($singlePrint ? $this->renderRead($results) : $this->renderTable($results));

                // Set icon property
                $this->_setIcon = ($icon ? $icon : ($this->_setTitleFallback ?? 'mdi mdi-table'));

                // Set title property
                $this->_setTitle = ($title ? $title : ($this->_setTitleFallback ?? phrase('Page not found!')));

                // Set description property
                $this->_setDescription = ($description ? $description : $this->_setDescriptionFallback);
            } else {
                /**
                 * -------------------------------------------------------------
                 * Non listed method
                 * -------------------------------------------------------------
                 */
                // Check if master view template is used
                $viewExist = (! in_array($this->template->getView($this->_view), ['../../aksara/Views/core/index', '../../themes/' . $this->template->theme . '/components/core/index', '../../aksara/Views/core/index_grid', '../../themes/' . $this->template->theme . '/components/core/index_grid', '../../aksara/Views/core/index_mobile', '../../themes/' . $this->template->theme . '/components/core/index_mobile', '../../aksara/Views/core/error', '../../themes/' . $this->template->theme . '/components/core/error']));

                // Set description property
                $this->_view = (is_array($this->_setTemplate) && isset($this->_setTemplate['index']) ? $this->_setTemplate['index'] : ($view && 'index' != $view ? $view : 'index'));

                // Get formatted results
                $results = (! $viewExist ? $this->renderTable($results) : $results);

                // Set icon property
                $this->_setIcon = ($icon ? $icon : ($this->_setTitleFallback ?? 'mdi mdi-table'));

                // Set title property
                $this->_setTitle = ($title ? $title : ($this->_setTitleFallback ?? phrase('Page not found!')));

                // Set description property
                $this->_setDescription = ($description ? $description : $this->_setDescriptionFallback);
            }
        } else {
            /**
             * -------------------------------------------------------------
             * No database table are included to renderer
             * -------------------------------------------------------------
             */
            // Check if request is submitting POST
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
                    }
                }
            }

            // Set template view property
            $this->_view = (is_array($this->_setTemplate) && isset($this->_setTemplate['index']) ? $this->_setTemplate['index'] : $this->_method);

            // Set default result
            $results = [];
            $total = 0;

            // Default icon property
            $this->_setIcon = (isset($this->_setIcon[$this->_method]) ? $this->_setIcon[$this->_method] : (isset($this->_setIcon['index']) ? $this->_setIcon['index'] : 'mdi mdi-file-document-outline'));

            // Default title property
            $this->_setTitle = (isset($this->_setTitle[$this->_method]) ? $this->_setTitle[$this->_method] : (isset($this->_setTitle['index']) ? $this->_setTitle['index'] : phrase('Untitled')));

            // Default description property
            $this->_setDescription = (isset($this->_setDescription[$this->_method]) ? $this->_setDescription[$this->_method] : (isset($this->_setDescription['index']) ? $this->_setDescription['index'] : null));

            if ($this->request->getGet('__fetch_metadata') && $this->apiClient) {
                return make_json([
                    'title' => $this->_setTitle,
                    'description' => $this->_setDescription,
                    'icon' => $this->_setIcon
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
            if (in_array($this->_method, ['read', 'update']) && in_array($key, $this->_setPrimary)) {
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
                'description' => preg_replace('/[^\S ]+/', '', $this->_setDescription ?? ''),
                'icon' => $this->_setIcon,
                'title' => $this->_setTitle,
                'modal_size' => ($this->_modalSize ? $this->_modalSize : ''),
                'segmentation' => array_map(function ($segment = null) {
                    return str_replace('.', '-', preg_replace('/[^a-zA-Z0-9]/', '_', $segment));
                }, $uri->getSegments())
            ],
            'breadcrumb' => $this->template->breadcrumb($this->_setBreadcrumb, $this->_setTitle, $this->_setPrimary),
            'links' => [
                'base_url' => base_url(),
                'current_module' => go_to(null, $queryParams),
                'current_page' => current_page()
            ],
            'query_params' => $this->request->getGet(),
            'results' => $results,
            '_token' => $this->_token
        ];

        if (in_array($this->_method, ['create', 'read', 'update'])) {
            unset($output['total']);
        } else {
            // Add pagination
            $output['pagination'] = $this->template->pagination([
                'total' => $total,
                'per_page' => $this->_limit,
                'limit' => $this->_limitBackup,
                'offset' => $this->_offset,
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
        if (in_array($this->_method, ['print', 'export', 'pdf'])) {
            $document = new Document();

            $document->pageSize('13in 8.5in');

            $output = view('templates/export', (array) json_decode(json_encode($output)));

            // Send to client
            if ('print' == $this->_method) {
                return $this->response->setBody($output)->sendBody();
            } else {
                return $document->generate($output, $title, ('export' == $this->_method ? ($this->request->getGet('method') ?? 'export') : 'embed'));
            }
        } elseif ($this->apiClient && ! in_array($this->request->getMethod(), ['GET'])) {
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

        // Benchmark dumping
        if ('benchmark' === $this->_debugging) {
            $timers = Services::timer()->getTimers();
            $results = [];
            foreach ($timers as $name => $timer) {
                $results[$name] = $timer['duration'];
            }
            arsort($results); // sort by highest duration

            $html = "<div style='padding:20px; font-family:sans-serif;'>";
            $html .= "<h2>Aksara Benchmark Results</h2><table border='1' cellpadding='10' style='border-collapse:collapse; width:100%; max-width:800px;'>";
            $html .= "<tr style='background:#f4f4f4;'><th>Process Name</th><th>Duration (Seconds)</th></tr>";
            foreach ($results as $name => $duration) {
                $color = $duration > 0.5 ? 'red' : ($duration > 0.1 ? 'orange' : 'green');
                $html .= sprintf("<tr><td>%s</td><td style='color:%s; font-weight:bold; text-align:right;'>%.5f</td></tr>", $name, $color, $duration);
            }
            $html .= "</table></div>";
            exit($html);
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
        timer('Core::serialize() Data Formatting');
        $serialized = $this->serialize($data);
        timer('Core::serialize() Data Formatting');

        $tableData = [];

        if ($serialized) {
            // --- Prepare Properties for Renderer ---

            // Define essential properties needed by the table renderer (whitelisting for abstraction/safety).
            $whitelistedProperties = [
                '_addButton', '_addDropdown', '_addToolbar', '_addFilter', '_columnOrder', '_gridView',
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

            timer('Core::renderTable() Formatter & HTML Table Output');
            // Run the renderer to format the serialized data into final table structure.
            $tableData = $renderer->render($serialized, count($data));
            timer('Core::renderTable() Formatter & HTML Table Output');
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
            return throw_exception(404, phrase('The data you requested does not exist or has been removed.'), current_page('../'));
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
                '_columnOrder', '_columnSize', '_fieldAppend', '_fieldPrepend', '_fieldOrder',
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
            return throw_exception(404, phrase('The data you would to update is not found.'), (! $this->apiClient ? current_page('../') : null));
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
                if (in_array($key, $this->_unsetField) || isset($this->_setDefault[$key]) || array_intersect(['current_timestamp'], $type) || ('create' === $this->_method && array_intersect(['updated_timestamp'], $type)) || ('update' === $this->_method && array_intersect(['created_timestamp'], $type))) {
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

                    if (in_array('required', $val['validation']) && isset($_FILES[$key]['error']) && 0 === $_FILES[$key]['error']) {
                        $val['validation'] = array_diff($val['validation'], ['required']);
                    }

                    $val['validation'][] = 'validate_upload[' . $key . '.image]';

                    // Single upload validation rules
                    $this->formValidation->setRule($key, (isset($this->_setAlias[$key]) ? $this->_setAlias[$key] : ucwords(str_replace('_', ' ', $key))), $val['validation']);
                } elseif (array_intersect(['images'], $type)) {
                    $validation = true;

                    if (in_array('required', $val['validation']) && isset($_FILES[$key]['error'][0]) && 0 === $_FILES[$key]['error'][0]) {
                        $val['validation'] = array_diff($val['validation'], ['required']);
                    }

                    $val['validation'][] = 'validate_upload[' . $key . '.image]';

                    // Images upload validation rules
                    $this->formValidation->setRule($key . '.*', (isset($this->_setAlias[$key]) ? $this->_setAlias[$key] : ucwords(str_replace('_', ' ', $key))), $val['validation']);
                } elseif (array_intersect(['file', 'files'], $type)) {
                    $validation = true;

                    if (in_array('required', $val['validation']) && ((isset($_FILES[$key]['error']) && 0 === $_FILES[$key]['error']) || (isset($_FILES[$key]['error'][0]) && 0 === $_FILES[$key]['error'][0]))) {
                        $val['validation'] = array_diff($val['validation'], ['required']);
                    }

                    $val['validation'][] = 'validate_upload[' . $key . ']';

                    // Files upload validation rules
                    $this->formValidation->setRule($key . '.*', (isset($this->_setAlias[$key]) ? $this->_setAlias[$key] : ucwords(str_replace('_', ' ', $key))), $val['validation']);
                } elseif (array_intersect(['password'], $type)) {
                    $validation = true;
                    $required = ('create' == $this->_method ? 'required|' : '');

                    // Password validation only when post field has value
                    if ($this->request->getPost($key) || 'create' == $this->_method) {
                        // Password validation rules
                        $this->formValidation->setRule($key, (isset($this->_setAlias[$key]) ? $this->_setAlias[$key] : ucwords(str_replace('_', ' ', $key))), $required . 'min_length[6]');
                        $this->formValidation->setRule($key . '_confirmation', phrase('Confirmation') . ' ' . (isset($this->_setAlias[$key]) ? $this->_setAlias[$key] : ucwords(str_replace('_', ' ', $key))), $required . 'matches[' . $key . ']');
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
                    if (in_array('required', $val['validation'])) {
                        $relationKey = $this->_setRelation[$key]['relationKey'];

                        if (is_array($relationKey) && isset($relationKey[0])) {
                            $relationKey = $relationKey[0];
                        }
                        // Apply rules only when it's required
                        $this->formValidation->setRule($key, (isset($this->_setAlias[$key]) ? $this->_setAlias[$key] : ucwords(str_replace('_', ' ', $key))), ['required', 'relation_checker[' . (strpos($this->_setRelation[$key]['relationTable'], ' ') !== false ? substr($this->_setRelation[$key]['relationTable'], 0, strpos($this->_setRelation[$key]['relationTable'], ' ')) : $this->_setRelation[$key]['relationTable']) . '.' . $relationKey . ']']);
                    } else {
                        // Find foreign data
                        $constrained = false;
                        $foreignData = $this->model->foreignData($this->_table);

                        if ($foreignData) {
                            // Find foreign data with loop
                            foreach ($foreignData as $_key => $_val) {
                                // Table has foreign key
                                if ($this->_setRelation[$key]['relationTable'] == $_val->foreign_table_name) {
                                    // Set constraint
                                    $constrained = true;
                                }
                            }
                        }

                        if ($constrained) {
                            // Apply only for constrained table relation
                            $this->formValidation->setRule($key, (isset($this->_setAlias[$key]) ? $this->_setAlias[$key] : ucwords(str_replace('_', ' ', $key))), ['required', 'relation_checker[' . (strpos($this->_setRelation[$key]['relationTable'], ' ') !== false ? substr($this->_setRelation[$key]['relationTable'], 0, strpos($this->_setRelation[$key]['relationTable'], ' ')) : $this->_setRelation[$key]['relationTable']) . '.' . $this->_setRelation[$key]['relationKey'] . ']']);
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
                    } elseif (array_intersect(['date', 'datepicker'], $type) && service('request')->getPost($key)) {
                        // Date (YYYY-MM-DD) validation rules
                        $val['validation'][] = 'valid_date';
                    } elseif (array_intersect(['timestamp', 'datetime', 'datetimepicker'], $type)) {
                        // Full timestamp validation rules
                        $val['validation'][] = 'valid_datetime';
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
            if ($validation && $this->formValidation->withRequest(service('request'))->run() === false) {
                // Unlink the files
                $this->_unlinkFiles(get_userdata('_uploaded_files'));

                // Unset uploaded files session to prevent orphaned files if update is successful
                unset_userdata('_uploaded_files');

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
                    (in_array($field, $this->_unsetField) && ! isset($this->_setDefault[$field]) && ! array_intersect(['slug', 'current_timestamp', 'created_timestamp', 'updated_timestamp'], $type)) ||
                    (in_array('disabled', $type) && ! isset($this->_setDefault[$field])) ||
                    ('create' === $this->_method && array_intersect(['updated_timestamp'], $type)) ||
                    ('update' === $this->_method && array_intersect(['created_timestamp'], $type)) ||
                    (in_array('password', $type) && ! $this->request->getPost($field) && 'create' !== $this->_method)
                ) {
                    continue;
                }

                if (array_key_exists($field, $this->request->getPost()) || array_intersect($type, ['current_timestamp', 'created_timestamp', 'updated_timestamp', 'image', 'images', 'file', 'files', 'slug', 'current_user', 'attribution'])) {
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
                    } elseif (array_intersect(['geospatial'], $type)) {
                        $value = $this->request->getPost($field);
                        if (is_array($value)) {
                            $value = json_encode($value);
                        }
                        $prepare[$field] = $value;
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
                elseif (array_intersect(['boolean'], $type) && ! $this->request->getPost($field) && ! in_array($field, $this->_unsetField)) {
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
            if ($prepare && in_array('create', [$this->_method])) {
                // Insert new data
                $this->insertData($this->_table, $prepare);
            } elseif ($prepare && in_array('update', [$this->_method])) {
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

                // Unset uploaded files session to prevent orphaned files if update is successful
                unset_userdata('_uploaded_files');

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
     * @hook beforeInsert
     * Optional method that can be overridden by a derived Controller or Model
     * to execute custom logic, validation, or data manipulation
     * immediately before a new record is inserted (CREATE operation).
     */
    protected function beforeInsert()
    {
        // Example Use: Setting 'created_at' timestamps or sanitizing user input fields.
    }

    /**
     * @hook afterInsert
     * An optional method that can be overridden by a derived Controller/Model
     * to execute subsequent logic after data has been successfully inserted (CREATE operation).
     *
     * @return void
     */
    protected function afterInsert()
    {
        // Example Usage: Updating cache, sending notifications, or queuing a background job.
    }

    /**
     * @hook beforeUpdate
     * Optional method that can be overridden by a derived Controller or Model
     * to execute custom logic, validation, or data manipulation
     * immediately before an existing record is updated (UPDATE operation).
     */
    protected function beforeUpdate()
    {
        // Example Use: Setting 'updated_at' timestamps or checking for data change conflicts.
    }

    /**
     * @hook afterUpdate
     * An optional method that can be overridden by a derived Controller/Model
     * to execute subsequent logic after data has been successfully updated (UPDATE operation).
     *
     * @return void
     */
    protected function afterUpdate()
    {
        // Example Usage: Recording change logs, invalidating related cache entries, or updating search indices.
    }

    /**
     * @hook beforeDelete
     * Optional method that can be overridden by a derived Controller or Model
     * to execute custom logic, validation, or related tasks
     * immediately before a record is permanently deleted (DELETE operation).
     */
    protected function beforeDelete()
    {
        // Example Use: Checking user permissions or deleting associated files/images.
    }

    /**
     * @hook afterDelete
     * An optional method that can be overridden by a derived Controller/Model
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
        // --- 1. API Method Validation ---
        if ($this->apiClient && ! in_array($this->request->getMethod(), ['POST'])) {
            $this->_unlinkFiles(get_userdata('_uploaded_files'));

            // Unset uploaded files session to prevent orphaned files if update is successful
            unset_userdata('_uploaded_files');

            return throw_exception(403, phrase('The method you requested is not acceptable.') . ' (' . $this->request->getMethod() . ')', $this->_redirectBack);
        }

        // --- 2. Table Existence Check and Execution ---
        if ($table && $this->model->tableExists($table)) {
            // --- 3. Before Insert Hook ---
            if (method_exists($this, 'beforeInsert')) {
                $this->beforeInsert();
            }

            if ($this->model->insert($table, $data)) {
                // --- 4. Success: Get Insert ID and Cleanup ---

                $autoIncrement = true;
                $primary = 0;

                // Special handling for PostgreSQL auto-increment simulation
                if ('Postgre' == $this->_dbDriver) {
                    $autoIncrement = false;
                    $fieldData = $this->model->fieldData($table);

                    foreach ($fieldData as $val) {
                        if (isset($this->_setDefault[$val->name])) {
                            $primary = $this->_setDefault[$val->name];
                        }

                        // Check for primary key or PostgreSQL nextval default
                        if (($val->primary_key ?? false) || (isset($val->default) && $val->default && stripos($val->default, 'nextval(') !== false)) {
                            $autoIncrement = true;
                        }

                        if ($primary && $autoIncrement) {
                            break;
                        }
                    }
                }

                $this->_insertId = $autoIncrement ? $this->model->insertId() : 0;

                // Update token timestamp and invalidate token
                set_userdata('token_timestamp', time());
                unset_userdata(sha1(uri_string()));

                // Unset uploaded files session to prevent orphaned files if update is successful
                unset_userdata('_uploaded_files');

                // --- 5. After Insert Hook ---
                if (method_exists($this, 'afterInsert')) {
                    $this->afterInsert();
                }

                // Send success response
                return throw_exception(($this->apiClient ? 200 : 301), phrase('The data was successfully submitted.'), $this->_redirectBack);
            } else {
                // --- 6. Failure: Error Handling and Cleanup ---
                $this->_unlinkFiles(get_userdata('_uploaded_files'));

                // Unset uploaded files session to prevent orphaned files if update is successful
                unset_userdata('_uploaded_files');

                $error = $this->model->error();
                $errorMessage = $error['message'] ?? phrase('Unable to submit your data.');

                // Display detailed error only for Administrator in non-production environments
                if (get_userdata('group_id') == 1 && ENVIRONMENT != 'production') {
                    $finalMessage = $errorMessage;
                } else {
                    $finalMessage = phrase('Unable to submit your data.') . ' ' . phrase('Please try again or contact the system administrator.') . ' ' . phrase('Error code') . ': <b>500 (INSERT)</b>';
                }

                return throw_exception(500, $finalMessage, $this->_redirectBack);
            }
        } else {
            // --- 7. Failure: Table Not Found ---
            $this->_unlinkFiles(get_userdata('_uploaded_files'));

            // Unset uploaded files session to prevent orphaned files if update is successful
            unset_userdata('_uploaded_files');

            return throw_exception(404, phrase('The selected database table does not exist.'), $this->_redirectBack);
        }
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
        // --- 1. API Method Validation ---
        if ($this->apiClient && ! in_array($this->request->getMethod(), ['POST'])) {
            $this->_unlinkFiles(get_userdata('_uploaded_files'));

            // Unset uploaded files session to prevent orphaned files if update is successful
            unset_userdata('_uploaded_files');

            return throw_exception(403, phrase('The method you requested is not acceptable.') . ' (' . $this->request->getMethod() . ')', $this->_redirectBack);
        }

        // --- 2. MAGIC INTERCEPTOR FOR VERTICAL EAV TABLES ---
        if ($table && $this->model->fieldExists('key', $table) && $this->model->fieldExists('value', $table) && ! $this->model->fieldExists('app_name', $table)) {
            if (method_exists($this, 'beforeUpdate')) {
                $this->beforeUpdate();
            }

            // Iteratively upsert vertical schema
            $hasType = $this->model->fieldExists('type', $table);

            // Ensure unchecked boolean fields are saved as 0
            foreach ($this->_setField as $field => $config) {
                if (isset($config['boolean']) && ! array_key_exists($field, $data)) {
                    $data[$field] = '0';
                }
            }

            foreach ($data as $key => $val) {
                $upsertData = ['value' => $val];
                if ($hasType) {
                    $upsertData['type'] = (isset($this->_setField[$key]['field_type']) ? (is_array($this->_setField[$key]['field_type']) ? 'varchar' : $this->_setField[$key]['field_type']) : 'varchar');
                }

                if ($this->model->getWhere($table, ['key' => $key], 1)->row()) {
                    $this->model->update($table, $upsertData, ['key' => $key]);
                } else {
                    $upsertData['key'] = $key;
                    $this->model->insert($table, $upsertData);
                }
            }

            // Update token timestamp and invalidate token
            set_userdata('token_timestamp', time());
            unset_userdata(sha1(uri_string()));

            // Unset uploaded files session to prevent orphaned files if update is successful
            unset_userdata('_uploaded_files');

            if (method_exists($this, 'afterUpdate')) {
                $this->afterUpdate();
            }

            return throw_exception(($this->apiClient ? 200 : 301), phrase('The data was successfully updated.'), $this->_redirectBack);
        }

        // --- 3. Table Existence Check and WHERE Determination ---
        if ($table && $this->model->tableExists($table)) {
            // Determine WHERE condition if not explicitly provided
            if (! $where) {
                $fieldExists = array_flip($this->model->listFields($table));
                $where = array_intersect_key($this->request->getGet(), $fieldExists);

                // If WHERE is still missing, data cannot be updated
                if (! $where) {
                    $this->_unlinkFiles(get_userdata('_uploaded_files'));

                    // Unset uploaded files session to prevent orphaned files if update is successful
                    unset_userdata('_uploaded_files');

                    return throw_exception(404, phrase('The data you would to delete is not found.'), $this->_redirectBack);
                }

                // Validate derived WHERE keys against table fields
                foreach ($where as $keyBackup => $val) {
                    $key = (stripos($keyBackup, '.') !== false) ? substr($keyBackup, stripos($keyBackup, '.') + 1) : $keyBackup;

                    if (! $this->model->fieldExists($key, $table)) {
                        unset($where[$keyBackup]);
                    }
                }
            }

            // Remove where clause that does not exist in table
            foreach ($where as $key => $val) {
                if (strpos($key, '.') !== false) {
                    $sourceTable = strstr($key, '.', true);

                    if ($sourceTable != $table) {
                        unset($where[$key]);
                    }
                }
            }

            // --- 3. Check if Data Exists ---
            $query = $this->model->getWhere($table, $where, 1)->row();

            if ($query) {
                // --- 4. Data Exists: Execute Update ---

                if (method_exists($this, 'beforeUpdate')) {
                    $this->beforeUpdate();
                }

                // Collect old files for cleanup
                $oldFiles = [];
                foreach ($query as $field => $value) {
                    if (isset($this->_setField[$field]['field_type']) && array_intersect($this->_setField[$field]['field_type'], ['file', 'files', 'image', 'images'])) {
                        $oldFiles[$field] = $value;
                    }
                }

                // Attempt to update data
                if ($this->model->update($table, $data, $where)) {
                    // Update token timestamp and invalidate token
                    set_userdata('token_timestamp', time());
                    unset_userdata(sha1(uri_string()));

                    // Unset uploaded files session to prevent orphaned files if update is successful
                    unset_userdata('_uploaded_files');

                    // Success: Cleanup and Hooks
                    $this->_unlinkFiles($oldFiles);

                    if (method_exists($this, 'afterUpdate')) {
                        $this->afterUpdate();
                    }

                    // Send success response
                    return throw_exception(($this->apiClient ? 200 : 301), phrase('The data was successfully updated.'), $this->_redirectBack);
                } else {
                    // Failure: Error Handling
                    $this->_unlinkFiles(get_userdata('_uploaded_files'));

                    // Unset uploaded files session to prevent orphaned files if update fails
                    unset_userdata('_uploaded_files');

                    $error = $this->model->error();

                    if (get_userdata('group_id') == 1 && isset($error['message'])) {
                        return throw_exception(500, $error['message'], $this->_redirectBack);
                    }

                    return throw_exception(500, phrase('Unable to update the data.') . ' ' . phrase('Please try again or contact the system administrator.') . ' ' . phrase('Error code') . ': <b>500 (UPDATE)</b>', $this->_redirectBack);
                }
            } elseif ($this->_permitUpsert) {
                // --- 5. Data Not Found, but UPSERT is Permitted: Insert Instead ---
                $this->insertData($table, $data);
            } else {
                // --- 6. Data Not Found, UPSERT Not Permitted ---
                $this->_unlinkFiles(get_userdata('_uploaded_files'));

                // Unset uploaded files session to prevent orphaned files if no data is found for update
                unset_userdata('_uploaded_files');

                return throw_exception(404, phrase('The data you would to update is not found.'), $this->_redirectBack);
            }
        } else {
            // --- 7. Failure: Table Not Found ---
            $this->_unlinkFiles(get_userdata('_uploaded_files'));

            // Unset uploaded files session to prevent orphaned files if table does not exist
            unset_userdata('_uploaded_files');

            return throw_exception(404, phrase('The selected database table does not exist.'), $this->_redirectBack);
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
    public function deleteData(?string $table = null, array $where = [], int $limit = 1): object|null
    {
        // --- 1. API Method and Demo Mode Validation ---
        if ($this->apiClient && ! in_array($this->request->getMethod(), ['DELETE'])) {
            return throw_exception(403, phrase('The method you requested is not acceptable.') . ' (' . $this->request->getMethod() . ')', $this->_redirectBack);
        }

        if ($this->_restrictOnDemo) {
            return throw_exception(403, phrase('This feature is disabled in demo mode.'), $this->_redirectBack);
        }

        // Check for explicit callback message set by set_messages('delete')
        if (isset($this->_setMessages['delete']) && ($this->_setMessages['delete']['return'] ?? false)) {
            return throw_exception($this->_setMessages['delete']['code'], $this->_setMessages['delete']['messages'], $this->_redirectBack);
        }

        // --- 2. Table Existence Check and WHERE Determination ---
        if ($table && $this->model->tableExists($table)) {
            // Determine WHERE condition if not explicitly provided
            if (! $where) {
                $fieldExists = array_flip($this->model->listFields($table));
                $where = array_intersect_key($this->request->getGet(), $fieldExists);

                if (! $where) {
                    return throw_exception(404, phrase('The data you would to delete is not found.'), $this->_redirectBack);
                }

                // Validate derived WHERE keys against table fields
                foreach ($where as $keyBackup => $val) {
                    // Extract column name from potential dotted format (e.g., table.column)
                    $key = (stripos($keyBackup, '.') !== false) ? substr($keyBackup, stripos($keyBackup, '.') + 1) : $keyBackup;

                    if (! $this->model->fieldExists($key, $table)) {
                        unset($where[$keyBackup]);
                    }
                }
            }

            // Remove where clause that does not exist in table
            foreach ($where as $key => $val) {
                if (strpos($key, '.') !== false) {
                    $sourceTable = strstr($key, '.', true);

                    if ($sourceTable != $table) {
                        unset($where[$key]);
                    }
                }
            }

            // Check if data actually exists before proceeding
            $query = $this->model->getWhere($table, $where, 1)->row();

            if ($query) {
                // --- 3. Data Exists: Execute Delete ---
                if (method_exists($this, 'beforeDelete')) {
                    $this->beforeDelete();
                }

                // Collect old files for cleanup
                $oldFiles = [];
                foreach ($query as $field => $value) {
                    if (isset($this->_setField[$field]['field_type']) && array_intersect($this->_setField[$field]['field_type'], ['file', 'files', 'image', 'images'])) {
                        $oldFiles[$field] = $value;
                    }
                }

                // Attempt to delete data
                if ($this->model->delete($table, $where, $limit)) {
                    // Update token timestamp and invalidate token
                    set_userdata('token_timestamp', time());
                    unset_userdata(sha1(uri_string()));

                    // Unset uploaded files session to prevent orphaned files if update is successful
                    unset_userdata('_uploaded_files');

                    // Success: Cleanup and Hooks
                    $this->_unlinkFiles($oldFiles);

                    if (method_exists($this, 'afterDelete')) {
                        $this->afterDelete();
                    }

                    // Send success response
                    return throw_exception(($this->apiClient ? 200 : 301), phrase('The data was successfully deleted.'), $this->_redirectBack);
                } else {
                    // Failure: Error Handling
                    $error = $this->model->error();

                    if (get_userdata('group_id') == 1 && isset($error['message'])) {
                        return throw_exception(500, $error['message'], $this->_redirectBack);
                    }

                    return throw_exception(500, phrase('Unable to delete the requested data.') . ' ' . phrase('Please try again or contact the system administrator.') . ' ' . phrase('Error code') . ': <b>500 (DELETE)</b>', $this->_redirectBack);
                }
            } else {
                // Data not found (query returned empty)
                return throw_exception(404, phrase('The data you would to delete is not found.'), $this->_redirectBack);
            }
        } else {
            // --- 4. Failure: Table Not Found ---
            return throw_exception(404, phrase('The selected database table does not exist.'), $this->_redirectBack);
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
    public function deleteBatch(?string $table = null): object|null
    {
        // --- 1. API Method and Demo Mode Validation ---
        if ($this->apiClient && ! in_array($this->request->getMethod(), ['DELETE'])) {
            return throw_exception(403, phrase('The method you requested is not acceptable.') . ' (' . $this->request->getMethod() . ')', $this->_redirectBack);
        }

        if ($this->_restrictOnDemo) {
            return throw_exception(403, phrase('This feature is disabled in demo mode.'), $this->_redirectBack);
        }

        // --- 2. Get Items and Initialization ---
        $items = $this->request->getPost('items');
        $affectedRows = 0;
        $ignoredRows = 0;
        $totalItems = is_array($items) ? count($items) : 0;

        if ($totalItems > 0) {
            // Before delete hook (runs once before the batch loop)
            if (method_exists($this, 'beforeDelete')) {
                $this->beforeDelete();
            }

            // Whitelist fields that exist in the table
            $fieldExists = array_flip($this->model->listFields($table));

            foreach ($items as $val) {
                // Decode item JSON (which contains the WHERE clause for the specific row)
                $whereCondition = json_decode($val, true);

                if (! is_array($whereCondition)) {
                    continue; // Skip invalid item format
                }

                // Only keep fields that exist in the table (whitelisting)
                $whereCondition = array_intersect_key($whereCondition, $fieldExists);

                if (! $whereCondition) {
                    continue; // Skip if no WHERE clause can be formed
                }

                // --- Check Row Exclusion Rules (if set_unset_delete was used) ---
                $ignore = false;
                if ($this->_unsetDelete) {
                    foreach ($this->_unsetDelete as $field => $excludedValues) {
                        if (isset($whereCondition[$field]) && in_array($whereCondition[$field], $excludedValues)) {
                            $ignore = true;
                            break;
                        }
                    }
                }

                if ($ignore) {
                    $ignoredRows++;
                    continue;
                }

                // --- Get Old Data and Files ---
                $query = $this->model->getWhere($table, $whereCondition, 1)->row();
                $oldFiles = null;

                if ($query) {
                    // Collect old files for unlink
                    foreach ($query as $field => $value) {
                        if (isset($this->_setField[$field]['field_type']) && array_intersect($this->_setField[$field]['field_type'], ['file', 'files', 'image', 'images'])) {
                            $oldFiles[$field] = $value;
                        }
                    }

                    // --- Execute Single Delete ---
                    if ($this->model->delete($table, $whereCondition)) {
                        $this->_unlinkFiles($oldFiles);
                        $affectedRows++;
                    }
                }
            }

            // After delete hook (runs once after the batch loop)
            if (method_exists($this, 'afterDelete')) {
                $this->afterDelete();
            }
        }

        // --- 3. Final Response ---
        if ($affectedRows) {
            // Update token timestamp and invalidate token
            set_userdata('token_timestamp', time());
            unset_userdata(sha1(uri_string()));

            $message = phrase('{{affected_rows}} of {{items}} data was successfully removed.', [
                'affected_rows' => $affectedRows,
                'items' => $totalItems
            ]);

            // If some rows were ignored, mention it (optional refinement)
            if ($ignoredRows > 0) {
                $message .= ' ' . phrase('Note: {{ignored_rows}} rows were skipped due to deletion restrictions.', ['ignored_rows' => $ignoredRows]);
            }

            // Deletion success
            return throw_exception(($this->apiClient ? 200 : 301), $message, $this->_redirectBack);
        } else {
            // Deletion fail (either no items were processed or all failed/ignored)
            $failMessage = ($totalItems > 0 && $ignoredRows == $totalItems)
                ? phrase('The selected data cannot be removed due to restrictions.')
                : phrase('Unable to remove the selected data.');

            return throw_exception(403, $failMessage, $this->_redirectBack);
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
        if (! in_array($this->_method, ['delete'])) {
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
    public function where(string|array $field = [], mixed $value = '', bool $escape = true): static
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
    public function orWhere(string|array $field = [], mixed $value = '', bool $escape = true): static
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
    public function whereIn(string|array $field = [], mixed $value = '', bool $escape = true): static
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
    public function orWhereIn(string|array $field = [], mixed $value = '', bool $escape = true): static
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
    public function whereNotIn(string|array $field = [], mixed $value = '', bool $escape = true): static
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
    public function orWhereNotIn(string|array $field = [], mixed $value = '', bool $escape = true): static
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
    public function like(string|array $field = [], mixed $match = '', string $side = 'both', bool $escape = true, bool $caseInsensitive = true): static
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
    public function orLike(string|array $field = [], mixed $match = '', string $side = 'both', bool $escape = true, bool $caseInsensitive = false): static
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
    public function notLike(string|array $field = [], mixed $match = '', string $side = 'both', bool $escape = true, bool $caseInsensitive = false): static
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
    public function orNotLike(string|array $field = [], mixed $match = '', string $side = 'both', bool $escape = true, bool $caseInsensitive = false): static
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
    public function having(string|array $field = [], mixed $value = '', bool $escape = true): static
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
    public function orHaving(string|array $field = [], mixed $value = '', bool $escape = true): static
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
    public function havingIn(string|array $field = [], mixed $value = '', bool $escape = true): static
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
    public function orHavingIn(string|array $field = [], mixed $value = '', bool $escape = true): static
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
    public function havingNotIn(string|array $field = [], mixed $value = '', bool $escape = true): static
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
    public function orHavingNotIn(string|array $field = [], mixed $value = '', bool $escape = true): static
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
    public function havingLike(string|array $field = [], mixed $match = '', string $side = 'both', bool $escape = true, bool $caseInsensitive = false): static
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
    public function orHavingLike(string|array $field = [], mixed $match = '', string $side = 'both', bool $escape = true, bool $caseInsensitive = false): static
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
    public function notHavingLike(string|array $field = [], mixed $match = '', string $side = 'both', bool $escape = true, bool $caseInsensitive = false): static
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
    public function orNotHavingLike(string|array $field = [], mixed $match = '', string $side = 'both', bool $escape = true, bool $caseInsensitive = false): static
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
        if (! in_array($this->_method, ['create', 'update', 'delete'])) {
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
        if (! in_array($this->_method, ['create', 'update', 'delete'])) {
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
        if (! in_array($this->_method, ['create', 'update', 'delete'])) {
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
        if (! in_array($this->_method, ['create', 'update', 'delete'])) {
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
        if (! in_array($this->_method, ['create', 'update', 'delete'])) {
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
        if (! in_array($this->_method, ['create', 'update', 'delete'])) {
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
        if (! in_array($this->_method, ['create', 'update', 'delete'])) {
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
        if (! in_array($this->_method, ['create', 'update', 'delete'])) {
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
        if (! in_array($this->_method, ['create', 'update', 'delete'])) {
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
     * @return \CodeIgniter\Database\BaseBuilder|mixed Returns the Query Builder instance ready for execution, or the result of the executed query.
     */
    private function _runQuery(?string $table = null, bool $recycling = false): mixed
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
            $select = preg_filter('/^/', $table . '.', $this->model->listFields($table));
            $columns = $this->model->fieldData($table);

            if ($columns) {
                foreach ($columns as $key => $val) {
                    if (in_array($this->_method, ['create', 'update']) && in_array($val->name, $this->_unsetField)) {
                        if (! isset($val->primary_key) || empty($val->primary_key)) {
                            unset($select[$val->name]);
                        }
                    } elseif (in_array($this->_method, ['read']) && in_array($val->name, $this->_unsetView)) {
                        if (! isset($val->primary_key) || empty($val->primary_key)) {
                            unset($select[$val->name]);
                        }
                    } elseif (in_array($val->name, $this->_unsetColumn)) {
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
                $compiledSelect = [];

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
                        list($backupTable, $field) = array_pad(explode('.', $val), 2, null);

                        if (! $field) {
                            $field = $backupTable;
                        }

                        // Get the name alias
                        $field = trim(($field && stripos($field, ' AS ') !== false ? substr($field, strripos($field, ' AS ') + 4) : $field));

                        if ($field && stripos($field, ' ') !== false) {
                            $field = substr($field, 0, strrpos($field, ' '));
                        }

                        if ($backupTable != $table && $field && $this->model->fieldExists($field, $backupTable)) {
                            // Format column of select
                            $val = $backupTable . '.' . $field . ' AS ' . $field;
                        }
                    }

                    // Cast geospatial to geojson
                    if ($field && in_array($this->model->dbDriver(), ['Postgre', 'MySQLi', 'SQLite3'])) {
                        $isGeospatial = false;
                        if (isset($this->_setField[$field]['geospatial'])) {
                            $targetTable = ($backupTable ?: $table);
                            if ($targetTable) {
                                $fieldData = $this->model->fieldData($targetTable);
                                foreach ($fieldData as $fd) {
                                    if ($fd->name == $field && isset($fd->type) && in_array(strtolower($fd->type), ['geometry', 'geography', 'user-defined'])) {
                                        $isGeospatial = true;
                                        break;
                                    }
                                }
                            }
                        }

                        // Cast to GeoJSON for create/update method (so form can read and manipulate it)
                        $asGeojsonFunc = ('SQLite3' === $this->model->dbDriver()) ? 'AsGeoJSON' : 'ST_AsGeoJSON';
                        if ($isGeospatial && in_array($this->_method, ['create', 'update']) && ! preg_match('/' . $asGeojsonFunc . '\s*\(/i', $val)) {
                            $valOrigin = ($backupTable && $backupTable != $field ? $backupTable . '.' . $field : ($table ? $table . '.' . $field : $field));
                            $val = $asGeojsonFunc . '(' . $valOrigin . ') AS ' . $field;
                        }
                    }

                    // Compile the selected field
                    $compiledSelect[] = $val;
                }

                // Check if select compiled
                if ($compiledSelect) {
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
                        'arguments' => [array_values($compiledSelect)]
                    ];
                }

                // Generate join query passed from set_relation
                if (is_array($this->_join) && sizeof($this->_join) > 0) {
                    $sortedJoin = [];
                    $visited = [];
                    $idToKey = [];

                    foreach (array_keys($this->_join) as $key) {
                        $id = trim($key);

                        if (preg_match('/\s+AS\s+(.+)$/i', $id, $m)) {
                            $id = trim($m[1], '`"');
                        } else {
                            $parts = preg_split('/\s+/', $id);
                            $id = trim(end($parts), '`"');
                        }

                        $idToKey[$id] = $key;
                    }

                    $visit = function ($key) use (&$visit, &$visited, &$sortedJoin, $idToKey) {
                        if (isset($visited[$key])) {
                            return;
                        }

                        $visited[$key] = true;
                        $condition = $this->_join[$key]['condition'] ?? '';
                        $id = trim($key);

                        if (preg_match('/\s+AS\s+(.+)$/i', $id, $m)) {
                            $id = trim($m[1], '`"');
                        } else {
                            $parts = preg_split('/\s+/', $id);
                            $id = trim(end($parts), '`"');
                        }

                        if (preg_match_all('/\b([a-zA-Z0-9_]+)\b\./', $condition, $matches)) {
                            foreach (array_unique($matches[1]) as $refId) {
                                if ($refId !== $id && isset($idToKey[$refId])) {
                                    $visit($idToKey[$refId]);
                                }
                            }
                        }

                        $sortedJoin[$key] = $this->_join[$key];
                    };

                    foreach (array_keys($this->_join) as $key) {
                        $visit($key);
                    }

                    $this->_join = $sortedJoin;

                    foreach ($this->_join as $dbTable => $params) {
                        $condition = str_replace('__PRIMARY_TABLE__', $table, $params['condition']);

                        foreach ($this->_setRelation ?? [] as $relation) {
                            if (($relation['relationTable'] ?? null) !== $dbTable) {
                                continue;
                            }

                            $relationTableClean = $dbTable;

                            if (strpos($relationTableClean, ' ') !== false) {
                                [, $relationTableClean] = explode(' ', $relationTableClean);
                            }

                            foreach ($relation['where'] ?? [] as $whereKey => $whereValue) {
                                if (
                                    is_string($whereKey) &&
                                    str_starts_with($whereKey, $relationTableClean . '.')
                                ) {
                                    $localField = substr($whereKey, strlen($relationTableClean . '.'));

                                    if ($this->model->fieldExists($localField, $table)) {
                                        $extraCondition = $whereKey . ' = ' . $table . '.' . $localField;

                                        if (strpos($condition, $extraCondition) === false) {
                                            $condition .= ' AND ' . $extraCondition;
                                        }
                                    }
                                }
                            }
                        }

                        $this->_prepare[] = [
                            'function' => 'join',
                            'arguments' => [$dbTable, $condition, $params['type'], $params['escape']]
                        ];
                    }
                }
            }
        }

        // Format compiled select
        if ($this->_compiledSelect) {
            foreach ($this->_compiledSelect as $key => $val) {
                // Check if column should be unset
                if (in_array($val, $this->_unsetSelect)) {
                    // Unset selected compiled select
                    unset($this->_compiledSelect[$key]);
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
                    if (in_array($column, $this->_unsetSelect)) {
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
                        $compiledSelectKey1 = array_search($column, $this->_compiledSelect);
                        $compiledSelectKey2 = array_search($alias, $this->_compiledSelect);

                        // Unset matched compiled select
                        unset($this->_compiledSelect[$compiledSelectKey1]);
                        unset($this->_compiledSelect[$compiledSelectKey2]);

                        // Extract source table
                        $sourceTable = substr($_val . '.', 0, strpos($_val, '.'));

                        // Check whether table or columns has compiled
                        if (! in_array($sourceTable, $this->_compiledTable) && ! $alias) {
                            // Field doesn't exists on compiled table
                            unset($arguments[0][$_key]);
                        }
                    }
                }

                // Make the selection column unique
                $arguments[0] = array_unique(array_merge($this->_compiledSelect, $arguments[0]));
            } elseif ('where' == $function) {
                // Extract source table from selection
                $sourceTable = (isset($arguments[0]) ? $arguments[0] : '');
                $sourceTable = substr($sourceTable . '.', 0, strpos($sourceTable, '.'));

                if ($sourceTable && ! in_array($sourceTable, $this->_compiledTable)) {
                    // Source table not in compilation
                    continue;
                }

                if (! preg_match('/[.<=>()]/', $arguments[0])) {
                    // Add table name prefix if not present to prevent ambiguity
                    $arguments[0] = $this->_table . '.' . $arguments[0];
                }
            } elseif ('selectSubquery' == $function) {
                // Free query builder
                $this->model->resetQuery();
            } elseif ('orderBy' == $function) {
                if (in_array($this->_method, ['create', 'read', 'update', 'delete'])) {
                    // Prevent order on create, read, update, and delete method
                    continue;
                } else {
                    // Add table name prefix if not present to prevent ambiguity
                    if (strpos($arguments[0], '.') === false && strpos($arguments[0], '(') === false && strpos($arguments[0], ')') === false) {
                        $arguments[0] = $this->_table . '.' . $arguments[0];
                    }
                }
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
    private function _fetch(?string $table = null, ?bool $row = false): array
    {
        // --- 1. Debugger ---
        if ($this->_debugging && 'benchmark' !== $this->_debugging) {
            // Run query with limit/offset for debug output
            $queryBuilder = $this->_runQuery($table);

            if (null !== $this->_limit) {
                $queryBuilder->limit($this->_limit, $this->_offset ?? 0);
            }

            if ($row) {
                // Get single row
                $query = $queryBuilder->row();
            } else {
                // Get multiple rows
                $query = $queryBuilder->result();
            }

            if ('query' == $this->_debugging) {
                exit('<div style="font-family:monospace">' . nl2br($this->model->lastQuery()) . '</div>');
            } elseif ($this->_debugging) {
                if (ENVIRONMENT === 'production') {
                    exit('<pre>' . print_r($query, true) . '</pre>');
                }
                dd($query);
            }
        }

        // --- 1. MAGIC INTERCEPTOR FOR VERTICAL EAV TABLES ---
        if ($this->model->fieldExists('key', $table ?: $this->_table) && $this->model->fieldExists('value', $table ?: $this->_table) && ! $this->model->fieldExists('app_name', $table ?: $this->_table)) {
            $verticalKeys = $this->model->get($table ?: $this->_table)->result();

            // Transpose vertical to horizontal
            $horizontalRow = new \stdClass();
            $allKeys = [];
            foreach ($verticalKeys as $vk) {
                $horizontalRow->{$vk->key} = $vk->value;
                $allKeys[] = $vk->key;
            }

            // Ensure all fields explicitly defined in the controller exist in the object
            // to prevent "Undefined property" errors in the Form Builder
            if (is_array($this->_setField)) {
                foreach ($this->_setField as $fieldKey => $fieldVal) {
                    if (! property_exists($horizontalRow, $fieldKey)) {
                        $horizontalRow->{$fieldKey} = '';
                    }
                }
            }

            // Reset preparation property for subsequent queries
            $this->_prepare = [];

            return [
                'results' => ($row ? $horizontalRow : [$horizontalRow]),
                'total' => 1
            ];
        }

        // --- 2. Execute Queries ---

        timer('Core::_fetchData() Database Query');
        // Query for results (with LIMIT/OFFSET)
        $resultsBuilder = $this->_runQuery($table);
        // Apply limit/offset after running the main query builder parameters
        if (null !== $this->_limit) {
            $resultsBuilder->limit($this->_limit, $this->_offset ?? 0);
        }

        if ($row) {
            // Get single row
            $results = $resultsBuilder->row();

            // Assign total
            $total = ($results ? 1 : 0);
        } else {
            // Get multiple rows
            $results = $resultsBuilder->result();

            // Query for total count (recycling the prepared parameters but skipping complex SELECT logic)
            $total = $this->_runQuery($table, true)->countAllResults();
        }
        timer('Core::_fetchData() Database Query');

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
    private function _getRelation(array $params = [], int|string|null $selected = 0, bool $ajax = false): array|string
    {
        // Try to fetch from runtime cache first to avoid N+1 query bottleneck
        $cacheKey = '';
        if ($selected && ! $ajax) {
            $cacheKey = md5(json_encode($params) . '_' . $selected);
            if (isset($this->_relationCache[$cacheKey])) {
                return $this->_relationCache[$cacheKey];
            }
        }

        // Use default value if nothing is selected and a default is defined.
        $fieldNameForDefault = is_array($params['primaryKey']) ? end($params['primaryKey']) : ($params['primaryKey'] ?? null);
        if (! $selected && ($this->_defaultValue[$fieldNameForDefault] ?? null)) {
            $selected = $this->_defaultValue[$fieldNameForDefault];
        }

        $compiledSelect = [];
        $like = [];
        $primaryKey = is_array($params['primaryKey']) ? end($params['primaryKey']) : ($params['primaryKey'] ?? null);

        // --- 1. SELECT and LIKE Clause Construction ---
        foreach ($params['select'] as $key => $val) {
            $parts = explode('.', $val);
            $column = $parts[1] ?? $val;
            $table = $parts[0] ?? null;

            // Handle column aliasing to prevent ambiguity if column names clash.
            if (in_array($column, $compiledSelect) && $table != $params['relationTable']) {
                $val .= ' AS ' . $column . '_' . $table;
            }

            $this->model->select($val);
            $compiledSelect[] = $column;

            // Build LIKE clause for search payload (used in AJAX).
            if ($search = $this->request->getPost('search')) {
                $likeKey = (stripos($val, ' AS ') !== false) ? substr($val, 0, stripos($val, ' AS ')) : $val;
                $like[$likeKey] = $search;
            }
        }

        // Apply LIKE clauses if present and not retrieving a single selected item.
        if ($like && ! $selected) {
            $this->model->groupStart();
            $num = 0;
            foreach ($like as $key => $val) {
                $this->model->{(($num) ? 'orLike' : 'like')}($key, $val, 'both', true, true);
                $num++;
            }
            $this->model->groupEnd();
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
            $relationTable = (strpos($params['relationTable'], ' ') !== false)
                ? substr($params['relationTable'], strpos($params['relationTable'], ' ') + 1)
                : $params['relationTable'];

            /*
            * When resolving selected value on update form, do not let relation scope
            * hide the selected option. The selected option should be resolved by its
            * relation key first.
            */
            foreach ($params['where'] as $whereKey => $whereValue) {
                if (
                    is_string($whereKey) &&
                    str_starts_with($whereKey, $relationTable . '.')
                ) {
                    $whereField = substr($whereKey, strlen($relationTable . '.'));

                    if (
                        ! is_array($params['relationKey']) &&
                        $whereField !== $params['relationKey']
                    ) {
                        unset($params['where'][$whereKey]);
                    }
                }
            }

            if (is_array($params['relationKey'])) {
                $selectedParts = explode('.', $selected);

                foreach ($params['relationKey'] as $k => $relKey) {
                    if (isset($selectedParts[$k])) {
                        $params['where'][$relationTable . '.' . $relKey] = $selectedParts[$k];
                    }
                }
            } else {
                $params['where'][$relationTable . '.' . $params['relationKey']] = $selected;
            }

            $params['limit'] = 1;
        }

        // --- 3.5 Capture Depends Payload (AJAX) ---
        if ($ajax) {
            $relationTable = (strpos($params['relationTable'], ' ') !== false) ? substr($params['relationTable'], strpos($params['relationTable'], ' ') + 1) : $params['relationTable'];
            $reserved = ['aksara', 'method', 'source', 'selected_list', 'search', 'page'];

            foreach ($this->request->getPost() as $key => $val) {
                if (! in_array($key, $reserved) && '' !== $val && null !== $val) {
                    // Prevent SQL injection on column names by only allowing alphanumeric, underscore, and dot
                    if (preg_match('/^[a-zA-Z0-9_\.]+$/', $key)) {
                        $checkField = (strpos($key, '.') !== false) ? substr($key, strpos($key, '.') + 1) : $key;

                        // Verify the field exists in the database table to prevent SQL errors
                        if ($this->model->fieldExists($checkField, $relationTable)) {
                            // Prefix with relation table to prevent ambiguous column errors if not explicitly prefixed
                            $whereKey = (strpos($key, '.') === false) ? $relationTable . '.' . $key : $key;
                            $params['where'][$whereKey] = $val;
                        }
                    }
                }
            }
        }

        // --- 4. Apply Custom WHERE Clauses ---
        if ($params['where']) {
            foreach ($params['where'] as $key => $val) {
                // Complex custom WHERE logic (IN, NOT IN) requiring raw SQL injection (false flag).
                if ((strpos($key, ' IN') !== false || strpos($key, ' NOT IN') !== false || strpos($key, ' LIKE') !== false || strpos($key, ' NOT LIKE') !== false) && is_array($val)) {
                    $this->model->where($key, $val, false);
                } elseif ($val && (strpos($val, ' IN') !== false || strpos($val, ' NOT IN') !== false || strpos($val, ' LIKE') !== false || strpos($val, ' NOT LIKE') !== false) && strpos($val, '(') !== false && strpos($val, ')') !== false) {
                    $this->model->where($val, null, false);
                } elseif (strpos($key, ' NOT LIKE') !== false) {
                    $field = trim(str_replace(' NOT LIKE', '', $key));
                    $this->model->notLike($field, $val, 'none');
                } elseif (strpos($key, ' LIKE') !== false) {
                    $field = trim(str_replace(' LIKE', '', $key));
                    $this->model->like($field, $val, 'none');
                } else {
                    $this->model->where($key, $val);
                }
            }
        }

        // --- 5. Apply ORDER BY and GROUP BY ---
        if ($params['orderBy'] && ! $selected) {
            if (is_array($params['orderBy'])) {
                foreach ($params['orderBy'] as $key => $val) {
                    if (strpos($key, '.') === false && strpos($key, '(') === false && strpos($key, ')') === false) {
                        // Add table name prefix if not present to prevent ambiguity
                        $key = $params['relationTable'] . '.' . $key;
                    }

                    $this->model->orderBy($key, $val);
                }
            } else {
                $this->model->orderBy($params['orderBy']);
            }
        }

        if ($params['join'] && $params['groupBy'] && ! $selected) {
            $this->model->groupBy($params['groupBy']);
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
        $query = $this->model->get($params['relationTable'], $params['limit'], $params['offset'])->result();

        if ($query) {
            foreach ($query as $key => $val) {
                $label = $params['output'];
                $attributes = $this->_setAttribute[$primaryKey] ?? '';
                $optionLabel = $this->_setOptionLabel[$primaryKey] ?? '';

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
                        if (isset($this->_setField[$replacement]['sprintf'])) { // Checking 'sprintf' should be against keys, not field_type
                            $val->$replacement = sprintf('%02d', $val->$replacement);
                        }

                        // Replace magic string in label, attributes, and option_label.
                        $pattern = "/\{\{(\s+)?($replace)(\s+)?\}\}/";
                        $label = preg_replace($pattern, $val->$replacement, $label);
                        $attributes = preg_replace($pattern, $val->$replacement, $attributes);
                        $optionLabel = preg_replace($pattern, $val->$replacement, $optionLabel);
                    }
                }

                // --- Output Formatting based on Method ---
                if (in_array($this->_method, ['create', 'update'])) {
                    // Formatting for form field (dropdown/select2)

                    if (is_array($params['primaryKey'])) {
                        // Composite key value and selected status determination.
                        $value = implode('.', array_map(fn ($k) => $val->$k ?? 0, $params['primaryKey']));
                    } else {
                        // Determine the value/ID for the option
                        $value = $val->$primaryKey ?? null;
                    }

                    if (null === $value) {
                        if (is_array($params['relationKey'])) {
                            $value = implode('.', array_map(fn ($k) => $val->$k ?? 0, $params['relationKey']));
                        } else {
                            $relationKey = $params['relationKey'];
                            $value = $val->$relationKey ?? 0;
                        }
                    }

                    // Determine the selected status
                    $isSelected = ($value == $selected);

                    if ($ajax) {
                        $output[] = ['id' => $value, 'text' => ($params['translate'] ? phrase($label) : $label)];
                    } else {
                        $output[] = ['value' => $value, 'label' => ($params['translate'] ? phrase($label) : $label), 'selected' => $isSelected];
                    }
                } else {
                    // Formatting for read/index view (single label string)
                    $output = ($params['translate'] ? phrase($label) : $label);

                    if ($cacheKey) {
                        $this->_relationCache[$cacheKey] = $output;
                    }

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
     * @param array $orderedId Array of primary key values in their new desired order.
     *
     * @return string The JSON response array (status and message).
     */
    private function _sortTable(array $orderedId = []): string
    {
        // Check if sorting is enabled or if the input format is invalid.
        if (! $this->_sortable || ! is_array($orderedId)) {
            return make_json([
                'status' => 400,
                'message' => phrase('The order format is invalid.')
            ]);
        }

        $primaryKeyField = $this->_sortable['primary_key'];
        $orderKeyField = $this->_sortable['order_key'];

        // Find any records in the table that have an order_key of 0 (e.g. newly added data or broken previous sorts)
        $zeroRecords = $this->model->select($primaryKeyField)
            ->where($orderKeyField, 0)
            ->orderBy($primaryKeyField, 'ASC')
            ->getWhere($this->_table, [])
            ->resultArray();

        if (count($zeroRecords) > 0) {
            // Get the current max order_key
            $maxRecord = $this->model->selectMax($orderKeyField, 'max_order')
                ->get($this->_table)
                ->row();

            $nextOrder = 1;
            if ($maxRecord && isset($maxRecord->max_order)) {
                $nextOrder = (int) $maxRecord->max_order + 1;
            }

            $batchData = [];
            foreach ($zeroRecords as $record) {
                $batchData[] = [
                    $primaryKeyField => $record[$primaryKeyField],
                    $orderKeyField => $nextOrder
                ];
                $nextOrder++;
            }

            // Apply the new order keys sequentially to the end of the list
            if ($batchData) {
                $this->model->updateBatch($this->_table, $batchData, $primaryKeyField);
            }
        }

        // Get the existing order keys corresponding to the submitted IDs.
        $query = $this->model->select($primaryKeyField)
            ->select($orderKeyField)
            ->whereIn($primaryKeyField, $orderedId)
            // Order by the original order key to get a clean sequence of old order values.
            ->orderBy($orderKeyField, 'ASC')
            ->getWhere($this->_table, [])
            ->resultArray();

        // Extract the original order keys into a simple, indexed array.
        // This array ($newOrder) now holds the old order values (e.g., 1, 2, 3, 4, ...)
        // which will be assigned to the new positions.
        $newOrder = [];
        foreach ($query as $val) {
            $newOrder[] = $val[$orderKeyField];
        }

        // $orderedId is the list of IDs in their NEW desired position.
        // $newOrder is the list of ORIGINAL order keys to be assigned.
        foreach ($orderedId as $key => $val) {
            // $val is the ID (primary key)
            // $newOrder[$key] is the old order key (which represents the new order position)
            $this->model->update(
                $this->_table,
                [
                    $orderKeyField => $newOrder[$key]
                ],
                [
                    $primaryKeyField => $val
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
     * @param string|null $fieldName  Internal tracking of the current field name (used for recursive calls).
     * @param array $fieldList Internal tracking of file lists for exclusion logic.
     *
     * @return void Returns immediately if the input is not a valid array.
     */
    private function _unlinkFiles(?array $files = [], ?string $fieldName = null, array $fieldList = []): void
    {
        foreach ($files ?? [] as $field => $src) {
            // Decode JSON source if necessary
            if (is_string($src) && is_json($src)) {
                $src = json_decode($src, true);
            }

            // --- Recursive Call Handling ---
            if (is_array($src)) {
                // Rename field for next condition (used for tracking array paths)
                $newFieldName = $fieldName ?? ($field . '_label');

                // Merge field list for exclusion logic
                $fieldList[$newFieldName] = array_merge($fieldList[$newFieldName] ?? [], $src);

                // Reinitialize function recursively
                $this->_unlinkFiles($src, $newFieldName, $fieldList);

                continue; // Move to the next item once recursion is handled.
            }

            // --- File Unlinking Logic ---

            // Determine the input name used in POST data for exclusion check.
            $inputName = urldecode(http_build_query($fieldList));
            $inputName = substr($inputName, 0, strpos($inputName, '='));

            // Define exclusion conditions:
            // 1. Placeholder file should never be deleted.
            // 2. File is marked for preservation in POST data (i.e., the user didn't change it).
            // 3. File upload slot is empty in $_FILES (meaning user didn't upload a new file).
            $fileUploadedEmpty = (! is_array($field) && isset($_FILES[$field]['tmp_name']) && empty($_FILES[$field]['tmp_name']));

            if ('placeholder.png' == $src || $this->request->getPost($inputName) || $fileUploadedEmpty) {
                continue; // Skip unlink
            }

            // Sanitize input file names to prevent directory traversal.
            $safeSrc = basename($src);
            $safeField = basename((string) $field); // Ensure $field is treated as string

            // Define potential file names to check (source file name and field name).
            $filesToCheck = [$safeSrc, $safeField];

            // Define the directories to check (main upload, thumbs, icons).
            $subdirectories = ['', 'thumbs/', 'icons/'];

            // Base upload path for the current module.
            $baseDir = UPLOAD_PATH . '/' . $this->_setUploadPath . '/';

            // Loop through all potential paths and attempt deletion.
            foreach ($subdirectories as $subdir) {
                foreach ($filesToCheck as $filename) {
                    $path = $baseDir . $subdir . $filename;

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
            'app_rest_clients',
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
        } elseif (! in_array($this->request->getMethod(), json_decode($client->method, true))) {
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
            'app_sessions',
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
                'app_sessions',
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
        if (in_array($clientIp, $whitelist)) {
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

        $agent = Services::request()->getUserAgent();
        $userAgent = '';

        // User agent detection
        if ($agent->isBrowser()) {
            $userAgent = $agent->getBrowser() . ' ' . $agent->getVersion();
        } elseif ($agent->isRobot()) {
            $userAgent = $agent->getRobot();
        } elseif ($agent->isMobile()) {
            $userAgent = $agent->getMobile();
        }

        // Prepare log data
        $prepare = [
            'ip_address' => ($this->request->hasHeader('x-forwarded-for') ? $this->request->getHeaderLine('x-forwarded-for') : $this->request->getIPAddress()),
            'browser' => $userAgent,
            'platform' => $agent->getPlatform(),
            'timestamp' => date('Y-m-d H:i:s')
        ];

        // 2. Check if this IP has visited TODAY
        $today = date('Y-m-d');
        $query = $this->model->getWhere('app_log_visitors', [
            'ip_address' => $prepare['ip_address'],
            'DATE(timestamp)' => $today  // Check if already visited today
        ], 1)->row();

        if (! $query) {
            // Visitor hasn't visited today (could be new or returning visitor)
            try {
                $logInsert = $this->model->insert('app_log_visitors', $prepare);

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
     * Increments the visit counters in the app_stats table for the specified periods.
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

        // Update the app_stats table (typically a single-row table).
        $this->model->update('app_stats');
    }

    /**
     * Automatically resets visit counters (daily, weekly, monthly, yearly) based on date comparison.
     */
    private function _autoResetCounters(): void
    {
        // Retrieve the single row statistics data.
        $stats = $this->model->get('app_stats', 1)->row();

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

            $this->model->insert('app_stats', $initialData);
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
            $this->model->update('app_stats', $updates);
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
            $languages = $this->model->getWhere('app_languages', ['status' => 1])->result();

            // Match Browser Locale to Available Languages
            foreach ($languages as $language) {
                $items = array_map('trim', explode(',', strtolower($language->locale))); // Available locales for this language

                foreach ($locales as $loc) {
                    if (in_array(strtolower(trim($loc)), $items)) {
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
            ->getWhere('app_languages', ['id' => $languageId], 1)
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
