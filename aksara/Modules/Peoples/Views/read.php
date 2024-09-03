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
        <div class="container">
            <div class="row">
                <div class="col-lg-8 offset-lg-2">
                    <div class="py-5">
                        <div class="text-center">
                            <img src="<?= base_url('assets/yao-ming.png'); ?>" width="128" alt="404" />
                        </div>
                        <h2 class="text-center">
                            <?= phrase('People was not found!'); ?>
                        </h2>
                        <p class="lead text-center mb-5">
                            <?= phrase('The people you are looking for was not found.'); ?>
                        </p>
                        <p class="text-center">
                            <a href="<?= current_page('../'); ?>" class="btn btn-outline-dark rounded-pill px-5 --xhr">
                                <i class="mdi mdi-arrow-left"></i> <?= phrase('Back to Peoples'); ?>
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
