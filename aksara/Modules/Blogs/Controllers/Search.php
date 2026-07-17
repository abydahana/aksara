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

class Search extends Core
{
    private string $_table = 'blogs';
    private ?string $_keywords;

    public function __construct()
    {
        parent::__construct();

        $this->_keywords = htmlspecialchars($this->request->getGet('q') ?? $this->request->getPost('q') ?? '');
    }

    public function index()
    {
        if ($this->request->getGet('category')) {
            $this->where('blogs_categories.category_slug', $this->request->getGet('category'));
        }

        if ($this->_keywords) {
            $this->groupStart()
                ->like('blogs.post_title', $this->_keywords)
                ->orLike('blogs.post_excerpt', $this->_keywords)
                ->groupEnd();
        }

        $this->setTitle(phrase('Search'))
        ->setDescription(phrase('Search results for') . ' ' . ($this->_keywords ? $this->_keywords : ($this->request->getGet('category') ? '{{ category_title }}' : phrase('all'))))
        ->setIcon('mdi mdi-magnify')

        ->setOutput([
            'keywords' => $this->_keywords,

            // List of category
            'categories' => $this->_getCategories(),

            // Latest get latest post
            'latest' => $this->_getLatest()
        ])

        ->select('
            blogs.post_slug,
            blogs.post_title,
            blogs.post_excerpt,
            blogs.post_tags,
            blogs.featured_image,
            blogs.updated_timestamp,
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
            'app_users.user_id = blogs.author'
        )

        ->where([
            'blogs.status' => 1
        ])

        ->orderBy('blogs.updated_timestamp', 'DESC')
        ->orderBy('(CASE WHEN blogs.language_id = ' . get_userdata('language_id') . ' THEN 1 ELSE 2 END)', 'ASC')

        ->render($this->_table);
    }

    private function _getCategories()
    {
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
            'blogs_categories.status' => 1,
            'blogs.status' => 1
        ])
        ->orderBy('category_title', 'RANDOM')
        ->orderBy('(CASE WHEN blogs.language_id = ' . get_userdata('language_id') . ' THEN 1 ELSE 2 END)', 'ASC')
        ->groupBy('blogs.language_id, category_id, category_slug, category_title, category_description, category_image')
        ->get('blogs_categories')
        ->result();

        return $query;
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
        ->orderBy('(CASE WHEN blogs.language_id = ' . get_userdata('language_id') . ' THEN 1 ELSE 2 END)', 'ASC')
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
