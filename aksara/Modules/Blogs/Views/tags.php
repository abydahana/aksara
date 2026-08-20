<?php

/**
 * @var mixed $meta
 * @var mixed $results
 * @var mixed $pagination
 */
?>

<section class="section-padding border-fade-bottom fade-in">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 text-center text-md-start">
                <h1 class="display-5 fw-bold">
                    <?= $meta->title ?>
                </h1>
                <p class="fs-5">
                    <?= $meta->description ?>
                </p>
            </div>
            <div class="col-md-6">
                <form action="<?= base_url('blogs/tags', ['page' => null]) ?>" method="GET" class="my-5">
                    <div class="d-flex g-3 rounded-pill border border-light-subtle p-1">
                        <div class="input-group ps-4">
                            <i class="mdi mdi-magnify mdi-2x text-muted"></i>
                            <input type="text" name="q" class="form-control form-control-lg fw-light border-0 bg-transparent" value="<?= htmlspecialchars($keywords ?? '') ?>" placeholder="<?= phrase('Search posts...') ?>" required>
                            <button type="submit" class="btn btn-primary btn-lg fw-light rounded-pill px-4">
                                <?= phrase('Search') ?> <i class="mdi mdi-arrow-right"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<?php if ($results): ?>
    <section class="section-padding">
        <div class="container">
            <div class="row">
                <?php foreach ($results as $key => $val): ?>
                    <?php
                    $itemTags = array_map('trim', explode(',', $val->post_tags));
                    $tags = null;

                    if (sizeof($itemTags) > 0) {
                        foreach ($itemTags as $label => $badge) {
                            if (2 == $label) {
                                break;
                            }

                            if ($badge) {
                                $tags .= '
                                    <a href="' . go_to('../tags', ['q' => $badge]) . '" class="--xhr">
                                        <span class="badge bg-secondary me-2">#' . trim($badge) . '</span>
                                    </a>
                                ';
                            }
                        }
                    } ?>

                    <div class="col-sm-6 col-lg-4 mb-3 mb-lg-4">
                        <div class="h-100 d-flex flex-column fade-in">
                            <div class="d-flex flex-column flex-grow-1 border border-hover p-3 pb-0 rounded-5">
                                <div class="d-flex g-0 align-items-end mb-3">
                                    <div class="pe-2">
                                        <a href="<?= base_url('user/' . $val->username) ?>" class="text-sm text-secondary d-block --xhr">
                                            <img src="<?= get_image('users', $val->photo, 'icon') ?>" class="img-fluid rounded-circle user-avatar" alt="<?= $val->first_name . ' ' . $val->last_name ?>" />
                                        </a>
                                    </div>
                                    <div class="flex-grow-1 d-flex justify-content-between align-items-end overflow-hidden gap-0">
                                        <div>
                                            <p class="m-0">
                                                <a href="<?= base_url('user/' . $val->username) ?>" class="text-body ps-2 text-decoration-none --xhr">
                                                    <b class="fs-5"><?= $val->first_name . ' ' . $val->last_name ?></b>
                                                </a>
                                            </p>
                                            <p class="m-0 lh-1">
                                                <a href="<?= base_url('user/' . $val->username) ?>" class="text-body ps-2 text-decoration-none --xhr">
                                                    <span class="small text-secondary">@<?= $val->username ?></span>
                                                </a>
                                            </p>
                                        </div>
                                        <span class="text-muted small"><i class="mdi mdi-clock-outline"></i> <?= time_ago($val->updated_at ?? $val->created_at) ?></span>
                                    </div>
                                </div>
                                <div class="d-flex flex-column justify-content-between gap-3">
                                    <div class="flex-grow-1">
                                        <h2 class="h5" class="mb-3">
                                            <a href="<?= base_url(['blogs', $val->category_slug, $val->post_slug]) ?>" class="text-body text-decoration-none --xhr">
                                                <?= truncate($val->post_title, 120) ?>
                                            </a>
                                        </h2>
                                        <p class="text-muted">
                                            <?= truncate($val->post_excerpt, 120) ?>
                                        </p>
                                        <div style="z-index:1">
                                            <?= $tags ?>
                                        </div>
                                    </div>
                                    <div style="margin-inline:-1rem">
                                        <a href="<?= base_url(['blogs', $val->category_slug, $val->post_slug]) ?>" class="d-block --xhr">
                                            <img src="<?= get_image('blogs', $val->featured_image, 'thumb') ?>" class="img-fluid w-100 bg-body-tertiary rounded-5" alt="<?= $val->post_title ?>" style="aspect-ratio: 3/2; object-fit: cover;">
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <?= pagination($pagination) ?>
        </div>
    </section>
<?php else: ?>
    <section class="section-padding fade-in">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 offset-lg-3 text-center">
                    <div class="text-center">
                        <i class="mdi mdi-dropbox display-1 text-muted"></i>
                    </div>
                    <h2 class="text-center">
                        <?= phrase('No post is found!') ?>
                    </h2>
                    <p class="fs-5">
                        <?= phrase('Your tag search does not match any result.') ?>
                    </p>
                    <div class="text-center mt-5">
                        <a href="<?= go_to('../', ['q' => null]) ?>" class="btn btn-outline-primary rounded-pill px-4 --xhr">
                            <i class="mdi mdi-arrow-left"></i> <?= phrase('Back to Index') ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php endif; ?>
