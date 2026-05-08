<?php
/**
 * @var mixed $results
 * @var mixed $meta
 * @var mixed $category
 * @var mixed $pagination
 */
?>

<?php if ($results): ?>
<section class="section-padding">
    <!-- Background Wavy Shape -->
    <svg class="position-absolute top-0 d-none d-md-block hero-blob" viewBox="0 0 948 458" fill="none">
        <path fill="currentColor" d="M179.493 278.507C88.0136 187.027 42.2737 141.287 21.1376 90.2621C-7.04587 22.2238 -7.04587 -54.2238 21.1376 -122.262C42.2737 -173.287 88.0136 -219.027 179.493 -310.507C270.973 -401.986 316.713 -447.726 367.738 -468.862C435.776 -497.046 512.224 -497.046 580.262 -468.862C631.287 -447.726 677.027 -401.986 768.507 -310.507C859.986 -219.027 905.726 -173.287 926.862 -122.262C955.046 -54.2238 955.046 22.2238 926.862 90.2621C905.726 141.287 859.986 187.027 768.507 278.507C677.027 369.986 631.287 415.726 580.262 436.862C512.224 465.046 435.776 465.046 367.738 436.862C316.713 415.726 270.973 369.986 179.493 278.507Z"/>
    </svg>
    <div class="container fade-in">
        <div class="row align-items-center">
            <div class="col-lg-8 text-center text-md-start">
                <h1 class="display-5 fw-bold">
                    <?= $meta->title; ?>
                </h1>
                <p class="fs-5">
                    <?= $meta->description; ?>
                </p>
                <div class="row">
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
            <div class="col-lg-4">
                <div class="d-none d-lg-block">
                    <?php if($category): ?>
                        <img src="<?= get_image('blogs', $category->category_image); ?>" class="img-fluid rounded-5" alt="..." />
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<section class="section-padding fade-in">
    <div class="container">
        <?php if ($results): ?>
            <div class="row">
                <?php foreach ($results as $key => $val): ?>
                    <?php
                        $item_tags = array_map('trim', explode(',', $val->post_tags));
                        $tags = null;

                        if (sizeof($item_tags) > 0) {
                            foreach ($item_tags as $label => $badge) {
                                if ($label == 2) {
                                    break;
                                }

                                if ($badge) {
                                    $tags .= '
                                        <a href="' . go_to('../tags', ['q' => $badge]) . '" class="--xhr">
                                            <span class="badge bg-secondary me-2">
                                                #' . trim($badge) . '
                                            </span>
                                        </a>
                                    ';
                                }
                            }
                        }
                    ?>
                    <div class="col-sm-6 col-lg-4 mb-3 mb-lg-4">
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
                                <div style="z-index:1">
                                    <?= $tags; ?>
                                </div>
                            </div>
                            <a href="<?= base_url(['blogs', $val->category_slug, $val->post_slug]); ?>" class="--xhr">
                                <img src="<?= get_image('blogs', $val->featured_image, 'thumb'); ?>" class="img-fluid rounded-4 w-100 bg-white" alt="<?= $val->post_title; ?>" style="aspect-ratio: 3/2; object-fit: cover;margin-top:-1.75rem">
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <?= pagination($pagination); ?>
        <?php else: ?>
            <div class="row">
                <div class="col-lg-8 offset-lg-2">
                    <div class="py-5">
                        <div class="text-center">
                            <img src="<?= base_url('assets/yao-ming.png'); ?>" width="128" alt="404" />
                        </div>
                        <h2 class="text-center">
                            <?= phrase('No category is found!'); ?>
                        </h2>
                        <p class="lead text-center">
                            <?= phrase('The category of post you requested was not found or it\'s been archived.'); ?>
                        </p>
                        <p class="text-center">
                            <a href="<?= current_page('../'); ?>" class="btn btn-outline-dark rounded-pill px-5 --xhr">
                                <i class="mdi mdi-arrow-left"></i> <?= phrase('Back to News'); ?>
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>
