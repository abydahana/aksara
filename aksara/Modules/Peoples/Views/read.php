<?php
    $people = (isset($results[0]) ? $results[0] : []);
?>
<?php if ($people): ?>
    <div class="bg-light">
        <div class="container">
            <br />
            <br />
            <br />
            <br />
            <br />
            <br />
            <br />
            <br />
        </div>
    </div>
    <div class="container">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="text-center" style="margin-top:-150px">
                    <a href="<?= get_image('peoples', $people->photo); ?>" target="_blank">
                        <img src="<?= get_image('peoples', $people->photo, 'thumb'); ?>" class="img-fluid rounded-pill mb-5" style="border:5px solid #fff" alt="<?= $people->first_name . ' ' . $people->last_name; ?>" />
                    </a>
                </div>
                <div class="mb-3">
                    <h4 class="text-muted mb-0">
                        <?= phrase('Full Name'); ?>
                    </h4>
                    <p class="lead">
                        <?= $people->first_name . ' ' . $people->last_name; ?>
                    </p>
                </div>
                <div class="mb-3">
                    <h4 class="text-muted mb-0">
                        <?= phrase('Position'); ?>
                    </h4>
                    <p class="lead">
                        <?= $people->position; ?>
                    </p>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-6">
                        <h4 class="text-muted mb-0">
                            <?= phrase('Email'); ?>
                        </h4>
                        <p class="lead">
                            <?= $people->email; ?>
                        </p>
                    </div>
                    <div class="col-sm-6">
                        <h4 class="text-muted mb-0">
                            <?= phrase('Mobile'); ?>
                        </h4>
                        <p class="lead">
                            <?= $people->mobile; ?>
                        </p>
                    </div>
                </div>
                <div class="mb-3">
                    <blockquote class="blockquote text-secondary">
                        <?= $people->biography; ?>
                    </blockquote>
                </div>
                <div class="mb-3">
                    <div class="row">
                        <?php if ($people->instagram): ?>
                            <div class="col-sm-6 col-md-4">
                                <a href="<?= $people->instagram; ?>" class="btn btn-outline-danger d-block  rounded-pill mb-3" target="_blank">
                                    <i class="mdi mdi-instagram"></i>
                                    Instagram
                                </a>
                            </div>
                        <?php endif; ?>
                        <?php if ($people->facebook): ?>
                            <div class="col-sm-6 col-md-4">
                                <a href="<?= $people->facebook; ?>" class="btn btn-outline-primary d-block  rounded-pill mb-3" target="_blank">
                                    <i class="mdi mdi-facebook"></i>
                                    Facebook
                                </a>
                            </div>
                        <?php endif; ?>
                        <?php if ($people->instagram): ?>
                            <div class="col-sm-6 col-md-4">
                                <a href="<?= $people->twitter; ?>" class="btn btn-outline-info d-block rounded-pill mb-3" target="_blank">
                                    <i class="mdi mdi-twitter"></i>
                                    Twitter
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php else: ?>
    <div class="py-3 py-md-5">
        <div class="text-center">
            <div class="rounded-circle bg-danger d-flex align-items-center justify-content-center" style="border:5px solid #fff; width:128px; height:128px; margin:0 auto">
                <i class="mdi mdi-account-off-outline mdi-5x text-light"></i>
            </div>
        </div>
        <div class="row mb-5">
            <div class="col-md-8 offset-md-2">
                <h2 class="text-center">
                    <?= phrase('People was not found!'); ?>
                </h2>
                <p class="lead text-center mb-5">
                <?= phrase('The people you are looking for was not found.'); ?>
                </p>
                <div class="text-center mt-5">
                    <a href="<?= go_to('../'); ?>" class="btn btn-outline-primary rounded-pill --xhr">
                        <i class="mdi mdi-arrow-left"></i>
                        <?= phrase('Back to Peoples'); ?>
                    </a>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php if ($similar): ?>
    <div class="py-3 py-md-5">
        <div class="container">
            <div class="row">
                <div class="col-md-8 offset-md-2">
                    <h3 class="my-3">
                        <?= phrase('People you might want to know'); ?>
                    </h3>
                    <div class="row">
                        <?php foreach ($similar as $key => $val): ?>
                            <div class="col-6 col-md-4">
                                <div class="card border-0 rounded-4 shadow-sm mb-3">
                                    <a href="<?= go_to('../' . $val->people_slug); ?>" class="--xhr">
                                        <img src="<?= get_image('peoples', $val->photo, 'thumb'); ?>" class="card-img-top" alt="<?= $val->first_name . ' '  . $val->last_name; ?>" width="100%" />
                                    </a>
                                    <div class="card-body">
                                        <a href="<?= go_to('../' . $val->people_slug); ?>" class="--xhr">
                                            <h6 class="card-title text-center text-truncate">
                                                <?= truncate($val->first_name, 22) . ' ' . truncate($val->last_name, 22); ?>
                                            </h6>
                                        </a>
                                        <a href="<?= go_to('../' . $val->people_slug); ?>" class="--xhr">
                                            <h6 class="card-subtitle fw-light text-center mb-2 text-muted text-truncate">
                                            <?= truncate($val->position, 22); ?>
                                            </h6>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
