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

class Read extends Core
{
    private $_table = 'blogs';

    public function __construct()
    {
        parent::__construct();

        $this->searchable(false);

        helper('Aksara\Modules\Blogs\Helpers\blog');
    }

    public function index($category = null, $slug = null)
    {
        $this->setTitle('{{ post_title }}', phrase('No post were found!'))
        ->setDescription('{{ post_excerpt }}', phrase('The post you requested was not found or already been archived.'))
        ->setIcon('mdi mdi-newspaper')
        ->setOutput([
            // Category detail
            'category' => $this->model->getWhere(
                'blogs__categories',
                [
                    'category_slug' => $category
                ],
                1
            )
            ->row(),

            // Get similar categories
            'categories' => $this->_get_categories(),

            // Get similar articles
            'related' => $this->_get_related($category, $slug),

            // Read other articles
            'recommendations' => $this->_get_recommendations($category, $slug)
        ])
        ->select('
            blogs.post_slug,
            blogs.post_title,
            blogs.post_excerpt,
            blogs.post_tags,
            blogs.featured_image,
            blogs.created_timestamp,
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
        ->groupStart()
        ->where('blogs.post_slug', $slug)
        ->orWhere('blogs.post_id', $this->request->getGet('post_id') ?? 0)
        ->groupEnd()
        ->where('status', 1)
        ->orderBy('(CASE WHEN blogs.language_id = ' . get_userdata('language_id') . ' THEN 1 ELSE 2 END)', 'ASC')
        ->limit(1)

        ->render($this->_table);
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
        ->orderBy('total_data', 'DESC')
        ->groupBy('blogs.language_id, category_id, category_slug, category_title, category_description, category_image')
        ->orderBy('(CASE WHEN blogs.language_id = ' . get_userdata('language_id') . ' THEN 1 ELSE 2 END)', 'ASC')
        ->getWhere(
            'blogs__categories',
            [
                'blogs.status' => 1
            ]
        )
        ->result();

        return $query;
    }

    private function _get_related($category = 0, $slug = '')
    {
        $query = $this->model->select('
            blogs.post_slug,
            blogs.post_title,
            blogs.post_excerpt,
            blogs.featured_image,
            blogs.updated_timestamp,
            blogs__categories.category_slug,
            blogs__categories.category_title,
            blogs__categories.category_image
        ')
        ->join(
            'blogs__categories',
            'blogs__categories.category_id = blogs.post_category'
        )
        ->orderBy('blogs.updated_timestamp', 'DESC')
        ->orderBy('(CASE WHEN blogs.language_id = ' . get_userdata('language_id') . ' THEN 1 ELSE 2 END)', 'ASC')
        ->limit(10)
        ->getWhere(
            'blogs',
            [
                'category_slug' => ($category ? $category : ''),
                'post_slug != ' => ($slug ? $slug : ''),
                'blogs.status' => 1
            ]
        )
        ->result();

        return $query;
    }

    private function _get_recommendations($category = 0, $slug = '')
    {
        $postTags = $this->model->select('
            blogs.post_tags
        ')
        ->join(
            'blogs__categories',
            'blogs__categories.category_id = blogs.post_category'
        )
        ->orderBy('(CASE WHEN blogs.language_id = ' . get_userdata('language_id') . ' THEN 1 ELSE 2 END)', 'ASC')
        ->getWhere(
            'blogs',
            [
                'category_slug' => ($category ? $category : ''),
                'post_slug' => ($slug ? $slug : ''),
                'blogs.status' => 1
            ],
            1
        )
        ->row('post_tags');

        $postTags = array_map('trim', explode(',', $postTags));

        if ($postTags) {
            $this->model->groupStart();

            foreach ($postTags as $key => $tag) {
                if ($key) {
                    $this->model->orLike('post_tags', $tag);
                } else {
                    $this->model->like('post_tags', $tag);
                }
            }

            $this->model->groupEnd();
        }

        $query = $this->model->select('
            blogs.post_title,
            blogs.post_slug,
            blogs__categories.category_slug
        ')
        ->join(
            'blogs__categories',
            'blogs__categories.category_id = blogs.post_category'
        )
        ->orderBy('blogs.post_title', 'RANDOM')
        ->orderBy('(CASE WHEN blogs.language_id = ' . get_userdata('language_id') . ' THEN 1 ELSE 2 END)', 'ASC')
        ->limit(5)
        ->getWhere(
            'blogs',
            [
                'blogs.post_slug != ' => ($slug ? $slug : ''),
                'blogs.status' => 1
            ]
        )
        ->result();

        $output = [];

        if ($query) {
            foreach ($query as $key => $val) {
                $output[] = [
                    'link' => base_url('blogs/' . $val->category_slug . '/' . $val->post_slug),
                    'title' => $val->post_title
                ];
            }
        }

        return $output;
    }
}
