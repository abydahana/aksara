<?php
    $carousel = null;
    $attribution = null;

    if (isset($detail->screenshot) && $detail->screenshot) {
        foreach ($detail->screenshot as $key => $val) {
            if (file_exists(ROOTPATH . 'themes' . DIRECTORY_SEPARATOR . $detail->folder . DIRECTORY_SEPARATOR . str_replace(['../', '..\\', './', '.\\'], '', $val->src))) {
                $screenshot = base_url('themes/' . $detail->folder . '/' . str_replace(['../', '..\\', './', '.\\'], '', $val->src));
            } else {
                $screenshot = get_image(null, 'placeholder_thumb.png');
            }

            $carousel .= '
                <div class="carousel-item rounded' . (! $key ? ' active' : null) . '">
                    <a href="' . $screenshot . '" target="_blank">
                        <img src="' . $screenshot . '" class="d-block rounded w-100" alt="' . $val->alt . '">
                    </a>
                </div>
            ';
        }
    }

    if (isset($detail->attribution) && $detail->attribution) {
        foreach ($detail->attribution as $key => $val) {
            $attribution .= '
                <div class="row">
                    <div class="col-4">
                        <label class="mb-0 text-muted">
                            ' . $key . '
                        </label>
                    </div>
                    <div class="col-8">
                        <label class="mb-0">
                            ' . $val . '
                        </label>
                    </div>
                </div>
            ';
        }
    }
?>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-6 col-lg-7">
            <div class="position-relative rounded" style="overflow: hidden">
                <div id="carouselExampleControls" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        <?= $carousel; ?>
                    </div>
                    <?php if (sizeof($detail->screenshot) > 1) { ?>
                        <a class="carousel-control-prev gradient-right" href="#carouselExampleControls" role="button" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        </a>
                        <a class="carousel-control-next gradient-left" href="#carouselExampleControls" role="button" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        </a>
                    <?php } ?>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-5">
            <h5>
                <?= $detail->name; ?>
                <?= ($detail->type == 'backend' ? '<span class="badge bg-dark float-end">' . phrase('Back End') . '</span>' : '<span class="badge bg-success float-end">' . phrase('Front End') . '</span>'); ?>
            </h5>
            <hr class="mt-1 mb-1" />
            <div class="row">
                <div class="col-4">
                    <label class="mb-0 text-muted">
                        <?= phrase('Author'); ?>
                    </label>
                </div>
                <div class="col-8">
                    <label class="mb-0">
                        <?= (isset($detail->website) ? '<a href="' . $detail->website . '" target="_blank"><b>' . $detail->author . '</b></a>' : '<b>' . $detail->author . '</b>'); ?>
                    </label>
                </div>
            </div>
            <div class="row">
                <div class="col-4">
                    <label class="mb-0 text-muted">
                        <?= phrase('Version'); ?>
                    </label>
                </div>
                <div class="col-8">
                    <label class="mb-0">
                        <?= $detail->version; ?>
                    </label>
                </div>
            </div>
            <?= $attribution; ?>
            <hr class="mt-1" />
            <div class="mb-0">
                <?= nl2br($detail->description); ?>
            </div>
        </div>
    </div>
    <hr class="m--3" />
    <div class="row">
        <div class="col-md-6 col-lg-7">
            <a href="<?= current_page('../update', ['item' => $detail->folder]); ?>" class="btn btn-outline-success btn-sm --modal">
                &nbsp; 
                <i class="mdi mdi-auto-fix"></i>
                <?= phrase('Update'); ?>
                &nbsp; 
            </a>
        </div>
        <div class="col-md-6 col-lg-5">
            <div class="row">
                <div class="col-sm-4">
                    <div class="d-grid">
                        <?php if (($detail->type == 'backend' && $detail->folder == get_setting('backend_theme')) || ($detail->type == 'frontend' && $detail->folder == get_setting('frontend_theme'))): ?>
                        <a href="<?= current_page('../customize', ['theme' => $detail->folder]); ?>" class="btn btn-dark btn-sm --modal">
                            <i class="mdi mdi-cogs"></i>
                            <?= phrase('Customize'); ?>
                        </a>
                        <?php else: ?>
                        <a href="<?= current_page('../activate'); ?>" class="btn btn-success btn-sm --modal">
                            <i class="mdi mdi-check"></i>
                            <?= phrase('Activate'); ?>
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="d-grid">
                        <a href="<?= base_url(('backend' == $detail->type ? 'dashboard' : null), ['aksara_mode' => 'preview-theme', 'aksara_theme' => $detail->folder, 'integrity_check' => $detail->integrity]); ?>" class="btn btn-outline-primary btn-sm" target="_blank">
                            <i class="mdi mdi-magnify"></i>
                            <?= phrase('Preview'); ?>
                        </a>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="d-grid">
                        <a href="<?= current_page('../delete', ['item' => $detail->folder]); ?>" class="btn btn-outline-danger btn-sm --modal">
                            <i class="mdi mdi-window-close"></i>
                            <?= phrase('Delete'); ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
