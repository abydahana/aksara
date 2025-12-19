
<?php if ($articles): ?>
<div class="bg-white">
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
                                            <img src="<?= get_image('blogs', $val->featured_image); ?>" class="d-block w-100 carousel-headline" alt="...">
                                        </a>
                                        <div class="carousel-caption text-start">
                                            <div class="mb-3">
                                                <a href="<?= base_url(['blogs', $val->category_slug, $val->post_slug]); ?>" class="--xhr d-block">
                                                    <h3 class="text-light">
                                                        <?= truncate($val->post_title, 80); ?>
                                                    </h3>
                                                    <p class="lead text-light d-none d-md-inline">
                                                        <?= truncate($val->post_excerpt, 90); ?>
                                                    </p>
                                                    <p class="text-muted">
                                                        <i class="mdi mdi-clock-outline"></i> <?= time_ago($val->updated_timestamp); ?>
                                                    </p>
                                                </a>
                                            </div>
                                            <div class="row g-0 align-items-center">
                                                <div class="col-1">
                                                    <a href="<?= base_url('user/' . $val->username); ?>" class="text-sm text-secondary --xhr">
                                                        <img src="<?= get_image('users', $val->photo, 'icon'); ?>" class="img-fluid rounded-circle" alt="..." />
                                                    </a>
                                                </div>
                                                <div class="col-8">
                                                    <a href="<?= base_url('user/' . $val->username); ?>" class="text-white ps-2 --xhr">
                                                        <b>
                                                            <?= $val->first_name . ' ' . $val->last_name; ?>
                                                        </b>
                                                    </a>
                                                </div>
                                                <div class="col-3 text-end">
                                                    <button type="button" class="btn btn-sm rounded-pill --modify <?= (is_liked($val->post_id, 'blogs/' . $val->category_slug . '/' . $val->post_slug) ? 'btn-danger' : 'btn-outline-danger'); ?>" data-href="<?= base_url('xhr/widget/comment/repute', ['post_id' => $val->post_id, 'path' => 'blogs/' . $val->category_slug . '/' . $val->post_slug]); ?>" data-class-add="btn-danger" data-class-remove="btn-outline-danger">
                                                        <i class="mdi mdi-heart"></i>
                                                    </button>
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
    <svg class="wave text-light" version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 100" preserveAspectRatio="none">
        <path class="wavePath" d="M826.337463,25.5396311 C670.970254,58.655965 603.696181,68.7870267 447.802481,35.1443383 C293.342778,1.81111414 137.33377,1.81111414 0,1.81111414 L0,150 L1920,150 L1920,1.81111414 C1739.53523,-16.6853983 1679.86404,73.1607868 1389.7826,37.4859505 C1099.70117,1.81111414 981.704672,-7.57670281 826.337463,25.5396311 Z" fill="currentColor"></path>
    </svg>
</div>
<?php endif; ?>

<div class="<?= ($articles ? 'bg-light' : null); ?>">
    <div class="py-3">
        <div class="container">
            <?php if ($articles): ?>
                <?php foreach ($articles as $key => $val): ?>
                    <div class="py-3">
                        <a href="<?= base_url(['blogs', $val->category_slug]); ?>" class="--xhr">
                            <h3 class="text-center text-sm-start text-primary mt-3">
                                <?= $val->category_title; ?>
                            </h3>
                        </a>
                        <p class="lead text-center text-sm-start">
                            <?= $val->category_description; ?>
                        </p>
                        <div class="swiper" data-slide-count-sm="2" data-slide-count-md="2" data-slide-count-lg="3" data-slide-count-xl="4" data-autoplay="1">
                            <div class="swiper-wrapper">
                                <?php foreach ($val->posts as $_key => $_val): ?>
                                    <div class="swiper-slide">
                                        <div class="card border-0 rounded-4 overflow-hidden">
                                            <a href="<?= base_url(['blogs', $val->category_slug, $_val->post_slug]); ?>" class="--xhr d-block">
                                                <div class="position-relative" style="background:url(<?= get_image('blogs', $_val->featured_image, 'thumb'); ?>) center center no-repeat; background-size: cover; height: 256px">
                                                    <div class="clip gradient-top"></div>
                                                    <div class="position-absolute bottom-0 p-3">
                                                        <h4 class="text-light" data-toggle="tooltip" title="<?= $_val->post_title; ?>">
                                                            <?= truncate($_val->post_title, 64); ?>
                                                        </h4>
                                                        <p class="text-white">
                                                            <i class="mdi mdi-clock-outline"></i>
                                                            <?= time_ago($_val->updated_timestamp); ?>
                                                        </p>
                                                    </div>
                                                </div>
                                            </a>
                                            <div class="card-body">
                                                <p class="lead card-text text-secondary">
                                                    <?= truncate($_val->post_excerpt, 64); ?>
                                                </p>
                                                <div class="row g-0 align-items-center">
                                                    <div class="col-1">
                                                        <a href="<?= base_url('user/' . $_val->username); ?>" class="text-sm text-secondary --xhr">
                                                            <img src="<?= get_image('users', $_val->photo, 'icon'); ?>" class="img-fluid rounded-circle" alt="..." />
                                                        </a>
                                                    </div>
                                                    <div class="col-8 overflow-hidden">
                                                        <a href="<?= base_url('user/' . $_val->username); ?>" class="text-dark ps-2 --xhr">
                                                            <b>
                                                                <?= $_val->first_name . ' ' . $_val->last_name; ?>
                                                            </b>
                                                        </a>
                                                    </div>
                                                    <div class="col-3 text-end">
                                                        <button type="button" class="btn btn-sm rounded-pill --modify <?= (is_liked($_val->post_id, 'blogs/' . $val->category_slug . '/' . $_val->post_slug) ? 'btn-secondary' : 'btn-outline-secondary'); ?>" data-href="<?= base_url('xhr/widget/comment/repute', ['post_id' => $_val->post_id, 'path' => 'blogs/' . $val->category_slug . '/' . $_val->post_slug]); ?>" data-class-add="btn-secondary" data-class-remove="btn-outline-secondary">
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
                <?php endforeach; ?>
            <?php else: ?>
                <div class="row">
                    <div class="col-lg-8 offset-lg-2">
                        <div class="py-5">
                            <div class="text-center">
                                <img src="<?= base_url('assets/yao-ming.png'); ?>" width="128" alt="404" />
                            </div>
                            <h2 class="text-center">
                                <?= phrase('No post is found!'); ?>
                            </h2>
                            <p class="lead text-center">
                                <?= phrase('No post is available at the moment.'); ?>
                            </p>
                            <p class="text-center">
                                <a href="<?= base_url(); ?>" class="btn btn-outline-dark rounded-pill px-5 --xhr">
                                    <i class="mdi mdi-arrow-left"></i> <?= phrase('Back to Home'); ?>
                                </a>
                            </p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <svg class="wave text-white" version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 100" preserveAspectRatio="none">
        <path class="wavePath" d="M826.337463,25.5396311 C670.970254,58.655965 603.696181,68.7870267 447.802481,35.1443383 C293.342778,1.81111414 137.33377,1.81111414 0,1.81111414 L0,150 L1920,150 L1920,1.81111414 C1739.53523,-16.6853983 1679.86404,73.1607868 1389.7826,37.4859505 C1099.70117,1.81111414 981.704672,-7.57670281 826.337463,25.5396311 Z" fill="currentColor"></path>
    </svg>
</div>
