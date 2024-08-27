<div class="py-3 py-md-5 bg-light">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-3 col-sm-2 col-md-1">
                <i class="<?= $meta->icon; ?> mdi-4x"></i>
            </div>
            <div class="col-9 col-sm-10 col-md-11">
                <h3 class="mb-0<?= (! $meta->description ? ' mt-3' : null); ?>">
                    <?= $meta->title; ?>
                </h3>
                <p class="lead">
                    <?= truncate($meta->description, 256); ?>
                </p>
            </div>
        </div>
    </div>
</div>
<div class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-md-8 offset-md-2 col-lg-6 offset-lg-3">
                <form action="<?= current_page(); ?>" method="POST" class="--validate-form">
                    <div class="mb-3">
                        <label class="d-block text-muted" for="password_input">
                            <?= phrase('New Password'); ?>
                        </label>
                        <input type="password" name="password" class="form-control" id="password_input" placeholder="<?= phrase('Enter your new password'); ?>" />
                    </div>
                    <div class="mb-3">
                        <label class="d-block text-muted" for="confirm_password_input">
                            <?= phrase('Confirm New Password'); ?>
                        </label>
                        <input type="password" name="confirm_password" class="form-control" id="confirm_password_input" placeholder="<?= phrase('Confirm your new password'); ?>" />
                    </div>
                    <div class="mb-3">
                        <div class="row">
                            <div class="col-6">
                                <div class="d-grid">
                                    <a href="<?= current_page('../'); ?>" class="btn btn-light --xhr">
                                        <i class="mdi mdi-arrow-left"></i>
                                        <?= phrase('Back'); ?>
                                    </a>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="mdi mdi-check"></i> 
                                        <?= phrase('Reset Password'); ?>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
