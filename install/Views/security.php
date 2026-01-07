<form action="<?= site_url('system'); ?>" method="POST" class="--validate-form">
    <h4>
        <?= phrase('Security Configuration'); ?>
    </h4>
    <p>
        <?= phrase('Enter your secret formula to secure your application.'); ?>
    </p>
    <div class="text-muted opacity-50">
        <hr class="row" />
    </div>
    <div class="form-group mb-3">
        <label class="d-block mb-0">
            <?= phrase('Encryption Key'); ?>
            <b class="text-danger">*</b>
        </label>
        <input type="text" name="encryption" class="form-control form-control-sm rounded-pill" placeholder="<?= phrase('Your encryption key'); ?>" value="<?= $encryptionKey; ?>" />
    </div>
    <div class="row">
        <div class="col-sm-6">
            <div class="form-group mb-3">
                <label class="d-block mb-0">
                    <?= phrase('Cookie Name'); ?>
                    <b class="text-danger">*</b>
                </label>
                <input type="text" name="cookie_name" class="form-control form-control-sm rounded-pill" placeholder="<?= phrase('Unique cookie name to prevent session conflict'); ?>" value="<?= $cookieName; ?>" />
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group mb-3">
                <label class="d-block mb-0">
                    <?= phrase('Session Expiration'); ?>
                    <b class="text-danger">*</b>
                    <i class="mdi mdi-help-circle-outline" data-bs-toggle="tooltip" title="<?= phrase('The number of seconds you want the session to last.'); ?>"></i>
                </label>
                <input type="number" name="session_expiration" class="form-control form-control-sm rounded-pill" placeholder="<?= phrase('In seconds'); ?>" value="<?= (session()->get('session_expiration') ?? 86400); ?>" />
            </div>
        </div>
    </div>
    <br/>
    <h5>
        <?= phrase('Superuser'); ?>
    </h5>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group mb-3">
                <label class="d-block mb-0">
                    <?= phrase('First Name'); ?>
                    <b class="text-danger">*</b>
                </label>
                <input type="text" name="first_name" class="form-control form-control-sm rounded-pill" placeholder="e.g: John" value="<?= session()->get('first_name'); ?>" />
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group mb-3">
                <label class="d-block mb-0">
                    <?= phrase('Last Name'); ?>
                </label>
                <input type="text" name="last_name" class="form-control form-control-sm rounded-pill" placeholder="e.g: Doe" value="<?= session()->get('last_name'); ?>" />
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group mb-3">
                <label class="d-block mb-0">
                    <?= phrase('Email Address'); ?>
                    <b class="text-danger">*</b>
                </label>
                <input type="email" name="email" class="form-control form-control-sm rounded-pill" placeholder="e.g: johndoe@example.com" value="<?= session()->get('email'); ?>" />
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group mb-3">
                <label class="d-block mb-0">
                    <?= phrase('Username'); ?>
                    <b class="text-danger">*</b>
                </label>
                <input type="text" name="username" class="form-control form-control-sm rounded-pill" placeholder="<?= phrase('Create username for superuser'); ?>" value="<?= session()->get('username'); ?>" />
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group mb-3">
                <label class="d-block mb-0">
                    <?= phrase('Password'); ?>
                    <b class="text-danger">*</b>
                </label>
                <input type="password" name="password" class="form-control form-control-sm rounded-pill" placeholder="<?= phrase('Password for superuser'); ?>" />
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group mb-3">
                <label class="d-block mb-0">
                    <?= phrase('Password Confirmation'); ?>
                    <b class="text-danger">*</b>
                </label>
                <input type="password" name="confirm_password" class="form-control form-control-sm rounded-pill" placeholder="<?= phrase('Retype the password'); ?>" />
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
                <a href="<?= site_url('database'); ?>" class="btn btn-light btn-sm rounded-pill --xhr">
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
