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

use Aksara\Laboratory\Core;

class Announcements extends Core
{
    private $_table = 'announcements';

    public function __construct()
    {
        parent::__construct();

        $this->restrict_on_demo();

        $this->set_permission();
        $this->set_theme('backend');

        $this->unset_method('clone');

        // Ignore query string signature
        $this->ignore_query_string('language');
    }

    public function index()
    {
        $this->add_filter($this->_filter());

        if ($this->request->getGet('language')) {
            $this->where('language_id', $this->request->getGet('language'));
        }

        $this->set_title(phrase('Announcements'))
        ->set_icon('mdi mdi-bullhorn-outline')
        ->set_primary('announcement_id')
        ->unset_column('announcement_id, content, created_timestamp, updated_timestamp, announcement_slug, language')
        ->unset_field('announcement_id')
        ->unset_view('announcement_id')
        ->column_order('cover')
        ->set_field([
            'content' => 'wysiwyg',
            'cover' => 'image',
            'start_date' => 'date',
            'end_date' => 'date',
            'created_timestamp' => 'created_timestamp',
            'updated_timestamp' => 'updated_timestamp',
            'status' => 'boolean'
        ])
        ->set_field(
            'placement',
            'radio',
            [
                0 => phrase('Front End'),
                1 => phrase('Back End')
            ]
        )
        ->set_field('announcement_slug', 'slug', 'title')
        ->set_field('announcement_title', 'hyperlink', 'announcements', ['announcement_slug' => 'announcement_slug'], true)

        ->add_button('../../../announcements/get', phrase('View Announcement'), 'btn-success', 'mdi mdi-eye', ['announcement_slug' => 'announcement_slug'], true)

        ->add_class('content', 'minimal')
        ->merge_field('start_date, end_date')
        ->set_relation(
            'language_id',
            'app__languages.id',
            '{{ app__languages.language }}',
            [
                'app__languages.status' => 1
            ]
        )
        ->field_position([
            'placement' => 2,
            'start_date' => 2,
            'end_date' => 2,
            'cover' => 2,
            'language_id' => 2,
            'status' => 2
        ])
        ->column_size([
            1 => 'col-md-8',
            2 => 'col-md-4'
        ])
        ->set_validation([
            'title' => 'required|max_length[256]|unique[' . $this->_table . '.title.announcement_id.' . $this->request->getGet('announcement_id') . ']',
            'content' => 'required',
            'language_id' => 'required',
            'start_date' => 'required',
            'end_date' => 'required|callback_validate_end_date',
            'status' => 'boolean'
        ])
        ->set_alias([
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

        ->default_value('placement', 0)

        ->order_by('updated_timestamp', 'DESC')

        ->modal_size('modal-xl')

        ->render($this->_table);
    }

    public function validate_end_date($value = null)
    {
        if (strtotime($this->request->getPost('start_date')) >= strtotime($value)) {
            $this->form_validation->setError('start_date', 'The end date must be greater than start date');
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
