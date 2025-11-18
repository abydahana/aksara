<!DOCTYPE html>
<html lang="<?= get_userdata('language') ?? 'en'; ?>"<?= (in_array(get_userdata('language'), ['ar', 'arc', 'dv', 'fa', 'ha', 'he', 'khw', 'ks', 'ku', 'ps', 'ur', 'yi']) ? ' dir="rtl"' : null); ?>>
    <head>
        <title><?= truncate($meta->title) . ' | ' . get_setting('app_name'); ?></title>
        <meta charset="UTF-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="msapplication-navbutton-color" content="#007bff" />
        <meta name="theme-color" content="#007bff" />
        <meta name="apple-mobile-web-app-status-bar-style" content="#007bff" />
        <meta name="mobile-web-app-capable" content="yes" />
        <meta name="viewport" content="user-scalable=no, width=device-width, height=device-height, initial-scale=1, maximum-scale=1" />
        <meta name="description" content="<?= truncate($meta->description); ?>" />
        <link rel="icon" type="image/x-icon" href="<?= get_image('settings', get_setting('app_icon'), 'icon'); ?>" />

        <?php
            echo aksara_header();

            echo asset_loader([
                'bootstrap/css/bootstrap.min.css',
                'local/css/styles.min.css'
            ]);
        ?>

        <link rel="preconnect" href="https://fonts.gstatic.com">
        <link href="https://fonts.googleapis.com/css2?family=Fira+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet">
        <style type="text/css">
            body
            {
                font-family: 'Fira Sans', sans-serif
            }
        </style>
    </head>
    <body>

        <?php include_once('header.php'); ?>
        <?php include_once('breadcrumb.php'); ?>
        <?php include_once('sidebar.php'); ?>

        <main id="page-wrapper">
            <section role="meta" id="title-wrapper">
                <div class="container-fluid d-none d-md-none d-lg-block d-xl-block">
                    <div class="row align-items-center alias-table-header border-bottom">
                        <div class="col-8">
                            <h5 class="text-truncate mb-0">
                                <i class="<?= $meta->icon; ?>" role="icon"></i>
                                <span role="title">
                                    <?= $meta->title; ?>
                                </span>
                            </h5>
                        </div>
                        <div class="col-4 text-end">
                            <div class="btn-group btn-group-sm">
                                <button type="button" class="btn<?= (! $meta->description ? ' d-none' : null); ?>" id="description-btn" data-bs-toggle="collapse" data-bs-target="#description-collapse">
                                    <i class="mdi mdi-information-outline"></i>
                                </button>
                                <a href="<?= current_page(); ?>" class="btn --xhr" role="reload" data-bs-toggle="tooltip" title="<?= phrase('Refresh'); ?>">
                                    <i class="mdi mdi-refresh"></i>
                                </a>
                                <button type="button" class="btn d-none d-sm-none d-md-none d-lg-block d-xl-block" role="expand" data-bs-toggle="tooltip" title="<?= phrase('Expand'); ?>">
                                    <i class="mdi mdi-arrow-expand"></i>
                                </button>
                                <button type="button" class="btn d-none d-sm-none d-md-none d-lg-block d-xl-block" role="close" data-bs-toggle="tooltip" title="<?= phrase('Remove'); ?>">
                                    <i class="mdi mdi-window-close"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="container-fluid border-bottom description-collapse collapse alias-description-collapse<?= ($meta->description ? ' show' : ''); ?>" id="description-collapse" role="description">
                    <?= $meta->description; ?>
                </div>
            </section>

            <article id="content-wrapper">

                <?= $content; ?>

            </article>

        </main>

        <?php
            echo aksara_footer();

            echo asset_loader([
                'bootstrap/js/bootstrap.bundle.min.js',
                'local/js/script.min.js'
            ]);
        ?>

    </body>
</html>
