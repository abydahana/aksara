<?php if (isset($results[0])): ?>
    <?php
        $page = $results[0];
        $carousel = ($page->carousel_content ? json_decode($page->carousel_content) : null);
        $accordion = ($page->faq_content ? json_decode($page->faq_content) : null);

        if ($carousel) {
            $navigation = null;
            $carouselItems = null;

            foreach ($carousel as $key => $val) {
                $navigation .= '<button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="' . $key . '"' . ($key == 0 ? ' class="active"' : '') . '></button>';
                $carouselItems .= '
                    <div class="carousel-item' . ($key == 0 || sizeof((array) $carousel) == 1 ? ' active' : '') . '" >
                        <img src="' . get_image('carousels', (isset($val->background) ? $val->background : 'placeholder.png')) . '" alt="..." class="d-block w-100" style="max-height:640px;object-fit: cover" />
                        <div class="clip gradient-top"></div>
                        <div class="carousel-caption">
                            <h2 class="fw-bold mb-3 text-light">
                                ' . (isset($val->title) ? $val->title : phrase('Untitled')) . '
                            </h2>
                            <p class="text-light mb-5">
                                ' . (isset($val->description) ? truncate($val->description, 260) : phrase('Description was not set')) . '
                            </p>
                            ' . (isset($val->link) && $val->link ? '
                            <a href="' . $val->link . '" class="btn btn-sm btn-outline-light btn-lg rounded-pill px-5">
                                ' . (isset($val->label) && $val->label ? $val->label : phrase('Read More')) . '
                                <i class="mdi mdi-chevron-right"></i>
                            </a>
                            ' : null) . '
                        </div>
                    </div>
                ';
            }

            echo '
                <div id="carouselExampleIndicators" class="carousel slide" data-bs-ride="carousel">
                    ' . (sizeof((array) $carousel) > 1 ? '
                    <div class="carousel-indicators">
                        ' . $navigation . '
                    </div>
                    ' : '') . '
                    <div class="carousel-inner">
                        ' . $carouselItems . '
                    </div>
                    ' . (sizeof((array) $carousel) > 1 ? '
                    <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    </a>
                    <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    </a>
                    ' : '') . '
                </div>
            ';
        }

        if ($accordion) {
            $output = null;

            foreach ($accordion as $key => $val) {
                if (! isset($val->title) || ! $val->body) {
                    continue;
                }

                $output .= '
                    <div class="accordion-item">
                        <div class="accordion-header" id="heading_' . $key . '">
                            <button type="button" class="accordion-button' . ($key ? ' collapsed' : null) . '" data-bs-toggle="collapse" data-bs-target="#collapse_' . $key . '" aria-expanded="' . (! $key ? 'true' : 'false') . '" aria-controls="collapse_' . $key . '">
                                ' . $val->title . '
                            </a>
                        </div>
                        <div id="collapse_' . $key . '" class="collapse' . (! $key ? ' show' : null) . '" aria-labelledby="heading_' . $key . '" data-bs-parent="#accordionExample">
                            <div class="accordion-body">
                                ' . $val->body . '
                            </div>
                        </div>
                    </div>
                ';
            }

            $accordion = '
                <div class="accordion" id="accordionExample">
                    ' . $output . '
                </div>
            ';
        }
    ?>

    <div class="bg-light">
        <div class="py-3 py-md-5">
            <div class="container">
                <div class="text-center text-sm-start">
                    <h3 class="mb-0">
                        <?= $meta->title; ?>
                    </h3>
                    <p class="lead mb-0">
                        <?= truncate($meta->description, 256); ?>
                    </p>
                </div>
            </div>
        </div>
        <svg class="wave text-white" version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 100" preserveAspectRatio="none">
            <path class="wavePath" d="M826.337463,25.5396311 C670.970254,58.655965 603.696181,68.7870267 447.802481,35.1443383 C293.342778,1.81111414 137.33377,1.81111414 0,1.81111414 L0,150 L1920,150 L1920,1.81111414 C1739.53523,-16.6853983 1679.86404,73.1607868 1389.7826,37.4859505 C1099.70117,1.81111414 981.704672,-7.57670281 826.337463,25.5396311 Z" fill="currentColor"></path>
        </svg>
    </div>
    <div class="container py-3">
        <div class="text-justify mb-3">
            <?= preg_replace('/(<[^>]+) style=".*?"/i', '$1', preg_replace('/<img src="(.*?)"/i', '<img id="og-image" src="$1" class="img-fluid rounded"', $page->page_content)); ?>
        </div>
        <div class="mb-3">
            <?= $accordion; ?>
        </div>
        <p>
            <i class="text-muted text-sm">
                <?= ($page->updated_timestamp ? phrase('Updated at') . ' ' . phrase(date('l', strtotime($page->updated_timestamp))) . ', ' . $page->updated_timestamp : phrase('Created at') . ' ' . phrase(date('l', strtotime($page->created_timestamp))) . ', ' . $page->created_timestamp); ?>
            </i>
        </p>
    </div>
<?php else: ?>
    <div class="py-3 py-md-5 container">
        <div class="text-center py-5">
            <h1 class="text-muted">
                404
            </h1>
            <i class="mdi mdi-dropbox mdi-5x text-muted"></i>
        </div>
        <div class="row mb-5">
            <div class="col-md-6 offset-md-3">
                <h2 class="text-center">
                    <?= phrase('Page not found!'); ?>
                </h2>
                <p class="lead text-center mb-5">
                    <?= phrase('The page you requested does not exist or already been archived.'); ?>
                </p>
                <div class="text-center mt-5">
                    <a href="<?= base_url(); ?>" class="btn btn-sm btn-outline-primary rounded-pill px-lg-5 --xhr">
                        <i class="mdi mdi-arrow-left"></i>
                        <?= phrase('Back to Homepage'); ?>
                    </a>
                </div>
            </div>
        </div>
        <?php if (isset($suggestions) && $suggestions): ?>
            <div class="row mb-2">
                <div class="col-md-10 offset-md-1">
                    <h5>
                        <?= phrase('Our Suggestions'); ?>
                        <blink>_</blink>
                    </h5>
                    <?php foreach ($suggestions as $index => $page): ?>
                        <?php if ($index): ?> &middot; <?php endif; ?>
                        <a href="<?= base_url('pages/' . $page->page_slug); ?>">
                            <?= $page->page_title; ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?>
