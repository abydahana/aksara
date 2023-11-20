<div class="py-3" id="comment-widget">
    <?php if (! service('request')->getGet('hidebutton')): ?>
    <div class="border-bottom pb-3 mb-3">
        <div class="row">
            <div class="col-4">
                <div class="d-grid">
                    <a href="<?= current_page('repute'); ?>" class="btn btn-light btn-sm btn-block rounded-pill text-truncate --modify" data-bs-toggle="tooltip" title="<?= phrase('Like'); ?>" data-post-id="<?= service('request')->getGet('post_id'); ?>">
                        <i class="mdi mdi-heart<?= ($likes_count ? ' me-2' : null); ?>"></i>
                        <b class="likes-count fw-bold"><?= ($likes_count ? $likes_count : ''); ?></b>
                    </a>
                </div>
            </div>
            <div class="col-4">
                <div class="d-grid">
                    <button type="button" class="btn btn-light btn-sm btn-block rounded-pill text-truncate" data-bs-toggle="tooltip" title="<?= phrase('Comment'); ?>" onclick="jExec($('textarea[name=comments]').first().trigger('focus'))">
                        <i class="mdi mdi-comment<?= ($comments_count ? ' me-2' : null); ?>"></i>
                        <b class="replies-count"><?= ($comments_count ? $comments_count : ''); ?></b>
                    </button>
                </div>
            </div>
            <div class="col-4">
                <div class="d-grid">
                    <button type="button" class="btn btn-light btn-sm btn-block rounded-pill text-truncate">
                        <i class="mdi mdi-share"></i>
                        <?= phrase('Share'); ?>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
    <form action="<?= current_page(); ?>" method="POST" enctype="multipart/form-data" class="--validate-form border-bottom pb-3 mb-3">
        <div class="form-group">
            <div class="row g-0 align-items-center">
                <div class="col-1">
                    <a href="<?= (get_userdata('username') ? base_url('user/' . get_userdata('username')) : 'javascript:void(0)'); ?>">
                        <img src="<?= get_image('users', get_userdata('photo'), 'icon'); ?>" class="img-fluid rounded-circle" />
                    </a>
                </div>
                <div class="col-11 ps-3">
                    <div class="position-relative">
                        <textarea name="comments" class="form-control nofocus" placeholder="<?= phrase('Type a comment'); ?>" rows="1"></textarea>
                        <div class="btn-group position-absolute bottom-0 end-0">
                            <button type="button" class="btn btn-link" data-toggle="tooltip" data-bs-toggle="tooltip" title="<?= phrase('Attach photo'); ?>" onclick="jExec($(this).closest('form').find('.fileupload').removeClass('d-none').find('input[type=file]').trigger('click'))">
                                <i class="mdi mdi-camera"></i>
                            </button>
                        </div>
                    </div>
                    <div data-provides="fileupload" class="fileupload fileupload-new d-none">
                        <span class="btn btn-file" style="width:80px">
                            <input type="file" name="attachment" accept="<?= implode(',', preg_filter('/^/', '.', array_map('trim', explode(',', IMAGE_FORMAT_ALLOWED)))); ?>" role="image-upload" id="attachment_input" />
                            <div class="fileupload-new text-center">
                                <img class="img-fluid upload_preview" src="<?= get_image('comment', 'placeholder.png', 'icon'); ?>" alt="..." />
                            </div>
                            <button type="button" class="btn btn-sm btn-danger rounded-circle position-absolute top-0 end-0" onclick="jExec($(this).closest('.btn-file').find('input[type=file]').val(''), $(this).closest('.btn-file').find('img').attr('src', '<?= get_image('comment', 'placeholder.png', 'icon'); ?>'), $(this).closest('.fileupload').addClass('d-none'))">
                                <i class="mdi mdi-window-close"></i>
                            </button>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="row g-0 align-items-center">
            <div class="col-11 offset-1 ps-3">
                <div role="validation-callback"></div>
            </div>
        </div>
    </form>
    <div id="comment-container">
        <?php foreach ($results as $key => $val): ?>
            <div class="comment-item">
                <div class="row g-0 mb-2">
                    <div class="col-1 pt-1">
                        <a href="<?= base_url('user/' . $val->username); ?>" class="--xhr">
                            <img src="<?= get_image('users', $val->photo, 'icon'); ?>" class="img-fluid rounded-circle" />
                        </a>
                    </div>
                    <div class="col-11 ps-3">
                        <div class="position-relative">
                            <div class="dropdown position-absolute end-0">
                                <button class="btn btn-link btn-sm dropdown-toggle" type="button" id="dropdownMenuButton1" data-toggle="dropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="mdi mdi-format-list-checks"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-right" aria-labelledby="dropdownMenuButton1">
                                    <?php if ($val->user_id == get_userdata('user_id')): ?>
                                    <li>
                                        <a class="dropdown-item --modal" href="<?= current_page('update', ['id' => $val->comment_id]); ?>">
                                            <?= phrase('Update'); ?>
                                        </a>
                                    </li>
                                    <?php else: ?>
                                    <li>
                                        <a class="dropdown-item --modal" href="<?= current_page('report', ['id' => $val->comment_id]); ?>">
                                            <?= phrase('Report'); ?>
                                        </a>
                                    </li>
                                    <?php endif; ?>
                                    <?php if (in_array(get_userdata('group_id'), [1, 2]) || $val->user_id == get_userdata('user_id')): ?>
                                    <li>
                                        <a class="dropdown-item --modal" href="<?= current_page('hide', ['id' => $val->comment_id]); ?>">
                                            <?= phrase('Visibility'); ?>
                                        </a>
                                    </li>
                                    <?php endif; ?>
                                </ul>
                            </div>
                        </div>
                        <div class="<?= (service('request')->getGet('comment_highlight') && service('request')->getGet('comment_highlight') == $val->comment_id ? 'bg-warning' : 'bg-light'); ?> rounded-4 py-2 px-3 d-inline-block">
                            <a href="<?= base_url('user/' . $val->username); ?>" class="--xhr">
                                <b id="comment-author-<?= $val->comment_id; ?>">
                                    <?= $val->first_name . ' ' . $val->last_name; ?>
                                </b>
                            </a>
                            <br />
                            <div id="comment-text-<?= $val->comment_id; ?>">
                                <?php if ($val->status): ?>
                                    <?= $val->comments; ?>
                                    <?php if ($val->attachment): ?>
                                        <div class="mt-3">
                                            <a href="<?= get_image('comment', $val->attachment); ?>" target="_blank">
                                                <img src="<?= get_image('comment', $val->attachment, 'thumb'); ?>" class="img-fluid rounded" alt="..." />
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <i class="text-muted">' . phrase('Comment is hidden') . '</i>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="py-1 ps-3">
                            <a href="<?= current_page('upvote', ['id' => $val->comment_id]); ?>" class="text-sm --modify">
                                <b class="text-secondary" id="comment-upvote-<?= $val->comment_id; ?>">
                                    <?= ($val->upvotes ? $val->upvotes : null); ?>
                                </b>
                                <b>
                                    <?= phrase('Upvote'); ?>
                                </b>
                            </a>
                            &middot;
                            <a href="<?= current_page(null, ['path' => $val->post_path, 'reply' => $val->comment_id]); ?>" class="text-sm --reply" data-profile-photo="<?= get_image('users', get_userdata('photo'), 'icon'); ?>" data-mention="<?= $val->first_name . ' ' . $val->last_name; ?>">
                                <b>
                                    <?= phrase('Reply'); ?>
                                </b>
                            </a>
                            &middot;
                            <span class="text-muted text-sm">
                                <?= time_ago($val->timestamp); ?>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="row g-0">
                    <div class="col-12 offset-0 col-sm-11 offset-sm-1 ps-0 ps-sm-3">
                        <div id="comment-reply">
                            <?php if ($val->replies): ?>
                                <div class="row">
                                    <div class="col-11 offset-1 col-sm-12 offset-sm-0">
                                        <div class="ps-3 mb-4">
                                            <a href="<?= current_page(null, ['parent_id' => $val->comment_id]); ?>" class="--fetch-replies text-dark fw-bold">
                                                <i class="mdi mdi-chevron-down"></i>
                                                <?= number_format($val->replies) . ' ' . ($val->replies > 1 ? phrase('replies') : phrase('reply')); ?>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<script type="text/javascript" src="<?= get_module_asset('js/scripts.js'); ?>"></script>
