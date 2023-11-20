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
                <a href="<?= go_to('../themes'); ?>" class="nav-link no-wrap --xhr">
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
                <a href="<?= current_page(); ?>" class="nav-link no-wrap --xhr text-bg-primary">
                    <i class="mdi mdi-console-network"></i>
                    <?= phrase('FTP Configuration'); ?>
                </a>
            </li>
        </ul>
    </div>
    <form action="<?= current_page(); ?>" method="POST" enctype="multipart/form-data">
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
                    <label class="text-muted d-block">
                        <input type="checkbox" name="checking" class="form-check-input" value="1" />
                        <?= phrase('Check connection while submitting'); ?>
                    </label>
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
