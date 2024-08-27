
<?php
    $toc = null;
    $article = null;
    $featured_image = null;
    $post_tags = null;

    if ($results) {
        $tags = [];
        
        foreach ($results as $key => $val) {
            $tags = explode(',', $val->post_tags);

            if (sizeof($tags) > 0) {
                // Get post tags
                foreach ($tags as $tag => $label) {
                    if (! $label) continue; // empty label

                    $post_tags .= '
                        <a href="' . go_to('../tags', ['q' => trim($label)]) . '" class="--xhr">
                            <span class="badge bg-secondary">
                                ' . trim($label) . '
                            </span>
                        </a>
                    ';
                }
            }

            // Reformat article output
            list($toc, $article) = toc_generator(str_replace('MsoNormalTable', 'table table-bordered', preg_replace('/(width|height)="\d*"\s/', '', preg_replace('~<p[^>]*>~', '<p class="text-lg-justify article text-break">', preg_replace('/(<[^>]+) style=".*?"/i', '$1', $val->post_content)))));
        }

        if ($results[0]->featured_image && $results[0]->featured_image != 'placeholder.png') {
            // Get featured image
            $featured_image = $results[0]->featured_image;
        }
    }
?>

<div class="py-3 py-md-5 bg-light background-clip" style="background:url(<?= get_image('blogs', $featured_image); ?>) center center no-repeat; background-size:cover">
    <div class="container py-lg-5">
        <h1 class="text-center text-md-start text-light">
            <?= $meta->title; ?>
        </h1>
        <?php if (! $toc): ?>
        <div class="lead">
            <p class="text-lg-justify article text-break text-light">
                <?= $meta->description; ?>
            </p>
        </div>
        <?php endif; ?>
    </div>
</div>
<div class="py-3">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <?php if ($article): ?>
                    <div class="row">
                        <div class="col-sm-6 col-md-8 mb-3 order-1 order-md-0">
                            <div class="row g-0 align-items-center">
                                <div class="col-2 col-sm-1">
                                    <a href="<?= base_url('user/' . $results[0]->username); ?>" class="--xhr">
                                        <img src="<?= get_image('users', $results[0]->photo, 'thumb'); ?>" class="img-fluid rounded-circle" />
                                    </a>
                                </div>
                                <div class="col-10 col-sm-11 ps-3">
                                    <h5 class="fw-bold mb-0">
                                        <a href="<?= base_url('user/' . $results[0]->username); ?>" class="--xhr">
                                            <?= $results[0]->first_name . ' ' . $results[0]->last_name; ?>
                                        </a>
                                    </h5>
                                    <p class="mb-0">
                                        <small class="text-muted" data-bs-toggle="tooltip" title="<?= $results[0]->updated_timestamp; ?>">
                                            <?= time_ago($results[0]->updated_timestamp); ?>
                                        </small>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-4 mb-3 order-0 order-md-1">
                            <div class="btn-group btn-group-sm d-flex rounded-pill overflow-hidden">
                                <a href="//www.facebook.com/sharer/sharer.php?u=<?= current_page(); ?>'" class="btn btn-primary" data-bs-toggle="tooltip" title="<?= phrase('Share to Facebook'); ?>" target="_blank">
                                    <i class="mdi mdi-facebook"></i>
                                </a>
                                <a href="//www.twitter.com/share?url=<?= current_page(); ?>" class="btn btn-info text-light" data-bs-toggle="tooltip" title="<?= phrase('Share to Twitter'); ?>" target="_blank">
                                    <i class="mdi mdi-twitter"></i>
                                </a>
                                <a href="//wa.me/?text=<?= current_page(); ?>" class="btn btn-success" data-bs-toggle="tooltip" title="<?= phrase('Send to WhatsApp'); ?>" target="_blank">
                                    <i class="mdi mdi-whatsapp"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    <?php if ($featured_image): ?>
                        <a href="<?= get_image('blogs', $featured_image); ?>" target="_blank"><img id="og-image" src="<?= get_image('blogs', $featured_image); ?>" class="img-fluid rounded d-none" width="100%" /></a>
                    <?php endif; ?>

                    <?php if ($toc): ?>
                        <div class="lead">
                            <p class="text-lg-justify article text-break">
                                <?= $meta->description; ?>
                            </p>
                        </div>
                        <div class="toc">
                            <fieldset class="border p-3 rounded-4 mb-3">
                                <legend><?= phrase('Table of Contents'); ?></legend>
                                <?= $toc; ?>
                            </fieldset>
                        </div>
                    <?php endif; ?>

                    <div class="lead">
                        <?= related_generator(preg_replace('/<img[^>]*src="(.*?)"/i', '<img id="og-image" src="$1" class="img-fluid rounded"', $article), $related); ?>
                    </div>

                    <div class="tags">
                        <?= $post_tags; ?>
                    </div>

                    <?= comment_widget(['post_id' => $results[0]->post_id, 'path' => service('uri')->getRoutePath()]); ?>
                <?php else: ?>
                    <div class="alert alert-warning">
                        <i class="mdi mdi-alert-outline"></i>
                        <?= phrase('The post you requested does not exist or already been archived'); ?>
                    </div>
                <?php endif; ?>
            </div>
            <div class="col-lg-4">
                <div class="sticky-top">
                    <div class="mb-5">
                        <h4 class="mb-3">
                            <?= phrase('Recommended Articles'); ?>
                        </h4>
                        <?php if ($recommendations): ?>
                            <?php foreach ($recommendations as $key => $val): ?>
                                <div class="card rounded-4 mb-3">
                                    <div class="card-body">
                                        <div class="row g-0 align-items-center">
                                            <div class="col-2">
                                                <a href="<?= go_to('../' . $val->category_slug . '/' . $val->post_slug); ?>" class="--xhr">
                                                    <img src="<?= get_image('blogs', $val->featured_image, 'icon'); ?>" class="img-fluid rounded-4" />
                                                </a>
                                            </div>
                                            <div class="col-10 ps-3">
                                                <h5>
                                                    <a href="<?= go_to('../' . $val->category_slug . '/' . $val->post_slug); ?>" class="text-dark --xhr">
                                                        <?= $val->post_title; ?>
                                                    </a>
                                                </h5>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    
                    <div class="mb-5">
                        <h4 class="mb-3">
                            <?= phrase('Other Categories'); ?>
                        </h4>
                        <div class="card rounded-4 mb-3">
                            <div class="card-body">
                                <?php if ($categories): ?>
                                    <?php foreach ($categories as $key => $val): ?>
                                        <?php if ($key): ?>
                                            <hr class="border-secondary" />
                                        <?php endif; ?>

                                        <div class="row g-0 align-items-center">
                                            <div class="col-2">
                                                <a href="<?= go_to('../' . $val->category_slug); ?>" class="--xhr">
                                                    <img src="<?= get_image('blogs', $val->category_image, 'icon'); ?>" class="img-fluid rounded-4" />
                                                </a>
                                            </div>
                                            <div class="col-10 ps-3">
                                                <a href="<?= go_to('../' . $val->category_slug); ?>" class="--xhr">
                                                    <h4 class="text-dark mb-0">
                                                        <?= $val->category_title; ?>
                                                    </h4>
                                                    <p class="mb-0 text-muted">
                                                        <?= number_format($val->total_data) . ' ' . ($val->total_data > 1 ? phrase('Articles') : phrase('Article')); ?>
                                                    </p>
                                                </a>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
