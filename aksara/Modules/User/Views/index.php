<?php
/**
 * @var mixed $results
 * @var mixed $meta
 */
$user = (isset($results[0]) ? $results[0] : []);

if ($user): ?>
    <section class="section-padding fade-in">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-6 offset-3 col-sm-4 offset-sm-4 col-lg-2 offset-lg-0 text-center">
                    <img src="<?= get_image('users', $user->photo, 'thumb'); ?>" class="img-fluid rounded-circle" alt="..." />
                </div>
                <div class="col-12 col-sm-12 col-lg-10">
                    <div class="text-center text-lg-start">
                        <h2 class="mb-0">
                            <?= $meta->title; ?>
                        </h2>
                        <p class="fs-5">
                            @<?= $user->username; ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <div class="border-fade bg-white py-2">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 offset-lg-2">
                    <div class="overflow-x-auto">
                        <ul class="nav nav-pills nav-pills-dark flex-nowrap">
                            <li class="nav-item">
                                <a href="<?= go_to($user->username, ['limit' => null, 'per_page' => null]); ?>" class="nav-link active rounded-pill no-wrap --xhr">
                                    <i class="mdi mdi-information-outline"></i> <?= phrase('About'); ?>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= go_to($user->username . '/activities', ['limit' => null, 'per_page' => null]); ?>" class="nav-link rounded-pill no-wrap --xhr">
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
    <div class="py-3 fade-in">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 offset-lg-2">
                    <div class="mb-3">
                        <h4 class="text-muted mb-0">
                            <?= phrase('Biography'); ?>
                        </h4>
                        <p class="fs-5">
                            <?= ($user->bio ? $user->bio : '-'); ?>
                        </p>
                    </div>
                    <div class="mb-3">
                        <h4 class="text-muted mb-0">
                            <?= phrase('Last Activity'); ?>
                        </h4>
                        <p class="fs-5">
                            <?= time_ago($user->last_activity); ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php else: ?>
    <div class="py-3 fade-in">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 offset-lg-2">
                    <div class="py-5">
                        <div class="text-center">
                            <img src="<?= base_url('assets/yao-ming.png'); ?>" width="128" alt="404" />
                        </div>
                        <h2 class="text-center">
                            <?= phrase('User not found!'); ?>
                        </h2>
                        <p class="fs-5 text-center">
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
