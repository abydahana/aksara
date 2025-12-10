<?php $view_mode = (service('request')->getGet('mode') === 'list') ? 'list' : 'grid'; ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-lg-8 pt-3 pb-3 bg-white border-end">
            <!-- View Toggle Buttons -->
            <div class="d-flex justify-content-between mb-3">
                <div>
                    <?php if ($view_mode == 'list' && $results->directory && ! isset($key)): ?>
                        <a href="<?= current_page(null, ['directory' => $results->parent_directory, 'file' => null, 'mode' => $view_mode]); ?>" class="btn btn-outline-secondary btn-sm --xhr">
                            <i class="mdi mdi-arrow-left"></i> <?= phrase('Back'); ?>
                        </a>
                    <?php endif; ?>
                </div>
                <div class="btn-group btn-group-sm" role="group" aria-label="View Toggle">
                    <a href="<?= current_page(null, ['mode' => 'grid']); ?>" class="btn btn-outline-secondary <?= (service('request')->getGet('mode') !== 'list') ? 'active' : '' ?> --xhr">
                        <i class="mdi mdi-view-grid"></i> <?= phrase('Grid'); ?>
                    </a>
                    <a href="<?= current_page(null, ['mode' => 'list']); ?>" class="btn btn-outline-secondary <?= (service('request')->getGet('mode') === 'list') ? 'active' : '' ?> --xhr">
                        <i class="mdi mdi-view-list"></i> <?= phrase('List'); ?>
                    </a>
                </div>
            </div>

            <?php if ($view_mode === 'grid'): ?>
                <!-- Grid View -->
                <div class="row align-items-end">
                    <?php if ($results->directory && ! isset($key)): ?>
                        <div class="col-4 col-sm-3 col-xl-2 text-center">
                            <a href="<?= current_page(null, ['directory' => $results->parent_directory, 'file' => null, 'mode' => $view_mode]); ?>" class="--xhr">
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
                                    <a href="<?= current_page(null, ['directory' => ($results->directory ? $results->directory . '/' : null) . $val->source, 'file' => null, 'mode' => $view_mode]); ?>" class="--xhr">
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
                                    <a href="<?= current_page(null, ['file' => ($results->directory ? $results->directory . '/' : null) . $val->source, 'mode' => $view_mode]); ?>" class="--xhr">
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

            <?php else: ?>
                <!-- List View -->
                <div class="list-view">
                    <?php if ($results->data): ?>
                        <div class="list-group">
                            <?php foreach ($results->data as $key => $val): ?>
                                <?php if ($val->type == 'directory'): ?>
                                    <a href="<?= current_page(null, ['directory' => ($results->directory ? $results->directory . '/' : null) . $val->source, 'file' => null, 'mode' => $view_mode]); ?>" class="list-group-item list-group-item-action py-0 --xhr">
                                        <div class="d-flex align-items-center">
                                            <div class="me-3">
                                                <i class="mdi mdi-folder-image mdi-3x text-info" style="width: 48px; height: 48px"></i>
                                            </div>
                                            <div class="flex-fill">
                                                <div class="fw-bold"><?= $val->label; ?></div>
                                                <small class="text-muted"><?= phrase('Folder'); ?></small>
                                            </div>
                                            <div>
                                                <i class="mdi mdi-chevron-right"></i>
                                            </div>
                                        </div>
                                    </a>
                                <?php else: ?>
                                    <a href="<?= current_page(null, ['file' => ($results->directory ? $results->directory . '/' : null) . $val->source, 'mode' => $view_mode]); ?>" class="list-group-item list-group-item-action --xhr">
                                        <div class="d-flex align-items-center">
                                            <div class="me-3">
                                                <img src="<?= $val->icon; ?>" class="img-fluid rounded bg-light" style="width: 48px; height: 48px; object-fit: contain;" alt="<?= $val->label; ?>" />
                                            </div>
                                            <div class="flex-fill">
                                                <div class="fw-bold"><?= $val->label; ?></div>
                                                <small class="text-muted"><?= $val->type; ?></small>
                                            </div>
                                            <div>
                                                <i class="mdi mdi-chevron-right"></i>
                                            </div>
                                        </div>
                                    </a>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
        <div class="col-lg-4 pt-3 pb-3 full-height bg-white border-start" style="margin-left:-1px">
            <div class="sticky-top">
                <?php if ($results->description): ?>
                    <?php if (in_array($results->description->mime_type, ['image/jpg', 'image/jpeg', 'image/png', 'image/gif'])): ?>
                        <div class="text-center mb-3">
                            <a href="<?= base_url($results->description->server_path); ?>" target="_blank">
                                <img src="<?= base_url($results->description->server_path); ?>" class="img-fluid rounded-4" alt="" style="max-width: 256px; max-height: 256px" />
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
                            <a href="<?= current_page(null, ['action' => 'delete', 'mode' => $view_mode]); ?>" class="btn btn-danger btn-sm d-block rounded-pill --xhr">
                                <i class="mdi mdi-window-close"></i>
                                <?= phrase('Remove'); ?>
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
