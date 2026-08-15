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

namespace Modules\Home\Models;

use Aksara\Laboratory\Model;

class HomeModel extends Model
{
    /**
     * Ideally, you can directly call the core model in the controller
     * like this:
     * $this->model->getWhere('app_stats', [])->row();
     * $this->model->countAllResults('blogs');
     * etc...
     * 
     * But because we need a complex query for the home layout,
     * separating the query like this will make the code more structured.
     * 
     * This is also used as an example of how Aksara MVC works.
     */
    public function getStatistics(): array
    {
        $query = $this->getWhere('app_stats', [])->row();

        $output = [
            'yearly' => 0,
            'monthly' => 0,
            'weekly' => 0,
            'daily' => 0,
            'posts' => $this->countAllResults('blogs'),
            'galleries' => $this->countAllResults('galleries'),
            'comments' => $this->countAllResults('post_comments')
        ];

        if ($query) {
            $output['daily'] = $query->daily_visits;
            $output['weekly'] = $query->weekly_visits;
            $output['monthly'] = $query->monthly_visits;
            $output['yearly'] = $query->yearly_visits;
        }

        return $output;
    }

    public function getSpotlight(): array
    {
        $query = $this->select('
            blogs.post_id,
            blogs.post_slug,
            blogs.post_title,
            blogs.post_excerpt,
            blogs.featured_image,
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
        ->orderBy('updated_at', 'DESC')
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

    public function getCategories()
    {
        return $this->select('
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
    }

    public function getArticles()
    {
        return $this->select('
            blogs.post_id,
            blogs.post_slug,
            blogs.post_title,
            blogs.post_excerpt,
            blogs.featured_image,
            blogs.created_at,
            blogs.updated_at,
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
            'app_users.user_id = blogs.created_by'
        )
        ->orderBy('blogs.created_at', 'DESC')
        ->getWhere(
            'blogs',
            [
                'blogs.status' => 1
            ],
            10
        )
        ->result();
    }

    public function getGalleries()
    {
        return $this->getWhere(
            'galleries',
            [
                'status' => 1
            ],
            4
        )
        ->result();
    }

    public function getPeoples()
    {
        return $this->getWhere(
            'peoples',
            [
                'status' => 1
            ],
            4
        )
        ->result();
    }

    public function getTestimonials()
    {
        return $this->select('
            testimonials.*,
            app_users.username
        ')
        ->join(
            'app_users',
            'app_users.user_id = testimonials.created_by',
            'LEFT'
        )
        ->getWhere(
            'testimonials',
            [
                'testimonials.status' => 1
            ],
            6
        )
        ->result();
    }
}
