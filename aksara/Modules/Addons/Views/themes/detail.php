<?php

/**
 * @var mixed $detail
 */
$carousel = null;
$attribution = null;

if (isset($detail->screenshot) && $detail->screenshot) {
    ob_start();

    foreach ($detail->screenshot as $key => $val) {
        if (file_exists(ROOTPATH . 'themes' . DIRECTORY_SEPARATOR . $detail->folder . DIRECTORY_SEPARATOR . str_replace(['../', '..\\', './', '.\\'], '', $val->src))) {
            $screenshot = base_url('themes/' . $detail->folder . '/' . str_replace(['../', '..\\', './', '.\\'], '', $val->src));
        } else {
            $screenshot = get_image(null, 'placeholder_thumb.png');
        } ?>

        <div class="carousel-item h-100 text-center rounded-4<?= ! $key ? ' active' : null ?>">
            <a href="<?= $screenshot ?>" target="_blank" class="d-block h-100">
                <img src="<?= $screenshot ?>" class="d-block w-100 h-100 object-fit-cover rounded-4" alt="<?= $val->alt ?>">
            </a>
        </div>
    <?php
    }

    $carousel = ob_get_clean();
}

if (isset($detail->attribution) && $detail->attribution) {
    ob_start();

    foreach ($detail->attribution as $key => $val) { ?>
        <div class="row">
            <div class="col-4 text-muted">
                <?= $key ?>
            </div>
            <div class="col-8">
                <?= $val ?>
            </div>
        </div>
    <?php
    }

    $attribution = ob_get_clean();
} ?>

<div>
    <div class="row">
        <div class="col-md-6 col-lg-7">
            <div class="position-relative ratio ratio-4x3 rounded-4 bg-dark overflow-hidden">
                <div id="carouselExampleControls" class="carousel slide h-100" data-bs-ride="carousel">
                    <div class="carousel-inner h-100">
                        <?= $carousel ?>
                    </div>

                    <?php if (sizeof($detail->screenshot) > 1) { ?>
                        <a class="carousel-control-prev gradient-right" href="#carouselExampleControls" role="button" data-bs-slide="prev" aria-label="<?= phrase('Previous') ?>">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        </a>
                        <a class="carousel-control-next gradient-left" href="#carouselExampleControls" role="button" data-bs-slide="next" aria-label="<?= phrase('Next') ?>">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        </a>
                    <?php } ?>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-5">
            <h5>
                <?= $detail->name ?>
                <?= 'backend' == $detail->type ? '<span class="badge bg-dark float-end">' . phrase('Backend Theme') . '</span>' : '<span class="badge bg-success float-end">' . phrase('Frontend Theme') . '</span>' ?>
            </h5>
            <div class="mb-3">
                <?= nl2br($detail->description) ?>
            </div>
            <div class="row mb-2">
                <div class="col-4 text-muted">
                    <?= phrase('Author') ?>
                </div>
                <div class="col-8">
                    <?= isset($detail->website) ? '<a href="' . $detail->website . '" target="_blank"><b>' . $detail->author . '</b></a>' : '<b>' . $detail->author . '</b>' ?>
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

            <?php if (isset($detail->rating) || isset($detail->rating_count)): ?>
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
                    }
                    ?>
                    <b class="ms-1"><?= number_format($rating, 1) ?></b>
                    <span class="text-muted">(<?= number_format($ratingCount) ?>)</span>
                </div>
            </div>
            <?php endif; ?>

            <?= $attribution ?>
        </div>
    </div>
    <hr style="margin-inline: -1rem" />
    <div class="row">
        <div class="col-md-6 col-lg-7">
            <a href="<?= current_page('../update', ['item' => $detail->folder]) ?>" class="btn btn-outline-success btn-sm rounded-pill px-4 --modal">
                <i class="mdi mdi-auto-fix"></i> <?= phrase('Update') ?>
            </a>
        </div>
        <div class="col-md-6 col-lg-5">
            <div class="row g-2">
                <div class="col-sm-4">
                    <div class="d-grid">
                        <?php if (('backend' == $detail->type && get_setting('backend_theme') == $detail->folder) || ('frontend' == $detail->type && get_setting('frontend_theme') == $detail->folder)): ?>
                            <button type="button" class="btn btn-dark btn-sm rounded-pill" disabled>
                                <i class="mdi mdi-check"></i> <?= phrase('Active') ?>
                            </button> <?php else: ?>
                            <a href="<?= current_page('../activate') ?>" class="btn btn-success btn-sm rounded-pill --modal">
                                <i class="mdi mdi-check"></i> <?= phrase('Activate') ?>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="d-grid">
                        <a href="<?= base_url('backend' == $detail->type ? 'dashboard' : null, [
                          'aksara_mode' => 'preview-theme',
                          'aksara_theme' => $detail->folder,
                          'integrity_check' => $detail->integrity,
                        ]) ?>" class="btn btn-outline-primary btn-sm rounded-pill" target="_blank">
                            <i class="mdi mdi-magnify"></i> <?= phrase('Preview') ?>
                        </a>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="d-grid">
                        <a href="<?= current_page('../delete', [
                          'item' => $detail->folder,
                        ]) ?>" class="btn btn-outline-danger btn-sm rounded-pill --modal">
                            <i class="mdi mdi-window-close"></i> <?= phrase('Delete') ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
