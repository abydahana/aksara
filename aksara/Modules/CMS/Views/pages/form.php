<?php

/**
 * @var mixed $results
 * @var mixed $forms
 */
$pageContent = $results->field_data->page_content->value ?? '{"components":[]}';
?>

<style type="text/css">
    #title-wrapper, [data-role="meta"] {
        display: none !important;
    }
</style>

<div class="pb-form-wrapper">
    <form action="<?= current_page() ?>" method="POST" class="--validate-form">
        <!-- Manually Placed Page Builder Toolbar -->
        <div class="pb-toolbar container-fluid py-1 sticky-lg-top bg-body border-bottom">
            <div class="row">
                <div class="col-4">
                    <div class="d-flex gap-2 justify-content-start">
                        <div class="pb-toolbar-group btn-group btn-group-sm bg-body-tertiary rounded-pill px-1 border">
                            <a href="<?= go_to() ?>" class="btn btn-link rounded-pill rounded-end-0 --xhr" data-bs-toggle="tooltip" title="<?= phrase('Back') ?>">
                                <i class="mdi mdi-arrow-left"></i> <?= phrase('Back') ?>
                            </a>
                            <button type="button" class="btn btn-link border-start pb-undo" data-bs-toggle="tooltip" title="<?= phrase('Undo') ?>">
                                <i class="mdi mdi-undo"></i>
                            </button>
                            <button type="button" class="btn btn-link border-start rounded-pill rounded-start-0 pb-redo" data-bs-toggle="tooltip" title="<?= phrase('Redo') ?>">
                                <i class="mdi mdi-redo"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-2">
                    <div class="d-flex gap-2 justify-content-end">
                        <div class="pb-toolbar-group btn-group btn-group-sm bg-body-tertiary rounded-pill px-1 border">
                            <button type="button" class="btn btn-link rounded-pill rounded-end-0 pb-device-btn active" data-device="desktop" data-bs-toggle="tooltip" title="<?= phrase('Desktop') ?>">
                                <i class="mdi mdi-monitor"></i>
                            </button>
                            <button type="button" class="btn btn-link border-start pb-device-btn" data-device="tablet" data-bs-toggle="tooltip" title="<?= phrase('Tablet') ?>">
                                <i class="mdi mdi-tablet"></i>
                            </button>
                            <button type="button" class="btn btn-sm pb-device-btn rounded-pill rounded-start-0" data-device="mobile" data-bs-toggle="tooltip" title="<?= phrase('Mobile') ?>">
                                <i class="mdi mdi-cellphone"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-6 text-end">
                    <div class="d-flex gap-2 justify-content-end">
                        <?php if (get_setting('ai_enabled')): ?>
                            <button type="button" class="btn btn-sm btn-info text-nowrap rounded-pill px-3 --ai-assistant">
                                <i class="mdi mdi-creation me-1"></i> AI
                            </button>
                        <?php endif; ?>

                        <button type="button" class="btn btn-sm btn-outline-secondary text-nowrap rounded-pill px-3 click-on-invalid" onclick="window._pageBuilder.openSettings('#pb-settings-container')">
                            <i class="mdi mdi-cogs me-1"></i> <?= phrase('Settings') ?>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-primary text-nowrap rounded-pill pb-preview-btn px-3">
                            <i class="mdi mdi-eye me-1"></i> <?= phrase('Preview') ?>
                        </button>
                        <button type="button" class="btn btn-sm btn-success text-nowrap rounded-pill pb-save-btn px-4">
                            <i class="mdi mdi-content-save me-1"></i> <?= phrase('Save') ?>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-0">
            <!-- Main: Page Builder Canvas -->
            <div class="col-12 bg-body-tertiary">
                <div id="page-builder"></div>
                <input type="hidden" name="page_content" id="page_content" value="<?= htmlspecialchars($pageContent, ENT_QUOTES) ?>" />
            </div>
        </div>

        <!-- Container: Page Metadata Settings (Hidden, will be moved to modal) -->
        <div id="pb-settings-container" style="display:none">
            <?= isset($results->field_data->page_title) ? form_input($results->field_data->page_title) : null ?>
            <?= isset($results->field_data->page_slug) ? form_input($results->field_data->page_slug) : null ?>
            <?= isset($results->field_data->page_description) ? form_input($results->field_data->page_description) : null ?>
            <?= isset($results->field_data->language_id) ? form_input($results->field_data->language_id) : null ?>
            <?= isset($results->field_data->status) ? form_input($results->field_data->status) : null ?>
            <div data-role="validation-callback"></div>
        </div>
    </form>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        require.css('<?= get_module_asset('css/pagebuilder.min.css') ?>');
        require.js([
            '<?= get_module_asset('js/sortable.min.js') ?>',
            '<?= get_module_asset('js/pagebuilder.min.js') ?>'
        ], function() {
            var builder = new AksaraPageBuilder({
                el: '#page-builder',
                input: '#page_content',
                preview_url: '<?= go_to('builder-preview') ?>',
                components: <?= json_encode($builderComponents ?? []) ?>,
                categories: <?= json_encode($builderCategories ?? []) ?>
            });

            // Collapse main sidebar for more space
            setTimeout(function() {
                if(!document.body.classList.contains('sidebar-collapsed')){
                    var sidebarToggle = document.querySelector('[data-toggle="sidebar"]');
                    if(sidebarToggle) sidebarToggle.click();
                }
            }, 100);

            window._pageBuilder = builder;
        });
    });
</script>
