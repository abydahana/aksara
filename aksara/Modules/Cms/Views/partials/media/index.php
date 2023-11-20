<div class="container-fluid">
    <div class="row">
        <div class="col-lg-8 pt-3 pb-3 bg-white border-end" style="margin-right:-1px">
            <div class="row align-items-end">
                <?php if ($results->directory && ! isset($key)): ?>
                    <div class="col-4 col-sm-3 col-xl-2 text-center">
                        <a href="<?= current_page(null, ['directory' => $results->parent_directory, 'file' => null]); ?>" class="--xhr">
                            <div class="p-3">
                                <i class="mdi mdi-arrow-left mdi-4x"></i>
                            </div>
                            <label class="d-block text-truncate">
                                <?= phrase('Back'); ?>
                            </label>
                        </a>
                    </div>
                <?php endif; ?>
                <?php if ($results->data): ?>
                    <?php foreach ($results->data as $key => $val): ?>
                        <?php if ($val->type == 'directory'): ?>
                            <div class="col-4 col-sm-3 col-xl-2 text-center">
                                <a href="<?= current_page(null, ['directory' => ($results->directory ? $results->directory . '/' : null) . $val->source, 'file' => null]); ?>" class="--xhr">
                                    <div class="p-3">
                                        <i class="mdi mdi-folder-image mdi-4x text-info"></i>
                                    </div>
                                    <label class="d-block text-truncate">
                                        <?= $val->label; ?>
                                    </label>
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="col-4 col-sm-3 col-xl-2 text-center">
                                <a href="<?= current_page(null, ['file' => ($results->directory ? $results->directory . '/' : null) . $val->source]); ?>" class="--xhr">
                                    <div class="p-3">
                                        <img src="<?= $val->icon; ?>" class="img-fluid rounded bg-light w-50" alt="..." />
                                    </div>
                                    <label class="d-block text-truncate">
                                        <?= $val->label; ?>
                                    </label>
                                </a>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
        <div class="col-lg-4 pt-3 pb-3 full-height bg-white border-start" style="margin-left:-1px">
            <?php if ($results->description): ?>
                <?php if (in_array($results->description->mime_type, ['image/jpg', 'image/jpeg', 'image/png', 'image/gif'])): ?>
                    <div class="text-center mb-3">
                        <a href="<?= base_url($results->description->server_path); ?>" target="_blank">
                            <img src="<?= base_url($results->description->server_path); ?>" class="img-fluid rounded-4" alt="" />
                        </a>
                    </div>
                <?php endif; ?>
                <div class="mb-3">
                    <label class="d-block text-muted mb-0">
                        <?= phrase('Filename'); ?>
                    </label>
                    <label class="d-block text-break-word">
                        <a href="<?= base_url($results->description->server_path); ?>" download="<?= $results->description->name; ?>">
                            <?= $results->description->name; ?>
                        </a>
                    </label>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="mb-3">
                            <label class="d-block text-muted mb-0">
                                <?= phrase('Mime Type'); ?>
                            </label>
                            <label class="d-block text-break-word">
                                <?= $results->description->mime_type; ?>
                            </label>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="mb-3">
                            <label class="d-block text-muted mb-0">
                                <?= phrase('Size'); ?>
                            </label>
                            <label class="d-block text-break-word">
                                <?= get_filesize(service('request')->getGet('directory'), $results->description->name); ?>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="d-block text-muted mb-0">
                        <?= phrase('Date Modified'); ?>
                    </label>
                    <label class="d-block text-break-word">
                        <?= date('Y-m-d H:i:s', $results->description->date); ?>
                    </label>
                </div>
                <div class="row">
                    <div class="col-6">
                        <a href="<?= base_url($results->description->server_path); ?>" class="btn btn-primary btn-sm d-block rounded-pill"  download="<?= $results->description->name; ?>">
                            <i class="mdi mdi-download"></i>
                            <?= phrase('Download'); ?>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="<?= current_page(null, ['action' => 'delete']); ?>" class="btn btn-danger btn-sm d-block rounded-pill --xhr">
                            <i class="mdi mdi-window-close"></i>
                            <?= phrase('Remove'); ?>
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
