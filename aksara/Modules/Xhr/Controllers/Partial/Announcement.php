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

namespace Aksara\Modules\XHR\Controllers\Partial;

use Aksara\Laboratory\Core;

class Announcement extends Core
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $query = $this->model->orderBy('announcement_id', 'desc')->getWhere(
            'announcements',
            [
                'status' => 1,
                'placement' => ($this->request->getGet('placement') ? 'backend' : 'frontend'),
                'start_date <= ' => date('Y-m-d'),
                'end_date >= ' => date('Y-m-d')
            ],
            10
        )
        ->result();

        $output = [];

        if ($query) {
            foreach ($query as $key => $val) {
                $output[] = [
                    'label' => $val->title,
                    'url' => base_url('announcements/' . $val->announcement_slug)
                ];
            }
        }

        return make_json($output);
    }
}
