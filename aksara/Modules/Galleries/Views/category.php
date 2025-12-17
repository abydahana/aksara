<?php
    $output = null;

    if ($results) {
        foreach ($results as $key => $val) {
            $images = json_decode($val->gallery_images, true);
            $labels = explode(',', $val->gallery_tags);

            if (is_array($images) && sizeof($images) > 0) {
                foreach ($images as $src => $alt) {
                    if (! $src) {
                        continue;
                    }

                    $output .= '
                        <div class="col-sm-6 col-md-3">
                            <a href="' . current_page($src) . '" class="--xhr">
                                <img src="' . get_image('galleries', $src, 'thumb') . '" class="rounded-4 w-100 mb-4" alt="' . $alt . '" />
                            </a>
                        </div>
                    ';
                }
            }
        }
    }
?>

<?php if ($output): ?>
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
    <div class="py-3 py-md-5">
        <div class="container">
            <div class="row">
                <?= $output; ?>
            </div>
        </div>
    </div>
<?php else: ?>
    <div class="py-3 py-md-5">
        <div class="container">
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
                            <a href="<?= current_page('../'); ?>" class="btn btn-outline-dark rounded-pill px-5 --xhr">
                                <i class="mdi mdi-arrow-left"></i> <?= phrase('Back to Galleries'); ?>
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
