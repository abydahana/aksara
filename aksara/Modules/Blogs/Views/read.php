<div class="py-3 py-md-5 bg-light background-clip" style="background:url(<?= get_image('blogs', (isset($results[0]->featured_image) ? $results[0]->featured_image : 'placeholder.png')); ?>) center center no-repeat; background-size:cover">
    <div class="container py-lg-5">
        <h1 class="text-center text-md-start text-light">
            <?= $meta->title; ?>
        </h1>
        <p class="lead text-center text-md-start  text-light mb-5">
            <?= $meta->description; ?>
        </p>
    </div>
</div>
<div class="py-3">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <?php
                    $output = null;

                    if ($results) {
                        foreach ($results as $key => $val) {
                            $item_tags = explode(',', $val->post_tags);
                            $tags = null;

                            if (sizeof($item_tags) > 0) {
                                foreach ($item_tags as $label => $badge) {
                                    if (! $badge) {
                                        continue;
                                    }

                                    $tags .= '
                                        <a href="' . go_to('../tags', ['q' => trim($badge)]) . '" class="--xhr">
                                            <span class="badge bg-secondary">
                                                ' . trim($badge) . '
                                            </span>
                                        </a>
                                    ';
                                }
                            }

                            $output .= '
                                <div class="text-break-word">
                                    ' . str_replace('MsoNormalTable', 'table table-bordered', preg_replace('/(width|height)="\d*"\s/', '', preg_replace('~<p[^>]*>~', '<p class="text-justify article text-break-word">', preg_replace('/(<[^>]+) style=".*?"/i', '$1', $val->post_content)))) . '
                                </div>
                                <p class="mb-5">
                                    ' . $tags . '
                                </p>
                            ';
                        }
                    }

                    if ($output) {
                        // Showing author and share button
                        echo '
                            <div class="row">
                                <div class="col-sm-6 col-md-8 mb-3">
                                    <div class="row g-0 align-items-center">
                                        <div class="col-2 col-sm-1">
                                            <a href="' . base_url('user/' . $results[0]->username) . '" class="--xhr">
                                                <img src="' . get_image('users', $results[0]->photo, 'thumb') . '" class="img-fluid rounded-circle" />
                                            </a>
                                        </div>
                                        <div class="col-10 col-sm-11 ps-3">
                                            <a href="' . base_url('user/' . $results[0]->username) . '" class="--xhr">
                                                <h6 class="fw-bold mb-0">
                                                    ' . $results[0]->first_name . ' ' . $results[0]->last_name . '
                                                </h6>
                                            </a>
                                            <p class="mb-0">
                                                <small class="text-muted" data-bs-toggle="tooltip" title="' . $results[0]->updated_timestamp . '">
                                                    ' . time_ago($results[0]->updated_timestamp) . '
                                                </small>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-md-4 mb-3">
                                    <div class="btn-group btn-group-sm d-flex rounded-pill overflow-hidden">
                                        <a href="//www.facebook.com/sharer/sharer.php?u=' . current_page() . '" class="btn btn-primary" data-bs-toggle="tooltip" title="' . phrase('Share to Facebook') . '" target="_blank">
                                            <i class="mdi mdi-facebook"></i>
                                        </a>
                                        <a href="//www.twitter.com/share?url=' . current_page() . '" class="btn btn-info text-light" data-bs-toggle="tooltip" title="' . phrase('Share to Twitter') . '" target="_blank">
                                            <i class="mdi mdi-twitter"></i>
                                        </a>
                                        <a href="//wa.me/?text=' . current_page() . '" class="btn btn-success" data-bs-toggle="tooltip" title="' . phrase('Send to WhatsApp') . '" target="_blank">
                                            <i class="mdi mdi-whatsapp"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        ';

                        if ($results[0]->featured_image && $results[0]->featured_image != 'placeholder.png') {
                            // Show featured image
                            echo '<a href="' . get_image('blogs', $results[0]->featured_image) . '" target="_blank"><img id="og-image" src="' . get_image('blogs', $results[0]->featured_image) . '" class="img-fluid rounded d-none" width="100%" /></a>';
                        }

                        // Show post content
                        echo preg_replace('/<img src="(.*?)"/i', '<img id="og-image" src="$1" class="img-fluid rounded"', $output);

                        // Show comment widget
                        echo comment_widget(['post_id' => $results[0]->post_id, 'path' => service('uri')->getRoutePath()]);
                    } else {
                        echo '
                            <div class="alert alert-warning">
                                <i class="mdi mdi-alert-outline"></i>
                                ' . phrase('The post you requested does not exist or already been archived') . '
                            </div>
                        ';
                    }
                ?>
            </div>
            <div class="col-lg-4 py-5">
                <div class="sticky-top">
                    <?php
                        if ($similar) {
                            $similar_article = null;

                            foreach ($similar as $key => $val) {
                                $similar_article .= '
                                    <div class="card border-light shadow-sm mb-3">
                                        <div class="card-body">
                                            <div class="row g-0 align-items-center">
                                                <div class="col-2">
                                                    <a href="' . go_to('../' . $val->category_slug . '/' . $val->post_slug) . '" class="--xhr">
                                                        <img src="' . get_image('blogs', $val->featured_image, 'icon') . '" class="img-fluid rounded" />
                                                    </a>
                                                </div>
                                                <div class="col-10 ps-3">
                                                    <a href="' . go_to('../' . $val->category_slug . '/' . $val->post_slug) . '" class="text-dark --xhr">
                                                        <b>
                                                            ' . $val->post_title . '
                                                        </b>
                                                    </a>
                                                    <p class="text-muted mb-0 text-sm">
                                                        ' . truncate($val->post_excerpt, 60) . '
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                ';
                            }

                            echo '
                                <div class="mb-5">
                                    <h5 class="mb-3">
                                        ' . phrase('Similar Articles') . '
                                    </h5>
                                    ' . $similar_article . '
                                </div>
                            ';
                        }

                        if ($categories) {
                            $similar_category = null;

                            foreach ($categories as $key => $val) {
                                $similar_category .= '
                                    ' . ($key ? '<hr class="border-secondary" />' : null) . '
                                    <div class="row g-0 align-items-center">
                                        <div class="col-2">
                                            <a href="' . go_to('../' . $val->category_slug) . '" class="--xhr">
                                                <img src="' . get_image('blogs', $val->category_image, 'icon') . '" class="img-fluid rounded" />
                                            </a>
                                        </div>
                                        <div class="col-10 ps-3">
                                            <a href="' . go_to('../' . $val->category_slug) . '" class="--xhr">
                                                <b class="text-dark  mb-0">
                                                    ' . $val->category_title . '
                                                </b>
                                                <p class="mb-0">
                                                    <small class="text-muted">
                                                        ' . number_format($val->total_data) . ' ' . ($val->total_data > 1 ? phrase('articles') : phrase('article')) . '
                                                    </small>
                                                </p>
                                            </a>
                                        </div>
                                    </div>
                                ';
                            }

                            echo '
                                <div class="mb-5">
                                    <h5 class="mb-3">
                                        ' . phrase('Similar Categories') . '
                                    </h5>
                                    <div class="card border-light shadow-sm mb-3">
                                        <div class="card-body">
                                            ' . $similar_category . '
                                        </div>
                                    </div>
                                </div>
                            ';
                        }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
