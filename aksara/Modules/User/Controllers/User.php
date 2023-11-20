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

namespace Aksara\Modules\User\Controllers;

class User extends \Aksara\Laboratory\Core
{
    private $_table = 'app__users';

    public function __construct()
    {
        parent::__construct();

        $this->searchable(false);

        if (service('request')->getGet('user_id')) {
            $query = $this->model->select('
                username
            ')
            ->get_where(
                $this->_table,
                [
                    'user_id' => service('request')->getGet('user_id')
                ],
                1
            )
            ->row('username');

            if ($query) {
                return throw_exception(301, null, current_page($query, ['user_id' => null]), true);
            }
        }
    }

    public function index($username = '', $tab = null)
    {
        if ($username) {
            $this->where('username', $username);
        } elseif (service('request')->getGet('user_id') > 0) {
            $this->where('user_id', service('request')->getGet('user_id'));
        } else {
            $this->where('user_id', get_userdata('user_id'));
        }

        $this->set_title('{{ first_name }} {{ last_name }}', phrase('User not found'))
        ->set_icon('mdi mdi-account')

        ->set_output([
            'suggestions' => $this->model->select('
                username,
                first_name,
                last_name
            ')
            ->order_by('username', 'RANDOM')
            ->get_where(
                $this->_table,
                [
                    'status' => 1,
                    'username != ' => ''
                ],
                8
            )
            ->result()
        ])
        ->limit(1)
        ->render($this->_table);
    }
}
