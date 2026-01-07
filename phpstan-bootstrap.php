<?php

/**
 * PHPStan Bootstrap for Aksara CMS
 */

// 1. Define Path Constants (CRITICAL for CodeIgniter 4)
define('ROOTPATH', __DIR__ . DIRECTORY_SEPARATOR);
define('APPPATH', ROOTPATH . 'app' . DIRECTORY_SEPARATOR);
define('SYSTEMPATH', ROOTPATH . 'vendor/codeigniter4/framework/system' . DIRECTORY_SEPARATOR);
define('FCPATH', ROOTPATH . 'public' . DIRECTORY_SEPARATOR);
define('WRITEPATH', ROOTPATH . 'writable' . DIRECTORY_SEPARATOR);

// 2. Load Composer Autoloader (Required for classes)
require_once ROOTPATH . 'vendor/autoload.php';

// 3. Load CI4 Common Functions 
// This allows PHPStan to recognize global functions like env(), service(), helper()
require_once SYSTEMPATH . 'Common.php';

// 4. Load Aksara Global Functions (The Real Ones)
// Instead of writing stubs like "function phrase() {}", we load the file where they actually exist.
// Adjust the path below if 'phrase()' is located somewhere else in Aksara.
if (file_exists(ROOTPATH . 'aksara/Common.php')) {
    require_once ROOTPATH . 'aksara/Common.php';
}

// 5. Define other constants to prevent "Undefined constant" errors
defined('OCI_COMMIT_ON_SUCCESS') || define('OCI_COMMIT_ON_SUCCESS', 32);
defined('UPLOAD_PATH') || define('UPLOAD_PATH', __DIR__ . '/uploads/');
defined('BASE_URL') || define('BASE_URL', 'http://localhost/');
defined('TIMEZONE') || define('TIMEZONE', 'Asia/Jakarta');
defined('ENCRYPTION_KEY') || define('ENCRYPTION_KEY', 'phpstan_test_key');
