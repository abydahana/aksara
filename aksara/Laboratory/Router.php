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

use Config\Services;

class Router
{
    private $_request;
    private string $_uriString;
    private bool $_found;
    private array $_collection;

    public function __construct($routes = null)
    {
        $this->_request = Services::request();
        $this->_uriString = trim(uri_string(), '/');

        if ($this->_uriString) {
            $uri = $this->_request->getUri();
            $uri = $uri->setPath($this->_uriString);
            $this->_request = $this->_request->withUri($uri);
        }

        $find_duplicate = array_reverse(explode('/', $this->_uriString));
        $is_duplicate = (isset($find_duplicate[0]) && isset($find_duplicate[1]) && $find_duplicate[0] == $find_duplicate[1] ? true : false);

        $this->_found = false;
        $this->_collection = [];

        helper('filesystem');

        $this->_directoryRoute($routes, directory_map(ROOTPATH . 'modules'), '\Modules\\');

        if (! $this->_found) {
            // Public module (module overwriter) not found core module instead
            $this->_directoryRoute($routes, directory_map(ROOTPATH . 'aksara/Modules'), '\Aksara\Modules\\');
        }

        if ($this->_collection) {
            // Get higher namespace as route priority
            $higher = max(array_keys($this->_collection));
            $namespace = $this->_collection[$higher];
            $namespace = substr($namespace, 0, strrpos($namespace, '.'));
            $controller = substr($namespace, strrpos($namespace, '\\') + 1);

            // Extract segments to find the actual method and its extra parameters
            $method = '';
            $extra_params = [];
            $uri_segments = explode('/', $this->_uriString);
            $method_found = false;
            
            if (class_exists($namespace)) {
                foreach ($uri_segments as $segment) {
                    if (strtolower($segment) == strtolower($controller)) {
                        continue;
                    }
                    if (!$method_found) {
                        $discovered = $this->_discoverMethod($namespace, $segment);
                        if ($discovered && method_exists($namespace, $discovered)) {
                            $method = $discovered;
                            $method_found = true;
                            continue;
                        }
                    }
                    if ($method_found) {
                        $extra_params[] = $segment;
                    }
                }
            }
            
            if (!$method) {
                $method = (strpos($this->_uriString, '/') !== false ? substr($this->_uriString, strrpos($this->_uriString, '/') + 1) : '');
            }

            // Get priority file
            $file = str_replace('\\', '/', lcfirst(ltrim(str_replace('\\' . $controller . '\\' . $controller, '\\' . $controller, $namespace . '\\' . ucfirst($method) . '.php'), '\\')));

            // Get second file under hierarchy
            $second_file = str_replace('\\', '/', lcfirst(ltrim(str_replace('\\' . $controller . '\\' . $controller, '\\' . $controller, substr($namespace, 0, strripos($namespace, '\\')) . '\\' . ucfirst($method) . '.php'), '\\')));

            // Check if priority file is exists
            if (file_exists(ROOTPATH . $file)) {
                // File exists, apply to route
                $namespace = str_replace('\\' . $controller . '\\' . $controller, '\\' . $controller, $namespace . '\\' . ucfirst($method));

                $method = $this->_discoverMethod($namespace, $method);

                // Add route for current request
                $dest = $namespace . ($is_duplicate && $method && method_exists($namespace, $method) ? '::' . $method : null);
                if (!empty($extra_params)) {
                    $dest .= '/' . implode('/', $extra_params);
                }
                $routes->add($this->_uriString, $dest);
            }

            // Check if second file is exists
            elseif (file_exists(ROOTPATH . $second_file)) {
                // File exists, apply to route
                $namespace = str_replace('\\' . $controller . '\\' . $controller, '\\' . $controller, substr($namespace, 0, strripos($namespace, '\\')) . '\\' . ucfirst($method));

                $method = $this->_discoverMethod($namespace, $method);

                // Add route for current request
                $dest = $namespace . ($is_duplicate && $method && method_exists($namespace, $method) ? '::' . $method : null);
                if (!empty($extra_params)) {
                    $dest .= '/' . implode('/', $extra_params);
                }
                $routes->add($this->_uriString, $dest);
            } else {
                $method = $this->_discoverMethod($namespace, $method);

                // Add route for current request
                $dest = $namespace . (! $is_duplicate && (method_exists($namespace, $method) || strtolower($controller) != strtolower($method)) ? '::' . $method : null);
                if (!empty($extra_params)) {
                    $dest .= '/' . implode('/', $extra_params);
                }
                $routes->add($this->_uriString, $dest);
            }
        }

        // Apply theme route
        $this->_themeRoute($routes);
    }

    /**
     * Recursive function to extract the module route
     * @param null|mixed $routes
     * @param null|mixed $namespace
     */
    private function _directoryRoute($routes = null, $directory = [], $namespace = null)
    {
        foreach ($directory as $key => $val) {
            if (is_array($val)) {
                // Subdirectory found, do more scan
                $this->_directoryRoute($routes, $val, $namespace . str_replace('/', '\\', $key));
            } else {
                $module = explode('/', $this->_uriString);

                // Check if file is a PHP
                if (
                    strpos($namespace, '\Config\\') !== false &&
                    stripos($namespace, '\Modules\\' . $module[0] . '\Config\\') !== false
                ) {
                    // Apply route from module route config
                    $extra_route = lcfirst(ltrim(str_replace('\\', '/', $namespace), '/')) . 'Routes.php';

                    if (file_exists(ROOTPATH . $extra_route)) {
                        // Add route of public module
                        require ROOTPATH . $extra_route;
                    }
                }

                if (
                    'php' == strtolower(pathinfo($val, PATHINFO_EXTENSION)) &&
                    strpos($namespace, '\Controllers\\') !== false
                ) {
                    // Desctructure namespace
                    $destructure = explode('/', str_replace('/controllers/', '/', str_replace('\\', '/', strtolower($namespace . pathinfo($val, PATHINFO_FILENAME)))));

                    $prev = null;
                    $module = null;

                    foreach ($destructure as $_key => $_val) {
                        // Check if previous segment is not matching with current segment
                        if ($prev != $_val) {
                            $module .= ($_key ? '/' : null) . $_val;
                        }

                        $prev = $_val;
                    }

                    // Format namespace to module slug
                    $module = ltrim(preg_replace(['/aksara\/modules\//', '/modules\//'], ['', ''], $module, 1), '/');

                    // Extract method from current slug
                    $method = substr($this->_uriString, strrpos($this->_uriString, '/') + 1);

                    // Check if module is matched with current slug
                    if ($module == $this->_uriString) {
                        // Check if file is exist
                        if (ROOTPATH . lcfirst(trim(str_replace('\\', '/', lcfirst(substr($namespace, 0, strrpos($namespace, '\\')) . '\\' . $val)), '/'))) {
                            $x = substr_count($namespace . $val, '\\');
                            $this->_collection[$x] = $namespace . $val;
                        } else {
                            $x = substr_count($namespace . $val, '\\');
                            $this->_collection[$x] = $namespace . $val;
                        }

                        $this->_found = true;
                    } elseif ($module. '/' . $method == $this->_uriString && ROOTPATH . lcfirst(trim(str_replace('\\', '/', lcfirst(substr($namespace, 0, strrpos($namespace, '\\')) . '\\' . $val)), '/'))) {
                        $x = substr_count($namespace . $val, '\\');
                        $this->_collection[$x] = $namespace . $val;

                        $this->_found = true;
                    } elseif (strpos($this->_uriString, $module . '/') === 0) {
                        $remaining_path = substr($this->_uriString, strlen($module . '/'));
                        $segments = explode('/', $remaining_path);
                        $method_candidate = $segments[0] ?? '';
                        $controller_class = $namespace . pathinfo($val, PATHINFO_FILENAME);
                        if ($method_candidate && class_exists($controller_class)) {
                            $matched_method = $this->_discoverMethod($controller_class, $method_candidate);
                            if (method_exists($controller_class, $matched_method)) {
                                $x = substr_count($namespace . $val, '\\');
                                $this->_collection[$x] = $namespace . $val;
                                $this->_found = true;
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * Dealing with file access of the theme that located
     * outside the public folder
     * @param null|mixed $routes
     */
    private function _themeRoute($routes = null)
    {
        if (
            service('uri')->getTotalSegments() >= 2 &&
            service('uri')->getSegment(2) && is_string(service('uri')->getSegment(2)) &&
            is_dir(ROOTPATH . 'themes/' . service('uri')->getSegment(2)) && 'themes' == service('uri')->getSegment(1)
        ) {
            // Add route to theme asset
            $routes->get($this->_uriString, '\Aksara\Modules\Assets\Controllers\Assets::themes');
        }
    }

    /**
     * Smart discovery to match methods that might be camelCase while the URI uses snake_case or dash-case
     */
    private function _discoverMethod(string $namespace, string $method): string
    {
        if ($method && class_exists($namespace)) {
            foreach (get_class_methods($namespace) as $class_method) {
                if (strtolower(str_replace(['_', '-'], '', $method)) == strtolower($class_method)) {
                    return $class_method;
                }
            }
        }

        return $method;
    }
}
