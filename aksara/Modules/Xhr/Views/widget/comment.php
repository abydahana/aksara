<div class="py-3" id="comment-widget">
    <div class="pb-3">
        <div class="row">
            <div class="col-4">
                <div class="d-grid">
                    <button type="button" data-href="<?= current_page('repute'); ?>" class="btn btn-light btn-sm btn-block rounded-pill text-truncate --upvote" data-bs-toggle="tooltip" title="<?= phrase('Like'); ?>" data-post-id="<?= service('request')->getGet('post_id'); ?>">
                        <i class="mdi mdi-heart<?= ($likesCount ? ' me-2' : null); ?>"></i>
                        <b class="likes-count fw-bold"><?= ($likesCount ? $likesCount : ''); ?></b>
                    </button>
                </div>
            </div>
            <div class="col-4">
                <div class="d-grid">
                    <button type="button" class="btn btn-light btn-sm btn-block rounded-pill text-truncate" data-bs-toggle="tooltip" title="<?= phrase('Comment'); ?>" onclick="jExec($('textarea[name=comments]').first().trigger('focus'))">
                        <i class="mdi mdi-comment<?= ($commentsCount ? ' me-2' : null); ?>"></i>
                        <b class="replies-count"><?= ($commentsCount ? $commentsCount : ''); ?></b>
                    </button>
                </div>
            </div>
            <div class="col-4">
                <div class="d-grid">
                    <button type="button" class="btn btn-light btn-sm btn-block rounded-pill text-truncate" role="share" data-href="<?= service('request')->getGet('path'); ?>">
                        <i class="mdi mdi-share"></i>
                        <?= phrase('Share'); ?>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <form action="<?= current_page(); ?>" method="POST" class="--validate-form pb-3" enctype="multipart/form-data">
        <div class="form-group">
            <div class="row g-0 align-items-center">
                <div class="col-1">
                    <a href="<?= (get_userdata('username') ? base_url('user/' . get_userdata('username')) : 'javascript:void(0)'); ?>" class="--xhr">
                        <img src="<?= get_image('users', get_userdata('photo'), 'icon'); ?>" class="img-fluid rounded-circle" />
                    </a>
                </div>
                <div class="col-11 ps-3">
                    <div class="position-relative">
                        <textarea name="comments" class="form-control nofocus" placeholder="<?= phrase('Type a comment'); ?>" rows="1"></textarea>
                        <div class="btn-group position-absolute bottom-0 end-0">
                            <button type="button" class="btn btn-link text-dark" data-toggle="tooltip" data-bs-toggle="tooltip" title="<?= phrase('Attach photo'); ?>" onclick="jExec($(this).closest('form').find('.fileupload').removeClass('d-none').find('input[type=file]').trigger('click'))">
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
        <input type="hidden" name="_token" value="<?= $_token; ?>" />
    </form>
    <div id="comment-container">
        <!-- COMMENTS LIST -->
        <?php if ($commentsCount): ?>
            <div class="load-more-container">
                <p class="text-center">
                    <a href="javascript:void(0)" data-href="<?= current_page(); ?>" class="load-more --fetch-comments">
                        <b><?= phrase('Load comments'); ?></b>
                    </a>
                </p>
            </div>
        <?php else: ?>
            <p class="text-center text-muted empty-comment-message"><?= phrase('Be the first to comment'); ?></p>
        <?php endif; ?>
    </div>
</div>

<script type="text/javascript" src="<?= get_module_asset('js/scripts.js'); ?>"></script>
