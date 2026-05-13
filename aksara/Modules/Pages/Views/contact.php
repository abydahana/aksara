<?php
/**
 * @var mixed $meta
 * @var mixed $captcha
 */
?>
<div class="position-relative">
    <div role="map" class="bg-light" data-coordinate="<?= htmlspecialchars(get_setting('office_map')); ?>" data-zoom="16" data-mousewheel="0" style="height:320px"></div>
</div>

<section class="section-padding fade-in">
    <div class="container text-center text-md-start">
        <h1 class="display-4 fw-bold text-dark">
            <?= $meta->title; ?>
        </h1>
        <p class="fs-5 text-muted mb-0">
            <?= truncate($meta->description, 256); ?>
        </p>
    </div>
</section>

<section class="section-padding fade-in">
    <div class="container">
        <div class="row">
            <div class="col-lg-6">
                <h3 class="mb-3">
                    <?= get_setting('office_name'); ?>
                </h3>
                <div class="mb-3">
                    <label class="text-muted d-block mb-0">
                        <?= phrase('Address'); ?>
                    </label>
                    <p class="fs-5">
                        <?= get_setting('office_address'); ?>
                    </p>
                </div>
                <div class="mb-3">
                    <label class="text-muted d-block mb-0">
                        <?= phrase('Email'); ?>
                    </label>
                    <p class="fs-5">
                        <a href="mailto:<?= get_setting('office_email'); ?>" target="_blank">
                            <?= get_setting('office_email'); ?>
                        </a>
                    </p>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="mb-3">
                            <label class="text-muted d-block mb-0">
                                <?= phrase('Phone'); ?>
                            </label>
                            <p class="fs-5">
                                <a href="tel:<?= get_setting('office_phone'); ?>" target="_blank">
                                    <?= get_setting('office_phone'); ?>
                                </a>
                            </p>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="mb-3">
                            <label class="text-muted d-block mb-0">
                                <?= phrase('WhatsApp'); ?>
                            </label>
                            <p class="fs-5">
                                <a href="https://api.whatsapp.com/send?phone=<?= str_replace(['+', '-', ' '], '', get_setting('whatsapp_number')); ?>&text=<?= phrase('Hello') . '%20' . get_setting('app_name'); ?>..." target="_blank">
                                    <?= get_setting('whatsapp_number'); ?>
                                </a>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="mb-3">
                            <label class="text-muted d-block mb-0">
                                <?= phrase('Twitter'); ?>
                            </label>
                            <p class="fs-5">
                                <a href="//twitter.com/<?= get_setting('twitter_username'); ?>" target="_blank">
                                    <?= get_setting('twitter_username'); ?>
                                </a>
                            </p>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="mb-3">
                            <label class="text-muted d-block mb-0">
                                <?= phrase('Instagram'); ?>
                            </label>
                            <p class="fs-5">
                                <a href="//instagram.com/<?= get_setting('instagram_username'); ?>" target="_blank">
                                    <?= get_setting('instagram_username'); ?>
                                </a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card border-0 rounded-4 shadow">
                    <div class="card-body p-4">
                        <h3 class="mb-3">
                            <?= phrase('Direct Inquiry'); ?>
                        </h3>
                        <form action="<?= current_page(); ?>" method="POST" class="--validate-form">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <input type="text" name="full_name" class="form-control" placeholder="<?= phrase('Full Name'); ?>" id="full_name_input" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <input type="text" name="email" class="form-control" placeholder="<?= phrase('Email Address'); ?>" id="email_input" />
                                    </div>
                                </div>
                            </div>
                            <div class="form-group mb-3">
                                <input type="text" name="subject" class="form-control" placeholder="<?= phrase('Subject'); ?>" id="subject_input" />
                            </div>
                            <div class="form-group mb-3">
                                <textarea type="text" name="messages" class="form-control" placeholder="<?= phrase('Messages'); ?>" rows="1" id="messages_input"></textarea>
                            </div>
                            <div class="form-group mb-4">
                                <div class="input-group">
                                    <span class="input-group-text bg-white p-0 captcha-refresh" style="cursor: pointer;" data-bs-toggle="tooltip" title="<?= phrase('Reload Captcha'); ?>">
                                        <?php
                                            if ($captcha->string) {
                                                echo '<b class="text-dark pe-3 ps-3">' . $captcha->string . '</b>';
                                            } else {
                                                echo '<img src="' . $captcha->image . '" class="img-fluid" alt="..." />';
                                            }
                                        ?>
                                    </span>
                                    <input type="text" name="captcha" class="form-control" id="captcha_input" placeholder="<?= phrase('Bot Challenge'); ?>" maxlength="32" />
                                </div>
                            </div>
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <div class="form-check form-switch">
                                        <input type="checkbox" name="copy" class="form-check-input" value="1" id="copy_input" checked />
                                        <label class="form-check-label" for="copy_input"> <?= phrase('Request a copy'); ?> </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-grid">
                                        <button type="submit" class="btn btn-primary">
                                            <?= phrase('Send Message'); ?> <i class="mdi mdi-send"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
