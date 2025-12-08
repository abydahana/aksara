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

if (! function_exists('asset_loader')) {
    /**
     * Load additional CSS or JS files.
     * Auto-detect: check theme assets first, fallback to core assets
     *
     * @param array|string $assets Array or comma-separated string of file paths
     * @return string|false HTML tags for loading assets
     */
    function asset_loader($assets = [])
    {
        $theme = null;
        $backtrace = debug_backtrace();

        // Auto-detect theme from backtrace
        foreach ($backtrace as $key => $val) {
            if (isset($val['file']) && ROOTPATH . 'aksara' . DIRECTORY_SEPARATOR . 'Laboratory' . DIRECTORY_SEPARATOR . 'Core.php' == $val['file']) {
                if (isset($val['object']->template->theme)) {
                    $theme = $val['object']->template->theme;
                } elseif (isset($val['object']->theme)) {
                    $theme = $val['object']->theme;
                }
            }
        }

        // Fallback: try get_theme() helper
        if (! $theme) {
            $theme = get_theme();
        }

        // Final fallback: from settings
        if (! $theme) {
            $theme = get_setting('frontend_theme') ?? 'default';
        }

        // Convert string to array
        if (! is_array($assets)) {
            $assets = array_map('trim', explode(',', $assets));
        }

        $output = '';

        foreach ($assets as $key => $val) {
            $val = trim($val);

            if (empty($val)) {
                continue;
            }

            $extension = strtolower(pathinfo($val, PATHINFO_EXTENSION));
            $file_exists = false;
            $file_url = '';

            // Priority 1: Check theme assets
            $theme_path = ROOTPATH . 'themes' . DIRECTORY_SEPARATOR . $theme . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $val);

            if (file_exists($theme_path)) {
                $file_exists = true;
                $file_url = base_url('themes/' . $theme . '/assets/' . $val);
            } else {
                // Priority 2: Fallback to core assets
                $core_path = ROOTPATH . 'assets' . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $val);

                if (file_exists($core_path)) {
                    $file_exists = true;
                    $file_url = base_url('assets/' . $val);
                }
            }

            // Generate HTML tag
            if ($file_exists) {
                if ('css' === $extension) {
                    $output .= '<link rel="stylesheet" type="text/css" href="' . $file_url . '" />' . "\n";
                } elseif ('js' === $extension) {
                    $output .= '<script type="text/javascript" src="' . $file_url . '"></script>' . "\n";
                } else {
                    // Log warning for unrecognized extension
                    log_message('warning', "Unknown asset extension: {$val}");
                }
            } else {
                // Log warning if file not found
                log_message('warning', "Asset not found: {$val} (theme: {$theme})");
            }
        }

        return $output ?: false;
    }
}

if (! function_exists('get_theme_asset')) {
    /**
     * Load theme asset.
     * The file location is directive to a folder named "assets" for security
     * purpose.
     *
     * @param mixed|null $data
     */
    function get_theme_asset($data = null)
    {
        $theme = false;
        $backtrace = debug_backtrace();

        foreach ($backtrace as $key => $val) {
            if (isset($val['file']) && ROOTPATH .  'aksara' . DIRECTORY_SEPARATOR . 'Laboratory' . DIRECTORY_SEPARATOR . 'Core.php' == $val['file'] && isset($val['object']->template->theme) && file_exists(ROOTPATH . 'themes' . DIRECTORY_SEPARATOR . $val['object']->template->theme . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . $data)) {
                return str_replace('/index.php/', '/', base_url('themes/' . $val['object']->template->theme . '/assets/' . $data));
            } elseif (isset($val['file']) && ROOTPATH .  'aksara' . DIRECTORY_SEPARATOR . 'Laboratory' . DIRECTORY_SEPARATOR . 'Core.php' == $val['file'] && isset($val['object']->theme) && file_exists(ROOTPATH . 'themes' . DIRECTORY_SEPARATOR . $val['object']->theme . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . $data)) {
                return str_replace('/index.php/', '/', base_url('themes/' . $val['object']->theme . '/assets/' . $data));
            }
        }

        return '#';
    }
}

if (! function_exists('get_module_asset')) {
    /**
     * Load module asset.
     * The file location is directive to a folder named "assets" for security
     * purpose.
     *
     * @param mixed|null $data
     */
    function get_module_asset($data = null, $x = false)
    {
        $controller = service('router')->controllerName();

        preg_match('/\\\Modules\\\(.*?)\\\Controllers\\\/', $controller, $matches);

        $module = $matches[1];

        if ($module) {
            if (
                file_exists(ROOTPATH . 'modules' . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . $data)
                || file_exists(ROOTPATH . 'aksara' . DIRECTORY_SEPARATOR . 'Modules' . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . $data)
            ) {
                return base_url('modules/' . $module . '/assets/' . $data);
            }
        }

        return '#';
    }
}

if (! function_exists('generate_menu')) {
    /**
     * Menu generator
     */
    function generate_menu($menus = [], $ul_class = 'navbar-nav', $li_class = 'nav-item', $a_class = 'nav-link', $toggle_class = 'dropdown-toggle', $toggle_initial = 'data-bs-toggle="dropdown"', $dropdown_class = 'dropdown', $sub_ul_class = 'dropdown-menu', $is_children = false, $level = 0)
    {
        $output = null;

        foreach ($menus as $key => $val) {
            if (isset($val->id) && isset($val->label) && isset($val->slug)) {
                if ('---' == $val->slug) {
                    $output .= '
                        <li class="' . $li_class . (isset($val->class) ? ' ' . $val->class : null) . '">
                            <span class="' . $a_class . '">
                                ' . (isset($val->icon) && $val->icon && ! in_array($val->icon, ['mdi mdi-blank']) ? '<i class="' . $val->icon . '"></i>' : null) . '<b class="text-sm hide-on-collapse">' . ($val->label ? $val->label : null) . '</b>
                            </span>
                        </li>
                    ';
                } else {
                    $segments = service('uri')->getSegments();
                    $slug = $val->slug;
                    $children = (isset($val->children) && $val->children ? $val->children : []);

                    if (preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $val->slug)) {
                        $val->slug = $val->slug . '" target="_blank';
                    } else {
                        $val->slug = base_url($val->slug);
                    }

                    $output .= '
                        <li class="' . $li_class . ($children && $dropdown_class ? ' ' . $dropdown_class : null) . ((! $children && isset($segments[$level]) && $segments[$level] == $slug) || service('uri')->getPath() == $slug || (service('uri')->getPath() && preg_replace(['/\/create/', '/\/read/', '/\/update/'], '', service('uri')->getPath()) == $slug) ? ' active' : '') . (isset($val->class) ? ' ' . $val->class : null) . '">
                            <a href="' . ($children ? '#' : $val->slug) . '" class="' . $a_class . ($children ? ' ' . $toggle_class : null) . '"' . ($children ? ' ' . $toggle_initial : ' data-segmentation="' . preg_replace('/[^a-zA-Z0-9]/', '_', $slug) . '"') . (isset($val->new_tab) && $val->new_tab && ! $children ? ' target="_blank"' : '  data-bs-auto-close="outside"') . '>
                                ' . (isset($val->icon) && $val->icon && ! in_array($val->icon, ['mdi mdi-blank']) ? '<i class="' . $val->icon . '"></i>' : null) . '<span class="hide-on-collapse">' . $val->label . '</span>
                            </a>
                            ' . ($children ? generate_menu($children, $ul_class, $li_class, $a_class, $toggle_class, $toggle_initial, $dropdown_class, $sub_ul_class, true, ($level + 1)) : null) . '
                        </li>
                    ';
                }
            }
        }

        return '<ul class="' . ($is_children ? $sub_ul_class : $ul_class) . '">' . $output . '</ul>';
    }
}
