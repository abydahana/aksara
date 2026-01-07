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

class Tags extends Core
{
    private $_keywords;

    public function __construct()
    {
        parent::__construct();

        $this->limit(24);

        $this->_keywords = ($this->request->getGet('q') || $this->request->getPost('q') ? htmlspecialchars(($this->request->getPost('q') ? $this->request->getPost('q') : $this->request->getGet('q'))) : null);
    }

    public function index()
    {
        $this->setTitle('#' . $this->_keywords)
        ->setDescription(phrase('Post tagged with') . ' #' . $this->_keywords)
        ->setIcon('mdi mdi-pound')

        ->setOutput([
            'keywords' => $this->_keywords,

            /* list of category */
            'categories' => $this->_get_categories(),

            /* latest post */
            'latest' => $this->_get_latest()
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

        ->like('blogs.post_tags', $this->_keywords)

        ->where([
            'blogs.status' => 1
        ])

        ->orderBy('(CASE WHEN blogs.language_id = ' . get_userdata('language_id') . ' THEN 1 ELSE 2 END)', 'ASC')

        ->render('blogs');
    }

    private function _get_categories()
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
            'blogs__categories.status' => 1,
            'blogs.status' => 1
        ])
        ->orderBy('category_title', 'RANDOM')
        ->orderBy('(CASE WHEN blogs.language_id = ' . get_userdata('language_id') . ' THEN 1 ELSE 2 END)', 'ASC')
        ->groupBy('blogs.language_id, category_id, category_slug, category_title, category_description, category_image')
        ->get('blogs__categories')
        ->result();

        return $query;
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
