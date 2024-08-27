<div class="py-3 py-md-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 offset-lg-2">
                <?php if ($results): ?>
                    <h1 class="text-center text-md-start">
                        <?= $meta->title; ?>
                    </h1>
                    <?php foreach($results as $key => $val): ?>
                        <?php if ($val->cover && 'placeholder.png' != $val->cover): ?>
                            <img src="<?= get_image('announcements', $val->cover); ?>" class="img-fluid rounded-4 mb-3" alt="..." />
                        <?php endif; ?>
                        <div class="lead">
                            <?= $val->content; ?>
                        </div>
                        <p class="text-muted">
                            <em>
                                <?= phrase('This announcement will be effective until'); ?> <?= $val->end_date; ?>
                            </em>
                        </p>
                        <a href="<?= current_page('../'); ?>" class="btn btn-outline-primary rounded-pill px-5 --xhr">
                            <i class="mdi mdi-arrow-left"></i>
                            <?= phrase('Back'); ?>
                        </a>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="py-5">
                        <div class="text-center">
                            <h1 class="text-muted">
                                404
                            </h1>
                            <i class="mdi mdi-dropbox mdi-5x text-muted"></i>
                        </div>
                        <h2 class="text-center">
                            <?= phrase('No announcement is found!'); ?>
                        </h2>
                        <p class="lead text-center mb-5">
                            <?= phrase('The announcement you requested was not found or its already been removed.'); ?>
                        </p>
                        <div class="text-center mt-5">
                            <a href="<?= go_to('../'); ?>" class="btn btn-outline-primary rounded-pill --xhr">
                                <i class="mdi mdi-arrow-left"></i>
                                <?= phrase('Back to Announcements'); ?>
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>