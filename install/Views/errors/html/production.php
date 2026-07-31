<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="mobile-web-app-capable" content="yes" />
        <meta name="viewport" content="user-scalable=no, width=device-width, height=device-height, initial-scale=1, maximum-scale=1" />
        <link rel="icon" type="image/x-icon" href="uploads/settings/icons/logo.png">
        <title>Site Under Maintenance!</title>
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
                border-radius: 3rem;
                padding: 3rem 2.5rem;
                max-width: 480px;
                width: 90%;
                text-align: center;
            }
            .logo-container img {
                width: 128px;
                height: auto;
            }
            .banner {
                position: relative;
                background: #fff;
                border: 1px solid #8494ab;
                padding: 2rem;
                margin-top: -1rem;
                border-radius: 1rem;
                z-index: 10;
            }
            .banner::before,
            .banner::after {
                content: "";
                position: absolute;
                top: calc(50% - 6px);
                width: 40px;
                height: 60px;
                transform: translateY(-50%);
                background-image: url("<?= base_url('assets/yao-ming-hand.png'); ?>");
                background-repeat: no-repeat;
                background-size: 80px 60px;
                pointer-events: none;
                z-index: 11;
            }
            .banner::before {
                left: -22px;
                background-position: 0 0;
            }
            .banner::after {
                right: -21px;
                background-position: -40px 0;
            }
            h1 {
                font-weight: 700;
                font-size: 1.875rem;
                margin: 0 0 .5rem 0;
                color: #0f172a;
                line-height: 1.3;
            }
            h1 b {
                color: #0f172a;
            }
            p {
                font-size: 1rem;
                line-height: 1.5;
                margin: 0 0 1rem 0;
            }
            .text-muted {
                color: #8494ab
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
            <div class="logo-container">
                <img src="<?= base_url('assets/yao-ming.png'); ?>" alt="Error" />
            </div>
            <div class="banner">
                <h1><?= phrase('Whoops!'); ?></h1>
                <p><?= phrase('We are temporary down for maintenance. Please come back again later...'); ?></p>

                <a href="<?= base_url(); ?>" class="btn-back">
                    <span>
                        <img
                        src="data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 512 404.43'><path fill='%23fff' d='m68.69 184.48 443.31.55v34.98l-438.96-.54 173.67 159.15-23.6 25.79L0 199.94 218.6.02l23.6 25.79z'/></svg>"
                        alt="Arrow">
                    </span>
                    <span><?= phrase('Back to Home'); ?></span>
                </a>
            </div>
        </div>
    </body>
</html>
