<?php
    /**
     * @var array $driver
     */

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
                <input type="text" name="database_hostname" class="form-control form-control-sm rounded-pill" placeholder="e.g: localhost" value="<?= session()->get('database_hostname') ?? 'localhost'; ?>" />
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group mb-3">
                <label class="d-block mb-0">
                    <?= phrase('Port'); ?>
                    <b class="text-danger">*</b>
                </label>
                <input type="number" name="database_port" class="form-control form-control-sm rounded-pill" placeholder="e.g: 3306" value="<?= session()->get('database_port') ?? 3306; ?>" />
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

<script>
    $(document).ready(function() {
        $('select[name=database_driver]').on('change', function() {
            var driver = $(this).val();
            var port = '';
            var username = '';
            
            if (driver === 'MySQLi' || driver === 'MySQL') {
                port = '3306';
                username = 'root';
            } else if (driver === 'Postgre') {
                port = '5432';
                username = 'postgres';
            } else if (driver === 'SQLSRV') {
                port = '1433';
                username = 'sa';
            } else if (driver === 'OCI8') {
                port = '1521';
                username = 'SYSTEM';
            } else if (driver === 'SQLite3') {
                port = '';
                username = '';
            }
            
            var $portInput = $('input[name=database_port]');
            var $userInput = $('input[name=database_username]');
            
            var currentPort = $portInput.val();
            var defaultPorts = ['3306', '5432', '1433', '1521', ''];
            
            // Overwrite value only if it is currently a default port
            if (defaultPorts.includes(currentPort)) {
                $portInput.val(port);
            }
            $portInput.attr('placeholder', (port ? 'e.g: ' + port : ''));
            
            var currentUsername = $userInput.val();
            var defaultUsernames = ['root', 'postgres', 'sa', 'SYSTEM', ''];
            
            // Overwrite value only if it is currently a default username
            if (defaultUsernames.includes(currentUsername)) {
                $userInput.val(username);
            }
            $userInput.attr('placeholder', (username ? 'e.g: ' + username : ''));
            
            // Toggle required asterisks visibility
            var $asterisks = $('input[name=database_hostname], input[name=database_port], input[name=database_username]').prev('label').find('.text-danger');
            if (driver === 'SQLite3') {
                $asterisks.addClass('d-none');
            } else {
                $asterisks.removeClass('d-none');
            }
        });
        
        // Trigger on load to adjust placeholder
        $('select[name=database_driver]').trigger('change');
    });
</script>
