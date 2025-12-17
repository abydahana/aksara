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
use CodeIgniter\Router\RouteCollection;

/**
 * Router class handles automatic route discovery for modules and themes in Aksara CMS.
 * It scans module directories to find controllers and automatically registers routes
 * based on the current URI structure.
 */
class Router
{
    /**
     * The current HTTP request instance.
     */
    private \CodeIgniter\HTTP\IncomingRequest $_request;

    /**
     * The current URI string without leading/trailing slashes.
     */
    private string $_uri_string;

    /**
     * Flag indicating whether a matching module controller was found.
     */
    private bool $_found = false;

    /**
     * Collection of discovered controller namespaces keyed by their depth
     * (number of backslashes). Used to determine priority when multiple
     * controllers match the URI.
     *
     * @var array<int, string>
     */
    private array $_collection = [];

    /**
     * The route collection instance to populate with discovered routes.
     * May be null if no route collection was provided.
     */
    private ?RouteCollection $_routes = null;

    /**
     * Constructor. Initializes the router and auto-discovers module routes.
     *
     * @param RouteCollection|null $routes The route collection to populate with discovered routes
     */
    public function __construct(?RouteCollection $routes = null)
    {
        $this->_request = \Config\Services::request();
        $this->_uri_string = trim(uri_string(), '/');
        $this->_routes = $routes;

        if ($this->_uri_string && method_exists($this->_request, 'setPath')) {
            $this->_request->setPath($this->_uri_string);
        }

        $uriSegments = array_reverse(explode('/', $this->_uri_string));
        $hasDuplicateSegment = isset($uriSegments[0], $uriSegments[1]) && $uriSegments[0] === $uriSegments[1];

        helper('filesystem');

        $this->_scanDirectory(directory_map(ROOTPATH . 'modules'), '\Modules\\');

        if (! $this->_found) {
            $this->_scanDirectory(directory_map(ROOTPATH . 'aksara/Modules'), '\Aksara\Modules\\');
        }

        if ($this->_collection && $this->_routes) {
            $this->_registerDiscoveredRoute($hasDuplicateSegment);
        }

        $this->_registerThemeRoute();
    }

    /**
     * Recursively scans directories to discover module controllers.
     *
     * @param array<string|array> $directory Directory structure from directory_map()
     * @param string|null $namespace Current namespace prefix
     */
    private function _scanDirectory(array $directory, ?string $namespace = null): void
    {
        foreach ($directory as $key => $item) {
            if (is_array($item)) {
                // Subdirectory found, recurse deeper
                $this->_scanDirectory($item, $namespace . str_replace('/', '\\', $key));
            } else {
                $this->_processFile($item, $namespace);
            }
        }
    }

    /**
     * Processes a single file to determine if it's a routeable controller or config file.
     *
     * @param string $filename The filename to process
     * @param string|null $namespace Current namespace prefix
     */
    private function _processFile(string $filename, ?string $namespace): void
    {
        $uriSegments = explode('/', $this->_uri_string);
        $firstSegment = $uriSegments[0] ?? '';

        // Check if file is a route configuration file
        if (
            strpos($namespace, '\Config\\') !== false &&
            stripos($namespace, '\Modules\\' . $firstSegment . '\Config\\') !== false
        ) {
            $this->_loadRouteConfig($namespace);
        }

        // Check if file is a PHP controller file
        if (
            strtolower(pathinfo($filename, PATHINFO_EXTENSION)) === 'php' &&
            strpos($namespace, '\Controllers\\') !== false
        ) {
            $this->_evaluateController($filename, $namespace);
        }
    }

    /**
     * Loads route configuration file if it exists.
     *
     * @param string $namespace The namespace containing the config
     */
    private function _loadRouteConfig(string $namespace): void
    {
        $routeConfigPath = lcfirst(ltrim(str_replace('\\', '/', $namespace), '/')) . 'Routes.php';

        if (file_exists(ROOTPATH . $routeConfigPath)) {
            $routes = $this->_routes; // Define $routes for the included file
            require ROOTPATH . $routeConfigPath;
        }
    }

    /**
     * Evaluates if a controller file matches the current URI.
     *
     * @param string $filename The controller filename
     * @param string $namespace The controller namespace
     */
    private function _evaluateController(string $filename, string $namespace): void
    {
        // Extract controller name from filename (without extension)
        $controllerName = pathinfo($filename, PATHINFO_FILENAME);

        // Convert namespace + controller name to lowercase path
        $namespacePath = strtolower($namespace . $controllerName);

        // Replace backslashes with forward slashes
        $normalizedPath = str_replace('\\', '/', $namespacePath);

        // Remove '/controllers/' segment to simplify path
        $simplifiedPath = str_replace('/controllers/', '/', $normalizedPath);

        // Split into segments
        $pathSegments = explode('/', $simplifiedPath);

        $previousSegment = null;
        $moduleSlug = null;

        foreach ($pathSegments as $index => $segment) {
            // Skip duplicate consecutive segments
            if ($previousSegment !== $segment) {
                $moduleSlug .= ($index > 0 ? '/' : '') . $segment;
            }
            $previousSegment = $segment;
        }

        // Remove Aksara/Modules or Modules prefix
        $moduleSlug = $this->_normalizeModuleSlug($moduleSlug);

        // Extract method from current URI
        $methodName = $this->_extractMethodFromUri();

        $controllerPath = $this->_buildControllerFilePath($namespace, $filename);

        // Check if module slug matches current URI
        if ($moduleSlug === $this->_uri_string) {
            $this->_addToCollection($namespace, $filename);
        } elseif (
            $moduleSlug . '/' . $methodName === $this->_uri_string &&
            file_exists($controllerPath)
        ) {
            $this->_addToCollection($namespace, $filename);
        }
    }

    /**
     * Normalizes module slug by removing Aksara/Modules or Modules prefix.
     *
     * @param string $slug The raw module slug
     * @return string The normalized module slug
     */
    private function _normalizeModuleSlug(string $slug): string
    {
        return ltrim(preg_replace(['/aksara\/modules\//', '/modules\//'], ['', ''], $slug, 1), '/');
    }

    /**
     * Extracts method name from current URI.
     *
     * @return string The method name or empty string
     */
    private function _extractMethodFromUri(): string
    {
        $lastSlashPos = strrpos($this->_uri_string, '/');
        return false !== $lastSlashPos ? substr($this->_uri_string, $lastSlashPos + 1) : '';
    }

    /**
     * Builds the full filesystem path for a controller.
     *
     * @param string $namespace The controller namespace
     * @param string $filename The controller filename
     * @return string The full filesystem path
     */
    private function _buildControllerFilePath(string $namespace, string $filename): string
    {
        // Get parent directory (remove last segment after last backslash)
        $parentDirEnd = strrpos($namespace, '\\');
        $parentNamespace = false !== $parentDirEnd ? substr($namespace, 0, $parentDirEnd) : $namespace;

        // Build controller path
        $controllerRelativePath = lcfirst($parentNamespace . '\\' . $filename);

        // Convert to filesystem path
        $filesystemPath = str_replace('\\', '/', $controllerRelativePath);

        return ROOTPATH . trim($filesystemPath, '/');
    }

    /**
     * Adds a controller to the collection.
     *
     * @param string $namespace The controller namespace
     * @param string $filename The controller filename
     */
    private function _addToCollection(string $namespace, string $filename): void
    {
        $namespaceDepth = substr_count($namespace . $filename, '\\');
        $this->_collection[$namespaceDepth] = $namespace . $filename;
        $this->_found = true;
    }

    /**
     * Registers the discovered route with the highest priority.
     *
     * @param bool $hasDuplicateSegment Whether the URI has duplicate segments
     */
    private function _registerDiscoveredRoute(bool $hasDuplicateSegment): void
    {
        if (! $this->_routes) {
            return;
        }

        $highestDepth = max(array_keys($this->_collection));
        $fullNamespace = $this->_collection[$highestDepth];
        $namespaceWithoutExtension = substr($fullNamespace, 0, strrpos($fullNamespace, '.'));

        $lastBackslashPos = strrpos($namespaceWithoutExtension, '\\');
        $controllerName = substr($namespaceWithoutExtension, $lastBackslashPos + 1);

        $methodName = $this->_extractMethodFromUri();

        // Simplify the complex path generation
        $priorityFilePath = $this->_generateControllerPath($namespaceWithoutExtension, $controllerName, $methodName);
        $alternativeFilePath = $this->_generateAlternativePath($namespaceWithoutExtension, $controllerName, $methodName);

        if (file_exists(ROOTPATH . $priorityFilePath)) {
            $controllerNamespace = $this->_buildControllerNamespace($namespaceWithoutExtension, $controllerName, $methodName, false);
            $routeTarget = $this->_buildRouteTarget($controllerNamespace, $methodName, $hasDuplicateSegment);
            $this->_routes->add($this->_uri_string, $routeTarget);
        } elseif (file_exists(ROOTPATH . $alternativeFilePath)) {
            $controllerNamespace = $this->_buildControllerNamespace($namespaceWithoutExtension, $controllerName, $methodName, true);
            $routeTarget = $this->_buildRouteTarget($controllerNamespace, $methodName, $hasDuplicateSegment);
            $this->_routes->add($this->_uri_string, $routeTarget);
        } else {
            $routeTarget = $this->_buildRouteTarget($namespaceWithoutExtension, $methodName, $hasDuplicateSegment);
            $this->_routes->add($this->_uri_string, $routeTarget);
        }
    }

    /**
     * Generates the primary controller file path.
     *
     * @param string $namespace The controller namespace
     * @param string $controllerName The controller name
     * @param string $methodName The method name
     * @return string The generated file path
     */
    private function _generateControllerPath(string $namespace, string $controllerName, string $methodName): string
    {
        if (empty($methodName)) {
            return '';
        }

        // Remove duplicate controller name if present
        $normalizedNamespace = str_replace(
            '\\' . $controllerName . '\\' . $controllerName,
            '\\' . $controllerName,
            $namespace
        );

        // Build file path: namespace + method
        $filePath = $normalizedNamespace . '\\' . ucfirst($methodName) . '.php';

        // Convert to filesystem path
        return lcfirst(ltrim(str_replace('\\', '/', $filePath), '\\'));
    }

    /**
     * Generates the alternative controller file path.
     *
     * @param string $namespace The controller namespace
     * @param string $controllerName The controller name
     * @param string $methodName The method name
     * @return string The generated alternative file path
     */
    private function _generateAlternativePath(string $namespace, string $controllerName, string $methodName): string
    {
        if (empty($methodName)) {
            return '';
        }

        // Get parent directory (remove last segment)
        $lastBackslashPos = strripos($namespace, '\\');
        if (false === $lastBackslashPos) {
            return '';
        }

        $parentNamespace = substr($namespace, 0, $lastBackslashPos);

        // Remove duplicate controller name if present
        $normalizedNamespace = str_replace(
            '\\' . $controllerName . '\\' . $controllerName,
            '\\' . $controllerName,
            $parentNamespace
        );

        // Build file path: parent namespace + method
        $filePath = $normalizedNamespace . '\\' . ucfirst($methodName) . '.php';

        // Convert to filesystem path
        return lcfirst(ltrim(str_replace('\\', '/', $filePath), '\\'));
    }

    /**
     * Builds the controller namespace for routing.
     *
     * @param string $baseNamespace The base namespace
     * @param string $controllerName The controller name
     * @param string $methodName The method name
     * @param bool $useParentNamespace Whether to use parent namespace
     * @return string The complete controller namespace
     */
    private function _buildControllerNamespace(string $baseNamespace, string $controllerName, string $methodName, bool $useParentNamespace = false): string
    {
        if (empty($methodName)) {
            return $baseNamespace;
        }

        if ($useParentNamespace) {
            $baseNamespace = substr($baseNamespace, 0, strripos($baseNamespace, '\\'));
        }

        return str_replace(
            '\\' . $controllerName . '\\' . $controllerName,
            '\\' . $controllerName,
            $baseNamespace . '\\' . ucfirst($methodName)
        );
    }

    /**
     * Builds the route target string.
     *
     * @param string $controllerNamespace The controller namespace
     * @param string $methodName The method name
     * @param bool $hasDuplicateSegment Whether URI has duplicate segments
     * @return string The route target string
     */
    private function _buildRouteTarget(string $controllerNamespace, string $methodName, bool $hasDuplicateSegment): string
    {
        if (empty($methodName)) {
            return $controllerNamespace;
        }

        $controllerName = substr($controllerNamespace, strrpos($controllerNamespace, '\\') + 1);

        // Determine if we should append method name
        $shouldAppendMethod = $hasDuplicateSegment
            ? method_exists($controllerNamespace, $methodName)
            : (method_exists($controllerNamespace, $methodName) || strtolower($controllerName) !== strtolower($methodName));

        return $shouldAppendMethod ? $controllerNamespace . '::' . $methodName : $controllerNamespace;
    }

    /**
     * Handles theme routes for assets located outside public folder.
     */
    private function _registerThemeRoute(): void
    {
        if (! $this->_routes) {
            return;
        }

        $uri = Services::uri();

        if (
            $uri->getTotalSegments() >= 2 &&
            $uri->getSegment(2) &&
            is_string($uri->getSegment(2)) &&
            is_dir(ROOTPATH . 'themes/' . $uri->getSegment(2)) &&
            $uri->getSegment(1) === 'themes'
        ) {
            $this->_routes->get($this->_uri_string, '\Aksara\Modules\Assets\Controllers\Assets::themes');
        }
    }
}
