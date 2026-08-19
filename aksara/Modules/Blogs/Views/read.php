<?php

/**
 * @var mixed $results
 * @var mixed $meta
 * @var mixed $recommendations
 * @var mixed $related
 * @var mixed $categories
 */
$fieldData = $results->field_data ?? null;
$toc = null;
$article = null;
$featuredImage = null;
$postTags = null;

if ($fieldData) {
    $tags = explode(',', $fieldData->post_tags->value);

    if (sizeof($tags) > 0) {
        // Get post tags
        foreach ($tags as $tag => $label) {
            if (! $label) {
                continue;
            }

            $postTags .= '
                <a href="' . go_to('../tags', ['q' => trim($label)]) . '" class="me-2 --xhr">
                    <span class="badge bg-secondary">' . trim($label) . '</span>
                </a>
            ';
        }
    }

    // Reformat article output
    [$toc, $article] = toc_generator(
        str_replace(
            'MsoNormalTable',
            'table table-bordered',
            preg_replace('/(width|height)="\d*"\s/', '', preg_replace('~<p[^>]*>~', '<p class="text-lg-justify article text-break">', preg_replace('/(<[^>]+) style=".*?"/i', '$1', $fieldData->post_content->value))),
        ),
    );

    if ($fieldData->featured_image->value && 'placeholder.png' != $fieldData->featured_image->value) {
        // Get featured image
        $featuredImage = $fieldData->featured_image->value;
    }
}

if ($article): ?>
    <section class="section-padding border-fade-bottom fade-in">
        <div class="container text-center text-md-start">
            <h1 class="display-4 fw-bold">
                <?= $meta->title ?>
            </h1>
            <p class="fs-5 text-muted mb-0">
                <?= truncate($meta->description, 256) ?>
            </p>
        </div>
    </section>
    <section class="section-padding">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <main class="sticky-lg-top">
                        <div class="row align-items-center mb-3 fade-in">
                            <div class="col-sm-6 col-md-8 mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-0">
                                        <a href="<?= base_url('user/' . $fieldData->username->value) ?>" class="d-block --xhr">
                                            <img src="<?= get_image('users', $fieldData->photo->value, 'thumb') ?>" class="img-fluid rounded-circle" width="64" height="64" />
                                        </a>
                                    </div>
                                    <div class="flex-grow-1 ps-3">
                                        <a href="<?= base_url('user/' . $fieldData->username->value) ?>" class="lh-1 --xhr">
                                            <strong class="fs-5 fw-bold mb-0">
                                                <?= $fieldData->first_name->value . ' ' . $fieldData->last_name->value ?>
                                            </strong>
                                        </a>
                                        <p class="mb-0">
                                            <a href="<?= base_url('user/' . $fieldData->username->value) ?>" class="text-muted lh-1 --xhr">
                                                @<?= $fieldData->username->value ?>
                                            </a>
                                            &middot;
                                            <span class="text-muted" data-bs-toggle="tooltip" title="<?= $fieldData->created_at->value ?>">
                                                <?= time_ago($fieldData->created_at->value) ?>
                                            </span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-4">
                                <div class="btn-group d-flex rounded-pill overflow-hidden">
                                    <a href="//www.facebook.com/sharer/sharer.php?u=<?= current_page() ?>" class="btn btn-primary border-0" data-bs-toggle="tooltip" title="<?= phrase('Share to Facebook') ?>" target="_blank">
                                        <i class="mdi mdi-facebook"></i>
                                    </a>
                                    <a href="//www.twitter.com/share?url=<?= current_page() ?>" class="btn btn-info text-white border-0" data-bs-toggle="tooltip" title="<?= phrase('Share to Twitter') ?>" target="_blank">
                                        <i class="mdi mdi-twitter"></i>
                                    </a>
                                    <a href="//wa.me/?text=<?= current_page() ?>" class="btn btn-success border-0" data-bs-toggle="tooltip" title="<?= phrase('Send to WhatsApp') ?>" target="_blank">
                                        <i class="mdi mdi-whatsapp"></i>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <article>
                            <?php if ($featuredImage): ?>
                                <div class="mb-3 fade-in">
                                    <a href="<?= get_image('blogs', $featuredImage) ?>" class="d-block" target="_blank"><img id="og-image" src="<?= get_image('blogs', $featuredImage) ?>" class="w-100 rounded" /></a>
                                </div>
                            <?php endif; ?>

                            <?php if ($toc): ?>
                                <section id="post-description" class="fade-in">
                                    <div class="fs-5">
                                        <p class="text-lg-justify article text-break">
                                            <?= $meta->description ?>
                                        </p>
                                    </div>
                                    <div class="toc">
                                        <fieldset class="border border-light-subtle p-3 rounded-4 mb-3">
                                            <legend><?= phrase('Table of Contents') ?></legend>

                                            <?= $toc ?>
                                        </fieldset>
                                    </div>
                                </section>
                            <?php endif; ?>

                            <section id="post-article" class="fs-5 fade-in">
                                <?= article_with_recommendation(preg_replace('/<img[^>]*src="(.*?)"/i', '<img id="og-image" src="$1" class="img-fluid rounded mb-4"', $article), $recommendations) ?>
                            </section>

                            <section id="post-meta" class="fade-in">
                                <div class="my-3">
                                    <?= $postTags ?>
                                </div>

                                <i class="text-muted text-sm"><?= $fieldData->updated_at->value ? phrase('Updated at') . ' ' . phrase(date('l', strtotime($fieldData->updated_at->value))) . ', ' . $fieldData->updated_at->value : phrase('Created at') . ' ' . phrase(date('l', strtotime($fieldData->created_at->value))) . ', ' . $fieldData->created_at->value ?></i>
                            </section>
                            <section id="post-author" class="my-4">
                                <div class="card bg-body-tertiary border-hover rounded-4 p-4 fade-in">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0">
                                            <a href="<?= base_url('user/' . $fieldData->username->value) ?>" class="d-block --xhr">
                                                <img src="<?= get_image('users', $fieldData->photo->value, 'thumb') ?>" class="img-fluid rounded-circle shadow-sm" width="72" height="72" alt="<?= $fieldData->first_name->value . ' ' . $fieldData->last_name->value ?>" loading="lazy" decoding="async" />
                                            </a>
                                        </div>
                                        <div class="flex-grow-1 ps-3">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <div>
                                                    <a href="<?= base_url('user/' . $fieldData->username->value) ?>" class="text-body text-decoration-none --xhr">
                                                        <h5 class="fw-bold mb-0">
                                                            <?= $fieldData->first_name->value . ' ' . $fieldData->last_name->value ?>
                                                        </h5>
                                                    </a>
                                                    <span class="text-muted small">@<?= $fieldData->username->value ?></span>
                                                </div>
                                                <a href="<?= base_url('user/' . $fieldData->username->value) ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3 --xhr">
                                                    <?= phrase('View Profile') ?>
                                                </a>
                                            </div>

                                            <?php if (isset($fieldData->bio->value) && $fieldData->bio->value): ?>
                                                <p class="mt-2 mb-0 text-secondary">
                                                    <?= nl2br(htmlspecialchars($fieldData->bio->value)) ?>
                                                </p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </section>
                            <section id="post-comments" class="mb-3 fade-in">
                                <?= comment_widget(['post_id' => $fieldData->post_id->value, 'path' => service('uri')->getRoutePath()]) ?>
                            </section>
                        </article>
                    </main>
                </div>
                <div class="col-lg-4">
                    <aside class="sticky-lg-top fade-in">
                        <div class="mb-5">
                            <h4 class="mb-3">
                                <?= phrase('Related Articles') ?>
                            </h4>

                            <?php if ($related): ?>
                                <?php foreach ($related as $key => $val): ?>
                                    <div class="card rounded-4 border-light-subtle border-hover mb-3">
                                        <div class="card-body">
                                            <div class="row g-0 align-items-center">
                                                <div class="col-2">
                                                    <a href="<?= go_to('../' . $val->category_slug . '/' . $val->post_slug) ?>" class="--xhr">
                                                        <img src="<?= get_image('blogs', $val->featured_image, 'icon') ?>" class="img-fluid rounded-4" />
                                                    </a>
                                                </div>
                                                <div class="col-10 ps-3">
                                                    <h2 class="h5">
                                                        <a href="<?= go_to('../' . $val->category_slug . '/' . $val->post_slug) ?>" class="text-body --xhr">
                                                            <?= $val->post_title ?>
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
                                <?= phrase('Other Categories') ?>
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
                                                    <a href="<?= go_to('../' . $val->category_slug) ?>" class="d-block --xhr">
                                                        <img src="<?= get_image('blogs', $val->category_image, 'icon') ?>" class="img-fluid rounded-4" />
                                                    </a>
                                                </div>
                                                <div class="col-10 ps-3">
                                                    <a href="<?= go_to('../' . $val->category_slug) ?>" class="--xhr">
                                                        <h4 class="text-body mb-0">
                                                            <?= $val->category_title ?>
                                                        </h4>
                                                        <p class="mb-0 text-muted">
                                                            <?= number_format($val->total_data) . ' ' . ($val->total_data > 1 ? phrase('Articles') : phrase('Article')) ?>
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
<?php else: ?>
    <div class="section-padding">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 offset-lg-3">
                    <?= view('templates/404', [...(array) $meta, 'searchAction' => go_to('../search', ['page' => null]), 'searchLabel' => phrase('Search posts...')]) ?>
                </div>
            </div>

            <?php if ($recommendations): ?>
                <div class="row">
                    <div class="col-lg-8 offset-lg-2">
                        <div class="card rounded-4 mt-5">
                            <div class="card-body p-4">
                                <h5 class="fw-bold mb-3">
                                    <i class="mdi mdi-newspaper-variant-outline text-primary me-2"></i> <?= phrase('Recommended Articles') ?>
                                </h5>
                                <div class="list-group list-group-flush">
                                    <?php foreach ($recommendations as $val): ?>
                                        <?php
                                    $link = is_array($val) ? $val['link'] ?? '#' : $val->link ?? '#';
                                        $title = is_array($val) ? $val['title'] ?? '' : $val->title ?? '';
                                        $image = is_array($val) ? $val['image'] ?? null : $val->image ?? null;
                                        ?>
                                        <div class="list-group-item border-0 px-0 py-2">
                                            <div class="row g-3 align-items-center">
                                                <?php if ($image): ?>
                                                    <div class="col-auto">
                                                        <a href="<?= $link ?>" class="d-block --xhr">
                                                            <img src="<?= $image ?>" class="rounded-4" alt="<?= $title ?>" width="56" height="56" style="object-fit: cover;" />
                                                        </a>
                                                    </div>
                                                <?php endif; ?>

                                                <div class="col">
                                                    <a href="<?= $link ?>" class="text-body fw-medium text-break text-decoration-none --xhr">
                                                        <?= $title ?>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>
