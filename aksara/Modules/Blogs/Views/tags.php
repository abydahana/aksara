<?php
/**
 * @var mixed $meta
 * @var mixed $results
 * @var mixed $pagination
 */
?>
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
</section>

<?php if ($results): ?>
    <section class="section-padding fade-in">
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
                    <div class="col-sm-6 col-lg-4 mb-3 mb-lg-4">
                        <div class="h-100 d-flex flex-column">
                            <div class="d-flex flex-column flex-grow-1 border p-3 rounded-top-4">
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
                                <h5 class="mb-3">
                                    <a href="<?= base_url(['blogs', $val->category_slug, $val->post_slug]); ?>" class="text-dark text-decoration-none --xhr">
                                        <?= truncate($val->post_title, 120); ?>
                                    </a>
                                </h5>
                                <div style="z-index:1">
                                    <?= $tags; ?>
                                </div>
                            </div>
                            <a href="<?= base_url(['blogs', $val->category_slug, $val->post_slug]); ?>" class="--xhr">
                                <img src="<?= get_image('blogs', $val->featured_image, 'thumb'); ?>" class="img-fluid w-100 bg-white rounded-bottom-4" alt="<?= $val->post_title; ?>" style="aspect-ratio: 3/2; object-fit: cover">
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <?= pagination($pagination); ?>
        </div>
    </section>
<?php else: ?>
    <section class="section-padding fade-in">
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
                        <p class="fs-5">
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
    </section>
<?php endif; ?>
