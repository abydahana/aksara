<div class="py-3 py-md-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 text-center text-md-start">
                <h1 class="display-5 fw-bold">
                    <?= $meta->title; ?>
                </h1>
                <p class="lead">
                    <?= $meta->description; ?>
                </p>
                <div class="row mb-5">
                    <div class="col-lg-10">
                        <form action="<?= base_url('blogs/search', ['per_page' => null]); ?>" method="GET" class="form-horizontal position-relative">
                            <div class="input-group input-group-lg">
                                <input type="text" name="q" class="form-control rounded-pill rounded-end" placeholder="<?= phrase('Search post'); ?>" />
                                <button type="submit" class="btn btn-dark  rounded-pill rounded-start">
                                    <i class="mdi mdi-magnify"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <?php if ($spotlight): ?>
                    <div class="carousel slide" id="carouselExampleCaptions" data-bs-ride="carousel">
                        <div class="carousel-inner rounded-4 overflow-hidden">
                            <?php foreach ($spotlight as $key => $val): ?>
                                <div class="carousel-item<?= (! $key ? ' active' : null); ?>">
                                    <div class="clip gradient-top"></div>
                                    <a href="<?= base_url(['blogs', $val->category_slug, $val->post_slug]); ?>" class="--xhr d-block">
                                        <img src="<?= get_image('blogs', $val->featured_image); ?>" class="d-block w-100" alt="..." style="max-height:360px;object-fit: cover">
                                    </a>
                                    <div class="carousel-caption text-start">
                                        <div class="mb-3">
                                            <a href="<?= base_url(['blogs', $val->category_slug, $val->post_slug]); ?>" class="--xhr d-block">
                                                <h4 class="text-light">
                                                    <?= truncate($val->post_title, 80); ?>
                                                </h4>
                                                <p class="text-light d-none d-md-inline">
                                                    <?= truncate($val->post_excerpt, 90); ?>
                                                </p>
                                            </a>
                                        </div>
                                        <div class="row g-0 align-items-center">
                                            <div class="col-1">
                                                <a href="<?= base_url('user/' . $val->username); ?>" class="text-sm text-secondary --xhr">
                                                    <img src="<?= get_image('users', $val->photo, 'icon'); ?>" class="img-fluid rounded-circle" alt="..." />
                                                </a>
                                            </div>
                                            <div class="col-6">
                                                <a href="<?= base_url('user/' . $val->username); ?>" class="text-white ps-2 --xhr">
                                                    <b>
                                                        <?= $val->first_name . ' ' . $val->last_name; ?>
                                                    </b>
                                                </a>
                                            </div>
                                            <div class="col-5 text-end">
                                                <small class="text-white text-sm">
                                                    <i class="mdi mdi-clock-outline"></i> <?= time_ago($val->updated_timestamp); ?>
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden"><?= phrase('Previous'); ?></span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden"><?= phrase('Next'); ?></span>
                        </button>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php if ($categories): ?>
<div class="py-3 py-md-5">
    <div class="container">
        <h1 class="display-5 text-center text-lg-start fw-bold lh-1">
            <?= phrase('Post Categories'); ?>
        </h1>
        <p class="text-center text-md-start lead">
            <?= phrase('Browse the articles by category'); ?>
        </p>
        <div class="row align-items-center">
            <?php foreach ($categories as $key => $val): ?>
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="card rounded-4 mb-3 <?= ($key < 4 ? (($key + 1) % 2 == 0 ? 'bg-secondary' : 'bg-dark') : (($key + 1) % 2 == 0 ? 'bg-dark' : 'bg-secondary')); ?>">
                        <div class="card-body py-lg-5 text-center text-light">
                            <a href="<?= base_url('blogs/' . $val->category_slug); ?>" class="--xhr">
                                <img src="<?= get_image('blogs', $val->category_image, 'icon'); ?>" class="img-fluid rounded-circle mb-3" />
                            </a>
                            <br />
                            <h5 class="mb-3 text-truncate">
                                <a href="<?= base_url('blogs/' . $val->category_slug); ?>" class="text-light --xhr">
                                    <?= $val->category_title; ?>
                                </a>
                            </h5>
                            <p class="mb-0 mb-md-3">
                                <?= number_format($val->post_total) . ' ' . phrase('articles'); ?>
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
<div class="py-3 py-md-5">
    <div class="container">
        <h1 class="display-5 text-center text-lg-start fw-bold lh-1">
            <?= phrase('Recent Articles'); ?>
        </h1>
        <p class="text-center text-md-start lead">
            <?= phrase('Read our newest articles'); ?>
        </p>
        <div class="swiper" data-slide-count-sm="2" data-slide-count-md="2" data-slide-count-lg="3" data-slide-count-xl="4" data-autoplay="1">
            <div class="swiper-wrapper">
                <?php foreach ($articles as $key => $val): ?>
                    <div class="swiper-slide">
                        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                            <a href="<?= base_url(['blogs', $val->category_slug, $val->post_slug]); ?>" class="--xhr d-block">
                                <div class="position-relative" style="background:url(<?= get_image('blogs', $val->featured_image, 'thumb'); ?>) center center no-repeat; background-size: cover; height: 256px">
                                    <div class="clip gradient-top"></div>
                                    <div class="position-absolute bottom-0 p-3">
                                        <b class="text-light" data-toggle="tooltip" title="<?= $val->post_title; ?>">
                                        <?= truncate($val->post_title, 64); ?>
                                        </b>
                                    </div>
                                </div>
                            </a>
                            <div class="card-body">
                                <p class="card-text text-secondary">
                                    <?= truncate($val->post_excerpt, 64); ?>
                                </p>
                                <div class="row g-0 align-items-center">
                                    <div class="col-1">
                                        <a href="<?= base_url('user/' . $val->username); ?>" class="text-sm text-secondary">
                                            <img src="<?= get_image('users', $val->photo, 'icon'); ?>" class="img-fluid rounded-circle" alt="..." />
                                        </a>
                                    </div>
                                    <div class="col-7">
                                        <a href="<?= base_url('user/' . $val->username); ?>" class="text-sm text-dark ps-2">
                                            <b>
                                                <?= $val->first_name . ' ' . $val->last_name; ?>
                                            </b>
                                        </a>
                                    </div>
                                    <div class="col-4 text-end">
                                        <small class="text-muted text-sm">
                                            <i class="mdi mdi-clock-outline"></i>
                                            <?= time_ago($val->updated_timestamp); ?>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php if ($galleries): ?>
<div class="py-3">
	<div class="container">
        <h1 class="display-5 text-center text-lg-start fw-bold lh-1">
            <?= phrase('Galleries'); ?>
        </h1>
        <p class="text-center text-md-start lead">
            <?= phrase('See our latest gallery activities'); ?>
        </p>
        <div class="swiper" data-slide-count-sm="2" data-slide-count-md="2" data-slide-count-lg="3" data-slide-count-xl="3">
            <div class="swiper-wrapper">
                <?php foreach ($galleries as $key => $val): ?>
                    <?php
                        $images = json_decode($val->gallery_images);

                        if (! $images) {
                            continue;
                        }
                    ?>
                    <?php foreach ($images as $src => $alt): ?>
                        <div class="swiper-slide">
                            <div class="card border-0 rounded-4 overflow-hidden shadow-sm mb-5">
                                <a href="<?= base_url(['galleries', $val->gallery_slug]); ?>" class="--xhr d-block">
                                    <div class="card-body" style="background:url(<?= get_image('galleries', $src, 'thumb'); ?>) center center no-repeat; background-size: cover; height: 256px">
                                        <div class="clip gradient-top"></div>
                                        <div class="position-absolute bottom-0 p-3">
                                            <b class="text-light">
                                                <?= $val->gallery_title; ?>
                                            </b>
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
<div class="py-3 py-md-5 bg-light">
    <div class="container">
        <h1 class="display-5 text-center text-lg-start fw-bold lh-1">
            <?= phrase('Meet Us'); ?>
        </h1>
        <p class="text-center text-md-start lead">
            <?= phrase('Talk to us, straight to our team'); ?>
        </p>
        <div class="row align-items-center">
            <?php foreach ($peoples as $key => $val): ?>
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="card rounded-4 mb-3 border-0 shadow-sm">
                        <div class="card-body text-center py-lg-5">
                            <a href="<?= base_url('peoples/' . $val->people_slug); ?>" class="--xhr">
                                <img src="<?= get_image('peoples', $val->photo, 'icon'); ?>" class="img-fluid rounded-circle mb-3" />
                            </a>
                            <br />
                            <h5 class="mb-3 text-truncate">
                                <a href="<?= base_url('peoples/' . $val->people_slug); ?>" class="--xhr">
                                    <?= $val->first_name . ' ' . $val->last_name; ?>
                                </a>
                            </h5>
                            <p class="mb-0 mb-md-3">
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
</div>
<?php endif; ?>

<div class="py-3" style="background:url(<?= get_image('home', 'map_bg.png'); ?>) center center no-repeat;background-size:contain">
    <div class="container py-lg-5">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="display-5 text-center text-lg-start fw-bold lh-1">
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
                        <h5 class="mt-0">
                            <?= phrase('Daily Visits'); ?>
                        </h5>
                    </div>
                    <div class="col-6 col-sm-6 text-center text-md-start mb-4">
                        <div class="counter color fw-semibold display-5">
                            <span data-from="10" data-to="<?= $statistics->weekly; ?>" data-refresh-interval="2" data-speed="600"> <?= number_format($statistics->weekly); ?> </span>
                        </div>
                        <h5 class="mt-0">
                            <?= phrase('Weekly Visits'); ?>
                        </h5>
                    </div>
                    <div class="col-6 col-sm-6 text-center text-md-start mb-4">
                        <div class="counter color fw-semibold display-5">
                            <span data-from="10" data-to="<?= $statistics->monthly; ?>" data-refresh-interval="11" data-speed="900"> <?= number_format($statistics->monthly); ?> </span>
                        </div>
                        <h5 class="mt-0">
                            <?= phrase('Monthly Visits'); ?>
                        </h5>
                    </div>
                    <div class="col-6 col-sm-6 text-center text-md-start mb-4">
                        <div class="counter color fw-semibold display-5">
                            <span data-from="100" data-to="<?= $statistics->yearly; ?>" data-refresh-interval="3" data-speed="1000"> <?= number_format($statistics->yearly); ?> </span>
                        </div>
                        <h5 class="mt-0">
                            <?= phrase('Yearly Visits'); ?>
                        </h5>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="row">
                    <div class="col-6 col-md-12">
                        <div class="card shadow-sm border-light-subtle rounded-4 mb-md-4 overflow-hidden">
                            <div class="card-body">
                                <div class="text-center">
                                    <img src="<?= get_image('home', 'base_map.png'); ?>" class="img-fluid">
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
                        <div class="card shadow-sm border-light-subtle rounded-4 overflow-hidden">
                            <div class="card-body">
                                <div class="text-center">
                                    <img src="<?= get_image('home', 'layer.png'); ?>" class="img-fluid">
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
                <div class="card shadow-sm border-light-subtle rounded-4 overflow-hidden">
                    <div class="card-body">
                        <div class="text-center">
                            <img src="<?= get_image('home', 'identification.png'); ?>" class="img-fluid">
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
