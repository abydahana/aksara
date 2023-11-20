<div class="py-3 py-md-5 bg-light">
    <div class="container">
        <h1 class="text-center text-md-start">
            <?= $meta->title; ?>
        </h1>
        <p class="lead text-center text-md-start">
            <?= truncate($meta->description, 256); ?>
        </p>
    </div>
</div>
<?php if ($results): ?>
    <div class="py-3 py-md-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <?php foreach ($results as $key => $val): ?>
                        <div class="mb-3">
                            <div class="row mb-3 align-items-center">
                                <div class="col-3 col-md-3 pt-2">
                                    <img src="<?= get_image('testimonials', $val->photo, 'thumb'); ?>" class="img-fluid rounded-4">
                                </div>
                                <div class="col-9 col-md-9">
                                    <h4 class="article fw-bold">
                                        <?= $val->testimonial_title; ?>
                                    </h4>
                                    <div class="article mb-4">
                                        <?= $val->testimonial_content; ?>
                                    </div>
                                    <p class="blockquote-footer mb-0">
                                        <b><?= $val->first_name . ' ' . $val->last_name; ?></b>, 
                                        <?= $val->timestamp; ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <?= pagination($pagination); ?>
        </div>
    </div>
<?php else: ?>
    <div class="py-3 py-md-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <div class="text-muted">
                        <i class="mdi mdi-information-outline"></i>
                        <?= phrase('No testimonial are available right now.'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
