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

class Category extends \Aksara\Laboratory\Core
{
    public function __construct()
    {
        parent::__construct();

        $this->searchable(false);
        $this->limit(24);
    }

    public function index($slug = null)
    {
        $this->set_title('{{ category_title }}', phrase('No category were found!'))
        ->set_description('{{ category_description }}')
        ->set_icon('mdi mdi-sitemap')

        ->set_output([
            /* category detail */
            'category' => $this->model->get_where(
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
        ->order_by('blogs.updated_timestamp', 'DESC')
        ->order_by('(CASE WHEN blogs.language_id = ' . get_userdata('language_id') . ' THEN 1 ELSE 2 END)', 'ASC')
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
        ->order_by('category_title', 'RANDOM')
        ->group_by('blogs.language_id, category_id, category_slug, category_title, category_description, category_image')
        ->order_by('(CASE WHEN blogs.language_id = ' . get_userdata('language_id') . ' THEN 1 ELSE 2 END)', 'ASC')
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
        ->order_by('blogs.post_id', 'DESC')
        ->get_where(
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
