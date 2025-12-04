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

namespace Aksara\Modules\Administrative\Controllers\Years;

class Years extends \Aksara\Laboratory\Core
{
    private $_table = 'app__years';

    public function __construct()
    {
        parent::__construct();

        $this->restrict_on_demo();

        $this->set_permission();
        $this->set_theme('backend');

        $this->unset_method('clone');

        $this->set_primary('year');
    }

    public function index()
    {
        $this->set_title(phrase('Years'))
        ->set_icon('mdi mdi-calendar-clock')
        ->set_field([
            'default' => 'boolean',
            'status' => 'boolean'
        ])
        ->set_validation([
            'year' => 'required|numeric|max_length[4]',
            'default' => 'boolean',
            'status' => 'boolean'
        ])
        ->set_alias([
            'year' => phrase('Year'),
            'default' => phrase('Default'),
            'status' => phrase('Status')
        ])
        ->render($this->_table);
    }

    public function before_insert()
    {
        if (service('request')->getPost('default')) {
            $this->model->update(
                $this->_table,
                [
                    'default' => 0
                ],
                [
                    'default' => 1
                ]
            );
        }
    }

    public function before_update()
    {
        if (service('request')->getPost('default')) {
            $this->model->update(
                $this->_table,
                [
                    'default' => 0
                ],
                [
                    'default' => 1
                ]
            );
        }
    }
}
