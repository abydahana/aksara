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

namespace Aksara\Modules\Cms\Controllers\Comments;

class Comments extends \Aksara\Laboratory\Core
{
    private $_table = 'post__comments';

    public function __construct()
    {
        parent::__construct();

        $this->set_permission();
        $this->set_theme('backend');

        $this->unset_method('create, update, clone,  delete');
    }

    public function index()
    {
        $this->set_title(phrase('Comments'))
        ->set_icon('mdi mdi-comment-multiple-outline')
        ->unset_column('post_id, post_type, reply_id, mention_id, edited, attachment')
        ->unset_view('post_id, post_type, reply_id, mention_id, edited, attachment')

        ->column_order('first_name, post_id, post_path, comments, timestamp, status')

        ->add_button('hide', phrase('Review'), 'btn btn-danger --modal', 'mdi mdi-toggle-switch', ['id' => 'comment_id'])

        ->set_field([
            'comments' => 'textarea',
            'status' => 'boolean'
        ])
        ->set_field('first_name', 'hyperlink', 'user', ['user_id' => 'user_id'], true)
        ->set_field('post_path', 'hyperlink', '{{ post_path }}', ['user_id' => 'user_id'], true)

        ->set_relation(
            'user_id',
            'app__users.user_id',
            '{{ app__users.first_name }} {{ app__users.last_name }}'
        )

        ->merge_content('{{ first_name }} {{ last_name }}', phrase('Full Name'))
        ->merge_content('{{ comment_id }}', phrase('Feedback'), 'callback_get_feedback')

        ->order_by('timestamp', 'DESC')

        ->render($this->_table);
    }

    public function hide()
    {
        $this->permission->must_ajax(current_page('../'));

        $comment_id = (service('request')->getGet('id') ? service('request')->getGet('id') : 0);

        $query = $this->model->get_where(
            $this->_table,
            [
                'comment_id' => $comment_id
            ],
            1
        )
        ->row();

        if (! $query) {
            return throw_exception(404, phrase('The comment you would to ' . ($query->status ? 'hide' : 'publish') . ' is not found', current_page('../')));
        }

        if (service('request')->getPost('comment_id') == sha1($comment_id . ENCRYPTION_KEY . get_userdata('session_generated'))) {
            if (DEMO_MODE) {
                // Demo mode
                return throw_exception(403, phrase('This feature is disabled in demo mode.'), go_to(null, ['id' => null]));
            }

            $this->model->update(
                $this->_table,
                [
                    'status' => ($query->status ? 0 : 1)
                ],
                [
                    'comment_id' => $comment_id
                ]
            );

            return throw_exception(301, phrase(($query->status ? phrase('The selected comment was successfully hidden.') : phrase('The selected comment was successfully published.'))), current_page('../'));
        }

        $html = '
            <form action="' . current_page() . '" method="POST" class="--validate-form">
                <input type="hidden" name="comment_id" value="' . sha1($comment_id . ENCRYPTION_KEY . get_userdata('session_generated')) . '" />
                <div class="text-center pt-3 pb-3 mb-3">
                    ' . phrase('Are you sure want to ' . ($query->status ? 'hide' : 'publish') . ' this comment?').  '
                </div>
                <hr class="border-secondary-subtle mx--3" />
                <div class="row">
                    <div class="col-6">
                        <div class="d-grid">
                            <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">
                                <i class="mdi mdi-window-close"></i>
                                ' . phrase('Cancel') . '
                            </button>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="d-grid">
                            <button type="submit" class="btn btn-dark rounded-pill">
                                <i class="mdi mdi-check"></i>
                                ' . ($query->status ? phrase('Hide') : phrase('Publish')) . '
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        ';

        return make_json([
            'status' => 200,
            'meta' => [
                'popup' => true
            ],
            'content' => $html
        ]);
    }

    public function get_feedback($params = [])
    {
        if (! isset($params['comment_id'])) {
            return false;
        }

        $query = $this->model->get_where(
            'post__comments_reports',
            [
                'comment_id' => $params['comment_id']
            ]
        )
        ->num_rows();

        if ($query) {
            return '<a href="' . current_page('feedback', ['id' => $params['comment_id'], 'column' => null, 'q' => null, 'per_page' => null, 'order' => null, 'sort' => null]) . '" class="badge bg-danger --xhr">' . number_format($query) . ' ' . ($query > 1 ? phrase('reports') : phrase('report')) . '</a>';
        }

        return false;
    }
}
