<?php

/**
 * @var mixed $results
 * @var mixed $meta
 * @var mixed $pagination
 */
if ($results): ?>
    <section class="section-padding border-fade-bottom fade-in">
        <div class="container position-relative text-center text-md-start">
            <h1 class="display-4 fw-bold">
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
                        <div class="row align-items-center mb-3 mb-lg-5 fade-in">
                            <div class="col-8 col-md-9">
                                <blockquote class="blockquote">
                                    <h2>
                                        <a href="<?= go_to($val->announcement_slug); ?>" class="--xhr">
                                            <?= $val->title; ?>
                                        </a>
                                    </h2>
                                    <div class="fs-5 mb-4 d-none d-md-block">
                                        <?= truncate($val->content, 160); ?>
                                    </div>
                                    <footer class="blockquote-footer">
                                        <?= phrase('Effective until') . ' ' . $val->end_date; ?>
                                    </footer>
                                </blockquote>
                            </div>
                            <div class="col-4 col-md-3">
                                <a href="<?= go_to($val->announcement_slug); ?>" class="--xhr">
                                    <img src="<?= get_image('announcements', $val->cover, 'thumb'); ?>" class="img-fluid rounded-4" alt="<?= $val->title; ?>" />
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <?= pagination($pagination); ?>
                <?php else: ?>
                    <div class="py-5 fade-in">
                        <div class="text-center">
                            <img src="<?= base_url('assets/yao-ming.png'); ?>" width="128" alt="404" />
                        </div>
                        <h2 class="text-center">
                            <?= phrase('No announcement is found!'); ?>
                        </h2>
                        <p class="fs-5 text-center">
                            <?= phrase('No announcement is available at the moment.'); ?>
                        </p>
                        <p class="text-center">
                            <a href="<?= base_url(); ?>" class="btn btn-outline-secondary rounded-pill px-5 --xhr">
                                <i class="mdi mdi-arrow-left"></i> <?= phrase('Back to Home'); ?>
                            </a>
                        </p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
