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
                        <div class="mb-3">
                            <div class="row mb-4">
                                <div class="col-3 col-md-3 pt-2 order-sm-<?= ($key %2 === 0 ? '0' : '1'); ?>">
                                    <img src="<?= get_image('testimonials', $val->photo, 'thumb'); ?>" class="img-fluid w-100 rounded-4">
                                </div>
                                <div class="col-9 col-md-9 order-sm-<?= ($key %2 === 0 ? '1' : '0'); ?>">
                                    <blockquote class="blockquote">
                                        <h2>
                                            <?= $val->testimonial_title; ?>
                                        </h2>
                                        <div class="lead mb-4">
                                            <?= $val->testimonial_content; ?>
                                        </div>
                                        <footer class="blockquote-footer">
                                            <b class="text-primary"><?= $val->first_name . ' ' . $val->last_name; ?></b>, <?= $val->timestamp; ?>
                                        </footer>
                                    </blockquote>
                                </div>
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
                            <?= phrase('No testimonial is found!'); ?>
                        </h2>
                        <p class="lead text-center mb-5">
                            <?= phrase('No testimonial is available at the moment.'); ?>
                        </p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
