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

class Announcements extends Core
{
    private $_table = 'announcements';

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
        ->setPrimary('announcement_id')
        ->unsetColumn('announcement_id, content, created_timestamp, updated_timestamp, announcement_slug, language')
        ->unsetField('announcement_id')
        ->unsetView('announcement_id')
        ->columnOrder('cover')
        ->setField([
            'content' => 'wysiwyg',
            'cover' => 'image',
            'start_date' => 'date',
            'end_date' => 'date',
            'created_timestamp' => 'created_timestamp',
            'updated_timestamp' => 'updated_timestamp',
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

        ->addButton('../../../announcements/get', phrase('View Announcement'), 'btn-success', 'mdi mdi-eye', ['announcement_slug' => 'announcement_slug'], true)

        ->addClass('content', 'minimal')
        ->mergeField('start_date, end_date')
        ->setRelation(
            'language_id',
            'app__languages.id',
            '{{ app__languages.language }}',
            [
                'app__languages.status' => 1
            ]
        )
        ->fieldPosition([
            'placement' => 2,
            'start_date' => 2,
            'end_date' => 2,
            'cover' => 2,
            'language_id' => 2,
            'status' => 2
        ])
        ->columnSize([
            1 => 'col-md-8',
            2 => 'col-md-4'
        ])
        ->setValidation([
            'title' => 'required|max_length[256]|unique[' . $this->_table . '.title.announcement_id.' . $this->request->getGet('announcement_id') . ']',
            'content' => 'required',
            'language_id' => 'required',
            'start_date' => 'required',
            'end_date' => 'required|callback_validate_end_date',
            'status' => 'boolean'
        ])
        ->setAlias([
            'title' => phrase('Title'),
            'announcement_slug' => phrase('Slug'),
            'content' => phrase('Content'),
            'placement' => phrase('Placement'),
            'start_date' => phrase('Start Date'),
            'end_date' => phrase('End Date'),
            'created_timestamp' => phrase('Created'),
            'updated_timestamp' => phrase('Updated'),
            'language_id' => phrase('Language'),
            'status' => phrase('Status')
        ])

        ->defaultValue('placement', 0)

        ->orderBy('updated_timestamp', 'DESC')

        ->modalSize('modal-xl')

        ->render($this->_table);
    }

    public function validateEndDate($value = null)
    {
        if (strtotime($this->request->getPost('start_date')) >= strtotime($value)) {
            $this->formValidation->setError('start_date', 'The end date must be greater than start date');
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
