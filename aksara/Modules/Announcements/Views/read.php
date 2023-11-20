<?php if ($results): ?>
<div class="py-3 py-md-5 bg-light text-secondary">
    <div class="container py-lg-5">
        <h1 class="text-center text-md-start">
            <?= $meta->title; ?>
        </h1>
        <p class="lead text-center text-md-start">
            <?= truncate($meta->description, 256); ?>
        </p>
    </div>
</div>
<div class="py-3 py-md-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <?php foreach($results as $key => $val): ?>
                    <p>
                        <?= $val->content; ?>
                    </p>
                    <p class="text-muted mb-5">
                        <em>
                            <?= phrase('This announcement will be effective until'); ?> <b><?= $val->end_date; ?></b>
                        </em>
                    </p>
                    <a href="<?= current_page('../'); ?>" class="btn btn-outline-primary rounded-pill --xhr">
                        <i class="mdi mdi-arrow-left"></i>
                        <?= phrase('Back'); ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
<?php else: ?>
<div class="py-3 py-md-5">
    <div class="container">
        <div class="row mb-5">
            <div class="col-md-6 offset-md-3">
                <div class="text-center py-5">
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
        </div>
    </div>
</div>
<?php endif; ?>
