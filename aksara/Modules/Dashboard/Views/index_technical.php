<div class="container-fluid py-3">
    <div class="row">
        <div class="col-6 col-lg-3 mb-3">
            <a href="<?= base_url('cms/blogs'); ?>" class="d-block --xhr" data-bs-toggle="tooltip" title="<?= phrase('Manage blog post'); ?>">
                <div class="card border-0 bg-primary text-center text-sm-start" style="overflow:hidden">
                    <div class="row align-items-center">
                        <div class="col-sm-4 col-xl-3" style="background:rgba(0, 0, 0, .1)">
                            <div class="p-3 text-center">
                                <i class="mdi mdi-newspaper mdi-2x text-light"></i>
                            </div>
                        </div>
                        <div class="col-sm-8 col-xl-9">
                            <h5 class="m-0 text-truncate text-light">
                                <?= phrase('Blogs'); ?>
                            </h5>
                            <p class="text-light mb-0">
                                <?= number_format($card->blogs) . ' ' . ($card->blogs > 2 ? phrase('articles') : phrase('article')); ?>
                            </p>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-6 col-lg-3 mb-3">
            <a href="<?= base_url('cms/pages'); ?>" class="d-block --xhr" data-bs-toggle="tooltip" title="<?= phrase('Manage frontend pages'); ?>">
                <div class="card border-0 bg-info text-center text-sm-start" style="overflow:hidden">
                    <div class="row align-items-center">
                        <div class="col-sm-4 col-xl-3" style="background:rgba(0, 0, 0, .1)">
                            <div class="p-3 text-center">
                                <i class="mdi mdi-file-multiple mdi-2x text-light"></i>
                            </div>
                        </div>
                        <div class="col-sm-8 col-xl-9">
                            <h5 class="m-0 text-truncate text-light">
                                <?= phrase('Pages'); ?>
                            </h5>
                            <p class="text-light mb-0">
                                <?= number_format($card->pages) . ' ' . ($card->pages > 2 ? phrase('pages') : phrase('page')); ?>
                            </p>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-6 col-lg-3 mb-3">
            <a href="<?= base_url('cms/galleries'); ?>" class="d-block --xhr" data-bs-toggle="tooltip" title="<?= phrase('Manage galleries'); ?>">
                <div class="card border-0 bg-danger text-center text-sm-start" style="overflow:hidden">
                    <div class="row align-items-center">
                        <div class="col-sm-4 col-xl-3" style="background:rgba(0, 0, 0, .1)">
                            <div class="p-3 text-center">
                                <i class="mdi mdi-folder-multiple-image mdi-2x text-light"></i>
                            </div>
                        </div>
                        <div class="col-sm-8 col-xl-9">
                            <h5 class="m-0 text-truncate text-light">
                                <?= phrase('Galleries'); ?>
                            </h5>
                            <p class="text-light mb-0">
                                <?= number_format($card->galleries) . ' ' . ($card->galleries > 2 ? phrase('albums') : phrase('album')); ?>
                            </p>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-6 col-lg-3 mb-3">
            <a href="<?= base_url('administrative/users'); ?>" class="d-block --xhr" data-bs-toggle="tooltip" title="<?= phrase('Manage users'); ?>">
                <div class="card border-0 bg-dark text-center text-sm-start" style="overflow:hidden">
                    <div class="row align-items-center">
                        <div class="col-sm-4 col-xl-3" style="background:rgba(0, 0, 0, .1)">
                            <div class="p-3 text-center">
                                <i class="mdi mdi-account-group-outline mdi-2x text-light"></i>
                            </div>
                        </div>
                        <div class="col-sm-8 col-xl-9">
                            <h5 class="m-0 text-truncate text-light">
                                <?= phrase('Users'); ?>
                            </h5>
                            <p class="text-light mb-0">
                                <?= number_format($card->users) . ' ' . ($card->users > 2 ? phrase('users') : phrase('user')); ?>
                            </p>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-8 bg-white">
            <div class="sticky-top">
                <div class="border rounded mb-3">
                    <div id="visitor-chart" class="rounded" style="width:100%; height:300px"></div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="card mb-3">
                            <div class="card-header bg-white border-0">
                                <h5 class="card-title mb-0">
                                    <?= phrase('Most used browser'); ?>
                                </h5>
                            </div>
                            <div class="card-body p-3">
                                <?php
                                    $num = 0;

                                    if (isset($visitors->browsers)) {
                                        foreach ($visitors->browsers as $key => $val) {
                                            echo '
                                                ' . ($num ? '<hr class="border-secondary mt-2 mb-2" />' : null) . '
                                                <div class="row no-gutters align-items-center">
                                                    <div class="col-3 col-sm-2">
                                                        <i class="mdi mdi-' . ($key == 'chrome' ? 'google-chrome text-success' : ($key == 'firefox' ? 'firefox text-warning' : ($key == 'safari' ? 'apple-safari text-primary' : ($key == 'edge' ? 'edge text-primary' : ($key == 'opera' ? 'opera text-danger' : ($key == 'explorer' ? 'internet-explorer text-info' : 'web text-muted')))))) . ' mdi-3x"></i>
                                                    </div>
                                                    <div class="col-9 col-sm-10 ps-3">
                                                        <b>
                                                            ' . ($key == 'chrome' ? 'Google Chrome' : ($key == 'firefox' ? 'Mozilla Firefox' : ($key == 'safari' ? 'Safari' : ($key == 'edge' ? 'Microsoft Edge' : ($key == 'opera' ? 'Opera' : ($key == 'explorer' ? 'Internet Explorer' : phrase('Unknown'))))))) . '
                                                        </b>
                                                        <p class="mb-0 text-sm text-muted">
                                                            ' . number_format($val) . ' ' . phrase('usage in a week') . '
                                                        </p>
                                                    </div>
                                                </div>
                                            ';
                                            $num++;
                                        }
                                    }
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card mb-3">
                            <div class="card-header bg-white border-0">
                                <h5 class="card-title mb-0">
                                    <?= phrase('Recent sign in'); ?>
                                </h5>
                            </div>
                            <div class="card-body p-3">
                                <?php
                                    foreach ($recent_signed as $key => $val) {
                                        echo '
                                            ' . ($key ? '<hr class="mt-2 mb-2" />' : null) . '
                                            <div class="row no-gutters align-items-center">
                                                <div class="col-3 col-sm-2">
                                                    <a href="' . base_url('user', ['user_id' => $val->user_id]) . '" target="_blank">
                                                        <img src="' . get_image('users', $val->photo, 'icon') . '" class="img-fluid rounded" />
                                                    </a>
                                                </div>
                                                <div class="col-9 col-sm-10 ps-3">
                                                    <a href="' . base_url('user', ['user_id' => $val->user_id]) . '" target="_blank">
                                                        <b>
                                                            ' . $val->first_name . ' ' . $val->last_name . '
                                                        </b>
                                                    </a>
                                                    <p class="mb-0 text-sm text-muted">
                                                        ' . $val->group_name . '
                                                    </p>
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
        </div>
        <div class="col-lg-4 bg-white">
            <div class="sticky-top">
                <?php if ($announcements): ?>
                    <div class="card mb-3">
                        <div class="card-header bg-white border-0">
                            <h5 class="card-title mb-0">
                                <?= phrase('Announcements'); ?>
                            </h5>
                        </div>
                        <?php
                            $announcement = null;

                            foreach ($announcements as $key => $val) {
                                $announcement .= '<li class="list-group-item"><a href="' . base_url('announcements/' . $val->announcement_slug) . '" class="fw-bold --xhr">' . $val->title . '</a></li>';
                            }

                            echo '
                                <ul class="list-group list-group-flush">
                                    ' . $announcement . '
                                </ul>
                            ';
                        ?>
                    </div>
                <?php endif; ?>
                
                <div class="card mb-3">
                    <div class="card-header bg-white border-0">
                        <h5 class="card-title mb-0">
                            <?= phrase('Application Information'); ?>
                        </h5>
                    </div>
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-6">
                                <div class="mb-3">
                                    <label class="d-block text-muted mb-0">
                                        AKSARA
                                    </label>
                                    <p>
                                        <?= aksara('version'); ?>
                                    </p>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="mb-3">
                                    <label class="d-block text-muted mb-0">
                                        <?= phrase('Build Version'); ?>
                                    </label>
                                    <p>
                                        <?= aksara('build_version'); ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="d-block text-muted mb-0">
                                <?= phrase('Last Modified'); ?>
                            </label>
                            <p>
                                <?= aksara('date_modified'); ?>
                            </p>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label class="d-block text-muted mb-0">
                                        <?= phrase('System Language'); ?>
                                    </label>
                                    <p>
                                        <?= (isset($system_language) ? $system_language : null); ?>
                                    </p>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label class="d-block text-muted mb-0">
                                        <?= phrase('Membership'); ?>
                                    </label>
                                    <p>
                                        <?= (get_setting('frontend_registration') ? '<span class="badge bg-success">' . phrase('enabled') . '</span>' : '<span class="badge bg-danger">' . phrase('Disabled') . '</span>'); ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label class="d-block text-muted mb-0">
                                        <?= phrase('Login Attempt'); ?>
                                    </label>
                                    <p>
                                        <?= (get_setting('login_attempt') ? '<span class="badge bg-success">' . get_setting('login_attempt') . ' ' . phrase('times') . '</span>' : '<span class="badge bg-danger">' . phrase('Disabled') . '</span>'); ?>
                                    </p>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label class="d-block text-muted mb-0">
                                        <?= phrase('Blocking Time'); ?>
                                    </label>
                                    <p>
                                        <?= (get_setting('blocking_time') ? '<span class="badge bg-success">' . get_setting('blocking_time') . ' ' . phrase('minutes') . '</span>' : '<span class="badge bg-danger">' . phrase('Disabled') . '</span>'); ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        require.js('<?= base_url('assets/highcharts/highcharts.min.js'); ?>', function() {
            Highcharts.chart('visitor-chart', {
                chart: {
                    type: 'areaspline'
                },
                title: {
                    text: '<b><?= phrase('Visitor Graph'); ?></b>'
                },
                legend: {
                    layout: 'vertical',
                    align: 'left',
                    verticalAlign: 'top',
                    x: 150,
                    y: 100,
                    floating: true,
                    borderWidth: 1,
                    borderRadius: 5
                },
                xAxis: {
                    categories: <?= (isset($visitors->categories) ? json_encode($visitors->categories) : '[]'); ?>,
                    plotBands: [{
                        from: 5.5,
                        to: 7.5,
                        color: 'rgba(68, 170, 213, .2)'
                    }]
                },
                yAxis: {
                    title: {
                        text: '<?= phrase('Visitor Total'); ?>'
                    },
                    allowDecimals: false
                },
                tooltip: {
                    shared: true,
                    valueSuffix: ' <?= phrase('Visits'); ?>'
                },
                plotOptions: {
                    areaspline: {
                        fillOpacity: .5
                    }
                },
                series: [{
                    name: '<?= phrase('Visitors'); ?>',
                    data: <?= (isset($visitors->visits) ? json_encode($visitors->visits) : '[]'); ?>
                }]
            })
        })
    })
</script>
