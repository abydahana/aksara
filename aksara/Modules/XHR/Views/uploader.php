<?php

/**
 * Aksara Centralized Media & File Uploader View
 *
 * Rendered inside dynamic modal wrapper (--modal).
 *
 * @author     Aby Dahana <abydahana@gmail.com>
 * @copyright  (c) Aksara Laboratory <https://aksaracms.com>
 * @license    MIT License
 */
?>

<div>
    <!-- Dropzone Upload Area -->
    <div class="uploader-upload-zone p-4 mb-3 border border-2 border-dashed rounded-3 text-center">
        <i class="mdi mdi-cloud-upload text-primary mdi-3x mb-2"></i>
        <h6 class="fw-bold mb-1"><?= phrase('Drag & Drop files here or click to upload') ?></h6>
        <p class="text-muted small mb-3"><?= phrase('Supports images, documents, and media files') ?></p>
        <input type="file" id="uploader-file-input" class="d-none" multiple />
        <button type="button" class="btn btn-primary btn-sm rounded-pill px-4" onclick="document.getElementById('uploader-file-input').click()">
            <i class="mdi mdi-plus me-1"></i><?= phrase('Choose File') ?>
        </button>
    </div>

    <!-- Filters & Controls Bar -->
    <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
        <div class="position-relative flex-grow-1">
            <i class="mdi mdi-magnify position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
            <input type="text" id="uploader-search-input" class="form-control form-control-sm rounded-pill ps-5" placeholder="<?= phrase('Search media...') ?>" />
        </div>
        <select id="uploader-sort-select" class="form-select form-select-sm rounded-pill w-auto">
            <option value="newest"><?= phrase('Newest First') ?></option>
            <option value="oldest"><?= phrase('Oldest First') ?></option>
            <option value="name_asc"><?= phrase('Name (A-Z)') ?></option>
            <option value="name_desc"><?= phrase('Name (Z-A)') ?></option>
        </select>
        <div class="btn-group btn-group-sm">
            <button type="button" class="btn btn-light rounded-pill rounded-end-0 uploader-view-toggle active" data-view="grid" data-bs-toggle="tooltip" title="<?= phrase('Grid View') ?>">
                <i class="mdi mdi-view-grid"></i>
            </button>
            <button type="button" class="btn btn-light rounded-pill rounded-start-0 uploader-view-toggle" data-view="list" data-bs-toggle="tooltip" title="<?= phrase('List View') ?>">
                <i class="mdi mdi-view-list"></i>
            </button>
        </div>
    </div>

    <!-- Media Container (Grid/List) -->
    <div id="uploader-media-container" class="uploader-grid-view" data-path="<?= htmlspecialchars($uploadPath ?? 'media') ?>">
        <div class="text-center py-5 text-muted">
            <div class="spinner-border spinner-border-sm text-primary mb-2" role="status"></div>
            <p class="mb-0 small"><?= phrase('Loading media...') ?></p>
        </div>
    </div>

    <!-- Footer Controls -->
    <hr class="border-secondary" style="margin-inline:-1rem" />
    <div class="d-flex justify-content-between align-items-center">
        <div id="uploader-pagination-info" class="small text-muted"></div>
        <ul id="uploader-pagination" class="pagination pagination-sm mb-0"></ul>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        require.js('<?= base_url('modules/XHR/assets/js/uploader.min.js') ?>', function() {
            new AksaraUploader({
                endpoint: '<?= base_url('xhr/uploader') ?>',
                path: '<?= htmlspecialchars($uploadPath ?? 'media') ?>',
                token: '<?= generate_csrf_token() ?>'
            });
        });
    })
</script>

