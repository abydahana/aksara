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

use CodeIgniter\HTTP\SiteURI;

if (! function_exists('base_url')) {
    /**
     * Base URL
     *
     * Create a local URL based on your basepath.
     * Segments can be passed in as a string or an array, same as site_url
     * or a URL to a file can be passed in, e.g. to an image file.
     */
    function base_url(string|array|null $path = null, ?array $params = []): string
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

        $previewTheme = false;

        if ('preview-theme' == $request->getGet('aksara_mode')) {
            $themeName = $request->getGet('aksara_theme');

            if (
                hash_equals(hash_hmac('sha256', $themeName . get_userdata('session_generated'), ENCRYPTION_KEY), (string) $request->getGet('integrity_check')) &&
                preg_match('/^[a-zA-Z0-9_-]+$/', $themeName) &&
                is_dir(ROOTPATH . 'themes/' . $themeName)
            ) {
                $previewTheme = true;
            }
        }

        if ($params || $previewTheme) {
            $params = array_merge($request->getGet(), $params);
        }

        if (! empty($params)) {
            // Unset old token
            unset($params['aksara']);

            $queryParams = [];

            foreach ($params as $key => $val) {
                if (null === $val || '' === $val) {
                    continue;
                }

                $queryParams[$key] = $val;
            }

            // Generate token
            $token = generate_token($path, $queryParams);

            if ($queryParams && $token) {
                $queryParams = array_merge(['aksara' => $token], $queryParams);
            }

            $queryString = ($queryParams ? '?' . str_replace(['%7B', '%7D'], ['{', '}'], http_build_query($queryParams)) : '');
        } else {
            $queryString = '';
        }

        $currentURI = service('request')->getUri();

        assert($currentURI instanceof SiteURI);

        if ((service('request')->getServer('HTTP_MOD_REWRITE') && strtolower(service('request')->getServer('HTTP_MOD_REWRITE')) == 'on') || (function_exists('apache_get_modules') && in_array('mod_rewrite', apache_get_modules())) || ($path && file_exists(FCPATH . $path))) {
            $finalUrl = $currentURI->baseUrl(($path ? rtrim($path, '/') : '')) . $queryString;
        } else {
            $finalUrl = $currentURI->baseUrl((config('App')->indexPage ? config('App')->indexPage . '/' : null) . ($path ? rtrim($path, '/') : '')) . $queryString;
        }

        return str_replace(['%7B', '%7D'], ['{', '}'], $finalUrl);
    }
}

if (! function_exists('current_page')) {
    /**
     * Current Page
     *
     * Get the current page URL and add the add extra parameter
     * on it.
     */
    function current_page(string|array|null $method = null, ?array $params = [], ?string $unset = null): string
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

            $queryParams = [];

            foreach ($params as $key => $val) {
                if (null === $val || '' === $val) {
                    continue;
                }

                $queryParams[$key] = $val;
            }

            // Generate token
            $token = generate_token(uri_string() . ($method ? '/' . $method : null), $queryParams);

            if ($queryParams && $token) {
                $queryParams = array_merge(['aksara' => $token], $queryParams);
            }

            $queryString = ($queryParams ? '?' . str_replace(['%7B', '%7D'], ['{', '}'], http_build_query($queryParams)) : '');
            $finalUrl = base_url(uri_string()) . ($method ? '/' . $method : null) . $queryString;
        } else {
            $finalUrl = base_url(uri_string()) . ($method ? '/' . $method : null);
        }

        return str_replace(['%7B', '%7D'], ['{', '}'], $finalUrl);
    }
}

if (! function_exists('go_to')) {
    /**
     * Go To
     *
     * Generate the next page from the current page and add
     * extra parameter on it.
     */
    function go_to(string|array|null $method = null, array $params = []): string
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

        $finalSlug = [];
        $previousSegment = null;

        foreach ($destructure as $key => $val) {
            if ($val != $previousSegment) {
                $finalSlug[] = $val;
            }

            $previousSegment = $val;
        }

        $finalSlug = implode('/', $finalSlug);
        $params = array_merge(service('request')->getGet(), $params);

        if (! empty($params)) {
            // Unset old token
            unset($params['aksara']);

            $queryParams = [];

            foreach ($params as $key => $val) {
                if (null === $val || '' === $val) {
                    continue;
                }

                $queryParams[$key] = $val;
            }

            // Generate token
            $token = generate_token($finalSlug . ($method ? '/' . $method : null), $queryParams);

            if ($queryParams && $token) {
                $queryParams = array_merge(['aksara' => $token], $queryParams);
            }

            $queryString = ($queryParams ? '?' . str_replace(['%7B', '%7D'], ['{', '}'], http_build_query($queryParams)) : '');
            $uri = $finalSlug . ($method ? '/' . $method : null);
        } else {
            $queryString = '';
            $uri = $finalSlug . ($method ? '/' . $method : null);
        }

        return str_replace(['%7B', '%7D'], ['{', '}'], base_url($uri) . $queryString);
    }
}

if (! function_exists('asset_url')) {
    /**
     * Asset URL
     *
     * Create a local URL based on your basepath.
     * Segments can be passed in as a string or an array, same as site_url
     * or a URL to a file can be passed in, e.g. to an image file.
     */
    function asset_url(string $file): string
    {
        return base_url('assets/' . $file);
    }
}
