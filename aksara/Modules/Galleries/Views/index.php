<?php if ($results): ?>
<div class="bg-light">
    <div class="py-3 py-md-5">
        <div class="container">
            <h1 class="text-center text-md-start">
                <?= $meta->title; ?>
            </h1>
            <p class="lead text-center text-md-start">
                <?= truncate($meta->description, 256); ?>
            </p>
        </div>
    </div>
    <svg class="wave text-white" version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 100" preserveAspectRatio="none">
        <path class="wavePath" d="M826.337463,25.5396311 C670.970254,58.655965 603.696181,68.7870267 447.802481,35.1443383 C293.342778,1.81111414 137.33377,1.81111414 0,1.81111414 L0,150 L1920,150 L1920,1.81111414 C1739.53523,-16.6853983 1679.86404,73.1607868 1389.7826,37.4859505 C1099.70117,1.81111414 981.704672,-7.57670281 826.337463,25.5396311 Z" fill="currentColor"></path>
    </svg>
</div>
<?php endif; ?>
<div class="py-3 py-md-5">
    <div class="container">
        <?php if ($results): ?>
            <div class="row">
                <?php foreach ($results as $key => $val): ?>
                    <?php
                        $cover = null;
                        $thumbnail = null;
                        $images = json_decode($val->gallery_images, true);

                        if (! empty($images)) {
                            $num = 1;

                            foreach ($images as $src => $alt) {
                                if ($num >= 4) {
                                    break;
                                }

                                if (1 == $num) {
                                    $cover = $src;
                                } elseif ($num > 1) {
                                    $thumbnail .= '<a href="' . go_to([$val->gallery_slug, $src]) . '" class="--xhr"><img src="' . get_image('galleries', $src, 'thumb') . '" class="w-100" /></a>';
                                }

                                $num++;
                            }
                        }
                    ?>
                    <div class="col-lg-6">
                        <div class="rounded-4 overflow-hidden mb-3">
                            <div class="row g-0">
                                <div class="col-<?= (count($images) <= 2 ? 'md-' : null) . (count($images) == 2 ? 6 : (count($images) == 1 ? 12 : 9)); ?> text-center d-flex align-items-center" style="background:url(<?= get_image('galleries', $cover); ?>) center center no-repeat; background-size:cover; min-height:320px">
                                    <div class="p-3 w-100" style="background:rgba(0, 0, 0, .5)">
                                        <h4 class="text-light">
                                            <span class="badge bg-primary float-end">
                                                <?= count($images); ?>
                                            </span>
                                            <?= $val->gallery_title; ?>
                                        </h4>
                                        <p class="text-light">
                                            <?= truncate($val->gallery_description, 160); ?>
                                        </p>
                                        <p class="text-light">
                                            <?php if (count($images) > 4): ?>
                                                <a href="<?= go_to($val->gallery_slug); ?>" class="btn btn-outline-light rounded-pill --xhr">
                                                    <i class="mdi mdi-folder-multiple-image"></i>
                                                    <?= phrase('Show all'); ?>
                                                </a>
                                            <?php else: ?>
                                                <a href="<?= go_to([$val->gallery_slug, $cover]); ?>" class="btn btn-outline-light rounded-pill --xhr">
                                                    <i class="mdi mdi-magnify-plus"></i>
                                                    <?= phrase('Show'); ?>
                                                </a>
                                            <?php endif; ?>
                                        </p>
                                    </div>
                                </div>
                                <?php if (count($images) > 1): ?>
                                    <div class="col-<?= (count($images) <= 2 ? 'md-' : null) . (count($images) > 2 ? 3 : 6); ?> bg-light d-flex align-items-center">
                                        <div class="w-100">
                                        <?= $thumbnail; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <?= pagination($pagination); ?>
        <?php else: ?>
            <div class="row">
                <div class="col-lg-8 offset-lg-2">
                    <div class="py-5">
                        <div class="text-center">
                            <img src="<?= base_url('assets/yao-ming.png'); ?>" width="128" alt="404" />
                        </div>
                        <h2 class="text-center">
                            <?= phrase('No album is found!'); ?>
                        </h2>
                        <p class="lead text-center">
                            <?= phrase('No album is available at the moment.'); ?>
                        </p>
                        <p class="text-center">
                            <a href="<?= base_url(); ?>" class="btn btn-outline-dark rounded-pill px-5 --xhr">
                                <i class="mdi mdi-arrow-left"></i> <?= phrase('Back to Home'); ?>
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
