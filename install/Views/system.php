<?php

/**
 * @var mixed $installationMode
 * @var mixed $timeZone
 */
$options = null;

foreach ($installationMode as $key => $val) {
    $options .= '<option value="' . $val['id'] . '"' . (session()->get('installation_mode') == $val['id'] || (! session()->get('installation_mode') && $val['selected']) ? ' selected' : null) . '>' . $val['label'] . '</option>';
}

// Get timezone options
$timeZoneList = null;

foreach ($timeZone as $key => $val) {
    $timeZoneList .= '<option value="' . $val . '"' . (session()->get('timezone') == $val ? ' selected' : null) . '>' . $val . '</option>';
} ?>

<form action="<?= site_url('finalizing') ?>" method="POST" class="--validate-form">
    <h4>
        <?= phrase('System Configuration') ?>
    </h4>
    <p>
        <?= phrase('Enter the basic system configuration.') ?>
        <?= phrase('You will able to change it after the installation.') ?>
    </p>
    <div class="text-muted opacity-50">
        <hr class="row" />
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group mb-3">
                <label class="d-block mb-0" for="installation_mode_input">
                    <?= phrase('Installation Mode') ?>
                    <b class="text-danger">*</b>
                </label>
                <select name="installation_mode" class="form-select form-select-sm rounded-pill" id="installation_mode_input" aria-label="<?= phrase('Installation Mode') ?>">
                    <?= $options ?>
                </select>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group mb-3">
                <label class="d-block mb-0" for="timezone_input">
                    <?= phrase('Timezone') ?>
                    <b class="text-danger">*</b>
                </label>
                <select name="timezone" class="form-select form-select-sm rounded-pill" id="timezone_input" aria-label="<?= phrase('Timezone') ?>">
                    <?= $timeZoneList ?>
                </select>
            </div>
        </div>
    </div>
    <div class="text-muted opacity-50">
        <hr />
    </div>
    <h5>
        <?= phrase('Site Settings') ?>
    </h5>
    <div class="row">
        <div class="col-md-12">
            <div class="form-group mb-3">
                <label class="d-block mb-0" for="site_title_input">
                    <?= phrase('Site Title') ?>
                    <b class="text-danger">*</b>
                </label>
                <input type="text" name="site_title" class="form-control form-control-sm rounded-pill" id="site_title_input" placeholder="<?= phrase('Enter the site title') ?>" value="<?= session()->get('site_title') ?>" />
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="form-group mb-3">
                <label class="d-block mb-0" for="site_description_input">
                    <?= phrase('Site Description') ?>
                    <b class="text-danger">*</b>
                </label>
                <textarea name="site_description" class="form-control form-control-sm rounded-pill" id="site_description_input" placeholder="<?= phrase('Enter the site description') ?>" rows="1"><?= session()->get('site_description') ?></textarea>
            </div>
        </div>
    </div>
    <div class="text-muted opacity-50">
        <hr />
    </div>
    <h5>
        <?= phrase('Upload Settings') ?>
    </h5>
    <div class="form-group mb-3">
        <label class="d-block mb-0" for="file_extension_input">
            <?= phrase('Allowed File Extension') ?>
            <b class="text-danger">*</b>
        </label>
        <input type="text" name="file_extension" class="form-control form-control-sm rounded-pill" id="file_extension_input" placeholder="<?= phrase('Separate with comma') ?>" value="<?= session()->get('file_extension') ??
  'jpg,jpeg,png,gif,pdf,xls,xlsx,doc,docx,csv' ?>" />
    </div>
    <div class="row">
        <div class="col-md-8">
            <div class="form-group mb-3">
                <label class="d-block mb-0" for="image_extension_input">
                    <?= phrase('Allowed Image Extension') ?>
                    <b class="text-danger">*</b>
                </label>
                <input type="text" name="image_extension" class="form-control form-control-sm rounded-pill" id="image_extension_input" placeholder="<?= phrase('Separate with comma') ?>" value="<?= session()->get('image_extension') ??
  'jpg,jpeg,png,gif' ?>" />
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group mb-3">
                <label class="d-block mb-0" for="max_upload_size_input">
                    <?= phrase('Upload Limit') ?>
                    <b class="text-danger">*</b>
                </label>
                <div class="input-group input-group-sm">
                    <input type="number" name="max_upload_size" class="form-control form-control-sm rounded-pill rounded-end" id="max_upload_size_input" placeholder="e.g: 2048" value="<?= session()->get('max_upload_size') ??
                      ((int) ini_get('upload_max_filesize') ?? 10) ?>" />
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
        <?= phrase('Image Width Dimension') ?> (px)
    </h5>
    <div class="row">
        <div class="col-md-4">
            <div class="form-group mb-3">
                <label class="d-block mb-0" for="image_dimension_input">
                    <?= phrase('Original') ?>
                    <b class="text-danger">*</b>
                </label>
                <div class="input-group input-group-sm">
                    <input type="number" name="image_dimension" class="form-control form-control-sm rounded-pill rounded-end" id="image_dimension_input" placeholder="in pixel" value="<?= session()->get('image_dimension') ?? 1920 ?>" />
                    <span class="input-group-text rounded-pill rounded-start">
                        px
                    </span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group mb-3">
                <label class="d-block mb-0" for="thumbnail_dimension_input">
                    <?= phrase('Thumbnail') ?>
                    <b class="text-danger">*</b>
                </label>
                <div class="input-group input-group-sm">
                    <input type="number" name="thumbnail_dimension" class="form-control form-control-sm rounded-pill rounded-end" id="thumbnail_dimension_input" placeholder="in pixel" value="<?= session()->get('thumbnail_dimension') ?? 480 ?>" />
                    <span class="input-group-text rounded-pill rounded-start">
                        px
                    </span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group mb-3">
                <label class="d-block mb-0" for="icon_dimension_input">
                    <?= phrase('Icon') ?>
                    <b class="text-danger">*</b>
                </label>
                <div class="input-group input-group-sm">
                    <input type="number" name="icon_dimension" class="form-control form-control-sm rounded-pill rounded-end" id="icon_dimension_input" placeholder="in pixel" value="<?= session()->get('icon_dimension') ?? 128 ?>" />
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
                <a href="<?= site_url('security') ?>" class="btn btn-outline-secondary btn-sm rounded-pill --xhr" aria-label="<?= phrase('Back') ?>">
                    <i class="mdi mdi-arrow-left"></i> <?= phrase('Back') ?>
                </a>
            </div>
        </div>
        <div class="col-6">
            <div class="d-grid">
                <input type="hidden" name="_token" value="<?= sha1(time()) ?>" />
                <button type="submit" class="btn btn-outline-primary btn-sm rounded-pill" aria-label="<?= phrase('Continue') ?>">
                    <i class="mdi mdi-check"></i> <?= phrase('Continue') ?>
                </button>
            </div>
        </div>
    </div>
</form>
