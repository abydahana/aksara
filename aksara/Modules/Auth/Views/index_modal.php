<div class="card-group">
    <div class="card border-top-0 border-bottom-0 border-start-0">
        <div class="card-body">
            <form action="<?= current_page(); ?>" method="POST" class="--validate-form" enctype="multipart/form-data">
                <div class="mb-3">
                    <p class="text-center text-muted">
                        <i class="mdi mdi-account-circle-outline mdi-5x text-muted"></i>
                    </p>
                </div>
                <div class="mb-3">
                    <p class="text-center text-muted">
                        <?= phrase('Please enter your account information to sign in.'); ?>
                    </p>
                </div>
                <div class="form-group mb-3">
                    <div class="input-group">
                        <span class="input-group-text rounded-pill rounded-end">
                            <i class="mdi mdi-account" style="width:22px"></i>
                        </span>
                        <input type="text" name="username" class="form-control rounded-pill rounded-start" id="username_input" placeholder="<?= phrase('Enter your username or email'); ?>" />
                    </div>
                </div>
                <div class="form-group mb-3">
                    <div class="input-group">
                        <span class="input-group-text rounded-pill rounded-end">
                            <i class="mdi mdi-fingerprint" style="width:22px"></i>
                        </span>
                        <input type="password" name="password" class="form-control" id="password_input" placeholder="<?= phrase('Enter password'); ?>" autocomplete="new-password" style="border-right:0" />
                        <span class="input-group-text bg-white rounded-pill rounded-start" style="border-left:0">
                            <i class="mdi mdi-eye-outline password-peek" data-parent=".form-group" data-peek=".form-control" style="width:22px"></i>
                        </span>
                    </div>
                </div>
                
                <?php
                    if ($years) {
                        $option = null;

                        foreach ($years as $key => $val) {
                            $option .= '<option value="' . $val->value . '"' . ($val->selected ? ' selected' : null) . '>' . $val->label . '</option>';
                        }

                        echo '
                            <div class="form-group mb-3">
                                <div class="input-group">
                                    <span class="input-group-text rounded-pill rounded-end">
                                        <i class="mdi mdi-calendar-check" style="width:22px"></i>
                                    </span>
                                    <select name="year" class="form-control rounded-pill rounded-start" placeholder="' . phrase('Choose year') . '" id="year_input">
                                        ' . $option . '
                                    </select>
                                </div>
                            </div>
                        ';
                    }
                ?>
                
                <div class="row mt-3">
                    <div class="col-7">
                        <div class="d-grid">
                            <a href="<?= current_page('forgot'); ?>" class="btn btn-link px-0 text-start --xhr">
                                <?= phrase('Forgot password?'); ?>
                            </a>
                        </div>
                    </div>
                    <div class="col-5">
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary rounded-pill">
                                <i class="mdi mdi-check"></i> 
                                <?= phrase('Sign In'); ?>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <?php if (get_setting('frontend_registration')): ?>
    <div class="card border-top-0 border-end-0 border-bottom-0">
        <div class="card-body d-flex align-items-center justify-content-center">
            <div>
                <p class="lead text-center text-black-50">
                    <?= phrase('Do not have an account?'); ?>
                </p>
                <?php if (get_setting('google_client_id') && get_setting('google_client_secret')): ?>
                <div class="d-grid mb-2">
                    <a href="<?= base_url('auth/sso/google'); ?>" class="btn btn-outline-danger rounded-pill">
                        <i class="mdi mdi-google"></i>
                        <?= phrase('Sign in with Google'); ?>
                    </a>
                </div>
                <?php endif; ?>
                <?php if (get_setting('facebook_app_id') && get_setting('facebook_app_secret')): ?>
                <div class="d-grid mb-2">
                    <a href="<?= base_url('auth/sso/facebook'); ?>" class="btn btn-outline-primary rounded-pill">
                        <i class="mdi mdi-facebook"></i>
                        <?= phrase('Sign in with Facebook'); ?>
                    </a>
                </div>
                <?php endif; ?>
                <div class="d-grid">
                    <a href="<?= base_url('auth/register'); ?>" class="btn btn-outline-success rounded-pill --xhr">
                        <i class="mdi mdi-account-plus"></i>
                        <?= phrase('Register an Account'); ?>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>
