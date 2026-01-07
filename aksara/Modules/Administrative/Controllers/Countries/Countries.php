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

namespace Aksara\Modules\Administrative\Controllers\Countries;

use Aksara\Laboratory\Core;

class Countries extends Core
{
    private $_table = 'app__countries';

    private $_primary;

    public function __construct()
    {
        parent::__construct();

        $this->restrictOnDemo();

        $this->setPermission();
        $this->setTheme('backend');

        $this->unsetMethod('clone');

        $this->_primary = $this->request->getGet('id');
    }

    public function index()
    {
        $this->setTitle(phrase('Manage Countries'))
        ->setIcon('mdi mdi-map-legend')
        ->unsetColumn('id')
        ->unsetField('id')
        ->unsetView('id')
        ->setField('status', 'boolean')
        ->setValidation([
            'code' => 'required|alpha_dash|max_length[5]|unique[' . $this->_table . '.code.id.' . $this->_primary . ']',
            'country' => 'required|alpha|max_length[32]',
            'status' => 'boolean'
        ])
        ->setAlias([
            'code' => phrase('Code'),
            'country' => phrase('Country'),
            'status' => phrase('Status')
        ])

        ->render($this->_table);
    }
}
