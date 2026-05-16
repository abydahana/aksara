<?php
/**
 * @var mixed $results
 * @var mixed $meta
 * @var mixed $suggestions
 */
if (isset($results[0])): ?>
    <?php
        $page = $results[0];
        $builder = new \Aksara\Libraries\PageBuilder\PageBuilder();
        $decoded = json_decode($page->page_content, true);

        if (json_last_error() === JSON_ERROR_NONE && isset($decoded['components'])): 
    ?>
        <div class="fade-in">
            <?= $builder->render($decoded); ?>
        </div>
    <?php else: ?>
        <section class="section-padding fade-in">
            <div class="container">
                <div class="text-justify mb-3">
                    <?= preg_replace('/(<[^>]+) style=".*?"/i', '$1', preg_replace('/<img src="(.*?)"/i', '<img id="og-image" src="$1" class="img-fluid rounded"', $page->page_content)); ?>
                </div>
                <p>
                    <i class="text-muted text-sm">
                        <?= ($page->updated_timestamp ? phrase('Updated at') . ' ' . phrase(date('l', strtotime($page->updated_timestamp))) . ', ' . $page->updated_timestamp : phrase('Created at') . ' ' . phrase(date('l', strtotime($page->created_timestamp))) . ', ' . $page->created_timestamp); ?>
                    </i>
                </p>
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
                    <?= phrase('The page you requested does not exist or already been archived.'); ?>
                </p>
                <div class="text-center mt-5">
                    <a href="<?= base_url(); ?>" class="btn btn-sm btn-outline-primary rounded-pill px-lg-5 --xhr">
                        <i class="mdi mdi-arrow-left"></i>
                        <?= phrase('Back to Homepage'); ?>
                    </a>
                </div>
            </div>
        </div>
        <?php if (isset($suggestions) && $suggestions): ?>
            <div class="row mb-2">
                <div class="col-md-10 offset-md-1">
                    <h5>
                        <?= phrase('Our Suggestions'); ?>
                        <blink>_</blink>
                    </h5>
                    <?php foreach ($suggestions as $index => $page): ?>
                        <?php if ($index): ?> &middot; <?php endif; ?>
                        <a href="<?= base_url('pages/' . $page->page_slug); ?>">
                            <?= $page->page_title; ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </section>
<?php endif; ?>
