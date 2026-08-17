<?php
/**
 * @var array $banned
 */
?>

<div class="container-fluid py-3">
    <?php if ($banned) { ?>
        <div class="row">
            <?php foreach ($banned as $val) { ?>
                <div class="col-sm-6 col-lg-4 col-xl-3 mb-3">
                    <div class="card h-100 rounded-4">
                        <div class="card-body">
                            <div class="d-flex align-items-start justify-content-between gap-2 mb-3">
                                <div class="min-w-0">
                                    <div class="text-muted small">
                                        <?= phrase('IP Address') ?>
                                    </div>
                                    <h5 class="card-title text-break mb-0">
                                        <?= htmlspecialchars($val->ip) ?>
                                    </h5>
                                </div>
                                <span class="badge bg-danger-subtle text-danger rounded-pill">
                                    <?= number_format($val->attempts) ?>
                                </span>
                            </div>

                            <div class="mb-3">
                                <div class="text-muted small">
                                    <?= phrase('URI') ?>
                                </div>
                                <div class="fw-bold text-break">
                                    <?= htmlspecialchars($val->uri) ?>
                                </div>
                            </div>

                            <div class="row g-2">
                                <div class="col-12">
                                    <div class="text-muted small">
                                        <?= phrase('Banned At') ?>
                                    </div>
                                    <div class="fw-bold">
                                        <?= htmlspecialchars($val->bannedAtLabel) ?>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="text-muted small">
                                        <?= phrase('Release At') ?>
                                    </div>
                                    <div class="fw-bold">
                                        <?= htmlspecialchars($val->expiresAtLabel) ?>
                                    </div>
                                </div>
                            </div>

                            <hr />

                            <div class="text-muted small">
                                <?= phrase('Reason') ?>
                            </div>
                            <div class="text-break">
                                <?= htmlspecialchars($val->reason) ?>
                            </div>
                        </div>
                        <div class="card-footer bg-transparent border-secondary-subtle">
                            <?php if ($val->isBanned) { ?>
                                <a href="<?= go_to('unban', ['file' => $val->file]) ?>" class="btn btn-success btn-sm rounded-pill --modal">
                                    <i class="mdi mdi-shield-check-outline"></i>
                                    <?= phrase('Unban') ?>
                                </a>
                            <?php } else { ?>
                                <a href="<?= go_to('ban', ['file' => $val->file]) ?>" class="btn btn-danger btn-sm rounded-pill --modal">
                                    <i class="mdi mdi-shield-alert-outline"></i>
                                    <?= phrase('Ban') ?>
                                </a>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    <?php } else { ?>
        <div class="py-5 text-center">
            <i class="mdi mdi-shield-check-outline mdi-5x text-success"></i>
            <h5>
                <?= phrase('No banned request logs') ?>
            </h5>
            <p class="text-muted">
                <?= phrase('There are no banned request cache files to show.') ?>
            </p>
        </div>
    <?php } ?>
</div>
