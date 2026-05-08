<?php
/**
 * @var mixed $categories
 * @var mixed $articles
 * @var mixed $galleries
 * @var mixed $peoples
 * @var mixed $statistics
 */
?>
<!-- Hero Section -->
<section class="hero-section pb-0 text-center">
    <div class="container">
        <div class="section-padding fade-in">
            <h1 class="display-4 fw-bold mb-3"><?= get_setting('app_name'); ?></h1>
            <p class="lead text-muted mb-4 mx-auto" style="max-width: 600px;">
                <?= get_setting('app_description'); ?>
            </p>
            <div class="d-flex justify-content-center gap-3 mb-5">
                <a href="<?= base_url('auth'); ?>" class="btn btn-primary rounded-pill px-4 --modal">GET STARTED <i class="mdi mdi-arrow-right"></i></a>
            </div>
            
            <div class="row mt-5">
                <div class="col-12 col-lg-10 offset-lg-1">
                    <img src="<?= get_module_asset('images/hero.png'); ?>" class="img-fluid rounded-5" alt="Map Interface">
                </div>
            </div>
        </div>
    </div>
</section>
<?php if ($categories): ?>
<section class="section-padding fade-in">
    <div class="container">
        <div class="text-center">
            <h3 class="fw-bold m-0 display-6"><?= phrase('Post Categories'); ?></h3>
            <p class="text-muted mb-5 fs-5"><?= phrase('Browse the articles by category'); ?></p>
        </div>
        <div class="row align-items-center">
            <?php foreach ($categories as $key => $val): ?>
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="card rounded-4 border-0 overflow-hidden mb-3 <?= ($key < 4 ? (($key + 1) % 2 == 0 ? 'bg-secondary' : 'bg-dark') : (($key + 1) % 2 == 0 ? 'bg-dark' : 'bg-secondary')); ?>" style="background:url(<?= get_image('blogs', $val->category_image, 'thumb'); ?>) center center no-repeat;background-size:cover">
                        <div class="card-body clip gradient-top position-relative py-lg-5 text-center text-light">
                            <a href="<?= base_url('blogs/' . $val->category_slug); ?>" class="--xhr">
                                <img src="<?= get_image('blogs', $val->category_image, 'icon'); ?>" class="img-fluid rounded-circle mb-3" />
                            </a>
                            <br />
                            <h4 class="mb-3 text-truncate">
                                <a href="<?= base_url('blogs/' . $val->category_slug); ?>" class="text-light --xhr">
                                    <?= $val->category_title; ?>
                                </a>
                            </h4>
                            <p class="lead mb-0 mb-md-3">
                                <?= number_format($val->post_total) . ' ' . phrase('Articles'); ?>
                            </p>
                            <div class="d-none d-md-block">
                                <a href="<?= base_url('blogs/' . $val->category_slug); ?>" class="btn btn-light btn-sm rounded-pill pr-3 pl-3 --xhr">
                                    <?= phrase('Browse Articles'); ?> <i class="mdi mdi-arrow-right"></i>
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
            <h3 class="fw-bold m-0 display-6"><?= phrase('Recent Articles'); ?></h3>
            <p class="text-muted fs-5"><?= phrase('Read our newest articles'); ?></p>
        </div>
        <div class="swiper" data-slide-count-sm="2" data-slide-count-md="2" data-slide-count-lg="3" data-slide-count-xl="4" data-autoplay="1">
            <div class="swiper-wrapper py-3">
                <?php foreach ($articles as $key => $val): ?>
                    <div class="swiper-slide h-auto">
                        <div class="h-100 d-flex flex-column">
                            <div class="d-flex flex-column flex-grow-1 border p-3 rounded-4">
                                <div class="row g-0 align-items-center mb-3">
                                    <div class="col-1">
                                        <a href="<?= base_url('user/' . $val->username); ?>" class="text-sm text-secondary --xhr">
                                            <img src="<?= get_image('users', $val->photo, 'icon'); ?>" class="img-fluid rounded-circle" alt="..." />
                                        </a>
                                    </div>
                                    <div class="col-11 overflow-hidden">
                                        <span class="text-muted text-sm float-end">
                                            <i class="mdi mdi-clock-outline"></i> <?= time_ago($val->updated_timestamp); ?>
                                        </span>
                                        <a href="<?= base_url('user/' . $val->username); ?>" class="text-dark ps-2 text-decoration-none --xhr">
                                            <b>
                                                <?= $val->first_name . ' ' . $val->last_name; ?>
                                            </b>
                                        </a>
                                    </div>
                                </div>
                                <h5 class="fw-bold mb-2" style="letter-spacing: -0.01em;">
                                    <a href="<?= base_url(['blogs', $val->category_slug, $val->post_slug]); ?>" class="text-dark text-decoration-none --xhr">
                                        <?= truncate($val->post_title, 64); ?>
                                    </a>
                                </h5>
                                <p class="text-muted small">
                                    <?= truncate($val->post_excerpt, 80); ?>
                                </p>
                            </div>
                            <a href="<?= base_url(['blogs', $val->category_slug, $val->post_slug]); ?>" class="--xhr">
                                <img src="<?= get_image('blogs', $val->featured_image, 'thumb'); ?>" class="img-fluid rounded-4 w-100 bg-white" alt="<?= $val->post_title; ?>" style="aspect-ratio: 3/2; object-fit: cover;margin-top:-1.5rem">
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="text-center">
            <a href="<?= base_url('blogs'); ?>" class="text-decoration-none fw-semibold --xhr"><?= phrase('See all news'); ?> <i class="mdi mdi-arrow-right"></i></a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Latest Galleries -->
<?php if ($galleries): ?>
<section class="section-padding fade-in">
    <div class="container">
        <div class="text-center">
            <h3 class="fw-bold m-0 display-6"><?= phrase('Featured Galleries'); ?></h3>
            <p class="text-muted fs-5"><?= phrase('See our latest gallery activities'); ?></p>
        </div>
        <div class="swiper" data-slide-count-sm="2" data-slide-count-md="2" data-slide-count-lg="3" data-slide-count-xl="3">
            <div class="swiper-wrapper py-3">
                <?php foreach ($galleries as $key => $val): ?>
                    <?php
                        $images = json_decode($val->gallery_images);

                        if (! $images) {
                            continue;
                        }
                    ?>
                    <?php foreach ($images as $src => $alt): ?>
                        <div class="swiper-slide">
                            <div class="card border-0 rounded-4 overflow-hidden mb-5">
                                <a href="<?= base_url(['galleries', $val->gallery_slug]); ?>" class="--xhr d-block">
                                    <div class="card-body" style="background:url(<?= get_image('galleries', $src, 'thumb'); ?>) center center no-repeat; background-size: cover; height: 256px">
                                        <div class="clip gradient-top"></div>
                                        <div class="position-absolute bottom-0 p-3">
                                            <h4 class="text-light">
                                                <?= $val->gallery_title; ?>
                                            </h4>
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
            <a href="<?= base_url('galleries'); ?>" class="text-decoration-none fw-semibold --xhr"><?= phrase('See all galleries'); ?> <i class="mdi mdi-arrow-right"></i></a>
        </div>
    </div>
</section>
<?php endif; ?>

<?php if ($peoples): ?>
<section class="section-padding fade-in">
    <div class="container">
        <div class="text-center">
            <h3 class="fw-bold m-0 display-6"><?= phrase('Meet Us'); ?></h3>
            <p class="text-muted fs-5"><?= phrase('Talk to us, straight to our team'); ?></p>
        </div>
        <div class="row align-items-center">
            <?php foreach ($peoples as $key => $val): ?>
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="card rounded-4 mb-3 border-0">
                        <div class="card-body text-center py-lg-5">
                            <p>
                                <a href="<?= base_url('peoples/' . $val->people_slug); ?>" class="--xhr">
                                    <img src="<?= get_image('peoples', $val->photo, 'thumb'); ?>" class="img-fluid rounded-circle px-lg-4 mb-3" />
                                </a>
                            </p>
                            <h4 class="text-truncate">
                                <a href="<?= base_url('peoples/' . $val->people_slug); ?>" class="--xhr">
                                    <?= $val->first_name . ' ' . $val->last_name; ?>
                                </a>
                            </h4>
                            <p class="lead mb-0 mb-md-3">
                                <?= $val->position; ?>
                            </p>
                            <div class="d-none d-md-block">
                                <a href="<?= base_url('peoples/' . $val->people_slug); ?>" class="btn btn-light btn-sm rounded-pill pr-3 pl-3 --xhr">
                                    <?= phrase('Get Closer'); ?> <i class="mdi mdi-arrow-right"></i>
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

<section class="section-padding fade-in">
    <div class="container py-lg-5">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="text-center text-md-start">
                    <h3 class="fw-bold m-0 display-6"><?= phrase('Today Statistics'); ?></h3>
                    <p class="text-muted fs-5"><?= phrase('and will continue to grow'); ?></p>
                </div>
                <div class="row">
                    <div class="col-6 col-sm-6 text-center text-md-start mb-4">
                        <div class="counter color fw-semibold display-5">
                            <span data-from="1" data-to="<?= $statistics->daily; ?>" data-refresh-interval="100" data-speed="1500"> <?= number_format($statistics->daily); ?> </span>
                        </div>
                        <h4 class="mt-0 text-secondary">
                            <?= phrase('Daily Visits'); ?>
                        </h4>
                    </div>
                    <div class="col-6 col-sm-6 text-center text-md-start mb-4">
                        <div class="counter color fw-semibold display-5">
                            <span data-from="10" data-to="<?= $statistics->weekly; ?>" data-refresh-interval="2" data-speed="600"> <?= number_format($statistics->weekly); ?> </span>
                        </div>
                        <h4 class="mt-0 text-secondary">
                            <?= phrase('Weekly Visits'); ?>
                        </h4>
                    </div>
                    <div class="col-6 col-sm-6 text-center text-md-start mb-4">
                        <div class="counter color fw-semibold display-5">
                            <span data-from="10" data-to="<?= $statistics->monthly; ?>" data-refresh-interval="11" data-speed="900"> <?= number_format($statistics->monthly); ?> </span>
                        </div>
                        <h4 class="mt-0 text-secondary">
                            <?= phrase('Monthly Visits'); ?>
                        </h4>
                    </div>
                    <div class="col-6 col-sm-6 text-center text-md-start mb-4">
                        <div class="counter color fw-semibold display-5">
                            <span data-from="100" data-to="<?= $statistics->yearly; ?>" data-refresh-interval="3" data-speed="1000"> <?= number_format($statistics->yearly); ?> </span>
                        </div>
                        <h4 class="mt-0 text-secondary">
                            <?= phrase('Yearly Visits'); ?>
                        </h4>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="row">
                    <div class="col-6 col-md-12">
                        <div class="card border-light-subtle rounded-4 mb-md-4 overflow-hidden">
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-6 offset-3 text-center">
                                        <i class="mdi mdi-post-outline mdi-5x text-secondary"></i>
                                    </div>
                                </div>
                                <div class="text-center">
                                    <h4 class="card-title fw-semibold color">
                                        <?= number_format($statistics->posts) . ' ' . phrase('Posts'); ?>
                                    </h4>
                                    <p class="card-text d-none d-sm-block text-muted">
                                        <?= phrase('Collected from all categories under blog'); ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-12">
                        <div class="card border-light-subtle rounded-4 overflow-hidden">
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-6 offset-3 text-center">
                                        <i class="mdi mdi-image-multiple mdi-5x text-secondary"></i>
                                    </div>
                                </div>
                                <div class="text-center">
                                    <h4 class="card-title fw-semibold color">
                                        <?= number_format($statistics->galleries) . ' ' . phrase('Albums'); ?>
                                    </h4>
                                    <p class="card-text d-none d-sm-block text-muted">
                                        <?= phrase('Collected from all galleries'); ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mt-3 mt-sm-0">
                <div class="card border-light-subtle rounded-4 overflow-hidden">
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-6 offset-3 text-center">
                                <i class="mdi mdi-comment-processing mdi-5x text-secondary"></i>
                            </div>
                        </div>
                        <div class="text-center">
                            <h4 class="card-title fw-semibold color">
                                <?= number_format($statistics->comments) . ' ' . phrase('Interactions'); ?>
                            </h4>
                            <p class="card-text text-muted">
                                <?= phrase('Collected from submitted comments'); ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
