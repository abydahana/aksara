<?php if (in_array(get_userdata('group_id'), [1]) && (! $permission->uploads || ! $permission->writable)): ?>
    <div class="alert alert-danger px-3 rounded-0 mb-0">
        <h5>
            <?= phrase('Notice'); ?>
        </h5>
        <?php if (! $permission->uploads): ?>
            <p class="mb-0">
                <b><?= str_replace('\\', '/', FCPATH . UPLOAD_PATH); ?></b> <?= phrase('is not writable.'); ?>
            </p>
        <?php endif; ?>
        <?php if (! $permission->uploads): ?>
            <p class="mb-0">
                <b><?= str_replace('\\', '/', WRITEPATH); ?></b> <?= phrase('is not writable.'); ?>
            </p>
        <?php endif; ?>
        <br />
        <a href="//github.com/abydahana/Aksara/issues/2" target="_blank"><b><?= phrase('Click here'); ?></b></a> <?= phrase('to get an advice to solve this problem.'); ?>
    </div>
<?php elseif (in_array(get_userdata('group_id'), array(1)) && is_dir(ROOTPATH . 'install')): ?>
    <div class="alert alert-warning px-3 rounded-0 mb-0">
        <h5>
            <?= phrase('Notice'); ?>
        </h5>
        <?= phrase('Please remove or rename the following directory to secure your application:'); ?>
        <code><?= str_replace('\\', '/', ROOTPATH); ?>install</code>
    </div>
<?php endif; ?>

<div class="updater-placeholder"></div>
<div class="container-fluid py-3">
    <div class="row" id="greeting-card">
        <div class="col-12">
            <div class="card mb-3">
                <div class="card-body p-3">
                    <h3>
                        <a href="javascript:void(0)" class="btn btn-close float-end" onclick="jExec($(this).closest('#greeting-card').slideUp())"></a>
                        <?= phrase('Welcome to'); ?> Aksara!
                    </h3>
                    <p class="text-muted">
                        <?= phrase('We have assembled some links to get you started.'); ?>
                    </p>
                    <div class="row">
                        <div class="col-md-4 mb-3 mb-md-0">
                            <h5 class="mb-3 mb-md-5">
                                <?= phrase('Getting started'); ?>
                            </h5>
                            <div class="text-center text-md-start">
                                <a href="<?= base_url('administrative/settings'); ?>" class="btn btn-info pe-5 ps-5 --xhr">
                                    <i class="mdi mdi-color-palette"></i>
                                    <?= phrase('Customize your app'); ?>
                                </a>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3 mb-md-0">
                            <h5 class="mb-3">
                                <?= phrase('Next step'); ?>
                            </h5>
                            <div class="row">
                                <div class="col-1">
                                    <i class="mdi mdi-plus"></i>
                                </div>
                                <div class="col-11">
                                    <a href="<?= base_url('cms/blogs'); ?>" class="text-primary --xhr">
                                        <?= phrase('Write a blog post'); ?>
                                    </a>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-1">
                                    <i class="mdi mdi-file"></i>
                                </div>
                                <div class="col-11">
                                    <a href="<?= base_url('cms/pages'); ?>" class="text-primary --xhr">
                                        <?= phrase('Manage the page'); ?>
                                    </a>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-1">
                                    <i class="mdi mdi-monitor-dashboard"></i>
                                </div>
                                <div class="col-11">
                                    <a href="<?= base_url(); ?>" class="text-primary" target="_blank">
                                        <?= phrase('View your site'); ?>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3 mb-md-0">
                            <h5 class="mb-3">
                                <?= phrase('More action'); ?>
                            </h5>
                            <div class="row">
                                <div class="col-1">
                                    <i class="mdi mdi-puzzle"></i>
                                </div>
                                <div class="col-11">
                                    <?= phrase('Manage'); ?> <a href="<?= base_url('addons'); ?>" class="text-primary --xhr"><?= phrase('Add-Ons'); ?></a> <?= phrase('or'); ?> <a href="<?= base_url('administrative/menus'); ?>" class="text-primary --xhr"><?= phrase('Menus'); ?></a>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-1">
                                    <i class="mdi mdi-comment-multiple-outline"></i>
                                </div>
                                <div class="col-11">
                                    <a href="<?= base_url('cms/comments'); ?>" class="text-primary --xhr">
                                        <?= phrase('Turn commenting on or off'); ?>
                                    </a>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-1">
                                    <i class="mdi mdi-information-outline"></i>
                                </div>
                                <div class="col-11">
                                    <a href="//aksaracms.com/pages/documentation" class="text-primary" target="_blank">
                                        <?= phrase('Learn more about'); ?> Aksara
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
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
                                    <?= phrase('Most used browsers'); ?>
                                </h5>
                            </div>
                            <div class="card-body p-3">
                                <?php
                                    $num = 0;

                                    if (isset($visitors->browsers)) {
                                        foreach ($visitors->browsers as $key => $val) {
                                            echo '
                                                ' . ($num ? '<hr class="border-secondary my-0" />' : null) . '
                                                <div class="row g-0 align-items-center">
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
                                        <?= (get_setting('frontend_registration') ? '<span class="badge bg-success">' . phrase('Enabled') . '</span>' : '<span class="badge bg-danger">' . phrase('Disabled') . '</span>'); ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label class="d-block text-muted mb-0">
                                        <?= phrase('Secure Login Attempt'); ?>
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
        if (typeof interval !== 'undefined') {
            clearInterval(interval)
        }
        
        var interval;
        
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
        }),
        
        $.ajax ({
            url: '<?= current_page(); ?>',
            method: 'POST',
            data: {
                request: 'fetch_information'
            },
            beforeSend: function() {
            }
        })
        .done(function(response) {
            if (response.update_available) {
                $('.updater-placeholder').html(
                    '<div class="alert alert-info rounded-0 border-0 p-3 mb-0">' +
                        '<h5>' +
                            '<?= phrase('Update Available'); ?>' +
                        '</h5>' +
                        '<p>' +
                            '<?= phrase('A newer version of Aksara is available.'); ?> ' +
                            '<?= phrase('Click the button below to update your core system directly.'); ?> ' +
                            '<?= phrase('Your created module and theme will not be overwritten.'); ?>' +
                        '</p>' +
                        '<hr />' +
                        '<a href="<?= base_url('administrative/updater'); ?>" class="btn btn-sm btn-success rounded-pill --xhr">' +
                            '<i class="mdi mdi-update"></i>' +
                            '<?= phrase('Update Now'); ?>' +
                        '</a>' +
                    '</div>'
                )
            }
            
            clearInterval(interval)
        })
    })
</script>
