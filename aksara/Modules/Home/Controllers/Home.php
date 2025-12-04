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

namespace Aksara\Modules\Home\Controllers;

class Home extends \Aksara\Laboratory\Core
{
    public function index()
    {
        $this->set_title(phrase('Welcome to') . ' ' . get_setting('app_name'))
        ->set_description(get_setting('app_description'))

        ->set_output([
            'error' => $this->_validate(),
            'permission' => [
                'uploads' => (is_dir(FCPATH . UPLOAD_PATH) && is_writable(FCPATH . UPLOAD_PATH) ? true : false),
                'writable' => (is_dir(WRITEPATH) && is_writable(WRITEPATH) ? true : false)
            ]
        ])

        ->render();
    }

    /**
     * This validation indicates the installation whether success or not
     */
    private function _validate()
    {
        $query = $this->model->get_where(
            'blogs',
            [
            ],
            1
        )
        ->row();

        if ($query) {
            return true;
        }

        return false;
    }
}
