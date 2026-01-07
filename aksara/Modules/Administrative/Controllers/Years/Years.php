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

use Aksara\Laboratory\Core;

class Years extends Core
{
    private $_table = 'app__years';

    public function __construct()
    {
        parent::__construct();

        $this->restrictOnDemo();

        $this->setPermission();
        $this->setTheme('backend');

        $this->unsetMethod('clone');

        $this->setPrimary('year');
    }

    public function index()
    {
        $this->setTitle(phrase('Years'))
        ->setIcon('mdi mdi-calendar-clock')
        ->setField([
            'default' => 'boolean',
            'status' => 'boolean'
        ])
        ->setValidation([
            'year' => 'required|numeric|max_length[4]',
            'default' => 'boolean',
            'status' => 'boolean'
        ])
        ->setAlias([
            'year' => phrase('Year'),
            'default' => phrase('Default'),
            'status' => phrase('Status')
        ])
        ->render($this->_table);
    }

    public function beforeInsert()
    {
        if ($this->request->getPost('default')) {
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

    public function beforeUpdate()
    {
        if ($this->request->getPost('default')) {
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
