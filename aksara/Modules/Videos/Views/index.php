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
                        <?= $meta->title ?>
                    </h1>
                    <p class="fs-5 text-muted mb-0">
                        <?= truncate($meta->description, 256) ?>
                    </p>
                </div>
                <div class="col-lg-5">
                    <form action="<?= go_to(null, ['page' => null]) ?>" method="GET">
                        <div class="d-flex g-3 rounded-pill border border-light-subtle p-1">
                            <div class="input-group ps-4">
                                <i class="mdi mdi-magnify mdi-2x text-muted"></i>
                                <input type="text" name="q" class="form-control form-control-lg fw-light border-0 bg-transparent" value="<?= htmlspecialchars(service('request')->getGet('q') ?? '') ?>" placeholder="<?= phrase('Search videos...') ?>">
                                <button type="submit" class="btn btn-primary btn-lg fw-light rounded-pill px-4">
                                    <?= phrase('Search') ?> <i class="mdi mdi-arrow-right"></i>
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
                    <div class="col-md-6 col-lg-4">
                        <div class="mb-3 fade-in">
                            <div class="rounded-5 border-hover mb-4">
                                <a href="<?= base_url('videos/' . $val->slug) ?>" class="--xhr">
                                    <img src="<?= get_image('videos', $val->cover, 'thumb') ?>" class="w-100 rounded-5" style="max-height:240px;object-fit: cover" alt="<?= $val->title ?>" loading="lazy" decoding="async" />
                                </a>
                            </div>
                            <div class="row g-0">
                                <div class="col-2">
                                    <a href="<?= base_url('user/' . $val->username) ?>" class="text-sm text-secondary d-block --xhr">
                                        <img src="<?= get_image('users', $val->photo, 'icon') ?>" class="img-fluid rounded-circle" alt="<?= $val->first_name . ' ' . $val->last_name ?>" loading="lazy" decoding="async" />
                                    </a>
                                </div>
                                <div class="col-10 ps-2">
                                    <h3 class="h5 mb-0">
                                        <a href="<?= base_url('videos/' . $val->slug) ?>" class="--xhr">
                                            <?= truncate($val->title, 80) ?>
                                        </a>
                                    </h3>
                                    <p class="mb-0">
                                        <a href="<?= base_url('user/' . $val->username) ?>" class="text-body --xhr">
                                            <strong><?= $val->first_name . ' ' . $val->last_name ?></strong>
                                        </a>
                                    </p>
                                    <p class="text-muted small mb-0"><?= time_ago($val->created_at) ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <?= pagination($pagination) ?>
        <?php else: ?>
            <div class="row">
                <div class="col-lg-6 offset-lg-3">
                    <?= view('templates/404', [...(array) $meta, 'searchLabel' => phrase('Search videos...')]) ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>
