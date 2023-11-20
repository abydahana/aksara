
<?php
    $changelog = null;

    if (isset($updater->changelog)) {
        $parsedown = new \Aksara\Libraries\Parsedown();

        foreach ($updater->changelog as $key => $val) {
            if ($key) {
                $changelog .= '<hr class="mt-1 mb-1" />';
            }

            $changelog .= '
                <a href="' . $val->commit_url . '" target="_blank">
                    <h2>
                        ' . $val->title . '
                        <i class="mdi mdi-launch"></i>
                    </h2>
                </a>
                <hr />
                <div class="row no-gutters">
                    <div class="col-4 col-md-2 col-lg-1 pt-1">
                        <a href="' . $val->profile_url . '" target="_blank">
                            <img src="' . $val->profile_avatar . '" class="img-fluid rounded-more" />
                        </a>
                    </div>
                    <div class="col-8 col-md-10 col-lg-11 ps-3 text-break-word">
                        <a href="' . $val->profile_url . '" target="_blank">
                            <h5>
                                ' . $val->committer . '
                                <i class="mdi mdi-launch"></i>
                            </h5>
                        </a>
                        <span>
                            ' . $val->date . '
                        </span>
                    </div>
                </div>
                <hr />
                ' . $parsedown->parse($val->message) . '
            ';
        }
    }
?>

<div class="container-fluid py-3">
    <?php if ($changelog): ?>
        <div class="alert alert-info rounded-0 border-0" style="margin-left:-1rem; margin-right:-1rem">
            <h5>
                <?= phrase('Update Available'); ?>
            </h5>
            <p class="mb-0">
                <?= phrase('A newer version of Aksara is available.'); ?>
                <?= phrase('Click the button below to update your core system directly.'); ?>
                <?= phrase('Your created module and theme will not be overwritten.'); ?>
            </p>
        </div>
        <form action="<?= current_page(); ?>" method="POST">
            <div class="row">
                <div class="col-lg-8">
                <?= $changelog; ?>
                </div>
            </div>
            <hr class="row" />
            <div class="row">
                <div class="col-lg-8">
                    <button type="submit" class="btn btn-success rounded-pill">
                        <i class="mdi mdi-reload"></i>
                        <?= phrase('Update Now'); ?>
                    </button>
                    <a href="//www.aksaracms.com/updater/file.zip" class="btn btn-dark rounded-pill ms-3">
                        <i class="mdi mdi-hammer"></i>
                        <?= phrase('Manual Update'); ?>
                    </a>
                </div>
            </div>
        </form>
    <?php else: ?>
        <div class="alert alert-success rounded-more">
            <h5>
                <?= phrase('Your core system is up to date'); ?>
            </h5>
            <p>
                <?= phrase('No update available at the moment. The update will be inform to you if available.'); ?>
            </p>
            <hr />
            <a href="<?= base_url('administrative/updater'); ?>" class="btn btn-sm btn-success rounded-pill --xhr show-progress">
                <i class="mdi mdi-update"></i>
                <?= phrase('Check Again'); ?>
            </a>
        </div>
    <?php endif; ?>
</div>
