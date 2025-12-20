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

namespace Aksara\Modules\Assets\Controllers;

use Config\Services;
use Aksara\Laboratory\Core;
use Throwable;

class Assets extends Core
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get file
     */
    public function index()
    {
        // Get the path from URI
        $path = uri_string();

        // Resolve the absolute path
        $targetFile = FCPATH . $path;
        $realPath = realpath($targetFile);

        /**
         * Security Check:
         * - realpath() returns false if the file doesn't exist.
         * - strpos() ensures the resolved path MUST still start with FCPATH.
         * This prevents users from using ../../../etc/passwd
         */
        if (false === $realPath || strpos($realPath, realpath(FCPATH)) !== 0) {
            return throw_exception(403, phrase('Access denied'), base_url());
        }

        if (is_file($realPath)) {
            return $this->response->download($realPath, null)->inline()->send();
        }

        return throw_exception(404, phrase('The page you requested does not exist or already been archived.'), base_url());
    }

    /**
     * Load CSS properties
     * @param null|mixed $theme
     */
    public function styles(?string $theme = null)
    {
        // Fallback to default theme if not provided
        $theme = $theme ?: (get_setting('frontend_theme') ?? 'default');
        $output = '';

        // Define local CSS files to load
        $local_css = [
            'assets/local/css/override.min.css',
            'assets/local/css/ie.fix.min.css'
        ];

        if (is_rtl()) {
            $local_css[] = 'assets/local/css/override.rtl.min.css';
        }

        // Load local CSS content with safety checks
        foreach ($local_css as $file) {
            if (is_file(FCPATH . $file)) {
                $output .= file_get_contents(FCPATH . $file) . "\n";
            }
        }

        try {
            $theme_config_path = ROOTPATH . 'themes/' . $theme . '/theme.json';

            if (is_file($theme_config_path)) {
                $theme_package = json_decode(file_get_contents($theme_config_path));
                $configs = $theme_package->configs ?? null;
                $colors = $theme_package->colorscheme ?? null;

                if (isset($configs->wrapper) && $colors) {
                    // Map JSON keys to CSS selectors to eliminate redundant if-else blocks
                    $mapping = [
                        'page' => 'body',
                        'header' => $configs->wrapper->header ?? null,
                        'footer' => $configs->wrapper->footer ?? null,
                        'breadcrumb' => $configs->wrapper->breadcrumb ?? null,
                        'sidebar' => $configs->wrapper->sidebar ?? null,
                        'content' => $configs->wrapper->content ?? null
                    ];

                    foreach ($mapping as $key => $selector) {
                        // Generate CSS if selector is defined and colors exist for this key
                        if ($selector && isset($colors->$key)) {
                            $bg = $colors->$key->background ?? '#fff';
                            $txt = $colors->$key->text ?? '#333';

                            $output .= "{$selector} { background: {$bg} !important; color: {$txt} !important; }\n";
                        }
                    }
                }
            }
        } catch (Throwable $e) {
            // Output error as a CSS comment instead of crashing or exiting
            log_message('error', '[Aksara] Styles Loader: ' . $e->getMessage());

            $output .= "/* Theme Engine Error: " . addslashes($e->getMessage()) . " */";
        }

        /**
         * Ideally, you don't need to change any code beyond this point.
         */
        $credits = <<<EOF
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
        EOF;

        // Return optimized response with caching
        return $this->response
            ->setHeader('Content-Type', 'text/css')
            ->setCache(['max-age' => 3600, 'public', 'must-revalidate'])
            ->setBody($credits . "\n\n" . $output)
            ->send();
    }

    /**
     * Load required javascript assets. The order is preserved.
     * @return \CodeIgniter\HTTP\Response
     */
    public function scripts(?string $theme = null)
    {
        // Fallback to default theme if not provided
        $theme = $theme ?: (get_setting('frontend_theme') ?? 'default');

        $output = '';

        // Define the manifest of scripts to be loaded in order
        $scripts = [
            'assets/jquery/jquery.min.js',
            'assets/actual/actual.min.js',
            'assets/visible/visible.min.js',
            'assets/local/js/require.min.js',
            'assets/twig/twig.min.js',
            'assets/local/js/function.min.js',
            'assets/local/js/parser.min.js',
            'assets/local/js/global.min.js'
        ];

        // Add IE Fixer if browser is Internet Explorer
        $agent = $this->request->getUserAgent();
        if ($agent->isBrowser('Internet Explorer')) {
            $scripts[] = 'assets/local/js/ie.fix.min.js';
        }

        try {
            foreach ($scripts as $file) {
                $path = FCPATH . $file;
                if (is_file($path)) {
                    $output .= file_get_contents($path) . "\n";
                }
            }
        } catch (Throwable $e) {
            // Log error internally and prevent script crash
            log_message('error', '[Aksara] Scripts Loader: ' . $e->getMessage());

            $output .= "\n/* Error loading some assets. Check server logs. */";
        }

        // Clean up double-slash comments to reduce file size
        $output = preg_replace('/\n(\s+)?\/\/[^\n]*/', '', $output);

        // Define JS configuration variables
        $config = 'const config = ' . ($this->_get_configs($theme) ?: '{}') . ';';
        $phrases = 'const phrases = ' . ($this->_get_phrases() ?: '{}') . ';';
        $components = 'const components = ' . ($this->_get_components($theme) ?: '{}') . ';';

        $credits = <<<EOF
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
        EOF;

        // Return optimized response with caching
        return $this->response
            ->setHeader('Content-Type', 'text/javascript')
            ->setCache(['max-age' => 3600, 'public', 'must-revalidate'])
            ->setBody($credits . "\n\n" . $config . "\n\n" . $phrases . "\n\n" . $components . "\n\n" . $output)
            ->send();
    }

    private function _get_configs($theme = null)
    {
        $uri = Services::uri();

        $configs = [
            'base_url' => preg_replace('/\?.*/', '', base_url()),
            'current_slug' => str_replace('.', '-', $uri->getPath()),
            'document_extension_allowed' => (json_encode(explode(',', DOCUMENT_FORMAT_ALLOWED)) ? json_encode(explode(',', DOCUMENT_FORMAT_ALLOWED)) : []),
            'image_extension_allowed' => (json_encode(explode(',', IMAGE_FORMAT_ALLOWED)) ? json_encode(explode(',', IMAGE_FORMAT_ALLOWED)) : []),
            'max_upload_size' => (MAX_UPLOAD_SIZE ? (MAX_UPLOAD_SIZE * 1024) : 0),
            'app_name' => get_setting('app_name'),
            'app_logo' => get_image('settings', get_setting('app_logo')),
            'app_icon' => get_image('settings', get_setting('app_icon'), 'icon'),
            'language' => get_userdata('language'),
            'timezone' => TIMEZONE,
            'openlayers_search_provider' => get_setting('openlayers_search_provider'),
            'openlayers_search_key' => get_setting('openlayers_search_key'),
            'default_map_tile' => get_setting('default_map_tile'),
            'action_sound' => (get_setting('action_sound') ? true : false)
        ];

        if (file_exists(ROOTPATH . 'themes/' . $theme . '/theme.json')) {
            try {
                $theme_package = file_get_contents(ROOTPATH . 'themes/' . $theme . '/theme.json');
                $theme_package = json_decode($theme_package, true);
                if (isset($theme_package['configs'])) {
                    // Merge main configs and theme package
                    $configs = array_merge($configs, $theme_package['configs']);
                }
            } catch (Throwable $e) {
                log_message('error', '[Aksara] JavaScript Config: ' . $e->getMessage());
            }
        }

        return json_encode($configs, JSON_UNESCAPED_SLASHES);
    }

    private function _get_phrases()
    {
        $phrases = '[]';

        try {
            if (file_exists(WRITEPATH . 'translations' . DIRECTORY_SEPARATOR . get_userdata('language') . '.json')) {
                $phrases = file_get_contents(WRITEPATH . 'translations' . DIRECTORY_SEPARATOR . get_userdata('language') . '.json');
                $phrases = json_decode($phrases, true);
            }
        } catch (Throwable $e) {
            log_message('error', '[Aksara] Phrase Loader: ' . $e->getMessage());
        }

        return json_encode($phrases, JSON_UNESCAPED_SLASHES);
    }

    private function _get_components(?string $theme = null): string
    {
        $results = [];
        $theme = $theme ?: (get_setting('frontend_theme') ?? 'default');
        $base_path = ROOTPATH . 'themes/' . $theme . '/components' . DIRECTORY_SEPARATOR;

        // Allowed component types/directories
        $allowed_types = ['core', 'table', 'form', 'view'];

        try {
            if (is_dir($base_path)) {
                helper('filesystem');

                // Map the directory with a depth of 2
                $map = directory_map($base_path, 2);

                if (is_array($map)) {
                    foreach ($map as $folder => $files) {
                        // Clean folder name from Directory Separator (e.g., "core/" -> "core")
                        $type = strtolower(rtrim($folder, DIRECTORY_SEPARATOR));

                        if (! is_array($files) || ! in_array($type, $allowed_types)) {
                            continue;
                        }

                        foreach ($files as $file) {
                            // We only care about .twig files
                            if (pathinfo($file, PATHINFO_EXTENSION) !== 'twig') {
                                continue;
                            }

                            $full_path = $base_path . $type . DIRECTORY_SEPARATOR . $file;

                            if (is_file($full_path)) {
                                $content = file_get_contents($full_path);

                                // Minify the Twig template string (remove extra whitespace/newlines)
                                // and store it with the key "type/filename.twig"
                                $results[$type . '/' . $file] = preg_replace('/\s+/', ' ', $content);
                            }
                        }
                    }
                }
            }
        } catch (Throwable $e) {
            log_message('error', '[Aksara] Component Loader: ' . $e->getMessage());
        }

        return json_encode($results, JSON_UNESCAPED_SLASHES);
    }
}
