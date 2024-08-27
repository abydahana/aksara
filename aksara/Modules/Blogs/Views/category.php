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
                <div class="row">
                    <div class="col-lg-8">
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
            <div class="col-lg-4 offset-lg-2">
                <div class="d-none d-lg-block">
                    <?php if($category): ?>
                        <img src="<?= get_image('blogs', $category->category_image); ?>" class="img-fluid rounded-4" alt="..." />
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if ($results): ?>
    <div class="py-3 py-md-5 bg-light">
        <div class="container">
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
                    <div class="col-sm-12 col-lg-6">
                        <div class="card border-0 rounded-4 mb-3">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-3">
                                        <a href="<?= base_url(['blogs', $val->category_slug, $val->post_slug]); ?>" class="--xhr">
                                            <img src="<?= get_image('blogs', $val->featured_image, 'thumb'); ?>" class="img-fluid rounded-4" alt="..." />
                                        </a>
                                    </div>
                                    <div class="col-9">
                                        <a href="<?= base_url(['blogs', $val->category_slug, $val->post_slug]); ?>" class="--xhr">
                                            <h4 class="card-title">
                                                <?= $val->post_title; ?>
                                            </h4>
                                        </a>
                                        <p class="d-none d-md-block text-muted">
                                            <?= truncate($val->post_excerpt, 100); ?>
                                        </p>
                                        <div class="row g-0 align-items-center">
                                            <div class="col-2 col-sm-1">
                                                <a href="<?= base_url('user/' . $val->username); ?>" class="text-sm text-secondary --xhr">
                                                    <img src="<?= get_image('users', $val->photo, 'icon'); ?>" class="img-fluid rounded-circle" alt="..." />
                                                </a>
                                            </div>
                                            <div class="col-7 col-sm-8">
                                                <p class="ps-2 m-0 text-muted">
                                                    <a href="<?= base_url('user/' . $val->username); ?>" class="text-dark --xhr">
                                                        <b>
                                                            <?= $val->first_name . ' ' . $val->last_name; ?>
                                                        </b>
                                                    </a>
                                                </p>
                                                <p class="ps-2 m-0 text-sm text-muted">
                                                    <i class="mdi mdi-clock-outline"></i> <?= time_ago($val->updated_timestamp); ?>
                                                </p>
                                            </div>
                                            <div class="col-3 col-sm-3 text-end">
                                                <button type="button" class="btn btn-sm rounded-pill --modify <?= (is_liked($val->post_id, 'blogs/' . $val->category_slug . '/' . $val->post_slug) ? 'btn-danger' : 'btn-outline-danger'); ?>" data-href="<?= base_url('xhr/widget/comment/repute', ['post_id' => $val->post_id, 'path' => 'blogs/' . $val->category_slug . '/' . $val->post_slug]); ?>" data-class-add="btn-danger" data-class-remove="btn-outline-danger">
                                                    <i class="mdi mdi-heart"></i>
                                                </button>
                                            </div>
                                        </div>
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
    <div class="py-3 py-md-5 bg-light">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 offset-lg-3 text-center">
                    <div class="py-5">
                        <i class="mdi mdi-dropbox mdi-5x text-muted"></i>
                        <h2>
                            <?= phrase('No post is available under this category'); ?>
                        </h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
