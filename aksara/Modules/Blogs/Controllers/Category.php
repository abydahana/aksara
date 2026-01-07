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

namespace Aksara\Modules\Blogs\Controllers;

use Aksara\Laboratory\Core;

class Category extends Core
{
    public function __construct()
    {
        parent::__construct();

        $this->searchable(false);
        $this->limit(24);
    }

    public function index($slug = null)
    {
        $this->setTitle('{{ category_title }}', phrase('No category were found!'))
        ->setDescription('{{ category_description }}')
        ->setIcon('mdi mdi-sitemap')

        ->setOutput([
            /* category detail */
            'category' => $this->model->getWhere(
                'blogs__categories',
                [
                    'category_slug' => $slug
                ],
                1
            )
            ->row(),

            // List of category
            'categories' => $this->_get_categories($slug),

            // Latest post
            'latest' => $this->_get_latest($slug)
        ])
        ->select('
            blogs.post_slug,
            blogs.post_title,
            blogs.post_excerpt,
            blogs.post_tags,
            blogs.featured_image,
            blogs.updated_timestamp,
            blogs__categories.category_slug,
            blogs__categories.category_title,
            blogs__categories.category_description,
            blogs__categories.category_image,
            app__users.first_name,
            app__users.last_name,
            app__users.username,
            app__users.photo
        ')
        ->join(
            'blogs__categories',
            'blogs__categories.category_id = blogs.post_category'
        )
        ->join(
            'app__users',
            'app__users.user_id = blogs.author'
        )
        ->orderBy('blogs.updated_timestamp', 'DESC')
        ->orderBy('(CASE WHEN blogs.language_id = ' . get_userdata('language_id') . ' THEN 1 ELSE 2 END)', 'ASC')
        ->where([
            'blogs__categories.category_slug' => $slug,
            'blogs.status' => 1
        ])

        ->render('blogs');
    }

    private function _get_categories($slug = null)
    {
        $query = $this->model->select('
            COUNT(blogs.post_id) AS total_data,
            blogs__categories.category_slug,
            blogs__categories.category_title,
            blogs__categories.category_description,
            blogs__categories.category_image
        ')
        ->join(
            'blogs',
            'blogs.post_category = blogs__categories.category_id'
        )
        ->where([
            'blogs__categories.category_slug !=' => $slug,
            'blogs__categories.status' => 1,
            'blogs.status' => 1
        ])
        ->orderBy('category_title', 'RANDOM')
        ->groupBy('blogs.language_id, category_id, category_slug, category_title, category_description, category_image')
        ->orderBy('(CASE WHEN blogs.language_id = ' . get_userdata('language_id') . ' THEN 1 ELSE 2 END)', 'ASC')
        ->get('blogs__categories')
        ->result();

        return $query;
    }

    private function _get_latest($slug = null)
    {
        $query = $this->model->select('
            blogs.post_id,
            blogs.post_slug,
            blogs.post_title,
            blogs.post_excerpt,
            blogs.featured_image,
            blogs.updated_timestamp,
            blogs__categories.category_title,
            blogs__categories.category_slug,
            app__users.username,
            app__users.first_name,
            app__users.last_name,
            app__users.photo
        ')
        ->join(
            'blogs__categories',
            'blogs__categories.category_id = blogs.post_category'
        )
        ->join(
            'app__users',
            'app__users.user_id = blogs.author'
        )
        ->orderBy('blogs.post_id', 'DESC')
        ->getWhere(
            'blogs',
            [
                'blogs.status' => 1,
                'blogs__categories.category_slug' => $slug
            ],
            5
        )
        ->result();

        return $query;
    }
}
