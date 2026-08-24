<?php

/**
 * @var mixed $detail
 */
$carousel = null;
$attribution = null;

if ($detail->screenshot) {
    ob_start();

    foreach ($detail->screenshot as $key => $val): ?>
        <div class="carousel-item h-100 text-center rounded-4<?= ! $key ? ' active' : null ?>">
            <a href="<?= $val->src ?>" target="_blank" class="d-block h-100">
                <img src="<?= $val->src ?>" class="d-block w-100 h-100 object-fit-cover rounded-4" alt="<?= $val->alt ?>">
            </a>
        </div>
    <?php endforeach;

    $carousel = ob_get_clean();
}

if (isset($detail->attribution) && $detail->attribution) {
    ob_start();

    foreach ($detail->attribution as $key => $val): ?>
        <div class="row">
            <div class="col-4 text-muted">
                <?= $key ?>
            </div>
            <div class="col-8">
                <?= $val ?>
            </div>
        </div>
    <?php endforeach;

    $attribution = ob_get_clean();
} ?>

<div class="container-fluid py-3">
    <div class="row">
        <div class="col-md-6 col-lg-7">
            <div class="sticky-lg-top" style="top:6rem">
                <div class="position-relative ratio ratio-4x3 rounded-4 bg-dark overflow-hidden">
                    <div id="carouselExampleControls" class="carousel slide h-100" data-bs-ride="carousel">
                        <div class="carousel-inner h-100">
                            <?= $carousel ?>
                        </div>

                        <?php if (sizeof($detail->screenshot) > 1): ?>
                            <a class="carousel-control-prev gradient-right" href="#carouselExampleControls" role="button" data-bs-slide="prev" aria-label="<?= phrase('Previous') ?>">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            </a>
                            <a class="carousel-control-next gradient-left" href="#carouselExampleControls" role="button" data-bs-slide="next" aria-label="<?= phrase('Next') ?>">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-5">
            <div class="sticky-lg-top" style="top:6rem">
                <h5>
                    <?= $detail->name ?>
                    <?= 'backend' == $detail->type
                    ? '<span class="badge bg-dark float-end">' . phrase('Backend Theme') . '</span>'
                    : ('frontend' == $detail->type
                        ? '<span class="badge bg-success float-end">' . phrase('Frontend Theme') . '</span>'
                        : '<span class="badge bg-primary float-end">' . phrase('Module') . '</span>') ?>
                </h5>
                <div class="mb-3">
                    <?= nl2br($detail->description) ?>
                </div>
                <div class="row mb-2">
                    <div class="col-4 text-muted d-flex align-items-center">
                        <?= phrase('Author') ?>
                    </div>
                    <div class="col-8 d-flex align-items-center">
                        <?php if (isset($detail->publisher_photo) && $detail->publisher_photo): ?>
                            <img src="<?= $detail->publisher_photo ?>" class="rounded-circle me-2" style="width:24px; height:24px; object-fit:cover" alt="<?= htmlspecialchars($detail->publisher ?? $detail->author) ?>" />
                        <?php endif; ?>

                        <?php if (isset($detail->publisher_url) && $detail->publisher_url): ?>
                            <a href="<?= $detail->publisher_url ?>" target="_blank" class="fw-bold text-decoration-none">
                                <?= htmlspecialchars($detail->publisher ?? $detail->author) ?>
                            </a>
                        <?php elseif (isset($detail->website) && $detail->website): ?>
                            <a href="<?= $detail->website ?>" target="_blank" class="fw-bold text-decoration-none">
                                <?= htmlspecialchars($detail->author) ?>
                            </a>
                        <?php else: ?>
                            <b><?= htmlspecialchars($detail->author) ?></b>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-4 text-muted">
                        <?= phrase('Version') ?>
                    </div>
                    <div class="col-8">
                        <?= $detail->version ?>
                    </div>
                </div>
                <?php
                $compatibilityList = [];
                $rawCompatibility = $detail->compatibility ?? $detail->compatible_version ?? $detail->min_version ?? null;

                if ($rawCompatibility) {
                    if (is_array($rawCompatibility)) {
                        $compatibilityList = $rawCompatibility;
                    } elseif (is_string($rawCompatibility)) {
                        $decoded = json_decode($rawCompatibility, true);
                        if (is_array($decoded)) {
                            $compatibilityList = $decoded;
                        } else {
                            $compatibilityList = [$rawCompatibility];
                        }
                    }
                }

                $lastUpdate = $detail->last_update ?? $detail->updated_at ?? $detail->updated ?? $detail->created_at ?? null;
                ?>
                <?php if ($compatibilityList): ?>
                <div class="row mb-2">
                    <div class="col-4 text-muted">
                        <?= phrase('Compatibility') ?>
                    </div>
                    <div class="col-8">
                        <?php foreach ($compatibilityList as $ver): ?>
                            <span class="badge bg-secondary me-1"><?= htmlspecialchars($ver) ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <?php if ($lastUpdate): ?>
                <div class="row mb-2">
                    <div class="col-4 text-muted">
                        <?= phrase('Last Update') ?>
                    </div>
                    <div class="col-8">
                        <?= date('d M Y', is_numeric($lastUpdate) ? (int) $lastUpdate : strtotime($lastUpdate)) ?>
                    </div>
                </div>
                <?php endif; ?>

                <div class="row mb-2">
                    <div class="col-4 text-muted">
                        <?= phrase('Rating') ?>
                    </div>
                    <div class="col-8">
                        <?php
                        $rating = isset($detail->rating) ? (float) $detail->rating : 0;
                        $ratingCount = isset($detail->rating_count) ? (int) $detail->rating_count : 0;

                        for ($i = 1; $i <= 5; $i++) {
                            if ($rating >= $i) {
                                echo '<i class="mdi mdi-star text-warning"></i>';
                            } elseif ($rating >= ($i - 0.5)) {
                                echo '<i class="mdi mdi-star-half-full text-warning"></i>';
                            } else {
                                echo '<i class="mdi mdi-star-outline text-muted"></i>';
                            }
                        } ?>

                        <b class="ms-1"><?= number_format($rating, 1) ?></b>
                        <span class="text-muted">(<?= number_format($ratingCount) ?>)</span>
                    </div>
                </div>

                <?= $attribution ?>

                <?php if (isset($detail->reviews) && $detail->reviews): ?>
                    <hr class="mt-3 mb-3" />
                    <div class="mb-0">
                        <b class="d-block mb-3"><?= phrase('User Reviews') ?></b>
                        <div class="pe-1">
                            <?php foreach ($detail->reviews as $review): ?>
                                <div class="mb-3 pb-2 border-bottom">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <div class="d-flex align-items-center">
                                            <?php if (isset($review->user_photo) && $review->user_photo): ?>
                                                <img src="<?= $review->user_photo ?>" class="rounded-circle user-avatar me-2" alt="<?= htmlspecialchars($review->user_name) ?>" />
                                            <?php endif; ?>

                                            <div>
                                                <?php if (isset($review->profile_url) && $review->profile_url): ?>
                                                    <a href="<?= $review->profile_url ?>" target="_blank" class="fw-bold text-decoration-none text-body">
                                                        <?= htmlspecialchars($review->user_name) ?>
                                                    </a>
                                                <?php else: ?>
                                                    <span class="fw-bold"><?= htmlspecialchars($review->user_name) ?></span>
                                                <?php endif; ?>

                                                <?php if (isset($review->created_at) && $review->created_at): ?>
                                                    <div class="text-muted small"><?= time_ago($review->created_at) ?></div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <span class="text-warning">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <i class="mdi mdi-star<?= $review->rating >= $i ? '' : '-outline text-muted' ?>"></i>
                                            <?php endfor; ?>
                                        </span>
                                    </div>
                                    <?php if (isset($review->comment) && $review->comment): ?>
                                        <div class="text-body my-1"><?= nl2br(htmlspecialchars($review->comment)) ?></div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <?php if (isset($detail->rating_count) && $detail->rating_count > 3 && isset($detail->detail_url) && $detail->detail_url): ?>
                            <a href="<?= $detail->detail_url ?>" class="btn btn-outline-primary btn-sm rounded-pill w-100 mt-2" target="_blank">
                                <?= phrase('Show all reviews') ?>
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <hr style="margin-inline: -1rem" />
    <div class="row">
        <div class="col-md-6 offset-md-6 col-lg-5 offset-lg-7">
            <div class="row g-2">
                <div class="col-sm-6">
                    <div class="d-grid">
                        <a href="<?= current_page('../install', [
                          'item' => $detail->path,
                          'type' => $detail->addon_type,
                        ]) ?>" class="btn btn-primary btn-sm rounded-pill --keep-modal show-progress">
                            <i class="mdi mdi-plus"></i> <?= phrase('Install') ?>
                        </a>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="d-grid">
                        <a href="<?= $detail->demo_url ?>" class="btn btn-outline-primary btn-sm rounded-pill" target="_blank">
                            <i class="mdi mdi-magnify"></i> <?= phrase('Preview') ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
