<?php
    // Database driver options
    $driver_list = null;

    foreach ($driver as $key => $val)
    {
        $driver_list .= '<option value="' . $key . '"' . (session()->get('database_driver') == $key ? ' selected' : null) . '>' . $val . '</option>';
    }
?>
<form action="<?= site_url('security'); ?>" method="POST" class="--validate-form">
    <h4>
        <?= phrase('Database Configuration'); ?>
    </h4>
    <p>
        <?= phrase('Fill the requested fields below with your database connection.'); ?>
    </p>
    <div class="text-muted opacity-50">
        <hr class="row" />
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group mb-3">
                <label class="d-block mb-0">
                    <?= phrase('Database Driver'); ?>
                    <b class="text-danger">*</b>
                </label>
                <select name="database_driver" class="form-select form-select-sm rounded-pill">
                    <?= $driver_list; ?>
                </select>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group mb-3">
                <label class="d-block mb-0">
                    <?= phrase('DSN'); ?> (<?= phrase('Optional'); ?>)
                </label>
                <input type="text" name="database_dsn" class="form-control form-control-sm rounded-pill" placeholder="e.g: dblib:host=localhost;" value="<?= session()->get('database_dsn'); ?>" />
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group mb-3">
                <label class="d-block mb-0">
                    <?= phrase('Hostname'); ?>
                    <b class="text-danger">*</b>
                </label>
                <input type="text" name="database_hostname" class="form-control form-control-sm rounded-pill" placeholder="e.g: localhost" value="<?= session()->get('database_hostname'); ?>" />
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group mb-3">
                <label class="d-block mb-0">
                    <?= phrase('Port'); ?>
                    <b class="text-danger">*</b>
                </label>
                <input type="number" name="database_port" class="form-control form-control-sm rounded-pill" placeholder="e.g: 3306" value="<?= session()->get('database_port'); ?>" />
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group mb-3">
                <label class="d-block mb-0">
                    <?= phrase('Username'); ?>
                    <b class="text-danger">*</b>
                </label>
                <input type="text" name="database_username" class="form-control form-control-sm rounded-pill" placeholder="e.g: root" value="<?= session()->get('database_username'); ?>" />
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group mb-3">
                <label class="d-block mb-0">
                    <?= phrase('Password'); ?>
                </label>
                <input type="password" name="database_password" class="form-control form-control-sm rounded-pill" value="<?= session()->get('database_password'); ?>" />
            </div>
        </div>
    </div>
    <div class="row align-items-center">
        <div class="col-md-6">
            <div class="form-group mb-3">
                <label class="d-block mb-0">
                    <?= phrase('Database Initial'); ?>
                    <b class="text-danger">*</b>
                </label>
                <input type="text" name="database_initial" class="form-control form-control-sm rounded-pill" placeholder="e.g: aksara_cms" value="<?= session()->get('database_initial'); ?>" />
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group mb-3">
                <label class="d-none d-md-block mb-0">&nbsp;</label>
                <label class="form-check form-switch">
                    <input type="checkbox" name="database_forge" class="form-check-input" value="1" />
                    <?= phrase('Create database if not exist'); ?>
                </label>
            </div>
        </div>
    </div>
    <div class="text-muted opacity-50">
        <hr class="row" />
    </div>
    <div class="--validation-callback"></div>
    <div class="row">
        <div class="col-6">
            <div class="d-grid">
                <a href="<?= site_url('requirement'); ?>" class="btn btn-light btn-sm rounded-pill --xhr">
                    <i class="mdi mdi-arrow-left"></i>
                    <?= phrase('Back'); ?>
                </a>
            </div>
        </div>
        <div class="col-6">
            <div class="d-grid">
                <input type="hidden" name="_token" value="<?= sha1(time()); ?>" />
                <button type="submit" class="btn btn-outline-primary btn-sm rounded-pill">
                    <i class="mdi mdi-check"></i>
                    <?= phrase('Continue'); ?>
                </button>
            </div>
        </div>
    </div>
</form>
