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

namespace Aksara\Modules\Cms\Controllers\Partials;

class Carousels extends \Aksara\Laboratory\Core
{
    private $_table = 'pages__carousels';

    public function __construct()
    {
        parent::__construct();

        $this->restrict_on_demo();

        $this->set_permission();
        $this->set_theme('backend');

        $this->unset_method('clone');

        $this->set_upload_path('carousels');
    }

    public function index()
    {
        $this->add_filter($this->_filter());

        if (service('request')->getGet('language')) {
            $this->where('language_id', service('request')->getGet('language'));
        }

        if ($this->get_method() === 'create') {
            $this->set_field('created_timestamp', 'current_timestamp');
        } elseif ($this->get_method() === 'update') {
            $this->set_field('updated_timestamp', 'current_timestamp');
        } else {
            $this->set_field('created_timestamp', 'datetime');
            $this->set_field('updated_timestamp', 'datetime');
        }

        $this->set_title(phrase('Carousels'))
        ->set_icon('mdi mdi-view-carousel')
        ->unset_column('carousel_id, created_timestamp, updated_timestamp, language')
        ->unset_field('carousel_id')
        ->unset_view('carousel_id')
        ->set_field([
            'carousel_description' => 'textarea',
            'carousel_content' => 'carousel',
            'status' => 'boolean'
        ])
        ->set_relation(
            'language_id',
            'app__languages.id',
            '{{ app__languages.language }}',
            [
                'app__languages.status' => 1
            ]
        )
        ->set_validation([
            'carousel_title' => 'required',
            'language_id' => 'required',
            'status' => 'boolean'
        ])
        ->set_alias([
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
