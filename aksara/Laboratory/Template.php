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
use Aksara\Laboratory\Model;
use Aksara\Libraries\Beautifier;
use Aksara\Libraries\Html_dom;
use Aksara\Laboratory\Renderer\Parser;
use Throwable;
use stdClass;

/**
 * Template handler class for managing themes, views, and output processing.
 */
class Template
{
    /**
     * @var string The active theme directory name (e.g., 'frontend' or 'backend').
     */
    public string $theme;

    /**
     * @var array|null Stores CSS files/links to be included.
     */
    private ?array $_css = null;

    /**
     * @var array|null Stores JavaScript files/links to be included.
     */
    private ?array $_js = null;

    /**
     * @var Model The database model instance.
     */
    private Model $_model;

    /**
     * @var string|null Stores partial view data.
     */
    private ?string $_partial_view = null;

    /**
     * @var string The current controller method name.
     */
    private string $_method;

    /**
     * Template constructor.
     *
     * @param string $theme The theme directory name to use, defaults to 'frontend'.
     * @param string $method The current controller method name, defaults to 'index'.
     */
    public function __construct(string $theme = 'frontend', string $method = 'index')
    {
        $this->theme = $theme;
        $this->_method = $method;

        $this->_model = new Model();

        if (! $this->theme) {
            // Throwback the default theme from site configuration
            $site_id = get_setting('id');

            $this->theme = (string) $this->_model->select('frontend_theme')->get_where(
                'app__settings',
                [
                    'id' => $site_id
                ],
                1
            )
            ->row('frontend_theme');
        }
    }

    /**
     * Getting active theme name from database configuration.
     *
     * @return string|false The active theme directory name or false if the theme type is invalid.
     */
    public function get_theme(): string|false
    {
        if (! in_array($this->theme, ['frontend', 'backend'])) {
            return false;
        }

        $site_id = get_setting('id');

        $query = $this->_model->select($this->theme . '_theme')->get_where(
            'app__settings',
            [
                'id' => $site_id
            ],
            1
        )
        ->row($this->theme . '_theme');

        return (string) $query;
    }

    /**
     * Getting the theme property from theme.json file.
     *
     * @param string|null $parameter The specific property key to retrieve (e.g., 'type').
     * @return mixed|false The property value or false if the theme.json or property doesn't exist.
     */
    public function get_theme_property(?string $parameter = null): mixed
    {
        if (file_exists(ROOTPATH . 'themes/' . $this->theme . '/theme.json')) {
            // Check if active theme has a property
            $property = new stdClass();

            try {
                $property = json_decode(file_get_contents(ROOTPATH . 'themes/' . $this->theme . '/theme.json'));
            } catch (Throwable $e) {
                // Safe abstraction
            }

            if ($parameter && isset($property->$parameter)) {
                return $property->$parameter;
            }
        }

        return false;
    }

    /**
     * Scan the view file location both camelized string and lowercase.
     *
     * Finds the most appropriate view file based on theme, module, language, and request type.
     *
     * @param string $view The base view file name, defaults to 'index'.
     * @return string The normalized path to the found view file (relative to ROOTPATH using '../../').
     */
    public function get_view(string $view = 'index'): string
    {
        $request = Services::request();
        $router = Services::router();

        // Get current controller namespace
        $view_path = preg_replace(['/\\\\aksara\\\\/i', '/\\\\modules\\\\/i', '/\\\\controllers\\\\/i'], ['\\', '\\', '\Views\\'], $router->controllerName(), 1);

        // Get parent module classname
        $parent_module = strtok($view_path, '\\');

        // Get current module classname
        $current_module = substr($view_path, strrpos($view_path, '\\') + 1);

        if (strtolower($parent_module) === strtolower($current_module)) {
            // Slice out the module path when the parent module has same name
            $view_path = substr($view_path, 0, strrpos($view_path, '\\'));
        }

        // Replace backslash to match with directory separator
        $view_path = str_replace([$current_module . '\\' . $current_module, '\\'], [strtolower($current_module), DIRECTORY_SEPARATOR], $view_path);

        // List module and view path
        list($modules, $views) = array_pad(explode(DIRECTORY_SEPARATOR . 'Views', $view_path), 2, null);

        // Convert view path as lowercase
        $view_path = $modules . '/Views' . ($views ? strtolower($views) : null);

        // Theme based viewfinder
        $theme_viewfinder = ROOTPATH . 'themes/' . $this->theme . '/views/' . strtolower(preg_replace('/\/views/i', '', $view_path, 1));

        // Theme based viewfinder fallback
        $fallback_theme_viewfinder = ROOTPATH . 'themes/' . $this->theme . '/components/core';

        // Module based viewfinder
        $module_viewfinder = ROOTPATH . 'modules' . $view_path;

        // Core module based viewfinder
        $core_viewfinder = ROOTPATH . 'aksara/Modules' . $view_path;

        // Core based viewfinder
        $fallback_viewfinder = ROOTPATH . 'aksara/Views/components/core';

        // View suffix
        $suffix = ($request->getUserAgent()->isMobile() ? '_mobile' : ('modal' == $request->getPost('prefer') ? '_modal' : (isset($_ENV['GRID_VIEW']) && $_ENV['GRID_VIEW'] ? '_grid' : null)));

        // Get user language for i18n view
        $language = get_userdata('language');

        // Method name to force as view when exists
        $method = (! in_array($router->methodName(), ['404', 'index', 'create', 'read', 'update']) ? $router->methodName() : $view);

        /**
         * ---------------------------------------------------------------------
         * Find view from theme
         * ---------------------------------------------------------------------
         */
        if (file_exists($theme_viewfinder . '/' . $language . '/' . $method . $suffix . '.twig') || file_exists($theme_viewfinder . '/' . $language . '/' . $method . $suffix . '.php')) {
            // View is found under i18n path
            $view = str_replace(ROOTPATH, '../../', $theme_viewfinder . '/' . $language . '/' . $method . $suffix);
        } elseif (file_exists($theme_viewfinder . '/' . $language . '/' . $view . $suffix . '.twig') || file_exists($theme_viewfinder . '/' . $language . '/' . $view . $suffix . '.php')) {
            // View is found under i18n path
            $view = str_replace(ROOTPATH, '../../', $theme_viewfinder . '/' . $language . '/' . $method . $suffix);
        } elseif (file_exists($theme_viewfinder . '/' . $language . '/' . $method. '.twig') || file_exists($theme_viewfinder . '/' . $language . '/' . $method . '.php')) {
            // View is found under i18n path
            $view = str_replace(ROOTPATH, '../../', $theme_viewfinder . '/' . $language . '/' . $view);
        } elseif (file_exists($theme_viewfinder . '/' . $language . '/' . $view. '.twig') || file_exists($theme_viewfinder . '/' . $language . '/' . $view . '.php')) {
            // View is found under i18n path
            $view = str_replace(ROOTPATH, '../../', $theme_viewfinder . '/' . $language . '/' . $view);
        } elseif (file_exists(dirname($theme_viewfinder) . '/' . $language . '/' . basename($theme_viewfinder) . $suffix . '.twig') || file_exists(dirname($theme_viewfinder) . '/' . $language . '/' . basename($theme_viewfinder) . $suffix . '.php')) {
            // View is found under i18n path of current classname (lowercase)
            $view = str_replace(ROOTPATH, '../../', dirname($theme_viewfinder) . '/' . $language . '/' . basename($theme_viewfinder) . $suffix);
        } elseif (file_exists(dirname($theme_viewfinder) . '/' . $language . '/' . basename($theme_viewfinder) . '.twig') || file_exists(dirname($theme_viewfinder) . '/' . $language . '/' . basename($theme_viewfinder) . '.php')) {
            // View is found under i18n path of current classname (lowercase)
            $view = str_replace(ROOTPATH, '../../', dirname($theme_viewfinder) . '/' . $language . '/' . basename($theme_viewfinder));
        } elseif (file_exists($theme_viewfinder . '/' . $method . $suffix . '.twig') || file_exists($theme_viewfinder . '/' . $method . $suffix . '.php')) {
            // View is found without i18n path
            $view = str_replace(ROOTPATH, '../../', $theme_viewfinder . '/' . $method . $suffix);
        } elseif (file_exists($theme_viewfinder . '/' . $view . $suffix . '.twig') || file_exists($theme_viewfinder . '/' . $view . $suffix . '.php')) {
            // View is found without i18n path
            $view = str_replace(ROOTPATH, '../../', $theme_viewfinder . '/' . $view . $suffix);
        } elseif (file_exists($theme_viewfinder . '/' . $method . '.twig') || file_exists($theme_viewfinder . '/' . $method . '.php')) {
            // View is found without i18n path
            $view = str_replace(ROOTPATH, '../../', $theme_viewfinder . '/' . $method);
        } elseif (file_exists($theme_viewfinder . '/' . $view . '.twig') || file_exists($theme_viewfinder . '/' . $view . '.php')) {
            // View is found without i18n path
            $view = str_replace(ROOTPATH, '../../', $theme_viewfinder . '/' . $view);
        } elseif (file_exists($theme_viewfinder . $suffix . '.twig') || file_exists($theme_viewfinder . $suffix . '.php')) {
            // View is found and same as current classname (lowercase)
            $view = str_replace(ROOTPATH, '../../', $theme_viewfinder . $suffix);
        } elseif (file_exists($theme_viewfinder . '.twig') || file_exists($theme_viewfinder . '.php')) {
            // View is found and same as current classname (lowercase)
            $view = str_replace(ROOTPATH, '../../', $theme_viewfinder);
        }

        /**
         * ---------------------------------------------------------------------
         * Find view from user modules
         * ---------------------------------------------------------------------
         */ elseif (file_exists($module_viewfinder . '/' . $language . '/' . $method . $suffix . '.twig') || file_exists($module_viewfinder . '/' . $language . '/' . $method . $suffix . '.php')) {
            // View is found under i18n path
            $view = str_replace(ROOTPATH, '../../', $module_viewfinder . '/' . $language . '/' . $method . $suffix);
        } elseif (file_exists($module_viewfinder . '/' . $language . '/' . $view . $suffix . '.twig') || file_exists($module_viewfinder . '/' . $language . '/' . $view . $suffix . '.php')) {
            // View is found under i18n path
            $view = str_replace(ROOTPATH, '../../', $module_viewfinder . '/' . $language . '/' . $view . $suffix);
        } elseif (file_exists($module_viewfinder . '/' . $language . '/' . $method . '.twig') || file_exists($module_viewfinder . '/' . $language . '/' . $method . '.php')) {
            // View is found under i18n path
            $view = str_replace(ROOTPATH, '../../', $module_viewfinder . '/' . $language . '/' . $method);
        } elseif (file_exists($module_viewfinder . '/' . $language . '/' . $view . '.twig') || file_exists($module_viewfinder . '/' . $language . '/' . $view . '.php')) {
            // View is found under i18n path
            $view = str_replace(ROOTPATH, '../../', $module_viewfinder . '/' . $language . '/' . $view);
        } elseif (file_exists(dirname($module_viewfinder) . '/' . $language . '/' . basename($module_viewfinder) . $suffix . '.twig') || file_exists(dirname($module_viewfinder) . '/' . $language . '/' . basename($module_viewfinder) . $suffix . '.php')) {
            // View is found under i18n path of current classname (lowercase)
            $view = str_replace(ROOTPATH, '../../', dirname($module_viewfinder) . '/' . $language . '/' . basename($module_viewfinder) . $suffix);
        } elseif (file_exists(dirname($module_viewfinder) . '/' . $language . '/' . basename($module_viewfinder) . '.twig') || file_exists(dirname($module_viewfinder) . '/' . $language . '/' . basename($module_viewfinder) . '.php')) {
            // View is found under i18n path of current classname (lowercase)
            $view = str_replace(ROOTPATH, '../../', dirname($module_viewfinder) . '/' . $language . '/' . basename($module_viewfinder));
        } elseif (file_exists($module_viewfinder . '/' . $method . $suffix . '.twig') || file_exists($module_viewfinder . '/' . $method . $suffix . '.php')) {
            // View is found without i18n path
            $view = str_replace(ROOTPATH, '../../', $module_viewfinder . '/' . $method . $suffix);
        } elseif (file_exists($module_viewfinder . '/' . $view . $suffix . '.twig') || file_exists($module_viewfinder . '/' . $view . $suffix . '.php')) {
            // View is found without i18n path
            $view = str_replace(ROOTPATH, '../../', $module_viewfinder . '/' . $view . $suffix);
        } elseif (file_exists($module_viewfinder . '/' . $method . '.twig') || file_exists($module_viewfinder . '/' . $method . '.php')) {
            // View is found without i18n path
            $view = str_replace(ROOTPATH, '../../', $module_viewfinder . '/' . $method);
        } elseif (file_exists($module_viewfinder . '/' . $view . '.twig') || file_exists($module_viewfinder . '/' . $view . '.php')) {
            // View is found without i18n path
            $view = str_replace(ROOTPATH, '../../', $module_viewfinder . '/' . $view);
        } elseif (file_exists($module_viewfinder . $suffix . '.twig') || file_exists($module_viewfinder . $suffix . '.php')) {
            // View is found and same as current classname (lowercase)
            $view = str_replace(ROOTPATH, '../../', $module_viewfinder . $suffix);
        } elseif (file_exists($module_viewfinder . '.twig') || file_exists($module_viewfinder . '.php')) {
            // View is found and same as current classname (lowercase)
            $view = str_replace(ROOTPATH, '../../', $module_viewfinder);
        }

        /**
         * ---------------------------------------------------------------------
         * Find view from core modules
         * ---------------------------------------------------------------------
         */ elseif (file_exists($core_viewfinder . '/' . $language . '/' . $method . $suffix . '.twig') || file_exists($core_viewfinder . '/' . $language . '/' . $method . $suffix . '.php')) {
            // View is found under i18n path
            $view = str_replace(ROOTPATH, '../../', $core_viewfinder . '/' . $language . '/' . $method . $suffix);
        } elseif (file_exists($core_viewfinder . '/' . $language . '/' . $view . $suffix . '.twig') || file_exists($core_viewfinder . '/' . $language . '/' . $view . $suffix . '.php')) {
            // View is found under i18n path
            $view = str_replace(ROOTPATH, '../../', $core_viewfinder . '/' . $language . '/' . $view . $suffix);
        } elseif (file_exists($core_viewfinder . '/' . $language . '/' . $method . '.twig') || file_exists($core_viewfinder . '/' . $language . '/' . $method . '.php')) {
            // View is found under i18n path
            $view = str_replace(ROOTPATH, '../../', $core_viewfinder . '/' . $language . '/' . $method);
        } elseif (file_exists($core_viewfinder . '/' . $language . '/' . $view . '.twig') || file_exists($core_viewfinder . '/' . $language . '/' . $view . '.php')) {
            // View is found under i18n path
            $view = str_replace(ROOTPATH, '../../', $core_viewfinder . '/' . $language . '/' . $view);
        } elseif (file_exists(dirname($core_viewfinder) . '/' . $language . '/' . basename($core_viewfinder) . $suffix . '.twig') || file_exists(dirname($core_viewfinder) . '/' . $language . '/' . basename($core_viewfinder) . $suffix . '.php')) {
            // View is found under i18n path of current classname (lowercase)
            $view = str_replace(ROOTPATH, '../../', dirname($core_viewfinder) . '/' . $language . '/' . basename($core_viewfinder) . $suffix);
        } elseif (file_exists(dirname($core_viewfinder) . '/' . $language . '/' . basename($core_viewfinder) . '.twig') || file_exists(dirname($core_viewfinder) . '/' . $language . '/' . basename($core_viewfinder) . '.php')) {
            // View is found under i18n path of current classname (lowercase)
            $view = str_replace(ROOTPATH, '../../', dirname($core_viewfinder) . '/' . $language . '/' . basename($core_viewfinder));
        } elseif (file_exists($core_viewfinder . '/' . $view . $suffix . '.twig') || file_exists($core_viewfinder . '/' . $view . $suffix . '.php')) {
            // View is found without i18n path
            $view = str_replace(ROOTPATH, '../../', $core_viewfinder . '/' . $view . $suffix);
        } elseif (file_exists($core_viewfinder . '/' . $method . '.twig') || file_exists($core_viewfinder . '/' . $method . '.php')) {
            // View is found without i18n path
            $view = str_replace(ROOTPATH, '../../', $core_viewfinder . '/' . $method);
        } elseif (file_exists($core_viewfinder . '/' . $view . '.twig') || file_exists($core_viewfinder . '/' . $view . '.php')) {
            // View is found without i18n path
            $view = str_replace(ROOTPATH, '../../', $core_viewfinder . '/' . $view);
        } elseif (file_exists($core_viewfinder . $suffix . '.twig') || file_exists($core_viewfinder . $suffix . '.php')) {
            // View is found and same as current classname (lowercase)
            $view = str_replace(ROOTPATH, '../../', $core_viewfinder . $suffix);
        } elseif (file_exists($core_viewfinder . '.twig') || file_exists($core_viewfinder . '.php')) {
            // View is found and same as current classname (lowercase)
            $view = str_replace(ROOTPATH, '../../', $core_viewfinder);
        }

        /**
         * ---------------------------------------------------------------------
         * Find fallback view if doesn't match anything from above occurrence
         * ---------------------------------------------------------------------
         */ elseif (file_exists($fallback_theme_viewfinder . '/' . $method . $suffix . '.twig') || file_exists($fallback_theme_viewfinder . '/' . $method . $suffix . '.php')) {
            // View is found without i18n path
            $view = str_replace(ROOTPATH, '../../', $fallback_theme_viewfinder . '/' . $method . $suffix);
        } elseif (file_exists($fallback_theme_viewfinder . '/' . $view . $suffix . '.twig') || file_exists($fallback_theme_viewfinder . '/' . $view . $suffix . '.php')) {
            // View is found without i18n path
            $view = str_replace(ROOTPATH, '../../', $fallback_theme_viewfinder . '/' . $view . $suffix);
        } elseif (file_exists($fallback_theme_viewfinder . '/' . $method . '.twig') || file_exists($fallback_theme_viewfinder . '/' . $method . '.php')) {
            // View is found without i18n path
            $view = str_replace(ROOTPATH, '../../', $fallback_theme_viewfinder . '/' . $method);
        } elseif (file_exists($fallback_theme_viewfinder . '/' . $view . '.twig') || file_exists($fallback_theme_viewfinder . '/' . $view . '.php')) {
            // View is found without i18n path
            $view = str_replace(ROOTPATH, '../../', $fallback_theme_viewfinder . '/' . $view);
        } elseif (file_exists($fallback_viewfinder . '/' . $method . $suffix . '.twig') || file_exists($fallback_viewfinder . '/' . $method . $suffix . '.php')) {
            // View fallback is found
            $view = str_replace(ROOTPATH, '../../', $fallback_viewfinder . '/' . $method . $suffix);
        } elseif (file_exists($fallback_viewfinder . '/' . $view . $suffix . '.twig') || file_exists($fallback_viewfinder . '/' . $view . $suffix . '.php')) {
            // View fallback is found
            $view = str_replace(ROOTPATH, '../../', $fallback_viewfinder . '/' . $view . $suffix);
        } elseif (file_exists($fallback_viewfinder . '/' . $method . '.twig') || file_exists($fallback_viewfinder . '/' . $method . '.php')) {
            // View fallback is found
            $view = str_replace(ROOTPATH, '../../', $fallback_viewfinder . '/' . $method);
        } elseif (file_exists($fallback_viewfinder . '/' . $view . '.twig') || file_exists($fallback_viewfinder . '/' . $view . '.php')) {
            // View fallback is found
            $view = str_replace(ROOTPATH, '../../', $fallback_viewfinder . '/' . $view);
        } elseif (file_exists($fallback_viewfinder . $suffix . '.twig') || file_exists($fallback_viewfinder . $suffix . '.php')) {
            // View fallback is found and same as classname (lowercase)
            $view = str_replace(ROOTPATH, '../../', $fallback_viewfinder . $suffix);
        } elseif (file_exists($fallback_viewfinder . '.twig') || file_exists($fallback_viewfinder . '.php')) {
            // View fallback is found and same as classname (lowercase)
            $view = str_replace(ROOTPATH, '../../', $fallback_viewfinder);
        } else {
            // No matches view, check fallback
            if ($router->getMatchedRoute()) {
                // No mode
                $view = str_replace(ROOTPATH, '../../', $fallback_viewfinder . '/error');
            } else {
                // No router found
                if (file_exists(dirname($theme_viewfinder) . '/404.twig') || file_exists(dirname($theme_viewfinder) . '/404.php')) {
                    // Use theme view
                    $view = str_replace(ROOTPATH, '../../', dirname($theme_viewfinder) . '/404');
                } elseif (file_exists($fallback_theme_viewfinder . '/404.twig') || file_exists($fallback_theme_viewfinder . '/404.php')) {
                    // Use theme component
                    $view = str_replace(ROOTPATH, '../../', $fallback_theme_viewfinder . '/404');
                } else {
                    // Use core view
                    $view = str_replace(ROOTPATH, '../../', $fallback_viewfinder . '/404');
                }
            }
        }

        // Remove duplicate directory separator
        $view = str_replace('//', '/', $view);

        return $view;
    }

    /**
     * Build output view or object.
     *
     * Processes the view, applies templates, minifies output, and sends the response.
     *
     * @param string|null $view The view file name to render (without extension).
     * @param array $data Data to be passed to the view.
     * @param string|null $table Reserved for future use (currently unused).
     * @return \CodeIgniter\HTTP\Response|object The HTTP Response object for non-AJAX requests or a JSON object for AJAX requests.
     */
    public function build(?string $view = null, array $data = [], ?string $table = null): \CodeIgniter\HTTP\Response
    {
        $request = Services::request();

        // Fix encoding
        $data = encoding_fixer($data);

        // Convert array to object
        $data = json_decode(json_encode($data), false); // Use false for $associative to ensure object

        // Get view
        $view = $this->get_view($view);

        // Load active theme helper if any
        if (is_dir(ROOTPATH . 'themes/' . $this->theme . '/helpers')) {
            // Load filesystem helper
            helper('filesystem');

            // List available helper files
            $helpers = directory_map(ROOTPATH . 'themes/' . $this->theme . '/helpers', 1);

            foreach ($helpers as $helper) {
                if (strtolower(pathinfo($helper, PATHINFO_EXTENSION)) === 'php') {
                    // Load helper
                    include_once ROOTPATH . 'themes/' . $this->theme . '/helpers/' . $helper;
                }
            }
        }

        // Main templates definition
        $main_templates = [
            '../../aksara/Views/core/index',
            '../../themes/' . $this->theme . '/components/core/index',
            '../../aksara/Views/core/index_grid',
            '../../themes/' . $this->theme . '/components/core/index_grid',
            '../../aksara/Views/core/index_mobile',
            '../../themes/' . $this->theme . '/components/core/index_mobile',
            '../../aksara/Views/core/form',
            '../../themes/' . $this->theme . '/components/core/form',
            '../../aksara/Views/core/form_modal',
            '../../themes/' . $this->theme . '/components/core/form_modal',
            '../../aksara/Views/core/read',
            '../../themes/' . $this->theme . '/components/core/read',
            '../../aksara/Views/core/read_modal',
            '../../themes/' . $this->theme . '/components/core/read_modal',
            '../../aksara/Views/core/error',
            '../../themes/' . $this->theme . '/components/core/error'
        ];

        // Set view to response
        $data->view = basename($view);

        $view_path_twig = str_replace('../../', ROOTPATH, $view . '.twig');
        $view_path_php = str_replace('../../', ROOTPATH, $view . '.php');

        if ((file_exists($view_path_twig) || file_exists($view_path_php)) && (! in_array($view, $main_templates) || (in_array($view, $main_templates) && ! $request->isAJAX()))) {
            if (file_exists($view_path_twig)) {
                // Load Twig template parser
                $parser = new Parser($this->theme);

                // Build html from result object
                $data->content = $parser->parse($view_path_twig, (array) $data);
            } else {
                // Build html from result object
                $data->content = view($view, (array) $data);
            }

            // Intersection key to keep property from unset
            $intersection_key = ['code', 'method', 'prefer', 'meta', 'breadcrumb', 'limit', 'links', 'total', 'current_page', 'current_module', 'query_params', 'elapsed_time', 'content', '_token'];

            foreach ($data as $key => $val) {
                if (! in_array($key, $intersection_key)) {
                    // Unset rendered object
                    unset($data->$key);
                }
            }
        }

        if ($request->isAJAX() && $request->getServer('HTTP_REFERER') && stripos($request->getServer('HTTP_REFERER'), $request->getServer('SERVER_NAME')) !== false) {
            // Send to client
            return make_json($data);
        } else {
            // Add core menus into data object
            $data->menus = encoding_fixer($this->_core_menus());

            // Convert array to object
            $data = json_decode(json_encode($data), false); // Use false for $associative to ensure object

            if (file_exists(ROOTPATH . 'themes/' . $this->theme . '/layout.twig')) {
                // Load Twig template parser
                $parser = new Parser($this->theme);

                // Build html from result object
                $parsed_view = $parser->parse(ROOTPATH . 'themes/' . $this->theme . '/layout.twig', (array) $data);
            } else {
                // Build html from result object
                $parsed_view = view('../../themes/' . $this->theme . '/layout', (array) $data);
            }

            // Minify output
            $output = $this->_minify(str_replace('</body>', '<div class="' . implode('', ['ak', 'sa', 'ra', '-', 'fo', 'ot', 'er']) . '"></div></body>', $parsed_view));

            $response = Services::response();

            // Add security headers
            $response->setHeader('Permissions-Policy', 'geolocation=(self "' . base_url() . '")');
            $response->setHeader('Referrer-Policy', 'same-origin');
            $response->setHeader('Set-Cookie', 'HttpOnly; Secure');
            $response->setHeader('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
            $response->setHeader('X-Content-Type-Options', 'nosniff');
            $response->setHeader('X-Frame-Options', 'SAMEORIGIN');
            $response->setHeader('X-XSS-Protection', '1; mode=block');

            return $response->setBody($output)->sendBody();
        }
    }

    /**
     * Generate breadcrumb array structure.
     *
     * @param array $data An associative array of slug => label for breadcrumb segments.
     * @param string|null $title The title for the current page (last segment label).
     * @param array $primary Array of primary key names to be preserved in query parameters.
     * @return array<int, array{url: string, label: string, icon: string}> The array of breadcrumb items.
     */
    public function breadcrumb(array $data = [], ?string $title = null, array $primary = []): array
    {
        $request = Services::request();
        $router = Services::router();
        $uri = Services::uri();

        $slug = null;
        $checker = $uri->getSegments();
        $matched_route = $router->getMatchedRoute();
        $matched_route = (isset($matched_route[0]) ? explode('/', $matched_route[0]) : []);

        if (! $data) {
            $data = [];

            foreach ($checker as $val) {
                $data[$val] = (end($matched_route) !== $router->methodName() && in_array($val, $matched_route) ? phrase(ucwords(str_replace('_', ' ', $val))) : ucwords(str_replace('_', ' ', $val)));
            }
        }

        if ($this->get_theme_property('type') == 'backend') {
            $output = [
                [
                    'url' => base_url('dashboard'),
                    'label' => phrase('Dashboard'),
                    'icon' => 'mdi mdi-home'
                ]
            ];
        } else {
            $output = [
                [
                    'url' => base_url(),
                    'label' => phrase('Homepage'),
                    'icon' => 'mdi mdi-home'
                ]
            ];
        }

        $current_slug = end($checker);
        $slug = null;
        $params = $request->getGet();

        foreach ($params as $key => $val) {
            if (in_array($key, array_merge($primary, ['per_page', 'q', 'order', 'sort', 'limit', 'offset']))) {
                $params[$key] = null;
            }
        }

        // Get last breadcrumb
        $current_page = end($data);

        // Remove last breadcrumb
        array_pop($data);

        foreach ($data as $key => $val) {
            $external = null;
            if (stripos($key, '://')) {
                $external = $key;
            } else {
                $slug .= ($slug ? '/' : null) . $key;
            }

            if ($key && $val) {
                $breadcrumb_params = $params;

                if (is_array($val) && isset($val['label'])) {
                    $label = $val['label'];

                    if (isset($val['parameter']) && is_array($val['parameter'])) {
                        $breadcrumb_params = array_merge($params, $val['parameter']);
                    }
                } else {
                    $label = $val;
                }

                $output[] = [
                    'url' => $external ?? base_url($slug, $breadcrumb_params),
                    'label' => $label,
                    'icon' => ''
                ];
            }
        }

        $output[] = [
            'url' => '',
            'label' => $current_page,
            'icon' => ''
        ];

        return $output;
    }

    /**
     * Generate pagination data structure.
     *
     * @param array $data Array containing pagination details (total_rows, per_page, offset).
     * @return array<string, mixed> The array of pagination data.
     */
    public function pagination(array $data = []): array
    {
        if (! $data) {
            // Safe abstraction
            $data = new stdClass();
        } elseif (is_array($data)) {
            // Convert array to object
            $data = json_decode(json_encode($data), false);
        }

        if (! isset($data->total_rows)) {
            // If there's no result, set to 0
            $data->total_rows = 0;
        }

        if (! isset($data->per_page)) {
            // If no per_page setting, set default
            $data->per_page = 25;
        }

        if (! isset($data->offset)) {
            // If there's no offset, set to 0
            $data->offset = 0;
        }

        $output = [];

        $pager = Services::pager();

        // Get last page
        $last_page = ($data->total_rows > $data->per_page ? (int) ceil($data->total_rows / $data->per_page) : 1);

        // Make pagination links
        $pagination = $pager->makeLinks(1, $data->per_page, $data->total_rows, 'pagination');

        // Parse HTML
        $parser = new Html_dom();
        $buffer = $parser->str_get_html($pagination);

        $request = Services::request();

        $query_params = [];

        foreach ($request->getGet() as $key => $val) {
            if (is_array($val)) {
                foreach ($val as $_key => $_val) {
                    if (is_array($_val)) {
                        continue;
                    }

                    $_key = preg_replace('/[^\w-]/', '', $_key);

                    if (! $_key || in_array($_key, ['q', 'per_page'])) {
                        continue;
                    }

                    $query_params[] = [
                        'name' => $_key,
                        'value' => htmlspecialchars($_val)
                    ];
                }

                continue;
            }

            $key = preg_replace('/[^\w-]/', '', $key);

            if (! $key || in_array($key, ['q', 'per_page'])) {
                continue;
            }

            $query_params[] = [
                'name' => $key,
                'value' => htmlspecialchars($val)
            ];
        }

        $output = [
            'total_rows' => (int) $data->total_rows,
            'per_page' => (int) $data->per_page,
            'action' => current_page(null, ['per_page' => null]),
            'filters' => [
                'hidden' => $query_params,
                'select' => [
                    [
                        'name' => 'limit',
                        'values' => [
                            [
                                'value' => (int) $data->per_page,
                                'label' => (int) $data->per_page,
                                'selected' => true
                            ],
                            [
                                'value' => (int) ($data->per_page * 2),
                                'label' => (int) ($data->per_page * 2),
                                'selected' => (int) $data->per_page === ($data->per_page * 2)
                            ],
                            [
                                'value' => (int) ($data->per_page * 4),
                                'label' => (int) ($data->per_page * 4),
                                'selected' => (int) $data->per_page === ($data->per_page * 4)
                            ],
                            [
                                'value' => (int) ($data->per_page * 8),
                                'label' => (int) ($data->per_page * 8),
                                'selected' => (int) $data->per_page === ($data->per_page * 8)
                            ],
                            [
                                'value' => (int) ($data->per_page * 20),
                                'label' => (int) ($data->per_page * 20),
                                'selected' => (int) $data->per_page === ($data->per_page * 20)
                            ]
                        ]
                    ]
                ],
                'number' => [
                    [
                        'name' => 'per_page',
                        'value' => (is_numeric($request->getGet('per_page')) && $request->getGet('per_page') ? (int) $request->getGet('per_page') : 1),
                        'min' => 1,
                        'max' => $last_page
                    ]
                ]
            ],
            'information' => phrase('Showing {{start}} - {{end}} of {{total}} entries found.', [
                'start' => ($data->offset ? number_format($data->offset) : ($data->total_rows ? 1 : 0)),
                'end' => ((($data->offset + $data->per_page) < $data->total_rows ? number_format(($data->offset + $data->per_page)) : number_format($data->total_rows))),
                'total' => number_format($data->total_rows)
            ])
        ];

        if (25 != $data->per_page) {
            $default_limit = [
                [
                    'value' => 25,
                    'label' => 25,
                    'selected' => 25 === (int) $data->per_page
                ]
            ];

            $output['filters']['select'][0]['values'] = array_merge($default_limit, $output['filters']['select'][0]['values']);
        }

        $output['links'] = [];
        foreach ($buffer->find('ul li') as $val) {
            /** @var \simple_html_dom_node $val */
            $link = $val->find('a', 0);
            if ($link) {
                $output['links'][] = [
                    'id' => (string) $link->id,
                    'parent_class' => (string) $val->class,
                    'class' => (string) $link->class,
                    'href' => (string) $link->href,
                    'label' => trim(str_replace('&amp;', '&', htmlspecialchars($link->innertext)))
                ];
            }
        }

        return $output;
    }

    /**
     * Function to beautify HTML.
     *
     * @param string|null $buffer The HTML content to beautify.
     * @return string|null The beautified HTML content.
     */
    private function _beautify(?string $buffer = null): ?string
    {
        $beautifier = new Beautifier([
            'indent_inner_html' => true,
            'indent_char' => ' ',
            'indent_size' => 4,
            'wrap_line_length' => 32786,
            'unformatted' => ['textarea', 'pre', 'code', 'script'],
            'preserve_newlines' => false,
            'max_preserve_newlines' => 32786,
            'indent_scripts' => 'normal' // keep|separate|normal
        ]);

        // Beautify output
        return $beautifier->beautify($buffer);
    }

    /**
     * Function to minify HTML.
     *
     * @param string|null $buffer The HTML content to minify.
     * @return string|null The minified HTML content.
     */
    private function _minify(?string $buffer = null): ?string
    {
        if (! is_string($buffer) || trim($buffer) === '') {
            return $buffer;
        }

        // Save content inside tags that must not be minified
        $preserve = [];
        $tags = ['pre', 'code', 'textarea', 'script', 'style'];

        foreach ($tags as $tag) {
            $pattern = '#<' . $tag . '\b[^>]*>.*?</' . $tag . '>#si';
            $buffer = preg_replace_callback($pattern, function ($match) use (&$preserve) {
                $key = '@@PRESERVE_' . count($preserve) . '@@';
                $preserve[$key] = $match[0];
                return $key;
            }, $buffer);
        }

        // Minify HTML outside preserved tags
        // Remove whitespace between tags
        $buffer = preg_replace('/>\s+</', '><', $buffer);

        // Remove multiple spaces
        $buffer = preg_replace('/\s{2,}/', ' ', $buffer);

        // Remove spaces before/after tags
        $buffer = preg_replace('/^\s+|\s+$/m', '', $buffer);

        // Restore preserved areas
        foreach ($preserve as $key => $content) {
            $buffer = str_replace($key, $content, $buffer);
        }

        return $buffer;
    }

    /**
     * Default core menus structure.
     *
     * @param array $menus Base menu array (usually empty or pre-loaded).
     * @return array The complete menu structure for the current user group and theme.
     */
    private function _core_menus(array $menus = []): array
    {
        if (! $menus) {
            $group_id = get_userdata('group_id');

            $menus_data = $this->_model->select('
                serialized_data
            ')
            ->group_start()
            ->where('group_id', $group_id)
            ->or_where('group_id', 0)
            ->group_end()
            ->get_where(
                'app__menus',
                [
                    'menu_placement' => ('frontend' == $this->get_theme_property('type') ? 'header' : 'sidebar')
                ],
                1
            )
            ->row('serialized_data');

            $menus = ($menus_data ? json_decode($menus_data, true) : []);
            $cms_menus = [
                [
                    'id' => 0,
                    'label' => 'Blogs',
                    'slug' => 'cms/blogs',
                    'icon' => 'mdi mdi-newspaper',
                    'children' => [
                        [
                            'id' => 0,
                            'label' => 'Posts',
                            'slug' => 'cms/blogs',
                            'icon' => 'mdi mdi-pencil'
                        ],
                        [
                            'id' => 0,
                            'label' => 'Categories',
                            'slug' => 'cms/blogs/categories',
                            'icon' => 'mdi mdi-sitemap'
                        ]
                    ]
                ],
                [
                    'id' => 0,
                    'label' => 'Pages',
                    'slug' => 'cms/pages',
                    'icon' => 'mdi mdi-book-open-page-variant'
                ],
                [
                    'id' => 0,
                    'label' => 'Galleries',
                    'slug' => 'cms/galleries',
                    'icon' => 'mdi mdi-folder-multiple-image'
                ],
                [
                    'id' => 0,
                    'label' => 'Videos',
                    'slug' => 'cms/videos',
                    'icon' => 'mdi mdi-youtube'
                ],
                [
                    'id' => 0,
                    'label' => 'Peoples',
                    'slug' => 'cms/peoples',
                    'icon' => 'mdi mdi-account-group-outline'
                ],
                [
                    'id' => 0,
                    'label' => 'Partial Content',
                    'slug' => 'cms/partials',
                    'icon' => 'mdi mdi-file-image',
                    'children' => [
                        [
                            'id' => 0,
                            'label' => 'Carousels',
                            'slug' => 'cms/partials/carousels',
                            'icon' => 'mdi mdi-image-multiple'
                        ],
                        [
                            'id' => 0,
                            'label' => 'FAQs',
                            'slug' => 'cms/partials/faqs',
                            'icon' => 'mdi mdi-file-question'
                        ],
                        [
                            'id' => 0,
                            'label' => 'Announcements',
                            'slug' => 'cms/partials/announcements',
                            'icon' => 'mdi mdi-bullhorn-outline'
                        ],
                        [
                            'id' => 0,
                            'label' => 'Testimonials',
                            'slug' => 'cms/partials/testimonials',
                            'icon' => 'mdi mdi-comment-account-outline'
                        ],
                        [
                            'id' => 0,
                            'label' => 'Inquiries',
                            'slug' => 'cms/partials/inquiries',
                            'icon' => 'mdi mdi-message-text'
                        ],
                        [
                            'id' => 0,
                            'label' => 'Media',
                            'slug' => 'cms/partials/media',
                            'icon' => 'mdi mdi-folder-image'
                        ]
                    ]
                ],
                [
                    'id' => 0,
                    'label' => 'Comments',
                    'slug' => 'cms/comments',
                    'icon' => 'mdi mdi-comment-multiple-outline'
                ]
            ];

            if (get_userdata('group_id') == 1 && $this->get_theme_property('type') == 'backend') {
                // Core menus for global administrator
                $core_menus = [
                    [
                        'id' => 0,
                        'label' => '',
                        'slug' => '---'
                    ],
                    [
                        'id' => 0,
                        'label' => 'CMS',
                        'slug' => 'cms',
                        'icon' => 'mdi mdi-dropbox',
                        'children' => $cms_menus
                    ],
                    [
                        'id' => 0,
                        'label' => '',
                        'slug' => '---'
                    ],
                    [
                        'id' => 0,
                        'label' => 'Core Tools',
                        'slug' => '---'
                    ],
                    [
                        'id' => 0,
                        'label' => 'Administrative',
                        'slug' => 'administrative',
                        'icon' => 'mdi mdi-cogs',
                        'children' => [
                            [
                                'id' => 0,
                                'label' => 'Users and Groups',
                                'slug' => 'administrative/users',
                                'icon' => 'mdi mdi-account-group-outline',
                                'children' => [
                                    [
                                        'id' => 0,
                                        'label' => 'Users',
                                        'slug' => 'administrative/users',
                                        'icon' => 'mdi mdi-account-group'
                                    ],
                                    [
                                        'id' => 0,
                                        'label' => 'Groups',
                                        'slug' => 'administrative/groups',
                                        'icon' => 'mdi mdi-sitemap'
                                    ],
                                    [
                                        'id' => 0,
                                        'label' => 'Privileges',
                                        'slug' => 'administrative/groups/privileges',
                                        'icon' => 'mdi mdi-account-check-outline'
                                    ]
                                ]
                            ],
                            [
                                'id' => 0,
                                'label' => 'Configurations',
                                'slug' => 'administrative',
                                'icon' => 'mdi mdi-wrench-outline',
                                'children' => [
                                    [
                                        'id' => 0,
                                        'label' => 'Site Settings',
                                        'slug' => 'administrative/settings',
                                        'icon' => 'mdi mdi-settings'
                                    ],
                                    [
                                        'id' => 0,
                                        'label' => 'Menus',
                                        'slug' => 'administrative/menus',
                                        'icon' => 'mdi mdi-menu'
                                    ],
                                    [
                                        'id' => 0,
                                        'label' => 'Translations',
                                        'slug' => 'administrative/translations',
                                        'icon' => 'mdi mdi-translate'
                                    ],
                                    [
                                        'id' => 0,
                                        'label' => 'Countries',
                                        'slug' => 'administrative/countries',
                                        'icon' => 'mdi mdi-map-legend'
                                    ],
                                    [
                                        'id' => 0,
                                        'label' => 'Years',
                                        'slug' => 'administrative/years',
                                        'icon' => 'mdi mdi-calendar-multiple-check'
                                    ],
                                    [
                                        'id' => 0,
                                        'label' => 'Connections',
                                        'slug' => 'administrative/connections',
                                        'icon' => 'mdi mdi-power-plug'
                                    ]
                                ]
                            ],
                            [
                                'id' => 0,
                                'label' => 'Logs',
                                'slug' => 'administrative/logs',
                                'icon' => 'mdi mdi-information-outline',
                                'children' => [
                                    [
                                        'id' => 0,
                                        'label' => 'Activities',
                                        'slug' => 'administrative/logs/activities',
                                        'icon' => 'mdi mdi-calendar-clock'
                                    ],
                                    [
                                        'id' => 0,
                                        'label' => 'Errors',
                                        'slug' => 'administrative/logs/errors',
                                        'icon' => 'mdi mdi-bug'
                                    ]
                                ]
                            ],
                            [
                                'id' => 0,
                                'label' => 'Session Cleaner',
                                'slug' => 'administrative/cleaner',
                                'icon' => 'mdi mdi-trash-can'
                            ]
                        ]
                    ],
                    [
                        'id' => 0,
                        'label' => 'Notifier',
                        'slug' => 'notifier',
                        'icon' => 'mdi mdi-bullhorn'
                    ],
                    [
                        'id' => 0,
                        'label' => 'Add-Ons',
                        'slug' => 'addons',
                        'icon' => 'mdi mdi-puzzle'
                    ],
                    [
                        'id' => 0,
                        'label' => 'APIs',
                        'slug' => 'apis',
                        'icon' => 'mdi mdi-code-braces',
                        'children' => [
                            [
                                'id' => 0,
                                'label' => 'Services',
                                'slug' => 'apis/services',
                                'icon' => 'mdi mdi-link-variant'
                            ],
                            [
                                'id' => 0,
                                'label' => 'Debug Tool',
                                'slug' => 'apis/debug_tool',
                                'icon' => 'mdi mdi-android-debug-bridge'
                            ],
                            [
                                'id' => 0,
                                'label' => 'Documentation',
                                'slug' => 'apis/documentation',
                                'icon' => 'mdi mdi mdi-book-open-page-variant'
                            ]
                        ]
                    ]
                ];

                $menus = array_merge($menus, $core_menus);
            } elseif (get_userdata('group_id') == 2 && $this->get_theme_property('type') == 'backend') {
                // CMS menus for technical
                $menus = array_merge($menus, $cms_menus);
            }
        }

        if ($this->get_theme_property('type') === 'backend') {
            $dashboard = [
                [
                    'id' => 0,
                    'label' => 'Main Navigation',
                    'slug' => '---',
                    'icon' => null,
                ],
                [
                    'id' => 0,
                    'label' => 'Dashboard',
                    'slug' => 'dashboard',
                    'icon' => 'mdi mdi-monitor-dashboard',
                ]
            ];

            $credits = [
                [
                    'id' => 0,
                    'label' => '',
                    'slug' => '---',
                    'icon' => null,
                ],
                [
                    'id' => 0,
                    'label' => 'About',
                    'slug' => 'pages/about',
                    'icon' => 'mdi mdi-blank',
                    'class' => 'text-sm'
                ],
                [
                    'id' => 0,
                    'label' => 'License',
                    'slug' => 'pages/license',
                    'icon' => 'mdi mdi-blank',
                    'class' => 'text-sm'
                ],
                [
                    'id' => 0,
                    'label' => 'Aksara ' . aksara('build_version'),
                    'slug' => 'https://aksaracms.com',
                    'icon' => 'mdi mdi-blank',
                    'class' => 'text-sm fw-bold',
                    'translate' => false
                ]
            ];

            $menus = array_merge($dashboard, $menus, $credits);
        }

        array_walk_recursive($menus, function (&$label, $key) {
            if ('label' == $key && $label && 'Aksara ' . aksara('build_version') != $label) {
                $label = phrase($label);
            }
        });

        return $menus;
    }
}
