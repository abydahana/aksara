<?php
/**
 * @var mixed $meta
 * @var mixed $results
 * @var mixed $pagination
 */
?>
<div class="section-padding fade-in">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 text-center text-md-start">
                <h1 class="display-5 fw-bold">
                    <?= $meta->title; ?>
                </h1>
                <p class="lead">
                    <?= $meta->description; ?>
                </p>
            </div>
            <div class="col-md-6">
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
</div>

<?php if ($results): ?>
    <div class="section-padding fade-in">
        <div class="container">
            <div class="row">
                <?php foreach ($results as $key => $val): ?>
                    <div class="col-sm-6 col-lg-4 mb-5">
                        <div class="h-100 d-flex flex-column">
                            <a href="<?= base_url(['blogs', $val->category_slug, $val->post_slug]); ?>" class="--xhr">
                                <img src="<?= get_image('blogs', $val->featured_image, 'thumb'); ?>" class="img-fluid rounded-4 w-100" alt="<?= $val->post_title; ?>" style="aspect-ratio: 3/2; object-fit: cover;">
                            </a>
                            <div class="px-0 pt-3 d-flex flex-column flex-grow-1">
                                <p class="text-muted small fw-semibold mb-2">
                                    <i class="mdi mdi-clock-outline"></i> <?= time_ago($val->updated_timestamp); ?>
                                </p>
                                <h5 class="fw-bold mb-2" style="letter-spacing: -0.01em;">
                                    <a href="<?= base_url(['blogs', $val->category_slug, $val->post_slug]); ?>" class="text-dark text-decoration-none --xhr">
                                        <?= truncate($val->post_title, 64); ?>
                                    </a>
                                </h5>
                                <p class="text-muted small mb-3 lh-lg">
                                    <?= truncate($val->post_excerpt, 80); ?>
                                </p>
                                <div class="row g-0 align-items-center mt-auto">
                                    <div class="col-2 col-sm-2 col-md-1">
                                        <a href="<?= base_url('user/' . $val->username); ?>" class="text-sm text-secondary --xhr">
                                            <img src="<?= get_image('users', $val->photo, 'icon'); ?>" class="img-fluid rounded-circle" alt="..." />
                                        </a>
                                    </div>
                                    <div class="col-7 col-sm-7 col-md-8 overflow-hidden">
                                        <a href="<?= base_url('user/' . $val->username); ?>" class="text-dark ps-2 text-decoration-none --xhr">
                                            <b>
                                                <?= $val->first_name . ' ' . $val->last_name; ?>
                                            </b>
                                        </a>
                                    </div>
                                    <div class="col-3 col-sm-3 col-md-3 text-end">
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

            <?= pagination($pagination); ?>
        </div>
    </div>
<?php else: ?>
    <div class="section-padding fade-in">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 offset-lg-3 text-center">
                    <div class="py-5">
                        <div class="text-center">
                            <i class="mdi mdi-dropbox mdi-5x text-muted"></i>
                        </div>
                        <h2 class="text-center">
                            <?= phrase('No post is found!'); ?>
                        </h2>
                        <p class="lead">
                            <?= phrase('Your tag search does not match any result.'); ?>
                        </p>
                        <div class="text-center mt-5">
                            <a href="<?= go_to('../', ['q' => null]); ?>" class="btn btn-outline-primary rounded-pill --xhr">
                                <i class="mdi mdi-arrow-left"></i>
                                <?= phrase('Back to Index'); ?>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
