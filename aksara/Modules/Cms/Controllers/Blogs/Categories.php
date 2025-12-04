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

class Categories extends \Aksara\Laboratory\Core
{
    protected $_table = 'blogs__categories';

    public function __construct()
    {
        parent::__construct();

        $this->restrict_on_demo();

        $this->set_permission();
        $this->set_theme('backend');

        $this->unset_method('clone');

        $this->set_upload_path('blogs');
        $this->unset_delete('category_id', [1]);
    }

    public function index()
    {
        $this->add_filter($this->_filter());

        if (service('request')->getGet('language')) {
            $this->where('language_id', service('request')->getGet('language'));
        }

        $this->set_title(phrase('Blog Categories'))
        ->set_icon('mdi mdi-sitemap')
        ->set_primary('category_id')
        ->unset_column('category_id, language')
        ->unset_field('category_id')
        ->unset_view('category_id')
        ->column_order('category_image')
        ->set_field([
            'category_image' => 'image',
            'category_description' => 'textarea',
            'status' => 'boolean'
        ])
        ->set_field('category_slug', 'slug', 'category_title')
        ->set_field('category_title', 'hyperlink', 'cms/blogs', ['category' => 'category_id'])
        ->set_relation(
            'language_id',
            'app__languages.id',
            '{{ app__languages.language }}',
            [
                'app__languages.status' => 1
            ]
        )
        ->set_validation([
            'category_title' => 'required|max_length[64]|unique[' . $this->_table . '.category_title.category_id.' . service('request')->getGet('category_id') . ']',
            'category_slug' => 'max_length[64]|unique[' . $this->_table . '.category_slug.category_id.' . service('request')->getGet('category_id') . ']',
            'category_description' => 'required',
            'language_id' => 'required',
            'status' => 'boolean'
        ])
        ->set_alias([
            'category_image' => phrase('Image'),
            'category_title' => phrase('Title'),
            'category_slug' => phrase('Slug'),
            'category_description' => phrase('Description'),
            'language' => phrase('Language'),
            'language_id' => phrase('Language')
        ])
        ->set_placeholder([
            'category_description' => phrase('Category details to improve SEO')
        ])

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
                    'selected' => service('request')->getGet('language') === $val->id
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
