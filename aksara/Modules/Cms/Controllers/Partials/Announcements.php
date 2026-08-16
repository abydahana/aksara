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

namespace Aksara\Modules\CMS\Controllers\Partials;

use Aksara\Laboratory\Core;

class Announcements extends Core
{
    private string $_table = 'announcements';

    public function __construct()
    {
        parent::__construct();

        $this->restrictOnDemo();

        $this->setPermission();
        $this->setTheme('backend');

        $this->unsetMethod('clone');

        // Ignore query string signature
        $this->ignoreQueryString('language');
    }

    public function index()
    {
        $this->addFilter($this->_filter());

        if ($this->request->getGet('language')) {
            $this->where('language_id', $this->request->getGet('language'));
        }

        $this->setTitle(phrase('Announcements'))
        ->setIcon('mdi mdi-bullhorn-outline')
        ->unsetColumn('announcement_id, content, announcement_slug, language, created_at, created_by')
        ->unsetField('announcement_id, created_by')
        ->unsetView('announcement_id')
        ->columnOrder('cover')

        ->setRelation(
            'created_by',
            'app_users.user_id',
            '{{app_users.first_name}} {{app_users.last_name}}'
        )
        ->setRelation(
            'language_id',
            'app_languages.id',
            '{{ app_languages.language }}',
            [
                'app_languages.status' => 1
            ]
        )

        ->setField([
            'content' => 'wysiwyg',
            'cover' => 'image',
            'start_date' => 'date',
            'end_date' => 'date',
            'status' => 'boolean'
        ])
        ->setField(
            'placement',
            'radio',
            [
                0 => phrase('Front End'),
                1 => phrase('Back End')
            ]
        )
        ->setField('announcement_slug', 'slug', 'title')
        ->setField('announcement_title', 'hyperlink', 'announcements', ['announcement_slug' => 'announcement_slug'], true)
        ->setField('created_by', 'hyperlink', 'user', ['user_id' => 'user_id'], true)

        ->addButton('../../../announcements/get', phrase('View Announcement'), 'btn-success', 'mdi mdi-eye', ['announcement_slug' => 'announcement_slug'], true)

        ->addClass('content', 'minimal')
        ->mergeField('start_date, end_date')
        ->fieldPosition([
            'cover' => 2,
            'language_id' => 2,
            'status' => 2,
            'created_at' => 2,
            'created_by' => 2
        ])
        ->columnSize([
            1 => 'col-md-8',
            2 => 'col-md-4'
        ])
        ->setValidation([
            'title' => 'required|max_length[256]|unique[' . $this->_table . '.title.announcement_id.' . $this->request->getGet('announcement_id') . ']',
            'content' => 'required',
            'language_id' => 'required',
            'start_date' => 'required|callback_validateStartDate',
            'end_date' => 'required|callback_validateEndDate',
            'status' => 'boolean'
        ])
        ->setAlias([
            'title' => phrase('Title'),
            'announcement_slug' => phrase('Slug'),
            'content' => phrase('Content'),
            'placement' => phrase('Placement'),
            'start_date' => phrase('Start Date'),
            'end_date' => phrase('End Date'),
            'language_id' => phrase('Language'),
            'status' => phrase('Status'),
            'created_at' => phrase('Created At'),
            'created_by' => phrase('Author')
        ])

        ->defaultValue([
            'placement' => 0,
            'start_date' => date('Y-m-d'),
            'end_date' => date('Y-m-d', strtotime('+1 day'))
        ])

        ->modalSize('modal-xl')

        ->render($this->_table);
    }

    public function validateStartDate($value = '')
    {
        if (strtotime($value) < strtotime(date('Y-m-d'))) {
            $this->formValidation->setError('start_date', 'The start date must be greater than or equal to today');
        }

        return true;
    }

    public function validateEndDate($value = '')
    {
        if (strtotime($value) <= strtotime(service('request')->getPost('start_date') ?? '')) {
            $this->formValidation->setError('end_date', 'The end date must be greater than start date');
        }

        return true;
    }

    private function _filter()
    {
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
            'language' => [
                'type' => 'select',
                'label' => phrase('Language'),
                'values' => $languages
            ]
        ];
    }
}
