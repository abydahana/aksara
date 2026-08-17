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

use Throwable;
use stdClass;
use Config\Services;
use CodeIgniter\HTTP\Response;
use CodeIgniter\HTTP\ResponseInterface;
use Aksara\Laboratory\Model;
use Aksara\Laboratory\Renderer\Parser;

/**
 * Template handler class for managing themes, views, and output processing.
 */
class Template
{
    /**
     * @var string Static cache for the active theme directory name.
     */
    public static string $activeTheme = '';

    /**
     * @var string The active theme directory name.
     */
    public string $theme;

    // ──────────────────────────────────────────────────────────────
    // Lifecycle
    // ──────────────────────────────────────────────────────────────

    /**
     * Template constructor.
     */
    public function __construct(string $theme = 'frontend')
    {
        $this->theme = $this->_resolveTheme($theme);
        self::$activeTheme = $this->theme;
    }

    // ──────────────────────────────────────────────────────────────
    // Theme Properties & Resolution
    // ──────────────────────────────────────────────────────────────

    /**
     * Getting active theme name.
     */
    public function getTheme(): string
    {
        return $this->theme;
    }

    /**
     * Getting the theme property from theme.json file.
     *
     * @return mixed|false The property value or false if the theme.json or property doesn't exist.
     */
    public function getThemeProperty(?string $parameter = null): mixed
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
     */
    public function getView(string $view = 'index'): string
    {
        $request = Services::request();
        $router = Services::router();

        // Get current controller namespace
        $viewPath = preg_replace(
            [
                '/\\\\aksara\\\\/i',
                '/\\\\modules\\\\/i',
                '/\\\\controllers\\\\/i'
            ],
            [
                '\\',
                '\\',
                '\Views\\'
            ],
            $router->controllerName(),
            1
        );

        // Get parent module classname
        $parentModule = strtok($viewPath, '\\');

        // Get current module classname
        $currentModule = substr($viewPath, strrpos($viewPath, '\\') + 1);

        if (strtolower($parentModule) === strtolower($currentModule)) {
            // Slice out the module path when the parent module has same name
            $viewPath = substr($viewPath, 0, strrpos($viewPath, '\\'));
        }

        // Replace backslash to match with directory separator
        $viewPath = str_replace([$currentModule . '\\' . $currentModule, '\\'], [strtolower($currentModule), '/'], $viewPath);

        // List module and view path
        list($modules, $views) = array_pad(explode('/Views', $viewPath), 2, null);

        // Convert view path as lowercase
        $viewPath = $modules . '/Views' . ($views ? strtolower($views) : null);

        // Theme based viewfinder
        $themeViewfinder = ROOTPATH . 'themes/' . $this->theme . '/views/' . strtolower(preg_replace('/\/views/i', '', $viewPath, 1));

        // Theme based viewfinder fallback
        $fallbackThemeViewfinder = ROOTPATH . 'themes/' . $this->theme . '/components/core';

        // Module based viewfinder
        $moduleViewfinder = ROOTPATH . 'modules' . $viewPath;

        // Core module based viewfinder
        $coreViewfinder = APPPATH . 'Modules' . $viewPath;

        // Fallback theme based viewfinder
        $fallbackTheme = ('backend' == $this->getThemeProperty('type') ? (get_setting('backend_theme') ?: 'backend') : (get_setting('frontend_theme') ?: 'default'));
        $fallbackViewfinder = ROOTPATH . 'themes/' . $fallbackTheme . '/components/core';

        // View suffix
        $suffix = (! is_cli() && $request->getUserAgent()->isMobile() ? '_mobile' : ('modal' == $request->getPost('prefer') ? '_modal' : (isset($_ENV['GRID_VIEW']) && $_ENV['GRID_VIEW'] ? '_grid' : null)));

        // Get user language for i18n view
        $language = get_userdata('language');

        // Method name to force as view when exists
        $method = (! in_array($router->methodName(), ['404', 'index', 'create', 'read', 'update']) ? $router->methodName() : $view);

        if (preg_match('#^(\.\./)+#', $view)) {
            // The view path starts with one or more parent directory traversals
            preg_match_all('#\.\./#', $view, $matches);

            $levels = count($matches[0]);

            // Traverse up the directory tree based on the number of "../" segments.
            while ($levels--) {
                $themeViewfinder = dirname($themeViewfinder);
                $fallbackThemeViewfinder = dirname($fallbackThemeViewfinder);
                $moduleViewfinder = dirname($moduleViewfinder);
                $coreViewfinder = dirname($coreViewfinder);
                $fallbackViewfinder = dirname($fallbackViewfinder);
            }

            // Strip leading "../" segments since the base view directories
            // have already been adjusted accordingly.
            $view = preg_replace('#^(\.\./)+#', '', $view);
        }

        /**
         * ---------------------------------------------------------------------
         * Find view from theme
         * ---------------------------------------------------------------------
         */
        if (file_exists($themeViewfinder . '/' . $language . '/' . $method . $suffix . '.twig') || file_exists($themeViewfinder . '/' . $language . '/' . $method . $suffix . '.php')) {
            // View is found under i18n path
            $view = str_replace(ROOTPATH, '../../', $themeViewfinder . '/' . $language . '/' . $method . $suffix);
        } elseif (file_exists($themeViewfinder . '/' . $language . '/' . $view . $suffix . '.twig') || file_exists($themeViewfinder . '/' . $language . '/' . $view . $suffix . '.php')) {
            // View is found under i18n path
            $view = str_replace(ROOTPATH, '../../', $themeViewfinder . '/' . $language . '/' . $view . $suffix);
        } elseif (file_exists($themeViewfinder . '/' . $language . '/' . $method . '.twig') || file_exists($themeViewfinder . '/' . $language . '/' . $method . '.php')) {
            // View is found under i18n path
            $view = str_replace(ROOTPATH, '../../', $themeViewfinder . '/' . $language . '/' . $method);
        } elseif (file_exists($themeViewfinder . '/' . $language . '/' . $view . '.twig') || file_exists($themeViewfinder . '/' . $language . '/' . $view . '.php')) {
            // View is found under i18n path
            $view = str_replace(ROOTPATH, '../../', $themeViewfinder . '/' . $language . '/' . $view);
        } elseif (file_exists(dirname($themeViewfinder) . '/' . $language . '/' . basename($themeViewfinder) . $suffix . '.twig') || file_exists(dirname($themeViewfinder) . '/' . $language . '/' . basename($themeViewfinder) . $suffix . '.php')) {
            // View is found under i18n path of current classname (lowercase)
            $view = str_replace(ROOTPATH, '../../', dirname($themeViewfinder) . '/' . $language . '/' . basename($themeViewfinder) . $suffix);
        } elseif (file_exists(dirname($themeViewfinder) . '/' . $language . '/' . basename($themeViewfinder) . '.twig') || file_exists(dirname($themeViewfinder) . '/' . $language . '/' . basename($themeViewfinder) . '.php')) {
            // View is found under i18n path of current classname (lowercase)
            $view = str_replace(ROOTPATH, '../../', dirname($themeViewfinder) . '/' . $language . '/' . basename($themeViewfinder));
        } elseif (file_exists($themeViewfinder . '/' . $method . $suffix . '.twig') || file_exists($themeViewfinder . '/' . $method . $suffix . '.php')) {
            // View is found without i18n path
            $view = str_replace(ROOTPATH, '../../', $themeViewfinder . '/' . $method . $suffix);
        } elseif (file_exists($themeViewfinder . '/' . $view . $suffix . '.twig') || file_exists($themeViewfinder . '/' . $view . $suffix . '.php')) {
            // View is found without i18n path
            $view = str_replace(ROOTPATH, '../../', $themeViewfinder . '/' . $view . $suffix);
        } elseif (file_exists($themeViewfinder . '/' . $method . '.twig') || file_exists($themeViewfinder . '/' . $method . '.php')) {
            // View is found without i18n path
            $view = str_replace(ROOTPATH, '../../', $themeViewfinder . '/' . $method);
        } elseif (file_exists($themeViewfinder . '/' . $view . '.twig') || file_exists($themeViewfinder . '/' . $view . '.php')) {
            // View is found without i18n path
            $view = str_replace(ROOTPATH, '../../', $themeViewfinder . '/' . $view);
        } elseif (file_exists($themeViewfinder . $suffix . '.twig') || file_exists($themeViewfinder . $suffix . '.php')) {
            // View is found and same as current classname (lowercase)
            $view = str_replace(ROOTPATH, '../../', $themeViewfinder . $suffix);
        } elseif (file_exists($themeViewfinder . '.twig') || file_exists($themeViewfinder . '.php')) {
            // View is found and same as current classname (lowercase)
            $view = str_replace(ROOTPATH, '../../', $themeViewfinder);
        }

        /**
         * ---------------------------------------------------------------------
         * Find view from user modules
         * ---------------------------------------------------------------------
         */ elseif (file_exists($moduleViewfinder . '/' . $language . '/' . $method . $suffix . '.twig') || file_exists($moduleViewfinder . '/' . $language . '/' . $method . $suffix . '.php')) {
            // View is found under i18n path
            $view = str_replace(ROOTPATH, '../../', $moduleViewfinder . '/' . $language . '/' . $method . $suffix);
        } elseif (file_exists($moduleViewfinder . '/' . $language . '/' . $view . $suffix . '.twig') || file_exists($moduleViewfinder . '/' . $language . '/' . $view . $suffix . '.php')) {
            // View is found under i18n path
            $view = str_replace(ROOTPATH, '../../', $moduleViewfinder . '/' . $language . '/' . $view . $suffix);
        } elseif (file_exists($moduleViewfinder . '/' . $language . '/' . $method . '.twig') || file_exists($moduleViewfinder . '/' . $language . '/' . $method . '.php')) {
            // View is found under i18n path
            $view = str_replace(ROOTPATH, '../../', $moduleViewfinder . '/' . $language . '/' . $method);
        } elseif (file_exists($moduleViewfinder . '/' . $language . '/' . $view . '.twig') || file_exists($moduleViewfinder . '/' . $language . '/' . $view . '.php')) {
            // View is found under i18n path
            $view = str_replace(ROOTPATH, '../../', $moduleViewfinder . '/' . $language . '/' . $view);
        } elseif (file_exists(dirname($moduleViewfinder) . '/' . $language . '/' . basename($moduleViewfinder) . $suffix . '.twig') || file_exists(dirname($moduleViewfinder) . '/' . $language . '/' . basename($moduleViewfinder) . $suffix . '.php')) {
            // View is found under i18n path of current classname (lowercase)
            $view = str_replace(ROOTPATH, '../../', dirname($moduleViewfinder) . '/' . $language . '/' . basename($moduleViewfinder) . $suffix);
        } elseif (file_exists(dirname($moduleViewfinder) . '/' . $language . '/' . basename($moduleViewfinder) . '.twig') || file_exists(dirname($moduleViewfinder) . '/' . $language . '/' . basename($moduleViewfinder) . '.php')) {
            // View is found under i18n path of current classname (lowercase)
            $view = str_replace(ROOTPATH, '../../', dirname($moduleViewfinder) . '/' . $language . '/' . basename($moduleViewfinder));
        } elseif (file_exists($moduleViewfinder . '/' . $method . $suffix . '.twig') || file_exists($moduleViewfinder . '/' . $method . $suffix . '.php')) {
            // View is found without i18n path
            $view = str_replace(ROOTPATH, '../../', $moduleViewfinder . '/' . $method . $suffix);
        } elseif (file_exists($moduleViewfinder . '/' . $view . $suffix . '.twig') || file_exists($moduleViewfinder . '/' . $view . $suffix . '.php')) {
            // View is found without i18n path
            $view = str_replace(ROOTPATH, '../../', $moduleViewfinder . '/' . $view . $suffix);
        } elseif (file_exists($moduleViewfinder . '/' . $method . '.twig') || file_exists($moduleViewfinder . '/' . $method . '.php')) {
            // View is found without i18n path
            $view = str_replace(ROOTPATH, '../../', $moduleViewfinder . '/' . $method);
        } elseif (file_exists($moduleViewfinder . '/' . $view . '.twig') || file_exists($moduleViewfinder . '/' . $view . '.php')) {
            // View is found without i18n path
            $view = str_replace(ROOTPATH, '../../', $moduleViewfinder . '/' . $view);
        } elseif (file_exists($moduleViewfinder . $suffix . '.twig') || file_exists($moduleViewfinder . $suffix . '.php')) {
            // View is found and same as current classname (lowercase)
            $view = str_replace(ROOTPATH, '../../', $moduleViewfinder . $suffix);
        } elseif (file_exists($moduleViewfinder . '.twig') || file_exists($moduleViewfinder . '.php')) {
            // View is found and same as current classname (lowercase)
            $view = str_replace(ROOTPATH, '../../', $moduleViewfinder);
        }

        /**
         * ---------------------------------------------------------------------
         * Find view from core modules
         * ---------------------------------------------------------------------
         */ elseif (file_exists($coreViewfinder . '/' . $language . '/' . $method . $suffix . '.twig') || file_exists($coreViewfinder . '/' . $language . '/' . $method . $suffix . '.php')) {
            // View is found under i18n path
            $view = str_replace(ROOTPATH, '../../', $coreViewfinder . '/' . $language . '/' . $method . $suffix);
        } elseif (file_exists($coreViewfinder . '/' . $language . '/' . $view . $suffix . '.twig') || file_exists($coreViewfinder . '/' . $language . '/' . $view . $suffix . '.php')) {
            // View is found under i18n path
            $view = str_replace(ROOTPATH, '../../', $coreViewfinder . '/' . $language . '/' . $view . $suffix);
        } elseif (file_exists($coreViewfinder . '/' . $language . '/' . $method . '.twig') || file_exists($coreViewfinder . '/' . $language . '/' . $method . '.php')) {
            // View is found under i18n path
            $view = str_replace(ROOTPATH, '../../', $coreViewfinder . '/' . $language . '/' . $method);
        } elseif (file_exists($coreViewfinder . '/' . $language . '/' . $view . '.twig') || file_exists($coreViewfinder . '/' . $language . '/' . $view . '.php')) {
            // View is found under i18n path
            $view = str_replace(ROOTPATH, '../../', $coreViewfinder . '/' . $language . '/' . $view);
        } elseif (file_exists(dirname($coreViewfinder) . '/' . $language . '/' . basename($coreViewfinder) . $suffix . '.twig') || file_exists(dirname($coreViewfinder) . '/' . $language . '/' . basename($coreViewfinder) . $suffix . '.php')) {
            // View is found under i18n path of current classname (lowercase)
            $view = str_replace(ROOTPATH, '../../', dirname($coreViewfinder) . '/' . $language . '/' . basename($coreViewfinder) . $suffix);
        } elseif (file_exists(dirname($coreViewfinder) . '/' . $language . '/' . basename($coreViewfinder) . '.twig') || file_exists(dirname($coreViewfinder) . '/' . $language . '/' . basename($coreViewfinder) . '.php')) {
            // View is found under i18n path of current classname (lowercase)
            $view = str_replace(ROOTPATH, '../../', dirname($coreViewfinder) . '/' . $language . '/' . basename($coreViewfinder));
        } elseif (file_exists($coreViewfinder . '/' . $view . $suffix . '.twig') || file_exists($coreViewfinder . '/' . $view . $suffix . '.php')) {
            // View is found without i18n path
            $view = str_replace(ROOTPATH, '../../', $coreViewfinder . '/' . $view . $suffix);
        } elseif (file_exists($coreViewfinder . '/' . $method . '.twig') || file_exists($coreViewfinder . '/' . $method . '.php')) {
            // View is found without i18n path
            $view = str_replace(ROOTPATH, '../../', $coreViewfinder . '/' . $method);
        } elseif (file_exists($coreViewfinder . '/' . $view . '.twig') || file_exists($coreViewfinder . '/' . $view . '.php')) {
            // View is found without i18n path
            $view = str_replace(ROOTPATH, '../../', $coreViewfinder . '/' . $view);
        } elseif (file_exists($coreViewfinder . $suffix . '.twig') || file_exists($coreViewfinder . $suffix . '.php')) {
            // View is found and same as current classname (lowercase)
            $view = str_replace(ROOTPATH, '../../', $coreViewfinder . $suffix);
        } elseif (file_exists($coreViewfinder . '.twig') || file_exists($coreViewfinder . '.php')) {
            // View is found and same as current classname (lowercase)
            $view = str_replace(ROOTPATH, '../../', $coreViewfinder);
        }

        /**
         * ---------------------------------------------------------------------
         * Find fallback view if doesn't match anything from above occurrence
         * ---------------------------------------------------------------------
         */ elseif (file_exists($fallbackThemeViewfinder . '/' . $method . $suffix . '.twig') || file_exists($fallbackThemeViewfinder . '/' . $method . $suffix . '.php')) {
            // View is found without i18n path
            $view = str_replace(ROOTPATH, '../../', $fallbackThemeViewfinder . '/' . $method . $suffix);
        } elseif (file_exists($fallbackThemeViewfinder . '/' . $view . $suffix . '.twig') || file_exists($fallbackThemeViewfinder . '/' . $view . $suffix . '.php')) {
            // View is found without i18n path
            $view = str_replace(ROOTPATH, '../../', $fallbackThemeViewfinder . '/' . $view . $suffix);
        } elseif (file_exists($fallbackThemeViewfinder . '/' . $method . '.twig') || file_exists($fallbackThemeViewfinder . '/' . $method . '.php')) {
            // View is found without i18n path
            $view = str_replace(ROOTPATH, '../../', $fallbackThemeViewfinder . '/' . $method);
        } elseif (file_exists($fallbackThemeViewfinder . '/' . $view . '.twig') || file_exists($fallbackThemeViewfinder . '/' . $view . '.php')) {
            // View is found without i18n path
            $view = str_replace(ROOTPATH, '../../', $fallbackThemeViewfinder . '/' . $view);
        } elseif (file_exists($fallbackViewfinder . '/' . $method . $suffix . '.twig') || file_exists($fallbackViewfinder . '/' . $method . $suffix . '.php')) {
            // View fallback is found
            $view = str_replace(ROOTPATH, '../../', $fallbackViewfinder . '/' . $method . $suffix);
        } elseif (file_exists($fallbackViewfinder . '/' . $view . $suffix . '.twig') || file_exists($fallbackViewfinder . '/' . $view . $suffix . '.php')) {
            // View fallback is found
            $view = str_replace(ROOTPATH, '../../', $fallbackViewfinder . '/' . $view . $suffix);
        } elseif (file_exists($fallbackViewfinder . '/' . $method . '.twig') || file_exists($fallbackViewfinder . '/' . $method . '.php')) {
            // View fallback is found
            $view = str_replace(ROOTPATH, '../../', $fallbackViewfinder . '/' . $method);
        } elseif (file_exists($fallbackViewfinder . '/' . $view . '.twig') || file_exists($fallbackViewfinder . '/' . $view . '.php')) {
            // View fallback is found
            $view = str_replace(ROOTPATH, '../../', $fallbackViewfinder . '/' . $view);
        } elseif (file_exists($fallbackViewfinder . $suffix . '.twig') || file_exists($fallbackViewfinder . $suffix . '.php')) {
            // View fallback is found and same as classname (lowercase)
            $view = str_replace(ROOTPATH, '../../', $fallbackViewfinder . $suffix);
        } elseif (file_exists($fallbackViewfinder . '.twig') || file_exists($fallbackViewfinder . '.php')) {
            // View fallback is found and same as classname (lowercase)
            $view = str_replace(ROOTPATH, '../../', $fallbackViewfinder);
        } else {
            // No matches view, check fallback
            if ($router->getMatchedRoute()) {
                // No mode
                $view = str_replace(ROOTPATH, '../../', $fallbackViewfinder . '/error');
            } else {
                // No router found
                if (file_exists(dirname($themeViewfinder) . '/404.twig') || file_exists(dirname($themeViewfinder) . '/404.php')) {
                    // Use theme view
                    $view = str_replace(ROOTPATH, '../../', dirname($themeViewfinder) . '/404');
                } elseif (file_exists($fallbackThemeViewfinder . '/404.twig') || file_exists($fallbackThemeViewfinder . '/404.php')) {
                    // Use theme component
                    $view = str_replace(ROOTPATH, '../../', $fallbackThemeViewfinder . '/404');
                } else {
                    // Use core view
                    $view = str_replace(ROOTPATH, '../../', $fallbackViewfinder . '/404');
                }
            }
        }

        // Remove duplicate directory separator
        $view = str_replace('//', '/', $view);

        // Adjust relative path prefix dynamically based on active view directory to support vendor layout
        if (str_starts_with($view, '../../')) {
            $view = $this->_getRelativePrefix() . substr($view, 6);
        }

        return $view;
    }

    // ──────────────────────────────────────────────────────────────
    // Page Building & Rendering
    // ──────────────────────────────────────────────────────────────

    /**
     * Build output view or object.
     *
     * Processes the view, applies templates, minifies output, and sends the response.
     *
     * @return \CodeIgniter\HTTP\Response|object The HTTP Response object for non-AJAX requests or a JSON object for AJAX requests.
     */
    public function build(?string $view = null, array $data = []): Response|ResponseInterface
    {
        $request = Services::request();

        // Fix encoding
        $data = encoding_fixer($data);

        $data = json_decode(json_encode($data), false);

        if (! is_object($data)) {
            $data = (object) $data;
        }

        // Get view
        $view = $this->getView($view);

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

        // Get relative path prefix dynamically
        $relativePrefix = $this->_getRelativePrefix();

        // Main templates definition
        $mainTemplates = [
            $relativePrefix . 'aksara/Views/core/index',
            $relativePrefix . 'themes/' . $this->theme . '/components/core/index',
            $relativePrefix . 'aksara/Views/core/index_grid',
            $relativePrefix . 'themes/' . $this->theme . '/components/core/index_grid',
            $relativePrefix . 'aksara/Views/core/index_mobile',
            $relativePrefix . 'themes/' . $this->theme . '/components/core/index_mobile',
            $relativePrefix . 'aksara/Views/core/form',
            $relativePrefix . 'themes/' . $this->theme . '/components/core/form',
            $relativePrefix . 'aksara/Views/core/form_modal',
            $relativePrefix . 'themes/' . $this->theme . '/components/core/form_modal',
            $relativePrefix . 'aksara/Views/core/read',
            $relativePrefix . 'themes/' . $this->theme . '/components/core/read',
            $relativePrefix . 'aksara/Views/core/read_modal',
            $relativePrefix . 'themes/' . $this->theme . '/components/core/read_modal',
            $relativePrefix . 'aksara/Views/core/error',
            $relativePrefix . 'themes/' . $this->theme . '/components/core/error'
        ];

        // Set view to response
        $data->view = basename($view);

        $viewPathTwig = str_replace($relativePrefix, ROOTPATH, $view . '.twig');
        $viewPathPhp = str_replace($relativePrefix, ROOTPATH, $view . '.php');

        if ((file_exists($viewPathTwig) || file_exists($viewPathPhp)) && (! in_array($view, $mainTemplates) || (in_array($view, $mainTemplates) && (is_cli() || ! $request->isAJAX())))) {
            if (file_exists($viewPathTwig)) {
                // Load Twig template parser
                $parser = new Parser();

                // Build html from result object
                $data->content = $parser->parse($viewPathTwig, (array) $data);
            } else {
                // Build html from result object
                $data->content = view($view, (array) $data);
            }

            // Intersection key to keep property from unset
            $intersectionKey = ['code', 'method', 'prefer', 'meta', 'breadcrumb', 'limit', 'links', 'total', 'current_page', 'current_module', 'query_params', 'elapsed_time', 'content', '_token'];

            foreach ($data as $key => $val) {
                if (! in_array($key, $intersectionKey)) {
                    // Unset rendered object
                    unset($data->$key);
                }
            }
        }

        if (! is_cli() && $request->isAJAX() && $request->getServer('HTTP_REFERER') && stripos($request->getServer('HTTP_REFERER'), $request->getServer('SERVER_NAME')) !== false) {
            // Send to client
            return make_json($data);
        } else {
            // Add core menus into data object
            $data->menus = encoding_fixer($this->_coreMenus());

            // Convert array to object
            $data = json_decode(json_encode($data), false); // Use false for $associative to ensure object

            if (file_exists(ROOTPATH . 'themes/' . $this->theme . '/layout.twig')) {
                // Load Twig template parser
                $parser = new Parser();

                // Build html from result object
                $parsedView = $parser->parse(ROOTPATH . 'themes/' . $this->theme . '/layout.twig', (array) $data);
            } else {
                // Build html from result object
                $parsedView = view($relativePrefix . 'themes/' . $this->theme . '/layout', (array) $data);
            }

            // Minify output
            $output = $this->_minify(str_replace('</body>', '<div class="' . implode('', ['ak', 'sa', 'ra', '-', 'fo', 'ot', 'er']) . '"></div></body>', $parsedView));

            $response = Services::response();

            // Add security headers
            $response->setHeader('Permissions-Policy', 'geolocation=(self "' . base_url() . '")');
            $response->setHeader('Referrer-Policy', 'same-origin');
            $response->setHeader('Set-Cookie', 'HttpOnly; Secure');
            $response->setHeader('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
            $response->setHeader('X-Content-Type-Options', 'nosniff');
            $response->setHeader('X-Frame-Options', 'SAMEORIGIN');
            $response->setHeader('X-XSS-Protection', '1; mode=block');

            return $response->setBody($output);
        }
    }

    // ──────────────────────────────────────────────────────────────
    // Navigation & Pagination
    // ──────────────────────────────────────────────────────────────

    /**
     * Generate breadcrumb array structure.
     *
     * @return array<int, array{url: string, label: string, icon: string}> The array of breadcrumb items.
     */
    public function breadcrumb(array $data = [], ?string $title = null, array $primary = []): array
    {
        $request = Services::request();
        $router = Services::router();
        $uri = Services::uri();

        $slug = null;
        $checker = $uri->getSegments();
        $matchedRoute = $router->getMatchedRoute();
        $matchedRoute = (isset($matchedRoute[0]) ? explode('/', $matchedRoute[0]) : []);

        if (! $data) {
            $data = [];

            foreach ($checker as $val) {
                $data[$val] = str_replace('_', ' ', $val);
            }
        }

        if ($this->getThemeProperty('type') == 'backend') {
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

        $slug = null;
        $params = $request->getGet();

        foreach ($params as $key => $val) {
            if (in_array($key, array_merge($primary, ['page', 'q', 'order', 'sort', 'limit', 'offset']))) {
                $params[$key] = null;
            }
        }

        // Get last breadcrumb
        $currentPage = end($data);

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
                $breadcrumbParams = $params;

                if (is_array($val) && isset($val['label'])) {
                    $label = $val['label'];

                    if (isset($val['parameter']) && is_array($val['parameter'])) {
                        $breadcrumbParams = array_merge($params, $val['parameter']);
                    }
                } else {
                    $label = $val;
                }

                $output[] = [
                    'url' => $external ?? base_url($slug, $breadcrumbParams),
                    'label' => phrase($label, [], true), // Use phrase only if exist in translation
                    'icon' => ''
                ];
            }
        }

        $output[] = [
            'url' => '',
            'label' => $title ?? $currentPage,
            'icon' => ''
        ];

        return $output;
    }

    /**
     * Generate pagination data structure.
     *
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

        if (! isset($data->total)) {
            // If there's no result, set to 0
            $data->total = 0;
        }

        if (! isset($data->limit)) {
            // If no limit setting, set default
            $data->limit = 25;
        }

        if (! isset($data->offset)) {
            // If there's no offset, set to 0
            $data->offset = 0;
        }

        $output = [];
        $queryParams = [];
        $request = Services::request();
        $pager = Services::pager();

        // Make pagination links
        $pagination = $pager->makeLinks(1, $data->limit, $data->total, 'pagination');

        // Get last page
        $lastPage = ($data->total > $data->limit ? (int) ceil($data->total / $data->limit) : 1);

        foreach ($request->getGet() as $key => $val) {
            if (is_array($val)) {
                foreach ($val as $_key => $_val) {
                    if (is_array($_val)) {
                        continue;
                    }

                    $_key = preg_replace('/[^\w-]/', '', $_key);

                    if (! $_key || in_array($_key, ['q', 'page'])) {
                        continue;
                    }

                    $queryParams[] = [
                        'name' => $_key,
                        'value' => htmlspecialchars($_val)
                    ];
                }

                continue;
            }

            $key = preg_replace('/[^\w-]/', '', $key);

            if (! $key || in_array($key, ['q', 'page'])) {
                continue;
            }

            $queryParams[] = [
                'name' => $key,
                'value' => htmlspecialchars($val)
            ];
        }

        $output = [
            'total' => (int) $data->total,
            'limit' => (int) $data->limit,
            'page' => (is_numeric($request->getGet('page')) && $request->getGet('page') ? (int) $request->getGet('page') : 1),
            'action' => current_page(null, ['page' => null]),
            'filters' => [
                'hidden' => $queryParams,
                'select' => [
                    [
                        'name' => 'limit',
                        'values' => [
                            [
                                'value' => (int) $data->limit,
                                'label' => (int) $data->limit,
                                'selected' => true
                            ],
                            [
                                'value' => (int) ($data->limit * 2),
                                'label' => (int) ($data->limit * 2),
                                'selected' => (int) $data->limit === ($data->limit * 2)
                            ],
                            [
                                'value' => (int) ($data->limit * 4),
                                'label' => (int) ($data->limit * 4),
                                'selected' => (int) $data->limit === ($data->limit * 4)
                            ],
                            [
                                'value' => (int) ($data->limit * 8),
                                'label' => (int) ($data->limit * 8),
                                'selected' => (int) $data->limit === ($data->limit * 8)
                            ],
                            [
                                'value' => (int) ($data->limit * 20),
                                'label' => (int) ($data->limit * 20),
                                'selected' => (int) $data->limit === ($data->limit * 20)
                            ]
                        ]
                    ]
                ],
                'number' => [
                    [
                        'name' => 'page',
                        'value' => (is_numeric($request->getGet('page')) && $request->getGet('page') ? (int) $request->getGet('page') : 1),
                        'min' => 1,
                        'max' => $lastPage
                    ]
                ]
            ],
            'information' => phrase('Showing {{start}} - {{end}} of {{total}} entries found.', [
                'start' => ($data->offset ? number_format($data->offset) : ($data->total ? 1 : 0)),
                'end' => ((($data->offset + $data->limit) < $data->total ? number_format(($data->offset + $data->limit)) : number_format($data->total))),
                'total' => number_format($data->total)
            ])
        ];

        if (25 != $data->limit) {
            $defaultLimit = [
                [
                    'value' => 25,
                    'label' => 25,
                    'selected' => 25 === (int) $data->limit
                ]
            ];

            $output['filters']['select'][0]['values'] = array_merge($defaultLimit, $output['filters']['select'][0]['values']);
        }

        $output['links'] = [];

        $dom = new \DOMDocument();
        $internalErrors = libxml_use_internal_errors(true);

        $dom->loadHTML('<?xml encoding="UTF-8">' . $pagination, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

        libxml_clear_errors();
        libxml_use_internal_errors($internalErrors);

        foreach ($dom->getElementsByTagName('li') as $li) {
            /** @var \DOMElement $li */
            $anchor = $li->getElementsByTagName('a')->item(0);

            if ($anchor) {
                $output['links'][] = [
                    'id' => (string) $anchor->getAttribute('id'),
                    'parent_class' => (string) $li->getAttribute('class'),
                    'class' => (string) $anchor->getAttribute('class'),
                    'href' => (string) $anchor->getAttribute('href'),
                    'label' => trim(htmlspecialchars($anchor->textContent))
                ];
            }
        }

        return $output;
    }

    // ──────────────────────────────────────────────────────────────
    // Private: Theme Resolution & Processing
    // ──────────────────────────────────────────────────────────────

    /**
     * Resolve a theme context (frontend/backend) or explicit theme folder.
     */
    private function _resolveTheme(string $theme): string
    {
        if (! $theme) {
            $theme = 'frontend';
        }

        if (in_array($theme, ['frontend', 'backend'])) {
            return get_setting($theme . '_theme') ?: ('backend' == $theme ? 'backend' : 'default');
        }

        return $theme;
    }

    /**
     * Function to minify HTML.
     *
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
     */
    private function _coreMenus(array $menus = []): array
    {
        if (! $menus) {
            $groupId = get_userdata('group_id');

            // Load model
            $model = new Model();

            // Get menu data
            $serializedMenu = $model->select('
                menu_structure
            ')
            ->groupStart()
            ->where('group_id', $groupId)
            ->orWhere('group_id', 0)
            ->groupEnd()
            ->getWhere(
                'app_menus',
                [
                    'menu_placement' => ('frontend' == $this->getThemeProperty('type') ? 'header' : 'sidebar')
                ],
                1
            )
            ->row('menu_structure');

            $menus = ($serializedMenu ? json_decode($serializedMenu, true) : []);
            $cmsMenus = [
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

            if (get_userdata('group_id') == 1 && $this->getThemeProperty('type') == 'backend') {
                // Core menus for global administrator
                $coreMenus = [
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
                        'children' => $cmsMenus
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
                                        'label' => 'Banned',
                                        'slug' => 'administrative/logs/banned',
                                        'icon' => 'mdi mdi-shield-alert-outline'
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

                $menus = array_merge($menus, $coreMenus);
            } elseif (get_userdata('group_id') == 2 && $this->getThemeProperty('type') == 'backend') {
                // CMS menus for technical
                $menus = array_merge($menus, $cmsMenus);
            }
        }

        if ($this->getThemeProperty('type') === 'backend') {
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

    /**
     * Compute relative path prefix from CodeIgniter's viewDirectory to project's ROOTPATH
     */
    private function _getRelativePrefix(): string
    {
        $paths = new \Config\Paths();
        $viewDirectory = realpath($paths->viewDirectory);
        $rootPath = realpath(ROOTPATH);

        if ($viewDirectory && $rootPath) {
            $fromParts = explode('/', trim(str_replace('\\', '/', $viewDirectory), '/'));
            $toParts = explode('/', trim(str_replace('\\', '/', $rootPath), '/'));
            $commonLength = 0;
            $minParts = min(count($fromParts), count($toParts));

            for ($i = 0; $i < $minParts; $i++) {
                if (strtolower($fromParts[$i]) === strtolower($toParts[$i])) {
                    $commonLength++;
                } else {
                    break;
                }
            }

            $upCount = count($fromParts) - $commonLength;

            return str_repeat('../', $upCount);
        }

        return '../../';
    }
}
