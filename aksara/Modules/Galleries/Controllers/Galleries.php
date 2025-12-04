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

namespace Aksara\Modules\Galleries\Controllers;

class Galleries extends \Aksara\Laboratory\Core
{
    private $_table = 'galleries';

    public function __construct()
    {
        parent::__construct();

        if (service('request')->getGet('gallery_slug')) {
            return throw_exception(301, null, go_to(service('request')->getGet('gallery_slug')));
        }

        $this->searchable(false);
        $this->limit(10);
    }

    public function index()
    {
        $this->set_title(phrase('Galleries'))
        ->set_description(phrase('Our gallery activities'))
        ->set_icon('mdi mdi-folder-multiple-image')
        ->set_primary('gallery_slug')
        ->order_by('gallery_id', 'DESC')
        ->where('status', 1)

        ->render($this->_table);
    }
}
