<?php

/**
 * @var mixed $results
 * @var mixed $meta
 * @var mixed $pagination
 * @var bool $isLogged
 * @var string $userFirstName
 * @var string $userLastName
 * @var string $userPhoto
 * @var mixed $captcha
 */
if ($results): ?>
    <section class="section-padding border-fade-bottom fade-in">
        <div class="container text-center text-md-start">
            <div class="row align-items-end">
                <div class="col-lg-7">
                    <h1 class="display-4 fw-bold">
                        <?= $meta->title ?>
                    </h1>
                    <p class="fs-5 text-muted mb-0">
                        <?= truncate($meta->description, 256) ?>
                    </p>
                </div>
                <div class="col-lg-5">
                    <form action="<?= go_to(null, ['page' => null]) ?>" method="GET">
                        <div class="d-flex g-3 rounded-pill border border-light-subtle p-1">
                            <div class="input-group ps-4">
                                <i class="mdi mdi-magnify mdi-2x text-muted"></i>
                                <input type="text" name="q" class="form-control form-control-lg fw-light border-0 bg-transparent" value="<?= htmlspecialchars(service('request')->getGet('q') ?? '') ?>" placeholder="<?= phrase(
  'Search testimonials...',
) ?>">
                                <button type="submit" class="btn btn-primary btn-lg fw-light rounded-pill px-4">
                                    <?= phrase('Search') ?> <i class="mdi mdi-arrow-right"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
<?php endif; ?>

<section class="section-padding">
    <div class="container">
        <div class="row">
            <!-- Left: Testimonial List -->
            <div class="col-lg-<?= $results ? '7' : '8 offset-lg-2' ?>">
                <main class="mb-4">
                    <?php if ($results): ?>
                        <?php foreach ($results as $key => $val): ?>
                            <div class="border border-hover rounded-5 p-3 mb-3 fade-in">
                                <div class="row align-items-end mb-4">
                                    <div class="col-3 col-md-3 pt-2 order-sm-<?= $key % 2 === 0 ? '0' : '1' ?>">
                                        <img src="<?= get_image('testimonials', $val->photo, 'thumb') ?>" class="img-fluid w-100 rounded-circle p-2 border">
                                    </div>
                                    <div class="col-9 col-md-9 order-sm-<?= $key % 2 === 0 ? '1' : '0' ?>">
                                        <?php if (isset($val->rating) && $val->rating > 0): ?>
                                            <div class="mb-2">
                                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                                    <i class="mdi mdi-star<?= $i <= $val->rating ? '' : '-outline' ?> text-warning fs-4"></i>
                                                <?php endfor; ?>
                                            </div>
                                        <?php endif; ?>
                                        <blockquote class="blockquote">

                                            <div class="fs-5 mb-4">
                                                <?= $val->testimonial_content ?>
                                            </div>
                                            <footer class="blockquote-footer">
                                                <?php if ($val->username): ?>
                                                <a href="<?= base_url('user/' . $val->username) ?>" class="--xhr">
                                                    <b class="text-primary"><?= $val->first_name . ' ' . $val->last_name ?></b>
                                                </a>
                                                <?php else: ?>
                                                    <b class="text-danger"><?= $val->first_name . ' ' . $val->last_name ?></b>
                                                <?php endif; ?>
                                                &middot;
                                                <?= format_date($val->created_at, 'long', true) ?>
                                            </footer>
                                        </blockquote>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>

                        <?= pagination($pagination) ?>
                    <?php else: ?>
                        <div class="row">
                            <div class="col-lg-10 offset-lg-1">
                                <?= view('templates/404', [...(array) $meta, 'searchLabel' => phrase('Search testimonials...')]) ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </main>
            </div>

            <!-- Right: Submit Testimonial Form -->
            <div class="col-lg-<?= $results ? '5' : '8 offset-lg-2' ?>">
                <aside class="sticky-lg-top" style="top: 1rem">
                    <?php if (service('request')->getGet('success')): ?>
                        <div class="card border-light-subtle border-hover rounded-5 fade-in">
                            <div class="card-body p-4">
                                <div class="text-center py-4">
                                    <i class="mdi mdi-check-circle-outline text-success" style="font-size: 4rem;"></i>
                                    <h3 class="mt-3"><?= phrase('Thank you!') ?></h3>
                                    <p class="text-muted"><?= phrase('Your testimonial was successfully submitted and will be reviewed by our team.') ?></p>
                                    <a href="<?= go_to(null, [
                                      'success' => null,
                                    ]) ?>" class="btn btn-outline-primary rounded-pill px-4 --xhr">
                                        <i class="mdi mdi-pencil-plus-outline"></i> <?= phrase('Submit Another') ?>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="card border-light-subtle border-hover rounded-5 fade-in">
                            <div class="card-body p-4">
                                <h3>
                                    <i class="mdi mdi-message-draw"></i> <?= phrase('Share Your Experience') ?>
                                </h3>
                                <p class="text-secondary fs-5 mb-4">
                                    <?= phrase('We would love to hear what you think about us.') ?>
                                </p>
                                <form action="<?= go_to('create') ?>" method="POST" enctype="multipart/form-data" class="--validate-form">
                                    <?php if (get_userdata('is_logged')): ?>
                                        <!-- Logged-in user: show avatar and name -->
                                        <div class="d-flex align-items-center mb-4 p-3 rounded-4 bg-body-tertiary border">
                                            <img src="<?= get_image('users', get_userdata('photo')) ?>" class="rounded-circle me-3" width="60" height="60" alt="avatar" style="object-fit: cover;" />
                                            <div>
                                                <strong class="d-block fs-5"><?= get_userdata('first_name') . ' ' . get_userdata('last_name') ?></strong>
                                                <small class="text-muted"><?= phrase('Posting as yourself') ?></small>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <!-- Guest: name and photo fields -->
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label class="form-label" for="first_name_input"><?= phrase('First Name') ?> <span class="text-danger">*</span></label>
                                                    <input type="text" name="first_name" id="first_name_input" class="form-control rounded-pill" placeholder="<?= phrase('Your first name') ?>" required />
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label class="form-label" for="last_name_input"><?= phrase('Last Name') ?></label>
                                                    <input type="text" name="last_name" id="last_name_input" class="form-control rounded-pill" placeholder="<?= phrase('Your last name') ?>" />
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                    <div class="form-group mb-3">
                                        <label for="testimonial_content_input" class="form-label"><?= phrase('Testimonial Content') ?> <span class="text-danger">*</span></label>
                                        <textarea name="testimonial_content" id="testimonial_content_input" class="form-control rounded-5" rows="1" placeholder="<?= phrase('Share your experience') ?>" required></textarea>
                                    </div>

                                    <!-- Star Rating -->
                                    <div class="form-group mb-3">
                                        <label class="form-label mb-0"><?= phrase('Rating') ?> <span class="text-danger">*</span></label>
                                        <div class="star-rating-input" id="starRatingInput">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <i class="mdi mdi-star-outline star-icon" data-value="<?= $i ?>" style="font-size: 2rem; cursor: pointer; transition: color .15s, transform .15s;"></i>
                                            <?php endfor; ?>
                                            <input type="hidden" name="rating" id="ratingValue" value="" />
                                        </div>
                                        <small class="text-muted" id="ratingLabel"><?= phrase('Click to rate') ?></small>
                                    </div>

                                    <?php if (empty($isLogged) && isset($captcha)): ?>
                                        <!-- Captcha for guests -->
                                        <div class="form-group mb-4">
                                            <div class="input-group">
                                                <span class="input-group-text bg-body-tertiary p-0 captcha-refresh rounded-pill rounded-end-0 overflow-hidden" style="cursor: pointer;" data-bs-toggle="tooltip" title="<?= phrase('Reload Captcha') ?>">
                                                    <?php if ($captcha->string) {
                                                      echo '<b class="text-body pe-3 ps-3">' . $captcha->string . '</b>';
                                                    } else {
                                                      echo '<img src="' . $captcha->image . '" class="img-fluid" alt="CAPTCHA" />';
                                                    } ?>
                                                </span>
                                                <input type="text" name="captcha" class="form-control rounded-pill rounded-start-0" id="captcha_input" placeholder="<?= phrase('Bot Challenge') ?>" maxlength="32" />
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <div class="d-grid">
                                        <button type="submit" class="btn btn-primary rounded-pill">
                                            <i class="mdi mdi-send"></i> <?= phrase('Submit Testimonial') ?>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    <?php endif; ?>
                </aside>
            </div>
        </div>
    </div>
</section>

<style>
    .star-rating-input .star-icon {
        color: var(--bs-secondary-color, #6c757d);
    }
    .star-rating-input .star-icon.active,
    .star-rating-input .star-icon.hover {
        color: #ffc107;
        transform: scale(1.15);
    }
    .star-rating-input .star-icon.active {
        transform: scale(1.1);
    }
</style>

<script>
    (function() {
        var container = document.getElementById('starRatingInput');
        if (! container) return;

        var stars = container.querySelectorAll('.star-icon');
        var input = document.getElementById('ratingValue');
        var label = document.getElementById('ratingLabel');
        var labels = [
            '',
            '<?= phrase('Poor') ?>',
            '<?= phrase('Fair') ?>',
            '<?= phrase('Good') ?>',
            '<?= phrase('Very Good') ?>',
            '<?= phrase('Excellent') ?>'
        ];

        function setStars(value, isHover) {
            stars.forEach(function(star) {
                var v = parseInt(star.getAttribute('data-value'));

                if (v <= value) {
                    star.classList.remove('mdi-star-outline');
                    star.classList.add('mdi-star');

                    if (isHover) {
                        star.classList.add('hover');
                    }
                } else {
                    star.classList.add('mdi-star-outline');
                    star.classList.remove('mdi-star');
                    star.classList.remove('hover');
                }
            });

            if (label && labels[value]) {
                label.textContent = labels[value];
            }
        }

        stars.forEach(function(star) {
            star.addEventListener('click', function() {
                var value = parseInt(this.getAttribute('data-value'));
                input.value = value;

                stars.forEach(function(s) {
                    s.classList.remove('active', 'hover');
                });

                setStars(value, false);

                for (var i = 0; i < value; i++) {
                    stars[i].classList.add('active');
                }
            });

            star.addEventListener('mouseenter', function() {
                var value = parseInt(this.getAttribute('data-value'));
                setStars(value, true);
            });

            star.addEventListener('mouseleave', function() {
                var current = parseInt(input.value) || 0;

                stars.forEach(function(s) {
                    s.classList.remove('hover');
                });

                setStars(current, false);

                if (current > 0 && label) {
                    label.textContent = labels[current];
                } else if (label) {
                    label.textContent = '<?= phrase('Click to rate') ?>';
                }
            });
        });
    })();
</script>
