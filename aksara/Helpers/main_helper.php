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

use Aksara\Laboratory\Renderer\Parser;

if (! function_exists('generate_token')) {
    /**
     * Generate security token to validate the query string values
     */
    function generate_token(?string $path = null, array $query_params = []): string|RuntimeException
    {
        // Validate encryption key
        if (! defined('ENCRYPTION_KEY') || empty(ENCRYPTION_KEY)) {
            throw new RuntimeException('ENCRYPTION_KEY must be defined for token generation');
        }

        // Get ignored query string from userdata
        $user_ignored = get_userdata('__ignored_query_string');

        // Default ignored params
        $default_ignored = ['aksara', 'q', 'per_page', 'limit', 'order', 'column', 'sort'];

        // Merge: split user ignored (if exists) with defaults
        $ignored_query_string = array_merge(
            $user_ignored ? array_map('trim', explode(',', $user_ignored)) : [],
            $default_ignored
        );

        // Trim whitespace and filter empty values
        $ignored_query_string = array_filter(array_map('trim', $ignored_query_string));

        // Remove duplicates
        $ignored_query_string = array_unique($ignored_query_string);

        // Exclude ignored params from query params
        $query_params = array_diff_key($query_params, array_flip($ignored_query_string));

        // No query params, empty return
        if (! $query_params) {
            return '';
        }

        // Normalize query param order
        ksort($query_params);

        // Normalize data to query string format
        $queryString = '';

        if (! empty($query_params)) {
            $queryString = http_build_query(array_filter($query_params, function ($value) {
                return null !== $value && '' !== $value;
            }));
        }

        // Resolve path using realpath logic
        $parts = explode('/', $path);
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

if (! function_exists('get_theme')) {
    /**
     * Get the active theme without using debug_backtrace
     */
    function get_theme(): string
    {
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

        return $theme;
    }
}

if (! function_exists('aksara_header')) {
    /**
     * Render the core Aksara header tags.
     *
     * This function generates a security CSRF token meta tag, loads the primary
     * theme-specific stylesheet, inclusion of Material Design Icons, and a
     * jQuery deferred execution script.
     *
     * @return string The rendered HTML tags for the head section.
     */
    function aksara_header(): string
    {
        // Identify the active theme
        $theme = get_theme();

        // Reserved for dynamic inline styles if needed
        $stylesheet = null;

        // Generate security token meta tag
        $output = '<meta name="_token" content="' . sha1(current_page() . ENCRYPTION_KEY . get_userdata('session_generated')) . '" />' . "\n";

        // Load theme-specific minified styles
        $output .= '<link rel="stylesheet" type="text/css" href="' . base_url('assets/css/' . $theme . '/styles.min.css') . '" />' . "\n";

        // Load Material Design Icons
        $output .= '<link rel="stylesheet" type="text/css" href="' . base_url('assets/materialdesignicons/css/materialdesignicons.min.css') . '" />' . "\n";

        // Deferred jQuery execution snippet to handle scripts loaded before jQuery is ready
        $output .= '<script type="text/javascript">(function(w,d,u){w.readyQ=[];w.bindReadyQ=[];function p(x,y){if (x=="ready"){w.bindReadyQ.push(y)}else{w.readyQ.push(x)}};var a={ready:p,bind:p};w.$=w.jQuery=function(f){if (f===d||f===u){return a}else{p(f)}}})(window,document)</script>' . "\n";

        if ($stylesheet) {
            $output .= '<style type="text/css">' . $stylesheet . '</style>';
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
     *
     * @return string The rendered HTML tags for the end of the template.
     */
    function aksara_footer(): string
    {
        // Identify the active theme
        $theme = get_theme();

        // Include flash messages (toast notifications) if any
        $output = (string) show_flashdata() . "\n";

        // Load theme-specific minified scripts
        $output .= '<script type="text/javascript" src="' . base_url('assets/js/' . $theme . '/scripts.min.js') . '"></script>' . "\n";

        // Execute the deferred jQuery queue
        $output .= '<script type="text/javascript">(function($,d){$.each(readyQ,function(i,f){$(f)});$.each(bindReadyQ,function(i,f){$(d).bind("ready",f)})})(jQuery,document)</script>' . "\n";

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
     *
     * @param int $code             HTTP status code (e.g., 200, 301, 400, 403, 404, 500)
     * @param string|array $data    Message string or array of error messages
     * @param string|null $target   The redirect target URL
     * @param mixed $redirect       Redirection mode ('soft', 'full', or boolean)
     */
    function throw_exception(int $code = 500, string|array $data = [], ?string $target = null, mixed $redirect = false)
    {
        $request = service('request');
        $response = service('response');
        $session = service('session');

        // Logic for Non-AJAX Request: Set Flashdata and Redirect
        if (! $request->isAJAX()) {
            if (! is_array($data)) {
                if (in_array($code, [200, 301])) {
                    $session->setFlashdata('success', $data);
                } elseif (in_array($code, [403, 404])) {
                    $session->setFlashdata('warning', $data);
                } else {
                    $session->setFlashdata('error', $data);
                }
            }

            $target = $target ?: base_url();

            // Perform redirect using CI4 response helper
            return $response->redirect($target)->send();
        }

        // Logic for AJAX Request: Return JSON Response
        $exception = [];

        if (is_array($data)) {
            foreach ($data as $key => $val) {
                // Remove bracket notation from validation keys
                $key = str_replace('[]', '', $key);
                $exception[$key] = $val;
            }
        } else {
            $exception = $data;
        }

        // Determine redirect behavior for AJAX
        if (! $redirect) {
            $redirect = ($request->getPost('__modal_index') <= 1 && 301 === $code ? 'soft' : false);
        }

        $output = [
            'code' => $code,
            'message' => $exception,
            'target' => $target ?: '',
            'redirect' => $redirect
        ];

        // Send JSON response using CI4 Response Class
        return $response->setStatusCode($code)->setJSON($output)->send();
    }
}

if (! function_exists('show_flashdata')) {
    /**
     * Render session flashdata as a Bootstrap toast notification.
     *
     * This function checks for the existence of success, warning, or error flashdata
     * and generates the corresponding HTML markup using Bootstrap classes and
     * Material Design Icons.
     *
     * @return string|false Returns the rendered HTML toast container or false if no flashdata exists.
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
                <div class="toast-container position-fixed bottom-0 end-0 p-3">
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
     *
     * @param string $path  The internal URL path to fetch metadata from.
     * @return object|array|\Throwable Returns a decoded JSON object on success, an empty array, or a Throwable exception on failure.
     */
    function fetch_metadata(string $path): object|array
    {
        try {
            // Initialize the CURL request service
            $client = service('curlrequest');

            // Perform internal API handshake to fetch metadata
            $response = $client->request('GET', base_url($path), [
                'headers' => [
                    'X-API-KEY' => ENCRYPTION_KEY,
                    'X-ACCESS-TOKEN' => session_id()
                ],
                'query' => [
                    '__fetch_metadata' => true
                ]
            ]);

            // Return decoded JSON response
            return json_decode($response->getBody());
        } catch (Throwable $e) {
            // Log the exception object for further handling
            log_message('error', $e->getMessage());
        }

        return [];
    }
}

if (! function_exists('array_sort')) {
    /**
     * Comparison function builder for array sorting.
     *
     * This helper creates a closure to be used with usort() for multi-column
     * sorting on both arrays of objects and associative arrays.
     *
     * @param array $data   An associative array of [column => direction].
     * @return \Closure     The comparison function.
     */
    function make_cmp(array $data = []): \Closure
    {
        return function (array|object $a, array|object $b) use (&$data): int {
            foreach ($data as $column => $sort) {
                if (! $sort) {
                    $sort = 'asc';
                }

                // Get values based on whether the element is an object or an array
                $val_a = (is_object($a) ? $a->$column : $a[$column]);
                $val_b = (is_object($b) ? $b->$column : $b[$column]);

                $diff = strcmp((string) $val_a, (string) $val_b);

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
     * Supports multi-column sorting by passing an associative array to $order_by.
     *
     * @param array|null $data      The collection to be sorted.
     * @param array|string $order_by The column name or an array of [column => direction].
     * @param string $sort          Default sort direction if $order_by is a string.
     * @return array                The sorted array.
     */
    function array_sort(?array $data = [], array|string $order_by = [], string $sort = 'asc'): array
    {
        if (! is_array($data)) {
            return [];
        }

        if (! is_array($order_by) && is_string($order_by)) {
            $order_by = [$order_by => $sort];
        }

        usort($data, make_cmp($order_by));

        return $data;
    }
}

if (! function_exists('reset_sort')) {
    /**
     * Recursively reset numeric keys in a multidimensional array.
     *
     * This function uses array_values() to re-index numeric keys while
     * preserving associative (string) keys.
     *
     * @param array $resource   The array to be re-indexed.
     * @return array            The re-indexed array.
     */
    function reset_sort(array $resource = []): array
    {
        $is_numeric = false;

        foreach ($resource as $key => $val) {
            // Recursively process nested arrays
            if (is_array($val)) {
                $resource[$key] = reset_sort($val);
            }

            // Detect if the current level has at least one numeric key
            if (is_numeric($key)) {
                $is_numeric = true;
            }
        }

        // Re-index only if numeric keys are found, otherwise preserve associative keys
        return $is_numeric ? array_values($resource) : $resource;
    }
}

if (! function_exists('form_input')) {
    /**
     * Render a form input component using a TWIG template.
     *
     * This function initializes the Twig parser based on the active theme
     * and processes the 'core/form_input.twig' template with the provided parameters.
     *
     * @param array|object $params  The configuration and data for the form input.
     * @return string               The rendered HTML content of the form input.
     */
    function form_input(array|object $params = []): string
    {
        // Identify the active theme to locate the correct template
        $theme = get_theme();

        // Initialize the Twig parser with theme context
        $parser = new \Aksara\Laboratory\Renderer\Parser($theme);

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
     *
     * @param array|object $params  The data to be displayed in the read view.
     * @return string               The rendered HTML content of the read-only form.
     */
    function form_read(array|object $params = []): string
    {
        // Identify the active theme
        $theme = get_theme();

        // Initialize the Twig parser with theme context
        $parser = new \Aksara\Laboratory\Renderer\Parser($theme);

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
     *
     * @param object $params    An object containing pagination data (total_rows, per_page, offset, etc.).
     * @return string|false     The rendered pagination HTML or false if pagination is not required.
     */
    function pagination(object $params)
    {
        // Check if pagination is necessary based on total rows and per page settings
        if (! $params || ($params->total_rows <= $params->per_page && ! service('request')->getGet('limit'))) {
            return false;
        }

        // Identify the active theme to locate the correct template
        $theme = get_theme();

        // Initialize the Twig parser with theme context
        $parser = new Parser($theme);

        // Parse and return the pagination component
        return $parser->parse('core/pagination.twig', $params);
    }
}
