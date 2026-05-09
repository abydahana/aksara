<?php
    $logs = (isset($logs) ? $logs : []);
    $announcements = (isset($announcements) ? $announcements : []);
    $group_name = (isset($group_name) ? $group_name : phrase('Unknown'));
?>
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
    <div class="row">
        <div class="col-lg-8">
            <div class="card overflow-hidden rounded-4 mb-3">
                <div class="card-body p-4">
                    <h5 class="mb-1">
                        <?= phrase('Welcome back'); ?>, <?= get_userdata('first_name') . ' ' . get_userdata('last_name'); ?>!
                    </h5>
                    <p class="text-muted mb-0">
                        <?= phrase('Here is what is happening with your account today.'); ?>
                    </p>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-sm-4">
                    <div class="card overflow-hidden rounded-4 mb-3">
                        <div class="card-body">
                            <div class="text-muted small text-uppercase fw-bold mb-1">
                                <?= phrase('Member Since'); ?>
                            </div>
                            <div class="h5 mb-0">
                                <?= (get_userdata('registered_date') ? date('d M Y', strtotime(get_userdata('registered_date'))) : '-'); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="card overflow-hidden rounded-4 mb-3">
                        <div class="card-body">
                            <div class="text-muted small text-uppercase fw-bold mb-1">
                                <?= phrase('Account Group'); ?>
                            </div>
                            <div class="h5 mb-0">
                                <?= $group_name; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="card overflow-hidden rounded-4 mb-3">
                        <div class="card-body">
                            <div class="text-muted small text-uppercase fw-bold mb-1">
                                <?= phrase('Last Activity'); ?>
                            </div>
                            <div class="h5 mb-0">
                                <?= ($logs ? date('H:i', strtotime($logs[0]->timestamp)) : '-'); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card overflow-hidden rounded-4 mb-3">
                <div class="card-header border-0 p-4">
                    <h6 class="fw-bold mb-0">
                        <?= phrase('Recent Activities'); ?>
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th class="border-0 px-4 text-muted small text-uppercase"><?= phrase('Date'); ?></th>
                                    <th class="border-0 px-4 text-muted small text-uppercase"><?= phrase('Platform'); ?></th>
                                    <th class="border-0 px-4 text-muted small text-uppercase"><?= phrase('Browser'); ?></th>
                                    <th class="border-0 px-4 text-muted small text-uppercase"><?= phrase('IP Address'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($logs): ?>
                                    <?php foreach ($logs as $log): ?>
                                        <tr>
                                            <td class="px-4 align-middle">
                                                <span class="text-sm"><?= date('d/m/Y H:i', strtotime($log->timestamp)); ?></span>
                                            </td>
                                            <td class="px-4 align-middle">
                                                <span class="badge bg-white text-dark rounded-pill"><?= $log->platform; ?></span>
                                            </td>
                                            <td class="px-4 align-middle">
                                                <span class="text-sm"><?= $log->browser; ?></span>
                                            </td>
                                            <td class="px-4 align-middle">
                                                <code class="text-sm text-primary"><?= $log->ip_address; ?></code>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="text-center p-4 text-muted">
                                            <?= phrase('No activity recorded yet.'); ?>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card overflow-hidden rounded-4 mb-3">
                <div class="card-header bg-transparent border-0 p-4">
                    <h6 class="fw-bold mb-0">
                        <?= phrase('Announcements'); ?>
                    </h6>
                </div>
                <div class="card-body p-4">
                    <?php if ($announcements): ?>
                        <?php foreach ($announcements as $announcement): ?>
                            <div class="mb-3 pb-3 border-bottom last-child-border-0">
                                <a href="<?= base_url('announcements/' . $announcement->announcement_slug); ?>" class="fw-bold text-decoration-none --xhr">
                                    <?= $announcement->title; ?>
                                </a>
                                <div class="text-muted small mt-1">
                                    <?= truncate($announcement->content, 100); ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-center py-4 text-muted">
                            <i class="mdi mdi-bullhorn-outline mdi-36px d-block mb-2"></i>
                            <?= phrase('No announcements available.'); ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card overflow-hidden rounded-4 bg-primary text-white mb-3">
                <div class="card-body p-4 text-center">
                    <i class="mdi mdi-account-cog-outline mdi-48px mb-3"></i>
                    <h6><?= phrase('Need help?'); ?></h6>
                    <p class="small opacity-75 mb-3">
                        <?= phrase('Update your profile or change your security settings here.'); ?>
                    </p>
                    <a href="<?= base_url('administrative/account'); ?>" class="btn btn-light btn-sm rounded-pill px-4 --xhr">
                        <?= phrase('Manage Account'); ?>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
