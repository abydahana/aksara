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

namespace Aksara\Modules\Pages\Controllers;

use Aksara\Laboratory\Core;

class Pages extends Core
{
    private string $_table = 'pages';

    public function __construct()
    {
        parent::__construct();

        $this->searchable(false);
    }

    public function index($slug = null)
    {
        $this->setTitle('{{ page_title }}', phrase('Page not found!'))
        ->setDescription('{{ page_description }}')
        ->setIcon('mdi mdi-file-document-outline')
        ->setOutput([
            'suggestions' => $this->model->select('
                page_slug,
                page_title
            ')
            ->getWhere(
                $this->_table,
                [
                    'status' => 1,
                    'language_id' => get_userdata('language_id')
                ],
                8
            )
            ->result()
        ])
        ->groupStart()
        ->where('pages.page_slug', $slug)
        ->orWhere('pages.page_id', $this->request->getGet('page_id') ?? 0)
        ->groupEnd()
        ->where('status', 1)
        ->orderBy('(CASE WHEN pages.language_id = ' . get_userdata('language_id') . ' THEN 1 ELSE 2 END)', 'ASC')
        ->limit(1)

        ->render($this->_table);
    }

    public function notFound()
    {
        $this->setTitle(phrase('Page not found!'))
        ->setDescription(phrase('The page you requested does not exist or already been archived.'))
        ->setOutput([
            'suggestions' => $this->model->select('
                page_slug,
                page_title
            ')
            ->getWhere(
                $this->_table,
                [
                    'status' => 1
                ],
                8
            )
            ->result()
        ])
        ->setTemplate('index', '404')
        ->render();
    }
}
