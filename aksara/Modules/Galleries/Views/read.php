<?php
    $count = 0;
    $images = (isset($results[0]) ? json_decode($results[0]->gallery_images) : []);
    $attributes = (isset($results[0]) ? json_decode($results[0]->gallery_attributes) : []);
    $current = service('uri')->getSegment(3);
    $carousel = null;

    if ($images) {
        foreach ($images as $key => $val) {
            $carousel .= '
                <div class="carousel-item text-center' . ($current == $key ? ' active' : null) . '">
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
<style type="text/css">
    .modal-header {
        display: none
    }
</style>
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
                <div class="col-3 col-md-2 pe-0">
                    <a href="<?= base_url('user/' . $results[0]->username); ?>" class="--xhr">
                        <img src="<?= get_image('users', $results[0]->photo, 'thumb'); ?>" class="img-fluid rounded-circle" />
                    </a>
                </div>
                <div class="col-9 col-md-10">
                    <a href="<?= current_page('../'); ?>" class="float-end --xhr">
                        <i class="mdi mdi-window-close"></i>
                    </a>
                    <div>
                        <a href="<?= base_url('user/' . $results[0]->username); ?>" class="--xhr">
                            <b class="mb-0">
                                <?= $results[0]->first_name . ' ' . $results[0]->last_name; ?>
                            </b>
                        </a>
                    </div>
                    <p class="mb-0">
                        <span class="text-sm text-muted" data-bs-toggle="tooltip" title="<?= $results[0]->updated_timestamp; ?>">
                            <?= time_ago($results[0]->updated_timestamp); ?>
                        </span>
                    </p>
                </div>
            </div>
            <div>
                <?= $results[0]->gallery_description; ?>
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
                <?= comment_widget(['post_id' => $results[0]->gallery_id, 'path' => service('uri')->getRoutePath()]); ?>
            </div>
        </div>
    </div>
</div>
