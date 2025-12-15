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

use Throwable;
use Config\Services;
use Aksara\Laboratory\Core;

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
        if (is_file(FCPATH . uri_string())) {
            helper('download');

            return force_download(basename(uri_string()), file_get_contents(FCPATH . uri_string()), true);
        }

        return throw_exception(404, phrase('The page you requested does not exist or already been archived.'), base_url());
    }

    /**
     * Load CSS properties
     * @param null|mixed $theme
     */
    public function styles($theme = null)
    {
        // Fallback ke theme default jika tidak ada
        if (! $theme) {
            $theme = get_setting('frontend_theme') ?? 'default';
        }

        $output = '';

        try {
            $output .= file_get_contents('assets/local/css/override.min.css') . "\n";
            $output .= file_get_contents('assets/local/css/ie.fix.min.css') . "\n";

            if (file_exists(ROOTPATH . 'themes/' . $theme . '/theme.json')) {
                $theme_package = file_get_contents(ROOTPATH . 'themes/' . $theme . '/theme.json');
                $theme_package = json_decode($theme_package);

                if (isset($theme_package->configs->wrapper) && isset($theme_package->colorscheme)) {
                    $colorscheme = $theme_package->colorscheme;

                    $page_background = (isset($colorscheme->page->background) ? $colorscheme->page->background : '#fff');
                    $page_text = (isset($colorscheme->page->text) ? $colorscheme->page->text : '#333');
                    $header_background = (isset($colorscheme->header->background) ? $colorscheme->header->background : '#fff');
                    $header_text = (isset($colorscheme->header->text) ? $colorscheme->header->text : '#333');
                    $footer_background = (isset($colorscheme->footer->background) ? $colorscheme->footer->background : '#fff');
                    $footer_text = (isset($colorscheme->footer->text) ? $colorscheme->footer->text : '#333');
                    $breadcrumb_background = (isset($colorscheme->breadcrumb->background) ? $colorscheme->breadcrumb->background : '#fff');
                    $breadcrumb_text = (isset($colorscheme->breadcrumb->text) ? $colorscheme->breadcrumb->text : '#333');
                    $sidebar_background = (isset($colorscheme->sidebar->background) ? $colorscheme->sidebar->background : '#fff');
                    $sidebar_text = (isset($colorscheme->sidebar->text) ? $colorscheme->sidebar->text : '#333');

                    $wrapper = (isset($theme_package->configs->wrapper) ? $theme_package->configs->wrapper : []);

                    $page_wrapper = (isset($wrapper->page) ? $wrapper->page : null);
                    $content_wrapper = (isset($wrapper->content) ? $wrapper->content : null);
                    $header_wrapper = (isset($wrapper->header) ? $wrapper->header : null);
                    $footer_wrapper = (isset($wrapper->footer) ? $wrapper->footer : null);
                    $breadcrumb_wrapper = (isset($wrapper->breadcrumb) ? $wrapper->breadcrumb : null);
                    $sidebar_wrapper = (isset($wrapper->sidebar) ? $wrapper->sidebar : null);

                    if ($page_wrapper) {
                        $output .= 'body{background: ' . $page_background . '!important; color: ' . $page_text . '!important}' . "\n";
                    }

                    if ($content_wrapper) {
                        $output .= 'body{background: ' . $page_background . '!important; color: ' . $page_text . '!important}' . "\n";
                    }

                    if ($header_wrapper) {
                        $output .= $header_wrapper . '{background: ' . $header_background . '!important; color: ' . $header_text . '!important}' . "\n";
                    }

                    if ($footer_wrapper) {
                        $output .= $footer_wrapper . '{background: ' . $footer_background . '!important; color: ' . $footer_text . '!important}' . "\n";
                    }

                    if ($breadcrumb_wrapper) {
                        $output .= $breadcrumb_wrapper . '{background: ' . $breadcrumb_background . '!important; color: ' . $breadcrumb_text . '!important}' . "\n";
                    }

                    if ($sidebar_wrapper) {
                        $output .= $sidebar_wrapper . '{background: ' . $sidebar_background . '!important; color: ' . $sidebar_text . '!important}' . "\n";
                    }
                }
            }
        } catch (Throwable $e) {
            exit($e->getMessage());
        }

        if (is_rtl()) {
            $output .= file_get_contents('assets/local/css/override.rtl.min.css') . "\n";
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

        $this->response->setHeader('Content-Type', 'text/css');
        $this->response->setBody($credits);
        $this->response->appendBody($output);

        return $this->response->send();
    }

    /**
     * Load required javascript's assets. The order must be as preserved.
     * @param null|mixed $theme
     */
    public function scripts($theme = null)
    {
        // Fallback ke theme default jika tidak ada
        if (! $theme) {
            $theme = get_setting('frontend_theme') ?? 'default';
        }

        $output = '';

        try {
            // Include core plugins, the order must be as preserved
            $output .= file_get_contents('assets/jquery/jquery.min.js');
            $output .= file_get_contents('assets/actual/actual.min.js');
            $output .= file_get_contents('assets/visible/visible.min.js');

            // JS and CSS loader with require function
            $output .= file_get_contents('assets/local/js/require.min.js');

            // Twig template parser
            $output .= file_get_contents('assets/twig/twig.min.js');

            // Global function
            $output .= file_get_contents('assets/local/js/function.min.js');

            // Template parser, require TwigJS
            $output .= file_get_contents('assets/local/js/parser.min.js');
            // Event listener
            $output .= file_get_contents('assets/local/js/global.min.js');

            if (strtolower($this->request->getUserAgent()->getBrowser()) == 'internet explorer') {
                // IE fixer
                $output .= file_get_contents('assets/local/js/ie.fix.min.js');
            }
        } catch (Throwable $e) {
            exit($e->getMessage());
        }

        // Remove comment like this (double slashes)
        $output = preg_replace('/\n(\s+)?\/\/[^\n]*/', '', $output);

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

        $this->response->setHeader('Content-Type', 'text/javascript');
        $this->response->setBody($credits);
        $this->response->appendBody('const config = ' . $this->_get_configs($theme) . ';');
        $this->response->appendBody('const phrases = ' . $this->_get_phrases() . ';');
        $this->response->appendBody('const components = ' . $this->_get_components($theme) . ';');
        $this->response->appendBody($output);

        return $this->response->send();
    }

    private function _get_configs($theme = null)
    {
        $uri = Services::uri();

        $configs = [
            'base_url' => preg_replace('/\?.*/', '', base_url()),
            'current_slug' => str_replace('.', '-', $uri->setSilent()->getPath()),
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
                // Safe abstraction
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
            // Safe abstraction
        }

        return json_encode($phrases, JSON_UNESCAPED_SLASHES);
    }

    private function _get_components($theme = null)
    {
        $components = [];

        try {
            helper('filesystem');

            $components = directory_map(ROOTPATH . 'themes/' . $theme . '/components', 2);

            foreach ($components as $path => $component) {
                // Theme path
                if (! is_array($component)) {
                    unset($components[$path]);

                    continue;
                }

                foreach ($component as $index => $template) {
                    // Component path
                    $type = strtolower(str_replace(DIRECTORY_SEPARATOR, '', $path));

                    if (! $template || ! in_array($type, ['core', 'table', 'form', 'view'])) {
                        unset($components[$path][$index]);

                        continue;
                    }

                    // Template path
                    $filename = pathinfo($template, PATHINFO_FILENAME);
                    $extension = pathinfo($template, PATHINFO_EXTENSION);

                    if (strtolower($extension) !== 'twig' || ! file_exists(ROOTPATH . 'themes/' . $theme . '/components/' . $type . '/' . $template)) {
                        unset($components[$path]);

                        continue;
                    }

                    $template_string = file_get_contents(ROOTPATH . 'themes/' . $theme . '/components/' . $type . '/' . $template);

                    $components[$type . '/' . $template] = preg_replace('/\s+/', ' ', $template_string);

                    unset($components[$path]);
                }
            }
        } catch (Throwable $e) {
            // Safe abstraction
        }

        return json_encode($components, JSON_UNESCAPED_SLASHES);
    }
}
