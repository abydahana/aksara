<?php

/**
 * @var object $meta
 * @var mixed $results
 */
$field_data = $results->field_data ?? null;

if ($field_data): ?>
    <div class="border-fade-bottom">
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
                    <a href="<?= get_image('peoples', $field_data->photo->value); ?>" class="d-block" target="_blank">
                        <img src="<?= get_image('peoples', $field_data->photo->value, 'thumb'); ?>" class="img-fluid bg-body rounded-pill mb-5" width="300" style="border:5px solid #fff" alt="<?= $field_data->first_name->value . ' ' . $field_data->last_name->value; ?>" />
                    </a>
                </div>
                <div class="mb-3">
                    <h2 class="h4 text-muted mb-0">
                        <?= phrase('Full Name'); ?>
                    </h2>
                    <p class="fs-5">
                        <?= $field_data->first_name->value . ' ' . $field_data->last_name->value; ?>
                    </p>
                </div>
                <div class="mb-3">
                    <h2 class="h4 text-muted mb-0">
                        <?= phrase('Position'); ?>
                    </h2>
                    <p class="fs-5">
                        <?= $field_data->position->value; ?>
                    </p>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-6">
                        <h2 class="h4 text-muted mb-0">
                            <?= phrase('Email'); ?>
                        </h2>
                        <p class="fs-5">
                            <?= $field_data->email->value; ?>
                        </p>
                    </div>
                    <div class="col-sm-6">
                        <h2 class="h4 text-muted mb-0">
                            <?= phrase('Mobile'); ?>
                        </h2>
                        <p class="fs-5">
                            <?= $field_data->mobile->value; ?>
                        </p>
                    </div>
                </div>
                <div class="mb-3">
                    <p class="fs-5 text-muted">
                        <?= $field_data->biography->value; ?>
                    </p>
                </div>
                <div class="mb-3">
                    <div class="row">
                        <?php if ($field_data->instagram->value): ?>
                            <div class="col-sm-6 col-md-4">
                                <a href="<?= $field_data->instagram->value; ?>" class="btn btn-outline-danger d-block rounded-pill mb-3" target="_blank">
                                    <i class="mdi mdi-instagram"></i>
                                    Instagram
                                </a>
                            </div>
                        <?php endif; ?>
                        <?php if ($field_data->facebook->value): ?>
                            <div class="col-sm-6 col-md-4">
                                <a href="<?= $field_data->facebook->value; ?>" class="btn btn-outline-primary d-block rounded-pill mb-3" target="_blank">
                                    <i class="mdi mdi-facebook"></i>
                                    Facebook
                                </a>
                            </div>
                        <?php endif; ?>
                        <?php if ($field_data->twitter->value): ?>
                            <div class="col-sm-6 col-md-4">
                                <a href="<?= $field_data->twitter->value; ?>" class="btn btn-outline-info d-block rounded-pill mb-3" target="_blank">
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
    <div class="section-padding">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 offset-lg-3">
                    <?= view('templates/404', [...(array) $meta, 'searchAction' => go_to('../', ['per_page' => null]), 'searchLabel' => phrase('Search peoples...')]); ?>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
