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

namespace Aksara\Modules\Auth\Controllers;

class Integrator extends \Aksara\Laboratory\Core
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        return make_json([
            'user_id' => get_userdata('user_id'),
            'group_id' => (get_userdata('group_id') <= 3 ? get_userdata('group_id') : 3),
            'username' => get_userdata('username'),
            'email' => get_userdata('email'),
            'first_name' => get_userdata('first_name'),
            'last_name' => get_userdata('last_name'),
            'gender' => get_userdata('gender'),
            'avatar' => get_image('users', get_userdata('photo'), 'thumb'),
            'ip_address' => (service('request')->hasHeader('x-forwarded-for') ? service('request')->getHeaderLine('x-forwarded-for') : service('request')->getIPAddress())
        ]);
    }
}
