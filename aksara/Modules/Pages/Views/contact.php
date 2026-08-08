<?php

/**
 * @var mixed $meta
 * @var object $results
 * @var mixed $captcha
 */
?>

<div class="position-relative">
    <div data-role="map" class="bg-light" data-geojson="<?= htmlspecialchars(get_setting('office_map')); ?>" data-zoom="16" data-mousewheel="0" style="height:320px"></div>
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

<section class="section-padding pt-0 fade-in">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h3 class="mb-3">
                    <?= get_setting('office_name'); ?>
                </h2>
                <div class="mb-3">
                    <span class="text-muted d-block mb-0"><?= phrase('Address'); ?></span>
                    <p class="fs-5">
                        <?= get_setting('office_address'); ?>
                    </p>
                </div>
                <div class="mb-3">
                    <span class="text-muted d-block mb-0"><?= phrase('Email'); ?></span>
                    <p class="fs-5">
                        <a href="mailto:<?= get_setting('office_email'); ?>" target="_blank">
                            <?= get_setting('office_email'); ?>
                        </a>
                    </p>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="mb-3">
                            <span class="text-muted d-block mb-0"><?= phrase('Phone'); ?></span>
                            <p class="fs-5">
                                <a href="tel:<?= get_setting('office_phone'); ?>" target="_blank">
                                    <?= get_setting('office_phone'); ?>
                                </a>
                            </p>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="mb-3">
                            <span class="text-muted d-block mb-0"><?= phrase('WhatsApp'); ?></span>
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
                            <span class="text-muted d-block mb-0"><?= phrase('Twitter'); ?></span>
                            <p class="fs-5">
                                <a href="//twitter.com/<?= get_setting('twitter_username'); ?>" target="_blank">
                                    <?= get_setting('twitter_username'); ?>
                                </a>
                            </p>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="mb-3">
                            <span class="text-muted d-block mb-0"><?= phrase('Instagram'); ?></span>
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
                <?php if (service('request')->getGet('success')): ?>
                    <div class="alert alert-success callout p-4 rounded-4">
                        <h2><?= phrase('Your message has been sent.'); ?></h2>
                        <p><?= phrase('We have received your message and will follow up as soon as possible using the phone number or email address you provided.'); ?></p>
                        <a href="<?= go_to(null, ['success' => null]); ?>" class="btn btn-success rounded-pill --xhr">
                            <?= phrase('Send another message') ?> <i class="mdi mdi-arrow-right"></i>
                        </a>
                    </div>
                <?php else: ?>
                <div class="card border-light-subtle rounded-5 fade-in">
                    <div class="card-body p-4">
                        <h3 class="mb-3">
                            <?= phrase('Direct Inquiry'); ?>
                        </h2>
                        <form action="<?= current_page(); ?>" method="POST" class="--validate-form">
                            <?= form_input($results->field_data->sender_full_name); ?>
                            <div class="row">
                                <div class="col-md-6">
                                    <?= form_input($results->field_data->sender_phone); ?>
                                </div>
                                <div class="col-md-6">
                                    <?= form_input($results->field_data->sender_email); ?>
                                </div>
                            </div>
                            <?= form_input($results->field_data->subject); ?>
                            <?= form_input($results->field_data->messages); ?>
                            <div class="form-group mb-4">
                                <div class="input-group">
                                    <span class="input-group-text bg-white p-0 captcha-refresh" style="cursor: pointer;" data-bs-toggle="tooltip" title="<?= phrase('Reload Captcha'); ?>">
                                        <?php
                                        if ($captcha->string) {
                                            echo '<b class="text-dark pe-3 ps-3">' . $captcha->string . '</b>';
                                        } else {
                                            echo '<img src="' . $captcha->image . '" class="img-fluid" alt="CAPTCHA" />';
                                        }
                                        ?>
                                    </span>
                                    <input type="text" name="captcha" class="form-control" id="captcha_input" placeholder="<?= phrase('Bot Challenge'); ?>" maxlength="32" />
                                </div>
                            </div>
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <?= form_input($results->field_data->copy); ?>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-grid">
                                        <button type="submit" class="btn btn-primary rounded-pill">
                                            <?= phrase('Send Message'); ?> <i class="mdi mdi-send"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
