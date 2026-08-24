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

use Aksara\Laboratory\Renderer\Parser;

if (! function_exists('generate_token')) {
    /**
     * Generate security token to validate the query string values
     */
    function generate_token(?string $path = null, array $queryParams = []): string|RuntimeException
    {
        // Validate encryption key
        if (! defined('ENCRYPTION_KEY') || empty(ENCRYPTION_KEY)) {
            throw new RuntimeException('ENCRYPTION_KEY must be defined for token generation');
        }

        // Get ignored query string from userdata
        $userIgnored = get_userdata('__ignored_query_string');

        // Default ignored params
        $defaultIgnored = ['aksara', 'q', 'page', 'limit', 'order', 'column', 'sort'];

        // Merge: split user ignored (if exists) with defaults
        $ignoredQueryString = array_merge(
            $userIgnored ? array_map('trim', explode(',', $userIgnored)) : [],
            $defaultIgnored
        );

        // Trim whitespace and filter empty values
        $ignoredQueryString = array_filter(array_map('trim', $ignoredQueryString));

        // Remove duplicates
        $ignoredQueryString = array_unique($ignoredQueryString);

        // Exclude ignored params from query params
        $queryParams = array_diff_key($queryParams, array_flip($ignoredQueryString));

        // No query params, empty return
        if (! $queryParams) {
            return '';
        }

        // Normalize query param order
        ksort($queryParams);

        // Normalize data to query string format
        $queryString = '';

        if (! empty($queryParams)) {
            $queryString = http_build_query(array_filter($queryParams, function ($value) {
                return null !== $value && '' !== $value;
            }));
        }

        // Resolve path using realpath logic
        $parts = explode('/', $path ?? '');
        $resolved = [];

        foreach ($parts as $part) {
            if ('..' === $part && count($resolved) > 0) {
                array_pop($resolved);
            } elseif ('.' !== $part && '' !== $part && '..' !== $part) {
                $resolved[] = $part;
            }
        }

        // Normalized path
        $normalizedPath = implode('/', $resolved);

        // Get session identifier
        $sessionId = get_userdata('session_generated') ?? '';

        // TEMPORARY: Timestamp validation disabled
        // Previous implementation: $timestamp = floor(time() / 3600);
        // Issue: Tokens created at minute 59 expired at minute 00 (next hour)
        $timestamp = 0; // Placeholder for future timestamp implementation

        // Create signature payload
        $payload = [
            'path' => $normalizedPath,
            'query' => $queryString,
            'timestamp' => $timestamp,    // Currently disabled
            'session' => $sessionId,
        ];

        // Generate signature
        $signature = implode('|', array_values($payload));

        // Create HMAC-SHA256 hash
        $hmac = hash_hmac('sha256', $signature, ENCRYPTION_KEY);

        // Return last 12 characters as token
        return substr($hmac, -12);
    }
}

if (! function_exists('generate_csrf_token')) {
    /**
     * Generate the CSRF token used by forms and AJAX responses.
     */
    function generate_csrf_token(?string $path = null): string
    {
        if (! defined('ENCRYPTION_KEY') || empty(ENCRYPTION_KEY)) {
            throw new RuntimeException('ENCRYPTION_KEY must be defined for CSRF token generation');
        }

        $uri = null !== $path ? trim($path, '/') : uri_string();

        return hash_hmac('sha256', $uri . get_userdata('session_generated') . get_userdata('token_timestamp'), ENCRYPTION_KEY);
    }
}

if (! function_exists('get_theme')) {
    /**
     * Get the active theme without using debug_backtrace
     */
    function get_theme(): string
    {
        if (! empty(\Aksara\Laboratory\Template::$activeTheme)) {
            return \Aksara\Laboratory\Template::$activeTheme;
        }

        $theme = '';
        $backtrace = debug_backtrace();

        foreach ($backtrace as $key => $val) {
            // Find active theme
            if (isset($val['file']) && ROOTPATH .  'aksara' . DIRECTORY_SEPARATOR . 'Laboratory' . DIRECTORY_SEPARATOR . 'Core.php' == $val['file']) {
                if (isset($val['object']->template->theme)) {
                    // Active theme found
                    $theme = $val['object']->template->theme;
                } elseif (isset($val['object']->theme)) {
                    // Active theme found
                    $theme = $val['object']->theme;
                }
            }
        }

        return $theme ?: (get_setting('frontend_theme') ?: 'default');
    }
}

if (! function_exists('aksara_header')) {
    /**
     * Render the core Aksara header tags.
     *
     * This function generates a security CSRF token meta tag, loads the primary
     * theme-specific stylesheet, inclusion of Material Design Icons, and a
     * jQuery deferred execution script.
     */
    function aksara_header(): string
    {
        // Identify the active theme
        $theme = get_theme();

        // Generate security token meta tag using the same CSRF token helper as Core
        $output = '<meta name="_token" content="' . generate_csrf_token() . '" />';

        // Load theme-specific minified styles
        $output .= '<link rel="stylesheet" type="text/css" href="' . base_url('assets/css/' . $theme . '/styles.min.css') . '" />';

        // Deferred jQuery execution snippet to handle scripts loaded before jQuery is ready
        $output .= '<script type="text/javascript">(function(w,d,u){w.readyQ=[];w.bindReadyQ=[];function p(x,y){if (x=="ready"){w.bindReadyQ.push(y)}else{w.readyQ.push(x)}};var a={ready:p,bind:p};w.$=w.jQuery=function(f){if (f===d||f===u){return a}else{p(f)}}})(window,document)</script>';

        if (get_setting('google_analytics_key')) {
            $analyticsKey = htmlspecialchars(get_setting('google_analytics_key'));

            $output .= '<script async src="https://www.googletagmanager.com/gtag/js?id=' . $analyticsKey . '"></script><script>window.dataLayer = window.dataLayer || [];function gtag(){dataLayer.push(arguments);}gtag(\'js\', new Date());gtag(\'config\', \'' . $analyticsKey . '\');</script>';
        }

        return $output;
    }
}

if (! function_exists('aksara_footer')) {
    /**
     * Render the core Aksara footer tags.
     *
     * This function includes flash messages (toasts), the main theme-specific
     * JavaScript file, and the completion script for the jQuery deferred execution queue.
     */
    function aksara_footer(): string
    {
        // Identify the active theme
        $theme = get_theme();

        // Include flash messages (toast notifications) if any
        $output = (string) show_flashdata();

        // Load theme-specific minified scripts
        $output .= '<script type="text/javascript" src="' . base_url('assets/js/' . $theme . '/scripts.min.js') . '"></script>';

        // Execute the deferred jQuery queue
        $output .= '<script type="text/javascript">(function($,d){$.each(readyQ,function(i,f){$(f)});$.each(bindReadyQ,function(i,f){$(d).bind("ready",f)})})(jQuery,document)</script>';

        return $output;
    }
}

if (! function_exists('throw_exception')) {
    /**
     * Throw an exception response or redirect.
     *
     * This function handles both AJAX and standard HTTP requests. For AJAX, it returns
     * a JSON response with appropriate status codes. For standard requests, it sets
     * flashdata and performs a redirect.
     */
    function throw_exception(int $code = 500, string|array|null $data = [], ?string $target = null, mixed $redirect = false)
    {
        if (is_cli()) {
            if (is_array($data)) {
                $data = json_encode($data, JSON_PRETTY_PRINT);
            }

            fwrite(STDERR, "[Error $code] " . $data . PHP_EOL);
            exit(1);
        }

        // Logic for Non-AJAX Request: Set Flashdata and Redirect
        if (! service('request')->isAJAX()) {
            if (! is_array($data)) {
                if (in_array($code, [200, 301])) {
                    service('session')->setFlashdata('success', $data);
                } elseif (in_array($code, [403, 404])) {
                    service('session')->setFlashdata('warning', $data);
                } else {
                    service('session')->setFlashdata('error', $data);
                }
            }

            $target = $target ?: base_url();

            // Redirect to target
            header('Location: ' . $target);
            exit;
        }

        // Logic for AJAX Request: Return JSON Response
        $exception = [];
        $responseData = null;

        if (is_array($data)) {
            if (array_key_exists('data', $data)) {
                $responseData = $data['data'];
                unset($data['data']);
            }
            if (isset($data['message'])) {
                $exception = $data['message'];
            } else {
                foreach ($data as $key => $val) {
                    // Remove bracket notation from validation keys
                    $key = str_replace('[]', '', $key);
                    $exception[$key] = $val;
                }
            }
        } else {
            $exception = $data;
        }

        // Determine redirect behavior for AJAX
        if (! $redirect) {
            $redirect = (service('request')->getPost('__modal_index') <= 1 && 301 === $code ? 'soft' : false);
        }

        $response = [
            'code' => $code,
            'message' => $exception,
            'target' => $target ?: '',
            'redirect' => $redirect
        ];

        if (null !== $responseData) {
            unset($response['target'], $response['redirect']);

            $response['data'] = $responseData;
        }

        $output = json_encode($response);

        // Set header response code
        http_response_code($code);

        header('Content-Type: application/json; charset=utf-8');

        echo $output;

        exit;
    }
}

if (! function_exists('show_flashdata')) {
    /**
     * Render session flashdata as a Bootstrap toast notification.
     *
     * This function checks for the existence of success, warning, or error flashdata
     * and generates the corresponding HTML markup using Bootstrap classes and
     * Material Design Icons.
     */
    function show_flashdata(): string
    {
        $output = '';

        // Check if there is any flashdata available in the session
        if (service('session')->getFlashdata()) {
            // Determine the alert context (color) and icon based on the message type
            $type = (service('session')->getFlashdata('success') ? 'success' : (service('session')->getFlashdata('warning') ? 'warning' : 'danger'));
            $icon = (service('session')->getFlashdata('success') ? 'check-circle-outline' : (service('session')->getFlashdata('warning') ? 'alert-octagram-outline' : 'emoticon-sad-outline'));
            $message = (service('session')->getFlashdata('success') ?: (service('session')->getFlashdata('warning') ?: service('session')->getFlashdata('error')));

            // Clear flashdata
            service('session')->remove(['success', 'warning', 'error']);

            return '
                <div class="toast-container position-fixed bottom-0 start-50 translate-middle-x p-3">
                    <div class="toast align-items-center text-bg-' . $type . ' fade show" role="alert" aria-live="assertive" aria-atomic="true">
                        <div class="d-flex">
                            <div class="toast-body">
                                <div class="row align-items-center">
                                    <div class="col-2">
                                        <i class="mdi mdi-' . $icon . ' mdi-2x"></i>
                                    </div>
                                    <div class="col-10 text-break">
                                        ' . $message . '
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="' . phrase('Close') . '"></button>
                        </div>
                    </div>
                </div>
            ';
        }

        return $output;
    }
}

if (! function_exists('fetch_metadata')) {
    /**
     * Fetch metadata from a specific URL path via internal API request.
     *
     * This function performs a GET request to the local application path with
     * security headers to retrieve page metadata like title, description, and icon.
     */
    function fetch_metadata(string $path): object
    {
        static $cache = [];

        // Clean & sanitize path to prevent SSRF and path traversal
        $path = trim($path, '/');

        // Remove scheme or domain if prepended
        if (preg_match('#^https?://#i', $path)) {
            $parsed = parse_url($path);
            $path = ltrim($parsed['path'] ?? '', '/');
        }

        // Return cached result if already fetched during this request lifecycle
        if (isset($cache[$path])) {
            return $cache[$path];
        }

        $fallback = new \stdClass();

        try {
            // Build safe local URL
            $targetUrl = base_url($path);

            // Initialize the CURL request service with security timeouts
            $client = service('curlrequest', [
                'timeout' => 5,
                'connect_timeout' => 3,
                'http_errors' => false,
                'allow_redirects' => [
                    'max' => 2,
                    'strict' => true,
                    'protocols' => ['http', 'https']
                ]
            ]);

            // Perform internal API handshake to fetch metadata
            $response = $client->request('GET', $targetUrl, [
                'headers' => [
                    'X-API-KEY' => ENCRYPTION_KEY,
                    'X-ACCESS-TOKEN' => session_id(),
                    'User-Agent' => 'Aksara-Internal-Fetcher/1.0'
                ],
                'query' => [
                    '__fetch_metadata' => true
                ]
            ]);

            // Ensure response is HTTP 200 OK before decoding
            if (200 === $response->getStatusCode()) {
                $decoded = json_decode((string) $response->getBody());

                if (is_object($decoded)) {
                    $cache[$path] = $decoded;

                    return $decoded;
                }
            }
        } catch (\Throwable $e) {
            // Log the exception message for debugging
            log_message('error', 'fetch_metadata error [' . $path . ']: ' . $e->getMessage());
        }

        $cache[$path] = $fallback;

        return $fallback;
    }
}

if (! function_exists('array_sort')) {
    /**
     * Comparison function builder for array sorting.
     *
     * This helper creates a closure to be used with usort() for multi-column
     * sorting on both arrays of objects and associative arrays.
     */
    function make_cmp(array $data = []): \Closure
    {
        return function (array|object $a, array|object $b) use (&$data): int {
            foreach ($data as $column => $sort) {
                if (! $sort) {
                    $sort = 'asc';
                }

                // Get values based on whether the element is an object or an array
                $valA = (is_object($a) ? $a->$column : $a[$column]);
                $valB = (is_object($b) ? $b->$column : $b[$column]);
                $diff = strcmp((string) $valA, (string) $valB);

                if (0 !== $diff) {
                    return (strtolower($sort) === 'asc') ? $diff : ($diff * -1);
                }
            }

            return 0;
        };
    }

    /**
     * Sort an array of objects or arrays by one or more columns.
     *
     * Supports multi-column sorting by passing an associative array to $orderBy.
     */
    function array_sort(?array $data = [], array|string $orderBy = [], string $sort = 'asc'): array
    {
        if (! is_array($data)) {
            return [];
        }

        if (! is_array($orderBy) && is_string($orderBy)) {
            $orderBy = [$orderBy => $sort];
        }

        usort($data, make_cmp($orderBy));

        return $data;
    }
}

if (! function_exists('reset_sort')) {
    /**
     * Recursively reset numeric keys in a multidimensional array.
     *
     * This function uses array_values() to re-index numeric keys while
     * preserving associative (string) keys.
     */
    function reset_sort(array $resource = []): array
    {
        $isNumeric = false;

        foreach ($resource as $key => $val) {
            // Recursively process nested arrays
            if (is_array($val)) {
                $resource[$key] = reset_sort($val);
            }

            // Detect if the current level has at least one numeric key
            if (is_numeric($key)) {
                $isNumeric = true;
            }
        }

        // Re-index only if numeric keys are found, otherwise preserve associative keys
        return $isNumeric ? array_values($resource) : $resource;
    }
}

if (! function_exists('form_input')) {
    /**
     * Render a form input component using a TWIG template.
     *
     * This function initializes the Twig parser based on the active theme
     * and processes the 'core/form_input.twig' template with the provided parameters.
     */
    function form_input(array|object $params = []): string
    {
        // Initialize the Twig parser with theme context
        $parser = new Parser();

        // Parse and return the form input component
        return $parser->parse('core/form_input.twig', ['params' => $params]);
    }
}

if (! function_exists('form_read')) {
    /**
     * Render a read-only form component using a TWIG template.
     *
     * Similar to form_input, but specifically for rendering data in a
     * non-editable (read-only) format using the 'core/form_read.twig' template.
     */
    function form_read(array|object $params = []): string
    {
        // Initialize the Twig parser with theme context
        $parser = new Parser();

        // Parse and return the form read component
        return $parser->parse('core/form_read.twig', ['params' => $params]);
    }
}

if (! function_exists('pagination')) {
    /**
     * Render the pagination navigation view.
     *
     * This function generates the HTML for pagination by parsing a Twig template.
     * It will return false if the total number of rows is less than or equal to
     * the items per page, unless a specific limit is requested via GET.
     */
    function pagination(object $params)
    {
        // Check if pagination is necessary based on total rows and per page settings
        if (! $params || ($params->total <= $params->limit && ! service('request')->getGet('limit'))) {
            return false;
        }

        // Initialize the Twig parser with theme context
        $parser = new Parser();

        // Parse and return the pagination component
        return $parser->parse('core/pagination.twig', $params);
    }
}
