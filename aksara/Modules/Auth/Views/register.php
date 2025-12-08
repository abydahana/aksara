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
<div class="py-3 py-md-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-6 offset-lg-3">
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
                            <?= phrase('By submitting this form, you are agree about all future action related to your account related to the {{term_of_service}}', ['terms_and_conditions' => '<a href="' . base_url('pages/guidelines/terms-and-conditions') . '" target="_blank"><b>' . phrase('Terms and Conditions') . '</b></a>']); ?>
                        </label>
                    </div>
                    <div class="mb-3">
                        <div class="d-grid">
                            <button type="submit" class="btn btn-outline-primary rounded-pill">
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
