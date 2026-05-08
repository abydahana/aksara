<?php
/**
 * @var mixed $results
 * @var mixed $meta
 */
$output = null;

if ($results) {
    foreach ($results as $key => $val) {
        $images = json_decode($val->gallery_images, true);
        $labels = explode(',', $val->gallery_tags);

        if (is_array($images) && sizeof($images) > 0) {
            foreach ($images as $src => $alt) {
                if (! $src) {
                    continue;
                }

                $output .= '
                    <div class="col-sm-6 col-md-3">
                        <a href="' . current_page($src) . '" class="--xhr">
                            <img src="' . get_image('galleries', $src, 'thumb') . '" class="rounded-5 w-100 mb-4" alt="' . $alt . '" />
                        </a>
                    </div>
                ';
            }
        }
    }
}

if ($output): ?>
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
<section class="section-padding">
    <div class="container">
        <div class="row">
            <?= $output; ?>
        </div>
    </div>
</section>
<?php else: ?>
    <section class="section-padding fade-in">
        <div class="container">
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
                            <a href="<?= current_page('../'); ?>" class="btn btn-outline-dark rounded-pill px-5 --xhr">
                                <i class="mdi mdi-arrow-left"></i> <?= phrase('Back to Galleries'); ?>
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php endif; ?>
