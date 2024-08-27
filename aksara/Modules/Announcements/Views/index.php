<?php if ($results): ?>
    <div class="py-3 py-md-5 bg-light d-lg-none">
        <div class="container">
            <h1 class="text-center text-md-start">
                <?= $meta->title; ?>
            </h1>
            <p class="lead text-center text-md-start">
                <?= truncate($meta->description, 256); ?>
            </p>
        </div>
    </div>
<?php endif; ?>

<div class="py-3 py-md-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 offset-lg-2">
                <?php if ($results): ?>
                    <?php foreach ($results as $key => $val): ?>
                        <div class="row align-items-center mb-3 mb-lg-5">
                            <div class="col-8 col-md-9">
                                <blockquote class="blockquote">
                                    <h2>
                                        <a href="<?= go_to($val->announcement_slug); ?>" class="--xhr">
                                            <?= $val->title; ?>
                                        </a>
                                    </h2>
                                    <div class="lead mb-4 d-none d-md-block">
                                        <?= truncate($val->content, 160); ?>
                                    </div>
                                    <footer class="blockquote-footer">
                                        <?= phrase('Effective until') . ' ' . $val->end_date; ?>
                                    </footer>
                                </blockquote>
                            </div>
                            <div class="col-4 col-md-3">
                                <a href="<?= go_to($val->announcement_slug); ?>" class="--xhr">
                                    <img src="<?= get_image('announcements', $val->cover, 'thumb'); ?>" class="img-fluid rounded-4" alt="..." />
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                        
                    <?= pagination($pagination); ?>
                <?php else: ?>
                    <div class="py-5">
                        <div class="text-center">
                            <i class="mdi mdi-dropbox mdi-5x text-muted"></i>
                        </div>
                        <h2 class="text-center">
                            <?= phrase('No announcement is found!'); ?>
                        </h2>
                        <p class="lead text-center mb-5">
                            <?= phrase('No announcement is available at the moment.'); ?>
                        </p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
