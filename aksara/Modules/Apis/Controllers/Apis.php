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

namespace Aksara\Modules\Apis\Controllers;

use Aksara\Laboratory\Core;

class Apis extends Core
{
    public function __construct()
    {
        parent::__construct();

        $this->setPermission();
        $this->setTheme('backend');

        $this->setMethod('index');
    }

    public function index()
    {
        $this->setTitle(phrase('API Management'))
        ->setIcon('mdi mdi-code-braces')

        ->render();
    }
}
