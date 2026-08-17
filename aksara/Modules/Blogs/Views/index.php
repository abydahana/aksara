<?php

/**
 * @var mixed $articles
 * @var mixed $meta
 * @var mixed $spotlight
 */
if ($articles): ?>

    <section class="section-padding border-fade-bottom fade-in">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 text-center text-md-start">
                    <h1 class="display-5 fw-bold">
                        <?= $meta->title ?>
                    </h1>
                    <p class="fs-5">
                        <?= $meta->description ?>
                    </p>
                    <div class="row mb-5">
                        <div class="col-lg-10">
                            <form action="<?= base_url('blogs/search', ['page' => null]) ?>" method="GET">
                                <div class="d-flex g-3 rounded-pill border border-light-subtle p-1">
                                    <div class="input-group ps-4">
                                        <i class="mdi mdi-magnify mdi-2x text-muted"></i>
                                        <input type="text" name="q" class="form-control form-control-lg fw-light border-0 bg-transparent" placeholder="<?= phrase('Search posts...') ?>" required>
                                        <button type="submit" class="btn btn-primary btn-lg fw-light rounded-pill px-4">
                                            <?= phrase('Search') ?> <i class="mdi mdi-arrow-right"></i>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <?php if ($spotlight): ?>
                        <div class="carousel slide" id="carouselExampleCaptions" data-bs-ride="carousel">
                            <div class="carousel-inner rounded-5 border-hover overflow-hidden">
                                <?php foreach ($spotlight as $key => $val): ?>
                                    <div class="carousel-item<?= ! $key ? ' active' : null ?>">
                                        <div class="clip gradient-top"></div>
                                        <a href="<?= base_url(['blogs', $val->category_slug, $val->post_slug]) ?>" class="d-block --xhr">
                                            <img src="<?= get_image('blogs', $val->featured_image) ?>" class="d-block w-100 carousel-headline" alt="<?= $val->post_title ?>">
                                        </a>
                                        <div class="carousel-caption text-start">
                                            <div class="mb-3">
                                                <a href="<?= base_url(['blogs', $val->category_slug, $val->post_slug]) ?>" class="--xhr">
                                                    <h2 class="h3 text-outline-body">
                                                        <?= truncate($val->post_title, 80) ?>
                                                    </h2>
                                                    <p class="fs-5 d-none d-md-inline text-outline-body">
                                                        <?= truncate($val->post_excerpt, 90) ?>
                                                    </p>
                                                    <p class="text-muted">
                                                        <i class="mdi mdi-clock-outline"></i> <?= time_ago($val->updated_at ?? $val->created_at) ?>
                                                    </p>
                                                </a>
                                            </div>
                                            <div class="row g-0 align-items-center">
                                                <div class="col-1">
                                                    <a href="<?= base_url('user/' . $val->username) ?>" class="text-sm text-outline-body d-block --xhr">
                                                        <img src="<?= get_image('users', $val->photo, 'icon') ?>" class="img-fluid rounded-circle" alt="<?= $val->first_name . ' ' . $val->last_name ?>" />
                                                    </a>
                                                </div>
                                                <div class="col-8">
                                                    <a href="<?= base_url('user/' . $val->username) ?>" class="ps-2 text-outline-body --xhr">
                                                        <b>
                                                            <?= $val->first_name . ' ' . $val->last_name ?>
                                                        </b>
                                                    </a>
                                                </div>
                                                <div class="col-3 text-end">
                                                    <button type="button" class="btn btn-sm rounded-pill --modify <?= is_liked($val->post_id, 'blogs/' . $val->category_slug . '/' . $val->post_slug)
                                                      ? 'btn-danger'
                                                      : 'btn-outline-danger' ?>" data-href="<?= base_url('xhr/widget/comment/repute', [
  'post_id' => $val->post_id,
  'path' => 'blogs/' . $val->category_slug . '/' . $val->post_slug,
]) ?>" data-class-add="btn-danger" data-class-remove="btn-outline-danger" aria-label="<?= phrase('Like') ?>" data-bs-toggle="tooltip" title="<?= phrase('Like') ?>">
                                                        <i class="mdi mdi-heart"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="prev" aria-label="<?= phrase('Previous') ?>">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="visually-hidden"><?= phrase('Previous') ?></span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="next" aria-label="<?= phrase('Next') ?>">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="visually-hidden"><?= phrase('Next') ?></span>
                            </button>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
<?php endif; ?>

<section class="section-padding">
    <div class="container">
        <?php if ($articles): ?>
            <?php foreach ($articles as $key => $val): ?>
                <div class="mb-5">
                    <h2 class="h3 text-center text-sm-start fade-in">
                        <a href="<?= base_url(['blogs', $val->category_slug]) ?>" class="--xhr">
                            <?= $val->category_title ?> <i class="mdi mdi-arrow-right"></i>
                        </a>
                    </h2>
                    <p class="text-muted fs-5 text-center text-sm-start fade-in">
                        <?= $val->category_description ?>
                    </p>
                    <div class="swiper fade-in" data-slide-count-sm="2" data-slide-count-md="2" data-slide-count-lg="3" data-slide-count-xl="4" data-autoplay="1">
                        <div class="swiper-wrapper">
                            <?php foreach ($val->posts as $_key => $_val): ?>
                                <div class="swiper-slide h-auto">
                                    <div class="h-100 d-flex flex-column">
                                        <div class="d-flex flex-column flex-grow-1 border border-hover p-3 pb-0 rounded-5">
                                            <div class="d-flex g-0 align-items-center mb-3">
                                                <div class="pe-2">
                                                    <a href="<?= base_url('user/' . $_val->username) ?>" class="text-sm text-secondary d-block --xhr">
                                                        <img src="<?= get_image('users', $_val->photo, 'icon') ?>" class="img-fluid rounded-circle" alt="<?= $_val->first_name . ' ' . $_val->last_name ?>" width="48" loading="lazy" decoding="async" />
                                                    </a>
                                                </div>
                                                <div class="flex-grow-1 d-flex flex-column justify-content-center overflow-hidden gap-0">
                                                    <div class="lh-1">
                                                        <a href="<?= base_url('user/' . $_val->username) ?>" class="text-body text-decoration-none --xhr">
                                                            <b><?= $_val->first_name . ' ' . $_val->last_name ?></b>
                                                        </a>
                                                    </div>
                                                    <div class="lh-1">
                                                        <span class="text-muted text-sm"><i class="mdi mdi-clock-outline"></i> <?= time_ago($_val->updated_at ?? $_val->created_at) ?></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="d-flex flex-grow-1 flex-column justify-content-between gap-3">
                                                <h3 class="h5 fw-bold mb-2" style="letter-spacing: -0.01em;">
                                                    <a href="<?= base_url(['blogs', $val->category_slug, $_val->post_slug]) ?>" class="text-body text-decoration-none --xhr">
                                                        <?= truncate($_val->post_title, 64) ?>
                                                    </a>
                                                </h3>
                                                <div style="margin-inline:-1rem">
                                                    <a href="<?= base_url(['blogs', $val->category_slug, $_val->post_slug]) ?>" class="d-block --xhr">
                                                        <img src="<?= get_image(
                                                            'blogs',
                                                            $_val->featured_image,
                                                            'thumb',
                                                        ) ?>" class="img-fluid rounded-5 w-100 bg-body-tertiary" alt="<?= $_val->post_title ?>" loading="lazy" decoding="async" style="aspect-ratio: 3/2; object-fit: cover">
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="row">
                <div class="col-lg-8 offset-lg-2">
                    <?= view('templates/404', [...(array) $meta, 'searchAction' => go_to('../search', ['page' => null]), 'searchLabel' => phrase('Search posts...')]) ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>
