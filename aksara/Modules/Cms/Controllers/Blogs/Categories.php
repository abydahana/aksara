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

use Aksara\Laboratory\Core;

class Categories extends Core
{
    private $_table = 'blogs__categories';

    public function __construct()
    {
        parent::__construct();

        $this->restrictOnDemo();

        $this->setPermission();
        $this->setTheme('backend');

        $this->unsetMethod('clone');

        $this->setUploadPath('blogs');
        $this->unsetDelete('category_id', [1]);

        // Ignore query string signature
        $this->ignoreQueryString('language');
    }

    public function index()
    {
        $this->addFilter($this->_filter());

        if ($this->request->getGet('language')) {
            $this->where('language_id', $this->request->getGet('language'));
        }

        $this->setTitle(phrase('Blog Categories'))
        ->setIcon('mdi mdi-sitemap')
        ->setPrimary('category_id')
        ->unsetColumn('category_id, language')
        ->unsetField('category_id')
        ->unsetView('category_id')
        ->columnOrder('category_image')
        ->setField([
            'category_image' => 'image',
            'category_description' => 'textarea',
            'status' => 'boolean'
        ])
        ->setField('category_slug', 'slug', 'category_title')
        ->setField('category_title', 'hyperlink', 'cms/blogs', ['category' => 'category_id'])
        ->setRelation(
            'language_id',
            'app__languages.id',
            '{{ app__languages.language }}',
            [
                'app__languages.status' => 1
            ]
        )
        ->setValidation([
            'category_title' => 'required|max_length[64]|unique[' . $this->_table . '.category_title.category_id.' . $this->request->getGet('category_id') . ']',
            'category_slug' => 'max_length[64]|unique[' . $this->_table . '.category_slug.category_id.' . $this->request->getGet('category_id') . ']',
            'category_description' => 'required',
            'language_id' => 'required',
            'status' => 'boolean'
        ])
        ->setAlias([
            'category_image' => phrase('Image'),
            'category_title' => phrase('Title'),
            'category_slug' => phrase('Slug'),
            'category_description' => phrase('Description'),
            'language' => phrase('Language'),
            'language_id' => phrase('Language')
        ])
        ->setPlaceholder([
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
            'language' => [
                'type' => 'select',
                'label' => phrase('Language'),
                'values' => $languages
            ]
        ];
    }
}
