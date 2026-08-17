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

namespace Aksara\Modules\CMS\Controllers\Videos;

use Aksara\Laboratory\Core;

class Videos extends Core
{
    private string $_table = 'videos';

    public function __construct()
    {
        parent::__construct();

        $this->restrictOnDemo();

        $this->setPermission();
        $this->setTheme('backend');
    }

    public function index()
    {
        $this->setTitle(phrase('Videos'))
        ->setIcon('mdi mdi-youtube')
        ->unsetColumn('id, slug, description, video_url')
        ->unsetField('id, created_by')
        ->unsetView('id')

        ->setRelation(
            'created_by',
            'app_users.user_id',
            '{{app_users.first_name}} {{app_users.last_name}}'
        )

        ->setField([
            'cover' => 'image',
            'description' => 'textarea',
            'featured' => 'boolean',
            'status' => 'boolean'
        ])
        ->setField('slug', 'slug', 'title')
        ->setField('created_by', 'hyperlink', 'user', ['user_id' => 'user_id'], true)

        ->setValidation([
            'title' => 'required|unique[' . $this->_table . '.title.id.' . $this->request->getGet('id') . ']',
            'description' => 'required',
            'video_url' => 'required|valid_url',
            'featured' => 'boolean',
            'status' => 'boolean'
        ])

        ->setAlias([
            'title' => phrase('Title'),
            'description' => phrase('Description'),
            'video_url' => phrase('Video URL'),
            'featured' => phrase('Featured'),
            'status' => phrase('Status'),
            'created_at' => phrase('Created At'),
            'created_by' => phrase('Author')
        ])

        ->setPlaceholder([
            'description' => phrase('Video description'),
            'video_url' => 'e.g: https://www.youtube.com/watch?v=ZyTjlNkFfd4'
        ])

        ->setTooltip([
            'video_url' => phrase('You can use YouTube, Facebook, Vimeo, DailyMotion, and Twitch videos.')
        ])

        ->render($this->_table);
    }
}
