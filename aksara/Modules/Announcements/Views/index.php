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
        <?php if ($results): ?>
        <div class="row">
            <div class="col-lg-8 offset-lg-2">
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
            </div>
        </div>
        <?php else: ?>
        <div class="row">
            <div class="col-lg-6 offset-lg-3">
                <div class="py-5 fade-in">
                    <div class="text-center mb-3">
                        <img src="<?= get_module_asset('images/404.png'); ?>" width="128" alt="404" />
                    </div>
                    <h2 class="text-center">
                        <?= phrase('No announcement is found!'); ?>
                    </h2>
                    <p class="fs-5 text-center">
                        <?= (service('request')->getGet('q') ? phrase('No announcement is found related to your keywords.') : phrase('No announcement is available at the moment.')); ?>
                    </p>
                    <form action="<?= go_to(null, ['per_page' => null]); ?>" method="GET">
                        <div class="d-flex g-3 rounded-pill border border-light-subtle p-1">
                            <div class="input-group ps-4">
                                <i class="mdi mdi-magnify mdi-2x text-muted"></i>
                                <input type="text" name="q" class="form-control form-control-lg fw-light border-0 bg-transparent" value="<?= htmlspecialchars(service('request')->getGet('q') ?? ''); ?>" placeholder="<?= phrase('Search announcements...'); ?>" required>
                                <button type="submit" class="btn btn-primary btn-lg fw-light rounded-pill px-4">
                                    <?= phrase('Search'); ?> <i class="mdi mdi-arrow-right"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>
