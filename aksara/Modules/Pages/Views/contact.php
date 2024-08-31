<div class="position-relative">
    <div role="map" class="bg-light" data-coordinate="<?= htmlspecialchars(get_setting('office_map')); ?>" data-zoom="16" data-mousewheel="0" style="height:320px"></div>
</div>

<div class="py-3 py-md-5 bg-light gradient">
    <div class="container">
        <h3 class="mb-0">
            <?= $meta->title; ?>
        </h3>
        <p class="lead">
            <?= truncate($meta->description, 256); ?>
        </p>
    </div>
</div>

<div class="py-3 py-md-5">
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
                    <p class="lead">
                        <?= get_setting('office_address'); ?>
                    </p>
                </div>
                <div class="mb-3">
                    <label class="text-muted d-block mb-0">
                        <?= phrase('Email'); ?>
                    </label>
                    <p class="lead">
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
                            <p class="lead">
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
                            <p class="lead">
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
                            <p class="lead">
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
                            <p class="lead">
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
</div>
