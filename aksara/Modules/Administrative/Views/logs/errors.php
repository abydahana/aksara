<?php

/**
 * @var mixed $logs
 * @var mixed $report
 */
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-9 stretch-height">
            <div class="sticky-lg-top font-monospace">
                <?php if ($report) {
                    $errors = [];
                    $num = 0;

                    foreach ($report as $key => $val) {
                        if (! $val || ! trim($val)) {
                            continue;
                        }

                        $title = null;

                        if (
                            strpos($val, 'CRITICAL - ') !== false ||
                            strpos($val, 'ALERT - ') !== false ||
                            strpos($val, 'EMERGENCY - ') !== false ||
                            strpos($val, 'DEBUG - ') !== false ||
                            strpos($val, 'ERROR - ') !== false ||
                            strpos($val, 'INFO - ') !== false ||
                            strpos($val, 'NOTICE - ') !== false ||
                            strpos($val, 'WARNING - ') !== false
                        ) {
                            $errors[$num] = [
                                'title' => htmlspecialchars($val, ENT_NOQUOTES, 'UTF-8'),
                                'traces' => [],
                            ];

                            $num++;
                        } elseif (isset($errors[$num - 1])) {
                            $errors[$num - 1]['traces'][] = htmlspecialchars($val, ENT_NOQUOTES, 'UTF-8');
                        }
                    }

                    foreach ($errors as $key => $val):
                        $traces = null;

                        foreach ($val['traces'] as $_key => $_val) {
                            $traces .= '<li>' . preg_replace('/^[\d\\s]+/', '', $_val) . '</li>';
                        } ?>

                        <div>
                            <h6 class="text-danger">
                                <?= $val['title'] ?>
                            </h6>

                            <?= $traces ? '<ol>' . $traces . '</ol>' : null ?>
                        </div>
                    <?php endforeach;
                } else {
                    echo '<div class="pt-3 pb-3">' . ($logs ? phrase('Click on the log file to show the error details.') : phrase('Yay! Your application is working fine.')) . '</div>';
                } ?>
            </div>
        </div>
        <div class="col-md-3">
            <div class="sticky-lg-top" style="top:5rem">
                <div class="pretty-scrollbar">
                    <?php if ($logs):
                        $logFiles = null;

                        ob_start();

                        foreach ($logs as $key => $val): ?>
                            <li class="list-group-item px-0">
                                <a href="<?= current_page('remove', ['log' => $val]) ?>" class="float-end text-danger --modal" data-bs-toggle="tooltip" title="<?= phrase('Remove') ?>">
                                    <i class="mdi mdi-window-close"></i>
                                </a>
                                <a href="<?= current_page(null, ['report' => $val]) ?>" class="<?= service('request')->getGet('report') == $val ? ' fw-bold' : '' ?> --xhr"><?= $val ?></a>
                            </li>
                        <?php endforeach;

                        $logFiles = ob_get_clean();
                        ?>
                        <div class="d-grid mt-3 mb-3">
                            <a href="<?= current_page('clear') ?>" class="btn btn-danger btn-sm --modal">
                                <i class="mdi mdi-delete-empty"></i> <?= phrase('Clear Logs') ?>
                            </a>
                        </div>
                        <ul class="list-group list-group-flush">
                            <?= $logFiles ?>
                        </ul>
                    <?php else: ?>
                        <div class="pt-3 pb-3"><?= phrase('No error log') ?></div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        if (UA !== 'mobile') {
            $('.stretch-height').css({
                minHeight: $(window).outerHeight(true) - (($('[data-role=header]').outerHeight(true) ?? 0) + ($('[data-role=breadcrumb]').outerHeight(true) ?? 0) + ($('[data-role=meta]').outerHeight(true) ?? 0)),
                borderLeft: '1px solid rgba(120,120,120,.1)'
            });

            if (typeof OverlayScrollbarsGlobal !== 'undefined') {
                $('.pretty-scrollbar').each(function() {
                    OverlayScrollbarsGlobal.OverlayScrollbars(this, {
                        scrollbars: {
                            theme: 'os-theme-dark',
                            autoHide: 'leave',
                            autoHideDelay: 500,
                            clickScroll: true
                        }
                    });
                });
            }
        }
    })
</script>
