<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="msapplication-navbutton-color" content="#ffffff" />
        <meta name="theme-color" content="#ffffff" />
        <meta name="apple-mobile-web-app-status-bar-style" content="#ffffff" />
        <meta name="mobile-web-app-capable" content="yes" />
        <meta name="viewport" content="user-scalable=no, width=device-width, height=device-height, initial-scale=1, maximum-scale=1" />
        <link rel="icon" type="image/x-icon" href="<?= base_url('uploads/settings/icons/logo.png'); ?>">
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap" rel="stylesheet">
        <title>Aksara is not installed!</title>
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
                border-radius: 8px;
                padding: 3rem 2.5rem;
                max-width: 480px;
                width: 90%;
                text-align: center;
            }
            .logo-container {
                margin-bottom: 2rem;
            }
            .logo-container img {
                width: 200px;
                height: auto;
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
                margin-bottom: 2.5rem;
                color: #64748b;
            }
            .btn-install {
                background-color: #1e293b;
                color: #ffffff;
                text-decoration: none;
                font-weight: 500;
                font-size: 1rem;
                padding: 0.5rem 2rem;
                border-radius: 3rem;
                transition: background-color 0.2s ease;
                box-sizing: border-box;
            }
            .btn-install:hover {
                background-color: #334155;
            }
            .btn-install:active {
                background-color: #0f172a;
            }
            .social-links {
                margin-top: 2.5rem;
                padding-top: 1.5rem;
                display: flex;
                justify-content: center;
                align-items: center;
                gap: 1.5rem;
            }
            .social-links a {
                color: #94a3b8;
                transition: color 0.2s ease, transform 0.2s ease;
                display: inline-flex;
                align-items: center;
                justify-content: center;
            }
            .social-links a:hover {
                color: #0f172a;
            }
            .social-links svg {
                width: 24px;
                height: 24px;
                fill: currentColor;
            }
            .icon-wa svg {
                transform: scale(0.85); /* Slightly scale down WhatsApp icon to match visual weight */
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
                <img src="<?= base_url('uploads/settings/logo.png'); ?>" alt="Aksara Logo" />
            </div>
            <h1>Aksara is not installed!</h1>
            <p>
                The system requires an initial configuration to work properly. Let's get things set up.
            </p>
            <a href="<?= base_url(); ?>" class="btn-install">
                Start Installation
            </a>
            
            <div class="social-links">
                <!-- YouTube -->
                <a href="https://youtube.com/abydahana" target="_blank" title="YouTube">
                    <svg viewBox="0 0 24 24">
                        <path d="M21.582,6.186c-0.23-0.86-0.908-1.538-1.768-1.768C18.254,4,12,4,12,4S5.746,4,4.186,4.418 c-0.86,0.23-1.538,0.908-1.768,1.768C2,7.746,2,12,2,12s0,4.254,0.418,5.814c0.23,0.86,0.908,1.538,1.768,1.768 C5.746,20,12,20,12,20s6.254,0,7.814-0.418c0.86-0.23,1.538-0.908,1.768-1.768C22,16.254,22,12,22,12S22,7.746,21.582,6.186z M10,15.464V8.536L16,12L10,15.464z"/>
                    </svg>
                </a>
                <!-- GitHub Sponsors -->
                <a href="https://github.com/sponsors/abydahana" target="_blank" title="GitHub Sponsors">
                    <svg viewBox="0 0 24 24">
                        <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                    </svg>
                </a>
                <!-- WhatsApp -->
                <a href="https://wa.me/6281381614558" class="icon-wa" target="_blank" title="WhatsApp">
                    <svg viewBox="0 0 24 24">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                    </svg>
                </a>
            </div>
        </div>
    </body>
</html>
