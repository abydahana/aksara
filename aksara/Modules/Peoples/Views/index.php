<?php if ($results): ?>
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
<?php endif; ?>

<div class="py-3 py-md-5">
    <div class="container">
        <?php if ($results): ?>
            <div class="row">
                <?php foreach ($results as $key => $val): ?>
                    <div class="col-6 col-md-4 col-lg-3">
                        <div class="mb-4 text-center">
                            <a href="<?= go_to($val->people_slug); ?>" class="--xhr">
                                <img src="<?= get_image('peoples', $val->photo, 'thumb'); ?>" class="img-fluid rounded-circle" alt="<?= $val->first_name . ' '  . $val->last_name; ?>" />
                            </a>
                            <h3 class="text-truncate">
                                <a href="<?= go_to($val->people_slug); ?>" class="--xhr">
                                    <?= truncate($val->first_name, 22) . ' ' . truncate($val->last_name, 22); ?>
                                </a>
                            </h3>
                            <h6 class="text-truncate">
                                <a href="<?= go_to($val->people_slug); ?>" class="text-muted --xhr">
                                    <?= truncate($val->position, 22); ?>
                                </a>
                            </h6>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <?= pagination($pagination); ?>
        <?php else: ?>
            <div class="row">
                <div class="col-lg-8 offset-lg-2">
                    <div class="py-5">
                        <div class="text-center">
                            <img src="<?= base_url('assets/yao-ming.png'); ?>" width="128" alt="404" />
                        </div>
                        <h2 class="text-center">
                            <?= phrase('No people is found!'); ?>
                        </h2>
                        <p class="lead text-center">
                            <?= phrase('No people is available at the moment.'); ?>
                        </p>
                        <p class="text-center">
                            <a href="<?= base_url(); ?>" class="btn btn-outline-dark rounded-pill px-5 --xhr">
                                <i class="mdi mdi-arrow-left"></i> <?= phrase('Back to Home'); ?>
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
