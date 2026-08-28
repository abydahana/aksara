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

namespace Aksara\Modules\Galleries\Controllers;

use Aksara\Laboratory\Core;

class Galleries extends Core
{
    private string $_table = 'galleries';

    public function __construct()
    {
        parent::__construct();

        if ($this->request->getGet('gallery_slug')) {
            return throw_exception(301, null, go_to($this->request->getGet('gallery_slug')));
        }
    }

    public function index()
    {
        $query = htmlspecialchars((string) ($this->request->getGet('q') ?? $this->request->getGet('keyword') ?? $this->request->getGet('keywords') ?? ''));

        if ($query) {
            $this->groupStart()
                ->like('galleries.gallery_title', $query)
                ->orLike('galleries.gallery_description', $query)
                ->groupEnd();
        }

        $this->setTitle(phrase('Galleries'))
        ->setDescription(phrase('Our gallery activities'), (service('request')->getGet('q') ? phrase('No albums were found related to your keywords.') : phrase('No albums are available at the moment.')))
        ->setIcon('mdi mdi-folder-multiple-image')
        ->setPrimary('gallery_slug')
        ->orderBy('gallery_id', 'DESC')
        ->where('status', 1)
        ->limit(10)
        ->render($this->_table);
    }
}
