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

class Faqs extends \Aksara\Laboratory\Core
{
    private $_table = 'pages__faqs';

    public function __construct()
    {
        parent::__construct();

        $this->restrict_on_demo();

        $this->set_permission();
        $this->set_theme('backend');

        $this->unset_method('clone');
    }

    public function index()
    {
        $this->add_filter($this->_filter());

        if (service('request')->getGet('language')) {
            $this->where('language_id', service('request')->getGet('language'));
        }

        $this->set_title(phrase('FAQs'))
        ->set_icon('mdi mdi-file-question')
        ->unset_column('faq_id, created_timestamp, updated_timestamp, language')
        ->unset_field('faq_id')
        ->unset_view('faq_id')
        ->set_field([
            'faq_description' => 'textarea',
            'faq_content' => 'accordion',
            'language' => 'language_picker',
            'created_timestamp' => 'created_timestamp',
            'updated_timestamp' => 'updated_timestamp',
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
            'faq_title' => 'required',
            'language_id' => 'required',
            'status' => 'boolean'
        ])
        ->set_alias([
            'faq_title' => phrase('Title'),
            'faq_description' => phrase('Description'),
            'faq_content' => phrase('Contents'),
            'language' => phrase('Language'),
            'created_timestamp' => phrase('Created'),
            'updated_timestamp' => phrase('Updated'),
            'language_id' => phrase('Language'),
            'status' => phrase('Status')
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
