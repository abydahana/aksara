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

namespace Aksara\Modules\CMS\Controllers\Comments;

use Aksara\Laboratory\Core;

class Comments extends Core
{
    private $_table = 'post__comments';

    public function __construct()
    {
        parent::__construct();

        $this->setPermission();
        $this->setTheme('backend');

        $this->unsetMethod('create, update, clone,  delete');
    }

    public function index()
    {
        $this->setTitle(phrase('Comments'))
        ->setIcon('mdi mdi-comment-multiple-outline')
        ->unsetColumn('post_id, post_type, reply_id, mention_id, edited, attachment')
        ->unsetView('post_id, post_type, reply_id, mention_id, edited, attachment')

        ->columnOrder('first_name, post_id, post_path, comments, timestamp, status')

        ->addButton('hide', phrase('Review'), 'btn btn-danger --modal', 'mdi mdi-toggle-switch', ['id' => 'comment_id'])

        ->setField([
            'comments' => 'textarea',
            'status' => 'boolean'
        ])
        ->setField('first_name', 'hyperlink', 'user', ['user_id' => 'user_id'], true)
        ->setField('post_path', 'hyperlink', '{{ post_path }}', ['user_id' => 'user_id'], true)

        ->setRelation(
            'user_id',
            'app__users.user_id',
            '{{ app__users.first_name }} {{ app__users.last_name }}'
        )

        ->mergeContent('{{ first_name }} {{ last_name }}', phrase('Full Name'))
        ->mergeContent('{{ comment_id }}', phrase('Feedback'), 'callback_get_feedback')

        ->orderBy('timestamp', 'DESC')

        ->render($this->_table);
    }

    public function hide()
    {
        $this->permission->must_ajax(current_page('../'));

        $commentId = ($this->request->getGet('id') ? $this->request->getGet('id') : 0);

        $query = $this->model->getWhere(
            $this->_table,
            [
                'comment_id' => $commentId
            ],
            1
        )
        ->row();

        if (! $query) {
            return throw_exception(404, phrase('The comment you want to hide is not found', current_page('../')));
        }

        if ($this->request->getPost('comment_id') == sha1($commentId . ENCRYPTION_KEY . get_userdata('session_generated'))) {
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
                    'comment_id' => $commentId
                ]
            );

            return throw_exception(301, phrase(($query->status ? phrase('The selected comment was successfully hidden.') : phrase('The selected comment was successfully published.'))), current_page('../'));
        }

        $html = '
            <form action="' . current_page() . '" method="POST" class="--validate-form">
                <input type="hidden" name="comment_id" value="' . sha1($commentId . ENCRYPTION_KEY . get_userdata('session_generated')) . '" />
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

    public function getFeedback($params = [])
    {
        if (! isset($params['comment_id'])) {
            return false;
        }

        $query = $this->model->getWhere(
            'post__comments_reports',
            [
                'comment_id' => $params['comment_id']
            ]
        )
        ->numRows();

        if ($query) {
            return '<a href="' . current_page('feedback', ['id' => $params['comment_id'], 'column' => null, 'q' => null, 'per_page' => null, 'order' => null, 'sort' => null]) . '" class="badge bg-danger --xhr">' . number_format($query) . ' ' . ($query > 1 ? phrase('reports') : phrase('report')) . '</a>';
        }

        return false;
    }
}
