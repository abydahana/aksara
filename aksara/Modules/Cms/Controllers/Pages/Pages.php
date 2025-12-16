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

namespace Aksara\Modules\Cms\Controllers\Pages;

use Aksara\Laboratory\Core;

class Pages extends Core
{
    private $_table = 'pages';

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

        if ($this->request->getGet('language')) {
            $this->where('language_id', $this->request->getGet('language'));
        } else {
            $this->where('language_id', get_setting('app_language') ?? 0);
        }

        $this->set_title(phrase('Pages'))
        ->set_icon('mdi mdi-file-document-outline')
        ->set_primary('page_id')
        ->unset_column('page_id, author, page_slug, page_content, carousel_title, faq_title, created_timestamp, updated_timestamp, language')
        ->unset_field('page_id, author')
        ->unset_view('page_id, author')
        ->column_order('page_title, page_description, carousel_title, faq_title, updated, status')
        ->field_order('page_title, page_description, carousel_id, faq_id, language_id, created_timestamp, updated_timestamp, status')
        ->set_field([
            'page_description' => 'textarea',
            'page_content' => 'wysiwyg',
            'created_timestamp' => 'created_timestamp',
            'updated_timestamp' => 'updated_timestamp',
            'status' => 'boolean'
        ])
        ->set_field('page_slug', 'slug', 'page_title')
        ->set_field('page_title', 'hyperlink', 'pages', ['page_id' => 'page_id'], true)

        ->add_button('translate', phrase('Translate'), 'btn-dark --modal', 'mdi mdi-translate', ['page_id' => 'page_id'])
        ->add_button('../../pages', phrase('View Page'), 'btn-success', 'mdi mdi-eye', ['page_id' => 'page_id'], true)

        ->field_append(
            'carousel_id',
            '<a href="' . go_to('../../cms/partials/carousels/create') . '" class="--modal"><i class="mdi mdi-plus-circle-outline me-1"></i>' . phrase('Add') . '</a>'
        )
        ->field_append(
            'faq_id',
            '<a href="' . go_to('../../cms/partials/faqs/create') . '" class="--modal"><i class="mdi mdi-plus-circle-outline me-1"></i>' . phrase('Add') . '</a>'
        )

        ->set_relation(
            'carousel_id',
            'pages__carousels.carousel_id',
            '{{ pages__carousels.carousel_title }}',
            [
                'pages__carousels.status' => 1
            ]
        )
        ->set_relation(
            'faq_id',
            'pages__faqs.faq_id',
            '{{ pages__faqs.faq_title }}',
            [
                'pages__faqs.status' => 1
            ]
        )
        ->set_relation(
            'language_id',
            'app__languages.id',
            '{{ app__languages.language }}',
            [
                'app__languages.status' => 1
            ]
        )
        ->set_validation([
            'page_title' => 'required|max_length[255]|unique[' . $this->_table . '.page_title.page_id.' . $this->request->getGet('page_id') . ']',
            'page_slug' => 'max_length[255]|unique[' . $this->_table . '.page_slug.page_id.' . $this->request->getGet('page_id') . '.language_id.' . ($this->request->getPost('language_id') ?? $this->request->getGet('language') ?? 0) . ']',
            'page_content' => 'required',
            'language_id' => 'required',
            'status' => 'boolean'
        ])
        ->set_default([
            'author' => get_userdata('user_id')
        ])
        ->set_alias([
            'page_title' => phrase('Title'),
            'page_description' => phrase('Description'),
            'page_slug' => phrase('Slug'),
            'page_content' => phrase('Content'),
            'carousel_id' => phrase('Carousel'),
            'carousel_title' => phrase('Carousel'),
            'faq_id' => phrase('Faq'),
            'faq_title' => phrase('Faq'),
            'created_timestamp' => phrase('Created'),
            'updated_timestamp' => phrase('Updated'),
            'language' => phrase('Language'),
            'language_id' => phrase('Language')
        ])
        ->set_placeholder([
            'page_description' => phrase('Page summary to improve SEO')
        ])
        ->field_position([
            'carousel_id' => 2,
            'carousel_title' => 2,
            'faq_id' => 2,
            'faq_title' => 2,
            'created_timestamp' => 2,
            'updated_timestamp' => 2,
            'status' => 2,
            'language_id' => 2,
            'language' => 2
        ])
        ->column_size([
            1 => 'col-md-8',
            2 => 'col-md-4'
        ])
        ->modal_size('modal-xl')

        ->render($this->_table);
    }

    public function translate()
    {
        $this->set_method('update');

        if (! $this->request->getGet('language')) {
            $current_language = $this->model->get_where(
                $this->_table,
                [
                    'page_id' => $this->request->getGet('page_id') ?? 0
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

        // Initialize page id
        $page_id = 0;

        try {
            // Get current data
            $data = $this->model->get_where(
                $this->_table,
                [
                    'page_id' => $this->request->getGet('page_id') ?? 0
                ],
                1
            )
            ->row();

            // Check if translation already exists
            $checker = $this->model->get_where(
                $this->_table,
                [
                    'page_slug' => $data->page_slug,
                    'language_id' => $this->request->getGet('language') ?? 0
                ],
                1
            )
            ->row();

            $page_id = $checker->page_id ?? 0;

            if (! $checker) {
                // Noop, modify data and create new translation
                unset($data->page_id);

                // Change language id
                $data->language_id = $this->request->getGet('language');

                // Insert new data
                $this->model->insert($this->_table, (array) $data);

                // Set new page id
                $page_id = $this->model->insert_id();
            }
        } catch (\Throwable $e) {
            return throw_exception(500, $e->getMessage());
        }

        $this->set_title(phrase('Translate Page'))
        ->set_icon('mdi mdi-translate')
        ->unset_field('page_id, language_id, page_slug, author, carousel_id, faq_id, status, created_timestamp, updated_timestamp')
        ->set_field([
            'page_description' => 'textarea',
            'page_content' => 'wysiwyg',
            'status' => 'boolean'
        ])
        ->where([
            'page_id' => $page_id
        ])
        ->set_validation([
            'page_title' => 'required|max_length[256]|unique[' . $this->_table . '.page_title.page_id.' . $this->request->getGet('page_id') . ']',
            'page_content' => 'required'
        ])
        ->set_alias([
            'page_title' => phrase('Title'),
            'page_description' => phrase('Description'),
            'page_content' => phrase('Content'),
        ])
        ->modal_size('modal-lg')
        ->render($this->_table);
    }

    private function _filter()
    {
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
                    'selected' => $this->request->getGet('language') === $val->id
                ];
            }
        }

        return [
            'language' => [
                'type' => 'select',
                'label' => phrase('Language'),
                'values' => $languages
            ]
        ];
    }
}
