<footer id="footer-wrapper" class="pt-5 pb-5 bg-white mt-5 position-relative overflow-hidden">
    <div class="border-fade">
        <div class="container">
            <div class="pt-5">
                <div class="row">
                    <div class="col-lg-3 mb-4">
                        <a href="<?= base_url(); ?>">
                            <img src="<?= get_image('settings', get_setting('app_icon'), 'icon'); ?>" class="img-fluid mb-3" />
                        </a>
                        <p class="text-muted mb-4">
                            <?= get_setting('app_description') ?: 'Maps for developers. Build amazing map applications with our powerful tools and APIs.'; ?>
                        </p>
                        <div class="d-flex gap-3">
                            <?php if (get_setting('twitter_username')): ?>
                                <a href="https://twitter.com/<?= get_setting('twitter_username'); ?>" class="text-secondary fs-5" target="_blank"><i class="mdi mdi-twitter"></i></a>
                            <?php endif; ?>
                            <?php if (get_setting('instagram_username')): ?>
                                <a href="https://instagram.com/<?= get_setting('instagram_username'); ?>" class="text-secondary fs-5" target="_blank"><i class="mdi mdi-instagram"></i></a>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="col-6 col-sm-4 col-lg-3 mb-4">
                        <h6 class="fw-bold mb-3 text-dark"><?= phrase('Featured'); ?></h6>
                        <ul class="list-unstyled">
                            <li class="mb-2"><a href="<?= base_url('blogs'); ?>" class="text-muted text-decoration-none --xhr"><?= phrase('News'); ?></a></li>
                            <li class="mb-2"><a href="<?= base_url('galleries'); ?>" class="text-muted text-decoration-none --xhr"><?= phrase('Galleries'); ?></a></li>
                            <li class="mb-2"><a href="<?= base_url('videos'); ?>" class="text-muted text-decoration-none --xhr"><?= phrase('Videos'); ?></a></li>
                            <li class="mb-2"><a href="<?= base_url('peoples'); ?>" class="text-muted text-decoration-none --xhr"><?= phrase('Peoples'); ?></a></li>
                            <li class="mb-2"><a href="<?= base_url('announcements'); ?>" class="text-muted text-decoration-none --xhr"><?= phrase('Announcements'); ?></a></li>
                            <li class="mb-2"><a href="<?= base_url('testimonials'); ?>" class="text-muted text-decoration-none --xhr"><?= phrase('Testimonials'); ?></a></li>
                        </ul>
                    </div>
                    
                    <div class="col-6 col-sm-4 col-lg-3 mb-4">
                        <h6 class="fw-bold mb-3 text-dark"><?= phrase('Knowledge Center'); ?></h6>
                        <ul class="list-unstyled">
                            <li class="mb-2"><a href="//www.aksaracms.com/pages/documentation" target="_blank" class="text-muted text-decoration-none"><?= phrase('Documentation'); ?></a></li>
                            <li class="mb-2"><a href="//www.aksaracms.com/pages/features" target="_blank" class="text-muted text-decoration-none"><?= phrase('Features'); ?></a></li>
                            <li class="mb-2"><a href="//www.aksaracms.com/pages/faqs" target="_blank" class="text-muted text-decoration-none"><?= phrase('FAQs'); ?></a></li>
                            <li class="mb-2"><a href="//www.aksaracms.com/pages/terms-and-conditions" target="_blank" class="text-muted text-decoration-none"><?= phrase('Terms and Conditions'); ?></a></li>
                            <li class="mb-2"><a href="//www.aksaracms.com/pages/privacy-policy" target="_blank" class="text-muted text-decoration-none"><?= phrase('Privacy Policy'); ?></a></li>
                        </ul>
                    </div>
                    
                    <div class="col-12 col-sm-4 col-lg-3 mb-4" style="background:url(<?= get_theme_asset('images/map_bg.png'); ?>) center center no-repeat; background-size:cover">
                        <h6 class="fw-bold mb-3 text-dark"><?= phrase('Contact Us'); ?></h6>
                        <div class="mb-4">
                            <?php if (get_setting('office_email')): ?>
                                <div class="d-flex align-items-center mb-2 text-muted">
                                    <i class="mdi mdi-at me-2 fs-5"></i>
                                    <a href="mailto:<?= get_setting('office_email'); ?>" class="text-muted text-decoration-none"><?= get_setting('office_email'); ?></a>
                                </div>
                            <?php endif; ?>
                            <?php if (get_setting('office_phone')): ?>
                                <div class="d-flex align-items-center mb-2 text-muted">
                                    <i class="mdi mdi-phone text-success me-2 fs-5"></i>
                                    <a href="tel:<?= get_setting('office_phone'); ?>" class="text-muted text-decoration-none"><?= get_setting('office_phone'); ?></a>
                                </div>
                            <?php endif; ?>
                            <?php if (get_setting('whatsapp_number')): ?>
                                <div class="d-flex align-items-center mb-2 text-muted">
                                    <i class="mdi mdi-whatsapp text-success me-2 fs-5"></i>
                                    <a href="https://api.whatsapp.com/send?phone=<?= str_replace(['+', '-', ' '], [null, null, null], get_setting('whatsapp_number')); ?>&text=<?= phrase('Hello') . '%20' . get_setting('app_name'); ?>..." target="_blank" class="text-muted text-decoration-none"><?= get_setting('whatsapp_number'); ?></a>
                                </div>
                            <?php endif; ?>
                            <?php if (get_setting('office_address')): ?>
                                <div class="d-flex align-items-start text-muted mt-3">
                                    <i class="mdi mdi-google-maps text-danger me-2 fs-5 mt-1"></i>
                                    <span class="lh-sm"><?= nl2br(get_setting('office_address')); ?></span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="pt-4 mt-2">
            <div class="row">
                <div class="col-md-6 text-center text-md-start">
                    <span class="text-muted">
                        &copy; <?= date('Y'); ?> <?= get_setting('office_name') ?: 'WebGIS'; ?>. All rights reserved.
                    </span>
                </div>
                <div class="col-md-6 text-center text-md-end mt-2 mt-md-0">
                    <span class="text-muted">
                        <i class="mdi mdi-earth"></i> <?= get_user_language(); ?>
                    </span>
                </div>
            </div>
        </div>
    </div>
</footer>
