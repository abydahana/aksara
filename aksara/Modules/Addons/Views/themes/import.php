<div class="container-fluid">
    <div class="sticky-top bg-body overflow-x-auto py-1 px-3 mx--3 mb-3 border-bottom">
        <ul class="nav nav-pills nav-pills-dark flex-nowrap">
            <li class="nav-item">
                <a href="<?= go_to('../'); ?>" class="nav-link rounded-pill no-wrap --xhr">
                    <i class="mdi mdi-cart"></i>
                    <?= phrase('Market'); ?>
                </a>
            </li>
            <li class="nav-item">
                <a href="<?= go_to('../themes'); ?>" class="nav-link rounded-pill no-wrap --xhr">
                    <i class="mdi mdi-palette"></i>
                    <?= phrase('Installed Theme'); ?>
                </a>
            </li>
            <li class="nav-item">
                <a href="<?= go_to('../modules'); ?>" class="nav-link rounded-pill no-wrap --xhr">
                    <i class="mdi mdi-puzzle"></i>
                    <?= phrase('Installed Module'); ?>
                </a>
            </li>
        </ul>
    </div>
    <div class="row">
        <div class="col-md-6">
            <form action="<?= current_page(); ?>" method="POST" class="--validate-form" enctype="multipart/form-data">
                <h5>
                    <?= phrase('Notes before you import'); ?>
                </h5>
                <ul class="mb-5">
                    <li>
                        <?= phrase('Make sure the package you want to import is downloaded from the official market.'); ?> (<a href="//www.aksaracms.com/market" class="text-primary fw-bold" target="_blank">Aksara Market</a>);
                    </li>
                    <li>
                        <?= phrase('Packages downloaded from outside the official market may contain exploit tools that risk your server.'); ?>;
                    </li>
                    <li>
                        <?= phrase('Make sure the package has passed the test on your development server.'); ?>;
                    </li>
                    <li>
                        <?= phrase('Import at your own risk.'); ?>.
                    </li>
                </ul>
                <div class="card rounded-4">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-8 col-md-9">
                                <div class="input-group mb-3">
                                    <input type="file" name="file" class="form-control rounded-pill" />
                                </div>
                            </div>
                            <div class="col-sm-4 col-md-3">
                                <div class="input-group d-grid mb-3">
                                    <button type="submit" class="btn btn-dark rounded-pill">
                                        <i class="mdi mdi-import"></i>
                                        <?= phrase('Import'); ?>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-check form-switch">
                                <input type="checkbox" name="upgrade" data-role="boolean" value="1" class="form-check-input" id="upgrade_input" checked="" autocomplete="off">
                                <label class="form-check-label" for="upgrade_input"> <?= phrase('Upgrade existing theme.'); ?> </label>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
