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

use Aksara\Laboratory\Core;

class Videos extends Core
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
            'timestamp' => 'current_timestamp',
            'status' => 'boolean'
        ])
        ->set_field('slug', 'slug', 'title')

        ->set_default([
            'author' => get_userdata('user_id')
        ])

        ->set_validation([
            'title' => 'required|unique[' . $this->_table . '.title.id.' . $this->request->getGet('id') . ']',
            'description' => 'required',
            'video_url' => 'required|valid_url',
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

        ->set_placeholder([
            'description' => phrase('Video description'),
            'video_url' => 'e.g: https://www.youtube.com/watch?v=ZyTjlNkFfd4'
        ])

        ->set_tooltip([
            'video_url' => phrase('You can use YouTube, Facebook, Vimeo, DailyMotion and Twitch video')
        ])

        ->render($this->_table);
    }
}
