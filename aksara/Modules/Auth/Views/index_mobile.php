<?php if ($activation): ?>
    <div class="alert alert-info border-0 rounded-0">
        <div class="container text-center">
            <i class="mdi mdi-check-circle mdi-5x"></i>
            <h3>
                <?= phrase('Account Registered'); ?>
            </h3>
            <?= phrase('Follow the link we sent to your email to activate your account.'); ?>
        </div>
    </div>
<?php endif; ?>
<style type="text/css">
    #footer-wrapper {
        display: none
    }
</style>
<div class="py-3 py-md-5">
    <div class="<?= (! $activation ? 'd-flex align-items-end justify-content-center' : null); ?>">
        <div class="container-fluid">
            <div class="card border-0">
                <div class="card-body">
                    <form action="<?= current_page(); ?>" method="POST" class="--validate-form" enctype="multipart/form-data">
                        <div class="mb-3">
                            <p class="text-center text-muted">
                                <i class="mdi mdi-account-circle-outline mdi-5x text-muted"></i>
                            </p>
                        </div>
                        <div class="mb-3">
                            <p class="text-center text-muted">
                                <?= phrase('Please enter your ccount information to sign in.'); ?>
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
                        
                        <div class="row mb-5">
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

                    <?php if (get_setting('frontend_registration')): ?>
                        <?php if ((get_setting('google_client_id') && get_setting('google_client_secret')) || (get_setting('facebook_app_id') && get_setting('facebook_app_secret'))): ?>
                            <p class="text-center text-muted">
                                <?= phrase('Or sign in with your social account'); ?>
                            </p>
                            <div class="row mb-3">
                                <?php if (get_setting('google_client_id') && get_setting('google_client_secret')): ?>
                                    <div class="col-6">
                                        <div class="d-grid">
                                            <a href="<?= base_url('auth/sso/google'); ?>" class="btn btn-outline-danger btn-sm rounded-pill">
                                                <i class="mdi mdi-google"></i>
                                                Google
                                            </a>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                <?php if (get_setting('facebook_app_id') && get_setting('facebook_app_secret')): ?>
                                    <div class="col-6">
                                        <div class="d-grid">
                                            <a href="<?= base_url('auth/sso/facebook'); ?>" class="btn btn-outline-primary btn-sm rounded-pill">
                                                <i class="mdi mdi-facebook"></i>
                                                Facebook
                                            </a>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                        <div class="text-center text-muted">
                            <?= phrase('Do not have an account?'); ?>
                            <a href="<?= base_url('auth/register'); ?>" class="--xhr">
                                <b>
                                    <?= phrase('Register an Account'); ?>
                                </b>
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
