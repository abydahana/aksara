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

namespace Aksara\Modules\Shortlink\Controllers;

class Shortlink extends \Aksara\Laboratory\Core
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index($params = '')
    {
        $query = null;

        if ($this->model->table_exists('app__shortlinks')) {
            $query = $this->model->get_where(
                'app__shortlinks',
                [
                    'hash' => $params
                ],
                1
            )
            ->row();
        }

        if ($query) {
            // Set the one time temporary session
            if (! get_userdata('is_logged')) {
                $data = json_decode($query->data, true);
                $session['sess_destroy_after'] = 'once';

                set_userdata($data);
            }

            // Redirect to real URL
            return throw_exception(301, null, $query->url);
        } else {
            return throw_exception(404, phrase('The page you requested does not exist or already been archived.'), base_url());
        }
    }
}
