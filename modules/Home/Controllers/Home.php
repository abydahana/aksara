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

namespace Modules\Home\Controllers;

class Home extends \Aksara\Laboratory\Core
{
    public function index()
    {
        $this->set_title(get_setting('app_name'))
        ->set_description(get_setting('app_description'))
        ->set_output([
            // Get today statistics
            'statistics' => $this->_get_statistics(),

            // Get highlighed articles
            'spotlight' => $this->_get_spotlight(),

            // Get highlighed articles
            'categories' => $this->_get_categories(),

            // Get latest articles
            'articles' => $this->_get_articles(),

            // Get the latest galleries
            'galleries' => $this->_get_galleries(),

            // Get the latest peoples
            'peoples' => $this->model->get_where(
                'peoples',
                [
                    'status' => 1
                ],
                4
            )
            ->result(),

            'testimonials' => $this->model->get_where(
                'testimonials',
                [
                    'status' => 1
                ],
                6
            )
            ->result()
        ])
        ->render();
    }

    private function _get_statistics()
    {
        $query = $this->model->get_where(
            'app__log_visitors',
            [
                'timestamp >= ' => date('Y-m-d H:i:s', strtotime('-1 year')),
                'timestamp <= ' => date('Y-m-d H:i:s')
            ]
        )
        ->result();

        $output = [
            'yearly' => 0,
            'monthly' => 0,
            'weekly' => 0,
            'daily' => 0,
            'posts' => $this->model->get('blogs')->count_all_results(),
            'galleries' => $this->model->get('galleries')->count_all_results(),
            'comments' => $this->model->get('post__comments')->count_all_results()
        ];

        if ($query) {
            foreach ($query as $key => $val) {
                if (strtotime($val->timestamp) >= strtotime('-1 day')) {
                    $output['daily']++;
                }

                if (strtotime($val->timestamp) >= strtotime('-1 week')) {
                    $output['weekly']++;
                }

                if (strtotime($val->timestamp) >= strtotime('-1 month')) {
                    $output['monthly']++;
                }

                if (strtotime($val->timestamp) >= strtotime('-1 year')) {
                    $output['yearly']++;
                }
            }
        }

        return $output;
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
        ->get_where(
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

    private function _get_articles()
    {
        $query = $this->model->select('
            blogs.post_id,
            blogs.post_slug,
            blogs.post_title,
            blogs.post_excerpt,
            blogs.featured_image,
            blogs.updated_timestamp,
            blogs__categories.category_slug,
            blogs__categories.category_title,
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
        ->order_by('created_timestamp', 'DESC')
        ->get_where(
            'blogs',
            [
                'blogs.status' => 1
            ],
            10
        )
        ->result();

        return $query;
    }

    private function _get_categories()
    {
        $query = $this->model->select('
            blogs__categories.*,
            (SELECT COUNT(post_id) FROM blogs WHERE post_category = blogs__categories.category_id) AS post_total
        ')
        ->order_by('post_total', 'DESC')
        ->get_where(
            'blogs__categories',
            [
                'status' => 1
            ],
            4
        )
        ->result();

        return $query;
    }

    private function _get_galleries()
    {
        $query = $this->model->get_where(
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
