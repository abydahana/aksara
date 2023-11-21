<?php
    $field_data = $results[0];
?>
<div class="container-fluid py-3">
    <div class="row">
        <div class="col-lg-6">
            <div class="row">
                <div class="col-sm-4">
                    <label class="text-muted d-block">
                        <?= phrase('Language'); ?>
                    </label>
                </div>
                <div class="col">
                    <label>
                        <?= (isset($field_data->language) ? $field_data->language : null); ?>
                    </label>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-4">
                    <label class="text-muted d-block">
                        <?= phrase('Alias'); ?>
                    </label>
                </div>
                <div class="col">
                    <label>
                        <?= (isset($field_data->description) ? $field_data->description : null); ?>
                    </label>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-4">
                    <label class="text-muted d-block">
                        <?= phrase('Code'); ?>
                    </label>
                </div>
                <div class="col">
                    <label>
                        <?= (isset($field_data->code) ? $field_data->code : null); ?>
                    </label>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-4">
                    <label class="text-muted d-block">
                        <?= phrase('Locale'); ?>
                    </label>
                </div>
                <div class="col">
                    <label>
                        <?= (isset($field_data->locale) ? $field_data->locale : null); ?>
                    </label>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-4">
                    <label class="text-muted d-block">
                        <?= phrase('Total phrases'); ?>
                    </label>
                </div>
                <div class="col">
                    <label>
                        <?= number_format($total_phrases); ?>
                    </label>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <form action="<?= current_page(null, ['per_page' => null]); ?>" method="GET">
                <div class="form-group mb-3">
                    <div class="input-group">
                        <input type="text" name="q" class="form-control" placeholder="<?= phrase('Search phrase'); ?>" value="<?= (service('request')->getGet('q') ? htmlspecialchars(service('request')->getGet('q')) : null); ?>" />
                        <button type="submit" class="btn btn-primary">
                            <i class="mdi mdi-magnify"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <hr class="row" />
    
    <?= pagination($pagination); ?>
    
    <hr class="row mt-3" />
    
    <form action="<?= current_page(); ?>" method="POST" class="--validate-form" enctype="multipart/form-data">
        <div class="row">
            <?php foreach ($phrases as $key => $val): ?>
                <div class="col-md-4">
                    <div class="form-group mb-3">
                        <div class="input-group">
                            <input type="text" name="phrases[<?= $key; ?>]" class="form-control" value="<?= $val; ?>" placeholder="<?= $key; ?>" data-bs-toggle="tooltip" title="<?= $key; ?>" autocomplete="off" />
                            <a href="<?= current_page('delete_phrase', ['phrase' => $key]); ?>" class="btn border --open-delete-confirm" data-bs-toggle="tooltip" title="<?= phrase('Delete Phrase'); ?>">
                                <i class="mdi mdi-trash-can-outline"></i>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="opt-btn-overlap-fix"></div>
        <div class="row opt-btn">
            <div class="col-md-12">
                <a href="<?= current_page('../', ['id' => null, 'code' => null, 'per_page' => null, 'q' => null]); ?>" class="btn btn-link --xhr">
                    <i class="mdi mdi-arrow-left"></i>
                    <?= phrase('Back'); ?>
                </a>
                <button type="submit" class="btn btn-primary float-end">
                    <i class="mdi mdi-check"></i>
                    <?= phrase('Translate'); ?>
                </button>
            </div>
        </div>
    </form>
</div>
