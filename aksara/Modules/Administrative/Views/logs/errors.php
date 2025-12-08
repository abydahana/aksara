<div class="container-fluid">
    <div class="row">
        <div class="col-md-3 order-2 order-md-1">
            <div class="sticky-top">
                <div class="pretty-scrollbar">
                    <?php
                        $errors = null;

                        if ($logs) {
                            foreach ($logs as $key => $val) {
                                $errors .= '
                                    <li class="list-group-item px-0">
                                        <a href="' . current_page('remove', ['log' => $val]) . '" class="float-end text-danger --modal" data-bs-toggle="tooltip" title="' . phrase('Remove') . '">
                                            <i class="mdi mdi-window-close"></i>
                                        </a>
                                        <a href="' . current_page(null, ['report' => $val]) . '" class="' . ($val == service('request')->getGet('report') ? ' fw-bold' : null) . ' --xhr">' . $val . '</a>
                                    </li>
                                ';
                            }

                            echo '
                                <div class="d-grid mt-3 mb-3">
                                    <a href="' . current_page('clear') . '" class="btn btn-danger btn-sm --modal">
                                        <i class="mdi mdi-delete-empty"></i>
                                        ' . phrase('Clear Logs') . '
                                    </a>
                                </div>
                                <ul class="list-group list-group-flush">
                                    ' . $errors . '
                                </ul>
                            ';
                        } else {
                            echo '<div class="pt-3 pb-3">' . phrase('No error log') . '</div>';
                        }
                    ?>
                </div>
            </div>
        </div>
        <div class="col-md-9 order-1 order-md-2 stretch-height">
            <div class="sticky-top">
                <?php
                    if ($report) {
                        $errors = [];
                        $num = 0;

                        foreach ($report as $key => $val) {
                            if (! $val || ! trim($val)) {
                                continue;
                            }

                            $title = null;

                            if (strpos($val, 'CRITICAL - ') !== false || strpos($val, 'ALERT - ') !== false || strpos($val, 'EMERGENCY - ') !== false || strpos($val, 'DEBUG - ') !== false || strpos($val, 'ERROR - ') !== false || strpos($val, 'INFO - ') !== false || strpos($val, 'NOTICE - ') !== false || strpos($val, 'WARNING - ') !== false) {
                                $errors[$num] = [
                                    'title' => $val,
                                    'traces' => []
                                ];

                                $num++;
                            } elseif (isset($errors[$num - 1])) {
                                $errors[$num - 1]['traces'][] = htmlspecialchars($val);
                            }
                        }

                        foreach($errors as $key => $val) {
                            $traces = null;

                            foreach($val['traces'] as $_key => $_val) {
                                $traces .= '<li>' . preg_replace('/^[\d\\s]+/', '', $_val) . '</li>';
                            }

                            echo '
                                <div>
                                    <h6 class="text-danger">
                                        ' . $val['title'] . '
                                    </h6>
                                    ' . ($traces ? '<ol>' . $traces . '</ol>' : null) . '
                                </div>
                            ';
                        }
                    } else {
                        echo '<div class="pt-3 pb-3">' . ($errors ? phrase('Click on the log file to show the error details.') : phrase('Yay! Your application is working fine.')) . '</div>';
                    }
                ?>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        if (UA !== 'mobile') {
            $('.stretch-height').css({
                minHeight: $(window).outerHeight(true) - (($('[role=header]').outerHeight(true) ?? 0) + ($('[role=breadcrumb]').outerHeight(true) ?? 0) + ($('[role=meta]').outerHeight(true) ?? 0)),
                borderLeft: '1px solid rgba(0,0,0,.2)'
            });

            if (typeof mCustomScrollbar === 'function') {
                $('.pretty-scrollbar').mCustomScrollbar({
                    autoHideScrollbar: true,
                    axis: 'y',
                    scrollInertia: 170,
                    mouseWheelPixels: 170,
                    setHeight:  $(window).outerHeight(true) - (($('[role=header]').outerHeight(true) ?? 0) + ($('[role=breadcrumb]').outerHeight(true) ?? 0) + ($('[role=meta]').outerHeight(true) ?? 0)),
                    advanced:
                    {
                        updateOnContentResize: true
                    },
                    autoHideScrollbar: false
                })
            }
        }
    })
</script>
