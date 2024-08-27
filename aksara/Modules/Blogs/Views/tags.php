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
            </div>
            <div class="col-md-6">
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
</div>

<?php if ($results): ?>
    <div class="py-3 py-md-5 bg-light">
        <div class="container">
            <div class="row">
                <?php foreach ($results as $key => $val): ?>
                    <div class="col-sm-6 col-md-4 col-lg-3">
                        <div class="card mb-4 border-0 rounded-4 overflow-hidden">
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

            <?= pagination($pagination); ?>
        </div>
    </div>
<?php else: ?>
    <div class="py-3 py-md-5">
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
