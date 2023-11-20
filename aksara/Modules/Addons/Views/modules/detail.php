<?php
    $carousel = null;
    $attribution = null;

    if (isset($detail->screenshot) && $detail->screenshot) {
        foreach ($detail->screenshot as $key => $val) {
            if (file_exists(ROOTPATH . 'modules' . DIRECTORY_SEPARATOR . $detail->folder . DIRECTORY_SEPARATOR . $detail->screenshot[0]->src)) {
                $screenshot = base_url('modules/' . $detail->folder . '/' . $val->src);
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
                    <?php if (isset($detail->screenshot) && sizeof($detail->screenshot) > 1): ?>
                        <a class="carousel-control-prev gradient-right" href="#carouselExampleControls" role="button" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        </a>
                        <a class="carousel-control-next gradient-left" href="#carouselExampleControls" role="button" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-5">
            <h5>
                <?= $detail->name; ?>
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
    <hr class="row" />
    <div class="row">
        <div class="col-md-6 offset-md-6 col-lg-5 offset-lg-7">
            <div class="row">
                <div class="col-sm-6">
                    <div class="d-grid">
                        <a href="<?= current_page('../update', ['item' => $detail->folder]); ?>" class="btn btn-outline-primary btn-sm --modal">
                            <i class="mdi mdi-auto-fix"></i>
                            <?= phrase('Update'); ?>
                        </a>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="d-grid">
                        <a href="<?= current_page('../delete', ['item' => $detail->folder]); ?>" class="btn btn-outline-danger btn-sm --modal">
                            <i class="mdi mdi-delete"></i>
                            <?= phrase('Uninstall'); ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
