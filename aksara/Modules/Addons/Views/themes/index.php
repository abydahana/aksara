<div class="container-fluid">
    <div class="row border-bottom bg-white mb-1 sticky-top" style="overflow-x:auto">
        <ul class="nav" style="flex-wrap: nowrap">
            <li class="nav-item">
                <a href="<?= go_to('../'); ?>" class="nav-link no-wrap --xhr">
                    <i class="mdi mdi-cart"></i>
                    <?= phrase('Market'); ?>
                </a>
            </li>
            <li class="nav-item">
                <a href="<?= current_page(); ?>" class="nav-link no-wrap --xhr text-bg-primary">
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
                <a href="<?= go_to('../ftp'); ?>" class="nav-link no-wrap --xhr">
                    <i class="mdi mdi-console-network"></i>
                    <?= phrase('FTP Configuration'); ?>
                </a>
            </li>
        </ul>
    </div>
    <div class="row border-bottom pb-1 mb-3">
        <div class="col-md-6 offset-md-6 text-end">
            <a href="<?= current_page('import'); ?>" class="btn btn-primary btn-sm --xhr">
                <i class="mdi mdi-import"></i>
                <?= phrase('Import Theme'); ?>
            </a>
        </div>
    </div>
    <div class="row">
        <?php if ($installed): ?>
            <?php foreach ($installed as $key => $val): ?>
                <?php
                    if (file_exists(ROOTPATH . 'themes' . DIRECTORY_SEPARATOR . $val->folder . DIRECTORY_SEPARATOR . str_replace(['../', '..\\', './', '.\\'], '', $val->screenshot[0]->src))) {
                        $screenshot = base_url('themes/' . $val->folder . '/' . str_replace(['../', '..\\', './', '.\\'], '', $val->screenshot[0]->src));
                    } else {
                        $screenshot = get_image(null, 'placeholder_thumb.png');
                    }
                ?>

                <div class="col-sm-6 col-md-4 col-lg-3">
                    <div class="card rounded-4 mb-3">
                        <div class="card-body p-3">
                            <div class="position-relative mb-3">
                                <?= ($val->type == 'backend' ? '<span class="badge bg-dark position-absolute end-0">' . phrase('Back End') . '</span>' : '<span class="badge bg-success position-absolute end-0">' . phrase('Front End') . '</span>'); ?>
                                <img src="<?= $screenshot; ?>" class="img-fluid rounded-4 border" alt="..." />
                            </div>
                            <div class="mb-3">
                                <b data-bs-toggle="tooltip" title="<?= $val->name; ?>">
                                <?= truncate($val->name, 80); ?>
                                </b>
                            </div>
                            <div class="row">
                                <div class="col-6">
                                    <?php if (($val->type == 'backend' && $val->folder == get_setting('backend_theme')) || ($val->type == 'frontend' && $val->folder == get_setting('frontend_theme'))): ?>
                                        <a href="<?= current_page('customize', ['item' => $val->folder]); ?>" class="btn btn-dark btn-xs d-block --modal">
                                            <i class="mdi mdi-cogs"></i>
                                            <?= phrase('Customize'); ?>
                                        </a>
                                    <?php else: ?>
                                        <a href="<?= current_page('activate', ['item' => $val->folder]); ?>" class="btn btn-success btn-xs d-block --modal">
                                            <i class="mdi mdi-check"></i>
                                            <?= phrase('Activate'); ?>
                                        </a>
                                    <?php endif; ?>
                                </div>
                                <div class="col-6">
                                    <a href="<?= base_url(('backend' == $val->type ? 'dashboard' : null), ['aksara_mode' => 'preview-theme', 'aksara_theme' => $val->folder, 'integrity_check' => $val->integrity]); ?>" class="btn btn-outline-primary d-block btn-xs" target="_blank">
                                        <i class="mdi mdi-magnify"></i>
                                        <?= phrase('Preview'); ?>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-sm-12">
                <div class="alert alert-warning">
                    <i class="mdi mdi-information-outline"></i>
                    <?= phrase('No installed theme'); ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
