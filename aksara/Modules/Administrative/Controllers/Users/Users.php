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

class Users extends Core
{
    private $_table = 'app__users';

    public function __construct()
    {
        parent::__construct();

        $this->restrict_on_demo();

        $this->set_permission();
        $this->set_theme('backend');

        $this->unset_method('clone');

        $this->unset_delete('user_id', [1]);

        // Ignore query string signature
        $this->ignore_query_string('group');
    }

    public function index()
    {
        $this->add_filter($this->_filter());

        if ($this->request->getGet('group')) {
            $this->where([
                'group_id' => $this->request->getGet('group')
            ]);
        }

        if (in_array($this->get_method(), ['create'])) {
            $this->set_validation('password', 'required|min_length[6]');
            $this->set_default('registered_date', date('Y-m-d'));
        }

        $this->set_title(phrase('Manage Users'))
        ->set_icon('mdi mdi-account-group-outline')

        ->set_primary('user_id, username')

        // Add extra option button
        ->add_button('privileges', phrase('Individual Privilege'), 'btn-success --xhr', 'mdi mdi-account-check-outline', ['user_id' => 'user_id'])

        ->unset_column('user_id, password, phone, gender, bio, address, country, language, postal_code, registered_date')
        ->unset_field('user_id, bio, phone, address, postal_code, country_id, last_login, is_logged, registered_date')
        ->unset_view('user_id, password')
        ->column_order('photo, username, first_name, email, group_name')
        ->field_order('photo, first_name, last_name, gender, username, email, password, bio, phone, address, postal_code, country_id, language_id, group_id, status')
        ->view_order('photo, first_name, last_name, gender, username, email, language, group_name, language_id, group_id, phone, address, postal_code, country, bio, registered_date, last_login, status')
        ->set_field([
            'password' => 'password',
            'photo' => 'image',
            'last_login' => 'datetime',
            'address' => 'textarea',
            'bio' => 'textarea',
            'is_logged' => 'boolean',
            'status' => 'boolean'
        ])
        ->set_field(
            'gender',
            'radio',
            [
                0 => phrase('Male'),
                1 => phrase('Female')
            ]
        )
        ->set_relation(
            'language_id',
            'app__languages.id',
            '{{ app__languages.language }}',
            [
                'app__languages.status' => 1
            ]
        )
        ->set_relation(
            'country_id',
            'app__countries.id',
            '{{ app__countries.country }}',
            [
                'app__countries.status' => 1
            ]
        )
        ->set_relation(
            'group_id',
            'app__groups.group_id',
            '{{ app__groups.group_name }}',
            [
                'app__groups.status' => 1
            ]
        )
        ->set_validation([
            'first_name' => 'required|string|max_length[32]',
            'last_name' => 'string|max_length[32]',
            'username' => 'required|alpha_numeric|unique[app__users.username.user_id.' . $this->request->getGet('user_id') . ']',
            'email' => 'required|valid_email|unique[app__users.email.user_id.' . $this->request->getGet('user_id') . ']',
            'language_id' => 'required',
            'group_id' => 'required',
            'status' => 'boolean'
        ])
        ->set_alias([
            'first_name' => phrase('First Name'),
            'last_name' => phrase('Last Name'),
            'username' => phrase('Username'),
            'email' => phrase('Email'),
            'password' => phrase('Password'),
            'phone' => phrase('Phone'),
            'bio' => phrase('Biography'),
            'address' => phrase('Address'),
            'postal_code' => phrase('Postal Code'),
            'country_id' => phrase('Country'),
            'country' => phrase('Country'),
            'language_id' => phrase('Language'),
            'language' => phrase('Language'),
            'group_id' => phrase('Group'),
            'group_name' => phrase('Group')
        ])

        ->merge_content('{{ first_name }} {{ last_name }}', phrase('Full Name'))
        ->merge_field('first_name, last_name', phrase('Full Name'))
        ->merge_field('username, email')
        ->merge_field('language_id, group_id')
        ->merge_field('language, group_name')
        ->merge_field('postal_code, country')
        ->merge_field('registered_date, last_login')

        ->render($this->_table);
    }

    private function _filter()
    {
        $groups = [
            [
                'id' => 0,
                'label' => phrase('All groups')
            ]
        ];

        $query = $this->model->get_where(
            'app__groups',
            [
                'status' => 1
            ]
        )
        ->result();

        if ($query) {
            foreach ($query as $key => $val) {
                $groups[] = [
                    'id' => $val->group_id,
                    'label' => $val->group_name,
                    'selected' => $this->request->getGet('group') == $val->group_id ?? false
                ];
            }
        }

        return [
            'group' => [
                'type' => 'select',
                'label' => phrase('Group'),
                'values' => $groups
            ]
        ];
    }
}
