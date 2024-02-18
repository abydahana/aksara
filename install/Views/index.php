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
        <link rel="shortcut icon" type="image/x-icon" href="favicon.ico" />
        <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css" type="text/css" />
        <link rel="stylesheet" href="assets/materialdesignicons/css/materialdesignicons.min.css" type="text/css" />
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,wght@0,400;0,500;0,700;1,400;1,500;1,700&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Fira+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet">
        <style type="text/css">
            body
            {
                font-family: 'Fira Sans', sans-serif
            }
            h1,h2,h3,h4,h5,h6,.display-5,.btn,.lead
            {
                font-family: 'DM Sans', sans-serif!important
            }
        </style>
    </head>
    <style type="text/css">
        html,
        body
        {
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
                                        <div class="text-muted opacity-50">
                                            <hr class="row" />
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
            $(document).ready(function()
            {
                if ($('input[name=timezone]').length)
                {
                    $('input[name=timezone]').val(Intl.DateTimeFormat().resolvedOptions().timeZone)
                }
                
                $('[data-bs-toggle=tooltip]').tooltip(),
                $('[data-bs-toggle=popover]').popover(),
                
                $('body').on('change', 'select[name=language]', function(e)
                {
                    window.location.href            = '?language=' + $(this).val()
                }),
                
                $('body').on('click change', 'input[name=agree]', function(e)
                {
                    if ($(this).is(':checked'))
                    {
                        $(this).closest('form').find('button[type=submit]').prop('disabled', false)
                    }
                    else
                    {
                        $(this).closest('form').find('button[type=submit]').prop('disabled', true)
                    }
                }),
                
                $('body').on('click change', 'input[name=request_config]', function(e)
                {
                    if ($(this).is(':checked'))
                    {
                        $('.using_ftp').slideUp()
                    }
                    else
                    {
                        $('.using_ftp').slideDown()
                    }
                }),
                
                $('body').on('click touch', '.--xhr', function(e)
                {
                    e.preventDefault(),
                    $.ajax
                    ({
                        url: $(this).attr('href'),
                        context: this,
                        beforeSend: function()
                        {
                            $('.failure').remove(),
                            $('[data-bs-toggle=tooltip]').tooltip('hide'),
                            $('[data-bs-toggle=popover]').popover('hide')
                        }
                    })
                    .done(function(response)
                    {
                        if (response.status == 301)
                        {
                            window.location.href    = response.url;
                            
                            return;
                        }
                        else if (response.status !== 200)
                        {
                            $('.--validation-callback').html('<div class="alert alert-warning failure"><b><?= phrase('Whoops!'); ?></b> ' + response.message + '</div>'),
                            $('html, body').animate
                            ({
                                scrollTop: $('.failure').offset().top - 60
                            }, 500)
                        }
                        
                        $('.step' + response.active).addClass('text-warning'),
                        $(response.passed).removeClass('text-warning').addClass('text-success'),
                        $('.step-content').html(response.html),
                        $('[data-bs-toggle=tooltip]').tooltip(),
                        $('[data-bs-toggle=popover]').popover()
                    })
                    .fail(function(response, status, error)
                    {
                        $(this).find('button[type=submit]').prop('disabled', false),
                        $('.--validation-callback').html('<div class="alert alert-danger failure"><b><?= phrase('Whoops!'); ?></b> ' + error + '</div>'),
                        $('html, body').animate
                        ({
                            scrollTop: $('.failure').offset().top - 60
                        }, 500)
                    })
                }),
                
                $('body').on('submit', '.--validate-form', function(e)
                {
                    e.preventDefault(),
                    $.ajax
                    ({
                        url: $(this).attr('action'),
                        method: $(this).attr('method'),
                        data: new FormData(this),
                        contentType: false,
                        processData: false,
                        context: this,
                        beforeSend: function()
                        {
                            $('.failure').remove(),
                            $('.--validation-callback').removeClass('alert alert-warning pr-3 pl-3').html(''),
                            $(this).find('button[type=submit]').prop('disabled', true).addClass('disabled').find('i.mdi').removeClass('mdi-check').addClass('mdi-loading mdi-spin'),
                            $('[data-bs-toggle=tooltip]').tooltip('hide'),
                            $('[data-bs-toggle=popover]').popover('hide')
                        },
                        complete: function(progress)
                        {
                            /* animate the submit button */
                            $(this).find('button[type=submit]:not(.btn-link)').prop('disabled', false).removeClass('disabled').find('i.mdi').removeClass('mdi-loading mdi-spin').addClass('mdi-check')
                        },
                    })
                    .done(function(response)
                    {
                        if (response.status === 200)
                        {
                            $('.step' + response.active).addClass('text-warning'),
                            $(response.passed).removeClass('text-warning').addClass('text-success'),
                            $('.step-content').html(response.html),
                            $('html, body').animate
                            ({
                                scrollTop: 0
                            }, 500)
                        }
                        else if (response.status === 400)
                        {
                            var num                    = 0;
                            
                            $('.--validation-callback').addClass('alert alert-warning pr-3 pl-3'),
                            $.each(response.validation, function(key, val)
                            {
                                $('<p class="' + (num ? 'mb-1 border-top' : 'mb-1') + '">' + val + '</p>').appendTo('.--validation-callback');
                                
                                num++;
                            })
                        }
                        else
                        {
                            $('.--validation-callback').html('<div class="alert alert-warning failure"><b><?= phrase('Whoops!'); ?></b> ' + response.message + '</div>'),
                            $('html, body').animate
                            ({
                                scrollTop: $('.failure').offset().top - 60
                            }, 500)
                        }
                        
                        $('[data-bs-toggle=tooltip]').tooltip(),
                        $('[data-bs-toggle=popover]').popover()
                    })
                    .fail(function(response, status, error)
                    {
                        $(this).find('button[type=submit]').prop('disabled', false),
                        $('.--validation-callback').html('<div class="alert alert-warning failure"><b><?= phrase('Whoops!'); ?></b> ' + error + '</div>'),
                        $('html, body').animate
                        ({
                            scrollTop: $('.failure').offset().top - 60
                        }, 500)
                    })
                })
            })
        </script>
    </body>
</html>
