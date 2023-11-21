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
                                <img src="' . get_image('galleries', $src, 'thumb') . '" class="rounded-4 shadow-sm w-100 mb-4" alt="' . $alt . '" />
                            </a>
                        </div>
                    ';
                }
            }
        }
    }
?>

<?php if ($output): ?>
    <div class="py-3 py-md-5 bg-light">
        <div class="container">
            <h1 class="text-center text-md-start">
                <?= $meta->title; ?>
            </h1>
            <p class="lead text-center text-md-start">
                <?= truncate($meta->description, 256); ?>
            </p>
        </div>
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
            <div class="text-center py-5">
                <h1 class="text-muted">
                    404
                </h1>
                <i class="mdi mdi-dropbox mdi-5x text-muted"></i>
            </div>
            <div class="row mb-5">
                <div class="col-md-6 offset-md-3">
                    <h2 class="text-center">
                        <?= phrase('Album not found!'); ?>
                    </h2>
                    <p class="lead text-center mb-5">
                        <?= phrase('The album you requested was not found or already been archived.'); ?>
                    </p>
                    <div class="text-center mt-5">
                        <a href="<?= go_to('../'); ?>" class="btn btn-outline-primary rounded-pill --xhr">
                            <i class="mdi mdi-arrow-left"></i>
                            <?= phrase('Back to Galleries'); ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
