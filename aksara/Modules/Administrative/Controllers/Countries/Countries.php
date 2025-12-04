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

class Countries extends \Aksara\Laboratory\Core
{
    private $_table = 'app__countries';

    private $_primary;

    public function __construct()
    {
        parent::__construct();

        $this->restrict_on_demo();

        $this->set_permission();
        $this->set_theme('backend');

        $this->unset_method('clone');

        $this->_primary = service('request')->getGet('id');
    }

    public function index()
    {
        $this->set_title(phrase('Manage Countries'))
        ->set_icon('mdi mdi-map-legend')
        ->unset_column('id')
        ->unset_field('id')
        ->unset_view('id')
        ->set_field('status', 'boolean')
        ->set_validation([
            'code' => 'required|alpha_dash|max_length[5]|unique[' . $this->_table . '.code.id.' . $this->_primary . ']',
            'country' => 'required|alpha|max_length[32]',
            'status' => 'boolean'
        ])
        ->set_alias([
            'code' => phrase('Code'),
            'country' => phrase('Country'),
            'status' => phrase('Status')
        ])

        ->render($this->_table);
    }
}
