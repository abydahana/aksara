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

class Read extends Core
{
    private string $_table = 'blogs';

    public function __construct()
    {
        parent::__construct();

        $this->searchable(false);
        $this->setMethod('read');

        helper('Aksara\Modules\Blogs\Helpers\blog');
    }

    public function index($category = null, $slug = null)
    {
        if ($this->request->getGet('post_id')) {
            $post = $this->model->select('
                blogs_categories.category_slug,
                blogs.post_slug
            ')
            ->join(
                'blogs_categories',
                'blogs_categories.category_id = blogs.post_category'
            )
            ->getWhere(
                $this->_table,
                [
                    'blogs.post_id' => $this->request->getGet('post_id')
                ],
                1
            )
            ->row();

            if ($post) {
                return throw_exception(301, null, base_url('blogs/' . $post->category_slug . '/' . $post->post_slug, ['post_id' => null]), true);
            }
        }

        $this->setTitle('{{ post_title }}', phrase('No post were found!'))
        ->setDescription('{{ post_excerpt }}', phrase('The post you requested was not found or already been archived.'))
        ->setIcon('mdi mdi-newspaper')
        ->setOutput([
            // Category detail
            'category' => $this->model->getWhere(
                'blogs_categories',
                [
                    'category_slug' => $category
                ],
                1
            )
            ->row(),

            // Get similar categories
            'categories' => $this->_getCategories(),

            // Get similar articles
            'related' => $this->_getRelated($category, $slug),

            // Read other articles
            'recommendations' => $this->_getRecommendations($category, $slug)
        ])
        ->select('
            blogs.post_id,
            blogs.post_slug,
            blogs.post_title,
            blogs.post_excerpt,
            blogs.post_content,
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
        ->groupStart()
        ->where('blogs.post_slug', $slug)
        ->orWhere('blogs.post_id', $this->request->getGet('post_id') ?? 0)
        ->groupEnd()
        ->where('status', 1)
        ->orderBy('(CASE WHEN blogs.language_id = ' . get_userdata('language_id') . ' THEN 1 ELSE 2 END)', 'ASC')
        ->limit(1)

        ->render($this->_table);
    }

    private function _getCategories()
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
        ->groupBy('category_id, category_slug, category_title, category_description, category_image')

        // Order by current language first
        ->orderBy('(CASE WHEN blogs.language_id = ' . get_userdata('language_id') . ' THEN 1 ELSE 2 END)', 'ASC')

        // Normal ordering
        ->orderBy('total_data', 'DESC')

        ->getWhere(
            'blogs_categories',
            [
                'blogs.status' => 1
            ]
        )
        ->result();

        return $query;
    }

    private function _getRelated($category = 0, $slug = '')
    {
        $keywords = $this->_titleKeywords($slug);
        $score = [];

        foreach ($keywords as $keyword) {
            $keyword = strtolower($keyword);
            $score[] = "CASE WHEN LOWER(blogs.post_title) LIKE " . $this->model->escape('%' . $keyword . '%') . " THEN 1 ELSE 0 END";
        }

        $query = $this->model->select('
            blogs.post_slug,
            blogs.post_title,
            blogs.post_excerpt,
            blogs.featured_image,
            blogs.updated_at,
            blogs_categories.category_slug,
            blogs_categories.category_title,
            blogs_categories.category_image
        ')
        ->join(
            'blogs_categories',
            'blogs_categories.category_id = blogs.post_category'
        )
        ->orderBy($score ? '(' . implode(' + ', $score) . ')' : '0', 'DESC', false)
        ->orderBy('(CASE WHEN blogs.language_id = ' . get_userdata('language_id') . ' THEN 1 ELSE 2 END)', 'ASC')
        ->orderBy('blogs.post_title', 'RANDOM')
        ->limit(5)
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

    private function _titleKeywords(string $slug = ''): array
    {
        $title = $this->model->select('post_title')
        ->orderBy('(CASE WHEN blogs.language_id = ' . get_userdata('language_id') . ' THEN 1 ELSE 2 END)', 'ASC')
        ->getWhere('blogs', [
            'post_slug' => $slug,
            'blogs.status' => 1
        ], 1)
        ->row('post_title');

        $title = strtolower(trim((string) $title));
        $words = preg_split('/[^\\pL\\pN]+/u', $title) ?: [];
        $words = array_values(array_unique(array_filter($words, static function ($word): bool {
            return mb_strlen($word) >= 4;
        })));

        return array_slice(array_merge([$title], $words), 0, 7);
    }

    private function _getRecommendations($category = 0, $slug = '')
    {
        $post_tags = $this->model->select('
            blogs.post_tags
        ')
        ->join(
            'blogs_categories',
            'blogs_categories.category_id = blogs.post_category'
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

        $post_tags = array_map('trim', explode(',', $post_tags));

        if (! empty(array_filter($post_tags))) {
            $this->model->groupStart();

            foreach ($post_tags as $key => $tag) {
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
            blogs_categories.category_slug
        ')
        ->join(
            'blogs_categories',
            'blogs_categories.category_id = blogs.post_category'
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
