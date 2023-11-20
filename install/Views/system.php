<?php
    // Get installation mode options
    $options = null;

    foreach($installation_mode as $key => $val)
    {
        $options .= '<option value="' . $val['id'] . '"' . (session()->get('installation_mode') == $val['id'] ? ' selected' : null) . '>' . $val['label'] . '</option>';
    }

    // Get timezone options
    $timezone_list = null;

    foreach ($timezone as $key => $val)
    {
        $timezone_list .= '<option value="' . $val . '"' . (session()->get('timezone') == $val ? ' selected' : null) . '>' . $val . '</option>';
    }
?>

<form action="<?= site_url('finalizing'); ?>" method="POST" class="--validate-form">
    <h4>
        <?= phrase('System Configuration'); ?>
    </h4>
    <p>
        <?= phrase('Enter the basic system configuration.'); ?>
        <?= phrase('You will able to change it after the installation.'); ?>
    </p>
    <div class="text-muted opacity-50">
        <hr class="row" />
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group mb-3">
                <label class="d-block mb-0">
                    <?= phrase('Installation Mode'); ?>
                    <b class="text-danger">*</b>
                </label>
                <select name="installation_mode" class="form-select form-select-sm rounded-pill">
                    <?= $options; ?>
                </select>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group mb-3">
                <label class="d-block mb-0">
                    <?= phrase('Timezone'); ?>
                    <b class="text-danger">*</b>
                </label>
                <select name="timezone" class="form-select form-select-sm rounded-pill">
                    <?= $timezone_list; ?>
                </select>
            </div>
        </div>
    </div>
    <div class="text-muted opacity-50">
        <hr />
    </div>
    <h5>
        <?= phrase('Site Settings'); ?>
    </h5>
    <div class="row">
        <div class="col-md-12">
            <div class="form-group mb-3">
                <label class="d-block mb-0">
                    <?= phrase('Site Title'); ?>
                    <b class="text-danger">*</b>
                </label>
                <input type="text" name="site_title" class="form-control form-control-sm rounded-pill" placeholder="<?= phrase('Enter the site title'); ?>" value="<?= session()->get('site_title'); ?>" />
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="form-group mb-3">
                <label class="d-block mb-0">
                    <?= phrase('Site Description'); ?>
                    <b class="text-danger">*</b>
                </label>
                <textarea name="site_description" class="form-control form-control-sm rounded-pill" placeholder="<?= phrase('Enter the site description'); ?>" rows="1"><?= session()->get('site_description'); ?></textarea>
            </div>
        </div>
    </div>
    <div class="text-muted opacity-50">
        <hr />
    </div>
    <h5>
        <?= phrase('Upload Settings'); ?>
    </h5>
    <div class="form-group mb-3">
        <label class="d-block mb-0">
            <?= phrase('Allowed File Extension'); ?>
            <b class="text-danger">*</b>
        </label>
        <input type="text" name="file_extension" class="form-control form-control-sm rounded-pill" placeholder="<?= phrase('Separate with comma'); ?>" value="<?= (session()->get('file_extension') ? session()->get('file_extension') : 'jpg,jpeg,gif,png,pdf,xls,xlsx,doc,docx,csv'); ?>" />
    </div>
    <div class="row">
        <div class="col-md-8">
            <div class="form-group mb-3">
                <label class="d-block mb-0">
                    <?= phrase('Allowed Image Extension'); ?>
                    <b class="text-danger">*</b>
                </label>
                <input type="text" name="image_extension" class="form-control form-control-sm rounded-pill" placeholder="<?= phrase('Separate with comma'); ?>" value="<?= (session()->get('image_extension') ? session()->get('image_extension') : 'jpg,png,gif'); ?>" />
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group mb-3">
                <label class="d-block mb-0">
                    <?= phrase('Upload Limit'); ?> (KB)
                    <b class="text-danger">*</b>
                </label>
                <div class="input-group input-group-sm">
                    <input type="number" name="max_upload_size" class="form-control form-control-sm rounded-pill rounded-end" placeholder="e.g: 2048" value="<?= (session()->get('max_upload_size') ? session()->get('max_upload_size') : 10); ?>" />
                    <span class="input-group-text rounded-pill rounded-start">
                        MB
                    </span>
                </div>
            </div>
        </div>
    </div>
    <div class="text-muted opacity-50">
        <hr />
    </div>
    <h5>
        <?= phrase('Image Width Dimension'); ?> (px)
    </h5>
    <div class="row">
        <div class="col-md-4">
            <div class="form-group mb-3">
                <label class="d-block mb-0">
                    <?= phrase('Original'); ?>
                    <b class="text-danger">*</b>
                </label>
                <div class="input-group input-group-sm">
                    <input type="number" name="image_dimension" class="form-control form-control-sm rounded-pill rounded-end" placeholder="in pixel" value="<?= (session()->get('image_dimension') ? session()->get('image_dimension') : 1024); ?>" />
                    <span class="input-group-text rounded-pill rounded-start">
                        px
                    </span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group mb-3">
                <label class="d-block mb-0">
                    <?= phrase('Thumbnail'); ?>
                    <b class="text-danger">*</b>
                </label>
                <div class="input-group input-group-sm">
                    <input type="number" name="thumbnail_dimension" class="form-control form-control-sm rounded-pill rounded-end" placeholder="in pixel" value="<?= (session()->get('thumbnail_dimension') ? session()->get('thumbnail_dimension') : 256); ?>" />
                    <span class="input-group-text rounded-pill rounded-start">
                        px
                    </span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group mb-3">
                <label class="d-block mb-0">
                    <?= phrase('Icon'); ?>
                    <b class="text-danger">*</b>
                </label>
                <div class="input-group input-group-sm">
                    <input type="number" name="icon_dimension" class="form-control form-control-sm rounded-pill rounded-end" placeholder="in pixel" value="<?= (session()->get('icon_dimension') ? session()->get('icon_dimension') : 80); ?>" />
                    <span class="input-group-text rounded-pill rounded-start">
                        px
                    </span>
                </div>
            </div>
        </div>
    </div>
    <div class="text-muted opacity-50">
        <hr class="row" />
    </div>
    <div class="--validation-callback"></div>
    <div class="row">
        <div class="col-6">
            <div class="d-grid">
                <a href="<?= site_url('security'); ?>" class="btn btn-light btn-sm rounded-pill --xhr">
                    <i class="mdi mdi-arrow-left"></i>
                    <?= phrase('Back'); ?>
                </a>
            </div>
        </div>
        <div class="col-6">
            <div class="d-grid">
                <input type="hidden" name="_token" value="<?= sha1(time()); ?>" />
                <button type="submit" class="btn btn-outline-primary btn-sm rounded-pill">
                    <i class="mdi mdi-check"></i>
                    <?= phrase('Continue'); ?>
                </button>
            </div>
        </div>
    </div>
</form>
