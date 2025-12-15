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

if (! function_exists('generate_token')) {
    /**
     * Generate security token to validate the query string values
     */
    function generate_token(?string $path = null, array $data = []): string
    {
        // Validate encryption key
        if (! defined('ENCRYPTION_KEY') || empty(ENCRYPTION_KEY)) {
            throw new RuntimeException('ENCRYPTION_KEY must be defined for token generation');
        }

        // Remove existing token from data to prevent token chaining
        unset($data['aksara']);

        // Normalize data to query string format
        $queryString = '';
        if (! empty($data)) {
            $queryString = http_build_query(array_filter($data, function ($value) {
                return null !== $value && '' !== $value;
            }));
        }

        // Normalize path
        $normalizedPath = '';
        if (null !== $path) {
            $normalizedPath = rtrim(urldecode($path), '/');
        }

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
     * Get active theme
     */
    function get_theme()
    {
        $theme = null;
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
     * Include additional CSS and JavaScript into head tag
     */
    function aksara_header()
    {
        $theme = get_theme();
        $stylesheet = null;

        $output = '<meta name="_token" content="' . sha1(current_page() . ENCRYPTION_KEY . get_userdata('session_generated')) . '" />' . "\n";
        $output .= '<link rel="stylesheet" type="text/css" href="' . base_url('assets/css/' . $theme . '/styles.min.css') . '" />' . "\n";
        $output .= '<link rel="stylesheet" type="text/css" href="' . base_url('assets/materialdesignicons/css/materialdesignicons.min.css') . '" />' . "\n";
        $output .= '<script type="text/javascript">(function(w,d,u){w.readyQ=[];w.bindReadyQ=[];function p(x,y){if (x=="ready"){w.bindReadyQ.push(y)}else{w.readyQ.push(x)}};var a={ready:p,bind:p};w.$=w.jQuery=function(f){if (f===d||f===u){return a}else{p(f)}}})(window,document)</script>' . "\n";

        if ($stylesheet) {
            $output .= '<style type="text/css">' . $stylesheet . '</style>';
        }

        return $output;
    }
}

if (! function_exists('aksara_footer')) {
    /**
     * Include additional JavaScript to end of template
     */
    function aksara_footer()
    {
        $theme = get_theme();

        $output = show_flashdata() . "\n";
        $output .= '<script type="text/javascript" src="' . base_url('assets/js/' . $theme . '/scripts.min.js') . '"></script>' . "\n";
        $output .= '<script type="text/javascript">(function($,d){$.each(readyQ,function(i,f){$(f)});$.each(bindReadyQ,function(i,f){$(d).bind("ready",f)})})(jQuery,document)</script>' . "\n";

        return $output;
    }
}

if (! function_exists('throw_exception')) {
    /**
     * Exception function
     *
     * @param   mixed|null $data
     * @param   mixed|null $target
     */
    function throw_exception(int $code = 500, $data = [], $target = null, $redirect = false)
    {
        // Check if data isn't an array
        if ($data && ! is_array($data)) {
            // Set the flashdata
            if (in_array($code, [200, 301])) {
                // Success
                service('session')->setFlashdata('success', $data);
            } elseif (in_array($code, [403, 404])) {
                // Warning
                service('session')->setFlashdata('warning', $data);
            } else {
                // Unexpected error
                service('session')->setFlashdata('error', $data);
            }
        }

        // Check if the request isn't through xhr
        if (! service('request')->isAJAX()) {
            if (! $target) {
                $target = base_url();
            }

            // Redirect to target
            exit(header('Location: ' . $target));
        }

        $exception = [];

        if (is_array($data)) {
            foreach ($data as $key => $val) {
                $key = str_replace('[]', '', $key);
                $exception[$key] = $val;
            }
        } else {
            $exception = $data;
        }

        if (! $redirect) {
            $redirect = (service('request')->getPost('__modal_index') <= 1 ? 'soft' : false);
        }

        $output = json_encode([
            'code' => $code,
            'message' => $exception,
            'target' => ($target ? $target : ''),
            'redirect' => $redirect
        ]);

        // Set header response code
        http_response_code($code);

        header('Content-Type: application/json');

        exit($output);
    }
}

if (! function_exists('show_flashdata')) {
    /**
     * Generate flashdata messages
     */
    function show_flashdata()
    {
        if (service('session')->getFlashdata()) {
            return '
                <div class="toast-container position-fixed bottom-0 end-0 p-3">
                    <div class="toast align-items-center text-bg-' . (service('session')->getFlashdata('success') ? 'success' : (service('session')->getFlashdata('warning') ? 'warning' : 'danger')) . ' fade show" role="alert" aria-live="assertive" aria-atomic="true">
                        <div class="d-flex">
                            <div class="toast-body">
                                <div class="row align-items-center">
                                    <div class="col-2">
                                        <i class="mdi mdi-' .(service('session')->getFlashdata('success') ? 'check-circle-outline' : (service('session')->getFlashdata('warning') ? 'alert-octagram-outline' : 'emoticon-sad-outline')) . ' mdi-2x"></i>
                                    </div>
                                    <div class="col-10 text-break">
                                        ' . (service('session')->getFlashdata('success') ? service('session')->getFlashdata('success') : (service('session')->getFlashdata('warning') ? service('session')->getFlashdata('warning') : service('session')->getFlashdata('error'))) . '
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="' . phrase('Close') . '"></button>
                        </div>
                    </div>
                </div>
            ';
        }

        return false;
    }
}

if (! function_exists('fetch_metadata')) {
    /**
     * Fetching metadata from url path
     */
    function fetch_metadata(string $path)
    {
        try {
            $client = service('curlrequest');

            $response = $client->request('GET', base_url($path), [
                'headers' => [
                    'X-API-KEY' => ENCRYPTION_KEY,
                    'X-ACCESS-TOKEN' => session_id()
                ],
                'query' => [
                    '__fetch_metadata' => true
                ]
            ]);

            return json_decode($response->getBody());
        } catch (Throwable $e) {
            return $e;
        }

        return [];
    }
}

if (! function_exists('array_sort')) {
    /**
     * Sort array
     */
    function make_cmp($data = [])
    {
        return function ($a, $b) use (&$data) {
            foreach ($data as $column => $sort) {
                if (! $sort) {
                    $sort = 'asc';
                }

                $diff = strcmp((is_object($a) ? $a->$column : $a[$column]), (is_object($b) ? $b->$column : $b[$column]));
                if (0 !== $diff) {
                    if ('asc' === strtolower($sort)) {
                        return $diff;
                    }

                    return $diff * -1;
                }
            }
            return 0;
        };
    }

    function array_sort($data = null, $order_by = [], $sort = 'asc')
    {
        if (! is_array($order_by) && is_string($order_by)) {
            $order_by = [$order_by => $sort];
        }

        usort($data, make_cmp($order_by));

        return $data;
    }
}

if (! function_exists('reset_sort')) {
    /**
     * Reset Sort
     */
    function reset_sort($resource = [])
    {
        $is_numeric = false;

        foreach ($resource as $key => $val) {
            if (is_array($val)) {
                $resource[$key] = reset_sort($val);
            }

            if (is_numeric($key)) {
                $is_numeric = true;
            }
        }

        if ($is_numeric) {
            return array_values($resource);
        } else {
            return $resource;
        }

        return array_values($resource);
    }
}

if (! function_exists('form_input')) {
    /**
     * Generate form input
     */
    function form_input($params = [])
    {
        $theme = get_theme();
        $parser = new \Aksara\Laboratory\Renderer\Parser($theme);

        return $parser->parse('core/form_input.twig', ['params' => $params]);
    }
}

if (! function_exists('form_read')) {
    /**
     * Generate form input
     */
    function form_read($params = [])
    {
        $theme = get_theme();
        $parser = new \Aksara\Laboratory\Renderer\Parser($theme);

        return $parser->parse('core/form_read.twig', ['params' => $params]);
    }
}

if (! function_exists('pagination')) {
    /**
     * Generate form input
     */
    function pagination($params = [])
    {
        if (! $params || ($params->total_rows <= $params->per_page && ! service('request')->getGet('limit'))) {
            return false;
        }

        $theme = get_theme();
        $parser = new \Aksara\Laboratory\Renderer\Parser($theme);

        return $parser->parse('core/pagination.twig', $params);
    }
}
