<?php

$hour = floor(SESSION_EXPIRATION / 3600);
?>

<div class="container-fluid py-3">
    <div class="row">
        <div class="col-lg-7">
            <div class="alert alert-warning rounded-4 callout">
                <h3><?= phrase('Session Cleaner') ?></h3>

                <p><?= phrase('You are about to clean up unused session garbage.') . ' ' . phrase('All inactive session within {{hour}} hours will be removed.', ['hour' => $hour]) . ' ' . phrase('This action cannot be undone.') ?></p>
                <a href="<?= go_to('session') ?>" class="btn btn-danger btn-sm px-5 rounded-pill --xhr show-progress">
                    <i class="mdi mdi-check"></i> <?= phrase('Click to continue') ?>
                </a>
            </div>
        </div>
        <div class="col-lg-7">
            <div class="alert alert-info rounded-4 callout">
                <h3><?= phrase('Cache Cleaner') ?></h3>

                <p><?= phrase('You are about to clear the application cache.') . ' ' . phrase('All cached application data will be removed.') . ' ' . phrase('This action cannot be undone.') ?></p>
                <a href="<?= go_to('cache') ?>" class="btn btn-info btn-sm px-5 rounded-pill --xhr show-progress">
                    <i class="mdi mdi-check"></i> <?= phrase('Click to continue') ?>
                </a>
            </div>
        </div>
    </div>
</div>
