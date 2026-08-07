<?php

/**
 * @var mixed $results
 */
$field_data = $results->field_data ?? null;
$count = 0;
$images = ($field_data ? json_decode($field_data->gallery_images->value) : []);
$attributes = ($field_data ? json_decode($field_data->gallery_attributes->value) : []);
$path = service('uri')->getRoutePath();
$current = service('uri')->getSegment(3);
$carousel = null;

if ($images) {
    foreach ($images as $key => $val) {
        $carousel .= '
            <div class="carousel-item text-center' . ($current == $key || (! $count && ! in_array($current, (array) $images)) ? ' active' : null) . '">
                <div class="full-height d-flex align-items-center justify-content-center bg-secondary">
                    <img src="' . get_image('galleries', $key) . '" class="img-fluid" alt="' . $val . '">
                    <div class="carousel-caption d-none d-md-block text-shadow">
                        ' . $val . '
                    </div>
                </div>
            </div>
        ';

        $count++;
    }
}
?>

<?php if ($field_data): ?>
    <div class="row g-0 bg-light">
        <div class="col-lg-8">
            <div class="sticky-top">
                <div class="photo-view">
                    <div class="position-relative overflow-hidden">
                        <div id="carouselExampleControls" class="carousel slide" data-bs-ride="carousel">
                            <div class="carousel-inner">
                                <?= $carousel; ?>
                            </div>
                            <?php if ($count > 1): ?>
                                <button class="carousel-control-prev gradient-right" type="button" data-bs-target="#carouselExampleControls" role="button" data-bs-slide="prev">
                                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                    <span class="visually-hidden">
                                        <?= phrase('Previous'); ?>
                                    </span>
                                </button>
                                <button class="carousel-control-next gradient-left" type="button" data-bs-target="#carouselExampleControls" role="button" data-bs-slide="next">
                                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                    <span class="visually-hidden">
                                        <?= phrase('Next'); ?>
                                    </span>
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 p-3 bg-white">
            <div class="sticky-top">
                <div class="row align-items-center mb-3">
                    <div class="col-2 pe-0">
                        <a href="<?= base_url('user/' . $field_data->username->value); ?>" class="--xhr">
                            <img src="<?= get_image('users', $field_data->photo->value, 'thumb'); ?>" class="img-fluid rounded-circle" alt="<?= $field_data->first_name->value . ' ' . $field_data->last_name->value; ?>" />
                        </a>
                    </div>
                    <div class="col-10">
                        <h5 class="fw-bold mb-0">
                            <a href="<?= current_page('../'); ?>" class="float-end btn btn-close --xhr">&nbsp;</a>
                            <a href="<?= base_url('user/' . $field_data->username->value); ?>" class="--xhr">
                                <?= $field_data->first_name->value . ' ' . $field_data->last_name->value; ?>
                            </a>
                        </h5>
                        <p class="mb-0">
                            <span class="text-muted" data-bs-toggle="tooltip" title="<?= $field_data->updated_at->value; ?>">
                                <?= time_ago($field_data->updated_at->value); ?>
                            </span>
                        </p>
                    </div>
                </div>
                <div>
                    <?= custom_nl2br($field_data->gallery_description->value, 1); ?>
                </div>
                <?php
                if ($attributes) {
                    echo '<hr class="border-secondary" />';

                    foreach ($attributes as $label => $value) {
                        if (! $value) continue;

                        echo '
                            <div class="row">
                                <div class="col-sm-4 col-lg-3">
                                    <label class="d-block text-muted">
                                        ' . $label . '
                                    </label>
                                </div>
                                <div class="col-sm-8 col-lg-9">
                                    <label>
                                        ' . $value . '
                                    </label>
                                </div>
                            </div>
                        ';
                    }
                }
                ?>
                <div>
                    <?= comment_widget(['post_id' => $field_data->gallery_id->value, 'path' => $path]); ?>
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
                            <?= phrase('Album not found!'); ?>
                        </h2>
                        <p class="fs-5 text-center mb-5">
                            <?= phrase('The album you requested was not found or its already been removed.'); ?>
                        </p>
                        <p class="text-center">
                            <a href="<?= current_page('../'); ?>" class="btn btn-outline-dark rounded-pill px-5 --xhr">
                                <i class="mdi mdi-arrow-left"></i> <?= phrase('Back to Galleries'); ?>
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
