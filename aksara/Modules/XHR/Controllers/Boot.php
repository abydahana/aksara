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
 * When the signs come, those who don't believe at "that time"
 * will have only two choices, commit suicide or become brutal.
 */

namespace Aksara\Modules\XHR\Controllers;

use Aksara\Laboratory\Core;

class Boot extends Core
{
    public function index()
    {
        $phrases = array_map('trim', explode(',', 'Sunday, Monday, Tuesday, Wednesday, Thursday, Friday, Saturday, Sun, Mon, Tue, Wed, Thu, Fri, Sat, January, February, March, April, May, June, July, August, September, October, November, December, Jan, Feb, Mar, Apr, May, Jun, Jul, Aug, Sep, Oct, Nov, Dec, now, second, seconds, minute, minutes, hour, hours, day, days, month, months, year, years'));

        foreach ($phrases as $key => $val) {
            phrase($val);
        }

        return throw_exception(301, phrase('The Aksara services was successfully started!'), base_url(), true);
    }
}
