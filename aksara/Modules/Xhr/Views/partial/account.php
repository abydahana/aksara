<div>
    <a href="<?= base_url('dashboard'); ?>" class="d-block --xhr">
        <i class="mdi mdi-monitor-dashboard"></i>
        <span><?= phrase('Dashboard'); ?></span>
    </a>
    <hr />
    <a href="<?= base_url('user'); ?>" class="d-block --xhr">
        <i class="mdi mdi-account-circle-outline"></i>
        <span><?= phrase('Profile'); ?></span>
    </a>
    <hr />
    <a href="<?= base_url('administrative/account'); ?>" class="d-block --xhr">
        <i class="mdi mdi-account-outline"></i>
        <span><?= phrase('Account'); ?></span>
    </a>
    <hr />
    <a href="<?= base_url('auth/sign_out'); ?>" class="d-block text-danger --xhr">
        <i class="mdi mdi-logout"></i>
        <span><?= phrase('Sign Out'); ?></span>
    </a>
</div>
