<?php

/**
 * @var mixed $results
 * @var mixed $meta
 */
$output = null;

if ($results) {
  foreach ($results as $key => $val) {
    $images = json_decode($val->gallery_images, true);
    $labels = explode(',', $val->gallery_tags);

    if (is_array($images) && sizeof($images) > 0) {
      foreach ($images as $src => $alt) {
        if (!$src) {
          continue;
        }

        $output .=
          '
                    <div class="col-sm-6 col-md-3">
                        <div class="rounded-5 border-hover mb-4">
                            <a href="' .
          current_page($src) .
          '" class="d-block --xhr">
                                <img src="' .
          get_image('galleries', $src, 'thumb') .
          '" class="rounded-5 w-100 fade-in" alt="' .
          $alt .
          '" loading="lazy" decoding="async" />
                            </a>
                        </div>
                    </div>
                ';
      }
    }
  }
}

if ($output): ?>
    <section class="section-padding border-fade-bottom fade-in">
        <div class="container text-center text-md-start">
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
                <?= $output ?>
            </div>
        </div>
    </section>
<?php else: ?>
    <section class="section-padding fade-in">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 offset-lg-3">
                    <?= view('templates/404', [...(array) $meta, 'searchAction' => go_to('../', ['page' => null]), 'searchLabel' => phrase('Search albums...')]) ?>
                </div>
            </div>
        </div>
    </section>
<?php endif; ?>
