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

class Tags extends Core
{
    private int $_languageId;
    private ?string $_keywords;

    public function __construct()
    {
        parent::__construct();

        $this->_languageId = get_userdata('language_id') ?? get_setting('app_language') ?? 1;
        $this->_keywords = ($this->request->getGet('q') || $this->request->getPost('q') ? htmlspecialchars(($this->request->getPost('q') ? $this->request->getPost('q') : $this->request->getGet('q'))) : null);
    }

    public function index()
    {
        $this->setTitle('#' . $this->_keywords)
        ->setDescription(phrase('Post tagged with') . ' #' . $this->_keywords)
        ->setIcon('mdi mdi-pound')

        ->setOutput([
            'keywords' => $this->_keywords,

            // List of category
            'categories' => $this->_getCategories(),

            // Latest post
            'latest' => $this->_getLatest()
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

        ->like('blogs.post_tags', $this->_keywords)

        ->where([
            'blogs.status' => 1
        ])

        ->limit(24)

        // Order by current language first
        ->orderBy('(CASE WHEN blogs.language_id = ' . $this->_languageId . ' THEN 1 ELSE 2 END)', 'ASC')

        // Normal ordering
        ->orderBy([
            'blogs.updated_at' => 'DESC',
            'blogs.created_at' => 'DESC'
        ])

        ->render('blogs');
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
            'blogs.status' => 1,
            'blogs.language_id' => $this->_languageId
        ])
        ->orderBy('category_title', 'RANDOM')
        ->groupBy('blogs_categories.category_id, blogs_categories.category_slug, blogs_categories.category_title, blogs_categories.category_description, blogs_categories.category_image')
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
        ->orderBy('(CASE WHEN blogs.language_id = ' . $this->_languageId . ' THEN 1 ELSE 2 END)', 'ASC')
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
