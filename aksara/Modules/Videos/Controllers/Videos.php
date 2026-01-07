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

namespace Aksara\Modules\Videos\Controllers;

use Aksara\Laboratory\Core;

class Videos extends Core
{
    private $_table = 'videos';

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->setTitle(phrase('Videos'))
        ->setDescription(phrase('Watch our latest videos'))
        ->setIcon('mdi mdi-youtube')
        ->select('
            videos.id,
            videos.title,
            videos.description,
            videos.video_url,
            videos.timestamp,
            app__users.username,
            app__users.first_name,
            app__users.last_name,
            app__users.photo
        ')
        ->join(
            'app__users',
            'app__users.user_id = videos.author'
        )
        ->render($this->_table);
    }

    public function watch($slug = '')
    {
        $this->setMethod('read')
        ->setTitle('{{ title }}')
        ->setDescription('{{ description }}')
        ->setIcon('mdi mdi-youtube')
        ->select('
            videos.id,
            videos.title,
            videos.description,
            videos.video_url,
            videos.timestamp,
            app__users.username,
            app__users.first_name,
            app__users.last_name,
            app__users.photo
        ')
        ->join(
            'app__users',
            'app__users.user_id = videos.author'
        )
        ->where('videos.slug', $slug)
        ->limit(1)
        ->render($this->_table);
    }
}
