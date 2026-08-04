<?php

/** @var int $expiresAt */
$expiresAt = isset($expiresAt)
    ? (int) $expiresAt
    : 0;

$remainingSeconds = $expiresAt > time()
    ? $expiresAt - time()
    : 0;

$remainingMinutes = $remainingSeconds > 0
    ? (int) ceil($remainingSeconds / 60)
    : 0;
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="mobile-web-app-capable" content="yes" />
        <meta name="viewport" content="user-scalable=no, width=device-width, height=device-height, initial-scale=1, maximum-scale=1" />
        <link rel="icon" type="image/x-icon" href="uploads/settings/icons/logo.png">
        <title>403 - <?= phrase('Access Banned'); ?></title>
        <style>
            html, body {
                min-height: 100vh;
                margin: 0;
                padding: 0;
                background-color: #ffffff;
                font-family: 'Inter', "Helvetica Neue", Helvetica, Arial, sans-serif;
                color: #0f172a;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            .content-wrapper {
                background-color: #ffffff;
                border: 3px dashed #faa;
                border-radius: 3rem;
                padding: 3rem 2.5rem;
                max-width: 480px;
                text-align: center;
            }
            .forbidden-icon img {
                max-width: 128px;
                max-height: auto;
                opacity: 0.5;
            }
            .logo-container {
                margin-top: 2rem;
            }
            .logo-container img {
                max-width: 200px;
                max-height: 48px;
                opacity: 0.5;
            }
            h1 {
                font-weight: 700;
                font-size: 1.875rem;
                margin: 0 0 1rem 0;
                color: #0f172a;
                line-height: 1.3;
            }
            h1 b {
                color: #0f172a;
            }
            p {
                font-size: 1rem;
                line-height: 1.5;
            }
            .text-muted {
                color: #8494ab
            }
            .text-danger {
                color: #800;
            }
            .btn-back {
                display: inline-flex;
                gap: 1rem;
                align-items: center;
                justify-content: center;
                background-color: #1e293b;
                color: #ffffff;
                text-decoration: none;
                font-size: 1rem;
                padding: 0.5rem 2rem;
                border-radius: 3rem;
                transition: background-color 0.2s ease;
                box-sizing: border-box;
            }
            .btn-back img {
                height: .75rem;
                margin: 0
            }
            .btn-back:hover {
                background-color: #334155;
            }
            .btn-back:active {
                background-color: #0f172a;
            }
            @media (max-width: 640px) {
                .content-wrapper {
                    padding: 2.5rem 1.5rem;
                }
            }
        </style>
    </head>
    <body>
        <div class="content-wrapper">
            <div class="forbidden-icon">
                <img src="<?= base_url('assets/forbidden.png'); ?>" alt="Forbidden" width="128" />
            </div>
            <h1 class="text-danger">403 - <?= phrase('Access Banned'); ?></h1>
            <p class="text-muted"><?= phrase('Your network address has been temporarily banned because it repeatedly submitted URLs containing disallowed characters.'); ?></p>

            <?php if ($remainingMinutes > 0): ?>
                <p>
                    <b><?= ($remainingMinutes > 1 ? phrase('Access will be restored in approximately {{remaining}} minutes.', ['remaining' => $remainingMinutes]) : phrase('Access will be restored in approximately {{remaining}} minute.', ['remaining' => $remainingMinutes])); ?></b>
                </p>
            <?php endif; ?>

            <div class="logo-container">
                <img src="<?= base_url(UPLOAD_PATH . '/settings/' . get_setting('app_logo')); ?>" alt="Logo" />
            </div>
        </div>
    </body>
</html>
