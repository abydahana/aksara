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

class Users extends Core
{
    private string $_table = 'app_users';

    public function __construct()
    {
        parent::__construct();

        $this->restrictOnDemo();

        $this->setPermission();
        $this->setTheme('backend');

        $this->unsetMethod('clone');

        $this->unsetDelete('user_id', [1]);

        // Ignore query string signature
        $this->ignoreQueryString('group');
    }

    public function index()
    {
        $this->addFilter($this->_filter());

        if ($this->request->getGet('group')) {
            $this->where([
                'group_id' => $this->request->getGet('group')
            ]);
        }

        if (in_array($this->getMethod(), ['create'])) {
            $this->setValidation('password', 'required|min_length[6]');
            $this->setDefault('registered_date', date('Y-m-d'));
        }

        $this->setTitle(phrase('Manage Users'))
            ->setIcon('mdi mdi-account-group-outline')
            ->setPrimary('user_id, username')

            ->addButton('privileges', phrase('Individual Privilege'), 'btn-success --xhr', 'mdi mdi-account-check-outline', ['user_id' => 'user_id'])

            ->unsetColumn('user_id, group_id, password, phone, gender, bio, address, country, language, postal_code, registered_date')
            ->unsetField('user_id, bio, phone, address, postal_code, country_id, last_login, is_logged, registered_date')
            ->unsetView('user_id, password')

            ->columnOrder('photo, username, first_name, email, group_name')
            ->fieldOrder('photo, first_name, last_name, gender, username, email, password, bio, phone, address, postal_code, country_id, language_id, group_id, status')
            ->viewOrder('photo, first_name, last_name, gender, username, email, language, group_name, language_id, group_id, phone, address, postal_code, country, bio, registered_date, last_login, status')

            ->setField([
                'password' => 'password',
                'photo' => 'image',
                'last_login' => 'datetime',
                'address' => 'textarea',
                'bio' => 'textarea',
                'is_logged' => 'boolean',
                'status' => 'boolean'
            ])
            ->setField(
                'gender',
                'radio',
                [
                    0 => phrase('Male'),
                    1 => phrase('Female')
                ]
            )
            ->setRelation(
                'language_id',
                'app_languages.id',
                '{{ app_languages.language }}',
                [
                    'app_languages.status' => 1
                ]
            )
            ->setRelation(
                'country_id',
                'app_countries.id',
                '{{ app_countries.country }}',
                [
                    'app_countries.status' => 1
                ]
            )
            ->setRelation(
                'group_id',
                'app_groups.group_id',
                '{{ app_groups.group_name }}',
                [
                    'app_groups.status' => 1
                ]
            )
            ->setValidation([
                'first_name' => 'required|string|max_length[32]',
                'last_name' => 'string|max_length[32]',
                'username' => 'required|alpha_numeric|unique[app_users.username.user_id.' . $this->request->getGet('user_id') . ']',
                'email' => 'required|valid_email|unique[app_users.email.user_id.' . $this->request->getGet('user_id') . ']',
                'language_id' => 'required',
                'group_id' => 'required',
                'status' => 'boolean'
            ])
            ->setAlias([
                'first_name' => phrase('First Name'),
                'last_name' => phrase('Last Name'),
                'username' => phrase('Username'),
                'email' => phrase('Email'),
                'password' => phrase('Password'),
                'phone' => phrase('Phone'),
                'bio' => phrase('Biography'),
                'gender' => phrase('Gender'),
                'address' => phrase('Address'),
                'postal_code' => phrase('Postal Code'),
                'country_id' => phrase('Country'),
                'country' => phrase('Country'),
                'language_id' => phrase('Language'),
                'language' => phrase('Language'),
                'group_id' => phrase('Group'),
                'group_name' => phrase('Group')
            ])
            ->mergeContent('{{ first_name }} {{ last_name }}', phrase('Full Name'))
            ->mergeField('first_name, last_name')
            ->mergeField('username, email')
            ->mergeField('language_id, group_id')
            ->mergeField('language, group_name')
            ->mergeField('postal_code, country')
            ->mergeField('registered_date, last_login')
            ->orderBy('user_id', 'ASC')
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

        $query = $this->model->getWhere(
            'app_groups',
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
