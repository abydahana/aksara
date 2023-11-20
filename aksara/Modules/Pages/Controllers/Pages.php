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

namespace Aksara\Modules\Pages\Controllers;

class Pages extends \Aksara\Laboratory\Core
{
    private $_table = 'pages';

    public function __construct()
    {
        parent::__construct();

        $this->searchable(false);
    }

    public function index($slug = null)
    {
        $this->set_title('{{ page_title }}', phrase('Page not found'))
        ->set_description('{{ page_description }}')
        ->set_icon('mdi mdi-file-document-outline')
        ->set_output([
            'suggestions' => $this->model->select('
                page_slug,
                page_title
            ')
            ->get_where(
                $this->_table,
                [
                    'status' => 1,
                    'language_id' => get_userdata('language_id')
                ],
                8
            )
            ->result()
        ])
        ->set_relation(
            'faq_id',
            'pages__faqs.faq_id',
            '{{ pages__faqs.faq_content }}',
            [
                'pages__faqs.status' => 1
            ]
        )
        ->set_relation(
            'carousel_id',
            'pages__carousels.carousel_id',
            '{{ pages__carousels.carousel_content }}',
            [
                'pages__carousels.status' => 1
            ]
        )
        ->group_start()
        ->where('pages.page_slug', $slug)
        ->or_where('pages.page_id', service('request')->getGet('page_id') ?? 0)
        ->group_end()
        ->where('status', 1)
        ->order_by('(CASE WHEN pages.language_id = ' . get_userdata('language_id') . ' THEN 1 ELSE 2 END)', 'ASC')
        ->limit(1)

        ->render($this->_table);
    }

    public function not_found()
    {
        $this->set_title(phrase('Page not found'))
        ->set_description(phrase('The page you requested does not exists or already been archived'))
        ->set_output([
            'suggestions' => $this->model->select('
                page_slug,
                page_title
            ')
            ->get_where(
                $this->_table,
                [
                    'status' => 1
                ],
                8
            )
            ->result()
        ])
        ->set_template('index', '404')
        ->render();
    }
}
