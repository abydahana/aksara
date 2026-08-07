<?php

/**
 * @var mixed $meta
 * @var mixed $keywords
 * @var mixed $total
 * @var mixed $results
 * @var mixed $pagination
 */
?>

<section class="section-padding border-fade-bottom fade-in">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 offset-lg-2 text-center text-md-start">
                <h1 class="display-5 fw-bold">
                    <?= $meta->title; ?>
                </h1>
                <p class="fs-5">
                    <?= $meta->description; ?>
                </p>
                <form action="<?= base_url('blogs/search', ['per_page' => null]); ?>" method="GET">
                    <div class="d-flex g-3 rounded-pill border border-light-subtle p-1">
                        <div class="input-group ps-4">
                            <i class="mdi mdi-magnify mdi-2x text-muted"></i>
                            <input type="text" name="q" class="form-control form-control-lg fw-light border-0 bg-transparent" value="<?= htmlspecialchars($keywords ?? ''); ?>" placeholder="<?= phrase('Search posts...'); ?>" required>
                            <button type="submit" class="btn btn-primary btn-lg fw-light rounded-pill px-4">
                                <?= phrase('Search'); ?> <i class="mdi mdi-arrow-right"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<section class="section-padding">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 offset-lg-2">
                <?php if ($keywords): ?>
                    <div class="alert alert-info rounded-4 border-0 lead fade-in">
                        <?php if ($pagination->total): ?>
                            <?= phrase('Your search keyword {{keywords}} has returning {{total}} data.', ['keywords' => $keywords, 'total' => number_format($pagination->total)]); ?>
                        <?php else: ?>
                            <?= phrase('Your search keyword {{keywords}} does not match any result.', ['keywords' => $keywords]); ?>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <?php if ($results): ?>
                    <?php foreach ($results as $key => $val): ?>
                        <div class="row g-0 g-md-3 align-items-center mb-5 fade-in">
                            <div class="col-9 col-md-10">
                                <a href="<?= base_url(['blogs', $val->category_slug]); ?>" class="text-muted --xhr">
                                    <span class="badge bg-dark">
                                        <?= $val->category_title; ?>
                                    </span>
                                </a>
                                <h3>
                                    <a href="<?= base_url(['blogs', $val->category_slug, $val->post_slug]); ?>" class="--xhr">
                                    <?= ($keywords ? preg_replace('/' . $keywords . '/i', '<b>$0</b>', truncate($val->post_title, 160)) : truncate($val->post_title, 160)); ?>
                                    </a>
                                </h3>
                                <div class="fs-5 text-muted">
                                    <?= ($keywords ? preg_replace('/' . $keywords . '/i', '<b>$0</b>', truncate($val->post_excerpt, 160)) : truncate($val->post_excerpt, 160)); ?>
                                </div>
                            </div>
                            <div class="col-3 col-md-2">
                                <a href="<?= base_url(['blogs', $val->category_slug, $val->post_slug]); ?>" class="--xhr">
                                    <img src="<?= get_image('blogs', $val->featured_image, 'thumb'); ?>" class="card-img rounded-4" alt="<?= $val->post_title; ?>" />
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <?= pagination($pagination); ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
