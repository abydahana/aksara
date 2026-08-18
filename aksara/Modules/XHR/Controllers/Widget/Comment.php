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
 * When the signs come, those who don't believe at "that time"
 * will have only two choices, commit suicide or become brutal.
 */

namespace Aksara\Modules\XHR\Controllers\Widget;

use Aksara\Laboratory\Core;
use Aksara\Laboratory\Validation;
use DateTime;

class Comment extends Core
{
    private string $_table = 'post_comments';
    private int $_maxDepth = 3;

    public function __construct()
    {
        parent::__construct();

        $this->permission->mustAjax();

        $this->limit(null);

        if (in_array($this->request->getPost('fetch'), ['comments', 'replies'])) {
            return $this->_fetchComments();
        } elseif ('token' === $this->request->getPost('fetch')) {
            $token = generate_csrf_token();

            set_userdata(sha1(uri_string()), $token);

            return make_json([
                'token' => $token
            ]);
        }
    }

    public function index()
    {
        if ($this->validToken($this->request->getPost('_token'))) {
            return $this->_validateForm();
        }

        $this->setTitle(phrase('Comments'))
        ->setIcon('mdi mdi-comment-multiple')

        ->setOutput([
            'likesCount' => $this->model->getWhere(
                'post_likes',
                [
                    'post_id' => $this->request->getGet('post_id'),
                    'post_path' => $this->request->getGet('path')
                ]
            )
            ->numRows(),

            'commentsCount' => $this->model->getWhere(
                'post_comments',
                [
                    'post_id' => $this->request->getGet('post_id'),
                    'post_path' => $this->request->getGet('path'),
                    'status' => 1
                ]
            )
            ->numRows()
        ])

        ->render();
    }

    public function update()
    {
        if (! get_userdata('is_logged')) {
            return throw_exception(403, phrase('Please sign in to update the comment.'));
        }

        $query = $this->model->getWhere(
            $this->_table,
            [
                'comment_id' => ($this->request->getGet('id') ? $this->request->getGet('id') : 0)
            ],
            1
        )
        ->row();

        if (! $query) {
            return throw_exception(404, phrase('The comment you want to update was not found.'));
        }

        if ($this->request->getPost('comment_id') == hash_hmac('sha256', $this->request->getGet('id') . get_userdata('session_generated'), ENCRYPTION_KEY)) {
            $this->formValidation->setRule('comments', phrase('Comments'), 'required');
            $this->formValidation->setRule('attachment', phrase('Attachment'), 'validate_upload[attachment.image]');

            if ($this->formValidation->run($this->request->getPost()) === false) {
                return throw_exception(400, $this->formValidation->getErrors());
            }

            $attachment = '';
            $uploadedFiles = Validation::$uploadedFiles;

            // Check if the uploaded file is valid
            if (isset($uploadedFiles['attachment']) && is_array($uploadedFiles['attachment'])) {
                // Loop to get source from unknown array key
                foreach ($uploadedFiles['attachment'] as $key => $src) {
                    // Set new source
                    $attachment = $src;
                }
            }

            // Insert to update history
            $this->model->insert(
                'post_comments_history',
                [
                    'comment_id' => $query->comment_id,
                    'comments' => $query->comments,
                    'attachment' => $query->attachment,
                    'created_at' => $query->created_at
                ]
            );

            // Update comment
            $this->model->update(
                $this->_table,
                [
                    'comments' => htmlspecialchars($this->request->getPost('comments')),
                    'attachment' => $attachment,
                    'edited' => 1
                ],
                [
                    'comment_id' => $this->request->getGet('id')
                ]
            );

            return make_json([
                'element' => '#comment-text-' . $this->request->getGet('id'),
                'content' => ($attachment ? '<div><a href="' . get_image('comment', $attachment) . '" target="' . '_blank' . '"><img src="' . get_image('comment', $attachment, 'thumb') . '" class="img-fluid rounded mb-3" alt="' . phrase('Attachment') . '" loading="lazy" decoding="async" /></a></div>' : null) . nl2br(htmlspecialchars($this->request->getPost('comments')))
            ]);
        }

        $html = '
            <form action="' . current_page() . '" method="POST" class="--validate-form" enctype="multipart/form-data">
                <input type="hidden" name="comment_id" value="' . hash_hmac('sha256', $this->request->getGet('id') . get_userdata('session_generated'), ENCRYPTION_KEY) . '" />
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
                            <input type="file" name="attachment" accept="' . implode(',', preg_filter('/^/', '.', array_map('trim', explode(',', IMAGE_FORMAT_ALLOWED)))) . '" data-role="image-upload" id="attachment_input" />
                            <div class="fileupload-new text-center">
                                <img class="img-fluid upload_preview" src="' . get_image('comment', $query->attachment, 'thumb'). '" alt="' . phrase('Preview') . '" loading="lazy" decoding="async" />
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
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
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

    public function repute()
    {
        if (! get_userdata('is_logged')) {
            return throw_exception(403, phrase('Please sign in to repute the post.'));
        }

        $query = $this->model->getWhere(
            'post_likes',
            [
                'post_id' => ($this->request->getGet('post_id') ? $this->request->getGet('post_id') : 0),
                'post_path' => $this->request->getGet('path'),
                'created_by' => get_userdata('user_id')
            ]
        )
        ->row();

        if ($query) {
            $query = $this->model->delete(
                'post_likes',
                [
                    'post_id' => ($this->request->getGet('post_id') ? $this->request->getGet('post_id') : 0),
                    'post_path' => $this->request->getGet('path'),
                    'created_by' => get_userdata('user_id')
                ]
            );

            // Delete notification
            $this->model->delete(
                'notifications',
                [
                    'from_user' => get_userdata('user_id'),
                    'to_user' => get_userdata('user_id'),
                    'type' => 'like',
                    'path' => $this->request->getGet('path')
                ]
            );
        } else {
            $query = $this->model->insert(
                'post_likes',
                [
                    'post_id' => ($this->request->getGet('post_id') ? $this->request->getGet('post_id') : 0),
                    'post_path' => $this->request->getGet('path'),
                    'created_by' => get_userdata('user_id'),
                    'created_at' => date('Y-m-d H:i:s')
                ]
            );

            // Insert notification
            $this->model->insert(
                'notifications',
                [
                    'from_user' => get_userdata('user_id'),
                    'to_user' => get_userdata('user_id'),
                    'type' => 'like',
                    'interaction_id' => ($this->request->getGet('post_id') ? $this->request->getGet('post_id') : 0),
                    'path' => $this->request->getGet('path'),
                    'created_at' => date('Y-m-d H:i:s'),
                ]
            );
        }

        $upvotes = $this->model->getWhere(
            'post_likes',
            [
                'post_id' => ($this->request->getGet('post_id') ? $this->request->getGet('post_id') : 0),
                'post_path' => $this->request->getGet('path')
            ]
        )
        ->numRows();

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
            'class_add' => ($upvotes ? $this->request->getPost('classAdd') : $this->request->getPost('classRemove')),
            'class_remove' => (! $upvotes ? $this->request->getPost('classAdd') : $this->request->getPost('classRemove'))
        ]);
    }

    public function upvote()
    {
        if (! get_userdata('is_logged')) {
            return throw_exception(403, phrase('Please sign in to upvote the comment.'));
        }

        // Get interaction
        $interaction = $this->model->getWhere(
            'post_comments',
            [
                'comment_id' => ($this->request->getGet('id') ? $this->request->getGet('id') : 0)
            ],
            1
        )
        ->row();

        if (! $interaction) {
            // No interaction is found
            return throw_exception(404, phrase('The interaction does not exists.'));
        }

        $query = $this->model->getWhere(
            'post_comments_likes',
            [
                'comment_id' => ($this->request->getGet('id') ? $this->request->getGet('id') : 0),
                'created_by' => get_userdata('user_id')
            ]
        )
        ->row();

        if ($query) {
            $query = $this->model->delete(
                'post_comments_likes',
                [
                    'comment_id' => ($this->request->getGet('id') ? $this->request->getGet('id') : 0),
                    'created_by' => get_userdata('user_id')
                ]
            );

            // Insert notification
            $this->model->delete(
                'notifications',
                [
                    'from_user' => get_userdata('user_id'),
                    'to_user' => $interaction->created_by,
                    'type' => 'upvote',
                    'path' => $this->request->getGet('path')
                ]
            );
        } else {
            $query = $this->model->insert(
                'post_comments_likes',
                [
                    'comment_id' => ($this->request->getGet('id') ? $this->request->getGet('id') : 0),
                    'created_by' => get_userdata('user_id'),
                    'created_at' => date('Y-m-d H:i:s')
                ]
            );

            // Insert notification
            $this->model->insert(
                'notifications',
                [
                    'from_user' => get_userdata('user_id'),
                    'to_user' => $interaction->created_by,
                    'type' => 'upvote',
                    'interaction_id' => ($this->request->getGet('id') ? $this->request->getGet('id') : 0),
                    'path' => $this->request->getGet('path'),
                    'created_at' => date('Y-m-d H:i:s'),
                ]
            );
        }

        $upvotes = $this->model->getWhere(
            'post_comments_likes',
            [
                'comment_id' => ($this->request->getGet('id') ? $this->request->getGet('id') : 0)
            ]
        )
        ->numRows();

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
            'element' => '#comment-upvote-' . $this->request->getGet('id'),
            'content' => ($upvotes ? $upvotes : null)
        ]);
    }

    public function hide()
    {
        $query = $this->model->getWhere(
            $this->_table,
            [
                'comment_id' => ($this->request->getGet('id') ? $this->request->getGet('id') : 0)
            ],
            1
        )
        ->row();

        if (! $query) {
            return throw_exception(404, phrase('The comment you want to hide was not found.'));
        }

        if ($this->request->getPost('comment_id') == hash_hmac('sha256', $this->request->getGet('id') . get_userdata('session_generated'), ENCRYPTION_KEY)) {
            $this->model->update(
                $this->_table,
                [
                    'status' => ($query->status ? 0 : 1)
                ],
                [
                    'comment_id' => $this->request->getGet('id')
                ]
            );

            return make_json([
                'element' => '#comment-text-' . $this->request->getGet('id'),
                'content' => ($query->status ? '<i class="text-muted">' . phrase('Comment hidden') . '</i>' : $query->comments)
            ]);
        }

        $html = '
            <form action="' . current_page() . '" method="POST" class="--validate-form">
                <input type="hidden" name="comment_id" value="' . hash_hmac('sha256', $this->request->getGet('id') . get_userdata('session_generated'), ENCRYPTION_KEY) . '" />
                <div class="text-center pt-3 pb-3 mb-3">
                    ' . ($query->status ? phrase('Are you sure want to hide this comment?') : phrase('Are you sure want to republish this comment?')) . '
                </div>
                <hr class="mx--3 border-secondary-subtle" />
                <div class="row">
                    <div class="col-6">
                        <div class="d-grid">
                            <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill" data-bs-dismiss="modal">
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

    public function report()
    {
        if (! get_userdata('is_logged')) {
            return throw_exception(403, phrase('Please sign in to report the comment.'));
        }

        $query = $this->model->getWhere(
            $this->_table,
            [
                'comment_id' => ($this->request->getGet('id') ? $this->request->getGet('id') : 0)
            ],
            1
        )
        ->row();

        if (! $query) {
            return throw_exception(404, phrase('The comment you want to report was not found.'));
        }

        if ($this->request->getPost('comment_id') == hash_hmac('sha256', $this->request->getGet('id') . get_userdata('session_generated'), ENCRYPTION_KEY)) {
            $checker = $this->model->getWhere(
                'post_comments_reports',
                [
                    'comment_id' => $query->comment_id,
                    'created_by' => get_userdata('user_id')
                ],
                1
            )
            ->row();

            if ($checker) {
                // Update feedback
                $this->model->update(
                    'post_comments_reports',
                    [
                        'message' => htmlspecialchars($this->request->getPost('message')),
                        'created_at' => $query->created_at
                    ],
                    [
                        'comment_id' => $query->comment_id,
                        'created_by' => get_userdata('user_id')
                    ]
                );
            } else {
                // Insert feedback
                $this->model->insert(
                    'post_comments_reports',
                    [
                        'comment_id' => $query->comment_id,
                        'created_by' => get_userdata('user_id'),
                        'message' => htmlspecialchars($this->request->getPost('message')),
                        'created_at' => $query->created_at
                    ]
                );
            }

            $content = '
                <div class="text-center">
                    <i class="mdi mdi-check-circle-outline mdi-5x text-success d-block"></i>
                    <p class="fs-5">' . phrase('Comment has been successfully reported and queued for review.') . '</p>
                    <button type="button" class="btn btn-dark btn-sm rounded-pill px-4" data-bs-dismiss="modal" aria-label="' . phrase('Close') . '">' . phrase('Close') . '</button>
                </div>
            ';

            return make_json([
                'status' => 200,
                'meta' => [
                    'popup' => true
                ],
                'content' => $content
            ]);
        }

        $content = '
            <form action="' . current_page() . '" method="POST" class="--validate-form">
                <input type="hidden" name="comment_id" value="' . hash_hmac('sha256', $this->request->getGet('id') . get_userdata('session_generated'), ENCRYPTION_KEY) . '" />
                <div class="text-center py-3">
                    ' . phrase('Are you sure want to report this comment?') . '
                </div>
                <div class="form-group mb-3">
                    <textarea name="message" class="form-control" id="message_input" placeholder="' . phrase('Write a feedback') . '" rows="3"></textarea>
                </div>
                <div class="row">
                    <div class="col-6">
                        <div class="d-grid">
                            <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill" data-bs-dismiss="modal" aria-label="' . phrase('Cancel') . '">
                                <i class="mdi mdi-window-close"></i>
                                ' . phrase('Cancel') . '
                            </button>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="d-grid">
                            <button type="submit" class="btn btn-danger btn-sm rounded-pill" aria-label="' . phrase('Report') . '">
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
                'popup' => true
            ],
            'content' => $content
        ]);
    }

    private function _fetchComments()
    {
        $limit = 10;
        $order = 'DESC';
        $page = (is_numeric($this->request->getGet('page_no')) ? $this->request->getGet('page_no') : 0);
        $parentId = $this->request->getGet('parent_id');

        if ($page) {
            $this->model->offset($limit * $page);
        }

        if ($parentId) {
            $order = 'ASC';

            $this->model->where('post_comments.reply_id', $parentId);
        } else {
            $this->model->where([
                'post_comments.post_id' => $this->request->getGet('post_id'),
                'post_comments.post_path' => $this->request->getGet('path'),
                'post_comments.reply_id' => 0
            ]);
        }

        $query = $this->model->select('
            post_comments.comment_id,
            post_comments.created_by,
            post_comments.post_id,
            post_comments.post_path,
            post_comments.reply_id,
            post_comments.mention_id,
            post_comments.comments,
            post_comments.attachment,
            post_comments.edited,
            post_comments.created_at,
            post_comments.status,
            app_users.photo,
            app_users.username,
            app_users.first_name,
            app_users.last_name,
            COUNT(distinct replies_table.comment_id) AS replies,
            COUNT(distinct upvotes_table.comment_id) AS upvotes
        ')
        ->join(
            'app_users',
            'app_users.user_id = post_comments.created_by'
        )
        ->join(
            'post_comments replies_table',
            'replies_table.reply_id = post_comments.comment_id',
            'LEFT'
        )
        ->join(
            'post_comments_likes upvotes_table',
            'upvotes_table.comment_id = post_comments.comment_id',
            'LEFT'
        )
        ->groupBy('post_comments.comment_id')
        ->orderBy('post_comments.comment_id', $order)
        ->getWhere(
            'post_comments',
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
                    'reply_url' => current_page(null, ['id' => $val->comment_id, 'path' => $this->request->getGet('path'), 'reply' => $this->request->getGet('reply') ?? $val->comment_id]),
                    'upvote_url' => current_page('upvote', ['id' => $val->comment_id, 'path' => $this->request->getGet('path'), 'parent_id' => null]),
                    'report_url' => (get_userdata('user_id') !== $val->created_by ? current_page('report', ['id' => $val->comment_id, 'path' => $this->request->getGet('path'), 'parent_id' => null]) : null),
                    'update_url' => (get_userdata('user_id') === $val->created_by ? current_page('update', ['id' => $val->comment_id, 'path' => $this->request->getGet('path'), 'parent_id' => null]) : null),
                    'hide_url' => (get_userdata('user_id') === $val->created_by || in_array(get_userdata('group_id'), [1, 2]) ? current_page('hide', ['id' => $val->comment_id, 'path' => $this->request->getGet('path'), 'parent_id' => null]) : null)
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
                        post_comments.comments,
                        app_users.first_name,
                        app_users.last_name
                    ')
                    ->join(
                        'app_users',
                        'app_users.user_id = post_comments.created_by'
                    )
                    ->getWhere(
                        'post_comments',
                        [
                            'post_comments.comment_id' => $val->mention_id
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

                // Convert creation time
                $val->created_at = time_ago($val->created_at, true);

                // Calculate comment depth level
                $val->depth = $this->_getCommentDepth((int) $val->comment_id);

                // Set highlight
                $val->highlight = $this->request->getGet('comment_highlight') == $val->comment_id;

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

    private function _validateForm()
    {
        if (DEMO_MODE) {
            // Demo mode
            return throw_exception(403, phrase('This feature is disabled in demo mode.'), base_url($this->request->getGet('path')));
        } elseif (! get_userdata('is_logged')) {
            // Non logged user
            return throw_exception(400, ['comments' => phrase('Please sign in to submit comment.')]);
        } elseif (! $this->request->getGet('post_id') || ! $this->request->getGet('path')) {
            // Invalid post
            return throw_exception(400, ['comments' => phrase('Unable to reply to invalid thread.')]);
        }

        $earlier = new DateTime(get_userdata('created_at'));
        $later = new DateTime(date('Y-m-d'));
        $difference = $earlier->diff($later);
        $interval = $difference->days;
        $dayMinimum = (is_numeric(get_setting('account_age_restriction')) ? get_setting('account_age_restriction') : 0);

        if (get_userdata('group_id') > 2 && $dayMinimum && $interval <= $dayMinimum) {
            // Minimize spam
            return throw_exception(403, phrase('Your account is not yet permitted to post a comment. Please try again after {{interval}} days.', ['interval' => ($interval > 0 ? $interval : 1)]));
        }

        if (time() <= get_userdata('_spam_timer')) {
            // Minimize spam
            return throw_exception(400, ['comments' => phrase('Please wait for previous comments to be processed.')]);
        }

        $this->formValidation->setRule('comments', phrase('Comments'), 'required');
        $this->formValidation->setRule('attachment', phrase('Attachment'), 'validate_upload[attachment.image]');

        if ($this->formValidation->run($this->request->getPost()) === false) {
            return throw_exception(400, $this->formValidation->getErrors());
        }

        $attachment = '';
        $uploadedFiles = Validation::$uploadedFiles;

        // Check if the uploaded file is valid
        if (isset($uploadedFiles['attachment']) && is_array($uploadedFiles['attachment'])) {
            // Loop to get source from unknown array key
            foreach ($uploadedFiles['attachment'] as $key => $src) {
                // Set new source
                $attachment = $src;
            }
        }

        $targetReplyId = ($this->request->getGet('reply') ? (int) $this->request->getGet('reply') : 0);
        $targetMentionId = ($this->request->getGet('mention') ? (int) $this->request->getGet('mention') : 0);

        $maxDepth = $this->_maxDepth;

        $replyId = 0;
        $mentionId = 0;

        if ($targetReplyId) {
            $targetDepth = $this->_getCommentDepth($targetReplyId);

            if ($targetDepth < $maxDepth) {
                // Within allowed depth: attach as direct child
                $replyId = $targetReplyId;
                $mentionId = ($targetMentionId ?: $targetReplyId);
            } else {
                // At or exceeds max depth limit: cap reply_id to target's parent reply_id
                $targetComment = $this->model->select('reply_id')->getWhere('post_comments', ['comment_id' => $targetReplyId], 1)->row();
                $replyId = ($targetComment && $targetComment->reply_id) ? (int) $targetComment->reply_id : $targetReplyId;
                $mentionId = $targetReplyId;
            }
        }

        $this->model->insert(
            $this->_table,
            [
                'created_by' => get_userdata('user_id'),
                'post_id' => $this->request->getGet('post_id'),
                'post_path' => $this->request->getGet('path'),
                'reply_id' => $replyId,
                'mention_id' => $mentionId,
                'comments' => htmlspecialchars($this->request->getPost('comments')),
                'attachment' => $attachment,
                'created_at' => date('Y-m-d H:i:s'),
                'status' => 1
            ]
        );

        $commentId = $this->model->insertId();

        if ($replyId) {
            // Get interaction
            $interaction = $this->model->getWhere(
                'post_comments',
                [
                    'comment_id' => $replyId
                ],
                1
            )
            ->row();

            if ($interaction && get_userdata('user_id') != $interaction->created_by) {
                // Insert notification
                $this->model->insert(
                    'notifications',
                    [
                        'from_user' => get_userdata('user_id'),
                        'to_user' => $interaction->created_by,
                        'type' => 'reply',
                        'interaction_id' => $commentId,
                        'path' => $this->request->getGet('path'),
                        'created_at' => date('Y-m-d H:i:s'),
                    ]
                );
            }
        }

        // Push into log's activity
        $this->_pushLogs($this->request->getGet('path'));

        // Set spam timer
        set_userdata('_spam_timer', strtotime('+' . (get_setting('spam_timer') ?? 10) . ' seconds'));

        $query = $this->model->select('
            post_comments.comment_id,
            post_comments.reply_id,
            post_comments.mention_id,
            post_comments.comments,
            post_comments.attachment,
            post_comments.created_at,

            app_users.first_name,
            app_users.last_name
        ')
        ->join(
            'app_users',
            'app_users.user_id = post_comments.created_by'
        )
        ->getWhere(
            'post_comments',
            [
                'post_comments.comment_id' => $mentionId,
                'post_comments.status' => 1
            ],
            1
        )
        ->row();

        $html = '
            <div class="comment-item">
                <div class="row g-0 mb-2">
                    <div class="col-1 pt-1">
                        <img src="' . get_image('users', get_userdata('photo'), 'icon') . '" class="img-fluid rounded-circle" />
                    </div>
                    <div class="col-11 ps-3">
                        <div class="d-flex align-items-center gap-1 comment-bubble">
                            <div class="bg-body-tertiary rounded-4 py-2 px-3 d-inline-block">
                                <div class="comment-header">
                                    <a href="' . base_url('user/' . get_userdata('username')) . '" class="text-body --xhr">
                                        <b>
                                            ' . get_userdata('first_name') . ' ' . get_userdata('last_name') . '
                                        </b>
                                    </a>
                                    &middot;
                                    <span class="text-muted">
                                        ' . phrase('Just now') . '
                                    </span>
                                </div>
                                <div id="comment-text-' . $commentId . '">
                                    ' . ($query ? '<div class="alert alert-warning border-0 border-start border-3 p-2 mb-2">' . phrase('Replying to') . ' <b>' . $query->first_name . ' '. $query->last_name . '</b><br />' . truncate($query->comments, 50) . '</div>' : null) . '

                                    ' . nl2br(htmlspecialchars($this->request->getPost('comments'))) . '
                                    ' . ($attachment ? '<div class="my-2"><a href="' . get_image('comment', $attachment) . '" class="d-block" target="_blank"><img src="' . get_image('comment', $attachment, 'thumb') . '" class="img-fluid rounded-4" alt="' . phrase('Attachment') . '" /></a></div>' : null) . '
                                </div>
                            </div>
                            <div class="dropdown comment-dropdown flex-shrink-0">
                                <button class="btn btn-link btn-sm text-body-secondary p-0" type="button" id="dropdownMenuButton' . $commentId . '" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="mdi mdi-dots-horizontal fs-5"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton' . $commentId . '">
                                    <li>
                                        <a class="dropdown-item --modal" href="' . current_page('update', ['id' => $commentId, 'path' => null]) . '">
                                            ' . phrase('Update') . '
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="py-1 ps-3">
                            <a href="' . current_page('upvote', ['id' => $commentId, 'path' => $this->request->getGet('path')]) . '" class="small text-body --upvote">
                                <b><span id="comment-upvote-' . $commentId . '"></span> ' . phrase('Upvote') . '</b>
                            </a>
                             &middot;
                            <a href="' . current_page(null, ['path' => $this->request->getGet('path'), 'reply' => $commentId, 'mention' => $commentId]) . '" class="small text-body --reply" data-profile-photo="' . get_image('users', get_userdata('photo'), 'icon') . '" data-mention="' . get_userdata('first_name') . ' ' . get_userdata('last_name') . '">
                                <b>' . phrase('Reply') . '</b>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="row g-0">
                    <div class="col-11 offset-1 ps-3">
                        <div id="comment-reply"></div>
                    </div>
                </div>
            </div>
        ';

        if ($replyId) {
            $insertMethod = 'insert_before';
        } else {
            $insertMethod = 'prepend_to';
        }

        return make_json([
            'content' => $html,
            $insertMethod => ($replyId ? '#comment-container #comment-reply form' : '#comment-container'),
            'remove_element' => '.empty-comment-message',
            'in_context' => ($replyId ? true : false)
        ]);
    }

    /**
     * Store activity logs into database
     *
     * @param   mixed|null $path
     * @param   mixed|null $method
     */
    private function _pushLogs($path = null, $method = '')
    {
        $query = $this->request->getGet();

        unset($query['aksara'], $query['post_id'], $query['path']);

        $agent = $this->request->getUserAgent();

        if ($agent->isBrowser()) {
            $userAgent = $agent->getBrowser() . ' ' . $agent->getVersion();
        } elseif ($agent->isRobot()) {
            $userAgent = $agent->getRobot();
        } elseif ($agent->isMobile()) {
            $userAgent = $agent->getMobile();
        } else {
            $userAgent = phrase('Unknown');
        }

        $prepare = [
            'user_id' => get_userdata('user_id'),
            'session_id' => COOKIE_NAME . session_id(),
            'path' => $path,
            'method' => $method,
            'query' => json_encode($query),
            'ip_address' => $this->request->getIPAddress(),
            'browser' => $userAgent,
            'platform' => $agent->getPlatform(),
            'timestamp' => date('Y-m-d H:i:s')
        ];

        $this->model->insert('app_log_activities', $prepare);
    }

    /**
     * Calculates the depth level of a comment recursively up to MAX_COMMENT_DEPTH.
     */
    private function _getCommentDepth(int $commentId): int
    {
        $depth = 1;
        $currentId = $commentId;

        while ($currentId > 0 && $depth < 10) {
            $row = $this->model->select('reply_id')->getWhere('post_comments', ['comment_id' => $currentId], 1)->row();
            if (! $row || ! $row->reply_id) {
                break;
            }
            $depth++;
            $currentId = (int) $row->reply_id;
        }

        return $depth;
    }
}
