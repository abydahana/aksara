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
                    <div class="col-md-6 col-lg-4">
                        <div class="mb-3">
                            <a href="<?= base_url('videos/' . $val->slug); ?>" class="--xhr">
                                <img src="<?= get_image('videos', $val->cover, 'thumb'); ?>" class="w-100 rounded-4 mb-3" style="max-height:240px;object-fit: cover" />
                            </a>
                            <div class="row g-0">
                                <div class="col-2">
                                    <a href="<?= base_url('user/' . $val->username); ?>" class="text-sm text-secondary --xhr">
                                        <img src="<?= get_image('users', $val->photo, 'icon'); ?>" class="img-fluid rounded-circle" alt="..." />
                                    </a>
                                </div>
                                <div class="col-10 ps-2">
                                    <h5 class="mb-0">
                                        <a href="<?= base_url('videos/' . $val->slug); ?>" class="--xhr">
                                            <?= truncate($val->title, 80); ?>
                                        </a>
                                    </h5>
                                    <p class="mb-0">
                                        <a href="<?= base_url('user/' . $val->username); ?>" class="text-dark --xhr">
                                            <b>
                                                <?= $val->first_name . ' ' . $val->last_name; ?>
                                            </b>
                                        </a>
                                    </p>
                                    <p class="mb-0 text-muted">
                                        <?= time_ago($val->timestamp); ?>
                                    </p>
                                </div>
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
                            <?= phrase('No video is found!'); ?>
                        </h2>
                        <p class="lead text-center">
                            <?= phrase('No video is available at the moment.'); ?>
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
