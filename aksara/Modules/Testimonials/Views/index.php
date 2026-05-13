<?php
/**
 * @var mixed $results
 * @var mixed $meta
 * @var mixed $pagination
 */

if ($results): ?>
<section class="section-padding">
    <div class="container position-relative text-center text-md-start fade-in">
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
</section>
