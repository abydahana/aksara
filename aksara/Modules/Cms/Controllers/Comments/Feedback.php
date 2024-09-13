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

class Feedback extends \Aksara\Laboratory\Core
{
    private $_table = 'post__comments_reports';

    private $_primary;

    public function __construct()
    {
        parent::__construct();

        $this->set_permission();
        $this->set_theme('backend');

        $this->unset_method('create, update, clone, delete');

        $this->_primary = (service('request')->getGet('id') ? service('request')->getGet('id') : 0);
    }

    public function index()
    {
        $query = $this->model->get_where(
            'post__comments',
            [
                'comment_id' => $this->_primary
            ],
            1
        )
        ->row();

        if ($query) {
            // Blogs type comment
            if ('blog' == $query->post_type) {
                $this->model->select('
                    blogs.post_slug,
                    blogs.post_title,
                    
                    blogs__categories.category_slug
                ')
                ->join(
                    'blogs',
                    'blogs.post_id = post__comments.post_id'
                )
                ->join(
                    'blogs__categories',
                    'blogs__categories.category_id = blogs.post_category'
                );
            }
        }

        $query = $this->model->select('
            post__comments.comment_id,
            
            app__users.user_id,
            app__users.first_name,
            app__users.last_name
        ')
        ->join(
            'app__users',
            'app__users.user_id = post__comments.user_id'
        )
        ->get_where(
            'post__comments',
            [
                'post__comments.comment_id' => $this->_primary
            ],
            1
        )
        ->row();

        if ($query) {
            $this->set_description('
                <div class="row border-bottom">
                    <div class="col-sm-4 col-md-2">
                        ' . phrase('User') . '
                    </div>
                    <div class="col-sm-8 col-md-10">
                        <a href="' . base_url('user', ['user_id' => $query->user_id]) . '" target="_blank">
                            <b>
                                ' . $query->first_name . ' ' . $query->last_name . '
                                <i class="mdi mdi-launch"></i>
                            </b>
                        </a>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-4 col-md-2">
                        ' . phrase('Post') . '
                    </div>
                    <div class="col-sm-8 col-md-10">
                        <a href="' . base_url('blogs/' . $query->category_slug . '/' . $query->post_slug, ['comment_highlight' => $query->comment_id]) . '" target="_blank">
                            <b>
                                ' . $query->post_title . '
                                <i class="mdi mdi-launch"></i>
                            </b>
                        </a>
                    </div>
                </div>
            ');
        }

        $this->set_title(phrase('Feedback'))
        ->set_icon('mdi mdi-file-alert-outline')
        ->unset_column('comment_id, post_id, reply_id, edited')
        ->unset_view('comment_id, post_id, reply_id, edited')

        ->column_order('first_name, message, timestamp')

        ->set_primary('comment_id, user_id')

        ->add_action('toolbar', '../hide', phrase('Review'), 'btn btn-danger --modal', 'mdi mdi-toggle-switch', ['id' => $this->_primary])

        ->set_field([
            'comments' => 'textarea',
            'status' => 'boolean'
        ])
        ->set_field('first_name', 'hyperlink', 'user', ['user_id' => 'user_id'], true)
        ->set_relation(
            'user_id',
            'app__users.user_id',
            '{{ app__users.first_name }} {{ app__users.last_name }}'
        )

        ->merge_content('{{ first_name }} {{ last_name }}', phrase('Full Name'))

        ->where([
            'comment_id' => $this->_primary
        ])

        ->order_by('timestamp', 'DESC')

        ->render($this->_table);
    }

    public function hide()
    {
        $this->permission->must_ajax(current_page('../'));

        $query = $this->model->get_where(
            'post__comments',
            [
                'comment_id' => $this->_primary
            ],
            1
        )
        ->row();

        if (! $query) {
            return throw_exception(404, phrase('The comment you want to ' . ($query->status ? 'hide' : 'publish') . ' is not found.', current_page('../')));
        }

        if (service('request')->getPost('comment_id') == sha1($this->_primary . ENCRYPTION_KEY . get_userdata('session_generated'))) {
            $this->model->update(
                $this->_table,
                [
                    'status' => ($query->status ? 0 : 1)
                ],
                [
                    'comment_id' => $this->_primary
                ]
            );

            return throw_exception(301, phrase(($query->status ? phrase('The selected comment was successfully hidden.') : phrase('The selected comment was successfully published.'))), current_page('../'));
        }

        $html = '
            <form action="' . current_page() . '" method="POST" class="--validate-form">
                <input type="hidden" name="comment_id" value="' . sha1($this->_primary . ENCRYPTION_KEY . get_userdata('session_generated')) . '" />
                <div class="text-center py-3">
                    ' . phrase('Are you sure want to ' . ($query->status ? 'hide' : 'publish') . ' this comment?').  '
                </div>
                <hr class="mx--3 border-secondary" />
                <div class="row">
                    <div class="col-6">
                        <div class="d-grid">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                                <i class="mdi mdi-window-close"></i>
                                ' . phrase('Cancel') . '
                            </button>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="d-grid">
                            <button type="submit" class="btn btn-danger">
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
                'title' => ($query->status ? phrase('Hide Comment') : phrase('Publish Comment')),
                'icon' => 'mdi mdi-toggle-switch',
                'popup' => true
            ],
            'content' => $html
        ]);
    }
}
