<?php

/**
 * @var mixed $results
 * @var mixed $meta
 * @var mixed $pagination
 */
if ($results): ?>
<section class="section-padding border-fade-bottom fade-in">
    <div class="container text-center text-md-start">
        <div class="row align-items-end">
            <div class="col-lg-7">
                <h1 class="display-4 fw-bold">
                    <?= $meta->title; ?>
                </h1>
                <p class="fs-5 text-muted mb-0">
                    <?= truncate($meta->description, 256); ?>
                </p>
            </div>
            <div class="col-lg-5">
                <form action="<?= go_to(null, ['per_page' => null]); ?>" method="GET">
                    <div class="d-flex g-3 rounded-pill border border-light-subtle p-1">
                        <div class="input-group ps-4">
                            <i class="mdi mdi-magnify mdi-2x text-muted"></i>
                            <input type="text" name="q" class="form-control form-control-lg fw-light border-0 bg-transparent" value="<?= htmlspecialchars(service('request')->getGet('q') ?? ''); ?>" placeholder="<?= phrase('Search peoples...'); ?>">
                            <button type="submit" class="btn btn-primary btn-lg fw-light rounded-pill px-4">
                                <?= phrase('Search'); ?> <i class="mdi mdi-arrow-right"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<section class="section-padding">
    <div class="container">
        <?php if ($results): ?>
            <div class="row">
                <?php foreach ($results as $key => $val): ?>
                    <div class="col-6 col-md-4 col-lg-3">
                        <div class="card rounded-5 bg-body border-hover fade-in">
                            <div class="card-body text-center">
                                <a href="<?= go_to($val->people_slug); ?>" class="d-block --xhr">
                                    <img src="<?= get_image('peoples', $val->photo, 'thumb'); ?>" class="img-fluid rounded-circle" alt="<?= $val->first_name . ' '  . $val->last_name; ?>" />
                                </a>
                                <h2 class="h3 text-truncate">
                                    <a href="<?= go_to($val->people_slug); ?>" class="--xhr">
                                        <?= truncate($val->first_name, 22) . ' ' . truncate($val->last_name, 22); ?>
                                    </a>
                                </h2>
                                <p class="fs-6 text-muted class="text-truncate">
                                    <a href="<?= go_to($val->people_slug); ?>" class="text-muted --xhr">
                                        <?= truncate($val->position, 22); ?>
                                    </a>
                                </p>
                                <div class="d-flex gap-2 justify-content-center">
                                    <?php if ($val->instagram): ?>
                                        <a href="<?= $val->instagram; ?>" class="btn btn-outline-danger d-block rounded-circle mb-3" target="_blank">
                                            <i class="mdi mdi-instagram"></i>
                                        </a>
                                    <?php endif; ?>
                                    <?php if ($val->facebook): ?>
                                        <a href="<?= $val->facebook; ?>" class="btn btn-outline-primary d-block rounded-circle mb-3" target="_blank">
                                            <i class="mdi mdi-facebook"></i>
                                        </a>
                                    <?php endif; ?>
                                    <?php if ($val->twitter): ?>
                                        <a href="<?= $val->twitter; ?>" class="btn btn-outline-info d-block rounded-circle mb-3" target="_blank">
                                            <i class="mdi mdi-twitter"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <?= pagination($pagination); ?>
        <?php else: ?>
            <div class="row">
                <div class="col-lg-6 offset-lg-3">
                    <?= view('templates/404', [...(array) $meta, 'searchLabel' => phrase('Search albums...')]); ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>
