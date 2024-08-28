<?php
    if (! $user) {
        $link_left = null;
        $link_right = null;
    
        if (isset($suggestions) && $suggestions) {
            foreach ($suggestions as $key => $val) {
                if (($key + 1) % 2 == 0) {
                    $link_right .= '
                        <li>
                            <a href="' . go_to($val->username) . '" class="--xhr">
                                ' . $val->first_name . ' ' . $val->last_name . '
                            </a>
                        </li>
                    ';
                } else {
                    $link_left .= '
                        <li>
                            <a href="' . go_to($val->username) . '" class="--xhr">
                                ' . $val->first_name . ' ' . $val->last_name . '
                            </a>
                        </li>
                    ';
                }
            }
        }
    }
?>
<?php if ($user): ?>
    <div class="py-3 py-md-5 bg-light">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-2 text-center">
                    <img src="<?= get_image('users', $user->photo, 'thumb'); ?>" class="img-fluid rounded-circle" alt="..." />
                </div>
                <div class="col-lg-10">
                    <div class="text-center text-lg-start">
                        <h2 class="mb-0">
                            <?= $user->first_name . ' ' . $user->last_name; ?>
                        </h2>
                        <p class="lead">
                            @<?= $user->username; ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="border-top border-bottom bg-white pt-1 pb-1">
        <div class="container">
            <div class="row">
                <div class="col-lg-10 offset-lg-2">
                    <ul class="nav nav-pills">
                        <li class="nav-item">
                            <a href="<?= go_to($user->username, ['limit' => null, 'per_page' => null]); ?>" class="nav-link rounded-pill no-wrap --xhr">
                                <i class="mdi mdi-information-outline"></i>
                                &nbsp;
                                <?= phrase('About'); ?>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= go_to($user->username . '/activity', ['limit' => null, 'per_page' => null]); ?>" class="nav-link rounded-pill no-wrap --xhr">
                                <i class="mdi mdi-account-clock-outline"></i>
                                &nbsp;
                                <?= phrase('Activity'); ?>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= go_to($user->username . '/likes', ['limit' => null, 'per_page' => null]); ?>" class="nav-link rounded-pill no-wrap --xhr active">
                                <i class="mdi mdi-heart"></i>
                                &nbsp;
                                <?= phrase('Likes'); ?>
                            </a>
                        </li>
                    </ul>
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
                            <h3 class="mb-0"><?= phrase('No Favorites'); ?></h3>
                            <hr />
                            <i class="mdi mdi-information-outline"></i> <?= phrase('User favorites will be shown here if they have made some upvote.'); ?>
                        </div>
                    <?php endif; ?>
                    <?php foreach ($results as $key => $val): ?>
                        <?php
                            $metadata = fetch_metadata($val->post_path);

                            if (! $metadata) continue;
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
                                            <span class="d-none d-sm-inline"> &middot; </span>
                                            <a href="<?= base_url($val->post_path); ?>" class="d-none d-sm-inline" target="_blank"> <?= phrase('Liked'); ?> </a>
                                            <p class="text-muted mb-0">
                                                <?= time_ago($val->timestamp); ?>
                                            </p>
                                        </div>
                                    </div>
                                    <hr class="border-secondary-subtle" />
                                    <h4>
                                        <a href="<?= base_url($val->post_path); ?>" class="text-dark" target="_blank">
                                            <?= $metadata->title; ?>
                                        </a>
                                        <p class="lead text-muted"><?= truncate($metadata->description, 160); ?></p>
                                    </h4>
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
            <div class="text-center py-5">
                <h1 class="text-muted">
                    404
                </h1>
                <i class="mdi mdi-dropbox mdi-5x text-muted"></i>
            </div>
            <div class="row mb-5">
                <div class="col-md-6 offset-md-3">
                    <h2 class="text-center">
                        <?= phrase('User not found'); ?>
                    </h2>
                    <p class="lead text-center mb-5">
                        <?= phrase('The user you requested does not exists.'); ?>
                    </p>
                    <div class="text-center mt-5">
                        <a href="<?= base_url(); ?>" class="btn btn-outline-primary rounded-pill --xhr">
                            <i class="mdi mdi-arrow-left"></i>
                            <?= phrase('Back to Homepage'); ?>
                        </a>
                    </div>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-md-10 offset-md-1">
                    <h5>
                        <i class="mdi mdi-lightbulb-on-outline"></i>
                        <?= phrase('Our suggestion'); ?>
                        <blink>_</blink>
                    </h5>
                </div>
            </div>
            <div class="row">
                <div class="col-md-5 offset-md-1">
                    <ul>
                        <?= $link_left; ?>
                    </ul>
                </div>
                <div class="col-md-5">
                    <ul>
                        <?= $link_right; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
