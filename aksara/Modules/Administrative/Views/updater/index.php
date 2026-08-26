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
        <div class="row no-gutters">
            <div class="col-4 col-md-2 col-lg-1 pt-1">
                <a href="<?= $val->profile_url ?>" target="_blank">
                    <img src="<?= $val->profile_avatar ?>" class="img-fluid rounded-more" alt="<?= htmlspecialchars((string) $val->committer) ?>" />
                </a>
            </div>
            <div class="col-8 col-md-10 col-lg-11 ps-3 text-break-word">
                <a href="<?= $val->profile_url ?>" target="_blank">
                    <h5>
                        <?= $val->committer ?>
                        <i class="mdi mdi-launch"></i>
                    </h5>
                </a>
                <span>
                    <?= $val->date ?>
                </span>
            </div>
        </div>
        <hr />
        <?= $parsedown->text($val->message) ?>
    <?php
    }

    $changelog = ob_get_clean();
} ?>

<div class="container-fluid py-3">
    <?php if ($changelog): ?>
        <div class="alert alert-info rounded-0 border-0" style="margin-left:-1rem; margin-right:-1rem">
            <h5>
                <?= phrase('Update Available') ?>
            </h5>
            <p class="mb-0">
                <?= phrase('A newer version of Aksara is available.') ?> <?= phrase('Click the button below to update your core system directly.') ?> <?= phrase('Your created module and theme will not be overwritten.') ?>
            </p>
        </div>
        <form action="<?= current_page() ?>" method="POST">
            <div class="row">
                <div class="col-lg-8">
                <?= $changelog ?>
                </div>
            </div>
            <hr class="mx--3" />
            <div class="row">
                <div class="col-lg-8">
                    <button type="submit" class="btn btn-success rounded-pill">
                        <i class="mdi mdi-reload"></i> <?= phrase('Update Now') ?>
                    </button>
                    <a href="<?= base_url('administrative/updater/migrate') ?>" class="btn btn-outline-success rounded-pill ms-2 --xhr --confirm" data-confirm="<?= phrase('Are you sure you want to run database migration and seeder?') ?>">
                        <i class="mdi mdi-database-refresh"></i> <?= phrase('Run Migration & Seeder') ?>
                    </a>
                    <a href="<?= base_url('administrative/updater/upload') ?>" class="btn btn-dark rounded-pill ms-2 --modal">
                        <i class="mdi mdi-upload"></i> <?= phrase('Manual Update') ?>
                    </a>
                </div>
            </div>
        </form>
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
