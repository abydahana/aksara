<div class="container-fluid">
    <div class="sticky-top bg-white overflow-x-auto py-1 px-3 mx--3 mb-3 border-bottom">
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
            <li class="nav-item">
                <a href="<?= current_page(); ?>" class="nav-link rounded-pill active no-wrap --xhr">
                    <i class="mdi mdi-console-network"></i>
                    <?= phrase('FTP Configuration'); ?>
                </a>
            </li>
        </ul>
    </div>
    <form action="<?= current_page(); ?>" method="POST" class="--validate-form" enctype="multipart/form-data">
        <div class="row">
            <div class="col-md-6">
                <div class="row">
                    <div class="col-md-9">
                        <?= form_input($results->field_data->hostname); ?>
                    </div>
                    <div class="col-md-3">
                        <?= form_input($results->field_data->port); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <?= form_input($results->field_data->username); ?>
                    </div>
                    <div class="col-md-6">
                        <?= form_input($results->field_data->password); ?>
                    </div>
                </div>
                <div class="form-group mb-3">
                    <div class="form-check form-switch">
                        <input type="checkbox" name="checking" role="boolean" value="1" class="form-check-input" id="checking_input" checked="" autocomplete="off">
                        <label class="form-check-label" for="checking_input"> <?= phrase('Check connection while submitting'); ?> </label>
                    </div>
                </div>
            </div>
        </div>
        <div class="opt-btn-overlap-fix"></div>
        <div class="row opt-btn">
            <div class="col-md-6">
                <a href="<?= go_to('../'); ?>" class="btn btn-link --xhr">
                    <i class="mdi mdi-arrow-left"></i>
                    <?= phrase('Back'); ?>
                </a>
                <button type="submit" class="btn btn-primary float-end">
                    <i class="mdi mdi-check"></i>
                    <?= phrase('Update'); ?>
                    <em class="text-sm">(ctrl+s)</em>
                </button>
            </div>
        </div>
    </form>
</div>
