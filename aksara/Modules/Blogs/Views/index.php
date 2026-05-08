<?php
/**
 * @var mixed $articles
 * @var mixed $meta
 * @var mixed $spotlight
 */
?>

<?php if ($articles): ?>
<section class="section-padding fade-in">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 text-center text-md-start">
                <h1 class="display-5 fw-bold">
                    <?= $meta->title; ?>
                </h1>
                <p class="fs-5">
                    <?= $meta->description; ?>
                </p>
                <div class="row mb-5">
                    <div class="col-lg-10">
                        <form action="<?= base_url('blogs/search', ['per_page' => null]); ?>" method="GET" class="form-horizontal position-relative">
                            <div class="input-group input-group-lg border rounded-pill bg-white overflow-hidden">
                                <input type="text" name="q" class="form-control border-0 bg-transparent shadow-none" placeholder="<?= phrase('Search post'); ?>" />
                                <button type="submit" class="btn btn-primary border-0 rounded-pill m-1 px-4">
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
                        <div class="carousel-inner rounded-5 overflow-hidden">
                            <?php foreach ($spotlight as $key => $val): ?>
                                <div class="carousel-item<?= (! $key ? ' active' : null); ?>">
                                    <div class="clip gradient-top"></div>
                                    <a href="<?= base_url(['blogs', $val->category_slug, $val->post_slug]); ?>" class="--xhr d-block">
                                        <img src="<?= get_image('blogs', $val->featured_image); ?>" class="d-block w-100 carousel-headline" alt="...">
                                    </a>
                                    <div class="carousel-caption text-start">
                                        <div class="mb-3">
                                            <a href="<?= base_url(['blogs', $val->category_slug, $val->post_slug]); ?>" class="--xhr d-block">
                                                <h3 class="text-light text-outline-dark">
                                                    <?= truncate($val->post_title, 80); ?>
                                                </h3>
                                                <p class="fs-5 text-light d-none d-md-inline text-outline-dark">
                                                    <?= truncate($val->post_excerpt, 90); ?>
                                                </p>
                                                <p class="text-muted">
                                                    <i class="mdi mdi-clock-outline"></i> <?= time_ago($val->updated_timestamp); ?>
                                                </p>
                                            </a>
                                        </div>
                                        <div class="row g-0 align-items-center">
                                            <div class="col-1">
                                                <a href="<?= base_url('user/' . $val->username); ?>" class="text-sm text-secondary text-outline-dark --xhr">
                                                    <img src="<?= get_image('users', $val->photo, 'icon'); ?>" class="img-fluid rounded-circle" alt="..." />
                                                </a>
                                            </div>
                                            <div class="col-8">
                                                <a href="<?= base_url('user/' . $val->username); ?>" class="text-white ps-2 text-outline-dark --xhr">
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
</section>
<?php endif; ?>

<section class="section-padding fade-in">
    <div class="container">
        <?php if ($articles): ?>
            <?php foreach ($articles as $key => $val): ?>
                <div class="mb-5">
                    <h3 class="text-center text-sm-start">
                        <a href="<?= base_url(['blogs', $val->category_slug]); ?>" class="--xhr">
                            <?= $val->category_title; ?> <i class="mdi mdi-arrow-right"></i>
                        </a>
                    </h3>
                    <p class="text-muted fs-5 text-center text-sm-start">
                        <?= $val->category_description; ?>
                    </p>
                    <div class="swiper" data-slide-count-sm="2" data-slide-count-md="2" data-slide-count-lg="3" data-slide-count-xl="4" data-autoplay="1">
                        <div class="swiper-wrapper">
                            <?php foreach ($val->posts as $_key => $_val): ?>
                                <div class="swiper-slide h-auto">
                                    <div class="h-100 d-flex flex-column">
                                        <div class="d-flex flex-column flex-grow-1 border p-3 rounded-4">
                                            <div class="row g-0 align-items-center mb-3">
                                                <div class="col-1">
                                                    <a href="<?= base_url('user/' . $_val->username); ?>" class="text-sm text-secondary --xhr">
                                                        <img src="<?= get_image('users', $_val->photo, 'icon'); ?>" class="img-fluid rounded-circle" alt="..." />
                                                    </a>
                                                </div>
                                                <div class="col-11 overflow-hidden">
                                                    <span class="text-muted text-sm float-end">
                                                        <i class="mdi mdi-clock-outline"></i> <?= time_ago($_val->updated_timestamp); ?>
                                                    </span>
                                                    <a href="<?= base_url('user/' . $_val->username); ?>" class="text-dark ps-2 text-decoration-none --xhr">
                                                        <b>
                                                            <?= $_val->first_name . ' ' . $_val->last_name; ?>
                                                        </b>
                                                    </a>
                                                </div>
                                            </div>
                                            <h5 class="mb-3">
                                                <a href="<?= base_url(['blogs', $val->category_slug, $_val->post_slug]); ?>" class="text-dark text-decoration-none --xhr">
                                                    <?= truncate($_val->post_title, 64); ?>
                                                </a>
                                            </h5>
                                        </div>
                                        <a href="<?= base_url(['blogs', $val->category_slug, $_val->post_slug]); ?>" class="--xhr">
                                            <img src="<?= get_image('blogs', $_val->featured_image, 'thumb'); ?>" class="img-fluid rounded-4 w-100 bg-white" alt="<?= $_val->post_title; ?>" style="aspect-ratio: 3/2; object-fit: cover;margin-top:-1.5rem">
                                        </a>
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
                        <p class="fs-5 text-center">
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
</section>
