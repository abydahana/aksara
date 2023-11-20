<div class="container-fluid">
    <div class="row border-bottom bg-white mb-3 sticky-top" style="overflow-x:auto">
        <ul class="nav" style="flex-wrap: nowrap">
            <li class="nav-item">
                <a href="<?= go_to('../'); ?>" class="nav-link no-wrap --xhr">
                    <i class="mdi mdi-cart"></i>
                    <?= phrase('Market'); ?>
                </a>
            </li>
            <li class="nav-item">
                <a href="<?= current_page('../'); ?>" class="nav-link no-wrap --xhr">
                    <i class="mdi mdi-palette"></i>
                    <?= phrase('Installed Theme'); ?>
                </a>
            </li>
            <li class="nav-item">
                <a href="<?= go_to('../modules'); ?>" class="nav-link no-wrap --xhr">
                    <i class="mdi mdi-puzzle"></i>
                    <?= phrase('Installed Module'); ?>
                </a>
            </li>
            <li class="nav-item">
                <a href="<?= go_to('../ftp'); ?>" class="nav-link no-wrap --xhr">
                    <i class="mdi mdi-console-network"></i>
                    <?= phrase('FTP Configuration'); ?>
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
                        <?= phrase('Make sure the package you would to import is downloaded from the official market'); ?> (<a href="//www.aksaracms.com/market" class="text-primary fw-bold" target="_blank">Aksara Market</a>);
                    </li>
                    <li>
                        <?= phrase('The package you download from outside the official market may contains exploit tool that risk your server'); ?>;
                    </li>
                    <li>
                        <?= phrase('Make sure the package was pass the test from your development server'); ?>;
                    </li>
                    <li>
                        <?= phrase('Do import with your own risk'); ?>.
                    </li>
                </ul>
                <div class="form-group mb-3">
                    <div class="input-group">
                        <input type="file" name="file" class="form-control" />
                        <button type="submit" class="btn btn-primary">
                            <i class="mdi mdi-import"></i>
                            <?= phrase('Import'); ?>
                        </button>
                    </div>
                </div>
                <div class="form-group mb-3">
                    <label>
                        <input type="checkbox" name="upgrade" class="form-check-input" value="1" />
                        <?= phrase('Upgrade Existing Theme'); ?>
                    </label>
                </div>
            </form>
        </div>
    </div>
</div>
