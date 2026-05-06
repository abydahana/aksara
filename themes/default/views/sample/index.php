<?php
/**
 * @var mixed $meta
 */
?>

<div class="section-padding">
    <!-- Background Wavy Shape -->
    <svg class="position-absolute top-0 d-none d-md-block hero-blob" viewBox="0 0 948 458" fill="none">
        <path fill="currentColor" d="M179.493 278.507C88.0136 187.027 42.2737 141.287 21.1376 90.2621C-7.04587 22.2238 -7.04587 -54.2238 21.1376 -122.262C42.2737 -173.287 88.0136 -219.027 179.493 -310.507C270.973 -401.986 316.713 -447.726 367.738 -468.862C435.776 -497.046 512.224 -497.046 580.262 -468.862C631.287 -447.726 677.027 -401.986 768.507 -310.507C859.986 -219.027 905.726 -173.287 926.862 -122.262C955.046 -54.2238 955.046 22.2238 926.862 90.2621C905.726 141.287 859.986 187.027 768.507 278.507C677.027 369.986 631.287 415.726 580.262 436.862C512.224 465.046 435.776 465.046 367.738 436.862C316.713 415.726 270.973 369.986 179.493 278.507Z"/>
    </svg>

    <div class="container position-relative fade-in" style="z-index: 1;">
        <div class="row align-items-center">
            <div class="col-md-8 py-3">
                <h1 class="display-4 fw-bold text-dark mb-3">
                    <?= $meta->title; ?>
                </h1>
                <p class="lead text-muted mb-0">
                    <?= truncate($meta->description, 256); ?>
                </p>
            </div>
        </div>
    </div>
</div>
<div class="py-3 py-md-5 fade-in">
    <div class="container">
        <div class="row">
            <div class="col-sm-6 col-lg-4 col-xxl-3 mb-4">
                <div class="card h-100 p-3 rounded-4 border-light card-hover">
                    <div class="card-body text-center d-flex flex-column align-items-center">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-4" style="width: 80px; height: 80px;background-color:rgba(43, 102, 255, 0.03)">
                            <i class="mdi mdi-earth-box mdi-3x"></i>
                        </div>
                        <h5 class="fw-bold text-dark mb-2">
                            <?= phrase('ArcGIS Feature Server'); ?>
                        </h5>
                        <p class="text-muted small mb-4 lh-base">
                            <?= phrase('Visualize the ArcGIS REST'); ?>
                        </p>
                        <a href="<?= go_to('arcgis'); ?>" class="text-decoration-none fw-bold mt-auto --xhr">
                            <?= phrase('Show Example'); ?>
                            <i class="mdi mdi-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-4 col-xxl-3 mb-4">
                <div class="card h-100 p-3 rounded-4 border-light card-hover">
                    <div class="card-body text-center d-flex flex-column align-items-center">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-4" style="width: 80px; height: 80px;background-color:rgba(43, 102, 255, 0.03)">
                            <i class="mdi mdi-map-marker-plus mdi-3x"></i>
                        </div>
                        <h5 class="fw-bold text-dark mb-2">
                            <?= phrase('Web Map Server'); ?>
                        </h5>
                        <p class="text-muted small mb-4 lh-base">
                            <?= phrase('Visualize the WMS feature'); ?>
                        </p>
                        <a href="<?= go_to('wms'); ?>" class="text-decoration-none fw-bold mt-auto --xhr">
                            <?= phrase('Show Example'); ?>
                            <i class="mdi mdi-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-4 col-xxl-3 mb-4">
                <div class="card h-100 p-3 rounded-4 border-light card-hover">
                    <div class="card-body text-center d-flex flex-column align-items-center">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-4" style="width: 80px; height: 80px;background-color:rgba(43, 102, 255, 0.03)">
                            <i class="mdi mdi-flag-triangle mdi-3x"></i>
                        </div>
                        <h5 class="fw-bold text-dark mb-2">
                            <?= phrase('Load KML File'); ?>
                        </h5>
                        <p class="text-muted small mb-4 lh-base">
                            <?= phrase('Rendering KML file with vector source'); ?>
                        </p>
                        <a href="<?= go_to('kml'); ?>" class="text-decoration-none fw-bold mt-auto --xhr">
                            <?= phrase('Show Example'); ?>
                            <i class="mdi mdi-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-4 col-xxl-3 mb-4">
                <div class="card h-100 p-3 rounded-4 border-light card-hover">
                    <div class="card-body text-center d-flex flex-column align-items-center">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-4" style="width: 80px; height: 80px;background-color:rgba(43, 102, 255, 0.03)">
                            <i class="mdi mdi-earth mdi-3x"></i>
                        </div>
                        <h5 class="fw-bold text-dark mb-2">
                            <?= phrase('Load KMZ File'); ?>
                        </h5>
                        <p class="text-muted small mb-4 lh-base">
                            <?= phrase('Load the KMZ file source'); ?>
                        </p>
                        <a href="<?= go_to('kmz'); ?>" class="text-decoration-none fw-bold mt-auto --xhr">
                            <?= phrase('Show Example'); ?>
                            <i class="mdi mdi-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-4 col-xxl-3 mb-4">
                <div class="card h-100 p-3 rounded-4 border-light card-hover">
                    <div class="card-body text-center d-flex flex-column align-items-center">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-4" style="width: 80px; height: 80px;background-color:rgba(43, 102, 255, 0.03)">
                            <i class="mdi mdi-zip-box mdi-3x"></i>
                        </div>
                        <h5 class="fw-bold text-dark mb-2">
                            <?= phrase('Zipped Geospatial'); ?>
                        </h5>
                        <p class="text-muted small mb-4 lh-base">
                            <?= phrase('Load zipped geojson or KML'); ?>
                        </p>
                        <a href="<?= go_to('zip'); ?>" class="text-decoration-none fw-bold mt-auto --xhr">
                            <?= phrase('Show Example'); ?>
                            <i class="mdi mdi-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-4 col-xxl-3 mb-4">
                <div class="card h-100 p-3 rounded-4 border-light card-hover">
                    <div class="card-body text-center d-flex flex-column align-items-center">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-4" style="width: 80px; height: 80px;background-color:rgba(43, 102, 255, 0.03)">
                            <i class="mdi mdi-json mdi-3x"></i>
                        </div>
                        <h5 class="fw-bold text-dark mb-2">
                            <?= phrase('Geojson'); ?>
                        </h5>
                        <p class="text-muted small mb-4 lh-base">
                            <?= phrase('Example of geojson feature'); ?>
                        </p>
                        <a href="<?= go_to('json'); ?>" class="text-decoration-none fw-bold mt-auto --xhr">
                            <?= phrase('Show Example'); ?>
                            <i class="mdi mdi-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-4 col-xxl-3 mb-4">
                <div class="card h-100 p-3 rounded-4 border-light card-hover">
                    <div class="card-body text-center d-flex flex-column align-items-center">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-4" style="width: 80px; height: 80px;background-color:rgba(43, 102, 255, 0.03)">
                            <i class="mdi mdi-google-maps mdi-3x"></i>
                        </div>
                        <h5 class="fw-bold text-dark mb-2">
                            <?= phrase('Custom Tiles URL'); ?>
                        </h5>
                        <p class="text-muted small mb-4 lh-base">
                            <?= phrase('Use custom tiles from URL'); ?>
                        </p>
                        <a href="<?= go_to('tiles'); ?>" class="text-decoration-none fw-bold mt-auto --xhr">
                            <?= phrase('Show Example'); ?>
                            <i class="mdi mdi-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-4 col-xxl-3 mb-4">
                <div class="card h-100 p-3 rounded-4 border-light card-hover">
                    <div class="card-body text-center d-flex flex-column align-items-center">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-4" style="width: 80px; height: 80px;background-color:rgba(43, 102, 255, 0.03)">
                            <i class="mdi mdi-google-street-view mdi-3x"></i>
                        </div>
                        <h5 class="fw-bold text-dark mb-2">
                            <?= phrase('Google Street View'); ?>
                        </h5>
                        <p class="text-muted small mb-4 lh-base">
                            <?= phrase('Showing the Google Street View'); ?>
                        </p>
                        <a href="<?= go_to('panorama'); ?>" class="text-decoration-none fw-bold mt-auto --xhr">
                            <?= phrase('Show Example'); ?>
                            <i class="mdi mdi-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
