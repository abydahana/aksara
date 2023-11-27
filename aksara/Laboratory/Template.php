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

use Aksara\Laboratory\Model;
use Aksara\Libraries\Beautifier;
use Aksara\Libraries\Html_dom;

use Aksara\Laboratory\Renderer\Parser;

class Template
{
    public $theme;

    private $_css;

    private $_js;

    private $_model;

    private $_partial_view;

    private $_method;

    public function __construct($theme = 'frontend', $method = 'index')
    {
        $this->theme = $theme;
        $this->_method = $method;

        $this->_model = new Model();

        if (! $this->theme) {
            // Throwback the default theme from site configuration
            $site_id = get_setting('id');

            $this->theme = $this->_model->select('frontend_theme')->get_where(
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
     * Getting active theme
     */
    public function get_theme()
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

        return $query;
    }

    /**
     * Getting the theme property
     *
     * @param   mixed|null $parameter
     * @param   null|mixed $parameter
     */
    public function get_theme_property($parameter = null)
    {
        if (file_exists('../themes/' . $this->theme . '/package.json')) {
            // Check if active theme has a property
            $property = new \stdClass();

            try {
                $property = json_decode(file_get_contents('../themes/' . $this->theme . '/package.json'));
            } catch(\Throwable $e) {
                // Safe abstraction
            }

            if ($parameter && isset($property->$parameter)) {
                return $property->$parameter;
            }
        }

        return false;
    }

    /**
     * Scan the view file location both camelized string and lowercase
     */
    public function get_view(string $view = 'index')
    {
        // Get current controller namespace
        $view_path = preg_replace(['/\\\\aksara\\\\/i', '/\\\\modules\\\\/i', '/\\\\controllers\\\\/i'], ['\\', '\\', '\Views\\'], service('router')->controllerName(), 1);

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
        $fallback_viewfinder = ROOTPATH . 'aksara/Views/core';

        // View suffix
        $suffix = (service('request')->getUserAgent()->isMobile() ? '_mobile' : ('modal' == service('request')->getPost('prefer') ? '_modal' : (env('GRID_VIEW') ? '_grid' : null)));

        // Get user language for i18n view
        $language = get_userdata('language');

        // Method name to force as view when exists
        $method = ('404' !== $view ? service('router')->methodName() : $view);

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
        } elseif (file_exists($theme_viewfinder . $suffix . '.twig') || file_exists($theme_viewfinder . '.php')) {
            // View is found and same as current classname (lowercase)
            $view = str_replace(ROOTPATH, '../../', $theme_viewfinder);
        }

        /**
         * ---------------------------------------------------------------------
         * Find view from user modules
         * ---------------------------------------------------------------------
         */
        elseif (file_exists($module_viewfinder . '/' . $language . '/' . $method . $suffix . '.twig') || file_exists($module_viewfinder . '/' . $language . '/' . $method . $suffix . '.php')) {
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
         */
        elseif (file_exists($core_viewfinder . '/' . $language . '/' . $method . $suffix . '.twig') || file_exists($core_viewfinder . '/' . $language . '/' . $method . $suffix . '.php')) {
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
         */
        elseif (file_exists($fallback_theme_viewfinder . '/' . $method . $suffix . '.twig') || file_exists($fallback_theme_viewfinder . '/' . $method . $suffix . '.php')) {
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
            if (service('router')->getMatchedRoute()) {
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
     * Build output view or object
     * @param   null|mixed $view
     * @param   null|mixed $table
     */
    public function build($view = null, $data = [], $table = null)
    {
        // Fix encoding
        $data = encoding_fixer($data);

        // Convert array to object
        $data = json_decode(json_encode($data));

        // Get view
        $view = $this->get_view($view);

        // Load active theme helper if any
        if (is_dir(ROOTPATH . 'themes/' . $this->theme . '/helpers')) {
            // Load filesystem helper
            helper('filesystem');

            // List available helper files
            $helpers = directory_map(ROOTPATH . 'themes/' . $this->theme . '/helpers', 1);

            foreach ($helpers as $key => $helper) {
                if (strtolower(pathinfo($val, PATHINFO_EXTENSION)) === 'php') {
                    // Load helper
                    helper('themes/' . $this->theme . '/helpers/' . $helper);
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

        if ((file_exists(str_replace('../../', ROOTPATH, $view . '.twig')) || file_exists(str_replace('../../', ROOTPATH, $view . '.php'))) && (! in_array($view, $main_templates) || (in_array($view, $main_templates) && ! service('request')->isAJAX()))) {
            if (file_exists(str_replace('../../', ROOTPATH, $view . '.twig'))) {
                // Load Twig template parser
                $parser = new Parser($this->theme);

                // Build html from result object
                $data->content = $parser->parse(str_replace('../../', ROOTPATH, $view . '.twig'), (array) $data);
            } else {
                // Build html from result object
                $data->content = view($view, (array) $data);
            }

            // Intersection key to keep property from unset
            $intersection_key = ['code', 'method', 'prefer', 'meta', 'breadcrumb', 'limit', 'links', 'total', 'current_page', 'current_module', 'query_string', 'elapsed_time', 'content', '_token'];

            foreach ($data as $key => $val) {
                if (! in_array($key, $intersection_key)) {
                    // Unset rendered object
                    unset($data->$key);
                }
            }
        }

        if (service('request')->isAJAX() && service('request')->getServer('HTTP_REFERER') && stripos(service('request')->getServer('HTTP_REFERER'), service('request')->getServer('SERVER_NAME')) !== false) {
            // Send to client
            return make_json($data);
        } else {
            // Add core menus into data object
            $data->menus = $this->_core_menus();

            // Convert array to object
            $data = json_decode(json_encode($data));

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
            $output = $this->_beautify($parsed_view);

            return service('response')->setBody($output)->send();
        }
    }

    /**
     * Generate breadcrumb
     *
     * @param   mixed|array $data
     * @param   string $title
     */
    public function breadcrumb($data = [], $title = null, bool $translate = false)
    {
        $slug = null;
        $checker = service('request')->uri->getSegments();
        $matched_route = service('router')->getMatchedRoute();
        $matched_route = (isset($matched_route[0]) ? explode('/', $matched_route[0]) : []);
        $params = service('request')->getGet();
        $params['per_page'] = null;
        $params['q'] = null;
        $params['order'] = null;
        $params['sort'] = null;

        if (! $data || ! is_array($data)) {
            $data = [];

            foreach ($checker as $key => $val) {
                $data[$val] = ($translate && in_array($val, $matched_route) ? phrase(ucwords(str_replace('_', ' ', $val))) : ucwords(str_replace('_', ' ', $val)));
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

        foreach ($data as $key => $val) {
            $slug .= ($slug ? '/' : null) . $key;

            if ($key && $val) {
                if ($key != $current_slug) {
                    $output[] = [
                        'url' => base_url($slug, $params),
                        'label' => $val,
                        'icon' => ''
                    ];
                }
            }
        }

        return $output;
    }

    /**
     * Generate pagination
     *
     * @param   mixed|array $data
     */
    public function pagination($data = [], bool $api_client = false)
    {
        if (! $data) {
            // Safe abstraction
            $data = new \stdClass();
        } elseif (is_array($data)) {
            // Convert array to object
            $data = json_decode(json_encode($data));
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

        $output = null;

        $pager = \Config\Services::pager();

        // Get last page
        $last_page = ($data->total_rows > $data->per_page ? (int) ceil($data->total_rows / $data->per_page) : 1);

        // Make pagination links
        $pagination = $pager->makeLinks(1, $data->per_page, $data->total_rows, 'pagination');

        // Parse HTML
        $parser = new \Aksara\Libraries\Html_dom();
        $buffer = $parser->str_get_html($pagination);

        $query_string = [];

        foreach (service('request')->getGet() as $key => $val) {
            $key = preg_replace('/[^\w-]/', '', $key);

            if (! $key || in_array($key, ['q', 'per_page'])) {
                continue;
            }

            $query_string[] = [
                'name' => $key,
                'value' => htmlspecialchars($val)
            ];
        }

        $output = [
            'total_rows' => $data->total_rows,
            'per_page' => $data->per_page,
            'action' => current_page(null, ['per_page' => null]),
            'filters' => [
                'hidden' => $query_string,
                'select' => [
                    [
                        'name' => 'limit',
                        'values' => [
                            [
                                'value' => $data->per_page,
                                'label' => $data->per_page,
                                'selected' => ! $data->per_page
                            ],
                            [
                                'value' => ($data->per_page * 2),
                                'label' => ($data->per_page * 2),
                                'selected' => ($data->per_page * 2) === $data->per_page
                            ],
                            [
                                'value' => ($data->per_page * 4),
                                'label' => ($data->per_page * 4),
                                'selected' => ($data->per_page * 4) === $data->per_page
                            ],
                            [
                                'value' => ($data->per_page * 8),
                                'label' => ($data->per_page * 8),
                                'selected' => ($data->per_page * 8) === $data->per_page
                            ],
                            [
                                'value' => ($data->per_page * 20),
                                'label' => ($data->per_page * 20),
                                'selected' => ($data->per_page * 20) === $data->per_page
                            ]
                        ]
                    ]
                ],
                'number' => [
                    [
                        'name' => 'per_page',
                        'value' => (is_numeric(service('request')->getGet('per_page')) && service('request')->getGet('per_page') ? service('request')->getGet('per_page') : 1),
                        'min' => 1,
                        'max' => $last_page
                    ]
                ]
            ],
            'information' => phrase('Showing') . ' ' . ($data->offset ? number_format($data->offset) : ($data->total_rows ? 1 : 0)) . ' - ' . (($data->offset + $data->per_page) < $data->total_rows ? number_format(($data->offset + $data->per_page)) : number_format($data->total_rows)) . ' ' . phrase('of') . ' ' . number_format($data->total_rows) . ' ' . ($data->total_rows > 1 ? phrase('entries found') : phrase('entry found'))
        ];

        foreach ($buffer->find('ul li') as $key => $val) {
            $output['links'][] = [
                'parent_class' => $val->class,
                'class' => $val->find('a', 0)->class,
                'href' => $val->find('a', 0)->href,
                'label' => trim(str_replace('&amp;', '&', htmlspecialchars($val->find('a', 0)->innertext)))
            ];
        }

        return $output;
    }

    /**
     * Function to beautify HTML
     * @param null|mixed $buffer
     */
    private function _beautify($buffer = null)
    {
        $beautifier = new Beautifier([
            'indent_inner_html' => true,
            'indent_char' => ' ',
            'indent_size' => 4,
            'wrap_line_length' => 32786,
            'unformatted' => ['code', 'pre', 'textarea'],
            'preserve_newlines' => false,
            'max_preserve_newlines' => 32786,
            'indent_scripts' => 'normal' // keep|separate|normal
        ]);

        // Beautify output
        return $beautifier->beautify($buffer);
    }

    /**
     * Function to minify HTML
     * @param null|mixed $buffer
     */
    private function _minify($buffer = null)
    {
        // Beautify buffer
        $buffer = $this->_beautify($buffer);

        // Make a backup regex
        $buffer = str_replace('\\//', '_BACKUP_', $buffer);

        // Remove comments from buffer
        $buffer = preg_replace('/(?:(?:\/\*(?:[^*]|(?:\*+[^*\/]))*\*+\/)|(?:(?<!\:|\\\|\'|\")\/\/.*))/', '', $buffer);

        // Restore backup
        $buffer = str_replace('_BACKUP_', '\//', $buffer);

        // Make a backup of "pre" tag
        preg_match_all('#\<pre.*\>(.*)\<\/pre\>#Uis', $buffer, $pre_backup);

        $buffer = str_replace($pre_backup[0], array_map(function ($element) {return '<pre>' . $element . '</pre>';}, array_keys($pre_backup[0])), $buffer);

        $pattern = [
            '/[\r|\n|\t]+/' => ' ',     // Replace end of line by space
            '/(\s)+/s' => '$1',         // Shorten multiple whitespace sequences
            '/<!--(.|\s)*?-->/' => ''   //remove HTML comments
        ];

        $buffer = preg_replace(array_keys($pattern), array_values($pattern), $buffer);

        // Rollback the pre tag
        $buffer = str_replace(array_map(function ($element) {return '<pre>' . $element . '</pre>';}, array_keys($pre_backup[0])), $pre_backup[0], $buffer);

        return $buffer;
    }

    /**
     * Default core menus
     */
    private function _core_menus(array $menus = [])
    {
        if (! $menus) {
            $group_id = get_userdata('group_id');

            $menus = $this->_model->select('
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

            $menus = ($menus ? json_decode($menus, true) : []);

            if (get_userdata('group_id') == 1 && $this->get_theme_property('type') == 'backend') {
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
                        'children' => [
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
                        ]
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
                        'label' => 'Notifications',
                        'slug' => 'notifications',
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
                    'slug' => '---',
                    'icon' => 'mdi mdi-blank',
                    'class' => 'text-sm',
                    'translate' => false
                ]
            ];

            $menus = array_merge($dashboard, $menus, $credits);
        }

        array_walk_recursive($menus, function (&$label, $key) {
            if ('label' == $key && $label && 'Aksara ' . aksara('build_version') != $label) {
                $label = phrase($label, true);
            }
        });

        return $menus;
    }
}
