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
     * Load additional CSS or JS files efficiently.
     * * @param array|string $assets Array or comma-separated string of file paths
     * @return string|false HTML tags for loading assets
     */
    function asset_loader(string|array $assets = []): string
    {
        // Use the optimized helper instead of debug_backtrace
        $theme = get_theme();

        // Fallback: from settings if helper returns null
        if (! $theme) {
            $theme = get_setting('frontend_theme') ?? 'default';
        }

        // Normalize assets to array
        if (! is_array($assets)) {
            $assets = array_map('trim', explode(',', $assets));
        }

        $output = '';

        foreach ($assets as $val) {
            $val = trim($val);
            if (empty($val)) {
                continue;
            }

            $extension = strtolower(pathinfo($val, PATHINFO_EXTENSION));
            $file_url = '';

            // Priority 1: Check theme assets directory
            if (file_exists(ROOTPATH . 'themes/' . $theme . '/assets/' . $val)) {
                $file_url = base_url('themes/' . $theme . '/assets/' . $val);
            }
            // Priority 2: Fallback to core assets directory
            elseif (file_exists(ROOTPATH . 'assets/' . $val)) {
                $file_url = base_url('assets/' . $val);
            }

            // Generate HTML tags based on extension
            if ($file_url) {
                if ('css' === $extension) {
                    $output .= '<link rel="stylesheet" type="text/css" href="' . $file_url . '" />' . "\n";
                } elseif ('js' === $extension) {
                    $output .= '<script type="text/javascript" src="' . $file_url . '"></script>' . "\n";
                }
            } else {
                log_message('warning', "Asset not found: {$val} (theme: {$theme})");
            }
        }

        return $output;
    }
}

if (! function_exists('get_theme_asset')) {
    /**
     * Load theme asset.
     * The file location is directed to a folder named "assets" within the active theme.
     *
     * @param string|null $data The relative path to the asset file
     * @return string The asset URL or '#' if not found
     */
    function get_theme_asset(string $data): string
    {
        // Use the optimized helper instead of scanning backtrace
        $theme = get_theme();

        if ($theme) {
            // Define the local path and web URL
            $asset_path = ROOTPATH . 'themes/' . $theme . '/assets/' . $data;

            // Check if the file exists within the theme's asset directory
            if (file_exists($asset_path)) {
                // Return the cleaned base URL
                return str_replace('/index.php/', '/', base_url('themes/' . $theme . '/assets/' . $data));
            }
        }

        // Return a dummy link if theme is not detected or file doesn't exist
        return '#';
    }
}

if (! function_exists('get_module_asset')) {
    /**
     * Load a module-specific asset.
     *
     * This function identifies the active module by parsing the current controller's
     * namespace via the router service. It checks for the file's existence within
     * the 'assets' directory of both user-defined modules and core modules.
     *
     * @param string|null $data The relative path to the asset file (e.g., 'assets/css/style.css').
     * @return string           Returns the full URL to the asset if found; otherwise, returns '#'.
     */
    function get_module_asset(?string $data = null): string
    {
        // Get the fully qualified class name of the current controller
        $controller = service('router')->controllerName();

        // Extract the module name using regex to capture the segment between \Modules\ and \Controllers\
        preg_match('/\\\Modules\\\(.*?)\\\Controllers\\\/', $controller, $matches);

        // Get the captured module name from the regex matches
        $module = $matches[1] ?? null;

        if ($module) {
            /**
             * Check if the asset exists in:
             * 1. The custom modules directory (/modules/...)
             * 2. The core Aksara modules directory (/aksara/Modules/...)
             */
            if (
                file_exists(ROOTPATH . "modules/$module/assets/$data")
                || file_exists(APPPATH . "Modules/$module/assets/$data")
            ) {
                // Return the base URL pointing to the assets directory
                return base_url("modules/$module/assets/$data");
            }
        }

        // Return a dummy link if the module cannot be determined or the file is missing
        return '#';
    }
}

if (! function_exists('generate_menu')) {
    /**
     * Menu generator
     */
    function generate_menu(
        array|object $menus,
        string $ul_class = 'navbar-nav',
        string $li_class = 'nav-item',
        string $a_class = 'nav-link',
        string $toggle_class = 'dropdown-toggle',
        string $toggle_initial = 'data-bs-toggle="dropdown"',
        string $dropdown_class = 'dropdown',
        string $sub_ul_class = 'dropdown-menu',
        bool $is_children = false,
        int $level = 0
    ): string {
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
