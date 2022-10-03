<?php

// redirect browser to "public" path when virtual host isn't set to "public" directory
// header('Location: public');

/**
 * Aksara - CMS PHP Framework 
 *
 * @package  Aksara CMS
 * @author   Abydahana <abydahana@gmail.com>
 */

$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)
);

// This file allows us to emulate Apache's "mod_rewrite" functionality from the
// built-in PHP web server. This provides a convenient way to test a Aksara CMS
// application without having installed a "real" web server software here.
if ($uri !== '/' && file_exists(__DIR__.'/public'.$uri)) {
    return false;
}

require_once __DIR__.'/public/index.php';


