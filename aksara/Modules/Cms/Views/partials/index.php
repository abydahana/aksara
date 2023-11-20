<div class="container-fluid py-3">
    <div class="row">
        <div class="col-md-5 col-xl-4">
            <a href="<?= go_to('announcements'); ?>" class="card rounded-4 text-white bg-secondary mb-3 --xhr">
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
            <a href="<?= go_to('carousels'); ?>" class="card rounded-4 text-white bg-secondary mb-3 --xhr">
                <div class="card-body py-2 px-3">
                    <div class="row align-items-center">
                        <div class="col-3">
                            <i class="mdi mdi-view-carousel mdi-3x"></i>
                        </div>
                        <div class="col">
                            <h5 class="card-title mb-0">
                                <?= phrase('Carousels'); ?>
                            </h5>
                            <p class="card-text">
                                <?= phrase('Manage Carousels'); ?>
                            </p>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>
    <div class="row">
        <div class="col-md-5 col-xl-4">
            <a href="<?= go_to('faqs'); ?>" class="card rounded-4 text-white bg-secondary mb-3 --xhr">
                <div class="card-body py-2 px-3">
                    <div class="row align-items-center">
                        <div class="col-3">
                            <i class="mdi mdi-help-circle-outline mdi-3x"></i>
                        </div>
                        <div class="col">
                            <h5 class="card-title mb-0">
                                <?= phrase('FAQ'); ?>
                            </h5>
                            <p class="card-text">
                                <?= phrase('Manage FAQ'); ?>
                            </p>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-5 col-xl-4">
            <a href="<?= go_to('media'); ?>" class="card rounded-4 text-white bg-secondary mb-3 --xhr">
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
