<header role="header" class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top navbar">
    <div class="container">
        <a class="navbar-brand pt-0 pb-0 d-none d-lg-block --xhr" href="<?= base_url(); ?>">
            <img src="<?= get_image('settings', get_setting('app_icon'), 'icon'); ?>" class="img-fluid img-icon rounded" />
            <img src="<?= get_image('settings', get_setting('app_logo')); ?>" class="img-fluid img-logo rounded" />
            <?= (get_userdata('year') ? '<span class="badge bg-warning">' . get_userdata('year') . '</span>' : ''); ?>
        </a>
        <a href="<?= current_page(); ?>" class="navbar-brand pt-0 pb-0 d-block d-lg-none text-truncate --xhr" role="title">
            <?= $meta->title; ?>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbarDark" aria-controls="offcanvasNavbarDark">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="offcanvas offcanvas-end text-bg-dark" tabindex="-1" id="offcanvasNavbarDark" aria-labelledby="offcanvasNavbarDarkLabel">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title" id="offcanvasNavbarDarkLabel">
                    <?= phrase('Main Navigation'); ?>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body">
                <?= generate_menu($menus, 'navbar-nav me-auto', 'nav-item', 'nav-link --xhr', 'dropdown-toggle', 'data-bs-toggle="dropdown"', 'dropdown', 'dropdown-menu'); ?>
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="<?= base_url('xhr/partial/language'); ?>" data-bs-toggle="dropdown" role="language">
                            <i class="mdi mdi-translate"></i>
                            <span><?= phrase('Language'); ?></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <!-- language list -->
                        </ul>
                    </li>
                    <?php if (get_userdata('is_logged')): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="javascript:void(0)" data-bs-toggle="dropdown">
                            <i class="mdi mdi-account-outline"></i>
                            <span><?= truncate(get_userdata('first_name'), 16); ?></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li class="nav-item">
                                <h6 href="javascript:void(0)" class="nav-link dropdown-header text-muted">
                                    <i class="mdi mdi-blank"></i>
                                    <span><?= phrase('User Panel'); ?></span>
                                </h6>
                            </li>
                            
                            <li><hr class="dropdown-divider"></li>
                            
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
                            <!-- divider -->
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
                    <li class="nav-item">
                        <span class="nav-link">&nbsp;</span>
                    </li>
                    <li class="nav-item">
                        <a href="<?= base_url('auth'); ?>" class="nav-link bg-primary rounded-pill px-3 text-center --modal" data-format="html">
                            <i class="mdi mdi-login"></i>
                            <span><?= phrase('Sign In'); ?></span>
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
</header>
