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
            <div class="col-lg-8">
                <?php if ($results): ?>
                    <?php foreach ($results as $key => $val): ?>
                        <blockquote class="blockquote <?= ($key ? 'mt-5' : null); ?>">
                            <a href="<?= go_to($val->announcement_slug); ?>" class="--xhr">
                                <h5 class="mb-0">
                                    <?= $val->title; ?>
                                </h5>
                            </a>
                            <p class="lead">
                                <?= truncate($val->content, 160); ?>
                            </p>
                            <footer class="blockquote-footer">
                                <?= phrase('Effective until') . ' ' . $val->end_date; ?>
                            </footer>
                        </blockquote>
                        
                        <?= pagination($pagination); ?>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-muted">
                        <i class="mdi mdi-information-outline"></i>
                        <?= phrase('No announcement is available at the moment.'); ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
