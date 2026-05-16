<?php
/**
 * @var mixed $results
 * @var mixed $forms
 */
?>
<style type="text/css">
    #title-wrapper, [role="meta"] { display: none !important; }
</style>
<div class="pb-form-wrapper">
    <form action="<?= current_page(); ?>" method="POST" class="--validate-form">
        <!-- Manually Placed Page Builder Toolbar -->
        <div class="pb-toolbar px-3 py-2 sticky-top bg-white border-bottom">
            <div class="pb-toolbar-group bg-light rounded-pill px-1 border">
                <button type="button" class="btn btn-sm btn-link text-dark pb-undo" title="Undo">
                    <i class="mdi mdi-undo"></i>
                </button>
                <div class="vr my-1"></div>
                <button type="button" class="btn btn-sm btn-link text-dark pb-redo" title="Redo">
                    <i class="mdi mdi-redo"></i>
                </button>
            </div>
            <div class="pb-toolbar-group bg-light rounded-pill px-1 border mx-auto">
                <button type="button" class="btn btn-sm pb-device-btn active rounded-pill" data-device="desktop" title="Desktop">
                    <i class="mdi mdi-monitor"></i>
                </button>
                <button type="button" class="btn btn-sm pb-device-btn rounded-pill" data-device="tablet" title="Tablet">
                    <i class="mdi mdi-tablet"></i>
                </button>
                <button type="button" class="btn btn-sm pb-device-btn rounded-pill" data-device="mobile" title="Mobile">
                    <i class="mdi mdi-cellphone"></i>
                </button>
            </div>
            <div class="pb-toolbar-group gap-2">
                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill pb-preview-btn px-4">
                    <i class="mdi mdi-eye me-1"></i><?= phrase('Preview'); ?>
                </button>
                <button type="button" class="btn btn-sm btn-success rounded-pill pb-save-btn px-4">
                    <i class="mdi mdi-content-save me-1"></i><?= phrase('Save Page'); ?>
                </button>
            </div>
        </div>

        <div class="row g-0">
            <!-- Sidebar: Page Metadata -->
            <div class="col-md-3 col-xxl-2 bg-white border-end pb-metadata-sidebar">
                <div class="p-3 sticky-top" style="top: 60px;">
                    <h6 class="fw-bold mb-3 text-uppercase small text-muted letter-spacing-1"><?= phrase('Page Metadata'); ?></h6>
                    <div class="form-group mb-3">
                        <?= (isset($results->field_data->page_title) ? form_input($results->field_data->page_title) : null); ?>
                    </div>
                    <div class="form-group mb-3">
                        <?= (isset($results->field_data->page_slug) ? form_input($results->field_data->page_slug) : null); ?>
                    </div>
                    <div class="form-group mb-3">
                        <?= (isset($results->field_data->page_description) ? form_input($results->field_data->page_description) : null); ?>
                    </div>
                    <div class="form-group mb-3">
                        <?= (isset($results->field_data->language_id) ? form_input($results->field_data->language_id) : null); ?>
                    </div>
                    <div class="form-group mb-3">
                        <?= (isset($results->field_data->status) ? form_input($results->field_data->status) : null); ?>
                    </div>
                    <div role="validation-callback"></div>
                </div>
            </div>

            <!-- Main: Page Builder Canvas -->
            <div class="col-md-9 col-xxl-10 bg-light">
                <div id="page-builder"></div>
                <?php
                    $page_content = (isset($results->field_data->page_content->value) ? $results->field_data->page_content->value : '{"components":[]}');
                ?>
                <input type="hidden" name="page_content" id="page_content" value="<?= htmlspecialchars($page_content, ENT_QUOTES); ?>" />
            </div>
        </div>
    </form>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        require.css('<?= get_module_asset('css/pagebuilder.css'); ?>');
        require.js([
            '<?= get_module_asset('js/sortable.min.js'); ?>',
            '<?= get_module_asset('js/pagebuilder.min.js'); ?>'
        ], function() {
            var builder = new AksaraPageBuilder({
                el: '#page-builder',
                input: '#page_content',
                preview_url: '<?= go_to('builder-preview'); ?>',
                components: <?= json_encode($builder_components ?? []); ?>,
                categories: <?= json_encode($builder_categories ?? []); ?>
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
