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

namespace Aksara\Modules\Cms\Controllers;

class Cms extends \Aksara\Laboratory\Core
{
    public function __construct()
    {
        parent::__construct();

        $this->set_permission();
        $this->set_theme('backend');

        $this->set_method('index');
    }

    public function index()
    {
        $this->set_title(phrase('Content Management System'))
        ->set_icon('mdi mdi-briefcase-outline')

        ->render();
    }
}
