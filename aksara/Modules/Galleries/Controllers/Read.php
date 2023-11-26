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

class Read extends \Aksara\Laboratory\Core
{
    private $_table = 'galleries';

    public function __construct()
    {
        parent::__construct();

        $this->searchable(false);
    }

    public function index($category = null, $slug = null)
    {
        $check = $this->model->get_where(
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

        $this->set_title('{{ gallery_title }}', phrase('Album not found!'))
        ->set_description('{{ gallery_description }}')
        ->set_icon('mdi mdi-image')
        ->set_output(
            'similar',
            $this->model
            ->select('gallery_images')
            ->get_where(
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
