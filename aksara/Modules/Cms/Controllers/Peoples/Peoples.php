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

namespace Aksara\Modules\CMS\Controllers\Peoples;

use Aksara\Laboratory\Core;

class Peoples extends Core
{
    private $_table = 'peoples';

    public function __construct()
    {
        parent::__construct();

        $this->restrictOnDemo();

        $this->setPermission();
        $this->setTheme('backend');

        $this->unsetMethod('clone');
    }

    public function index()
    {
        $this->setTitle(phrase('Peoples'))
        ->setIcon('mdi mdi-account-group-outline')
        ->unsetColumn('people_id, people_slug, biography, instagram, facebook, twitter')
        ->unsetField('people_id')
        ->unsetView('people_id')
        ->setField([
            'biography' => 'textarea',
            'email' => 'email',
            'photo' => 'image',
            'status' => 'boolean'
        ])
        ->setField('people_slug', 'slug', 'full_name')
        ->setField('full_name', 'hyperlink', 'peoples', ['people_slug' => 'people_slug'], true)

        ->addButton('../../peoples/user', phrase('View People'), 'btn-success', 'mdi mdi-eye', ['people_slug' => 'people_slug'], true)

        ->columnOrder('photo, full_name')
        ->fieldOrder('photo')
        ->setValidation([
            'first_name' => 'required|string',
            'last_name' => 'string',
            'people_slug' => 'max_length[64]|unique[' . $this->_table . '.people_slug.people_id.' . $this->request->getGet('people_id') . ']',
            'status' => 'boolean'
        ])
        ->mergeField('first_name, last_name')
        ->mergeField('mobile, instagram')
        ->mergeField('facebook, twitter')
        ->mergeContent('{{ first_name }} {{ last_name }}', phrase('Full Name'))
        ->fieldSize([
            'mobile' => 'col-md-6',
            'instagram' => 'col-md-6',
            'facebook' => 'col-md-6',
            'twitter' => 'col-md-6'
        ])
        ->setAlias([
            'photo' => phrase('Photo'),
            'first_name' => phrase('First Name'),
            'last_name' => phrase('Last Name'),
            'people_slug' => phrase('Slug'),
            'position' => phrase('Position'),
            'mobile' => phrase('Mobile'),
            'email' => phrase('Email'),
            'instagram' => phrase('Instagram Profile URL'),
            'facebook' => phrase('Facebook Profile URL'),
            'twitter' => phrase('X Profile URL'),
            'biography' => phrase('Biography'),
            'status' => phrase('Status')
        ])

        ->render($this->_table);
    }
}
