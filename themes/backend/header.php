<header role="header" class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top" id="header-wrapper">
    <div class="container-fluid">
        <a class="navbar-brand pt-0 pb-0 d-none d-lg-block" href="<?= base_url(); ?>" target="_blank">
            <img src="<?= get_image('settings', get_setting('app_icon'), 'icon'); ?>" class="img-fluid img-icon rounded" />
            <img src="<?= get_image('settings', get_setting('app_logo')); ?>" class="img-fluid img-logo rounded" />
            <?= (get_userdata('year') ? '<span class="badge bg-warning">' . get_userdata('year') . '</span>' : ''); ?>
        </a>
        <a href="<?= current_page(); ?>" class="--xhr navbar-brand pt-0 pb-0 d-block d-lg-none text-truncate" role="title">
            <?= $meta->title; ?>
        </a>
        <button class="navbar-toggler border-0" type="button" data-toggle="sidebar">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarText">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a href="javascript:void(0)" class="nav-link" data-toggle="sidebar">
                        <i class="mdi mdi-arrow-left"></i>
                    </a>
                </li>
            </ul>
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a href="javascript:void(0)" class="nav-link" data-toggle="fullscreen">
                        <i class="mdi mdi-fullscreen"></i>
                    </a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="<?= base_url('xhr/partial/language'); ?>" data-bs-toggle="dropdown" role="language">
                        <i class="mdi mdi-translate"></i>
                        <?= phrase('Language'); ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <!-- language list -->
                    </ul>
                </li>
                <li class="nav-item">
                    <a href="<?= base_url('administrative/account'); ?>" class="nav-link --xhr">
                        <i class="mdi mdi-cogs"></i>
                        <?= phrase('Account'); ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= base_url('auth/sign_out'); ?>" class="nav-link --xhr">
                        <i class="mdi mdi-logout"></i>
                        <?= phrase('Sign Out'); ?>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</header>
