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

namespace Aksara\Modules\Xhr\Controllers;

class Boot extends \Aksara\Laboratory\Core
{
    public function index()
    {
        $phrases = array_map('trim', explode(',', 'Sunday, Monday, Tuesday, Wednesday, Thursday, Friday, Saturday, January, February, March, April, May, June, July, August, September, October, November, December'));

        foreach ($phrases as $key => $val) {
            phrase($val);
        }

        return throw_exception(301, phrase('The Aksara services was successfully started!'), base_url(), true);
    }
}
