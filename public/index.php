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

/**
 * Hack the installation under a limited shared hosting (cPanel). Read the
 * detailed trick here: https://github.com/abydahana/aksara/discussions/439
 */
$frameworkPath = '';

if ($frameworkPath) {
    // Fix trailing slash
    $frameworkPath = rtrim($frameworkPath, '/') . DIRECTORY_SEPARATOR;
}

// Check PHP version.
$minPhpVersion = '8.1'; // If you update this, don't forget to update `spark`.

if (version_compare(PHP_VERSION, $minPhpVersion, '<')) {
    $message = sprintf(
        'Your PHP version must be %s or higher to run CodeIgniter. Current version: %s',
        $minPhpVersion,
        PHP_VERSION
    );

    exit('<center>' . $message . '</center>');
} elseif (! in_array('intl', array_map('strtolower', get_loaded_extensions()))) {
    exit('<center>You need to enable the INTL module on your server. <a href="//www.google.com/search?q=install+intl+extension" target="_blank"><b>Click here</b></a> to install the INTL extension on your server.</center>');
} elseif (! is_dir('../' . $frameworkPath . 'vendor')) {
    exit('<center>Please run "<code style="color:green">composer install</code>" from "<code style="color:red">' . dirname(__DIR__) . '</code>" to fetch the required dependencies before we start the installation wizard.<br /><a href="//getcomposer.org/download" target="_blank"><b>Click here</b></a> to download the composer if it\'s not yet installed on your web server.</center>');
} elseif (
    ! is_writable('../' . $frameworkPath . 'writable') || 
    ! is_writable('../' . $frameworkPath . 'writable/session') || 
    ! is_writable('uploads')) {
    exit('<center>You need to make the following directory and it\'s subdirectories to be writable recursively: ' . (! is_writable('../' . $frameworkPath . 'writable') || ! is_writable('../' . $frameworkPath . 'writable/session') ? '<br /><code style="color:red">' . dirname(__DIR__) . DIRECTORY_SEPARATOR . $frameworkPath . 'writable' . '</code>' : null) . (! is_writable('uploads') ? '<br /><code style="color:red">' . dirname(__DIR__) . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'uploads' . '</code>' : null) . '<br />Please <a href="//github.com/abydahana/Aksara/issues/2" target="_blank"><b>click here</b></a> to get support related to this error.</center>');
}

// check if configuration file already exists
if (! file_exists('../' . $frameworkPath . 'config.php')) {
    if (! is_dir('../' . $frameworkPath . 'install')) {
        exit('<center>The config file or installer does not exists!</center>');
    }

    define('BASE_URL', ((isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) === 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https') ? 'https' : 'http') . '://' . (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost') . str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']));

    $config_path = $frameworkPath . 'install';
} else {
    // include the configuration file
    require_once '../' . $frameworkPath . 'config.php';

    $config_path = $frameworkPath . 'aksara';
}

/*
 *---------------------------------------------------------------
 * SET THE CURRENT DIRECTORY
 *---------------------------------------------------------------
 */

// Path to the front controller (this file)
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR);

// Ensure the current directory is pointing to the front controller's directory
if (getcwd() . DIRECTORY_SEPARATOR !== FCPATH) {
    chdir(FCPATH);
}

/*
 *---------------------------------------------------------------
 * BOOTSTRAP THE APPLICATION
 *---------------------------------------------------------------
 * This process sets up the path constants, loads and registers
 * our autoloader, along with Composer's, loads our constants
 * and fires up an environment-specific bootstrapping.
 */

// LOAD OUR PATHS CONFIG FILE
// This is the line that might need to be changed, depending on your folder structure.
require FCPATH . '../' . $config_path . '/Config/Paths.php';
// ^^^ Change this line if you move your application folder

$paths = new Config\Paths();

// LOAD THE FRAMEWORK BOOTSTRAP FILE
require $paths->systemDirectory . '/Boot.php';

exit(CodeIgniter\Boot::bootWeb($paths));
