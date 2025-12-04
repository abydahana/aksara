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

class Blogs extends \Aksara\Laboratory\Core
{
    public function __construct()
    {
        parent::__construct();

        $this->searchable(false);
    }

    public function index()
    {
        $this->set_title(phrase('Our News Updates'))
        ->set_description(phrase('Follow an update from us'))
        ->set_icon('mdi mdi-newspaper')
        ->set_output([
            /* get highlighed articles */
            'spotlight' => $this->_get_spotlight(),

            /* get latest post by categories */
            'articles' => $this->_get_articles(),

            /* get latest post */
            'latest' => $this->_get_latest()
        ])

        ->render();
    }

    private function _get_spotlight()
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
        ->order_by('updated_timestamp', 'DESC')
        ->order_by('(CASE WHEN blogs.language_id = ' . get_userdata('language_id') . ' THEN 1 ELSE 2 END)', 'ASC')
        ->get_where(
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

    private function _get_articles()
    {
        $query = $this->model->get_where(
            'blogs__categories',
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
                    app__users.username,
                    app__users.first_name,
                    app__users.last_name,
                    app__users.photo
                ')
                ->join(
                    'app__users',
                    'app__users.user_id = blogs.author'
                )
                ->order_by('updated_timestamp', 'DESC')
                ->order_by('(CASE WHEN blogs.language_id = ' . get_userdata('language_id') . ' THEN 1 ELSE 2 END)', 'ASC')
                ->get_where(
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

    private function _get_latest()
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
        ->order_by('blogs.post_id', 'DESC')
        ->get_where(
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
