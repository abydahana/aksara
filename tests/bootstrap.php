<?php

/**
 * This file is part of Aksara CMS, both framework and publishing
 * platform.
 *
 * @author     Aby Dahana <abydahana@gmail.com>
 * @copyright  (c) Aksara Laboratory <https://aksaracms.com>
 * @license    MIT License
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the LICENSE.txt file.
 *
 * When the signs is coming, those who don't believe at "that time"
 * have only two choices, commit suicide or become brutal.
 */

defined('BASE_URL') || define('BASE_URL', 'http://localhost:8080/');
defined('TIMEZONE') || define('TIMEZONE', 'Asia/Jakarta');
defined('ENCRYPTION_KEY') || define('ENCRYPTION_KEY', 'testing_key_12345678901234567890123456789012');
defined('COOKIE_NAME') || define('COOKIE_NAME', 'aksara_test');
defined('DEMO_MODE') || define('DEMO_MODE', false);
defined('BACKEND_ONLY') || define('BACKEND_ONLY', false);
defined('SESSION_EXPIRATION') || define('SESSION_EXPIRATION', 7200);

// Database constants (default to safe values for testing)
defined('DB_DSN') || define('DB_DSN', '');
defined('DB_DRIVER') || define('DB_DRIVER', 'MySQLi');
defined('DB_HOSTNAME') || define('DB_HOSTNAME', 'localhost');
defined('DB_PORT') || define('DB_PORT', 3306);
defined('DB_USERNAME') || define('DB_USERNAME', 'root');
defined('DB_PASSWORD') || define('DB_PASSWORD', '');
defined('DB_DATABASE') || define('DB_DATABASE', 'aksara_test');

// Upload constants
defined('UPLOAD_PATH') || define('UPLOAD_PATH', 'uploads');
defined('MAX_UPLOAD_SIZE') || define('MAX_UPLOAD_SIZE', 32);
defined('DOCUMENT_FORMAT_ALLOWED') || define('DOCUMENT_FORMAT_ALLOWED', 'jpg,jpeg,png,gif,pdf,xls,xlsx,doc,docx,csv');
defined('IMAGE_FORMAT_ALLOWED') || define('IMAGE_FORMAT_ALLOWED', 'jpg,jpeg,png,gif');
defined('IMAGE_DIMENSION') || define('IMAGE_DIMENSION', 1024);
defined('THUMBNAIL_DIMENSION') || define('THUMBNAIL_DIMENSION', 256);
defined('ICON_DIMENSION') || define('ICON_DIMENSION', 80);

// Helper for Mock Settings
require_once __DIR__ . '/Support/MockSettings.php';
require_once __DIR__ . '/Support/Fakes.php';

// Create dummy uploads/placeholder.png if not exists
if (! is_dir(UPLOAD_PATH)) {
    mkdir(UPLOAD_PATH, 0755, true);
}
if (! file_exists(UPLOAD_PATH . '/placeholder.png')) {
    file_put_contents(UPLOAD_PATH . '/placeholder.png', 'dummy image content');
}

// Inject mock get_setting before framework bootstrap might load Common.php
if (! function_exists('get_setting')) {
    function get_setting($parameter)
    {
        // Try to get from MockSettings if class exists (autoloaded or manual)
        if (class_exists('Tests\Support\MockSettings')) {
            $val = \Tests\Support\MockSettings::get($parameter);
            if (null !== $val) {
                return $val;
            }
        }

        // Fallback or empty if not set
        return '';
    }
}

// Load the framework bootstrap
require __DIR__ . '/../vendor/codeigniter4/framework/system/Test/bootstrap.php';

set_error_handler(function ($errno, $errstr, $errfile, $errline) {
    if (E_NOTICE === $errno || E_USER_NOTICE === $errno || E_DEPRECATED === $errno || E_USER_DEPRECATED === $errno) {
        fwrite(STDERR, "NOTICE/DEPRECATION CAUGHT: $errstr in $errfile:$errline\n");
    }
    return false; // Continue with normal handler
});
