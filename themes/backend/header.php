<?php
/**
 * @var object $meta
 */
?>
<header data-role="header" class="navbar navbar-expand-lg navbar-light bg-white border-bottom fixed-top" id="header-wrapper">
    <div class="container-fluid">
        <div class="navbar-brand pt-0 pb-0 d-none d-lg-block">
            <a href="<?= base_url(); ?>" target="_blank">
                <img src="<?= get_image('settings', get_setting('app_icon'), 'icon'); ?>" class="img-fluid img-icon rounded" alt="<?= get_setting('app_name'); ?> Icon" loading="lazy" decoding="async" />
                <img src="<?= get_image('settings', get_setting('app_logo')); ?>" class="img-fluid img-logo rounded" alt="<?= get_setting('app_name'); ?> Logo" loading="lazy" decoding="async" />
            </a>
        </div>
        <a href="<?= current_page(); ?>" class="--xhr navbar-brand pt-0 pb-0 d-block d-lg-none text-truncate" data-role="title">
            <?= $meta->title; ?>
        </a>
        <button class="navbar-toggler border-0" type="button" data-toggle="sidebar" aria-label="<?= phrase('Toggle sidebar'); ?>">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarText">
            <ul class="navbar-nav me-auto align-items-center">
                <li class="nav-item">
                    <button type="button" role="button" class="nav-link py-0" data-toggle="sidebar" aria-label="Toggle Sidebar">
                        <i class="mdi mdi-menu-open fs-4" data-sidebar-toggle-icon></i>
                    </button>
                </li>
                <li class="nav-item">
                    <?php if (get_userdata('year')): ?>
                        <div class="dropdown">
                            <button class="btn btn-danger btn-sm border-light fw-bold rounded-pill px-3 dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <?= get_userdata('year'); ?>
                            </button>
                            <ul class="dropdown-menu">
                                <?php foreach(get_active_years() as $key => $val): ?>
                                    <li>
                                        <a class="dropdown-item --xhr<?= get_userdata('year') == $val->year ? ' active' : ''; ?>" href="<?= base_url('xhr/set_year', ['year' => $val->year]); ?>"><?= $val->year; ?></a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                </li>
            </ul>
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a href="<?= current_page(); ?>" class="nav-link" data-toggle="theme" aria-label="<?= phrase('Toggle theme'); ?>">
                        <i class="mdi mdi-weather-night"></i>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= current_page(); ?>" class="nav-link" data-toggle="fullscreen" aria-label="<?= phrase('Toggle fullscreen'); ?>">
                        <i class="mdi mdi-fullscreen"></i>
                    </a>
                </li>
                <?php if (get_userdata('is_logged')): ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="<?= base_url('notifications/partials'); ?>" data-bs-toggle="dropdown" data-role="notifications" aria-label="<?= phrase('Notifications'); ?>">
                        <i class="mdi mdi-bell-ring"></i> <span class="d-md-none"><?= phrase('Notifications'); ?></span> <span id="notification-count" class="badge bg-danger"></span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <!-- Notification list -->
                    </ul>
                </li>
                <?php endif; ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="<?= base_url('xhr/partial/language'); ?>" data-bs-toggle="dropdown" data-role="language" aria-label="<?= phrase('Language'); ?>">
                        <i class="mdi mdi-translate"></i> <?= phrase('Language'); ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <!-- Language list -->
                    </ul>
                </li>
                <li class="nav-item">
                    <a href="<?= base_url('administrative/account'); ?>" class="nav-link --xhr">
                        <i class="mdi mdi-cogs"></i> <?= phrase('Account'); ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= base_url('auth/sign_out'); ?>" class="nav-link --xhr">
                        <i class="mdi mdi-logout"></i> <?= phrase('Sign Out'); ?>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</header>
<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function() {
        const icons = document.querySelectorAll('[data-sidebar-toggle-icon]');

        if (! icons.length) {
            return;
        }

        const updateSidebarIcon = function() {
            const isCollapsed = document.body.classList.contains('sidebar-collapsed');

            icons.forEach(function(icon) {
                icon.classList.toggle('mdi-menu', isCollapsed);
                icon.classList.toggle('mdi-menu-open', ! isCollapsed);
            });
        };

        document.body.addEventListener('click', function(event) {
            if (event.target.closest('[data-toggle="sidebar"]')) {
                setTimeout(updateSidebarIcon, 0);
            }
        });

        updateSidebarIcon();
    });
</script>
