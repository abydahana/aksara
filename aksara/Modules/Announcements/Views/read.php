<?php

/**
 * @var mixed $results
 * @var mixed $meta
 */

$field_data = $results->field_data ?? null;
?>

<section class="section-padding border-fade-bottom fade-in">
    <div class="container position-relative text-center text-md-start">
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
            <div class="col-lg-8 offset-lg-2">
                <?php if ($field_data): ?>
                    <?php if ($field_data->cover->value && 'placeholder.png' != $field_data->cover->value): ?>
                        <img src="<?= get_image('announcements', $field_data->cover->value); ?>" class="img-fluid rounded-4 mb-3 fade-in" alt="<?= $field_data->title->value ?? phrase('Announcement'); ?>" />
                    <?php endif; ?>
                    <div class="fs-5 fade-in">
                        <?= $field_data->content->value; ?>
                    </div>
                    <p class="text-muted fade-in">
                        <em>
                            <?= phrase('This announcement will be effective until {{end_date}}.', ['end_date' => $field_data->end_date->value]); ?>
                        </em>
                    </p>
                    <a href="<?= current_page('../'); ?>" class="btn btn-outline-primary rounded-pill px-5 fade-in --xhr">
                        <i class="mdi mdi-arrow-left"></i>
                        <?= phrase('Back'); ?>
                    </a>
                <?php else: ?>
                    <div class="py-5 fade-in">
                        <div class="text-center">
                            <img src="<?= base_url('assets/yao-ming.png'); ?>" width="128" alt="404" />
                        </div>
                        <h2 class="text-center">
                            <?= phrase('No announcement is found!'); ?>
                        </h2>
                        <p class="fs-5 text-center">
                            <?= phrase('The announcement you requested was not found or its already been removed.'); ?>
                        </p>
                        <p class="text-center">
                            <a href="<?= current_page('../'); ?>" class="btn btn-outline-secondary rounded-pill px-5 --xhr">
                                <i class="mdi mdi-arrow-left"></i> <?= phrase('Back to Announcements'); ?>
                            </a>
                        </p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
