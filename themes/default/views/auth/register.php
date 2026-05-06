<?php
/**
 * @var mixed $meta
 * @var mixed $captcha
 */
?>
<div class="section-padding fade-in">
    <!-- Background Wavy Shape -->
    <svg class="position-absolute top-0 d-none d-md-block hero-blob" viewBox="0 0 948 458" fill="none">
        <path fill="currentColor" d="M179.493 278.507C88.0136 187.027 42.2737 141.287 21.1376 90.2621C-7.04587 22.2238 -7.04587 -54.2238 21.1376 -122.262C42.2737 -173.287 88.0136 -219.027 179.493 -310.507C270.973 -401.986 316.713 -447.726 367.738 -468.862C435.776 -497.046 512.224 -497.046 580.262 -468.862C631.287 -447.726 677.027 -401.986 768.507 -310.507C859.986 -219.027 905.726 -173.287 926.862 -122.262C955.046 -54.2238 955.046 22.2238 926.862 90.2621C905.726 141.287 859.986 187.027 768.507 278.507C677.027 369.986 631.287 415.726 580.262 436.862C512.224 465.046 435.776 465.046 367.738 436.862C316.713 415.726 270.973 369.986 179.493 278.507Z"/>
    </svg>

    <div class="container position-relative fade-in" style="z-index: 1;">
        <div class="row">
            <div class="col-lg-6 offset-lg-3">
                <div class="text-center mb-5">
                    <i class="<?= $meta->icon; ?> mdi-5x text-secondary mb-3"></i>
                    <h3 class="fw-bold mb-2"><?= $meta->title; ?></h3>
                    <p class="text-muted"><?= truncate($meta->description, 256); ?></p>
                </div>
                <form action="<?= current_page(); ?>" method="POST" class="--validate-form">
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group mb-4">
                                <label class="d-block fw-bold" for="first_name_input">
                                    <?= phrase('First Name'); ?>
                                </label>
                                <input type="text" name="first_name" class="form-control" id="first_name_input" placeholder="<?= phrase('Your first name'); ?>" autocomplete="off" maxlength="64" />
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group mb-4">
                                <label class="d-block fw-bold" for="last_name_input">
                                    <?= phrase('Last Name'); ?>
                                </label>
                                <input type="text" name="last_name" class="form-control" id="last_name_input" placeholder="<?= phrase('Your last name'); ?>" autocomplete="off" maxlength="64" />
                            </div>
                        </div>
                    </div>
                    <div class="form-group mb-4">
                        <label class="d-block fw-bold" for="email_input">
                            <?= phrase('Email Address'); ?>
                        </label>
                        <input type="email" name="email" class="form-control" id="email_input" placeholder="<?= phrase('Enter your email address'); ?>" autocomplete="off" maxlength="128" />
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group mb-4">
                                <label class="d-block fw-bold" for="username_input">
                                    <?= phrase('Username'); ?>
                                </label>
                                <input type="text" name="username" class="form-control" id="username_input" placeholder="<?= phrase('Choose your username'); ?>" autocomplete="off" maxlength="32" />
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group mb-4 position-relative">
                                <label class="d-block fw-bold" for="password_input">
                                    <?= phrase('Password'); ?>
                                </label>
                                <div class="input-group">
                                    <input type="password" name="password" class="form-control rounded-end" id="password_input" placeholder="<?= phrase('Minimum'); ?> 6 <?= phrase('characters'); ?>" maxlength="32" style="border-right:0" />
                                    <span class="input-group-text bg-white rounded-start" style="border-left:0">
                                        <i class="mdi mdi-eye-outline password-peek" data-parent=".form-group" data-peek=".form-control" style="width:22px"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-4">
                                <label class="d-block fw-bold" for="email_input">
                                    <?= phrase('Phone Number'); ?>
                                </label>
                                <input type="phone" name="phone" class="form-control" id="phone_input" placeholder="0812XXXX" autocomplete="off" maxlength="16" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-4">
                                <label class="d-block" for="captcha_input">
                                    <?= phrase('Enter shown character'); ?>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-dark border-dark p-0">
                                        <?php
                                            if ($captcha->string) {
                                                echo '<b class="text-light pe-3 ps-3">' . $captcha->string . '</b>';
                                            } else {
                                                echo '<img src="' . $captcha->image . '" class="img-fluid" alt="..." />';
                                            }
                                        ?>
                                    </span>
                                    <input type="text" name="captcha" class="form-control" id="captcha_input" placeholder="XXXXXX" maxlength="32" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 mb-4">
                        <label class="text-muted d-block">
                            <?= phrase('By submitting this form, you are agree about all future action related to your account related to the {{terms_and_conditions}}', ['terms_and_conditions' => '<a href="' . base_url('pages/guidelines/terms-and-conditions') . '" target="_blank"><b>' . phrase('Terms and Conditions') . '</b></a>']); ?>
                        </label>
                    </div>
                    <div class="mb-3">
                        <div class="d-grid">
                            <button type="submit" class="btn btn-outline-secondary rounded-pill">
                                <i class="mdi mdi-check"></i>
                                <?= phrase('Register Account'); ?>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
