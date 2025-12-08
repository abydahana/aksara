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

namespace Aksara\Modules\Cms\Controllers\Blogs;

class Blogs extends \Aksara\Laboratory\Core
{
    private $_table = 'blogs';

    public function __construct()
    {
        parent::__construct();

        $this->restrict_on_demo();

        $this->set_permission();
        $this->set_theme('backend');
    }

    public function index()
    {
        $this->add_filter($this->_filter());

        if (service('request')->getGet('category')) {
            $query = $this->model->get_where(
                'blogs__categories',
                [
                    'category_id' => service('request')->getGet('category')
                ]
            )
            ->row();

            if ($query) {
                $this->set_description('
                    <div class="row">
                        <div class="col-4 col-sm-3 col-md-2 text-muted text-uppercase">
                            ' . phrase('Category') . '
                        </div>
                        <div class="col-8 col-sm-9 col-md-4 fw-bold">
                            ' . $query->category_title . '
                        </div>
                    </div>
                ')
                ->unset_field('post_category')
                ->set_default([
                    'post_category' => $query->category_id
                ])
                ->where([
                    'post_category' => $query->category_id
                ]);
            }
        }

        if (service('request')->getGet('language')) {
            $this->where('language_id', service('request')->getGet('language'));
        } else {
            $this->where('language_id', get_setting('app_language') ?? 0);
        }

        $this->set_title(phrase('Blogs'))
        ->set_icon('mdi mdi-newspaper')
        ->set_primary('post_id')
        ->unset_column('post_id, post_excerpt, post_slug, post_content, post_tags, created_timestamp, headline, language')
        ->unset_field('post_id, author, created_timestamp, updated_timestamp')
        ->unset_view('post_id')
        ->column_order('featured_image, post_title, category_title, first_name, headline, updated_timestamp, status')
        ->field_order('post_title, post_slug, post_excerpt, post_content, featured_image, post_category, post_tags, language_id, headline, status')
        ->view_order('post_title, post_slug, post_excerpt, post_content, featured_image, post_category, post_tags, headline, status')
        ->set_field([
            'post_excerpt' => 'textarea',
            'post_content' => 'wysiwyg',
            'post_tags' => 'tagsinput',
            'author' => 'current_user',
            'headline' => 'boolean',
            'featured_image' => 'image',
            'created_timestamp' => 'created_timestamp',
            'updated_timestamp' => 'updated_timestamp',
            'status' => 'boolean'
        ])
        ->set_field('post_slug', 'slug', 'post_title')
        ->set_field('post_title', 'hyperlink', 'blogs/read', ['post_id' => 'post_id'], true)
        ->set_field('category_title', 'hyperlink', 'cms/blogs', ['category' => 'post_category'])

        ->add_button('translate', phrase('Translate'), 'btn-dark --modal', 'mdi mdi-translate', ['post_id' => 'post_id'])
        ->add_button('../../blogs/read', phrase('View Post'), 'btn-success', 'mdi mdi-eye', ['post_id' => 'post_id'], true)

        ->field_append(
            'post_category',
            '<a href="' . go_to('categories/create') . '" class="--modal"><i class="mdi mdi-plus-circle-outline me-1"></i>' . phrase('Add') . '</a>'
        )

        ->set_validation([
            'post_title' => 'required|max_length[255]|unique[' . $this->_table . '.post_title.post_id.' . service('request')->getGet('post_id') . ']',
            'post_slug' => 'max_length[255]|unique[' . $this->_table . '.post_slug.post_id.' . service('request')->getGet('post_id') . '.language_id.' . (service('request')->getPost('language_id') ?? service('request')->getGet('language') ?? 0) . ']',
            'post_content' => 'required',
            'post_category' => 'required',
            'language_id' => 'required',
            'post_tags' => 'required',
            'headline' => 'boolean',
            'status' => 'boolean'
        ])
        ->set_relation(
            'post_category',
            'blogs__categories.category_id',
            '{{ blogs__categories.category_title }}'
        )
        ->set_relation(
            'author',
            'app__users.user_id',
            '{{ app__users.first_name }} {{ app__users.last_name }}'
        )
        ->set_relation(
            'language_id',
            'app__languages.id',
            '{{ app__languages.language }}',
            [
                'app__languages.status' => 1
            ]
        )
        ->merge_content('{{ first_name }} {{ last_name }}', phrase('Author'))
        ->set_alias([
            'post_title' => phrase('Title'),
            'post_slug' => phrase('Slug'),
            'post_excerpt' => phrase('Excerpt'),
            'post_content' => phrase('Content'),
            'featured_image' => phrase('Cover'),
            'post_category' => phrase('Category'),
            'post_tags' => phrase('Tags'),
            'category_title' => phrase('Category'),
            'headline' => phrase('Headline'),
            'status' => phrase('Status'),
            'created_timestamp' => phrase('Created'),
            'updated_timestamp' => phrase('Updated'),
            'language' => phrase('Language'),
            'language_id' => phrase('Language')
        ])
        ->set_placeholder([
            'post_excerpt' => phrase('Article summary to improve SEO'),
            'post_tags' => phrase('Separate with commas')
        ])
        ->field_position([
            'post_category' => 2,
            'category_title' => 2,
            'post_tags' => 2,
            'status' => 2,
            'headline' => 2,
            'featured_image' => 2,
            'language_id' => 2,
            'language' => 2
        ])
        ->column_size([
            1 => 'col-md-8',
            2 => 'col-md-4'
        ])
        ->modal_size('modal-xl')
        ->set_default([
            'author' => get_userdata('user_id')
        ])
        ->item_reference([
            'category_title'
        ])

        ->render($this->_table);
    }

    public function translate()
    {
        $this->set_method('update');

        if (! service('request')->getGet('language')) {
            $current_language = $this->model->get_where(
                $this->_table,
                [
                    'post_id' => service('request')->getGet('post_id') ?? 0
                ],
                1
            )
            ->row('language_id');

            $languages = $this->model->get_where(
                'app__languages',
                [
                    'id !=' => $current_language,
                    'status' => 1
                ]
            )
            ->result();

            // Build language list
            $language_list = '';

            foreach ($languages as $key => $val) {
                $language_list .= '<a href="' . go_to('translate', ['language' => $val->id]) . '" class="list-group-item list-group-item-action --modal">
                    <i class="mdi mdi-translate me-2"></i> ' . $val->language . '
                </a>';
            }

            $content = '<div class="list-group list-group-flush">' . $language_list . '</div>';

            return make_json([
                'meta' => [
                    'title' => phrase('Choose Language'),
                    'icon' => 'mdi mdi-translate',
                    'popup' => true,
                    'modal_size' => 'modal-sm'
                ],
                'content' => $content,
            ]);
        }

        // Initialize post id
        $post_id = 0;

        try {
            // Get current data
            $data = $this->model->get_where(
                $this->_table,
                [
                    'post_id' => service('request')->getGet('post_id') ?? 0
                ],
                1
            )
            ->row();

            // Check if translation already exists
            $checker = $this->model->get_where(
                $this->_table,
                [
                    'post_slug' => $data->post_slug,
                    'language_id' => service('request')->getGet('language') ?? 0
                ],
                1
            )
            ->row();

            $post_id = $checker->post_id ?? 0;

            if (! $checker) {
                // Noop, modify data and create new translation
                unset($data->post_id);

                // Change language id
                $data->language_id = service('request')->getGet('language');

                // Insert new data
                $this->model->insert($this->_table, (array) $data);

                // Set new post id
                $post_id = $this->model->insert_id();
            }
        } catch (\Exception $e) {
            return throw_exception(500, $e->getMessage());
        }

        $this->set_title(phrase('Translate Blog Post'))
        ->set_icon('mdi mdi-translate')
        ->unset_field('post_id, post_category, language_id, post_slug, featured_image, author, headline, status, created_timestamp, updated_timestamp')
        ->set_field([
            'post_excerpt' => 'textarea',
            'post_content' => 'wysiwyg',
            'post_tags' => 'tagsinput',
            'status' => 'boolean'
        ])
        ->where([
            'post_id' => $post_id
        ])
        ->set_validation([
            'post_title' => 'required|max_length[256]|unique[' . $this->_table . '.post_title.post_id.' . service('request')->getGet('post_id') . ']',
            'post_content' => 'required',
            'post_tags' => 'required'
        ])
        ->set_alias([
            'post_title' => phrase('Title'),
            'post_excerpt' => phrase('Excerpt'),
            'post_content' => phrase('Content'),
            'post_tags' => phrase('Tags')
        ])
        ->modal_size('modal-lg')
        ->render($this->_table);
    }

    private function _filter()
    {
        $all_categories = [
            [
                'id' => 0,
                'label' => phrase('All categories')
            ]
        ];

        $categories = $this->model->select('
            category_id AS id,
            category_title AS label
        ')
        ->get_where(
            'blogs__categories',
            [
                'status' => 1
            ]
        )
        ->result();

        $languages = [
            [
                'id' => 0,
                'label' => phrase('All languages')
            ]
        ];

        $languages_query = $this->model->get_where(
            'app__languages',
            [
                'status' => 1
            ]
        )
        ->result();

        if ($languages_query) {
            foreach ($languages_query as $key => $val) {
                $languages[] = [
                    'id' => $val->id,
                    'label' => $val->language,
                    'selected' => service('request')->getGet('language') === $val->id
                ];
            }
        }

        return [
            'category' => [
                'label' => phrase('Category'),
                'values' => array_merge($all_categories, $categories)
            ],
            'language' => [
                'label' => phrase('Language'),
                'values' => $languages
            ]
        ];
    }
}
