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

class Read extends \Aksara\Laboratory\Core
{
    private $_table = 'blogs';

    public function __construct()
    {
        parent::__construct();

        $this->searchable(false);
    }

    public function index($category = null, $slug = null)
    {
        $this->set_title('{{ post_title }}', phrase('No post were found!'))
        ->set_description('{{ post_excerpt }}', phrase('The post you requested was not found or already been archived.'))
        ->set_icon('mdi mdi-newspaper')
        ->set_output([
            /* category detail */
            'category' => $this->model->get_where(
                'blogs__categories',
                [
                    'category_slug' => $category
                ],
                1
            )
            ->row(),

            /* get similar categories */
            'categories' => $this->_get_categories(),

            /* get similar articles */
            'similar' => $this->_get_similar($category, $slug)
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
        ->group_start()
        ->where('blogs.post_slug', $slug)
        ->or_where('blogs.post_id', service('request')->getGet('post_id') ?? 0)
        ->group_end()
        ->where('status', 1)
        ->order_by('(CASE WHEN blogs.language_id = ' . get_userdata('language_id') . ' THEN 1 ELSE 2 END)', 'ASC')
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
        ->order_by('total_data', 'DESC')
        ->group_by('blogs.language_id, category_id, category_slug, category_title, category_description, category_image')
        ->order_by('(CASE WHEN blogs.language_id = ' . get_userdata('language_id') . ' THEN 1 ELSE 2 END)', 'ASC')
        ->get_where(
            'blogs__categories',
            [
                'blogs.status' => 1
            ]
        )
        ->result();

        return $query;
    }

    private function _get_similar($category = 0, $slug = '')
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
        ->order_by('blogs.updated_timestamp', 'DESC')
        ->order_by('(CASE WHEN blogs.language_id = ' . get_userdata('language_id') . ' THEN 1 ELSE 2 END)', 'ASC')
        ->limit(10)
        ->get_where(
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
}
