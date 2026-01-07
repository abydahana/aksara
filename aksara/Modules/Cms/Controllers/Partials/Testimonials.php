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

class Testimonials extends Core
{
    private $_table = 'testimonials';

    public function __construct()
    {
        parent::__construct();

        $this->restrictOnDemo();

        $this->setPermission();
        $this->setTheme('backend');

        $this->unsetMethod('clone');

        $this->setUploadPath('testimonials');

        // Ignore query string signature
        $this->ignoreQueryString('language');
    }

    public function index()
    {
        $this->addFilter($this->_filter());

        if ($this->request->getGet('language')) {
            $this->where('language_id', $this->request->getGet('language'));
        }

        $this->setTitle(phrase('Testimonials'))
        ->setIcon('mdi mdi-comment-account-outline')
        ->setPrimary('testimonial_id')
        ->unsetColumn('testimonial_id, testimonial_content, timestamp, language')
        ->unsetField('testimonial_id')
        ->unsetView('testimonial_id')
        ->setField([
            'photo' => 'image',
            'testimonial_content' => 'textarea',
            'timestamp' => 'current_timestamp',
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
            'first_name' => 'required|string',
            'last_name' => 'string',
            'testimonial_title' => 'required|string',
            'testimonial_content' => 'required|string',
            'language_id' => 'required',
            'status' => 'boolean'
        ])
        ->setAlias([
            'first_name' => phrase('First Name'),
            'last_name' => phrase('Last Name'),
            'testimonial_title' => phrase('Title'),
            'testimonial_content' => phrase('Testimony'),
            'language_id' => phrase('Language'),
            'status' => phrase('Status')
        ])
        ->mergeField('first_name, last_name')
        ->mergeContent('{{ first_name }} {{ last_name }}', phrase('Full Name'))

        ->orderBy('timestamp', 'DESC')

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
