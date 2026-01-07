
<?php
    $toc = null;
    $article = null;
    $featuredImage = null;
    $postTags = null;

    if ($results) {
        $tags = [];

        foreach ($results as $key => $val) {
            $tags = explode(',', $val->post_tags);

            if (sizeof($tags) > 0) {
                // Get post tags
                foreach ($tags as $tag => $label) {
                    if (! $label) continue; // empty label

                    $postTags .= '
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
            $featuredImage = $results[0]->featured_image;
        }
    }
?>

<div class="bg-light background-clip" style="background:url(<?= get_image('blogs', $featuredImage); ?>) center center no-repeat; background-size:cover">
    <div class="py-3 py-md-5">
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
    <svg class="wave text-white position-absolute bottom-0" version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 100" preserveAspectRatio="none">
        <path class="wavePath" d="M826.337463,25.5396311 C670.970254,58.655965 603.696181,68.7870267 447.802481,35.1443383 C293.342778,1.81111414 137.33377,1.81111414 0,1.81111414 L0,150 L1920,150 L1920,1.81111414 C1739.53523,-16.6853983 1679.86404,73.1607868 1389.7826,37.4859505 C1099.70117,1.81111414 981.704672,-7.57670281 826.337463,25.5396311 Z" fill="currentColor"></path>
    </svg>
</div>
<div class="py-3">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <div class="sticky-top">
                    <?php if ($article): ?>
                        <div class="row align-items-center mb-3">
                            <div class="col-sm-6 col-md-8 mb-3 order-1 order-md-0">
                                <div class="row align-items-center">
                                    <div class="col-2 pe-0">
                                        <a href="<?= base_url('user/' . $results[0]->username); ?>" class="--xhr">
                                            <img src="<?= get_image('users', $results[0]->photo, 'thumb'); ?>" class="img-fluid rounded-circle" />
                                        </a>
                                    </div>
                                    <div class="col-10">
                                        <h5 class="fw-bold mb-0">
                                            <a href="<?= base_url('user/' . $results[0]->username); ?>" class="--xhr">
                                                <?= $results[0]->first_name . ' ' . $results[0]->last_name; ?>
                                            </a>
                                        </h5>
                                        <p class="mb-0">
                                            <span class="text-muted" data-bs-toggle="tooltip" title="<?= $results[0]->created_timestamp; ?>">
                                                <?= time_ago($results[0]->created_timestamp); ?>
                                            </span>
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

                        <?php if ($featuredImage): ?>
                            <a href="<?= get_image('blogs', $featuredImage); ?>" target="_blank"><img id="og-image" src="<?= get_image('blogs', $featuredImage); ?>" class="img-fluid rounded d-none" width="100%" /></a>
                        <?php endif; ?>

                        <?php if ($toc): ?>
                            <div class="lead">
                                <p class="text-lg-justify article text-break">
                                    <?= $meta->description; ?>
                                </p>
                            </div>
                            <div class="toc">
                                <fieldset class="border border-light-subtle p-3 rounded-4 mb-3">
                                    <legend><?= phrase('Table of Contents'); ?></legend>
                                    <?= $toc; ?>
                                </fieldset>
                            </div>
                        <?php endif; ?>

                        <div class="lead">
                            <?= recommendation_generator(preg_replace('/<img[^>]*src="(.*?)"/i', '<img id="og-image" src="$1" class="img-fluid rounded"', $article), $recommendations); ?>
                        </div>

                        <div class="tags">
                            <?= $postTags; ?>
                        </div>

                        <div>
                            <i class="text-muted text-sm"><?= ($results[0]->updated_timestamp ? phrase('Updated at') . ' ' . phrase(date('l', strtotime($results[0]->updated_timestamp))) . ', ' . $results[0]->updated_timestamp : phrase('Created at') . ' ' . phrase(date('l', strtotime($results[0]->created_timestamp))) . ', ' . $results[0]->created_timestamp); ?></i>
                        </div>

                        <?= comment_widget(['post_id' => $results[0]->post_id, 'path' => service('uri')->getRoutePath()]); ?>

                        <div class="d-md-none py-3">&nbsp;</div>
                    <?php else: ?>
                        <div class="alert alert-warning callout mb-5">
                            <h3 class="mb-0"><?= phrase('No post found!'); ?></h3>
                            <hr />
                            <p class="lead mb-0">
                                <i class="mdi mdi-alert-outline"></i> <?= phrase('The post you requested does not exist or already been archived.'); ?>
                            </p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="sticky-top">
                    <div class="mb-5">
                        <h4 class="mb-3">
                            <?= phrase('Related Articles'); ?>
                        </h4>
                        <?php if ($related): ?>
                            <?php foreach ($related as $key => $val): ?>
                                <div class="card rounded-4 border-light-subtle mb-3">
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
                        <div class="card rounded-4 border-light-subtle mb-3">
                            <div class="card-body">
                                <?php if ($categories): ?>
                                    <?php foreach ($categories as $key => $val): ?>
                                        <?php if ($key): ?>
                                            <hr class="mx--3 border-secondary" />
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
