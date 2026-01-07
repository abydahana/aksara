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

namespace Aksara\Modules\Announcements\Controllers;

use Aksara\Laboratory\Core;

class Announcements extends Core
{
    private $_table = 'announcements';

    public function __construct()
    {
        parent::__construct();

        $this->searchable(false);
        $this->limit(10);
    }

    public function index()
    {
        $this->setTitle(phrase('Announcements'))
        ->setDescription(phrase('Announcements'))
        ->setIcon('mdi mdi-bullhorn-outline')
        ->setPrimary('announcements_slug')

        ->where([
            'status' => 1,
            'end_date > ' => date('Y-m-d')
        ])

        ->orderBy('end_date', 'DESC')
        ->orderBy('(CASE WHEN language_id = ' . get_userdata('language_id') . ' THEN 1 ELSE 2 END)', 'ASC')

        ->render($this->_table);
    }
}
