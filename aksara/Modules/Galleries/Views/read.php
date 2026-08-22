<?php

/**
 * @var object $meta
 * @var mixed $results
 */
$fieldData = $results->field_data ?? null;
$count = 0;
$images = $fieldData ? json_decode($fieldData->gallery_images->value) : [];
$attributes = $fieldData ? json_decode($fieldData->gallery_attributes->value) : [];
$path = service('uri')->getRoutePath();
$current = service('uri')->getSegment(3);
$carousel = null;

if ($images) {
    ob_start();

    foreach ($images as $key => $val) { ?>
        <div class="carousel-item text-center<?= $current == $key || (! $count && ! in_array($current, (array) $images)) ? ' active' : null ?>">
            <div class="full-height d-flex align-items-center justify-content-center bg-secondary">
                <img src="<?= get_image('galleries', $key) ?>" class="img-fluid" alt="<?= $val ?>" loading="lazy" decoding="async">
                <div class="carousel-caption d-none d-md-block text-shadow">
                    <?= $val ?>
                </div>
            </div>
        </div>

    <?php
        $count++;
    }

    $carousel = ob_get_clean();
}

if ($fieldData): ?>
    <div class="row g-0 bg-body-tertiary">
        <div class="col-lg-8">
            <div class="sticky-lg-top">
                <div class="photo-view">
                    <div class="position-relative overflow-hidden">
                        <div id="carouselExampleControls" class="carousel slide" data-bs-ride="carousel">
                            <div class="carousel-inner">
                                <?= $carousel ?>
                            </div>

                            <?php if ($count > 1): ?>
                                <button class="carousel-control-prev gradient-right" type="button" data-bs-target="#carouselExampleControls" data-bs-slide="prev" aria-label="<?= phrase('Previous') ?>">
                                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                    <span class="visually-hidden">
                                        <?= phrase('Previous') ?>
                                    </span>
                                </button>
                                <button class="carousel-control-next gradient-left" type="button" data-bs-target="#carouselExampleControls" data-bs-slide="next" aria-label="<?= phrase('Next') ?>">
                                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                    <span class="visually-hidden">
                                        <?= phrase('Next') ?>
                                    </span>
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 p-3 bg-body">
            <div class="sticky-lg-top">
                <div class="row align-items-center mb-3">
                    <div class="col-2 pe-0">
                        <a href="<?= base_url('user/' . $fieldData->username->value) ?>" class="d-block --xhr">
                            <img src="<?= get_image('users', $fieldData->photo->value, 'thumb') ?>" class="img-fluid rounded-circle" alt="<?= $fieldData->first_name->value . ' ' . $fieldData->last_name->value ?>" loading="lazy" decoding="async" />
                        </a>
                    </div>
                    <div class="col-10">
                        <h2 class="h5 fw-bold mb-0">
                            <a href="<?= current_page('../') ?>" class="float-end btn btn-close --xhr">&nbsp;</a>
                            <a href="<?= base_url('user/' . $fieldData->username->value) ?>" class="--xhr">
                                <?= $fieldData->first_name->value . ' ' . $fieldData->last_name->value ?>
                            </a>
                        </h5>
                        <p class="mb-0">
                            <span class="text-muted" data-bs-toggle="tooltip" title="<?= $fieldData->updated_at->value ?>">
                                <?= time_ago($fieldData->updated_at->value) ?>
                            </span>
                        </p>
                    </div>
                </div>
                <div>
                    <?= custom_nl2br($fieldData->gallery_description->value, 1) ?>
                </div>

                <?php if ($attributes): ?>
                    <hr class="border-secondary" />
                    <?php foreach ($attributes as $label => $value):
                        if (! $value) {
                            continue;
                        } ?>

                        <div class="row">
                            <div class="col-sm-4 col-lg-3 text-muted">
                                <?= $label ?>
                            </div>
                            <div class="col-sm-8 col-lg-9">
                                <?= $value ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>

                <div>
                    <?= comment_widget(['post_id' => $fieldData->gallery_id->value, 'path' => $path]) ?>
                </div>
            </div>
        </div>
    </div>
<?php else: ?>
    <div class="py-5 fade-iin">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 offset-lg-3">
                    <?= view('templates/404', [...(array) $meta, 'searchAction' => go_to('../', ['page' => null]), 'searchLabel' => phrase('Search albums...')]) ?>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
