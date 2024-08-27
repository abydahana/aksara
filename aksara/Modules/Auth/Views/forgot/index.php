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
            <div class="col-md-8 offset-md-2 col-md-8 offset-md-2 col-lg-6 offset-lg-3">
                <form action="<?= current_page(); ?>" method="POST" class="--validate-form">
                    <div class="mb-3">
                        <label class="d-block text-muted mb-3" for="username_input">
                            <?= phrase('Enter your valid username or email to request a password reset link.'); ?>
                        </label>
                        <input type="text" name="username" class="form-control" id="username_input" placeholder="<?= phrase('Enter your username or email'); ?>" />
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
