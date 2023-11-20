<div class="container-fluid py-3">
    <?php if (get_userdata('group_id') == 1): ?>
    <div class="row">
        <div class="col-md-5 col-xl-4">
            <a href="<?= go_to('users'); ?>" class="card rounded-4 text-white bg-secondary mb-3 --xhr">
                <div class="card-body py-2 px-3">
                    <div class="row align-items-center">
                        <div class="col-3">
                            <i class="mdi mdi-account-group mdi-3x"></i>
                        </div>
                        <div class="col">
                            <h5 class="card-title mb-0">
                                <?= phrase('Users'); ?>
                            </h5>
                            <p class="card-text">
                                <?= phrase('Manage Users'); ?>
                            </p>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-5 col-xl-4">
            <a href="<?= go_to('groups'); ?>" class="card rounded-4 text-white bg-secondary mb-3 --xhr">
                <div class="card-body py-2 px-3">
                    <div class="row align-items-center">
                        <div class="col-3">
                            <i class="mdi mdi-sitemap mdi-3x"></i>
                        </div>
                        <div class="col">
                            <h5 class="card-title mb-0">
                                <?= phrase('Groups'); ?>
                            </h5>
                            <p class="card-text">
                                <?= phrase('Manage Groups'); ?>
                            </p>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-5 col-xl-4">
            <a href="<?= go_to('groups/privileges'); ?>" class="card rounded-4 text-white bg-secondary mb-3 --xhr">
                <div class="card-body py-2 px-3">
                    <div class="row align-items-center">
                        <div class="col-3">
                            <i class="mdi mdi-account-check-outline mdi-3x"></i>
                        </div>
                        <div class="col">
                            <h5 class="card-title mb-0">
                                <?= phrase('Privileges'); ?>
                            </h5>
                            <p class="card-text">
                                <?= phrase('Manage Group Privileges'); ?>
                            </p>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>
    <div class="row">
        <div class="col-md-5 col-xl-4">
            <a href="<?= go_to('settings'); ?>" class="card rounded-4 text-white bg-secondary mb-3 --xhr">
                <div class="card-body py-2 px-3">
                    <div class="row align-items-center">
                        <div class="col-3">
                            <i class="mdi mdi-wrench mdi-flip-h mdi-3x"></i>
                        </div>
                        <div class="col">
                            <h5 class="card-title mb-0">
                                <?= phrase('Site Settings'); ?>
                            </h5>
                            <p class="card-text">
                                <?= phrase('Update Site Configuration'); ?>
                            </p>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-5 col-xl-4">
            <a href="<?= go_to('menus'); ?>" class="card rounded-4 text-white bg-secondary mb-3 --xhr">
                <div class="card-body py-2 px-3">
                    <div class="row align-items-center">
                        <div class="col-3">
                            <i class="mdi mdi-menu mdi-3x"></i>
                        </div>
                        <div class="col">
                            <h5 class="card-title mb-0">
                                <?= phrase('Menus'); ?>
                            </h5>
                            <p class="card-text">
                                <?= phrase('Manage Menus'); ?>
                            </p>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-5 col-xl-4">
            <a href="<?= go_to('translations'); ?>" class="card rounded-4 text-white bg-secondary mb-3 --xhr">
                <div class="card-body py-2 px-3">
                    <div class="row align-items-center">
                        <div class="col-3">
                            <i class="mdi mdi-translate mdi-3x"></i>
                        </div>
                        <div class="col">
                            <h5 class="card-title mb-0">
                                <?= phrase('Translations'); ?>
                            </h5>
                            <p class="card-text">
                                <?= phrase('Manage Translations'); ?>
                            </p>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>
    <div class="row">
        <div class="col-md-5 col-xl-4">
            <a href="<?= go_to('countries'); ?>" class="card rounded-4 text-white bg-secondary mb-3 --xhr">
                <div class="card-body py-2 px-3">
                    <div class="row align-items-center">
                        <div class="col-3">
                            <i class="mdi mdi-account-edit mdi-3x"></i>
                        </div>
                        <div class="col">
                            <h5 class="card-title mb-0">
                                <?= phrase('Countries'); ?>
                            </h5>
                            <p class="card-text">
                                <?= phrase('Manage Countries'); ?>
                            </p>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-5 col-xl-4">
            <a href="<?= go_to('logs'); ?>" class="card rounded-4 text-white bg-secondary mb-3 --xhr">
                <div class="card-body py-2 px-3">
                    <div class="row align-items-center">
                        <div class="col-3">
                            <i class="mdi mdi-information-outline mdi-3x"></i>
                        </div>
                        <div class="col">
                            <h5 class="card-title mb-0">
                                <?= phrase('Logs'); ?>
                            </h5>
                            <p class="card-text">
                                <?= phrase('Check Application Logs'); ?>
                            </p>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>
    <?php endif; ?>
    <div class="row">
        <div class="col-md-5 col-xl-4">
            <a href="<?= go_to('account'); ?>" class="card rounded-4 text-white bg-secondary mb-3 --xhr">
                <div class="card-body py-2 px-3">
                    <div class="row align-items-center">
                        <div class="col-3">
                            <i class="mdi mdi-cogs mdi-3x"></i>
                        </div>
                        <div class="col">
                            <h5 class="card-title mb-0">
                                <?= phrase('Account'); ?>
                            </h5>
                            <p class="card-text">
                                <?= phrase('Update Your Account'); ?>
                            </p>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>
