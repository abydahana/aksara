<?php if (! get_userdata('username') || ! get_userdata('password')): ?>
    <div class="alert alert-danger border-0 rounded-0 mb-0">
        <h5>
            <?= phrase('Notice'); ?>
        </h5>
        <?php if (! get_userdata('username')) : ?>
            <p class="mb-0">
                <?= phrase('Please set your username as an alternative to the email when signing in.'); ?>
            </p>
        <?php endif; ?>
        <?php if (! get_userdata('password')) : ?>
            <p class="mb-0">
                <?= phrase('Please set your password to keep your account safe.'); ?>
            </p>
        <?php endif; ?>
        <br />
        <a href="<?= base_url('administrative/account'); ?>" class="fw-bold --xhr">
            <?= phrase('Update your profile'); ?>
        </a>
    </div>
<?php endif; ?>

<div class="container-fluid py-3">
    <h5>
        <?= phrase('Welcome back'); ?>, <?= get_userdata('first_name') . ' ' . get_userdata('last_name'); ?>!
    </h5>
</div>
