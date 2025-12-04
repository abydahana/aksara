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

namespace Aksara\Modules\Testimonials\Controllers;

class Testimonials extends \Aksara\Laboratory\Core
{
    private $_table = 'testimonials';

    public function __construct()
    {
        parent::__construct();

        $this->searchable(false);
        $this->limit(10);
    }

    public function index()
    {
        $this->set_title(phrase('Testimonials'))
        ->set_description(phrase('What was people said about us.'))
        ->set_icon('mdi mdi-bullhorn-outline')

        ->where([
            'status' => 1
        ])

        ->order_by('timestamp', 'desc')
        ->order_by('(CASE WHEN language_id = ' . get_userdata('language_id') . ' THEN 1 ELSE 2 END)', 'ASC')

        ->render($this->_table);
    }
}
