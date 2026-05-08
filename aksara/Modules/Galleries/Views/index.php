<?php
/**
 * @var mixed $results
 * @var mixed $meta
 * @var mixed $pagination
 */

if ($results): ?>
<section class="section-padding">
    <!-- Background Wavy Shape -->
    <svg class="position-absolute top-0 d-none d-md-block hero-blob" viewBox="0 0 948 458" fill="none">
        <path fill="currentColor" d="M179.493 278.507C88.0136 187.027 42.2737 141.287 21.1376 90.2621C-7.04587 22.2238 -7.04587 -54.2238 21.1376 -122.262C42.2737 -173.287 88.0136 -219.027 179.493 -310.507C270.973 -401.986 316.713 -447.726 367.738 -468.862C435.776 -497.046 512.224 -497.046 580.262 -468.862C631.287 -447.726 677.027 -401.986 768.507 -310.507C859.986 -219.027 905.726 -173.287 926.862 -122.262C955.046 -54.2238 955.046 22.2238 926.862 90.2621C905.726 141.287 859.986 187.027 768.507 278.507C677.027 369.986 631.287 415.726 580.262 436.862C512.224 465.046 435.776 465.046 367.738 436.862C316.713 415.726 270.973 369.986 179.493 278.507Z"/>
    </svg>
    <div class="container position-relative text-center text-md-start fade-in" style="z-index: 1;">
        <h1 class="display-4 fw-bold text-dark">
            <?= $meta->title; ?>
        </h1>
        <p class="fs-5 text-muted mb-0">
            <?= truncate($meta->description, 256); ?>
        </p>
    </div>
</section>
<?php endif; ?>
<section class="section-padding">
    <div class="container">
        <?php if ($results): ?>
            <div class="row">
                <?php foreach ($results as $key => $val): ?>
                    <?php
                        $cover = null;
                        $thumbnail = null;
                        $images = json_decode($val->gallery_images, true);

                        if (! empty($images)) {
                            $num = 1;

                            foreach ($images as $src => $alt) {
                                if ($num >= 4) {
                                    break;
                                }

                                if (1 == $num) {
                                    $cover = $src;
                                } elseif ($num > 1) {
                                    $thumbnail .= '<a href="' . go_to([$val->gallery_slug, $src]) . '" class="--xhr"><img src="' . get_image('galleries', $src, 'thumb') . '" class="w-100" /></a>';
                                }

                                $num++;
                            }
                        }
                    ?>
                    <div class="col-lg-6">
                        <div class="rounded-4 overflow-hidden mb-3">
                            <div class="row g-0">
                                <div class="col-<?= (count($images) <= 2 ? 'md-' : null) . (count($images) == 2 ? 6 : (count($images) == 1 ? 12 : 9)); ?> text-center d-flex align-items-center" style="background:url(<?= get_image('galleries', $cover); ?>) center center no-repeat; background-size:cover; min-height:320px">
                                    <div class="p-3 w-100" style="background:rgba(0, 0, 0, .5)">
                                        <h4 class="text-light">
                                            <span class="badge bg-primary float-end">
                                                <?= count($images); ?>
                                            </span>
                                            <?= $val->gallery_title; ?>
                                        </h4>
                                        <p class="text-light">
                                            <?= truncate($val->gallery_description, 160); ?>
                                        </p>
                                        <p class="text-light">
                                            <?php if (count($images) > 4): ?>
                                                <a href="<?= go_to($val->gallery_slug); ?>" class="btn btn-outline-light rounded-pill --xhr">
                                                    <i class="mdi mdi-folder-multiple-image"></i>
                                                    <?= phrase('Show all'); ?>
                                                </a>
                                            <?php else: ?>
                                                <a href="<?= go_to([$val->gallery_slug, $cover]); ?>" class="btn btn-outline-light rounded-pill --xhr">
                                                    <i class="mdi mdi-magnify-plus"></i>
                                                    <?= phrase('Show'); ?>
                                                </a>
                                            <?php endif; ?>
                                        </p>
                                    </div>
                                </div>
                                <?php if (count($images) > 1): ?>
                                    <div class="col-<?= (count($images) <= 2 ? 'md-' : null) . (count($images) > 2 ? 3 : 6); ?> bg-light d-flex align-items-center">
                                        <div class="w-100">
                                        <?= $thumbnail; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
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
                            <?= phrase('No album is found!'); ?>
                        </h2>
                        <p class="lead text-center">
                            <?= phrase('No album is available at the moment.'); ?>
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
</section>
