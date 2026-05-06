<?php
/**
 * @var mixed $articles
 * @var mixed $statistics
 * @var mixed $galleries
 */
?>

<!-- Hero Section -->
<section class="hero-section pb-0 text-center">
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

<!-- Trusted By Section -->
<section class="section-padding fade-in">
    <div class="container text-center">
        <p class="text-muted fw-semibold mb-4">Trusted by Organizations and Enterprises</p>
        <div class="d-flex flex-wrap justify-content-center align-items-center gap-4 gap-md-5 opacity-75">
            <h4 class="fw-bold m-0 text-secondary">GeekTech</h4>
            <h4 class="fw-bold m-0 text-secondary">Djayasoft</h4>
            <h4 class="fw-bold m-0 text-secondary">CrossLink</h4>
        </div>
    </div>
</section>

<!-- Build Applications Section -->
<section class="section-padding fade-in">
    <div class="container text-center">
        <h3 class="fw-bold mb-3 display-6 text-dark">Build Powerful Enterprise Applications Faster</h3>
        <p class="text-muted mb-5 fs-5">A robust framework and publishing platform designed to help you build complex applications with ease.</p>
        
        <div class="row mt-5">
            <div class="col-md-4 mb-4">
                <div class="bg-light rounded-5 h-100 d-flex flex-column text-center pt-5 px-4 px-lg-5 overflow-hidden card-hover">
                    <h4 class="fw-bold text-dark mb-3">Modular Architecture</h4>
                    <p class="text-muted small mb-4 lh-base">Create modular and scalable applications with a clean, extensible codebase that follows modern standards and best practices.</p>
                    <img src="<?= get_module_asset('images/modular-architecture.png'); ?>" class="img-fluid mt-auto rounded-top-5" alt="Modular Architecture">
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="bg-light rounded-5 h-100 d-flex flex-column text-center pt-5 px-4 px-lg-5 overflow-hidden card-hover">
                    <h4 class="fw-bold text-dark mb-3">Universal Database Support</h4>
                    <p class="text-muted small mb-4 lh-base">Switch between PostgreSQL, MySQL, SQL Server, and SQLite seamlessly without changing a single line of your application code.</p>
                    <img src="<?= get_module_asset('images/universal-database-support.png'); ?>" class="img-fluid mt-auto rounded-top-5" alt="Universal Database Support">
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="bg-light rounded-5 h-100 d-flex flex-column text-center pt-5 px-4 px-lg-5 overflow-hidden card-hover">
                    <h4 class="fw-bold text-dark mb-3">Enterprise-Ready Features</h4>
                    <p class="text-muted small mb-4 lh-base">Built-in authentication, role-based access control, and an intuitive administrative dashboard ready for your next big project.</p>
                    <img src="<?= get_module_asset('images/enterprise-ready-features.png'); ?>" class="img-fluid mt-auto rounded-top-5" alt="Enterprise-Ready Features">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Design Feature Section -->
<section class="section-padding">
    <div class="container fade-in text-center">
        <h3 class="fw-bold mb-3 display-6 text-dark">Developer-Friendly Environment</h3>
        <p class="text-muted mb-5 fs-5">Designed for speed and efficiency. Aksara CMS provides a seamless developer experience from initial installation to production deployment.</p>
    </div>
    <div class="container fade-in">
        <div class="row align-items-center">
            <div class="col-md-5 mb-4 mb-md-0 text-center text-md-start">
                <h3 class="fw-bold mb-3 display-6 text-dark">Rapid Prototyping & Scaffolding</h3>
                <p class="text-muted mb-4 fs-5 lh-base">
                    Build sophisticated CRUD applications in minutes. Our powerful scaffolding and core libraries handle the heavy lifting, letting you focus on business logic.
                </p>
            </div>
            <div class="col-md-6 offset-md-1">
                <img src="<?= get_module_asset('images/rapid-prototyping-scaffolding.png'); ?>" class="img-fluid rounded-5 w-100" alt="Rapid Prototyping & Scaffolding">
            </div>
        </div>
    </div>
</section>

<!-- Upload Feature Section -->
<section class="section-padding fade-in">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6 mb-4 mb-md-0 order-2 order-md-1">
                <img src="<?= get_module_asset('images/flexible-content-management.png'); ?>" class="img-fluid rounded-5 w-100" alt="Flexible Content Management">
            </div>
            <div class="col-md-5 offset-md-1 order-1 order-md-2 mb-4 mb-md-0 text-center text-md-start">
                <h3 class="fw-bold mb-3 display-6">Flexible Content Management</h3>
                <p class="text-muted mb-4 fs-5">
                    Manage your content, media, and users through a beautiful, intuitive interface that's easy to customize and adapt to any requirement.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Search Feature Section -->
<section class="section-padding fade-in">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-5 mb-4 mb-md-0 text-center text-md-start">
                <h3 class="fw-bold mb-3 display-6">High Performance & Scalability</h3>
                <p class="text-muted mb-4 fs-5">
                    Optimized for speed and low resource consumption, ensuring your applications stay fast and responsive even under heavy traffic.
                </p>
            </div>
            <div class="col-md-6 offset-md-1">
                <img src="<?= get_module_asset('images/high-performance-and-scalability.png'); ?>" class="img-fluid rounded-5 w-100" alt="High Performance & Scalability">
            </div>
        </div>
    </div>
</section>

<!-- Publish Anywhere -->
<section class="section-padding fade-in">
    <div class="container">
        <div class="bg-light rounded-5 p-5">
            <div class="row align-items-center p-lg-5">
                <div class="col-md-6 text-center text-md-start mb-5 mb-md-0">
                    <h3 class="fw-bold mb-3 display-6 text-dark">Effortless Installation</h3>
                    <p class="text-muted mb-4 fs-5 lh-base">
                        Setup is incredibly easy with our built-in installation wizard. Get your project up and running in less than 5 minutes on any environment.
                    </p>
                    <div class="mt-4 text-center text-md-start">
                        <a href="https://wa.me/6281381614558" target="_blank" class="btn btn-primary rounded-pill px-4"><i class="mdi mdi-cloud-upload"></i> &nbsp; Get Your VPS</a>
                        <a href="//wa.me/6281381614558" target="_blank" class="text-decoration-none fw-semibold ms-4 d-none d-lg-inline">Free consultation <i class="mdi mdi-arrow-right"></i></a>
                    </div>
                </div>
                <div class="col-md-6 text-center">
                    <div class="position-relative mx-auto" style="width: 280px; height: 280px;">
                        <!-- Concentric circles -->
                        <div class="position-absolute top-50 start-50 translate-middle rounded-circle" style="width: 180px; height: 180px; background-color: rgba(43, 102, 255, 0.05);"></div>
                        <div class="position-absolute top-50 start-50 translate-middle rounded-circle" style="width: 280px; height: 280px; background-color: rgba(43, 102, 255, 0.03);"></div>
                        
                        <!-- Top icon -->
                        <div class="position-absolute top-0 start-50 translate-middle-x bg-white rounded-circle d-flex align-items-center justify-content-center" style="width: 72px; height: 72px; z-index: 2;">
                            <div class="bg-secondary text-light border border-light text-light rounded-circle d-flex align-items-center justify-content-center icon-float-1" style="width: 48px; height: 48px;">
                                <i class="mdi mdi-cloud fs-3"></i>
                            </div>
                        </div>
                        <!-- Bottom icon -->
                        <div class="position-absolute bottom-0 start-50 translate-middle-x bg-white rounded-circle d-flex align-items-center justify-content-center" style="width: 72px; height: 72px; z-index: 2;">
                            <div class="bg-secondary text-light border border-light rounded-circle d-flex align-items-center justify-content-center icon-float-2" style="width: 48px; height: 48px;">
                                <i class="mdi mdi-server fs-3"></i>
                            </div>
                        </div>
                        <!-- Left icon -->
                        <div class="position-absolute top-50 start-0 translate-middle-y bg-white rounded-circle d-flex align-items-center justify-content-center" style="width: 72px; height: 72px; z-index: 2;">
                            <div class="bg-secondary text-light border border-light rounded-circle d-flex align-items-center justify-content-center icon-float-3" style="width: 48px; height: 48px;">
                                <i class="mdi mdi-laptop-mac fs-3"></i>
                            </div>
                        </div>
                        <!-- Right icon -->
                        <div class="position-absolute top-50 end-0 translate-middle-y bg-white rounded-circle d-flex align-items-center justify-content-center" style="width: 72px; height: 72px; z-index: 2;">
                            <div class="bg-secondary text-light border border-light rounded-circle d-flex align-items-center justify-content-center icon-float-4" style="width: 48px; height: 48px;">
                                <i class="mdi mdi-database fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Statistics Section -->
<section class="section-padding fade-in border-fade">
    <div class="container text-center">
        <h2 class="fw-bold mb-5 display-6"><?= phrase('Community & Engagement'); ?></h2>
        <div class="row justify-content-center">
            <div class="col-6 col-md-4 mb-5">
                <h2 class="display-4 fw-bold mb-2"><?= number_format($statistics->daily); ?></h2>
                <h5 class="fw-bold mb-2"><?= phrase('Daily Visits'); ?></h5>
                <p class="text-muted small mb-0"><?= phrase('Unique visitors today'); ?>.</p>
            </div>
            <div class="col-6 col-md-4 mb-5">
                <h2 class="display-4 fw-bold mb-2"><?= number_format($statistics->weekly); ?></h2>
                <h5 class="fw-bold mb-2"><?= phrase('Weekly Visits'); ?></h5>
                <p class="text-muted small mb-0"><?= phrase('Total visits this week'); ?>.</p>
            </div>
            <div class="col-6 col-md-4 mb-5">
                <h2 class="display-4 fw-bold mb-2"><?= number_format($statistics->monthly); ?></h2>
                <h5 class="fw-bold mb-2"><?= phrase('Monthly Visits'); ?></h5>
                <p class="text-muted small mb-0"><?= phrase('Total visits this month'); ?>.</p>
            </div>
            <div class="col-6 col-md-4 mb-4 mb-md-0">
                <h2 class="display-4 fw-bold mb-2"><?= number_format($statistics->yearly); ?></h2>
                <h5 class="fw-bold mb-2"><?= phrase('Yearly Visits'); ?></h5>
                <p class="text-muted small mb-0"><?= phrase('Total visits this year'); ?>.</p>
            </div>
            <div class="col-6 col-md-4 mb-4 mb-md-0">
                <h2 class="display-4 fw-bold mb-2"><?= number_format($statistics->posts); ?></h2>
                <h5 class="fw-bold mb-2"><?= phrase('Published Articles'); ?></h5>
                <p class="text-muted small mb-0"><?= phrase('Total content available'); ?>.</p>
            </div>
            <div class="col-6 col-md-4 mb-0">
                <h2 class="display-4 fw-bold mb-2"><?= number_format($statistics->comments); ?></h2>
                <h5 class="fw-bold mb-2"><?= phrase('Interactions'); ?></h5>
                <p class="text-muted small mb-0"><?= phrase('User comments and feedback'); ?>.</p>
            </div>
        </div>
    </div>
</section>

<!-- Latest Galleries -->
<?php if ($galleries): ?>
<section class="section-padding fade-in">
    <div class="container">
        <div class="text-center mb-5">
            <h3 class="fw-bold m-0 display-6"><?= phrase('Featured Galleries'); ?></h3>
            <p class="text-muted mb-5 fs-5"><?= phrase('See our latest gallery activities'); ?></p>
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

<!-- Latest News -->
<?php if ($articles): ?>
<section class="section-padding fade-in">
    <div class="container">
        <div class="text-center mb-5">
            <h3 class="fw-bold m-0 display-6"><?= phrase('Recent Articles'); ?></h3>
            <p class="text-muted mb-5 fs-5"><?= phrase('Read our newest articles'); ?></p>
        </div>
        <div class="swiper" data-slide-count-sm="2" data-slide-count-md="2" data-slide-count-lg="3" data-slide-count-xl="4" data-autoplay="1">
            <div class="swiper-wrapper py-3">
                <?php foreach ($articles as $key => $val): ?>
                    <div class="swiper-slide">
                        <a href="<?= base_url(['blogs', $val->category_slug, $val->post_slug]); ?>" class="--xhr">
                            <img src="<?= get_image('blogs', $val->featured_image, 'thumb'); ?>" class="img-fluid rounded-5 w-100" alt="Blog" style="aspect-ratio: 3/2; object-fit: cover;">
                        </a>
                        <div class="px-0 pt-3">
                            <p class="text-muted small mb-2">
                                <a href="<?= base_url('user/' . $val->username); ?>" class="text-dark fw-semibold --xhr"><?= $val->first_name . ' ' . $val->last_name; ?></a>
                                <span class="text-sm float-end"><?= time_ago($val->updated_timestamp); ?></span>
                            </p>
                            <h5 class="fw-bold mb-2" style="letter-spacing: -0.01em;">
                                <a href="<?= base_url(['blogs', $val->category_slug, $val->post_slug]); ?>" class="text-dark text-decoration-none --xhr">
                                    <?= truncate($val->post_title, 50); ?>
                                </a>
                            </h5>
                            <p class="text-muted small lh-lg">
                                <?= truncate($val->post_excerpt, 80); ?>
                            </p>
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

<!-- Start Building CTA -->
<section class="section-padding pb-0 border-fade-top text-center fade-in">
    <div class="container">
        <h2 class="fw-bold mb-3">Build Your Application Today</h2>
        <p class="text-secondary mb-4">Get the complete source code of <strong>Aksara CMS</strong> and start building your own platform without limitations.</p>
        <div class="d-flex justify-content-center">
            <a href="https://github.com/abydahana/aksara" target="_blank" class="btn btn-primary rounded-pill px-4"><i class="mdi mdi-github-circle"></i> GET SOURCE CODE</a>
        </div>
    </div>
</section>
