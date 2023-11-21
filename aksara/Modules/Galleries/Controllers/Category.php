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

class Category extends \Aksara\Laboratory\Core
{
    private $_table = 'galleries';

    private $_primary;

    public function __construct()
    {
        parent::__construct();

        $this->searchable(false);
        $this->limit(10);

        $this->_primary = service('request')->getGet('gallery_id');
    }

    public function index($slug = null)
    {
        if (! $slug && service('request')->getGet('gallery_slug')) {
            $slug = service('request')->getGet('gallery_slug');
        }

        $this->set_title('{{ gallery_title }}', phrase('Album not found!'))
        ->set_description('{{ gallery_description }}')
        ->set_icon('mdi mdi-image')
        ->where('gallery_slug', $slug)
        ->limit(1)

        ->render($this->_table);
    }
}
