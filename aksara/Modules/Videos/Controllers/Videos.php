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

namespace Aksara\Modules\Videos\Controllers;

use Aksara\Laboratory\Core;

class Videos extends Core
{
    private string $_table = 'videos';

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $query = htmlspecialchars((string) ($this->request->getGet('q') ?? $this->request->getGet('keyword') ?? $this->request->getGet('keywords') ?? ''));

        if ($query) {
            $this->groupStart()
                ->like('videos.title', $query)
                ->orLike('videos.description', $query)
                ->groupEnd();
        }

        $this->setTitle(phrase('Videos'))
        ->setDescription(phrase('Watch our latest videos'), (service('request')->getGet('q') ? phrase('No video is found related to your keywords.') : phrase('No video is available at the moment.')))
        ->setIcon('mdi mdi-youtube')
        ->select('
            videos.id,
            videos.title,
            videos.description,
            videos.video_url,
            videos.created_at,
            app_users.username,
            app_users.first_name,
            app_users.last_name,
            app_users.photo
        ')
        ->join(
            'app_users',
            'app_users.user_id = videos.created_by'
        )
        ->where('videos.status', 1)
        ->render($this->_table);
    }

    public function watch($slug = '')
    {
        $this->setMethod('read')
        ->setTitle('{{ title }}', phrase('No video is found!'))
        ->setDescription('{{ description }}', phrase('The video you are looking for might have been deleted or the url is invalid.'))
        ->setIcon('mdi mdi-youtube')
        ->select('
            videos.id,
            videos.title,
            videos.description,
            videos.video_url,
            videos.created_at,
            app_users.username,
            app_users.first_name,
            app_users.last_name,
            app_users.photo
        ')
        ->join(
            'app_users',
            'app_users.user_id = videos.created_by'
        )
        ->where('videos.slug', $slug)
        ->limit(1)
        ->render($this->_table);
    }
}
