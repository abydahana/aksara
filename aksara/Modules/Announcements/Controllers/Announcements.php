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

namespace Aksara\Modules\Announcements\Controllers;

use Aksara\Laboratory\Core;

class Announcements extends Core
{
    private string $_table = 'announcements';

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->setTitle(phrase('Announcements'))
        ->setDescription(phrase('Stay updated with our latest announcements.'), (service('request')->getGet('q') ? phrase('No announcement is found related to your keywords.') : phrase('No announcement is available at the moment.')))
        ->setIcon('mdi mdi-bullhorn-outline')
        ->setPrimary('announcements_slug')

        ->where([
            'status' => 1,
            'end_date > ' => date('Y-m-d')
        ])

        ->limit(10)

        ->orderBy('end_date', 'DESC')
        ->orderBy('(CASE WHEN language_id = ' . get_userdata('language_id') . ' THEN 1 ELSE 2 END)', 'ASC')

        ->render($this->_table);
    }
}
