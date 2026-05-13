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
            <div class="col-md-8 col-lg-6">
                <form action="<?= current_page(); ?>" method="POST" class="--validate-form">
                    <div class="text-center text-md-start mb-3">
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
</section>
