<?php
    $error = false;

    if (phpversion() < 7.4 || ! in_array('mbstring', $extension, true) || ! in_array('intl', $extension, true) || ! in_array('gd', $extension, true) || ! in_array('json', $extension, true) || ! in_array('xml', $extension, true))
    {
        $error = true;
    }
?>
<form action="<?= site_url('database'); ?>" method="POST" class="--validate-form">
    <h4>
        <?= phrase('Awesome!'); ?>
    </h4>
    <p>
        <?= phrase('You just read our notes and pretend to agree with it.'); ?>
    </p>
    <div class="text-muted opacity-50">
        <hr class="row" />
    </div>
    <p>
        <?= phrase('We will help you to prepare your application using this installation wizard.'); ?>
        <?= phrase('Before you go, make sure this pre-requirements are fulfilled without any warning.'); ?>
        <?= phrase('Otherwise your application will not work properly.'); ?>
    </p>
    <div class="text-muted opacity-50">
        <hr class="row" />
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="d-block mb-0">
                    <?= phrase('PHP Version'); ?>
                </label>
                <p>
                    <?= (phpversion() < 7.4 ? '<b class="text-danger">' . phpversion() . '</b>, ' . phrase('The minimum required version is') . ' <b>7.4</b>' : '<b class="text-success">' . phpversion() . '</b>'); ?>
                </p>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="d-block mb-0">
                    <?= phrase('Rewrite Module'); ?>
                </label>
                <p>
                    <?= (! $modRewrite ? '<b class="text-danger">' . phrase('Off') . '</b>, ' . phrase('Turn it on!') : '<b class="text-success">' . phrase('On') . '</b>'); ?>
                </p>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="d-block mb-0">
                    <?= phrase('Internationalization'); ?> (intl)
                </label>
                <p>
                    <?= (! in_array('intl', $extension, true) ? '<b class="text-danger">' . phrase('Off') . '</b>, ' . phrase('Turn it on!') : '<b class="text-success">' . phrase('On') . '</b>'); ?>
                </p>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="d-block mb-0">
                    <?= phrase('ZIP Archiver'); ?>
                </label>
                <p>
                    <?= (! class_exists('ZipArchive') ? '<b class="text-danger">' . phrase('Disabled') . '</b>' : '<b class="text-success">' . phrase('Available') . '</b>'); ?>
                </p>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="d-block mb-0">
                    <?= phrase('Multibyte String'); ?> (mbstring)
                </label>
                <p>
                    <?= (! in_array('mbstring', $extension, true) ? '<b class="text-danger">' . phrase('Off') . '</b>, ' . phrase('Turn it on!') : '<b class="text-success">' . phrase('On') . '</b>'); ?>
                </p>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="d-block mb-0">
                    <?= phrase('PHP GD'); ?>
                </label>
                <p>
                    <?= (! in_array('gd', $extension, true) ? '<b class="text-danger">' . phrase('Off') . '</b>, ' . phrase('Turn it on!') : '<b class="text-success">' . phrase('On') . '</b>'); ?>
                </p>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="d-block mb-0">
                    <?= phrase('JSON'); ?>
                </label>
                <p>
                    <?= (! in_array('json', $extension, true) ? '<b class="text-danger">' . phrase('Off') . '</b>, ' . phrase('Turn it on!') : '<b class="text-success">' . phrase('On') . '</b>'); ?>
                </p>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="d-block mb-0">
                    <?= phrase('XML'); ?>
                </label>
                <p>
                    <?= (! in_array('xml', $extension, true) ? '<b class="text-danger">' . phrase('Off') . '</b>, ' . phrase('Turn it on!') : '<b class="text-success">' . phrase('On') . '</b>'); ?>
                </p>
            </div>
        </div>
    </div>
    <?= ($error ? '<div class="alert alert-warning failure"><b>' . phrase('Whoops!') . '</b> ' . phrase('Some requirement are not yet fulfilled.') . ' ' . phrase('Please update your server configuration and click on refresh button to continue the installation.') . '</div>' : (! $modRewrite ? '<div class="alert alert-warning failure"><b>' . phrase('Whoops!') . '</b> ' . phrase('The rewrite module is disabled by your server.') . ' ' . phrase('You can continue the installation but we recommend to enable it.') . '</div>' : null)); ?>
    <div class="text-muted opacity-50">
        <hr class="row" />
    </div>
    <div class="--validation-callback"></div>
    <div class="row">
        <div class="col-md-6">
            <?= ($error ? '<div class="d-grid"><a href="' . site_url('requirement') . '" class="btn btn-light btn-sm --xhr"><i class="mdi mdi-reload"></i> ' . phrase('Refresh') . '</a></div>' : '&nbsp;'); ?>
        </div>
        <div class="col-md-6 text-right">
            <div class="d-grid">
                <input type="hidden" name="_token" value="<?= sha1(time()); ?>" />
                <button type="submit" class="btn btn-outline-primary btn-sm rounded-pill"<?= ($error ? ' disabled' : null); ?>>
                    <i class="mdi mdi-check"></i>
                    <?= phrase('Continue'); ?>
                </button>
            </div>
        </div>
    </div>
</form>
