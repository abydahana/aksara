<?php

/**
 * @var object|null $results
 * @var mixed $meta
 * @var iterable|null $suggestions
 */
$field_data = $results->field_data ?? null;

if ($field_data): ?>
    <?php
    $builder = new \Aksara\Libraries\PageBuilder\PageBuilder();
    $page_content = $field_data->page_content->value ?? '';
    $decoded = json_decode($page_content, true);

    $updated_at = $field_data->updated_at->value ?? null;
    $created_at = $field_data->created_at->value ?? null;
    $timestamp = $updated_at ?: $created_at;
    $timestamp_label = $updated_at
        ? phrase('Updated at')
        : phrase('Created at');
    ?>

    <?php if (json_last_error() === JSON_ERROR_NONE && isset($decoded['components'])): ?>
        <div class="fade-in">
            <?= $builder->render($decoded); ?>
        </div>
    <?php else: ?>
        <section class="section-padding fade-in">
            <div class="container">
                <div class="text-justify mb-3">
                    <?php
                    $content = preg_replace(
                        '/<img src="(.*?)"/i',
                        '<img id="og-image" src="$1" class="img-fluid rounded"',
                        $page_content
                    );

                    $content = preg_replace(
                        '/(<[^>]+) style=".*?"/i',
                        '$1',
                        $content
                    );
                    ?>

                    <?= $content; ?>
                </div>

                <?php if ($timestamp): ?>
                    <p>
                        <i class="text-muted text-sm">
                            <?= $timestamp_label; ?>
                            <?= phrase(date('l', strtotime($timestamp))); ?>,
                            <?= esc($timestamp); ?>
                        </i>
                    </p>
                <?php endif; ?>
            </div>
        </section>
    <?php endif; ?>
<?php else: ?>
    <section class="section-padding fade-in">
        <div class="container text-center py-5">
            <h1 class="text-muted">
                404
            </h1>
            <i class="mdi mdi-dropbox mdi-5x text-muted"></i>
        </div>

        <div class="row mb-5">
            <div class="col-md-6 offset-md-3">
                <h2 class="text-center">
                    <?= phrase('Page not found!'); ?>
                </h2>

                <p class="fs-5 text-center mb-5">
                    <?= phrase('The page you requested does not exist or has already been archived.'); ?>
                </p>

                <div class="text-center mt-5">
                    <a
                        href="<?= base_url(); ?>"
                        class="btn btn-sm btn-outline-primary rounded-pill px-lg-5 --xhr"
                    >
                        <i class="mdi mdi-arrow-left"></i>
                        <?= phrase('Back to Homepage'); ?>
                    </a>
                </div>
            </div>
        </div>

        <?php if (! empty($suggestions)): ?>
            <div class="row mb-2">
                <div class="col-md-10 offset-md-1">
                    <h5>
                        <?= phrase('Our Suggestions'); ?>
                        <span class="blink">_</span>
                    </h5>

                    <?php foreach ($suggestions as $index => $page): ?>
                        <?php if ($index): ?>
                            &middot;
                        <?php endif; ?>

                        <a href="<?= base_url('pages/' . rawurlencode($page->page_slug)); ?>">
                            <?= esc($page->page_title); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </section>
<?php endif; ?>
