<?php

/**
 * @var object $meta
 * @var mixed $results
 */
$videoType = null;

if ($results && $results->fieldData) {
    if (stripos($results->fieldData->video_url->value, '/youtube.com') !== false) {
        $videoType = 'video/x-youtube';
    } else if (stripos($results->fieldData->video_url->value, 'facebook.com') !== false) {
        $videoType = 'video/facebook';
    } else if (stripos($results->fieldData->video_url->value, 'vimeo.com') !== false) {
        $videoType = 'video/vimeo';
    } else if (stripos($results->fieldData->video_url->value, 'dailymotion.com') !== false || stripos($results->fieldData->video_url->value, 'dai.ly') !== false) {
        $videoType = 'video/dailymotion';
    } else if (stripos($results->fieldData->video_url->value, 'twitch.tv') !== false) {
        $videoType = 'video/twitch';
    }
}

if ($results && $videoType): ?>
    <div class="row g-0">
        <div class="col-lg-8">
            <div class="sticky-top">
                <div class="full-height bg-body-tertiary p-3 d-flex align-items-center">
                    <video data-role="videoplayer" id="video" class="rounded-4">
                        <source src="<?= $results->fieldData->video_url->value; ?>" type="<?= $videoType; ?>">
                    </video>
                </div>
            </div>
        </div>
        <div class="col-lg-4 p-3 bg-body">
            <div class="sticky-top">
                <div class="row align-items-center mb-3">
                    <div class="col-2 pe-0">
                        <a href="<?= base_url('user/' . $results->fieldData->username->value); ?>" class="d-block --xhr">
                            <img src="<?= get_image('users', $results->fieldData->photo->value, 'thumb'); ?>" class="img-fluid rounded-circle" alt="<?= $results->fieldData->first_name->value . ' ' . $results->fieldData->last_name->value; ?>" loading="lazy" decoding="async" />
                        </a>
                    </div>
                    <div class="col-10">
                        <h2 class="h5 fw-bold mb-0">
                            <a href="<?= current_page('../'); ?>" class="float-end btn btn-close --xhr">&nbsp;</a>
                            <a href="<?= base_url('user/' . $results->fieldData->username->value); ?>" class="--xhr">
                                <?= $results->fieldData->first_name->value . ' ' . $results->fieldData->last_name->value; ?>
                            </a>
                        </h5>
                        <p class="mb-0">
                            <span class="text-muted" data-bs-toggle="tooltip" title="<?= $results->fieldData->created_at->value; ?>">
                                <?= time_ago($results->fieldData->created_at->value); ?>
                            </span>
                        </p>
                    </div>
                </div>
                <h3 class="h4">
                    <?= $results->fieldData->title->value; ?>
                </h3>
                <div>
                    <?= custom_nl2br($results->fieldData->description->value, 1); ?>
                </div>
                <div>
                    <?= comment_widget(['post_id' => $results->fieldData->id->value, 'path' => service('uri')->getRoutePath()]); ?>
                </div>
            </div>
        </div>
    </div>
<?php else: ?>
    <div class="section-padding">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 offset-lg-3">
                    <?= view('templates/404', [...(array) $meta, 'searchLabel' => phrase('Search videos...')]); ?>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
