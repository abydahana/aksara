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

class Tags extends \Aksara\Laboratory\Core
{
    private $_keywords;

    public function __construct()
    {
        parent::__construct();

        $this->limit(24);

        $this->_keywords = (service('request')->getGet('q') || service('request')->getPost('q') ? htmlspecialchars((service('request')->getPost('q') ? service('request')->getPost('q') : service('request')->getGet('q'))) : null);
    }

    public function index()
    {
        $this->set_title('#' . $this->_keywords)
        ->set_description(phrase('Post tagged with') . ' #' . $this->_keywords)
        ->set_icon('mdi mdi-pound')

        ->set_output([
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

        ->order_by('(CASE WHEN blogs.language_id = ' . get_userdata('language_id') . ' THEN 1 ELSE 2 END)', 'ASC')

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
        ->order_by('category_title', 'RANDOM')
        ->order_by('(CASE WHEN blogs.language_id = ' . get_userdata('language_id') . ' THEN 1 ELSE 2 END)', 'ASC')
        ->group_by('blogs.language_id, category_id, category_slug, category_title, category_description, category_image')
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
        ->order_by('blogs.post_id', 'DESC')
        ->order_by('(CASE WHEN blogs.language_id = ' . get_userdata('language_id') . ' THEN 1 ELSE 2 END)', 'ASC')
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
