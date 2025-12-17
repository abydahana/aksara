<?php if ($results): ?>
    <div class="bg-light">
        <div class="py-3 py-md-5">
            <div class="container">
                <h1 class="text-center">
                    <?= $meta->title; ?>
                </h1>
                <p class="lead text-center">
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
        <div class="row">
            <div class="col-lg-8 offset-lg-2">
                <?php if ($results): ?>
                    <?php foreach($results as $key => $val): ?>
                        <?php if ($val->cover && 'placeholder.png' != $val->cover): ?>
                            <img src="<?= get_image('announcements', $val->cover); ?>" class="img-fluid rounded-4 mb-3" alt="..." />
                        <?php endif; ?>
                        <div class="lead">
                            <?= $val->content; ?>
                        </div>
                        <p class="text-muted">
                            <em>
                                <?= phrase('This announcement will be effective until {{end_date}}.', ['end_date' => $val->end_date]); ?>
                            </em>
                        </p>
                        <a href="<?= current_page('../'); ?>" class="btn btn-outline-primary rounded-pill px-5 --xhr">
                            <i class="mdi mdi-arrow-left"></i>
                            <?= phrase('Back'); ?>
                        </a>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="py-5">
                        <div class="text-center">
                            <img src="<?= base_url('assets/yao-ming.png'); ?>" width="128" alt="404" />
                        </div>
                        <h2 class="text-center">
                            <?= phrase('No announcement is found!'); ?>
                        </h2>
                        <p class="lead text-center">
                            <?= phrase('The announcement you requested was not found or its already been removed.'); ?>
                        </p>
                        <p class="text-center">
                            <a href="<?= current_page('../'); ?>" class="btn btn-outline-dark rounded-pill px-5 --xhr">
                                <i class="mdi mdi-arrow-left"></i> <?= phrase('Back to Announcements'); ?>
                            </a>
                        </p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
