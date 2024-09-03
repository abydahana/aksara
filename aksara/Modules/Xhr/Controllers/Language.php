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

class Language extends \Aksara\Laboratory\Core
{
    public function __construct()
    {
        parent::__construct();

        $this->permission->must_ajax(base_url());
    }

    public function index($params = null)
    {
        $query = $this->model->select('id')->get_where(
            'app__languages',
            [
                'code' => $params
            ],
            1
        )
        ->row('id');

        if ($query) {
            set_userdata('language', $params);
            set_userdata('language_id', $query);

            if (get_userdata('is_logged') && ! DEMO_MODE) {
                $this->model->update('app__users', ['language_id' => $query], ['user_id' => get_userdata('user_id')]);
            }
        }

        return throw_exception(301, null, service('request')->getServer('HTTP_REFERER'), true);
    }
}
