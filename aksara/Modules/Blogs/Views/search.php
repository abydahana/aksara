<div class="py-3 py-md-5 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 offset-lg-2 text-center text-md-start">
                <h1 class="display-5 fw-bold">
                    <?= $meta->title; ?>
                </h1>
                <p class="lead">
                    <?= $meta->description; ?>
                </p>
                <form action="<?= base_url('blogs/search', ['per_page' => null]); ?>" method="GET" class="form-horizontal position-relative">
                    <div class="input-group input-group-lg">
                        <input type="text" name="q" class="form-control rounded-pill rounded-end" placeholder="<?= phrase('Search post'); ?>" value="<?= $keywords; ?>" />
                        <button type="submit" class="btn btn-dark  rounded-pill rounded-start">
                            <i class="mdi mdi-magnify"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="py-3 py-md-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 offset-lg-2">
                <?php if ($keywords): ?>
                    <p class="lead">
                        <?php if ($total): ?>
                            <?= phrase('Your search keyword {{ keywords }} has returning {{ total }} data.', ['keywords' => $keywords, 'total' => number_format($total)]); ?>
                        <?php else: ?>
                            <?= phrase('Your search keyword {{ keywords }} does not match any result.', ['keywords' => $keywords]); ?>
                        <?php endif; ?>
                    </p>
                <?php endif; ?>
                <?php if ($results): ?>
                    <?php foreach ($results as $key => $val): ?>
                        <div class="row g-0 g-md-3 align-items-center mb-5">
                            <div class="col-9 col-md-10">
                                <a href="<?= base_url(['blogs', $val->category_slug]); ?>" class="text-muted --xhr">
                                    <span class="badge bg-dark">
                                        <?= $val->category_title; ?>
                                    </span>
                                </a>
                                <h3>
                                    <a href="<?= base_url(['blogs', $val->category_slug, $val->post_slug]); ?>" class="text-primary --xhr">
                                    <?= ($keywords ? preg_replace('/' . $keywords . '/i', '<b>$0</b>', truncate($val->post_title, 160)) : truncate($val->post_title, 160)); ?>
                                    </a>
                                </h3>
                                <div class="lead">
                                    <?= ($keywords ? preg_replace('/' . $keywords . '/i', '<b>$0</b>', truncate($val->post_excerpt, 160)) : truncate($val->post_excerpt, 160)); ?>
                                </div>
                            </div>
                            <div class="col-3 col-md-2">
                                <a href="<?= base_url(['blogs', $val->category_slug, $val->post_slug]); ?>" class="--xhr">
                                    <img src="<?= get_image('blogs', $val->featured_image, 'thumb'); ?>" class="card-img rounded-4" alt="..." />
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <?= pagination($pagination); ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
