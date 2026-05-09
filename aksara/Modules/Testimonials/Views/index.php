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
        <div class="row">
            <div class="col-lg-8 offset-lg-2">
                <?php if ($results): ?>
                    <?php foreach ($results as $key => $val): ?>
                        <div class="mb-3">
                            <div class="row align-items-center mb-4">
                                <div class="col-3 col-md-3 pt-2 order-sm-<?= ($key %2 === 0 ? '0' : '1'); ?>">
                                    <img src="<?= get_image('testimonials', $val->photo, 'thumb'); ?>" class="img-fluid w-100 rounded-circle p-2 border">
                                </div>
                                <div class="col-9 col-md-9 order-sm-<?= ($key %2 === 0 ? '1' : '0'); ?>">
                                    <blockquote class="blockquote">
                                        <h2>
                                            <?= $val->testimonial_title; ?>
                                        </h2>
                                        <div class="fs-5 mb-4">
                                            <?= $val->testimonial_content; ?>
                                        </div>
                                        <footer class="blockquote-footer">
                                            <b class="text-primary"><?= $val->first_name . ' ' . $val->last_name; ?></b>, <?= $val->timestamp; ?>
                                        </footer>
                                    </blockquote>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <?= pagination($pagination); ?>
                <?php else: ?>
                    <div class="py-5">
                        <div class="text-center">
                            <img src="<?= base_url('assets/yao-ming.png'); ?>" width="128" alt="404" />
                        </div>
                        <h2 class="text-center">
                            <?= phrase('No testimonial is found!'); ?>
                        </h2>
                        <p class="fs-5 text-center">
                            <?= phrase('No testimonial is available at the moment.'); ?>
                        </p>
                        <p class="text-center">
                            <a href="<?= base_url(); ?>" class="btn btn-outline-dark rounded-pill px-5 --xhr">
                                <i class="mdi mdi-arrow-left"></i> <?= phrase('Back to Home'); ?>
                            </a>
                        </p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
