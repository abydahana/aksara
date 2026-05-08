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
<section class="section-padding fade-in">
    <div class="container text-center text-md-start">
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
                        <p class="fs-5 text-center">
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
