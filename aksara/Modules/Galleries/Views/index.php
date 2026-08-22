<?php

/**
 * @var mixed $results
 * @var mixed $meta
 * @var mixed $pagination
 */
if ($results): ?>
    <section class="py-5 fade-in">
        <div class="container text-center text-md-start">
            <div class="row align-items-end">
                <div class="col-lg-7">
                    <h1 class="display-4 fw-bold">
                        <?= $meta->title ?>
                    </h1>
                    <p class="fs-5 text-muted mb-0">
                        <?= truncate($meta->description, 256) ?>
                    </p>
                </div>
                <div class="col-lg-5">
                    <form action="<?= go_to(null, ['page' => null]) ?>" method="GET">
                        <div class="d-flex g-3 rounded-pill border border-light-subtle p-1">
                            <div class="input-group ps-4">
                                <i class="mdi mdi-magnify mdi-2x text-muted"></i>
                                <input type="text" name="q" class="form-control form-control-lg fw-light border-0 bg-transparent" value="<?= htmlspecialchars(service('request')->getGet('q') ?? '') ?>" placeholder="<?= phrase('Search albums...') ?>">
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
<?php endif; ?>

<section class="py-5">
    <div class="container">
        <?php if ($results): ?>
            <div class="row">
                <?php foreach ($results as $key => $val): ?>
                    <?php
                    $cover = null;
                    $thumbnail = null;
                    $images = json_decode($val->gallery_images, true);

                    if (! empty($images)) {
                        $num = 1;

                        ob_start();

                        foreach ($images as $src => $alt) {
                            if ($num >= 4) {
                                break;
                            }

                            if (1 == $num) {
                                $cover = $src;
                            } elseif ($num > 1) { ?>
                                <a href="<?= go_to([$val->gallery_slug, $src]) ?>" class="d-block --xhr">
                                    <img src="<?= get_image('galleries', $src, 'thumb') ?>" class="w-100" alt="<?= htmlspecialchars((string) ($alt ?: $val->gallery_title)) ?>" loading="lazy" decoding="async" />
                                </a>
                                <?php
                            }

                            $num++;
                        }

                        $thumbnail = ob_get_clean();
                    } ?>

                    <div class="col-lg-6">
                        <div class="rounded-5 border-hover overflow-hidden mb-4 fade-in">
                            <div class="row g-0">
                                <div class="col-<?= (count($images) <= 2 ? 'md-' : null) . (count($images) == 2 ? 6 : (count($images) == 1 ? 12 : 8)) ?> text-center d-flex align-items-end" style="background:url(<?= get_image(
                                    'galleries',
                                    $cover,
                                ) ?>) center center no-repeat; background-size:cover; min-height:min(360px, 50vh)">
                                    <div class="p-3 m-3 rounded-5 w-100" style="background:rgba(0, 0, 0, .5)">
                                        <h2 class="h4 text-light">
                                            <span class="badge bg-primary float-end">
                                                <?= count($images) ?>
                                            </span> <?= $val->gallery_title ?>
                                        </h4>
                                        <p class="text-light">
                                            <?= truncate($val->gallery_description, 160) ?>
                                        </p>
                                        <p class="text-light">
                                            <?php if (count($images) > 4): ?>
                                                <a href="<?= go_to($val->gallery_slug) ?>" class="btn btn-outline-light rounded-pill --xhr">
                                                    <i class="mdi mdi-folder-multiple-image"></i> <?= phrase('Show all') ?>
                                                </a> <?php else: ?>
                                                <a href="<?= go_to([$val->gallery_slug, $cover]) ?>" class="btn btn-outline-light rounded-pill px-4 --xhr">
                                                    <i class="mdi mdi-magnify-plus"></i> <?= phrase('Show') ?>
                                                </a>
                                            <?php endif; ?>
                                        </p>
                                    </div>
                                </div>

                                <?php if (count($images) > 1): ?>
                                    <div class="col-<?= (count($images) <= 2 ? 'md-' : null) . (count($images) > 2 ? 4 : 6) ?> bg-body-tertiary d-flex align-items-center">
                                        <div class="w-100">
                                        <?= $thumbnail ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <?= pagination($pagination) ?>
        <?php else: ?>
            <div class="row">
                <div class="col-lg-6 offset-lg-3">
                    <?= view('templates/404', [...(array) $meta, 'searchLabel' => phrase('Search albums...')]) ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>
