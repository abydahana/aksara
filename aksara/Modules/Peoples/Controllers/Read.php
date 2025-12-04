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

namespace Aksara\Modules\Peoples\Controllers;

class Read extends \Aksara\Laboratory\Core
{
    private $_table = 'peoples';

    public function __construct()
    {
        parent::__construct();

        $this->searchable(false);
    }

    public function index($slug = null)
    {
        if (service('request')->getGet('people_slug')) {
            $slug = service('request')->getGet('people_slug');
        }

        $this->set_title('{{ first_name }} {{ last_name }}', phrase('The people you are looking for was not found!'))
        ->set_description('{{ biography }}')
        ->set_icon('mdi mdi-account-outline')
        ->set_output(
            'similar',
            $this->model
            ->get_where(
                $this->_table,
                [
                    'people_slug !=' => $slug
                ],
                4
            )
            ->result()
        )
        ->where('people_slug', $slug)
        ->limit(1)

        ->render($this->_table);
    }
}
