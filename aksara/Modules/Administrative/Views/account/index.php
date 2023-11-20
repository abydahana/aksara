<div class="container-fluid py-3">
    <div class="row">
        <div class="col-md-4">
            <a href="<?= go_to('edit'); ?>" class="card text-white bg-secondary mb-3 rounded-4 --xhr">
                <div class="card-body pt-2 pe-3 pb-2 ps-3">
                    <div class="row align-items-center">
                        <div class="col-3">
                            <i class="mdi mdi-account-convert mdi-3x"></i>
                        </div>
                        <div class="col">
                            <h5 class="card-title mb-0">
                                <?= phrase('Update Profile'); ?>
                            </h5>
                            <p class="card-text">
                                <?= phrase('Update your profile information'); ?>
                            </p>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="<?= go_to('logs'); ?>" class="card text-white bg-secondary mb-3 rounded-4 --xhr">
                <div class="card-body pt-2 pe-3 pb-2 ps-3">
                    <div class="row align-items-center">
                        <div class="col-3">
                            <i class="mdi mdi-account-key-outline mdi-3x"></i>
                        </div>
                        <div class="col">
                            <h5 class="card-title mb-0">
                                <?= phrase('Login Activity'); ?>
                            </h5>
                            <p class="card-text">
                                <?= phrase('See your login activity'); ?>
                            </p>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>
