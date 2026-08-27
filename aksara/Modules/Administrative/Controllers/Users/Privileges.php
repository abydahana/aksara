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

namespace Aksara\Modules\Administrative\Controllers\Users;

use Aksara\Laboratory\Core;

class Privileges extends Core
{
    private string $_table = 'app_users_privileges';
    private ?int $_primary;
    private ?object $_user;

    public function __construct()
    {
        parent::__construct();

        $this->setMethod('update');
        $this->setPermission();
        $this->setTheme('backend');
        $this->permitUpsert();

        $this->_primary = $this->request->getGet('user_id');

        $this->_user = $this->model->select('
            app_users.user_id,
            app_users.username,
            app_users.first_name,
            app_users.last_name,
            app_users.photo,
            app_users.group_id,
            app_groups.group_name,
            ' . $this->_table . '.sub_level,
            ' . $this->_table . '.access_year
        ')
        ->join(
            'app_groups',
            'app_groups.group_id = app_users.group_id'
        )
        ->join(
            $this->_table,
            $this->_table . '.user_id = app_users.user_id',
            'left'
        )
        ->getWhere(
            'app_users',
            [
                'app_users.user_id' => $this->_primary
            ],
            1
        )
        ->row();

        // Check if user exists
        if (! $this->_user || in_array($this->_user->group_id, [1])) {
            // Otherwise, throw the exception
            return throw_exception(404, phrase('You are not permitted to modify the selected user.'), current_page('../', ['user_id' => null]));
        }
    }

    public function index()
    {
        $this->setTitle(phrase('Custom User Privileges'))
        ->setIcon('mdi mdi-account-check-outline')
        ->setOutput([
            'userdata' => $this->_user,
            'year' => $this->_year(),
            'subLevel' => $this->_subLevel(),
            'visibleMenu' => $this->_visibleMenu()
        ])
        ->setDefault([
            'user_id' => $this->_primary,
            'access_year' => $this->request->getPost('year')
        ])
        ->where('user_id', $this->_primary)
        ->limit(1)

        ->render($this->_table);
    }

    /**
     * List the years if available
     */
    private function _year()
    {
        $query = $this->model->getWhere(
            'app_years',
            [
                'status' => 1
            ]
        )
            ->result();

        return $query;
    }

    /**
     * List the relation to sub level 1
     *
     * This is just a sample for sub level relation. You can change the table
     * for the sub level relation to your own table that will be used to
     * separate users based on their level
     */
    private function _subLevel()
    {
        /**
         * Replace below line with query builder.
         */
        $query = [];

        return $query;
    }

    /**
     * List the visible menu
     */
    private function _visibleMenu()
    {
        // Get existing user menu if any
        $existingMenu = $this->model->select('visible_menu')->getWhere($this->_table, ['user_id' => $this->_primary], 1)->row('visible_menu');
        $existingMenu = json_decode($existingMenu);

        // Get sidebar menu by user group from the database
        $visibleMenu = $this->model->select('
                app_menus.menu_structure
            ')
            ->join(
                'app_groups',
                'app_groups.group_id = app_users.group_id'
            )
            ->join(
                'app_menus',
                'app_menus.group_id = app_groups.group_id'
            )
            ->getWhere(
                'app_users',
                [
                    'app_users.user_id' => $this->_primary,
                    'app_menus.menu_placement' => 'sidebar'
                ],
                1
            )
            ->row('menu_structure');

        // Decode serialized menu
        $visibleMenu = json_decode($visibleMenu);

        // Set default item
        $items = null;

        if ($visibleMenu) {
            foreach ($visibleMenu as $menu => $item) {
                if (! isset($item->id) || ! isset($item->slug) || ! isset($item->label)) {
                    continue;
                }

                $items .= '
                    <li' . (isset($item->children) && $item->children ? ' class="check-group"' : null) . '>
                        <label class="big-label">
                            <input type="checkbox"name="menus[]" value="' . $item->id . '" class="form-check-input"' . (isset($item->children) && $item->children ? ' data-role="checker" data-parent=".check-group"' : null) . (isset($existingMenu->$item->id) ? ' checked' : null) . ' />
                            &nbsp;
                            <i class="' . (isset($item->icon) ? $item->icon : 'mdi mdi-circle-outline') . '"></i>
                            ' . phrase($item->label, [], true) . '
                        </label>
                        ' . (isset($item->children) ? $this->_childrenMenu($item->children, $existingMenu) : null) . '
                    </li>
                ';
            }

            $items = '
                <ul class="list-unstyled">
                    ' . $items . '
                </ul>
            ';
        }

        return $items;
    }

    /**
     * Re-loop the available menu to find the children
     */
    private function _childrenMenu($children = [], $existingMenu = [])
    {
        $items = null;

        if ($children) {
            foreach ($children as $menu => $item) {
                if (! isset($item->id) || ! isset($item->slug) || ! isset($item->label)) {
                    continue;
                }

                $items .= '
                    <li' . (isset($item->children) && $item->children ? ' class="check-group"' : null) . '>
                        <label class="big-label">
                            <input type="checkbox"name="menus[]" value="' . $item->id . '" class="form-check-input checker-children"' . (isset($item->children) && $item->children ? ' data-role="checker" data-parent=".check-group"' : null) . (isset($existingMenu->$item->id) ? ' checked' : null) . ' />
                            &nbsp;
                            <i class="' . (isset($item->icon) ? $item->icon : 'mdi mdi-circle-outline') . '"></i>
                            ' . phrase($item->label, [], true) . '
                        </label>
                        ' . (isset($item->children) ? $this->_childrenMenu($item->children) : null) . '
                    </li>
                ';
            }

            $items = '
                <ul class="list-unstyled ms-3">
                    ' . $items . '
                </ul>
            ';
        }

        return $items;
    }
}
