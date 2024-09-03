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
                                <a href="<?= go_to($user->username . '/guestbook', ['comment_highlight' => null]); ?>" class="nav-link active rounded-pill no-wrap --xhr">
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
                    <div class="alert alert-info callout mb-0">
                        <p class="lead mb-0">
                            <i class="mdi mdi-information-outline"></i> <?= phrase('Please be respectful to each other and be sure to follow our community standard.'); ?>
                        </p>
                    </div>
                    <?= comment_widget(['post_id' => $user->user_id, 'path' => service('uri')->getRoutePath()]); ?>
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
