<?php
    $hour = floor(SESSION_EXPIRATION / 3600);
?>
<div class="container-fluid py-3">
    <div class="row">
        <div class="col-lg-8">
            <div class="alert alert-warning rounded-4">
                <p>
                    <b>
                        <?= phrase('You are about to cleaning up unused session garbage.'); ?>
                    </b>
                    <br />
                    <?= phrase('All inactive session within'); ?> <b><?= $hour . ' ' . ($hour > 1 ? phrase('hours') : phrase('hour')); ?></b> <?= phrase('will be removed.'); ?>
                    <?= phrase('This action cannot be undone.'); ?>
                </p>
                <hr />
                <a href="<?= go_to('clean'); ?>" class="btn btn-danger btn-sm rounded-pill --xhr show-progress">
                    <i class="mdi mdi-check"></i>
                    <?= phrase('Click to continue'); ?>
                </a>
            </div>
        </div>
    </div>
</div>
