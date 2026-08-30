<?php

/**
 * @var mixed $updater
 */
$changelog = null;

if (isset($updater->changelog)) {
    $parsedown = new \Parsedown();

    ob_start();

    foreach ($updater->changelog as $key => $val) {
        if ($key) {
            echo '<hr class="mt-1 mb-1" />';
        } ?>

        <a href="<?= $val->commit_url ?>" target="_blank">
            <h2>
                <?= $val->title ?>
                <i class="mdi mdi-launch"></i>
            </h2>
        </a>
        <hr />
        <div class="d-flex align-items-center gap-3">
            <a href="<?= $val->profile_url ?>" target="_blank">
                <img src="<?= $val->profile_avatar ?>" class="img-fluid d-block user-avatar rounded-3" alt="<?= htmlspecialchars((string) $val->committer) ?>" />
            </a>
            <div>
                <p class="m-0">
                    <a href="<?= $val->profile_url ?>" target="_blank">
                        <strong class="fs-5">
                            <?= $val->committer ?>
                            <i class="mdi mdi-launch"></i>
                        </strong>
                    </a>
                </p>
                <p class="m-0">
                    <span class="text-muted"><?= $val->date ?></span>
                </p>
            </div>
        </div>
        <hr />
        <?= $parsedown->text($val->message) ?>
    <?php
    }

    $changelog = ob_get_clean();
} ?>

<div class="container-fluid <?= ($changelog ? 'pb-3' : 'py-3') ?>">
    <?php if ($changelog): ?>
        <div class="alert alert-info rounded-0 border-0" style="margin-inline:-1rem">
            <h5>
                <?= phrase('Update Available') ?>
            </h5>
            <p class="mb-0">
                <?= phrase('A newer version of Aksara is available.') ?> <?= phrase('Click the button below to update your core system directly.') ?> <?= phrase('Your created module and theme will not be overwritten.') ?>
            </p>
        </div>
        <div class="row">
            <div class="col-lg-8">
            <?= $changelog ?>
            </div>
        </div>
        <hr class="mx--3" />
        <div class="d-flex align-items-center gap-2">
            <form action="<?= go_to('update') ?>" method="POST" class="--validate-form">
                <button type="submit" class="btn btn-primary">
                    <i class="mdi mdi-reload"></i> <?= phrase('Update Now') ?>
                </button>
            </form>
            <a href="<?= go_to('migrate') ?>" class="btn btn-secondary --xhr --confirm" data-confirm="<?= phrase('Are you sure you want to run database migration and seeder?') ?>">
                <i class="mdi mdi-database-refresh"></i> <?= phrase('Run Migration & Seeder') ?>
            </a>
            <?php if (! $changelog): ?>
                <a href="<?= go_to('upload') ?>" class="btn btn-dark --modal">
                    <i class="mdi mdi-upload"></i> <?= phrase('Manual Update') ?>
                </a>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="row">
            <div class="col-lg-7">
                <div class="alert alert-secondary rounded-4">
                    <h5>
                        <?= phrase('Your core system is up to date.') ?>
                    </h5>
                    <p>
                        <?= phrase('No update available at the moment. The update will be inform to you if available.') ?>
                    </p>
                    <div class="d-flex align-items-center flex-wrap gap-2">
                        <a href="<?= base_url('administrative/updater') ?>" class="btn btn-sm btn-primary rounded-pill px-4 --xhr show-progress">
                            <i class="mdi mdi-update"></i> <?= phrase('Check Again') ?>
                        </a>
                        <a href="<?= base_url('administrative/updater/upload') ?>" class="btn btn-sm btn-outline-dark rounded-pill px-4 --modal">
                            <i class="mdi mdi-upload"></i> <?= phrase('Manual Update') ?>
                        </a>
                        <a href="<?= base_url('administrative/updater/migrate') ?>" class="btn btn-sm btn-outline-success rounded-pill px-4 --xhr --confirm" data-confirm="<?= phrase('Are you sure you want to run database migration and seeder?') ?>">
                            <i class="mdi mdi-database-refresh"></i> <?= phrase('Run Migration & Seeder') ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
