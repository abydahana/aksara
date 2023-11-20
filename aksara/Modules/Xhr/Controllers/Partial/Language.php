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

class Language extends \Aksara\Laboratory\Core
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        // Validate request
        if ('dropdown' == service('request')->getPost('prefer')) {
            // Prefer dropdown
            return $this->_languages(true);
        } elseif ('modal' != service('request')->getPost('prefer')) {
            // Redirect non modal request
            return throw_exception(404, phrase('The page you requested does not exist or already been archived.'));
        }

        $this->set_title(phrase('Change Language'))
        ->set_icon('mdi mdi-translate')

        ->set_output([
            'languages' => $this->_languages()
        ])

        ->render();
    }

    private function _languages($json = false)
    {
        $query = $this->model->get_where(
            'app__languages',
            [
                'status' => 1
            ]
        )
        ->result();

        if ($json) {
            return make_json($query);
        }

        return $query;
    }
}
