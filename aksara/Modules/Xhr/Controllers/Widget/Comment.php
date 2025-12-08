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

namespace Aksara\Modules\Xhr\Controllers\Widget;

class Comment extends \Aksara\Laboratory\Core
{
    private $_table = 'post__comments';

    public function __construct()
    {
        parent::__construct();

        $this->permission->must_ajax();
        $this->limit(null);

        if (in_array(service('request')->getPost('fetch'), ['comments', 'replies'])) {
            return $this->_fetch_comments();
        } elseif ('token' === service('request')->getPost('fetch')) {
            $token = sha1(current_page() . ENCRYPTION_KEY . get_userdata('session_generated'));

            set_userdata(sha1(current_page() . get_userdata('session_generated') . ENCRYPTION_KEY), $token);

            return make_json([
                'token' => $token
            ]);
        }
    }

    public function index()
    {
        if ($this->valid_token(service('request')->getPost('_token'))) {
            return $this->_validate_form();
        }

        $this->set_title(phrase('Comments'))
        ->set_icon('mdi mdi-comment-multiple')

        ->set_output([
            'likes_count' => $this->model->get_where(
                'post__likes',
                [
                    'post_id' => service('request')->getGet('post_id'),
                    'post_path' => service('request')->getGet('path')
                ]
            )
            ->num_rows(),

            'comments_count' => $this->model->get_where(
                'post__comments',
                [
                    'post_id' => service('request')->getGet('post_id'),
                    'post_path' => service('request')->getGet('path'),
                    'status' => 1
                ]
            )
            ->num_rows()
        ])

        ->render();
    }

    public function repute()
    {
        if (! get_userdata('is_logged')) {
            return throw_exception(403, phrase('Please sign in to repute the post.'));
        }

        $query = $this->model->get_where(
            'post__likes',
            [
                'post_id' => (service('request')->getGet('post_id') ? service('request')->getGet('post_id') : 0),
                'post_path' => service('request')->getGet('path'),
                'user_id' => get_userdata('user_id')
            ]
        )
        ->row();

        if ($query) {
            $query = $this->model->delete(
                'post__likes',
                [
                    'post_id' => (service('request')->getGet('post_id') ? service('request')->getGet('post_id') : 0),
                    'post_path' => service('request')->getGet('path'),
                    'user_id' => get_userdata('user_id')
                ]
            );

            // Delete notification
            $this->model->delete(
                'notifications',
                [
                    'from_user' => get_userdata('user_id'),
                    'to_user' => get_userdata('user_id'),
                    'type' => 'like',
                    'path' => service('request')->getGet('path')
                ]
            );
        } else {
            $query = $this->model->insert(
                'post__likes',
                [
                    'post_id' => (service('request')->getGet('post_id') ? service('request')->getGet('post_id') : 0),
                    'post_path' => service('request')->getGet('path'),
                    'user_id' => get_userdata('user_id'),
                    'timestamp' => date('Y-m-d H:i:s')
                ]
            );

            // Insert notification
            $this->model->insert(
                'notifications',
                [
                    'from_user' => get_userdata('user_id'),
                    'to_user' => get_userdata('user_id'),
                    'type' => 'like',
                    'interaction_id' => (service('request')->getGet('post_id') ? service('request')->getGet('post_id') : 0),
                    'path' => service('request')->getGet('path'),
                    'timestamp' => date('Y-m-d H:i:s'),
                ]
            );
        }

        $upvotes = $this->model->get_where(
            'post__likes',
            [
                'post_id' => (service('request')->getGet('post_id') ? service('request')->getGet('post_id') : 0),
                'post_path' => service('request')->getGet('path')
            ]
        )
        ->num_rows();

        if ($upvotes > 999) {
            if ($upvotes < 1000000) {
                $upvotes = number_format($upvotes / 1000) . 'K';
            } elseif ($upvotes < 1000000000) {
                $upvotes = number_format($upvotes / 1000000, 2) . 'M';
            } else {
                $upvotes = number_format($upvotes / 1000000000, 2) . 'B';
            }
        }

        return make_json([
            'element' => '.likes-count',
            'content' => ($upvotes ? $upvotes : null),
            'class_add' => ($upvotes ? service('request')->getPost('classAdd') : service('request')->getPost('classRemove')),
            'class_remove' => (! $upvotes ? service('request')->getPost('classAdd') : service('request')->getPost('classRemove'))
        ]);
    }

    public function upvote()
    {
        if (! get_userdata('is_logged')) {
            return throw_exception(403, phrase('Please sign in to upvote the comment.'));
        }

        // Get interaction
        $interaction = $this->model->get_where(
            'post__comments',
            [
                'comment_id' => (service('request')->getGet('id') ? service('request')->getGet('id') : 0)
            ],
            1
        )
        ->row();

        if (! $interaction) {
            // No interaction is found
            return throw_exception(404, phrase('The interaction does not exists.'));
        }

        $query = $this->model->get_where(
            'post__comments_likes',
            [
                'comment_id' => (service('request')->getGet('id') ? service('request')->getGet('id') : 0),
                'user_id' => get_userdata('user_id')
            ]
        )
        ->row();

        if ($query) {
            $query = $this->model->delete(
                'post__comments_likes',
                [
                    'comment_id' => (service('request')->getGet('id') ? service('request')->getGet('id') : 0),
                    'user_id' => get_userdata('user_id')
                ]
            );

            // Insert notification
            $this->model->delete(
                'notifications',
                [
                    'from_user' => get_userdata('user_id'),
                    'to_user' => $interaction->user_id,
                    'type' => 'upvote',
                    'path' => service('request')->getGet('path')
                ]
            );
        } else {
            $query = $this->model->insert(
                'post__comments_likes',
                [
                    'comment_id' => (service('request')->getGet('id') ? service('request')->getGet('id') : 0),
                    'user_id' => get_userdata('user_id'),
                    'timestamp' => date('Y-m-d H:i:s')
                ]
            );

            // Insert notification
            $this->model->insert(
                'notifications',
                [
                    'from_user' => get_userdata('user_id'),
                    'to_user' => $interaction->user_id,
                    'type' => 'upvote',
                    'interaction_id' => (service('request')->getGet('id') ? service('request')->getGet('id') : 0),
                    'path' => service('request')->getGet('path'),
                    'timestamp' => date('Y-m-d H:i:s'),
                ]
            );
        }

        $upvotes = $this->model->get_where(
            'post__comments_likes',
            [
                'comment_id' => (service('request')->getGet('id') ? service('request')->getGet('id') : 0)
            ]
        )
        ->num_rows();

        if ($upvotes > 999) {
            if ($upvotes < 1000000) {
                $upvotes = number_format($upvotes / 1000) . 'K';
            } elseif ($upvotes < 1000000000) {
                $upvotes = number_format($upvotes / 1000000, 2) . 'M';
            } else {
                $upvotes = number_format($upvotes / 1000000000, 2) . 'B';
            }
        }

        return make_json([
            'element' => '#comment-upvote-' . service('request')->getGet('id'),
            'content' => ($upvotes ? $upvotes : null)
        ]);
    }

    public function update()
    {
        if (! get_userdata('is_logged')) {
            return throw_exception(403, phrase('Please sign in to update the comment.'));
        }

        $query = $this->model->get_where(
            $this->_table,
            [
                'comment_id' => (service('request')->getGet('id') ? service('request')->getGet('id') : 0)
            ],
            1
        )
        ->row();

        if (! $query) {
            return throw_exception(404, phrase('The comment you want to update was not found.'));
        }

        if (service('request')->getPost('comment_id') == sha1(service('request')->getGet('id') . ENCRYPTION_KEY . get_userdata('session_generated'))) {
            $this->form_validation->setRule('comments', phrase('Comments'), 'required');
            $this->form_validation->setRule('attachment', phrase('Attachment'), 'validate_upload[attachment.image]');

            if ($this->form_validation->run(service('request')->getPost()) === false) {
                return throw_exception(400, $this->form_validation->getErrors());
            }

            $attachment = '';
            $uploaded_files = get_userdata('_uploaded_files');

            // Check if the uploaded file is valid
            if (isset($uploaded_files['attachment']) && is_array($uploaded_files['attachment'])) {
                // Loop to get source from unknown array key
                foreach ($uploaded_files['attachment'] as $key => $src) {
                    // Set new source
                    $attachment = $src;
                }
            }

            // Insert to update history
            $this->model->insert(
                'post__comments_history',
                [
                    'comment_id' => $query->comment_id,
                    'comments' => $query->comments,
                    'attachment' => $query->attachment,
                    'timestamp' => $query->timestamp
                ]
            );

            // Update comment
            $this->model->update(
                $this->_table,
                [
                    'comments' => htmlspecialchars(service('request')->getPost('comments')),
                    'attachment' => $attachment,
                    'edited' => 1
                ],
                [
                    'comment_id' => service('request')->getGet('id')
                ]
            );

            return make_json([
                'element' => '#comment-text-' . service('request')->getGet('id'),
                'content' => ($attachment ? '<div><a href="' . get_image('comment', $attachment) . '" target="_blank"><img src="' . get_image('comment', $attachment, 'thumb') . '" class="img-fluid rounded mb-3" alt="..." /></a></div>' : null) . nl2br(htmlspecialchars(service('request')->getPost('comments')))
            ]);
        }

        $html = '
            <form action="' . current_page() . '" method="POST" class="--validate-form" enctype="multipart/form-data">
                <input type="hidden" name="comment_id" value="' . sha1(service('request')->getGet('id') . ENCRYPTION_KEY . get_userdata('session_generated')) . '" />
                <div class="form-group mb-3">
                    <label class="d-block text-muted" for="comments_input">
                        '. phrase('Comments') . '
                    </label>
                    <textarea name="comments" class="form-control" id="comments_input" placeholder="' . phrase('Type a comment') . '" rows="1">' . (isset($query->comments) ? $query->comments : null) . '</textarea>
                </div>
                <div class="form-group">
                    <label class="d-block text-muted" for="comments_input">
                        '. phrase('Attachment') . '
                    </label>
                    <div data-provides="fileupload" class="fileupload fileupload-new">
                        <span class="btn btn-file d-block">
                            <input type="file" name="attachment" accept="' . implode(',', preg_filter('/^/', '.', array_map('trim', explode(',', IMAGE_FORMAT_ALLOWED)))) . '" role="image-upload" id="attachment_input" />
                            <div class="fileupload-new text-center">
                                <img class="img-fluid upload_preview" src="' . get_image('comment', $query->attachment, 'thumb'). '" alt="..." />
                            </div>
                            <button type="button" class="btn btn-sm btn-danger rounded-circle position-absolute top-0 end-0" onclick="jExec($(this).closest(\'.btn-file\').find(\'input[type=file]\').val(\'\'), $(this).closest(\'.btn-file\').find(\'img\').attr(\'src\', \'' . get_image('comment', 'placeholder.png', 'icon') . '\'))">
                                <i class="mdi mdi-window-close"></i>
                            </button>
                        </span>
                    </div>
                </div>
                <hr class="mx--3" />
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
                            <button type="submit" class="btn btn-primary">
                                <i class="mdi mdi-check"></i>
                                ' . phrase('Update') . '
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        ';

        return make_json([
            'status' => 200,
            'meta' => [
                'title' => phrase('Update Comment'),
                'icon' => 'mdi mdi-square-edit-outline',
                'popup' => true
            ],
            'content' => $html,
            'reactivate' => true
        ]);
    }

    public function report()
    {
        if (! get_userdata('is_logged')) {
            return throw_exception(403, phrase('Please sign in to report the comment.'));
        }

        $query = $this->model->get_where(
            $this->_table,
            [
                'comment_id' => (service('request')->getGet('id') ? service('request')->getGet('id') : 0)
            ],
            1
        )
        ->row();

        if (! $query) {
            return throw_exception(404, phrase('The comment you want to report was not found.'));
        }

        if (service('request')->getPost('comment_id') == sha1(service('request')->getGet('id') . ENCRYPTION_KEY . get_userdata('session_generated'))) {
            $checker = $this->model->get_where(
                'post__comments_reports',
                [
                    'comment_id' => $query->comment_id,
                    'user_id' => get_userdata('user_id')
                ],
                1
            )
            ->row();

            if ($checker) {
                // Update feedback
                $this->model->update(
                    'post__comments_reports',
                    [
                        'message' => htmlspecialchars(service('request')->getPost('message')),
                        'timestamp' => $query->timestamp
                    ],
                    [
                        'comment_id' => $query->comment_id,
                        'user_id' => get_userdata('user_id')
                    ]
                );
            } else {
                // Insert feedback
                $this->model->insert(
                    'post__comments_reports',
                    [
                        'comment_id' => $query->comment_id,
                        'user_id' => get_userdata('user_id'),
                        'message' => htmlspecialchars(service('request')->getPost('message')),
                        'timestamp' => $query->timestamp
                    ]
                );
            }

            return throw_exception(200, phrase('Comment was successfully reported and queued for review.'));
        }

        $html = '
            <form action="' . current_page() . '" method="POST" class="--validate-form">
                <input type="hidden" name="comment_id" value="' . sha1(service('request')->getGet('id') . ENCRYPTION_KEY . get_userdata('session_generated')) . '" />
                <div class="text-center pt-3 pb-3 border-bottom">
                    ' . phrase('Are you sure want to report this comment?') . '
                </div>
                <div class="form-group">
                    <textarea name="message" class="form-control" id="message_input" placeholder="' . phrase('Write a feedback') . '" rows="1"></textarea>
                </div>
                <hr />
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
                                ' . phrase('Report') . '
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        ';

        return make_json([
            'status' => 200,
            'meta' => [
                'title' => phrase('Report Comment'),
                'icon' => 'mdi mdi-alert-outline',
                'popup' => true
            ],
            'content' => $html
        ]);
    }

    public function hide()
    {
        $query = $this->model->get_where(
            $this->_table,
            [
                'comment_id' => (service('request')->getGet('id') ? service('request')->getGet('id') : 0)
            ],
            1
        )
        ->row();

        if (! $query) {
            return throw_exception(404, phrase('The comment you want to hide was not found.'));
        }

        if (service('request')->getPost('comment_id') == sha1(service('request')->getGet('id') . ENCRYPTION_KEY . get_userdata('session_generated'))) {
            $this->model->update(
                $this->_table,
                [
                    'status' => ($query->status ? 0 : 1)
                ],
                [
                    'comment_id' => service('request')->getGet('id')
                ]
            );

            return make_json([
                'element' => '#comment-text-' . service('request')->getGet('id'),
                'content' => ($query->status ? '<i class="text-muted">' . phrase('Comment hidden') . '</i>' : $query->comments)
            ]);
        }

        $html = '
            <form action="' . current_page() . '" method="POST" class="--validate-form">
                <input type="hidden" name="comment_id" value="' . sha1(service('request')->getGet('id') . ENCRYPTION_KEY . get_userdata('session_generated')) . '" />
                <div class="text-center pt-3 pb-3 mb-3">
                    ' . ($query->status ? phrase('Are you sure want to hide this comment?') : phrase('Are you sure want to republish this comment?')) . '
                </div>
                <hr class="mx--3 border-secondary-subtle" />
                <div class="row">
                    <div class="col-6">
                        <div class="d-grid">
                            <button type="button" class="btn btn-light btn-sm rounded-pill" data-bs-dismiss="modal">
                                <i class="mdi mdi-window-close"></i>
                                ' . phrase('Cancel') . '
                            </button>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="d-grid">
                            <button type="submit" class="btn btn-dark btn-sm rounded-pill">
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
                'popup' => true,
                'modal_size' => 'modal-sm'
            ],
            'content' => $html
        ]);
    }

    private function _fetch_comments()
    {
        $limit = 10;
        $order = 'DESC';
        $page = (is_numeric(service('request')->getGet('page')) ? service('request')->getGet('page') : 0);
        $parent_id = service('request')->getGet('parent_id');

        if ($page) {
            $this->model->offset($limit * $page);
        }

        if ($parent_id) {
            $order = 'ASC';

            $this->model->where('post__comments.reply_id', $parent_id);
        } else {
            $this->model->where([
                'post__comments.post_id' => service('request')->getGet('post_id'),
                'post__comments.post_path' => service('request')->getGet('path'),
                'post__comments.reply_id' => 0
            ]);
        }

        $query = $this->model->select('
            post__comments.comment_id,
            post__comments.user_id,
            post__comments.post_id,
            post__comments.post_path,
            post__comments.reply_id,
            post__comments.mention_id,
            post__comments.comments,
            post__comments.attachment,
            post__comments.edited,
            post__comments.timestamp,
            post__comments.status,
            app__users.photo,
            app__users.username,
            app__users.first_name,
            app__users.last_name,
            COUNT(distinct replies_table.comment_id) AS replies,
            COUNT(distinct upvotes_table.comment_id) AS upvotes
        ')
        ->join(
            'app__users',
            'app__users.user_id = post__comments.user_id'
        )
        ->join(
            'post__comments replies_table',
            'replies_table.reply_id = post__comments.comment_id',
            'LEFT'
        )
        ->join(
            'post__comments_likes upvotes_table',
            'upvotes_table.comment_id = post__comments.comment_id',
            'LEFT'
        )
        ->group_by('post__comments.comment_id')
        ->order_by('post__comments.comment_id', $order)
        ->get_where(
            'post__comments',
            [],
            $limit
        )
        ->result();

        $output = [];

        if ($query) {
            foreach ($query as $key => $val) {
                // Get user photo
                $val->user_photo = get_image('users', get_userdata('photo'), 'icon');

                // Get commenter photo
                $val->photo = get_image('users', $val->photo, 'icon');

                // Create links
                $val->links = [
                    'profile_url' => base_url('user/' . $val->username),
                    'replies_url' => current_page(null, ['parent_id' => $val->comment_id, 'page' => null]),
                    'reply_url' => current_page('reply', ['id' => $val->comment_id, 'path' => service('request')->getGet('path'), 'reply' => service('request')->getGet('reply') ?? $val->comment_id]),
                    'upvote_url' => current_page('upvote', ['id' => $val->comment_id, 'path' => service('request')->getGet('path'), 'parent_id' => null]),
                    'report_url' => (get_userdata('user_id') !== $val->user_id ? current_page('report', ['id' => $val->comment_id, 'path' => service('request')->getGet('path'), 'parent_id' => null]) : null),
                    'update_url' => (get_userdata('user_id') === $val->user_id ? current_page('update', ['id' => $val->comment_id, 'path' => service('request')->getGet('path'), 'parent_id' => null]) : null),
                    'hide_url' => (get_userdata('user_id') === $val->user_id || in_array(get_userdata('group_id'), [1, 2]) ? current_page('hide', ['id' => $val->comment_id, 'path' => service('request')->getGet('path'), 'parent_id' => null]) : null)
                ];

                if ($val->attachment) {
                    // Set attachment url
                    $val->attachment = [
                        'original' => get_image('comment', $val->attachment),
                        'thumbnail' => get_image('comment', $val->attachment, 'thumb')
                    ];
                } else {
                    $val->attachment = [];
                }

                if ($val->mention_id) {
                    // Get mention
                    $mention = $this->model->select('
                        post__comments.comments,
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
                            'post__comments.comment_id' => $val->mention_id
                        ],
                        1
                    )
                    ->row();

                    if ($mention) {
                        // Add mention
                        $val->mention = [
                            'user' => $mention->first_name . ' ' . $mention->last_name,
                            'comment' => truncate($mention->comments, 20)
                        ];
                    }
                }

                // Convert timestamp
                $val->timestamp = time_ago($val->timestamp);

                // Set highlight
                $val->highlight = service('request')->getGet('comment_highlight') == $val->comment_id;

                $output[] = $val;
            }
        }

        return make_json([
            'page' => ($page + 1),
            'limit' => $limit,
            'total' => sizeof($output),
            'next_page' => current_page(null, ['page' => ($page + 1)]),
            'comments' => $output
        ]);
    }

    private function _validate_form()
    {
        if (DEMO_MODE) {
            // Demo mode
            return throw_exception(403, phrase('This feature is disabled in demo mode.'), base_url(service('request')->getGet('path')));
        } elseif (! get_userdata('is_logged')) {
            // Non logged user
            return throw_exception(400, ['comments' => phrase('Please sign in to submit comment.')]);
        } elseif (! service('request')->getGet('post_id') || ! service('request')->getGet('path')) {
            // Invalid post
            return throw_exception(400, ['comments' => phrase('Unable to reply to invalid thread.')]);
        }

        $earlier = new \DateTime(get_userdata('registered_date'));
        $later = new \DateTime(date('Y-m-d'));
        $difference = $earlier->diff($later);
        $interval = $difference->days;
        $day_minimum = (is_numeric(get_setting('account_age_restriction')) ? get_setting('account_age_restriction') : 0);

        if (get_userdata('group_id') > 2 && $day_minimum && $interval <= $day_minimum) {
            // Minimize spam
            return throw_exception(403, phrase('Your account is not yet permitted to post a comment. Please try again after {{interval}} days.', ['interval' => ($interval > 0 ? $interval : 1)]));
        }

        if (time() <= get_userdata('_spam_timer')) {
            // Minimize spam
            return throw_exception(400, ['comments' => phrase('Please wait for previous comments to be processed.')]);
        }

        $this->form_validation->setRule('comments', phrase('Comments'), 'required');
        $this->form_validation->setRule('attachment', phrase('Attachment'), 'validate_upload[attachment.image]');

        if ($this->form_validation->run(service('request')->getPost()) === false) {
            return throw_exception(400, $this->form_validation->getErrors());
        }

        $attachment = '';
        $uploaded_files = get_userdata('_uploaded_files');

        // Check if the uploaded file is valid
        if (isset($uploaded_files['attachment']) && is_array($uploaded_files['attachment'])) {
            // Loop to get source from unknown array key
            foreach ($uploaded_files['attachment'] as $key => $src) {
                // Set new source
                $attachment = $src;
            }
        }

        $reply_id = (service('request')->getGet('reply') ? service('request')->getGet('reply') : 0);
        $mention_id = (service('request')->getGet('mention') ? service('request')->getGet('mention') : 0);

        $this->model->insert(
            $this->_table,
            [
                'user_id' => get_userdata('user_id'),
                'post_id' => service('request')->getGet('post_id'),
                'post_path' => service('request')->getGet('path'),
                'reply_id' => $reply_id,
                'mention_id' => $mention_id,
                'comments' => htmlspecialchars(service('request')->getPost('comments')),
                'attachment' => $attachment,
                'timestamp' => date('Y-m-d H:i:s'),
                'status' => 1
            ]
        );

        $comment_id = $this->model->insert_id();

        if ($reply_id) {
            // Get interaction
            $interaction = $this->model->get_where(
                'post__comments',
                [
                    'comment_id' => $reply_id
                ],
                1
            )
            ->row();

            if ($interaction && get_userdata('user_id') != $interaction->user_id) {
                // Insert notification
                $this->model->insert(
                    'notifications',
                    [
                        'from_user' => get_userdata('user_id'),
                        'to_user' => $interaction->user_id,
                        'type' => 'reply',
                        'interaction_id' => $comment_id,
                        'path' => service('request')->getGet('path'),
                        'timestamp' => date('Y-m-d H:i:s'),
                    ]
                );
            }
        }

        // Push into log's activity
        $this->_push_logs(service('request')->getGet('path'));

        // Set spam timer
        set_userdata('_spam_timer', strtotime('+' . (get_setting('spam_timer') ?? 10) . ' seconds'));

        $query = $this->model->select('
            post__comments.comment_id,
            post__comments.reply_id,
            post__comments.mention_id,
            post__comments.comments,
            post__comments.attachment,
            post__comments.timestamp,

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
                'post__comments.comment_id' => $mention_id,
                'post__comments.status' => 1
            ],
            1
        )
        ->row();

        $html = '
            <div class="row g-0 mb-2 ' . (! $reply_id ? 'comment-item' : null) . '">
                <div class="col-1 pt-1">
                    <img src="' . get_image('users', get_userdata('photo'), 'icon') . '" class="img-fluid rounded-circle" />
                </div>
                <div class="col-11 ps-3">
                    <div class="position-relative">
                        <div class="dropdown position-absolute end-0">
                            <button class="btn btn-link btn-sm dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="mdi mdi-format-list-checks"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton1">
                                <li>
                                    <a class="dropdown-item --modal" href="' . current_page('update', ['id' => $comment_id, 'path' => null]) . '">
                                        ' . phrase('Update') . '
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="bg-light rounded-4 py-2 px-3 d-inline-block">
                        <a href="' . base_url('user/' . get_userdata('username')) . '" class="--xhr">
                            <b>
                                ' . get_userdata('first_name') . ' ' . get_userdata('last_name') . '
                            </b>
                        </a>
                        <br />
                        <div id="comment-text-' . $comment_id . '">
                            ' . ($query ? '<div class="alert alert-warning border-0 border-start border-3 p-2">' . phrase('Replying to') . ' <b>' . $query->first_name . ' '. $query->last_name . '</b><br />' . truncate($query->comments, 50) . '</div>' : null) . '

                            ' . nl2br(htmlspecialchars(service('request')->getPost('comments'))) . '
                            ' . ($attachment ? '<div><a href="' . get_image('comment', $attachment) . '" target="_blank"><img src="' . get_image('comment', $attachment, 'thumb') . '" class="img-fluid rounded-5" alt="..." /></a></div>' : null) . '
                        </div>
                    </div>
                    <div class="py-1 ps-3">
                        <a href="javascript:void(0)" data-href="' . current_page('upvote', ['id' => $comment_id, 'path' => service('request')->getGet('path')]) . '" class="text-sm --upvote">
                            <b class="text-secondary" id="comment-upvote-' . $comment_id . '"></b>
                            &nbsp;
                            <b>
                                ' . phrase('Upvote') . '
                            </b>
                        </a>
                         &middot;
                        <a href="javascript:void(0)" data-href="' . current_page(null, ['path' => service('request')->getGet('path'), 'reply' => ($reply_id ? $reply_id : $comment_id), 'mention' => ($reply_id ? $comment_id : null)]) . '" class="text-sm --reply" data-profile-photo="' . get_image('users', get_userdata('photo'), 'icon') . '" data-mention="' . get_userdata('first_name') . ' ' . get_userdata('last_name') . '">
                            <b>
                                ' . phrase('Reply') . '
                            </b>
                        </a>
                         &middot;
                        <span class="text-muted text-sm">
                            ' . time_ago(date('Y-m-d H:i:s')) . '
                        </span>
                    </div>

                    ' . (! $reply_id ? '<div id="comment-reply"></div>' : null) . '
                </div>
            </div>
        ';

        if ($reply_id) {
            $insert_method = 'insert_before';
        } else {
            $insert_method = 'prepend_to';
        }

        return make_json([
            'content' => $html,
            $insert_method => ($reply_id ? '#comment-container #comment-reply form' : '#comment-container'),
            'remove_element' => '.empty-comment-message',
            'in_context' => ($reply_id ? true : false)
        ]);
    }

    /**
     * Store activity logs into database
     *
     * @param   mixed|null $path
     * @param   mixed|null $method
     */
    private function _push_logs($path = null, $method = '')
    {
        $query = service('request')->getGet();

        unset($query['aksara'], $query['post_id'], $query['path']);

        $agent = service('request')->getUserAgent();

        if ($agent->isBrowser()) {
            $user_agent = $agent->getBrowser() . ' ' . $agent->getVersion();
        } elseif ($agent->isRobot()) {
            $user_agent = $agent->getRobot();
        } elseif ($agent->isMobile()) {
            $user_agent = $agent->getMobile();
        } else {
            $user_agent = phrase('Unknown');
        }

        $prepare = [
            'user_id' => service('session')->get('user_id'),
            'session_id' => COOKIE_NAME . session_id(),
            'path' => $path,
            'method' => $method,
            'query' => json_encode($query),
            'ip_address' => (service('request')->hasHeader('x-forwarded-for') ? service('request')->getHeaderLine('x-forwarded-for') : service('request')->getIPAddress()),
            'browser' => $user_agent,
            'platform' => $agent->getPlatform(),
            'timestamp' => date('Y-m-d H:i:s')
        ];

        $this->model->insert('app__log_activities', $prepare);
    }
}
