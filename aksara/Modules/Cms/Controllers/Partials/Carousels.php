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

namespace Aksara\Modules\CMS\Controllers\Partials;

use Aksara\Laboratory\Core;

class Carousels extends Core
{
    private $_table = 'pages__carousels';

    public function __construct()
    {
        parent::__construct();

        $this->restrictOnDemo();

        $this->setPermission();
        $this->setTheme('backend');

        $this->unsetMethod('clone');

        $this->setUploadPath('carousels');

        // Ignore query string signature
        $this->ignoreQueryString('language');
    }

    public function index()
    {
        $this->addFilter($this->_filter());

        if ($this->request->getGet('language')) {
            $this->where('language_id', $this->request->getGet('language'));
        }

        $this->setTitle(phrase('Carousels'))
        ->setIcon('mdi mdi-view-carousel')
        ->unsetColumn('carousel_id, created_timestamp, updated_timestamp, language')
        ->unsetField('carousel_id')
        ->unsetView('carousel_id')
        ->setField([
            'carousel_description' => 'textarea',
            'carousel_content' => 'carousel',
            'created_timestamp' => 'created_timestamp',
            'updated_timestamp' => 'updated_timestamp',
            'status' => 'boolean'
        ])
        ->setRelation(
            'language_id',
            'app__languages.id',
            '{{ app__languages.language }}',
            [
                'app__languages.status' => 1
            ]
        )
        ->setValidation([
            'carousel_title' => 'required',
            'language_id' => 'required',
            'status' => 'boolean'
        ])
        ->setAlias([
            'carousel_title' => phrase('Title'),
            'carousel_description' => phrase('Description'),
            'carousel_content' => phrase('Contents'),
            'created_timestamp' => phrase('Created'),
            'updated_timestamp' => phrase('Updated'),
            'language' => phrase('Language'),
            'language_id' => phrase('Language'),
            'status' => phrase('Status')
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
