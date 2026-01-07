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

use Aksara\Laboratory\Core;

class Read extends Core
{
    private $_table = 'galleries';

    public function __construct()
    {
        parent::__construct();

        $this->searchable(false);
    }

    public function index($category = null, $slug = null)
    {
        $check = $this->model->getWhere(
            $this->_table,
            [
                'galleries.gallery_slug' => $category
            ]
        )
        ->row();

        if (! $check) {
            // No album found
            return throw_exception(404, phrase('Album not found!'), current_page('../'));
        }

        $this->setTitle('{{ gallery_title }}', phrase('Album not found!'))
        ->setDescription('{{ gallery_description }}')
        ->setIcon('mdi mdi-image')
        ->setOutput(
            'similar',
            $this->model
            ->select('gallery_images')
            ->getWhere(
                $this->_table,
                [
                    'gallery_slug' => $category
                ],
                1
            )
            ->row('gallery_images')
        )
        ->select('
            galleries.*,
            app__users.first_name,
            app__users.last_name,
            app__users.username,
            app__users.photo
        ')
        ->join(
            'app__users',
            'app__users.user_id = galleries.author'
        )
        ->where([
            'galleries.gallery_slug' => $category
        ])
        ->limit(1)

        ->render($this->_table);
    }
}
