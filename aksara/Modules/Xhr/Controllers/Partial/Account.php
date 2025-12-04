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

namespace Aksara\Modules\Xhr\Controllers\Partial;

class Account extends \Aksara\Laboratory\Core
{
    public function __construct()
    {
        parent::__construct();

        if ('modal' != service('request')->getPost('prefer')) {
            return throw_exception(404, phrase('The page you requested does not exist or already been archived.'));
        } elseif (! get_userdata('user_id')) {
            return throw_exception(403, phrase('You do not have a sufficient privileges to access the requested page.'), base_url());
        }
    }

    public function index()
    {
        $this->set_title(phrase('Option'))
        ->set_icon('mdi mdi-cogs')

        ->set_output([
            'user' => $this->_user()
        ])

        ->render();
    }

    private function _user()
    {
        $query = $this->model->get_where(
            'app__users',
            [
                'user_id' => get_userdata('user_id')
            ]
        )
        ->result();

        return $query;
    }
}
