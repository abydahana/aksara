<div class="container-fluid py-3">
    <div class="row">
        <div class="col-md-5 col-xl-4">
            <a href="<?= go_to('announcements'); ?>" class="card rounded-4 mb-3 --xhr">
                <div class="card-body py-2 px-3">
                    <div class="row align-items-center">
                        <div class="col-3">
                            <i class="mdi mdi-bullhorn-outline mdi-3x"></i>
                        </div>
                        <div class="col">
                            <h5 class="card-title mb-0">
                                <?= phrase('Announcements'); ?>
                            </h5>
                            <p class="card-text">
                                <?= phrase('Manage Announcements'); ?>
                            </p>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-5 col-xl-4">
            <a href="<?= go_to('testimonials'); ?>" class="card rounded-4 mb-3 --xhr">
                <div class="card-body py-2 px-3">
                    <div class="row align-items-center">
                        <div class="col-3">
                            <i class="mdi mdi-format-quote-close-outline mdi-3x"></i>
                        </div>
                        <div class="col">
                            <h5 class="card-title mb-0">
                                <?= phrase('Testimonials'); ?>
                            </h5>
                            <p class="card-text">
                                <?= phrase('Manage Testimonials'); ?>
                            </p>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>
    <div class="row">
        <div class="col-md-5 col-md-10 col-xl-4 col-xl-8">
            <a href="<?= go_to('media'); ?>" class="card rounded-4 mb-3 --xhr">
                <div class="card-body py-2 px-3">
                    <div class="row align-items-center">
                        <div class="col-3">
                            <i class="mdi mdi-folder-multiple-image mdi-3x"></i>
                        </div>
                        <div class="col">
                            <h5 class="card-title mb-0">
                                <?= phrase('Media'); ?>
                            </h5>
                            <p class="card-text">
                                <?= phrase('Manage uploaded media'); ?>
                            </p>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>
