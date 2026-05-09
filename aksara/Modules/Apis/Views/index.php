<div class="container-fluid py-3">
    <div class="row">
        <div class="col-md-5 col-xl-4">
            <a href="<?= go_to('services'); ?>" class="card rounded-4 text-white bg-secondary mb-3 --xhr">
                <div class="card-body py-2 px-3">
                    <div class="row align-items-center">
                        <div class="col-3">
                            <i class="mdi mdi-link-variant mdi-3x"></i>
                        </div>
                        <div class="col">
                            <h5 class="card-title mb-0">
                                <?= phrase('Services'); ?>
                            </h5>
                            <p class="card-text">
                                <?= phrase('Manage service URLs'); ?>
                            </p>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-5 col-xl-4">
            <a href="<?= go_to('clients'); ?>" class="card rounded-4 text-white bg-secondary mb-3 --xhr">
                <div class="card-body py-2 px-3">
                    <div class="row align-items-center">
                        <div class="col-3">
                            <i class="mdi mdi-account-check-outline mdi-3x"></i>
                        </div>
                        <div class="col">
                            <h5 class="card-title mb-0">
                                <?= phrase('Clients'); ?>
                            </h5>
                            <p class="card-text">
                                <?= phrase('Manage API Clients'); ?>
                            </p>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>
    <div class="row">
        <div class="col-md-5 col-xl-4">
            <a href="<?= go_to('apis'); ?>" class="card rounded-4 text-white bg-secondary mb-3 --xhr">
                <div class="card-body py-2 px-3">
                    <div class="row align-items-center">
                        <div class="col-3">
                            <i class="mdi mdi-security-network mdi-3x"></i>
                        </div>
                        <div class="col">
                            <h5 class="card-title mb-0">
                                <?= phrase('Permissions'); ?>
                            </h5>
                            <p class="card-text">
                                <?= phrase('Manage client permissions'); ?>
                            </p>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-5 col-xl-4">
            <a href="<?= go_to('debugger'); ?>" class="card rounded-4 text-white bg-secondary mb-3 --xhr">
                <div class="card-body py-2 px-3">
                    <div class="row align-items-center">
                        <div class="col-3">
                            <i class="mdi mdi-android-debug-bridge mdi-3x"></i>
                        </div>
                        <div class="col">
                            <h5 class="card-title mb-0">
                                <?= phrase('Debug Tools'); ?>
                            </h5>
                            <p class="card-text">
                                <?= phrase('Debug created API Services'); ?>
                            </p>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>
