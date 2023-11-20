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
     * Load additional css or js file.
     * The file location is directive to a folder named "assets" for security
     * purpose.
     */
    function asset_loader($assets = [])
    {
        $theme = null;
        $backtrace = debug_backtrace();

        foreach ($backtrace as $key => $val) {
            if (isset($val['file']) && ROOTPATH .  'aksara' . DIRECTORY_SEPARATOR . 'Laboratory' . DIRECTORY_SEPARATOR . 'Core.php' == $val['file'] && isset($val['object']->template->theme)) {
                $theme = $val['object']->template->theme;
            }
        }

        if (! $theme) {
            return false;
        }

        if (! is_array($assets)) {
            $assets = array_map('trim', explode(',', $assets));
        }

        $output = null;

        foreach ($assets as $key => $val) {
            if (file_exists(ROOTPATH . 'themes' . DIRECTORY_SEPARATOR . $theme . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . $val)) {
                if ('css' == strtolower(pathinfo($val, PATHINFO_EXTENSION))) {
                    $output .= "<link rel=\"stylesheet\" type=\"text/css\" href=\"" . base_url('themes/' . $theme . '/assets/' . $val) . "\" />\n";
                } else {
                    $output .= "<script type=\"text/javascript\" src=\"" . base_url('themes/' . $theme . '/assets/' . $val) . "\"></script>\n";
                }
            }
        }

        return $output;
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
            if (isset($val['file']) && ROOTPATH .  'aksara' . DIRECTORY_SEPARATOR . 'Laboratory' . DIRECTORY_SEPARATOR . 'Core.php' == $val['file'] && isset($val['object']->template->theme) && file_exists(ROOTPATH . 'themes' . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . $val['object']->template->theme . DIRECTORY_SEPARATOR . $data)) {
                return str_replace('/index.php/', '/', base_url('themes/' . $val['object']->template->theme . '/assets/' . $data));
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
            if (file_exists(ROOTPATH . 'aksara' . DIRECTORY_SEPARATOR . 'Modules' . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . $data)) {
                return base_url('modules/aksara/' . $module . '/assets/' . $data);
            } elseif (file_exists(ROOTPATH . 'modules' . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . $data)) {
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
                                ' . (isset($val->icon) && $val->icon ? '<i class="' . $val->icon . '"></i>' : null) . '
                                <b class="text-sm hide-on-collapse">
                                    ' . ($val->label ? $val->label : null) . '
                                </b>
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
                                ' . (isset($val->icon) && $val->icon ? '<i class="' . $val->icon . '"></i>' : null) . '
                                <span class="hide-on-collapse">
                                    ' . $val->label . '
                                </span>
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
