<?php
    $hour = floor(SESSION_EXPIRATION / 3600);
?>
<div class="container-fluid py-3">
    <div class="row">
        <div class="col-lg-7">
            <div class="alert alert-warning rounded-4 callout">
                <p>
                    <h3><?= phrase('Information'); ?></h3>
                    <?= phrase('You are about to cleaning up unused session garbage.') . ' ' . phrase('All inactive session within {{hour}} hours will be removed.', ['hour' => $hour]) . ' ' . phrase('This action cannot be undone.'); ?>
                </p>
                <a href="<?= go_to('clean'); ?>" class="btn btn-danger btn-sm px-5 rounded-pill --xhr show-progress">
                    <i class="mdi mdi-check"></i> <?= phrase('Click to continue'); ?>
                </a>
            </div>
        </div>
    </div>
</div>
