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

namespace Aksara\Modules\CMS\Controllers\Blogs;

use Throwable;
use Aksara\Laboratory\Core;
use Aksara\Libraries\AI\AI;

class Blogs extends Core
{
    private string $_table = 'blogs';

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
                'blogs_categories',
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

        if (get_setting('ai_enabled')) {
            $this->addSubmitButton(null, null, 'AI', 'btn btn-info --ai-assistant', 'mdi mdi-creation')
            ->setAiContext([
                'instructions' => [
                    'Generate editorial blog content only for the supplied blog fields.',
                    'For textarea and wysiwyg fields, write complete, useful article content instead of short placeholders.',
                    'For wysiwyg post content, use clean semantic HTML paragraphs, headings, and lists only when they help readability.',
                    'If a language_id field exists, infer the final content language from the instruction and generated content, then use an available language id.',
                    'If a post_category field exists, infer the best matching blog category from the instruction and generated content, then use an available category id.'
                ],
                'data' => [
                    'post_category' => $this->_categories()
                ]
            ]);
        }

        if ($this->request->getGet('language')) {
            $this->where('language_id', $this->request->getGet('language'));
        }

        $this->setTitle(phrase('Blogs'))
        ->setIcon('mdi mdi-newspaper')

        ->unsetColumn('post_id, post_excerpt, post_slug, post_content, post_tags, headline, language')
        ->unsetField('post_id, created_by')
        ->unsetView('post_id')

        ->columnOrder('featured_image, post_title, category_title, headline, status')
        ->fieldOrder('post_title, post_slug, post_excerpt, post_content, featured_image, post_category, post_tags, language_id, headline, status')
        ->viewOrder('post_title, post_slug, post_excerpt, post_content, featured_image, post_category, post_tags, headline, status')

        // Group rows by post category
        ->itemReference('post_category')

        ->addButton('translate', phrase('Translate'), 'btn-dark --modal', 'mdi mdi-translate', ['post_id' => 'post_id'])

        ->setRelation(
            'post_category',
            'blogs_categories.category_id',
            '{{ blogs_categories.category_title }}'
        )
        ->setRelation(
            'language_id',
            'app_languages.id',
            '{{ app_languages.language }}',
            [
                'app_languages.status' => 1
            ]
        )
        ->setRelation(
            'created_by',
            'app_users.user_id',
            '{{ app_users.first_name }} {{ app_users.last_name }}'
        )

        ->setField([
            'post_excerpt' => 'textarea',
            'post_content' => 'wysiwyg',
            'post_tags' => 'tagsinput',
            'headline' => 'boolean',
            'featured_image' => 'image',
            'status' => 'boolean'
        ])
        ->setField('post_slug', 'slug', 'post_title')
        ->setField('post_title', 'hyperlink', 'blogs/read', ['post_id' => 'post_id'], true)
        ->setField('post_category', 'hyperlink', 'cms/blogs', ['category' => 'post_category'])
        ->setField('created_by', 'hyperlink', 'user', ['user_id' => 'created_by'], true)

        ->fieldAppend(
            'post_category',
            '<a href="' . go_to('categories/create') . '" class="--modal"><i class="mdi mdi-plus-circle-outline me-1"></i>' . phrase('Add') . '</a>'
        )
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
            'language' => 2,
            'created_at' => 2,
            'created_by' => 2
        ])
        ->columnSize([
            1 => 'col-md-8',
            2 => 'col-md-4'
        ])
        ->modalSize('modal-xl')

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
            'language_id' => phrase('Language'),
            'created_at' => phrase('Created At'),
            'created_by' => phrase('Author')
        ])

        ->orderBy([
            'blogs_categories.category_title' => 'ASC',
            'created_at' => 'DESC'
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
                'app_languages',
                [
                    'id !=' => $currentLanguage,
                    'status' => 1
                ]
            )
            ->result();

            // Build language list
            $languageList = '';

            foreach ($languages as $key => $val) {
                $languageList .= '<div class="list-group-item list-group-item-action position-relative p-0">
                    <a href="' . go_to('translate', ['language' => $val->id]) . '" class="d-block px-3 py-3 pe-5 text-body text-decoration-none --modal">
                        <i class="mdi mdi-translate me-2"></i> ' . $val->language . '
                    </a>
                    <a href="' . go_to('translate', ['language' => $val->id, 'ai' => true]) . '" class="btn btn-sm btn-info px-0 rounded-circle float-end position-absolute top-50 end-0 me-3 translate-middle-y --modal" data-bs-toggle="tooltip" title="' . phrase('Translate with AI') . '">
                        <i class="mdi mdi-creation"></i>
                    </a>
                </div>';
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
                'reactivate' => ['tooltip']
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
                $data->language_id = $this->request->getGet('language');

                if ($this->_shouldTranslateWithAi()) {
                    $data = $this->_translateWithAi($data, (int) $data->language_id);
                }

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
        ->unsetField('post_id, post_category, language_id, post_slug, featured_image, headline, status')
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

    private function _translateWithAi(object $data, int $languageId): object
    {
        // Increase max_execution_time to 5 minutes
        ini_set('max_execution_time', 300);

        $language = $this->model->getWhere('app_languages', [
            'id' => $languageId,
            'status' => 1
        ], 1)
        ->row();

        if (! $language) {
            return $data;
        }

        $ai = new AI();

        if (! $ai->ready()) {
            return $data;
        }

        $target = trim((string) ($language->language ?? ''));
        $fields = [
            'post_title' => 'plain title',
            'post_excerpt' => 'plain excerpt',
            'post_content' => 'HTML article body',
            'post_tags' => 'comma separated tags'
        ];

        foreach ($fields as $field => $context) {
            if (empty($data->{$field})) {
                continue;
            }

            $response = $ai->translate((string) $data->{$field}, $target, [
                'content_type' => 'blog post ' . $context,
                'route' => 'cms/blogs',
                'language' => $target,
                'site_name' => get_setting('app_name')
            ]);

            if (($response['status'] ?? 500) < 400 && ! empty($response['content'])) {
                $data->{$field} = trim((string) $response['content']);
            }
        }

        return $data;
    }

    private function _shouldTranslateWithAi(): bool
    {
        return in_array($this->request->getGet('ai'), ['1', 1, true, 'true'], true);
    }

    private function _filter()
    {
        $categories = [
            [
                'id' => 0,
                'label' => phrase('All categories')
            ]
        ];

        $categoriesQuery = $this->model->select('
            category_id AS id,
            category_title AS label
        ')
        ->getWhere(
            'blogs_categories',
            [
                'status' => 1
            ]
        )
        ->result();

        if ($categoriesQuery) {
            foreach ($categoriesQuery as $key => $val) {
                $categories[] = [
                    'id' => $val->id,
                    'label' => $val->label,
                    'selected' => $this->request->getGet('category') === $val->id
                ];
            }
        }

        $languages = [
            [
                'id' => 0,
                'label' => phrase('All languages')
            ]
        ];

        $languagesQuery = $this->model->select('
            id,
            language AS label
        ')
        ->getWhere(
            'app_languages',
            [
                'status' => 1
            ]
        )
        ->result();

        if ($languagesQuery) {
            foreach ($languagesQuery as $key => $val) {
                $languages[] = [
                    'id' => $val->id,
                    'label' => $val->label,
                    'selected' => $this->request->getGet('language') === $val->id
                ];
            }
        }

        return [
            'category' => [
                'label' => phrase('Category'),
                'values' => $categories
            ],
            'language' => [
                'label' => phrase('Language'),
                'values' => $languages
            ]
        ];
    }

    private function _categories(): array
    {
        if (! $this->model->tableExists('blogs_categories')) {
            return [];
        }

        return array_map(static function ($category) {
            return [
                'id' => (int) $category->category_id,
                'title' => (string) $category->category_title,
                'slug' => (string) ($category->category_slug ?? ''),
                'description' => (string) ($category->category_description ?? '')
            ];
        }, $this->model->getWhere('blogs_categories')->result());
    }
}
