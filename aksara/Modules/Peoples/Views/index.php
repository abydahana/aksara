<?php
    $output = null;
?>
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
                <?php foreach ($results as $key => $val): ?>
                    <div class="col-sm-6 col-md-4 col-lg-3">
                        <div class="card border-0 rounded-4 shadow-sm mb-3 overflow-hidden">
                            <a href="<?= go_to($val->people_slug); ?>" class="--xhr">
                                <img src="<?= get_image('peoples', $val->photo, 'thumb'); ?>" class="card-img-top w-100" alt="<?= $val->first_name . ' '  . $val->last_name; ?>" />
                            </a>
                            <div class="card-body">
                                <a href="<?= go_to($val->people_slug); ?>" class="--xhr">
                                    <h5 class="card-title text-center text-truncate">
                                        <?= truncate($val->first_name, 22) . ' ' . truncate($val->last_name, 22); ?>
                                    </h5>
                                </a>
                                <a href="<?= go_to($val->people_slug); ?>" class="--xhr">
                                    <h6 class="card-subtitle fw-light text-center mb-2 text-muted text-truncate">
                                        <?= truncate($val->position, 22); ?>
                                    </h6>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <?= pagination($pagination); ?>
        </div>
    </div>
<?php else: ?>
    <div class="py-3 py-md-5">
        <div class="container">
            <div class="text-muted">
                <i class="mdi mdi-information-outline"></i>
                <?= phrase('No people are available right now.'); ?>
            </div>
        </div>
    </div>
<?php endif; ?>
