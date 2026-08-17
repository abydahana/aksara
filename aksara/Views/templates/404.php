<?php
/**
 * @var object $meta
 */
?>

<div class="fade-in">
    <div class="text-center mb-3">
        <h1 class="display-1 lh-1 text-muted">
            404
        </h1>
        <i class="mdi mdi-dropbox display-1 text-muted"></i>
    </div>
    <h2 class="text-center">
        <?= $meta->title ?>
    </h2>
    <p class="fs-5 text-center">
        <?= $meta->description ?>
    </p>
    <form action="<?= $searchAction ?? go_to(null, ['page' => null]) ?>" method="GET">
        <div class="d-flex g-3 rounded-pill border border-light-subtle p-1">
            <div class="input-group ps-4">
                <i class="mdi mdi-magnify mdi-2x text-muted"></i>
                <input type="text" name="q" class="form-control form-control-lg fw-light border-0 bg-transparent" value="<?= htmlspecialchars(service('request')->getGet('q') ?? '') ?>" placeholder="<?= $searchLabel ??
  phrase('Search something...') ?>">
                <button type="submit" class="btn btn-primary btn-lg fw-light rounded-pill px-4">
                    <?= phrase('Search') ?> <i class="mdi mdi-arrow-right"></i>
                </button>
            </div>
        </div>
    </form>
</div>
