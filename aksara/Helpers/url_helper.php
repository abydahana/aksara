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

use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\SiteURI;
use CodeIgniter\HTTP\URI;
use CodeIgniter\Router\Exceptions\RouterException;
use Config\App;

if (! function_exists('base_url')) {
    /**
     * Base URL
     *
     * Create a local URL based on your basepath.
     * Segments can be passed in as a string or an array, same as site_url
     * or a URL to a file can be passed in, e.g. to an image file.
     *
     * @param   string $path
     * @param   array  $params
     *
     * @return  string
     */
    function base_url($path = '', $params = [])
    {
        $request = service('request');

        if (is_array($path)) {
            $path = implode('/', array_values($path));
        }

        if (is_object($params)) {
            $params = (array) $params;
        }

        if (! is_array($params)) {
            $params = [];
        }

        if ($params || ('preview-theme' == $request->getGet('aksara_mode') && sha1($request->getGet('aksara_theme') . ENCRYPTION_KEY . get_userdata('session_generated')) == $request->getGet('integrity_check') && is_dir(ROOTPATH . 'themes/' . $request->getGet('aksara_theme')))) {
            $params = array_merge($request->getGet(), $params);
        }

        if (! empty($params)) {
            // Unset old token
            unset($params['aksara']);

            $query_params = [];

            foreach ($params as $key => $val) {
                if (! $val || in_array($key, $params) && ! $params[$key]) {
                    continue;
                }

                $query_params[$key] = $val;
            }

            // Generate token
            $token = generate_token($path, $query_params);

            if ($query_params) {
                $query_params = array_merge(['aksara' => $token], $query_params);
            }

            $uri = $path . ($query_params ? '?' . http_build_query($query_params) : null);
        } else {
            $uri = $path;
        }

        $currentURI = service('request')->getUri();

        assert($currentURI instanceof SiteURI);

        if ((service('request')->getServer('HTTP_MOD_REWRITE') && strtolower(service('request')->getServer('HTTP_MOD_REWRITE')) == 'on') || (function_exists('apache_get_modules') && in_array('mod_rewrite', apache_get_modules())) || ($uri && file_exists(FCPATH . $uri))) {
            return $currentURI->baseUrl(($uri ? rtrim($uri, '/') : ''));
        }

        return $currentURI->baseUrl((config('App')->indexPage ? config('App')->indexPage . '/' : null) . ($uri ? rtrim($uri, '/') : ''));
    }
}

if (! function_exists('current_page')) {
    /**
     * Current Page
     *
     * Get the current page URL and add the add extra parameter
     * on it.
     *
     * @param   string $method
     * @param   array  $params
     * @param   string $unset
     *
     * @return  string
     */
    function current_page($method = null, $params = [], $unset = null)
    {
        if (is_object($params)) {
            $params = (array) $params;
        }
        if (! is_array($params)) {
            $params = [];
        }

        if ($unset && isset($params[$unset])) {
            unset($params[$unset]);
        }

        $params = array_merge(service('request')->getGet(), $params);

        if (! empty($params)) {
            // Unset old token
            unset($params['aksara']);

            $query_params = [];

            foreach ($params as $key => $val) {
                if (! $val || in_array($key, $params) && ! $params[$key]) {
                    continue;
                }

                $query_params[$key] = $val;
            }

            // Generate token
            $token = generate_token(uri_string() . ($method ? '/' . $method : null), $query_params);

            if ($query_params) {
                $query_params = array_merge(['aksara' => $token], $query_params);
            }

            return base_url(uri_string()) . ($method ? '/' . $method : null) . ($query_params ? '?' . http_build_query($query_params) : null);
        } else {
            return base_url(uri_string()) . ($method ? '/' . $method : null);
        }
    }
}

if (! function_exists('go_to')) {
    /**
     * Go To
     *
     * Generate the next page from the current page and add
     * extra parameter on it.
     *
     * @param   string $method
     * @param   array  $params
     *
     * @return  string
     */
    function go_to($method = null, $params = [])
    {
        if (is_array($method)) {
            $method = implode('/', $method);
        }

        if (is_object($params)) {
            $params = (array) $params;
        }

        if (! is_array($params)) {
            $params = [];
        }

        $slug = strtolower(str_replace('\\', '/', service('router')->controllerName()));
        $slug = preg_replace(['/\/aksara\/modules\//', '/\/modules\//', '/\/controllers\//'], ['', '', '/'], $slug, 1);

        $destructure = explode('/', $slug ?? '');

        $final_slug = [];
        $previous_segment = null;

        foreach ($destructure as $key => $val) {
            if ($val != $previous_segment) {
                $final_slug[] = $val;
            }

            $previous_segment = $val;
        }

        $final_slug = implode('/', $final_slug);

        $params = array_merge(service('request')->getGet(), $params);

        if (! empty($params)) {
            // Unset old token
            unset($params['aksara']);

            $query_params = [];

            foreach ($params as $key => $val) {
                if (! $val || in_array($key, $params) && ! $params[$key]) {
                    continue;
                }

                $query_params[$key] = $val;
            }

            // Generate token
            $token = generate_token($final_slug . ($method ? '/' . $method : null), $query_params);

            if ($query_params) {
                $query_params = array_merge(['aksara' => $token], $query_params);
            }

            $uri = $final_slug . ($method ? '/' . $method : null) . ($query_params ? '?' . http_build_query($query_params) : null);
        } else {
            $uri = $final_slug . ($method ? '/' . $method : null);
        }

        return base_url($uri);
    }
}

if (! function_exists('asset_url')) {
    /**
     * Asset URL
     *
     * Create a local URL based on your basepath.
     * Segments can be passed in as a string or an array, same as site_url
     * or a URL to a file can be passed in, e.g. to an image file.
     *
     * @return  string
     */
    function asset_url($file = '')
    {
        return base_url('assets/' . $file);
    }
}
