<?php
/**
 * @var mixed $articles
 * @var mixed $statistics
 * @var mixed $galleries
 */
?>

<!-- Hero Section -->
<section class="hero-section text-center pt-5 mt-5">
    <!-- Background Wavy Shape -->
    <svg class="position-absolute top-0 d-none d-md-block hero-blob text-light" viewBox="0 0 948 458" fill="none">
        <path fill="currentColor" d="M179.493 278.507C88.0136 187.027 42.2737 141.287 21.1376 90.2621C-7.04587 22.2238 -7.04587 -54.2238 21.1376 -122.262C42.2737 -173.287 88.0136 -219.027 179.493 -310.507C270.973 -401.986 316.713 -447.726 367.738 -468.862C435.776 -497.046 512.224 -497.046 580.262 -468.862C631.287 -447.726 677.027 -401.986 768.507 -310.507C859.986 -219.027 905.726 -173.287 926.862 -122.262C955.046 -54.2238 955.046 22.2238 926.862 90.2621C905.726 141.287 859.986 187.027 768.507 278.507C677.027 369.986 631.287 415.726 580.262 436.862C512.224 465.046 435.776 465.046 367.738 436.862C316.713 415.726 270.973 369.986 179.493 278.507Z"/>
    </svg>
    <div class="container">
        <div class="section-padding fade-in">
            <h1 class="display-4 fw-bold mb-3"><?= get_setting('app_name'); ?></h1>
            <p class="lead text-muted mb-4 mx-auto" style="max-width: 600px;">
                <?= get_setting('app_description'); ?>
            </p>
            <div class="d-flex justify-content-center gap-3 mb-5">
                <a href="<?= base_url('auth'); ?>" class="btn btn-primary rounded-pill px-4 --modal">SET SAIL</a>
            </div>
            
            <div class="row mt-5">
                <div class="col-12 col-lg-10 offset-lg-1">
                    <img src="<?= get_module_asset('images/hero.png'); ?>" class="img-fluid rounded-5" alt="Treasure Map">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Trusted By Section -->
<section class="section-padding fade-in">
    <div class="container text-center">
        <p class="text-muted fw-semibold mb-4">Trusted by the Seven Seas</p>
        <div class="d-flex flex-wrap justify-content-center align-items-center gap-4 gap-md-5 opacity-75">
            <h4 class="fw-bold m-0 text-secondary">GeekTech</h4>
            <h4 class="fw-bold m-0 text-secondary">CV. HSD</h4>
            <h4 class="fw-bold m-0 text-secondary">Djayasoft</h4>
            <h4 class="fw-bold m-0 text-secondary">CrossLink</h4>
            <h4 class="fw-bold m-0 text-secondary">DigiDesa</h4>
        </div>
    </div>
</section>


<!-- Statistics Section -->
<section class="section-padding fade-in bg-light">
    <div class="container text-center">
        <h2 class="fw-bold mb-5 display-6">Crews & Parleys</h2>
        <div class="row justify-content-center">
            <div class="col-6 col-md-4 mb-5">
                <h2 class="display-4 fw-bold text-primary mb-2"><?= number_format($statistics->daily); ?></h2>
                <h5 class="fw-bold mb-2">Daily Loot</h5>
                <p class="text-muted small mb-0">Landlubbers spotted today.</p>
            </div>
            <div class="col-6 col-md-4 mb-5">
                <h2 class="display-4 fw-bold text-primary mb-2"><?= number_format($statistics->weekly); ?></h2>
                <h5 class="fw-bold mb-2">Weekly Booty</h5>
                <p class="text-muted small mb-0">Plunder this week.</p>
            </div>
            <div class="col-6 col-md-4 mb-5">
                <h2 class="display-4 fw-bold text-primary mb-2"><?= number_format($statistics->monthly); ?></h2>
                <h5 class="fw-bold mb-2">Monthly Treasures</h5>
                <p class="text-muted small mb-0">Treasure this month.</p>
            </div>
            <div class="col-6 col-md-4 mb-4 mb-md-0">
                <h2 class="display-4 fw-bold text-primary mb-2"><?= number_format($statistics->yearly); ?></h2>
                <h5 class="fw-bold mb-2">Yearly Gold</h5>
                <p class="text-muted small mb-0">Gold this year.</p>
            </div>
            <div class="col-6 col-md-4 mb-4 mb-md-0">
                <h2 class="display-4 fw-bold text-primary mb-2"><?= number_format($statistics->posts); ?></h2>
                <h5 class="fw-bold mb-2">Scrolls Penned</h5>
                <p class="text-muted small mb-0">Scrolls in the hold.</p>
            </div>
            <div class="col-6 col-md-4 mb-0">
                <h2 class="display-4 fw-bold text-primary mb-2"><?= number_format($statistics->comments); ?></h2>
                <h5 class="fw-bold mb-2">Rumors &amp; Murmurs</h5>
                <p class="text-muted small mb-0">User comments & feedback.</p>
            </div>
        </div>
    </div>
</section>

<!-- Latest Galleries -->
<?php if ($galleries): ?>
<section class="section-padding fade-in">
    <div class="container">
        <div class="text-center mb-5">
            <h3 class="fw-bold m-0 display-6">Treasured Maps</h3>
        </div>
        <div class="row">
            <?php foreach (array_slice($galleries, 0, 4) as $key => $val): ?>
                <?php
                    $images = json_decode($val->gallery_images);
                    if (! $images) continue;
                    $src = array_key_first((array)$images);
                ?>
                <div class="col-md-6 col-lg-3 mb-4">
                    <div class="h-100">
                        <a href="<?= base_url(['galleries', $val->gallery_slug]); ?>" class="--xhr">
                            <img src="<?= get_image('galleries', $src, 'thumb'); ?>" class="img-fluid rounded-5 w-100" alt="Treasure" style="aspect-ratio: 1/1; object-fit: cover;">
                        </a>
                        <div class="px-0 pt-3 text-center">
                            <h5 class="fw-bold mb-2" style="letter-spacing: -0.01em;">
                                <a href="<?= base_url(['galleries', $val->gallery_slug]); ?>" class="text-dark text-decoration-none --xhr">
                                    <?= truncate($val->gallery_title, 50); ?>
                                </a>
                            </h5>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="mt-5 text-center">
            <a href="<?= base_url('galleries'); ?>" class="text-decoration-none fw-semibold --xhr">See all maps <i class="mdi mdi-arrow-right"></i></a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Latest News -->
<?php if ($articles): ?>
<section class="section-padding fade-in">
    <div class="container">
        <div class="text-center mb-5">
            <h3 class="fw-bold m-0 display-6">Latest Tales</h3>
        </div>
        <div class="row">
            <?php foreach (array_slice($articles, 0, 4) as $key => $val): ?>
                <div class="col-md-6 col-lg-3 mb-4">
                    <div class="h-100">
                        <a href="<?= base_url(['blogs', $val->category_slug, $val->post_slug]); ?>" class="--xhr">
                            <img src="<?= get_image('blogs', $val->featured_image, 'thumb'); ?>" class="img-fluid rounded-5 w-100" alt="Scroll" style="aspect-ratio: 3/2; object-fit: cover;">
                        </a>
                        <div class="px-0 pt-3">
                            <p class="text-muted small fw-semibold mb-2"><?= time_ago($val->updated_timestamp); ?></p>
                            <h5 class="fw-bold mb-2" style="letter-spacing: -0.01em;">
                                <a href="<?= base_url(['blogs', $val->category_slug, $val->post_slug]); ?>" class="text-dark text-decoration-none --xhr">
                                    <?= truncate($val->post_title, 50); ?>
                                </a>
                            </h5>
                            <p class="text-muted small mb-0 lh-lg">
                                <?= truncate($val->post_excerpt, 80); ?>
                            </p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="mt-5 text-center">
            <a href="<?= base_url('blogs'); ?>" class="text-decoration-none fw-semibold --xhr">See all tales <i class="mdi mdi-arrow-right"></i></a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Start Building CTA -->
<section class="section-padding pb-0 border-fade-top text-center fade-in">
    <div class="container">
        <h2 class="fw-bold mb-3">Forge Yer Vessel Today</h2>
        <p class="text-secondary mb-4">Grab the charts of <strong>Aksara CMS</strong> and start buildin&#39; yer own ship without chains.</p>
        <div class="d-flex justify-content-center">
            <a href="https://github.com/abydahana/aksara" target="_blank" class="btn btn-primary rounded-pill px-4"><i class="mdi mdi-github"></i> GRAB THE CHARTS</a>
        </div>
    </div>
</section>
