<div class="container-fluid py-3">
    <div class="row">
        <div class="col-md-5 col-xl-4">
            <a href="<?= go_to('blogs'); ?>" class="card rounded-4 text-white bg-secondary mb-3 --xhr">
                <div class="card-body py-2 px-3">
                    <div class="row align-items-center">
                        <div class="col-3">
                            <i class="mdi mdi-newspaper mdi-3x"></i>
                        </div>
                        <div class="col">
                            <h5 class="card-title mb-0">
                                <?= phrase('Blogs'); ?>
                            </h5>
                            <p class="card-text">
                                <?= phrase('Manage blog post'); ?>
                            </p>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-5 col-xl-4">
            <a href="<?= go_to('pages'); ?>" class="card rounded-4 text-white bg-secondary mb-3 --xhr">
                <div class="card-body py-2 px-3">
                    <div class="row align-items-center">
                        <div class="col-3">
                            <i class="mdi mdi-file mdi-3x"></i>
                        </div>
                        <div class="col">
                            <h5 class="card-title mb-0">
                                <?= phrase('Pages'); ?>
                            </h5>
                            <p class="card-text">
                                <?= phrase('Manage frontend pages'); ?>
                            </p>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-5 col-xl-4">
            <a href="<?= go_to('galleries'); ?>" class="card rounded-4 text-white bg-secondary mb-3 --xhr">
                <div class="card-body py-2 px-3">
                    <div class="row align-items-center">
                        <div class="col-3">
                            <i class="mdi mdi-folder-multiple-image mdi-3x"></i>
                        </div>
                        <div class="col">
                            <h5 class="card-title mb-0">
                                <?= phrase('Galleries'); ?>
                            </h5>
                            <p class="card-text">
                                <?= phrase('Manage photo albums'); ?>
                            </p>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>
    <div class="row">
        <div class="col-md-5 col-xl-4">
            <a href="<?= go_to('peoples'); ?>" class="card rounded-4 text-white bg-secondary mb-3 --xhr">
                <div class="card-body py-2 px-3">
                    <div class="row align-items-center">
                        <div class="col-3">
                            <i class="mdi mdi-account-group-outline mdi-3x"></i>
                        </div>
                        <div class="col">
                            <h5 class="card-title mb-0">
                                <?= phrase('Peoples'); ?>
                            </h5>
                            <p class="card-text">
                                <?= phrase('Manage peoples'); ?>
                            </p>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-5 col-xl-4">
            <a href="<?= go_to('partials/announcements'); ?>" class="card rounded-4 text-white bg-secondary mb-3 --xhr">
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
                                <?= phrase('Manage announcements'); ?>
                            </p>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-5 col-xl-4">
            <a href="<?= go_to('partials/carousels'); ?>" class="card rounded-4 text-white bg-secondary mb-3 --xhr">
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
                                <?= phrase('Manage carousels'); ?>
                            </p>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>
    <div class="row">
        <div class="col-md-8">
            <a href="<?= go_to('partials/faqs'); ?>" class="card rounded-4 text-white bg-secondary mb-3 --xhr">
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
                                <?= phrase('Manage frequently asked question'); ?>
                            </p>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-5 col-xl-4">
            <a href="<?= go_to('partials/media'); ?>" class="card rounded-4 text-white bg-secondary mb-3 --xhr">
                <div class="card-body py-2 px-3">
                    <div class="row align-items-center">
                        <div class="col-3">
                            <i class="mdi mdi-folder-image mdi-3x"></i>
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
