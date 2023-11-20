<div class="container-fluid py-3">
    <p>
        <?= phrase('You are signed in on these devices or have been recently in.'); ?>
        <?= phrase('There might be multiple activity sessions from the same device.'); ?>
    </p>
    <div class="row">
        <div class="col-md-7 col-xxl-6">
            <?php foreach($logs as $platform => $log): ?>
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-5">
                                <b><?= $platform; ?></b>
                            </div>
                            <div class="col-sm-7">
                                <?php foreach($log as $key => $session): ?>
                                    <?php if ($key): ?>
                                        <hr />
                                    <?php endif; ?>

                                    <div>
                                        <?php if ($session->ip_address != service('request')->getIPAddress()): ?>
                                            <a href="<?= current_page('kick', ['session' => $session->session_id]); ?>" class="btn btn-sm btn-danger float-end rounded-pill --xhr">
                                                <i class="mdi mdi-logout"></i>
                                                <?= phrase('Kick'); ?>
                                            </a>
                                        <?php endif; ?>
                                        <b>
                                            <?= date('d F Y, H:i', strtotime($session->timestamp)); ?>
                                        </b>
                                        <br />
                                        <?= $session->browser; ?>
                                        <br />
                                        <a href="//ipinfo.io/<?= $session->ip_address; ?>" target="_blank">
                                            <b class="--fetch-ip-info" data-ip="<?= $session->ip_address; ?>">
                                                <?= $session->ip_address; ?> <i class="mdi mdi-launch"></i>
                                            </b>
                                        </a>
                                        <?php if ($session->ip_address == service('request')->getIPAddress()): ?>
                                            <br />
                                            <i class="mdi mdi-check-circle text-primary"></i>
                                            <?= phrase('your current session'); ?>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
