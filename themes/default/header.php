<?php
/**
 * @var object $meta
 * @var array $menus
 */

// Cache menu output once — generate_menu() mutates $menus by reference,
// calling it twice would corrupt slugs (all links become target="_blank")
$_menu_html = generate_menu($menus, 'navbar-nav mx-auto gap-3', 'nav-item', 'nav-link --xhr', 'dropdown-toggle', 'data-bs-toggle="dropdown"', 'dropdown', 'dropdown-menu');
?>
<header role="header" class="navbar navbar-expand-lg navbar-light border-fade-bottom fixed-top" id="header-wrapper" data-hide-on-scroll="true">
    <div class="container">
        <a class="navbar-brand pt-0 pb-0 --xhr" href="<?= base_url(); ?>">
            <img src="<?= get_image('settings', get_setting('app_icon'), 'icon'); ?>" class="img-fluid img-icon rounded" />
            <img src="<?= get_image('settings', get_setting('app_logo')); ?>" class="img-fluid img-logo rounded" />
            <?= (get_userdata('year') ? '<span class="badge bg-warning">' . get_userdata('year') . '</span>' : ''); ?>
        </a>
        <button class="navbar-toggler mobile-menu-toggle" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbarDark" aria-controls="offcanvasNavbarDark" aria-label="<?= phrase('Toggle navigation'); ?>">
            <span class="mobile-menu-toggle-lines" aria-hidden="true">
                <span class="mobile-menu-toggle-line"></span>
                <span class="mobile-menu-toggle-line"></span>
                <span class="mobile-menu-toggle-line"></span>
            </span>
        </button>
        <!-- Desktop inline menu (lg+) -->
        <div class="collapse navbar-collapse d-none d-lg-flex">
            <?= $_menu_html; ?>
            <ul class="navbar-nav ml-auto align-items-lg-center gap-3">
                <?php if (get_userdata('is_logged')): ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="<?= base_url('notifications/partials'); ?>" data-bs-toggle="dropdown" role="notifications">
                        <i class="mdi mdi-bell-ring"></i>
                        <span class="d-md-none"><?= phrase('Notifications'); ?></span> <span id="notification-count" class="badge bg-danger"></span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <!-- Notification list -->
                    </ul>
                </li>
                <?php endif; ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="<?= base_url('xhr/partial/language'); ?>" data-bs-toggle="dropdown" role="language">
                        <i class="mdi mdi-translate"></i>
                        <span><?= phrase('Language'); ?></span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <!-- Language list -->
                    </ul>
                </li>
                <?php if (get_userdata('is_logged')): ?>
                <li class="nav-item dropdown user-account">
                    <a class="nav-link dropdown-toggle" href="javascript:void(0)" data-bs-toggle="dropdown">
                        <i class="mdi mdi-account-outline"></i>
                        <span><?= truncate(get_userdata('first_name'), 16); ?></span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li class="nav-item">
                            <a class="nav-link no-ajax" href="<?= base_url('dashboard'); ?>">
                                <i class="mdi mdi-monitor-dashboard"></i>
                                <span><?= phrase('Dashboard'); ?></span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link no-ajax" href="<?= base_url('administrative/account'); ?>">
                                <i class="mdi mdi-account-circle-outline"></i>
                                <span><?= phrase('Account'); ?></span>
                            </a>
                        </li>
                        <!-- Divider -->
                        <li><hr class="dropdown-divider"></li>

                        <li class="nav-item">
                            <a class="nav-link text-danger" href="<?= base_url('auth/sign_out'); ?>">
                                <i class="mdi mdi-logout"></i>
                                <span><?= phrase('Sign Out'); ?></span>
                            </a>
                        </li>
                    </ul>
                </li>
                <?php else: ?>
                <li class="nav-item user-login ms-2">
                    <a href="<?= base_url('auth'); ?>" class="btn btn-sm btn-primary rounded-pill px-4 text-nowrap --modal" data-format="html">
                        <span><?= phrase('Sign In'); ?></span>
                    </a>
                </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</header>

<!-- Bottom Sheet offcanvas (outside header to avoid backdrop-filter containing block) -->
<div class="offcanvas offcanvas-bottom text-bg-dark" tabindex="-1" id="offcanvasNavbarDark" aria-labelledby="offcanvasNavbarDarkLabel">
    <div class="offcanvas-header flex-column align-items-center pb-0 position-relative">
        <div class="drag-handle mb-1"></div>
        <h5 class="offcanvas-title w-100 text-center" id="offcanvasNavbarDarkLabel">
            <?= phrase('Main Navigation'); ?>
        </h5>
    </div>
    <div class="offcanvas-body">
        <?= $_menu_html; ?>
        <ul class="navbar-nav ml-auto align-items-lg-center gap-3">
            <?php if (get_userdata('is_logged')): ?>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="<?= base_url('notifications/partials'); ?>" data-bs-toggle="dropdown" role="notifications">
                    <i class="mdi mdi-bell-ring"></i>
                    <span><?= phrase('Notifications'); ?></span> <span id="notification-count" class="badge bg-danger"></span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <!-- Notification list -->
                </ul>
            </li>
            <?php endif; ?>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="<?= base_url('xhr/partial/language'); ?>" data-bs-toggle="dropdown" role="language">
                    <i class="mdi mdi-translate"></i>
                    <span><?= phrase('Language'); ?></span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <!-- Language list -->
                </ul>
            </li>
            <?php if (get_userdata('is_logged')): ?>
            <li class="nav-item dropdown user-account">
                <a class="nav-link dropdown-toggle" href="javascript:void(0)" data-bs-toggle="dropdown">
                    <i class="mdi mdi-account-outline"></i>
                    <span><?= truncate(get_userdata('first_name'), 16); ?></span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li class="nav-item">
                        <a class="nav-link no-ajax" href="<?= base_url('dashboard'); ?>">
                            <i class="mdi mdi-monitor-dashboard"></i>
                            <span><?= phrase('Dashboard'); ?></span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link no-ajax" href="<?= base_url('administrative/account'); ?>">
                            <i class="mdi mdi-account-circle-outline"></i>
                            <span><?= phrase('Account'); ?></span>
                        </a>
                    </li>
                    <!-- Divider -->
                    <li><hr class="dropdown-divider"></li>

                    <li class="nav-item">
                        <a class="nav-link text-danger" href="<?= base_url('auth/sign_out'); ?>">
                            <i class="mdi mdi-logout"></i>
                            <span><?= phrase('Sign Out'); ?></span>
                        </a>
                    </li>
                </ul>
            </li>
            <?php else: ?>
            <li class="nav-item user-login m-3">
                <a href="<?= base_url('auth'); ?>" class="btn btn-sm btn-primary rounded-pill px-4 py-2 text-nowrap --modal" data-format="html">
                    <span><?= phrase('Sign In'); ?></span>
                </a>
            </li>
            <?php endif; ?>
        </ul>
    </div>
</div>
