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

namespace Aksara\Modules\Administrative\Controllers\Account;

class Edit extends \Aksara\Laboratory\Core
{
    private $_table = 'app__users';

    public function __construct()
    {
        parent::__construct();

        $this->restrict_on_demo();

        $this->parent_module('administrative/account');

        $this->set_permission();
        $this->set_theme('backend');

        $this->searchable(false);

        $this->set_method('update');
    }

    public function index()
    {
        if (get_setting('username_changes') || ! get_userdata('username')) {
            $username_required = 'required|';

            $this->field_order('photo, first_name, last_name, email, username, password, phone, address, postal_code, country_id, language_id');
            $this->merge_field('email, username');
        } else {
            $username_required = null;

            $this->unset_field('username');
            $this->field_order('photo, first_name, last_name, email, phone, password, address, postal_code, country_id, language_id');
            $this->merge_field('email, phone');
        }

        $this->set_title(phrase('Account Settings'))
        ->set_icon('mdi mdi-account-edit')
        ->set_upload_path('users')
        ->unset_field('user_id, group_id, registered_date, last_login, status')
        ->set_field([
            'bio' => 'textarea',
            'address' => 'textarea',
            'photo' => 'image',
            'password' => 'password'
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
            '{{ app__countries.country AS country_name }}',
            [
                'app__countries.status' => 1
            ]
        )
        ->merge_field('first_name, last_name')
        ->merge_field('country_id, language_id')
        ->set_validation([
            'first_name' => 'required|max_length[32]',
            'last_name' => 'max_length[32]',
            'email' => 'required|valid_email|unique[app__users.email.user_id.' . get_userdata('user_id') . ']',
            'username' => $username_required . 'alpha_dash|unique[app__users.username.user_id.' . get_userdata('user_id') . ']',
            'bio' => 'string',
            'address' => 'string',
            'language_id' => 'required'
        ])
        ->where('user_id', get_userdata('user_id'))
        ->set_alias([
            'photo' => phrase('Photo'),
            'first_name' => phrase('First Name'),
            'last_name' => phrase('Last Name'),
            'email' => phrase('Email'),
            'phone' => phrase('Phone'),
            'password' => phrase('Password'),
            'address' => phrase('Address'),
            'postal_code' => phrase('Postal Code'),
            'country_id' => phrase('Country'),
            'language_id' => phrase('Language'),
            'bio' => phrase('Biography')
        ])

        ->render($this->_table);
    }

    public function after_update()
    {
        $language = $this->model->select('code')->get_where(
            'app__languages',
            [
                'id' => service('request')->getPost('language')
            ]
        )
        ->row('code');

        set_userdata([
            'username' => (service('request')->getPost('username') ? service('request')->getPost('username') : get_userdata('username')),
            'language' => $language
        ]);
    }
}
