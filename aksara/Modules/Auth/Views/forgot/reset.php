<?php
/**
 * @var mixed $meta
 */
?>
<section class="section-padding">
    <div class="container position-relative text-center text-md-start fade-in">
        <h1 class="display-4 fw-bold text-dark">
            <?= $meta->title; ?>
        </h1>
        <p class="fs-5 text-muted mb-0">
            <?= truncate($meta->description, 256); ?>
        </p>
    </div>
</section>
<section class="section-padding">
    <div class="container">
        <div class="row">
            <div class="col-md-8 offset-md-2 col-md-8 offset-md-2 col-lg-6 offset-lg-3">
                <form action="<?= current_page(); ?>" method="POST" class="--validate-form">
                    <div class="text-center text-md-start mb-3">
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
</section>
