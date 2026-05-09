<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="msapplication-navbutton-color" content="#007bff" />
        <meta name="theme-color" content="#007bff" />
        <meta name="apple-mobile-web-app-status-bar-style" content="#007bff" />
        <meta name="mobile-web-app-capable" content="yes" />
        <meta name="viewport" content="user-scalable=no, width=device-width, height=device-height, initial-scale=1, maximum-scale=1" />
        <link rel="icon" type="image/x-icon" href="uploads/settings/icons/logo.png">
        <title>Site Under Maintenance!</title>
        <style>
            html,
            body {
                min-height: 100%;
                margin: 0;
                padding: 0
            }
            body {
                height: 100%;
                background: #fafafa;
                font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
                color: #777;
                font-weight: 400;
            }
            h1 {
                font-weight: lighter;
                letter-spacing: 0.8;
                font-size: 3rem;
                margin-top: 0;
                margin-bottom: 0;
                color: #222;
            }
            .centered {
                position: fixed;
                width: 100%;
                top: 50%;
                transform: translateY(-50%);
                text-align: center
            }
            a:active,
            a:link,
            a:visited {
                color: #007bff;
            }
        </style>
    </head>
    <body>
        <div class="centered">
            <img src="<?= base_url('assets/yao-ming.png'); ?>" width="128" alt="404" />
            <h1>Whoops!</h1>
            <p>We seem to have hit a snag. Please try again later.</p>
            <p><a href="<?= base_url(); ?>"><b>Back to Home</b></a>
        </div>
    </body>
</html>
