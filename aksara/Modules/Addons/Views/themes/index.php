<?php

/**
 * @var mixed $installed
 */
?>

<div class="container-fluid">
    <div class="sticky-lg-top bg-body overflow-x-auto py-1 px-3 mx--3 mb-1 border-bottom">
        <ul class="nav nav-pills nav-pills-dark flex-nowrap">
            <li class="nav-item">
                <a href="<?= go_to('../') ?>" class="nav-link rounded-pill text-nowrap --xhr">
                    <i class="mdi mdi-cart"></i> <?= phrase('Market') ?>
                </a>
            </li>
            <li class="nav-item">
                <a href="<?= current_page() ?>" class="nav-link rounded-pill active text-nowrap --xhr">
                    <i class="mdi mdi-palette"></i> <?= phrase('Installed Theme') ?>
                </a>
            </li>
            <li class="nav-item">
                <a href="<?= go_to('../modules') ?>" class="nav-link rounded-pill text-nowrap --xhr">
                    <i class="mdi mdi-puzzle"></i> <?= phrase('Installed Module') ?>
                </a>
            </li>
        </ul>
    </div>
    <div class="row border-bottom pb-1 mb-3">
        <div class="col-md-6 offset-md-6 text-end">
            <a href="<?= current_page('import') ?>" class="btn btn-primary btn-sm rounded-pill --xhr">
                <i class="mdi mdi-import"></i> <?= phrase('Import Theme') ?>
            </a>
        </div>
    </div>
    <div class="row">
        <?php if ($installed): ?>
            <?php foreach ($installed as $key => $val): ?>
                <?php if (file_exists(ROOTPATH . 'themes' . DIRECTORY_SEPARATOR . $val->folder . DIRECTORY_SEPARATOR . str_replace(['../', '..\\', './', '.\\'], '', $val->screenshot[0]->src))) {
                    $screenshot = base_url('themes/' . $val->folder . '/' . str_replace(['../', '..\\', './', '.\\'], '', $val->screenshot[0]->src));
                } else {
                    $screenshot = get_image(null, 'placeholder_thumb.png');
                } ?>

                <div class="col-sm-6 col-md-4 col-lg-3">
                    <div class="card border-hover rounded-4 mb-3">
                        <div class="card-body p-3">
                            <div class="position-relative mb-3">
                                <?= 'backend' == $val->type ? '<span class="badge bg-dark position-absolute top-0 end-0 z-1 m-2">' . phrase('Back End') . '</span>' : '<span class="badge bg-success position-absolute top-0 end-0 z-1 m-2">' . phrase('Front End') . '</span>' ?>
                                <div class="ratio ratio-4x3 bg-dark rounded-4 overflow-hidden">
                                    <a href="<?= current_page('detail', ['item' => $val->folder]) ?>" class="--modal d-block h-100">
                                        <img src="<?= $screenshot ?>" class="img-fluid w-100 h-100 object-fit-cover rounded-4 border" alt="<?= $val->name ?>" />
                                    </a>
                                </div>
                            </div>
                            <div class="mb-3">
                                <a href="<?= current_page('detail', ['item' => $val->folder]) ?>" class="text-decoration-none text-body --modal">
                                    <b data-bs-toggle="tooltip" title="<?= $val->name ?>">
                                        <?= truncate($val->name, 80) ?>
                                    </b>
                                </a>
                            </div>
                            <div class="row">
                                <div class="col-6">
                                    <?php if (('backend' == $val->type && get_setting('backend_theme') == $val->folder) || ('frontend' == $val->type && get_setting('frontend_theme') == $val->folder)): ?>
                                        <button type="button" class="btn btn-dark btn-xs d-block w-100" disabled>
                                            <i class="mdi mdi-check"></i> <?= phrase('Active') ?>
                                        </button>
                                    <?php else: ?>
                                        <a href="<?= current_page('activate', [
                                          'item' => $val->folder,
                                        ]) ?>" class="btn btn-success btn-xs d-block --modal">
                                            <i class="mdi mdi-check"></i> <?= phrase('Activate') ?>
                                        </a>
                                    <?php endif; ?>
                                </div>
                                <div class="col-6">
                                    <a href="<?= base_url('backend' == $val->type ? 'dashboard' : null, [
                                      'aksara_mode' => 'preview-theme',
                                      'aksara_theme' => $val->folder,
                                      'integrity_check' => $val->integrity,
                                    ]) ?>" class="btn btn-outline-primary d-block btn-xs" target="_blank">
                                        <i class="mdi mdi-magnify"></i> <?= phrase('Preview') ?>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-sm-12">
                <div class="alert alert-warning callout">
                    <i class="mdi mdi-information-outline"></i> <?= phrase('No installed theme.') ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
