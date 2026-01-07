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

namespace Aksara\Modules\CMS\Controllers\Blogs;

use Aksara\Laboratory\Core;
use Throwable;

class Blogs extends Core
{
    private $_table = 'blogs';

    public function __construct()
    {
        parent::__construct();

        $this->restrictOnDemo();

        $this->setPermission();
        $this->setTheme('backend');

        // Ignore query string signature
        $this->ignoreQueryString('category, language');
    }

    public function index()
    {
        $this->addFilter($this->_filter());

        if ($this->request->getGet('category')) {
            $query = $this->model->getWhere(
                'blogs__categories',
                [
                    'category_id' => $this->request->getGet('category')
                ]
            )
            ->row();

            if ($query) {
                $this->setDescription('
                    <div class="row">
                        <div class="col-4 col-sm-3 col-md-2 text-muted text-uppercase">
                            ' . phrase('Category') . '
                        </div>
                        <div class="col-8 col-sm-9 col-md-4 fw-bold">
                            ' . $query->category_title . '
                        </div>
                    </div>
                ')
                ->unsetField('post_category')
                ->setDefault([
                    'post_category' => $query->category_id
                ])
                ->where([
                    'post_category' => $query->category_id
                ]);
            }
        }

        if ($this->request->getGet('language')) {
            $this->where('language_id', $this->request->getGet('language'));
        } else {
            $this->where('language_id', get_setting('app_language') ?? 0);
        }

        $this->setTitle(phrase('Blogs'))
        ->setIcon('mdi mdi-newspaper')
        ->setPrimary('post_id')
        ->unsetColumn('post_id, post_excerpt, post_slug, post_content, post_tags, created_timestamp, headline, language')
        ->unsetField('post_id, author, created_timestamp, updated_timestamp')
        ->unsetView('post_id')
        ->columnOrder('featured_image, post_title, category_title, first_name, headline, updated_timestamp, status')
        ->fieldOrder('post_title, post_slug, post_excerpt, post_content, featured_image, post_category, post_tags, language_id, headline, status')
        ->viewOrder('post_title, post_slug, post_excerpt, post_content, featured_image, post_category, post_tags, headline, status')
        ->setField([
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
        ->setField('post_slug', 'slug', 'post_title')
        ->setField('post_title', 'hyperlink', 'blogs/read', ['post_id' => 'post_id'], true)
        ->setField('category_title', 'hyperlink', 'cms/blogs', ['category' => 'post_category'])

        ->addButton('translate', phrase('Translate'), 'btn-dark --modal', 'mdi mdi-translate', ['post_id' => 'post_id'])
        ->addButton('../../blogs/read', phrase('View Post'), 'btn-success', 'mdi mdi-eye', ['post_id' => 'post_id'], true)

        ->fieldAppend(
            'post_category',
            '<a href="' . go_to('categories/create') . '" class="--modal"><i class="mdi mdi-plus-circle-outline me-1"></i>' . phrase('Add') . '</a>'
        )

        ->setValidation([
            'post_title' => 'required|max_length[255]|unique[' . $this->_table . '.post_title.post_id.' . $this->request->getGet('post_id') . ']',
            'post_slug' => 'max_length[255]|unique[' . $this->_table . '.post_slug.post_id.' . $this->request->getGet('post_id') . '.language_id.' . ($this->request->getPost('language_id') ?? $this->request->getGet('language') ?? 0) . ']',
            'post_content' => 'required',
            'post_category' => 'required',
            'language_id' => 'required',
            'post_tags' => 'required',
            'headline' => 'boolean',
            'status' => 'boolean'
        ])
        ->setRelation(
            'post_category',
            'blogs__categories.category_id',
            '{{ blogs__categories.category_title }}'
        )
        ->setRelation(
            'author',
            'app__users.user_id',
            '{{ app__users.first_name }} {{ app__users.last_name }}'
        )
        ->setRelation(
            'language_id',
            'app__languages.id',
            '{{ app__languages.language }}',
            [
                'app__languages.status' => 1
            ]
        )
        ->mergeContent('{{ first_name }} {{ last_name }}', phrase('Author'))
        ->setAlias([
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
        ->setPlaceholder([
            'post_excerpt' => phrase('Article summary to improve SEO'),
            'post_tags' => phrase('Separate with commas')
        ])
        ->fieldPosition([
            'post_category' => 2,
            'category_title' => 2,
            'post_tags' => 2,
            'status' => 2,
            'headline' => 2,
            'featured_image' => 2,
            'language_id' => 2,
            'language' => 2
        ])
        ->columnSize([
            1 => 'col-md-8',
            2 => 'col-md-4'
        ])
        ->modalSize('modal-xl')
        ->setDefault([
            'author' => get_userdata('user_id')
        ])
        ->itemReference([
            'category_title'
        ])

        ->render($this->_table);
    }

    public function translate()
    {
        $this->setMethod('update');

        if (! $this->request->getGet('language')) {
            $currentLanguage = $this->model->getWhere(
                $this->_table,
                [
                    'post_id' => $this->request->getGet('post_id') ?? 0
                ],
                1
            )
            ->row('language_id');

            $languages = $this->model->getWhere(
                'app__languages',
                [
                    'id !=' => $currentLanguage,
                    'status' => 1
                ]
            )
            ->result();

            // Build language list
            $languageList = '';

            foreach ($languages as $key => $val) {
                $languageList .= '<a href="' . go_to('translate', ['language' => $val->id]) . '" class="list-group-item list-group-item-action --modal">
                    <i class="mdi mdi-translate me-2"></i> ' . $val->language . '
                </a>';
            }

            $content = '<div class="list-group list-group-flush">' . $languageList . '</div>';

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
        $postId = 0;

        try {
            // Get current data
            $data = $this->model->getWhere(
                $this->_table,
                [
                    'post_id' => $this->request->getGet('post_id') ?? 0
                ],
                1
            )
            ->row();

            // Check if translation already exists
            $checker = $this->model->getWhere(
                $this->_table,
                [
                    'post_slug' => $data->post_slug,
                    'language_id' => $this->request->getGet('language') ?? 0
                ],
                1
            )
            ->row();

            $postId = $checker->post_id ?? 0;

            if (! $checker) {
                // Noop, modify data and create new translation
                unset($data->post_id);

                // Change language id
                $data->languageId = $this->request->getGet('language');

                // Insert new data
                $this->model->insert($this->_table, (array) $data);

                // Set new post id
                $postId = $this->model->insertId();
            }
        } catch (Throwable $e) {
            return throw_exception(500, $e->getMessage());
        }

        $this->setTitle(phrase('Translate Blog Post'))
        ->setIcon('mdi mdi-translate')
        ->unsetField('post_id, post_category, language_id, post_slug, featured_image, author, headline, status, created_timestamp, updated_timestamp')
        ->setField([
            'post_excerpt' => 'textarea',
            'post_content' => 'wysiwyg',
            'post_tags' => 'tagsinput',
            'status' => 'boolean'
        ])
        ->where([
            'post_id' => $postId
        ])
        ->setValidation([
            'post_title' => 'required|max_length[256]|unique[' . $this->_table . '.post_title.post_id.' . $this->request->getGet('post_id') . ']',
            'post_content' => 'required',
            'post_tags' => 'required'
        ])
        ->setAlias([
            'post_title' => phrase('Title'),
            'post_excerpt' => phrase('Excerpt'),
            'post_content' => phrase('Content'),
            'post_tags' => phrase('Tags')
        ])
        ->modalSize('modal-lg')
        ->render($this->_table);
    }

    private function _filter()
    {
        $allCategories = [
            [
                'id' => 0,
                'label' => phrase('All categories')
            ]
        ];

        $categories = $this->model->select('
            category_id AS id,
            category_title AS label
        ')
        ->getWhere(
            'blogs__categories',
            [
                'status' => 1
            ]
        )
        ->resultArray();

        $languages = [
            [
                'id' => 0,
                'label' => phrase('All languages')
            ]
        ];

        $languagesQuery = $this->model->getWhere(
            'app__languages',
            [
                'status' => 1
            ]
        )
        ->result();

        if ($languagesQuery) {
            foreach ($languagesQuery as $key => $val) {
                $languages[] = [
                    'id' => $val->id,
                    'label' => $val->language,
                    'selected' => $this->request->getGet('language') === $val->id
                ];
            }
        }

        return [
            'category' => [
                'label' => phrase('Category'),
                'values' => array_merge($allCategories, $categories)
            ],
            'language' => [
                'label' => phrase('Language'),
                'values' => $languages
            ]
        ];
    }
}
