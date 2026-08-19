<?php

/**
 * @var object $meta
 * @var string $content
 */
?>

<!DOCTYPE html>
<html lang="<?= get_userdata('language') ?? 'en' ?>"<?= is_rtl() ? ' dir="rtl"' : null ?>>
    <head>
        <title><?= truncate($meta->title) . ' | ' . get_setting('app_name') ?></title>
        <meta charset="UTF-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="msapplication-navbutton-color" content="#212529" />
        <meta name="theme-color" content="#212529" />
        <meta name="apple-mobile-web-app-status-bar-style" content="#212529" />
        <meta name="mobile-web-app-capable" content="yes" />
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5" />
        <meta name="description" content="<?= truncate($meta->description ?: get_setting('app_description')) ?>" />
        <meta name="referrer" content="strict-origin-when-cross-origin">
        <script type="text/javascript">
            (function() {
                var savedTheme = <?= json_encode(get_userdata('app_theme')) ?> || localStorage.getItem('bs-theme') || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
                document.documentElement.setAttribute('data-bs-theme', savedTheme);
            })();
        </script>

        <?php
        echo aksara_header();

        echo asset_loader([is_rtl() ? 'bootstrap/css/bootstrap.rtl.min.css' : 'bootstrap/css/bootstrap.min.css', 'materialdesignicons/css/materialdesignicons.min.css', 'local/css/styles.min.css', 'local/css/theme.min.css', 'local/css/mobile.min.css']);
        ?>

        <link rel="icon" type="image/x-icon" href="<?= get_image('settings', get_setting('app_icon'), 'icon') ?>" />
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Ubuntu:ital,wght@0,300;0,400;0,500;0,700;1,300;1,400;1,500;1,700&display=swap" rel="stylesheet">
        <style type="text/css">
            body, h1, h2, h3, h4, h5, h6, .display-4, .display-5, .btn, .lead, .nav-link {
                font-family: 'Ubuntu', sans-serif !important;
            }
        </style>
    </head>
    <body>

        <?php include_once 'header.php'; ?>
        <?php include_once 'breadcrumb.php'; ?>
        <?php
        $userAgent = service('request')->getUserAgent();
        $suffix = (! is_cli() && $userAgent->isMobile() ? '_mobile' : '');
        include_once 'sidebar' . $suffix . '.php';
        ?>

        <main id="page-wrapper">
            <section data-role="meta" id="title-wrapper">
                <div class="container-fluid d-none d-md-none d-lg-block d-xl-block">
                    <div class="row align-items-center alias-table-header border-bottom">
                        <div class="col-8">
                            <h5 class="text-truncate mb-0">
                                <i class="<?= $meta->icon ?>" data-role="icon"></i>
                                <span data-role="title">
                                    <?= $meta->title ?>
                                </span>
                            </h5>
                        </div>
                        <div class="col-4 text-end">
                            <div class="btn-group btn-group-sm">
                                <button type="button" class="btn<?= !$meta->description ? ' d-none' : null ?>" id="description-btn" data-bs-toggle="collapse" data-bs-target="#description-collapse" aria-label="<?= phrase('Toggle Description') ?>">
                                    <i class="mdi mdi-information-outline"></i>
                                </button>
                                <a href="<?= current_page() ?>" class="btn --xhr" data-role="reload" data-bs-toggle="tooltip" title="<?= phrase('Refresh') ?>" aria-label="<?= phrase('Refresh') ?>">
                                    <i class="mdi mdi-refresh"></i>
                                </a>
                                <button type="button" class="btn d-none d-sm-none d-md-none d-lg-block d-xl-block" data-role="expand" data-bs-toggle="tooltip" title="<?= phrase('Expand') ?>" aria-label="<?= phrase('Expand') ?>">
                                    <i class="mdi mdi-arrow-expand"></i>
                                </button>
                                <button type="button" class="btn d-none d-sm-none d-md-none d-lg-block d-xl-block" data-role="close" data-bs-toggle="tooltip" title="<?= phrase('Remove') ?>" aria-label="<?= phrase('Remove') ?>">
                                    <i class="mdi mdi-window-close"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="container-fluid border-bottom py-3 py-md-0 description-collapse collapse alias-description-collapse<?= ($meta->description ? ' show' : '') ?>" id="description-collapse" data-role="description">
                    <?= $meta->description ?>
                </div>
            </section>

            <article id="content-wrapper">

                <?= $content ?>

            </article>

        </main>

        <?php
        echo aksara_footer();
        echo asset_loader([
            'bootstrap/js/bootstrap.bundle.min.js',
            'local/js/scripts.min.js',
            'local/js/mobile.min.js'
        ]);
        ?>

        <?php if (get_setting('ai_enabled')): ?>
            <script type="text/javascript">
                window.AksaraAI = {
                    endpoint: '<?= base_url('xhr/ai') ?>',
                    image: <?= get_setting('ai_image_enabled') ? 'true' : 'false' ?>
                };
            </script>
            <script type="text/javascript" src="<?= base_url('modules/XHR/assets/js/purify.min.js') ?>"></script>
            <script type="text/javascript" src="<?= base_url('modules/XHR/assets/js/marked.min.js') ?>"></script>
            <script type="text/javascript" src="<?= base_url('modules/XHR/assets/js/ai.min.js') ?>"></script>
        <?php endif; ?>

    </body>
</html>
