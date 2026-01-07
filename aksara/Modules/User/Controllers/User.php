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

use Aksara\Laboratory\Core;

class User extends Core
{
    private $_table = 'app__users';

    public function __construct()
    {
        parent::__construct();

        $this->searchable(false);
        $this->limit(10);

        if ($this->request->getGet('user_id')) {
            $query = $this->model->select('
                username
            ')
            ->getWhere(
                $this->_table,
                [
                    'user_id' => $this->request->getGet('user_id')
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
        } elseif ($this->request->getGet('user_id') > 0) {
            $this->where('user_id', $this->request->getGet('user_id'));
        } else {
            $this->where('user_id', get_userdata('user_id'));
        }

        $this->setTitle('{{ first_name }} {{ last_name }}', phrase('User not found'))
        ->setIcon('mdi mdi-account')

        ->setOutput([
            'suggestions' => $this->model->select('
                user_id,
                username,
                first_name,
                last_name
            ')
            ->orderBy('username', 'RANDOM')
            ->getWhere(
                $this->_table,
                [
                    'status' => 1,
                    'username != ' => ''
                ],
                8
            )
            ->result()
        ])
        ->select('
            (SELECT MAX(timestamp) FROM app__log_activities WHERE user_id = app__users.user_id) AS last_activity
        ')
        ->limit(1)
        ->render($this->_table);
    }

    public function activities($username = '')
    {
        $user = $this->model->select('
            user_id,
            username,
            first_name,
            last_name,
            photo
        ')
        ->getWhere(
            $this->_table,
            [
                'username' => $username
            ],
            1
        )
        ->row();

        if ($user) {
            $title = phrase('{{user}}\'s Activities', ['user' => $user->first_name . ' ' . $user->last_name]);
        } else {
            $title = phrase('Activities');
        }

        $this->setTitle($title)
        ->setIcon('mdi mdi-account-clock-outline')
        ->setOutput([
            'user' => $user
        ])
        ->join(
            'app__users',
            'app__users.user_id = post__comments.user_id'
        )
        ->where([
            'app__users.username' => $username,
            'post__comments.status' => 1
        ])
        ->orderBy([
            'post__comments.timestamp' => 'DESC'
        ])
        ->render('post__comments', 'activities');
    }

    public function likes($username = '')
    {
        $user = $this->model->select('
            user_id,
            username,
            first_name,
            last_name,
            photo
        ')
        ->getWhere(
            $this->_table,
            [
                'username' => $username
            ],
            1
        )
        ->row();

        if ($user) {
            $title = phrase('{{user}}\'s Likes', ['user' => $user->first_name . ' ' . $user->last_name]);
        } else {
            $title = phrase('Likes');
        }

        $this->setTitle($title)
        ->setIcon('mdi mdi-heart')
        ->setOutput([
            'user' => $user
        ])
        ->join(
            'app__users',
            'app__users.user_id = post__likes.user_id'
        )
        ->where([
            'app__users.username' => $username
        ])
        ->orderBy([
            'post__likes.timestamp' => 'DESC'
        ])
        ->render('post__likes', 'likes');
    }

    public function guestbook($username = '')
    {
        $user = $this->model->select('
            user_id,
            username,
            first_name,
            last_name,
            photo
        ')
        ->getWhere(
            $this->_table,
            [
                'username' => $username
            ],
            1
        )
        ->row();

        if ($user) {
            $title = phrase('The Guest Book of {{user}}', ['user' => $user->first_name . ' ' . $user->last_name]);
        } else {
            $title = phrase('Guest Book');
        }

        $this->setTitle($title)
        ->setIcon('mdi mdi-book')
        ->setOutput([
            'user' => $user
        ])
        ->render();
    }
}
