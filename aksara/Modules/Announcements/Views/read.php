<?php

/**
 * @var mixed $results
 * @var mixed $meta
 */

$fieldData = $results->field_data; ?>

<section class="section-padding border-fade-bottom fade-in">
    <div class="container position-relative text-center text-md-start">
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
            <div class="col-lg-8 offset-lg-2">
                <?php if ($fieldData): ?>
                    <?php if ($fieldData->cover->value && 'placeholder.png' != $fieldData->cover->value): ?>
                        <img src="<?= get_image('announcements', $fieldData->cover->value) ?>" class="img-fluid rounded-4 mb-3 fade-in" alt="<?= $fieldData->title->value ?? phrase('Announcement') ?>" />
                    <?php endif; ?>
                    <div class="fs-5 fade-in">
                        <?= $fieldData->content->value ?>
                    </div>
                    <p class="text-muted fade-in">
                        <em>
                            <?= phrase('This announcement will be effective until {{end_date}}.', [
                              'end_date' => $fieldData->end_date->value,
                            ]) ?>
                        </em>
                    </p>
                    <a href="<?= current_page('../') ?>" class="btn btn-outline-primary rounded-pill px-5 fade-in --xhr">
                        <i class="mdi mdi-arrow-left"></i> <?= phrase('Back') ?>
                    </a>
                <?php else: ?>
                    <?= view('templates/404', [...(array) $meta, 'searchAction' => go_to('../', ['page' => null]), 'searchLabel' => phrase('Search announcements...')]) ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
