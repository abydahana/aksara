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

class Blogs extends Core
{
    public function __construct()
    {
        parent::__construct();

        $this->searchable(false);
    }

    public function index()
    {
        $this->setTitle(phrase('Our News Updates'))
        ->setDescription(phrase('Follow an update from us'))
        ->setIcon('mdi mdi-newspaper')
        ->setOutput([
            /* get highlighed articles */
            'spotlight' => $this->_getSpotlight(),

            /* get latest post by categories */
            'articles' => $this->_getArticles(),

            /* get latest post */
            'latest' => $this->_getLatest()
        ])

        ->render();
    }

    private function _getSpotlight()
    {
        $query = $this->model->select('
            blogs.post_id,
            blogs.post_slug,
            blogs.post_title,
            blogs.post_excerpt,
            blogs.featured_image,
            blogs.updated_timestamp,
            blogs_categories.category_title,
            blogs_categories.category_slug,
            app_users.username,
            app_users.first_name,
            app_users.last_name,
            app_users.photo
        ')
        ->join(
            'blogs_categories',
            'blogs_categories.category_id = blogs.post_category'
        )
        ->join(
            'app_users',
            'app_users.user_id = blogs.author'
        )
        ->orderBy('updated_timestamp', 'DESC')
        ->orderBy('(CASE WHEN blogs.language_id = ' . get_userdata('language_id') . ' THEN 1 ELSE 2 END)', 'ASC')
        ->getWhere(
            'blogs',
            [
                'blogs.status' => 1,
                'blogs.headline' => 1
            ],
            9
        )
        ->result();

        return $query;
    }

    private function _getArticles()
    {
        $query = $this->model->getWhere(
            'blogs_categories',
            [
                'status' => 1
            ]
        )
        ->result();

        $output = [];

        if ($query) {
            foreach ($query as $key => $val) {
                $articles = $this->model->select('
                    blogs.post_id,
                    blogs.post_slug,
                    blogs.post_title,
                    blogs.post_excerpt,
                    blogs.featured_image,
                    blogs.updated_timestamp,
                    app_users.username,
                    app_users.first_name,
                    app_users.last_name,
                    app_users.photo
                ')
                ->join(
                    'app_users',
                    'app_users.user_id = blogs.author'
                )
                ->orderBy('updated_timestamp', 'DESC')
                ->orderBy('(CASE WHEN blogs.language_id = ' . get_userdata('language_id') . ' THEN 1 ELSE 2 END)', 'ASC')
                ->getWhere(
                    'blogs',
                    [
                        'blogs.post_category' => $val->category_id,
                        'blogs.status' => 1
                    ],
                    6
                )
                ->result();

                $posts = [];

                if ($articles) {
                    foreach ($articles as $_key => $_val) {
                        $posts[] = $_val;
                    }
                }

                $output[] = [
                    'category_id' => $val->category_id,
                    'category_title' => $val->category_title,
                    'category_slug' => $val->category_slug,
                    'category_description' => $val->category_description,
                    'category_image' => $val->category_image,
                    'posts' => $posts
                ];
            }
        }

        return $output;
    }

    private function _getLatest()
    {
        $query = $this->model->select('
            blogs.post_id,
            blogs.post_slug,
            blogs.post_title,
            blogs.post_excerpt,
            blogs.featured_image,
            blogs.updated_timestamp,
            blogs_categories.category_title,
            blogs_categories.category_slug,
            app_users.username,
            app_users.first_name,
            app_users.last_name,
            app_users.photo
        ')
        ->join(
            'blogs_categories',
            'blogs_categories.category_id = blogs.post_category'
        )
        ->join(
            'app_users',
            'app_users.user_id = blogs.author'
        )
        ->orderBy('blogs.post_id', 'DESC')
        ->getWhere(
            'blogs',
            [
                'blogs.status' => 1
            ],
            5
        )
        ->result();

        return $query;
    }
}
