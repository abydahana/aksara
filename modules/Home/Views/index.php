<?php
/**
 * @var mixed $categories
 * @var mixed $articles
 * @var mixed $galleries
 * @var mixed $peoples
 * @var mixed $statistics
 */
?>
<style type="text/css">
    @media (min-width:960px) {
        .navbar.fixed-top {
            background-color: rgba(var(--bs-dark-rgb),.75)!important;
            transition: all ease-in-out .3s
        }
        .navbar.fixed-top.scrolled {
            background-color: rgba(var(--bs-dark-rgb),1)!important
        }
    }
	#fullscreen-video {
		position: absolute;
		right: 0;
		bottom: 0;
		min-width: 100%;
		min-height: 100%
	}
	.video-overlay {
		position: absolute;
		right: 0;
		bottom: 0;
		min-width: 100%;
		min-height: 100%;
        background-color: rgba(33, 33, 33, .75)
	}
    .circles {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        overflow: hidden;
    }
    .circles li {
        position: absolute;
        display: block;
        list-style: none;
        width: 20px;
        height: 20px;
        background: rgba(255, 255, 255, 0.2);
        animation: animate 25s linear infinite;
        bottom: -150px;

    }
    .circles li:nth-child(1) {
        left: 25%;
        width: 80px;
        height: 80px;
        animation-delay: 0s;
    }
    .circles li:nth-child(2) {
        left: 10%;
        width: 20px;
        height: 20px;
        animation-delay: 2s;
        animation-duration: 12s;
    }
    .circles li:nth-child(3) {
        left: 70%;
        width: 20px;
        height: 20px;
        animation-delay: 4s;
    }
    .circles li:nth-child(4) {
        left: 40%;
        width: 60px;
        height: 60px;
        animation-delay: 0s;
        animation-duration: 18s;
    }
    .circles li:nth-child(5) {
        left: 65%;
        width: 20px;
        height: 20px;
        animation-delay: 0s;
    }
    .circles li:nth-child(6) {
        left: 75%;
        width: 110px;
        height: 110px;
        animation-delay: 3s;
    }
    .circles li:nth-child(7) {
        left: 35%;
        width: 150px;
        height: 150px;
        animation-delay: 7s;
    }
    .circles li:nth-child(8) {
        left: 50%;
        width: 25px;
        height: 25px;
        animation-delay: 15s;
        animation-duration: 45s;
    }
    .circles li:nth-child(9) {
        left: 20%;
        width: 15px;
        height: 15px;
        animation-delay: 2s;
        animation-duration: 35s;
    }
    .circles li:nth-child(10) {
        left: 85%;
        width: 150px;
        height: 150px;
        animation-delay: 0s;
        animation-duration: 11s;
    }
    @keyframes animate {
        0% {
            transform: translateY(0) rotate(0deg);
            opacity: 1;
            border-radius: 0;
        }
        100% {
            transform: translateY(-1000px) rotate(720deg);
            opacity: 0;
            border-radius: 100%
        }
    }
</style>
<script type="text/javascript">
    window.addEventListener('scroll', function() {
        const header = document.querySelector('.navbar.fixed-top');

        if (window.scrollY > $('.hero').outerHeight()) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }
    });
</script>
<div class="py-3 bg-light position-relative hero">
	<video autoplay muted loop id="fullscreen-video">
		<source src="<?= get_module_asset('videos/liquid.mp4'); ?>" type="video/mp4" />
	</video>
	<div class="video-overlay"></div>
    <div class="area">
        <ul class="circles">
            <li></li>
            <li></li>
            <li></li>
            <li></li>
            <li></li>
            <li></li>
            <li></li>
            <li></li>
            <li></li>
            <li></li>
        </ul>
    </div>
    <div class="container position-relative">
        <div class="row full-height align-items-center">
            <div class="col-md-6 text-center text-md-start">
                <div class="w-100 mb-3">
                    <h1 class="fw-bold mb-3 text-light text-glow">
                        <?= get_setting('app_name'); ?>
                    </h1>
                    <p class="lead mb-3 text-light text-glow mb-5">
                        <?= nl2br(get_setting('app_description')); ?>
                    </p>
                    <div class="row">
                        <div class="col-sm-6 offset-sm-3 col-md-6 offset-md-0">
                            <a href="<?= base_url('blogs'); ?>" class="btn btn-dark btn-lg px-5 rounded-pill --xhr">
                                <?= phrase('Explore our articles'); ?> <i class="mdi mdi-arrow-right text-light"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="row mb-3">
                    <div class="col-lg-10 offset-lg-1">
                        <img src="<?= get_module_asset('images/hero.png'); ?>" class="img-fluid rounded-5" alt="...">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <svg class="wave text-white position-absolute" style="bottom:0" version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 100" preserveAspectRatio="none">
        <path class="wavePath" d="M826.337463,25.5396311 C670.970254,58.655965 603.696181,68.7870267 447.802481,35.1443383 C293.342778,1.81111414 137.33377,1.81111414 0,1.81111414 L0,150 L1920,150 L1920,1.81111414 C1739.53523,-16.6853983 1679.86404,73.1607868 1389.7826,37.4859505 C1099.70117,1.81111414 981.704672,-7.57670281 826.337463,25.5396311 Z" fill="currentColor"></path>
    </svg>
</div>

<?php if ($categories): ?>
<div class="py-3 bg-light">
    <div class="container">
        <h1 class="text-center text-lg-start fw-bold lh-1">
            <?= phrase('Post Categories'); ?>
        </h1>
        <p class="text-center text-md-start lead">
            <?= phrase('Browse the articles by category'); ?>
        </p>
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
</div>
<?php endif; ?>

<?php if ($articles): ?>
<div class="py-3 pb-0 bg-light">
    <div class="container">
        <h1 class="text-center text-lg-start fw-bold lh-1">
            <?= phrase('Recent Articles'); ?>
        </h1>
        <p class="text-center text-md-start lead">
            <?= phrase('Read our newest articles'); ?>
        </p>
        <div class="swiper" data-slide-count-sm="2" data-slide-count-md="2" data-slide-count-lg="3" data-slide-count-xl="4" data-autoplay="1">
            <div class="swiper-wrapper py-3">
                <?php foreach ($articles as $key => $val): ?>
                    <div class="swiper-slide">
                        <div class="card border-0 rounded-4 overflow-hidden">
                            <a href="<?= base_url(['blogs', $val->category_slug, $val->post_slug]); ?>" class="--xhr d-block">
                                <div class="position-relative" style="background:url(<?= get_image('blogs', $val->featured_image, 'thumb'); ?>) center center no-repeat; background-size: cover; height: 256px">
                                    <div class="clip gradient-top"></div>
                                    <div class="position-absolute bottom-0 p-3">
                                        <h4 class="text-light" data-toggle="tooltip" title="<?= $val->post_title; ?>">
                                            <?= truncate($val->post_title, 64); ?>
                                        </h4>
                                        <p class="text-white">
                                            <i class="mdi mdi-clock-outline"></i>
                                            <?= time_ago($val->updated_timestamp); ?>
                                        </p>
                                    </div>
                                </div>
                            </a>
                            <div class="card-body">
                                <p class="lead card-text text-secondary">
                                    <?= truncate($val->post_excerpt, 64); ?>
                                </p>
                                <div class="row g-0 align-items-center">
                                    <div class="col-1">
                                        <a href="<?= base_url('user/' . $val->username); ?>" class="text-sm text-secondary">
                                            <img src="<?= get_image('users', $val->photo, 'icon'); ?>" class="img-fluid rounded-circle" alt="..." />
                                        </a>
                                    </div>
                                    <div class="col-8 overflow-hidden">
                                        <a href="<?= base_url('user/' . $val->username); ?>" class="text-dark ps-2">
                                            <b>
                                                <?= $val->first_name . ' ' . $val->last_name; ?>
                                            </b>
                                        </a>
                                    </div>
                                    <div class="col-3 text-end">
                                        <button type="button" class="btn btn-sm rounded-pill --modify <?= (is_liked($val->post_id, 'blogs/' . $val->category_slug . '/' . $val->post_slug) ? 'btn-secondary' : 'btn-outline-secondary'); ?>" data-href="<?= base_url('xhr/widget/comment/repute', ['post_id' => $val->post_id, 'path' => 'blogs/' . $val->category_slug . '/' . $val->post_slug]); ?>" data-class-add="btn-secondary" data-class-remove="btn-outline-secondary">
                                            <i class="mdi mdi-heart"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <svg class="wave text-white" version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 100" preserveAspectRatio="none">
        <path class="wavePath" d="M826.337463,25.5396311 C670.970254,58.655965 603.696181,68.7870267 447.802481,35.1443383 C293.342778,1.81111414 137.33377,1.81111414 0,1.81111414 L0,150 L1920,150 L1920,1.81111414 C1739.53523,-16.6853983 1679.86404,73.1607868 1389.7826,37.4859505 C1099.70117,1.81111414 981.704672,-7.57670281 826.337463,25.5396311 Z" fill="currentColor"></path>
    </svg>
</div>
<?php endif; ?>

<?php if ($galleries): ?>
<div class="py-3">
	<div class="container">
        <h1 class="text-center text-lg-start fw-bold lh-1">
            <?= phrase('Galleries'); ?>
        </h1>
        <p class="text-center text-md-start lead">
            <?= phrase('See our latest gallery activities'); ?>
        </p>
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
	</div>
</div>
<?php endif; ?>

<?php if ($peoples): ?>
<div class="py-3 pb-0 bg-light">
    <div class="container">
        <h1 class="text-center text-lg-start fw-bold lh-1">
            <?= phrase('Meet Us'); ?>
        </h1>
        <p class="text-center text-md-start lead">
            <?= phrase('Talk to us, straight to our team'); ?>
        </p>
        <div class="row align-items-center">
            <?php foreach ($peoples as $key => $val): ?>
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="card rounded-4 mb-3 border-0">
                        <div class="card-body text-center py-lg-5">
                            <a href="<?= base_url('peoples/' . $val->people_slug); ?>" class="--xhr">
                                <img src="<?= get_image('peoples', $val->photo, 'icon'); ?>" class="img-fluid rounded-circle mb-3" />
                            </a>
                            <br />
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
    <svg class="wave text-white" version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 100" preserveAspectRatio="none">
        <path class="wavePath" d="M826.337463,25.5396311 C670.970254,58.655965 603.696181,68.7870267 447.802481,35.1443383 C293.342778,1.81111414 137.33377,1.81111414 0,1.81111414 L0,150 L1920,150 L1920,1.81111414 C1739.53523,-16.6853983 1679.86404,73.1607868 1389.7826,37.4859505 C1099.70117,1.81111414 981.704672,-7.57670281 826.337463,25.5396311 Z" fill="currentColor"></path>
    </svg>
</div>
<?php endif; ?>

<div class="py-3">
    <div class="container py-lg-5">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="text-center text-lg-start fw-bold lh-1">
                    <?= phrase('Today Statistics'); ?>
                </h1>
                <p class="text-center text-md-start lead mb-5">
                    <?= phrase('and will continue to grow'); ?>
                </p>
                <div class="row">
                    <div class="col-6 col-sm-6 text-center text-md-start mb-4">
                        <div class="counter color fw-semibold display-5">
                            <span data-from="1" data-to="<?= $statistics->daily; ?>" data-refresh-interval="100" data-speed="1500"> <?= number_format($statistics->daily); ?> </span>
                        </div>
                        <h4 class="mt-0">
                            <?= phrase('Daily Visits'); ?>
                        </h4>
                    </div>
                    <div class="col-6 col-sm-6 text-center text-md-start mb-4">
                        <div class="counter color fw-semibold display-5">
                            <span data-from="10" data-to="<?= $statistics->weekly; ?>" data-refresh-interval="2" data-speed="600"> <?= number_format($statistics->weekly); ?> </span>
                        </div>
                        <h4 class="mt-0">
                            <?= phrase('Weekly Visits'); ?>
                        </h4>
                    </div>
                    <div class="col-6 col-sm-6 text-center text-md-start mb-4">
                        <div class="counter color fw-semibold display-5">
                            <span data-from="10" data-to="<?= $statistics->monthly; ?>" data-refresh-interval="11" data-speed="900"> <?= number_format($statistics->monthly); ?> </span>
                        </div>
                        <h4 class="mt-0">
                            <?= phrase('Monthly Visits'); ?>
                        </h4>
                    </div>
                    <div class="col-6 col-sm-6 text-center text-md-start mb-4">
                        <div class="counter color fw-semibold display-5">
                            <span data-from="100" data-to="<?= $statistics->yearly; ?>" data-refresh-interval="3" data-speed="1000"> <?= number_format($statistics->yearly); ?> </span>
                        </div>
                        <h4 class="mt-0">
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
                                        <img src="<?= get_module_asset('images/blog.png'); ?>" class="img-fluid">
                                    </div>
                                </div>
                                <div class="text-center">
                                    <h4 class="card-title fw-semibold color">
                                        <?= number_format($statistics->posts) . ' ' . phrase('Posts'); ?>
                                    </h4>
                                    <p class="card-text d-none d-sm-block">
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
                                        <img src="<?= get_module_asset('images/album.png'); ?>" class="img-fluid">
                                    </div>
                                </div>
                                <div class="text-center">
                                    <h4 class="card-title fw-semibold color">
                                        <?= number_format($statistics->galleries) . ' ' . phrase('Albums'); ?>
                                    </h4>
                                    <p class="card-text d-none d-sm-block">
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
                                <img src="<?= get_module_asset('images/interaction.png'); ?>" class="img-fluid">
                            </div>
                        </div>
                        <div class="text-center">
                            <h4 class="card-title fw-semibold color">
                                <?= number_format($statistics->comments) . ' ' . phrase('Interactions'); ?>
                            </h4>
                            <p class="card-text">
                                <?= phrase('Collected from submitted comments'); ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
