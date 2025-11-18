<div class="container-fluid py-3">
    <div class="row">
        <div class="col-md-4">
            <a href="<?= go_to('activities'); ?>" class="card rounded-4 text-white bg-secondary mb-3 --xhr">
                <div class="card-body py-2 px-3">
                    <div class="row align-items-center">
                        <div class="col-3">
                            <i class="mdi mdi-calendar-clock mdi-3x"></i>
                        </div>
                        <div class="col">
                            <h5 class="card-title mb-0">
                                <?= phrase('Activities'); ?>
                            </h5>
                            <p class="card-text">
                                <?= phrase('Show log activities.'); ?>
                            </p>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="<?= go_to('errors'); ?>" class="card rounded-4 text-white bg-secondary mb-3 --xhr">
                <div class="card-body py-2 px-3">
                    <div class="row align-items-center">
                        <div class="col-3">
                            <i class="mdi mdi-bug mdi-3x"></i>
                        </div>
                        <div class="col">
                            <h5 class="card-title mb-0">
                                <?= phrase('Errors'); ?>
                            </h5>
                            <p class="card-text">
                                <?= phrase('Show error logs.'); ?>
                            </p>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>
