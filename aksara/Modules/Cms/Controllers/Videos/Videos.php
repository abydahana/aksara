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

namespace Aksara\Modules\Cms\Controllers\Videos;

class Videos extends \Aksara\Laboratory\Core
{
    private $_table = 'videos';

    public function __construct()
    {
        parent::__construct();

        $this->restrict_on_demo();

        $this->set_permission();
        $this->set_theme('backend');
    }

    public function index()
    {
        $this->set_title(phrase('Videos'))
        ->set_icon('mdi mdi-youtube')
        ->unset_column('id, slug, video_url, author, timestamp')
        ->unset_field('id, author, timestamp')
        ->unset_view('id, author, timestamp')

        ->set_field([
            'cover' => 'image',
            'description' => 'textarea',
            'featured' => 'boolean',
            'status' => 'boolean'
        ])
        ->set_field('slug', 'to_slug', 'title')

        ->set_default([
            'author' => get_userdata('user_id'),
            'timestamp' => date('Y-m-d H:i:s')
        ])

        ->set_validation([
            'title' => 'required|callback_validate_title',
            'description' => 'required',
            'video_url' => 'valid_url',
            'featured' => 'boolean',
            'status' => 'boolean'
        ])

        ->set_alias([
            'title' => phrase('Title'),
            'description' => phrase('Description'),
            'video_url' => phrase('Video URL'),
            'featured' => phrase('Featured'),
            'status' => phrase('Status')
        ])

        ->render($this->_table);
    }
}
