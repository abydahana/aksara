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
        <div class="row">
            <div class="col-lg-8 offset-lg-2">
                <?php if ($results): ?>
                    <?php foreach ($results as $key => $val): ?>
                        <div class="mb-3">
                            <div class="row align-items-center mb-4">
                                <div class="col-3 col-md-3 pt-2 order-sm-<?= ($key %2 === 0 ? '0' : '1'); ?>">
                                    <img src="<?= get_image('testimonials', $val->photo, 'thumb'); ?>" class="img-fluid w-100 rounded-circle p-2 border">
                                </div>
                                <div class="col-9 col-md-9 order-sm-<?= ($key %2 === 0 ? '1' : '0'); ?>">
                                    <blockquote class="blockquote">
                                        <h2>
                                            <?= $val->testimonial_title; ?>
                                        </h2>
                                        <div class="lead mb-4">
                                            <?= $val->testimonial_content; ?>
                                        </div>
                                        <footer class="blockquote-footer">
                                            <b class="text-primary"><?= $val->first_name . ' ' . $val->last_name; ?></b>, <?= $val->timestamp; ?>
                                        </footer>
                                    </blockquote>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <?= pagination($pagination); ?>
                <?php else: ?>
                    <div class="py-5">
                        <div class="text-center">
                            <img src="<?= base_url('assets/yao-ming.png'); ?>" width="128" alt="404" />
                        </div>
                        <h2 class="text-center">
                            <?= phrase('No testimonial is found!'); ?>
                        </h2>
                        <p class="lead text-center">
                            <?= phrase('No testimonial is available at the moment.'); ?>
                        </p>
                        <p class="text-center">
                            <a href="<?= base_url(); ?>" class="btn btn-outline-dark rounded-pill px-5 --xhr">
                                <i class="mdi mdi-arrow-left"></i> <?= phrase('Back to Home'); ?>
                            </a>
                        </p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
