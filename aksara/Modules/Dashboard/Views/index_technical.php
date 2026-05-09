<?php
    /**
     * @var mixed $card
     * @var mixed $visitors
     * @var mixed $recent_signed
     * @var mixed $announcements
     * @var mixed $system_language
     * @var mixed $group_name
     * @var mixed $logs
     */
    $logs = (isset($logs) ? $logs : []);
?>
<div class="container-fluid py-3">
    <div class="row mb-3">
        <div class="col-lg-8">
            <div class="card overflow-hidden rounded-4 mb-3">
                <div class="card-body p-4">
                    <h5 class="mb-1">
                        <?= phrase('Welcome back'); ?>, <?= get_userdata('first_name') . ' ' . get_userdata('last_name'); ?>!
                    </h5>
                    <p class="text-muted mb-0">
                        <?= phrase('You are signed in as {{ group_name }}.', ['group_name' => '<b>' . $group_name . '</b>']); ?>
                    </p>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card overflow-hidden rounded-4 mb-3">
                <div class="card-body p-4 text-center">
                    <div class="text-muted small text-uppercase fw-bold mb-1">
                        <?= phrase('Today'); ?>
                    </div>
                    <div class="h5 mb-0 text-primary fw-bold">
                        <?= phrase(date('l')) . ', ' . date('d') . ' ' . phrase(date('F')) . ' ' . date('Y'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-6 col-lg-3 mb-3">
            <a href="<?= base_url('cms/blogs'); ?>" class="d-block --xhr" data-bs-toggle="tooltip" title="<?= phrase('Manage blog post'); ?>">
                <div class="card border-0 rounded-4 bg-primary text-center text-sm-start mb-3" style="overflow:hidden">
                    <div class="row align-items-center">
                        <div class="col-sm-4 col-xl-3">
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
                <div class="card border-0 rounded-4 bg-info text-center text-sm-start mb-3" style="overflow:hidden">
                    <div class="row align-items-center">
                        <div class="col-sm-4 col-xl-3">
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
                <div class="card border-0 rounded-4 bg-success text-center text-sm-start mb-3" style="overflow:hidden">
                    <div class="row align-items-center">
                        <div class="col-sm-4 col-xl-3">
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
            <a href="<?= base_url('cms/videos'); ?>" class="d-block --xhr" data-bs-toggle="tooltip" title="<?= phrase('Manage videos'); ?>">
                <div class="card border-0 rounded-4 bg-danger text-center text-sm-start mb-3" style="overflow:hidden">
                    <div class="row align-items-center">
                        <div class="col-sm-4 col-xl-3">
                            <div class="p-3 text-center">
                                <i class="mdi mdi-youtube mdi-2x text-light"></i>
                            </div>
                        </div>
                        <div class="col-sm-8 col-xl-9">
                            <h5 class="m-0 text-truncate text-light">
                                <?= phrase('Videos'); ?>
                            </h5>
                            <p class="text-light mb-0">
                                <?= number_format($card->videos) . ' ' . ($card->videos > 2 ? phrase('videos') : phrase('video')); ?>
                            </p>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-8">
            <div class="sticky-top" style="top:6rem">
                <div class="border rounded-4 overflow-hidden mb-3">
                    <div id="visitor-chart" class="rounded-4" style="width:100%; height:300px"></div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="card overflow-hidden rounded-4 mb-3">
                            <div class="card-header border-0 p-4">
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
                        <div class="card overflow-hidden rounded-4 mb-3">
                            <div class="card-header border-0 p-4">
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
                <div class="card overflow-hidden rounded-4 mb-3">
                    <div class="card-header border-0 p-4">
                        <h5 class="card-title mb-0">
                            <?= phrase('Recent Activities'); ?>
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th class="border-0 px-4 text-muted small text-uppercase"><?= phrase('Date'); ?></th>
                                        <th class="border-0 px-4 text-muted small text-uppercase"><?= phrase('Platform'); ?></th>
                                        <th class="border-0 px-4 text-muted small text-uppercase"><?= phrase('Browser'); ?></th>
                                        <th class="border-0 px-4 text-muted small text-uppercase"><?= phrase('IP Address'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($logs): ?>
                                        <?php foreach ($logs as $log): ?>
                                            <tr>
                                                <td class="px-4 align-middle">
                                                    <span class="text-sm"><?= date('d/m/Y H:i', strtotime($log->timestamp)); ?></span>
                                                </td>
                                                <td class="px-4 align-middle">
                                                    <span class="badge bg-white text-dark rounded-pill border"><?= $log->platform; ?></span>
                                                </td>
                                                <td class="px-4 align-middle">
                                                    <span class="text-sm"><?= $log->browser; ?></span>
                                                </td>
                                                <td class="px-4 align-middle">
                                                    <code class="text-sm text-primary"><?= $log->ip_address; ?></code>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="4" class="text-center p-4 text-muted">
                                                <?= phrase('No activity recorded yet.'); ?>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="sticky-top" style="top:6rem">
                <?php if ($announcements): ?>
                    <div class="card overflow-hidden rounded-4 mb-3">
                        <div class="card-header border-0 p-4">
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

                <div class="card overflow-hidden rounded-4 mb-3">
                    <div class="card-header bg-transparent border-0 p-4">
                        <h5 class="fw-bold mb-0">
                            <?= phrase('Announcements'); ?>
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <?php if ($announcements): ?>
                            <?php foreach ($announcements as $announcement): ?>
                                <div class="mb-3 pb-3 border-bottom last-child-border-0">
                                    <a href="<?= base_url('announcements/' . $announcement->announcement_slug); ?>" class="fw-bold text-decoration-none --xhr">
                                        <?= $announcement->title; ?>
                                    </a>
                                    <div class="text-muted small mt-1">
                                        <?= truncate($announcement->content, 100); ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="text-center py-4 text-muted">
                                <i class="mdi mdi-bullhorn-outline mdi-36px d-block mb-2"></i>
                                <?= phrase('No announcements available.'); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="card overflow-hidden rounded-4 mb-3">
                    <div class="card-header border-0 p-4">
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
        require.js('<?= base_url('assets/echarts/echarts.min.js'); ?>', function() {
            // Initialize chart
            const visitorChart = echarts.init(document.getElementById('visitor-chart'));

            // Render chart
            visitorChart.setOption({
                title: {
                    text: '<?= phrase('Visitor Graph'); ?>',
                    textStyle: {
                        fontWeight: 'bold'
                    }
                },
                tooltip: {
                    trigger: 'axis',
                    valueFormatter: function(value) {
                        return value + ' <?= phrase('Visits'); ?>';
                    }
                },
                grid: {
                    left: '3%',
                    right: '3%',
                    bottom: '3%',
                    containLabel: true
                },
                xAxis: {
                    type: 'category',
                    boundaryGap: false,
                    data: <?= (isset($visitors->categories) ? json_encode($visitors->categories) : '[]'); ?>
                },
                yAxis: {
                    type: 'value',
                    name: '<?= phrase('Visitor Total'); ?>',
                    minInterval: 1
                },
                series: [{
                    name: '<?= phrase('Visitors'); ?>',
                    type: 'line',
                    smooth: true,
                    areaStyle: {
                        opacity: 0.5
                    },
                    data: <?= (isset($visitors->visits) ? json_encode($visitors->visits) : '[]'); ?>,
                }]
            });

            // Responsive resize
            window.addEventListener('resize', function() {
                visitorChart.resize();
            });
        });
    })
</script>
