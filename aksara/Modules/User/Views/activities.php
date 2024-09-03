<?php if ($user): ?>
    <div class="py-3 py-md-5 bg-light">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-6 offset-3 col-sm-4 offset-sm-4 col-lg-2 offset-lg-0 text-center">
                    <img src="<?= get_image('users', $user->photo, 'thumb'); ?>" class="img-fluid rounded-circle" alt="..." />
                </div>
                <div class="col-12 col-sm-12 col-lg-10">
                    <div class="text-center text-lg-start">
                        <h2 class="mb-0">
                            <?= $user->first_name; ?> <?= $user->last_name; ?>
                        </h2>
                        <p class="lead">
                            @<?= $user->username; ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="border-top border-bottom border-light-subtle bg-white py-2">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 offset-lg-2">
                    <div class="overflow-x-auto">
                        <ul class="nav nav-pills nav-pills-dark flex-nowrap">
                            <li class="nav-item">
                                <a href="<?= go_to($user->username, ['limit' => null, 'per_page' => null]); ?>" class="nav-link rounded-pill no-wrap --xhr">
                                    <i class="mdi mdi-information-outline"></i> <?= phrase('About'); ?>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= go_to($user->username . '/activities', ['limit' => null, 'per_page' => null]); ?>" class="nav-link active rounded-pill no-wrap --xhr">
                                    <i class="mdi mdi-account-clock-outline"></i> <?= phrase('Activities'); ?>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= go_to($user->username . '/likes', ['limit' => null, 'per_page' => null]); ?>" class="nav-link rounded-pill no-wrap --xhr">
                                    <i class="mdi mdi-heart"></i> <?= phrase('Likes'); ?>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= go_to($user->username . '/guestbook', ['comment_highlight' => null]); ?>" class="nav-link rounded-pill no-wrap --xhr">
                                    <i class="mdi mdi-book"></i> <?= phrase('Guestbook'); ?>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="py-3">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 offset-lg-2">
                    <?php if (! $results): ?>
                        <div class="alert alert-warning callout">
                            <h3 class="mb-0"><?= phrase('No Activities'); ?></h3>
                            <hr />
                            <p class="lead mb-0">
                                <i class="mdi mdi-information-outline"></i> <?= phrase('User activity will be shown here if they have made some interaction.'); ?>
                            </p>
                        </div>
                    <?php endif; ?>
                    <?php foreach ($results as $key => $val): ?>
                        <?php
                            $metadata = fetch_metadata($val->post_path);
                        ?>
                        <div class="activity-item mb-3">
                            <div class="card rounded-4 border-light-subtle mb-3">
                                <div class="card-body">
                                    <div class="row g-0 align-items-center">
                                        <div class="col-2 col-lg-1 pe-3">
                                            <img src="<?= get_image('users', $user->photo, 'thumb'); ?>" class="img-fluid rounded-circle" alt="..." />
                                        </div>
                                        <div class="col-10 col-lg-9">
                                            <h5 class="fw-bold d-inline mb-0"> <?= $user->first_name . ' ' . $user->last_name; ?> </h5>
                                            <p class="text-muted mb-0">
                                                <?= time_ago($val->timestamp); ?> &middot; <a href="<?= base_url($val->post_path, ['comment_highlight' => $val->comment_id]); ?>" target="_blank"> <?= phrase('Commented'); ?> </a>
                                            </p>
                                        </div>
                                    </div>
                                    <p class="lead mb-0"><?= truncate($val->comments, 160); ?></p>
                                    <?php if ($val->attachment): ?>
                                        <a href="<?= get_image('comment', $val->attachment); ?>" target="_blank">
                                            <img src="<?= get_image('comment', $val->attachment, 'icon'); ?>" class="img-fluid rounded-4" alt="..." />
                                        </a>
                                    <?php endif; ?>
                                    <?php if (isset($metadata->title)): ?>
                                        <hr class="border-secondary-subtle" />
                                        <h4>
                                            <a href="<?= base_url($val->post_path, ['comment_highlight' => $val->comment_id]); ?>" class="text-dark" target="_blank">
                                                <?= $metadata->title; ?>
                                            </a>
                                        </h4>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <?= pagination($pagination); ?>
                </div>
            </div>
        </div>
    </div>
<?php else: ?>
    <div class="py-3 py-md-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 offset-lg-2">
                    <div class="py-5">
                        <div class="text-center">
                            <img src="<?= base_url('assets/yao-ming.png'); ?>" width="128" alt="404" />
                        </div>
                        <h2 class="text-center">
                            <?= phrase('User not found'); ?>
                        </h2>
                        <p class="lead text-center">
                            <?= phrase('The user you requested does not exists.'); ?>
                        </p>
                        <p class="text-center">
                            <a href="<?= base_url(); ?>" class="btn btn-outline-dark rounded-pill px-5 --xhr">
                                <i class="mdi mdi-arrow-left"></i> <?= phrase('Back to Home'); ?>
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
