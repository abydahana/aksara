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

if (! function_exists('generate_menu')) {
    /**
     * Menu generator
     */
    function generate_menu(
        array|object $menus,
        string $ulClass = 'navbar-nav',
        string $liClass = 'nav-item',
        string $aClass = 'nav-link',
        string $toggleClass = 'dropdown-toggle',
        string $toggleInitial = 'data-bs-toggle="dropdown"',
        string $dropdownClass = 'dropdown',
        string $subUlClass = 'dropdown-menu',
        bool $isChildren = false,
        int $level = 0
    ): string {
        $output = null;

        foreach ($menus as $key => $val) {
            if (! $val->label) {
                $output .= '<li style="height:1rem"></li>';

                continue;
            }

            if (isset($val->id) && isset($val->label) && isset($val->slug)) {
                if (! $val->slug || '---' == $val->slug) {
                    $output .= '
                        <li class="d-flex px-3">
                            ' . (isset($val->icon) && $val->icon ? '<i class="' . $val->icon . '"></i>' : null) . '<span class="text-sm text-uppercase hide-on-collapse">' . ($val->label ? $val->label : null) . '</span>
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
                        <li class="' . $liClass . ($children && $dropdownClass ? ' ' . $dropdownClass : null) . ((! $children && isset($segments[$level]) && $segments[$level] == $slug) || service('uri')->getPath() == $slug || (service('uri')->getPath() && preg_replace(['/\/create/', '/\/read/', '/\/update/'], '', service('uri')->getPath()) == $slug) ? ' active' : '') . (isset($val->class) ? ' ' . $val->class : null) . '">
                            <a href="' . ($children ? '#' : $val->slug) . '" class="' . $aClass . ($children ? ' ' . $toggleClass : null) . '"' . ($children ? ' ' . $toggleInitial : ' data-segmentation="' . preg_replace('/[^a-zA-Z0-9]/', '_', $slug) . '"') . (isset($val->new_tab) && $val->new_tab && ! $children ? ' target="_blank"' : '  data-bs-auto-close="outside"') . '>
                                ' . (isset($val->icon) && $val->icon ? '<i class="' . $val->icon . '"></i>' : null) . '<span class="hide-on-collapse">' . $val->label . '</span>
                            </a>
                            ' . ($children ? generate_menu($children, $ulClass, $liClass, $aClass, $toggleClass, $toggleInitial, $dropdownClass, $subUlClass, true, ($level + 1)) : null) . '
                        </li>
                    ';
                }
            }
        }

        return '<ul class="' . ($isChildren ? $subUlClass : $ulClass) . '">' . $output . '</ul>';
    }
}
