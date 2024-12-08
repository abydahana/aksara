<!DOCTYPE html>
<html lang="en">
    <head>
        <title><?= phrase('Aksara Installer'); ?></title>
        <meta charset="UTF-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="msapplication-navbutton-color" content="#007bff" />
        <meta name="theme-color" content="#007bff" />
        <meta name="apple-mobile-web-app-status-bar-style" content="#007bff" />
        <meta name="apple-mobile-web-app-capable" content="yes" />
        <meta name="viewport" content="user-scalable=no, width=device-width, height=device-height, initial-scale=1, maximum-scale=1" />
        <link rel="icon" type="image/x-icon" href="uploads/settings/icons/logo.png">
        <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css" type="text/css" />
        <link rel="stylesheet" href="assets/materialdesignicons/css/materialdesignicons.min.css" type="text/css" />
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,wght@0,400;0,500;0,700;1,400;1,500;1,700&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Fira+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet">
        <style type="text/css">
            body {
                font-family: 'Fira Sans', sans-serif
            }
            h1,h2,h3,h4,h5,h6,.display-5,.btn,.lead {
                font-family: 'DM Sans', sans-serif!important
            }
        </style>
    </head>
    <style type="text/css">
        html,
        body {
            height: 100%;
            min-height: 100%
        }
    </style>
    <body class="bg-light">
        <div class="container-fluid h-100">
            <div class="row h-100 align-items-center">
                <div class="col-xl-10 offset-xl-1 col-xxl-8 offset-xxl-2 py-3">
                    <div class="card shadow-sm border-0 border-top border-secondary-subtle rounded-4 overflow-hidden">
                        <div class="card-body py-0 px-3">
                            <div class="row">
                                <div class="col-md-4 border-end pt-3 d-none d-md-block position-relative">
                                    <div class="sticky-top mb-3 pb-5" style="top:15px">
                                        <a href="//www.aksaracms.com" class="text-primary text-decoration-none" target="_blank">
                                            <h4 class="fw-bold mb-3">
                                                <b>
                                                    Aksara
                                                </b>
                                                <small class="text-sm text-secondary fw-light">
                                                    Installer
                                                </small>
                                            </h4>
                                        </a>
                                        <div class="position-relative">
                                            <div class="opacity-50">
                                                <hr class="row" />
                                            </div>
                                            <a href="//palestinecampaign.org" target="_blank">
                                                <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABIAAAAeCAIAAACubtl7AAAACXBIWXMAAAsTAAALEwEAmpwYAAAKHmlUWHRYTUw6Y29tLmFkb2JlLnhtcAAAAAAAPD94cGFja2V0IGJlZ2luPSLvu78iIGlkPSJXNU0wTXBDZWhpSHpyZVN6TlRjemtjOWQiPz4gPHg6eG1wbWV0YSB4bWxuczp4PSJhZG9iZTpuczptZXRhLyIgeDp4bXB0az0iQWRvYmUgWE1QIENvcmUgNi4wLWMwMDIgNzkuMTY0NDYwLCAyMDIwLzA1LzEyLTE2OjA0OjE3ICAgICAgICAiPiA8cmRmOlJERiB4bWxuczpyZGY9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkvMDIvMjItcmRmLXN5bnRheC1ucyMiPiA8cmRmOkRlc2NyaXB0aW9uIHJkZjphYm91dD0iIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RFdnQ9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZUV2ZW50IyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtbG5zOmRjPSJodHRwOi8vcHVybC5vcmcvZGMvZWxlbWVudHMvMS4xLyIgeG1sbnM6cGhvdG9zaG9wPSJodHRwOi8vbnMuYWRvYmUuY29tL3Bob3Rvc2hvcC8xLjAvIiB4bWxuczp0aWZmPSJodHRwOi8vbnMuYWRvYmUuY29tL3RpZmYvMS4wLyIgeG1sbnM6ZXhpZj0iaHR0cDovL25zLmFkb2JlLmNvbS9leGlmLzEuMC8iIHhtbG5zOnhtcD0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wLyIgeG1wTU06RG9jdW1lbnRJRD0iYWRvYmU6ZG9jaWQ6cGhvdG9zaG9wOmRkOTk1YzNiLTVkODctOTA0Yi04ZTNjLTlkYzE5Njg0Mjg0YyIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDplMjUzMjQ0NS02Y2E5LTQxYjQtODJlYS03MTVkNTkyYTM2YTgiIHhtcE1NOk9yaWdpbmFsRG9jdW1lbnRJRD0iNDc4MUEzMTZBOTJFQkE5QUM3MzA2QzFERjU5QzIxNTMiIGRjOmZvcm1hdD0iaW1hZ2UvcG5nIiBwaG90b3Nob3A6Q29sb3JNb2RlPSIzIiBwaG90b3Nob3A6SUNDUHJvZmlsZT0iIiB0aWZmOkltYWdlV2lkdGg9IjEwMDAiIHRpZmY6SW1hZ2VMZW5ndGg9IjEwMDAiIHRpZmY6UGhvdG9tZXRyaWNJbnRlcnByZXRhdGlvbj0iMiIgdGlmZjpTYW1wbGVzUGVyUGl4ZWw9IjMiIHRpZmY6WFJlc29sdXRpb249IjEvMSIgdGlmZjpZUmVzb2x1dGlvbj0iMS8xIiB0aWZmOlJlc29sdXRpb25Vbml0PSIxIiBleGlmOkV4aWZWZXJzaW9uPSIwMjMxIiBleGlmOkNvbG9yU3BhY2U9IjY1NTM1IiBleGlmOlBpeGVsWERpbWVuc2lvbj0iMTAwMCIgZXhpZjpQaXhlbFlEaW1lbnNpb249IjEwMDAiIHhtcDpDcmVhdGVEYXRlPSIyMDI0LTA4LTMxVDIyOjQ2OjA1KzA3OjAwIiB4bXA6TW9kaWZ5RGF0ZT0iMjAyNC0wOC0zMVQyMjo1NDoxOSswNzowMCIgeG1wOk1ldGFkYXRhRGF0ZT0iMjAyNC0wOC0zMVQyMjo1NDoxOSswNzowMCI+IDx4bXBNTTpIaXN0b3J5PiA8cmRmOlNlcT4gPHJkZjpsaSBzdEV2dDphY3Rpb249InNhdmVkIiBzdEV2dDppbnN0YW5jZUlEPSJ4bXAuaWlkOmI1NWQ2NGQ2LTZjNzEtNDQ2NC1hNmRlLTA2ODFhNGYzODI2ZSIgc3RFdnQ6d2hlbj0iMjAyNC0wOC0zMVQyMjo0OTo1OCswNzowMCIgc3RFdnQ6c29mdHdhcmVBZ2VudD0iQWRvYmUgUGhvdG9zaG9wIDIxLjIgKE1hY2ludG9zaCkiIHN0RXZ0OmNoYW5nZWQ9Ii8iLz4gPHJkZjpsaSBzdEV2dDphY3Rpb249ImNvbnZlcnRlZCIgc3RFdnQ6cGFyYW1ldGVycz0iZnJvbSBpbWFnZS9qcGVnIHRvIGltYWdlL3BuZyIvPiA8cmRmOmxpIHN0RXZ0OmFjdGlvbj0iZGVyaXZlZCIgc3RFdnQ6cGFyYW1ldGVycz0iY29udmVydGVkIGZyb20gaW1hZ2UvanBlZyB0byBpbWFnZS9wbmciLz4gPHJkZjpsaSBzdEV2dDphY3Rpb249InNhdmVkIiBzdEV2dDppbnN0YW5jZUlEPSJ4bXAuaWlkOmIzZWM5MjczLWVkNmYtNDY1YS1iMzE3LWZmZGI1YzZlMDRjYSIgc3RFdnQ6d2hlbj0iMjAyNC0wOC0zMVQyMjo0OTo1OCswNzowMCIgc3RFdnQ6c29mdHdhcmVBZ2VudD0iQWRvYmUgUGhvdG9zaG9wIDIxLjIgKE1hY2ludG9zaCkiIHN0RXZ0OmNoYW5nZWQ9Ii8iLz4gPHJkZjpsaSBzdEV2dDphY3Rpb249InNhdmVkIiBzdEV2dDppbnN0YW5jZUlEPSJ4bXAuaWlkOmUyNTMyNDQ1LTZjYTktNDFiNC04MmVhLTcxNWQ1OTJhMzZhOCIgc3RFdnQ6d2hlbj0iMjAyNC0wOC0zMVQyMjo1NDoxOSswNzowMCIgc3RFdnQ6c29mdHdhcmVBZ2VudD0iQWRvYmUgUGhvdG9zaG9wIDIxLjIgKE1hY2ludG9zaCkiIHN0RXZ0OmNoYW5nZWQ9Ii8iLz4gPC9yZGY6U2VxPiA8L3htcE1NOkhpc3Rvcnk+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOmI1NWQ2NGQ2LTZjNzEtNDQ2NC1hNmRlLTA2ODFhNGYzODI2ZSIgc3RSZWY6ZG9jdW1lbnRJRD0iNDc4MUEzMTZBOTJFQkE5QUM3MzA2QzFERjU5QzIxNTMiIHN0UmVmOm9yaWdpbmFsRG9jdW1lbnRJRD0iNDc4MUEzMTZBOTJFQkE5QUM3MzA2QzFERjU5QzIxNTMiLz4gPHRpZmY6Qml0c1BlclNhbXBsZT4gPHJkZjpTZXE+IDxyZGY6bGk+ODwvcmRmOmxpPiA8cmRmOmxpPjg8L3JkZjpsaT4gPHJkZjpsaT44PC9yZGY6bGk+IDwvcmRmOlNlcT4gPC90aWZmOkJpdHNQZXJTYW1wbGU+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+q9gGPAAAAXlJREFUOBFjqPYyf86gvY/BaBej0R5UtBuJvZcRqMBwF4PhO0aHVlZlBs45bmUBli8ZdA4yGO1kMN7LYLQHhvYiMXaByU+Mju1sqgxMDAxGNY4cc93zQyyeMmgfZjDagaQNjnYyGO5jMH7HaN/EpgzSAwRmlY4m1Y6sc93SI6yeM+gcBtuJqecTk301mwJUDxAA9ZhVOZpUOXLOdUuLsnnCoHmIQW8XgylEz24Gk30MBm8ZLCqQ9UC0QXSaAnXOdktJcTrBILGHQX0vg8VeBrM9DFpnGRQKhFUYGBlQAEQbSGe1k3qBkfrqpEurl+1n4NvJoLEHZC3X/Xmzxd3tGBhwaDOtdlIrNALa+fP//zdbtq5gYFjGwPBkwcL///+ra2nh1VZkbFRh/+7rR6DSG5NnXuro+g8GqioqBLQZVzm+/vDmPypQU1MjrO3Nx7dEaKsZ1UalkKSvtppRRw5J26jhSCOitVU5mVSDkGm1s1qRqXGV05tPhLUBAJ0yfWHLQhIAAAAAAElFTkSuQmCC" class="position-absolute end-0 border border-dark border-top-0" style="top:0" />
                                            </a>
                                        </div>
                                        <p class="step requirement py-1">
                                            <b>
                                                <?= phrase('Checking Requirements'); ?>
                                            </b>
                                        </p>
                                        <p class="step database py-1">
                                            <b>
                                                <?= phrase('Database Configuration'); ?>
                                            </b>
                                        </p>
                                        <p class="step security py-1">
                                            <b>
                                                <?= phrase('Security Configuration'); ?>
                                            </b>
                                        </p>
                                        <p class="step system py-1">
                                            <b>
                                                <?= phrase('System Configuration'); ?>
                                            </b>
                                        </p>
                                        <p class="step final py-1">
                                            <b>
                                                <?= phrase('Finalizing'); ?>
                                            </b>
                                        </p>
                                    </div>
                                    
                                    <div class="position-absolute start-0 end-0 bottom-0 w-100 p-3">
                                        <a href="//youtube.com/abydahana?sub_confirmation=1" class="btn btn-danger btn-sm rounded-pill" target="_blank" data-bs-toggle="tooltip" title="<?= phrase('Subscribe to my channel'); ?>">
                                            <i class="mdi mdi-youtube"></i>
                                        </a>
                                        <a href="//fb.me/abyprogrammer" class="btn btn-primary btn-sm rounded-pill" target="_blank" data-bs-toggle="tooltip" title="<?= phrase('Be my friend'); ?>">
                                            <i class="mdi mdi-facebook"></i>
                                        </a>
                                        <a href="//github.com/abydahana" class="btn btn-dark btn-sm rounded-pill" target="_blank" data-bs-toggle="tooltip" title="<?= phrase('Follow my GitHub'); ?>">
                                            <i class="mdi mdi-github-circle"></i>
                                        </a>
                                        <a href="//trakteer.id/aksaralaboratory" class="btn btn-outline-danger btn-sm rounded-pill float-end" target="_blank" data-bs-toggle="tooltip" title="Trakteer!">
                                            <i class="mdi mdi-square-inc-cash"></i>
                                        </a>
                                    </div>
                                </div>
                                <div class="col-md-8 pt-3 pb-3">
                                    <div class="d-md-none text-center">
                                        <a href="//www.aksaracms.com" class="text-primary text-decoration-none" target="_blank">
                                            <h4 class="fw-bold mb-3">
                                                Aksara <small class="text-sm text-secondary fw-light">Installer</small>
                                            </h4>
                                        </a>
                                        <div class="text-muted opacity-50">
                                            <hr class="row" />
                                        </div>
                                    </div>
                                    <div class="sticky-top step-content" style="top:15px">
                                        <form action="<?= site_url('requirement'); ?>" method="POST" class="--validate-form">
                                            <div class="row">
                                                <div class="col-7">
                                                    <h4>
                                                        <?= phrase('Hello there'); ?>,
                                                    </h4>
                                                </div>
                                                <div class="col-5">
                                                    <select name="language" class="form-select form-select-sm rounded-pill" placeholder="<?= phrase('Choose language'); ?>">
                                                        <option value="en"<?= (session()->get('language') == 'en' ? ' selected' : null); ?>>English</option>
                                                        <option value="id"<?= (session()->get('language') == 'id' ? ' selected' : null); ?>>Indonesia</option>
                                                        <option value="ru"<?= (session()->get('language') == 'ru' ? ' selected' : null); ?>>Русский</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <p>
                                                <?= phrase('Thank you for choosing Aksara'); ?>
                                            </p>
                                            <div class="text-muted opacity-50">
                                                <hr class="row" />
                                            </div>
                                            <p>
                                                <?= phrase('Before we start the installation, please take a moment to read this few notes.'); ?>
                                                <?= phrase('You could check the agreement box and skip reading as usual.'); ?>
                                            </p>
                                            <ol>
                                                <li>
                                                    <p>
                                                        <?= phrase('Article 1'); ?>
                                                    </p>
                                                </li>
                                                <li>
                                                    <p>
                                                        <?= phrase('Article 2'); ?>
                                                    </p>
                                                </li>
                                                <li>
                                                    <p>
                                                        <?= phrase('Article 3'); ?>
                                                    </p>
                                                </li>
                                            </ol>
                                            <p>
                                                <?= phrase('Three notes should be enough.'); ?>
                                                <?= phrase('I look forward to your support.'); ?>
                                            </p>
                                            <p class="mb-0">
                                                <?= phrase('The fool'); ?>,
                                            </p>
                                            <p>
                                                <a href="//abydahana.github.io" class="text-primary text-decoration-none" target="_blank">
                                                    <b>
                                                        Aby Dahana
                                                    </b>
                                                </a>
                                            </p>
                                            <div class="text-muted opacity-50">
                                                <hr class="row" />
                                            </div>
                                            <div class="--validation-callback"></div>
                                            <div class="row align-items-center">
                                                <div class="col-md-6">
                                                    <label class="mb-3 mb-md-0">
                                                        <input type="checkbox" name="agree" class="form-check-input" value="1" />
                                                        <?= phrase('Pretend to agree'); ?>
                                                    </label>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="d-grid">
                                                        <?= (! session()->get('timezone') ? '<input type="hidden" name="timezone" />' : null); ?>
                                                        <input type="hidden" name="_token" value="<?= sha1(time()); ?>" />
                                                        <button type="submit" class="btn btn-outline-primary btn-sm rounded-pill" disabled>
                                                            <i class="mdi mdi-check"></i>
                                                            <?= phrase('Start Installation'); ?>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script type="text/javascript" src="assets/jquery/jquery.min.js"></script>
        <script type="text/javascript" src="assets/bootstrap/js/bootstrap.bundle.min.js"></script>
        
        <script type="text/javascript">
            $(document).ready(function() {
                if ($('input[name=timezone]').length) {
                    $('input[name=timezone]').val(Intl.DateTimeFormat().resolvedOptions().timeZone)
                }
                
                $('[data-bs-toggle=tooltip]').tooltip(),
                $('[data-bs-toggle=popover]').popover(),
                
                $('body').on('change', 'select[name=language]', function(e) {
                    window.location.href = '?language=' + $(this).val()
                }),
                
                $('body').on('click change', 'input[name=agree]', function(e) {
                    if ($(this).is(':checked')) {
                        $(this).closest('form').find('button[type=submit]').prop('disabled', false)
                    } else {
                        $(this).closest('form').find('button[type=submit]').prop('disabled', true)
                    }
                }),
                
                $('body').on('click change', 'input[name=request_config]', function(e) {
                    if ($(this).is(':checked')) {
                        $('.using_ftp').slideUp()
                    } else {
                        $('.using_ftp').slideDown()
                    }
                }),
                
                $('body').on('click touch', '.--xhr', function(e) {
                    e.preventDefault(),
                    $.ajax({
                        url: $(this).attr('href'),
                        context: this,
                        beforeSend: function() {
                            $('.failure').remove(),
                            $('[data-bs-toggle=tooltip]').tooltip('hide'),
                            $('[data-bs-toggle=popover]').popover('hide')
                        }
                    })
                    .done(function(response) {
                        if (response.status == 301) {
                            window.location.href    = response.url;
                            
                            return;
                        } else if (response.status !== 200) {
                            $('.--validation-callback').html('<div class="alert alert-warning failure"><b><?= phrase('Whoops!'); ?></b> ' + response.message + '</div>'),
                            $('html, body').animate({
                                scrollTop: $('.failure').offset().top - 60
                            }, 500)
                        }
                        
                        $('.step' + response.active).addClass('text-warning'),
                        $(response.passed).removeClass('text-warning').addClass('text-success'),
                        $('.step-content').html(response.html),
                        $('[data-bs-toggle=tooltip]').tooltip(),
                        $('[data-bs-toggle=popover]').popover()
                    })
                    .fail(function(response, status, error) {
                        $(this).find('button[type=submit]').prop('disabled', false),
                        $('.--validation-callback').html('<div class="alert alert-danger failure"><b><?= phrase('Whoops!'); ?></b> ' + error + '</div>'),
                        $('html, body').animate({
                            scrollTop: $('.failure').offset().top - 60
                        }, 500)
                    })
                }),
                
                $('body').on('submit', '.--validate-form', function(e) {
                    e.preventDefault(),
                    $.ajax({
                        url: $(this).attr('action'),
                        method: $(this).attr('method'),
                        data: new FormData(this),
                        contentType: false,
                        processData: false,
                        context: this,
                        beforeSend: function() {
                            $('.failure').remove(),
                            $('.--validation-callback').removeClass('alert alert-warning pr-3 pl-3').html(''),
                            $(this).find('button[type=submit]').prop('disabled', true).addClass('disabled').find('i.mdi').removeClass('mdi-check').addClass('mdi-loading mdi-spin'),
                            $('[data-bs-toggle=tooltip]').tooltip('hide'),
                            $('[data-bs-toggle=popover]').popover('hide')
                        },
                        complete: function(progress) {
                            /* animate the submit button */
                            $(this).find('button[type=submit]:not(.btn-link)').prop('disabled', false).removeClass('disabled').find('i.mdi').removeClass('mdi-loading mdi-spin').addClass('mdi-check')
                        },
                    })
                    .done(function(response) {
                        if (typeof response.code !== 'undefined' && response.code === 200) {
                            /* indicates that the Aksara was successfully installed */
                            window.location.href = '<?= base_url(); ?>';

                            return;
                        }

                        if (response.status === 200) {
                            $('.step' + response.active).addClass('text-warning'),
                            $(response.passed).removeClass('text-warning').addClass('text-success'),
                            $('.step-content').html(response.html),
                            $('html, body').animate({
                                scrollTop: 0
                            }, 500)
                        } else if (response.status === 400) {
                            var num = 0;
                            
                            $('.--validation-callback').addClass('alert alert-warning pr-3 pl-3'),
                            $.each(response.validation, function(key, val) {
                                $('<p class="' + (num ? 'mb-1 border-top' : 'mb-1') + '">' + val + '</p>').appendTo('.--validation-callback');
                                
                                num++;
                            })
                        } else {
                            $('.--validation-callback').html('<div class="alert alert-warning failure"><b><?= phrase('Whoops!'); ?></b> ' + response.message + '</div>'),
                            $('html, body').animate({
                                scrollTop: $('.failure').offset().top - 60
                            }, 500)
                        }
                        
                        $('[data-bs-toggle=tooltip]').tooltip(),
                        $('[data-bs-toggle=popover]').popover()
                    })
                    .fail(function(response, status, error) {
                        $(this).find('button[type=submit]').prop('disabled', false),
                        $('.--validation-callback').html('<div class="alert alert-warning failure"><b><?= phrase('Whoops!'); ?></b> ' + error + '</div>'),
                        $('html, body').animate({
                            scrollTop: $('.failure').offset().top - 60
                        }, 500)
                    })
                })
            })
        </script>
    </body>
</html>
