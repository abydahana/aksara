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

namespace Modules\Home\Controllers;

use Aksara\Laboratory\Core;

class Home extends Core
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->setTitle(phrase('Welcome'))
        ->setDescription(get_setting('app_description'))
        ->setOutput([
            // Get today statistics
            'statistics' => $this->_getStatistics(),

            // Get highlighed articles
            'spotlight' => $this->_getSpotlight(),

            // Get highlighed articles
            'categories' => $this->_getCategories(),

            // Get latest articles
            'articles' => $this->_getArticles(),

            // Get the latest galleries
            'galleries' => $this->_getGalleries(),

            // Get the latest peoples
            'peoples' => $this->model->getWhere(
                'peoples',
                [
                    'status' => 1
                ],
                4
            )
            ->result(),

            'testimonials' => $this->model->getWhere(
                'testimonials',
                [
                    'status' => 1
                ],
                6
            )
            ->result(),
        ])
        ->render();
    }

    private function _getStatistics()
    {
        $query = $this->model->getWhere(
            'app_stats',
            []
        )
        ->row();

        $output = [
            'yearly' => 0,
            'monthly' => 0,
            'weekly' => 0,
            'daily' => 0,
            'posts' => $this->model->countAllResults('blogs'),
            'galleries' => $this->model->countAllResults('galleries'),
            'comments' => $this->model->countAllResults('post_comments')
        ];

        if ($query) {
            $output['daily'] = $query->daily_visits;
            $output['weekly'] = $query->weekly_visits;
            $output['monthly'] = $query->monthly_visits;
            $output['yearly'] = $query->yearly_visits;
        }

        return $output;
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
        ->getWhere(
            'blogs',
            [
                'blogs.status' => 1,
                'blogs.headline' => 1
            ],
            9
        )
        ->result();

        $output = [];

        if ($query) {
            foreach ($query as $key => $val) {
                $output[] = $val;
            }
        }

        return $output;
    }

    private function _getArticles()
    {
        $query = $this->model->select('
            blogs.post_id,
            blogs.post_slug,
            blogs.post_title,
            blogs.post_excerpt,
            blogs.featured_image,
            blogs.updated_timestamp,
            blogs_categories.category_slug,
            blogs_categories.category_title,
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
        ->orderBy('created_timestamp', 'DESC')
        ->getWhere(
            'blogs',
            [
                'blogs.status' => 1
            ],
            10
        )
        ->result();

        return $query;
    }

    private function _getCategories()
    {
        $query = $this->model->select('
            blogs_categories.*,
            (SELECT COUNT(post_id) FROM blogs WHERE post_category = blogs_categories.category_id) AS post_total
        ')
        ->orderBy('post_total', 'DESC')
        ->getWhere(
            'blogs_categories',
            [
                'status' => 1
            ],
            4
        )
        ->result();

        return $query;
    }

    private function _getGalleries()
    {
        $query = $this->model->getWhere(
            'galleries',
            [
                'status' => 1
            ],
            4
        )
        ->result();

        return $query;
    }
}
