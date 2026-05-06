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

        $this->setPermission();
        $this->setTheme('backend');

        $this->setMethod('update');
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
            ' . $this->_table . '.sub_level_1,
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

        /* check if user is exists */
        if (! $this->_user || in_array($this->_user->group_id, [1])) {
            /* otherwise, throw the exception */
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
            'sub_level_1' => $this->_subLevel1(),
            'visible_menu' => $this->_visibleMenu()
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
    private function _subLevel1()
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
        /* get existing user menu if any */
        $existing_menu = $this->model->select('visible_menu')->getWhere($this->_table, ['user_id' => $this->_primary], 1)->row('visible_menu');
        $existing_menu = json_decode($existing_menu);

        /* get sidebar menu by user group from the database */
        $visible_menu = $this->model->select('
            app_menus.serialized_data
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
        ->row('serialized_data');

        /* decode serialized menu */
        $visible_menu = json_decode($visible_menu);

        /* set default item */
        $items = null;
        if ($visible_menu) {
            foreach ($visible_menu as $menu => $item) {
                if (! isset($item->id) || ! isset($item->slug) || ! isset($item->label)) {
                    continue;
                }
                $items .= '
                    <li' . (isset($item->children) && $item->children ? ' class="check-group"' : null) . '>
                        <label class="control-label big-label">
                            <input type="checkbox"name="menus[]" value="' . $item->id . '" class="form-check-input"' . (isset($item->children) && $item->children ? ' role="checker" data-parent=".check-group"' : null) . (isset($existing_menu->$item->id) ? ' checked' : null) . ' />
                            &nbsp;
                            <i class="' . (isset($item->icon) ? $item->icon : 'mdi mdi-circle-outline') . '"></i>
                            ' . phrase($item->label, [], true) . '
                        </label>
                        ' . (isset($item->children) ? $this->_childrenMenu($item->children, $existing_menu) : null) . '
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
    private function _childrenMenu($children = [], $existing_menu = [])
    {
        $items = null;
        if ($children) {
            foreach ($children as $menu => $item) {
                if (! isset($item->id) || ! isset($item->slug) || ! isset($item->label)) {
                    continue;
                }
                $items .= '
                    <li' . (isset($item->children) && $item->children ? ' class="check-group"' : null) . '>
                        <label class="control-label big-label">
                            <input type="checkbox"name="menus[]" value="' . $item->id . '" class="form-check-input checker-children"' . (isset($item->children) && $item->children ? ' role="checker" data-parent=".check-group"' : null) . (isset($existing_menu->$item->id) ? ' checked' : null) . ' />
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
