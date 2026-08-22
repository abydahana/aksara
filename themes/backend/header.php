<?php

/**
 * @var object $meta
 */
$userAgent = service('request')->getUserAgent();
$isMobile = ! is_cli() && $userAgent->isMobile();
?>

<header data-role="header" class="navbar navbar-expand-lg bg-body border-bottom fixed-top" id="header-wrapper">
    <div class="container-fluid flex-nowrap">
        <div class="navbar-brand pt-0 pb-0 d-none d-lg-block">
            <a href="<?= base_url() ?>" target="_blank">
                <img src="<?= get_image('settings', get_setting('app_icon'), 'icon') ?>" class="img-fluid img-icon rounded" alt="<?= get_setting('app_name') ?> Icon" loading="lazy" decoding="async" />
                <img src="<?= get_image('settings', get_setting('app_logo')) ?>" class="img-fluid img-logo rounded" alt="<?= get_setting('app_name') ?> Logo" loading="lazy" decoding="async" />
            </a>
        </div>
        <a href="<?= current_page() ?>" class="navbar-brand pt-0 pb-0 d-block d-lg-none text-truncate fw-bold me-auto --xhr" data-role="title" style="min-width: 0;">
            <?= $meta->title ?>
        </a>
        <div class="d-flex align-items-center d-lg-none ms-auto">
            <a href="<?= base_url('xhr/theme/editor') ?>" class="nav-link px-2 --modal" aria-label="<?= phrase('Theme Editor') ?>" title="<?= phrase('Theme Editor') ?>">
                <i class="mdi mdi-palette-outline fs-2"></i>
            </a>
        </div>
        <?php if ($isMobile): ?>
            <button class="navbar-toggler mobile-menu-toggle flex-shrink-0 border-0 d-lg-none me-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasBottomSheet" aria-controls="offcanvasBottomSheet" aria-label="<?= phrase('Toggle navigation') ?>">
                <span class="mobile-menu-toggle-lines" aria-hidden="true">
                    <span class="mobile-menu-toggle-line"></span>
                    <span class="mobile-menu-toggle-line"></span>
                    <span class="mobile-menu-toggle-line"></span>
                </span>
            </button>
        <?php else: ?>
            <button class="navbar-toggler mobile-menu-toggle flex-shrink-0 border-0 d-lg-none me-2" type="button" data-toggle="sidebar" aria-label="<?= phrase('Toggle sidebar') ?>" aria-expanded="false">
                <span class="mobile-menu-toggle-lines" aria-hidden="true">
                    <span class="mobile-menu-toggle-line"></span>
                    <span class="mobile-menu-toggle-line"></span>
                    <span class="mobile-menu-toggle-line"></span>
                </span>
            </button>
        <?php endif; ?>
        <div class="collapse navbar-collapse" id="navbarText">
            <ul class="navbar-nav me-auto align-items-center">
                <li class="nav-item">
                    <?php if (get_userdata('year')): ?>
                        <div class="dropdown">
                            <button class="btn btn-danger btn-sm border fw-bold rounded-pill px-3 dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <?= get_userdata('year') ?>
                            </button>
                            <ul class="dropdown-menu">
                                <?php foreach (get_active_years() as $key => $val): ?>
                                    <li>
                                        <a class="dropdown-item --xhr<?= get_userdata('year') == $val->year ? ' active' : '' ?>" href="<?= base_url('xhr/set_year', ['year' => $val->year]) ?>">
                                            <?= $val->year ?>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                </li>
            </ul>
            <ul class="navbar-nav align-items-center ms-auto">
                <li class="nav-item">
                    <a href="<?= current_page() ?>" class="nav-link py-0" data-toggle="fullscreen" aria-label="<?= phrase('Toggle fullscreen') ?>">
                        <i class="mdi mdi-fullscreen fs-5"></i>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= base_url('xhr/theme/editor') ?>" class="nav-link py-0 --modal" aria-label="<?= phrase('Theme Editor') ?>" title="<?= phrase('Theme Editor') ?>">
                        <i class="mdi mdi-palette-outline fs-5"></i>
                    </a>
                </li>
                <?php if (get_userdata('is_logged')): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link py-0" href="<?= base_url('notifications/partials') ?>" data-bs-toggle="dropdown" data-role="notifications" aria-label="<?= phrase('Notifications') ?>">
                            <i class="mdi mdi-bell-ring fs-5"></i> <span class="d-md-none"><?= phrase('Notifications') ?></span> <span id="notification-count" class="badge bg-danger"></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <!-- Notification list -->
                        </ul>
                    </li>
                <?php endif; ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="<?= base_url('xhr/partial/language') ?>" data-bs-toggle="dropdown" data-role="language" aria-label="<?= phrase('Language') ?>">
                        <i class="mdi mdi-translate"></i> <?= phrase('Language') ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <!-- Language list -->
                    </ul>
                </li>
                <li class="nav-item">
                    <a href="<?= base_url('administrative/account') ?>" class="nav-link --xhr">
                        <i class="mdi mdi-cogs"></i> <?= phrase('Account') ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= base_url('auth/sign_out') ?>" class="nav-link --xhr">
                        <i class="mdi mdi-logout"></i> <?= phrase('Sign Out') ?>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</header>
