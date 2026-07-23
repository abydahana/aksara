<?php
/**
 * @var object $meta
 * @var string $content
 */
?>
<!DOCTYPE html>
<html lang="<?= get_userdata('language') ?? 'en'; ?>"<?= (is_rtl() ? ' dir="rtl"' : null); ?>>
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
        <meta name="referrer" content="strict-origin-when-cross-origin">
        <link rel="icon" type="image/x-icon" href="<?= get_image('settings', get_setting('app_icon'), 'icon'); ?>" />

        <?php
            echo aksara_header();

            echo asset_loader([
                (is_rtl() ? 'bootstrap/css/bootstrap.rtl.min.css' : 'bootstrap/css/bootstrap.min.css'),
                'materialdesignicons/css/materialdesignicons.min.css',
                'local/css/styles.min.css',
                'local/css/mobile.min.css',
                'local/css/theme.min.css'
            ]);
        ?>

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

        <?php require_once('header.php'); ?>

        <main id="content-wrapper">
            <section id="content-placeholder">
                <?= $content; ?>
            </section>
        </main>

        <?php require_once('footer.php'); ?>

        <?php
            echo aksara_footer();

            echo asset_loader([
                'bootstrap/js/bootstrap.bundle.min.js',
                'local/js/scripts.min.js'
            ]);
        ?>
    </body>
</html>
