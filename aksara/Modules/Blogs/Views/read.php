<?php

/**
 * @var mixed $results
 * @var mixed $meta
 * @var mixed $recommendations
 * @var mixed $related
 * @var mixed $categories
 */
$field_data = $results->field_data ?? null;
$toc = null;
$article = null;
$featured_image = null;
$post_tags = null;

if ($field_data) {
    $tags = explode(',', $field_data->post_tags->value);

    if (sizeof($tags) > 0) {
        // Get post tags
        foreach ($tags as $tag => $label) {
            if (! $label) continue; // empty label

            $post_tags .= '
                <a href="' . go_to('../tags', ['q' => trim($label)]) . '" class="me-2 --xhr">
                    <span class="badge bg-secondary">
                        ' . trim($label) . '
                    </span>
                </a>
            ';
        }
    }

    // Reformat article output
    list($toc, $article) = toc_generator(str_replace('MsoNormalTable', 'table table-bordered', preg_replace('/(width|height)="\d*"\s/', '', preg_replace('~<p[^>]*>~', '<p class="text-lg-justify article text-break">', preg_replace('/(<[^>]+) style=".*?"/i', '$1', $field_data->post_content->value)))));

    if ($field_data->featured_image->value && $field_data->featured_image->value != 'placeholder.png') {
        // Get featured image
        $featured_image = $field_data->featured_image->value;
    }
}
?>

<section class="section-padding border-fade-bottom fade-in">
    <div class="container text-center text-md-start">
        <h1 class="display-4 fw-bold">
            <?= $meta->title; ?>
        </h1>
        <p class="fs-5 text-muted mb-0">
            <?= truncate($meta->description, 256); ?>
        </p>
    </div>
</section>
<section class="section-padding">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <main class="sticky-top">
                    <?php if ($article): ?>
                        <div class="row align-items-center mb-3 fade-in">
                            <div class="col-sm-6 col-md-8 mb-3 order-1 order-md-0">
                                <div class="row align-items-center">
                                    <div class="col-2 pe-0">
                                        <a href="<?= base_url('user/' . $field_data->username->value); ?>" class="--xhr">
                                            <img src="<?= get_image('users', $field_data->photo->value, 'thumb'); ?>" class="img-fluid rounded-circle" />
                                        </a>
                                    </div>
                                    <div class="col-10">
                                        <h2 class="h5" class="fw-bold mb-0">
                                            <a href="<?= base_url('user/' . $field_data->username->value); ?>" class="--xhr">
                                                <?= $field_data->first_name->value . ' ' . $field_data->last_name->value; ?>
                                            </a>
                                        </h2>
                                        <p class="mb-0">
                                            <span class="text-muted" data-bs-toggle="tooltip" title="<?= $field_data->created_at->value; ?>">
                                                <?= time_ago($field_data->created_at->value); ?>
                                            </span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-4 mb-3 order-0 order-md-1">
                                <div class="btn-group btn-group-sm d-flex rounded-pill overflow-hidden">
                                    <a href="//www.facebook.com/sharer/sharer.php?u=<?= current_page(); ?>" class="btn btn-primary border-0" data-bs-toggle="tooltip" title="<?= phrase('Share to Facebook'); ?>" target="_blank">
                                        <i class="mdi mdi-facebook"></i>
                                    </a>
                                    <a href="//www.twitter.com/share?url=<?= current_page(); ?>" class="btn btn-info text-white border-0" data-bs-toggle="tooltip" title="<?= phrase('Share to Twitter'); ?>" target="_blank">
                                        <i class="mdi mdi-twitter"></i>
                                    </a>
                                    <a href="//wa.me/?text=<?= current_page(); ?>" class="btn btn-success border-0" data-bs-toggle="tooltip" title="<?= phrase('Send to WhatsApp'); ?>" target="_blank">
                                        <i class="mdi mdi-whatsapp"></i>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <article>
                            <?php if ($featured_image): ?>
                                <div class="mb-3 fade-in">
                                    <a href="<?= get_image('blogs', $featured_image); ?>" target="_blank"><img id="og-image" src="<?= get_image('blogs', $featured_image); ?>" class="w-100 rounded" /></a>
                                </div>
                            <?php endif; ?>

                            <?php if ($toc): ?>
                                <div class="fade-in">
                                    <div class="fs-5">
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
                                </div>
                            <?php endif; ?>

                            <div class="fs-5 fade-in">
                                <?= article_with_recommendation(preg_replace('/<img[^>]*src="(.*?)"/i', '<img id="og-image" src="$1" class="img-fluid rounded mb-4"', $article), $recommendations); ?>
                            </div>

                            <div class="fade-in">
                                <div class="my-3">
                                    <?= $post_tags; ?>
                                </div>

                                <i class="text-muted text-sm"><?= ($field_data->updated_at->value ? phrase('Updated at') . ' ' . phrase(date('l', strtotime($field_data->updated_at->value))) . ', ' . $field_data->updated_at->value : phrase('Created at') . ' ' . phrase(date('l', strtotime($field_data->created_at->value))) . ', ' . $field_data->created_at->value); ?></i>
                            </div>
                            <div class="fade-in">
                                <?= comment_widget(['post_id' => $field_data->post_id->value, 'path' => service('uri')->getRoutePath()]); ?>
                            </div>

                            <div class="d-md-none py-3">&nbsp;</div>
                        </article>
                    <?php else: ?>
                        <div class="alert alert-warning callout mb-5 fade-in">
                            <h3 class="mb-0"><?= phrase('No post found!'); ?></h3>
                            <hr />
                            <p class="fs-5 mb-0">
                                <i class="mdi mdi-alert-outline"></i> <?= phrase('The post you requested does not exist or already been archived.'); ?>
                            </p>
                        </div>
                    <?php endif; ?>
                </main>
            </div>
            <div class="col-lg-4">
                <aside class="sticky-top fade-in">
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
                                                <h2 class="h5">
                                                    <a href="<?= go_to('../' . $val->category_slug . '/' . $val->post_slug); ?>" class="text-body --xhr">
                                                        <?= $val->post_title; ?>
                                                    </a>
                                                </h2>
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
                                                    <h4 class="text-body mb-0">
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
                </aside>
            </div>
        </div>
    </div>
</section>
