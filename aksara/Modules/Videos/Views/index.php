<?php if ($results): ?>
<div class="bg-light">
    <div class="py-3 py-md-5">
        <div class="container">
            <h1 class="text-center text-md-start">
                <?= $meta->title; ?>
            </h1>
            <p class="lead text-center text-md-start">
                <?= truncate($meta->description, 256); ?>
            </p>
        </div>
    </div>
    <svg class="wave text-white" version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 100" preserveAspectRatio="none">
        <path class="wavePath" d="M826.337463,25.5396311 C670.970254,58.655965 603.696181,68.7870267 447.802481,35.1443383 C293.342778,1.81111414 137.33377,1.81111414 0,1.81111414 L0,150 L1920,150 L1920,1.81111414 C1739.53523,-16.6853983 1679.86404,73.1607868 1389.7826,37.4859505 C1099.70117,1.81111414 981.704672,-7.57670281 826.337463,25.5396311 Z" fill="currentColor"></path>
    </svg>
</div>
<?php endif; ?>
<div class="py-3 py-md-5">
    <div class="container">
        <?php if ($results): ?>
            <div class="row">
                <?php foreach ($results as $key => $val): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="mb-3">
                            <a href="<?= base_url('videos/' . $val->slug); ?>" class="--xhr">
                                <img src="<?= get_image('videos', $val->cover, 'thumb'); ?>" class="w-100 rounded-4 mb-3" style="max-height:240px;object-fit: cover" />
                            </a>
                            <div class="row g-0">
                                <div class="col-2">
                                    <a href="<?= base_url('user/' . $val->username); ?>" class="text-sm text-secondary --xhr">
                                        <img src="<?= get_image('users', $val->photo, 'icon'); ?>" class="img-fluid rounded-circle" alt="..." />
                                    </a>
                                </div>
                                <div class="col-10 ps-2">
                                    <h5 class="mb-0">
                                        <a href="<?= base_url('videos/' . $val->slug); ?>" class="--xhr" data-bs-toggle="tooltip" title="<?= $val->title; ?>">
                                            <?= truncate($val->title, 60); ?>
                                        </a>
                                    </h5>
                                    <p class="mb-0">
                                        <a href="<?= base_url('user/' . $val->username); ?>" class="text-dark --xhr">
                                            <b>
                                                <?= $val->first_name . ' ' . $val->last_name; ?>
                                            </b>
                                        </a>
                                    </p>
                                    <p class="mb-0 text-muted">
                                        <?= time_ago($val->timestamp); ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <?= pagination($pagination); ?>
        <?php else: ?>
            <div class="row">
                <div class="col-lg-8 offset-lg-2">
                    <div class="py-5">
                        <div class="text-center">
                            <img src="<?= base_url('assets/yao-ming.png'); ?>" width="128" alt="404" />
                        </div>
                        <h2 class="text-center">
                            <?= phrase('No video is found!'); ?>
                        </h2>
                        <p class="lead text-center">
                            <?= phrase('No video is available at the moment.'); ?>
                        </p>
                        <p class="text-center">
                            <a href="<?= base_url(); ?>" class="btn btn-outline-dark rounded-pill px-5 --xhr">
                                <i class="mdi mdi-arrow-left"></i> <?= phrase('Back to Home'); ?>
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
