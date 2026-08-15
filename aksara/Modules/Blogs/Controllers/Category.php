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
        $language_id = get_userdata('language_id') ?? get_setting('app_language') ?? 0;

        $this->setTitle('{{ category_title }}', phrase('No category were found!'))
        ->setDescription('{{ category_description }}')
        ->setIcon('mdi mdi-sitemap')

        ->setOutput([
            /* category detail */
            'category' => $this->model->getWhere(
                'blogs_categories',
                [
                    'category_slug' => $slug,
                    'language_id' => $language_id
                ],
                1
            )
            ->row(),

            // List of category
            'categories' => $this->_getCategories($slug),

            // Latest post
            'latest' => $this->_getLatest($slug)
        ])
        ->select('
            blogs.post_slug,
            blogs.post_title,
            blogs.post_excerpt,
            blogs.post_tags,
            blogs.featured_image,
            blogs.created_at,
            blogs.updated_at,
            blogs_categories.category_slug,
            blogs_categories.category_title,
            blogs_categories.category_description,
            blogs_categories.category_image,
            app_users.first_name,
            app_users.last_name,
            app_users.username,
            app_users.photo
        ')
        ->join(
            'blogs_categories',
            'blogs_categories.category_id = blogs.post_category'
        )
        ->join(
            'app_users',
            'app_users.user_id = blogs.created_by'
        )

        ->where([
            'blogs_categories.category_slug' => $slug,
            'blogs.status' => 1
        ])

        // Order by current language first
        ->orderBy('(CASE WHEN blogs.language_id = ' . get_userdata('language_id') . ' THEN 1 ELSE 2 END)', 'ASC')

        // Normal ordering
        ->orderBy([
            'blogs.updated_at' => 'DESC',
            'blogs.created_at' => 'DESC'
        ])

        ->render('blogs');
    }

    private function _getCategories($slug = null)
    {
        $language_id = get_userdata('language_id') ?? get_setting('app_language') ?? 0;

        $query = $this->model->select('
            COUNT(blogs.post_id) AS total_data,
            blogs_categories.category_slug,
            blogs_categories.category_title,
            blogs_categories.category_description,
            blogs_categories.category_image
        ')
        ->join(
            'blogs',
            'blogs.post_category = blogs_categories.category_id'
        )
        ->where([
            'blogs_categories.category_slug !=' => $slug,
            'blogs_categories.status' => 1,
            'blogs_categories.language_id' => $language_id,
            'blogs.status' => 1,
            'blogs.language_id' => $language_id
        ])
        ->orderBy('category_title', 'RANDOM')
        ->groupBy('blogs_categories.category_id, blogs_categories.category_slug, blogs_categories.category_title, blogs_categories.category_description, blogs_categories.category_image')
        ->get('blogs_categories')
        ->result();

        return $query;
    }

    private function _getLatest($slug = null)
    {
        $query = $this->model->select('
            blogs.post_id,
            blogs.post_slug,
            blogs.post_title,
            blogs.post_excerpt,
            blogs.featured_image,
            blogs.created_at,
            blogs.updated_at,
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
            'app_users.user_id = blogs.created_by'
        )
        ->orderBy('blogs.post_id', 'DESC')
        ->getWhere(
            'blogs',
            [
                'blogs.status' => 1,
                'blogs_categories.category_slug' => $slug
            ],
            5
        )
        ->result();

        return $query;
    }
}
