<?php

/**
 * @var object|null $results
 * @var mixed $meta
 * @var iterable|null $suggestions
 */
$fieldData = $results->field_data ?? null;

if ($fieldData):
    $builder = new \Aksara\Libraries\PageBuilder\PageBuilder();
    $pageContent = $fieldData->page_content->value ?? '';
    $decoded = json_decode($pageContent, true);
    $updatedAt = $fieldData->updated_at->value ?? null;
    $createdAt = $fieldData->created_at->value ?? null;
    $timestamp = $updatedAt ?: $createdAt;
    $timestampLabel = $updatedAt ? phrase('Updated at') : phrase('Created at');

    $content = preg_replace('/<img src="(.*?)"/i', '<img id="og-image" src="$1" class="img-fluid rounded"', $pageContent);
    $content = preg_replace('/(<[^>]+) style=".*?"/i', '$1', $content);

    if (json_last_error() === JSON_ERROR_NONE && isset($decoded['components'])): ?>

        <div class="fade-in">
            <?= $builder->render($decoded) ?>
        </div>
    <?php else: ?>
        <section class="section-padding fade-in">
            <div class="container">
                <div class="text-justify mb-3">
                    <?= $content ?>
                </div>

                <?php if ($timestamp): ?>
                    <p>
                        <i class="text-muted text-sm">
                            <?= $timestampLabel ?>
                            <?= phrase(date('l', strtotime($timestamp))) ?>,
                            <?= esc($timestamp) ?>
                        </i>
                    </p>
                <?php endif; ?>
            </div>
        </section>
    <?php endif;
?>
<?php else: ?>
    <section class="section-padding fade-in">
        <div class="container">
            <div class="row mb-5">
                <div class="col-md-6 offset-md-3">
                    <?= view('templates/404', [...(array) $meta, 'searchLabel' => phrase('Search pages...')]) ?>
                </div>
            </div>
        </div>

        <?php if (! empty($suggestions)): ?>
            <div class="row mb-2">
                <div class="col-md-8 offset-md-2">
                    <h2 class="h5">
                        <?= phrase('Our Suggestions') ?>
                        <span class="blink">_</span>
                    </h2>

                    <?php foreach ($suggestions as $index => $page): ?>
                        <?php if ($index): ?>
                            &middot;
                        <?php endif; ?>

                        <a href="<?= base_url('pages/' . rawurlencode($page->page_slug)) ?>" class="fs-5 --xhr">
                            <?= esc($page->page_title) ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <div class="text-center mt-5">
            <a href="<?= base_url() ?>" class="btn btn-outline-primary rounded-pill px-lg-5 --xhr">
                <i class="mdi mdi-arrow-left"></i> <?= phrase('Back to Homepage') ?>
            </a>
        </div>
    </section>
<?php endif; ?>
