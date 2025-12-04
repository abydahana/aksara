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

class About extends \Aksara\Laboratory\Core
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->set_title(phrase('About the project'))
        ->set_icon('mdi mdi-information-outline')
        ->set_description(phrase('Including the credit of external resources.'))

        ->render();
    }
}
