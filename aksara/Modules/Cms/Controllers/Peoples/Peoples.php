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

namespace Aksara\Modules\Cms\Controllers\Peoples;

use Aksara\Laboratory\Core;

class Peoples extends Core
{
    private $_table = 'peoples';

    public function __construct()
    {
        parent::__construct();

        $this->restrict_on_demo();

        $this->set_permission();
        $this->set_theme('backend');

        $this->unset_method('clone');
    }

    public function index()
    {
        $this->set_title(phrase('Peoples'))
        ->set_icon('mdi mdi-account-group-outline')
        ->unset_column('people_id, people_slug, biography, instagram, facebook, twitter')
        ->unset_field('people_id')
        ->unset_view('people_id')
        ->set_field([
            'biography' => 'textarea',
            'email' => 'email',
            'photo' => 'image',
            'status' => 'boolean'
        ])
        ->set_field('people_slug', 'slug', 'full_name')
        ->set_field('full_name', 'hyperlink', 'peoples', ['people_slug' => 'people_slug'], true)

        ->add_button('../../peoples/user', phrase('View People'), 'btn-success', 'mdi mdi-eye', ['people_slug' => 'people_slug'], true)

        ->column_order('photo, full_name')
        ->field_order('photo')
        ->set_validation([
            'first_name' => 'required|string',
            'last_name' => 'string',
            'people_slug' => 'max_length[64]|unique[' . $this->_table . '.people_slug.people_id.' . $this->request->getGet('people_id') . ']',
            'status' => 'boolean'
        ])
        ->merge_field('first_name, last_name')
        ->merge_field('mobile, instagram')
        ->merge_field('facebook, twitter')
        ->merge_content('{{ first_name }} {{ last_name }}', phrase('Full Name'))
        ->field_size([
            'mobile' => 'col-md-6',
            'instagram' => 'col-md-6',
            'facebook' => 'col-md-6',
            'twitter' => 'col-md-6'
        ])
        ->set_alias([
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
