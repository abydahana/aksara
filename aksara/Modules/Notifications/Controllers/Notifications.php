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

namespace Aksara\Modules\Notifications\Controllers;

class Notifications extends \Aksara\Laboratory\Core
{
    private $_table = 'notifications';

    public function __construct()
    {
        parent::__construct();

        if (! get_userdata('is_logged')) {
            return throw_exception(403, phrase('You do not have a sufficient privileges to access this page.'));
        }
    }

    public function index()
    {
        $this->set_title(phrase('Notifications'))
        ->set_icon('mdi mdi-bell-ring')
        ->render($this->_table);
    }

    public function partials()
    {
        $query = $this->model->select('
            app__users.first_name,
            app__users.last_name,
            app__users.photo AS avatar,
            notifications.type,
            notifications.interaction_id,
            notifications.path AS url,
            notifications.timestamp
        ')
        ->join(
            'app__users',
            'app__users.user_id = notifications.from_user'
        )
        ->order_by('notifications.timestamp', 'DESC')
        ->get_where(
            $this->_table,
            [
                'from_user != ' => get_userdata('user_id'),
                'to_user' => get_userdata('user_id')
            ],
            10
        )
        ->result();

        if ($query) {
            // Update notification status
            $this->model->update(
                $this->_table,
                [
                    'opened' => 1
                ],
                [
                    'to_user' => get_userdata('user_id')
                ]
            );

            foreach ($query as $key => $val) {
                $query_string = [];

                if (in_array($val->type, ['comment', 'reply', 'upvote'])) {
                    $query_string['comment_highlight'] = $val->interaction_id;
                }

                $query[$key]->user = $val->first_name . ' ' . $val->last_name;
                $query[$key]->avatar = get_image('users', $val->avatar, 'icon');
                $query[$key]->text = ('comment' === $val->type ? phrase('commented on your post.') : ('reply' === $val->type ? phrase('replying to your comment.') : ('like' === $val->type ? phrase('likes your post') : ('upvote' === $val->type ? phrase('upvoted your comment.') : phrase('reacted to your post.')))));
                $query[$key]->url = base_url($val->url, $query_string);
                $query[$key]->timestamp = time_ago($val->timestamp);
            }
        }

        return make_json($query);
    }

    public function polling()
    {
        $query = $this->model->select_count('*', 'total')->get_where(
            $this->_table,
            [
                'from_user != ' => get_userdata('user_id'),
                'to_user' => get_userdata('user_id'),
                'opened' => 0
            ]
        )
        ->row('total');

        return make_json([
            'unread' => ($query < 100 ? $query : '99+')
        ]);
    }
}
