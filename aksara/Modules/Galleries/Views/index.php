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
            <div class="row row-cols-1 row-cols-lg-2 g-4">
                <?php foreach ($results as $key => $val): ?>
                    <?php
                    $cover = null;
                    $thumbs = [];
                    $images = json_decode($val->gallery_images, true);

                    if (! empty($images) && is_array($images)) {
                        $cover = array_key_first($images);
                        $thumbs = array_slice($images, 1, 3, true);
                    }

                    $totalImages = count($images ?? []);
                    $extraCount = $totalImages - 4;
                    $thumbCount = count($thumbs);
                    $coverCol = 1 === $thumbCount ? '7' : ($thumbCount > 1 ? '8' : '12');
                    $thumbCol = 1 === $thumbCount ? '5' : ($thumbCount > 1 ? '4' : '0');
                    ?>
                    <div class="col">
                        <div class="card bg-transparent h-100 border-0 overflow-hidden">
                            <div class="row g-2 h-100" style="min-height: 380px;">
                                <!-- Cover Image & Info -->
                                <div class="col-<?= $coverCol ?> d-flex">
                                    <div class="w-100 h-100 position-relative d-flex align-items-end rounded-4 overflow-hidden" style="background: url('<?= get_image('galleries', $cover) ?>') center center / cover no-repeat; min-height: 380px;">
                                        <!-- Gradient Overlay -->
                                        <div class="position-absolute top-0 start-0 w-100 h-100" style="background: linear-gradient(to top, rgba(0, 0, 0, 0.85) 0%, rgba(0, 0, 0, 0.35) 50%, rgba(0, 0, 0, 0.1) 100%);"></div>

                                        <!-- Badge Count -->
                                        <div class="position-absolute top-0 start-0 p-3 z-1">
                                            <span class="badge bg-primary bg-opacity-90 backdrop-blur rounded-pill px-3 py-2 shadow-sm">
                                                <i class="mdi mdi-image-multiple me-1"></i> <?= $totalImages ?> <?= ($totalImages > 1 ? phrase('Photos') : phrase('Photo')) ?>
                                            </span>
                                        </div>

                                        <!-- Info Content -->
                                        <div class="position-relative z-1 p-3 p-sm-4 w-100 text-light">
                                            <h3 class="h4 fw-bold mb-2">
                                                <a href="<?= go_to($val->gallery_slug) ?>" class="text-white text-decoration-none --xhr">
                                                    <?= $val->gallery_title ?>
                                                </a>
                                            </h3>
                                            <?php if ($val->gallery_description): ?>
                                                <p class="text-white-50 small mb-3" style="display: -webkit-box; line-clamp: 2; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                                    <?= truncate($val->gallery_description, 120) ?>
                                                </p>
                                            <?php endif; ?>

                                            <div>
                                                <a href="<?= go_to($val->gallery_slug) ?>" class="btn btn-sm btn-light rounded-pill px-3 shadow-sm --xhr fw-medium">
                                                    <?= phrase('View Album') ?> <i class="mdi mdi-arrow-right ms-1"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Bento Thumbnails Column -->
                                <?php if (! empty($thumbs)): ?>
                                    <div class="col-<?= $thumbCol ?> d-flex flex-column gap-2 h-100">
                                        <?php
                                        $thumbCount = count($thumbs);
                                        $i = 0;
                                        foreach ($thumbs as $src => $alt):
                                            $i++;
                                            $isLast = ($i === $thumbCount);
                                        ?>
                                            <a href="<?= go_to([$val->gallery_slug, $src]) ?>" class="d-block flex-fill position-relative rounded-4 overflow-hidden --xhr" style="height: 0;">
                                                <img src="<?= get_image('galleries', $src, 'thumb') ?>" class="w-100 h-100 object-fit-cover" alt="<?= htmlspecialchars((string) ($alt ?: $val->gallery_title)) ?>" loading="lazy" decoding="async" />
                                                <?php if ($isLast && $extraCount > 0): ?>
                                                    <div class="position-absolute top-0 start-0 w-100 h-100 bg-dark bg-opacity-60 backdrop-blur d-flex align-items-center justify-content-center text-white fw-bold fs-5">
                                                        +<?= $extraCount ?>
                                                    </div>
                                                <?php endif; ?>
                                            </a>
                                        <?php endforeach; ?>
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
