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
                <a href="<?= go_to('../themes') ?>" class="nav-link rounded-pill text-nowrap --xhr">
                    <i class="mdi mdi-palette"></i> <?= phrase('Installed Theme') ?>
                </a>
            </li>
            <li class="nav-item">
                <a href="<?= current_page() ?>" class="nav-link rounded-pill active text-nowrap --xhr">
                    <i class="mdi mdi-puzzle"></i> <?= phrase('Installed Module') ?>
                </a>
            </li>
        </ul>
    </div>
    <div class="row border-bottom pb-1 mb-3">
        <div class="col-md-6 offset-md-6 text-end">
            <a href="<?= current_page('import') ?>" class="btn btn-primary btn-sm rounded-pill --xhr">
                <i class="mdi mdi-import"></i> <?= phrase('Import Module') ?>
            </a>
        </div>
    </div>
    <div class="row">
        <?php if ($installed): ?>
            <?php foreach ($installed as $key => $val): ?>
                <?php if (file_exists(ROOTPATH . 'modules' . DIRECTORY_SEPARATOR . $val->folder . DIRECTORY_SEPARATOR . str_replace(['../', '..\\', './', '.\\'], '', $val->screenshot[0]->src))) {
                    $screenshot = base_url('modules/' . $val->folder . '/' . str_replace(['../', '..\\', './', '.\\'], '', $val->screenshot[0]->src));
                } else {
                    $screenshot = get_image(null, 'placeholder_thumb.png');
                } ?>

                <div class="col-sm-6 col-md-4 col-lg-3">
                    <div class="card border-hover rounded-4 mb-3">
                        <div class="card-body p-3">
                            <div class="position-relative mb-3">
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
                                    <a href="<?= current_page('update', [
                                      'item' => $val->folder,
                                    ]) ?>" class="btn btn-outline-success d-block btn-xs --modal">
                                        <i class="mdi mdi-auto-fix"></i> <?= phrase('Update') ?>
                                    </a>
                                </div>
                                <div class="col-6">
                                    <a href="<?= current_page('delete', [
                                      'item' => $val->folder,
                                    ]) ?>" class="btn btn-danger d-block btn-xs --modal">
                                        <i class="mdi mdi-delete"></i> <?= phrase('Uninstall') ?>
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
                    <i class="mdi mdi-information-outline"></i> <?= phrase('No installed module.') ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
