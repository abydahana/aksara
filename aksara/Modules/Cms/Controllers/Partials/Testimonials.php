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

class Testimonials extends \Aksara\Laboratory\Core
{
    private $_table = 'testimonials';

    public function __construct()
    {
        parent::__construct();

        $this->restrict_on_demo();

        $this->set_permission();
        $this->set_theme('backend');

        $this->unset_method('clone');

        $this->set_upload_path('testimonials');
    }

    public function index()
    {
        $this->add_filter($this->_filter());

        if (service('request')->getGet('language')) {
            $this->where('language_id', service('request')->getGet('language'));
        }

        $this->set_title(phrase('Testimonials'))
        ->set_icon('mdi mdi-comment-account-outline')
        ->set_primary('testimonial_id')
        ->unset_column('testimonial_id, testimonial_content, timestamp, language')
        ->unset_field('testimonial_id')
        ->unset_view('testimonial_id')
        ->set_field([
            'photo' => 'image',
            'testimonial_content' => 'textarea',
            'timestamp' => 'current_timestamp',
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
            'first_name' => 'required|string',
            'last_name' => 'string',
            'testimonial_title' => 'required|string',
            'testimonial_content' => 'required|string',
            'language_id' => 'required',
            'status' => 'boolean'
        ])
        ->set_alias([
            'first_name' => phrase('First Name'),
            'last_name' => phrase('Last Name'),
            'testimonial_title' => phrase('Title'),
            'testimonial_content' => phrase('Testimony'),
            'language_id' => phrase('Language'),
            'status' => phrase('Status')
        ])
        ->merge_field('first_name, last_name')
        ->merge_content('{{ first_name }} {{ last_name }}', phrase('Full Name'))

        ->order_by('timestamp', 'DESC')

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
