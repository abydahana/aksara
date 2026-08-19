<?php

/**
 * @var object $meta
 * @var mixed $results
 * @var mixed $pagination
 * @var mixed $user
 */
if ($user): ?>
    <section class="section-padding">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-6 offset-3 col-sm-4 offset-sm-4 col-lg-2 offset-lg-0 text-center">
                    <img src="<?= get_image('users', $user->photo, 'thumb') ?>" class="img-fluid rounded-circle" alt="<?= $user->first_name . ' ' . $user->last_name ?>" />
                </div>
                <div class="col-12 col-sm-12 col-lg-10">
                    <div class="text-center text-lg-start">
                        <h2 class="mb-0">
                            <?= $user->first_name ?> <?= $user->last_name ?>
                        </h2>
                        <p class="fs-5">
                            @<?= $user->username ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <div class="border-fade bg-body py-2">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 offset-lg-2">
                    <div class="overflow-x-auto">
                        <ul class="nav nav-pills nav-pills-dark flex-nowrap">
                            <li class="nav-item">
                                <a href="<?= go_to($user->username, [
                                  'limit' => null,
                                  'page' => null,
                                ]) ?>" class="nav-link rounded-pill no-wrap --xhr">
                                    <i class="mdi mdi-information-outline"></i> <?= phrase('About') ?>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= go_to($user->username . '/activities', [
                                  'limit' => null,
                                  'page' => null,
                                ]) ?>" class="nav-link active rounded-pill no-wrap --xhr">
                                    <i class="mdi mdi-account-clock-outline"></i> <?= phrase('Activities') ?>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= go_to($user->username . '/likes', [
                                  'limit' => null,
                                  'page' => null,
                                ]) ?>" class="nav-link rounded-pill no-wrap --xhr">
                                    <i class="mdi mdi-heart"></i> <?= phrase('Likes') ?>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= go_to($user->username . '/guestbook', [
                                  'comment_highlight' => null,
                                ]) ?>" class="nav-link rounded-pill no-wrap --xhr">
                                    <i class="mdi mdi-book"></i> <?= phrase('Guestbook') ?>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="py-3 fade-in">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 offset-lg-2">
                    <?php if (! $results): ?>
                        <div class="alert alert-warning callout rounded-4">
                            <h3 class="mb-0"><?= phrase('No Activities') ?></h3>
                            <hr />
                            <p class="fs-5 mb-0">
                                <i class="mdi mdi-information-outline"></i> <?= phrase('User activity will be shown here if they have made some interaction.') ?>
                            </p>
                        </div>
                    <?php endif; ?>

                    <?php foreach ($results as $key => $val): ?>
                        <?php
                        $metadata = fetch_metadata($val->post_path);

                        if (! $metadata || ! isset($metadata->title)) {
                            continue;
                        } ?>

                        <div class="activity-item mb-3">
                            <div class="border rounded-4 mb-4 p-4">
                                <div class="row g-0 align-items-center">
                                    <div class="col-2 col-lg-1 pe-3">
                                        <img src="<?= get_image('users', $user->photo, 'thumb') ?>" class="img-fluid rounded-circle" alt="<?= $user->first_name . ' ' . $user->last_name ?>" loading="lazy" decoding="async" />
                                    </div>
                                    <div class="col-10 col-lg-9">
                                        <h5 class="fw-bold d-inline mb-0"> <?= $user->first_name . ' ' . $user->last_name ?> </h4>
                                        <p class="text-muted mb-0">
                                            <?= time_ago($val->created_at) ?>
                                            &middot;
                                            <a href="<?= base_url($val->post_path, [
                                                'comment_highlight' => $val->comment_id,
                                            ]) ?>" target="_blank">
                                                <?= phrase('Commented') ?>
                                            </a>
                                        </p>
                                    </div>
                                </div>
                                <hr class="border-secondary-subtle" />
                                <blockquote class="blockquote">
                                    <p><?= truncate($val->comments, 160) ?></p>

                                    <?php if ($val->attachment): ?>
                                        <a href="<?= get_image('comment', $val->attachment) ?>" class="d-block" target="_blank">
                                            <img src="<?= get_image('comment', $val->attachment, 'icon') ?>" class="img-fluid rounded-4" alt="Attachment" />
                                        </a>
                                    <?php endif; ?>
                                </blockquote>

                                <?php if (isset($metadata->title)): ?>
                                    <div class="rounded-4 border p-3">
                                        <h5>
                                            <a href="<?= base_url($val->post_path, ['comment_highlight' => $val->comment_id]) ?>" class="text-body" target="_blank">
                                                <?= $metadata->title ?>
                                            </a>
                                        </h5>
                                        <p><?= $metadata->description ?></p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <?= pagination($pagination) ?>
                </div>
            </div>
        </div>
    </div>
<?php else: ?>
    <div class="py-3 fade-in">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 offset-lg-2">
                    <?= view('templates/404', [...(array) $meta, 'searchLabel' => phrase('Search users...')]) ?>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
