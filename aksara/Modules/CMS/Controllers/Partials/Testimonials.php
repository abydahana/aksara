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

namespace Aksara\Modules\CMS\Controllers\Partials;

use Aksara\Laboratory\Core;

class Testimonials extends Core
{
    private string $_table = 'testimonials';

    public function __construct()
    {
        parent::__construct();

        $this->restrictOnDemo();
        $this->setPermission();
        $this->setTheme('backend');
        $this->setUploadPath('testimonials');
    }

    public function index()
    {
        $this->setTitle(phrase('Testimonials'))
        ->setIcon('mdi mdi-comment-account-outline')
        ->unsetColumn('testimonial_id')
        ->unsetField('testimonial_id, created_by')
        ->unsetView('testimonial_id')

        ->setRelation(
            'created_by',
            'app_users.user_id',
            '{{app_users.first_name}} {{app_users.last_name}}'
        )

        ->setField([
            'photo' => 'image',
            'testimonial_content' => 'textarea',
            'rating' => 'range',
            'status' => 'boolean'
        ])
        ->setField('created_by', 'hyperlink', 'user', ['user_id' => 'user_id'], true)
        ->setAttribute('rating', 'min="0" max="5" step="1"')

        ->setValidation([
            'first_name' => 'required|string',
            'last_name' => 'string',
            'testimonial_content' => 'required|string',
            'rating' => 'numeric|greater_than_equal_to[0]|less_than_equal_to[5]',
            'status' => 'boolean'
        ])
        ->setAlias([
            'first_name' => phrase('First Name'),
            'last_name' => phrase('Last Name'),
            'testimonial_content' => phrase('Testimony'),
            'rating' => phrase('Rating'),
            'status' => phrase('Status'),
            'created_at' => phrase('Created At'),
            'created_by' => phrase('Created By')
        ])

        ->render($this->_table);
    }
}
