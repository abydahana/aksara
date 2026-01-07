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

use Aksara\Laboratory\Core;

class Edit extends Core
{
    private $_table = 'app__users';

    public function __construct()
    {
        parent::__construct();

        $this->restrictOnDemo();

        $this->parentModule('administrative/account');

        $this->setPermission();
        $this->setTheme('backend');

        $this->searchable(false);

        $this->setMethod('update');
    }

    public function index()
    {
        if (get_setting('username_changes') || ! get_userdata('username')) {
            $usernameRequired = 'required|';

            $this->fieldOrder('photo, first_name, last_name, email, username, password, phone, address, postal_code, country_id, language_id');
            $this->mergeField('email, username');
        } else {
            $usernameRequired = null;

            $this->unsetField('username');
            $this->fieldOrder('photo, first_name, last_name, email, phone, password, address, postal_code, country_id, language_id');
            $this->mergeField('email, phone');
        }

        $this->setTitle(phrase('Account Settings'))
        ->setIcon('mdi mdi-account-edit')
        ->setUploadPath('users')
        ->unsetField('user_id, group_id, registered_date, last_login, status')
        ->setField([
            'bio' => 'textarea',
            'address' => 'textarea',
            'photo' => 'image',
            'password' => 'password'
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
            'app__languages.id',
            '{{ app__languages.language }}',
            [
                'app__languages.status' => 1
            ]
        )
        ->setRelation(
            'country_id',
            'app__countries.id',
            '{{ app__countries.country AS country_name }}',
            [
                'app__countries.status' => 1
            ]
        )
        ->mergeField('first_name, last_name')
        ->mergeField('country_id, language_id')
        ->setValidation([
            'first_name' => 'required|max_length[32]',
            'last_name' => 'max_length[32]',
            'email' => 'required|valid_email|unique[app__users.email.user_id.' . get_userdata('user_id') . ']',
            'username' => $usernameRequired . 'alpha_dash|unique[app__users.username.user_id.' . get_userdata('user_id') . ']',
            'bio' => 'string',
            'address' => 'string',
            'language_id' => 'required'
        ])
        ->where('user_id', get_userdata('user_id'))
        ->setAlias([
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

    public function afterUpdate()
    {
        $language = $this->model->select('code')->getWhere(
            'app__languages',
            [
                'id' => $this->request->getPost('language')
            ]
        )
        ->row('code');

        set_userdata([
            'username' => ($this->request->getPost('username') ? $this->request->getPost('username') : get_userdata('username')),
            'language' => $language
        ]);
    }
}
