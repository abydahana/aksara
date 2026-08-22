<?php

/**
 * @var object $meta
 * @var mixed $results
 */
$fieldData = $results->field_data ?? null;

if ($fieldData): ?>
    <div class="border-bottom">
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
                    <a href="<?= get_image('peoples', $fieldData->photo->value) ?>" class="d-block" target="_blank">
                        <img src="<?= get_image('peoples', $fieldData->photo->value, 'thumb') ?>" class="img-fluid bg-body rounded-pill mb-5" width="300" style="border:5px solid #fff" alt="<?= $fieldData->first_name->value . ' ' . $fieldData->last_name->value ?>" />
                    </a>
                </div>
                <div class="mb-3">
                    <h2 class="h4 text-muted mb-0">
                        <?= phrase('Full Name') ?>
                    </h2>
                    <p class="fs-5">
                        <?= $fieldData->first_name->value . ' ' . $fieldData->last_name->value ?>
                    </p>
                </div>
                <div class="mb-3">
                    <h2 class="h4 text-muted mb-0">
                        <?= phrase('Position') ?>
                    </h2>
                    <p class="fs-5">
                        <?= $fieldData->position->value ?>
                    </p>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-6">
                        <h2 class="h4 text-muted mb-0">
                            <?= phrase('Email') ?>
                        </h2>
                        <p class="fs-5">
                            <?= $fieldData->email->value ?>
                        </p>
                    </div>
                    <div class="col-sm-6">
                        <h2 class="h4 text-muted mb-0">
                            <?= phrase('Mobile') ?>
                        </h2>
                        <p class="fs-5">
                            <?= $fieldData->mobile->value ?>
                        </p>
                    </div>
                </div>
                <div class="mb-3">
                    <p class="fs-5 text-muted">
                        <?= $fieldData->biography->value ?>
                    </p>
                </div>
                <div class="mb-3">
                    <div class="row">
                        <?php if ($fieldData->instagram->value): ?>
                            <div class="col-sm-6 col-md-4">
                                <a href="<?= $fieldData->instagram->value ?>" class="btn btn-outline-danger d-block rounded-pill mb-3" target="_blank">
                                    <i class="mdi mdi-instagram"></i>
                                    Instagram
                                </a>
                            </div>
                        <?php endif; ?>

                        <?php if ($fieldData->facebook->value): ?>
                            <div class="col-sm-6 col-md-4">
                                <a href="<?= $fieldData->facebook->value ?>" class="btn btn-outline-primary d-block rounded-pill mb-3" target="_blank">
                                    <i class="mdi mdi-facebook"></i>
                                    Facebook
                                </a>
                            </div>
                        <?php endif; ?>

                        <?php if ($fieldData->twitter->value): ?>
                            <div class="col-sm-6 col-md-4">
                                <a href="<?= $fieldData->twitter->value ?>" class="btn btn-outline-info d-block rounded-pill mb-3" target="_blank">
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
    <div class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 offset-lg-3">
                    <?= view('templates/404', [...(array) $meta, 'searchAction' => go_to('../', ['page' => null]), 'searchLabel' => phrase('Search peoples...')]) ?>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
