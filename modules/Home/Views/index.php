<?php
/**
 * @var array<int, object> $categories
 * @var array<int, object> $articles
 * @var array<int, object> $galleries
 * @var array<int, object> $peoples
 * @var array<int, object> $testimonials
 * @var object $statistics
 */
?>

<!-- Hero Section -->
<style>
    .home-hero {
        position: relative;
        overflow: hidden;
        padding: 8rem 0;
        background:
            radial-gradient(circle at 15% 20%, rgba(var(--bs-primary-rgb), .18), transparent 28rem),
            radial-gradient(circle at 88% 12%, rgba(25, 135, 84, .16), transparent 24rem),
            linear-gradient(135deg, var(--bs-body-bg) 0%, var(--bs-tertiary-bg) 52%, var(--bs-body-bg) 100%);
    }

    [data-bs-theme="dark"] .home-hero {
        background:
            radial-gradient(circle at 15% 20%, rgba(var(--bs-primary-rgb), .25), transparent 28rem),
            radial-gradient(circle at 88% 12%, rgba(25, 135, 84, .2), transparent 24rem),
            linear-gradient(135deg, var(--bs-body-bg) 0%, var(--bs-tertiary-bg) 52%, var(--bs-body-bg) 100%);
    }

    .home-hero:before,
    .home-hero:after {
        content: "";
        position: absolute;
        pointer-events: none;
        opacity: .55;
    }

    .home-hero:before {
        inset: 0;
        background-image:
            linear-gradient(rgba(15, 23, 42, .055) 1px, transparent 1px),
            linear-gradient(90deg, rgba(15, 23, 42, .055) 1px, transparent 1px);
        background-size: 56px 56px;
        mask-image: linear-gradient(to bottom, #000 0%, transparent 78%);
    }

    [data-bs-theme="dark"] .home-hero:before {
        background-image:
            linear-gradient(rgba(255, 255, 255, .05) 1px, transparent 1px),
            linear-gradient(90deg, rgba(255, 255, 255, .05) 1px, transparent 1px);
    }

    .home-hero:after {
        right: -8rem;
        bottom: -13rem;
        width: 28rem;
        height: 28rem;
        border: 1px solid rgba(var(--bs-primary-rgb), .18);
        border-radius: 50%;
        box-shadow: inset 0 0 0 4rem rgba(255, 255, 255, .35);
    }

    [data-bs-theme="dark"] .home-hero:after {
        box-shadow: inset 0 0 0 4rem rgba(255, 255, 255, .05);
    }

    .home-hero .container {
        position: relative;
        z-index: 1;
    }

    .hero-kicker {
        display: inline-flex;
        align-items: center;
        gap: .5rem;
        padding: .45rem .9rem;
        border: 1px solid rgba(var(--bs-primary-rgb), .16);
        border-radius: 999px;
        background: var(--bs-tertiary-bg, rgba(255, 255, 255, .74));
        color: var(--bs-primary);
        font-size: .875rem;
        font-weight: 700;
        box-shadow: 0 1rem 3rem rgba(0, 0, 0, .06);
    }

    .hero-title {
        max-width: 920px;
        margin-inline: auto;
        color: var(--bs-body-color, #111827);
        font-size: clamp(2.5rem, 7vw, 5rem);
        line-height: .95;
        letter-spacing: 0;
    }

    .hero-title span {
        color: var(--bs-primary);
    }

    .hero-copy {
        max-width: 720px;
        margin-inline: auto;
        color: var(--bs-secondary-color, #64748b);
        font-size: clamp(1.05rem, 2vw, 1.35rem);
    }

    .hero-action {
        box-shadow: 0 1rem 2.5rem rgba(var(--bs-primary-rgb), .22);
    }

    .hero-pill-row {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: .75rem;
        margin-top: 2.5rem;
    }

    .hero-pill {
        display: inline-flex;
        align-items: center;
        gap: .5rem;
        padding: .75rem 1rem;
        border: 1px solid var(--bs-border-color, rgba(15, 23, 42, .08));
        border-radius: 999px;
        background: var(--bs-surface-bg, rgba(255, 255, 255, .72));
        color: var(--bs-body-color, #334155);
        font-weight: 600;
        backdrop-filter: blur(10px);
    }

    @media (max-width: 575.98px) {
        .home-hero {
            padding: 5rem 0 3.5rem;
        }

        .hero-pill {
            width: 100%;
            justify-content: center;
        }
    }
</style>
<section class="hero-section home-hero text-center">
    <div class="container">
        <div class="fade-in">
            <div class="hero-kicker mb-4">
                <i class="mdi mdi-sparkles"></i>
                <?= phrase('Welcome') ?>
            </div>
            <h1 class="hero-title fw-bold mb-4">
                <?= get_setting('app_name') ?><span>.</span>
            </h1>
            <p class="hero-copy mb-4">
                <?= get_setting('app_description') ?>
            </p>
            <div class="d-flex flex-column flex-sm-row justify-content-center gap-3">
                <a href="<?= base_url('auth') ?>" class="btn btn-primary btn-lg rounded-pill px-4 hero-action --modal">
                    <?= phrase('Get Started') ?> <i class="mdi mdi-arrow-right"></i>
                </a>
                <a href="<?= base_url('blogs') ?>" class="btn btn-primary btn-lg border rounded-pill px-4 --xhr">
                    <i class="mdi mdi-magnify"></i> <?= phrase('Explore Articles') ?>
                </a>
            </div>
        </div>
    </div>
</section>

<?php if ($categories): ?>
    <section class="section-padding fade-in">
        <div class="container">
            <div class="text-center">
                <h2 class="fw-bold m-0 display-6"><?= phrase('Post Categories') ?></h2>
                <p class="text-muted mb-5 fs-5"><?= phrase('Browse the articles by category') ?></p>
            </div>
            <div class="row align-items-center">
                <?php foreach ($categories as $key => $val): ?>
                    <div class="col-6 col-md-4 col-lg-3">
                        <div class="card rounded-5 border-hover overflow-hidden mb-3 <?= $key < 4 ? (($key + 1) % 2 == 0 ? 'bg-body-tertiary' : 'bg-body-secondary') : (($key + 1) % 2 == 0 ? 'bg-body-secondary' : 'bg-body-tertiary') ?>">
                            <div class="card-body clip gradient-top position-relative py-lg-5 text-center">
                                <a href="<?= base_url('blogs/' . $val->category_slug) ?>" class="d-block --xhr">
                                    <img src="<?= get_image(
                                      'blogs',
                                      $val->category_image,
                                      'thumb',
                                    ) ?>" class="img-fluid rounded-circle border border-3 border-light mb-3" width="128" alt="<?= $val->category_title ?>" loading="lazy" decoding="async" />
                                </a>
                                <br />
                                <h3 class="h4 mb-3 text-truncate">
                                    <a href="<?= base_url('blogs/' . $val->category_slug) ?>" class="text-body text-decoration-none --xhr">
                                        <?= $val->category_title ?>
                                    </a>
                                </h3>
                                <p class="fs-5 mb-0 mb-md-3 text-body">
                                    <?= number_format($val->post_total) . ' ' . phrase('Articles') ?>
                                </p>
                                <div class="d-none d-md-block">
                                    <a href="<?= base_url('blogs/' . $val->category_slug) ?>" class="btn btn-outline-light btn-sm rounded-pill px-3 --xhr">
                                        <?= phrase('Browse Articles') ?> <i class="mdi mdi-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
<?php endif; ?>

<!-- Latest News -->
<?php if ($articles): ?>
    <section class="section-padding fade-in">
        <div class="container">
            <div class="text-center">
                <h2 class="fw-bold m-0 display-6"><?= phrase('Recent Articles') ?></h2>
                <p class="text-muted fs-5"><?= phrase('Read our newest articles') ?></p>
            </div>
            <div class="swiper mb-4" data-slide-count-sm="2" data-slide-count-md="2" data-slide-count-lg="3" data-slide-count-xl="4" data-autoplay="1">
                <div class="swiper-wrapper py-3">
                    <?php foreach ($articles as $key => $val): ?>
                        <div class="swiper-slide h-auto">
                            <div class="h-100 d-flex flex-column">
                                <div class="d-flex flex-column flex-grow-1 border border-hover p-3 pb-0 rounded-5">
                                    <div class="d-flex g-0 align-items-center mb-3">
                                        <div class="pe-2">
                                            <a href="<?= base_url('user/' . $val->username) ?>" class="text-sm text-secondary d-block --xhr">
                                                <img src="<?= get_image('users', $val->photo, 'icon') ?>" class="img-fluid rounded-circle" alt="<?= $val->first_name . ' ' . $val->last_name ?>" width="48" loading="lazy" decoding="async" />
                                            </a>
                                        </div>
                                        <div class="flex-grow-1 d-flex flex-column justify-content-center overflow-hidden gap-0">
                                            <div class="lh-1">
                                                <a href="<?= base_url('user/' . $val->username) ?>" class="text-body text-decoration-none --xhr">
                                                    <b><?= $val->first_name . ' ' . $val->last_name ?></b>
                                                </a>
                                            </div>
                                            <div class="lh-1">
                                                <span class="text-muted text-sm"><i class="mdi mdi-clock-outline"></i> <?= time_ago($val->updated_at ?? $val->created_at) ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex flex-grow-1 flex-column justify-content-between gap-3">
                                        <h3 class="h5 fw-bold mb-2" style="letter-spacing: -0.01em;">
                                            <a href="<?= base_url(['blogs', $val->category_slug, $val->post_slug]) ?>" class="text-body text-decoration-none --xhr">
                                                <?= truncate($val->post_title, 64) ?>
                                            </a>
                                        </h3>
                                        <div style="margin-inline:-1rem">
                                            <a href="<?= base_url(['blogs', $val->category_slug, $val->post_slug]) ?>" class="d-block --xhr">
                                                <img src="<?= get_image(
                                                  'blogs',
                                                  $val->featured_image,
                                                  'thumb',
                                                ) ?>" class="img-fluid rounded-5 w-100 bg-body-tertiary" alt="<?= $val->post_title ?>" loading="lazy" decoding="async" style="aspect-ratio: 3/2; object-fit: cover">
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="text-center">
                <a href="<?= base_url('blogs') ?>" class="text-decoration-none fw-semibold --xhr"><?= phrase('See all news') ?> <i class="mdi mdi-arrow-right"></i></a>
            </div>
        </div>
    </section>
<?php endif; ?>

<!-- Latest Galleries -->
<?php if ($galleries): ?>
    <section class="section-padding fade-in">
        <div class="container">
            <div class="text-center">
                <h2 class="fw-bold m-0 display-6"><?= phrase('Featured Galleries') ?></h2>
                <p class="text-muted fs-5"><?= phrase('See our latest gallery activities') ?></p>
            </div>
            <div class="swiper mb-4" data-slide-count-sm="2" data-slide-count-md="2" data-slide-count-lg="3" data-slide-count-xl="3">
                <div class="swiper-wrapper py-3">
                    <?php foreach ($galleries as $key => $val): ?>
                        <?php
                        $images = json_decode($val->gallery_images);

                        if (!$images) {
                          continue;
                        }
                        ?>
                        <?php foreach ($images as $src => $alt): ?>
                            <div class="swiper-slide">
                                <div class="card border-hover rounded-5 overflow-hidden">
                                    <a href="<?= base_url(['galleries', $val->gallery_slug]) ?>" class="--xhr d-block">
                                        <div class="card-body" style="background:url(<?= get_image('galleries', $src, 'thumb') ?>) center center no-repeat; background-size: cover; height: 256px">
                                            <div class="clip gradient-top"></div>
                                            <div class="position-absolute bottom-0 p-3">
                                                <h3 class="h4 text-outline-body">
                                                    <?= $val->gallery_title ?>
                                                </h3>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="text-center">
                <a href="<?= base_url('galleries') ?>" class="text-decoration-none fw-semibold --xhr"><?= phrase('See all galleries') ?> <i class="mdi mdi-arrow-right"></i></a>
            </div>
        </div>
    </section>
<?php endif; ?>

<?php if ($peoples): ?>
    <section class="section-padding fade-in">
        <div class="container">
            <div class="text-center">
                <h2 class="fw-bold m-0 display-6"><?= phrase('Meet Us') ?></h2>
                <p class="text-muted fs-5"><?= phrase('Talk to us, straight to our team') ?></p>
            </div>
            <div class="row align-items-center">
                <?php foreach ($peoples as $key => $val): ?>
                    <div class="col-6 col-md-4 col-lg-3">
                        <div class="card rounded-5 mb-3 bg-body border-hover">
                            <div class="card-body text-center py-lg-5">
                                <p>
                                    <a href="<?= base_url('peoples/' . $val->people_slug) ?>" class="d-block --xhr">
                                        <img src="<?= get_image('peoples', $val->photo, 'thumb') ?>" class="img-fluid rounded-circle px-lg-4 mb-3" alt="<?= $val->first_name . ' ' . $val->last_name ?>" loading="lazy" decoding="async" />
                                    </a>
                                </p>
                                <h3 class="h4 text-truncate">
                                    <a href="<?= base_url('peoples/' . $val->people_slug) ?>" class="--xhr">
                                        <?= $val->first_name . ' ' . $val->last_name ?>
                                    </a>
                                </h3>
                                <p class="fs-5 mb-0 mb-md-3">
                                    <?= $val->position ?>
                                </p>
                                <div class="d-none d-md-block">
                                    <a href="<?= base_url('peoples/' . $val->people_slug) ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3 --xhr">
                                        <?= phrase('Get Closer') ?> <i class="mdi mdi-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
<?php endif; ?>

<!-- Testimonials Swiper -->
<?php if ($testimonials): ?>
    <section class="section-padding fade-in">
        <div class="container">
            <div class="text-center mb-4">
                <h2 class="fw-bold m-0 display-6"><?= phrase('What have people said about us?') ?></h2>
            </div>
            <div class="swiper mb-4" data-slide-count-sm="1" data-slide-count-md="2" data-slide-count-lg="2" data-slide-count-xl="2" data-autoplay="1" data-auto-height="1" data-loop="1">
                <div class="swiper-wrapper py-3 align-items-center">
                    <?php foreach ($testimonials as $key => $val): ?>
                        <div class="swiper-slide h-auto">
                            <div class="border border-hover rounded-5 p-3">
                                <div class="d-flex align-items-end">
                                    <div>
                                        <img src="<?= get_image('testimonials', $val->photo, 'icon') ?>" class="rounded-circle me-3 border p-1" width="80" alt="<?= $val->first_name .
  ' ' .
  $val->last_name ?>" style="object-fit: cover;" loading="lazy" decoding="async" />
                                    </div>
                                    <div>
                                        <?php if (isset($val->rating) && $val->rating > 0): ?>
                                            <div class="mb-3">
                                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                                    <i class="mdi mdi-star<?= $i <= $val->rating ? '' : '-outline' ?> text-warning fs-4"></i>
                                                <?php endfor; ?>
                                            </div>
                                        <?php endif; ?>
                                        <div class="fs-5 text-body">
                                            <?= $val->testimonial_content ?>
                                        </div>
                                        <div class="py-3">
                                            <?php if ($val->username): ?>
                                            <a href="<?= base_url('user/' . $val->username) ?>" class="--xhr">
                                                <b class="d-block fs-5 text-primary"><?= $val->first_name . ' ' . $val->last_name ?></b>
                                            </a>
                                            <?php else: ?>
                                                <b class="d-block fs-5 text-danger"><?= $val->first_name . ' ' . $val->last_name ?></b>
                                            <?php endif; ?>
                                            <small class="text-muted"><?= format_date($val->created_at, 'long', true) ?></small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>
<?php endif; ?>

<section class="section-padding pt-0 fade-in">
    <div class="container py-lg-5">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="text-center text-md-start">
                    <h2 class="fw-bold m-0 display-6"><?= phrase('Today Statistics') ?></h2>
                    <p class="text-muted fs-5"><?= phrase('and will continue to grow') ?></p>
                </div>
                <div class="row">
                    <div class="col-6 col-sm-6 text-center text-md-start mb-4">
                        <div class="counter color display-5 fw-semibold">
                            <span data-from="1" data-to="<?= $statistics->daily ?>" data-refresh-interval="100" data-speed="1500"> <?= number_format($statistics->daily) ?> </span>
                        </div>
                        <h3 class="h4 mt-0 text-secondary fw-light">
                            <?= phrase('Daily Visits') ?>
                        </h3>
                    </div>
                    <div class="col-6 col-sm-6 text-center text-md-start mb-4">
                        <div class="counter color fw-semibold display-5">
                            <span data-from="10" data-to="<?= $statistics->weekly ?>" data-refresh-interval="2" data-speed="600"> <?= number_format($statistics->weekly) ?> </span>
                        </div>
                        <h3 class="h4 mt-0 text-secondary fw-light">
                            <?= phrase('Weekly Visits') ?>
                        </h3>
                    </div>
                    <div class="col-6 col-sm-6 text-center text-md-start mb-4">
                        <div class="counter color fw-semibold display-5">
                            <span data-from="10" data-to="<?= $statistics->monthly ?>" data-refresh-interval="11" data-speed="900"> <?= number_format($statistics->monthly) ?> </span>
                        </div>
                        <h3 class="h4 mt-0 text-secondary fw-light">
                            <?= phrase('Monthly Visits') ?>
                        </h3>
                    </div>
                    <div class="col-6 col-sm-6 text-center text-md-start mb-4">
                        <div class="counter color fw-semibold display-5">
                            <span data-from="100" data-to="<?= $statistics->yearly ?>" data-refresh-interval="3" data-speed="1000"> <?= number_format($statistics->yearly) ?> </span>
                        </div>
                        <h3 class="h4 mt-0 text-secondary fw-light">
                            <?= phrase('Yearly Visits') ?>
                        </h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="row">
                    <div class="col-6 col-md-12">
                        <div class="card border-light-subtle border-hover rounded-5 mb-md-4 overflow-hidden">
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-6 offset-3 text-center">
                                        <i class="mdi mdi-post-outline mdi-5x text-secondary"></i>
                                    </div>
                                </div>
                                <div class="text-center">
                                    <h4 class="card-title fw-semibold color">
                                        <?= number_format($statistics->posts) . ' ' . phrase('Posts') ?>
                                    </h4>
                                    <p class="card-text d-none d-sm-block text-muted">
                                        <?= phrase('Collected from all categories under blog') ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-12">
                        <div class="card border-light-subtle border-hover rounded-5 overflow-hidden">
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-6 offset-3 text-center">
                                        <i class="mdi mdi-image-multiple mdi-5x text-secondary"></i>
                                    </div>
                                </div>
                                <div class="text-center">
                                    <h4 class="card-title fw-semibold color">
                                        <?= number_format($statistics->galleries) . ' ' . phrase('Albums') ?>
                                    </h4>
                                    <p class="card-text d-none d-sm-block text-muted">
                                        <?= phrase('Collected from all galleries') ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mt-3 mt-sm-0">
                <div class="card border-light-subtle border-hover rounded-5 overflow-hidden">
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-6 offset-3 text-center">
                                <i class="mdi mdi-comment-processing mdi-5x text-secondary"></i>
                            </div>
                        </div>
                        <div class="text-center">
                            <h4 class="card-title fw-semibold color">
                                <?= number_format($statistics->comments) . ' ' . phrase('Interactions') ?>
                            </h4>
                            <p class="card-text text-muted">
                                <?= phrase('Collected from submitted comments') ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
