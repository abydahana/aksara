<?php
    $hour = floor(SESSION_EXPIRATION / 3600);
?>
<div class="container-fluid py-3">
    <div class="row">
        <div class="col-lg-8">
            <div class="alert alert-warning callout">
                <p>
                    <h3><?= phrase('Information'); ?></h3>
                    <?= phrase('You are about to cleaning up unused session garbage.') . ' ' . phrase('All inactive session within {{ hour }} hours will be removed.', ['hour' => $hour]) . ' ' . phrase('This action cannot be undone.'); ?>
                </p>
                <hr />
                <a href="<?= go_to('clean'); ?>" class="btn btn-danger rounded-pill --xhr show-progress">
                    <i class="mdi mdi-check"></i>
                    <?= phrase('Click to continue'); ?>
                </a>
            </div>
        </div>
    </div>
</div>
